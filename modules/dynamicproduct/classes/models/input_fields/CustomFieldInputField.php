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
namespace DynamicProduct\classes\models\input_fields;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\models\DynamicInputField;

class CustomFieldInputField extends DynamicInputField
{
    public $type = _DP_CUSTOM_;

    public static function create($id = null, $id_lang = null, $id_shop = null)
    {
        $input_field = new CustomFieldInputField($id, $id_lang, $id_shop);
        $field = $input_field->getDynamicField();
        $script_name = $field['settings']['script_name'];
        if (!$script_name) {
            $script_name = $field['name'];
        }
        $cls = 'DynamicProductData\\scripts\\' . $script_name . '\\InputField';
        if (class_exists($cls)) {
            return new $cls($id, $id_lang, $id_shop);
        }

        return $input_field;
    }
}
