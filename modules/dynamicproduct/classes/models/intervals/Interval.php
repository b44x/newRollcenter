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

use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\ModelHelper;
use DynamicProduct\classes\models\DynamicObject;

class Interval extends DynamicObject
{
    public $id_product;

    /** @var IntervalField[] */
    public $interval_fields;

    /** @var IntervalConditionGroup[] */
    public $condition_groups;

    /** @var IntervalFormula[] */
    public $interval_formulas;

    public static $cache = [];

    public static $definition = [
        'table' => 'dynamicproduct_interval',
        'primary' => 'id_interval',
        'multilang' => false,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->assignIntervalFields();
        $this->assignConditionGroups();
        $this->assignIntervalFormulas();
    }

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $key = "getByIdProduct($id_product, $order, $id_lang)";
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $intervals = parent::getByIdProduct($id_source_product, $order, $id_lang);
        self::$cache[$key] = $intervals;

        return $intervals;
    }

    public static function getRowsByProduct($id_product)
    {
        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }

        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $rows = \Db::getInstance()->executeS('
            SELECT i.id_interval as id, 
            icg.id_interval_condition_group,
            ic.id_interval_condition, ic.id_field, ic.type,
            icr.min, icr.max,
            icv.value
            
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_interval i
            
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_interval_condition_group icg 
            ON i.id_interval = icg.id_interval
            
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_interval_condition ic 
            ON icg.id_interval_condition_group = ic.id_interval_condition_group
            
            LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_interval_condition_range icr
            ON (ic.id_interval_condition = icr.id_interval_condition AND ic.type = "range")
            
            LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_interval_condition_value icv
            ON (ic.id_interval_condition = icv.id_interval_condition AND ic.type = "values")
            
            WHERE i.id_product = ' . (int) $id_source_product . '
                
            ORDER BY i.id_interval, 
            icg.id_interval_condition_group, 
            ic.id_interval_condition, 
            icr.id_interval_condition_range, 
            icv.id_interval_condition_value');

        $intervals = [];

        $rows = ModelHelper::castNumericValues(
            $rows,
            self::class,
            [
                'int' => ['id_interval_condition_group', 'id_interval_condition'],
                'float' => ['min', 'max'],
            ]
        );

        foreach ($rows as $row) {
            $id = $row['id'];
            $id_interval_condition_group = $row['id_interval_condition_group'];
            if (!isset($intervals[$id])) {
                $intervals[$id] = [
                    'id' => $id,
                    'condition_groups' => [],
                ];
            }

            $condition_groups = &$intervals[$id]['condition_groups'];

            if (!isset($condition_groups[$id_interval_condition_group])) {
                $condition_groups[$id_interval_condition_group] = [
                    'id' => $id_interval_condition_group,
                    'id_interval' => $id,
                    'conditions' => [],
                ];
            }

            $id_condition = $row['id_interval_condition'];
            if (!isset($condition_groups[$id_interval_condition_group]['conditions'][$id_condition])) {
                $condition_groups[$id_interval_condition_group]['conditions'][$id_condition] = [
                    'id' => $id_condition,
                    'id_interval_condition_group' => $id_interval_condition_group,
                    'id_field' => $row['id_field'],
                    'type' => $row['type'],
                ];
            }

            $conditions = &$condition_groups[$id_interval_condition_group]['conditions'];
            if ($row['type'] == 'range') {
                $conditions[$id_condition]['min'] = (float) $row['min'];
                $conditions[$id_condition]['max'] = (float) $row['max'];
            } else {
                $conditions[$id_condition]['values'][] = $row['value'];
            }
        }

        return self::$cache[$id_product] = $intervals;
    }

    private function assignIntervalFields()
    {
        $this->interval_fields = IntervalField::getByInterval($this->id);
    }

    private function assignConditionGroups()
    {
        $this->condition_groups = IntervalConditionGroup::getByInterval($this->id);
    }

    private function assignIntervalFormulas()
    {
        $this->interval_formulas = IntervalFormula::getIntervalFormulas($this->condition_groups);
    }

    public function delete()
    {
        foreach ($this->condition_groups as $interval_condition_group) {
            $interval_condition_group->delete();
        }

        foreach ($this->interval_fields as $interval_field) {
            $interval_field->delete();
        }

        foreach ($this->interval_formulas as $interval_field_formulas) {
            foreach ($interval_field_formulas as $interval_field_formula) {
                /* @var IntervalFormula $interval_field_formula */
                $interval_field_formula->delete();
            }
        }

        return parent::delete();
    }
}
