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
namespace DynamicProduct\classes\models\intervals;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\helpers\ModelHelper;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicObject;

class IntervalFormula extends DynamicObject
{
    public $id_interval_condition_group;
    public $id_interval_field;
    public $formula;

    private static $cache = [];

    public static $definition = [
        'table' => 'dynamicproduct_interval_formula',
        'primary' => 'id_interval_formula',
        'multilang' => false,
        'fields' => [
            'id_interval_condition_group' => ['type' => self::TYPE_INT],
            'id_interval_field' => ['type' => self::TYPE_INT],
            'formula' => ['type' => self::TYPE_HTML],
        ],
    ];

    public static function getRowsByProduct($id_product)
    {
        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }
        $rows = \Db::getInstance()->executeS(/* @lang MySQL */ '
            SELECT *, formula.id_interval_formula as id
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_interval_formula` as formula
            
            JOIN `' . _DB_PREFIX_ . 'dynamicproduct_interval_field` as field
            ON formula.id_interval_field = field.id_interval_field
            
            JOIN `' . _DB_PREFIX_ . 'dynamicproduct_interval` as i
            ON field.id_interval = i.id_interval
            
            WHERE i.id_product = ' . (int) $id_product);

        $rows = ModelHelper::castNumericValues(
            $rows,
            IntervalFormula::class,
            ['int' => ['id_product', 'id_interval', 'id_field']]
        );

        return self::$cache[$id_product] = $rows;
    }

    /**
     * @param IntervalConditionGroup[] $condition_groups
     * @param DynamicField[] $fields
     *
     * @return IntervalFormula[]
     */
    public static function getIntervalFormulas($condition_groups)
    {
        $field_formulas = [];
        $interval_formulas = self::getByConditionGroups($condition_groups);
        foreach ($interval_formulas as $formula) {
            if (!isset($field_formulas[$formula->id_interval_field])) {
                $field_formulas[$formula->id_interval_field] = [];
            }
            $field_formulas[$formula->id_interval_field][$formula->id_interval_condition_group] = $formula;
        }

        return $field_formulas;
    }

    public static function getIntervalFormula($id_interval_condition_group, $id_interval_field)
    {
        $sql = new \DbQuery();
        $sql->select(self::$definition['primary']);
        $sql->from(self::$definition['table']);
        $sql->where('id_interval_condition_group = ' . (int) $id_interval_condition_group);
        $sql->where('id_interval_field = ' . (int) $id_interval_field);
        $id = (int) \Db::getInstance()->getValue($sql);
        $interval_formula = new self($id);
        $interval_formula->id_interval_condition_group = (int) $id_interval_condition_group;
        $interval_formula->id_interval_field = (int) $id_interval_field;

        return $interval_formula;
    }

    /**
     * @param IntervalConditionGroup[] $condition_groups
     *
     * @return self[]
     */
    private static function getByConditionGroups($condition_groups)
    {
        $ids = array_map(static function ($condition_group) {
            return (int) $condition_group->id;
        }, $condition_groups);
        if (!count($ids)) {
            return [];
        }
        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_interval_condition_group IN(' . pSQL(implode(',', $ids)) . ')');
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new self($id);
            if (\Validate::isLoadedObject($object)) {
                $objects[$id] = $object;
            }
        }

        return $objects;
    }

    /**
     * @param $id_condition_group
     *
     * @return IntervalFormula[]
     */
    public static function getByConditionGroup($id_condition_group)
    {
        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_interval_condition_group = ' . (int) $id_condition_group);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new self($id);
            if (\Validate::isLoadedObject($object)) {
                $objects[$id] = $object;
            }
        }

        return $objects;
    }

    /**
     * @param $id_interval_field
     *
     * @return IntervalFormula[]
     */
    public static function getByIntervalField($id_interval_field)
    {
        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_interval_field = ' . (int) $id_interval_field);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new self($id);
            if (\Validate::isLoadedObject($object)) {
                $objects[$id] = $object;
            }
        }

        return $objects;
    }
}
