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
if (!defined('_PS_VERSION_')) {
    exit;
}

/* @noinspection PhpUnusedPrivateMethodInspection */

use DynamicProduct\classes\controllers\front\DynamicFrontController;
use DynamicProduct\classes\helpers\DynamicCalculatorHelper;
use DynamicProduct\classes\helpers\DynamicCustomizationHelper;
use DynamicProduct\classes\helpers\PreviewHelper;
use DynamicProduct\classes\models\DynamicEquation;
use DynamicProduct\classes\models\DynamicInput;
use DynamicProduct\classes\models\DynamicInputField;

class DynamicProductCustomizationModuleFrontController extends DynamicFrontController
{
    protected function processSaveCustomization()
    {
        $id_product = $this->id_product;
        $id_attribute = $this->id_attribute;
        $quantity = (int) Tools::getValue('quantity');
        $add_to_cart = (int) Tools::getValue('add_to_cart');
        $id_customizations = Tools::getValue('id_customizations');
        $dp_input = (int) Tools::getValue('dp_id_input');
        $fields = Tools::getValue('fields');
        $dp_cart = (int) Tools::getValue('dp_cart', 0);

        $customization_helper = new DynamicCustomizationHelper($this->module, $this->context);

        if (!is_array($id_customizations)) {
            $id_customizations = [$id_attribute => 0];
        }

        $result = $customization_helper->addToCart(
            $id_product,
            $id_attribute,
            $quantity,
            $fields,
            $id_customizations,
            $add_to_cart,
            true,
            $dp_input,
            $dp_cart
        );

        $this->respond($result);
    }

    protected function processSaveInput()
    {
        $id_product = $this->id_product;
        $id_attribute = $this->id_attribute;
        $quantity = (int) Tools::getValue('quantity');
        $fields = Tools::getValue('fields');

        $customization_helper = new DynamicCustomizationHelper($this->module, $this->context);

        $id_customizations = [$id_attribute => 0];

        $result = $customization_helper->addToCart(
            $id_product,
            $id_attribute,
            $quantity,
            $fields,
            $id_customizations,
            false,
            false
        );

        $this->respond($result);
    }

    public function processSaveOrderCustomization()
    {
        if (!$this->module->provider->isAdmin()) {
            $this->respond([
                'error' => true,
                'message' => 'Forbidden',
            ]);
        }

        $id_input = (int) Tools::getValue('dp_id_input');
        $dynamic_input = new DynamicInput($id_input);

        $fields = Tools::getValue('fields');
        list($input_fields) = DynamicInputField::getInputFieldsFromData(
            $this->id_product,
            $this->id_attribute,
            $fields
        );

        $preview_helper = new PreviewHelper($this->module, $this->context);
        $preview_helper->addPreviewField($input_fields);

        $customization_helper = new DynamicCustomizationHelper($this->module, $this->context);

        $input_fields_old = DynamicInputField::getByIdInput($dynamic_input->id);
        foreach ($input_fields_old as $input_field) {
            $input_field->delete();
        }
        $customization_helper->saveInputFields($input_fields, $dynamic_input->id);

        $price_equation = DynamicEquation::getPriceEquation($this->id_product);
        $price = DynamicEquation::calculatePriceFormula(
            $this->id_product,
            $this->id_attribute,
            $price_equation,
            $input_fields
        );
        $dynamic_input->updatePrice($price);

        $weight_equation = DynamicEquation::getWeightEquation($this->id_product);
        $weight = DynamicEquation::calculateWeightFormula(
            $this->id_product,
            $this->id_attribute,
            $weight_equation,
            $input_fields
        );
        $dynamic_input->updateWeight($weight);

        $cart = new Cart($dynamic_input->id_cart);

        $price = Product::getPriceStatic(
            $this->id_product,
            false,
            $dynamic_input->id_attribute,
            6,
            null,
            false,
            true,
            $dynamic_input->cart_quantity,
            false,
            $dynamic_input->id_customer,
            $dynamic_input->id_cart,
            $cart->id_address_delivery,
            $reduction,
            true,
            true,
            null,
            true,
            $dynamic_input->id_customization
        );

        $price_ttc = Product::getPriceStatic(
            $this->id_product,
            true,
            $dynamic_input->id_attribute,
            6,
            null,
            false,
            true,
            $dynamic_input->cart_quantity,
            false,
            $dynamic_input->id_customer,
            $dynamic_input->id_cart,
            $cart->id_address_delivery,
            $reduction,
            true,
            true,
            null,
            true,
            $dynamic_input->id_customization
        );

        $order_detail = $dynamic_input->getOrderDetail();
        $order_detail->unit_price_tax_excl = $price;
        $order_detail->unit_price_tax_incl = $price_ttc;
        $order_detail->total_price_tax_excl = $price * $dynamic_input->cart_quantity;
        $order_detail->total_price_tax_incl = $price_ttc * $dynamic_input->cart_quantity;
        $order_detail->save();

        $order = new Order($order_detail->id_order);

        $order->total_products = $cart->getOrderTotal(
            false,
            Cart::ONLY_PRODUCTS,
            null,
            $order->id_carrier,
            false,
            true
        );
        $order->total_products_wt = $cart->getOrderTotal(
            true,
            Cart::ONLY_PRODUCTS,
            null,
            $order->id_carrier,
            false,
            true
        );

        $order->total_paid_tax_excl = $cart->getOrderTotal(
            false,
            Cart::BOTH,
            null,
            $order->id_carrier,
            false,
            true
        );

        $order->total_paid_tax_incl = $cart->getOrderTotal(
            true,
            Cart::BOTH,
            null,
            $order->id_carrier,
            false,
            true
        );

        $order->save();

        // get order invoice
        $id_invoice = (int) Db::getInstance()->getValue(
            'SELECT id_order_invoice FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order = ' . (int) $order->id
        );

        if ($id_invoice) {
            $order_invoice = new OrderInvoice($id_invoice);
            if (Validate::isLoadedObject($order_invoice)) {
                $order_invoice->total_products = $order->total_products;
                $order_invoice->total_products_wt = $order->total_products_wt;
                $order_invoice->total_paid_tax_excl = $order->total_paid_tax_excl;
                $order_invoice->total_paid_tax_incl = $order->total_paid_tax_incl;
                $order_invoice->save();
            }
        }

        $this->respond();
    }

    public function processSaveDynamicProduct()
    {
        $name = Tools::getValue('name');
        $id_product_attribute = (int) Tools::getValue('id_attribute');
        $fields = Tools::getValue('fields');
        list($input_fields) = DynamicInputField::getInputFieldsFromData(
            $this->id_product,
            $this->id_attribute,
            $fields
        );

        $preview_helper = new PreviewHelper($this->module, $this->context);
        $preview_helper->addPreviewField($input_fields);

        $calculator_helper = new DynamicCalculatorHelper($this->module, $this->context);
        $customization_helper = new DynamicCustomizationHelper($this->module, $this->context);

        $price_equation_result = $calculator_helper->getPriceEquationResult(
            $this->id_product,
            $id_product_attribute,
            $input_fields
        );

        $weight_equation_result = $calculator_helper->getWeightEquationResult(
            $this->id_product,
            $id_product_attribute,
            $input_fields
        );

        $true_conditions = DynamicEquation::getTrueConditions();

        $dynamic_input = $customization_helper->saveDynamicInput(
            $this->id_product,
            $id_product_attribute,
            0,
            0,
            0,
            $price_equation_result,
            $weight_equation_result,
            $input_fields,
            $true_conditions,
            $name,
            (int) Tools::getValue('bookmark'),
            $this->module->provider->isAdmin()
        );

        $customization_helper->saveInputFields($input_fields, $dynamic_input->id);
        $this->respond([
            'link' => $dynamic_input->getEditLink(),
        ]);
    }
}
