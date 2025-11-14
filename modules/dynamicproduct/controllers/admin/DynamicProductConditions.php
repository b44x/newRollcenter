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
use DynamicProduct\classes\factory\DynamicFieldFactory;
use DynamicProduct\classes\models\DynamicCondition;
use DynamicProduct\classes\models\DynamicEquation;

class DynamicProductConditionsController extends ModuleAdminController
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

    private function processAddCondition()
    {
        $dynamic_condition = new DynamicCondition();
        $dynamic_condition->id_product = $this->id_product;
        $dynamic_condition->position = DynamicCondition::getHighestPosition($dynamic_condition);
        $dynamic_condition->save();
        $this->respond([
            'condition' => new DynamicCondition($dynamic_condition->id),
        ]);
    }

    private function processSaveCondition()
    {
        $id_condition = (int) Tools::getValue('id_condition');
        $dynamic_condition = new DynamicCondition($id_condition);
        $dynamic_condition->formula = Tools::getValue('formula');
        $dynamic_condition->save();
        $this->respond([
            'condition' => $dynamic_condition,
        ]);
    }

    private function processSaveFieldStates()
    {
        $id_condition = Tools::getValue('id_condition');
        $hidden_fields = Tools::getValue('hidden_fields', []);

        // Clear all previous visibility values
        Db::getInstance()->delete(
            $this->module->name . '_condition_visibility',
            'id_condition = ' . (int) $id_condition
        );

        foreach ($hidden_fields as $id_field) {
            if ((int) $id_field) {
                Db::getInstance()->insert(
                    $this->module->name . '_condition_visibility',
                    [
                        'id_condition' => (int) $id_condition,
                        'id_field' => (int) $id_field,
                        'visible' => 0,
                    ],
                    false,
                    true,
                    Db::REPLACE
                );
            }
        }

        $this->respond([
            'condition' => new DynamicCondition($id_condition),
        ]);
    }

    private function processSaveGroupStates()
    {
        $id_condition = Tools::getValue('id_condition');
        $hidden_groups = Tools::getValue('hidden_groups', []);

        // Clear all previous visibility values
        Db::getInstance()->delete(
            $this->module->name . '_condition_group_visibility',
            'id_condition = ' . (int) $id_condition
        );

        foreach ($hidden_groups as $id_group) {
            if ((int) $id_group) {
                Db::getInstance()->insert(
                    $this->module->name . '_condition_group_visibility',
                    [
                        'id_condition' => (int) $id_condition,
                        'id_group' => (int) $id_group,
                        'visible' => 0,
                    ],
                    false,
                    true,
                    Db::REPLACE
                );
            }
        }

        $this->respond([
            'condition' => new DynamicCondition($id_condition),
        ]);
    }

    private function processSaveStepStates()
    {
        $id_condition = Tools::getValue('id_condition');
        $hidden_steps = Tools::getValue('hidden_steps', []);

        // Clear all previous visibility values
        Db::getInstance()->delete(
            $this->module->name . '_condition_step_visibility',
            'id_condition = ' . (int) $id_condition
        );

        foreach ($hidden_steps as $id_step) {
            if ((int) $id_step) {
                Db::getInstance()->insert(
                    $this->module->name . '_condition_step_visibility',
                    [
                        'id_condition' => (int) $id_condition,
                        'id_step' => (int) $id_step,
                        'visible' => 0,
                    ],
                    false,
                    true,
                    Db::REPLACE
                );
            }
        }

        $this->respond([
            'condition' => new DynamicCondition($id_condition),
        ]);
    }

    private function processSaveName()
    {
        $id_condition = (int) Tools::getValue('id_condition');
        $dynamic_condition = new DynamicCondition($id_condition);
        $dynamic_condition->name = Tools::getValue('name');
        $dynamic_condition->save();
        $this->respond([
            'condition' => $dynamic_condition,
        ]);
    }

    private function processSaveFormula()
    {
        $id_condition = (int) Tools::getValue('id_condition');
        $formula = Tools::getValue('formula');
        $fields = Tools::getValue('fields');
        $validation = DynamicEquation::checkFormula($this->id_product, $formula, $fields);
        if ($validation !== true) {
            $this->respond([
                'error' => true,
                'message' => $validation,
            ]);
        }
        $dynamic_condition = new DynamicCondition($id_condition);
        $dynamic_condition->formula = $formula;
        $dynamic_condition->save();
        $this->respond([
            'condition' => $dynamic_condition,
        ]);
    }

    private function processDeleteCondition()
    {
        $id_condition = (int) Tools::getValue('id_condition');
        $dynamic_condition = new DynamicCondition($id_condition);
        $dynamic_condition->delete();
        $this->respond();
    }

    private function processSaveOrder()
    {
        $order = Tools::getValue('order');
        foreach ($order as $index => $id_condition) {
            $condition = new DynamicCondition($id_condition);
            $condition->position = $index + 1;
            $condition->save();
        }
        $this->respond([
            'conditions' => DynamicCondition::getRowsByProduct($this->id_product),
        ]);
    }

    private function processSaveOptionState()
    {
        $id_condition = Tools::getValue('id_condition');
        $id_field = Tools::getValue('id_field');
        $id_option = Tools::getValue('id_option');
        $visible = Tools::getValue('visible');
        if (!(int) $visible) {
            Db::getInstance()->insert(
                $this->module->name . '_condition_option_visibility',
                [
                    'id_condition' => (int) $id_condition,
                    'id_field' => (int) $id_field,
                    'id_option' => (int) $id_option,
                    'visible' => (int) $visible,
                ],
                false,
                true,
                Db::REPLACE
            );
        } else {
            Db::getInstance()->delete(
                $this->module->name . '_condition_option_visibility',
                'id_condition = ' . (int) $id_condition .
                ' AND id_option = ' . (int) $id_option
            );
        }
        $this->respond([
            'condition' => new DynamicCondition($id_condition),
        ]);
    }

    public function processToggleAllOptions()
    {
        $id_condition = Tools::getValue('id_condition');
        $id_field = Tools::getValue('id_field');

        $has_hidden_options = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . $this->module->name . '_condition_option_visibility` 
            WHERE id_condition = ' . (int) $id_condition .
            ' and id_field = ' . (int) $id_field
        );

        if ($has_hidden_options) {
            Db::getInstance()->delete(
                $this->module->name . '_condition_option_visibility',
                'id_condition = ' . (int) $id_condition .
                ' AND id_field = ' . (int) $id_field
            );
        } else {
            $dynamic_field = DynamicFieldFactory::create(null, $id_field);
            $options = $dynamic_field->getOptions();
            foreach ($options as $option) {
                Db::getInstance()->insert(
                    $this->module->name . '_condition_option_visibility',
                    [
                        'id_condition' => (int) $id_condition,
                        'id_field' => (int) $id_field,
                        'id_option' => (int) $option['id'],
                        'visible' => 0,
                    ],
                    false,
                    true,
                    Db::REPLACE
                );
            }
        }

        $this->respond([
            'condition' => new DynamicCondition($id_condition),
        ]);
    }

    private function processFixConditionGroups()
    {
        $sql = new DbQuery();
        $sql->from($this->module->name . '_condition_group_visibility');
        $sql->where('visible = 0');
        $rows = Db::getInstance()->executeS($sql, false);
        while ($row = Db::getInstance()->nextRow($rows)) {
            $id_condition = (int) $row['id_condition'];
            $id_group = (int) $row['id_group'];
            $condition = new DynamicCondition($id_condition);
            if (Validate::isLoadedObject($condition)) {
                $sql = new DbQuery();
                $sql->from($this->module->name . '_product_field_group');
                $sql->where('id_field_group = ' . (int) $id_group);
                $id_product_field_group = (int) Db::getInstance()->getValue($sql);
                if ($id_product_field_group) {
                    Db::getInstance()->update($this->module->name . '_condition_group_visibility', [
                        'id_group' => (int) $id_product_field_group,
                    ], '`id_condition` = ' . (int) $id_condition . ' AND `id_group` =' . (int) $id_group);
                }
            }
        }

        $this->respond([
            'conditions' => DynamicCondition::getRowsByProduct($this->id_product),
        ]);
    }

    public function respond(
        $data = [],
        $success = 1
    ) {
        $success = $success && (int) !array_key_exists('error', $data);
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
