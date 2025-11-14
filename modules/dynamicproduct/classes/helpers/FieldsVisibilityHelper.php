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

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\DynamicEquation;
use DynamicProduct\classes\models\DynamicInputField;

class FieldsVisibilityHelper
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

    public function getFieldsVisibility($id_product, $id_attribute, $input_fields)
    {
        $conditions_visibility = DynamicEquation::getHiddenItems($id_product, $input_fields);
        $hidden_fields_by_attribute = $this->module->provider->getVisibilityValues($id_product, $id_attribute);
        $hidden_fields = array_merge($conditions_visibility['fields'], array_keys($hidden_fields_by_attribute));
        $conditions_visibility['fields'] = $hidden_fields;

        $visibility_helper = new FieldsVisibilityHelper($this->module, $this->context);
        $visibility_helper->setExcludedFields($input_fields, $conditions_visibility);

        return $conditions_visibility;
    }

    /**
     * @param DynamicInputField[] $input_fields
     * @param $visibility
     */
    public function setExcludedFields(&$input_fields, $visibility)
    {
        foreach ($input_fields as &$input_field) {
            $field = $input_field->getDynamicField();
            if ($this->isHidden($visibility, $input_field->id_field)
                || $this->belongsToHiddenGroup($visibility, $field['id_group'])) {
                $input_field->setExcluded();
            }
            $options = $field['options'];
            $hidden_options = $visibility['options'][$input_field->id_field] ?? [];
            if ($this->hasHiddenOptions($hidden_options, $options)) {
                $input_field->setExcludedOptions($hidden_options, $options);
                if ($input_field->type === _DP_DROPDOWN_ && $input_field->isSelectedOptionExcluded()) {
                    $input_field->selectFirstVisibleOption();
                } elseif ($input_field->type === _DP_RADIO_ && $input_field->isSelectedOptionExcluded()) {
                    $input_field->removeExcludedOptionsFromSelection();
                } elseif ($input_field->type === _DP_THUMBNAILS_ && $input_field->hasExcludedOptions()) {
                    $input_field->removeExcludedOptionsFromSelection();
                }
            }
        }
    }

    private function isHidden($visibility, $id_field)
    {
        if (isset($visibility['fields'][0]) && $visibility['fields'][0] === 0) {
            return true;
        }

        return in_array($id_field, $visibility['fields']);
    }

    private function hasHiddenOptions($hidden_options, $options)
    {
        $option_ids = array_keys($options);
        $intersect = array_intersect($option_ids, $hidden_options);

        return count($intersect) > 0;
    }

    private function belongsToHiddenGroup($visibility, $id_group)
    {
        return in_array($id_group, $visibility['groups']);
    }

    public function mergeArrays(array &$hidden_items1, array &$hidden_items2)
    {
        return [
            'fields' => array_merge($hidden_items1['fields'], $hidden_items2['fields']),
            'options' => DynamicTools::mergeRecursive($hidden_items1['options'], $hidden_items2['options']),
            'groups' => array_merge($hidden_items1['groups'], $hidden_items2['groups']),
            'steps' => array_merge($hidden_items1['steps'], $hidden_items2['steps']),
        ];
    }
}
