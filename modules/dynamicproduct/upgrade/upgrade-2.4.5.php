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

use DynamicProduct\classes\models\DynamicCombinationField;
use DynamicProduct\classes\models\DynamicCombinationValue;

/**
 * @param DynamicProduct $module
 *
 * @return bool
 */
function upgrade_module_2_4_5($module)
{
    $result = $module->installer->upgradeSQL('2.4.5');

    $product_values = [];

    /** @var DynamicCombinationValue[] $combination_values */
    $combination_values = DynamicCombinationValue::getAll();
    foreach ($combination_values as $combination_value) {
        $id_product = $combination_value->id_product;
        $id_field = $combination_value->id_field;
        if (!isset($product_values[$id_product])) {
            $product_values[$id_product] = [];
        }
        if (!in_array($id_field, $product_values[$id_product])) {
            $product_values[$id_product][] = $id_field;
        }
    }

    foreach ($product_values as $id_product => $id_fields) {
        foreach ($id_fields as $id_field) {
            $combination_field = new DynamicCombinationField();
            $combination_field->id_product = (int) $id_product;
            $combination_field->id_field = (int) $id_field;
            $combination_field->save();
        }
    }

    return $result;
}
