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

use DynamicProduct\classes\DynamicTools;

class OptionPriceHelper
{
    /**
     * @param array $option
     * @param int $id_product
     * @param array $settings
     *
     * @return array
     */
    public static function displayValue($option, $id_product, $settings)
    {
        $module = DynamicTools::getModule();
        $value = $option['value'];
        if ((float) $value) {
            if (!$settings['custom_suffix']) {
                $price = $module->provider->convertPrice((float) $value);
                if (!(int) $settings['display_price_tax_excl']) {
                    $price = $module->calculator->applyTax($price, false, false, $id_product);
                }
                $option['displayed_value'] = DynamicTools::formatPrice($price);
            } else {
                $option['displayed_value'] = (float) $value . $settings['custom_suffix'];
            }
        }

        return $option;
    }

    /**
     * @param array $option
     * @param int $id_product
     * @param array $settings
     *
     * @return array
     */
    public static function displaySecondaryValue($option, $id_product, $settings)
    {
        $module = DynamicTools::getModule();

        if ((float) $option['secondary_value']) {
            if (!$settings['custom_suffix']) {
                $price = $module->provider->convertPrice((float) $option['secondary_value']);
                if (!$settings['display_price_tax_excl']) {
                    $price = $module->calculator->applyTax($price, false, false, $id_product);
                }
                $option['displayed_value'] = DynamicTools::formatPrice($price);
            } else {
                $option['displayed_value'] = (float) $option['secondary_value'] . $settings['custom_suffix'];
            }
        }

        return $option;
    }
}
