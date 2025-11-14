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

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\DynamicEquationHelper;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicInputField;
use DynamicProduct\libs\parser\MathParser;
use DynamicProduct\libs\parser\MathParserParserException;

function mpAdd($p1, $p2)
{
    return (float) $p1 + (float) $p2;
}

function mpAddStr($p1, $p2)
{
    if (is_string($p1)) {
        return $p1 . $p2;
    } elseif (!is_numeric($p2)) {
        throw new Exception(MathParser::getMessage1('InvConcatOper2', $p2));
    }

    return $p1 + $p2;
}

function mpSubtract($p1, $p2)
{
    return (float) $p1 - (float) $p2;
}

function mpMult($p1, $p2)
{
    return (float) $p1 * (float) $p2;
}

function mpDiv($p1, $p2)
{
    return (float) $p1 / (float) $p2;
}

function mpPower($p1, $p2)
{
    return pow((float) $p1, (float) $p2);
}

function mpMod($p1, $p2)
{
    return (float) $p1 % (float) $p2;
}

function mpNotequals($p1, $p2)
{
    return ($p1 != $p2) ? 1 : 0;
}

function mpEquals($p1, $p2)
{
    return ($p1 == $p2) ? 1 : 0;
}

function mpLt($p1, $p2)
{
    return ($p1 < $p2) ? 1 : 0;
}

function mpGt($p1, $p2)
{
    return ($p1 > $p2) ? 1 : 0;
}

function mpLtequals($p1, $p2)
{
    return ($p1 <= $p2) ? 1 : 0;
}

function mpGtequals($p1, $p2)
{
    return ($p1 >= $p2) ? 1 : 0;
}

function mpUnaryadd($p1)
{
    return $p1;
}

function mpNegate($p1)
{
    return -$p1;
}

function mpNot($p1)
{
    return ((!$p1) == 0) ? 0 : 1;
}

function mpAnd($p1, $p2)
{
    return (($p1 == 0 ? false : true) && ($p2 == 0 ? false : true)) ? 1 : 0;
}

function mpAndStr($p1, $p2)
{
    if (is_string($p1)) {
        return $p1 . $p2;
    } elseif (!is_numeric($p2)) {
        throw new Exception(MathParser::getMessage1('InvConcatOper', $p2));
    }

    return $p1 && $p2;
}

function mpOr($p1, $p2)
{
    return (($p1 == 0 ? false : true) || ($p2 == 0 ? false : true)) ? 1 : 0;
}

function mpBitwiseAnd($p1, $p2)
{
    return $p1 & $p2;
}

function mpBitwiseOr($p1, $p2)
{
    return $p1 | $p2;
}

function sumFunc()
{
    $p = func_get_args();
    $count = func_num_args();
    $tot = 0;
    for ($i = 0; $i < $count; ++$i) {
        $tot += $p[$i];
    }

    return $tot;
}

function mpMax()
{
    $p = func_get_args();
    $count = func_num_args();
    if ($count < 1) {
        throw new MathParserParserException(MathParser::getMessage2('WrngNPrms2', 'MAX', '1'), 'max', 'max');
    }

    return max($p);
}

function mpMin()
{
    $p = func_get_args();
    $count = func_num_args();
    if ($count < 1) {
        throw new MathParserParserException(MathParser::getMessage2('WrngNPrms2', 'MIN', '1'), 'min', 'min');
    }

    return min($p);
}

function ifFunc($cond, $true_case, $false_case)
{
    return $cond != 0 ? $true_case : $false_case;
}

function mpNum($val)
{
    if (is_numeric($val)) {
        return (float) $val;
    }
    throw new MathParserParserException(MathParser::getMessage2('InvNum', $val), 'NUM', 'NUM');
}

function checkFunc($val)
{
    return (int) (Tools::strlen($val) > 0);
}

function mpContains($val, $partial)
{
    return (int) (strpos($val, $partial) !== false);
}

function strLength($val)
{
    return (int) Tools::strlen($val);
}

function mpSubstr($str, $start, $length = false)
{
    return Tools::substr($str, $start, $length);
}

function mpReplace($original, $replaced, $replacement)
{
    return str_replace($replaced, $replacement, $original);
}

function mpGrid($filename, $row_value, $column_value, $default = 0)
{
    return DynamicEquationHelper::getGridValue($filename, $row_value, $column_value, $default);
}

function mpSquare($val)
{
    return $val * $val;
}

function mpCotan($val)
{
    return 1.0 / tan($val);
}

function mpSign($val)
{
    if ($val > 0) {
        return 1;
    }
    if ($val < 0) {
        return -1;
    }

    return 0;
}

function mpTrunc($val)
{
    if ($val > 0) {
        return floor($val);
    }
    if ($val < 0) {
        return floor($val) + 1;
    }

    return 0;
}

function mpFloat($val)
{
    return (float) $val;
}

function mpLogn($base, $val)
{
    return log($val) / log($base);
}

function mpRand()
{
    $max = getrandmax();

    return rand(0, $max) / $max;
}

function mpRound($val, $precision = 0)
{
    return Tools::ps_round($val, (int) $precision);
}

function mpRoundUp($val, $precision = 0)
{
    return Tools::ps_round($val, (int) $precision, PS_ROUND_UP);
}

function mpRoundDown($val, $precision = 0)
{
    return Tools::ps_round($val, (int) $precision, PS_ROUND_DOWN);
}

function mpConcat()
{
    $p = func_get_args();
    $s = '';
    foreach ($p as $val) {
        $s .= $val;
    }

    return $s;
}

function mpPrice($val)
{
    return DynamicTools::formatPrice($val);
}

/**
 * @param $id_field
 * @param DynamicInputField[] $input_fields
 *
 * @return mixed|string
 */
function mpLabel($id_field, $input_fields = null)
{
    if (empty($input_fields)) {
        return 0;
    }

    $input_fields_array = array_values($input_fields);
    $index = array_search((int) $id_field, array_column($input_fields_array, 'id_field'));

    if ($index === false || !isset($input_fields_array[$index])) {
        return 0;
    }

    $input_field = $input_fields_array[$index];

    $selected_options = $input_field->selected_options;
    if (empty($selected_options)) {
        return '';
    }

    $selected_option = (int) $selected_options[0];

    if (!$selected_option) {
        return '';
    }

    return DynamicField::getLabelForDefaultLang($selected_option, $input_field->getDynamicField()['type']);
}

/**
 * @param $id_field
 * @param DynamicInputField[] $input_fields
 *
 * @return string
 */
function mpRef($id_field, $input_fields = null)
{
    if (empty($input_fields)) {
        return '';
    }

    $input_fields_array = array_values($input_fields);
    $index = array_search((int) $id_field, array_column($input_fields_array, 'id_field'));

    if ($index === false || !isset($input_fields_array[$index])) {
        return '';
    }

    $input_field = $input_fields_array[$index];

    return $input_field->setSKU();
}

/**
 * @param $id_field
 * @param $input_fields
 *
 * @return int
 */
function mpNumSelected($id_field, $input_fields = null)
{
    if (empty($input_fields)) {
        return 0;
    }

    $input_fields_array = array_values($input_fields);
    $index = array_search((int) $id_field, array_column($input_fields_array, 'id_field'));

    if ($index === false || !isset($input_fields_array[$index])) {
        return 0;
    }

    $input_field = $input_fields_array[$index];

    return count($input_field->selected_options);
}
