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
namespace DynamicProduct\classes\models;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\ConfigLinkHelper;

class DynamicConfig
{
    /**
     * List of field types.
     */
    public const TYPE_INT = 1;
    public const TYPE_BOOL = 2;
    public const TYPE_STRING = 3;
    public const TYPE_FLOAT = 4;
    public const TYPE_DATE = 5;
    public const TYPE_HTML = 6;

    /** @var \DynamicProduct */
    protected $module;

    public $id_product;
    public $active = 0;
    public $required = 0;
    public $exclude = 0;
    public $displayed_price = 0;
    public $displayed_price_label;
    public $display_starting_from = 1;
    public $display_dynamic_price = 0;
    public $display_customization_cost = 0;
    public $recalc = 0;
    public $always_recalc = 0;
    public $display_weight = 0;
    public $hide_qty = 0;
    public $multiply_price = 0;
    public $allow_save = 0;
    public $split_summary = 0;
    public $custom_calculation = 0;
    public $all_steps_required = 1;

    private static $cache = [];

    public static $definition = [
        'table' => 'dynamicproduct_config',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'active' => ['type' => self::TYPE_INT],
            'required' => ['type' => self::TYPE_INT],
            'exclude' => ['type' => self::TYPE_INT],
            'displayed_price' => ['type' => self::TYPE_FLOAT],
            'displayed_price_label' => ['type' => self::TYPE_STRING],
            'display_starting_from' => ['type' => self::TYPE_INT],
            'display_dynamic_price' => ['type' => self::TYPE_INT],
            'display_customization_cost' => ['type' => self::TYPE_INT],
            'recalc' => ['type' => self::TYPE_INT],
            'always_recalc' => ['type' => self::TYPE_INT],
            'display_weight' => ['type' => self::TYPE_INT],
            'hide_qty' => ['type' => self::TYPE_INT],
            'multiply_price' => ['type' => self::TYPE_INT],
            'allow_save' => ['type' => self::TYPE_INT],
            'split_summary' => ['type' => self::TYPE_INT],
            'custom_calculation' => ['type' => self::TYPE_INT],
            'all_steps_required' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id_product = null)
    {
        $this->id_product = (int) $id_product;
        $this->module = DynamicTools::getModule();
        $this->setValues();
    }

    public static function getByProduct($id_product)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return new DynamicConfig($id_source_product);
    }

    public static function getConfigs()
    {
        $module = DynamicTools::getModule();
        $DB_PREFIX = _DB_PREFIX_;
        $prefix = $DB_PREFIX . $module->name;

        return \Db::getInstance()->executeS("
            SELECT cfg.id_product, COUNT(ln.id_product) nb_linked
            FROM {$prefix}_config cfg
            LEFT JOIN {$prefix}_product_config_link ln ON ln.id_product_source = cfg.id_product
            JOIN {$DB_PREFIX}product p ON p.id_product = cfg.id_product
            WHERE cfg.name = 'active' AND cfg.value = 1
            GROUP BY cfg.id_product
            ORDER BY nb_linked DESC, cfg.id_product DESC
        ");
    }

    private function setValues()
    {
        $cache_key = self::$definition['table'] . '_' . $this->id_product;
        if (DynamicTools::canUseCache() && isset(self::$cache[$cache_key])) {
            $result = self::$cache[$cache_key];
        } else {
            $sql = new \DbQuery();
            $sql->from(self::$definition['table']);
            $sql->select('name, value');
            $sql->where('id_product = ' . (int) $this->id_product);
            $result = \Db::getInstance()->executeS($sql);
            self::$cache[$cache_key] = $result;
        }
        $fields = self::$definition['fields'];
        if (is_array($result)) {
            foreach ($result as $row) {
                $property = $row['name'];
                $value = $row['value'];
                if (isset($fields[$property])) {
                    $field = $fields[$property];
                    $value = \ObjectModel::formatValue($value, $field['type']);
                    $this->$property = $value;
                }
            }
        }
    }

    /**
     * @param $values
     *
     * @return DynamicConfig
     */
    public static function copyFromArray($values)
    {
        $obj = new self();
        $fields = self::$definition['fields'];
        $field_names = array_keys($fields);
        foreach ($field_names as $field) {
            if (isset($values[$field])) {
                $obj->$field = $values[$field];
            }
        }

        return $obj;
    }

    public function isProductPriceExcluded()
    {
        return $this->active && $this->exclude && !$this->module->provider->isAfter1730();
    }

    public static function isActive($id_product)
    {
        return DynamicConfig::getByProduct($id_product)->active;
    }

    public static function isExcluded($id_product)
    {
        $product_config = DynamicConfig::getByProduct($id_product);

        return $product_config->isProductPriceExcluded();
    }

    public static function getDisplayWeight($id_product)
    {
        return DynamicConfig::getByProduct($id_product)->display_weight;
    }

    public static function getMultiplyPrice($id_product)
    {
        return DynamicConfig::getByProduct($id_product)->multiply_price;
    }

    public static function getDisplayedPrice($id_product)
    {
        $product_config = DynamicConfig::getByProduct($id_product);

        return (float) $product_config->displayed_price;
    }

    public static function getActiveProducts()
    {
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->select('id_product, value');
        $sql->where('name="active" AND value=1 AND id_product != 0');
        $result = \Db::getInstance()->executeS($sql);

        return DynamicTools::organizeBy('id_product', $result, 'value');
    }

    public static function getActiveProductsWithLabels($id_lang)
    {
        $products = [];
        $active_products = array_keys(self::getActiveProducts());
        foreach ($active_products as $id_product) {
            $products[$id_product] = [
                'id_product' => $id_product,
                'label' => $id_product . ' - ' . \Product::getProductName($id_product, null, $id_lang),
            ];
        }

        return $products;
    }

    public function saveValue($name, $value)
    {
        $fields = self::$definition['fields'];
        if (!isset($fields[$name])) {
            return false;
        }
        $field = $fields[$name];
        $value = \ObjectModel::formatValue($value, $field['type']);
        $data = [
            'id_product' => (int) $this->id_product,
            'name' => pSQL($name),
            'value' => pSQL($value),
        ];

        return \Db::getInstance()->insert(self::$definition['table'], $data, false, true, \Db::REPLACE);
    }

    public function save()
    {
        $fields = self::$definition['fields'];
        foreach ($fields as $property => $info) {
            $value = \ObjectModel::formatValue($this->$property, $info['type']);
            $data = [
                'id_product' => (int) $this->id_product,
                'name' => pSQL($property),
                'value' => pSQL($value),
            ];
            \Db::getInstance()->insert(self::$definition['table'], $data, false, true, \Db::REPLACE);
        }
    }

    public function delete()
    {
        \Db::getInstance()->delete(self::$definition['table'], 'id_product = ' . (int) $this->id_product);
    }
}
