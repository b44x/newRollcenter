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

class DynamicCommonField extends DynamicObject
{
    public $id_product;
    public $id_group;
    public $id_field;
    public $id_step;
    public $position;

    public static $definition = [
        'table' => 'dynamicproduct_common_field',
        'primary' => 'id_common_field',
        'group_by' => 'id_product',
        'multilang' => false,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'id_group' => ['type' => self::TYPE_INT],
            'id_field' => ['type' => self::TYPE_INT],
            'id_step' => ['type' => self::TYPE_INT],
            'position' => ['type' => self::TYPE_INT],
        ],
    ];

    /**
     * @param $id_field
     *
     * @return DynamicCommonField[]
     */
    public static function getByIdField($id_field)
    {
        $common_fields = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_field = ' . (int) $id_field);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_field = $row['id_common_field'];
            $common_field = new self($id_field);
            if (\Validate::isLoadedObject($common_field)) {
                $common_fields[$id_field] = $common_field;
            }
        }

        return $common_fields;
    }

    /**
     * @param $id_product
     *
     * @return DynamicCommonField[]
     */
    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $common_fields = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_source_product);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_field = $row['id_common_field'];
            $common_field = new self($id_field, $id_lang);
            if (\Validate::isLoadedObject($common_field)) {
                $common_fields[$id_field] = $common_field;
            }
        }

        return $common_fields;
    }

    public static function getByFieldAndProduct($id_field, $id_product)
    {
        $sql = new \DbQuery();
        $sql->select(self::$definition['primary']);
        $sql->from(self::$definition['table']);
        $sql->where('id_field = ' . (int) $id_field);
        $sql->where('id_product = ' . (int) $id_product);
        $id_common_field = (int) \Db::getInstance()->getValue($sql);
        $common_field = new self($id_common_field);
        $common_field->id_field = (int) $id_field;
        $common_field->id_product = (int) $id_product;

        return $common_field;
    }
}
