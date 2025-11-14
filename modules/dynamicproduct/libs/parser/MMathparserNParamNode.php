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

use DynamicProduct\classes\models\DynamicInputField;

class MMathparserNParamNode extends MathParserNode
{
    public $nodes;
    public $fptr;
    /** @var DynamicInputField[] */
    public $input_fields;

    public function __construct($n, $func_addr, $input_fields = [])
    {
        $this->nodes = &$n;
        $this->fptr = $func_addr;
        $this->input_fields = $input_fields;
    }

    public function getValue()
    {
        if ($this->fptr->name == 'IF') {
            $cond = $this->nodes[0]->getValue();
            if ($cond == 0) {
                return $this->nodes[2]->getValue();
            } else {
                return $this->nodes[1]->getValue();
            }
        } else {
            $p = [];
            $count = count($this->nodes);
            for ($i = 0; $i < $count; ++$i) {
                $value = $this->nodes[$i]->getValue();
                if ($value === '""') {
                    $value = '';
                }
                $p[] = $value;
            }

            if ($this->fptr->add_fields) {
                $p[] = $this->input_fields;
            }

            return call_user_func_array($this->fptr->event_handler, $p);
        }
    }

    public function getValueAsDouble()
    {
        $value = $this->getValue();
        if (is_float($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        throw new \Exception('Value is not numeric: ' . $value);
    }

    public function getValueAsString()
    {
        return (string) $this->getValue();
    }

    public function isFunctionUsed($name)
    {
        if ($this->fptr->name === $name) {
            return true;
        }
        foreach ($this->nodes as $n) {
            if ($n->isFunctionUsed($name)) {
                return true;
            }
        }

        return false;
    }

    public function isVariableUsed($name)
    {
        foreach ($this->nodes as $n) {
            if ($n->isVariableUsed($name)) {
                return true;
            }
        }

        return false;
    }

    public function optimize()
    {
        $i = 0;
        foreach ($this->nodes as $n) {
            $this->nodes[$i] = MathParser::optimizeNode($n);
            ++$i;
        }
    }
}
