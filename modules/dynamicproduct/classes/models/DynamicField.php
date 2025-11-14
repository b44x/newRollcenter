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
use DynamicProduct\classes\factory\DynamicFieldFactory;
use DynamicProduct\classes\helpers\ColorHelper;
use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\FieldGroupHelper;
use DynamicProduct\classes\helpers\ModelHelper;
use DynamicProduct\classes\helpers\OptionPriceHelper;
use DynamicProduct\classes\models\intervals\IntervalField;

class DynamicField extends DynamicObject
{
    public $dir = 'images/field';

    public $id_product;
    public $id_group;
    public $id_step;
    public $id_unit;
    public $name;
    public $type;
    public $init;
    public $active;
    public $image;
    public $favorite;
    public $common;
    public $label;
    public $value;
    public $short_description;
    public $description;
    public $placeholder;

    public $deleted = 0;

    /** @var DynamicUnitValue */
    public $settings;

    /** @var DynamicDropdownOption[]|DynamicThumbnailsOption[]|DynamicRadioOption[] */
    public $options = [];

    /** @var DynamicUnit */
    public $unit;
    public $linked = false;

    public $image_url;
    public $thumb_url;

    private static $fields_by_name = [];

    public static $cached_fields = [];
    public static $cached_default_field;

    public static $definition = [
        'table' => 'dynamicproduct_field',
        'primary' => 'id_field',
        'group_by' => 'id_product',
        'multilang' => true,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_step' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_unit' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'init' => ['type' => self::TYPE_FLOAT],
            'active' => ['type' => self::TYPE_INT],
            'image' => ['type' => self::TYPE_STRING],
            'favorite' => ['type' => self::TYPE_INT],
            'common' => ['type' => self::TYPE_INT],
            'position' => ['type' => self::TYPE_INT],
            'deleted' => ['type' => self::TYPE_INT],
            /* Lang fields */
            'label' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
                'validate' => 'isGenericName',
                'size' => 200,
            ],
            'value' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
                'validate' => 'isCleanHtml',
                'size' => 100,
            ],
            'short_description' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'required' => false,
            ],
            'description' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'required' => false,
            ],
            'placeholder' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->initUnit($id_lang);
        $this->initSettings($id_lang);
        $this->initOptions();
        $this->initImage();
    }

    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        return parent::getByIdProduct($id_source_product, $order, $id_lang);
    }

    /**
     * @param $id_product
     * @param $field_name
     * @param null $id_lang
     *
     * @return DynamicField
     *
     * @internal param $id_lang
     */
    public static function getFieldByName($id_product, $field_name, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $key = $id_source_product . '_' . $field_name . '_' . $id_lang;
        if (isset(self::$fields_by_name[$key])) {
            return self::$fields_by_name[$key];
        }
        $sql = new \DbQuery();
        $sql->select('id_field');
        $sql->select('type');
        $sql->from(self::$definition['table']);
        $sql->where('id_product = ' . (int) $id_source_product);
        $sql->where('name = "' . pSQL($field_name) . '"');
        $sql->where('!deleted');
        $field = \Db::getInstance()->getRow($sql);

        if (!$field) {
            return self::$fields_by_name[$key] = new DynamicField();
        }

        return self::$fields_by_name[$key] = DynamicFieldFactory::create($field['type'], $field['id_field'], $id_lang);
    }

    /**
     * @param $id_product
     * @param $id_lang
     *
     * @return DynamicField[]
     */
    public static function getFieldsByIdProduct($id_product, $id_lang = null)
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);
        $dynamic_fields = [];
        $sql = 'SELECT `id_field`, `id_group`, `type`, `id_step`, `position`, false as linked 
        FROM `' . _DB_PREFIX_ . 'dynamicproduct_field`
        WHERE id_product = ' . (int) $id_source_product . ' AND !deleted 
        UNION
        (SELECT cf.`id_field`, cf.`id_group`, f.`type`, cf.`id_step`, cf.`position`, true as linked 
        FROM `' . _DB_PREFIX_ . 'dynamicproduct_common_field` cf
        JOIN `' . _DB_PREFIX_ . 'dynamicproduct_field` f
        ON f.`id_field` = cf.`id_field`
        WHERE cf.`id_product` = ' . (int) $id_source_product . ')
        ORDER BY `position`';
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id_field = (int) $row['id_field'];
            $dynamic_field = DynamicFieldFactory::create((int) $row['type'], $id_field, $id_lang);
            $dynamic_field->linked = $row['linked'];
            $dynamic_field->id_step = (int) $row['id_step'];
            $dynamic_field->position = (int) $row['position'];
            $dynamic_field->id_group = (int) $row['id_group'];
            $dynamic_fields[$id_field] = $dynamic_field;
        }

        return $dynamic_fields;
    }

    /**
     * @param $id_product
     * @param $id_lang
     *
     * @return array
     */
    public static function getFieldRowsByProduct($id_product, $id_lang = null, $include_deleted = false)
    {
        $key = $id_lang . '_' . $include_deleted;
        if (!isset(self::$cached_fields[$key])) {
            self::$cached_fields[$key] = [];
        }

        if (isset(self::$cached_fields[$key][$id_product])) {
            return self::$cached_fields[$key][$id_product];
        }

        $module = DynamicTools::getmodule();

        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $dynamic_fields = [];
        $default_settings = (new DynamicUnitValue())->getObjectValues(!$id_lang);

        $fields_by_type = [
            _DP_DROPDOWN_ => [],
            _DP_RADIO_ => [],
            _DP_THUMBNAILS_ => [],
            _DP_PREVIEW_ => [],
        ];

        $id_default_lang = \Configuration::get('PS_LANG_DEFAULT');
        $id_lang_fallback = $id_lang ?: (int) $id_default_lang;

        $sql = 'SELECT f.id_field as id, f.id_product, f.name, f.type, f.active, f.common, f.favorite, f.image, f.init, f.id_unit, f.id_group, f.id_step, f.position, false as linked, 
        fl.id_lang, fl.label, fl.value, fl.short_description, fl.description, fl.placeholder, 
        ul.name as unit_name, u.symbol as unit_symbol, u.displayed as unit_displayed
        
        FROM ' . _DB_PREFIX_ . 'dynamicproduct_field f
        LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_field_lang fl ON f.id_field = fl.id_field
        
        LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_unit u ON (f.id_unit = u.id_unit)
        LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_unit_lang ul ON (u.id_unit = ul.id_unit AND ul.id_lang = ' . (int) $id_lang_fallback . ')
        
        WHERE f.id_product = ' . (int) $id_source_product . ' AND ' . ($include_deleted ? '1' : '!f.deleted') . '
        UNION
        (SELECT cf.id_field as id, cf.id_product, f.name, f.type, f.active, f.common, f.favorite, f.image, f.init, f.id_unit, cf.id_group, cf.id_step, cf.position, true as linked, 
        fl.id_lang, fl.label, fl.value, fl.short_description, fl.description, fl.placeholder, 
        ul.name as unit_name, u.symbol as unit_symbol, u.displayed as unit_displayed
        FROM ' . _DB_PREFIX_ . 'dynamicproduct_common_field cf
        
        JOIN ' . _DB_PREFIX_ . 'dynamicproduct_field f ON (cf.id_field = f.id_field)
        JOIN ' . _DB_PREFIX_ . 'dynamicproduct_field_lang fl ON f.id_field = fl.id_field
        
        LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_unit u ON (f.id_unit = u.id_unit)
        LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_unit_lang ul ON (u.id_unit = ul.id_unit AND ul.id_lang = ' . (int) $id_lang_fallback . ')
        
        WHERE cf.id_product = ' . (int) $id_source_product . ')
        ORDER BY position;';

        $rows = \Db::getInstance()->executeS($sql);

        $lang_fields = ['label', 'value', 'short_description', 'description', 'placeholder'];
        $rows = ModelHelper::groupByLang($rows, $lang_fields);
        $rows = ModelHelper::pickLang(
            $rows,
            $id_lang,
            $id_default_lang,
            $lang_fields
        );

        $field_base_url = $module->provider->getDataDirUrl('images/field');

        foreach ($rows as $row) {
            $id_field = (int) $row['id'];
            $row['linked'] = (int) $row['linked'];

            if (!empty($row['image'])) {
                $row['image_url'] = $field_base_url . $row['image'];
                $row['thumb_url'] = $field_base_url . $row['id'] . '-thumb.jpg';
            }

            $dynamic_fields[$id_field] = $row;
            $dynamic_fields[$id_field]['settings'] = $default_settings;
            $dynamic_fields[$id_field]['unit'] = [
                'name' => $row['unit_name'],
                'symbol' => $row['unit_symbol'],
                'displayed' => $row['unit_displayed'],
            ];
            $dynamic_fields[$id_field]['options'] = [];

            if (isset($fields_by_type[$row['type']])) {
                $fields_by_type[$row['type']][] = $id_field;
            }
        }

        if (empty($dynamic_fields)) {
            return self::$cached_fields[$key][$id_product] = $dynamic_fields;
        }

        $dynamic_fields = ModelHelper::castNumericValues($dynamic_fields, DynamicField::class);

        $settings = \Db::getInstance()->executeS('
                SELECT *, uv.id_unit_value as id
                FROM ' . _DB_PREFIX_ . 'dynamicproduct_unit_value uv
                LEFT JOIN ' . _DB_PREFIX_ . 'dynamicproduct_unit_value_lang uvl 
                ON (
                    uvl.id_unit_value = uv.id_unit_value' . ($id_lang ? ' AND uvl.id_lang = ' . (int) $id_lang : '') . '
                    )
                WHERE uv.id_field IN (' . implode(',', array_keys($dynamic_fields)) . ')
        ');

        $settings = ModelHelper::groupByLang($settings, ['price_unit']);
        $settings = ModelHelper::pickLang($settings, $id_lang, $id_default_lang, ['price_unit']);
        $settings = ModelHelper::castNumericValues($settings, DynamicUnitValue::class);

        // assign the settings to each field
        foreach ($settings as $setting) {
            $id_field = (int) $setting['id_field'];
            $dynamic_fields[$id_field]['settings'] = $setting;
        }

        foreach ($fields_by_type as $type => $ids) {
            if (empty($ids)) {
                continue;
            }

            switch ($type) {
                case _DP_DROPDOWN_:
                    $options = \Db::getInstance()->executeS('
                        SELECT *, o.id_dropdown_option as id
                        FROM ' . _DB_PREFIX_ . 'dynamicproduct_dropdown_option o
                        JOIN ' . _DB_PREFIX_ . 'dynamicproduct_dropdown_option_lang ol 
                        ON ol.id_dropdown_option = o.id_dropdown_option
                        WHERE o.id_field IN (' . implode(',', $ids) . ')
                        AND o.deleted = 0
                        ORDER BY o.position ASC');

                    if (empty($options)) {
                        continue 2;
                    }

                    $options = ModelHelper::groupByLang($options, ['label', 'description']);
                    $options = ModelHelper::pickLang($options, $id_lang, $id_default_lang, ['label', 'description']);

                    $base_url = $module->provider->getDataDirUrl('images/dropdown');
                    foreach ($options as &$option) {
                        $option = ModelHelper::castNumericRowValues($option, DynamicDropdownOption::class);

                        $id_field = $option['id_field'];

                        if (!empty($option['image'])) {
                            $option['image_url'] = $base_url . $option['image'];
                            $option['thumb_url'] = $base_url . $option['id'] . '-thumb.jpg';
                        }
                        if (!empty($option['preview'])) {
                            $option['preview_url'] = $base_url . $option['preview'];
                            $option['preview_thumb_url'] = $base_url . $option['id'] . '-preview-thumb.jpg';
                        }

                        $option['displayed_value'] = null;

                        $field_settings = $dynamic_fields[$id_field]['settings'];

                        if ((int) $field_settings['display_value_price']) {
                            $option = OptionPriceHelper::displayValue($option, $id_source_product, $field_settings);
                        }

                        if ((int) $field_settings['display_secondary_value_price']) {
                            $option = OptionPriceHelper::displaySecondaryValue(
                                $option,
                                $id_source_product,
                                $field_settings
                            );
                        }

                        $id = $option['id'];
                        $dynamic_fields[$id_field]['options'][$id] = $option;
                    }

                    break;
                case _DP_RADIO_:
                    $options = \Db::getInstance()->executeS('
                        SELECT *, o.id_radio_option as id 
                        FROM ' . _DB_PREFIX_ . 'dynamicproduct_radio_option o
                        JOIN ' . _DB_PREFIX_ . 'dynamicproduct_radio_option_lang ol 
                        ON ol.id_radio_option = o.id_radio_option
                        WHERE o.id_field IN (' . implode(',', $ids) . ')
                        AND o.deleted = 0
                        ORDER BY o.position ASC');

                    if (empty($options)) {
                        continue 2;
                    }

                    $options = ModelHelper::groupByLang($options, ['label', 'description']);
                    $options = ModelHelper::pickLang($options, $id_lang, $id_default_lang, ['label', 'description']);

                    $base_url = $module->provider->getDataDirUrl('images/radio');
                    foreach ($options as &$option) {
                        $option = ModelHelper::castNumericRowValues($option, DynamicRadioOption::class);

                        $id_field = $option['id_field'];

                        if (!empty($option['preview'])) {
                            $option['preview_url'] = $base_url . $option['preview'];
                            $option['preview_thumb_url'] = $base_url . $option['id'] . '-preview-thumb.jpg';
                        }

                        $option['displayed_value'] = null;

                        $field_settings = $dynamic_fields[$id_field]['settings'];

                        if ((int) $field_settings['display_value_price']) {
                            $option = OptionPriceHelper::displayValue($option, $id_source_product, $field_settings);
                        }

                        if ((int) $field_settings['display_secondary_value_price']) {
                            $option = OptionPriceHelper::displaySecondaryValue(
                                $option,
                                $id_source_product,
                                $field_settings
                            );
                        }

                        $id = $option['id'];
                        $dynamic_fields[$id_field]['options'][$id] = $option;
                    }

                    break;
                case _DP_THUMBNAILS_:
                    $options = \Db::getInstance()->executeS('
                        SELECT *, o.id_thumbnails_option as id
                        FROM ' . _DB_PREFIX_ . 'dynamicproduct_thumbnails_option o
                        JOIN ' . _DB_PREFIX_ . 'dynamicproduct_thumbnails_option_lang ol 
                        ON ol.id_thumbnails_option = o.id_thumbnails_option
                        WHERE o.id_field IN (' . implode(',', $ids) . ')
                        AND o.deleted = 0
                        ORDER BY o.position ASC');

                    if (empty($options)) {
                        continue 2;
                    }

                    $options = ModelHelper::groupByLang($options, ['label', 'description']);
                    $options = ModelHelper::pickLang($options, $id_lang, $id_default_lang, ['label', 'description']);

                    $base_url = $module->provider->getDataDirUrl('images/thumbnails');
                    foreach ($options as &$option) {
                        $option = ModelHelper::castNumericRowValues($option, DynamicThumbnailsOption::class);

                        $id_field = $option['id_field'];

                        if (!empty($option['image'])) {
                            $option['image_url'] = $base_url . $option['image'];
                            $option['thumb_url'] = $base_url . $option['id'] . '-thumb.jpg';
                        }
                        if (!empty($option['preview'])) {
                            $option['preview_url'] = $base_url . $option['preview'];
                            $option['preview_thumb_url'] = $base_url . $option['id'] . '-preview-thumb.jpg';
                        }

                        $option['displayed_value'] = null;

                        $field_settings = $dynamic_fields[$id_field]['settings'];

                        if ((int) $field_settings['display_value_price']) {
                            $option = OptionPriceHelper::displayValue($option, $id_source_product, $field_settings);
                        }

                        if ((int) $field_settings['display_secondary_value_price']) {
                            $option = OptionPriceHelper::displaySecondaryValue(
                                $option,
                                $id_source_product,
                                $field_settings
                            );
                        }

                        $id = $option['id'];
                        $dynamic_fields[$id_field]['options'][$id] = $option;
                    }

                    break;
                case _DP_PREVIEW_:
                    $options = \Db::getInstance()->executeS('
                        SELECT *, o.id_preview_option as id 
                        FROM ' . _DB_PREFIX_ . 'dynamicproduct_preview_option o
                        WHERE o.id_field IN (' . implode(',', $ids) . ')
                        AND o.deleted = 0
                        ORDER BY o.position ASC');

                    if (empty($options)) {
                        continue 2;
                    }

                    $base_url = $module->provider->getDataDirUrl('images/preview');
                    foreach ($options as &$option) {
                        if (!empty($option['preview'])) {
                            $option['preview_url'] = $base_url . $option['preview'];
                            $option['preview_thumb_url'] = $base_url . $option['id'] . '-preview-thumb.jpg';
                        }

                        $id = $option['id'];
                        $id_field = $option['id_field'];
                        $dynamic_fields[$id_field]['options'][$id] = $option;
                    }
                    break;
            }
        }

        return self::$cached_fields[$key][$id_product] = $dynamic_fields;
    }

    /**
     * @param $id_field
     * @param $id_lang
     * @param $include_deleted
     *
     * @return mixed
     */
    public static function getFieldFromCache($id_field, $id_lang = null, $include_deleted = false)
    {
        $key = $id_lang . '_' . $include_deleted;
        if (!isset(self::$cached_fields[$key])) {
            self::$cached_fields[$key] = [];
        }

        foreach (self::$cached_fields[$key] as $fields) {
            if (isset($fields[$id_field])) {
                return $fields[$id_field];
            }
        }

        if ((int) $id_field) {
            $id_product = \Db::getInstance()->getValue(
                'SELECT id_product FROM ' . _DB_PREFIX_ . 'dynamicproduct_field WHERE id_field = ' . (int) $id_field
            );
            if ((int) $id_product && !isset(self::$cached_fields[$key][$id_product])) {
                $dynamic_fields = self::getFieldRowsByProduct($id_product, $id_lang, $include_deleted);
                if (isset($dynamic_fields[$id_field])) {
                    return $dynamic_fields[$id_field];
                }
            }
        }

        if (self::$cached_default_field) {
            return self::$cached_default_field;
        }

        self::$cached_default_field = (new DynamicField())->getObjectValues();

        return self::$cached_default_field;
    }

    public static function getGroupedFields($id_product, $id_lang)
    {
        $module = DynamicTools::getModule();

        $grouped = [];

        $group_helper = new FieldGroupHelper($module, \Context::getContext());

        $groups = DynamicFieldGroup::getRowsByProduct($id_product, $id_lang);

        $product_groups = DynamicProductFieldGroup::getRowsByProduct($id_product);

        $position = 0;
        foreach ($product_groups as $product_group) {
            $position = $product_group['position'];
            $grouped[$product_group['id']] = [
                'id' => $product_group['id'],
                'collapsible' => $product_group['collapsible'],
                'start_collapsed' => $product_group['start_collapsed'],
                'id_control_field' => $product_group['id_control_field'],
                'id_source_group' => 0,
                'id_step' => $product_group['id_step'],
                'position' => $position,
                'group' => $group_helper->getGroup($product_group['id_field_group'], $groups),
                'fields' => [],
            ];
        }
        // add a default group for fields with no group
        $grouped[0] = [
            'id' => 0,
            'position' => $position + 1,
            'id_control_field' => 0,
            'id_source_group' => 0,
            'group' => $group_helper->getGroup(0, $groups),
            'fields' => [],
        ];

        $fields = self::getFieldRowsByProduct($id_product, $id_lang);
        foreach ($fields as $field) {
            $id_group = $field['id_group'];
            if (!isset($grouped[$id_group])) {
                $grouped[0]['fields'][$field['id']] = $field;
            } else {
                $grouped[$id_group]['fields'][$field['id']] = $field;
            }
        }

        // clear empty groups
        foreach ($grouped as $id_field_group => $item) {
            if (!count($item['fields'])) {
                unset($grouped[$id_field_group]);
            }
        }

        return $grouped;
    }

    public static function getHighestPosition($object)
    {
        $max_field_position = parent::getHighestPosition($object);
        $max_common_field_position = DynamicCommonField::getHighestPosition($object);

        return max($max_field_position, $max_common_field_position);
    }

    /**
     * @param $id_lang
     *
     * @return DynamicField[]
     */
    public static function getFavoriteFields($id_lang)
    {
        $rows = \Db::getInstance()->executeS('SELECT f.id_field as id, f.id_product, f.name, fl.label 
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_field` f
            JOIN `' . _DB_PREFIX_ . 'dynamicproduct_field_lang` fl ON fl.id_field = f.id_field
            WHERE f.favorite = 1 AND f.deleted = 0 AND fl.id_lang = ' . $id_lang . ';');

        return ModelHelper::castNumericValues($rows, self::class);
    }

    /**
     * @param $id_lang
     *
     * @return DynamicField[]
     */
    public static function getCommonFields($id_lang)
    {
        $rows = \Db::getInstance()->executeS('SELECT f.id_field as id, f.id_product, f.name, fl.label 
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_field` f
            JOIN `' . _DB_PREFIX_ . 'dynamicproduct_field_lang` fl ON fl.id_field = f.id_field
            WHERE f.common = 1 AND f.deleted = 0 AND fl.id_lang = ' . $id_lang . ';');

        return ModelHelper::castNumericValues($rows, self::class);
    }

    private function initSettings($id_lang)
    {
        $this->settings = $this->getSettings($id_lang);
    }

    /**
     * @return DynamicUnitValue
     */
    public function getSettings($id_lang = null)
    {
        return DynamicUnitValue::getUnitValue($this->id, $id_lang);
    }

    private function initOptions()
    {
        $this->options = $this->getOptions();
    }

    public function getInitialValue()
    {
        return $this->init;
    }

    public function getInitialOptions($initial_value = null)
    {
        return [];
    }

    /**
     * @return DynamicDropdownOption[]
     */
    public function getOptions()
    {
        return [];
    }

    public static function getLabelForDefaultLang($id_option, $type)
    {
        $id_default_lang = (int) \Configuration::get('PS_LANG_DEFAULT');

        switch ($type) {
            case _DP_DROPDOWN_:
                return \Db::getInstance()->getValue('SELECT label FROM ' . _DB_PREFIX_ . 'dynamicproduct_dropdown_option_lang
                    WHERE id_dropdown_option = ' . (int) $id_option . ' AND id_lang = ' . (int) $id_default_lang);
            case _DP_RADIO_:
                return \Db::getInstance()->getValue('SELECT label FROM ' . _DB_PREFIX_ . 'dynamicproduct_radio_option_lang
                    WHERE id_radio_option = ' . (int) $id_option . ' AND id_lang = ' . (int) $id_default_lang);
            case _DP_THUMBNAILS_:
                return \Db::getInstance()->getValue('SELECT label FROM ' . _DB_PREFIX_ . 'dynamicproduct_thumbnails_option_lang
                    WHERE id_thumbnails_option = ' . (int) $id_option . ' AND id_lang = ' . (int) $id_default_lang);
            case _DP_PREVIEW_:
                return \Db::getInstance()->getValue('SELECT label FROM ' . _DB_PREFIX_ . 'dynamicproduct_preview_option_lang
                    WHERE id_preview_option = ' . (int) $id_option . ' AND id_lang = ' . (int) $id_default_lang);
        }

        return '';
    }

    /**
     * @param $id_field
     *
     * @return DynamicUnitValue
     */
    public function getUnitValues()
    {
        return DynamicUnitValue::getUnitValuesByIdField($this->id);
    }

    /**
     * @return DynamicUnit
     */
    public function initUnit($id_lang)
    {
        return $this->unit = DynamicUnit::getUnit($this->id_unit, $id_lang);
    }

    public function getUnitName()
    {
        return $this->unit->name;
    }

    private function initImage()
    {
        if ($this->hasImage()) {
            $this->image_url = $this->getImageUrl();
            $this->thumb_url = $this->getThumbUrl();
        }
    }

    public function hasImage()
    {
        return $this->getPath('image');
    }

    public function getThumb()
    {
        return $this->getThumbPath('image');
    }

    public function getThumbUrl()
    {
        if ($path = $this->getThumbPath('image')) {
            return $this->getUrl() . basename($path);
        }

        return $this->getPixelUrl();
    }

    public function getImage()
    {
        return $this->getPath('image');
    }

    public function getImageUrl()
    {
        if ($path = $this->getPath('image')) {
            return $this->getUrl() . basename($path);
        }

        return $this->getPixelUrl();
    }

    public function copyImagesFrom($id_field)
    {
        $field = new DynamicField($id_field);

        $image = $field->getImage();
        if (is_file($image)) {
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $this->image = $this->id . '.' . $extension;
            $dest_image = $this->getPathForCreation('image');
            $this->save();
            copy($image, $dest_image);
        }

        $thumb = $field->getThumb();
        if (is_file($thumb)) {
            $dest_thumb = $this->getThumbPathForCreation('image');
            copy($thumb, $dest_thumb);
        }
    }

    public function delete()
    {
        $path = $this->getPath('image');
        $thumb = $this->getThumbPath('image');
        if (is_file($path)) {
            unlink($path);
        }
        if (is_file($thumb)) {
            unlink($thumb);
        }

        $interval_fields = IntervalField::getByField($this->id);
        foreach ($interval_fields as $interval_field) {
            $interval_field->delete();
        }

        $this->deleted = true;

        return $this->save();
    }

    public function getClearColor()
    {
        $color_helper = new ColorHelper($this->module, $this->context);

        return $color_helper->getClearColor($this->value);
    }
}
