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
use DynamicProduct\classes\helpers\ModelHelper;

class ExecOrder extends DynamicObject
{
    public $id_product;
    public $id_exec;
    public $position;

    public const EXEC_INTERVAL = 0;
    public const EXEC_FIELD_FORMULAS = 1;
    public const EXEC_GRIDS = 2;
    public const EXEC_CONDITIONS = 3;

    public static $definition = [
        'table' => 'dynamicproduct_exec_order',
        'primary' => 'id_exec_order',
        'multilang' => false,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'id_exec' => ['type' => self::TYPE_INT],
            'position' => ['type' => self::TYPE_INT],
        ],
        'group_by' => 'id_product',
    ];

    public static function getLabels()
    {
        $module = DynamicTools::getModule();
        $source = DynamicTools::getSource();

        return [
            self::EXEC_INTERVAL => $module->l('Intervals', $source),
            self::EXEC_FIELD_FORMULAS => $module->l('Field Formulas', $source),
            self::EXEC_GRIDS => $module->l('Grids', $source),
            self::EXEC_CONDITIONS => $module->l('Conditions', $source),
        ];
    }

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    public static function getRowsByProduct(int $id_product, $order = false)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $rows = \Db::getInstance()->executeS('
            SELECT *, id_exec_order as id FROM `' . _DB_PREFIX_ . 'dynamicproduct_exec_order`
            WHERE `id_product` = ' . (int) $id_source_product . ($order ? '
            ORDER BY `position` ASC' : ''));

        return ModelHelper::castNumericValues($rows, self::class);
    }
}
