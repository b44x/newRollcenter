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

class MathParserVariable
{
    private $parser;
    public $name;
    public $value;
    public $value_provider;

    public function getRuntimeValue()
    {
        return !isset($this->value_provider) ? $this->value : $this->value_provider($this->parser, $this->name);
    }

    public function __construct($parser, $aname, $new_val, $value_provider)
    {
        $this->parser = $parser;
        $this->name = $aname;
        $this->value = $new_val;
        $this->value_provider = $value_provider;
    }
}
