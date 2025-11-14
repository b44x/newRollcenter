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
use DynamicProduct\classes\models\DynamicDropdownOption;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicPreviewOption;
use DynamicProduct\classes\models\DynamicRadioOption;
use DynamicProduct\classes\models\DynamicThumbnailsOption;
use Eventviva\ImageResize;

Module::getInstanceByName('dynamicproduct');

class DynamicProductFieldsOptionsController extends ModuleAdminController
{
    /** @var DynamicProduct */
    public $module;
    public $action;

    /** @var Context */
    public $context;
    public $id_field;
    public $id_option;
    public $id_product;
    public $id_default_lang;

    private static $types = [
        _DP_DROPDOWN_ => DynamicDropdownOption::class,
        _DP_THUMBNAILS_ => DynamicThumbnailsOption::class,
        _DP_RADIO_ => DynamicRadioOption::class,
        _DP_PREVIEW_ => DynamicPreviewOption::class,
    ];

    private static $image_folders = [
        _DP_DROPDOWN_ => 'dropdown',
        _DP_THUMBNAILS_ => 'thumbnails',
        _DP_RADIO_ => 'radio',
        _DP_PREVIEW_ => 'preview',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->action = Tools::getValue('action');
        $this->id_product = (int) Tools::getValue('id_product');
        $this->id_field = (int) Tools::getValue('id_field');
        $this->id_option = (int) Tools::getValue('id_option');
        $this->id_default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
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
        $this->respond([
            'error' => true,
            'message' => 'Invalid action!',
        ]);
        exit;
    }

    private function processAddOption()
    {
        $new_option = DynamicFieldFactory::getOptionInstance($this->id_field);
        $new_option->value = 0;
        $new_option->id_field = $this->id_field;
        $new_option->position = $new_option::getHighestPosition($new_option);
        $new_option->save();

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processSaveOption()
    {
        $id_option = (int) Tools::getValue('id');
        /** @var DynamicDropdownOption $dynamic_option */
        $dynamic_option = DynamicFieldFactory::getOptionInstance($this->id_field, $id_option);
        $dynamic_option->saveFromPost();

        if (property_exists($dynamic_option, 'is_default') && $dynamic_option->is_default) {
            /** @noinspection PhpUndefinedVariableInspection */
            $table = $dynamic_option::$definition['table'];
            $primary = $dynamic_option::$definition['primary'];
            Db::getInstance()->update(
                $table,
                ['is_default' => 0],
                'id_field = ' . (int) $this->id_field .
                ' AND `' . bqSQL($primary) . '` != ' . $id_option
            );
        }

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processSaveColor()
    {
        $color = Tools::getValue('color');
        $option = DynamicFieldFactory::getOptionInstance($this->id_field, $this->id_option);
        $option->color = $color;
        $option->save();

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processSaveOptionsOrder()
    {
        $order = Tools::getValue('order');
        if (is_array($order)) {
            foreach ($order as $position => $id_option) {
                $option = DynamicFieldFactory::getOptionInstance($this->id_field, $id_option);
                $option->position = (int) $position;
                $option->save();
            }
        }
        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processDeleteOption()
    {
        $option = DynamicFieldFactory::getOptionInstance($this->id_field, $this->id_option);
        $option->delete();
        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processSaveImage()
    {
        $dynamic_field = new DynamicField($this->id_field);
        $image_folder = self::$image_folders[$dynamic_field->type];
        $img_dir = $this->module->provider->getDataDir('images/' . $image_folder);

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
        $extension = pathinfo($save_path, PATHINFO_EXTENSION);

        $filename = $this->id_option . '.' . $extension;
        ImageManager::resize($save_path, $img_dir . $filename);

        list($width, $height) = getimagesize($save_path);
        $class_name = self::$types[$dynamic_field->type];
        /** @var DynamicDropdownOption $option */
        $option = new $class_name($this->id_option);
        $option->image = $filename;
        $option->image_width = $width;
        $option->image_height = $height;
        $option->save();

        $image = new ImageResize($save_path);
        $image->resizeToHeight(_DP_THUMB_);
        $image->save($img_dir . $this->id_option . '-thumb.jpg', null, 100);

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processSavePreview()
    {
        $dynamic_field = new DynamicField($this->id_field);
        $image_folder = self::$image_folders[$dynamic_field->type];
        $img_dir = $this->module->provider->getDataDir('images/' . $image_folder);

        $uploader = new Uploader();
        $uploader->setName('file');
        $uploader->setAcceptTypes(['webp', 'jpeg', 'png', 'jpg']);
        $file = $uploader->process();
        $upload = $file[0];

        if ($upload['error']) {
            $this->respond([
                'error' => true,
                'message' => $upload['error'],
            ]);
        }

        $save_path = $upload['save_path'];

        $extension = pathinfo($save_path, PATHINFO_EXTENSION);
        $preview_filename = $this->id_option . "-preview.{$extension}";
        ImageManager::resize($save_path, $img_dir . $preview_filename);

        $image = new ImageResize($save_path);
        $image->resizeToHeight(_DP_THUMB_);
        $image->save($img_dir . $this->id_option . '-preview-thumb.jpg', null, 100);

        $class_name = self::$types[$dynamic_field->type];
        /** @var DynamicDropdownOption $option */
        $option = new $class_name($this->id_option);
        $option->preview = $preview_filename;
        $option->save();

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processImportImages()
    {
        $dynamic_field = new DynamicField($this->id_field);
        $type = (int) $dynamic_field->type;
        $image_folder = self::$image_folders[$type];
        $img_dir = $this->module->provider->getDataDir('images/' . $image_folder);

        $languages = Language::getLanguages();

        $uploader = new Uploader();
        $uploader->setName('files');
        $uploader->setAcceptTypes(['jpeg', 'webp', 'png', 'jpg']);
        $files = $uploader->process();

        foreach ($files as $file) {
            if ($file['error'] === 0) {
                $path = $file['save_path'];
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                /** @var DynamicDropdownOption $class_name */
                $class_name = self::$types[$type];
                $option = new $class_name();
                $option->value = 0;
                $option->id_field = $this->id_field;
                $option->position = $class_name::getHighestPosition($option);
                $option->save();

                foreach ($languages as $language) {
                    $option->label[$language['id_lang']] = DynamicTools::capitalizeFilename(basename($path));
                }

                list($width, $height) = getimagesize($path);
                $filename = $option->id . '.' . $extension;
                ImageManager::resize($path, $img_dir . $filename);
                $option->image = $filename;
                $option->image_width = $width;
                $option->image_height = $height;
                $option->save();

                $image = new ImageResize($path);
                $image->resizeToHeight(_DP_THUMB_);
                $image->save($img_dir . $option->id . '-thumb.jpg', null, 100);
            }
        }

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processImportPreviews()
    {
        $dynamic_field = new DynamicField($this->id_field);
        $type = (int) $dynamic_field->type;
        $image_folder = self::$image_folders[$type];
        $img_dir = $this->module->provider->getDataDir('images/' . $image_folder);

        $languages = Language::getLanguages();

        $uploader = new Uploader();
        $uploader->setName('files');
        $uploader->setAcceptTypes(['jpeg', 'webp', 'png', 'jpg']);
        $files = $uploader->process();

        foreach ($files as $file) {
            if ($file['error'] === 0) {
                $path = $file['save_path'];

                /** @var DynamicDropdownOption $class_name */
                $class_name = self::$types[$type];
                $option = new $class_name();
                $option->value = 0;
                $option->id_field = $this->id_field;
                $option->position = $class_name::getHighestPosition($option);
                foreach ($languages as $language) {
                    $option->label[$language['id_lang']] = DynamicTools::capitalizeFilename(basename($path));
                }
                $option->save();

                $extension = pathinfo($path, PATHINFO_EXTENSION);

                $filename = $option->id . '-preview.' . $extension;

                ImageManager::resize($path, $img_dir . $filename);

                $image = new ImageResize($path);
                $image->resizeToHeight(_DP_THUMB_);
                $image->save($img_dir . $option->id . '-preview-thumb.jpg', null, 100);

                $option->preview = $filename;
                $option->save();
            }
        }

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processDeleteOptionImage()
    {
        $option = DynamicFieldFactory::getOptionInstance($this->id_field, $this->id_option);

        $path = $option->getImage();
        $thumb = $option->getThumb();
        if (is_file($path)) {
            unlink($path);
        }
        if (is_file($thumb)) {
            unlink($thumb);
        }

        $option->image = null;
        $option->image_width = null;
        $option->image_height = null;
        $option->save();

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processDeletePreviewImage()
    {
        $option = DynamicFieldFactory::getOptionInstance($this->id_field, $this->id_option);
        $path = $option->getPreview();
        $thumb = $option->getPreviewThumb();
        if (is_file($path)) {
            unlink($path);
        }
        if (is_file($thumb)) {
            unlink($thumb);
        }

        $option->preview = null;
        $option->save();

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    private function processDeleteOptionColor()
    {
        $option = DynamicFieldFactory::getOptionInstance($this->id_field, $this->id_option);
        $option->color = null;
        $option->save();

        $this->respond([
            'field' => DynamicFieldFactory::create(null, $this->id_field),
        ]);
    }

    public function respond($data = [], $success = 1)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $success = $success && (int) !array_key_exists('error', $data);
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
