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

class Product extends ProductCore
{
    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
        $result = parent::getProductProperties($id_lang, $row, $context);
        /** @var DynamicProduct $module */
        $module = Module::getInstanceByName('dynamicproduct');
        if (Module::isEnabled('dynamicproduct') && $module->provider->isAfter1730()) {
            $id_product = (int) ($row['id_product'] ?? $row['id']);

            $dynamic_config = DynamicProduct\classes\models\DynamicConfig::getByProduct($id_product);
            if ($dynamic_config->active) {
                $displayed_price = DynamicProduct\classes\models\DynamicConfig::getDisplayedPrice($id_product);
                if ($displayed_price || $dynamic_config->display_dynamic_price) {
                    $module->calculator->assignProductPrices($row, $displayed_price, $result);
                }
            }
        }

        return $result;
    }
}
