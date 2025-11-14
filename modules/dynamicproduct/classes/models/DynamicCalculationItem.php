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

class DynamicCalculationItem extends DynamicObject
{
    public $id_product;
    public $id_item;
    public $type;
    public $position;

    public const CONDITION_ITEM = 'condition';
    public const FIELD_FORMULA_ITEM = 'field_formula';
    public const INTERVAL_ITEM = 'interval';
    public const GRID_ITEM = 'grid';

    public static $definition = [
        'table' => 'dynamicproduct_calculation_item',
        'primary' => 'id_calculation_item',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'id_item' => ['type' => self::TYPE_INT],
            'type' => ['type' => self::TYPE_STRING],
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

        $rows = \Db::getInstance()->executeS('
            SELECT *, id_calculation_item as id 
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_calculation_item`
            WHERE `id_product` = ' . (int) $id_source_product . '
            ORDER BY `position` ASC
        ');

        return ModelHelper::castNumericValues($rows, self::class);
    }
}
