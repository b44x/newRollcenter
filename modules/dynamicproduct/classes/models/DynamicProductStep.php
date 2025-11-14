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

class DynamicProductStep extends DynamicObject
{
    public $id_step;
    public $id_product;
    public $position;

    /** @var DynamicStep */
    public $step;

    public static $definition = [
        'table' => 'dynamicproduct_product_step',
        'primary' => 'id_product_step',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'id_step' => ['type' => self::TYPE_INT],
            'position' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->step = new DynamicStep($this->id_step, $id_lang);
    }

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    public static function getRowsByProduct(int $id_product)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $rows = \Db::getInstance()->executeS(
            '
            SELECT *, id_product_step as id FROM `' . _DB_PREFIX_ . 'dynamicproduct_product_step`
            WHERE `id_product` = ' . (int) $id_source_product . '
            ORDER BY `position` ASC'
        );

        return ModelHelper::castNumericValues($rows, self::class);
    }

    public static function getRowsByProductWithLabels(int $id_product, int $id_lang)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $rows = \Db::getInstance()->executeS(
            '
            SELECT ps.*, s.*, sl.label, ps.id_product_step as id FROM `' . _DB_PREFIX_ . 'dynamicproduct_product_step` ps
           
            LEFT JOIN `' . _DB_PREFIX_ . 'dynamicproduct_step` s ON (s.id_step = ps.id_step)
            LEFT JOIN `' . _DB_PREFIX_ . 'dynamicproduct_step_lang` sl 
            ON (sl.id_step = s.id_step AND sl.id_lang = ' . (int) $id_lang . ')
            
            WHERE ps.`id_product` = ' . (int) $id_source_product . '
            ORDER BY ps.`position` ASC'
        );

        $rows = ModelHelper::castNumericValues($rows, self::class);

        return ModelHelper::castNumericValues($rows, DynamicStep::class);
    }
}
