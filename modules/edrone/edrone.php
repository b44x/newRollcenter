<?php
/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

require_once __DIR__ . '/vendor/autoload.php';

use Edrone\EdroneModule\EdroneIns;
use Edrone\EdroneModule\EdroneEventOrder;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

define('PAGE_PRODUCT', 'product_view');
define('PAGE_CATEGORY', 'product_category_view');
define('PAGE_ADD_TO_CART', 'add_to_cart');
define('PAGE_ORDER', 'order');
define('PAGE_OTHER', 'other');
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'edrone_ef.php');


if (!defined('_PS_VERSION_')) {
    exit;
}

class Edrone extends Module implements WidgetInterface
{
    /** @const string */
    const SUBSCRIBER_EXISTS_FIELD = 'EDRONE_SUBSCRIBER_EXISTS';

    /** @const string */
    const SUBSCRIBER_STATUS_FIELD = 'EDRONE_SUBSCRIBER_STATUS';

    /** @const string */
    const APP_ID_FIELD = 'EDRONE_APP_ID';

    /** @const string */
    const SSIDE_STATUS_FIELD = 'EDRONE_SSIDE_STATUS';

    const USER_ID_SEPARATOR = ':';

    /** @var array Smarty variables */
    public $edroneVariables = [];

    /** @var array Order confirmation data */
    public $edroneOrder = [];

    public function __construct()
    {
        $this->name = 'edrone';
        $this->tab = 'advertising_marketing';
        $this->version = '1.2.3';
        $this->author = 'edrone';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = '3ed42ac40c8dd2c980edfe89be1eacde';

        parent::__construct();

        $this->displayName = $this->l('Edrone CRM 1.2.3');
        $this->description = $this->l('Module which provides integration with Edrone');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if ((float) _PS_VERSION_ < 1.7) {
            die("This module supports only PrestaShop 1.7 and above. To install module on your version, please contact our support for different package");
        }

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Webservice key generation - for future implementation
//        $webservice = new WebserviceKey();
//        $webservice->active = 1;
//        $webservice->key = 'FS40KOQ0UG22TQCVFP5BCWLGJ1W3YC6K';
//        $webservice->description = 'edrone key';
//
//        $webservice->add();


        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayOrderConfirmation') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('actionObjectCustomerAddAfter') &&
            $this->registerHook('actionCartUpdateQuantityBefore') &&
            $this->registerHook('moduleRoutes') &&
            Configuration::updateValue(self::APP_ID_FIELD, ' ') &&
            Configuration::updateValue(self::SSIDE_STATUS_FIELD, 0) &&
            Configuration::updateValue(self::SUBSCRIBER_STATUS_FIELD, 1) &&
            Configuration::updateValue(self::SUBSCRIBER_EXISTS_FIELD, 1);

    }

    public function hookActionCartUpdateQuantityBefore($params)
    {
        $product = $params['product'];

        if (!Validate::isLoadedObject($this->context->cart) || !Tools::getIsset("id_product")) {
            return;
        }

        if ((int) Tools::getValue('add') === 1) {
            if (isset($product) && $product) {
                $product_category_names_array = Category::getCategoryInformation(Product::getProductCategories($product->id));

                $quest = array();
                $quest['product_titles'] = $product->name;
                $quest['product_category_ids'] = implode("~", array_column($product_category_names_array, 'id_category'));
                $quest['product_category_names'] = implode("~", array_column($product_category_names_array, 'name'));
                $id_image = Product::getCover($product->id);

                if ((is_array($id_image) || $id_image instanceof Countable) && count($id_image) > 0) {
                    $quest['product_images'] = $this->context->link->getImageLink($product->link_rewrite, $id_image['id_image']);
                }
                $quest['product_ids'] = $product->id;
                $quest['product_skus'] = $product->reference;
                $quest['action_type'] = 'add_to_cart';

                NotificationTemp::setNotification($quest, $this->context->cookie->getName());
            }
        }
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    private function getUrlEncodedIfNotNull($val)
    {
        if ($val !== null) {
            return urlencode($val);
        }
    }

    private function checkPage()
    {
        /*
         * In the current condition PAGE_CATEGORY won't be recognized if we use custom module to display category page
         */
        if ($this->context->controller instanceof ProductController) {
            return PAGE_PRODUCT;
        }

        if ($this->context->controller instanceof CategoryController) {
            return PAGE_CATEGORY;
        }
    }

    public function generateUserUID(Customer $user)
    {
        return base64_encode($user->id . self::USER_ID_SEPARATOR . $user->email);
    }

    public function getUserFromUserUID($uid)
    {
        $decoded = base64_decode($uid);
        $userData = explode(self::USER_ID_SEPARATOR, $decoded);

        return [
            'id_customer' => $userData[0],
            'email' => $userData[1]
        ];
    }

    private function getInfoPageCategory()
    {
        $categoryId = Tools::getValue('id_category');
        if ($categoryId) {
            $category = Category::getCategoryInformation([$categoryId]);
            $categoryName = $category[$categoryId]["name"] ?: "";

            $this->edroneVariables = array_merge($this->edroneVariables, [
                'product_category_ids' => $this->getUrlEncodedIfNotNull($categoryId),
                'product_category_names' => $this->getUrlEncodedIfNotNull($categoryName),
            ]);
        }
    }

    private function getInfoPageProduct()
    {
        if ($id_product = (int) Tools::getValue('id_product')) {
            $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
        }

        $product_category_ids = [];
        $product_category_names = [];

        $product_category_names_array = Category::getCategoryInformation(
            Product::getProductCategories($id_product)
        );

        if (is_array($product_category_names_array)) {
            $product_category_ids = implode("~", array_column($product_category_names_array, 'id_category'));
            ;
            $product_category_names = implode("~", array_column($product_category_names_array, 'name'));
        }

        // Unused, commented for future reference
        //  $images = Image::getImages((int) $this->context->cookie->id_lang, (int) $product->id);
        $id_image = Product::getCover($product->id);
        if ((is_array($id_image) || $id_image instanceof Countable) && sizeof($id_image) > 0) {
            $image_url = $this->context->link->getImageLink($product->link_rewrite, $id_image['id_image']);
        }
        $context = Context::getContext();
        $userAddress = new Address((int) $context->cart->id_address_delivery);

        if ($userAddress->phone_mobile !== '') {
            $phone = $userAddress->phone_mobile;
        } elseif ($userAddress->phone !== '') {
            $phone = $userAddress->phone;
        }

        if (!isset($phone) || !$phone) {
            $phone = '';
        }

        $this->edroneVariables = array_merge($this->edroneVariables, [
            'product_titles' => $this->getUrlEncodedIfNotNull($product->name),
            'product_images' => $this->getUrlEncodedIfNotNull($image_url),
            'product_ids' => $this->getUrlEncodedIfNotNull($product->id),
            'product_skus' => $this->getUrlEncodedIfNotNull($product->reference),
            'product_category_ids' => $this->getUrlEncodedIfNotNull($product_category_ids),
            'product_urls' => $this->getUrlEncodedIfNotNull($this->context->link->getProductLink($product)),
            'product_category_names' => $this->getUrlEncodedIfNotNull($product_category_names),
            'action_type' => $this->getUrlEncodedIfNotNull(PAGE_PRODUCT),
            'product_counts' => $this->getUrlEncodedIfNotNull($product->quantity),
            'base_payment_value' => $this->getUrlEncodedIfNotNull(Tools::ps_round($product->base_price, 2, PS_ROUND_DOWN)),
            'base_currency' => $this->getUrlEncodedIfNotNull($context->currency->iso_code),
            'city' => $this->getUrlEncodedIfNotNull($userAddress->city),
            'country' => $this->getUrlEncodedIfNotNull($userAddress->country),
            'phone' => $this->getUrlEncodedIfNotNull($phone),
            'product_brand_ids' => $this->getUrlEncodedIfNotNull($product->id_manufacturer),
            'product_brand_names' => $this->getUrlEncodedIfNotNull($product->manufacturer_name),
        ]);
    }

    private function getInfoPageOther()
    {
        $this->edroneVariables = array_merge($this->edroneVariables, [
            'action_type' => $this->getUrlEncodedIfNotNull(PAGE_OTHER)
        ]);
    }

    private function getMoreInformation()
    {
        switch ($this->checkPage()) {
            case PAGE_PRODUCT:
                $this->getInfoPageProduct();
                break;
            case PAGE_CATEGORY:
                $this->getInfoPageCategory();
                break;
            case PAGE_OTHER:
                $this->getInfoPageOther();
                break;
        }
    }

    public function hookActionObjectCustomerAddAfter($params)
    {
        // Resets after refresh, to find better solution
        $customer = $params['object'];

        if (!(int)$customer->is_guest && (int) $customer->newsletter) {
            setcookie('edroneSendRegisterTrace', 1);
        }
    }

    public function hookDisplayOrderConfirmation($params)
    {
        if (!Configuration::get(self::SSIDE_STATUS_FIELD)) {
            $this->prepareOrderEvent($params);

            Media::addJsDef([
                'sendOrderByBackendController' => $this->context->link->getModuleLink('edrone', 'EdroneSendOrderByBackend'),
                'edroneCustomerId' => $this->context->customer->id,
                'edroneOrderId' => $params['order']->id
            ]);

            $this->context->smarty->assign([
                'edroneVariables' => $this->edroneOrder
            ]);

            return $this->display(__FILE__, 'views/templates/front/load-js-variables.tpl');
        }
    }

    public function prepareOrderEvent($params)
    {
        $order = $params['order'];
        $products = $order->getProducts();
        $customer = $params['edroneCustomer'] ?? $this->context->customer;
        $gender = new Gender($customer->id_gender);
        $address = new Address($order->id_address_delivery);
        $currency = Currency::getCurrency($order->id_currency);
        $phone = isset($address->phone) ? $address->phone : $address->phone_mobile;
        $shop_lang = (int) $this->context->language->id;
        $country = is_array($this->context->country->name) ?
                        $this->context->country->name[$shop_lang] :
                        $this->context->country->name; // Multilingual store support
        $productQuantitiesTotal = 0;
        $productIds =
        $coupons =
        $productNames =
        $productSkus =
        $productImages =
        $productCategoryIds =
        $productCategoryNames =
        $productBrandNames =
        $productBrandIds =
        $productQuantities =
        $orderCategoryIds =
        $orderCategoryNames =
        $productUrls = [];

        foreach ($products as $product) {
            $productObj = new Product($product['id_product']);
            $productIds[] = $product['id_product'] ?? 0;
            $productNames[] = $product['product_name'] ?? '';
            $productSkus[] = $product['reference'] ?? '';
            $productQuantities[] = $product['product_quantity'];
            $productQuantitiesTotal += (int) $product['product_quantity'];
            $productBrandIds[] = $product['id_manufacturer'] ?? 0;
            $productBrandNames[] = Manufacturer::getNameById($product['id_manufacturer']);
            $productUrls[] = $this->context->link->getProductLink($product);
            $id_image = Product::getCover($product['id_product']);

            if ((is_array($id_image) || $id_image instanceof Countable) && count($id_image) > 0) {
                $linkRewrite = $productObj->link_rewrite;

                if (is_array($linkRewrite)) {
                    $linkRewrite = $linkRewrite[$this->context->language->id]; // Get only first index to avoid duplicate images
                }

                $productImages[] = $this->context->link->getImageLink($linkRewrite, $id_image['id_image']);
            }

            $productCategoryIdsRaw = Product::getProductCategories($product['product_id']);
            $additionalCategoryInformation = Category::getCategoryInformation($productCategoryIdsRaw);

            foreach ($productCategoryIdsRaw as $productCategoryId) {
                $productCategoryIds[] = $productCategoryId;
                $productCategoryNames[] = is_array($additionalCategoryInformation) ? $additionalCategoryInformation[$productCategoryId]['name'] : '';
            }

            $orderCategoryIds[] = implode('~', $productCategoryIds);
            $orderCategoryNames[] = implode('~', $productCategoryNames);

        }

        // Gather potential coupon codes
        foreach ($order->getDiscounts() as $discount) {
            $discountId = $discount['id_cart_rule'];
            $cartRule = new CartRule($discountId);
            if (!empty($cartRule->code)) {
                $coupons[] = $cartRule->code;
            }
        }

        switch ($gender->type) {
            case 1:
                $customerGender = 'F';
                break;
            case 0:
                $customerGender = 'M';
                break;
            default:
                $customerGender = 'Unknown';
                break;
        }

        if (isset($order) && $order) {

            $this->edroneOrder = [
                'action_type' => 'order',
                'email' => $customer->email,
                'first_name' => $customer->firstname,
                'last_name' => $customer->lastname,
                'phone' => $phone ?? '', // In case shop doesn't force users to add their number
                'gender' => $customerGender,
                'coupon' => urlencode(implode('|', $coupons)),
                'product_skus' => urlencode(implode('|', $productSkus)),
                'product_ids' => urlencode(implode('|', $productIds)),
                'product_titles' => urlencode(implode('|', $productNames)),
                'product_images' => urlencode(implode('|', $productImages)), // use "|" sign to separate products from each other and use url_encode
                'product_urls' => urlencode(implode('|', $productUrls)), // use "|" sign to separate products from each other and use url_encode
                'product_category_ids' => urlencode(implode('|', $orderCategoryIds)), // use "|" sign to separate products from each other and "~" to separate values connected to product from each other and use url_encode
                'product_category_names' => urlencode(implode('|', $orderCategoryNames)), // use "|" sign to separate products from each other and "~" to separate values connected to product from each other and use url_encode
                'product_counts' => urlencode(implode('|', $productQuantities)),
                'product_brand_names' => urlencode(implode('|', $productBrandNames)),
                'product_brand_ids' => urlencode(implode('|', $productBrandIds)),
                'product_quantity_total' => $productQuantitiesTotal,
                'order_id' => $order->id,
                'country' =>  $country,
                'city' => $address->city ? urlencode($address->city) : '',
                'base_currency' => $this->context->currency->iso_code,
                'order_currency' => is_array($currency) ? $currency['iso_code'] : 'Unknown currency',
                'base_payment_value' => Tools::ps_round($order->total_paid, 2, PS_ROUND_DOWN),
                'shop_lang' => $shop_lang,
                'order_payment_value' => Tools::ps_round($order->total_paid, 2, PS_ROUND_DOWN),
                'uid' => $this->generateUserUID($customer)
            ];

            if (isset($customer->birthday) && $customer->birthday != '0000-00-00') {
                $this->edroneOrder['birth_date'] = $customer->birthday;
            }
        }
    }

    public function hookActionValidateOrder($params)
    {
        $this->prepareOrderEvent($params);

        $result = NotificationTemp::setNotification($this->edroneOrder, $this->context->cookie->getName());
        $context = Context::getContext();

        //SERVER SIDE
        if (Configuration::get(self::SSIDE_STATUS_FIELD)) {
            if (!empty($context->language->language_code) && Configuration::get(self::APP_ID_FIELD . '_' . $context->language->language_code)) {
                $appId = self::APP_ID_FIELD . '_' . $context->language->language_code;
                $edrone = new EdroneIns(trim(Configuration::get($appId)));
            } else {
                $edrone = new EdroneIns(trim(Configuration::get(self::APP_ID_FIELD)));
            }

            $edrone->prepare(
                EdroneEventOrder::create()->
                productUrls($this->edroneOrder['product_urls'])->
                userFirstName($this->edroneOrder['first_name'])->
                userLastName($this->edroneOrder['last_name'])->
                userEmail($this->edroneOrder['email'])->
                productIds($this->edroneOrder['product_ids'])->
                productSkus($this->edroneOrder['product_skus'])->
                productTitles($this->edroneOrder['product_titles'])->
                productImages($this->edroneOrder['product_images'])->
                productCategoryIds($this->edroneOrder['product_category_ids'])->
                productCategoryNames($this->edroneOrder['product_category_names'])->
                orderId($this->edroneOrder['order_id'])->
                orderPaymentValue($this->edroneOrder['base_payment_value'])->
                shopLang($this->edroneOrder['shop_lang'])->
                coupon($this->edroneOrder['coupon'])->
                orderCurrency($this->edroneOrder['order_currency'])->
                orderBaseCurrency($this->edroneOrder['base_currency'])->
                orderBasePaymentValue($this->edroneOrder['base_payment_value'])->
                productCounts($this->edroneOrder['product_counts'])->
                userUid($this->edroneOrder['uid'])
            )->send();
        }
    }

    public function hookDisplayHeader()
    {
        $context = Context::getContext();

        if (!empty($context->language->language_code) && Configuration::get(self::APP_ID_FIELD . '_' . $context->language->language_code)) {
            $app_id = Configuration::get(self::APP_ID_FIELD . '_' . $context->language->language_code);
        } else {
            $app_id = Configuration::get(self::APP_ID_FIELD);
        }

        // We are unable to use Context cookie class due to session changes
        if (isset($_COOKIE["edroneSendRegisterTrace"]) && $_COOKIE["edroneSendRegisterTrace"]) {
            Media::addJsDef([
                'edroneSendRegisterTrace' => 1,
                'edroneTag' => 'Register'
            ]);

            // Remove cookie
            setcookie('edroneSendRegisterTrace', '', time() - 3600);
        }

        $this->edroneVariables = [
            'app_id' =>  $app_id
        ];

        $this->getMoreInformation();

        if ($this->context->customer->isLogged()) {
            $this->edroneVariables['user_id'] = $this->generateUserUID($this->context->customer);
        }

        $this->context->smarty->assign([
            'edroneVariables' => $this->edroneVariables
        ]);

        Media::addJsDef([
            'edroneIsSSOrder' => Configuration::get(self::SSIDE_STATUS_FIELD),
            'edroneSessionController' => $this->context->link->getModuleLink('edrone', 'EdroneUserSessionData'),
            'edroneAddToCartController' => $this->context->link->getModuleLink('edrone', 'EdroneAddToCartAjax'),
        ]);

        $this->context->controller->addJS($this->_path . 'views/js/edroneInit.js');
        $this->context->controller->addJS($this->_path . 'views/js/ajaxCart.js');
        $this->context->controller->addJS($this->_path . 'views/js/buttonAddToCart.js');

        if (Configuration::get(self::SUBSCRIBER_STATUS_FIELD) == '1') {
            $this->context->controller->addJS($this->_path . 'views/js/edroneSubscribe.js');
        }

        return $this->display(__FILE__, 'views/templates/front/load-js-variables.tpl');
    }

    public function hookModuleRoutes()
    {
        return [
            'edrone-update-subscriber-status' => [
                'controller' => 'edroneUpdateSubscriberStatus',
                'rule' => 'update_subscriber_status',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'edrone'
                ]
            ]
        ];
    }

    public function getContent()
    {
        $output = null;
        $allLanguages = Language::getLanguages(false); // false - get also inactive languages

        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue(self::SUBSCRIBER_STATUS_FIELD, Tools::getValue(self::SUBSCRIBER_STATUS_FIELD));
            Configuration::updateValue(self::SUBSCRIBER_EXISTS_FIELD, Tools::getValue(self::SUBSCRIBER_EXISTS_FIELD));
            Configuration::updateValue(self::APP_ID_FIELD, Tools::getValue(self::APP_ID_FIELD) ? trim(Tools::getValue(self::APP_ID_FIELD)) : Configuration::get(self::APP_ID_FIELD));
            Configuration::updateValue(self::SSIDE_STATUS_FIELD, Tools::getValue(self::SSIDE_STATUS_FIELD));

            foreach ($allLanguages as $language) {
                $appIdTmp = self::APP_ID_FIELD . '_' . $language['language_code'];
                $appIdLang = (string) Tools::getValue($appIdTmp);
                Configuration::updateValue($appIdTmp, trim($appIdLang));
            }
            $output .= $this->displayConfirmation($this->l('Settings updated...'));
        }

        // Pass $allLanguages variable to avoid
        return $output . $this->displayForm($allLanguages);
    }

    public function displayForm($allLanguages)
    {
        // Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $options = [
            [
                'id_option' => 1, // The value of the 'value' attribute of the <option> tag.
                'name' => 'YES'              // The value of the text content of the  <option> tag.
            ]
        ];

        $inputFields = [
            [
                'type' => 'text',
                'label' => $this->l('Default AppId'),
                'name' => self::APP_ID_FIELD,
                'size' => 20,
                'required' => true,
            ],
        ];

        foreach ($allLanguages as $language) {
            $inputFields[] = [
                'type' => 'text',
                'label' => $this->l('AppId for ') . $language['name'],
                'name' => self::APP_ID_FIELD . '_' . $language['language_code'],
                'size' => 20,
                'required' => false,
            ];
        }

        array_push(
            $inputFields,
            ...[
            [
                'type' => 'switch',
                'label' => $this->l('Server side method'),
                'is_bool' => true,
                'name' => self::SSIDE_STATUS_FIELD,
                'id' => self::SSIDE_STATUS_FIELD,
                'values' => [
                    [
                        'id' => self::SSIDE_STATUS_FIELD . '_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ],
                    [
                        'id' => self::SSIDE_STATUS_FIELD . '_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    ]
                ],
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Subscribe newsletter to edrone'),
                'is_bool' => true,
                'name' => self::SUBSCRIBER_STATUS_FIELD,
                'id' => self::SUBSCRIBER_STATUS_FIELD,
                'values' => [
                    [
                        'id' => self::SUBSCRIBER_STATUS_FIELD . '_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ],
                    [
                        'id' => self::SUBSCRIBER_STATUS_FIELD . '_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    ]
                ],
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Update only existing subscriber'),
                'is_bool' => true,
                'name' => self::SUBSCRIBER_EXISTS_FIELD,
                'id' => self::SUBSCRIBER_EXISTS_FIELD,
                'values' => [
                    [
                        'id' => 'subscriberExists_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ],
                    [
                        'id' => 'subscriberExists_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    ]
                ],
            ],
            ]
        );

        // Init Fields form array
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => $inputFields,
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = false;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;

        // Load current value
        $helper->fields_value[self::APP_ID_FIELD] = Configuration::get(self::APP_ID_FIELD);
        foreach ($allLanguages as $language) {
            $appIdTmp = self::APP_ID_FIELD . '_' . $language['language_code'];
            $helper->fields_value[$appIdTmp] = Configuration::get($appIdTmp);
        }

        $helper->fields_value[self::SSIDE_STATUS_FIELD] = Configuration::get(self::SSIDE_STATUS_FIELD);
        $helper->fields_value[self::SUBSCRIBER_STATUS_FIELD] = Configuration::get(self::SUBSCRIBER_STATUS_FIELD);
        $helper->fields_value[self::SUBSCRIBER_EXISTS_FIELD] = Configuration::get(self::SUBSCRIBER_EXISTS_FIELD);
        return $helper->generateForm($fields_form);
    }

    public function renderWidget($hookName, array $configuration)
    {
        if ($hookName == 'displayOrderConfirmation') {
            // Set order info
            $customer = $configuration['customer'];

            if (!$customer instanceof Customer) {
                $customerInstance = new Customer();
                $customer = $customerInstance->getByEmail($customer['email']);
            }

            $customerOrders = Order::getCustomerOrders($customer->id);
            $latestOrder = $customerOrders[0]; // They are sorted by date desc

            $order = new Order($latestOrder['id_order']);

            return $this->hookDisplayOrderConfirmation(['order' => $order]);
        }
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        return $configuration;
    }

}
