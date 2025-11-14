<?php
/**
 * 2007-2025 TuniSoft
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    TuniSoft (tunisoft.solutions@gmail.com)
 * @copyright 2007-2025 TuniSoft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/* @noinspection PhpUnusedPrivateMethodInspection */

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\factory\DynamicFieldFactory;
use DynamicProduct\classes\helpers\DynamicFieldsHelper;
use DynamicProduct\classes\helpers\FormulasHelper;
use DynamicProduct\classes\models\DynamicCommonField;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\input_fields\UploadInputField;
use ZipStream\ZipStream;

class DynamicProductFieldsController extends ModuleAdminController
{
    /** @var DynamicProduct */
    public $module;
    public $action;

    /** @var Context */
    public $context;
    public $id_field;
    public $id_product;
    public $id_default_lang;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->action = Tools::getValue('action');
        $this->id_product = (int) Tools::getValue('id_product');
        $this->id_field = (int) Tools::getValue('id_field');
        $this->id_default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
    }

    public function initContent()
    {
        return 'okk';
    }

    public function display()
    {
    }

    public function postProcess()
    {
        $source = basename(__FILE__, '.php');
        $restricted = DynamicTools::getRestricted('_DP_RESTRICTED_');
        if ((int) $this->context->employee->id_profile !== 1 && in_array($this->id_product, $restricted, false)) {
            exit(json_encode([
                'error' => true,
                'message' => $this->module->l('This product is for viewing only!', $source),
            ]));
        }

        $method = 'process' . Tools::toCamelCase($this->action, true);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }
    }

    private function processAddField()
    {
        $type = (int) Tools::getValue('type', 1);
        $id_field_after = (int) Tools::getValue('after', 0);

        $dynamic_field = new DynamicField();
        $dynamic_field->id_product = $this->id_product;

        $new_position = DynamicField::getHighestPosition($dynamic_field);
        if ($id_field_after) {
            $field_after = new DynamicField($id_field_after);
            if (Validate::isLoadedObject($field_after)) {
                $position = $field_after->position;
                $new_position = $position + 1;
                Db::getInstance()->update(
                    DynamicField::$definition['table'],
                    ['position' => ['type' => 'sql', 'value' => '`position` + 1']],
                    'id_product = ' . (int) $this->id_product . ' AND position > ' . (int) $position
                );
                Db::getInstance()->update(
                    DynamicCommonField::$definition['table'],
                    ['position' => ['type' => 'sql', 'value' => '`position` + 1']],
                    'id_product = ' . (int) $this->id_product . ' AND position > ' . (int) $position
                );
            }
        }

        $dynamic_field->name = Tools::getValue('name', '');
        $dynamic_field->position = $new_position;
        $dynamic_field->type = $type;
        $dynamic_field->active = 1;
        $dynamic_field->id_group = 0;
        $dynamic_field->id_step = 0;
        $dynamic_field->save();
        $this->respond([
            'field' => DynamicFieldFactory::create($dynamic_field->type, $dynamic_field->id),
            'fields' => DynamicField::getFieldRowsByProduct($this->id_product),
        ]);
    }

    private function processSaveField()
    {
        $id_field = (int) Tools::getValue('id');
        $dynamic_field = new DynamicField($id_field);
        $original_name = $dynamic_field->name;
        $id_product_original = (int) $dynamic_field->id_product;
        $dynamic_field->saveFromPost();
        if ($id_product_original !== $this->id_product) {
            $dynamic_field->id_product = $id_product_original;
            $dynamic_field->save();
        }

        $reason = Tools::getValue('reason');
        $updates = false;
        if ($reason === 'name_change') {
            $updates = FormulasHelper::updateFormulas($dynamic_field, $original_name, $dynamic_field->name);
        }

        if ($reason === 'toggle_common' && !$dynamic_field->common) {
            $common_fields = DynamicCommonField::getByIdField($id_field);
            foreach ($common_fields as $common_field) {
                $common_field->delete();
            }
        }

        $this->respond([
            'field' => DynamicFieldFactory::create($dynamic_field->type, $dynamic_field->id),
            'updates' => $updates,
        ]);
    }

    private function processDuplicateField()
    {
        $this->module->handler->copyField($this->id_field, $this->id_product);
        $this->respond([
            'fields' => DynamicField::getFieldRowsByProduct($this->id_product),
        ]);
    }

    private function processDeleteField()
    {
        $fields_helper = new DynamicFieldsHelper($this->module, $this->context);
        $dynamic_field = $fields_helper->deleteField($this->id_product, $this->id_field);

        $this->respond([
            'field' => $dynamic_field,
        ]);
    }

    private function processDeleteFields()
    {
        $fields = Tools::getValue('fields');
        $fields_helper = new DynamicFieldsHelper($this->module, $this->context);

        foreach ($fields as $id_field) {
            $fields_helper->deleteField($this->id_product, (int) $id_field);
        }

        $this->respond();
    }

    private function processSaveFieldImage()
    {
        $img_dir = $this->module->provider->getDataDir('images/field');

        $uploader = new Uploader();
        $uploader->setName('file');
        $uploader->setAcceptTypes(['jpeg', 'webp', 'png', 'jpg']);
        $file = $uploader->process();
        $upload = $file[0];

        if ($upload['error']) {
            $this->respond([
                'error' => true,
                'message' => $upload['error'],
            ]);
        }

        $save_path = $upload['save_path'];
        $ext = pathinfo($save_path, PATHINFO_EXTENSION);
        $filename = $this->id_field . '.' . $ext;
        ImageManager::resize($save_path, $img_dir . $filename);
        ImageManager::resize($save_path, $img_dir . $this->id_field . '-thumb.jpg', 35, 35);

        $dynamic_field = new DynamicField($this->id_field);
        $dynamic_field->image = $filename;
        $dynamic_field->save();

        $this->respond([
            'field' => new DynamicField($this->id_field),
        ]);
    }

    private function processDeleteFieldImage()
    {
        $field = new DynamicField($this->id_field);
        $path = $field->getImage();
        $thumb = $field->getThumb();
        if (is_file($path)) {
            unlink($path);
        }
        if (is_file($thumb)) {
            unlink($thumb);
        }

        $dynamic_field = new DynamicField($this->id_field);
        $dynamic_field->image = '';
        $dynamic_field->save();

        $this->respond([
            'field' => new DynamicField($this->id_field),
        ]);
    }

    private function processSaveFieldsOrder()
    {
        $order = Tools::getValue('order');
        if (is_array($order)) {
            foreach ($order as $position => $id_field) {
                $field = new DynamicField($id_field);
                if ((int) $field->id_product !== $this->id_product) {
                    $common_field = DynamicCommonField::getByFieldAndProduct($id_field, $this->id_product);
                    $common_field->position = $position;
                    $common_field->save();
                } else {
                    $field->position = (int) $position;
                    $field->save();
                }
            }
        }
        $this->respond();
    }

    private function processLoadFavoriteField()
    {
        $id_field = (int) Tools::getValue('id_field');
        $field = new DynamicField($id_field);
        $new_field = $this->module->handler->copyField($field->id, $this->id_product);
        $id_new_field = $new_field['id_field'];

        $new_field = new DynamicField($id_new_field);
        $new_field->position = DynamicField::getHighestPosition($new_field);
        $new_field->favorite = false;
        $new_field->save();

        $this->respond([
            'field' => DynamicFieldFactory::create($new_field->type, $new_field->id),
        ]);
    }

    private function processLoadCommonField()
    {
        $source = basename(__FILE__, '.php');
        $id_field = (int) Tools::getValue('id_field');
        $dynamic_field = new DynamicField($id_field);

        $common_field = DynamicCommonField::getByFieldAndProduct($id_field, $this->id_product);
        if (Validate::isLoadedObject($common_field)) {
            $this->respond([
                'error' => true,
                'message' => $this->module->l('A common field can only be included once in the same product', $source),
            ]);
        }
        $common_field->position = 1;

        $product_fields = array_values(DynamicField::getByIdProduct($this->id_product));
        if (!empty($product_fields)) {
            $common_field->position = DynamicField::getHighestPosition($product_fields[0]);
        }

        $common_field->save();

        // modify original field for display only
        $field = DynamicFieldFactory::create($dynamic_field->type, $dynamic_field->id);
        $field->position = $common_field->position;
        $field->common = true;
        $field->linked = true;

        $this->respond([
            'field' => $field,
        ]);
    }

    private function processDownloadFiles()
    {
        $prefix = Tools::getValue('prefix');
        $id_input_field = (int) Tools::getValue('id_input_field');
        $input_field = new UploadInputField($id_input_field);
        $uploads = $input_field->data_obj;
        $files = [];
        foreach ($uploads as $upload) {
            $path = $input_field->getFilePath($upload['file']);
            $keep_path = $input_field->getKeepFilePath($upload['file']);
            if ($keep_path || $path) {
                $files[] = [
                    'path' => $keep_path ?: $path,
                    'filename' => $upload['filename'],
                ];
            }
        }

        $zip_name = '/tmp/' . (int) $prefix . '-uploads_' . $input_field->name . '_' . $id_input_field . '.zip';

        // Opening a zip stream
        $archive = new \ZipStream\Option\Archive();
        $archive->setSendHttpHeaders(true);
        $zip = new ZipStream(basename($zip_name), $archive);
        foreach ($files as $file) {
            if (!is_file($file['path'])) {
                continue;
            }
            $zip->addFileFromPath($file['filename'], $file['path']);
        }

        $zip->finish();
    }

    public function respond(
        $data = [],
        $success = 1
    ) {
        $success = $success && (int) !array_key_exists('error', $data);
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
