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

class DynamicProductConfigLink extends DynamicObject
{
    /** @var \DynamicProduct */
    protected $module;

    public $id_product;
    public $id_product_source;

    public static $definition = [
        'table' => 'dynamicproduct_product_config_link',
        'primary' => 'id_product_config_link',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'id_product_source' => ['type' => self::TYPE_INT],
        ],
    ];

    /**
     * @param $id_product
     * @param $id_product_source
     *
     * @return DynamicProductConfigLink
     */
    public static function createLink($id_product, $id_product_source)
    {
        self::removeLink($id_product);
        $config_link = new self();
        $config_link->id_product = (int) $id_product;
        $config_link->id_product_source = (int) $id_product_source;
        $config_link->save();

        return $config_link;
    }

    public static function removeLink($id_product)
    {
        return \Db::getInstance()->delete(self::$definition['table'], 'id_product = ' . (int) $id_product);
    }

    public static function removeLinks($id_source_product)
    {
        return \Db::getInstance()->delete(self::$definition['table'], 'id_product_source = ' . (int) $id_source_product);
    }
}
