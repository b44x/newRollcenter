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
namespace DynamicProduct\classes\models;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Cache;
use Cart;
use Customization;
use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\LegacyInputFields;
use Product;

class DynamicInput extends DynamicObject
{
    public $id_product;
    public $id_attribute;
    public $id_cart;
    public $id_customer;
    public $id_guest;
    public $id_customization;
    public $price;
    public $weight;
    public $cart_quantity;
    public $dynamic_quantity;
    public $hash;
    public $true_conditions;
    public $name;
    public $is_bookmarked;
    public $is_admin;
    public $is_editable = 1;
    public $date_add;
    public $date_upd;

    /** @var DynamicInputField[] */
    public $input_fields;

    public $true_conditions_array = [];

    protected $webserviceParameters = [
        'objectNodeName' => 'dynamic_input',
        'objectsNodeName' => 'dynamic_inputs',
        'fields' => [
            'id_product' => ['xlink_resource' => 'products'],
            'id_attribute' => ['xlink_resource' => 'combinations'],
            'id_cart' => ['xlink_resource' => 'carts'],
            'id_customer' => ['xlink_resource' => 'customers'],
            'id_guest' => ['xlink_resource' => 'guests'],
            'id_customization' => ['xlink_resource' => 'customizations'],
            'price' => [],
            'weight' => [],
            'dynamic_quantity' => [],
            'hash' => [],
            'true_conditions' => [],
            'name' => [],
            'is_bookmarked' => [],
            'is_admin' => [],
            'is_editable' => [],
            'date_add' => [],
            'date_upd' => [],
        ],
        'associations' => [
            'dynamic_input_fields' => [
                'resource' => 'dynamic_input_field',
                'fields' => [
                    'id' => ['required' => true],
                    'id_field' => [],
                    'name' => [],
                    'label' => [],
                    'value' => [],
                    'secondary_value' => [],
                    'display_value' => [],
                    'sku' => [],
                    'displayed' => [],
                ],
            ],
        ],
    ];

    public static $definition = [
        'table' => 'dynamicproduct_input',
        'primary' => 'id_input',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_guest' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_customization' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'weight' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'cart_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'dynamic_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'hash' => ['type' => self::TYPE_STRING],
            'true_conditions' => ['type' => self::TYPE_STRING],
            'name' => ['type' => self::TYPE_STRING],
            'is_bookmarked' => ['type' => self::TYPE_BOOL],
            'is_admin' => ['type' => self::TYPE_BOOL],
            'is_editable' => ['type' => self::TYPE_BOOL],
            'date_add' => ['type' => self::TYPE_DATE],
            'date_upd' => ['type' => self::TYPE_DATE],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->true_conditions_array = json_decode($this->true_conditions ?: '[]', true);
    }

    public static function getInputsDynamicQuantity($id_product, $id_product_attribute)
    {
        $dynamic_inputs = self::getInputsByProduct($id_product, $id_product_attribute);
        $input_quantity = 0;
        foreach ($dynamic_inputs as $dynamic_input) {
            $dynamic_input->assignCartQuantity();
            $dynamic_quantity = $dynamic_input->dynamic_quantity;
            if ($dynamic_quantity) {
                $input_quantity += $dynamic_quantity * $dynamic_input->cart_quantity;
            }
        }

        return (int) $input_quantity;
    }

    /**
     * @param \Cart $cart
     */
    public static function updateCartQuantities($cart, $add = false)
    {
        $id_cart = (int) $cart->id;
        $dynamic_inputs = self::getInputsByIdCart($id_cart);
        foreach ($dynamic_inputs as $dynamic_input) {
            $dynamic_input->assignCartQuantity();
            $dynamic_quantity = $dynamic_input->dynamic_quantity * $dynamic_input->cart_quantity;
            if ($dynamic_quantity) {
                $customization_quantity = self::getProductQuantityInCart(
                    $cart,
                    $dynamic_input->id_product,
                    $dynamic_input->id_attribute,
                    $dynamic_input->id_customization
                );
                $cart_quantity = $dynamic_quantity - $customization_quantity;
                if ($cart_quantity && !\StockAvailable::dependsOnStock($dynamic_input->id_product)) {
                    \StockAvailable::updateQuantity(
                        $dynamic_input->id_product,
                        $dynamic_input->id_attribute,
                        $cart_quantity * ($add ? 1 : -1)
                    );
                }
            }
        }
    }

    /**
     * @param $id_cart
     *
     * @return array|bool
     */
    private static function getInputRowsByIdCart($id_cart)
    {
        if (!$id_cart) {
            return [];
        }
        $module = DynamicTools::getModule();
        $sql = '
            SELECT di.id_input FROM `' . _DB_PREFIX_ . 'customized_data` cd
            INNER JOIN `' . _DB_PREFIX_ . 'customization` cc
            ON cd.`id_customization` = cc.`id_customization`
            INNER JOIN `' . _DB_PREFIX_ . 'dynamicproduct_input` di
            ON CONCAT("|", di.`id_input`, "|") = cd.`value` OR di.`id_input` = cd.`value`
            INNER JOIN `' . _DB_PREFIX_ . 'cart_product` cp
            ON cp.id_cart = cc.id_cart AND cp.id_product_attribute = cc.id_product_attribute
            WHERE cc.`id_cart` = ' . (int) $id_cart . ' AND cc.`in_cart` = 1 AND cd.id_module = ' . (int) $module->id;

        return \Db::getInstance()->executeS($sql);
    }

    public function assignCartQuantity()
    {
        $this->cart_quantity = self::getCustomizationQuantity($this->id);
    }

    public function getInputFields($id_lang = null)
    {
        // cache fields first
        DynamicField::getFieldRowsByProduct($this->id_product, $id_lang);
        $this->input_fields = DynamicInputField::getByIdInput($this->id, $id_lang);
        if (empty($this->input_fields)) {
            $legacy_input_fields = new LegacyInputFields($this->module, $this->context);
            $this->input_fields = $legacy_input_fields->getInputFields($this->id_product, $this->id, $id_lang);
        }

        return $this->input_fields;
    }

    public function getWsDynamicInputFields()
    {
        $rows = [];
        $input_fields = DynamicInputField::getByIdInput($this->id, $this->context->language->id);
        foreach ($input_fields as $input_field) {
            $displayed = !$input_field->isSkipped() && !$input_field->isSkippedName();
            if (!$displayed && \Tools::getValue('display') !== 'full') {
                continue;
            }
            $values = $input_field->getObjectValues();
            $values['label'] = $input_field->getLabel();
            $values['display_value'] = $input_field->getDynamicValue($input_fields);
            $values['displayed'] = $displayed;
            $rows[] = $values;
        }

        return $rows;
    }

    /**
     * @param $id_cart
     *
     * @return DynamicInput[]
     */
    public static function getInputsByIdCart($id_cart)
    {
        if (!$id_cart) {
            return [];
        }

        $cache_key = "DynamicInput::getInputsByIdCart($id_cart)";
        if (DynamicTools::canUseCache() && \Cache::isStored($cache_key)) {
            return \Cache::retrieve($cache_key);
        }
        $dynamic_inputs = [];
        $rows = self::getInputRowsByIdCart($id_cart);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $id_input = $row['id_input'];
                $dynamic_input = new self((int) $id_input);
                if (\Validate::isLoadedObject($dynamic_input)) {
                    $dynamic_inputs[$id_input] = $dynamic_input;
                }
            }
        }
        \Cache::store($cache_key, $dynamic_inputs);

        return $dynamic_inputs;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     *
     * @return DynamicInput[]
     */
    public static function getInputsByProduct($id_product, $id_attribute = false)
    {
        $cache_key = "DynamicInput::getInputsByProduct($id_product, $id_attribute)";
        if (DynamicTools::canUseCache() && \Cache::isStored($cache_key)) {
            return \Cache::retrieve($cache_key);
        }
        $module = DynamicTools::getModule();
        $id_cart = $module->provider->getCart();
        $dynamic_inputs = [];
        $sql = '
            SELECT di.id_input FROM `' . _DB_PREFIX_ . 'customized_data` cd
            INNER JOIN `' . _DB_PREFIX_ . 'customization` cc
            ON cd.`id_customization` = cc.`id_customization`
            INNER JOIN `' . _DB_PREFIX_ . 'dynamicproduct_input` di
            ON CONCAT("|", di.`id_input`, "|") = cd.`value` OR di.`id_input` = cd.`value`
            WHERE di.`id_product` = ' . (int) $id_product .
            ($id_attribute !== false ? ' AND di.`id_attribute` = ' . (int) $id_attribute : '')
            . ' AND cc.`in_cart` = 1 AND cc.`id_cart` = ' . (int) $id_cart;
        $rows = \Db::getInstance()->executeS($sql);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $id_input = $row['id_input'];
                $dynamic_input = new DynamicInput((int) $id_input);
                if (\Validate::isLoadedObject($dynamic_input)) {
                    $dynamic_inputs[$id_input] = $dynamic_input;
                }
            }
        }
        \Cache::store($cache_key, $dynamic_inputs);

        return $dynamic_inputs;
    }

    /**
     * @param $id_product
     * @param $id_attribute
     * @param $id_customization
     *
     * @return DynamicInput
     */
    public static function getInputByCustomization($id_customization)
    {
        $cache_key = "DynamicInput::getInputByCustomization($id_customization)";
        if (DynamicTools::canUseCache() && \Cache::isStored($cache_key)) {
            return \Cache::retrieve($cache_key);
        }

        $sql = 'SELECT id_input FROM `' . _DB_PREFIX_ . 'dynamicproduct_input`
            WHERE `id_customization` = ' . (int) $id_customization;

        $id_input = \Db::getInstance()->getValue($sql);
        $dynamic_input = new DynamicInput($id_input);

        \Cache::store($cache_key, $dynamic_input);

        return $dynamic_input;
    }

    public function deleteFromCart()
    {
        $id_customization = $this->id_customization;
        if ($id_customization) {
            $customization = self::getCustomizationInfos($this->id_customization);
            if (!$customization) {
                return;
            }
            \Context::getContext()->cart->deleteProduct(
                $customization['id_product'],
                $customization['id_product_attribute'],
                $id_customization,
                $customization['id_address_delivery']
            );
            $customizationObj = new \Customization($id_customization);
            if (\Validate::isLoadedObject($customizationObj)) {
                $customizationObj->delete();
            }
        }
    }

    public static function deleteCustomization($dp_input)
    {
        if ($dp_input) {
            $dp_dynamic_input = new self($dp_input);
            $dp_dynamic_input->deleteFromCart();
            $dp_dynamic_input->delete();
        }
    }

    public static function cartHasCustomization($id_cart)
    {
        return count(self::getInputsByIdCart($id_cart));
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function updatePrice($price)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customized_data` SET `price` = ' . (float) $price .
            ' WHERE `id_customization` = ' . (int) $this->id_customization .
            ' AND `id_module` = ' . (int) $this->module->id;
        $success = \Db::getInstance()->execute($sql);
        if ($success) {
            $this->price = $price;
            $this->save();
        }
    }

    public function updateWeight($weight)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customized_data` SET `weight` = ' . (float) $weight .
            ' WHERE `id_customization` = ' . (int) $this->id_customization .
            ' AND `id_module` = ' . (int) $this->module->id;
        $success = \Db::getInstance()->execute($sql);
        if ($success) {
            $this->weight = $weight;
            $this->save();
        }
    }

    /**
     * @return \OrderDetail
     */
    public function getOrderDetail()
    {
        $sql = 'SELECT id_order_detail
        FROM `' . _DB_PREFIX_ . 'order_detail`
        WHERE `id_customization` = ' . (int) $this->id_customization;
        $id_order_detail = (int) \Db::getInstance()->getValue($sql);

        return new \OrderDetail($id_order_detail);
    }

    private function getProductPriceWithReduction()
    {
        if ($this->canExcludeProductPrice()) {
            return 0;
        }

        return \Product::getPriceStatic(
            $this->id_product,
            !\Product::getTaxCalculationMethod(),
            $this->id_attribute,
            6,
            null,
            false,
            true,
            (int) $this->cart_quantity
        );
    }

    public function getTotalProductPriceWithReduction()
    {
        return $this->getProductPriceWithReduction() * $this->cart_quantity;
    }

    public function getTotalPrice()
    {
        return $this->price * $this->cart_quantity;
    }

    public static function getMaxSizes($product)
    {
        $inputs = self::getInputsByProduct($product->id);

        $maxSizesFromInputs = self::getMaxSizesFromInputs($inputs, $product);
        $maxSizesFromProduct = self::getMaxSizesFromProduct($product);

        return self::getMaxSizesFromArray($maxSizesFromInputs, $maxSizesFromProduct);
    }

    private static function getMaxSizesFromArray(array $maxSizesFromInputs, array $maxSizesFromProduct)
    {
        $maxSizes = [];
        foreach ($maxSizesFromInputs as $key => $size) {
            $maxSizes[$key] = max($size, $maxSizesFromProduct[$key]);
        }

        return $maxSizes;
    }

    public static function getMaxSizesFromInputs($inputs, $product)
    {
        $suffixes = [
            '' => 1,
            '_mm' => 0.1,
            '_m' => 100,
        ];

        $sizes = [
            'width' => 0,
            'height' => 0,
            'depth' => 0,
        ];

        if (is_array($inputs)) {
            $id_inputs = [];
            foreach ($inputs as $input) {
                $id_inputs[] = (int) $input->id;
            }

            if (count($id_inputs)) {
                $inputs_list = implode(',', $id_inputs);
                $key = "getMaxSizesFromInputs($inputs_list)";
                if (\Cache::isStored($key)) {
                    $input_fields = \Cache::retrieve($key);
                } else {
                    $sql = 'SELECT `name`, `value` as value
                        FROM ' . _DB_PREFIX_ . 'dynamicproduct_input_field
                        WHERE `id_input` IN (' . $inputs_list . ')
                        AND `name` IN (
                            "width",
                            "width_m",
                            "width_mm",
                            "height",
                            "height_m",
                            "height_mm",
                            "depth",
                            "depth_m",
                            "depth_mm"
                        )';
                    $input_fields = \Db::getInstance()->executeS($sql);
                    \Cache::store($key, $input_fields);
                }

                foreach ($input_fields as $input_field) {
                    foreach ($suffixes as $suffix => $coefficient) {
                        $name = $input_field['name'];
                        $suffix_pattern = "/$suffix$/";
                        if (preg_match($suffix_pattern, $name)) {
                            $name_without_suffix = preg_replace($suffix_pattern, '', $name);
                            if (isset($sizes[$name_without_suffix])) {
                                $value = (float) $input_field['value'] * $coefficient;
                                if (!$value && property_exists($product, $name_without_suffix)) {
                                    $value = (float) $product->{$name_without_suffix};
                                }
                                if ($value > $sizes[$name_without_suffix]) {
                                    $sizes[$name_without_suffix] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        $convert = [
            'm' => 0.01,
            'cm' => 1,
            'mm' => 10,
            'in' => 0.393701,
        ];

        $dimension_unit = \Configuration::get('PS_DIMENSION_UNIT');

        foreach ($sizes as $key => $size) {
            if (isset($convert[$dimension_unit])) {
                $sizes[$key] = $size * $convert[$dimension_unit];
            }
        }

        return $sizes;
    }

    private static function getMaxSizesFromProduct($product)
    {
        $module = DynamicTools::getModule();

        $sizes = [
            'width' => (float) $product->width,
            'height' => (float) $product->height,
            'depth' => (float) $product->depth,
        ];

        $id_cart = $module->provider->getCart();

        // check if product exists in cart wihtout id_customization
        $sql = 'SELECT `quantity`
        FROM `' . _DB_PREFIX_ . 'cart_product`
        WHERE `id_cart` = ' . (int) $id_cart .
            ' AND `id_product` = ' . (int) $product->id .
            ' AND `id_customization` = 0';
        if ((int) \Db::getInstance()->getValue($sql)) {
            return $sizes;
        }

        return [
            'width' => 0,
            'height' => 0,
            'depth' => 0,
        ];
    }

    public static function getCustomizationInfos($id_customization)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('customization');
        $sql->where('id_customization = ' . (int) $id_customization);

        return \Db::getInstance()->getRow($sql);
    }

    public static function getCustomizationQuantity($id_input)
    {
        $sql = new \DbQuery();
        $sql->select('c.quantity');
        $sql->from('customization', 'c');
        $sql->leftJoin('customized_data', 'cd', 'c.id_customization = cd.id_customization');
        $sql->where('cd.value = "|' . (int) $id_input . '|" OR cd.value = ' . (int) $id_input);

        return (int) \Db::getInstance()->getValue($sql);
    }

    /**
     * @param \Cart $cart
     * @param $id_product
     * @param $id_attribute
     * @param $id_customization
     */
    public static function getProductQuantityInCart($cart, $id_product, $id_attribute, $id_customization)
    {
        $quantity = $cart->containsProduct($id_product, $id_attribute, $id_customization);

        return isset($quantity['quantity']) ? (int) $quantity['quantity'] : 0;
    }

    public function duplicateInput($id_cart_new)
    {
        $has_errors = false;

        $input_fields = $this->getInputFields();

        $this->id_cart = (int) $id_cart_new;
        $this->id_guest = (int) $this->module->provider->getGuest();
        $this->id_customer = (int) $this->module->provider->getCustomer();
        $this->add();

        foreach ($input_fields as $input_field) {
            $id_field = $input_field->id_field;
            $is_field = (bool) (int) $id_field;

            $field_available = true;
            $newly_disabled = false;

            if ($is_field) {
                $dynamic_field = new DynamicField($id_field);
                $field_available = \Validate::isLoadedObject($dynamic_field);
                $newly_disabled = $input_field->visible && !$dynamic_field->active;
            }

            if (!$is_field || ($field_available && !$newly_disabled)) {
                $input_field->id_input = $this->id;
                $input_field->add();
            } else {
                $has_errors = true;
            }
        }

        return [(int) $this->id, $has_errors];
    }

    public function checkAuth()
    {
        if ($this->is_admin) {
            return true;
        }
        if (!(int) $this->id_customer && !(int) $this->id_guest) {
            return true;
        }
        if (DynamicTools::isModuleDevMode() || $this->module->provider->isAdmin()) {
            return true;
        }
        $id_guest = $this->module->provider->getGuest();
        $id_customer = $this->module->provider->getCustomer();
        $matches_guest = $id_guest && ($id_guest === (int) $this->id_guest);
        $matches_customer = $id_customer && ($id_customer === (int) $this->id_customer);

        return $matches_guest || $matches_customer;
    }

    public function canDisplayWeight()
    {
        return DynamicConfig::getDisplayWeight($this->id_product) && (float) $this->weight;
    }

    public function getWeight()
    {
        return $this->weight + $this->module->provider->getProductWeight($this->id_product, $this->id_attribute);
    }

    public function canExcludeProductPrice()
    {
        return DynamicConfig::isExcluded($this->id_product);
    }

    /** @noinspection PhpUnused */
    public function getEditLink($is_admin = false)
    {
        $product_url = $this->module->provider->getProductLink($this->id_product, $this->id_attribute);
        $product_url = DynamicTools::removeAnchor($product_url);
        $params = [
            'edit_customization' => $this->id_customization,
        ];
        if (!$this->id_customization) {
            $params = [
                'edit_input' => $this->id,
            ];
        }
        if ($is_admin && $this->module->provider->isAdmin()) {
            $params['is_admin_edit'] = true;
        }

        return DynamicTools::addQueryToUrl($product_url, $params) . $this->hash;
    }

    public function getCSVLink()
    {
        return DynamicTools::addQueryToUrl(
            $this->context->link->getAdminLink('DynamicProductCSV'),
            [
                'action' => 'download_csv',
                'id_input' => $this->id,
            ]
        );
    }

    public function getCSVSummary()
    {
        $csv = 'Field,Value';
        $summary = $this->getSummaryArray();
        foreach ($summary as $input_field) {
            $csv .= "\n";
            $csv .= $input_field['label'] . ',' . $input_field['value'];
        }

        return $csv;
    }

    public function getSummaryArrayWithId()
    {
        $input_fields = $this->getInputFields($this->context->language->id);

        $summary = [];
        foreach ($input_fields as $input_field) {
            if (!$input_field->isSkipped() && !$input_field->isSkippedName()) {

//if ($input_field->id == 17236) { var_dump($input_field->value); exit; }
//var_dump($input_field);
//exit;
                $summary[] = [
                    'id' => $input_field->id,
                    'label' => $input_field->label,
                    'value' => $input_field->getDynamicValue($input_fields),
		    'originalValue' => $input_field->value,
		    'name' => $input_field->name,
                ];
            }
        }

        return $summary;
    }

    public function getSummaryArray()
    {
        $input_fields = $this->getInputFields($this->context->language->id);

        $summary = [];
        foreach ($input_fields as $input_field) {
            if (!$input_field->isSkipped() && !$input_field->isSkippedName()) {
                $summary[] = [
                    'label' => $input_field->label,
                    'value' => $input_field->getDynamicValue($input_fields),
                ];
            }
        }

        return $summary;
    }

    public function delete()
    {
        $input_fields = $this->getInputFields(\Context::getContext()->language->id);
        foreach ($input_fields as $input_field) {
            $input_field->delete();
        }

        return parent::delete();
    }
}
