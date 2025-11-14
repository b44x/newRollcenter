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

use DynamicProduct\classes\models\DynamicField;

class DynamicGroupsHelper
{
    /**
     * @param array $grouped_fields
     *
     * @return array
     */
    public static function duplicateGroups(array $grouped_fields, array $fields)
    {
        $new_fields = [];
        foreach ($grouped_fields as $group) {
            if ($group['id_control_field']) {
                $control_field = DynamicField::getFieldFromCache($group['id_control_field']);
                $field_name = $control_field['name'];
                $value = (int) $fields[$field_name]['value'];

                if ($value) {
                    for ($i = 1; $i < $value + 1; ++$i) {
                        $id_group = 10000 * $i + $group['id'];
                        $new_group = array_merge([], $group);
                        $new_group['id'] = $id_group;
                        $new_group['id_control_field'] = 0;
                        $new_group['id_source_group'] = $group['id'];
                        $new_group['group']['label'] .= " ($i)";
                        $group_fields = $new_group['fields'];
                        $new_group_fields = [];
                        foreach ($group_fields as $field) {
                            $new_field = array_merge([], $field);
                            // $new_field['id'] = 10000 * $i + $new_field['id'];
                            $new_field['id_group'] = $id_group;
                            $new_field['name'] .= "_$i";
                            $new_field['duplicated'] = 1;
                            $new_group_fields[] = $new_field;
                            $new_fields[] = $new_field;
                        }
                        $new_group['fields'] = $new_group_fields;
                        $grouped_fields[$id_group] = $new_group;
                    }
                }

                $field_names = array_keys($fields);
                foreach ($group['fields'] as $field) {
                    foreach ($field_names as $field_name) {
                        if (preg_match('/^' . $field['name'] . '_(\d+)$/', $field_name, $m)) {
                            if (isset($m[1]) && $m[1] > $value) {
                                unset($fields[$field_name]);
                            }
                        }
                    }
                }
            }
        }

        return [$grouped_fields, $fields, $new_fields];
    }
}
