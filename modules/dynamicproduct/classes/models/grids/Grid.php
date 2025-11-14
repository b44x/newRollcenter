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
namespace DynamicProduct\classes\models\grids;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\ModelHelper;
use DynamicProduct\classes\models\DynamicObject;

class Grid extends DynamicObject
{
    public $id_product;
    public $id_field_target;
    public $id_field_column;
    public $id_field_row;

    public static $definition = [
        'table' => 'dynamicproduct_grid',
        'primary' => 'id_grid',
        'multilang' => false,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'id_field_target' => ['type' => self::TYPE_INT],
            'id_field_column' => ['type' => self::TYPE_INT],
            'id_field_row' => ['type' => self::TYPE_INT],
        ],
    ];

    /** @var GridColumn[] */
    public $columns;

    /** @var GridRow[] */
    public $rows;
    /** @var GridValue[] */
    public $values;

    private static $cache = [];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->assignColumns();
        $this->assignRows();
        $this->assignValues();
    }

    public static function getByIdProduct($id_product, $order = false, $id_lang = null): array
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    public static function getRowsByProduct($id_product)
    {
        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }

        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $grids = [];

        $db_grids = \Db::getInstance()->executeS('SELECT g.*, g.id_grid as id
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                WHERE g.id_product = ' . (int) $id_source_product);

        $rows = \Db::getInstance()->executeS('SELECT gr.id_grid, gr.id_grid_row as id, gr.value
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                JOIN `' . _DB_PREFIX_ . 'dynamicproduct_grid_row` gr ON (g.id_grid = gr.id_grid)
                WHERE g.id_product = ' . (int) $id_source_product);

        $columns = \Db::getInstance()->executeS('SELECT gc.id_grid,gc.id_grid_column as id, gc.value
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                JOIN `' . _DB_PREFIX_ . 'dynamicproduct_grid_column` gc ON (g.id_grid = gc.id_grid)
                WHERE g.id_product = ' . (int) $id_source_product);

        $values = \Db::getInstance()->executeS('SELECT gv.id_grid, gv.id_grid_row, gv.id_grid_column, gv.value
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                JOIN `' . _DB_PREFIX_ . 'dynamicproduct_grid_value` gv ON (g.id_grid = gv.id_grid)
                WHERE g.id_product = ' . (int) $id_source_product);

        foreach ($db_grids as $grid) {
            $grid = ModelHelper::castNumericRowValues($grid, self::class);
            $grid = array_merge($grid, [
                'rows' => [],
                'columns' => [],
                'values' => [],
            ]);
            $grids[$grid['id']] = $grid;
        }

        foreach ($rows as $row) {
            $row = ModelHelper::castNumericRowValues($row, GridRow::class);
            $grids[$row['id_grid']]['rows'][$row['id']] = $row;
        }

        foreach ($columns as $column) {
            $column = ModelHelper::castNumericRowValues($column, GridColumn::class);
            $grids[$column['id_grid']]['columns'][$column['id']] = $column;
        }

        // index values by row and column ids
        foreach ($values as $value) {
            $grids[$value['id_grid']]['values'][$value['id_grid_row']][$value['id_grid_column']] = (float) $value['value'];
        }

        return self::$cache[$id_product] = $grids;
    }

    public static function loadGrid($id_grid)
    {
        $default_grid = [
            'id' => $id_grid,
            'id_grid' => $id_grid,
            'id_product' => 0,
            'id_field_target' => 0,
            'id_field_column' => 0,
            'id_field_row' => 0,
            'rows' => [],
            'columns' => [],
            'values' => [],
        ];

        $grid = \Db::getInstance()->getRow('SELECT g.*, g.id_grid as id
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                WHERE g.id_grid = ' . (int) $id_grid);

        $grid = array_merge($default_grid, $grid);

        $rows = \Db::getInstance()->executeS('SELECT gr.id_grid, gr.id_grid_row as id, gr.value
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                JOIN `' . _DB_PREFIX_ . 'dynamicproduct_grid_row` gr ON (g.id_grid = gr.id_grid)
                WHERE g.id_grid = ' . (int) $id_grid);

        $columns = \Db::getInstance()->executeS('SELECT gc.id_grid,gc.id_grid_column as id, gc.value
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                JOIN `' . _DB_PREFIX_ . 'dynamicproduct_grid_column` gc ON (g.id_grid = gc.id_grid)
                WHERE g.id_grid = ' . (int) $id_grid);

        $values = \Db::getInstance()->executeS('SELECT gv.id_grid, gv.id_grid_row, gv.id_grid_column, gv.value
                FROM `' . _DB_PREFIX_ . 'dynamicproduct_grid` g
                JOIN `' . _DB_PREFIX_ . 'dynamicproduct_grid_value` gv ON (g.id_grid = gv.id_grid)
                WHERE g.id_grid = ' . (int) $id_grid);

        foreach ($rows as $row) {
            $row = ModelHelper::castNumericRowValues($row, GridRow::class);
            $grid['rows'][$row['id']] = $row;
        }

        foreach ($columns as $column) {
            $column = ModelHelper::castNumericRowValues($column, GridColumn::class);
            $grid['columns'][$column['id']] = $column;
        }

        foreach ($values as $value) {
            $grid['values'][$value['id_grid_row']][$value['id_grid_column']] = (float) $value['value'];
        }

        return $grid;
    }

    private function assignColumns()
    {
        $this->columns = GridColumn::getByGrid($this->id);
    }

    private function assignRows()
    {
        $this->rows = GridRow::getByGrid($this->id);
    }

    private function assignValues()
    {
        $this->values = GridValue::getByGrid($this->id);
    }

    public function delete()
    {
        foreach ($this->columns as $column) {
            $column->delete();
        }
        foreach ($this->rows as $row) {
            $row->delete();
        }
        foreach ($this->values as $value) {
            $value->delete();
        }

        return parent::delete();
    }

    /**
     * @param GridColumn[]|GridRow[] $items
     * @param float $value
     */
    public static function findValue($items, $value)
    {
        /** @var GridColumn[]|GridRow[] $item_values */
        $item_values = array_values($items);
        $count = count($item_values);
        for ($i = 0; $i < $count - 1; ++$i) {
            $current_value = $item_values[$i]['value'];
            if (is_numeric($value)) {
                if ($value >= $current_value && $value < $item_values[$i + 1]['value']) {
                    return $item_values[$i]['id'];
                }
            } else {
                if ($value === $current_value) {
                    return $item_values[$i]['id'];
                }
            }
        }

        if (is_numeric($value)) {
            if ($value >= $item_values[$count - 1]['value']) {
                return $item_values[$count - 1]['id'];
            }
        } else {
            if ($value === $item_values[$count - 1]['value']) {
                return $item_values[$count - 1]['id'];
            }
        }

        return null;
    }

    /**
     * @param GridColumn[]|GridRow[] $items
     * @param float $value
     */
    public static function getBounds($items, $value)
    {
        /** @var GridColumn[]|GridRow[] $item_values */
        $item_values = array_values($items);
        $count = count($item_values);
        for ($i = 0; $i < $count - 1; ++$i) {
            $current_value = $item_values[$i]['value'];
            if (is_numeric($value)) {
                if ($value >= $current_value && $value < $item_values[$i + 1]['value']) {
                    return ["({$current_value})", '<=', "[$value]", '<', $item_values[$i + 1]['value']];
                }
            } else {
                if ($value === $current_value) {
                    return ["({$current_value})", '=', "[$value]"];
                }
            }
        }

        if (is_numeric($value)) {
            if ($value >= $item_values[$count - 1]['value']) {
                return ["({$item_values[$count - 1]['value']})", '<=', "[$value]"];
            }
        } else {
            if ($value === $item_values[$count - 1]['value']) {
                return ["({$item_values[$count - 1]['value']})", '=', "[$value]"];
            }
        }

        return [];
    }
}
