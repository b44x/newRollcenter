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

class FieldFormula extends DynamicObject
{
    public $id_product;
    public $formula;
    public $position;

    private static $cache = [];

    public static $definition = [
        'table' => 'dynamicproduct_field_formula',
        'primary' => 'id_field_formula',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'formula' => ['type' => self::TYPE_HTML],
            'position' => ['type' => self::TYPE_INT],
        ],
        'group_by' => 'id_product',
    ];

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    /**
     * @param $id_product
     *
     * @return FieldFormula[]
     */
    public static function getByProduct($id_product)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $field_formulas = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_source_product);
        $sql->orderBy('position ASC');
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_field_formula = $row['id_field_formula'];
            $field_formula = new self($id_field_formula);
            if (\Validate::isLoadedObject($field_formula)) {
                $field_formulas[$id_field_formula] = $field_formula;
            }
        }

        return $field_formulas;
    }

    /**
     * @param $id_product
     *
     * @return array{
     *    id_field_formula: int,
     *    formula: string,
     * }
     */
    public static function getRowsByProduct($id_product)
    {
        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $rows = \Db::getInstance()->executeS('SELECT id_field_formula as id, formula, position 
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_field_formula 
            WHERE id_product = ' . (int) $id_source_product . ' 
            ORDER BY position ASC
        ');

        $rows = ModelHelper::castNumericValues($rows, self::class);

        return self::$cache[$id_product] = $rows;
    }

    public static function deleteByProduct($id_product)
    {
        return \Db::getInstance()->delete(self::$definition['table'], 'id_product = ' . (int) $id_product);
    }

    public static function getTargetField($formula)
    {
        preg_match("/\[\s*(.*?)\s*\]\s*\=/", $formula, $matches);
        if ($matches && isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    public static function getTargetFormula($formula)
    {
        $sides = explode('=', $formula, 2);
        if (isset($sides[1])) {
            return $sides[1];
        }

        return null;
    }
}
