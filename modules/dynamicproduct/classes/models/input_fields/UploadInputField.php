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
namespace DynamicProduct\classes\models\input_fields;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\DynamicInputField;

class UploadInputField extends DynamicInputField
{
    public $dir = 'upload';
    public $keep_dir = 'upload_keep';

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if ($this->data) {
            $this->data_obj = json_decode($this->data, true);
        }
    }

    public function getFileUrl($file)
    {
        $dir = $this->getKeepFilePath($file) ? $this->keep_dir : $this->dir;
        $folder_url = $this->module->provider->getDataDirUrl($dir);

        return $folder_url . $file;
    }

    public function getDownloadUrl($id_order)
    {
        return DynamicTools::addQueryToUrl($this->context->link->getAdminLink('DynamicProductFields'), [
            'action' => 'DownloadFiles',
            'id_input_field' => $this->id,
            'prefix' => $id_order,
        ]);
    }

    public function isSkipped()
    {
        if (parent::isSkipped()) {
            return true;
        }

        return (empty($this->data_obj) || !count($this->data_obj)) && empty($this->value);
    }

    public function getFilePath($file)
    {
        $keepFilePath = $this->getKeepFilePath($file);
        if ($keepFilePath) {
            return $keepFilePath;
        }

        return $this->module->provider->getDataDir($this->dir) . basename($file);
    }

    public function getKeepFilePath($file)
    {
        $keep_path = $this->module->provider->getDataDir('upload_keep') . basename($file);

        return is_file($keep_path) ? $keep_path : false;
    }

    public function getThumbFilePath($file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $dir = $this->getKeepFilePath($file) ? $this->keep_dir : $this->dir;
        $upload_dir = $this->module->provider->getDataDir($dir);

        return $upload_dir . str_replace('.' . $extension, $this->thumb_suffix, basename($file));
    }

    public function getThumbUrl($file)
    {
        $dir = $this->getKeepFilePath($file) ? $this->keep_dir : $this->dir;
        $folder_url = $this->module->provider->getDataDirUrl($dir);
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        return $folder_url . str_replace('.' . $extension, $this->thumb_suffix, basename($file));
    }

    public function save($null_values = false, $auto_date = true)
    {
        $data_backup = $this->data;
        if (is_string($this->data)) {
            $data = json_decode($this->data, true);
            if (is_array($data)) {
                foreach ($data as &$upload) {
                    unset($upload['thumb_url']);
                    unset($upload['image_url']);
                }
                $this->value = count($data);
                $this->data = json_encode($data);
            }
        }

        $result = parent::save($null_values, $auto_date);
        $this->data = $data_backup;

        return $result;
    }
}
