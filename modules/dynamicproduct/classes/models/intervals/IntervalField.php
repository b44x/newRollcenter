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
namespace DynamicProduct\classes\models\intervals;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\ModelHelper;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicObject;

class IntervalField extends DynamicObject
{
    public $id_interval;
    public $id_field;

    /** @var DynamicField */
    public $field;

    private static $cache = [];

    public static $definition = [
        'table' => 'dynamicproduct_interval_field',
        'primary' => 'id_interval_field',
        'multilang' => false,
        'fields' => [
            'id_interval' => ['type' => self::TYPE_INT],
            'id_field' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->assignField($id_lang);
    }

    /**
     * @param $id_interval
     *
     * @return IntervalField[]
     */
    public static function getByInterval($id_interval)
    {
        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_interval = ' . (int) $id_interval);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new self($id);
            if (\Validate::isLoadedObject($object)) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    public static function getRowsByProduct($id_product)
    {
        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }

        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $rows = \Db::getInstance()->executeS(/* @lang MySQL */ '
            SELECT *, dif.id_interval_field as id 
            
            FROM ' . _DB_PREFIX_ . 'dynamicproduct_interval i
            
            JOIN ' . _DB_PREFIX_ . 'dynamicproduct_interval_field dif
            ON i.id_interval = dif.id_interval
            
            WHERE i.id_product = ' . (int) $id_source_product);

        $rows = ModelHelper::castNumericValues($rows, self::class);

        return self::$cache[$id_source_product] = $rows;
    }

    /**
     * @param $id_field
     *
     * @return IntervalField[]
     */
    public static function getByField($id_field)
    {
        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_field = ' . (int) $id_field);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new self($id);
            if (\Validate::isLoadedObject($object)) {
                $objects[$id] = $object;
            }
        }

        return $objects;
    }

    private function assignField($id_lang)
    {
        $this->field = new DynamicField($this->id_field, $id_lang);
    }

    public function delete()
    {
        $interval_formulas = IntervalFormula::getByIntervalField($this->id);
        foreach ($interval_formulas as $interval_formula) {
            $interval_formula->delete();
        }

        return parent::delete();
    }
}
