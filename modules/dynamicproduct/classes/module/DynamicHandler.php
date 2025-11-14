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
namespace DynamicProduct\classes\module;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\FieldOptionHelper;
use DynamicProduct\classes\models\DynamicCalculationItem;
use DynamicProduct\classes\models\DynamicCombinationField;
use DynamicProduct\classes\models\DynamicCombinationValue;
use DynamicProduct\classes\models\DynamicCommonField;
use DynamicProduct\classes\models\DynamicCondition;
use DynamicProduct\classes\models\DynamicConfig;
use DynamicProduct\classes\models\DynamicDropdownOption;
use DynamicProduct\classes\models\DynamicEquation;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicFieldGroup;
use DynamicProduct\classes\models\DynamicInput;
use DynamicProduct\classes\models\DynamicObject;
use DynamicProduct\classes\models\DynamicPreviewOption;
use DynamicProduct\classes\models\DynamicProductConfigLink;
use DynamicProduct\classes\models\DynamicProductFieldGroup;
use DynamicProduct\classes\models\DynamicProductStep;
use DynamicProduct\classes\models\DynamicProportion;
use DynamicProduct\classes\models\DynamicRadioOption;
use DynamicProduct\classes\models\DynamicStep;
use DynamicProduct\classes\models\DynamicThumbnailsOption;
use DynamicProduct\classes\models\DynamicUnit;
use DynamicProduct\classes\models\DynamicUnitValue;
use DynamicProduct\classes\models\ExecOrder;
use DynamicProduct\classes\models\FieldFormula;
use DynamicProduct\classes\models\grids\Grid;
use DynamicProduct\classes\models\grids\GridColumn;
use DynamicProduct\classes\models\grids\GridRow;
use DynamicProduct\classes\models\grids\GridValue;
use DynamicProduct\classes\models\intervals\Interval;
use DynamicProduct\classes\models\intervals\IntervalCondition;
use DynamicProduct\classes\models\intervals\IntervalConditionGroup;
use DynamicProduct\classes\models\intervals\IntervalConditionRange;
use DynamicProduct\classes\models\intervals\IntervalConditionValue;
use DynamicProduct\classes\models\intervals\IntervalField;
use DynamicProduct\classes\models\intervals\IntervalFormula;

class DynamicHandler
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

    public function copyConfig(
        $id_product_new,
        $id_product_old,
        $copy_options = [],
        $clear = false
    ) {
        $default_options = [
            'steps' => true,
            'groups' => true,
            'fields' => true,
            'equations' => true,
            'combination_values' => true,
            'combinations_visibility' => true,
            'proportions' => true,
            'conditions' => true,
            'field_formulas' => true,
            'intervals' => true,
            'grids' => true,
            'calculation_order' => true,
            'settings' => true,
        ];
        $copy_options = array_merge($default_options, $copy_options);

        if ($clear) {
            $product_fields = DynamicField::getFieldsByIdProduct($id_product_new);
            foreach ($product_fields as $product_field) {
                if ((int) $product_field->id_product === (int) $id_product_new) {
                    $unit_value = $product_field->getUnitValues();
                    $unit_value->delete();
                    $product_field->delete();
                }
                $common_field = DynamicCommonField::getByFieldAndProduct($product_field->id, $id_product_new);
                $common_field->delete();
            }
        }

        $field_groups_new = [];
        $product_steps_new = [];
        $fields_new = [];
        $options_new = [];

        $conditions_new = [];
        $field_formulas_new = [];
        $intervals_new = [];
        $grids_new = [];

        if ($copy_options['steps']) {
            if ($clear) {
                DynamicProductStep::deleteByProduct($id_product_new);
            }
            $product_steps = DynamicProductStep::getByIdProduct($id_product_old);
            foreach ($product_steps as $product_step) {
                $id_product_step = $product_step->id;
                $product_step->id_product = $id_product_new;
                $product_step->add();
                $product_steps_new[$id_product_step] = $product_step->id;
            }
        }

        if ($copy_options['groups']) {
            if ($clear) {
                DynamicProductFieldGroup::deleteByProduct($id_product_new);
            }
            $product_field_groups = DynamicProductFieldGroup::getByIdProduct($id_product_old);
            foreach ($product_field_groups as $product_field_group) {
                $id_step_new = (int) $this->module->provider->getNewID(
                    $product_steps_new,
                    $product_field_group->id_step
                );
                $id_field_group = $product_field_group->id;
                $product_field_group->id_product = $id_product_new;
                $product_field_group->id_step = $id_step_new;
                $product_field_group->add();
                $field_groups_new[$id_field_group] = $product_field_group->id;
            }
        }

        if ($copy_options['fields']) {
            $product_fields = DynamicField::getFieldsByIdProduct($id_product_old);
            foreach ($product_fields as $field) {
                if ($field->deleted) {
                    continue;
                }
                $id_group_new = (int) $this->module->provider->getNewID(
                    $field_groups_new,
                    $field->id_group
                );
                $id_step_new = (int) $this->module->provider->getNewID(
                    $product_steps_new,
                    $field->id_step
                );
                $new_field = $this->copyField(
                    $field->id,
                    $id_product_new,
                    $id_product_old,
                    $id_group_new,
                    $id_step_new,
                    true
                );
                $options_new[$field->id] = $new_field['options'];
                $id_new_field = $new_field['id_field'];
                if ($id_new_field !== (int) $field->id) {
                    $fields_new[(int) $field->id] = $id_new_field;
                }
            }
        }

        if ($copy_options['groups']) {
            $new_field_groups = DynamicProductFieldGroup::getByIdProduct($id_product_new);
            foreach ($new_field_groups as $new_field_group) {
                if ($new_field_group->id_control_field) {
                    $new_field_group->id_control_field = (int) $this->module->provider->getNewID(
                        $fields_new,
                        $new_field_group->id_control_field
                    );
                    $new_field_group->save();
                }
            }
        }

        if ($copy_options['equations']) {
            DynamicEquation::deleteByProduct($id_product_new);
            $product_equations = DynamicEquation::getEquationsByIdProduct($id_product_old);
            foreach ($product_equations as $product_equation) {
                $product_equation->id_product = $id_product_new;
                $product_equation->add();
            }
        }

        $attributes_mapping = [];
        if ($copy_options['combination_values'] && $copy_options['fields']) {
            DynamicCombinationField::deleteByProduct($id_product_new);
            DynamicCombinationValue::deleteByProduct($id_product_new);

            $combination_values = DynamicCombinationValue::getValuesByIdProduct($id_product_old);
            $combination_values = DynamicCombinationValue::organizeByAttributes($combination_values);
            $attributes_old = \Product::getProductAttributesIds($id_product_old);
            $attributes_new = \Product::getProductAttributesIds($id_product_new);

            $attributes_mapping = DynamicTools::mapAttributes($attributes_old, $attributes_new);

            foreach ($attributes_mapping as $id_attribute_old => $id_attribute_new) {
                if (isset($combination_values[$id_attribute_old])) {
                    /** @var DynamicCombinationValue[] $attribute_values */
                    $attribute_values = $combination_values[$id_attribute_old];
                    foreach ($attribute_values as $combination_value) {
                        $combination_value->id_product = $id_product_new;
                        $combination_value->id_attribute = $id_attribute_new;
                        $combination_value->id_field = $this->module->provider->getNewID(
                            $fields_new,
                            $combination_value->id_field
                        );
                        $combination_value->add();
                    }
                }
            }

            $combination_fields = DynamicCombinationField::getByIdProduct($id_product_old);
            foreach ($combination_fields as $combination_field) {
                $combination_field->id_product = $id_product_new;
                $combination_field->id_field = $this->module->provider->getNewID(
                    $fields_new,
                    $combination_field->id_field
                );
                $combination_field->add();
            }
        }

        if ($copy_options['combinations_visibility'] && $copy_options['fields']) {
            \Db::getInstance()->delete('dynamicproduct_visibility', 'id_product = ' . (int) $id_product_new);
            $hidden_fields = $this->module->provider->getVisibilityValues($id_product_old);
            foreach ($attributes_mapping as $id_attribute_old => $id_attribute_new) {
                if (isset($hidden_fields[$id_attribute_old])) {
                    $values = $hidden_fields[$id_attribute_old];
                    foreach ($values as $id_field => $visible) {
                        if (isset($fields_new[$id_field]) || (int) $id_field === 0) {
                            \Db::getInstance()->insert(
                                $this->module->name . '_visibility',
                                [
                                    'id_product' => (int) $id_product_new,
                                    'id_attribute' => (int) $id_attribute_new,
                                    'id_field' => (int) $this->module->provider->getNewID($fields_new, $id_field),
                                    'visible' => (int) $visible,
                                ]
                            );
                        }
                    }
                }
            }
        }

        if ($copy_options['proportions']) {
            if ($clear) {
                DynamicProportion::deleteByProduct($id_product_new);
            }
            $proportions = DynamicProportion::getByProduct($id_product_old);
            foreach ($proportions as $proportion) {
                $proportion->id_product = $id_product_new;
                $proportion->id_field = $this->module->provider->getNewID($fields_new, $proportion->id_field);
                $proportion->id_field_src = $this->module->provider->getNewID($fields_new, $proportion->id_field_src);
                $proportion->add();
            }
        }

        if ($copy_options['conditions']) {
            if ($clear) {
                DynamicCondition::deleteByProduct($id_product_new);
            }
            $conditions = DynamicCondition::getByProduct($id_product_old);
            foreach ($conditions as $condition) {
                $hidden_group = $condition->hidden_groups;
                $hidden_steps = $condition->hidden_steps;
                $hidden_fields = $condition->hidden_fields;
                $hidden_options = $condition->hidden_options;

                $id_condition = $condition->id;
                $condition->id_product = $id_product_new;
                $condition->add();
                $id_condition_new = (int) $condition->id;
                $conditions_new[$id_condition] = $id_condition_new;

                foreach ($hidden_group as $id_group_old) {
                    $id_group_new = $this->module->provider->getNewID($field_groups_new, $id_group_old);
                    $data = [
                        'id_condition' => (int) $id_condition_new,
                        'id_group' => (int) $id_group_new,
                        'visible' => 0,
                    ];
                    \Db::getInstance()->insert(
                        $this->module->name . '_condition_group_visibility',
                        $data,
                        false,
                        true,
                        \Db::REPLACE
                    );
                }

                foreach ($hidden_steps as $id_step_old) {
                    $id_step_new = $this->module->provider->getNewID($product_steps_new, $id_step_old);
                    $data = [
                        'id_condition' => (int) $id_condition_new,
                        'id_step' => (int) $id_step_new,
                        'visible' => 0,
                    ];
                    \Db::getInstance()->insert(
                        $this->module->name . '_condition_step_visibility',
                        $data,
                        false,
                        true,
                        \Db::REPLACE
                    );
                }

                foreach ($hidden_fields as $id_field_old) {
                    $id_field_new = $this->module->provider->getNewID($fields_new, $id_field_old);
                    $data = [
                        'id_condition' => (int) $id_condition_new,
                        'id_field' => (int) $id_field_new,
                        'visible' => 0,
                    ];
                    \Db::getInstance()->insert(
                        $this->module->name . '_condition_visibility',
                        $data,
                        false,
                        true,
                        \Db::REPLACE
                    );
                }

                foreach ($hidden_options as $id_field_old => $options) {
                    foreach ($options as $id_option_old) {
                        $id_field_new = $this->module->provider->getNewID($fields_new, $id_field_old);
                        $id_option_new = $this->module->provider->getNewOption($options_new, $id_field_old, $id_option_old);
                        $data = [
                            'id_condition' => (int) $id_condition_new,
                            'id_field' => (int) $id_field_new,
                            'id_option' => (int) $id_option_new,
                            'visible' => 0,
                        ];
                        \Db::getInstance()->insert(
                            $this->module->name . '_condition_option_visibility',
                            $data,
                            false,
                            true,
                            \Db::REPLACE
                        );
                    }
                }
            }
        }

        if ($copy_options['field_formulas']) {
            if ($clear) {
                FieldFormula::deleteByProduct($id_product_new);
            }
            $field_formulas = FieldFormula::getByProduct($id_product_old);
            foreach ($field_formulas as $field_formula) {
                $id_field_formula = $field_formula->id;
                $field_formula->id_product = $id_product_new;
                $field_formula->add();
                $field_formulas_new[$id_field_formula] = $field_formula->id;
            }
        }

        if ($copy_options['intervals']) {
            if ($clear) {
                Interval::deleteByProduct($id_product_new);
            }
            $new_interval_fields = [];
            $intervals = Interval::getByIdProduct($id_product_old);
            foreach ($intervals as $interval) {
                $id_interval = $interval->id;
                $interval->id_product = $id_product_new;
                $interval->add();
                $intervals_new[$id_interval] = $interval->id;

                $interval_fields = IntervalField::getByInterval($id_interval);
                foreach ($interval_fields as $interval_field) {
                    $id_interval_field = $interval_field->id;
                    $interval_field->id_interval = $interval->id;
                    $id_field_new = $this->module->provider->getNewID($fields_new, $interval_field->id_field);
                    $interval_field->id_field = $id_field_new;
                    $interval_field->add();
                    $new_interval_fields[$id_interval_field] = $interval_field->id;
                }

                $condition_groups = IntervalConditionGroup::getByInterval($id_interval);
                foreach ($condition_groups as $condition_group) {
                    $id_condition_group = $condition_group->id;
                    $condition_group->id_interval = $interval->id;
                    $condition_group->add();

                    $interval_conditions = IntervalCondition::getByIntervalConditionGroup($id_condition_group);
                    foreach ($interval_conditions as $interval_condition) {
                        $id_interval_condition = $interval_condition->id;
                        $interval_condition->id_interval_condition_group = $condition_group->id;
                        $id_field_new = $this->module->provider->getNewID($fields_new, $interval_condition->id_field);
                        $interval_condition->id_field = $id_field_new;
                        $interval_condition->add();

                        $condition_range = IntervalConditionRange::getByIntervalCondition($id_interval_condition);
                        if (\Validate::isLoadedObject($condition_range)) {
                            $condition_range->id_interval_condition = $interval_condition->id;
                            $condition_range->add();
                        }

                        $condition_values = IntervalConditionValue::getByIntervalCondition($id_interval_condition);
                        foreach ($condition_values as $condition_value) {
                            $condition_value->id_interval_condition = $interval_condition->id;
                            $condition_value->add();
                        }
                    }

                    $interval_formulas = IntervalFormula::getByConditionGroup($id_condition_group);
                    foreach ($interval_formulas as $interval_formula) {
                        $interval_formula->id_interval_condition_group = $condition_group->id;
                        $interval_formula->id_interval_field = $new_interval_fields[$interval_formula->id_interval_field];
                        $interval_formula->add();
                    }
                }
            }
        }

        if ($copy_options['grids']) {
            if ($clear) {
                Grid::deleteByProduct($id_product_new);
            }
            $grids = Grid::getByIdProduct($id_product_old);
            foreach ($grids as $grid) {
                $grid->id_product = $id_product_new;
                $grid->id_field_column = $this->module->provider->getNewID(
                    $fields_new,
                    $grid->id_field_column
                );
                $grid->id_field_row = $this->module->provider->getNewID(
                    $fields_new,
                    $grid->id_field_row
                );
                $grid->id_field_target = $this->module->provider->getNewID(
                    $fields_new,
                    $grid->id_field_target
                );
                $id_grid = $grid->id;
                $grid->add();
                $id_grid_new = $grid->id;
                $grids_new[$id_grid] = $id_grid_new;

                $columns = [];
                foreach ($grid->columns as $column) {
                    $id_column = $column->id;
                    $column->id_grid = $id_grid_new;
                    $column->add();
                    $columns[$id_column] = $column->id;
                }

                $rows = [];
                foreach ($grid->rows as $row) {
                    $id_row = $row->id;
                    $row->id_grid = $id_grid_new;
                    $row->add();
                    $rows[$id_row] = $row->id;
                }

                foreach ($grid->values as $value) {
                    $value->id_grid = $id_grid_new;
                    $value->id_grid_column = isset($columns[$value->id_grid_column]) ? $columns[$value->id_grid_column] : 0;
                    $value->id_grid_row = isset($rows[$value->id_grid_row]) ? $rows[$value->id_grid_row] : 0;
                    $value->add();
                }
            }
        }

        if ($copy_options['calculation_order']) {
            ExecOrder::deleteByProduct($id_product_new);
            $exec_orders = ExecOrder::getByIdProduct($id_product_old);
            foreach ($exec_orders as $exec_order) {
                $exec_order->id_product = $id_product_new;
                $exec_order->add();
            }

            if ($clear) {
                DynamicCalculationItem::deleteByProduct($id_product_new);
            }
            $calculation_items = DynamicCalculationItem::getByIdProduct($id_product_old);
            foreach ($calculation_items as $calculation_item) {
                $calculation_item->id_product = $id_product_new;
                $item_id_new = $calculation_item->id_item;
                $is_one_step_item = $calculation_item->id_item === 0;
                if (!$is_one_step_item) {
                    switch ($calculation_item->type) {
                        case DynamicCalculationItem::CONDITION_ITEM:
                            $item_id_new = $conditions_new[$calculation_item->id_item] ?? 0;
                            break;
                        case DynamicCalculationItem::FIELD_FORMULA_ITEM:
                            $item_id_new = $field_formulas_new[$calculation_item->id_item] ?? 0;
                            break;
                        case DynamicCalculationItem::INTERVAL_ITEM:
                            $item_id_new = $intervals_new[$calculation_item->id_item] ?? 0;
                            break;
                        case DynamicCalculationItem::GRID_ITEM:
                            $item_id_new = $grids_new[$calculation_item->id_item] ?? 0;
                            break;
                    }
                    $calculation_item->id_item = $item_id_new;
                }
                if ($item_id_new || $is_one_step_item) {
                    $calculation_item->add();
                }
            }
        }

        if ($copy_options['settings']) {
            $product_config = DynamicConfig::getByProduct($id_product_new);
            $product_config->delete();
            $product_config = DynamicConfig::getByProduct($id_product_old);
            $product_config->id_product = (int) $id_product_new;
            $product_config->save();
        }
    }

    public function clearConfig($id_product)
    {
        $product_fields = DynamicField::getFieldsByIdProduct($id_product);
        foreach ($product_fields as $product_field) {
            if ((int) $product_field->id_product === (int) $id_product) {
                $unit_value = $product_field->getUnitValues();
                $unit_value->delete();
                $product_field->delete();
            }
            $common_field = DynamicCommonField::getByFieldAndProduct($product_field->id, $id_product);
            $common_field->delete();
        }

        DynamicProductFieldGroup::deleteByProduct($id_product);

        DynamicCombinationValue::deleteByProduct($id_product);
        \Db::getInstance()->delete('dynamicproduct_visibility', 'id_product = ' . (int) $id_product);

        DynamicEquation::deleteByProduct($id_product);

        DynamicProportion::deleteByProduct($id_product);

        DynamicCondition::deleteByProduct($id_product);

        FieldFormula::deleteByProduct($id_product);

        Interval::deleteByProduct($id_product);

        Grid::deleteByProduct($id_product);

        ExecOrder::deleteByProduct($id_product);

        $product_config = new DynamicConfig($id_product);
        $product_config->delete();
    }

    public function exportConfig($id_product, $link_images): array
    {
        $data = [
            'version' => $this->module->version,
            'link_images' => $link_images,
        ];

        $languages = \Language::getLanguages(false);
        $lang_data = [];
        foreach ($languages as $language) {
            $lang_data[(int) $language['id_lang']] = $language['iso_code'];
        }
        $data['lang_data'] = $lang_data;

        $data['id_lang_default'] = (int) \Configuration::get('PS_LANG_DEFAULT');

        $units = [];

        $product_fields = DynamicField::getFieldsByIdProduct($id_product);
        foreach ($product_fields as $product_field) {
            $field_values = $product_field->getObjectValues();
            $image_path = $product_field->getImage();
            $thumb_path = $product_field->getThumb();
            if ($image_path) {
                $field_values['image_data'] =
                    $link_images ? $product_field->getImageUrl() : DynamicTools::encodeFile($image_path);
            }
            if ($thumb_path) {
                $field_values['thumb_data'] =
                    $link_images ? $product_field->getThumbUrl() : DynamicTools::encodeFile($thumb_path);
            }

            if (count($product_field->options)) {
                foreach ($product_field->options as $option) {
                    /** @var DynamicDropdownOption $option_obj */
                    $option_obj = FieldOptionHelper::getOptionInstance($option['id'], $product_field->type);
                    if ($option_obj) {
                        $image_path = $option_obj->getImage();
                        $thumb_path = $option_obj->getThumb();
                        if ($image_path) {
                            $field_values['options'][$option['id']]['image_data'] =
                                $link_images ? $option_obj->getImageUrl() : DynamicTools::encodeFile($image_path);
                        }
                        if ($thumb_path) {
                            $field_values['options'][$option['id']]['thumb_data'] =
                                $link_images ? $option_obj->getThumbUrl() : DynamicTools::encodeFile($thumb_path);
                        }
                        if (property_exists($option_obj, 'preview') && $option_obj->preview) {
                            $preview_path = $option_obj->getPreview();
                            $preview_thumb_path = $option_obj->getPreviewThumb();
                            if ($preview_path) {
                                $field_values['options'][$option['id']]['preview_data'] =
                                    $link_images ?
                                        $option_obj->getPreviewUrl() :
                                        DynamicTools::encodeFile($preview_path);
                            }
                            if ($preview_thumb_path) {
                                $field_values['options'][$option['id']]['preview_thumb_data'] =
                                    $link_images ?
                                        $option_obj->getPreviewThumbUrl() :
                                        DynamicTools::encodeFile($preview_thumb_path);
                            }
                        }
                    }
                }
            }

            $data['fields'][$product_field->id] = $field_values;
            $id_unit = (int) $product_field->id_unit;
            if ($id_unit) {
                $unit = new DynamicUnit($id_unit);
                $units[$id_unit] = $unit->getObjectValues(true);
            }
        }
        $data['units'] = $units;

        $product_equations = DynamicEquation::getEquationsByIdProduct($id_product);
        foreach ($product_equations as $product_equation) {
            $data['equations'][$product_equation->id] = $product_equation->getObjectValues();
        }

        $combination_values = DynamicCombinationValue::getValuesByIdProduct($id_product);

        $data['combination_values'] = [];

        $attributes_old = \Product::getProductAttributesIds($id_product);
        $attributes_new = $this->createDummyAttributes(count($attributes_old));
        $attributes_mapping = DynamicTools::mapAttributes($attributes_old, $attributes_new);

        foreach ($combination_values as $combination_value) {
            $combination_value->id_attribute = $attributes_mapping[$combination_value->id_attribute];
            $data['combination_values'][$combination_value->id] = $combination_value->getObjectValues();
        }

        $combination_fields = DynamicCombinationField::getByIdProduct($id_product);
        foreach ($combination_fields as $combination_field) {
            $data['combination_fields'][$combination_field->id] = $combination_field->getObjectValues();
        }

        $fields_visibility_values = $this->module->provider->getVisibilityValues($id_product);
        $mapped_visibility_values = [];

        foreach ($fields_visibility_values as $id_attribute => $visibility_values) {
            $mapped_id = $attributes_mapping[$id_attribute];
            $mapped_visibility_values[$mapped_id] = $visibility_values;
        }
        $data['fields_visibility_values'] = $mapped_visibility_values;

        $product_config = DynamicConfig::getByProduct($id_product);
        $data['config'] = DynamicObject::getValues($product_config);

        $proportions = DynamicProportion::getByIdProduct($id_product);
        foreach ($proportions as $proportion) {
            $data['proportions'][$proportion->id] = $proportion->getObjectValues();
        }

        $conditions = DynamicCondition::getByIdProduct($id_product);
        foreach ($conditions as $condition) {
            $data['conditions'][$condition->id] = $condition->getObjectValues();
        }

        $field_formulas = FieldFormula::getByIdProduct($id_product);
        foreach ($field_formulas as $field_formula) {
            $data['field_formulas'][$field_formula->id] = $field_formula->getObjectValues();
        }

        $intervals = Interval::getByIdProduct($id_product);
        foreach ($intervals as $interval) {
            $data['intervals'][$interval->id] = $interval->getObjectValues();
        }

        $grids = Grid::getByIdProduct($id_product);
        foreach ($grids as $grid) {
            $data['grids'][$grid->id] = $grid->getObjectValues();
        }

        $field_groups = [];
        $product_field_groups = DynamicProductFieldGroup::getRowsByProduct($id_product);
        foreach ($product_field_groups as $field_group) {
            $data['product_field_groups'][$field_group['id']] = $field_group;
            $id_field_group = (int) $field_group['id_field_group'];
            $field_group = new DynamicFieldGroup($id_field_group);
            $field_groups[$id_field_group] = $field_group->getObjectValues(true);
        }
        $data['field_groups'] = $field_groups;

        $steps = [];
        $product_steps = DynamicProductStep::getRowsByProduct($id_product);
        foreach ($product_steps as $product_step) {
            $data['product_steps'][$product_step['id']] = $product_step;
            $id_step = (int) $product_step['id_step'];
            $step = new DynamicStep($id_step);
            $steps[$id_step] = $step->getObjectValues(true);
        }
        $data['steps'] = $steps;

        $exec_orders = ExecOrder::getByIdProduct($id_product);
        foreach ($exec_orders as $exec_order) {
            $data['exec_orders'][$exec_order->id] = $exec_order->getObjectValues();
        }

        $calculation_items = DynamicCalculationItem::getRowsByProduct($id_product);
        foreach ($calculation_items as $calculation_item) {
            $data['calculation_items'][$calculation_item['id']] = $calculation_item;
        }

        return $data;
    }

    public function importConfig($id_product_new, $data): void
    {
        DynamicProductConfigLink::removeLink($id_product_new);

        $link_images = $data['link_images'] ?? false;

        $lang_codes = [];
        $lang_data = $data['lang_data'] ?? null;
        if ($lang_data) {
            $languages = \Language::getLanguages(false);
            foreach ($languages as $language) {
                $lang_codes[$language['iso_code']] = (int) $language['id_lang'];
            }
        }

        $first_lang_id = is_array($lang_data) && count(array_keys($lang_data)) ? array_keys($lang_data)[0] : 0;
        $id_lang_default = isset($data['id_lang_default']) ? (int) $data['id_lang_default'] : $first_lang_id;
        $current_lang = (int) \Configuration::get('PS_LANG_DEFAULT');

        $product_fields = DynamicField::getFieldsByIdProduct($id_product_new);
        foreach ($product_fields as $product_field) {
            if (!(int) $product_field->common || (int) $product_field->id_product === $id_product_new) {
                $unit_value = $product_field->getUnitValues();
                $unit_value->delete();
                $product_field->delete();
            }
            $common_field = DynamicCommonField::getByFieldAndProduct($product_field->id, $id_product_new);
            $common_field->delete();
        }

        $units_new = [];
        $field_groups_new = [];
        $steps_new = [];
        $product_field_groups_new = [];
        $product_steps_new = [];
        $fields_new = [];
        $options_new = [];

        $conditions_new = [];
        $field_formulas_new = [];
        $intervals_new = [];
        $grids_new = [];

        $create_new_items = isset($data['version']);

        if ($create_new_items) {
            if (isset($data['units'])) {
                /** @var DynamicUnit[] $units */
                $units = $this->getObjectsFromData($data['units'], DynamicUnit::class);
                foreach ($units as $unit) {
                    if (isset($unit->name[$id_lang_default])) {
                        $id_unit_old = (int) $unit->id;
                        $existing = $this->findUnit($id_unit_old, $unit->symbol);
                        if ($existing) {
                            $units_new[$id_unit_old] = $existing->id;
                        } else {
                            $unit->name[$current_lang] = $unit->name[$id_lang_default];
                            $unit->add();
                            $units_new[$id_unit_old] = (int) $unit->id;
                        }
                    }
                }
            }

            if (isset($data['field_groups'])) {
                /** @var DynamicFieldGroup[] $field_groups */
                $field_groups = $this->getObjectsFromData($data['field_groups'], DynamicFieldGroup::class);
                foreach ($field_groups as $field_group) {
                    if (isset($field_group->label[$id_lang_default])) {
                        $id_field_group_old = (int) $field_group->id;
                        $label = $field_group->label[$id_lang_default];
                        $existing = $this->findFieldGroup($id_field_group_old, $label);
                        if ($existing) {
                            $field_groups_new[$id_field_group_old] = $existing->id;
                        } else {
                            $field_group->label[$current_lang] = $label;
                            $field_group->add();
                            $field_groups_new[$id_field_group_old] = (int) $field_group->id;
                        }
                    }
                }
            }

            if (isset($data['steps'])) {
                /** @var DynamicStep[] $steps */
                $steps = $this->getObjectsFromData($data['steps'], DynamicStep::class);
                foreach ($steps as $step) {
                    if (isset($step->label[$id_lang_default])) {
                        $id_step_old = (int) $step->id;
                        $label = $step->label[$id_lang_default];
                        $existing = $this->findStep($id_step_old, $label);
                        if ($existing) {
                            $steps_new[$id_step_old] = $existing->id;
                        } else {
                            $step->label[$current_lang] = $label;
                            $step->add();
                            $steps_new[$id_step_old] = (int) $step->id;
                        }
                    }
                }
            }
        }

        DynamicProductStep::deleteByProduct($id_product_new);
        if (isset($data['product_steps'])) {
            /** @var DynamicProductStep[] $product_steps */
            $product_steps = $this->getObjectsFromData($data['product_steps'], DynamicProductStep::class);
            foreach ($product_steps as $product_step) {
                $id_product_step = $product_step->id;
                $id_step_new = (int) $this->module->provider->getNewID(
                    $steps_new,
                    $product_step->id_step
                );
                $product_step->id_product = $id_product_new;
                $product_step->id_step = $id_step_new;
                $product_step->add();
                $product_steps_new[$id_product_step] = $product_step->id;
            }
        }

        $product_field_groups_key = $create_new_items ? 'product_field_groups' : 'field_groups';
        DynamicProductFieldGroup::deleteByProduct($id_product_new);
        if (isset($data[$product_field_groups_key])) {
            /** @var DynamicProductFieldGroup[] $product_field_groups */
            $product_field_groups = $this->getObjectsFromData(
                $data[$product_field_groups_key],
                DynamicProductFieldGroup::class
            );
            foreach ($product_field_groups as $product_field_group) {
                $id_product_step_new = (int) $this->module->provider->getNewID(
                    $product_steps_new,
                    $product_field_group->id_step
                );
                $id_field_group_new = (int) $this->module->provider->getNewID(
                    $field_groups_new,
                    $product_field_group->id_field_group
                );
                $id_product_field_group = $product_field_group->id;
                $product_field_group->id_product = $id_product_new;
                $product_field_group->id_field_group = $id_field_group_new;
                $product_field_group->id_step = $id_product_step_new;
                $product_field_group->add();
                $product_field_groups_new[$id_product_field_group] = $product_field_group->id;
            }
        }

        $option_types = [
            _DP_DROPDOWN_ => DynamicDropdownOption::class,
            _DP_THUMBNAILS_ => DynamicThumbnailsOption::class,
            _DP_RADIO_ => DynamicRadioOption::class,
            _DP_PREVIEW_ => DynamicPreviewOption::class,
        ];

        if (isset($data['fields'])) {
            /** @var DynamicField[] $product_fields */
            $product_fields = $this->getObjectsFromData($data['fields'], DynamicField::class);
            foreach ($product_fields as $field) {
                $id_group_new = (int) $this->module->provider->getNewID(
                    $product_field_groups_new,
                    $field->id_group
                );
                $id_product_step_new = (int) $this->module->provider->getNewID(
                    $product_steps_new,
                    $field->id_step
                );
                $id_unit_new = (int) $this->module->provider->getNewID(
                    $units_new,
                    $field->id_unit
                );
                $field->id_product = (int) $id_product_new;
                $field->id_unit = (int) $id_unit_new;
                $field->id_group = $id_group_new;
                $field->id_step = $id_product_step_new;
                $field->favorite = false;
                $id_field_old = $field->id;
                $this->remapLangIds($field, $lang_data, $lang_codes, $id_lang_default);
                $field->add();

                $field_data = $data['fields'][$id_field_old];

                $unit_value = DynamicUnitValue::copyFromArray($field_data['settings'], null, false);
                $unit_value->id_field = $field->id;
                $unit_value->add();

                $image_data = $field_data['image_data'] ?? null;
                if ($image_data) {
                    $content = $link_images ? self::downloadLink($image_data) : DynamicTools::decodeData($image_data);
                    if ($content) {
                        $extention = pathinfo($field->image, PATHINFO_EXTENSION);
                        $field->image = $field->id . '.' . $extention;
                        $field->save();
                        $image_path = $field->getPathForCreation('id');
                        file_put_contents($image_path, $content);
                    }
                }

                $thumb_data = $field_data['thumb_data'] ?? null;
                if ($thumb_data) {
                    $content = $link_images ? self::downloadLink($thumb_data) : DynamicTools::decodeData($thumb_data);
                    if ($content) {
                        $thumb_path = $field->getThumbPathForCreation('id');
                        file_put_contents($thumb_path, $content);
                    }
                }

                $id_field_new = (int) $field->id;

                $field_options_new = [];
                $options_class = $option_types[$field->type] ?? null;
                if ($options_class) {
                    /** @var DynamicDropdownOption[] $options */
                    $options_data = $data['fields'][$id_field_old]['options'];
                    $options = $this->getObjectsFromData($options_data, $options_class);
                    foreach ($options as $option) {
                        $id_option = $option->id;
                        $option->id_field = $id_field_new;
                        $this->remapLangIds($option, $lang_data, $lang_codes, $id_lang_default);
                        $option->add();

                        $option_data = $options_data[$id_option];
                        $image_data = $option_data['image_data'] ?? null;
                        if ($image_data) {
                            $content = $link_images ?
                                self::downloadLink($image_data) :
                                DynamicTools::decodeData($image_data);
                            if ($content) {
                                $extension = pathinfo($option->image, PATHINFO_EXTENSION);
                                $image = "{$option->id}.{$extension}";
                                $option->image = $image;
                                $image_path = $option->getPathForCreation('image');
                                file_put_contents($image_path, $content);
                            }
                        }

                        $thumb_data = $option_data['thumb_data'] ?? null;
                        if ($thumb_data) {
                            $content = $link_images ?
                                self::downloadLink($thumb_data) :
                                DynamicTools::decodeData($thumb_data);
                            $thumb_path = $option->getThumbPathForCreation('image');
                            file_put_contents($thumb_path, $content);
                        }

                        $preview_data = $option_data['preview_data'] ?? null;
                        if ($preview_data) {
                            $content = $link_images ?
                                self::downloadLink($preview_data) :
                                DynamicTools::decodeData($preview_data);
                            if ($content) {
                                $extension = pathinfo($option->preview, PATHINFO_EXTENSION);
                                $preview = "{$option->id}-preview.{$extension}";
                                $option->preview = $preview;
                                $preview_path = $option->getPathForCreation('preview');
                                file_put_contents($preview_path, $content);
                            }
                        }

                        $preview_thumb_data = $option_data['preview_thumb_data'] ?? null;
                        if ($preview_thumb_data) {
                            $content = $link_images ?
                                self::downloadLink($preview_thumb_data) :
                                DynamicTools::decodeData($preview_thumb_data);
                            if ($content) {
                                $preview_thumb_path = $option->getThumbPathForCreation('preview');
                                file_put_contents($preview_thumb_path, $content);
                            }
                        }

                        $option->save();
                        $field_options_new[$id_option] = $option->id;
                    }
                }
                $options_new[$id_field_old] = $field_options_new;
                $fields_new[$id_field_old] = $id_field_new;
            }
        }

        $new_field_groups = DynamicProductFieldGroup::getByIdProduct($id_product_new);
        foreach ($new_field_groups as $new_field_group) {
            if ($new_field_group->id_control_field) {
                $new_field_group->id_control_field = (int) $this->module->provider->getNewID(
                    $fields_new,
                    $new_field_group->id_control_field
                );
                $new_field_group->save();
            }
        }

        DynamicEquation::deleteByProduct($id_product_new);
        if (isset($data['equations'])) {
            $equations = $this->getObjectsFromData($data['equations'], DynamicEquation::class);
            foreach ($equations as $equation) {
                $equation->id_product = $id_product_new;
                $equation->add();
            }
        }

        $attributes_new = \Product::getProductAttributesIds($id_product_new);
        $attributes_old = $this->createDummyAttributes(count($attributes_new));
        $attributes_mapping = DynamicTools::mapAttributes($attributes_old, $attributes_new);

        DynamicCombinationValue::deleteByProduct($id_product_new);
        if (isset($data['combination_values'])) {
            /** @var DynamicCombinationValue[] $combination_values */
            $combination_values = $this->getObjectsFromData(
                $data['combination_values'],
                DynamicCombinationValue::class
            );
            foreach ($combination_values as $combination_value) {
                if (isset($attributes_mapping[$combination_value->id_attribute])) {
                    $combination_value->id_product = $id_product_new;
                    $combination_value->id_attribute = $attributes_mapping[$combination_value->id_attribute];
                    $combination_value->id_field = $this->module->provider->getNewID(
                        $fields_new,
                        $combination_value->id_field
                    );
                    $combination_value->add();
                }
            }
        }

        if (isset($data['combination_fields'])) {
            DynamicCombinationField::deleteByProduct($id_product_new);
            /** @var DynamicCombinationField[] $combination_fields */
            $combination_fields = $this->getObjectsFromData(
                $data['combination_fields'],
                DynamicCombinationField::class
            );
            foreach ($combination_fields as $combination_field) {
                $combination_field->id_product = $id_product_new;
                $combination_field->id_field = $this->module->provider->getNewID(
                    $fields_new,
                    $combination_field->id_field
                );
                $combination_field->add();
            }
        }

        if (isset($data['hidden_fields'])) {
            \Db::getInstance()->delete('dynamicproduct_visibility', 'id_product = ' . (int) $id_product_new);
            $hidden_fields = $data['hidden_fields'];
            foreach ($attributes_mapping as $id_attribute_old => $id_attribute_new) {
                if (isset($hidden_fields[$id_attribute_old])) {
                    $values = $hidden_fields[$id_attribute_old];
                    foreach ($values as $id_field => $visible) {
                        if (isset($fields_new[$id_field]) || (int) $id_field === 0) {
                            \Db::getInstance()->insert(
                                $this->module->name . '_visibility',
                                [
                                    'id_product' => (int) $id_product_new,
                                    'id_attribute' => (int) $id_attribute_new,
                                    'id_field' => (int) $this->module->provider->getNewID($fields_new, $id_field),
                                    'visible' => (int) $visible,
                                ]
                            );
                        }
                    }
                }
            }
        }

        DynamicProportion::deleteByProduct($id_product_new);
        if (isset($data['proportions'])) {
            $proportions = $this->getObjectsFromData($data['proportions'], DynamicProportion::class);
            foreach ($proportions as $proportion) {
                $proportion->id_product = $id_product_new;
                $proportion->id_field = $this->module->provider->getNewID($fields_new, $proportion->id_field);
                $proportion->id_field_src = $this->module->provider->getNewID($fields_new, $proportion->id_field_src);
                $proportion->add();
            }
        }

        DynamicCondition::deleteByProduct($id_product_new);
        if (isset($data['conditions'])) {
            /** @var DynamicCondition[] $conditions */
            $conditions = $this->getObjectsFromData($data['conditions'], DynamicCondition::class);
            foreach ($conditions as $condition) {
                $hidden_groups = $data['conditions'][$condition->id]['hidden_groups'];
                $hidden_steps = $data['conditions'][$condition->id]['hidden_steps'];
                $hidden_fields = $data['conditions'][$condition->id]['hidden_fields'];
                $hidden_options = $data['conditions'][$condition->id]['hidden_options'];

                $condition->id_product = $id_product_new;
                $id_condition = (int) $condition->id;
                $condition->add();
                $id_condition_new = (int) $condition->id;
                $conditions_new[$id_condition] = $id_condition_new;

                foreach ($hidden_groups as $id_group_old) {
                    $id_group_new = $this->module->provider->getNewID($product_field_groups_new, $id_group_old);
                    $visibility_data = [
                        'id_condition' => (int) $id_condition_new,
                        'id_group' => (int) $id_group_new,
                        'visible' => 0,
                    ];
                    \Db::getInstance()->insert(
                        $this->module->name . '_condition_group_visibility',
                        $visibility_data,
                        false,
                        true,
                        \Db::REPLACE
                    );
                }

                foreach ($hidden_steps as $id_step_old) {
                    $id_product_step_new = $this->module->provider->getNewID($product_steps_new, $id_step_old);
                    $visibility_data = [
                        'id_condition' => (int) $id_condition_new,
                        'id_step' => (int) $id_product_step_new,
                        'visible' => 0,
                    ];
                    \Db::getInstance()->insert(
                        $this->module->name . '_condition_step_visibility',
                        $visibility_data,
                        false,
                        true,
                        \Db::REPLACE
                    );
                }

                foreach ($hidden_fields as $id_field_old) {
                    $id_field_new = $this->module->provider->getNewID($fields_new, $id_field_old);
                    $visibility_data = [
                        'id_condition' => (int) $id_condition_new,
                        'id_field' => (int) $id_field_new,
                        'visible' => 0,
                    ];
                    \Db::getInstance()->insert(
                        $this->module->name . '_condition_visibility',
                        $visibility_data,
                        false,
                        true,
                        \Db::REPLACE
                    );
                }

                foreach ($hidden_options as $id_field_old => $options) {
                    foreach ($options as $id_option_old) {
                        $id_field_new = $this->module->provider->getNewID($fields_new, $id_field_old);
                        $id_option_new = $this->module->provider->getNewOption(
                            $options_new,
                            $id_field_old,
                            $id_option_old
                        );
                        $visibility_data = [
                            'id_condition' => (int) $id_condition_new,
                            'id_field' => (int) $id_field_new,
                            'id_option' => (int) $id_option_new,
                            'visible' => 0,
                        ];
                        \Db::getInstance()->insert(
                            $this->module->name . '_condition_option_visibility',
                            $visibility_data,
                            false,
                            true,
                            \Db::REPLACE
                        );
                    }
                }
            }
        }

        FieldFormula::deleteByProduct($id_product_new);
        if (isset($data['field_formulas'])) {
            $field_formulas = $this->getObjectsFromData($data['field_formulas'], FieldFormula::class);
            foreach ($field_formulas as $field_formula) {
                $id_field_formula = $field_formula->id;
                $field_formula->id_product = $id_product_new;
                $field_formula->add();
                $field_formulas_new[$id_field_formula] = $field_formula->id;
            }
        }

        Interval::deleteByProduct($id_product_new);
        if (isset($data['intervals'])) {
            $new_interval_fields = [];
            $intervals = $this->getObjectsFromData($data['intervals'], Interval::class);
            foreach ($intervals as $interval) {
                $id_interval = $interval->id;
                $interval->id_product = $id_product_new;
                $interval->add();
                $intervals_new[$id_interval] = $interval->id;

                $interval_fields = $this->getObjectsFromData(
                    $data['intervals'][$id_interval]['interval_fields'],
                    IntervalField::class
                );
                foreach ($interval_fields as $interval_field) {
                    $id_interval_field = $interval_field->id;
                    $interval_field->id_interval = $interval->id;
                    $id_field_new = $this->module->provider->getNewID($fields_new, $interval_field->id_field);
                    $interval_field->id_field = $id_field_new;
                    $interval_field->add();
                    $new_interval_fields[$id_interval_field] = $interval_field->id;
                }

                $condition_groups = $this->getObjectsFromData(
                    $data['intervals'][$id_interval]['condition_groups'],
                    IntervalConditionGroup::class
                );
                foreach ($condition_groups as $condition_group) {
                    $id_condition_group = $condition_group->id;
                    $condition_group->id_interval = $interval->id;
                    $condition_group->add();

                    $conditionGroupData =
                        $data['intervals'][$id_interval]['condition_groups'][$id_condition_group];
                    $interval_conditions = $this->getObjectsFromData(
                        $conditionGroupData['conditions'],
                        IntervalCondition::class
                    );
                    foreach ($interval_conditions as $interval_condition) {
                        $id_interval_condition = $interval_condition->id;
                        $interval_condition->id_interval_condition_group = $condition_group->id;
                        $id_field_new = $this->module->provider->getNewID($fields_new, $interval_condition->id_field);
                        $interval_condition->id_field = $id_field_new;
                        $interval_condition->add();

                        if ($interval_condition->type === 'range') {
                            $condition_range = IntervalConditionRange::copyFromArray(
                                $conditionGroupData['conditions'][$id_interval_condition]['condition_range']
                            );
                            $condition_range->id_interval_condition = $interval_condition->id;
                            $condition_range->add();
                        }

                        if ($interval_condition->type === 'values') {
                            $condition_values = $this->getObjectsFromData(
                                $conditionGroupData['conditions'][$id_interval_condition]['condition_value'],
                                IntervalConditionValue::class
                            );
                            foreach ($condition_values as $condition_value) {
                                $condition_value->id_interval_condition = $interval_condition->id;
                                $condition_value->add();
                            }
                        }
                    }

                    $interval_formulas = $this->getObjectsFromData(
                        $conditionGroupData['interval_formulas'],
                        IntervalFormula::class
                    );
                    foreach ($interval_formulas as $interval_formula) {
                        $interval_formula->id_interval_condition_group = $condition_group->id;
                        if (isset($new_interval_fields[$interval_formula->id_interval_field])) {
                            $interval_formula->id_interval_field =
                                $new_interval_fields[$interval_formula->id_interval_field];
                            $interval_formula->add();
                        }
                    }
                }
            }
        }

        Grid::deleteByProduct($id_product_new);
        if (isset($data['grids'])) {
            $grids = $this->getObjectsFromData($data['grids'], Grid::class);
            foreach ($grids as $grid) {
                $id_grid_old = $grid->id;
                $grid->id_product = $id_product_new;
                $grid->id_field_column = $this->module->provider->getNewID(
                    $fields_new,
                    $grid->id_field_column
                );
                $grid->id_field_row = $this->module->provider->getNewID(
                    $fields_new,
                    $grid->id_field_row
                );
                $grid->id_field_target = $this->module->provider->getNewID(
                    $fields_new,
                    $grid->id_field_target
                );
                $grid->add();
                $id_grid_new = $grid->id;
                $grids_new[$id_grid_old] = $id_grid_new;

                $columns = [];
                $grid->columns = $this->getObjectsFromData($data['grids'][$id_grid_old]['columns'], GridColumn::class);
                foreach ($grid->columns as $column) {
                    $id_column = $column->id;
                    $column->id_grid = $id_grid_new;
                    $column->add();
                    $columns[$id_column] = $column->id;
                }

                $rows = [];
                $grid->rows = $this->getObjectsFromData($data['grids'][$id_grid_old]['rows'], GridRow::class);
                foreach ($grid->rows as $row) {
                    $id_row = $row->id;
                    $row->id_grid = $id_grid_new;
                    $row->add();
                    $rows[$id_row] = $row->id;
                }

                $grid->values = $this->getObjectsFromData($data['grids'][$id_grid_old]['values'], GridValue::class);
                foreach ($grid->values as $value) {
                    $value->id_grid = $id_grid_new;
                    $value->id_grid_column = $columns[$value->id_grid_column] ?? 0;
                    $value->id_grid_row = $rows[$value->id_grid_row] ?? 0;
                    $value->add();
                }
            }
        }

        ExecOrder::deleteByProduct($id_product_new);
        if (isset($data['exec_orders'])) {
            $exec_orders = $this->getObjectsFromData($data['exec_orders'], ExecOrder::class);
            foreach ($exec_orders as $exec_order) {
                $exec_order->id_product = $id_product_new;
                $exec_order->add();
            }
        }

        DynamicCalculationItem::deleteByProduct($id_product_new);
        if (isset($data['calculation_items'])) {
            $calculation_items = $this->getObjectsFromData($data['calculation_items'], DynamicCalculationItem::class);
            foreach ($calculation_items as $calculation_item) {
                $calculation_item->id_product = $id_product_new;
                $item_id_new = $calculation_item->id_item;
                if ($calculation_item->id_item) {
                    switch ($calculation_item->type) {
                        case DynamicCalculationItem::CONDITION_ITEM:
                            $item_id_new = (int) ($conditions_new[$calculation_item->id_item] ?? 0);
                            break;
                        case DynamicCalculationItem::FIELD_FORMULA_ITEM:
                            $item_id_new = (int) ($field_formulas_new[$calculation_item->id_item] ?? 0);
                            break;
                        case DynamicCalculationItem::INTERVAL_ITEM:
                            $item_id_new = (int) ($intervals_new[$calculation_item->id_item] ?? 0);
                            break;
                        case DynamicCalculationItem::GRID_ITEM:
                            $item_id_new = (int) ($grids_new[$calculation_item->id_item] ?? 0);
                            break;
                    }
                }
                $calculation_item->id_item = $item_id_new;
                $calculation_item->add();
            }
        }

        $product_config = DynamicConfig::getByProduct($id_product_new);
        $product_config->delete();
        if (isset($data['config'])) {
            $product_config = DynamicConfig::copyFromArray($data['config']);
            $product_config->id_product = (int) $id_product_new;
            $product_config->save();
        }
    }

    public static function downloadLink($link)
    {
        $module = DynamicTools::getModule();
        try {
            $cache_file = $module->provider->getDataFile(
                'cache/' . md5($link)
            );

            if (file_exists($cache_file)) {
                return \Tools::file_get_contents($cache_file);
            }

            $contents = \Tools::file_get_contents($link);

            if ($contents) {
                file_put_contents($cache_file, $contents);
            }

            return $contents;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addCustomField($id_product, $no_add = false)
    {
        DynamicTools::checkAddress();

        $field_table = $this->module->name . '_customization_field';
        $sql = new \DbQuery();
        $sql->from($field_table);
        $sql->where('id_product = ' . (int) $id_product);
        $row = \Db::getInstance()->getRow($sql);

        if ($row && !$this->isReserved((int) $row['id_customization_field'])) {
            $id_customization_field = (int) $row['id_customization_field'];
            // check if customization really exists
            $sql = new \DbQuery();
            $sql->from('customization_field');
            $sql->where('id_product = ' . (int) $id_product);
            $sql->where('id_customization_field = ' . (int) $id_customization_field);
            $sql->where('is_deleted = 0');
            $customization_field = \Db::getInstance()->getRow($sql);
            if (!$customization_field) {
                \Db::getInstance()->delete($field_table, 'id_product = ' . (int) $id_product);

                return $this->addCustomField($id_product);
            }

            $product_config = DynamicConfig::getByProduct($id_product);
            if ((int) $customization_field['required'] !== (int) $product_config->required) {
                $active = $product_config->active;
                $required = $product_config->required;
                $data = [
                    'required' => $required && $active,
                    'is_module' => 1,
                ];
                \Db::getInstance()->update(
                    'customization_field',
                    $data,
                    'id_customization_field = ' . (int) $id_customization_field
                );
                if ($no_add) {
                    $this->reserveCustomField($id_customization_field);

                    return (int) $id_customization_field;
                }
            } else {
                $data = [
                    'is_module' => 1,
                ];
                \Db::getInstance()->update(
                    'customization_field',
                    $data,
                    'id_customization_field = ' . (int) $id_customization_field
                );
            }

            $this->updateCustomizableProp($id_product);
            $this->reserveCustomField($id_customization_field);

            return $id_customization_field;
        }

        if ($no_add) {
            return null;
        }

        $product_config = DynamicConfig::getByProduct($id_product);

        if (!$product_config->active) {
            return null;
        }

        \Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', 1);

        // check if product has customization field before adding
        $sql = new \DbQuery();
        $sql->from('customization_field');
        $sql->where('id_product = ' . (int) $id_product);
        $sql->where('type = ' . (int) \Product::CUSTOMIZE_TEXTFIELD);
        $customization_fields = \Db::getInstance()->executeS($sql);
        if (count($customization_fields)) {
            $customization_field = $customization_fields[0];
            $id_customization_field = (int) $customization_field['id_customization_field'];
            if (!$this->isReserved($id_customization_field)) {
                $data = [
                    'required' => (int) $product_config->required,
                    'is_module' => 1,
                ];

                \Db::getInstance()->update(
                    'customization_field',
                    $data,
                    'id_customization_field = ' . (int) $id_customization_field
                );

                $this->updateCustomizableProp($id_product);
                $this->reserveCustomField($id_customization_field);

                return $id_customization_field;
            }
        }

        $labels = [];
        $languages = \Language::getLanguages();
        foreach ($languages as $lang) {
            $label = DynamicTools::translate('Customization', $lang['iso_code'], 'display-extra');
            $labels[$lang['id_lang']] = $label;
        }

        $customization_field = new \CustomizationField();
        $customization_field->name = $labels;
        $customization_field->id_product = (int) $id_product;
        $customization_field->type = \ProductCore::CUSTOMIZE_TEXTFIELD;
        $customization_field->required = (bool) $product_config->required;
        $customization_field->is_module = true;
        $customization_field->save();
        $id_customization_field = (int) $customization_field->id;

        $data = [
            'id_product' => (int) $id_product,
            'id_customization_field' => (int) $id_customization_field,
        ];

        \Db::getInstance()->insert($field_table, $data);
        $id_customization_field = (int) \Db::getInstance()->Insert_ID();

        $this->updateCustomizableProp($id_product);
        $this->reserveCustomField($id_customization_field);

        return $id_customization_field;
    }

    /**
     * @param $id_product
     * @param int $count
     */
    private function updateCustomizableProp($id_product): void
    {
        $sql = new \DbQuery();
        $sql->from('customization_field');
        $sql->where('id_product = ' . (int) $id_product);
        $sql->where('type = ' . (int) \Product::CUSTOMIZE_TEXTFIELD);
        $customization_fields = \Db::getInstance()->executeS($sql);
        $count = count($customization_fields);

        $product = new \Product($id_product);
        $customizable = $this->module->provider->hasRequiredField($id_product) ? 2 : 1;
        if ((int) $product->customizable !== $customizable || (int) $product->text_fields !== $count) {
            \Db::getInstance()->update(
                'product',
                [
                    'text_fields' => (int) $count,
                    'customizable' => $customizable,
                ],
                'id_product = ' . (int) $id_product
            );
            \Db::getInstance()->update(
                'product_shop',
                [
                    'text_fields' => (int) $count,
                    'customizable' => $customizable,
                ],
                'id_product = ' . (int) $id_product
            );
        }
    }

    public function setCustomFieldRequired($id_product, $required = 1)
    {
        $id_customization_field = $this->addCustomField($id_product);
        \Db::getInstance()->update(
            'customization_field',
            ['required' => (int) $required],
            'id_customization_field = ' . (int) $id_customization_field
        );

        $customizable = $this->module->provider->hasRequiredField($id_product) ? 2 : 1;
        \Db::getInstance()->update(
            'product',
            [
                'customizable' => $customizable,
            ],
            'id_product = ' . (int) $id_product
        );
        \Db::getInstance()->update(
            'product_shop',
            [
                'customizable' => $customizable,
            ],
            'id_product = ' . (int) $id_product
        );
    }

    public function reserveCustomField($id_customization_field)
    {
        \Db::getInstance()->insert('tunisoft_customization_field', [
            'id_customization_field' => (int) $id_customization_field,
            'id_module' => (int) $this->module->id,
        ], false, true, \Db::REPLACE);
    }

    public function isReserved($id_customization_field)
    {
        $sql = new \DbQuery();
        $sql->select('id_module');
        $sql->from('tunisoft_customization_field');
        $sql->where('id_customization_field = ' . (int) $id_customization_field);
        $id_module = (int) \Db::getInstance()->getValue($sql);

        return $id_module && $id_module !== (int) $this->module->id;
    }

    public function duplicateInputs($id_cart_old, $id_cart_new)
    {
        $has_errors = false;
        if (!(int) $id_cart_new) {
            return false;
        }
        if (!(int) $id_cart_old) {
            return false;
        }
        $dynamic_inputs = DynamicInput::getInputsByIdCart($id_cart_old);
        foreach ($dynamic_inputs as $dynamic_input) {
            $id_input = $dynamic_input->id;
            list($id_input_new, $input_has_errors) = $dynamic_input->duplicateInput($id_cart_new);
            $has_errors |= $input_has_errors;
            $new_value = '';
            if ($id_input_new) {
                $new_value = '|' . $id_input_new . '|';
                $old_value = '|' . $id_input . '|';

                $sql = '
                SELECT c.id_customization FROM `' . _DB_PREFIX_ . 'customization` c
                JOIN    `' . _DB_PREFIX_ . 'customized_data` cd ON cd.id_customization = c.id_customization
                WHERE   (cd.value = "' . pSQL($old_value) . '" OR cd.value = ' . (int) $id_input . ') 
                        AND c.id_cart = ' . (int) $id_cart_new . ';';
                $id_customization_new = (int) \Db::getInstance()->getValue($sql);

                $dynamic_input->id_customization = $id_customization_new;
                $dynamic_input->save();

                $sql = '
                UPDATE  `' . _DB_PREFIX_ . 'customized_data` cd
                JOIN    `' . _DB_PREFIX_ . 'customization` c ON cd.id_customization = c.id_customization
                SET     cd.value = "' . pSQL($new_value) . '"
                WHERE   cd.value = "' . pSQL($old_value) . '" AND c.id_cart = ' . (int) $id_cart_new . ';';
                \Db::getInstance()->execute($sql);

                // replace id_input directly
                $sql = '
                UPDATE  `' . _DB_PREFIX_ . 'customized_data` cd
                JOIN    `' . _DB_PREFIX_ . 'customization` c ON cd.id_customization = c.id_customization
                SET     cd.value = ' . (int) $id_input_new . '
                WHERE   cd.value = ' . (int) $id_input . ' AND c.id_cart = ' . (int) $id_cart_new . ';';
                \Db::getInstance()->execute($sql);
            }

            if ($input_has_errors) {
                $sql = '
                SELECT id_customization FROM `' . _DB_PREFIX_ . 'customized_data`
                 WHERE   value = "' . pSQL($new_value) . '"';
                $id_customization = (int) \Db::getInstance()->getValue($sql);
                $this->context->cart->deleteProduct(
                    $dynamic_input->id_product,
                    $dynamic_input->id_attribute,
                    $id_customization,
                    \Context::getContext()->cart->id_address_delivery
                );
            }
        }

        return $has_errors;
    }

    public function addCart()
    {
        if ((int) $this->context->cookie->id_cart) {
            $id_cart = (int) $this->context->cookie->id_cart;
            if (!\Validate::isLoadedObject($this->context->cart)) {
                $prev_cart = new \Cart($id_cart);
                if (\Validate::isLoadedObject($prev_cart)) {
                    $this->context->cart = $prev_cart;

                    return $id_cart;
                }
            } else {
                return $id_cart;
            }
        }

        if (!\Validate::isLoadedObject($this->context->cart)) {
            $this->context->cart = new \Cart();
            $this->context->cart->id_currency = $this->module->provider->getCurrency();
        }

        if (class_exists('Guest')) {
            $guest = new \Guest($this->module->provider->getGuest());
            $this->context->cart->mobile_theme = $guest->mobile_theme;
        }

        $id_customer = $this->module->provider->getCustomer();
        if ($id_customer) {
            $id_address = \Address::getFirstCustomerAddressId($id_customer);
            $this->context->cart->id_address_delivery = $id_address;
            $this->context->cart->id_address_invoice = $id_address;
        }

        if (!(int) $this->context->cart->id_shop) {
            $this->context->cart->id_shop = $this->context->shop->id;
        }
        if (!(int) $this->context->cart->id_currency) {
            $this->context->cart->id_currency = $this->module->provider->getCurrency();
        }

        $this->context->cart->add();
        if ($this->context->cart->id) {
            $this->context->cookie->id_cart = (int) $this->context->cart->id;
            $this->context->cookie->write();
            $this->context->cart->id = (int) $this->context->cart->id;
        }

        return (int) $this->context->cart->id;
    }

    /**
     * @return DynamicInput[]
     */
    public function getOOSInputs()
    {
        if ($this->context->cart === null) {
            return [];
        }
        $oos_inputs = [];
        $dynamic_inputs = DynamicInput::getInputsByIdCart($this->context->cart->id);
        foreach ($dynamic_inputs as $dynamic_input) {
            $inputs_quantity = DynamicInput::getInputsDynamicQuantity(
                $dynamic_input->id_product,
                $dynamic_input->id_attribute
            );
            $oos_check = DynamicEquation::checkProductStock(
                $dynamic_input,
                $dynamic_input->getInputFields(),
                $inputs_quantity
            );
            if (!$oos_check) {
                $oos_inputs[] = $dynamic_input;
            }
        }

        return $oos_inputs;
    }

    public function getDisabledOptions()
    {
        if ($this->context->cart === null) {
            return [];
        }
        $errors = [];

        $types = [
            8 => DynamicDropdownOption::class,
            12 => DynamicThumbnailsOption::class,
            16 => DynamicRadioOption::class,
        ];

        $sql = '
        SELECT ov.id_option, dpif.options, i.id_input, dpif.type
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_condition` c
             JOIN `' . _DB_PREFIX_ . 'dynamicproduct_condition_option_visibility` ov 
             ON c.`id_condition` = ov.`id_condition`
             JOIN `' . _DB_PREFIX_ . 'dynamicproduct_input` i ON c.`id_product` = i.`id_product`
             JOIN `' . _DB_PREFIX_ . 'customization` cus ON i.`id_customization` = cus.`id_customization`
             JOIN `' . _DB_PREFIX_ . 'cart_product` cp ON cp.`id_customization` = cus.`id_customization`
             JOIN `' . _DB_PREFIX_ . 'dynamicproduct_input_field` dpif
                  ON dpif.`id_input` = i.`id_input`
             WHERE c.`formula` = 1
                AND i.id_cart = ' . (int) $this->context->cart->id . '
                AND cus.in_cart = 1';

        $rows = \Db::getInstance()->executeS($sql);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $id_option = $row['id_option'];
                if (strpos($row['options'], '"' . $id_option . '"') === false) {
                    continue;
                }
                $id_input = $row['id_input'];
                $type = $row['type'];

                $dynamic_input = new DynamicInput($id_input);

                $product = new \Product($dynamic_input->id_product, false, $this->context->language->id);

                $class_name = $types[$type];
                /** @var DynamicDropdownOption $option */
                $option = new $class_name($id_option, $this->context->language->id);
                if (\Validate::isLoadedObject($option)) {
                    $errors[] = [
                        'option' => $option->label,
                        'product' => $product->name,
                        'edit_link' => $dynamic_input->getEditLink(),
                    ];
                }
            }
        }

        return $errors;
    }

    private function createDummyAttributes($count)
    {
        $attributes = [];
        $ids = range(1, $count);
        foreach ($ids as $id) {
            $attributes[] = [
                'id_product_attribute' => $id,
            ];
        }

        return $attributes;
    }

    /**
     * @param $items
     * @param string $class
     *
     * @return $class
     */
    private function getObjectsFromData($items, $class)
    {
        $objects = [];
        if (!is_array($items)) {
            return [];
        }
        foreach ($items as $item) {
            /** @var DynamicObject $class */
            $obj = $class::copyFromArray($item, null, false);
            $objects[] = $obj;
        }

        return $objects;
    }

    /**
     * @param $id_field
     * @param $id_product_new
     * @param $id_product_old
     * @param $id_group_new
     * @param $id_step_new
     *
     * @return array|int|null
     */
    public function copyField($id_field, $id_product_new, $id_product_old = 0, $id_group_new = 0, $id_step_new = 0, $copy_position = false)
    {
        $options_new = [];
        $field = new DynamicField($id_field);

        $same_product = !$id_product_old;

        if ($field->common && (int) $field->id_product !== (int) $id_product_new && !$same_product) {
            $common_field = DynamicCommonField::getByFieldAndProduct($field->id, $id_product_old);
            $common_field->id_product = (int) $id_product_new;
            $common_field->id_group = $id_group_new;
            $common_field->id_step = $id_step_new;
            $common_field->position = $field->position;
            // if it's a new common field, find max position
            if (!$copy_position && !$common_field->id) {
                $max_common_pos = \Db::getInstance()->getValue(
                    'SELECT MAX(position) FROM ' . _DB_PREFIX_ . $this->module->name . '_common_field WHERE id_product = ' . (int) $id_product_new
                );
                $max_fields_pos = \Db::getInstance()->getValue(
                    'SELECT MAX(position) FROM ' . _DB_PREFIX_ . $this->module->name . '_field WHERE id_product = ' . (int) $id_product_new
                );
                $common_field->position = max($max_common_pos, $max_fields_pos) + 1;
            }
            $common_field->add();

            return [
                'id_field' => $field->id,
                'options' => [],
            ];
        }

        if ($same_product) {
            \Db::getInstance()->update(
                DynamicField::$definition['table'],
                ['position' => ['type' => 'sql', 'value' => '`position` + 1']],
                'position > ' . (int) $field->position . ' AND id_product = ' . (int) $id_product_new
            );

            \Db::getInstance()->update(
                DynamicCommonField::$definition['table'],
                ['position' => ['type' => 'sql', 'value' => '`position` + 1']],
                'position > ' . (int) $field->position . ' AND id_product = ' . (int) $id_product_new
            );

            ++$field->position;
        }
        // in case it's a favorite field copy
        if ($field->id_product === $id_product_new) {
            $field->name .= '_copy';
        }
        $field->common = false;

        $field->id_product = (int) $id_product_new;
        $field->favorite = false;
        $field->id_group = $id_group_new;
        $field->id_step = $id_step_new;
        $id_field_old = (int) $field->id;
        $field->add();

        $field->copyImagesFrom($id_field_old);
        $id_field_new = (int) $field->id;

        $unit_value = DynamicUnitValue::getUnitValuesByIdField($id_field_old);
        $unit_value->id_field = $id_field_new;
        $unit_value->add();

        if ((int) $field->type === _DP_DROPDOWN_) {
            $dropdown_options = DynamicDropdownOption::getDropdownOptionsByIdField($id_field_old);
            foreach ($dropdown_options as $dropdown_option) {
                $id_dropdown_option = (int) $dropdown_option['id'];
                $new_dropdown_option = new DynamicDropdownOption($id_dropdown_option);
                $new_dropdown_option->id_field = $id_field_new;
                $new_dropdown_option->add();
                $options_new[$id_dropdown_option] = (int) $new_dropdown_option->id;
                $new_dropdown_option->copyImagesFrom($id_dropdown_option);
            }
        }

        if ((int) $field->type === _DP_RADIO_) {
            $radio_options = DynamicRadioOption::getRadioOptionsByIdField($id_field_old);
            foreach ($radio_options as $radio_option) {
                $id_radio_option = (int) $radio_option['id'];
                $new_radio_option = new DynamicRadioOption($id_radio_option);
                $new_radio_option->id_field = $id_field_new;
                $new_radio_option->add();
                $options_new[$id_radio_option] = (int) $new_radio_option->id;
                $new_radio_option->copyImagesFrom($id_radio_option);
            }
        }

        if ((int) $field->type === _DP_THUMBNAILS_) {
            $thumbnails_options = DynamicThumbnailsOption::getThumbnailsOptionsByIdField($id_field_old);
            foreach ($thumbnails_options as $thumbnails_option) {
                $id_thumbnails_option = $thumbnails_option['id'];
                $new_thumbnails_option = new DynamicThumbnailsOption($id_thumbnails_option);
                $new_thumbnails_option->id_field = $id_field_new;
                $new_thumbnails_option->add();
                $options_new[$id_thumbnails_option] = (int) $new_thumbnails_option->id;
                $new_thumbnails_option->copyImagesFrom($id_thumbnails_option);
            }
        }

        if ((int) $field->type === _DP_PREVIEW_) {
            $previews_options = DynamicPreviewOption::getPreviewOptionsByIdField($id_field_old);
            foreach ($previews_options as $previews_option) {
                $id_previews_option = $previews_option['id'];
                $new_preview_option = new DynamicPreviewOption($id_previews_option);
                $new_preview_option->id_field = $id_field_new;
                $new_preview_option->add();
                $options_new[$id_previews_option] = (int) $new_preview_option->id;
                $new_preview_option->copyImagesFrom($id_previews_option);
            }
        }

        return [
            'id_field' => $id_field_new,
            'options' => $options_new,
        ];
    }

    private function remapLangIds(DynamicObject $object, $lang_data, array $lang_codes, $import_default_lang)
    {
        if (!$lang_data) {
            return;
        }
        $lang_fields = DynamicObject::getLangFields($object);
        foreach ($lang_fields as $lang_field) {
            $values = $object->$lang_field;
            $new_value = [];
            if (is_array($values)) {
                foreach ($values as $id_lang => $value) {
                    if (isset($lang_data[$id_lang])) {
                        $iso_code = $lang_data[$id_lang];
                        if (isset($lang_codes[$iso_code])) {
                            $new_id_lang = $lang_codes[$iso_code];
                            $new_value[$new_id_lang] = $value;
                        }
                    }
                }
            }
            $id_lang_default = (int) \Configuration::get('PS_LANG_DEFAULT');
            if (empty($new_value[$id_lang_default])) {
                $current_lang_value = $object->$lang_field;
                $new_value[$id_lang_default] = $current_lang_value[$import_default_lang] ?? null;
            }
            $object->$lang_field = $new_value;
        }
    }

    private function findFieldGroup($id_group, $label)
    {
        $id_lang_default = (int) \Configuration::get('PS_LANG_DEFAULT');
        $field_group = new DynamicFieldGroup((int) $id_group, $id_lang_default);
        if (\Validate::isLoadedObject($field_group) && $field_group->label === $label) {
            return $field_group;
        }

        $field_groups = DynamicFieldGroup::getAll($id_lang_default);
        foreach ($field_groups as $field_group) {
            if ($field_group->label === $label) {
                return $field_group;
            }
        }

        return null;
    }

    private function findStep($id_step, $label)
    {
        $id_lang_default = (int) \Configuration::get('PS_LANG_DEFAULT');
        $step = new DynamicStep((int) $id_step, $id_lang_default);
        if (\Validate::isLoadedObject($step) && $step->label === $label) {
            return $step;
        }

        $steps = DynamicStep::getAll($id_lang_default);
        foreach ($steps as $step) {
            if ($step->label === $label) {
                return $step;
            }
        }

        return null;
    }

    private function findUnit($id_unit, $symbol)
    {
        $id_lang_default = (int) \Configuration::get('PS_LANG_DEFAULT');
        $unit = new DynamicUnit((int) $id_unit, $id_lang_default);
        if (\Validate::isLoadedObject($unit) && $unit->symbol === $symbol) {
            return $unit;
        }

        $units = DynamicUnit::getAll($id_lang_default);
        foreach ($units as $unit) {
            if ($unit->symbol === $symbol) {
                return $unit;
            }
        }

        return null;
    }
}
