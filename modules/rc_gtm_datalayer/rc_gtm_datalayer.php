<?php
if (!defined('_PS_VERSION_')) { exit; }

class Rc_Gtm_Datalayer extends Module
{
    public function __construct()
    {
        $this->name = 'rc_gtm_datalayer';
        $this->tab = 'analytics_stats';
        $this->version = '1.1.4';
        $this->author = 'Rollcenter';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'RC: dataLayer init';
        $this->description = 'Dodaje globalny dataLayer (rc_init) + zdarzenia e-commerce.';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayFooter')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    private function getControllerPhpSelf(): string
    {
        return $this->context->controller ? (string)$this->context->controller->php_self : '';
    }

    /**
     * Hook aktywny (na przyszłość), ale NIE ładujemy tu JS-ów.
     * Wszystkie nasze JS-y ładujemy pewnie przez hookDisplayHeader + head.tpl.
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        return;
    }

    /**
     * Items z OrderDetails (purchase) – item_id preferuje SKU/reference, fallback product_id.
     */
    private function getPurchaseItemsByOrderId(int $idOrder): array
    {
        $items = [];
        if ($idOrder <= 0) {
            return $items;
        }

        $order = new Order($idOrder);
        if (!Validate::isLoadedObject($order)) {
            return $items;
        }

        $products = $order->getProducts(); // order_detail rows

        foreach ($products as $p) {
            $name = (string)($p['product_name'] ?? $p['name'] ?? '');
            $qty  = (int)($p['product_quantity'] ?? $p['quantity'] ?? 1);

            $productId = $p['product_id'] ?? null;
            $ref = $p['product_reference'] ?? $p['reference'] ?? null;

            // unit price incl tax
            $unitIncl = null;
            if (isset($p['unit_price_tax_incl'])) {
                $unitIncl = (float)$p['unit_price_tax_incl'];
            } elseif (isset($p['product_price_wt'])) {
                $unitIncl = (float)$p['product_price_wt'];
            }

            $item = [
                'item_name' => $name,
                'quantity'  => $qty > 0 ? $qty : 1,
            ];

            // item_id: preferuj SKU/reference, fallback product_id
            if (!empty($ref)) {
                $item['item_id'] = (string)$ref;
            } elseif (!empty($productId)) {
                $item['item_id'] = (string)$productId;
            }

            if ($unitIncl !== null) {
                $item['price'] = round($unitIncl, 2);
            }

            $items[] = $item;
        }

        return $items;
    }

    public function hookDisplayHeader($params)
    {
        $context = $this->context;

        $page = $this->getCurrentPageType();
        $ctrlPhpSelf = $this->getControllerPhpSelf();
        $ctrlGet = (string)Tools::getValue('controller');

        // currency z kontekstu (dla rc_init)
        $ctxCurrencyIso = ($context->currency && !empty($context->currency->iso_code)) ? $context->currency->iso_code : null;

        $dl = [
            'event' => 'rc_init',
            'page_type' => $page,
            'currency' => $ctxCurrencyIso,
            'lang' => $context->language ? $context->language->iso_code : null,
            'shop_id' => (int)$context->shop->id,
            'controller_php_self' => $ctrlPhpSelf,
            'controller_get' => $ctrlGet,
        ];

        // mapowanie JS per page_type (ładowanie w head.tpl)
        $rcJs = [
            'purchase' => null,
            'category' => null,
            'category_select' => null,
            'cart' => null,
            'checkout' => null,
        ];

        if ($page === 'purchase') {
            $rcJs['purchase'] = $this->_path.'views/js/purchase.js?v='.(string)$this->version;
        }
        if ($page === 'category') {
            $rcJs['category'] = $this->_path.'views/js/category_view_item_list.js?v='.(string)$this->version;
            $rcJs['category_select'] = $this->_path.'views/js/category_select_item.js?v='.(string)$this->version;
        }
        if ($page === 'cart') {
            $rcJs['cart'] = $this->_path.'views/js/cart_remove_from_cart.js?v='.(string)$this->version;
        }
        if ($page === 'checkout') {
            $rcJs['checkout'] = $this->_path.'views/js/checkout_shipping_payment.js?v='.(string)$this->version;
        }

        // backend payload dla purchase (rcPurchasePayload)
        $purchasePayloadJson = null;

        if ($page === 'purchase') {
            $idOrder = (int)Tools::getValue('id_order');
            if ($idOrder > 0) {
                $order = new Order($idOrder);
                if (Validate::isLoadedObject($order)) {
                    $orderCurrency = new Currency((int)$order->id_currency);
                    $orderCurrencyIso = Validate::isLoadedObject($orderCurrency) ? $orderCurrency->iso_code : 'PLN';

                    $value = (float)$order->total_paid_tax_incl;
                    $tax = (float)$order->total_paid_tax_incl - (float)$order->total_paid_tax_excl;
                    $shipping = (float)$order->total_shipping_tax_incl;

                    $payload = [
                        'transaction_id' => (string)$order->reference,
                        'currency'       => (string)$orderCurrencyIso,
                        'value'          => round($value, 2),
                        'tax'            => round($tax, 2),
                        'shipping'       => round($shipping, 2),
                        'items'          => $this->getPurchaseItemsByOrderId($idOrder),
                    ];

                    $purchasePayloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }

        $this->context->smarty->assign([
            'rc_datalayer_json' => json_encode($dl, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'rc_js' => $rcJs,
            'rc_purchase_payload_json' => $purchasePayloadJson,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/head.tpl');
    }

    public function hookDisplayFooter($params)
    {
        $ctrl = $this->getControllerPhpSelf();

        if ($ctrl === 'product') {
            return
                $this->display(__FILE__, 'views/templates/hook/product_view_item.tpl') .
                $this->display(__FILE__, 'views/templates/hook/product_add_to_cart.tpl');
        }

        if ($ctrl === 'cart') {
            return $this->display(__FILE__, 'views/templates/hook/cart_view_cart.tpl');
        }

        if ($ctrl === 'order') {
            return $this->display(__FILE__, 'views/templates/hook/checkout_begin_checkout.tpl');
        }

        return '';
    }

    private function getCurrentPageType()
    {
        $ctrl = $this->getControllerPhpSelf();
        if (!$ctrl) {
            $ctrl = Tools::getValue('controller');
        }
        if (!$ctrl) return 'unknown';

        $map = [
            'index' => 'home',
            'category' => 'category',
            'product' => 'product',
            'cart' => 'cart',
            'order' => 'checkout',
            'orderconfirmation' => 'purchase',
            'order-confirmation' => 'purchase',
            'search' => 'search',
        ];

        return $map[$ctrl] ?? $ctrl;
    }
}
