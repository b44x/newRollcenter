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

use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\ModelHelper;

class DynamicCondition extends DynamicObject
{
    public $id_product;
    public $name;
    public $formula;
    public $position;

    public $hidden_fields;
    public $hidden_options;
    public $hidden_groups;
    public $hidden_steps;

    private static $cache = [];

    public static $definition = [
        'table' => 'dynamicproduct_condition',
        'primary' => 'id_condition',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'name' => ['type' => self::TYPE_STRING],
            'formula' => ['type' => self::TYPE_HTML],
            'position' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->hidden_fields = DynamicCondition::getHiddenFields($this->id);
        $this->hidden_options = DynamicCondition::getHiddenOptions($this->id);
        $this->hidden_groups = DynamicCondition::getHiddenGroups($this->id);
        $this->hidden_steps = DynamicCondition::getHiddenSteps($this->id);
    }

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    /**
     * @param $id_product
     *
     * @return DynamicCondition[]
     */
    public static function getByProduct($id_product)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $dynamic_conditions = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_source_product);
        $sql->orderBy('position ASC');
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_condition = $row['id_condition'];
            $dynamic_condition = new self($id_condition);
            if (\Validate::isLoadedObject($dynamic_condition)) {
                $dynamic_conditions[$id_condition] = $dynamic_condition;
            }
        }

        return $dynamic_conditions;
    }

    /**
     * @param $id_product
     *
     * @return array
     */
    public static function getRowsByProduct($id_product)
    {
        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $conditions = \Db::getInstance()->executeS('
            SELECT c.id_condition as id, c.formula, c.name, c.position
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_condition c
            WHERE c.id_product = ' . (int) $id_source_product . ' 
            ORDER BY position ASC
        ');

        $hidden_fields = \Db::getInstance()->executeS('
            SELECT cv.*             
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_condition c
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_condition_visibility cv ON (c.id_condition = cv.id_condition)
            WHERE c.id_product = ' . (int) $id_source_product);

        $hidden_options = \Db::getInstance()->executeS('
            SELECT ov.*              
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_condition c
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_condition_option_visibility ov ON (c.id_condition = ov.id_condition)
            WHERE c.id_product = ' . (int) $id_source_product);

        $hidden_groups = \Db::getInstance()->executeS('
            SELECT gv.*                 
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_condition c
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_condition_group_visibility gv ON (c.id_condition = gv.id_condition)
            WHERE c.id_product = ' . (int) $id_source_product);

        $hidden_steps = \Db::getInstance()->executeS('
            SELECT sv.*                
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_condition c
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_condition_step_visibility sv ON (c.id_condition = sv.id_condition)
            WHERE c.id_product = ' . (int) $id_source_product);

        $hidden_fields_by_condition = [];
        if ($hidden_fields) {
            foreach ($hidden_fields as $hidden_field) {
                if (!isset($hidden_fields_by_condition[$hidden_field['id_condition']])) {
                    $hidden_fields_by_condition[$hidden_field['id_condition']] = [];
                }
                $hidden_fields_by_condition[$hidden_field['id_condition']][] = (int) $hidden_field['id_field'];
            }
        }

        $hidden_options_by_condition = [];
        if (is_array($hidden_options)) {
            foreach ($hidden_options as $hidden_option) {
                if (!isset($hidden_options_by_condition[$hidden_option['id_condition']])) {
                    $hidden_options_by_condition[$hidden_option['id_condition']] = [];
                }
                $id_field = $hidden_option['id_field'];
                if (!isset($hidden_options_by_condition[$hidden_option['id_condition']][$id_field])) {
                    $hidden_options_by_condition[$hidden_option['id_condition']][$id_field] = [];
                }
                $hidden_options_by_condition[$hidden_option['id_condition']][$id_field][] = (int) $hidden_option['id_option'];
            }
        }

        $hidden_groups_by_condition = [];
        if (is_array($hidden_groups)) {
            foreach ($hidden_groups as $hidden_group) {
                if (!isset($hidden_groups_by_condition[$hidden_group['id_condition']])) {
                    $hidden_groups_by_condition[$hidden_group['id_condition']] = [];
                }
                $hidden_groups_by_condition[$hidden_group['id_condition']][] = (int) $hidden_group['id_group'];
            }
        }

        $hidden_steps_by_condition = [];
        if (is_array($hidden_steps)) {
            foreach ($hidden_steps as $hidden_step) {
                if (!isset($hidden_steps_by_condition[$hidden_step['id_condition']])) {
                    $hidden_steps_by_condition[$hidden_step['id_condition']] = [];
                }
                $hidden_steps_by_condition[$hidden_step['id_condition']][] = (int) $hidden_step['id_step'];
            }
        }

        if (is_array($conditions)) {
            foreach ($conditions as &$condition) {
                $condition = ModelHelper::castNumericRowValues($condition, self::class);

                $condition['hidden_fields'] = $hidden_fields_by_condition[$condition['id']] ?? [];
                $condition['hidden_options'] = $hidden_options_by_condition[$condition['id']] ?? [];
                $condition['hidden_groups'] = $hidden_groups_by_condition[$condition['id']] ?? [];
                $condition['hidden_steps'] = $hidden_steps_by_condition[$condition['id']] ?? [];
            }
        }

        return self::$cache[$id_product] = $conditions;
    }

    public static function deleteByProduct($id_product)
    {
        return \Db::getInstance()->delete(self::$definition['table'], 'id_product = ' . (int) $id_product);
    }

    public static function getHiddenFields($id_condition)
    {
        $sql = new \DbQuery();
        $sql->from('dynamicproduct_condition_visibility');
        $sql->where('id_condition = ' . (int) $id_condition);
        $sql->where('visible = 0');
        $result = \Db::getInstance()->executeS($sql);
        $fields = [];
        if (is_array($result)) {
            foreach ($result as $item) {
                $fields[] = (int) $item['id_field'];
            }
        }

        return $fields;
    }

    public static function getHiddenOptions($id_condition)
    {
        $sql = new \DbQuery();
        $sql->from('dynamicproduct_condition_option_visibility');
        $sql->where('id_condition = ' . (int) $id_condition);
        $sql->where('visible = 0');
        $result = \Db::getInstance()->executeS($sql);
        $options = [];
        if (is_array($result)) {
            foreach ($result as $item) {
                $id_field = (int) $item['id_field'];
                $id_option = (int) $item['id_option'];

                if (!isset($options[$id_field])) {
                    $options[$id_field] = [];
                }

                $options[$id_field][] = $id_option;
            }
        }

        return $options;
    }

    public static function getHiddenGroups($id_condition)
    {
        $sql = new \DbQuery();
        $sql->from('dynamicproduct_condition_group_visibility');
        $sql->where('id_condition = ' . (int) $id_condition);
        $sql->where('visible = 0');
        $result = \Db::getInstance()->executeS($sql);
        $groups = [];
        if (is_array($result)) {
            foreach ($result as $item) {
                $groups[] = (int) $item['id_group'];
            }
        }

        return $groups;
    }

    public static function getHiddenSteps($id_condition)
    {
        $sql = new \DbQuery();
        $sql->from('dynamicproduct_condition_step_visibility');
        $sql->where('id_condition = ' . (int) $id_condition);
        $sql->where('visible = 0');
        $result = \Db::getInstance()->executeS($sql);
        $steps = [];
        if (is_array($result)) {
            foreach ($result as $item) {
                $steps[] = (int) $item['id_step'];
            }
        }

        return $steps;
    }

    public static function filterHiddenFields($id_condition, $rows)
    {
        $items = array_filter($rows, function ($item) use ($id_condition) {
            return $item['id_condition'] == $id_condition;
        });

        return array_map(function ($item) {
            return (int) $item['id_field'];
        }, $items);
    }

    public static function filterHiddenOptions($id_condition, $rows)
    {
        $items = array_filter($rows, function ($item) use ($id_condition) {
            return $item['id_condition'] == $id_condition;
        });

        return array_map(function ($item) {
            return (int) $item['id_option'];
        }, $items);
    }

    public static function filterHiddenGroups($id_condition, $rows)
    {
        $items = array_filter($rows, function ($item) use ($id_condition) {
            return $item['id_condition'] == $id_condition;
        });

        return array_map(function ($item) {
            return (int) $item['id_group'];
        }, $items);
    }

    public static function filterHiddenSteps($id_condition, $rows)
    {
        $items = array_filter($rows, function ($item) use ($id_condition) {
            return $item['id_condition'] == $id_condition;
        });

        return array_map(function ($item) {
            return (int) $item['id_step'];
        }, $items);
    }
}
