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

use DynamicProduct\classes\controllers\front\DynamicFrontController;
use DynamicProduct\classes\helpers\FileHelper;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\input_fields\FileInputField;
use DynamicProduct\classes\models\input_fields\ImageInputField;

class DynamicProductUploaderModuleFrontController extends DynamicFrontController
{
    protected function processUploadImage()
    {
        $source = basename(__FILE__, '.php');
        $id_field = (int) Tools::getValue('id_field');

        $field = new DynamicField($id_field);

        if ((int) $field->type !== _DP_IMAGE_) {
            $this->respond([
                'error' => true,
                'message' => $this->module->l('The upload could not be completed', $source),
            ]);
        }

        $options = $field->getSettings();

        $size = $options->max_size * 1024 * 1024;
        $img_dir = $this->module->provider->getDataDir('upload');

        $uploader = new Uploader();
        if ($size) {
            $uploader->setMaxSize($size);
        } else {
            $uploader->setCheckFileSize(false);
        }

        $uploader->setName('files');
        $uploader->setAcceptTypes(['jpeg', 'webp', 'png', 'jpg']);
        $files = $uploader->process();

        if (!count($files)) {
            $this->respond([
                'error' => true,
                'message' => $this->module->l('No files were uploaded', $source),
            ]);
        }

        $input_field = new ImageInputField();
        $input_field->id_field = $id_field;
        $input_field->name = $field->name;
        $input_field->visible = 1;

        $errors = [];
        $uploads = [];
        foreach ($files as $upload) {
            if ($size && $upload['size'] > $size) {
                $errors[] = $upload['name'] . ': ' . $this->module->l('This image is too big, the maximum allowed size is', $source)
                    . ' ' . $options->max_size . ' ' . $this->module->l('MB', $source);
                continue;
            }

            if ($upload['error']) {
                $errors[] = $upload['name'] . ': ' . $upload['error'];
                continue;
            }

            $extension = pathinfo($upload['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . mt_rand();

            $file = $img_dir . $filename . '.' . $extension;

            $save_path = $upload['save_path'];

            $info = getimagesize($save_path);

            if ($options->min_width && (int) $info[0] < $options->min_width) {
                $this->respond([
                    'error' => true,
                    'message' => $this->module->l('The minimum required width is', $source)
                        . ' ' . $options->min_width . ' ' . $this->module->l('pixels', $source)
                        . ' (' . $this->module->l('The uploaded image has a width of', $source)
                        . ' ' . $info[0] . ' ' . $this->module->l('pixels', $source) . ')',
                ]);
                unlink($save_path);
            }

            if ($options->min_height && (int) $info[1] < $options->min_height) {
                $this->respond([
                    'error' => true,
                    'message' => $this->module->l('The minimum required height is', $source)
                        . ' ' . $options->min_height . ' ' . $this->module->l('pixels', $source)
                        . ' (' . $this->module->l('The uploaded image has a height of', $source)
                        . ' ' . $info[1] . ' ' . $this->module->l('pixels', $source) . ')',
                ]);
                unlink($save_path);
            }

            if (is_file($save_path) && rename($save_path, $file)) {
                $uploaded = [
                    'file' => basename($file),
                    'filename' => basename($upload['name']),
                ];

                $image = $img_dir . $filename . '.' . $extension;
                $thumb = $img_dir . $filename . '-thumb.jpg';

                ImageManager::resize($save_path, $image, null, null, $extension);
                ImageManager::resize($image, $thumb, 256, 256, $extension);

                $folder_url = $this->module->provider->getDataDirUrl('upload');
                $uploaded['thumb_url'] = $folder_url . basename($thumb);
                $uploaded['image_url'] = $folder_url . basename($file);

                $uploads[] = $uploaded;
                $input_field->data = $uploads;
            } else {
                $errors[] = $upload['name'] . ': ' . $this->module->l('The file could not be saved', $source);
            }
        }

        if (count($errors)) {
            $this->respond([
                'error' => true,
                'errors' => $errors,
                'message' => $this->module->l('Some errors were encountered while uploading the files', $source),
            ]);
        } else {
            $this->respond([
                'input_field' => $input_field,
            ]);
        }
    }

    protected function processUploadFile()
    {
        $source = basename(__FILE__, '.php');
        $id_field = (int) Tools::getValue('id_field');

        $field = new DynamicField($id_field);

        if ((int) $field->type !== _DP_FILE_) {
            $this->respond([
                'error' => true,
                'message' => $this->module->l('The upload could not be completed', $source),
            ]);
        }

        $options = $field->getSettings();
        $extensions = $options->extensions;
        $extensions = explode(',', $extensions);
        foreach ($extensions as $key => $extension) {
            if (!$extension) {
                unset($extensions[$key]);
            }
        }

        $size = $options->max_size * 1024 * 1024;
        $file_dir = $this->module->provider->getDataDir('upload');

        $uploader = new Uploader();
        if ($size) {
            $uploader->setMaxSize($size);
        } else {
            $uploader->setCheckFileSize(false);
        }

        $uploader->setName('files');
        $uploader->setAcceptTypes($extensions);
        $uploader->setSavePath($file_dir);
        $files = $uploader->process();

        if (!count($files)) {
            $this->respond([
                'error' => true,
                'message' => $this->module->l('No files were uploaded', $source),
            ]);
        }

        $input_field = new FileInputField();
        $input_field->id_field = $id_field;
        $input_field->name = $field->name;
        $input_field->visible = 1;

        $errors = [];
        $uploads = [];
        foreach ($files as $upload) {
            if ($size && $upload['size'] > $size) {
                $errors[] = $upload['name'] . ': ' . $this->module->l('This file is too big, the maximum allowed size is', $source)
                    . ' ' . $options->max_size . ' ' . $this->module->l('MB', $source);
                continue;
            }

            if ($upload['error']) {
                $errors[] = $upload['name'] . ': ' . $upload['error'];
                continue;
            }

            $extension = pathinfo($upload['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . mt_rand();

            $file = $file_dir . $filename . '.' . $extension;

            $save_path = $upload['save_path'];

            if (is_file($save_path) && rename($save_path, $file)) {
                $uploaded = [
                    'file' => basename($file),
                    'filename' => basename($upload['name']),
                ];

                $file_helper = new FileHelper($this->module, $this->context);
                if ($file_helper->isImage($file)) {
                    $folder_url = $this->module->provider->getDataDirUrl('upload');
                    $thumb = $file_dir . $filename . '-thumb.jpg';
                    ImageManager::resize($file, $thumb, 256, 256, $extension);
                    $uploaded['thumb_url'] = $folder_url . basename($thumb);
                    $uploaded['image_url'] = $folder_url . basename($file);
                } else {
                    $uploaded['thumb_url'] = null;
                    $uploaded['image_url'] = null;
                }

                $uploads[] = $uploaded;
                $input_field->data = $uploads;
            } else {
                $errors[] = $upload['name'] . ': ' . $this->module->l('The file could not be saved', $source);
            }
        }

        if (count($errors)) {
            $this->respond([
                'error' => true,
                'errors' => $errors,
                'message' => $this->module->l('Some errors were encountered while uploading the files', $source),
            ]);
        } else {
            $this->respond([
                'input_field' => $input_field,
            ]);
        }
    }
}
