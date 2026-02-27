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
use DynamicProduct\classes\models\DynamicInput;
use DynamicProduct\classes\models\DynamicInputField;

class DynamicCustomizationHelper
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

    public function addToCart(
        int $id_product,
        int $id_attribute,
        int $quantity,
        array $fields,
        array $id_customizations = null,
        bool $add_to_cart = true,
        bool $save_customization = true,
        int $dp_input = 0,
        int $dp_cart = 0
    ) {
        $id_inputs = [];

        $input = new DynamicInput($dp_input);
        if (\Validate::isLoadedObject($input) && $input->checkAuth()) {
            $is_same_cart = (int) $input->id_cart && (int) $input->id_cart === (int) $this->module->provider->getCart();
            if ($is_same_cart) {
                DynamicInput::deleteCustomization($dp_input);
            }
        }

        $fields['quantity']['value'] = $quantity;

        list($input_fields) = DynamicInputField::getInputFieldsFromData(
            $id_product,
            $id_attribute,
            $fields
        );

        $preview_helper = new PreviewHelper($this->module, $this->context);
        $preview_helper->addPreviewField($input_fields);

        $calculator_helper = new DynamicCalculatorHelper($this->module, $this->context);

        try {
            $calculator_helper->checkFormulas($id_product, $input_fields);
        } catch (\Exception $e) {
            return [
                'error' => 1,
                'message' => DynamicTools::reportException($e),
            ];
        }

        $id_cart = $dp_cart ?: (int) $this->module->handler->addCart();
        $id_customization_field = $this->module->handler->addCustomField($id_product);

        $id_address_delivery = $this->module->provider->getDeliveryAddressID($this->context);

        $customization_helper = new DynamicCustomizationHelper($this->module, $this->context);

        $attributes_errors = [];
        foreach ($id_customizations as $id_product_attribute => $id_customization) {
            if (!$id_customization && $save_customization) {
                $id_customization = $customization_helper->saveCustomization(
                    $id_product,
                    $id_product_attribute,
                    $id_address_delivery,
                    $id_cart
                );
                $id_customizations[$id_product_attribute] = $id_customization;
            }

            $price_equation_result = $calculator_helper->getPriceEquationResult(
                $id_product,
                $id_product_attribute,
                $input_fields
            );

            $weight_equation_result = $calculator_helper->getWeightEquationResult(
                $id_product,
                $id_product_attribute,
                $input_fields
            );

            $true_conditions = DynamicEquation::getTrueConditions();

            $dynamic_input = $customization_helper->saveDynamicInput(
                $id_product,
                $id_product_attribute,
                $id_cart,
                $id_customization,
                $quantity,
                $price_equation_result,
                $weight_equation_result,
                $input_fields,
                $true_conditions
            );

            $id_inputs[$id_product_attribute] = $dynamic_input->id;

            $customization_helper->saveCustomizationData(
                $id_product,
                $this->module->id,
                $id_customization,
                $id_customization_field,
                $dynamic_input
            );

            $customization_helper->saveInputFields($input_fields, $dynamic_input->id);

            $cart = $this->context->cart;
            if ($add_to_cart) {
                $added = $cart->updateQty(
                    (int) $dynamic_input->cart_quantity,
                    (int) $dynamic_input->id_product,
                    (int) $dynamic_input->id_attribute,
                    $id_customization,
                    'up',
                    (int) $cart->id_address_delivery,
                    new \Shop((int) $this->context->shop->id),
                    false
                );
                if (!$added) {
                    $attributes_errors[] = $id_product_attribute;
                }
            }
        }

        return [
            'id_module' => $this->module->id,
            'id_customizations' => $id_customizations,
            'id_inputs' => $id_inputs,
            'attributes_errors' => $attributes_errors,
            'input_fields' => $input_fields,
        ];
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param $id_address_delivery
     * @param $id_cart
     *
     * @return int
     */
    public function saveCustomization($id_product, $id_attribute, $id_address_delivery, $id_cart)
    {
        $data = [
            'id_product_attribute' => (int) $id_attribute,
            'id_address_delivery' => (int) $id_address_delivery,
            'id_cart' => (int) $id_cart,
            'id_product' => (int) $id_product,
            'quantity' => 0,
            'in_cart' => 0,
        ];
        \Db::getInstance()->insert('customization', $data);

        return \Db::getInstance()->Insert_ID();
    }

    /**
     * @param $id_product
     * @param $id_module
     * @param $id_customization
     * @param $id_customization_field
     * @param $dynamic_input
     */
    public function saveCustomizationData(
        $id_product,
        $id_module,
        $id_customization,
        $id_customization_field,
        $dynamic_input
    ) {
        /** @noinspection UnnecessaryCastingInspection */
        $data = [
            'id_customization' => (int) $id_customization,
            'id_module' => (int) $id_module,
            'type' => (int) \Product::CUSTOMIZE_TEXTFIELD,
            'index' => (int) $id_customization_field,
            'value' => (int) $dynamic_input->id,
            'price' => (float) $dynamic_input->price,
            'weight' => (float) $dynamic_input->weight,
        ];

        \Db::getInstance()->insert('customized_data', $data, false, true, \Db::REPLACE);
        $customizable = $this->module->provider->hasRequiredField($id_product) ? 2 : 1;
        \Db::getInstance()->update(
            'product',
            ['customizable' => $customizable],
            'id_product = ' . (int) $id_product
        );
    }

    public function saveDynamicInput(
        $id_product,
        $id_attribute,
        $id_cart,
        $id_customization,
        $quantity,
        $price_equation_result,
        $weight_equation_result,
        $input_fields,
        $true_conditions = [],
        $name = null,
        $is_bookmarked = false,
        $is_admin = false
    ) {
        $dynamic_input = new DynamicInput();
        $dynamic_input->id_product = (int) $id_product;
        $dynamic_input->id_attribute = (int) $id_attribute;
        $dynamic_input->id_cart = (int) $id_cart;
        $dynamic_input->cart_quantity = (int) $quantity;
        $dynamic_input->id_customer = (int) $this->module->provider->getCustomer();
        $dynamic_input->id_guest = (int) $this->module->provider->getGuest();
        $dynamic_input->hash = \Tools::getValue('hash');
        $dynamic_input->true_conditions = json_encode($true_conditions);
        $dynamic_input->name = $name;
        $dynamic_input->is_bookmarked = $is_bookmarked;
        $dynamic_input->is_admin = (int) $is_admin;

        $dynamic_input->price = (float) $price_equation_result;
        $dynamic_input->weight = (float) $weight_equation_result;
        $dynamic_input->dynamic_quantity = DynamicEquation::getDynamicQuantity($dynamic_input, $input_fields);
        $dynamic_input->id_customization = (int) $id_customization;
        $dynamic_input->save();

        return $dynamic_input;
    }

    /**
     * @param DynamicInputField[] $input_fields
     * @param $id_input
     */
    public function saveInputFields($input_fields, $id_input)
    {
        foreach ($input_fields as $input_field) {
            $input_field->id = null;

            $field = $input_field->getDynamicField();
            $field_position = (int) $field['position'];
            $group_position = 0;
            $group_step = 0;
            $step_position = 0;
            if ((int) $field['id_group']) {
                $group_position = (int) \Db::getInstance()->getValue(
                    'SELECT position FROM ' . _DB_PREFIX_ . 'dynamicproduct_product_field_group WHERE id_product_field_group = ' . (int) $field['id_group']
                );
                $group_step = (int) \Db::getInstance()->getValue(
                    'SELECT id_step FROM ' . _DB_PREFIX_ . 'dynamicproduct_product_field_group WHERE id_product_field_group = ' . (int) $field['id_group']
                );
            }
            if ((int) $field['id_step']) {
                $step_position = (int) \Db::getInstance()->getValue(
                    'SELECT position FROM ' . _DB_PREFIX_ . 'dynamicproduct_product_step WHERE id_product_step = ' . (int) $field['id_step']
                );
            }
            if ($group_step) {
                $step_position = (int) \Db::getInstance()->getValue(
                    'SELECT position FROM ' . _DB_PREFIX_ . 'dynamicproduct_product_step WHERE id_product_step = ' . (int) $group_step
                );
            }

            $input_field->position = $field_position + $group_position * 100 + $step_position * 10000;
            $input_field->id_input = (int) $id_input;
            if ($input_field->data && !is_string($input_field->data)) {
                $input_field->data = json_encode($input_field->data);
            }
            $input_field->save();
        }
    }

    public function getCustomerProducts($id_customer, $id_guest)
    {
        $inputs = [];
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from(DynamicInput::$definition['table']);
        $conditions = [];

        if ($id_customer) {
            $conditions[] = 'id_customer = ' . (int) $id_customer;
        }
        if ($id_guest) {
            $conditions[] = 'id_guest = ' . (int) $id_guest;
        }

        if (!count($conditions)) {
            return [];
        }

        $condition = implode(' OR ', $conditions);
        $sql->where(pSQL($condition));
        $sql->where('is_bookmarked = 1');
        $db = \Db::getInstance();
        $result = $db->executeS($sql, false);
        while ($row = $db->nextRow($result)) {
            $input = new DynamicInput((int) $row['id_input']);
            if (\Validate::isLoadedObject($input)) {
                $inputs[] = $input;
            }
        }

        return $inputs;
    }
}
