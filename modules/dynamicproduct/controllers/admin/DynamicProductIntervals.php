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
use DynamicProduct\classes\models\intervals\Interval;
use DynamicProduct\classes\models\intervals\IntervalCondition;
use DynamicProduct\classes\models\intervals\IntervalConditionGroup;
use DynamicProduct\classes\models\intervals\IntervalConditionRange;
use DynamicProduct\classes\models\intervals\IntervalConditionValue;
use DynamicProduct\classes\models\intervals\IntervalField;
use DynamicProduct\classes\models\intervals\IntervalFormula;

class DynamicProductIntervalsController extends ModuleAdminController
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

    private function processLoadData()
    {
        $this->respond([
            'intervals' => Interval::getRowsByProduct($this->id_product),
            'interval_fields' => IntervalField::getRowsByProduct($this->id_product),
            'interval_formulas' => IntervalFormula::getRowsByProduct($this->id_product),
        ]);
    }

    public function processAddInterval()
    {
        $interval = new Interval();
        $interval->id_product = $this->id_product;
        $interval->save();

        $interval_condition_group = new IntervalConditionGroup();
        $interval_condition_group->id_interval = $interval->id;
        $interval_condition_group->save();

        $interval_condition = new IntervalCondition();
        $interval_condition->id_interval_condition_group = $interval_condition_group->id;
        $interval_condition->type = 'range';
        $interval_condition->save();

        $interval_field = new IntervalField();
        $interval_field->id_interval = $interval->id;
        $interval_field->save();

        $this->respond([
            'interval' => new Interval($interval->id),
        ]);
    }

    public function processDeleteInterval()
    {
        $id_interval = (int) Tools::getValue('id_interval');
        $interval = new Interval($id_interval);
        $interval->delete();
        $this->respond();
    }

    public function processAddConditionGroup()
    {
        $id_interval = (int) Tools::getValue('id_interval');

        $last_condition_group = IntervalConditionGroup::getLastConditionGroup($id_interval);

        $condition_group = new IntervalConditionGroup();
        $condition_group->id_interval = $id_interval;
        $condition_group->save();

        if (Validate::isLoadedObject($last_condition_group)) {
            $interval_conditions = IntervalCondition::getByIntervalConditionGroup($last_condition_group->id);
            foreach ($interval_conditions as $interval_condition) {
                $id_interval_condition = $interval_condition->id;
                $interval_condition->id_interval_condition_group = $condition_group->id;
                $interval_condition->add();

                $condition_range = IntervalConditionRange::getByIntervalCondition($id_interval_condition);
                if (Validate::isLoadedObject($condition_range)) {
                    $condition_range->id_interval_condition = $interval_condition->id;
                    $diff = (float) $condition_range->max - (float) $condition_range->min;
                    $condition_range->min = (float) $condition_range->max;
                    $condition_range->max += $diff;
                    $condition_range->add();
                }

                $condition_values = IntervalConditionValue::getByIntervalCondition($id_interval_condition);
                foreach ($condition_values as $condition_value) {
                    $condition_value->id_interval_condition = $interval_condition->id;
                    $condition_value->add();
                }
            }
        }

        $this->respond([
            'condition_group' => new IntervalConditionGroup($condition_group->id),
        ]);
    }

    public function processDeleteConditionGroup()
    {
        $id_interval_condition_group = (int) Tools::getValue('id_interval_condition_group');
        $interval_condition_group = new IntervalConditionGroup($id_interval_condition_group);
        $interval_condition_group->delete();
        $this->respond();
    }

    public function processAddIntervalCondition()
    {
        $id_interval_condition_group = (int) Tools::getValue('id_interval_condition_group');

        $interval_condition = new IntervalCondition();
        $interval_condition->id_interval_condition_group = $id_interval_condition_group;
        $interval_condition->id_field = 0;
        $interval_condition->type = IntervalCondition::$TYPE_RANGE;
        $interval_condition->save();

        $id_interval_condition = $interval_condition->id;

        $condition_range = IntervalConditionRange::getByIntervalCondition($id_interval_condition);
        $condition_range->min = 0;
        $condition_range->max = 100;
        $condition_range->save();

        $this->respond([
            'interval_condition' => new IntervalCondition($id_interval_condition),
        ]);
    }

    public function processSaveIntervalCondition()
    {
        $id_interval_condition = (int) Tools::getValue('id_interval_condition');
        $id_field = Tools::getValue('id_field');
        $type = Tools::getValue('type');
        $min = (float) Tools::getValue('min');
        $max = Tools::getValue('max');
        if ($max === '∞') {
            // 0 as the max is alias for +inf
            $max = 0;
        } else {
            $max = (float) $max;
        }
        $values = Tools::getValue('values');

        $interval_condition = new IntervalCondition($id_interval_condition);
        if (Validate::isLoadedObject($interval_condition)) {
            $interval_condition->id_field = $id_field;
            $interval_condition->type = $type;
            $interval_condition->save();

            if ($type === 'range') {
                $condition_range = IntervalConditionRange::getByIntervalCondition($id_interval_condition);
                $condition_range->min = $min;
                $condition_range->max = $max;
                $condition_range->save();
            }

            if ($type === 'values') {
                $condition_values = IntervalConditionValue::getByIntervalCondition($id_interval_condition);
                foreach ($condition_values as $condition_value) {
                    $condition_value->delete();
                }
                $values = explode(',', $values);
                foreach ($values as $value) {
                    $condition_value = new IntervalConditionValue();
                    $condition_value->id_interval_condition = $id_interval_condition;
                    $condition_value->value = $value;
                    $condition_value->save();
                }
            }
        }
        $this->respond();
    }

    public function processDeleteIntervalCondition()
    {
        $id_interval_condition = (int) Tools::getValue('id_interval_condition');
        $interval_condition = new IntervalCondition($id_interval_condition);
        $interval_condition->delete();
        $this->respond();
    }

    public function processAddIntervalField()
    {
        $id_interval = (int) Tools::getValue('id_interval');
        $interval_field = new IntervalField();
        $interval_field->id_interval = $id_interval;
        $interval_field->save();
        $this->respond([
            'interval_field' => new IntervalField($interval_field->id),
        ]);
    }

    public function processSaveIntervalField()
    {
        $id_interval_field = (int) Tools::getValue('id_interval_field');
        $id_field = (int) Tools::getValue('id_field');
        $interval_field = new IntervalField($id_interval_field);
        if (Validate::isLoadedObject($interval_field)) {
            $interval_field->id_field = $id_field;
            $interval_field->save();
        }
        $this->respond();
    }

    public function processDeleteIntervalField()
    {
        $id_interval_field = (int) Tools::getValue('id_interval_field');
        $interval_field = new IntervalField($id_interval_field);
        $interval_field->delete();
        $this->respond();
    }

    public function processSaveIntervalFormula()
    {
        $id_interval_condition_group = (int) Tools::getValue('id_interval_condition_group');
        $id_interval_field = (int) Tools::getValue('id_interval_field');

        $formula = Tools::getValue('formula');
        $fields = Tools::getValue('fields');
        $validation = DynamicEquation::checkFormula($this->id_product, $formula, $fields);
        if ($validation !== true) {
            $this->respond([
                'error' => true,
                'message' => $validation,
            ]);
        }

        $interval_formula = IntervalFormula::getIntervalFormula($id_interval_condition_group, $id_interval_field);
        $interval_formula->formula = $formula;
        $interval_formula->save();
        $this->respond([
            'interval_formula' => $interval_formula,
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
