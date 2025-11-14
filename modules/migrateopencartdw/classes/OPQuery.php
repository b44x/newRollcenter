<?php
/**
* 2007-2024 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class OPQuery
{
    // --- Query builder vars:
    protected $source_cart;
    protected $tp;
    protected $offset;
    protected $row_count = 10;
    protected $version;
    protected $languages;

    // --- Constructor / destructor:

    public function __construct()
    {
    }

    // --- Configuration methods:

    public function setRowCount($number)
    {
        $this->row_count = (int)$number;
    }

    public function setLanguages($string)
    {
        $this->languages = pSQL($string);
    }

    public function setVersion($string)
    {
        $this->version = $string;
    }

    public function setCart($string)
    {
        $this->source_cart = $string;
    }

    public function setPrefix($string)
    {
        $this->tp = pSQL($string);
    }

    public function setOffset($number)
    {
        $this->offset = (int)$number;
    }

    // --- get query string methods:

    public function getDefaultShopValues()
    {
        $q = array();
        $q['default_lang'] = "SELECT lg.* FROM ". pSQL($this->tp) ."setting AS lg WHERE lg.key = 'config_language' AND lg.store_id = 0";

        return $q;
    }

    public function getMappingInfo($default_values)
    {


        $q = array();

        $q['languages'] = 'SELECT `language_id` as `source_id`, `name` as `source_name` FROM `' . pSQL($this->tp) . 'language` ';
        $q['currencies'] = 'SELECT `currency_id` as `source_id`, `title` as `source_name` FROM `' . pSQL($this->tp) . 'currency` ';
        $q['tax_class'] = 'SELECT tax_class_id as `source_id`, title as `source_name` FROM `' . pSQL($this->tp) . 'tax_class`';
        $q['order_states'] = 'SELECT order_status_id as source_id, name as source_name FROM ' . pSQL($this->tp) . 'order_status where language_id = (SELECT a.language_id from ' . pSQL($this->tp) . 'language AS a LEFT JOIN ' .pSQL($this->tp). 'setting AS b ON a.code = b.value LIMIT 1)';
        $q['customer_groups'] = 'SELECT customer_group_id as `source_id`, name as `source_name` FROM `' . pSQL($this->tp) . 'customer_group`';

        return $q;
    }

    public function getCountInfo()
    {
        $q = array();

        if (Tools::getValue('entities_manufacturers') == 1) {
            $q['manufacturers'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'manufacturer`';
        }

        if (Tools::getValue('entities_categories') == 1) {
            $q['categories'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'category`';
        }

        if (Tools::getValue('entities_products') == 1) {
            if (Tools::getValue('migrate_recent_data')) {
                $last_migrated_product_id = DWMigratedDataOP::getLastId('product');
                $q['products'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'product`  WHERE product_id > ' . (int)$last_migrated_product_id;
            } else {
                $q['products'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'product`';
            }
//            $q['products'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'product` where product_id in (2859, 3693)';
        }

        if (Tools::getValue('entities_customers') == 1) {
            if (Tools::getValue('migrate_recent_data')) {
                $last_migrated_customer_id = DWMigratedDataOP::getLastId('customer');
                $q['customers'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'customer` WHERE customer_id > '. (int)$last_migrated_customer_id;
            } else {
                $q['customers'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'customer`';
            }
        }

        if (Tools::getValue('entities_orders') == 1 && Tools::getIsset('entities_customers') == 1) {
            if (Tools::getValue('migrate_recent_data')) {
                $last_migrated_order_id = DWMigratedDataOP::getLastId('order');
                $q['orders'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'order` WHERE order_status_id !=0 AND order_id > ' . (int)$last_migrated_order_id;
            } else {
                $q['orders'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'order` WHERE order_status_id !=0';
            }
        }

        return $q;
    }


    // --- Manufactures methods:

    public function manufactures()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'manufacturer LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    // --- Category methods:

    public function category()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'category ORDER BY category_id ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    public function singleCategory($id_category)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'category WHERE category_id = ' . (int)$id_category;
    }

    public function categorySqlSecond($id_categories)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'category_description WHERE category_id IN (' . pSQL($id_categories) . ')';
    }

    // --- Product method:

    public function product()
    {
        if (Tools::getValue('migrate_recent_data')) {
            $last_migrated_product_id = DWMigratedDataOP::getLastId('product');
            return 'SELECT * FROM ' . pSQL($this->tp) . 'product  WHERE product_id > ' . (int)$last_migrated_product_id. '  ORDER BY product_id ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        } else {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'product LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        }
//        return 'SELECT * FROM ' . pSQL($this->tp) . 'product where product_id in (2859, 3693) LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }


    public function singleProduct($products_id)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'product WHERE product_id = ' . (int)$products_id;
    }

    public function productSqlSecond($products_id)
    {
        $q = array();
        $q['product_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_description WHERE product_id IN (' . pSQL($products_id) . ')';
        $q['tags'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_description WHERE product_id IN (' . pSQL($products_id) . ')';
        $q['specific_price'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_special WHERE product_id IN (' . pSQL($products_id) . ')';
        $q['category_product'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_to_category WHERE product_id IN (' . pSQL($products_id) . ')';
        $q['image'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_image WHERE product_id IN (' . pSQL($products_id) . ')';
        $q['sku'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product WHERE product_id IN (' . pSQL($products_id) . ')';
        $q['option_image'] = 'SELECT a.*, b.image FROM `' . pSQL($this->tp) . 'product_option_value` AS a LEFT JOIN `' . pSQL($this->tp) . 'option_value` AS b ON a.option_value_id = b.option_value_id LEFT JOIN `' . pSQL($this->tp) . 'option` AS c ON a.option_id = c.option_id WHERE product_id IN (' . pSQL($products_id) . ')  AND c.type = "image"';
        $q['product_attribute'] = 'SELECT a.*, b.* FROM ' . pSQL($this->tp) . 'product_option_value AS a JOIN ' .pSQL($this->tp). 'option_value_description AS b ON a.option_value_id = b.option_value_id WHERE product_id IN (' . pSQL($products_id) . ')';

        return $q;
    }

    public function productSqlThird($options_id, $options_values_id)
    {
        $q = array();
        $q['attribute_group'] = 'SELECT a.*, b.* FROM `' . pSQL($this->tp) . 'option_description` AS a JOIN `' .pSQL($this->tp). 'option` AS b ON a.option_id = b.option_id WHERE a.option_id IN (' . pSQL($options_id) . ')';
        $q['attribute'] = 'SELECT a.*, b.* FROM ' . pSQL($this->tp) . 'option_value AS a JOIN ' . pSQL($this->tp) . 'option_value_description AS b on a.option_value_id = b.option_value_id WHERE a.option_value_id IN (' . pSQL($options_values_id) . ')';

        return $q;
    }



    // --- Order method:

    public function order()
    {
//        return "SELECT a.*, b.address_id as id_address_delivery, c.cart_id, d.title as payment FROM " . pSQL($this->tp) . "order AS a LEFT JOIN ". pSQL($this->tp) ."address AS b ON a.customer_id = b.customer_id LEFT JOIN  " .pSQL($this->tp). "cart AS c ON a.customer_id = c.customer_id LEFT JOIN " .pSQL($this->tp). "order_total AS d ON a.order_id = d.order_id WHERE d.code = 'shipping' AND a.order_id != 0 ORDER BY a.order_id ASC LIMIT  " .
//            (int)$this->offset . ',' . (int)$this->row_count;
//        return 'select a.*, b.address_id as id_address_delivery, d.title as payment from `' . pSQL($this->tp) . 'order` as a left join ' . pSQL($this->tp) . 'address AS b ON a.customer_id = b.customer_id left join ' . pSQL($this->tp) . 'order_total AS d ON a.order_id = d.order_id WHERE d.code = \'total\' AND a.order_id != 0 ORDER BY order_id ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        if (Tools::getValue('migrate_recent_data')) {
            $last_migrated_order_id = DWMigratedDataOP::getLastId('order');
            return 'select * from `' . pSQL($this->tp) . 'order` WHERE order_status_id !=0  AND order_id > ' . (int)$last_migrated_order_id. ' ORDER BY order_id ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        } else {
            return 'select * from `' . pSQL($this->tp) . 'order` WHERE order_status_id !=0 ORDER BY order_id ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        }
    }

    public function orderSqlSecond($orders_id)
    {
        $q = array();
        $q['order_detail'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_product WHERE order_id IN (' . pSQL($orders_id) . ')';
        $q['order_total'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_total WHERE order_id IN (' . pSQL($orders_id) . ')';
        $q['orders_product_attribute'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_product WHERE order_id IN (' . pSQL($orders_id) . ')';
        $q['order_history'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_history WHERE order_id IN (' . pSQL($orders_id) . ')';

        return $q;
    }

    public function customerThreads($id_customer_threads)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'customer_history WHERE customer_history_id IN (' . pSQL($id_customer_threads) . ')';
    }

    // --- Customer methods:

    public function customers()
    {
        if (Tools::getValue('migrate_recent_data')) {
            $last_migrated_customer_id = DWMigratedDataOP::getLastId('customer');
            return 'SELECT * FROM ' . pSQL($this->tp) . 'customer WHERE customer_id > '. (int)$last_migrated_customer_id. ' ORDER BY customer_id LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        } else {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'customer LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        }
    }

    public function address($customers_id)
    {
//        return 'SELECT  addr.*, c.iso_code_2, z.zone_code FROM ' . pSQL($this->tp) . 'address as addr
//                LEFT JOIN ' . pSQL($this->tp) . 'country AS c ON c.country_id = addr.country_id
//                LEFT JOIN ' . pSQL($this->tp) . 'zone AS z ON z.zone_id = addr.zone_id
//                WHERE addr.customer_id IN (' . pSQL($customers_id) . ')';
        return 'SELECT  addr.*, c.iso_code_2, z.code FROM ' . pSQL($this->tp) . 'address as addr
                LEFT JOIN ' . pSQL($this->tp) . 'country AS c ON c.country_id = addr.country_id
                LEFT JOIN `' . pSQL($this->tp) . 'zone` AS z ON z.zone_id = addr.zone_id 
                WHERE addr.customer_id IN (' . pSQL($customers_id) . ')';
    }
}
