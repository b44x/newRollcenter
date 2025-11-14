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
/** @noinspection PhpUnusedParameterInspection */

namespace DynamicProduct\classes\models;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\CachedEvaluationHelper;
use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\DebugHelper;
use DynamicProduct\classes\helpers\FieldsVisibilityHelper;
use DynamicProduct\classes\helpers\MathParserHelper;
use DynamicProduct\classes\helpers\ProductHelper;
use DynamicProduct\classes\models\grids\Grid;
use DynamicProduct\classes\models\grids\GridValue;
use DynamicProduct\classes\models\intervals\Interval;
use DynamicProduct\classes\models\intervals\IntervalField;
use DynamicProduct\classes\models\intervals\IntervalFormula;

class DynamicEquation extends DynamicObject
{
    public const _DP_PRICE_EQ_ = 0;
    public const _DP_WEIGHT_EQ_ = 1;
    public const _DP_QUANTITY_EQ_ = 2;
    public const _DP_COST_EQ_ = 3;

    public $id_product;
    public $id_formula;
    public $formula;

    public static $true_conditions = [];
    public static $cached_evaluations = [];

    public static $extraFields = ['product_price', 'product_weight', 'quantity'];

    public static $definition = [
        'table' => 'dynamicproduct_equation',
        'primary' => 'id_equation',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_formula' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'formula' => ['type' => self::TYPE_HTML],
        ],
    ];

    /**
     * @param $id_product
     *
     * @return DynamicEquation[]
     */
    public static function getEquationsByIdProduct($id_product)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $dynamic_equations = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_source_product);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_equation = $row['id_equation'];
            $id_formula = $row['id_formula'];
            $dynamic_equation = new self($id_equation);
            if (\Validate::isLoadedObject($dynamic_equation)) {
                $dynamic_equations[$id_formula] = $dynamic_equation;
            }
        }

        return $dynamic_equations;
    }

    /**
     * @param $id_product
     * @param $id_formula
     *
     * @return DynamicEquation
     */
    public static function getEquationByIdFormula($id_product, $id_formula)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $sql = new \DbQuery();
        $sql->select('id_equation');
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_source_product);
        $sql->where('id_formula = ' . (int) $id_formula);
        $id_equation = \Db::getInstance()->getValue($sql, false);

        return new self($id_equation);
    }

    /**
     * @param $id_product
     *
     * @return DynamicEquation
     */
    public static function getPriceEquation($id_product)
    {
        return self::getEquationByIdFormula($id_product, self::_DP_PRICE_EQ_);
    }

    /**
     * @param $id_product
     *
     * @return DynamicEquation
     */
    public static function getWeightEquation($id_product)
    {
        return self::getEquationByIdFormula($id_product, self::_DP_WEIGHT_EQ_);
    }

    /**
     * @param $id_product
     *
     * @return DynamicEquation
     */
    public static function getQuantityEquation($id_product)
    {
        return self::getEquationByIdFormula($id_product, self::_DP_QUANTITY_EQ_);
    }

    /**
     * @param $id_product
     *
     * @return DynamicEquation
     */
    public static function getCostEquation($id_product)
    {
        return self::getEquationByIdFormula($id_product, self::_DP_COST_EQ_);
    }

    /**
     * @param $formula
     * @param DynamicInputField[] $input_fields
     * @param string $name
     *
     * @return float
     */
    public static function evaluateFormula($formula, $input_fields, $name = null)
    {
        $id_product = (int) \Tools::getValue('id_product');
        CachedEvaluationHelper::loadEvaluationCache($id_product);

        $literal_expression = self::getFormulaLiteral($formula, $input_fields);
        if (strlen(trim($literal_expression)) === 0) {
            $result = 0;
        } else {
            $hash = CachedEvaluationHelper::hashExpression($literal_expression, $formula);
            $result = CachedEvaluationHelper::getCached($id_product, $hash);
            if ($result === false) {
                if (self::isArithmetic($literal_expression)) {
                    $result = eval('return ' . $literal_expression . ';'); /* expression is validated using isArithmetic */
                } else {
                    $parser = MathParserHelper::getMathParser();
                    $parser->setFields($input_fields);
                    $parser->setExpression($literal_expression);
                    $result = $parser->getValue();
                }
                CachedEvaluationHelper::cache($id_product, $hash, $result);
            }
            if ($name) {
                \DynamicProduct::$debug_messages['calculation'][] = [
                    'name' => $name,
                    'type' => 'formula',
                    'formula' => $formula,
                    'literal' => $literal_expression,
                    'result' => $result,
                ];
            }
        }

        return $result;
    }

    private static function isArithmetic(string $literal_expression)
    {
        return preg_match('/^[0-9\+\-\*\/\(\)\.]+$/', $literal_expression);
    }

    public function __destruct()
    {
        $id_product = (int) \Tools::getValue('id_product');
        CachedEvaluationHelper::storeEvaluationCache($id_product);
    }

    public static function matchFields($formula)
    {
        preg_match_all('/(\[+|"\{)(.*?)(\]+|\}")/', $formula, $matches);

        list($formula_fields, , $field_names) = $matches;

        return [$formula_fields, $field_names];
    }

    public static function shouldReplaceWithSecondaryValue($formula_field)
    {
        return preg_match('/\[\[/', $formula_field) && preg_match('/\]\]/', $formula_field);
    }

    public static function shouldReplaceWithID($formula_field)
    {
        return preg_match('/"\{/', $formula_field) && preg_match('/\}"/', $formula_field);
    }

    public static function checkFormula($id_product, $formula, $fields)
    {
        $source = DynamicTools::getSource();
        $module = DynamicTools::getModule();

        if (!\Tools::strlen($formula)) {
            return true;
        }

        $matches = self::matchFields($formula);
        $formula = str_replace(['[', ']'], ['(', ')'], $formula);
        $formula = str_replace(['{', '}'], ['(', ')'], $formula);
        $parser = MathParserHelper::getMathParser();
        if (is_array($matches) && isset($matches[0])) {
            $matches = $matches[0];
            if (is_array($matches)) {
                foreach ($matches as $formula_field) {
                    $field_name = str_replace(['[', ']', '"', '{', '}'], '', $formula_field);
                    try {
                        if (!in_array($field_name, $fields, true) && !in_array($field_name, self::$extraFields, true)) {
                            throw new \RuntimeException($module->l('Validation', $source) . ': ' . sprintf($module->l('Field %s not found', $source), $formula_field));
                        }
                        $field = new DynamicField();
                        if ((int) $id_product) {
                            $field = DynamicField::getFieldByName($id_product, $field_name);
                        }
                        if (in_array((int) $field->type, [_DP_TEXT_, _DP_DATE_, _DP_TEXTAREA_], true)) {
                            $formula = str_replace('(' . $field_name . ')', $field_name, $formula);
                            $parser->setVariable($field_name, 'A');
                        } else {
                            $parser->setVariable($field_name, 1);
                        }
                    } catch (\Exception $e) {
                        return DynamicTools::reportException($e, true);
                    }
                }
            }
        }

        $parser->setExpression($formula);

        try {
            $parser->getValue();
        } catch (\Exception $e) {
            $message = DynamicTools::reportException($e, true);
            if (is_array($matches)) {
                foreach ($matches as $match) {
                    $string = $match;
                    $match = \Tools::strtoupper($match);
                    $match = str_replace(['[', ']'], ['(', ')'], $match);
                    $message = str_replace($match, $string, $message);
                }
            }

            return $message;
        }

        return true;
    }

    /**
     * @param $formula
     * @param DynamicInputField[] $input_fields
     *
     * @return string
     */
    public static function getFormulaLiteral($formula, $input_fields)
    {
        if (empty($formula)) {
            return '';
        }
        $matches = self::matchFields($formula);
        if (!count($matches)) {
            return $formula;
        }

        $source = DynamicTools::getSource();
        $module = DynamicTools::getModule();

        list($formula_fields, $field_names) = $matches;
        foreach ($formula_fields as $index => $formula_field) {
            $field_name = trim($field_names[$index]);
            if (!isset($input_fields[$field_name]) && !in_array($field_name, self::$extraFields, true)) {
                throw new \RuntimeException(sprintf($module->l('Field %s not found', $source), $formula_field));
            }
            if (isset($input_fields[$field_name])) {
                $input_field = $input_fields[$field_name];
                if (self::shouldReplaceWithSecondaryValue($formula_field)) {
                    $secondary_value = $input_field->getSecondaryValueForCalculation($input_fields);
                    $formula = str_replace($formula_field, $secondary_value, $formula);
                } elseif (self::shouldReplaceWithID($formula_field)) {
                    $value = $input_field->id_field;
                    $formula = str_replace($formula_field, $value, $formula);
                } else {
                    $value = $input_field->getValueForCalculation($input_fields);
                    $secondary_value = $input_field->getSecondaryValueForCalculation($input_fields);
                    $formula = str_replace("[$formula_field]", $secondary_value, $formula);
                    $formula = str_replace($formula_field, $value, $formula);
                }
            }
        }

        return $formula;
    }

    public static function containsQuantityField($formula)
    {
        return strpos($formula, '[quantity]') !== false;
    }

    /**
     * @param DynamicInputField[] $input_fields
     */
    private static function getInputFieldsAsVariables($input_fields)
    {
        $variables = [];
        foreach ($input_fields as $input_field) {
            // check if $name can be used as a PHP variable name
            if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $input_field->name)) {
                $variables[$input_field->name] = $input_field->getValueForCalculation($input_fields);
                $variables['_' . $input_field->name] = $input_field->getSecondaryValueForCalculation($input_fields);
            }
        }

        return $variables;
    }

    /**
     * @param DynamicEquation $equation
     * @param $values
     * @param $id_product
     * @param $id_attribute
     * @param int $quantity
     *
     * @return float
     */
    public static function calculatePriceFormula(
        $id_product,
        $id_attribute,
        $equation,
        $input_fields
    ) {
        $result = 0;

        if (\Validate::isLoadedObject($equation)) {
            $result = self::evaluateFormula($equation->formula, $input_fields);
        }

        // try to use the calculator
        $result = self::hookPriceCalculator($id_product, $id_attribute, $input_fields, $result);

        if ($result !== false) {
            return $result;
        }

        return 0;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField[] $input_fields
     * @param float $result
     *
     * @return float
     */
    public static function hookPriceCalculator($id_product, $id_attribute, $input_fields, $result = false)
    {
        $module = DynamicTools::getModule();
        $calculator_global = $module->provider->getDataFile('calculator/products.php');
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $calculator = $module->provider->getDataFile('calculator/product' . (int) $id_source_product . '.php');

        if (!file_exists($calculator) && !file_exists($calculator_global)) {
            return $result;
        }

        $variables = self::getInputFieldsAsVariables($input_fields);
        extract($variables, EXTR_OVERWRITE);

        if (file_exists($calculator_global)) {
            include $calculator_global;
        }
        if (file_exists($calculator)) {
            include $calculator;
        }

        foreach ($input_fields as $input_field) {
            // check if $name can be used as a PHP variable name
            if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $input_field->name)) {
                if (!$input_field->isExcluded()) {
                    if (isset(${$input_field->name}) && ${$input_field->name} !== $input_field->value) {
                        $input_field->setValue(${$input_field->name});
                    }
                    if (isset(${'_' . $input_field->name})
                        && ${'_' . $input_field->name} !== $input_field->secondary_value) {
                        $input_field->setSecondaryValue(${'_' . $input_field->name});
                    }
                }
            }
        }

        return (float) $result;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param $equation
     * @param DynamicInputField[] $input_fields
     *
     * @return float
     */
    public static function calculateWeightFormula($id_product, $id_attribute, $equation, $input_fields)
    {
        $weight = 0;

        if (\Validate::isLoadedObject($equation)) {
            $weight = self::evaluateFormula($equation->formula, $input_fields);
        }

        // try to use the calculator
        $weight = self::hookWeightCalculator($id_product, $id_attribute, $input_fields, $weight);

        if ($weight !== false) {
            return $weight;
        }

        return 0;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField[] $input_fields
     * @param float $weight
     *
     * @return float
     */
    public static function hookWeightCalculator($id_product, $id_attribute, $input_fields, $weight = false)
    {
        $module = DynamicTools::getModule();
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $calculator_global = $module->provider->getDataFile('calculator/weights.php');
        $calculator = $module->provider->getDataFile('calculator/weight' . (int) $id_source_product . '.php');

        if (!file_exists($calculator) && !file_exists($calculator_global)) {
            return $weight;
        }

        extract(self::getInputFieldsAsVariables($input_fields));

        if (file_exists($calculator_global)) {
            include $calculator_global;
        }
        if (file_exists($calculator)) {
            include $calculator;
        }

        foreach ($input_fields as $input_field) {
            // check if $name can be used as a PHP variable name
            if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $input_field->name)) {
                if (${$input_field->name} !== $input_field->value) {
                    $input_field->setValue(${$input_field->name});
                }
                if (${'_' . $input_field->name} !== $input_field->secondary_value) {
                    $input_field->setSecondaryValue(${'_' . $input_field->name});
                }
            }
        }

        return (float) $weight;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param $equation
     * @param DynamicInputField[] $input_fields
     *
     * @return int
     */
    public static function calculateQuantityFormula($id_product, $id_attribute, $equation, $input_fields)
    {
        $qty = 0;

        if (\Validate::isLoadedObject($equation)) {
            $qty = self::evaluateFormula($equation->formula, $input_fields);
        }

        // try to use the calculator
        $qty = self::hookQuantityCalculator($id_product, $id_attribute, $input_fields, $qty);

        if ($qty !== false) {
            /* @noinspection UnnecessaryCastingInspection */
            return (int) $qty;
        }

        return 0;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField[] $input_fields
     * @param int $qty
     *
     * @return int
     */
    public static function hookQuantityCalculator($id_product, $id_attribute, $input_fields, $qty = false)
    {
        $module = DynamicTools::getModule();
        $calculator_global = $module->provider->getDataFile('calculator/quantity.php');
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $calculator = $module->provider->getDataFile('calculator/quantity' . (int) $id_source_product . '.php');

        if (!file_exists($calculator) && !file_exists($calculator_global)) {
            return $qty;
        }

        extract(self::getInputFieldsAsVariables($input_fields));

        if (file_exists($calculator_global)) {
            include $calculator_global;
        }
        if (file_exists($calculator)) {
            include $calculator;
        }

        foreach ($input_fields as $input_field) {
            // check if $name can be used as a PHP variable name
            if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $input_field->name)) {
                if (${$input_field->name} !== $input_field->value) {
                    $input_field->setValue(${$input_field->name});
                }
                if (${'_' . $input_field->name} !== $input_field->secondary_value) {
                    $input_field->setSecondaryValue(${'_' . $input_field->name});
                }
            }
        }

        return (float) $qty;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField[] $input_fields
     */
    public static function hookAllocator($id_product, $id_attribute, &$input_fields)
    {
        $module = DynamicTools::getModule();
        $allocator_global = $module->provider->getDataFile('allocations/products.php');
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $allocator = $module->provider->getDataFile('allocations/product' . (int) $id_source_product . '.php');

        if (!file_exists($allocator) && !file_exists($allocator_global)) {
            return;
        }

        extract(self::getInputFieldsAsVariables($input_fields));

        ${'product_price'} = $module->provider->getProductPrice($id_product, $id_attribute);

        if (file_exists($allocator_global)) {
            include $allocator_global;
        }
        if (file_exists($allocator)) {
            include $allocator;
        }

        /** @var DynamicInputField[] $custom_fields */
        $custom_fields = array_filter($input_fields, function ($input_field) {
            return $input_field->type === _DP_CUSTOM_;
        });

        foreach ($custom_fields as $input_field) {
            // check if $name can be used as a PHP variable name
            $script_name = $input_field->getDynamicField()['settings']['script_name'];
            if (!$script_name) {
                $script_name = $input_field->name;
            }
            $custom_allocations = $module->provider->getDataFile("scripts/{$script_name}/allocations.php");
            if (file_exists($custom_allocations)) {
                include $custom_allocations;
            }
        }

        foreach ($input_fields as $input_field) {
            // check if $name can be used as a PHP variable name
            if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $input_field->name)) {
                if (${$input_field->name} !== $input_field->value) {
                    $input_field->setValue(${$input_field->name});
                }
                if (${'_' . $input_field->name} !== $input_field->secondary_value) {
                    $input_field->setSecondaryValue(${'_' . $input_field->name});
                }
            }
        }
    }

    /**
     * @param $id_product
     * @param DynamicInputField[] $input_fields
     */
    public static function execFieldFormulas($id_product, &$input_fields, $id_field_formula = 0)
    {
        $source = DynamicTools::getSource();
        $module = DynamicTools::getModule();
        $field_formulas = FieldFormula::getRowsByProduct($id_product);
        foreach ($field_formulas as $field_formula) {
            if ($id_field_formula && $field_formula['id'] != $id_field_formula) {
                continue;
            }
            $formula = $field_formula['formula'];
            if (empty($formula)) {
                continue;
            }
            $target_field = FieldFormula::getTargetField($formula);
            $target_formula = FieldFormula::getTargetFormula($formula);
            if ($target_field && !isset($input_fields[$target_field])) {
                throw new \RuntimeException($module->l('Field Formula', $source) . ': ' . sprintf($module->l('Field [%s] not found', $source), $target_field));
            }
            if ($target_field && $target_formula && isset($input_fields[$target_field])) {
                try {
                    $name = "Field formula [$target_field] #{$field_formula['id']}";
                    $result = self::evaluateFormula(
                        $target_formula,
                        $input_fields,
                        $name
                    );
                    $input_fields[$target_field]->unlockValue();
                    $input_fields[$target_field]->setValue($result);
                    $input_fields[$target_field]->lockValue();
                    $input_fields[$target_field]->forceValue();

                    $index = array_search($name, array_column(\DynamicProduct::$debug_messages['calculation'], 'name'));
                    if ($index > -1) {
                        \DynamicProduct::$debug_messages['calculation'][$index]['formula'] =
                            $formula;
                    }
                } catch (\Exception $e) {
                    throw new \RuntimeException($module->l('Field Formula', $source) . "({$formula})" . ': ' . DynamicTools::reportException($e, true));
                }
            }
        }
    }

    /**
     * @param $id_product
     * @param DynamicInputField[] $input_fields
     */
    public static function execIntervalConditions($id_product, array &$input_fields, $id_item = 0)
    {
        $source = DynamicTools::getSource();
        $module = DynamicTools::getModule();
        $intervals = Interval::getRowsByProduct($id_product);
        $product_interval_fields = IntervalField::getRowsByProduct($id_product);
        foreach ($intervals as $interval) {
            $id_interval = (int) $interval['id'];
            if ($id_item && $id_interval !== $id_item) {
                continue;
            }
            $executed_group = false;
            $condition_groups = $interval['condition_groups'];
            foreach ($condition_groups as $condition_group) {
                // if a group has already been executed, do not carry on iterating the rest of the groups
                if (!$executed_group) {
                    $is_true = true;
                    $id_condition_group = $condition_group['id'];
                    $interval_conditions = $condition_group['conditions'];
                    foreach ($interval_conditions as $interval_condition) {
                        // if a group condition is false, do not complete the rest of the iterations
                        if ($is_true) {
                            /* @noinspection NestedPositiveIfStatementsInspection */
                            if ($interval_condition['id_field']) {
                                $id_field_int = (int) $interval_condition['id_field'];
                                if ($id_field_int) {
                                    $condition_field = new DynamicField($interval_condition['id_field']);
                                    /** @var DynamicInputField $input_field */
                                    $input_field = $input_fields[$condition_field->name] ?: null;
                                } else {
                                    $input_field = $input_fields[$interval_condition['id_field']] ?: null;
                                }
                                if ($input_field) {
                                    if ($interval_condition['type'] === 'range') {
                                        $is_true &= $input_field->isWithinLimit($interval_condition, $input_fields);
                                    }
                                    if ($interval_condition['type'] === 'values') {
                                        $is_true &= $input_field->isWithinValues($interval_condition, $input_fields);
                                    }
                                }
                            }
                        }
                    }
                    if ($is_true) {
                        $executed_group = true;
                        $interval_fields = array_filter(
                            $product_interval_fields,
                            function ($interval_field) use ($interval) {
                                return (int) $interval_field['id_interval'] === $interval['id'];
                            }
                        );
                        foreach ($interval_fields as $interval_field) {
                            if (!(int) $interval_field['id_field']) {
                                continue;
                            }
                            $target_field = DynamicField::getFieldFromCache($interval_field['id_field']);
                            $target_field_name = $target_field['name'];
                            if (isset($input_fields[$target_field_name]) && (int) $target_field['id']) {
                                $id_interval_field = $interval_field['id'];
                                $interval_formula = IntervalFormula::getIntervalFormula(
                                    $id_condition_group,
                                    $id_interval_field
                                );
                                try {
                                    $name = "Interval [$target_field_name] #{$interval_field['id']} ";
                                    $result = self::evaluateFormula($interval_formula->formula, $input_fields, $name);
                                } catch (\Exception $e) {
                                    throw new \RuntimeException($module->l('Interval Formula', $source) . "({$interval_formula->formula})" . ': ' . DynamicTools::reportException($e, true));
                                }
                                $input_fields[$target_field_name]->unlockValue();
                                $input_fields[$target_field_name]->setValue($result);
                                $input_fields[$target_field_name]->lockValue();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $id_product
     * @param DynamicInputField[] $input_fields
     */
    public static function execGrids($id_product, array &$input_fields, $id_grid = 0)
    {
        $grids = Grid::getRowsByProduct($id_product);
        foreach ($grids as $grid) {
            if ($id_grid && (int) $grid['id'] !== $id_grid) {
                continue;
            }
            $target_field = DynamicField::getFieldFromCache((int) $grid['id_field_target']);
            $target_field_name = $target_field['name'];
            if (isset($input_fields[$target_field_name])) {
                $column_field = DynamicField::getFieldFromCache((int) $grid['id_field_column']);
                $row_field = DynamicField::getFieldFromCache((int) $grid['id_field_row']);
                if (isset($input_fields[$column_field['name']], $input_fields[$row_field['name']])) {
                    $column_value = $input_fields[$column_field['name']]->getValueForCalculation($input_fields);
                    $row_value = $input_fields[$row_field['name']]->getValueForCalculation($input_fields);
                    $id_column = Grid::findValue($grid['columns'], $column_value);
                    $id_row = Grid::findValue($grid['rows'], $row_value);
                    $grid_value = GridValue::findValue($grid['values'], $id_row, $id_column);
                    \DynamicProduct::$debug_messages['calculation'][] = [
                        'name' => "Grid [$target_field_name] #{$grid['id']}",
                        'type' => 'grid',
                        'target_fields' => [
                            'column' => $column_field['name'],
                            'row' => $row_field['name'],
                        ],
                        'bounds' => [
                            'columns' => Grid::getBounds($grid['columns'], $column_value),
                            'rows' => Grid::getBounds($grid['rows'], $row_value),
                        ],
                        'result' => $grid_value,
                    ];
                    if ($grid_value !== null) {
                        $input_fields[$target_field_name]->unlockValue();
                        $input_fields[$target_field_name]->setValue($grid_value);
                        $input_fields[$target_field_name]->lockValue();
                    }
                }
            }
        }
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param array $exec_order
     * @param $input_fields
     *
     * @return array[]
     */
    public static function processExecOrder($id_product, $id_attribute, $exec_order, &$input_fields)
    {
        $module = DynamicTools::getModule();
        $context = DynamicTools::getContext();

        $fields_visibility = [];

        foreach ($exec_order as $item) {
            switch ($item['id_exec']) {
                case ExecOrder::EXEC_INTERVAL:
                    self::execIntervalConditions($id_product, $input_fields);
                    break;
                case ExecOrder::EXEC_FIELD_FORMULAS:
                    self::execFieldFormulas($id_product, $input_fields);
                    break;
                case ExecOrder::EXEC_GRIDS:
                    self::execGrids($id_product, $input_fields);
                    break;
                case ExecOrder::EXEC_CONDITIONS:
                    $visibility_helper = new FieldsVisibilityHelper($module, $context);
                    $fields_visibility = $visibility_helper->getFieldsVisibility(
                        $id_product,
                        $id_attribute,
                        $input_fields
                    );
                    break;
            }
        }

        return [$fields_visibility];
    }

    public static function processCustomOrder($id_product, $id_attribute, &$input_fields)
    {
        $module = DynamicTools::getModule();
        $context = DynamicTools::getContext();

        $fields_visibility = [
            'fields' => array_keys($module->provider->getVisibilityValues($id_product, $id_attribute)),
            'options' => [],
            'groups' => [],
            'steps' => [],
        ];

        $calculation_items = DynamicCalculationItem::getRowsByProduct($id_product);
        foreach ($calculation_items as $calculation_item) {
            $id_item = (int) $calculation_item['id_item'];
            switch ($calculation_item['type']) {
                case DynamicCalculationItem::CONDITION_ITEM:
                    $hidden_items = DynamicEquation::getHiddenItems($id_product, $input_fields, $id_item);
                    $visibility_helper = new FieldsVisibilityHelper($module, $context);
                    $fields_visibility = $visibility_helper->mergeArrays(
                        $fields_visibility,
                        $hidden_items
                    );
                    break;
                case DynamicCalculationItem::FIELD_FORMULA_ITEM:
                    self::execFieldFormulas($id_product, $input_fields, $id_item);
                    break;
                case DynamicCalculationItem::INTERVAL_ITEM:
                    self::execIntervalConditions($id_product, $input_fields, $id_item);
                    break;
                case DynamicCalculationItem::GRID_ITEM:
                    self::execGrids($id_product, $input_fields, $id_item);
                    break;
                default:
                    break;
            }
        }

        return [$fields_visibility];
    }

    public static function getUserJsDefinitions($id_product)
    {
        $module = DynamicTools::getModule();
        $declaration_global = $module->provider->getDataFile('declarations/products.php');
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $declaration = $module->provider->getDataFile('declarations/product' . (int) $id_source_product . '.php');

        // declarations arrays
        $declarations = [];
        $global_declarations = [];
        $product_declarations = [];

        if (file_exists($declaration_global)) {
            include $declaration_global;
        }

        if (is_array($declarations)) {
            $global_declarations = array_merge([], $declarations);
        }

        if (file_exists($declaration)) {
            include $declaration;
        }

        if (is_array($declarations)) {
            $product_declarations = array_merge([], $declarations);
        }

        $fields = DynamicField::getFieldRowsByProduct($id_product, DynamicTools::getContext()->language->id);

        /** @var DynamicInputField[] $custom_fields */
        $custom_fields = array_filter($fields, function ($input_field) {
            return $input_field['type'] === _DP_CUSTOM_;
        });

        $custom_declarations = [];
        foreach ($custom_fields as $field) {
            // check if $name can be used as a PHP variable name
            $script_name = $field['settings']['script_name'];
            if (!$script_name) {
                $script_name = $field['name'];
            }
            $declarations_file = $module->provider->getDataFile("scripts/{$script_name}/declarations.php");
            if (file_exists($declarations_file)) {
                include $declarations_file;
            }

            if (is_array($declarations)) {
                $custom_declarations = array_merge($custom_declarations, $declarations);
            }
        }

        return array_merge($global_declarations, $product_declarations, $custom_declarations);
    }

    /**
     * @param DynamicInput $dynamic_input
     * @param DynamicInputField[] $input_fields
     *
     * @return int
     */
    public static function getDynamicQuantity($dynamic_input, $input_fields = null)
    {
        $equation = self::getQuantityEquation($dynamic_input->id_product);

        return (int) self::calculateQuantityFormula(
            $dynamic_input->id_product,
            $dynamic_input->id_attribute,
            $equation,
            $input_fields ?: $dynamic_input->getInputFields()
        );
    }

    /**
     * @param DynamicInput $dynamic_input
     * @param int $dynamic_quantity
     *
     * @return bool
     */
    public static function checkProductStock($dynamic_input, $input_fields = [], $dynamic_quantity = 0)
    {
        $product_helper = new ProductHelper(DynamicTools::getModule(), DynamicTools::getContext());
        if ($product_helper->isAvailableWhenOutOfStock($dynamic_input->id_product)) {
            return true;
        }
        if (!$dynamic_quantity) {
            $dynamic_quantity = self::getDynamicQuantity($dynamic_input, $input_fields) * (float) $dynamic_input->cart_quantity;
            if (!$dynamic_quantity) {
                return true;
            }

            $inputs_quantity = DynamicInput::getInputsDynamicQuantity(
                $dynamic_input->id_product,
                $dynamic_input->id_attribute
            );

            $dynamic_quantity += $inputs_quantity;
        }
        $available_quantity = \StockAvailable::getQuantityAvailableByProduct(
            $dynamic_input->id_product,
            $dynamic_input->id_attribute
        );

        return $available_quantity >= $dynamic_quantity;
    }

    public static function getHiddenItems($id_product, $input_fields, $id_condition = 0)
    {
        $source = DynamicTools::getSource();
        $module = DynamicTools::getModule();
        $visibility_helper = new FieldsVisibilityHelper($module, DynamicTools::getContext());
        $hidden_items = [
            'fields' => [],
            'options' => [],
            'groups' => [],
            'steps' => [],
        ];
        self::$true_conditions = [];
        $dynamic_conditions = DynamicCondition::getRowsByProduct($id_product);
        foreach ($dynamic_conditions as $dynamic_condition) {
            if ($id_condition && $dynamic_condition['id'] != $id_condition) {
                continue;
            }

            $formula = $dynamic_condition['formula'];
            $name = 'Condition ' . \Tools::truncate($formula, 80, '...');
            try {
                $condition_result = self::evaluateFormula($formula, $input_fields, $name);
                $index = array_search($name, array_column(\DynamicProduct::$debug_messages['calculation'], 'name'));
                if ($index > -1) {
                    \DynamicProduct::$debug_messages['calculation'][$index]['type'] = 'condition';
                }
            } catch (\Exception $e) {
                throw new \RuntimeException($module->l('Condition Formula', $source) . "({$formula})" . ': ' . DynamicTools::reportException($e, true));
            }
            if ($condition_result) {
                if (!empty($dynamic_condition['name'])) {
                    self::$true_conditions[$dynamic_condition['name']] = true;
                }

                $hidden_fields = $dynamic_condition['hidden_fields'];
                $hidden_options = $dynamic_condition['hidden_options'];
                $hidden_groups = $dynamic_condition['hidden_groups'];
                $hidden_steps = $dynamic_condition['hidden_steps'];

                if (count($hidden_fields)
                    || count($hidden_options)
                    || count($hidden_groups)
                    || count($hidden_steps)) {
                    if ($module->provider->isModuleDebugMode()) {
                        $fieldNames = DebugHelper::getFieldNames($hidden_fields);
                        \DynamicProduct::$debug_messages['calculation'][$index]['fields'] = $fieldNames;
                        \DynamicProduct::$debug_messages['calculation'][$index]['options'] =
                            DebugHelper::getOptionNames($hidden_options);
                        \DynamicProduct::$debug_messages['calculation'][$index]['groups'] =
                            DebugHelper::getGroupNames($hidden_groups);
                        \DynamicProduct::$debug_messages['calculation'][$index]['steps'] =
                            DebugHelper::getStepNames($hidden_steps);
                    }

                    $hidden_items['fields'] = array_merge($hidden_items['fields'], $hidden_fields);
                    $hidden_items['options'] = DynamicTools::mergeRecursive($hidden_items['options'], $hidden_options);
                    $hidden_items['groups'] = array_merge($hidden_items['groups'], $hidden_groups);
                    $hidden_items['steps'] = array_merge($hidden_items['steps'], $hidden_steps);
                    $visibility_helper->setExcludedFields($input_fields, $hidden_items);
                }
            }
        }

        return $hidden_items;
    }

    public static function getTrueConditions()
    {
        return array_keys(self::$true_conditions);
    }
}
