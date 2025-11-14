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
namespace DynamicProduct\classes\models;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DynamicUnitValue extends DynamicObject
{
    public $id_field;
    public $id_unit;
    public $min;
    public $max;
    public $step = 1;
    public $init;
    public $extra;
    public $required;
    public $min_width;
    public $min_height;
    public $max_size;
    public $max_files = 1;
    public $extensions;
    public $min_date;
    public $max_date;
    public $disabled_days;
    public $multiselect;
    public $color;
    public $display_value_price;
    public $display_secondary_value_price;
    public $display_secondary_value_description;
    public $display_price_tax_excl;
    public $custom_suffix;
    public $display_in_popup;
    public $hide_when_empty;
    public $show_in_summary;
    public $show_image_in_summary;
    public $is_dynamic_value = 1;
    public $price_unit;
    public $ps_style;
    public $show_dropzone;
    public $script_name;
    public $json_config;

    private static $unit_values = [];

    public static $definition = [
        'table' => 'dynamicproduct_unit_value',
        'primary' => 'id_unit_value',
        'multilang' => true,
        'fields' => [
            'id_field' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_unit' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'min' => ['type' => self::TYPE_FLOAT],
            'max' => ['type' => self::TYPE_FLOAT],
            'step' => ['type' => self::TYPE_FLOAT],
            'init' => ['type' => self::TYPE_FLOAT],
            'extra' => ['type' => self::TYPE_STRING],
            'required' => ['type' => self::TYPE_INT],
            'min_width' => ['type' => self::TYPE_INT],
            'min_height' => ['type' => self::TYPE_INT],
            'max_size' => ['type' => self::TYPE_INT],
            'max_files' => ['type' => self::TYPE_INT],
            'extensions' => ['type' => self::TYPE_STRING],
            'min_date' => ['type' => self::TYPE_STRING],
            'max_date' => ['type' => self::TYPE_STRING],
            'disabled_days' => ['type' => self::TYPE_STRING],
            'multiselect' => ['type' => self::TYPE_INT],
            'color' => ['type' => self::TYPE_STRING],
            'display_value_price' => ['type' => self::TYPE_INT],
            'display_secondary_value_price' => ['type' => self::TYPE_INT],
            'display_secondary_value_description' => ['type' => self::TYPE_INT],
            'display_price_tax_excl' => ['type' => self::TYPE_INT],
            'custom_suffix' => ['type' => self::TYPE_STRING],
            'display_in_popup' => ['type' => self::TYPE_INT],
            'hide_when_empty' => ['type' => self::TYPE_INT],
            'show_in_summary' => ['type' => self::TYPE_INT],
            'show_image_in_summary' => ['type' => self::TYPE_INT],
            'is_dynamic_value' => ['type' => self::TYPE_INT],
            /* Lang fields */
            'price_unit' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
                'size' => 256,
            ],
            'ps_style' => ['type' => self::TYPE_INT],
            'show_dropzone' => ['type' => self::TYPE_INT],
            'script_name' => ['type' => self::TYPE_STRING],
            'json_config' => ['type' => self::TYPE_STRING],
        ],
    ];

    /**
     * @param $id_field
     *
     * @return DynamicUnitValue
     */
    public static function getUnitValue($id_field, $id_lang = null)
    {
        $key = $id_field;
        if (isset(self::$unit_values[$key])) {
            return self::$unit_values[$key];
        }
        $sql = new \DbQuery();
        $sql->select('id_unit_value');
        $sql->from(self::$definition['table']);
        $sql->where('id_field = ' . (int) $id_field);
        $sql->orderBy('id_unit_value');
        $id_unit_value = \Db::getInstance()->getValue($sql);
        $dynamic_unit_value = new self($id_unit_value, $id_lang);
        $dynamic_unit_value->id_field = (int) $id_field;

        return self::$unit_values[$key] = $dynamic_unit_value;
    }

    /**
     * @param $id_field
     *
     * @return DynamicUnitValue
     */
    public static function getUnitValuesByIdField($id_field)
    {
        return self::getUnitValue($id_field);
    }
}
