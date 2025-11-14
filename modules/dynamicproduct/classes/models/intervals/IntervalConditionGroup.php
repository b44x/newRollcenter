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

class IntervalConditionGroup extends DynamicObject
{
    public $id_interval;

    /** @var IntervalCondition[] */
    public $conditions;

    /** @var [] */
    public $interval_formulas;

    public static $CACHE = [];

    public static $definition = [
        'table' => 'dynamicproduct_interval_condition_group',
        'primary' => 'id_interval_condition_group',
        'multilang' => false,
        'fields' => [
            'id_interval' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->assignConditions();
        $this->assignFormulas();
    }

    /**
     * @param $id_interval
     *
     * @return IntervalConditionGroup[]
     */
    public static function getByInterval($id_interval)
    {
        $key = "getByInterval($id_interval)";
        if (isset(self::$CACHE[$key])) {
            return self::$CACHE[$key];
        }

        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_interval = ' . (int) $id_interval);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new self($id);
            if (\Validate::isLoadedObject($object)) {
                $objects[$id] = $object;
            }
        }

        self::$CACHE[$key] = $objects;

        return $objects;
    }

    /**
     * @param $id_interval
     *
     * @return IntervalConditionGroup
     */
    public static function getLastConditionGroup($id_interval)
    {
        $condition_groups = self::getByInterval($id_interval);
        $count = count($condition_groups);
        if ($count) {
            $array_keys = array_keys($condition_groups);
            $id_condition_group = $array_keys[$count - 1];

            return new self($id_condition_group);
        }

        return new self();
    }

    private function assignConditions()
    {
        $this->conditions = IntervalCondition::getByIntervalConditionGroup($this->id);
    }

    private function assignFormulas()
    {
        $this->interval_formulas = IntervalFormula::getByConditionGroup($this->id);
    }

    public function delete()
    {
        $interval_conditions = IntervalCondition::getByIntervalConditionGroup($this->id);
        foreach ($interval_conditions as $interval_condition) {
            $interval_condition->delete();
        }
        $interval_formulas = IntervalFormula::getByConditionGroup($this->id);
        foreach ($interval_formulas as $interval_formula) {
            $interval_formula->delete();
        }

        return parent::delete();
    }
}
