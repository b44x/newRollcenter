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
use DynamicProduct\classes\models\DynamicConfig;
use DynamicProduct\classes\models\DynamicEquation;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicInput;
use DynamicProduct\classes\models\DynamicInputField;
use DynamicProduct\classes\models\DynamicProportion;

class DynamicCalculatorHelper
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
     * @param DynamicInputField[] $input_fields
     */
    public function processCalculation(
        $id_product,
        $id_attribute,
        $input_fields,
        $fields_visibility,
        $adapter_data
    ) {
        $this->checkFormulas($id_product, $input_fields);

        $is_container_hidden = isset($fields_visibility[0]) && (int) $fields_visibility[0] === 0;

        $calculated_prices = $this->getCalculatedPrices(
            $id_product,
            $id_attribute,
            $input_fields,
            $adapter_data,
            $is_container_hidden
        );

        $calculated_weight = $this->getCalculatedWeight(
            $id_product,
            $id_attribute,
            $input_fields,
            $is_container_hidden
        );

        $in_stock = $this->checkProductStock($id_product, $id_attribute, $input_fields);

        $true_conditions = DynamicEquation::getTrueConditions();

        $quantity_discounts = $this->getQuantityDiscounts(
            $id_product,
            $id_attribute,
            $calculated_prices
        );

        return $calculated_prices +
            $calculated_weight +
            [
                'visibility' => $fields_visibility,
                'true_conditions' => $true_conditions,
                'debug_messages' => \DynamicProduct::$debug_messages,
                'oos' => $in_stock,
                'input_fields' => $input_fields,
                'quantity_discounts' => $quantity_discounts,
            ];
    }

    /**
     * @param $id_product
     * @param DynamicInputField[] $input_fields
     *
     * @throws \Exception
     */
    public function checkFormulas($id_product, $input_fields)
    {
        $source = DynamicTools::getSource();
        $price_equation = DynamicEquation::getPriceEquation($id_product);
        $weight_equation = DynamicEquation::getWeightEquation($id_product);
        $quantity_equation = DynamicEquation::getQuantityEquation($id_product);

        try {
            DynamicEquation::evaluateFormula($price_equation->formula, $input_fields, 'Price formula');
        } catch (\Exception $e) {
            $message = $this->module->l('Price Equation', $source)
                . ' (' . $price_equation->formula . '): ' .
                DynamicTools::reportException($e, true);
            \DynamicProduct::$debug_messages['validation']['price_formula'] = $message;
            throw new \RuntimeException($message);
        }

        try {
            DynamicEquation::evaluateFormula($weight_equation->formula, $input_fields, 'Weight formula');
        } catch (\Exception $e) {
            \DynamicProduct::$debug_messages['validation']['weight_formula'] = $e->getMessage();
            throw new \RuntimeException($this->module->l('Weight Equation', $source) . ' (' . $weight_equation->formula . '): ' . DynamicTools::reportException($e, true));
        }

        try {
            DynamicEquation::evaluateFormula($quantity_equation->formula, $input_fields, 'Quantity formula');
        } catch (\Exception $e) {
            \DynamicProduct::$debug_messages['validation']['quantity_formula'] = $e->getMessage();
            throw new \RuntimeException($this->module->l('Quantity Equation', $source) . ' (' . $quantity_equation->formula . '): ' . DynamicTools::reportException($e, true));
        }

        if ($this->module->provider->isModuleDebugMode()) {
            $cost_equation = DynamicEquation::getCostEquation($id_product);
            try {
                DynamicEquation::evaluateFormula($cost_equation->formula, $input_fields, 'Cost formula');
            } catch (\Exception $e) {
                \DynamicProduct::$debug_messages['validation']['cost_formula'] = $e->getMessage();
                throw new \RuntimeException($this->module->l('Cost Equation', $source) . ' (' . $cost_equation->formula . '): ' . DynamicTools::reportException($e, true));
            }
        }
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param $price_ht
     * @param $quantity
     * @param $id_cart
     */
    public function getCustomizationPrices($id_product, $id_attribute, $price_ht, $quantity, $id_cart)
    {
        $specific_price = $this->module->calculator->getReduction([
            'id_product' => $id_product,
            'id_product_attribute' => $id_attribute,
            'quantity' => $quantity,
            'id_cart' => $id_cart,
        ]);

        $price_ht_nr = $this->applyGroupReductions($price_ht, $id_product);
        $price_ht = $this->applySpecificPriceReductions($price_ht_nr, $specific_price);

        $price_ttc = $this->module->calculator->applyTax($price_ht_nr, null, false, $id_product);
        $price_ttc_nr = $price_ttc;
        $price_ttc = $this->applySpecificPriceReductions($price_ttc, $specific_price);

        return [
            'price_ht' => $price_ht,
            'price_ht_nr' => $price_ht_nr,
            'price_ttc' => $price_ttc,
            'price_ttc_nr' => $price_ttc_nr,
        ];
    }

    public function resetDebugMessages()
    {
        \DynamicProduct::$debug_messages = [
            'errors' => [],
            'validation' => [],
            'calculation' => [],
        ];
    }

    private function getAdapterPrices($adapter_data, $id_product, $id_attribute, $quantity, $id_cart)
    {
        $specific_price = $this->module->calculator->getReduction([
            'id_product' => $id_product,
            'id_product_attribute' => $id_attribute,
            'quantity' => $quantity,
            'id_cart' => $id_cart,
        ]);

        $customization_prices = [
            'price_ht' => 0,
            'price_ht_nr' => 0,
            'price_ttc' => 0,
            'price_ttc_nr' => 0,
        ];

        if (!$adapter_data || !isset($adapter_data['prices'])) {
            return $customization_prices;
        }

        $current_id_module = (int) $this->module->id;
        foreach ($adapter_data['prices'] as $id_module => $prices) {
            if ((int) $id_module !== $current_id_module) {
                $prices['price_ht'] = $this->applySpecificPriceReductions($prices['price_ht_nr'], $specific_price);
                $prices['price_ttc'] = $this->applySpecificPriceReductions($prices['price_ttc_nr'], $specific_price);
                $customization_prices = $this->combinePrices($customization_prices, $prices);
            }
        }

        return $customization_prices;
    }

    public function getProductPrices($id_product, $id_attribute, $quantity)
    {
        $price_ht = \Product::getPriceStatic(
            $id_product,
            false,
            $id_attribute,
            6,
            null,
            false,
            true,
            $quantity
        );

        $price_ht_nr = \Product::getPriceStatic(
            $id_product,
            false,
            $id_attribute,
            6,
            null,
            false,
            false,
            $quantity
        );

        $price_ttc = \Product::getPriceStatic(
            $id_product,
            true,
            $id_attribute,
            6,
            null,
            false,
            true,
            $quantity
        );

        $price_ttc_nr = \Product::getPriceStatic(
            $id_product,
            true,
            $id_attribute,
            6,
            null,
            false,
            false,
            $quantity
        );

        return [
            'price_ht' => $price_ht,
            'price_ht_nr' => $price_ht_nr,
            'price_ttc' => $price_ttc,
            'price_ttc_nr' => $price_ttc_nr,
        ];
    }

    public function calculateDisplayPrice($id_product, $id_attribute)
    {
        list($input_fields) = DynamicInputField::getInputFieldsFromData(
            $id_product,
            $id_attribute,
            [],
            DynamicInputField::LOAD_ALL
        );

        return $this->getPriceEquationResult($id_product, $id_attribute, $input_fields);
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField[] $input_fields
     *
     * @return float
     */
    public function getPriceEquationResult($id_product, $id_attribute, $input_fields)
    {
        $price_equation = DynamicEquation::getPriceEquation($id_product);

        // 6 decimals is the database float precision in this case
        return \Tools::ps_round(DynamicEquation::calculatePriceFormula(
            $id_product,
            $id_attribute,
            $price_equation,
            $input_fields
        ), 6);
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField $input_fields
     *
     * @return float
     */
    public function getWeightEquationResult($id_product, $id_attribute, $input_fields)
    {
        $weight_equation = DynamicEquation::getWeightEquation($id_product);

        // 6 decimals is the database float precision in this case
        return \Tools::ps_round(DynamicEquation::calculateWeightFormula(
            $id_product,
            $id_attribute,
            $weight_equation,
            $input_fields
        ), 6);
    }

    /**
     * @param $id_product
     * @param DynamicInputField[] $input_fields
     */
    public function applyProportions($id_product, $input_fields)
    {
        if (!isset($input_fields['changed'])) {
            return;
        }
        $changed_field = $input_fields['changed']->value;
        if (empty($changed_field)) {
            return;
        }
        $proportions = DynamicProportion::getDataByProduct($id_product);
        $changed_field_value = (float) $input_fields[$changed_field]->value;
        foreach ($proportions as $field_name => $proportion) {
            if ($field_name === $input_fields['changed']->value) {
                $target_field_name = $proportion['target'];
                $value = (float) $proportion['proportion'];
                $target_input_field = $input_fields[$target_field_name];
                $target_input_field->setValue($changed_field_value * $value);
            }
        }
    }

    public function getValueFromProportion($id_field)
    {
        $dynamic_proportions = DynamicProportion::getWithIdField($id_field);

        foreach ($dynamic_proportions as $dynamic_proportion) {
            if ((int) $dynamic_proportion->id_field_src === (int) $id_field) {
                $field = new DynamicField($dynamic_proportion->id_field);
                if ((float) $field->init && (float) $dynamic_proportion->value) {
                    return $field->init / $dynamic_proportion->value;
                }
            }
            if ((int) $dynamic_proportion->id_field === (int) $id_field) {
                $field = new DynamicField($dynamic_proportion->id_field_src);
                if ((float) $field->init && (float) $dynamic_proportion->value) {
                    return $field->init * $dynamic_proportion->value;
                }
            }
        }

        return 0;
    }

    public function applyGroupReductions($price, $id_product)
    {
        list($price, $category_reduction) = $this->module->calculator->applyGroupCategoryReduction($price, $id_product);
        if ($category_reduction === false) {
            $price = $this->module->calculator->applyGroupReduction($price);
        }

        return $price;
    }

    public function applySpecificPriceReductions($price, $specific_price = null)
    {
        return $this->module->calculator->applyReduction($price, $specific_price);
    }

    public function convertPrices($prices)
    {
        $id_default_currency = (int) \Configuration::get('PS_CURRENCY_DEFAULT');
        if (\Validate::isLoadedObject($this->context->currency)
            && (int) $this->context->currency->id !== $id_default_currency) {
            return $this->multiplyPrices($prices, (float) $this->context->currency->conversion_rate);
        }

        return $prices;
    }

    public function combinePrices($prices, $product_prices)
    {
        return [
            'price_ht' => $prices['price_ht'] + $product_prices['price_ht'],
            'price_ht_nr' => $prices['price_ht_nr'] + $product_prices['price_ht_nr'],
            'price_ttc' => $prices['price_ttc'] + $product_prices['price_ttc'],
            'price_ttc_nr' => $prices['price_ttc_nr'] + $product_prices['price_ttc_nr'],
        ];
    }

    public function roundPrices($prices, $precision)
    {
        return [
            'price_ht' => \Tools::ps_round($prices['price_ht'], $precision),
            'price_ht_nr' => \Tools::ps_round($prices['price_ht_nr'], $precision),
            'price_ttc' => \Tools::ps_round($prices['price_ttc'], $precision),
            'price_ttc_nr' => \Tools::ps_round($prices['price_ttc_nr'], $precision),
        ];
    }

    public function multiplyPrices($prices, $multiplier)
    {
        return [
            'price_ht' => $prices['price_ht'] * $multiplier,
            'price_ht_nr' => $prices['price_ht_nr'] * $multiplier,
            'price_ttc' => $prices['price_ttc'] * $multiplier,
            'price_ttc_nr' => $prices['price_ttc_nr'] * $multiplier,
        ];
    }

    public function formatPrices($final_prices)
    {
        return [
            'price_ht' => DynamicTools::formatPrice($final_prices['price_ht']),
            'price_ht_nr' => DynamicTools::formatPrice($final_prices['price_ht_nr']),
            'price_ttc' => DynamicTools::formatPrice($final_prices['price_ttc']),
            'price_ttc_nr' => DynamicTools::formatPrice($final_prices['price_ttc_nr']),
        ];
    }

    /**
     * @param DynamicInputField[] $input_fields
     *
     * @return array
     */
    public function getCalculatedPrices(
        $id_product,
        $id_attribute,
        $input_fields,
        $adapter_data,
        $is_container_hidden
    ) {
        $dynamic_config = DynamicConfig::getByProduct($id_product);
        $quantity = (int) $input_fields['quantity']->value;

        $price_equation_result = $this->getPriceEquationResult(
            $id_product,
            $id_attribute,
            $input_fields
        );

        $id_cart = $this->module->provider->getCart();

        if ($is_container_hidden) {
            $prices = [
                'price_ht' => 0,
                'price_ht_nr' => 0,
                'price_ttc' => 0,
                'price_ttc_nr' => 0,
            ];
        } else {
            $prices = $this->getCustomizationPrices(
                $id_product,
                $id_attribute,
                $price_equation_result,
                $quantity,
                $id_cart
            );
        }

        $adapter_prices = $this->getAdapterPrices($adapter_data, $id_product, $id_attribute, $quantity, $id_cart);
        $modules_prices = $this->combinePrices($prices, $adapter_prices);

        $modules_prices = $this->convertPrices($modules_prices);

        $product_prices = $this->getProductPrices(
            $id_product,
            $id_attribute,
            $quantity
        );

        $final_prices = $this->combinePrices($modules_prices, $product_prices);
        $unit_prices = [
            'price_ht' => $final_prices['price_ht'],
            'price_ht_nr' => $final_prices['price_ht_nr'],
            'price_ttc' => $final_prices['price_ttc'],
            'price_ttc_nr' => $final_prices['price_ttc_nr'],
        ];

        $unit_prices = $this->roundPrices($unit_prices, 6);
        if (defined('_PS_PRICE_COMPUTE_PRECISION_')) {
            $unit_prices = $this->roundPrices($unit_prices, _PS_PRICE_COMPUTE_PRECISION_);
        }

        if ($dynamic_config->multiply_price) {
            $final_prices = $this->multiplyPrices($final_prices, $quantity);
        }

        $final_prices = $this->roundPrices($final_prices, 6);
        if (defined('_PS_PRICE_COMPUTE_PRECISION_')) {
            $final_prices = $this->roundPrices($final_prices, _PS_PRICE_COMPUTE_PRECISION_);
        }

        $formatted_unit_prices = $this->formatPrices($unit_prices);
        $formatted_prices = $this->formatPrices($final_prices);

        return [
            'customization_prices' => $prices,
            'product_prices' => $product_prices,
            'unit_prices' => $unit_prices,
            'final_prices' => $final_prices,
            'formatted_unit_prices' => $formatted_unit_prices,
            'formatted_prices' => $formatted_prices,
            'use_tax' => !\Product::getTaxCalculationMethod(),
        ];
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField[] $input_fields
     *
     * @return array
     */
    public function getCalculatedWeight($id_product, $id_attribute, $input_fields, $is_container_hidden)
    {
        $weight = 0;
        if (!$is_container_hidden) {
            $weight = $this->getWeightEquationResult($id_product, $id_attribute, $input_fields);
        }
        $product_weight = $this->module->provider->getProductWeight($id_product, $id_attribute);
        $total_weight = $weight + $product_weight;
        if (DynamicConfig::getMultiplyPrice($id_product)) {
            $total_weight *= $input_fields['quantity']->value;
        }
        $weight_formatted = $total_weight . ' ' . \Configuration::get('PS_WEIGHT_UNIT');

        return [
            'weight' => $total_weight,
            'weight_formatted' => $weight_formatted,
        ];
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param DynamicInputField[] $input_fields
     */
    public function checkProductStock($id_product, $id_attribute, $input_fields)
    {
        $dynamic_input = new DynamicInput();
        $dynamic_input->input_fields = $input_fields;
        $dynamic_input->id_product = $id_product;
        $dynamic_input->id_attribute = $id_attribute;
        $dynamic_input->cart_quantity = $input_fields['quantity']->value;

        return !DynamicEquation::checkProductStock($dynamic_input, $input_fields);
    }

    public function getQuantityDiscounts($id_product, $id_attribute, $calculated_prices)
    {
        $discounts = [];

        $quantity_discounts = \SpecificPrice::getQuantityDiscounts(
            $id_product,
            $this->context->shop->id,
            $this->context->currency->id,
            $this->context->country->id,
            $this->context->customer->id_default_group,
            $id_attribute,
            false,
            (int) $this->context->customer->id
        );

        $price_ht_nr = (float) $calculated_prices['customization_prices']['price_ht_nr'];

        foreach ($quantity_discounts as $quantity_discount) {
            $quantity = (int) $quantity_discount['from_quantity'];

            $product_prices = $this->getProductPrices(
                $id_product,
                $id_attribute,
                $quantity
            );

            $price_ht = $this->module->calculator->applyReduction($price_ht_nr, $quantity_discount);
            $price_ttc = $this->module->calculator->applyTax($price_ht, $this->context->cart, false, $id_product);
            $price_ttc_nr = $this->module->calculator->applyTax($price_ht_nr, $this->context->cart, false, $id_product);

            $discounted_prices = [
                'price_ht' => $price_ht,
                'price_ht_nr' => $price_ht_nr,
                'price_ttc' => $price_ttc,
                'price_ttc_nr' => $price_ttc_nr,
            ];

            $combined_prices = $this->combinePrices($product_prices, $discounted_prices);
            $formatted_unit_prices = $this->formatPrices($combined_prices);
            $total_prices = $this->multiplyPrices($combined_prices, $quantity);
            $formatted_prices = $this->formatPrices($total_prices);

            $savings = [
                'price_ht' => $total_prices['price_ht_nr'] - $total_prices['price_ht'],
                'price_ht_nr' => 0,
                'price_ttc' => $total_prices['price_ttc_nr'] - $total_prices['price_ttc'],
                'price_ttc_nr' => 0,
            ];
            $savings_formatted = $this->formatPrices($savings);

            $discounts[$quantity] = [
                'unit_prices' => $combined_prices,
                'unit_prices_formatted' => $formatted_unit_prices,
                'totals' => $total_prices,
                'totals_formatted' => $formatted_prices,
                'savings' => $savings,
                'savings_formatted' => $savings_formatted,
            ];
        }

        return $discounts;
    }
}
