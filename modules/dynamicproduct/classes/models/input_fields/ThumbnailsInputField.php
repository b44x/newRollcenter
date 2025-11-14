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

use DynamicProduct\classes\models\DynamicInputField;
use DynamicProduct\classes\models\DynamicThumbnailsOption;

class ThumbnailsInputField extends DynamicInputField
{
    public $type = _DP_THUMBNAILS_;

    public function isSkipped()
    {
        return parent::isSkipped() || count($this->selected_options) === 0;
    }

    public function displayValue()
    {
        if (!count($this->selected_options)) {
            return null;
        }

        $values = [];

        foreach ($this->selected_options as $id_option) {
            $thumbnails_option = new DynamicThumbnailsOption($id_option, $this->id_lang);
            if (\Validate::isLoadedObject($thumbnails_option)) {
                $values[] = $thumbnails_option->label;
            }
        }

        return join(', ', $values);
    }

    /** @noinspection PhpUnused */
    public function getDisplayedValues($input_fields)
    {
        $options = $this->field['options'];

        $displayed_values = [];
        foreach ($this->selected_options as $id_thumbnail_option) {
            if (!isset($options[$id_thumbnail_option])) {
                continue;
            }
            $thumbnails_option = $options[$id_thumbnail_option];
            $value = $thumbnails_option['label'];
            foreach ($input_fields as $input_field) {
                $value = str_replace(
                    "[{$input_field->name}]",
                    htmlspecialchars($input_field->value_formatted),
                    $value
                );
            }
            $displayed_values[] = $value;
        }

        return $displayed_values;
    }

    public function getSKU()
    {
        if (!count($this->selected_options)) {
            return null;
        }

        $values = [];
        $options = $this->field['options'];

        foreach ($this->selected_options as $id_option) {
            if (!isset($options[$id_option])) {
                continue;
            }
            $thumbnails_option = $options[$id_option];
            $values[] = $thumbnails_option['sku'];
        }

        return join(', ', array_filter($values));
    }

    public function getImagesUrls()
    {
        if (is_array($this->selected_options) && count($this->selected_options) === 1) {
            $options = $this->field['options'];
            if (!isset($options[$this->selected_options[0]])) {
                return [];
            }
            $option = $options[$this->selected_options[0]];
            if (\Validate::isLoadedObject($option) && $option->hasImage()) {
                return [
                    'thumb_url' => $option->thumb_url,
                    'image_url' => $option->image_url,
                ];
            }
        }

        return [];
    }
}
