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
use DynamicProduct\classes\models\DynamicCommonField;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicProductFieldGroup;
use DynamicProduct\classes\models\DynamicProductStep;
use DynamicProduct\classes\models\DynamicStep;

class DynamicProductStepsController extends ModuleAdminController
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
        if ((int) $this->context->employee->id_profile !== 1 && in_array($this->id_product, $restricted)) {
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

    private function processGetSteps()
    {
        $this->respond([
            'product_steps' => DynamicProductStep::getRowsByProduct($this->id_product),
            'steps' => DynamicStep::getAllRows($this->context->language->id),
        ]);
    }

    private function processInsertStep()
    {
        $id_step = (int) Tools::getValue('id_step');
        $product_step = new DynamicProductStep();
        $product_step->id_product = (int) $this->id_product;
        $product_step->id_step = $id_step;
        $product_step->position = DynamicProductStep::getHighestPosition($product_step);
        $product_step->save();
        $this->respond([
            'product_steps' => DynamicProductStep::getRowsByProduct($this->id_product),
        ]);
    }

    private function processDeleteStep()
    {
        $id_step = (int) Tools::getValue('id_step');
        $product_step = new DynamicProductStep($id_step);
        $product_step->delete();
        DynamicProductStep::fixPositions(DynamicProductStep::getByIdProduct($this->id_product));
        $this->respond([
            'product_steps' => DynamicProductStep::getRowsByProduct($this->id_product),
        ]);
    }

    private function processReorderSteps()
    {
        $order = (array) Tools::getValue('order');
        foreach ($order as $position => $id_product_step) {
            $product_step = new DynamicProductStep((int) $id_product_step);
            $product_step->position = (int) $position + 1;
            $product_step->save();
        }
        $this->respond([
            'product_steps' => DynamicProductStep::getRowsByProduct($this->id_product),
        ]);
    }

    private function processSaveGroupsOrder()
    {
        $order = (array) Tools::getValue('order');
        foreach ($order as $position => $order_data) {
            $id_product_group = (int) $order_data['id_group'];
            $product_group = new DynamicProductFieldGroup((int) $id_product_group);
            $product_group->id_step = (int) $order_data['id_step'];
            $product_group->position = (int) $position + 1;
            $product_group->save();
        }
        $this->respond();
    }

    private function processSaveFieldsOrder()
    {
        $order = Tools::getValue('order');
        if (is_array($order)) {
            foreach ($order as $position => $order_data) {
                $id_field = (int) $order_data['id_field'];
                $field = new DynamicField($id_field);
                if ((int) $field->id_product !== $this->id_product) {
                    $common_field = DynamicCommonField::getByFieldAndProduct($id_field, $this->id_product);
                    $common_field->id_step = (int) $order_data['id_step'];
                    $common_field->position = (int) $position;
                    $common_field->save();
                } else {
                    $field->id_step = (int) $order_data['id_step'];
                    $field->position = (int) $position;
                    $field->save();
                }
            }
        }
        $this->respond();
    }

    private function processSaveStep()
    {
        $id_field = (int) Tools::getValue('id_field');
        $id_step = (int) Tools::getValue('id_step');
        $dynamic_field = new DynamicField($id_field);
        if ((int) $dynamic_field->id_product !== $this->id_product) {
            $common_field = DynamicCommonField::getByFieldAndProduct($id_field, $this->id_product);
            $common_field->id_step = $id_step;
            $common_field->save();
        } else {
            $dynamic_field->id_step = $id_step;
            $dynamic_field->save();
        }
        $this->respond();
    }

    private function respond($data = [], $success = 1)
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
