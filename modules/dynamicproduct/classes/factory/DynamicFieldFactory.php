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

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\dynamic_fields\CheckboxField;
use DynamicProduct\classes\models\dynamic_fields\ColorPickerField;
use DynamicProduct\classes\models\dynamic_fields\CountryField;
use DynamicProduct\classes\models\dynamic_fields\CustomField;
use DynamicProduct\classes\models\dynamic_fields\DateField;
use DynamicProduct\classes\models\dynamic_fields\DropDownField;
use DynamicProduct\classes\models\dynamic_fields\ErrorField;
use DynamicProduct\classes\models\dynamic_fields\FeatureField;
use DynamicProduct\classes\models\dynamic_fields\FileField;
use DynamicProduct\classes\models\dynamic_fields\HtmlField;
use DynamicProduct\classes\models\dynamic_fields\ImageField;
use DynamicProduct\classes\models\dynamic_fields\NumericField;
use DynamicProduct\classes\models\dynamic_fields\PHPField;
use DynamicProduct\classes\models\dynamic_fields\PreviewField;
use DynamicProduct\classes\models\dynamic_fields\RadioField;
use DynamicProduct\classes\models\dynamic_fields\SliderField;
use DynamicProduct\classes\models\dynamic_fields\SwitchField;
use DynamicProduct\classes\models\dynamic_fields\TextAreaField;
use DynamicProduct\classes\models\dynamic_fields\TextField;
use DynamicProduct\classes\models\dynamic_fields\ThumbnailsField;
use DynamicProduct\classes\models\DynamicDropdownOption;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicPreviewOption;
use DynamicProduct\classes\models\DynamicRadioOption;
use DynamicProduct\classes\models\DynamicThumbnailsOption;

class DynamicFieldFactory
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    private static $types = [
        _DP_INPUT_ => NumericField::class,
        _DP_TEXT_ => TextField::class,
        _DP_DATE_ => DateField::class,
        _DP_IMAGE_ => ImageField::class,
        _DP_PHP_ => PHPField::class,
        _DP_DROPDOWN_ => DropDownField::class,
        _DP_CHECKBOX_ => CheckboxField::class,
        _DP_SWITCH_ => SwitchField::class,
        _DP_FILE_ => FileField::class,
        _DP_SLIDER_ => SliderField::class,
        _DP_THUMBNAILS_ => ThumbnailsField::class,
        _DP_TEXTAREA_ => TextAreaField::class,
        _DP_FEATURE_ => FeatureField::class,
        _DP_RADIO_ => RadioField::class,
        _DP_COLORPICKER_ => ColorPickerField::class,
        _DP_HTML_ => HtmlField::class,
        _DP_ERROR_ => ErrorField::class,
        _DP_CUSTOM_ => CustomField::class,
        _DP_PREVIEW_ => PreviewField::class,
        _DP_COUNTRY_ => CountryField::class,
    ];

    private static $option_types = [
        _DP_DROPDOWN_ => DynamicDropdownOption::class,
        _DP_THUMBNAILS_ => DynamicThumbnailsOption::class,
        _DP_RADIO_ => DynamicRadioOption::class,
        _DP_PREVIEW_ => DynamicPreviewOption::class,
        _DP_COUNTRY_ => DynamicDropdownOption::class,
    ];

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    /**
     * @param int $type
     * @param int $id_field
     * @param int $id_lang
     *
     * @return DynamicField
     */
    public static function create($type = null, $id_field = null, $id_lang = null)
    {
        if (!$type) {
            $type = self::getFieldType($id_field);
        }

        if (isset(self::$types[$type])) {
            return new self::$types[$type]($id_field, $id_lang);
        }

        return new DynamicField($id_field, $id_lang);
    }

    private static function getFieldType($id_field)
    {
        $module = DynamicTools::getModule();
        $sql = new \DbQuery();
        $sql->select('type');
        $sql->from($module->name . '_field');
        $sql->where('id_field = ' . (int) $id_field);

        return (int) \Db::getInstance()->getValue($sql);
    }

    /**
     * @param int $id_option
     *
     * @return DynamicDropdownOption|DynamicThumbnailsOption|DynamicRadioOption
     */
    public static function getOptionInstance($id_field, $id_option = 0)
    {
        $dynamic_field = new DynamicField($id_field);
        $type = (int) $dynamic_field->type;
        $class_name = self::$option_types[$type];

        return new $class_name($id_option);
    }
}
