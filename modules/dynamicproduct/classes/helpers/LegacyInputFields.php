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
use DynamicProduct\classes\models\DynamicInputField;

class LegacyInputFields
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

    public function getInputFields($id_product, $id_input, $id_lang)
    {
        $input_fields = [];
        $inputs = $this->getInputValues($id_input);
        if (is_array($inputs)) {
            foreach ($inputs as $field_name => $value) {
                if (strpos($field_name, 'data_')) {
                    continue;
                }
                if (!$this->isSkipped($field_name)) {
                    if (isset($inputs['data_content_' . $field_name])) {
                        $value = $inputs['data_content_' . $field_name];
                    }
                    $dynamic_field = DynamicField::getFieldByName($id_product, $field_name, $id_lang);
                    $input_field = new DynamicInputField();
                    $input_field->label = $dynamic_field->label;
                    $input_field->name = $field_name;
                    $input_field->value = $value;
                    $input_field->secondary_value = $value;
                    $input_field->visible = true;
                    $input_field->setFormattedValue();
                    $input_field->setDisplayValue();
                    $input_fields[] = $input_field;
                }
            }
        }

        return $input_fields;
    }

    private function getInputValues($id_input)
    {
        $sql = new \DbQuery();
        $sql->select('inputs');
        $sql->from($this->module->name . '_input');
        $sql->where('id_input = ' . (int) $id_input);
        $inputs = \Db::getInstance()->getValue($sql);
        if ($this->shouldEscapeJson($inputs)) {
            $inputs = $this->unescape($inputs);
        }

        return json_decode($inputs, true);
    }

    private function shouldEscapeJson($inputs)
    {
        return strpos($inputs, '{\\"') !== false;
    }

    public function unescape($str)
    {
        $search = ['\\\\', '\\0', "\'", '\"'];
        $replace = ['\\', "\0", "'", '"'];

        return str_replace($search, $replace, $str);
    }

    private function isSkipped($field_name)
    {
        if (preg_match('/^data_/', $field_name)) {
            return true;
        }
    }
}
