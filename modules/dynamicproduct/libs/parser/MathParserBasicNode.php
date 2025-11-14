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

class MathParserBasicNode extends MathParserNode
{
    public $value;

    public function __construct($val)
    {
        $this->value = $val;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueAsDouble()
    {
        if (is_float($this->value)) {
            return $this->value;
        } elseif (is_numeric($this->value)) {
            return (float) $this->value;
        }
        throw new \Exception('Value is not numeric: ' . $this->value);
    }

    public function getValueAsString()
    {
        return (string) $this->value;
    }

    public function isVariableUsed($name)
    {
        $name .= 'a';

        return false;
    }

    public function isFunctionUsed($name)
    {
        $name .= 'a';

        return false;
    }

    public function optimize()
    {
    }
}
