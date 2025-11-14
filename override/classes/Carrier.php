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
class Carrier extends CarrierCore
{
    /*
    * module: dynamicproduct
    * date: 2025-03-24 14:49:19
    * version: 3.22.19
    */
    public static function getAvailableCarrierList(
        Product $product,
        $id_warehouse = 0,
        $id_address_delivery = null,
        $id_shop = null,
        $cart = null,
        &$error = []
    ) {
        if (!Module::isEnabled('dynamicproduct')) {
            return parent::getAvailableCarrierList(
                $product,
                $id_warehouse,
                $id_address_delivery,
                $id_shop,
                $cart,
                $error
            );
        }
        if ($cart === null) {
            $cart = Context::getContext()->cart;
        }
        Module::getInstanceByName('dynamicproduct');
        $sizes = DynamicProduct\classes\models\DynamicInput::getMaxSizes($product);
        if ((float) $sizes['width']) {
            $product->width = $sizes['width'];
        }
        if ((float) $sizes['height']) {
            $product->height = $sizes['height'];
        }
        if ((float) $sizes['depth']) {
            $product->depth = $sizes['depth'];
        }
        return parent::getAvailableCarrierList($product, $id_warehouse, $id_address_delivery, $id_shop, $cart, $error);
    }
}
