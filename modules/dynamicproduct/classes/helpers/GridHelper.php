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
use DynamicProduct\classes\models\grids\Grid;
use DynamicProduct\classes\models\grids\GridColumn;
use DynamicProduct\classes\models\grids\GridRow;
use DynamicProduct\classes\models\grids\GridValue;

class GridHelper
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

    public function importCSVData($id_product, $csv_data)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $source = DynamicTools::getSource();
        $columns = $csv_data[0] ?: null;
        if (!$columns) {
            throw new \RuntimeException($this->module->l('Invalid CSV data format', $source));
        }

        $grid = new Grid();
        $grid->id_product = (int) $id_source_product;
        $grid->save();
        $id_grid = $grid->id;

        /** @var GridColumn[] $grid_columns */
        $grid_columns = [];
        /** @var GridRow[] $grid_rows */
        $grid_rows = [];

        $columns_count = count($columns);
        for ($c = 1; $c < $columns_count; ++$c) {
            $grid_column = new GridColumn();
            $grid_column->id_grid = $id_grid;
            $grid_column->value = (float) $columns[$c];
            $grid_column->save();
            $grid_columns[$c] = $grid_column;
        }

        $rows_count = count($csv_data);
        for ($r = 1; $r < $rows_count; ++$r) {
            if (isset($csv_data[$r][0])) {
                $grid_row = new GridRow();
                $grid_row->id_grid = $id_grid;
                $grid_row->value = (float) $csv_data[$r][0];
                $grid_row->save();
                $grid_rows[$r] = $grid_row;
            }
        }

        for ($r = 1; $r < $rows_count; ++$r) {
            $csv_row = $csv_data[$r];
            $count_values = count($csv_row);
            for ($c = 1; $c < $count_values; ++$c) {
                $value = $csv_row[$c];
                if (isset($grid_columns[$c], $grid_rows[$r])) {
                    $grid_value = new GridValue();
                    $grid_value->id_grid = $id_grid;
                    $grid_value->id_grid_column = $grid_columns[$c]->id;
                    $grid_value->id_grid_row = $grid_rows[$r]->id;
                    $grid_value->value = (float) $value;
                    $grid_value->save();
                }
            }
        }

        return $id_grid;
    }
}
