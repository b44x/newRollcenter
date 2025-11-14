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

class ProductHelper
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

    public static function getProductFeatureFields($id_product)
    {
        $feature_fields = [];
        $product_features = \Product::getFrontFeaturesStatic((int) \Configuration::get('PS_LANG_DEFAULT'), $id_product);
        foreach ($product_features as $product_feature) {
            $label = $product_feature['name'];
            $name = self::getCleanFeatureName($label);
            $feature_fields[] = [
                'name' => $name,
                'label' => $label,
                'value' => $product_feature['value'],
            ];
        }

        return $feature_fields;
    }

    public static function getProductAttributeFields($id_product)
    {
        $attribute_fields = [];

        $groups = \AttributeGroup::getAttributesGroups((int) \Configuration::get('PS_LANG_DEFAULT'));
        foreach ($groups as $group) {
            $id_group = $group['id_attribute_group'];
            if (DynamicTools::productHasAttributeGroup($id_product, $id_group)) {
                $label = $group['name'];
                $name = self::getCleanAttributeName($label);
                $attribute_fields[] = [
                    'name' => $name,
                    'label' => $label,
                ];
            }
        }

        return $attribute_fields;
    }

    public static function getProductDatabaseFields()
    {
        $module = DynamicTools::getModule();
        $database_fields = [];
        $csv_files = \Tools::scandir($module->provider->getDataDir('databases'), 'csv');

        foreach ($csv_files as $csv_file) {
            $label = pathinfo($csv_file, PATHINFO_FILENAME);
            $name = $label;
            $database_fields[] = [
                'name' => $name,
                'label' => $label,
            ];
        }

        return $database_fields;
    }

    public static function getCleanAttributeName($label)
    {
        $name = \Tools::replaceAccentedChars($label);
        $name = 'attribute_' . str_replace([' ', '-'], ['_', '_'], \Tools::strtolower($name));

        return $name;
    }

    public static function getCleanFeatureName($label)
    {
        $name = \Tools::replaceAccentedChars($label);
        $name = str_replace(' ', '_', \Tools::strtolower($name));
        $name = preg_replace('/[^0-9a-zA-Z_]/', '', $name);
        $name = 'feature_' . \Tools::strtolower($name);

        return $name;
    }

    public function isAvailableWhenOutOfStock($id_product)
    {
        $product = new \Product($id_product, true);

        return \Product::isAvailableWhenOutOfStock($product->out_of_stock);
    }
}
