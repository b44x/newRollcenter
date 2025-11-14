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

use DynamicProduct\classes\models\DynamicObject;

class IntervalConditionRange extends DynamicObject
{
    public $id_interval_condition;
    public $min;
    public $max;

    /** @var int[] */
    public $values;

    public static $definition = [
        'table' => 'dynamicproduct_interval_condition_range',
        'primary' => 'id_interval_condition_range',
        'multilang' => false,
        'fields' => [
            'id_interval_condition' => ['type' => self::TYPE_INT],
            'min' => ['type' => self::TYPE_FLOAT],
            'max' => ['type' => self::TYPE_FLOAT],
        ],
    ];

    /**
     * @param $id_interval
     *
     * @return IntervalConditionRange
     */
    public static function getByIntervalCondition($id_interval_condition)
    {
        $sql = new \DbQuery();
        $sql->select(self::$definition['primary']);
        $sql->from(static::$definition['table']);
        $sql->where('id_interval_condition = ' . (int) $id_interval_condition);
        $id = (int) \Db::getInstance()->getValue($sql);
        $object = new self($id);
        $object->id_interval_condition = (int) $id_interval_condition;

        return $object;
    }
}
