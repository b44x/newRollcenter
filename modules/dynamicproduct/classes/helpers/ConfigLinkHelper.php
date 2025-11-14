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
use DynamicProduct\classes\models\DynamicProductConfigLink;

class ConfigLinkHelper
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    private static $cache = [];

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public static function getSourceProduct($id_product)
    {
        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }
        $sql = new \DbQuery();
        $sql->select('id_product_source');
        $sql->from(DynamicProductConfigLink::$definition['table']);
        $sql->where('id_product = ' . (int) $id_product);
        $id_source_product = (int) \Db::getInstance()->getValue($sql);

        if (!$id_source_product) {
            $id_source_product = (int) \Db::getInstance()->getValue('
            SELECT cl.id_product
            FROM ' . _DB_PREFIX_ . 'category_product cp
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_product_config_category_link cl
            ON cl.id_category = cp.id_category 
            AND cp.id_category in (SELECT id_category FROM ' . _DB_PREFIX_ . 'category_product WHERE id_product = ' . (int) $id_product . ')');
        }

        $result = $id_source_product ?: (int) $id_product;
        self::$cache[$id_product] = $result;

        return $result;
    }

    public static function getSourceAttribute($id_product_source, $id_product, $id_attribute)
    {
        if (!(int) $id_attribute) {
            return $id_attribute;
        }

        if ($id_product_source == $id_product) {
            return $id_attribute;
        }

        $attributes_source = \Product::getProductAttributesIds($id_product_source);
        $attributes_current = \Product::getProductAttributesIds($id_product);

        $attributes_mapping = DynamicTools::mapAttributes($attributes_current, $attributes_source, true);

        $source_attribute = $attributes_mapping[$id_attribute] ?? $id_attribute;

        if (!$source_attribute) {
            $source_attribute = $id_attribute;
        }

        return $source_attribute;
    }

    public static function getNbLinkedConfigs($id_source_product)
    {
        $sql = new \DbQuery();
        $sql->select('COUNT(id_product)');
        $sql->from(DynamicProductConfigLink::$definition['table']);
        $sql->where('id_product_source = ' . (int) $id_source_product);
        $sql->groupBy('id_product_source');

        $count = (int) \Db::getInstance()->getValue($sql);

        if (!$count) {
            $linked_categories = \Db::getInstance()->executeS('SELECT id_category FROM 
            ' . _DB_PREFIX_ . 'dynamicproduct_product_config_category_link 
            WHERE id_product = ' . (int) $id_source_product);
            if ($linked_categories) {
                $id_categories = array_map(function ($item) {
                    return (int) $item['id_category'];
                }, $linked_categories);

                return \Db::getInstance()->getValue('SELECT COUNT(id_product) FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_category_default IN (' . implode(',', $id_categories) . ')');
            }
        }

        return $count;
    }

    public static function getLinkedConfigs()
    {
        $sql = new \DbQuery();
        $sql->select('id_product');
        $sql->from(DynamicProductConfigLink::$definition['table']);
        $result = \Db::getInstance()->executeS($sql);

        return DynamicTools::organizeBy('id_product', $result);
    }

    public static function getLinkedCategories()
    {
        $linked = [];
        $result = \Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'dynamicproduct_product_config_category_link');
        foreach ($result as $item) {
            $linked[] = $item['id_category'];
        }

        return $linked;
    }

    public static function isCategoryLinked($id_source_product)
    {
        return (bool) \Db::getInstance()->getValue('SELECT id_category FROM ' . _DB_PREFIX_ . 'dynamicproduct_product_config_category_link WHERE id_product = ' . (int) $id_source_product);
    }
}
