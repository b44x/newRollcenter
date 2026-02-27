<?php
/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

require_once _PS_MODULE_DIR_ . 'edrone/vendor/autoload.php';

use Edrone\EdroneModule\EdroneIns;
use Edrone\EdroneModule\EdroneEventOrder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class EdroneEdroneSendOrderByBackendModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        header("Cache-Control: no-cache, max-age=0");
        header("Content-type: application/json");

        $orderId = Tools::getValue('order_id');
        $customerId = Tools::getValue('customer_id');

        if ((!isset($orderId) || !$orderId) || (!isset($customerId) || !$customerId)) {
            return false;
        }

        $module = Module::getInstanceByName('edrone');
        $context = Context::getContext();
        $customer = new Customer($customerId);
        $order = new Order($orderId);

        $module->prepareOrderEvent([
            'order' => $order,
            'edroneCustomer' => $customer // We need to make it custom variable so it is recognized from PS passing customer
        ]);

        if (!empty($context->language->language_code) && Configuration::get(Edrone::APP_ID_FIELD . '_' . $context->language->language_code)) {
            $appId = Edrone::APP_ID_FIELD . '_' . $context->language->language_code;
            $edrone = new EdroneIns(trim(Configuration::get($appId)));
        } else {
            $edrone = new EdroneIns(trim(Configuration::get(Edrone::APP_ID_FIELD)));
        }

        $isOrderSent = $edrone->prepare(
            EdroneEventOrder::create()->
            productUrls($module->edroneOrder['product_urls'])->
            userFirstName($module->edroneOrder['first_name'])->
            userLastName($module->edroneOrder['last_name'])->
            userEmail($module->edroneOrder['email'])->
            productIds($module->edroneOrder['product_ids'])->
            productSkus($module->edroneOrder['product_skus'])->
            productTitles($module->edroneOrder['product_titles'])->
            productImages($module->edroneOrder['product_images'])->
            productCategoryIds($module->edroneOrder['product_category_ids'])->
            productCategoryNames($module->edroneOrder['product_category_names'])->
            orderId($module->edroneOrder['order_id'])->
            orderPaymentValue($module->edroneOrder['base_payment_value'])->
            shopLang($module->edroneOrder['shop_lang'])->
            coupon($module->edroneOrder['coupon'])->
            orderCurrency($module->edroneOrder['order_currency'])->
            orderBaseCurrency($module->edroneOrder['base_currency'])->
            orderBasePaymentValue($module->edroneOrder['base_payment_value'])->
            productCounts($module->edroneOrder['product_quantity_total'])->
            userUid($module->edroneOrder['uid'])
        )->send();

        $callableReturn = method_exists('Tools', 'jsonEncode') // Support for PS 8
            ? 'Tools::jsonEncode'
            : 'json_encode';

        die($callableReturn([
            'orderSentSuccessfully' => (int) $isOrderSent
        ]));
    }
}

