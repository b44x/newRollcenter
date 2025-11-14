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
if (!defined('_PS_VERSION_')) {
    exit;
}

/* @noinspection PhpUnusedPrivateMethodInspection */

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\GridHelper;
use DynamicProduct\classes\models\grids\Grid;
use DynamicProduct\classes\models\grids\GridColumn;
use DynamicProduct\classes\models\grids\GridRow;
use DynamicProduct\classes\models\grids\GridValue;

class DynamicProductGridsController extends ModuleAdminController
{
    /** @var DynamicProduct */
    public $module;
    public $action;

    /** @var Context */
    public $context;
    public $id_product;
    public $id_default_lang;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->action = Tools::getValue('action');
        $this->id_product = (int) Tools::getValue('id_product');
        $this->id_default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
    }

    public function postProcess()
    {
        $source = basename(__FILE__, '.php');
        $restricted = DynamicTools::getRestricted('_DP_RESTRICTED_');
        if ((int) $this->context->employee->id_profile !== 1 && in_array($this->id_product, $restricted, false)) {
            exit(json_encode([
                'error' => true,
                'message' => $this->module->l('This product is for viewing only!', $source),
            ]));
        }

        $method = 'process' . Tools::toCamelCase($this->action, true);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        exit;
    }

    private function processLoadGrids()
    {
        $this->respond([
            'grids' => Grid::getRowsByProduct($this->id_product),
        ]);
    }

    private function processAddGrid()
    {
        $grid = new Grid();
        $grid->id_product = (int) $this->id_product;
        $grid->save();

        $id_grid = (int) $grid->id;

        $grid_column = new GridColumn();
        $grid_column->id_grid = $id_grid;
        $grid_column->save();

        $grid_column = new GridColumn();
        $grid_column->id_grid = $id_grid;
        $grid_column->save();

        $grid_row = new GridRow();
        $grid_row->id_grid = $id_grid;
        $grid_row->add();

        $grid_row = new GridRow();
        $grid_row->id_grid = $id_grid;
        $grid_row->add();

        $this->respond([
            'grid' => Grid::loadGrid($id_grid),
        ]);
    }

    private function processSaveGrid()
    {
        $grid_data = Tools::getValue('grid');
        $grid = new Grid((int) $grid_data['id']);
        $grid->id_field_target = (int) $grid_data['id_field_target'];
        $grid->id_field_column = (int) $grid_data['id_field_column'];
        $grid->id_field_row = (int) $grid_data['id_field_row'];
        $grid->save();
        $this->respond([
            'grid' => Grid::loadGrid($grid->id),
        ]);
    }

    private function processDeleteGrid()
    {
        $id_grid = (int) Tools::getValue('id_grid');
        $grid = new Grid($id_grid);
        $grid->delete();
        $this->respond();
    }

    private function processAddColumn()
    {
        $id_grid = (int) Tools::getValue('id_grid');
        $grid_column = new GridColumn();
        $grid_column->id_grid = $id_grid;
        $grid_column->add();
        $this->respond([
            'grid' => Grid::loadGrid($id_grid),
        ]);
    }

    private function processSaveColumn()
    {
        $column = Tools::getValue('column');
        $grid_column = new GridColumn((int) $column['id']);
        $grid_column->value = $column['value'];
        $grid_column->save();
        $this->respond([
            'grid' => Grid::loadGrid($grid_column->id_grid),
        ]);
    }

    private function processDeleteColumn()
    {
        $id_column = Tools::getValue('id_column');
        $grid_column = new GridColumn($id_column);
        $grid_column->delete();
        $this->respond([
            'grid' => Grid::loadGrid($grid_column->id_grid),
        ]);
    }

    private function processAddRow()
    {
        $id_grid = (int) Tools::getValue('id_grid');
        $grid_row = new GridRow();
        $grid_row->id_grid = $id_grid;
        $grid_row->add();
        $this->respond([
            'grid' => Grid::loadGrid($id_grid),
        ]);
    }

    private function processSaveRow()
    {
        $row = Tools::getValue('row');
        $grid_row = new GridRow((int) $row['id']);
        $grid_row->value = $row['value'];
        $grid_row->save();
        $this->respond([
            'grid' => Grid::loadGrid($grid_row->id_grid),
        ]);
    }

    private function processDeleteRow()
    {
        $id_row = Tools::getValue('id_row');
        $grid_row = new GridRow($id_row);
        $grid_row->delete();
        $this->respond([
            'grid' => Grid::loadGrid($grid_row->id_grid),
        ]);
    }

    private function processSaveValue()
    {
        $id_grid = (int) Tools::getValue('id_grid');
        $id_column = (int) Tools::getValue('id_column');
        $id_row = (int) Tools::getValue('id_row');
        $value = Tools::getValue('value');

        if ($value === '') {
            $grid_value = GridValue::getByProperties($id_grid, $id_column, $id_row);
            if (Validate::isLoadedObject($grid_value)) {
                $grid_value->delete();
            }
        } else {
            $grid_value = GridValue::getByProperties($id_grid, $id_column, $id_row);
            $grid_value->value = (float) $value;
            $grid_value->save();
        }

        $this->respond();
    }

    private function processImportCSV(): void
    {
        $id_grid = null;
        $uploader = new Uploader();
        $uploader->setName('csv');
        $uploader->setAcceptTypes(['csv']);
        $files = $uploader->process();
        if (isset($files[0]) && $files[0]['error'] === 0) {
            $save_path = $files[0]['save_path'];
            if (is_file($save_path)) {
                $csv_data = array_map('str_getcsv', file($save_path));
                $grid_helper = new GridHelper($this->module, $this->context);
                try {
                    $id_grid = $grid_helper->importCSVData($this->id_product, $csv_data);
                } catch (Exception $e) {
                    $this->respond([
                        'error' => true,
                        'message' => DynamicTools::reportException($e),
                    ]);
                }
            }
        }
        if ($id_grid) {
            $this->respond([
                'grid' => Grid::loadGrid($id_grid),
            ]);
        } else {
            $source = DynamicTools::getSource();
            $this->respond([
                'error' => true,
                'message' => $this->module->l('Could not import data, please check your file then try again', $source),
            ]);
        }
    }

    public function respond($data = [], $success = 1)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $success = $success && (int) !array_key_exists('error', $data);
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
