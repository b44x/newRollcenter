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

class DynamicCombinationValue extends DynamicObject
{
    public $id_product;
    public $id_attribute;
    public $id_field;
    public $value;

    public static $definition = [
        'table' => 'dynamicproduct_combination_value',
        'primary' => 'id_combination_value',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_field' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'value' => ['type' => self::TYPE_FLOAT],
        ],
    ];

    /**
     * @param $id_product
     *
     * @return DynamicCombinationValue[]
     */
    public static function getValuesByIdProduct($id_product)
    {
        $combination_values = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_product);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_combination_value = $row['id_combination_value'];
            $combination_value = new self($id_combination_value);
            if (\Validate::isLoadedObject($combination_value)) {
                $combination_values[$id_combination_value] = $combination_value;
            }
        }

        return $combination_values;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param $id_field
     *
     * @return DynamicCombinationValue
     */
    public static function getCombinationValue($id_product, $id_attribute, $id_field)
    {
        $sql = new \DbQuery();
        $sql->select('id_combination_value');
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_product);
        $sql->where('id_attribute = ' . (int) $id_attribute);
        $sql->where('id_field = ' . (int) $id_field);
        $id_combination_value = \Db::getInstance()->getValue($sql, false);
        $combination_value = new self($id_combination_value);
        if (!\Validate::isLoadedObject($combination_value)) {
            $combination_value->id_product = $id_product;
            $combination_value->id_attribute = $id_attribute;
            $combination_value->id_field = $id_field;
        }

        return $combination_value;
    }

    public static function getValue($id_attribute, $id_field)
    {
        $sql = new \DbQuery();
        $sql->select('init');
        $sql->from(self::$definition['table']);
        $sql->where('id_attribute = ' . (int) $id_attribute);
        $sql->where('id_field = ' . (int) $id_field);
        $init = \Db::getInstance()->getValue($sql);
        if ($init === false) {
            return false;
        }

        return (float) $init;
    }

    /**
     * @param DynamicCombinationValue[] $combination_values
     *
     * @return array
     */
    public static function organizeByAttributes($combination_values)
    {
        $result = [];
        foreach ($combination_values as $combination_value) {
            if (isset($result[$combination_value->id_attribute])) {
                $result[$combination_value->id_attribute][] = $combination_value;
            } else {
                $result[$combination_value->id_attribute] = [$combination_value];
            }
        }

        return $result;
    }

    /**
     * @param DynamicCombinationValue[] $combination_values
     *
     * @return array
     */
    public static function organizeByAttributesAndFields($combination_values)
    {
        $result = [];
        foreach ($combination_values as $combination_value) {
            $id_attribute = $combination_value->id_attribute;
            $id_field = $combination_value->id_field;
            if (!isset($result[$id_attribute])) {
                $result[$id_attribute] = [];
            }
            $result[$id_attribute][$id_field] = $combination_value;
        }

        return $result;
    }
}
