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
namespace DynamicProduct\classes\factory;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\models\DynamicInputField;
use DynamicProduct\classes\models\input_fields\CheckboxInputField;
use DynamicProduct\classes\models\input_fields\ColorPickerInputField;
use DynamicProduct\classes\models\input_fields\CountryInputField;
use DynamicProduct\classes\models\input_fields\CustomFieldInputField;
use DynamicProduct\classes\models\input_fields\DateInputField;
use DynamicProduct\classes\models\input_fields\DividerInputField;
use DynamicProduct\classes\models\input_fields\DropdownInputField;
use DynamicProduct\classes\models\input_fields\ErrorInputField;
use DynamicProduct\classes\models\input_fields\FeatureInputField;
use DynamicProduct\classes\models\input_fields\FileInputField;
use DynamicProduct\classes\models\input_fields\FixedInputField;
use DynamicProduct\classes\models\input_fields\HtmlInputField;
use DynamicProduct\classes\models\input_fields\ImageInputField;
use DynamicProduct\classes\models\input_fields\NumericInputField;
use DynamicProduct\classes\models\input_fields\PhpVariableInputField;
use DynamicProduct\classes\models\input_fields\PreviewInputField;
use DynamicProduct\classes\models\input_fields\PriceInputField;
use DynamicProduct\classes\models\input_fields\RadioInputField;
use DynamicProduct\classes\models\input_fields\SliderInputField;
use DynamicProduct\classes\models\input_fields\SwitchInputField;
use DynamicProduct\classes\models\input_fields\TextAreaInputField;
use DynamicProduct\classes\models\input_fields\TextInputField;
use DynamicProduct\classes\models\input_fields\ThumbnailsInputField;

class InputFieldFactory
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    private static $types = [
        _DP_INPUT_ => NumericInputField::class,
        _DP_FIXED_ => FixedInputField::class,
        _DP_PRICE_ => PriceInputField::class,
        _DP_TEXT_ => TextInputField::class,
        _DP_DATE_ => DateInputField::class,
        _DP_IMAGE_ => ImageInputField::class,
        _DP_PHP_ => PhpVariableInputField::class,
        _DP_DROPDOWN_ => DropdownInputField::class,
        _DP_CHECKBOX_ => CheckboxInputField::class,
        _DP_SWITCH_ => SwitchInputField::class,
        _DP_FILE_ => FileInputField::class,
        _DP_SLIDER_ => SliderInputField::class,
        _DP_THUMBNAILS_ => ThumbnailsInputField::class,
        _DP_TEXTAREA_ => TextAreaInputField::class,
        _DP_FEATURE_ => FeatureInputField::class,
        _DP_RADIO_ => RadioInputField::class,
        _DP_COLORPICKER_ => ColorPickerInputField::class,
        _DP_HTML_ => HtmlInputField::class,
        _DP_ERROR_ => ErrorInputField::class,
        _DP_DIVIDER_ => DividerInputField::class,
        _DP_CUSTOM_ => CustomFieldInputField::class,
        _DP_PREVIEW_ => PreviewInputField::class,
        _DP_COUNTRY_ => CountryInputField::class,
    ];

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    /**
     * @param int $type
     * @param int $id_input_field
     * @param int $id_lang
     *
     * @return DynamicInputField
     */
    public static function create($type, $id_input_field = null, $id_lang = null)
    {
        if (isset(self::$types[$type])) {
            /** @var DynamicInputField $class */
            $class = self::$types[$type];
            if (method_exists($class, 'create')) {
                return $class::create($id_input_field, $id_lang);
            }

            return new $class($id_input_field, $id_lang);
        }

        return new DynamicInputField($id_input_field, $id_lang);
    }
}
