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
namespace DynamicProduct\libs\parser;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\DynamicInputField;

class MathParser
{
    /** @var DynamicInputField[] */
    protected $input_fields;
    protected $expression;
    protected $dirty;
    protected $optimization_on;
    protected $node;
    protected $variables;
    protected $functions;
    protected static $translator;
    protected $string_literals_allowed = true;
    protected $str_concat_operator;
    public $variable_resolver;
    protected static $add_func;
    protected static $subtract_func;
    protected static $mult_func;
    protected static $div_func;
    protected static $power_func;
    protected static $mod_func;
    protected static $unaryadd_func;
    protected static $negate_func;
    protected static $not_func;
    protected static $notequals_func;
    protected static $equals_func;
    protected static $lt_func;
    protected static $gt_func;
    protected static $ltequals_func;
    protected static $gtequals_func;
    protected static $and_func;
    protected static $or_func;
    protected static $bitwise_and_func;
    protected static $bitwise_or_func;
    protected $add_op;
    protected $and_op;

    public function __construct()
    {
        include_once 'MathParserFunctions.php';
        $this->expression = '';
        $this->node = null;
        $this->dirty = true;
        $this->optimization_on = false;

        $this->variables = [];
        $this->functions = [];

        $this->createDefaultFuncs();
        $this->createDefaultVars();

        if (!isset(self::$add_func)) {
            self::$add_func = new MathParserParserFunction('+', 'mpAdd', 2);
            self::$subtract_func = new MathParserParserFunction('-', 'mpSubtract', 2);
            self::$mult_func = new MathParserParserFunction('*', 'mpMult', 2);
            self::$div_func = new MathParserParserFunction('/', 'mpDiv', 2);
            self::$power_func = new MathParserParserFunction('^', 'mpPower', 2);
            self::$mod_func = new MathParserParserFunction('%', 'mpMod', 2);

            self::$notequals_func = new MathParserParserFunction('!=', 'mpNotequals', 2);
            self::$equals_func = new MathParserParserFunction('=', 'mpEquals', 2);
            self::$lt_func = new MathParserParserFunction('<', 'mpLt', 2);
            self::$gt_func = new MathParserParserFunction('>', 'mpGt', 2);
            self::$ltequals_func = new MathParserParserFunction('<=', 'mpLtequals', 2);
            self::$gtequals_func = new MathParserParserFunction('>=', 'mpGtequals', 2);

            self::$unaryadd_func = new MathParserParserFunction('+', 'mpUnaryadd', 1);
            self::$negate_func = new MathParserParserFunction('-', 'mpNegate', 1);
            self::$not_func = new MathParserParserFunction('!', 'mpNot', 1);

            self::$and_func = new MathParserParserFunction('&', 'mpAnd', 2);
            self::$or_func = new MathParserParserFunction('|', 'mpOr', 2);
        }

        $this->add_op = self::$add_func;
        $this->and_op = self::$and_func;
        $this->str_concat_operator = '&';
    }

    public function getStringLiteralsAllowed()
    {
        return $this->string_literals_allowed;
    }

    public function setStringLiteralsAllowed($value)
    {
        $this->string_literals_allowed = $value;
        if ($value == true) {
            if ($this->str_concat_operator == '&') {
                $this->and_op = new MathParserParserFunction('&', 'mpAndStr', 2);
            } elseif ($this->str_concat_operator == '+') {
                $this->add_op = new MathParserParserFunction('+', 'mpAddStr', 2);
            } else {
                throw new \Exception('Invalid string concat operator: ' . $this->str_concat_operator);
            }
        } else {
            $this->add_op = self::$add_func;
            $this->and_op = self::$and_func;
        }
    }

    public function getStrConcatOperator()
    {
        return $this->str_concat_operator;
    }

    public function setStrConcatOperator($value)
    {
        $this->str_concat_operator = $value;
        if ($value == '&') {
            if ($this->string_literals_allowed) {
                $this->and_op = new MathParserParserFunction('&', 'mpAndStr', 2);
                $this->add_op = self::$add_func;
            } else {
                $this->add_op = new MathParserParserFunction('+', 'mpAddStr', 2);
                $this->and_op = self::$and_func;
            }
        } elseif ($value == '+') {
            if ($this->string_literals_allowed) {
                $this->add_op = new MathParserParserFunction('+', 'mpAddStr', 2);
                $this->and_op = self::$and_func;
            } else {
                $this->and_op = new MathParserParserFunction('&', 'mpAndStr', 2);
                $this->add_op = self::$add_func;
            }
        } else {
            throw new \Exception('Invalid string concat operator: ' . $value);
        }
    }

    public function getVariableResolver()
    {
        return $this->variable_resolver;
    }

    public function setVariableResolver($value)
    {
        $this->variable_resolver = $value;
        if ($value == null) {
            $this->dirty = true;
        }
    }

    public static function &getTranslationStrings()
    {
        if (!isset(self::$translator)) {
            self::$translator = [];

            $module = DynamicTools::getModule();
            self::$translator['ExpEmpty'] = $module->l('expression is empty.', 'mathparser');

            self::$translator['VarNtExst'] = $module->l('Variable %s does not exist.', 'mathparser');
            self::$translator['VarNtDbl'] = $module->l('Variable %s is not numeric.', 'mathparser');
            self::$translator['VarNtStr'] = $module->l('Variable %s is not a string.', 'mathparser');
            self::$translator['VarNtDblStr'] =
                $module->l('Variable %s should be a Double or a String. It is {1}.', 'mathparser');
            self::$translator['VarExt'] = $module->l('Variable %s already exists.', 'mathparser');
            self::$translator['NtVarNm'] = $module->l('%s is not a valid variable name.', 'mathparser');
            self::$translator['SntxErr'] = $module->l('Syntax error in expression %s', 'mathparser');
            self::$translator['NtFncNm'] = $module->l('%s is not a valid function name.', 'mathparser');
            self::$translator['FncExst'] = $module->l('Function %s already exists.', 'mathparser');
            self::$translator['WrngNPrms'] =
                $module->l('Function %s must accept at least 0 parameters not %s.', 'mathparser');

            self::$translator['WrngNPrms2'] =
                $module->l('Function %s requires at least %s parameters.', 'mathparser');
            self::$translator['ExpNtVld'] =
                $module->l('Sub expression "%s" in "%s" is not valid.', 'mathparser');

            self::$translator['InvNPrm'] =
                $module->l('Invalid number of parameters in "%s".', 'mathparser');
            self::$translator['BrcktMis'] =
                $module->l('Bracket mismatch in expression "%s" at index %s.', 'mathparser');
            self::$translator['MisBrckt'] =
                $module->l('Missing bracket ")" in expression "%s".', 'mathparser');
            self::$translator['InvConcatOper'] = $module->l(
                'Parameter #2 %s is not a numeric value. Cannot apply logical and (&) operator.
				If you want to concatenate 2 strings, use STR() function to convert first parameter to string.',
                'mathparser'
            );
            self::$translator['InvConcatOper2'] =
                $module->l(
                    'Parameter #2 %s is not a numeric value. Cannot apply plus ( + ) operator.
				    If you want to concatenate 2 strings, use STR() function to convert first parameter to string.',
                    'mathparser'
                );
            self::$translator['InvNum'] =
                $module->l('Cannot convert to a number. %s is not a numeric value.', 'mathparser');
        }
        foreach (self::$translator as $key => $item) {
            self::$translator[$key] = htmlspecialchars_decode($item);
        }

        return self::$translator;
    }

    public function setTranslationStrings($strings)
    {
        self::$translator = $strings;
    }

    public function getVariable($varname)
    {
        if ($varname == null) {
            throw new \Exception('Variable name cannot be null.');
        }
        $upcname = strtoupper($varname);
        $a_variable = $this->variables[$upcname];
        if ($a_variable == null) {
            throw new \Exception($this->getMessage1('VarNtExst', $varname));
        }

        return $a_variable->value;
    }

    public function setVariable($varname, $new_val, $fn_value_provider = null)
    {
        if ($varname == null) {
            throw new \Exception('Variable name cannot be null.');
        }
        if (is_numeric($new_val)) {
            $new_val = (float) $new_val;
        } else {
            if (!is_string($new_val)) {
                throw new \Exception('Variable should be floating point or string value: ' . $new_val);
            }
        }
        $upcname = strtoupper($varname);
        $existing = isset($this->variables[$upcname]) ? $this->variables[$upcname] : null;
        if ($existing != null) {
            $existing->value = $new_val;
        } else {
            if (!$this->isValidName($upcname)) {
                throw new \Exception($this->getMessage1('NtVarNm', $varname));
            }
            $var = new MathParserVariable($this, $upcname, $new_val, $fn_value_provider);
            $this->variables[$upcname] = $var;
            $this->dirty = true;
        }
    }

    public function evaluate()
    {
        if ($this->dirty) {
            $this->parse();
        }

        return $this->node->getValue();
    }

    public function parse()
    {
        if (!isset($this->expression) || !(\Tools::strlen($this->expression) > 0)) {
            $this->node = null;
            throw new \Exception($this->getMessage('ExpEmpty'));
        }

        $formula = $this->expression;
        $this->upperCase($formula);
        $len = \Tools::strlen($formula);
        $brackets = self::checkBrackets($formula);
        if ($brackets > -1 && $brackets < $len) {
            throw new MathParserParserException($this->getMessage2('BrcktMis', $formula, $brackets), \Tools::substr($formula, $brackets), $formula);
        } elseif ($brackets == $len) {
            throw new MathParserParserException($this->getMessage1('MisBrckt', $formula), $formula, $formula);
        }

        if (($this->node = $this->createParseTree($formula)) == null) {
            throw new MathParserParserException($this->getMessage2('ExpNtVld', $formula, $formula), $formula, $formula);
        }

        if ($this->optimization_on) {
            $this->optimize();
        }
        $this->dirty = false;
    }

    private function upperCase(&$c)
    {
        $len = \Tools::strlen($c);
        $chrArray = preg_split('//u', $c, -1, PREG_SPLIT_NO_EMPTY);
        $inside_string_const = false;
        for ($i = 0; $i < $len; ++$i) {
            $ch = $chrArray[$i];
            if ($ch == '"') {
                $inside_string_const = !$inside_string_const;
                continue;
            }
            if (!$inside_string_const) {
                $chrArray[$i] = strtoupper($ch);
            }
        }
        $c = implode('', $chrArray);
    }

    public function createVar($varname, $var_value, $value_provider = null)
    {
        $this->setVariable($varname, $var_value, $value_provider);
    }

    public function createFunc($new_func_name, $func_addr, $param_count, $add_fields = false)
    {
        if ($new_func_name == null) {
            throw new \Exception('Function name cannot be null.');
        }
        if ($func_addr == null) {
            throw new \Exception('Function implementation cannot be null.');
        }

        $upcname = strtoupper($new_func_name);
        if (!$this->isValidName($upcname)) {
            throw new \Exception($this->getMessage1('NtFncNm', $new_func_name));
        }

        if ($this->isFunction($upcname)) {
            throw new \Exception($this->getMessage1('FncExst', $new_func_name));
        } elseif ($param_count < -1) {
            throw new \Exception($this->getMessage2('WrngNPrms', $new_func_name, (string) $param_count));
        } else {
            $func = new MathParserParserFunction($upcname, $func_addr, $param_count, $add_fields);
            $this->functions[$upcname] = $func;
        }
        $this->dirty = true;
    }

    public function createDefaultFuncs()
    {
        $this->createFunc('SQR', 'mpSquare', 1);
        $this->createFunc('SIN', 'sin', 1);
        $this->createFunc('COS', 'cos', 1);
        $this->createFunc('ATAN', 'atan', 1);
        $this->createFunc('SINH', 'sinh', 1);
        $this->createFunc('COSH', 'cosh', 1);
        $this->createFunc('COTAN', 'mpCotan', 1);
        $this->createFunc('TAN', 'tan', 1);
        $this->createFunc('EXP', 'exp', 1);
        $this->createFunc('LN', 'log', 1);
        $this->createFunc('LOG', 'log10', 1);
        $this->createFunc('SQRT', 'sqrt', 1);
        $this->createFunc('ABS', 'abs', 1);
        $this->createFunc('SIGN', 'mpSign', 1);
        $this->createFunc('TRUNC', 'mpTrunc', 1);
        $this->createFunc('CEIL', 'ceil', 1);
        $this->createFunc('FLOOR', 'floor', 1);
        $this->createFunc('RND', 'mpRand', 0);
        $this->createFunc('ROUND', 'mpRound', 2);
        $this->createFunc('ROUNDUP', 'mpRoundUp', 2);
        $this->createFunc('ROUNDDOWN', 'mpRoundDown', 2);
        $this->createFunc('VAL', 'mpFloat', 1);

        $this->createFunc('POW', 'pow', 2);
        $this->createFunc('LOGN', 'mpLogn', 2);
        $this->createFunc('MIN', 'mpMin', -1);
        $this->createFunc('MAX', 'mpMax', -1);
        $this->createFunc('MOD', 'modulo', 2);

        $this->createFunc('IF', 'ifFunc', 3);

        $this->createFunc('STRLEN', 'strLength', 1);
        $this->createFunc('STR', 'str', 1);

        $this->createFunc('CHECK', 'checkFunc', 1);

        $this->createFunc('SUBSTR', 'mpSubstr', 3);
        $this->createFunc('REPLACE', 'mpReplace', 3);
        $this->createFunc('CONCAT', 'mpConcat', -1);
        $this->createFunc('TRIM', 'trim', 1);
        $this->createFunc('RTRIM', 'rtrim', 1);
        $this->createFunc('LTRIM', 'ltrim', 1);
        $this->createFunc('CHR', 'chr', 1);
        $this->createFunc('NUM', 'mpNum', 1);
        $this->createFunc('SUM', 'sumFunc', -1);

        $this->createFunc('GRID', 'mpGrid', -1);

        $this->createFunc('BINARY_AND', 'mpBitwiseAnd', 2);
        $this->createFunc('BINARY_OR', 'mpBitwiseOr', 2);
        $this->createFunc('CONTAINS', 'mpContains', 2);

        $this->createFunc('PRICE', 'mpPrice', 1);
        $this->createFunc('LABEL', 'mpLabel', 1, true);
        $this->createFunc('REF', 'mpRef', 1, true);
        $this->createFunc('NUM_SELECTED', 'mpNumSelected', 1, true);
    }

    public function createDefaultVars()
    {
        try {
            $this->createVar('PI', 3.14159265358979, null);
            $this->createVar('X', 0.0, null);
            $this->createVar('Y', 0.0, null);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteVar($var_name)
    {
        if ($var_name == null) {
            throw new \Exception('Variable name cannot be null.');
        }

        $upcname = strtoupper($var_name);
        unset($this->variables[$upcname]);
        $this->dirty = true;
    }

    public function deleteFunc($func_name)
    {
        if ($func_name == null) {
            throw new \Exception('Function name cannot be null.');
        }
        $upcname = strtoupper($func_name);
        unset($this->functions[$upcname]);

        $this->dirty = true;
    }

    public function deleteAllVars()
    {
        $this->variables = [];
        $this->dirty = true;
    }

    public function deleteAllFuncs()
    {
        $this->functions = [];
        $this->dirty = true;
    }

    public function freeParseTree()
    {
        $this->node = null;
        $this->dirty = true;
    }

    public function isVariableUsed($var_name)
    {
        if ($var_name == null) {
            throw new \Exception('Variable name cannot be null.');
        }

        if ($this->dirty) {
            $this->parse();
        }

        $var_name = strtoupper($var_name);

        return $this->node->isVariableUsed($var_name);
    }

    public function isFunctionUsed($func_name)
    {
        if ($func_name == null) {
            throw new \Exception('Function name cannot be null.');
        }
        if ($this->dirty) {
            $this->parse();
        }

        $func_name = strtoupper($func_name);

        return $this->node->isFunctionUsed($func_name);
    }

    public function isVariable($var_name)
    {
        if ($var_name == null) {
            throw new \Exception('Variable name cannot be null.');
        }
        $var_name = strtoupper($var_name);

        return isset($this->variables[$var_name]);
    }

    public function isFunction($func_name)
    {
        if ($func_name == null) {
            throw new \Exception('Function name cannot be null.');
        }
        $func_name = strtoupper($func_name);

        return isset($this->functions[$func_name]);
    }

    public function getVariablesUsed()
    {
        if ($this->dirty && $this->expression != null && \Tools::strlen($this->expression) > 0) {
            $this->parse();
        }

        return MathParser::findVariablesUsed($this->node);
    }

    public static function findVariablesUsed($a_node)
    {
        $list = [];
        if ($a_node instanceof MathParserUnknownVarNode) {
            $list[] = $a_node->var_name;
        } elseif ($a_node instanceof MathParserVarNode) {
            $list[] = $a_node->var_name;
        } elseif ($a_node instanceof MMathparserNParamNode) {
            foreach ($a_node->nodes as $n) {
                if ($n instanceof MathParserUnknownVarNode) {
                    $list[] = $n->var_name;
                } else {
                    $list = array_merge($list, MathParser::findVariablesUsed($n));
                }
            }
        }

        return $list;
    }

    public function getVariables()
    {
        return array_keys($this->variables);
    }

    public function getFunctions()
    {
        return array_keys($this->functions);
    }

    public function optimize()
    {
        $this->node = self::optimizeNode($this->node);
    }

    public function getValue()
    {
        return $this->evaluate();
    }

    public function getValueAsDouble()
    {
        $obj = $this->evaluate();

        return (float) $obj;
    }

    public function getValueAsString()
    {
        return (string) $this->evaluate();
    }

    private function isValidChar($index, $c)
    {
        if ($index == 0) {
            if ($c >= 'A' && $c <= 'Z') {
                return true;
            }
            if ($c == '_') {
                return true;
            }

            return false;
        }
        if (($c >= '0' && $c <= '9') || ($c >= 'A' && $c <= 'Z')) {
            return true;
        }
        if ($c == '_') {
            return true;
        }

        return false;
    }

    private function isValidName($name)
    {
        $len = \Tools::strlen($name);
        $chrArray = preg_split('//u', $name, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < $len; ++$i) {
            if (!$this->isValidChar($i, $chrArray[$i])) {
                return false;
            }
        }

        return true;
    }

    protected static function checkBrackets($formula)
    {
        $n = 0;
        $len = \Tools::strlen($formula);
        $inside_string_const = false;
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < $len; ++$i) {
            $ch = $chrArray[$i];
            if ($ch == '\"') {
                $inside_string_const = !$inside_string_const;
                continue;
            }
            if (!$inside_string_const) {
                if ($ch == '(') {
                    ++$n;
                } elseif ($ch == ')') {
                    --$n;
                }

                if ($n < 0) {
                    return $i;
                }
            }
        }

        return ($n == 0) ? -1 : $len;
    }

    protected static function removeOuterBrackets($formula)
    {
        $temp = $formula;
        $len = \Tools::strlen($temp);
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        while (($len > 2) && ($chrArray[0] == '(') && ($chrArray[$len - 1] == ')')) {
            $temp = trim(\Tools::substr($temp, 1, $len - 2));
            if (self::checkBrackets($temp) == -1) {
                $formula = $temp;
            }
            $len = \Tools::strlen($temp);
            $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        }

        return $formula;
    }

    protected static function isValidDouble($formula, &$dbl_val)
    {
        if (is_numeric($formula)) {
            $dbl_val = (float) $formula;

            return true;
        }

        return false;
    }

    private static function getStringLiteral($formula)
    {
        $len = \Tools::strlen($formula);
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        if (!($len > 1 && $chrArray[0] == '"' && $chrArray[$len - 1] == '"')) {
            return null;
        }
        if ($formula == '""') {
            return $formula;
        }
        $temp = \Tools::substr($formula, 1, $len - 2);
        if (self::checkEscapes($temp)) {
            return str_replace('""', '"', $temp);
        }

        return null;
    }

    private static function checkEscapes($formula)
    {
        $len = \Tools::strlen($formula);
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        $ch = null;
        $inside_string_const = false;
        for ($i = 0; $i < $len; ++$i) {
            $ch = $chrArray[$i];
            if ($ch == '"') {
                if ($inside_string_const) {
                    return false;
                }
                ++$i;
                if ($i < $len && $chrArray[$i] == '"') {
                    $inside_string_const = true;
                } else {
                    return false;
                }
                continue;
            }
            $inside_string_const = false;
        }

        return true;
    }

    protected function createParseTree($exp_to_parse)
    {
        $exp_to_parse = trim($exp_to_parse);
        if (($len = \Tools::strlen($exp_to_parse)) == 0) {
            return null;
        }

        $formula = $this->removeOuterBrackets($exp_to_parse);

        if (\Tools::strlen($formula) != $len) {
            $formula = trim($formula);
            if (\Tools::strlen($formula) == 0) {
                return null;
            }
        }
        $dbl_val = null;
        if ($this->isValidDouble($formula, $dbl_val)) {
            return new MathParserBasicNode($dbl_val);
        }

        if ($this->string_literals_allowed) {
            $temp_formula = $this->getStringLiteral($formula);
            if ($temp_formula != null) {
                return new MathParserBasicNode($temp_formula);
            }
        }
        $var_node = $this->createVarNode($formula);
        if ($var_node != null) {
            return $var_node;
        }
        $left_node = null;
        $right_node = null;
        $last_oper = $this->findLastOper($formula);

        $func_addr = null;
        if (!($last_oper > 0)) {
            $param = null;
            if ($this->isOneParamFunc($formula, $func_addr, $param, $last_oper)) {
                $left_node = $this->createParseTree($param);
                if ($left_node == null) {
                    throw new MathParserParserException($this->getMessage2('ExpNtVld', $param, $formula), $param, $formula);
                }
                if ($func_addr != null) {
                    return new MMathparserNParamNode([$left_node], $func_addr, $this->input_fields);
                }
            }
        }

        $param_left = null;
        $param_right = null;
        if ($this->isTwoParamFunc($formula, $func_addr, $param_left, $param_right, $last_oper)) {
            if (($left_node = $this->createParseTree($param_left)) == null) {
                throw new MathParserParserException($this->getMessage2('ExpNtVld', $param_left, $formula), $param_left, $formula);
            }
            if (($right_node = $this->createParseTree($param_right)) == null) {
                throw new MathParserParserException($this->getMessage2('ExpNtVld', $param_right, $formula), $param_right, $formula);
            }

            if ($func_addr != null) {
                return new MMathparserNParamNode([$left_node, $right_node], $func_addr, $this->input_fields);
            }
        }

        $parms = null;

        if ($this->isNParamFunc($formula, $func_addr, $parms)) {
            if (!isset($parms)) {
                throw new MathParserParserException($this->getMessage1('InvNPrm', $formula), $formula, $formula);
            }
            $n_param = count($parms);
            $nodes = [];
            for ($i = 0; $i < $n_param; ++$i) {
                if (($left_node = $this->createParseTree($parms[$i])) == null) {
                    throw new MathParserParserException($this->getMessage2('ExpNtVld', $parms[$i], $formula), $parms[$i], $formula);
                }
                $nodes[$i] = $left_node;
            }
            if ($func_addr != null) {
                return new MMathparserNParamNode($nodes, $func_addr, $this->input_fields);
            }
        }

        return null;
    }

    private static function findLastOper($formula)
    {
        $precedence = 13;
        $bracket_level = 0;
        $result = -1;
        $len = \Tools::strlen($formula);
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        $last_was_operator = 0;
        $inside_string_literal = false;
        for ($i = 0; $i < $len; ++$i) {
            $current_ch = $chrArray[$i];
            if ($current_ch === '"') {
                $inside_string_literal = !$inside_string_literal;
                $last_was_operator = 0;
                continue;
            }
            if ($inside_string_literal) {
                continue;
            }
            if ($last_was_operator > 2) {
                return -1;
            }
            switch ($current_ch) {
                case ')':
                    --$bracket_level;
                    $last_was_operator = 0;
                    break;
                case '(':
                    ++$bracket_level;
                    $last_was_operator = 0;
                    break;

                case '|':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 1) {
                            $precedence = 1;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;

                case '&':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 2) {
                            $precedence = 2;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;

                case '!':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 3) {
                            $precedence = 3;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;

                case '=':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 4) {
                            $precedence = 4;
                            $result = $i;
                        }
                    }
                    if ($last_was_operator > 0) {
                        $prev_oper_index = $i - $last_was_operator;
                        if ($chrArray[$prev_oper_index] == '<' || $chrArray[$prev_oper_index] == '>') {
                            $prev_oper_index = $prev_oper_index;
                            break;
                        }
                    }

                    ++$last_was_operator;
                    break;

                case '>':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 5) {
                            $precedence = 5;
                            $result = $i;
                        }
                    }
                    if ($last_was_operator > 0) {
                        if ($chrArray[$i - $last_was_operator] == '<') {
                            $i = $i;
                            break;
                        }
                    }
                    ++$last_was_operator;
                    break;

                case '<':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 5) {
                            $precedence = 5;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;

                case '-':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 7) {
                            $precedence = 7;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;
                case '+':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 7) {
                            $precedence = 7;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;
                case '%':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 9) {
                            $precedence = 9;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;
                case '/':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 9) {
                            $precedence = 9;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;
                case '*':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 9) {
                            $precedence = 9;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;
                case '^':
                    if (!($bracket_level > 0 || $last_was_operator > 0)) {
                        if ($precedence >= 12) {
                            $precedence = 12;
                            $result = $i;
                        }
                    }
                    ++$last_was_operator;
                    break;

                case 'E':
                    if ($i > 0 && $last_was_operator == 0) {
                        $ch = $chrArray[$i - 1];
                        if ($ch >= '0' && $ch <= '9') {
                            $j = $i;
                            while ($j > 0) {
                                --$j;
                                $ch = $chrArray[$j];
                                if ($ch == '.' || ($ch >= '0' && $ch <= '9')) {
                                    continue;
                                }
                                if ($ch == '_' || ($ch >= 'A' && $ch <= 'Z')) {
                                    $last_was_operator = 0;
                                    break;
                                }
                                ++$last_was_operator;
                                break;
                            }
                            if ($j == 0 && ($ch >= '0' && $ch <= '9')) {
                                ++$last_was_operator;
                            }
                        } else {
                            $last_was_operator = 0;
                        }
                    } else {
                        $last_was_operator = 0;
                    }
                    break;
                case ' ':
                    break;
                default:
                    $last_was_operator = 0;
                    break;
            }
        }

        return $result;
    }

    private function isTwoParamFunc($formula, &$func_addr, &$param_left, &$param_right, $curr_char)
    {
        $func_addr = null;
        $param_left = null;
        $param_right = null;
        $len = \Tools::strlen($formula);
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);

        if ($curr_char > 0) { // if function in question is an operand
            if ($curr_char > $len - 2) {
                return false;
            }
            $curr_ch = $chrArray[$curr_char];

            // was it an operand also? we want to find <>, >=, <=
            if ($curr_ch == '!') {
                $next_ch = $chrArray[$curr_char + 1]; // look ahead.
                if ($next_ch == '=') {
                    $func_addr = self::$notequals_func;
                    $param_left = \Tools::substr($formula, 0, $curr_char);
                    $param_right = \Tools::substr($formula, $curr_char + 2);

                    return true;
                }
            } elseif ($curr_ch == '<') {
                $next_ch = $chrArray[$curr_char + 1]; // look ahead.
                if ($next_ch == '>') {
                    $func_addr = self::$notequals_func;
                    $param_left = \Tools::substr($formula, 0, $curr_char);
                    $param_right = \Tools::substr($formula, $curr_char + 2);
                } elseif ($next_ch == '=') {
                    $func_addr = self::$ltequals_func;
                    $param_left = \Tools::substr($formula, 0, $curr_char);
                    $param_right = \Tools::substr($formula, $curr_char + 2);
                } else {
                    $func_addr = self::$lt_func; // default case.
                    $param_left = \Tools::substr($formula, 0, $curr_char);
                    $param_right = \Tools::substr($formula, $curr_char + 1);
                }

                if (!(\Tools::strlen($param_left) > 0)) {
                    return false;
                }
                if (!(\Tools::strlen($param_right) > 0)) {
                    return false;
                }

                return true; // all output is assigned, now we return true.
            } elseif ($curr_ch == '>') {
                $next_ch = $chrArray[$curr_char + 1];
                if ($next_ch == '=') {
                    $func_addr = self::$gtequals_func;
                    $param_left = \Tools::substr($formula, 0, $curr_char);
                    $param_right = \Tools::substr($formula, $curr_char + 2);
                } else {
                    $func_addr = self::$gt_func; // default case.
                    $param_left = \Tools::substr($formula, 0, $curr_char);
                    $param_right = \Tools::substr($formula, $curr_char + 1);
                }
                if (!(\Tools::strlen($param_left) > 0)) {
                    return false;
                }

                if (!(\Tools::strlen($param_right) > 0)) {
                    return false;
                }

                return true; // all output is assigned, now we return true.
            } else {
                $param_left = \Tools::substr($formula, 0, $curr_char);
                if (!(\Tools::strlen($param_left) > 0)) {
                    return false;
                }

                $param_right = \Tools::substr($formula, $curr_char + 1);
                if (!(\Tools::strlen($param_right) > 0)) {
                    return false;
                }

                switch ($chrArray[$curr_char]) {
                    // analytical operators:
                    case '+':
                        $func_addr = $this->add_op;
                        break;
                    case '-':
                        $func_addr = self::$subtract_func;
                        break;
                    case '*':
                        $func_addr = self::$mult_func;
                        break;
                    case '/':
                        $func_addr = self::$div_func;
                        break;
                    case '^':
                        $func_addr = self::$power_func;
                        break;
                    case '%':
                        $func_addr = self::$mod_func;
                        break;

                    case '<':
                        $func_addr = self::$lt_func;
                        break;
                    case '>':
                        $func_addr = self::$gt_func;
                        break;
                    case '=':
                        $func_addr = self::$equals_func;
                        break;
                    case '&':
                        $func_addr = $this->and_op;
                        break;
                    case '|':
                        $func_addr = self::$or_func;
                        break;
                }
            }

            return true; // all output is assigned, now we return true.
        }
        // if we reach here, result is false
        // if main operation is not an operand but a function
        // $bracket_level;
        // $param_start;
        $temp = '';
        if ($chrArray[$len - 1] == ')') {  // last character must be brackets closing function param list
            $i = 0;
            while ($this->isValidChar($i, $chrArray[$i])) {
                $temp .= $chrArray[$i];
                ++$i;
            }
            while ($chrArray[$i] == ' ') {
                ++$i;
            }

            if (($chrArray[$i] == '(') && ($i < $len - 1)) {
                $func = $this->functions[$temp];
                if ($func != null && $func->param_count == 2) {
                    $func_addr = $func;
                    $param_start = $i + 1;
                    $bracket_level = 1;
                    $inside_string_literal = false;
                    while (!($i > $len - 1 - 1)) { // last character is a ')', that's why we use i>len - 1
                        ++$i;
                        switch ($chrArray[$i]) {
                            case '"':
                                $inside_string_literal = !$inside_string_literal;
                                break;
                            case '(':
                                if (!$inside_string_literal) {
                                    ++$bracket_level;
                                }
                                break;
                            case ')':
                                if (!$inside_string_literal) {
                                    --$bracket_level;
                                }
                                break;
                            case ',':
                                if ((!$inside_string_literal) && (1 == $bracket_level) && ($i < $len - 2)) {
                                    // last character is a ')', that's why we use i>Len-2
                                    $param_left = \Tools::substr($formula, $param_start, $i - $param_start);
                                    $param_right = \Tools::substr($formula, $i + 1, $len - 1 - ($i + 1));

                                    // last character is a ')', that's why we use Len-1-i
                                    return true; // we are sure that it is a two parameter function
                                }
                                break;
                        }
                    }
                }
            }
        }

        return false; // means we could not find it
    }

    private function isOneParamFunc($formula, &$func_addr, &$param, $curr_char)
    {
        $func_addr = null;
        $param = null;
        $param_start = null;
        $len = \Tools::strlen($formula);
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        if ($curr_char == 0) {
            $param = \Tools::substr($formula, 1);
            if (!(\Tools::strlen($param) > 0)) {
                return false;
            }

            switch ($chrArray[$curr_char]) {
                case '+':
                    $func_addr = self::$unaryadd_func;
                    break;
                case '-':
                    $func_addr = self::$negate_func;
                    break;
                case '!':
                    $func_addr = self::$not_func;
                    break;
                default:
                    $func_addr = null;

                    return false;
            }

            return true;
        }
        if ($chrArray[$len - 1] == ')') {
            $i = 0;
            $temp = '';
            while ($this->isValidChar($i, $chrArray[$i])) {
                $temp .= $chrArray[$i];
                ++$i;
            }
            while ($chrArray[$i] == ' ') {
                ++$i;
            }
            if (($chrArray[$i] == '(') && ($i < $len - 2)) {
                $func = $this->functions[$temp];
                if ($func != null && $func->param_count == 1) {
                    $func_addr = $func;
                    $param_start = $i + 1;
                    $param = \Tools::substr($formula, $param_start, $len - 1 - $param_start);

                    return true;
                }
            }
        }

        return false;
    }

    private function isNParamFunc($formula, &$func_addr, &$parms)
    {
        $func_addr = null;
        $parms = null;

        $len = \Tools::strlen($formula);
        $chrArray = preg_split('//u', $formula, -1, PREG_SPLIT_NO_EMPTY);
        $temp = '';
        if ($chrArray[$len - 1] == ')') {
            $i = 0;
            while ($this->isValidChar($i, $chrArray[$i])) {
                $temp .= $chrArray[$i];
                ++$i;
            }
            while ($chrArray[$i] == ' ') {
                ++$i;
            }
            if (($chrArray[$i] == '(') && ($i < $len - 1)) {
                $func_addr = $this->functions[$temp];

                if ($func_addr != null) {
                    $n_params = $func_addr->param_count;
                    if ($n_params > -1) {
                        $parms = [];
                        if ($n_params == 0) {
                            if ($chrArray[$i + 1] == ')') {
                                return true;
                            }
                        }
                        $param_start = $i + 1;
                        $bracket_level = 1;
                        $p_index = 0;
                        $inside_string_literal = false;
                        while (!($i > $len - 1 - 1)) {
                            ++$i;
                            switch ($chrArray[$i]) {
                                case '"':
                                    $inside_string_literal = !$inside_string_literal;
                                    break;
                                case '(':
                                    if (!$inside_string_literal) {
                                        ++$bracket_level;
                                    }
                                    break;
                                case ')':
                                    if (!$inside_string_literal) {
                                        --$bracket_level;
                                    }
                                    break;
                                case ',':
                                    if ((!$inside_string_literal) && (1 == $bracket_level) && ($i < $len - 2)) {
                                        if ($n_params > -1 && !($p_index < $n_params)) {
                                            return false;
                                        }
                                        $parms[$p_index++] = \Tools::substr($formula, $param_start, $i - $param_start);

                                        if ($p_index == $n_params - 1) {
                                            $parms[$p_index] = \Tools::substr($formula, $i + 1, $len - 1 - ($i + 1));

                                            return true;
                                        }
                                        $param_start = $i + 1;
                                    }
                                    break;
                            }
                        }
                    } else {
                        $list = [];
                        $param_start = $i + 1;
                        $bracket_level = 1;
                        $inside_string_literal = false;
                        while (!($i > $len - 1 - 1)) {
                            ++$i;
                            switch ($chrArray[$i]) {
                                case '"':
                                    $inside_string_literal = !$inside_string_literal;
                                    break;
                                case '(':
                                    if (!$inside_string_literal) {
                                        ++$bracket_level;
                                    }
                                    break;
                                case ')':
                                    if (!$inside_string_literal) {
                                        --$bracket_level;
                                    }
                                    break;
                                case ',':
                                    if (!$inside_string_literal && (1 == $bracket_level) && ($i < $len - 2)) {
                                        $list[] = \Tools::substr($formula, $param_start, $i - $param_start);
                                        $param_start = $i + 1;
                                    }
                                    break;
                            }
                        }
                        $remaining = trim(\Tools::substr($formula, $param_start, $len - 1 - $param_start));
                        if (\Tools::strlen($remaining) > 0) {
                            $list[] = $remaining;
                        }

                        $parms = $list;

                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function optimizeNode($a_node)
    {
        $a_node->optimize();
        if ($a_node instanceof MMathparserNParamNode) {
            $count = count($a_node->nodes);
            for ($i = 0; $i < $count; ++$i) {
                if (!($a_node->nodes[$i] instanceof MathParserBasicNode)) {
                    return $a_node;
                }
            }

            return new MathParserBasicNode($a_node->getValue());
        }

        return $a_node;
    }

    public function getexpression()
    {
        return $this->expression;
    }

    public function setExpression($value)
    {
        if ($value != $this->expression) {
            $this->expression = $value;
            $this->dirty = true;
        }
    }

    public function setFields($input_fields)
    {
        $this->input_fields = $input_fields;
    }

    public function isoptimizationOn()
    {
        return $this->optimization_on;
    }

    public function setoptimizationOn($value)
    {
        $this->optimization_on = $value;
    }

    protected function createVarNode($var_name)
    {
        $variable = isset($this->variables[$var_name]) ? $this->variables[$var_name] : null;
        if ($variable != null) {
            return new MathParserVarNode($variable, $var_name);
        } elseif ($this->variable_resolver != null) {
            $len = \Tools::strlen($var_name);
            $chrArray = preg_split('//u', $var_name, -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0; $i < $len; ++$i) {
                $ch = $chrArray[$i];
                if (!$this->isValidChar($i, $ch)) {
                    return null;
                }
            }

            return new MathParserUnknownVarNode($this, $var_name);
        }

        return null;
    }

    public static function getMessage($key)
    {
        try {
            if (self::$translator == null) {
                self::getTranslationStrings();
            }
            $s = self::$translator[$key];
            if ($s == null) {
                $s = $key;
            }

            return $s;
        } catch (\Exception $e) {
            return $key;
        }
    }

    public static function getMessage1($key, $param)
    {
        $temp = self::getMessage($key);

        return sprintf($temp, $param);
    }

    public static function getMessage2($key, $param0, $param1)
    {
        $temp = self::getMessage($key);

        return sprintf($temp, $param0, $param1);
    }
}
