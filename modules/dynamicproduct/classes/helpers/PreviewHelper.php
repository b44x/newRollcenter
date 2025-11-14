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
namespace DynamicProduct\classes\helpers;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\factory\DynamicFieldFactory;
use DynamicProduct\classes\models\DynamicInputField;

class PreviewHelper
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    /**
     * @param DynamicInputField[] $input_fields
     *
     * @return void
     */
    public function addPreviewField(&$input_fields)
    {
        $paths = [];
        foreach ($input_fields as $input_field) {
            if (is_array($input_field->selected_options)) {
                foreach ($input_field->selected_options as $id_option) {
                    $option = DynamicFieldFactory::getOptionInstance($input_field->id_field, $id_option);
                    if ($option->hasPreview()) {
                        $paths[] = $option->getPreview();
                    }
                }
            }
        }

        if (!count($paths)) {
            return;
        }

        $bg = null;
        foreach ($paths as $path) {
            list($width, $height, $type) = getimagesize($path);
            $img = \ImageManager::create($type, $path);
            if (!$bg) {
                $bg = $img;
                imagealphablending($bg, true);
                imagesavealpha($bg, true);
            } else {
                imagealphablending($img, true);
                imagesavealpha($img, true);
                imagecopy($bg, $img, 0, 0, 0, 0, $width, $height);
            }
        }

        $uniqid = uniqid();
        $filename = 'preview-' . $uniqid . '.png';
        $filepath = $this->module->provider->getDataFile('upload/' . $filename);
        imagepng($bg, $filepath);

        if (!isset($input_fields['preview'])) {
            $preview_field = new DynamicInputField();
            $preview_field->name = 'preview';
            $preview_field->label = $this->module->l('Preview', DynamicTools::getSource());
            $preview_field->type = _DP_IMAGE_;
        } else {
            $preview_field = $input_fields['preview'];
        }

        $preview_field->visible = true;
        $preview_field->value = 1;
        $preview_field->data = [[
            'file' => basename($filepath),
        ]];
        $preview_field->setData($this->context->language->id);
        $input_fields['preview'] = $preview_field;

        $thumb = $this->module->provider->getDataFile('upload/preview-' . $uniqid . '-thumb.jpg');
        \ImageManager::resize($filepath, $thumb, 256, 256);
    }
}
