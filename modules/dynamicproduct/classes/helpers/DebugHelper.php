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
use DynamicProduct\classes\factory\DynamicFieldFactory;
use DynamicProduct\classes\models\DynamicField;

class DebugHelper
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

    public static function getFieldNames(array $hidden_fields)
    {
        if (empty($hidden_fields)) {
            return [];
        }

        $names = [];
        foreach ($hidden_fields as $id_field) {
            $field = DynamicField::getFieldFromCache($id_field, DynamicTools::getContext()->language->id);
            if ($field['name']) {
                $names[] = $field['name'];
            }
        }

        return $names;
    }

    public static function getOptionNames(array $hidden_options)
    {
        if (empty($hidden_options)) {
            return [];
        }

        $context = \Context::getContext();
        $option_names = [];

        foreach ($hidden_options as $id_field => $options) {
            $dynamicField = DynamicFieldFactory::create(null, $id_field, $context->language->id);
            $option_names[$dynamicField->name] = [];
            $fieldOptions = $dynamicField->getOptions();
            foreach ($fieldOptions as $option) {
                $option_names[$dynamicField->name][] = $option['label'];
            }
        }

        return $option_names;
    }

    public static function getGroupNames(array $hidden_groups)
    {
        if (empty($hidden_groups)) {
            return [];
        }

        $id_lang = (int) \Context::getContext()->language->id;

        $result = \Db::getInstance()->executeS('
            SELECT fgl.`label`
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_product_field_group` pfg
            JOIN `' . _DB_PREFIX_ . 'dynamicproduct_field_group_lang` fgl ON (pfg.id_field_group = fgl.id_field_group)
            WHERE  pfg.`id_product_field_group` IN (' . implode(',', array_map('intval', $hidden_groups)) . ') 
            AND fgl.`id_lang` = ' . (int) $id_lang);

        return array_map(function ($item) {
            return $item['label'];
        }, $result);
    }

    public static function getStepNames(array $hidden_steps)
    {
        if (empty($hidden_steps)) {
            return [];
        }

        $id_lang = (int) \Context::getContext()->language->id;

        $result = \Db::getInstance()->executeS('
            SELECT psl.`label`
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_product_step` ps
            JOIN `' . _DB_PREFIX_ . 'dynamicproduct_step_lang` psl ON (ps.id_step = psl.id_step)
            WHERE  ps.`id_product_step` IN (' . implode(',', array_map('intval', $hidden_steps)) . ') 
            AND psl.`id_lang` = ' . (int) $id_lang);

        return array_map(function ($item) {
            return $item['label'];
        }, $result);
    }
}
