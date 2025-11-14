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
namespace DynamicProduct\classes\helpers;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ColorHelper
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getClearColor($value)
    {
        $dark = '#222';
        $light = '#DDD';
        $luminence = $this->getColorLuminence($value);

        return $luminence > 0.22 ? $dark : $light;
    }

    private function getColorLuminence($value)
    {
        $color = $this->HEXToRGB($value);

        return ((0.2126 * $color[0]) + (0.7152 * $color[1]) + (0.0722 * $color[2])) / 255;
    }

    private function HEXToRGB($value)
    {
        $values = str_replace('#', '', $value);
        switch (\Tools::strlen($values)) {
            case 3:
                list($r, $g, $b) = sscanf($values, '%1s%1s%1s');

                return [hexdec("$r$r"), hexdec("$g$g"), hexdec("$b$b")];
            case 6:
                return array_map('hexdec', sscanf($values, '%2s%2s%2s'));
            default:
                return false;
        }
    }
}
