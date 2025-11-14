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
namespace DynamicProduct\classes\helpers;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\models\DynamicCondition;
use DynamicProduct\classes\models\DynamicEquation;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\FieldFormula;
use DynamicProduct\classes\models\intervals\Interval;
use DynamicProduct\classes\models\intervals\IntervalConditionGroup;
use DynamicProduct\classes\models\intervals\IntervalFormula;

class FormulasHelper
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    /**
     * @param DynamicField $field
     * @param string $original_name
     * @param string $new_name
     *
     * @return array
     */
    public static function updateFormulas($field, $original_name, $new_name)
    {
        $id_product = (int) $field->id_product;

        $replacement = [
            'original' => ["[[{$original_name}]]", "[{$original_name}]"],
            'new' => ["[[{$new_name}]]", "[{$new_name}]"],
        ];

        // update formulas
        $formulas = DynamicEquation::getByIdProduct($id_product);
        foreach ($formulas as $formula) {
            $formula->formula = str_replace($replacement['original'], $replacement['new'], $formula->formula);
            $formula->save();
        }

        // update condition formulas
        $conditions = DynamicCondition::getByProduct($id_product);
        foreach ($conditions as $condition) {
            $condition->formula = str_replace($replacement['original'], $replacement['new'], $condition->formula);
            $condition->save();
        }

        // update field formulas
        $field_formulas = FieldFormula::getByProduct($id_product);
        foreach ($field_formulas as $field_formula) {
            $field_formula->formula = str_replace($replacement['original'], $replacement['new'], $field_formula->formula);
            $field_formula->save();
        }

        // update intervals formulas
        $intervals = Interval::getByIdProduct($id_product);
        foreach ($intervals as $interval) {
            $condition_groups = IntervalConditionGroup::getByInterval($interval->id);
            foreach ($condition_groups as $condition_group) {
                $interval_formulas = IntervalFormula::getByConditionGroup($condition_group->id);
                foreach ($interval_formulas as $interval_formula) {
                    $interval_formula->formula = str_replace($replacement['original'], $replacement['new'], $interval_formula->formula);
                    $interval_formula->save();
                }
            }
        }

        return [
            'equations' => DynamicEquation::getEquationsByIdProduct($id_product),
            'conditions' => DynamicCondition::getRowsByProduct($id_product),
            'field_formulas' => FieldFormula::getRowsByProduct($id_product),
            'intervals' => Interval::getRowsByProduct($id_product),
        ];
    }
}
