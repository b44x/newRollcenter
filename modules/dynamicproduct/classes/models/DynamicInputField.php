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
use DynamicProduct\classes\factory\InputFieldFactory;
use DynamicProduct\classes\helpers\DynamicCalculatorHelper;
use DynamicProduct\classes\helpers\DynamicFieldsHelper;
use DynamicProduct\classes\helpers\FieldsVisibilityHelper;

class DynamicInputField extends DynamicObject
{
    /** @var array */
    protected $field;

    public $id_input;
    public $id_field;

    public $name;
    public $label;
    public $value;
    public $secondary_value = 0;
    public $options;
    public $type;
    public $visible;

    public $data;
    public $data_obj;

    public $position;

    public $value_formatted;
    public $display_value;
    public $sku;
    public $selected_options = [];
    public $duplicated = 0;

    public $image_url;
    public $thumb_url;
    public $image_size;

    private $excluded = false;
    private $excluded_options = [];

    private $locked_value = false;
    private $force_value = false;

    protected $skipped_names = ['quantity'];

    public const LOAD_NONE = 0;
    public const LOAD_INITIAL = 1;
    public const LOAD_ALL = 2;

    private static $null_values = [
      _DP_INPUT_ => 0,
      _DP_TEXT_ => '',
      _DP_DATE_ => '',
      _DP_IMAGE_ => '',
      _DP_DROPDOWN_ => 0,
      _DP_CHECKBOX_ => 0,
      _DP_SWITCH_ => 0,
      _DP_FILE_ => '',
      _DP_SLIDER_ => 0,
      _DP_THUMBNAILS_ => 0,
      _DP_TEXTAREA_ => '',
      _DP_RADIO_ => 0,
      _DP_COLORPICKER_ => '',
      _DP_PHP_ => 0,
    ];

    protected $webserviceParameters = [
      'objectNodeName' => 'dynamic_input_field',
      'objectsNodeName' => 'dynamic_input_fields',
      'fields' => [
        'id_input' => ['required' => true, 'xlink_resource' => 'dynamic_inputs'],
        'id_field' => [],
        'name' => [],
        'label' => ['getter' => 'getLabel'],
        'value' => ['getter' => 'displayValue'],
        'secondary_value' => [],
        'sku' => ['getter' => 'renderSKU'],
        'type' => [],
        'visible' => [],
        'data' => [],
        'position' => [],
      ],
    ];

    public static $definition = [
      'table' => 'dynamicproduct_input_field',
      'primary' => 'id_input_field',
      'fields' => [
        'id_input' => ['type' => self::TYPE_INT],
        'id_field' => ['type' => self::TYPE_INT],
        'name' => ['type' => self::TYPE_STRING],
        'value' => ['type' => self::TYPE_HTML],
        'secondary_value' => ['type' => self::TYPE_STRING],
        'options' => ['type' => self::TYPE_STRING],
        'type' => ['type' => self::TYPE_INT],
        'visible' => ['type' => self::TYPE_BOOL],
        'data' => ['type' => self::TYPE_STRING],
        'position' => ['type' => self::TYPE_INT],
      ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->module = DynamicTools::getModule();
        if (!$id_lang) {
            $id_lang = $this->module->provider->getLangID($this->context);
        }
        parent::__construct($id, $id_lang, $id_shop);
        $this->setData($id_lang);
    }

    public function setData($id_lang)
    {
        $this->setDynamicField($id_lang);
        $this->setFormattedValue();
        $this->setLabel($id_lang);
        $this->setSelectedOptions();
        $this->setDisplayValue();
        $this->setSKU();
    }

    /**
     * @param $id_input
     * @param $id_lang
     *
     * @return DynamicInputField[]
     *
     * @throws \PrestaShopDatabaseException
     */
    public static function getByIdInput($id_input, $id_lang = null)
    {
        $input_fields = [];
        $sql = new \DbQuery();
        $sql->from(self::$definition['table']);
        $sql->where('id_input = ' . (int) $id_input);
        $sql->orderBy('position ASC');
        $rows = \Db::getInstance()->executeS($sql, false, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_input_field = $row['id_input_field'];
            $dynamic_input_field = InputFieldFactory::create((int) $row['type'], (int) $id_input_field, $id_lang);
            if (\Validate::isLoadedObject($dynamic_input_field)) {
                $input_fields[$dynamic_input_field->name] = $dynamic_input_field;
            }
        }

        return $input_fields;
    }

    protected function setDynamicField($id_lang = null)
    {
        $this->field = DynamicField::getFieldFromCache($this->id_field, $id_lang, true);
    }

    public function getDynamicField()
    {
        return $this->field;
    }

    public function setValue($value)
    {
        if (!$this->locked_value) {
            $this->value = $value;
            $this->syncSelectedOption();
            $this->setFormattedValue();
            $this->setDisplayValue();
        }
    }

    public function setSecondaryValue($secondary_value)
    {
        if (!$this->locked_value) {
            $this->secondary_value = $secondary_value;
        }
    }

    public function setFormattedValue()
    {
        $this->value_formatted = $this->value;
        if ($this->type === _DP_PRICE_) {
            $this->value_formatted = $this->module->provider->convertAndFormatPrice((float) $this->value);
        }
    }

    public function setDisplayValue()
    {
        $display_value = null;
        if (in_array($this->type, [_DP_DROPDOWN_, _DP_RADIO_, _DP_THUMBNAILS_])) {
            $display_value = $this->displayValue();
            if ($display_value === null) {
                $display_value = '';
            }
        }

        $this->display_value = $display_value;
    }

    public function setSKU()
    {
        $sku = null;
        if (in_array($this->type, [_DP_DROPDOWN_, _DP_RADIO_, _DP_THUMBNAILS_])) {
            $sku = $this->getSKU();
            if ($sku === null) {
                $sku = '';
            }
        }

        $this->sku = $sku;

        return $sku;
    }

    public function syncSelectedOption()
    {
        $this->selected_options = [];
        $this->secondary_value = 0;
        foreach ($this->field['options'] as $option) {
            if (is_numeric($this->value)) {
                if ((float) $option['value'] === (float) $this->value) {
                    $this->selected_options = [$option['id']];
                    $this->secondary_value = $option['secondary_value'];
                    break;
                }
            } elseif ($option['value'] === $this->value) {
                $this->selected_options = [$option['id']];
                $this->secondary_value = $option['secondary_value'] ?? 0;
                break;
            }
        }
    }

    /**
     * @param DynamicInputField[] $input_fields
     */
    public function getValueForCalculation(array $input_fields = [])
    {
        if ($this->isExcluded()) {
            return $this->getNullValue();
        }

        if (!empty($input_fields) && !$this->duplicated) {
            $id_group = $this->field['id_group'];
            $group = DynamicProductFieldGroup::getGroupFromCache($id_group);

            if ($group && (int) $group['id_control_field']) {
                $field_name = $this->name;
                $matches = preg_grep('/^' . $field_name . '_\d+$/', array_keys($input_fields));
                $total = 0;
                foreach ($matches as $match) {
                    $total += $input_fields[$match]->getValueForCalculation();
                }

                return $total;
            }
        }

        if ($this->hasExcludedOptions()) {
            return $this->getOptionsTotalValue();
        }

        $reset = false;
        $value = $this->value;

        if ($this->isForcedValue()) {
            return $value;
        }

        $str_values = [];

        if (is_array($this->selected_options) && count($this->selected_options)) {
            $has_strings = false;

            foreach ($this->selected_options as $selected_option) {
                if (isset($this->field['options'][$selected_option])) {
                    $option_value = $this->field['options'][$selected_option]['value'];
                    if (!is_numeric($option_value)) {
                        $has_strings = true;
                        break;
                    }
                }
            }

            foreach ($this->selected_options as $selected_option) {
                if (isset($this->field['options'][$selected_option])) {
                    if (!$reset) {
                        // only reset value if we have options, if not, use the value directly
                        $value = 0;
                        $reset = true;
                    }
                    // if we encounter a string, return it immediately
                    $option_value = $this->field['options'][$selected_option]['value'];
                    if ($has_strings) {
                        $str_values[] = $option_value;
                    } else {
                        $value += (float) $option_value;
                    }
                }
            }
        }
        if (count($str_values)) {
            if (count($str_values) === 1 && !(int) $this->field['settings']['multiselect']) {
                return $str_values[0];
            }

            return implode(';', $str_values);
        }

        return $value;
    }

    /**
     * @param DynamicInputField[] $input_fields
     */
    public function getSecondaryValueForCalculation(array $input_fields = [])
    {
        if ($this->isExcluded()) {
            return $this->getNullValue();
        }

        if (!empty($input_fields) && !$this->duplicated) {
            $id_group = $this->field['id_group'];
            $group = DynamicProductFieldGroup::getGroupFromCache($id_group);

            if ($group && $group['id_control_field']) {
                $field_name = $this->name;
                $matches = preg_grep('/^' . $field_name . '_\d+$/', array_keys($input_fields));
                $total = 0;
                foreach ($matches as $match) {
                    $total += $input_fields[$match]->getSecondaryValueForCalculation();
                }

                return $total;
            }
        }

        if ($this->hasExcludedOptions()) {
            return $this->getOptionsTotalSecondaryValue();
        }
        $reset = false;
        $value = $this->secondary_value;

        if (is_array($this->selected_options) && count($this->selected_options)) {
            foreach ($this->selected_options as $selected_option) {
                if (isset($this->field['options'][$selected_option])) {
                    if (!$reset) {
                        // only reset value if we have options, if not, use the secondary value directly
                        $value = 0;
                        $reset = true;
                    }
                    // if we encounter a string, return it immediately
                    $option_secondary_value = 0;
                    if (isset($this->field['options'][$selected_option]['secondary_value'])) {
                        $option_secondary_value = $this->field['options'][$selected_option]['secondary_value'];
                    }
                    if (!is_numeric($option_secondary_value) && !empty($option_secondary_value)) {
                        return $option_secondary_value;
                    }
                    $value += (float) $option_secondary_value;
                }
            }
        }

        if ($value === null) {
            $value = 0;
        }

        return $value;
    }

    /**
     * @param DynamicInputField[] $input_fields
     *
     * @return string
     */
    public function getDynamicValue($input_fields)
    {
        $value = $this->displayValue();

        if (!$this->field['settings']['is_dynamic_value']) {
            return $value;
        }

        foreach ($input_fields as $input_field) {
            $value = str_replace(
                [
                  "[[$input_field->name]]",
                  "[$input_field->name]",
                  "{{$input_field->name}}",
                ],
                [
                  htmlspecialchars($input_field->secondary_value),
                  htmlspecialchars($input_field->value_formatted),
                  htmlspecialchars($input_field->display_value ?? ''),
                ],
                $value
            );
        }

        return $value;
    }

    /**
     * @param DynamicInputField[] $input_fields
     *
     * @return string
     */
    public function getDynamicLabel($input_fields)
    {
        $value = $this->label;

        if (!$this->field['settings']['is_dynamic_value']) {
            return $value;
        }

        foreach ($input_fields as $input_field) {
            $value = str_replace(
                [
                  "[[$input_field->name]]",
                  "[$input_field->name]",
                  "{{$input_field->name}}",
                ],
                [
                  htmlspecialchars($input_field->secondary_value),
                  htmlspecialchars($input_field->value_formatted),
                  htmlspecialchars($input_field->display_value ?? ''),
                ],
                $value
            );
        }

        return $value;
    }

    public function getSKU()
    {
        return null;
    }

    public function renderSKU()
    {
        if (!$this->selected_options || !isset($this->selected_options[0])) {
            return null;
        }

        $option = $this->field['options'][$this->selected_options[0]];

        return $option['sku'];
    }

    public function isWithinLimit(array $interval_condition, array $input_fields)
    {
        $value = $this->getValueForCalculation($input_fields);

        return $value >= $interval_condition['min']
          && ($value < $interval_condition['max'] || (float) $interval_condition['max'] === 0.0);
    }

    public function isWithinValues(array $interval_condition, array $input_fields)
    {
        return in_array($this->getValueForCalculation($input_fields), $interval_condition['values']);
    }

    public function getLabel()
    {
        $id_lang = $this->context->language->id;
        $this->setLabel($id_lang);

        if (is_string($this->label)) {
            return $this->label;
        }

        if (isset($this->label[$id_lang])) {
            return $this->label[$id_lang];
        }

        $id_default_lang = (int) \Configuration::get('PS_LANG_DEFAULT');
        if (isset($this->label[$id_default_lang])) {
            return $this->label[$id_default_lang];
        }

        return '';
    }

    private function setLabel($id_lang)
    {
        if ($this->name === 'quantity') {
            $this->label = 'Quantity';
        }
        $field = $this->getDynamicField();
        if (is_array($field['label']) && isset($field['label'][$id_lang])) {
            $this->label = $field['label'][$id_lang];
        } elseif (is_string($field['label'])) {
            $this->label = $field['label'];
        } else {
            $this->label = '';
        }
    }

    public function getNullValue()
    {
        if (isset(self::$null_values[$this->type])) {
            return self::$null_values[$this->type];
        }

        return $this->value;
    }

    public function setExcluded()
    {
        $this->excluded = true;
    }

    public function isExcluded()
    {
        return $this->excluded;
    }

    public function setExcludedOptions($hidden_options, $options)
    {
        $option_ids = array_keys($options);
        $excluded_options = array_intersect($option_ids, $hidden_options);
        $this->excluded_options = array_values($excluded_options);
    }

    public function hasExcludedOptions()
    {
        return count($this->excluded_options) > 0;
    }

    public function isSelectedOptionExcluded()
    {
        if (!isset($this->selected_options[0])) {
            return false;
        }

        return in_array($this->selected_options[0], $this->excluded_options);
    }

    public function getOptionsTotalValue()
    {
        $total_value = 0;
        foreach ($this->selected_options as $id_option) {
            if (isset($this->field['options'][$id_option]) && !in_array($id_option, $this->excluded_options)) {
                $value = $this->field['options'][$id_option]['value'];
                if (!is_numeric($value)) {
                    return $value;
                }
                $total_value += (float) $value;
            }
        }

        return $total_value;
    }

    public function getOptionsTotalSecondaryValue()
    {
        $total_value = 0;
        foreach ($this->selected_options as $id_option) {
            if (isset($this->field['options'][$id_option]) && !in_array($id_option, $this->excluded_options)) {
                $secondary_value = $this->field['options'][$id_option]['secondary_value'];
                if (!is_numeric($secondary_value)) {
                    return $secondary_value;
                }
                $total_value += (float) $secondary_value;
            }
        }

        return $total_value;
    }

    public function selectFirstVisibleOption()
    {
        if ($this->hasExcludedOptions()) {
            foreach ($this->field['options'] as $option) {
                $id_option = $option['id'];
                if (!in_array($id_option, $this->excluded_options)) {
                    $this->setSelectOption($option);

                    return true;
                }
            }
        }

        return true;
    }

    public function removeExcludedOptionsFromSelection()
    {
        $selected_options = [];
        foreach ($this->selected_options as $selected_option) {
            if (!in_array((int) $selected_option, $this->excluded_options)) {
                $selected_options[] = $selected_option;
            }
        }
        $this->selected_options = $selected_options;
        $this->options = json_encode($selected_options);
    }

    private function setSelectedOptions()
    {
        if (is_string($this->options)) {
            $this->selected_options = json_decode($this->options, true);
        }

        if (is_array($this->options)) {
            $this->selected_options = $this->options;
        }
    }

    /**
     * @param DynamicDropdownOption $option
     */
    private function setSelectOption($option)
    {
        if (!$this->locked_value) {
            $id_option = (int) $option['id'];
            $this->setValue($option['value']);
            $this->setSecondaryValue($option['secondary_value']);
            $this->selected_options = [$id_option];
            $this->options = json_encode([$id_option]);
        }
    }

    protected function getFirstOption()
    {
        return isset($this->selected_options[0]) ? (int) $this->selected_options[0] : 0;
    }

    public static function getDefaultInputFields($id_product, $id_attribute, $load = self::LOAD_NONE, $values = [])
    {
        return self::getInputFieldsFromData(
            $id_product,
            $id_attribute,
            [],
            $load,
            $values
        );
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param $fields
     * @param bool|int $load
     * @param array $values
     *
     * @return array[DynamicInputField[], array]
     */
    public static function getInputFieldsFromData(
        $id_product,
        $id_attribute,
        $fields,
        $load = self::LOAD_NONE,
        array $values = []
    ): array {
        $module = DynamicTools::getModule();
        $context = DynamicTools::getContext();

        $fields_helper = new DynamicFieldsHelper($module, $context);
        $fields_helper->addFields($id_product, $id_attribute, $fields, $load, $values);
        list($grouped_fields, $fields, $new_fields) = $fields_helper->duplicateFields($id_product, $fields);
        $fields_helper->addFields($id_product, $id_attribute, $fields, $load, $values, $new_fields);
        $fields_helper->addAttributes($id_product, $id_attribute, $fields);
        $fields_helper->addFeatures($id_product, $fields);
        $fields_helper->addExtraFields($id_product, $id_attribute, $fields);

        $input_fields = [];
        foreach ($fields as $field) {
            if (!empty($field['name'])) {
                $input_fields[$field['name']] =
                  self::getInputFieldFromData($field, $context->language->id, $fields['changed']);
            }
        }

        $dynamic_calculator_helper = new DynamicCalculatorHelper(
            DynamicTools::getModule(),
            DynamicTools::getContext()
        );

        $dynamic_calculator_helper->applyProportions($id_product, $input_fields);

        DynamicEquation::hookAllocator($id_product, $id_attribute, $input_fields);

        $config = DynamicConfig::getByProduct($id_product);

        $calculation_items = DynamicCalculationItem::getRowsByProduct($id_product);

        if ($config->custom_calculation && count($calculation_items)) {
            list($fields_visibility) = DynamicEquation::processCustomOrder(
                $id_product,
                $id_attribute,
                $input_fields
            );
        } else {
            $exec_order = ExecOrder::getRowsByProduct($id_product, true);
            if (count($exec_order)) {
                list($fields_visibility) = DynamicEquation::processExecOrder(
                    $id_product,
                    $id_attribute,
                    $exec_order,
                    $input_fields
                );
            } else {
                $visibility_helper = new FieldsVisibilityHelper($module, $context);
                $fields_visibility = $visibility_helper->getFieldsVisibility(
                    $id_product,
                    $id_attribute,
                    $input_fields
                );

                DynamicEquation::execIntervalConditions($id_product, $input_fields);
                DynamicEquation::execFieldFormulas($id_product, $input_fields);
                DynamicEquation::execGrids($id_product, $input_fields);
            }
        }

        return [$input_fields, $fields_visibility, $grouped_fields];
    }

    /**
     * @param $field
     * @param $id_lang
     * @param $changed
     *
     * @return DynamicInputField
     */
    public static function getInputFieldFromData($field, $id_lang, $changed): DynamicInputField
    {
        $input_field = InputFieldFactory::create($field['type']);

        if (isset($field['duplicated']) && (int) $field['duplicated']) {
            $input_field->duplicated = 1;
        }

        foreach (self::$definition['fields'] as $field_name => $field_info) {
            if (isset($field[$field_name])) {
                if (!in_array($field_info['type'], [self::TYPE_STRING, self::TYPE_HTML])) {
                    $value = self::formatValue($field[$field_name], $field_info['type']);
                } else {
                    $value = $field[$field_name];
                }
                $input_field->{$field_name} = $value;
            }
        }

        $input_field->setData($id_lang);

        // Disallow setting the html value from javascript
        if (isset($field['type']) && (int) $field['type'] === _DP_HTML_) {
            $input_field->value = '';
            $input_field->value_formatted = '';
        }

        if (isset($field['selected_options'])) {
            if (is_string($field['selected_options'])) {
                $field['selected_options'] = json_decode($field['selected_options'], true);
            }
            if (is_array($field['selected_options'])) {
                $input_field->selected_options = array_map('intval', $field['selected_options']);
            }
            $input_field->options = json_encode($input_field->selected_options, true);
            $input_field->value = $input_field->getValueForCalculation();
            $input_field->secondary_value = $input_field->getSecondaryValueForCalculation();
            $input_field->setDisplayValue();
        }

        $has_changed_field = !empty($changed['value']);

        if (!$has_changed_field && $input_field->type === _DP_INPUT_ && !(float) $input_field->value) {
            $dynamic_calculator_helper = new DynamicCalculatorHelper(
                DynamicTools::getModule(),
                DynamicTools::getContext()
            );
            $input_field->value = $dynamic_calculator_helper->getValueFromProportion($input_field->id_field);
        }

        if ($input_field->name == 'quantity'
          && $changed['value'] != 'quantity'
          && \Tools::getIsset('quantity')
        ) {
            $input_field->value = \Tools::getValue('quantity', 1);
        }

        return $input_field;
    }

    public function isSkipped()
    {
        return !(int) $this->visible || in_array($this->name, $this->skipped_names, true);
    }

    public function isSkippedName()
    {
        if (!$this->field['name']) {
            return false;
        }

        return strpos($this->field['name'], '_') === 0;
    }

    public function isAdminField()
    {
        if (!$this->field['name']) {
            return false;
        }

        return strpos($this->field['name'], 'admin_') === 0;
    }

    public function lockValue()
    {
        $this->locked_value = true;
    }

    public function unlockValue()
    {
        $this->locked_value = false;
    }

    public function forceValue()
    {
        $this->force_value = true;
    }

    public function isForcedValue()
    {
        return $this->force_value;
    }

    public function displayValue()
    {
        return $this->value;
    }

    public function shouldShowImageInSummary()
    {
        return $this->field['settings']['show_image_in_summary'];
    }

    private function getTemplateName()
    {
        if (isset($this->module->field_types[$this->type])) {
            return $this->module->field_types[$this->type]['name'];
        }

        return null;
    }

    public function getTemplatePath()
    {
        $template = sprintf('views/templates/hook/partial/%s.tpl', $this->getTemplateName());
        $path = $this->module->getDir() . $template;
        if (!is_file($path)) {
            return false;
        }

        return sprintf(
            'module:%s/%s',
            $this->module->name,
            $template
        );
    }

    public function getImagesUrls()
    {
        return [];
    }
}
