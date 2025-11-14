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
use DynamicProduct\classes\models\DynamicEquation;
use DynamicProduct\classes\models\FieldFormula;

class DynamicProductFieldFormulasController extends ModuleAdminController
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

    private function processAddFieldFormula()
    {
        $field_formula = new FieldFormula();
        $field_formula->id_product = $this->id_product;
        $field_formula->position = FieldFormula::getHighestPosition($field_formula);
        $field_formula->save();
        $this->respond([
            'field_formula' => new FieldFormula($field_formula->id),
        ]);
    }

    private function processSaveFormula()
    {
        $source = basename(__FILE__, '.php');
        $id_field_formula = (int) Tools::getValue('id_field_formula');
        $formula = Tools::getValue('formula');
        $fields = Tools::getValue('fields');
        $validation = DynamicEquation::checkFormula($this->id_product, $formula, $fields);
        if ($validation !== true) {
            $this->respond([
                'error' => true,
                'message' => $validation,
            ]);
        }
        $field_formula = new FieldFormula($id_field_formula);
        $field_formula->formula = $formula;

        $target_field = FieldFormula::getTargetField($formula);
        $target_formula = FieldFormula::getTargetFormula($formula);
        if (empty($target_field) || empty($target_formula)) {
            $this->respond([
                'error' => true,
                'message' => $this->module->l('The formula must have the format [field] = formula...', $source),
            ]);
        }

        $field_formula->save();
        $this->respond([
            'field_formula' => $field_formula,
        ]);
    }

    private function processDeleteFieldFormula()
    {
        $id_field_formula = (int) Tools::getValue('id_field_formula');
        $field_formula = new FieldFormula($id_field_formula);
        $field_formula->delete();
        $this->respond();
    }

    private function processSaveOrder()
    {
        $order = Tools::getValue('order');
        foreach ($order as $index => $id_field_formula) {
            $field_formula = new FieldFormula($id_field_formula);
            $field_formula->position = $index + 1;
            $field_formula->save();
        }
        $this->respond([
            'field_formulas' => FieldFormula::getRowsByProduct($this->id_product),
        ]);
    }

    public function respond($data = [], $success = 1)
    {
        $success = $success && (int) !array_key_exists('error', $data);
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
