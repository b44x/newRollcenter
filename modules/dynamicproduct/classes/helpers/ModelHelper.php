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

class ModelHelper
{
    private static $model_fields = [];

    public static function groupByLang($rows, $lang_fields)
    {
        $options = [];

        foreach ($rows as $row) {
            if (!isset($options[$row['id']])) {
                $options[$row['id']] = $row;
                foreach ($lang_fields as $lang_field) {
                    $options[$row['id']][$lang_field] = [];
                }
            }
            foreach ($lang_fields as $lang_field) {
                $options[$row['id']][$lang_field][$row['id_lang']] = $row[$lang_field];
            }
        }

        return $options;
    }

    /**
     * @param $rows
     * @param $model
     *
     * @return array
     */
    public static function castNumericValues($rows, $model, $extra_fields = [])
    {
        if (isset(self::$model_fields[$model])) {
            $model_fields = self::$model_fields[$model];
        } else {
            $model_fields = self::getModelFields($model);
        }

        if (!isset($extra_fields['int'])) {
            $extra_fields['int'] = [];
        }

        if (!isset($extra_fields['float'])) {
            $extra_fields['float'] = [];
        }

        foreach ($rows as &$row) {
            foreach ($model_fields['int'] as $numeric_field) {
                if (isset($row[$numeric_field])) {
                    $row[$numeric_field] = (int) $row[$numeric_field];
                }
            }
            foreach ($model_fields['float'] as $float_field) {
                if (isset($row[$float_field])) {
                    $row[$float_field] = (float) $row[$float_field];
                }
            }

            foreach ($extra_fields['int'] as $numeric_field) {
                if (isset($row[$numeric_field])) {
                    $row[$numeric_field] = (int) $row[$numeric_field];
                }
            }

            foreach ($extra_fields['float'] as $float_field) {
                if (isset($row[$float_field])) {
                    $row[$float_field] = (float) $row[$float_field];
                }
            }
        }

        return $rows;
    }

    public static function castNumericRowValues($row, $model, $extra_fields = [])
    {
        $rows = [$row];
        $rows = self::castNumericValues($rows, $model, $extra_fields);

        return $rows[0];
    }

    private static function getModelFields($model)
    {
        /** @var \ObjectModel $definition */
        $fields = $model::$definition['fields'];

        $model_fields = [
            'int' => [],
            'float' => [],
        ];

        foreach ($fields as $field => $info) {
            if (in_array($info['type'], [\ObjectModel::TYPE_INT, \ObjectModel::TYPE_BOOL])) {
                $model_fields['int'][] = $field;
            } elseif ($info['type'] == \ObjectModel::TYPE_FLOAT) {
                $model_fields['float'][] = $field;
            }
        }

        $model_fields['int'][] = 'id';
        $model_fields['int'][] = 'id_lang';
        $model_fields['int'][] = $model::$definition['primary'];

        return self::$model_fields[$model] = $model_fields;
    }

    public static function groupById(array $rows)
    {
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['id']] = $row;
        }

        return $grouped;
    }

    public static function pickLang(array $rows, $id_lang, $id_default_lang, array $lang_fields)
    {
        if (!$id_lang) {
            return $rows;
        }

        foreach ($rows as &$row) {
            foreach ($lang_fields as $lang_field) {
                $val = $row[$lang_field][$id_lang] ?? '';
                if (empty($val)) {
                    $val = $row[$lang_field][$id_default_lang] ?? '';
                }
                $row[$lang_field] = $val;
            }
        }

        return $rows;
    }
}
