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
/**
 * 2010-2023 Tuni-Soft
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize the module for your
 * needs please refer to
 * http://doc.prestashop.com/display/PS15/Overriding+default+behaviors
 * for more information.
 *
 * @author    Tuni-Soft
 * @copyright 2010-2023 Tuni-Soft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace DynamicProduct\classes\models;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\helpers\ConfigLinkHelper;

class DynamicProportion extends DynamicObject
{
    public $id_product;
    public $id_field;
    public $id_field_src;
    public $value;

    public static $definition = [
        'table' => 'dynamicproduct_proportions',
        'primary' => 'id_proportion',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_field' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_field_src' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'value' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
        ],
    ];

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    /**
     * @param $id_product
     *
     * @return DynamicProportion[]
     */
    public static function getByProduct($id_product)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $dynamic_proportions = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_source_product);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_proportion = $row['id_proportion'];
            $dynamic_proportion = new self($id_proportion);
            if (\Validate::isLoadedObject($dynamic_proportion)) {
                $dynamic_proportions[$id_proportion] = $dynamic_proportion;
            }
        }

        return $dynamic_proportions;
    }

    public static function deleteByProduct($id_product)
    {
        return \Db::getInstance()->delete(self::$definition['table'], 'id_product = ' . (int) $id_product);
    }

    public static function getDataByProduct($id_product)
    {
        $data = [];
        $dynamic_proportions = self::getByProduct($id_product);
        foreach ($dynamic_proportions as $dynamic_proportion) {
            $field_src = new DynamicField((int) $dynamic_proportion->id_field_src);
            $field_target = new DynamicField((int) $dynamic_proportion->id_field);
            if (\Validate::isLoadedObject($field_src) && \Validate::isLoadedObject($field_target)) {
                $proportion = (float) $dynamic_proportion->value;
                if ($proportion) {
                    $data[$field_src->name] = [
                        'target' => $field_target->name,
                        'proportion' => $proportion,
                    ];
                    $data[$field_target->name] = [
                        'target' => $field_src->name,
                        'proportion' => 1 / $proportion,
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @param $id_field
     *
     * @return DynamicProportion[]
     */
    public static function getWithIdField($id_field)
    {
        $dynamic_proportions = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_field = ' . (int) $id_field . ' OR id_field_src = ' . (int) $id_field);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_proportion = $row['id_proportion'];
            $dynamic_proportion = new self($id_proportion);
            if (\Validate::isLoadedObject($dynamic_proportion)) {
                $dynamic_proportions[$id_proportion] = $dynamic_proportion;
            }
        }

        return $dynamic_proportions;
    }
}
