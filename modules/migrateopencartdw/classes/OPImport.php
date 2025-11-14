<?php
/**
* 2007-2024 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'loggers/OPLogger.php';
require_once 'OPValidator.php';
class OPImport
{
    const UNFRIENDLY_ERROR = false;
    // --- Objects, Option & response vars:

    private $validator;
    protected $obj;
    protected $process;
    protected $client;
    protected $query;
    protected $url;
    protected $force_ids;
    protected $regenerate;
    protected $image_path = '/image/';
    protected $image_supplier_path;
    protected $version;
    protected $shop_is_feature_active;
    protected $mapping;
    protected $ps_validation_errors = true;
    protected $migrate_recent_data = false;

    protected $error_msg = [];
    protected $warning_msg;
    protected $response;
    protected $default_lang;
    protected $default_shop;

    // --- Constructor / destructor:

    public function __construct(
        DWProcessOP $process,
        $version,
        $url_cart,
        $force_ids,
        OPClient $client = null,
        OPQuery $query = null
    ) {
        $this->regenerate = false; // @TODO dynamic from step two
        $this->process = $process;
        $this->version = $version;
        $this->url = $url_cart;
        $this->force_ids = $force_ids;
        $this->client = $client;
        $this->query = $query;
        $this->mapping = DWMappingOP::listMapping(true, true);
        $this->shop_is_feature_active = Shop::isFeatureActive();
        $this->logger = new OPLogger();
        $this->validator = new OPValidator();
        $this->default_lang = Configuration::get('PS_LANG_DEFAULT');
        $this->default_shop = Configuration::get('PS_SHOP_DEFAULT');
    }

    // --- Configuration methods:

    public function setImagePath()
    {
        $this->image_path = '/image/';
    }

    public function setImageSupplierPath($string)
    {
        $this->image_supplier_path = $string;
    }

    public function setRecentData($bool)
    {
        $this->migrate_recent_data = $bool;
    }

    public function setPsValidationErrors($bool)
    {
        $this->ps_validation_errors = $bool;
    }

    public function preserveOn()
    {
        $this->force_ids = true;
    }

    public function preserveOff()
    {
        $this->force_ids = false;
    }

    // --- After object methods:

    public function getErrorMsg()
    {
        return $this->error_msg;
    }

    public function getWarningMsg()
    {
        return $this->warning_msg;
    }

    public function getResponse()
    {
        return $this->response;
    }

    // --- Import methods:

    /**
     * @param $manufacturers
     * @param $manufacturersAdditionalSecond
     */
    public function manufacturers($manufacturers)
    {
        foreach ($manufacturers as $manufacturer) {
            if ($manufacturerObj = $this->createObjectModel('Manufacturer', $manufacturer['manufacturer_id'])) {
                $manufacturerObj->name = Tools::substr(htmlspecialchars_decode($manufacturer['name']), 0, 64) ?: Tools::substr(html_entity_decode($manufacturer['name']), 0, 64);
                $manufacturerObj->date_add = date('Y-m-d H:i:s', time());
                $manufacturerObj->active = 1;

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($manufacturerObj);
                $this->validator->checkFields();
                $manufacturer_error_tmp = $this->validator->getValidationMessages();
                if ($manufacturerObj->id && $manufacturerObj->manufacturerExists($manufacturerObj->id)) {
                    try {
                        $res = $manufacturerObj->update();
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }

                if (!$res) {
                    try {
                        $res = $manufacturerObj->add(false);
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }
                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Manufacturer (ID: %1$s) cannot be saved. %2$s'), (isset($manufacturer['manufacturer_id']) && !empty($manufacturer['manufacturer_id'])) ? Tools::safeOutput($manufacturer['manufacturer_id']) : 'No ID', $err_tmp), 'Manufacturer');
                } else {
                    $url = $this->url . $this->image_path . $manufacturer['image'];
                    if (!(OPImport::copyImg($manufacturerObj->id, null, $url, 'manufacturers', $this->regenerate))) {
                        $this->showMigrationMessageAndLog($url . ' ' . Tools::displayError('cannot be copied.'), 'Manufacturer');
                    }
                    // @TODO Associate manufacturers to shop
                    self::addLog('Manufacturer', $manufacturer['manufacturer_id'], $manufacturerObj->id);
                }
                $this->showMigrationMessageAndLog($manufacturer_error_tmp, 'Manufacturer');
            }
        }
        $this->updateProcess(count($manufacturers));
    }

    /**
     * @param $categories
     * @param $categoriesAdditionalSecond
     * @param bool $innerMethodCall
     */
    public function categories($categories, $categoriesAdditionalSecond, $innerMethodCall = false)
    {
        foreach ($categories as $category) {
            if (empty($category['parent_id']) || $category['parent_id'] == 0 || $category['parent_id'] == $category['category_id']) {
                $category['parent_id'] = Configuration::get('PS_HOME_CATEGORY');
            }
            if ($categoryObj = $this->createObjectModel('Category', $category['category_id'])) {
                $categoryObj->active = 1;
                if (isset($category['parent_id']) && $category['parent_id'] != 2) {
                    if (!(int)self::getLocalId('category', (int)$category['parent_id'], 'data')) {
                        // -- if parent category not exist create it
                        if ($category['parent_id'] != Configuration::get('PS_HOME_CATEGORY')) {
                            $this->client->json_encodeOff();
                            $this->client->setPostData($this->query->singleCategory((int)$category['parent_id']));
                            if ($this->client->query()) {
                                $parentCategory = $this->client->getContent();
                                $this->client->setPostData($this->query->categorySqlSecond(AdminMigrateOpenCartController::getCleanIDs($parentCategory, 'category_id')));
                                if ($this->client->query()) {
                                    if (!empty($parentCategory)) {
                                        $parentCategoryLang = $this->client->getContent();
                                        $import = new OPImport($this->process, $this->version, $this->url, $this->force_ids, $this->client, $this->query);
                                        $import->setPsValidationErrors($this->ps_validation_errors);
                                        $import->setImagePath($this->image_path);

                                        $import->categories($parentCategory, $parentCategoryLang, true);

                                        $this->error_msg = $import->getErrorMsg();
                                        $this->warning_msg = $import->getWarningMsg();
                                        $this->response = $import->getResponse();
                                    }
                                }
                            }
                        } else {
                            $categoryObj->id_parent = Configuration::get('PS_HOME_CATEGORY');
                        }
                    } else {
                        $categoryObj->id_parent = (int)self::getLocalId('category', (int)$category['parent_id'], 'data');
                    }
                } else {
                    $categoryObj->id_parent = Configuration::get('PS_HOME_CATEGORY');
                }
//                $categoryObj->position = $category['position'];
                if ($category['status'] == 0) {
                    $categoryObj->active = 0;
                }
                $categoryObj->date_add = empty($category['date_added']) ? date('Y-m-d H:i:s', time()) : $category['date_added'];
                $categoryObj->date_upd = empty($category['date_modified']) ? date('Y-m-d H:i:s', time()) : $category['date_modified'];
                foreach ($categoriesAdditionalSecond as $lang) {
                    if ($lang['category_id'] == $category['category_id']) {
                        $lang['language_id'] = (int)self::getLanguageID($lang['language_id']);
                        $categoryObj->name[$lang['language_id']] = Tools::substr(html_entity_decode($lang['name']), 0, 128) ?: Tools::substr(htmlspecialchars_decode($lang['name']), 0, 128);
                        $categoryObj->link_rewrite[$lang['language_id']] = Tools::link_rewrite(Tools::substr(html_entity_decode($lang['name']), 0, 128) ?: Tools::substr(htmlspecialchars_decode($lang['name']), 0, 128));

                        if (isset($categoryObj->link_rewrite[$lang['language_id']]) && !empty($categoryObj->link_rewrite[$lang['language_id']])) {
                            $valid_link = Validate::isLinkRewrite($categoryObj->link_rewrite[$lang['language_id']]);
                        } else {
                            $valid_link = false;
                        }
                        if (!$valid_link) {
                            $categoryObj->link_rewrite[$lang['language_id']] = Tools::link_rewrite($categoryObj->name[$lang['language_id']]);

                            if ($categoryObj->link_rewrite[$lang['language_id']] == '') {
                                $categoryObj->link_rewrite[$lang['language_id']] = 'friendly-url-autogeneration-failed';
                                $this->showMigrationMessageAndLog(sprintf('URL rewriting failed to auto-generate a friendly URL for: %s', $categoryObj->name[$lang['language_id']]), 'Category');
                            }

                            $this->showMigrationMessageAndLog(sprintf('Rewrite link for %1$s (ID: %2$s) was re-written as %3$s.', $lang['link_rewrite'], (isset($category['category_id']) && !empty($category['category_id'])) ? $category['category_id'] : 'null', $categoryObj->link_rewrite[$lang['language_id']]), 'Category');
                        }
                        $categoryObj->description[$lang['language_id']] = Tools::htmlentitiesDecodeUTF8($lang['description']);
                        $categoryObj->meta_title[$lang['language_id']] = $lang['meta_title'];
                        $categoryObj->meta_description[$lang['language_id']] = $lang['meta_description'];
                        $categoryObj->meta_keywords[$lang['language_id']] = $lang['meta_keyword'];
                    }
                }

                // Add to _shop relations
//                $categoriesShopsRelations = $this->getChangedIdShop($categoriesAdditionalSecond['category_shop'], 'category_id');
//                if (array_key_exists($category['category_id'], $categoriesShopsRelations)) {
//                    $categoryObj->id_shop_list = array_values($categoriesShopsRelations[$category['category_id']]);
//                }

                //@TODO get shop id from step-2
//                if (!$this->shop_is_feature_active) {
//                    $categoryObj->id_shop_default = 1;
//                } else {
//                    $categoryObj->id_shop_default = $category['category_id'];
//                }

                $res = false;
                $err_tmp = '';
                $this->validator->setObject($categoryObj);
                $this->validator->checkFields();
                $category_error_tmp = $this->validator->getValidationMessages();
                if ($categoryObj->id && $categoryObj->id == $categoryObj->id_parent) {
                    $this->showMigrationMessageAndLog(Tools::displayError('A category cannot be its own parent'), 'Category');
                    continue;
                }

                if ($categoryObj->id == Configuration::get('PS_ROOT_CATEGORY')) {
                    $this->showMigrationMessageAndLog(Tools::displayError('The root category cannot be modified.'), 'Category');
                    continue;
                }

                /* No automatic nTree regeneration for import */
                $categoryObj->doNotRegenerateNTree = true;
                // If id category AND id category already in base, trying to update
                if ($categoryObj->id && $categoryObj->categoryExists($categoryObj->id)) {
                    try {
                        $res = $categoryObj->update();
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }

                // If no category_id or update failed
                if (!$res) {
                    try {
                        $res = $categoryObj->add(false);
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }
                // If both failed, mysql error
                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf('Category (ID: %1$s) cannot be saved. %2$s', (isset($category['category_id']) && !empty($category['category_id'])) ? Tools::safeOutput($category['category_id']) : 'No ID', $err_tmp), 'Category');
                } else {
//                    $url = $this->url . $this->image_path . $category['image'];
//                    if (!(OPImport::copyImg($categoryObj->id, null, $url, 'categories', $this->regenerate))) {
//                        $this->warning_msg[] = $url . ' ' . Tools::displayError('cannot be copied.');
//                    }
                    self::addLog('Category', $category['category_id'], $categoryObj->id);
                }
                $this->showMigrationMessageAndLog($category_error_tmp, 'Category');
            }
        }
        if (!$innerMethodCall) {
            $this->updateProcess(count($categories));
        }
        Category::regenerateEntireNtree();
    }

    /**
     * @param $products
     * @param $product2AdditionalSecond
     * @param $productAdditionalThird
     * @param $productAdditionalThird
     */

    public function products($products, $productAdditionalSecond, $productAdditionalThird, $innerMethodCall = false)
    {
        Module::setBatchMode(true);
        $mapping = DWMappingOP::listMapping(true, true);
        // import attribute group
        foreach ($productAdditionalThird['attribute_group'] as $attributeGroup) {
            if ($attributeGroupObj = $this->createObjectModel('AttributeGroup', $attributeGroup['option_id'])) {
                $attributeGroupObj->is_color_group = ($attributeGroup['type'] == 'image') ? $attributeGroup['is_color_group'] : 0;
                $attributeGroupObj->group_type = ($attributeGroup['type'] == 'image') ? 'color' : 'select';
//                $attributeGroupObj->is_color_group = 0;
//                $attributeGroupObj->group_type = 'select';
                foreach ($productAdditionalThird['attribute_group'] as $lang) {
                    if ($attributeGroup['option_id'] == $lang['option_id']) {
                        $lang['language_id'] = (int)self::getLanguageID($lang['language_id']);
                        $attributeGroupObj->name[$lang['language_id']] = Tools::substr(html_entity_decode($lang['name']), 0, 128) ?: Tools::substr(htmlspecialchars_decode($lang['name']), 0, 128);
                        $attributeGroupObj->public_name[$lang['language_id']] = Tools::substr(html_entity_decode($lang['name']), 0, 64) ?: Tools::substr(htmlspecialchars_decode($lang['name']), 0, 64);
                    }
                }

                $res = false;
                $err_tmp = '';
                $this->validator->setObject($attributeGroupObj);
                $this->validator->checkFields();
                $attribute_group_error_tmp = $this->validator->getValidationMessages();
                if ($attributeGroupObj->id && AttributeGroup::existsInDatabase($attributeGroupObj->id, 'attribute_group')) {
                    try {
                        $res = $attributeGroupObj->update();
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }

                if (!$res) {
                    try {
                        $res = $attributeGroupObj->add(false);
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }

                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf(Tools::displayError('AttributeGroup (ID: %1$s) cannot be saved. %2$s'), (isset($attributeGroup['option_id']) && !empty($attributeGroup['option_id'])) ? Tools::safeOutput($attributeGroup['option_id']) : 'No ID', $err_tmp), 'AttributeGroup');
                } else {
                    self::addLog('AttributeGroup', $attributeGroup['option_id'], $attributeGroupObj->id);
                }
                $this->showMigrationMessageAndLog($attribute_group_error_tmp, 'AttributeGroup');
            }
        }
        // import attribute
//         foreach ($productAdditionalThird['attribute'] as $attribute) {
//             if ($attributeObj = $this->createObjectModel('Attribute', $attribute['option_value_id'])) {
//                 $attributeObj->id_attribute_group = (int)self::getLocalID('attributegroup', (int)$attribute['option_id'], 'data');
// //                $attributeObj->color = $attribute['color'];
//                 foreach ($productAdditionalThird['attribute'] as $lang) {
//                     if ($attribute['option_value_id'] == $lang['option_value_id']) {
//                         $lang['language_id'] = (int)self::getLanguageID($lang['language_id']);
//                         $attributeObj->name[$lang['language_id']] = Tools::substr(html_entity_decode($lang['name']), 0, 128);
//                     }
//                 }

//                 $res = false;
//                 $err_tmp = '';
//                 $this->validator->setObject($attributeObj);
//                 $this->validator->checkFields();
//                 $attribute_error_tmp = $this->validator->getValidationMessages();
//                 if ($attributeObj->id && Attribute::existsInDatabase($attributeObj->id, 'attribute')) {
//                     try {
//                         $res = $attributeObj->update();
//                     } catch (PrestaShopException $e) {
//                         $err_tmp = $e->getMessage();
//                     }
//                 }

//                 if (!$res) {
//                     try {
//                         $res = $attributeObj->add(false);
//                     } catch (PrestaShopException $e) {
//                         $err_tmp = $e->getMessage();
//                     }
//                 }

//                 if (!$res) {
//                     $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Attribute (ID: %1$s) cannot be saved. %2$s'), (isset($attribute['option_value_id']) && !empty($attribute['option_value_id'])) ? Tools::safeOutput($attribute['option_value_id']) : 'No ID', $err_tmp), 'Attribute');
//                 } else {
//                     self::addLog('Attribute', $attribute['option_value_id'], $attributeObj->id);
//                     DWMigratedDataOP::import('Attribute', $attribute['option_value_id'], $attributeObj->id);
//                     $url = $this->url . $this->image_path . str_replace(' ', '%20', $attribute['image']);
//                     if (self::imageExits($url)) {
//                         self::copyImg($attributeObj->id, null, $url, 'attributes');
//                     }
//                 }
//                 $this->showMigrationMessageAndLog($attribute_error_tmp, 'Attribute');
//             }
//         }

//         import Products
        foreach ($products as $product) {
//            ddd($product);
            if ($productObj = $this->createObjectModel('Product', $product['product_id'])) {
                $productObj->id_manufacturer = (int)self::getLocalId('manufacturer', $product['manufacturer_id'], 'data');
                $productObj->reference = Tools::substr(htmlspecialchars_decode($product['model']), 0, 32) ?: Tools::substr(html_entity_decode($product['model']), 0, 32);
                $productObj->weight = $product['weight'];
                $productObj->id_category_default = (int)self::getLocalID('category', $this->getDefaultCategory($productAdditionalSecond['category_product'], $product['product_id']), 'data');
                if (empty($product['tax_class_id']) || $product['tax_class_id'] == 0) {
                    $productObj->id_tax_rules_group = 0;
                } elseif (empty($product['tax_class_id']) && !in_array($product['tax_class_id'], $mapping['tax_class'])) {
                    $productObj->id_tax_rules_group = $mapping['tax_class'][$product['tax_class_id']];
                }
                $productObj->minimal_quantity = $product['minimum'];
                $productObj->price = Tools::ps_round($product['price'], 6);
                $productObj->wholesale_price = $productObj->price;
                if (empty($productObj->price)) {
                    $productObj->price = 0;
                    $productObj->wholesale_price = 0;
                }
                $productObj->active = $product['status'];
                $productObj->available_for_order = $product['status'];
                $productObj->condition = 'new';
                $productObj->show_price = 1;
                $productObj->indexed = 0; // always zero for new PS $product['indexed'];
//                $productObj->cache_default_attribute = $product['cache_default_attribute'];
                $productObj->date_add = $product['date_added'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $product['date_added'];
                $productObj->date_upd = $product['date_modified'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $product['date_modified'];
                $productObj->id_shop_default = $this->default_shop;
//                $productObj->id_color_default = $product['id_color_default']; // @deprecated 1.5.0
                $productObj->quantity = $product['quantity'];


                //add tags
                foreach ($productAdditionalSecond['tags'] as $tag) {
                    if (empty($tag['tag'])) {
                        continue;
                    }
                    if ($tag['product_id'] == $product['product_id']) {
                        $tag['language_id'] = (int)self::getLanguageID($tag['language_id']);
                        Tag::addTags($tag['language_id'], $tag['product_id'], $tag['tag'], ',');
                    }
                }
                // import feature
//                foreach ($productAdditionalSecond['feature_product'] as $feature) {
//                    if ($featureObj = $this->createObjectModel('Feature', $feature['id_feature'])) {
//                        if (isset($feature['position']) && !self::isEmpty($feature['position'])) {
//                            $featureObj->position = (int)$feature['position'];
//                        } else {
//                            $featureObj->position = Feature::getHigherPosition() + 1;
//                        }
//
//                        foreach ($productAdditionalThird['feature_lang'] as $lang) {
//                            if ($lang['id_feature'] == $feature['id_feature']) {
//                                $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
//                                $featureObj->name[$lang['id_lang']] = $lang['name'];
//                                if (self::isEmpty($featureObj->name[$lang['id_lang']])) {
//                                    $featureObj->name[$lang['id_lang']] = 'empty';
//                                }
//                            }
//                        }
//
//                        // Add to _shop relations
//                        $featuresShopsRelations = $this->getChangedIdShop($productAdditionalThird['feature_shop'], 'id_feature');
//                        if (array_key_exists($feature['id_feature'], $featuresShopsRelations)) {
//                            $featureObj->id_shop_list = array_values($featuresShopsRelations[$feature['id_feature']]);
//                        }
//
//                        $res = false;
//                        $err_tmp = '';
//                        if (($field_error = $featureObj->validateFields(self::UNFRIENDLY_ERROR, true)) === true && ($lang_field_error = $featureObj->validateFieldsLang(self::UNFRIENDLY_ERROR, true)) === true) {
//                            if ($featureObj->id && Feature::existsInDatabase($featureObj->id, 'feature')) {
//                                try {
//                                    $res = $featureObj->update();
//                                } catch (PrestaShopException $e) {
//                                    $err_tmp = $e->getMessage();
//                                }
//                            }
//
//                            if (!$res) {
//                                try {
//                                    $res = $featureObj->add(false);
//                                } catch (PrestaShopException $e) {
//                                    $err_tmp = $e->getMessage();
//                                }
//                            }
//
//                            if (!$res) {
//                                $this->showMigrationMessageAndLog(sprintf(Tools::displayError($this->module->l('Feature (ID: %1$s) cannot be saved. %2$s')), (isset($feature['id_feature']) && !self::isEmpty($feature['id_feature'])) ? Tools::safeOutput($feature['id_feature']) : 'No ID', $err_tmp));
//                            } else {
//                                self::addLog('Feature', $feature['id_feature'], $featureObj->id);
//                            }
//                        } else {
//                            $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError();
//                            if ($error_tmp != '') {
//                                $this->showMigrationMessageAndLog(sprintf(Tools::displayError($this->module->l('Feature (ID: %1$s) cannot be saved. %2$s')), (isset($feature['id_feature']) && !self::isEmpty($feature['id_feature'])) ? Tools::safeOutput($feature['id_feature']) : 'No ID', $error_tmp));
//                            }
//                        }
//                    }
//                }

                // import feature value
                foreach ($productAdditionalSecond['sku'] as $featureValue) {

                    if ($featureValueObj = $this->createObjectModel('FeatureValue', $featureValue['product_id'])) {
                        $featureValueObj->id_feature = 8;
//                        $featureValueObj->custom = $featureValue['custom'];
//                        ddd($featureValue);

                        foreach ($productAdditionalSecond['product_lang'] as $lang) {

                            if ($lang['product_id'] == $featureValue['product_id']) {
                                $lang['language_id'] = self::getLanguageID($lang['language_id']);
                                $featureValueObj->value[$lang['language_id']] = (!empty($featureValue['sku']) ? $featureValue['sku'] : ' ');
                            }
                        }

                        $res = false;
                        $err_tmp = '';
                        if (($field_error = $featureValueObj->validateFields(self::UNFRIENDLY_ERROR, true)) === true    && ($lang_field_error = $featureValueObj->validateFieldsLang(self::UNFRIENDLY_ERROR, true)) === true) {
                            if ($featureValueObj->id && FeatureValue::existsInDatabase($featureValueObj->id, 'feature_value')) {
                                try {
                                    $res = $featureValueObj->update();
                                } catch (PrestaShopException $e) {
                                    $err_tmp = $e->getMessage();
                                }
                            }

                            if (!$res) {
                                try {
                                    $res = $featureValueObj->add(false);
                                } catch (PrestaShopException $e) {
                                    $err_tmp = $e->getMessage();
                                }
                            }

                            if (!$res) {
                                $this->showMigrationMessageAndLog(sprintf(Tools::displayError($this->module->l('FeatureValue (ID: %1$s) cannot be saved. %2$s')), (isset($featureValue['product_id']) && !empty($featureValue['product_id'])) ? Tools::safeOutput($featureValue['product_id']) : 'No ID', $err_tmp));
                            } else {
                                self::addLog('FeatureValue', $featureValue['product_id'], $featureValueObj->id);
                            }
                        } else {
//                            $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError();
//                            if ($error_tmp != '') {
//                                $this->showMigrationMessageAndLog(sprintf(Tools::displayError($this->module->l('FeatureValue (ID: %1$s) cannot be saved. %2$s')), (isset($featureValue['product_id']) && !empty($featureValue['product_id'])) ? Tools::safeOutput($featureValue['product_id']) : 'No ID', $error_tmp));
//                            }
                        }
                    }
                }

                foreach ($productAdditionalSecond['sku'] as $featureProduct) {
                    if ($featureProduct['product_id'] == $product['product_id']) {
                        Product::addFeatureProductImport($productObj->id, 8, $productObj->id);
                    }
                }
                //language fields
                foreach ($productAdditionalSecond['product_lang'] as $lang) {
                    if ($lang['product_id'] == $product['product_id']) {
                        $str = Tools::substr(htmlspecialchars_decode($lang['name']), 0, 128) ?: Tools::substr(html_entity_decode($lang['name']), 0, 128);
//                        $stip = $lang['name'];
                        $lang['language_id'] = (int)self::getLanguageID($lang['language_id']);
//                        $productObj->name[$lang['language_id']] = preg_replace('/[^A-Za-z0-9\-]/', ' ', $stip);
                        $productObj->name[$lang['language_id']] = $str;
                        $productObj->link_rewrite[$lang['language_id']] = Tools::link_rewrite($str);
                        if (isset($productObj->link_rewrite[$lang['language_id']]) && !empty($productObj->link_rewrite[$lang['language_id']])) {
                            $valid_link = Validate::isLinkRewrite($productObj->link_rewrite[$lang['language_id']]);
                        } else {
                            $valid_link = false;
                        }
                        if (!$valid_link) {
                            $productObj->link_rewrite[$lang['language_id']] = Tools::link_rewrite($productObj->name[$lang['language_id']]);
                            if ($productObj->link_rewrite[$lang['language_id']] == '') {
                                $productObj->link_rewrite[$lang['language_id']] = 'friendly-url-autogeneration-failed';
                                $this->showMigrationMessageAndLog(sprintf(Tools::displayError('URL rewriting failed to auto-generate a friendly URL for: %s'), $productObj->name[$lang['language_id']]), 'Product');
                            }
                            $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Rewrite link for %1$s (ID: %2$s) was re-written as %3$s.'), $lang['link_rewrite'], (isset($product['product_id']) && !empty($product['product_id'])) ? $product['product_id'] : 'null', $productObj->link_rewrite[$lang['language_id']]), 'Product');
                        }
                        $productObj->description[$lang['language_id']] = Tools::htmlentitiesDecodeUTF8($lang['description'], true);
                        $productObj->meta_title[$lang['language_id']] = $lang['meta_title'];
                        $productObj->meta_description[$lang['language_id']] = $lang['meta_description'];
                        $productObj->meta_keywords[$lang['language_id']] = $lang['meta_keyword'];
//                        $productObj->available_now[$lang['language_id']] = $lang['available_now'];
//                        $productObj->available_later[$lang['language_id']] = $lang['available_later'];
                    }
                }

//                //@TODO get shop id from step-2
//                if (!$this->shop_is_feature_active) {
//                    $productObj->id_shop_default = (int)$this->default_shop;
//                } else {
//                    $productObj->id_shop_default = (int)Context::getContext()->shop->id;
//                }

                $res = false;
                $err_tmp = '';
                $this->validator->setObject($productObj);
                $this->validator->checkFields();
                $product_error_tmp = $this->validator->getValidationMessages();
                if ($productObj->id && Product::existsInDatabase((int)$productObj->id, 'product')) {
                    try {
                        $res = $productObj->update();
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }

                if (!$res) {
                    try {
                        $res = $productObj->add(false);
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }
                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf('Product (ID: %1$s) cannot be saved. %2$s', (isset($product['product_id']) && !empty($product['product_id'])) ? Tools::safeOutput($product['product_id']) : 'No ID', $err_tmp), 'Product');
                } else {
                    // set quantity to StockAvailable
                    StockAvailable::setQuantity($productObj->id, 0, $product['quantity']);
                    StockAvailable::setProductOutOfStock($productObj->id, true);


                    //import Category_Product
                    $sql_values = array();
                    foreach ($productAdditionalSecond['category_product'] as $categoryProduct) {
                        if ($categoryProduct['product_id'] == $product['product_id']) {
                            if ((int)$categoryProduct['category_id'] == Configuration::get('PS_HOME_CATEGORY')) {
                                $sql_values[] = '(' . (int)$categoryProduct['category_id'] . ', ' . (int)$productObj->id . ')';
                            } else {
                                $sql_values[] = '(' . (int)self::getLocalID('category', (int)$categoryProduct['category_id'], 'data') . ', ' . (int)$productObj->id . ')';
                            }
                        }
                    }
                    if (!empty($sql_values)) {
                        // [For PrestaShop Team] - $sql_values variable have created with using int cast
                        $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'category_product` (`id_category`, `id_product`) VALUES ' . implode(',', $sql_values));

                        if (!$result) {
                            $this->showMigrationMessageAndLog(Tools::displayError('Can\'t add category_product. ' . Db::getInstance()->getMsgError()), 'Product');
                        }
                    }

                    //import Product Attribute
                    $default_on = true;
                    $attr_option = array();
                    $attr_combinations = array();
                    $attr_combinations2 = array();
                    $attr_combinationsEND = array();      // for product-> product_attribute table
                    $a = array();
                    $prod_attr = array(); // for product_attribute -> attribute table

                    foreach ($productAdditionalSecond['product_attribute'] as $productAttribute) {
                        if ($productAttribute['product_id'] == $product['product_id']) {
                            if (!in_array($productAttribute['option_id'], $attr_option)) {
                                $attr_option[] = $productAttribute['option_id'];
                            }
                        }
                    }


                    if (count($attr_option) > 1) {   // if need to generate combination for attribute
                        foreach ($productAdditionalSecond['product_attribute'] as $productAttribute) {
                            if ($productAttribute['product_id'] == $product['product_id']) {
                                $attr_combinations[] = $productAttribute;
                                $attr_combinations2[] = $productAttribute;
                            }
                        }
                        foreach ($attr_combinations as $productAttribute) {
                            foreach ($attr_combinations2 as $productAttribute2) {
                                if ($productAttribute['option_id'] != $productAttribute2['option_id'] && !in_array($productAttribute['option_value_id'] * $productAttribute2['option_value_id'], $a)) {
                                    $a[] = $productAttribute['option_value_id'] * $productAttribute2['option_value_id'];
                                    $id = Configuration::get('attribute_id');
                                    if ($combinationModel = new Combination($id)) {
                                        $id++;
                                        Configuration::updateValue('attribute_id', $id);
                                        $combinationModel->id_product = $productObj->id;
//                                    $combinationModel->unit_price_impact = $productAttribute['unit_price_impact'];
                                        $combinationModel->minimal_quantity = 1;
                                        $combinationModel->reference = Tools::substr(htmlspecialchars_decode($productObj->reference), 0, 32) ?: Tools::substr(html_entity_decode($productObj->reference), 0, 32);
                                        if (empty($productAttribute['price'])) {
                                            $combinationModel->price = 0;
                                        } else {
                                            if ($productAttribute['price_prefix'] == '+') {
                                                $combinationModel->price = $productAttribute['price'];
                                            } else {
                                                $combinationModel->price = -$productAttribute['price'];
                                            }
                                        }
//                                    $combinationModel->price = (isset($productAttribute['price'])) ? $productAttribute['price'] : null;
                                        if ($default_on) {
                                            $combinationModel->default_on = 1;
                                            $default_on = false;
                                        }

                                        $res = false;
                                        $err_tmp = '';
                                        $this->validator->setObject($combinationModel);
                                        $this->validator->checkFields();
                                        $combination_error_tmp = $this->validator->getValidationMessages();
                                        if ($combinationModel->id && Combination::existsInDatabase($combinationModel->id, 'product_attribute')) {
                                            try {
                                                $res = $combinationModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            try {
                                                $res = $combinationModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf('Product attribute (ID: %1$s) cannot be saved. %2$s', (isset($productAttribute['option_id']) && !empty($productAttribute['option_id'])) ? Tools::safeOutput($combinationModel->id) : 'No ID', $err_tmp), 'Combination');
                                        } else {
                                            StockAvailable::setQuantity($combinationModel->id_product, $combinationModel->id, $product['quantity']);
                                            self::addLog('Combination', $combinationModel->id, $combinationModel->id);

                                            //import product_attribute_combination
                                            $sql_values = array();

                                            foreach ($productAdditionalSecond['product_attribute'] as $productAttributeCombination) {
                                                if (!isset($productAttributeCombination['product_option_value_id'])) {
                                                    return null;
                                                }
                                                if ($productAttributeCombination['product_option_value_id'] == $productAttribute['product_option_value_id']) {
                                                    $sql_values[] = '(' . (int)self::getLocalID('attribute', (int)$productAttribute['option_value_id'], 'data') . ', ' . (int)$combinationModel->id . ')';
                                                    $sql_values[] = '(' . (int)self::getLocalID('attribute', (int)$productAttribute2['option_value_id'], 'data') . ', ' . (int)$combinationModel->id . ')';
                                                }
                                            }
                                            if (!empty($sql_values)) {
                                                // [For PrestaShop Team] - $sql_values variable have created with using int cast
                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'product_attribute_combination` (`id_attribute`, `id_product_attribute`) VALUES ' . implode(',', $sql_values));
                                                if (!$result) {
                                                    $this->showMigrationMessageAndLog('Can\'t add product_attribute_combination. ' . Db::getInstance()->getMsgError(), 'Combination');
                                                }
                                            }
                                        }
                                        $this->showMigrationMessageAndLog($combination_error_tmp, 'Combination');
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($productAdditionalSecond['product_attribute'] as $productAttribute) {
                            if ($productAttribute['product_id'] == $product['product_id']) {
                                $id = Configuration::get('attribute_id');
                                if ($combinationModel = new Combination($id)) {
                                    $id++;
                                    Configuration::updateValue('attribute_id', $id);
                                    $combinationModel->id_product = $productObj->id;
//                                    $combinationModel->unit_price_impact = $productAttribute['unit_price_impact'];
                                    $combinationModel->minimal_quantity = 1;
                                    $combinationModel->reference = Tools::substr(htmlspecialchars_decode($productObj->reference), 0, 32) ?: Tools::substr(html_entity_decode($productObj->reference), 0, 32);
                                    if (empty($productAttribute['price'])) {
                                        $combinationModel->price = 0;
                                    } else {
                                        if ($productAttribute['price_prefix'] == '+') {
                                            $combinationModel->price = $productAttribute['price'];
                                        } else {
                                            $combinationModel->price = -$productAttribute['price'];
                                        }
                                    }
//                                    $combinationModel->price = (isset($productAttribute['price'])) ? $productAttribute['price'] : null;
                                    if ($default_on) {
                                        $combinationModel->default_on = 1;
                                        $default_on = false;
                                    }

                                    $res = false;
                                    $err_tmp = '';
                                    $this->validator->setObject($combinationModel);
                                    $this->validator->checkFields();
                                    $combination_error_tmp = $this->validator->getValidationMessages();
                                    if ($combinationModel->id && Combination::existsInDatabase($combinationModel->id, 'product_attribute')) {
                                        try {
                                            $res = $combinationModel->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        try {
                                            $res = $combinationModel->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Product attribute (ID: %1$s) cannot be saved. %2$s'), (isset($productAttribute['option_id']) && !empty($productAttribute['option_id'])) ? Tools::safeOutput($combinationModel->id) : 'No ID', $err_tmp), 'Combination');
                                    } else {
                                        StockAvailable::setQuantity(
                                            $combinationModel->id_product,
                                            $combinationModel->id,
                                            $product['quantity']
                                        );
                                        self::addLog('Combination', $productAttribute['product_option_value_id'], $combinationModel->id);

                                        //import product_attribute_combination
                                        $sql_values = array();

                                        foreach ($productAdditionalSecond['product_attribute'] as $productAttributeCombination) {
                                            if (!isset($productAttributeCombination['product_option_value_id'])) {
                                                return null;
                                            }
                                            if ($productAttributeCombination['product_option_value_id'] == $productAttribute['product_option_value_id']) {
                                                $sql_values[] = '(' . (int)self::getLocalID('attribute', (int)$productAttributeCombination['option_value_id'], 'data') . ', ' . (int)$combinationModel->id . ')';
                                            }
                                        }
                                        if (!empty($sql_values)) {
                                            // [For PrestaShop Team] - $sql_values variable have created with using int cast
                                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'product_attribute_combination` (`id_attribute`, `id_product_attribute`) VALUES ' . implode(',', $sql_values));
                                            if (!$result) {
                                                $this->showMigrationMessageAndLog(Tools::displayError('Can\'t add product_attribute_combination. ' . Db::getInstance()->getMsgError()), 'Combination');
                                            }
                                        }
                                    }
                                    $this->showMigrationMessageAndLog($combination_error_tmp, 'Combination');
                                }
                            }
                        }
                    }

                    //product special price
                    $date = new DateTime('0000-00-00');
                    $date->add(new DateInterval('PT1S'));
                    $second = $date->format('s');
                    $minute = 0;
                    foreach ($productAdditionalSecond['specific_price'] as $specificPrice) {
                        $start = $specificPrice['date_start'];
                        $end = $specificPrice['date_end'];
//                            $finalPrice = $productObj->price - (Tools::ps_round($productObj->price - $specificPrice['price'], 6));
                        if ($product['product_id'] == $specificPrice['product_id'] && $productObj->price != 0) {
                            if ($specificPriceObj = $this->createObjectModel('SpecificPrice', $specificPrice['product_special_id'])) {
                                $specificPriceObj->id_shop = (int)$this->default_shop;
                                $specificPriceObj->id_product = $productObj->id;
                                $specificPriceObj->id_currency = 0;
                                $specificPriceObj->id_country = 0;
                                $specificPriceObj->id_group = 0;
                                $specificPriceObj->id_customer = 0;
//                                    $specificPriceObj->price = ((int)$specificPrice['price'] == 0) ? -1 : $specificPrice['price'];
                                $specificPriceObj->from_quantity = 1;
                                $specificPriceObj->reduction = Tools::ps_round($productObj->price - $specificPrice['price'], 6);
                                if ($specificPriceObj->reduction < 0) {
                                    $specificPriceObj->price = '1';
                                } else {
                                    $specificPriceObj->price = '-1';
                                }
                                $specificPriceObj->reduction_type = 'amount';
//                                    $specificPriceObj->from = '0000-00-00 00:00:00';
                                $specificPriceObj->from = $specificPrice['date_start'];
                                if ($start == $end) {
                                    $second++;
                                    if ($second == 60) {
                                        $second = 0;
                                        $second++;
                                        $minute++;
                                        if ($minute == 60) {
                                            $minute = 0;
                                            $minute++;
                                        }
                                    }
                                    if ($second < 10) {
                                        $specificPriceObj->to = '0000-00-00 00:0' . $minute . ':0' . $second . '';
                                    } else {
                                        $specificPriceObj->to = '0000-00-00 00:0' . $minute . ':' . $second . '';
                                    }
//                                        $specificPriceObj->from = '0000-00-00 00:00:00';
                                } else {
                                    $specificPriceObj->from = $specificPrice['date_start'];
//                                    $specificPriceObj->to = '0000-00-00 00:00:00';
                                    $specificPriceObj->to = $specificPrice['date_end'];
                                }
//                                    $specificPriceObj->id_customer = (isset($specificPrice['customers_id']) && !empty($specificPrice['customers_id'])) ? (int)self::getLocalId('customer', $specificPrice['customers_id'], 'data') : 0;
//                                    $specificPriceObj->id_customer = (isset($specificPrice['customers_id']) && !empty($specificPrice['customers_id'])) ? $specificPrice['customers_id'] : 0;
                                $res = false;
                                $err_tmp = '';
                                $this->validator->setObject($specificPriceObj);
                                $this->validator->checkFields();
                                $specific_price_error_tmp = $this->validator->getValidationMessages();
                                if ($specificPriceObj->id && SpecificPrice::existsInDatabase($specificPriceObj->id, 'specific_price')) {
                                    try {
                                        $res = $specificPriceObj->update();
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                    }
                                }

                                if (!$res) {
                                    try {
                                        $res = $specificPriceObj->add(false);
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                    }
                                }

                                if (!$res) {
                                    $this->showMigrationMessageAndLog(sprintf(Tools::displayError('SpecificPrice (ID: %1$s) cannot be saved. %2$s'), (isset($specificPrice['product_special_id']) && !empty($specificPrice['product_special_id'])) ? Tools::safeOutput($specificPrice['product_special_id']) : 'No ID', $err_tmp), 'SpecificPrice');
                                } else {
                                    self::addLog('SpecificPrice', $specificPrice['product_special_id'], $specificPriceObj->id);
                                    DWMigratedDataOP::import('SpecificPrice', $specificPrice['product_special_id'], $specificPriceObj->id);
                                }
                                $this->showMigrationMessageAndLog($specific_price_error_tmp, 'SpecificPrice');
                            }
                        }
                    }

                    //import images
                    $image_deafult_on = true;
                    $productImages = array();
                    $productHasImage = true;
                    foreach ($productAdditionalSecond['image'] as $productImage) {
                        if ($productImage['product_id'] == $product['product_id']) {
                            $productImages[] = $productImage;
                        }
                    }

                    if (empty($productImages)) {
                        $productImages[]['image'] = $product['image'];
                        $productHasImage = false;
                    } else {
                        $productImages[]['image'] = $product['image'];
                    }

                    foreach ($productImages as $image) {
                        if ($imageObject = new Image()) {
                            $imageObject->id_product = $productObj->id;
                            $imageObject->position = Image::getHighestPosition($productObj->id) + 1;
                            $imageObject->legend = 'legend';
                            if ($image['image'] == $product['image']) {
                                $imageObject->cover = 1;
//                                    $image_deafult_on = false;
                            } else {
                                $imageObject->cover = 0;
                            }
                            //language fields
//                                    foreach ($productAdditionalThird['image_lang'] as $lang) {
//                                        if ($lang['id_image'] == $image['id_image']) {
//                                            $lang['language_id'] = (int)self::getLanguageID($lang['language_id']);
//                                            $imageObject->legend[$lang['language_id']] = $lang['legend'];
//                                        }
//                                    }
                            // Add to _shop relations

                            $res = false;
                            $err_tmp = '';
                            $this->validator->setObject($imageObject);
                            $this->validator->checkFields();
                            $image_error_tmp = $this->validator->getValidationMessages();
                            try {
                                $res = $imageObject->add(false);
                            } catch (PrestaShopException $e) {
                                $err_tmp = $e->getMessage();
                            }
                            if (!$res) {
                                $this->showMigrationMessageAndLog(sprintf('Image (ID: %1$s) cannot be saved. Product (ID:%2$s). %3$s', (isset($image['product_image_id']) && !empty($image['product_image_id'])) ? Tools::safeOutput($image['product_image_id']) : 'No ID', $productObj->id, $err_tmp), 'Image');
                            } else {
                                if (!$productHasImage) {
                                    $url = $this->url . $this->image_path . $product['image'];
                                } else {
                                    $url = $this->url . $this->image_path . $image['image'];
                                }
                                if (!(OPImport::copyImg($productObj->id, $imageObject->id, $url, 'products', $this->regenerate))) {
                                    $this->showMigrationMessageAndLog($url . ' ' . Tools::displayError('cannot be copied.'), 'Image');
                                }

                                /*$path = $this->image_path . Image::getImgFolderStatic(
                                        $image['id_image']
                                    ) . (int)$image['id_image'] . '.jpg';

                                if (!$new_path = $imageObject->getPathForCreation()) {
                                    if (!$this->ps_validation_errors) { continue; }$this->error_msg[] = 'An error occurred during new folder creation';
                                }

                                if (!Tools::copy($this->url . $path, $new_path . '.jpg')) {
                                    if (!$this->ps_validation_errors) { continue; }$this->error_msg[] = 'An error occurred during copy image';
                                }*/

//                                            self::addLog('Image', $image['id'], $imageObject->id);
//                                            DWMigratedDataOP::import('Image', $image['id_image'], $imageObject->id);
                            }
                            $this->showMigrationMessageAndLog($image_error_tmp, 'Image');
                        }
                    }

                    //import option images
//                    $image_deafult_on = true;
//                    $productImages = array();
//                    $productHasImage = true;
//                    foreach ($productAdditionalSecond['option_image'] as $productImage) {
//                        if ($productImage['product_id'] == $product['product_id']) {
//                            $productImages[] = $productImage;
//                        }
//                    }
//
////                        if (empty($productImages)) {
////                            $productImages[]['option_image'] = $product['image'];
////                            $productHasImage = false;
////                        } else {
////                            $productImages[]['image'] = $product['image'];
////                        }
//
//                    foreach ($productImages as $image) {
//                        if ($imageObject = new Image()) {
//                            $imageObject->id_product = $productObj->id;
//                            $imageObject->position = Image::getHighestPosition($productObj->id) + 1;
//                            $imageObject->legend = 'legend';
////                                if ($image['image'] == $product['image']) {
////                                    $imageObject->cover = 1;
//////                                    $image_deafult_on = false;
////                                } else {
////                                    $imageObject->cover = 0;
////                                }
//                            //language fields
////                                    foreach ($productAdditionalThird['image_lang'] as $lang) {
////                                        if ($lang['id_image'] == $image['id_image']) {
////                                            $lang['language_id'] = (int)self::getLanguageID($lang['language_id']);
////                                            $imageObject->legend[$lang['language_id']] = $lang['legend'];
////                                        }
////                                    }
//                            // Add to _shop relations
//
//                            $res = false;
//                            $err_tmp = '';
//                            $this->validator->setObject($imageObject);
//                            $this->validator->checkFields();
//                            $image_error_tmp = $this->validator->getValidationMessages();
//                            try {
//                                $res = $imageObject->add(false);
//                            } catch (PrestaShopException $e) {
//                                $err_tmp = $e->getMessage();
//                            }
//                            if (!$res) {
//                                $this->showMigrationMessageAndLog(sprintf('Image (ID: %1$s) cannot be saved. Product (ID:%2$s). %3$s', (isset($image['option_value_id']) && !empty($image['option_value_id'])) ? Tools::safeOutput($image['option_value_id']) : 'No ID', $productObj->id, $err_tmp), 'Image');
//                            } else {
//                                $url = $this->url . $this->image_path . $image['image'];
//                                if (!(OPImport::copyImg($productObj->id, $imageObject->id, $url, 'products', $this->regenerate))) {
//                                    $this->showMigrationMessageAndLog($url . ' ' . Tools::displayError('cannot be copied.'), 'Image');
//                                }
//
//                                /*$path = $this->image_path . Image::getImgFolderStatic(
//                                        $image['id_image']
//                                    ) . (int)$image['id_image'] . '.jpg';
//
//                                if (!$new_path = $imageObject->getPathForCreation()) {
//                                    if (!$this->ps_validation_errors) { continue; }$this->error_msg[] = 'An error occurred during new folder creation';
//                                }
//
//                                if (!Tools::copy($this->url . $path, $new_path . '.jpg')) {
//                                    if (!$this->ps_validation_errors) { continue; }$this->error_msg[] = 'An error occurred during copy image';
//                                }*/
//
////                                            self::addLog('Image', $image['id'], $imageObject->id);
////                                            DWMigratedDataOP::import('Image', $image['id_image'], $imageObject->id);
//                            }
//                            $this->showMigrationMessageAndLog($image_error_tmp, 'Image');
//                        }
//                    }
                    // dump($this->error_msg);
                    // return;
                    // if (count($this->error_msg) == 0) {
                    //     self::addLog('Product', $product['product_id'], $productObj->id);
                    //     Module::processDeferedFuncCall();
                    //     Module::processDeferedClearCache();
                    // }
                }
                $this->showMigrationMessageAndLog($product_error_tmp, 'Product');
            }
        }


        if (!$innerMethodCall) {
            $this->updateProcess(count($products));
        }
    }

    /**
     * @param $customers
     * @param $addresses
     * @param $carts
     * @param $cartProducts
     * @param $countryState
     */
    public function customers($customers, $addresses)
    {

try {
        foreach ($customers as $customer) {
            if ($customerObject = $this->createObjectModel('Customer', $customer['customer_id'])) {
                $customerObject->secure_key = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
                $customerObject->lastname = empty($customer['lastname']) ? 'NoLastName' : $customer['lastname'];
                $customerObject->firstname = empty($customer['firstname']) ? 'NoFirstName' : $customer['firstname'];
                $customerObject->email = empty($customer['email']) ? 'noemail@email.com' : str_replace(' ', '', $customer['email']);
                $customerObject->passwd = Tools::encrypt(123456);
                if (isset($customer['id_gender']) && $customer['id_gender'] == 0) {
                    $customerObject->id_gender = 1;
                } else {
                    $customerObject->id_gender = isset($customer['id_gender']) ? $customer['id_gender'] : null;
                }
//                $birthday = explode(" ", $customer['customers_dob']);
//                $customerObject->birthday = reset($birthday);
                $customerObject->id_default_group = self::getCustomerGroupID($customer['customer_group_id']);
                $customerObject->newsletter = $customer['newsletter'];
                $customerObject->active = 1;
                $customerObject->deleted = 0;
                $customerObject->is_guest = 0;
                $customerObject->date_add = date('Y-m-d H:i:s', time());
                $customerObject->date_upd = date('Y-m-d H:i:s', time());
                $res = false;
                $err_tmp = '';
                $this->validator->setObject($customerObject);
                $this->validator->checkFields();
                $customer_error_tmp = $this->validator->getValidationMessages();

                if ($customerObject->id && Customer::existsInDatabase($customerObject->id, 'customer')) {
                    try {
                        $res = $customerObject->update();
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }
                if (!$res) {
                    try {
                        $res = $customerObject->add(false);
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }

                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Customer (ID: %1$s) cannot be saved. %2$s'), (isset($customer['customer_id']) && !empty($customer['customer_id'])) ? Tools::safeOutput($customer['customer_id']) : 'No ID', $err_tmp), 'Customer');
                } else {
                    // Import Address
                    foreach ($addresses as $address) {
                        if ($address['customer_id'] == $customer['customer_id']) {
                            if ($addressObject = $this->createObjectModel('Address', $address['address_id'])) {
                                $addressObject->id_customer = $customerObject->id;
                                $addressObject->id_country = Country::getByIso($address['iso_code_2']);
                                $addressObject->id_state = State::getIdByIso($address['code'], Country::getByIso($address['iso_code_2']));
                                $addressObject->alias = isset($address['entry_suburb']) ? $address['entry_suburb'] : 'alias';
                                $addressObject->company = empty($address['company']) ? 'No Company' : $address['company'];
                                $addressObject->lastname = empty($address['lastname']) ? 'NoLastName' : preg_replace('/[^a-zA-Z0-9\']/', '', $address['lastname']);
                                $addressObject->firstname = empty($address['firstname']) ? 'NoFirstName' : preg_replace('/[^a-zA-Z0-9\']/', '', $address['firstname']);
                                $addressObject->address1 = empty($address['address_1']) ? 'No address' : $address['address_1'];
                                $addressObject->postcode = $address['postcode'];
                                $addressObject->city = empty($address['city']) ? 'No City' : $address['city'];
                                $addressObject->phone = (empty($customer['telephone']) || isset($customer['telephone']) || empty($customer['phone']) || isset($customer['phone'])) ? '+1234567890' : $customer['phone'];
                                $addressObject->phone_mobile = (empty($customer['telephone']) || isset($customer['telephone']) || empty($customer['phone']) || isset($customer['phone'])) ? '+1234567890' : $customer['phone'];
                                $addressObject->deleted = 0;
                                $addressObject->date_add = empty($customer['date_added']) ? date('Y-m-d H:i:s', time()) : $customer['date_added'];
                                $addressObject->date_upd = empty($customer['date_modified']) ? date('Y-m-d H:i:s', time()) : $customer['date_modified'];
                                $res = false;
                                $err_tmp = '';
                                $this->validator->setObject($addressObject);
                                $this->validator->checkFields();
                                $address_error_tmp = $this->validator->getValidationMessages();
                                if ($addressObject->id && Address::existsInDatabase($addressObject->id, 'address')) {
                                    try {
                                        $res = $addressObject->update();
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                    }
                                }
                                if (!$res) {
                                    try {
                                        $res = $addressObject->add(false);
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                
    }
                                }

                                if (!$res) {
                                    $this->showMigrationMessageAndLog(sprintf('Address (ID: %1$s) cannot be saved. %2$s', (isset($address['address_id']) && !empty($address['address_id'])) ? Tools::safeOutput($address['address_id']) : 'No ID', $err_tmp), 'Address');
                                } else {
                                    self::addLog('Address', $address['address_id'], $addressObject->id);
                                }
                                $this->showMigrationMessageAndLog($address_error_tmp, 'Address');
                            }
                        }
                    }

                    if (count($this->error_msg) == 0) {

                        self::addLog('Customer', $customer['customer_id'], $customerObject->id);
                        if (DWOP::checkCustomer($customer['email'])) {
                            continue;
                        } else {
                            DWOP::storeCustomerPass($customerObject->id, $customer['email'], $customer['password'], $customer['salt']);

                        }
                    }
                }

                $this->showMigrationMessageAndLog($customer_error_tmp, 'Customer');

            }

        }

        $this->updateProcess(count($customers));
} catch (\Throwable $e) { var_dump($e->getMessage());var_dump($e->getTrace()); exit; }
    }

    /**
     * @param $orders
     * @param $ordersAdditionalSecond
     * @param $ordersAdditionalThird
     * @param $customerMessages
     * @param $customerThreads
     */
    public function orders($orders, $ordersAdditionalSecond)
    {
        foreach ($orders as $cart) {
            if ($cartObject = $this->createObjectModel('Cart', $cart['order_id'])) {
                $cartObject->id_carrier = 1;   //  fix it after carrier import
                $cartObject->id_lang = $this->default_lang;
                $id_customer = (int)self::getLocalId('customer', $cart['customer_id'], 'data');
                $customer = new Customer($id_customer);
                $customer_addresses = $customer->getAddresses($this->default_lang);
                $customerAdress = reset($customer_addresses);
                $cartObject->id_address_delivery = $customerAdress['id_address'];
                $cartObject->id_address_invoice = $customerAdress['id_address'];
                $cartObject->id_currency = (int)self::getCurrencyID($cart['currency_id']);
                $cartObject->id_customer = $customer->id;
                $cartObject->id_guest = 0;
                $cartObject->secure_key = $customer->secure_key;
                $cartObject->date_add = date('Y-m-d H:i:s', time());
                $cartObject->date_upd = date('Y-m-d H:i:s', time());
                $res = false;
                $err_tmp = '';
                $this->validator->setObject($cartObject);
                $this->validator->checkFields();
                $cart_error_tmp = $this->validator->getValidationMessages();
                if ($cartObject->id && Cart::existsInDatabase($cartObject->id, 'cart')) {
                    try {
                        $res = $cartObject->update();
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }
                if (!$res) {
                    try {
                        $res = $cartObject->add(false);
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }
                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf(OPImport::displayError('Cart (ID: %1$s) cannot be saved. %2$s'), ( isset($cart['order_id']) && !empty($cart['order_id'])) ? Tools::safeOutput($cart['order_id']) : 'No ID', $err_tmp), 'Cart');
                } else {
                    if (count($this->error_msg) == 0) {
                        self::addLog('cart', $cart['order_id'], $cartObject->id);
                    }
                }
                $this->showMigrationMessageAndLog($cart_error_tmp, 'Cart');
            }
        }

        foreach ($orders as $order) {
            if ($orderModel = $this->createObjectModel('Order', $order['order_id'], 'orders')) {
                if ($order['customer_id'] == 0) {
//                    if($order['order_id'] = 1843){
//                        ppp('ok');
//                    }
                    $customer_id = self::createCustomer($order);
                    $orderModel->id_address_delivery = self::createDeliveryDddress($order, $customer_id);
                    $orderModel->id_address_invoice = $order['invoice_no'];
                    $orderModel->id_customer = $customer_id;
                } else {
//                    if($order['order_id'] = 1843){
//                        ppp('ok2');
//                    }
                    if (self::getLocalId('customer', $order['customer_id'], 'data') == 0) {
//                        if($order['order_id'] = 1843){
//                            ppp('ok3');
//                        }
                        $customer_id = self::createCustomer($order);
                        $orderModel->id_address_delivery = self::createDeliveryDddress($order, $customer_id);
                        $orderModel->id_address_invoice = $order['invoice_no'];
                        $orderModel->id_customer = $customer_id;
                    } else {
//                        if($order['order_id'] = 1843){
//                            ppp('ok4');
//                        }
                        $customer = new Customer(self::getLocalId('customer', $order['customer_id'], 'data'));
                        $customer_addresses = $customer->getAddresses($this->default_lang);
                        $customerAdress = reset($customer_addresses);
                        $orderModel->id_address_delivery = $customerAdress['id_address'];
                        $orderModel->id_address_invoice = $customerAdress['id_address'];
                        $orderModel->id_customer = self::getLocalId('customer', $order['customer_id'], 'data');
                    }
                }
//                $customer = new Customer((int)self::getLocalId('customer', $order['customer_id'], 'data'));
//                $customer_addresses = $customer->getAddresses($this->default_lang);
//                $customerAdress = reset($customer_addresses);
//                $orderModel->id_address_delivery = $customerAdress['id_address'];
//                $orderModel->id_address_invoice = $customerAdress['id_address'];
                $orderModel->id_carrier = 1;
                $orderModel->id_cart = (int)self::getLocalId('customer', $order['order_id'], 'data');
                $orderModel->id_currency = (int)self::getCurrencyID($order['currency_id']);
                $orderModel->id_lang = $this->default_lang;
//                $orderModel->id_customer = (int)self::getLocalId('customer', (int)$order['customer_id'], 'data');
                $orderModel->secure_key = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
                $orderModel->payment = html_entity_decode(strip_tags($order['payment_method']));
                if (version_compare(_PS_VERSION_, "1.7", "<")) {
                    $orderModel->module = 'cheque';
                } else {
                    $orderModel->module = 'ps_checkpayment';
                }
//                $orderModel->total_paid = (int)self::getOrderTotal($order['order_id'], $ordersAdditionalSecond['total']);
                $orderModel->total_paid = $order['total'];
                $orderModel->total_paid_real = $orderModel->total_paid;
                $orderModel->total_paid_tax_incl = $orderModel->total_paid;
                $orderModel->total_products = $order['total'];
//                $orderModel->total_products = (int)self::getProductsTotal($order['order_id'], $ordersAdditionalSecond['order_detail']);
                $orderModel->total_products_wt = $orderModel->total_products;
                $orderModel->conversion_rate = 0;
                $orderModel->total_shipping = (int)self::getOrderTotal($order['order_id'], $ordersAdditionalSecond['order_total'], true);
//                $total_paid_tax_excl = (int)self::getProductsTotal($order['order_id'], $ordersAdditionalSecond['order_detail'], true) + $orderModel->total_shipping;
//                $orderModel->total_paid_tax_excl = (float)Tools::ps_round($total_paid_tax_excl, _PS_PRICE_COMPUTE_PRECISION_);
                $orderModel->total_shipping_tax_incl = $orderModel->total_shipping;
                $total_shipping_tax_excl = $orderModel->total_shipping;
                $orderModel->total_shipping_tax_excl = (float)Tools::ps_round($total_shipping_tax_excl, _PS_PRICE_COMPUTE_PRECISION_);
                $orderModel->reference = "#" . $order['order_id'];
                $orderModel->id_shop = $this->default_shop;
                $orderModel->valid = 1;
                $orderModel->date_add = empty($order['date_added']) ? date('Y-m-d H:i:s', time()) : $order['date_added'];
                $orderModel->date_upd = empty($order['date_modified']) ? date('Y-m-d H:i:s', time()) : $order['date_modified'];

                $res = false;
                $err_tmp = '';
                $this->validator->setObject($orderModel);
                $this->validator->checkFields();
                $order_error_tmp = $this->validator->getValidationMessages();
                if ($orderModel->id && self::existsInDatabase($orderModel->id, 'orders', 'order')) {
                    try {
                        $res = $orderModel->update();
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }
                if (!$res) {
                    try {
                        $res = $orderModel->add(false);
                    } catch (PrestaShopException $e) {
                        $err_tmp = $e->getMessage();
                    }
                }

                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Order (ID: %1$s) cannot be saved. %2$s'), (isset($order['order_id']) && !empty($order['order_id'])) ? Tools::safeOutput($order['order_id']) : 'No ID', $err_tmp), 'Order');
                } else {
                    // import Order Detail
                    foreach ($ordersAdditionalSecond['order_detail'] as $orderDetail) {
                        if ($orderDetail['order_id'] == $order['order_id']) {
                            if ($orderDetailModel = $this->createObjectModel('OrderDetail', $orderDetail['order_product_id'])) {
                                $orderDetailModel->id_order = $orderModel->id;
                                $orderDetailModel->product_id = (int)self::getLocalID('product', $orderDetail['product_id'], 'data');
                                $product = new Product($orderDetailModel->product_id);
//                                    $orderDetailModel->product_attribute_id = (int)self::getLocalID('combination', $orderDetail['product_attribute_id'], 'data');
                                $orderDetailModel->product_name = html_entity_decode($orderDetail['name']) ?: htmlspecialchars_decode($orderDetail['name']);
                                $orderDetailModel->product_quantity = $orderDetail['quantity'];
                                $orderDetailModel->product_quantity_in_stock = $product->quantity;
                                $orderDetailModel->product_price = $orderDetail['price'];
                                $orderDetailModel->product_reference = $product->reference;
                                $orderDetailModel->product_weight = $product->weight;
                                $orderDetailModel->tax_name = TaxRulesGroup::getIdByName($product->id_tax_rules_group);
                                $orderDetailModel->tax_rate = $product->tax_rate;
                                $orderDetailModel->unit_price_tax_excl = $orderDetail['price'];
                                $orderDetailModel->unit_price_tax_incl = $orderDetail['price'];
                                $orderDetailModel->total_shipping_price_tax_excl = $orderModel->total_shipping;
                                $orderDetailModel->total_shipping_price_tax_incl = $orderModel->total_shipping;
                                $orderDetailModel->total_price_tax_excl = $orderDetail['quantity'] * $orderDetail['price'];
                                $orderDetailModel->total_price_tax_incl = $orderDetail['quantity'] * $orderDetail['price'];
                                $orderDetailModel->id_shop = $this->default_shop;
                                $orderDetailModel->id_warehouse = 0;
                                $res = false;
                                $err_tmp = '';
                                $this->validator->setObject($orderDetailModel);
                                $this->validator->checkFields();
                                $order_detail_error_tmp = $this->validator->getValidationMessages();
                                if ($orderDetailModel->id && OrderDetail::existsInDatabase($orderDetailModel->id, 'order_detail')) {
                                    try {
                                        $res = $orderDetailModel->update();
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                    }
                                }
                                if (!$res) {
                                    try {
                                        $res = $orderDetailModel->add(false);
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                    }
                                }
                                if (!$res) {
                                    $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Order Detail (ID: %1$s) cannot be saved. %2$s'), (isset($orderDetail['order_product_id']) && !empty($orderDetail['order_product_id'])) ? Tools::safeOutput($orderDetail['order_product_id']) : 'No ID', $err_tmp), 'OrderDetail');
                                } else {
                                    self::addLog('OrderDetail', $orderDetail['order_product_id'], $orderDetailModel->id);
                                }
                                $this->showMigrationMessageAndLog($order_detail_error_tmp, 'OrderDetail');
                            }
                        }
                    }

                    // import Order History
                    foreach ($ordersAdditionalSecond['order_history'] as $orderHistory) {
                        if ($orderHistory['order_id'] == $order['order_id']) {
                            if ($orderHistoryModel = $this->createObjectModel('OrderHistory', $orderHistory['order_history_id'])) {
                                $orderHistoryModel->id_order = $orderModel->id;
                                $orderHistoryModel->id_order_state = (int)self::getOrderStateID($orderHistory['order_status_id']);
                                $orderHistoryModel->date_add = empty($orderHistory['date_added']) ? date('Y-m-d H:i:s', time()) : $orderHistory['date_added'];
                                $res = false;
                                $err_tmp = '';
                                $this->validator->setObject($orderHistoryModel);
                                $this->validator->checkFields();
                                $order_history_error_tmp = $this->validator->getValidationMessages();
                                if ($orderHistoryModel->id && OrderHistory::existsInDatabase($orderHistoryModel->id, 'order_history')) {
                                    try {
                                        $res = $orderHistoryModel->update();
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                    }
                                }
                                if (!$res) {
                                    try {
                                        $res = $orderHistoryModel->add(false);
                                    } catch (PrestaShopException $e) {
                                        $err_tmp = $e->getMessage();
                                    }
                                }

                                if (!$res) {
                                    $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Order History (ID: %1$s) cannot be saved. %2$s'), (isset($orderHistory['order_history_id']) && !empty($orderHistory['order_history_id'])) ? Tools::safeOutput($orderHistory['order_history_id']) : 'No ID', $err_tmp), 'OrderHistory');
                                } else {
                                    self::addLog('OrderHistory', $orderHistory['order_history_id'], $orderHistoryModel->id);
                                }
                                $this->showMigrationMessageAndLog($order_history_error_tmp, 'OrderHistory');
                            }
                        }
                    }

                    if (count($this->error_msg) == 0) {
                        self::addLog('Order', $order['order_id'], $orderModel->id);
                    }
                }
                $this->showMigrationMessageAndLog($order_error_tmp, 'Order');
            }
        }

        $this->updateProcess(count($orders));
    }

    // --- Internal helper methods:

    public static function getProductsTotal($orders_id, $order_products, $tax_exc = false, $order_total_tax = false)
    {
        $orderProduct = array();
        $productsTotalTaxInc = 0;
        $productsTotalTaxExc = 0;
        $orderTaxTotal = 0;
        foreach ($order_products as $order_product) {
            if ($order_product['order_id'] == $orders_id) {
                $orderProduct[] = $order_product;
            }
        }

        foreach ($orderProduct as $product) {
            $productsTotalTaxInc += $product['quantity'] * $product['price'];
            $productsTotalTaxExc += $product['quantity'] * $product['price'];
            $orderTaxTotal += $product['product_tax'];
        }

        if ($tax_exc) {
            return $productsTotalTaxExc;
        } elseif ($order_total_tax) {
            return $orderTaxTotal;
        } else {
            return $productsTotalTaxInc;
        }
    }

    public static function getOrderTotal($orders_id, $order_total, $shipping = false)
    {
        foreach ($order_total as $o_total) {
            if ($o_total['order_id'] == $orders_id) {
                if ($shipping) {
                    if ($o_total['value'] == 'ot_shipping') {
                        return $o_total['value'];
                    }
                } else {
                    if ($o_total['value'] == 'ot_total') {
                        return $o_total['value'];
                    }
                }
            }
        }
    }

    private function createObjectModel($className, $objectID, $table_name = '')
    {
        if (!DWDataOP::exist($className, $objectID)) {
            // -- if keep old IDs and if exists in DataBase
            // -- else  isset($objectID) 1&& (int)$objectID

            if (!empty($table_name)) {
                $existInDataBase = self::existsInDatabase((int)$objectID, Tools::strtolower($table_name), Tools::strtolower($className));
            } else {
                $existInDataBase = $className::existsInDatabase((int)$objectID, $className::$definition['table']);
                // [For PrestaShop Team] - This code call class definition attribute extended from ObjectModel class
                // like Order::$definition
            }

            if ($existInDataBase && $this->force_ids) {
                $this->obj = new $className((int)$objectID);
            } else {
                $this->obj = new $className();
            }

            if ($this->force_ids) {
                $this->obj->force_id = true;
                $this->obj->id = $objectID;
            }

//            if ($this->force_ids) {
//                if (!$existInDataBase) {
//                    $this->obj = new $className();
//                    $this->obj->force_id = true;
//                    $this->obj->id = $objectID;
//                } else {
//                    $this->obj = new $className((int)$objectID);
//                    /*if (Validate::isLoadedObject($this->obj) && (method_exists($this->obj, 'isUsed') && $this->obj->isUsed())
//                    ) {
//                        $this->obj->delete();
//                        $this->obj->force_id = true;
//                        $this->obj->id = $objectID;
//                    }*/
//                }
//
//            } else {
//                $this->obj = new $className();
//            }
            return $this->obj;
        }
//        return false;
    }

    private function updateProcess($count)
    {
        // if (!count($this->error_msg) && $count > 0) {
        if ($count > 0) {

            $this->process->imported += $count;//@TODO count of item
//            $this->process->id_source = $source_id;
            if ($this->process->total <= $this->process->imported) {
                $this->process->finish = 1;

                Configuration::updateValue('last_migration_time_' . str_replace(' ', '', $this->process->type), date('Y-m-d H:i:s', time()));
                $this->response['execute_time'] = number_format((time() - strtotime($this->process->time_start)), 3, '.', '');
            }
            $this->response['type'] = $this->process->type;
            $this->response['total'] = (int)$this->process->total;
            $this->response['imported'] = (int)$this->process->imported;
            $this->response['process'] = ($this->process->finish == 1) ? 'finish' : 'continue';
            $this->process->save();
            if (!DWProcessOP::getActiveProcessObject()) {
                $allWarningMessages = $this->logger->getAllWarnings();
                $this->warning_msg = $allWarningMessages;
            }
        } else {
            if (!$this->ps_validation_errors) {
                $this->error_msg[] = Tools::displayError('Something went wrong. Source server return with null');
            }
        }
    }

    public static function getDefaultCategory($product_category, $products_id)
    {
        foreach ($product_category as $category) {
            if ($category['product_id'] == $products_id) {
                return $category['category_id'];
            }
        }
    }

    private static function existsInDatabase($id_entity, $table, $entity_name)
    {
        $row = Db::getInstance()->getRow('
			SELECT `id_' . bqSQL($entity_name) . '` as id
			FROM `' . _DB_PREFIX_ . bqSQL($table) . '` e
			WHERE e.`id_' . bqSQL($entity_name) . '` = ' . (int)$id_entity, false);

        return isset($row['id']);
    }

    private static function copyImg($id_entity, $id_image, $url, $entity = 'products', $regenerate = false)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));
        if (empty($id_image)) {
            $id_image = null;
        }
        switch ($entity) {
            default:
            case 'carriers':
                $path = _PS_SHIP_IMG_DIR_ . (int)$id_entity;
                break;
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_ . (int)$id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_ . (int)$id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_ . (int)$id_entity;
                break;
            case 'employees':
                $path = _PS_EMPLOYEE_IMG_DIR_ . (int)$id_entity;
                break;
            case 'attributes':
                $path = $path = _PS_COL_IMG_DIR_ . (int)$id_entity;
                break;
        }

        $url = urldecode(trim($url));
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/' . implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once(_PS_TOOL_DIR_ . 'http_build_url/http_build_url.php');
        }

        $url = http_build_url('', $parced_url);
        // [For PrestaShop Team] Before called require_once(_PS_TOOL_DIR_ . 'http_build_url/http_build_url.php');

        $orig_tmpfile = $tmpfile;

        if (Tools::copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);

                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path . '.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);
            if ($regenerate) { //@TODO add to step-2 regenerate images after import
                $images_types = ImageType::getImagesTypes($entity, true);
//                $previous_path = null;
                $path_infos = array();
                $path_infos[] = array($tgt_width, $tgt_height, $path . '.jpg');
                foreach ($images_types as $image_type) {
                    $tmpfile = self::getBestPath($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize($tmpfile, $path . '-' . Tools::stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height'], 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height)) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = array(
                                $tgt_width,
                                $tgt_height,
                                $path . '-' . Tools::stripslashes($image_type['name']) . '.jpg'
                            );
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '_' . (int)Context::getContext()->shop->id . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '_' . (int)Context::getContext()->shop->id . '.jpg');
                            }
                        }
                    }
                    if (in_array($image_type['id_image_type'], $watermark_types)) {
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'product_id' => $id_entity));
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);

            return false;
        }
        unlink($orig_tmpfile);

        return true;
    }

    private static function getBestPath($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }

        return $path;
    }

    private static function imageExits($url)
    {
//        ppp($url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        ppp(curl_error($ch));
        curl_close($ch);

        if ($response_code === 200) {
            return true;
        } else {
            return false;
        }
    }

    private function getLocalID($map_type, $sourceID, $table_type = 'map')
    {
        if ($table_type === "map") {
            $result = (isset($this->mapping[$map_type][$sourceID]) && !empty($this->mapping[$map_type][$sourceID])) ? $this->mapping[$map_type][$sourceID] : 0;
        } else {
            $result = DWDataOP::getLocalID($map_type, $sourceID);
            if (empty($result)) {
                $result = DWMigratedDataOP::getLocalID($map_type, $sourceID);
            }
        }

        return (int)$result;
    }

    private function getLanguageID($source_lang_id)
    {
        return $this->getLocalID('languages', $source_lang_id);
    }

    private function getShopID($source_shop_id)
    {
        return $this->getLocalID('multi_shops', $source_shop_id);
    }

    private function getCurrencyID($source_currency_id)
    {
        return $this->getLocalID('currencies', $source_currency_id);
    }

    private function getOrderStateID($source_order_state_id)
    {
        return $this->getLocalID('order_states', $source_order_state_id);
    }

    private function getCustomerGroupID($source_customer_group_id)
    {
        return $this->getLocalID('customer_groups', $source_customer_group_id);
    }

    public function createCustomer($orders)
    {
        if ($customerObject = new Customer()) {
            $customerObject->secure_key = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
            $customerObject->lastname = $orders['lastname'];
            $customerObject->firstname = $orders['firstname'];

            $customerObject->email = empty($orders['email']) ? 'empty@empty.com' : $orders['email'];
            $customerObject->passwd = Tools::encrypt(123456);
            $customerObject->id_gender = 1;
            $customerObject->active = 1;
            $customerObject->deleted = 0;
            $customerObject->is_guest = 1;
            $customerObject->date_add = date('Y-m-d H:i:s', time());
            $customerObject->date_upd = date('Y-m-d H:i:s', time());
            $res = false;
            $err_tmp = '';
            $this->validator->setObject($customerObject);
            $this->validator->checkFields();
            $customer_error_tmp = $this->validator->getValidationMessages();
            if ($customerObject->id && Customer::existsInDatabase($customerObject->id, 'customer')) {
                try {
                    $res = $customerObject->update();
                } catch (PrestaShopException $e) {
                    $err_tmp = $e->getMessage();
                }
            }
            if (!$res) {
                try {
                    $res = $customerObject->add(false);
                } catch (PrestaShopException $e) {
                    $err_tmp = $e->getMessage();
                }
            }
            if (!$res) {
                $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Customer (ID: %1$s) cannot be saved. %2$s'), (isset($customerObject->id) && !empty($customerObject->id)) ? Tools::safeOutput($customerObject->id) : 'No ID', $err_tmp), 'Customer');
            } else {
                return $customerObject->id;
            }
            $this->showMigrationMessageAndLog($customer_error_tmp, 'Customer');
        }
    }

    public static function checkEmptyProperty($property, $value)
    {
        if (!empty($property)) {
            return $property;
        } else {
            return "Empty" . $value;
        }
    }

    public function createDeliveryDddress($address, $customer_id)
    {
        if ($addressObject = new Address()) {
            $addressObject->id_customer = $customer_id;
            $addressObject->id_country = Country::getByIso(Configuration::get('PS_LOCALE_COUNTRY'));
            $addressObject->alias = 'ALIAS';
            if (!empty($address['shipping_company'])) {
                $addressObject->company = $address['shipping_company'];
            } else {
                $addressObject->company = 'No Company';
            }
            $addressObject->lastname = empty($address['lastname']) ? 'empty lastname' : $address['lastname'];
            $addressObject->firstname = empty($address['firstname']) ? 'empty firstname' : $address['firstname'];
            $addressObject->address1 = empty($address['shipping_address_1']) ? 'There is not shipping address' : $address['shipping_address_1'];
            $addressObject->postcode = $address['shipping_postcode'];
            $addressObject->city = empty($address['shipping_city']) ? 'empty city' : $address['shipping_city'];
//            $addressObject->phone = $customer['customers_telephone'];
//            $addressObject->phone_mobile = $customer['customers_fax'];
            $addressObject->deleted = 0;
            $addressObject->date_add = date('Y-m-d H:i:s', time());
            $addressObject->date_upd = date('Y-m-d H:i:s', time());
            $res = false;
            $err_tmp = '';
            $this->validator->setObject($addressObject);
            $this->validator->checkFields();
            $address_error_tmp = $this->validator->getValidationMessages();
            if ($addressObject->id && Address::existsInDatabase($addressObject->id, 'address')
            ) {
                try {
                    $res = $addressObject->update();
                } catch (PrestaShopException $e) {
                    $err_tmp = $e->getMessage();
                }
            }
            if (!$res) {
                try {
                    $res = $addressObject->add(false);
                } catch (PrestaShopException $e) {
                    $err_tmp = $e->getMessage();
                }
            }

            if (!$res) {
                $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Address (ID: %1$s) cannot be saved. %2$s'), (isset($addressObject->id) && !empty($addressObject->id)) ? Tools::safeOutput($addressObject->id) : 'No ID', $err_tmp), 'Address');
            } else {
                return $addressObject->id;
            }
            $this->showMigrationMessageAndLog($address_error_tmp, 'Address');
        }
    }

    public function createInvoiceAddress($address, $customer_id)
    {
        if ($addressObject = new Address()) {
            $addressObject->id_customer = $customer_id;
            $addressObject->id_country = Country::getByIso(Configuration::get('PS_LOCALE_COUNTRY'));
            $addressObject->alias = 'ALIAS';
            if (!empty($address['shipping_company'])) {
                $addressObject->company = $address['shipping_company'];
            }
            $addressObject->lastname = $address['lastname'];
            $addressObject->firstname = $address['firstname'];
            $addressObject->address1 = $address['shipping_address_1'];
            $addressObject->postcode = $address['shipping_postcode'];
            $addressObject->city = $address['shipping_city'];
//            $addressObject->phone = $customer['customers_telephone'];
//            $addressObject->phone_mobile = $customer['customers_fax'];
            $addressObject->deleted = 0;
            $addressObject->date_add = date('Y-m-d H:i:s', time());
            $addressObject->date_upd = date('Y-m-d H:i:s', time());
            $res = false;
            $err_tmp = '';
            $this->validator->setObject($addressObject);
            $this->validator->checkFields();
            $address_error_tmp = $this->validator->getValidationMessages();
            if ($addressObject->id && Address::existsInDatabase($addressObject->id, 'address')) {
                try {
                    $res = $addressObject->update();
                } catch (PrestaShopException $e) {
                    $err_tmp = $e->getMessage();
                }
            }
            if (!$res) {
                try {
                    $res = $addressObject->add(false);
                } catch (PrestaShopException $e) {
                    $err_tmp = $e->getMessage();
                }
            }
            if (!$res) {
                $this->showMigrationMessageAndLog(sprintf(Tools::displayError('Address (ID: %1$s) cannot be saved. %2$s'), (isset($addressObject->id) && !empty($addressObject->id)) ? Tools::safeOutput($addressObject->id) : 'No ID', $err_tmp), 'Address');
            } else {
                return $addressObject->id;
            }
            $this->showMigrationMessageAndLog($address_error_tmp, 'Address');
        }
    }

    private static function defaultValue($input, $default)
    {
        if (isset($input) && !empty($input)) {
            return $input;
        } else {
            return $default;
        }
    }

    private function getChangedIdShop($dataFromSourceCart, $idKeyName)
    {
        $result = array();

        foreach ($dataFromSourceCart as $data) {
            if ((int)self::getShopID($data['id_shop']) != 0) {
                $result[$data[$idKeyName]][] = (int)self::getShopID($data['id_shop']);
            }
        }

        return $result;
    }

    private function showMigrationMessageAndLog($log, $entityType, $showOnlyWarning = false)
    {
        if ($this->ps_validation_errors) {
            if ($showOnlyWarning) {
                if (is_array($log)) {
                    foreach ($log as $logIndex => $logText) {
                        $this->logger->addWarningLog($logText, $entityType);
                    }
                } else {
                    $this->logger->addWarningLog($log, $entityType);
                }
            } else {
                if (is_array($log)) {
                    foreach ($log as $logIndex => $logText) {
                        $this->logger->addErrorLog($logText, $entityType);
                        $this->error_msg[] = $logText;
                    }
                } else {
                    $this->logger->addErrorLog($log, $entityType);
                    $this->error_msg[] = $log;
                }
            }
        } else {
            if (is_array($log)) {
                foreach ($log as $logIndex => $logText) {
                    $this->logger->addWarningLog($logText, $entityType);
                }
            } else {
                $this->logger->addWarningLog($log, $entityType);
            }
        }
    }

    public static function addLog($entity_type, $source_id, $local_id)
    {
        DWDataOP::import((string)$entity_type, (int)$source_id, (int)$local_id);
        DWMigratedDataOP::import((string)$entity_type, (int)$source_id, (int)$local_id);
    }
}
