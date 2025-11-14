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

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\ProductHelper;
use DynamicProduct\classes\models\DynamicCombinationField;
use DynamicProduct\classes\models\DynamicCombinationValue;
use DynamicProduct\classes\models\DynamicConfig;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicInput;
use DynamicProduct\classes\models\DynamicProductConfigCategoryLink;
use DynamicProduct\classes\models\DynamicProductConfigLink;

class DynamicProductSettingsController extends ModuleAdminController
{
    /** @var DynamicProduct */
    public $module;
    public $action;

    /** @var Context */
    public $context;
    public $id_product;
    public $id_default_lang;

    public function __construct()
    {
        if (empty($_POST)) {
            $_POST = json_decode(Tools::file_get_contents('php://input'), true);
        }
        parent::__construct();
        $this->context = Context::getContext();
        $this->action = Tools::getValue('action');
        $this->id_product = (int) Tools::getValue('id_product');
        $this->id_default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
    }

    public function postProcess()
    {
        $source = basename(__FILE__, '.php');
        $restricted = DynamicTools::getRestricted('_DP_RESTRICTED_');
        if ((int) $this->context->employee->id_profile !== 1 && in_array($this->id_product, $restricted)) {
            exit(json_encode([
                'error' => true,
                'message' => $this->module->l('This product is for viewing only!', $source),
            ]));
        }

        $method = 'process' . Tools::toCamelCase($this->action, true);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        exit;
    }

    private function processLoadData()
    {
        $id_product = $this->id_product;

        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        // get product combinations
        $combinations = $this->module->provider->getProductCombinations($id_product);

        if ($id_product !== $id_source_product) {
            $source_combinations = $this->module->provider->getProductCombinations($id_source_product);
            foreach ($combinations as $index => &$combination) {
                if (!isset($source_combinations[$index])) {
                    break;
                }
                $combination['id_product_attribute'] = $source_combinations[$index]['id_product_attribute'];
            }
        }

        $visibility_values = false;
        if (is_array($combinations) && count($combinations)) {
            $visibility_values = $this->module->provider->getVisibilityValues($id_source_product);
        }

        $combination_fields = DynamicCombinationField::getByIdProduct($id_source_product);

        $combination_values = DynamicCombinationValue::getValuesByIdProduct($id_source_product);
        $combination_values = DynamicCombinationValue::organizeByAttributesAndFields($combination_values);

        $id_lang = $this->context->language->id;

        $this->respond([
            'fields' => DynamicField::getFieldRowsByProduct($this->id_product),
            'favorite_fields' => DynamicField::getFavoriteFields($id_lang),
            'common_fields' => DynamicField::getCommonFields($id_lang),
            'combinations' => $combinations,
            'combination_fields' => $combination_fields,
            'combination_values' => $combination_values,
            'visibility' => $visibility_values,
            'databases' => ProductHelper::getProductDatabaseFields(),
        ]);
    }

    private function processLoadProducts()
    {
        $this->respond([
            'products' => DynamicConfig::getActiveProducts(),
            'linked_configs' => ConfigLinkHelper::getLinkedConfigs(),
        ]);
    }

    private function processGetActiveProducts()
    {
        $this->respond([
            'products' => DynamicConfig::getActiveProductsWithLabels($this->context->language->id),
        ]);
    }

    private function processAddProductToOrder()
    {
        $id_order = (int) Tools::getValue('id_order');
        $id_product = (int) Tools::getValue('id_product');
        $id_attribute = (int) Tools::getValue('id_attribute');
        $id_input = (int) Tools::getValue('id_input');
        $quantity = (int) Tools::getValue('quantity', 1);

        $order = new Order($id_order);
        $id_cart = $order->id_cart;

        $input = new DynamicInput($id_input);
        $product = new Product($id_product, true, $this->context->language->id);

        $id_customization_field = $this->module->handler->addCustomField($id_product);

        Db::getInstance()->insert('customization', [
            'id_product' => $id_product,
            'id_product_attribute' => $id_attribute,
            'id_cart' => $id_cart,
            'quantity' => $quantity,
            'in_cart' => 1,
        ]);
        $id_customization = (int) Db::getInstance()->Insert_ID();

        Db::getInstance()->insert('customized_data', [
            'id_customization' => $id_customization,
            'type' => Product::CUSTOMIZE_TEXTFIELD,
            'value' => $id_input,
            'index' => $id_customization_field,
            'price' => $input->price,
            'weight' => $input->weight,
            'id_module' => $this->module->id,
        ]);

        $input->id_customization = $id_customization;
        $input->save();

        Db::getInstance()->insert('cart_product', [
            'id_cart' => $id_cart,
            'id_shop' => $order->id_shop,
            'id_address_delivery' => $order->id_address_delivery,
            'id_product' => $id_product,
            'id_product_attribute' => $id_attribute,
            'id_customization' => $id_customization,
            'quantity' => $quantity,
        ]);

        $product_price = Product::getPriceStatic(
            $id_product,
            false,
            $id_attribute,
            6,
            null,
            false,
            false,
            $quantity
        );

        $price_tax_incl = Product::getPriceStatic(
            $id_product,
            true,
            $id_attribute,
            6,
            null,
            false,
            false,
            $quantity,
            false,
            null,
            $id_cart,
            null,
            $specific_price_output,
            true,
            true,
            null,
            true,
            $id_customization
        );

        $id_order_invoice = (int) Db::getInstance()->getValue('
            SELECT id_order_invoice 
            FROM ' . _DB_PREFIX_ . 'order_invoice 
            WHERE id_order = ' . $id_order
        );

        Db::getInstance()->insert('order_detail', [
            'id_order' => $id_order,
            'id_order_invoice' => $id_order_invoice,
            'id_shop' => $order->id_shop,
            'product_id' => $id_product,
            'product_attribute_id' => $id_attribute,
            'product_name' => $product->name,
            'product_quantity' => $quantity,
            'product_price' => $product_price,
            'product_weight' => $input->weight,
            'product_reference' => $product->reference,
            'product_supplier_reference' => $product->supplier_reference,
            'id_customization' => $id_customization,

            'unit_price_tax_incl' => $price_tax_incl,
            'unit_price_tax_excl' => $product_price,

            'total_price_tax_incl' => $price_tax_incl * $quantity,
            'total_price_tax_excl' => $product_price * $quantity,
        ]);

        $products = $order->getProductsDetail();
        $totalProductsWithTaxes = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price_tax_incl'];
        }, 0);
        $totalProductsWithoutTaxes = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price_tax_excl'];
        }, 0);

        Db::getInstance()->update('orders', [
            'total_products_wt' => $totalProductsWithoutTaxes,
            'total_products' => $totalProductsWithTaxes,
            'total_paid_tax_incl' => $totalProductsWithTaxes,
            'total_paid_tax_excl' => $totalProductsWithoutTaxes,
        ], 'id_order = ' . $id_order);

        Db::getInstance()->update('order_invoice', [
            'total_products_wt' => $totalProductsWithoutTaxes,
            'total_products' => $totalProductsWithTaxes,
            'total_paid_tax_incl' => $totalProductsWithTaxes,
            'total_paid_tax_excl' => $totalProductsWithoutTaxes,
        ], 'id_order = ' . $id_order);

        $this->respond([
            'id_customization' => $id_customization,
        ]);
    }

    private function processSaveInput()
    {
        $name = Tools::getValue('name');
        $value = Tools::getValue('value');

        $product_config = new DynamicConfig($this->id_product);
        $product_config->saveValue($name, $value);

        if ($name === 'active' || $name === 'required') {
            $this->module->handler->addCustomField($this->id_product);
        }

        if ($name === 'active' && (int) $value === 0) {
            $this->module->handler->setCustomFieldRequired($this->id_product, false);
        }

        $this->respond();
    }

    private function processCopyProductConfig()
    {
        $options = Tools::getValue('options', []);
        $clear = (int) ($options['clear'] ?? 1);
        $link = (int) ($options['link'] ?? 0);

        $id_target_product = (int) Tools::getValue('id_target_product');
        $id_source_product = (int) Tools::getValue('id_source_product');

        if ($link) {
            if ($clear) {
                $this->module->handler->clearConfig($id_target_product);
            }
            DynamicProductConfigLink::createLink($id_target_product, $id_source_product);
        } else {
            DynamicProductConfigLink::removeLink($id_target_product);
            $this->module->handler->copyConfig($id_target_product, $id_source_product, $options, $clear);
        }
        $this->respond();
    }

    private function processLinkCategoryConfig()
    {
        $source = basename(__FILE__, '.php');
        if (DynamicTools::isDemoMode() && !DynamicTools::isSuperAdmin()) {
            $this->respond([
                'error' => true,
                'message' => 'This function is not available in the demo mode!',
            ]);
        }
        $id_category = (int) Tools::getValue('id_category');
        $id_product = (int) Tools::getValue('id_product');

        // allow linking to multiple categories
        // DynamicProductConfigLink::removeLinks($id_product);

        $existing_link = DynamicProductConfigCategoryLink::getLinkByCategory($id_category);
        if (is_array($existing_link) && count($existing_link) > 0) {
            $this->respond([
                'error' => true,
                'message' => sprintf($this->module->l('This category is already linked to product #%d!', $source), $existing_link['id_product']),
            ]);
        }

        DynamicProductConfigCategoryLink::createLink($id_product, $id_category);

        $this->respond();
    }

    private function processClearProductConfig()
    {
        if (DynamicTools::isDemoMode() && !DynamicTools::isSuperAdmin()) {
            $this->respond([
                'error' => true,
                'message' => 'This function is not available in the demo mode!',
            ]);
        }

        $id_target_product = (int) Tools::getValue('id_target_product');
        $this->module->handler->clearConfig($id_target_product);
        DynamicProductConfigLink::removeLink($id_target_product);
        $this->respond();
    }

    private function processGetCategoryProducts()
    {
        $id_category = (int) Tools::getValue('id_target_category');

        $category = new Category($id_category);
        $products = $category->getProducts(
            $this->context->language->id,
            0,
            100000000,
            null,
            null,
            false,
            false,
            false,
            1,
            false
        );
        $this->respond([
            'products' => array_map(function ($product) {
                return (int) $product['id_product'];
            }, $products),
        ]);
    }

    private function processExportConfig()
    {
        $link_images = (int) Tools::getValue('link_images');
        $data = $this->module->handler->exportConfig($this->id_product, $link_images);
        $this->respond([
            'data' => $data,
        ]);
    }

    private function processImportConfig()
    {
        $uploader = new Uploader();
        $uploader->setMaxSize(1024 * 1000 * 100);
        $uploader->setName('file');
        $uploader->setAcceptTypes(['json']);
        $file = $uploader->process();
        $upload = $file[0];

        if ($upload['error']) {
            $this->respond([
                'error' => true,
                'message' => $upload['error'],
            ]);
        }

        $save_path = $upload['save_path'];
        $contents = Tools::file_get_contents($save_path);
        $data = json_decode($contents, true);
        if (!$data) {
            $source = DynamicTools::getSource();
            $this->respond([
                'error' => true,
                'message' => $this->module->l('Could not import data, please check your file then try again', $source),
            ]);
        }

        try {
            $this->module->handler->importConfig($this->id_product, $data);
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                throw $e;
            }
            $this->respond([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }

        $this->respond();
    }

    private function processImportJsonData()
    {
        $json = Tools::getValue('json');
        $data = json_decode($json, true);
        if (!$data) {
            $source = DynamicTools::getSource();
            $this->respond([
                'error' => true,
                'message' => $this->module->l('Could not import data, please check your file then try again', $source),
            ]);
        }

        $this->module->handler->importConfig($this->id_product, $data);

        $this->respond();
    }

    private function processUnlinkConfig()
    {
        DynamicProductConfigLink::removeLink($this->id_product);
        $this->respond();
    }

    private function processUnlinkConfigs()
    {
        DynamicProductConfigLink::removeLinks($this->id_product);
        DynamicProductConfigCategoryLink::removeLink($this->id_product);
        $this->respond();
    }

    public function respond($data = [], $success = 1)
    {
        $success = $success && (int) !array_key_exists('error', $data);
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
