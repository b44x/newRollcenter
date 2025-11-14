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

use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\ModelHelper;

class DynamicProductFieldGroup extends DynamicObject
{
    public $id_field_group;
    public $id_product;
    public $id_step;
    public $collapsible;
    public $start_collapsed;
    public $id_control_field;
    public $position;

    private static $cache = [];

    public static $definition = [
        'table' => 'dynamicproduct_product_field_group',
        'primary' => 'id_product_field_group',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'id_step' => ['type' => self::TYPE_INT],
            'id_field_group' => ['type' => self::TYPE_INT],
            'collapsible' => ['type' => self::TYPE_INT],
            'start_collapsed' => ['type' => self::TYPE_INT],
            'id_control_field' => ['type' => self::TYPE_INT],
            'position' => ['type' => self::TYPE_INT],
        ],
    ];

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    public static function getRowsByProduct($id_product)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $rows = \Db::getInstance()->executeS(
            '
            SELECT *, id_product_field_group as id FROM `' . _DB_PREFIX_ . 'dynamicproduct_product_field_group`
            WHERE `id_product` = ' . (int) $id_source_product . '
            ORDER BY `position` ASC'
        );

        return self::$cache[$id_product] = ModelHelper::castNumericValues($rows, self::class);
    }

    public static function getGroupFromCache($id_group)
    {
        if (!$id_group) {
            return false;
        }
        foreach (self::$cache as $rows) {
            foreach ($rows as $row) {
                if ($row['id'] == $id_group) {
                    return $row;
                }
            }
        }

        return false;
    }
}
