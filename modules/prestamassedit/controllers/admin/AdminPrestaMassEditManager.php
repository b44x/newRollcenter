<?php
/**
 * 2008-2025 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2025 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPrestaMassEditManagerController extends ModuleAdminController
{
    private $tabClassName = 'AdminPrestaMassEditManager';

    protected $actionBtnText;
    protected $fieldNameText;
    protected $availabilityPreferenceOptions;
    protected $deliveryTimeOptions;
    protected $visibilityType;
    protected $conditionOptions;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;

        $this->actionBtnText = [
            '0' => $this->trans('Disable All', [], 'Modules.Prestamassedit.Admin'),
            '1' => $this->trans('Enable All', [], 'Modules.Prestamassedit.Admin'),
            'prepend' => $this->trans('Append Before', [], 'Modules.Prestamassedit.Admin'),
            'append' => $this->trans('Append After', [], 'Modules.Prestamassedit.Admin'),
            'replace' => $this->trans('Replace', [], 'Modules.Prestamassedit.Admin'),
            'replace_all' => $this->trans('Replace All', [], 'Modules.Prestamassedit.Admin'),
            'add' => $this->trans('Add', [], 'Modules.Prestamassedit.Admin'),
            'remove' => $this->trans('Remove', [], 'Modules.Prestamassedit.Admin'),
            'remove_all' => $this->trans('Remove All', [], 'Modules.Prestamassedit.Admin'),
            'add_amount' => $this->trans('+ amount', [], 'Modules.Prestamassedit.Admin'),
            'sub_amount' => $this->trans('- amount', [], 'Modules.Prestamassedit.Admin'),
            'add_perc' => $this->trans('+ %', [], 'Modules.Prestamassedit.Admin'),
            'sub_perc' => $this->trans('- %', [], 'Modules.Prestamassedit.Admin'),
        ];

        $this->fieldNameText = [
            'name' => $this->trans('Name', [], 'Modules.Prestamassedit.Admin'),
            'description_short' => $this->trans('Summary', [], 'Modules.Prestamassedit.Admin'),
            'description' => $this->trans('Description', [], 'Modules.Prestamassedit.Admin'),
            'reference' => $this->trans('Reference', [], 'Modules.Prestamassedit.Admin'),
            'status' => $this->trans('Status', [], 'Modules.Prestamassedit.Admin'),
            'default_category' => $this->trans('Category Default', [], 'Modules.Prestamassedit.Admin'),
            'categories' => $this->trans('Categories', [], 'Modules.Prestamassedit.Admin'),
            'brands' => $this->trans('Brand', [], 'Modules.Prestamassedit.Admin'),
            'features' => $this->trans('Features', [], 'Modules.Prestamassedit.Admin'),
            'related_prod' => $this->trans('Related Product', [], 'Modules.Prestamassedit.Admin'),
            'price' => $this->trans('Price', [], 'Modules.Prestamassedit.Admin'),
            'unit_price' => $this->trans('Price Per Unit', [], 'Modules.Prestamassedit.Admin'),
            'wholesale_price' => $this->trans('Cost Price', [], 'Modules.Prestamassedit.Admin'),
            'id_tax_rules_group' => $this->trans('Tax Rule', [], 'Modules.Prestamassedit.Admin'),
            'on_sale' => $this->trans('Display The (On Sale) Flag On Products', [], 'Modules.Prestamassedit.Admin'),
            'specific_price' => $this->trans('Specific Price', [], 'Modules.Prestamassedit.Admin'),
            'qty' => $this->trans('Quantity', [], 'Modules.Prestamassedit.Admin'),
            'min_qty' => $this->trans('Minimum Quantity', [], 'Modules.Prestamassedit.Admin'),
            'low_stock' => $this->trans('Low Stock Level', [], 'Modules.Prestamassedit.Admin'),
            'mail_on_low_stock' => $this->trans('Mail Me On Low Quantity', [], 'Modules.Prestamassedit.Admin'),
            'availability_preference' => $this->trans('Order behavior when out of stock', [], 'Modules.Prestamassedit.Admin'),
            'label_in_stock' => $this->trans('Label when in stock', [], 'Modules.Prestamassedit.Admin'),
            'label_out_stock' => $this->trans('Label when out of stock (and back order allowed)', [], 'Modules.Prestamassedit.Admin'),
            'stock_location' => $this->trans('Stock Location', [], 'Modules.Prestamassedit.Admin'),
            'combination' => $this->trans('Combinations', [], 'Modules.Prestamassedit.Admin'),
            'weight' => $this->trans('Weight', [], 'Modules.Prestamassedit.Admin'),
            'height' => $this->trans('Height', [], 'Modules.Prestamassedit.Admin'),
            'width' => $this->trans('Width', [], 'Modules.Prestamassedit.Admin'),
            'depth' => $this->trans('Depth', [], 'Modules.Prestamassedit.Admin'),
            'additional_delivery_times' => $this->trans('Delivery time', [], 'Modules.Prestamassedit.Admin'),
            'delivery_out_stock' => $this->trans('Delivery time out stock', [], 'Modules.Prestamassedit.Admin'),
            'delivery_in_stock' => $this->trans('Delivery time in stock', [], 'Modules.Prestamassedit.Admin'),
            'shipping_fee' => $this->trans('Shipping fee', [], 'Modules.Prestamassedit.Admin'),
            'available_carrier' => $this->trans('Available carrier', [], 'Modules.Prestamassedit.Admin'),
            'meta_title' => $this->trans('Meta title', [], 'Modules.Prestamassedit.Admin'),
            'meta_description' => $this->trans('Meta description', [], 'Modules.Prestamassedit.Admin'),
            'meta_url' => $this->trans('Friendly url', [], 'Modules.Prestamassedit.Admin'),
            'visibility' => $this->trans('Visibility', [], 'Modules.Prestamassedit.Admin'),
            'condition' => $this->trans('Condition', [], 'Modules.Prestamassedit.Admin'),
            'available_for_order' => $this->trans('Available for order', [], 'Modules.Prestamassedit.Admin'),
            'online_only' => $this->trans('Web Only (Not sold in your retail store)', [], 'Modules.Prestamassedit.Admin'),
            'show_condition' => $this->trans('Display condition on products', [], 'Modules.Prestamassedit.Admin'),
            'isbn' => $this->trans('ISBN', [], 'Modules.Prestamassedit.Admin'),
            'ean13' => $this->trans('EAN-13 or JAN barcode', [], 'Modules.Prestamassedit.Admin'),
            'upc' => $this->trans('UPC barcode', [], 'Modules.Prestamassedit.Admin'),
            'mpn' => $this->trans('MPN', [], 'Modules.Prestamassedit.Admin'),
            'tags' => $this->trans('Tags', [], 'Modules.Prestamassedit.Admin'),
            'customizing' => $this->trans('Customizing', [], 'Modules.Prestamassedit.Admin'),
        ];

        // Allowed by prestashop to save into table
        $this->availabilityPreferenceOptions = [
            0 => $this->trans('Deny orders', [], 'Modules.Prestamassedit.Admin'),
            1 => $this->trans('Allow orders', [], 'Modules.Prestamassedit.Admin'),
            2 => $this->trans('Use default behavior (Deny orders)', [], 'Modules.Prestamassedit.Admin'),
        ];
        $this->deliveryTimeOptions = [
            0 => $this->trans('None', [], 'Modules.Prestamassedit.Admin'),
            1 => $this->trans('Default delivery time', [], 'Modules.Prestamassedit.Admin'),
            2 => $this->trans('Specific delivery time to this product', [], 'Modules.Prestamassedit.Admin'),
        ];
        // PrestaShop's default values for visibility
        $this->visibilityType = [
            'both' => $this->trans('Everywhere', [], 'Modules.Prestamassedit.Admin'),
            'catalog' => $this->trans('Catalog only', [], 'Modules.Prestamassedit.Admin'),
            'search' => $this->trans('Search only', [], 'Modules.Prestamassedit.Admin'),
            'none' => $this->trans('Nowhere', [], 'Modules.Prestamassedit.Admin'),
        ];
        $this->conditionOptions = [
            'new' => $this->trans('New', [], 'Modules.Prestamassedit.Admin'),
            'used' => $this->trans('Used', [], 'Modules.Prestamassedit.Admin'),
            'refurbished' => $this->trans('Refurbished', [], 'Modules.Prestamassedit.Admin'),
        ];
    }

    public function initModal()
    {
        parent::initModal();
        $languages = Language::getLanguages(false);
        $translateLinks = [];
        $module = Module::getInstanceByName($this->module->name);
        if (false === $module) {
            return;
        }
        $isNewTranslateSystem = $module->isUsingNewTranslationSystem();
        $link = $this->context->link;
        foreach ($languages as $lang) {
            if ($isNewTranslateSystem) {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslationSf',
                    true,
                    [
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $module->name,
                        'locale' => $lang['locale'],
                    ]
                );
            } else {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslations',
                    true,
                    [],
                    [
                        'type' => 'modules',
                        'module' => $module->name,
                        'lang' => $lang['iso_code'],
                    ]
                );
            }
        }
        $tabLink = 'index.php?tab=AdminTranslations&token=';
        $adminTranslation = Tools::getAdminTokenLite('AdminTranslations') . '&type=modules&module=';
        $configure = $this->module->name . '&lang=';
        $this->context->smarty->assign(
            [
                'trad_link' => $tabLink . $adminTranslation . $configure,
                'module_languages' => $languages,
                'module_name' => $this->module->name,
                'translateLinks' => $translateLinks,
            ]
        );
        $modal_content = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/modal_translation.tpl'
        );
        $this->modals[] = [
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->trans('Translate this module', [], 'Modules.Prestamassedit.Admin'),
            'modal_content' => $modal_content,
        ];
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['refresh'] = [
            'href' => self::$currentIndex . '&token=' . $this->token,
            'icon' => 'process-icon-refresh',
            'desc' => $this->trans('Refresh', [], 'Modules.Prestamassedit.Admin'),
        ];
        $this->page_header_toolbar_btn['desc-module-back'] = [
            'href' => $this->context->link->getAdminLink('AdminModulesSf'),
            'icon' => 'process-icon-back',
            'desc' => $this->trans('Go to module manager', [], 'Modules.Prestamassedit.Admin'),
        ];
        $this->page_header_toolbar_btn['desc-module-translate'] = [
            'href' => '#',
            'desc' => $this->trans('Translate', [], 'Modules.Prestamassedit.Admin'),
            'icon' => 'process-icon-flag',
            'modal_target' => '#moduleTradLangSelect',
        ];
    }

    public function renderList()
    {
        return $this->renderView();
    }

    public function renderView()
    {
        $this->setMultiLangData();
        $this->setQueryBuilderData();
        $this->setUpdateFormData();
        $languages = Language::getLanguages(false);
        $this->context->smarty->assign([
            'languages' => $languages,
            'current_lang' => $this->context->language,
        ]);
        Media::addJsDef([
            'languages' => $languages,
            'current_lang' => $this->context->language,
        ]);
        return parent::renderView();
    }

    public function setMultiLangData()
    {
        $queryBuilderLang = [
            'add_rule' => $this->trans('Add Rule', [], 'Modules.Prestamassedit.Admin'),
            'add_group' => $this->trans('Add Group', [], 'Modules.Prestamassedit.Admin'),
            'delete_rule' => $this->trans('Delete', [], 'Modules.Prestamassedit.Admin'),
            'delete_group' => $this->trans('Delete Group', [], 'Modules.Prestamassedit.Admin'),
            'conditions' => [
                'AND' => $this->trans('AND', [], 'Modules.Prestamassedit.Admin'),
                'OR' => $this->trans('OR', [], 'Modules.Prestamassedit.Admin'),
            ],
            'operators' => [
                'equal' => $this->trans('equal', [], 'Modules.Prestamassedit.Admin'),
                'not_equal' => $this->trans('not equal', [], 'Modules.Prestamassedit.Admin'),
                'in' => $this->trans('in', [], 'Modules.Prestamassedit.Admin'),
                'not_in' => $this->trans('not in', [], 'Modules.Prestamassedit.Admin'),
                'less' => $this->trans('less', [], 'Modules.Prestamassedit.Admin'),
                'less_or_equal' => $this->trans('less or equal', [], 'Modules.Prestamassedit.Admin'),
                'greater' => $this->trans('greater', [], 'Modules.Prestamassedit.Admin'),
                'greater_or_equal' => $this->trans('greater or equal', [], 'Modules.Prestamassedit.Admin'),
                'between' => $this->trans('between', [], 'Modules.Prestamassedit.Admin'),
                'not_between' => $this->trans('not between', [], 'Modules.Prestamassedit.Admin'),
                'begins_with' => $this->trans('begins with', [], 'Modules.Prestamassedit.Admin'),
                'not_begins_with' => $this->trans('not begins with', [], 'Modules.Prestamassedit.Admin'),
                'contains' => $this->trans('contains', [], 'Modules.Prestamassedit.Admin'),
                'not_contains' => $this->trans('not contains', [], 'Modules.Prestamassedit.Admin'),
                'ends_with' => $this->trans('ends with', [], 'Modules.Prestamassedit.Admin'),
                'not_ends_with' => $this->trans('not ends with', [], 'Modules.Prestamassedit.Admin'),
                'is_empty' => $this->trans('is empty', [], 'Modules.Prestamassedit.Admin'),
                'is_not_empty' => $this->trans('is not empty', [], 'Modules.Prestamassedit.Admin'),
                'is_null' => $this->trans('is null', [], 'Modules.Prestamassedit.Admin'),
                'is_not_null' => $this->trans('is not null', [], 'Modules.Prestamassedit.Admin'),
            ],
        ];

        $dataTablesLang = [
            'decimal' => '',
            'emptyTable' => $this->trans('No data found', [], 'Modules.Prestamassedit.Admin'),
            'info' => $this->trans('Showing _START_ to _END_ of _TOTAL_ entries', [], 'Modules.Prestamassedit.Admin'),
            'infoEmpty' => $this->trans('Showing 0 to 0 of 0 entries', [], 'Modules.Prestamassedit.Admin'),
            'infoFiltered' => $this->trans('(filtered from _MAX_ total entries)', [], 'Modules.Prestamassedit.Admin'),
            'infoPostFix' => '',
            'thousands' => ',',
            'lengthMenu' => $this->trans('_MENU_ Items per page', [], 'Modules.Prestamassedit.Admin'),
            'loadingRecords' => $this->trans('Loading...', [], 'Modules.Prestamassedit.Admin'),
            'processing' => '',
            'search' => $this->trans('Search:', [], 'Modules.Prestamassedit.Admin'),
            'zeroRecords' => $this->trans('No data found', [], 'Modules.Prestamassedit.Admin'),
            'aria' => [
                'orderable' => $this->trans('Order by this column', [], 'Modules.Prestamassedit.Admin'),
                'orderableReverse' => $this->trans('Reverse order this column', [], 'Modules.Prestamassedit.Admin'),
            ],
        ];

        $tagifyPromptLang = $this->trans('Add Tags', [], 'Modules.Prestamassedit.Admin');

        $messagesByJs = [
            'on_no_product_select' => $this->trans('No product has been selected for updating.', [], 'Modules.Prestamassedit.Admin'),
            'on_row_delete' => $this->trans('Are you certain you want to delete this row?', [], 'Modules.Prestamassedit.Admin'),
            'on_update_final' => $this->trans('Have you verified all the values for updating?', [], 'Modules.Prestamassedit.Admin'),
        ];

        Media::addJsDef([
            '$queryBuilderLang' => $queryBuilderLang,
            '$dataTablesLang' => $dataTablesLang,
            '$tagifyPromptLang' => $tagifyPromptLang,
            'messagesByJs' => $messagesByJs,
        ]);
    }

    public function setQueryBuilderData()
    {
        $idLang = $this->context->language->id;
        $shops = Shop::getShops();
        $products = Product::getProducts($idLang, 0, 0, 'id_product', 'ASC', false, true);

        $attributeGroups = AttributeGroup::getAttributesGroups($idLang);
        $productAttrs = [];
        foreach ($attributeGroups as $attributeGroup) {
            $key = str_replace(' ', '-', $attributeGroup['name']);
            $productAttrs[$key] = AttributeGroup::getAttributes($idLang, $attributeGroup['id_attribute_group']);
        }

        $manufacturers = Manufacturer::getManufacturers(false, $idLang);
        $suppliers = Supplier::getSuppliers(false, $idLang);
        $categories = Category::getCategories($idLang, true, false, '', 'ORDER BY c.`id_category`');
        $features = Feature::getFeatures($idLang);

        $featureValuesArr = [];
        foreach ($features as $feature) {
            $featureValues = FeatureValue::getFeatureValuesWithLang($idLang, $feature['id_feature']);
            foreach ($featureValues as $featureValue) {
                $featureValuesArr[] = [
                    $featureValue['id_feature_value'] => $feature['name'] . ' - ' . $featureValue['value'],
                ];
            }
        }
        $productStatus = [
            1 => $this->trans('Active', [], 'Modules.Prestamassedit.Admin'),
            0 => $this->trans('Inactive', [], 'Modules.Prestamassedit.Admin'),
        ];
        $availableForOrder = [
            1 => $this->trans('Yes', [], 'Modules.Prestamassedit.Admin'),
            0 => $this->trans('No', [], 'Modules.Prestamassedit.Admin'),
        ];

        $productArr = [];
        foreach ($products as $product) {
            $productArr[] = [$product['id_product'] => '(' . $this->trans('ID:', [], 'Modules.Prestamassedit.Admin') . ' ' .
                $product['id_product'] . ') ' . $product['name']];
        }
        $productAttrArr = [];
        foreach ($productAttrs as $attrGroupName => $productAttr) {
            foreach ($productAttr as $attr) {
                $productAttrArr[] = [
                    $attr['id_attribute'] => $attrGroupName . ' – ' . $attr['name'],
                ];
            }
        }
        $supplierArr = [];
        foreach ($suppliers as $supplier) {
            $supplierArr[] = [
                $supplier['id_supplier'] => '(' . $this->trans('ID:', [], 'Modules.Prestamassedit.Admin') . ' ' .
                    $supplier['id_supplier'] . ') ' . $supplier['name'],
            ];
        }
        $manufacturerArr = [];
        foreach ($manufacturers as $manufacturer) {
            $manufacturerArr[] = [$manufacturer['id_manufacturer'] => '(' . $this->trans('ID:', [], 'Modules.Prestamassedit.Admin') . ' ' .
                $manufacturer['id_manufacturer'] . ') ' . $manufacturer['name']];
        }
        $categoryArr = [];
        foreach ($categories as $category) {
            $categoryArr[] = [$category['id_category'] => '(' . $this->trans('ID:', [], 'Modules.Prestamassedit.Admin') . ' ' .
                $category['id_category'] . ') ' . $category['name']];
        }
        $shopArr = [];
        foreach ($shops as $shop) {
            $shopArr[] = [$shop['id_shop'] => $shop['name']];
        }
        $shopArr[] = [0 => $this->trans('All', [], 'Modules.Prestamassedit.Admin')];

        $filterData = [
            [
                'id' => 'id',
                'field' => 'p.`id_product`',
                'label' => $this->trans('Product ID', [], 'Modules.Prestamassedit.Admin'),
                'type' => 'integer',
                'unique' => true,
                'operators' => ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between'],
                'data' => ['autocomplete' => 'off'],
            ],
            [
                'id' => 'name',
                'field' => 'pl.`name`',
                'label' => $this->trans('Name', [], 'Modules.Prestamassedit.Admin'),
                'type' => 'string',
                'size' => 40,
                'unique' => true,
                'operators' => ['contains', 'not_contains'],
                'data' => ['autocomplete' => 'off'],
            ],
            [
                'id' => 'status',
                'field' => 'p.`active`',
                'label' => $this->trans('Status', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'input' => 'select',
                'values' => $productStatus,
                'operators' => ['equal', 'not_equal'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'visibility',
                'field' => 'p.`visibility`',
                'label' => $this->trans('Visibility', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'input' => 'select',
                'values' => $this->visibilityType,
                'operators' => ['equal', 'not_equal'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'product_type',
                'field' => 'p.`product_type`',
                'label' => $this->trans('Product Type', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'input' => 'select',
                'values' => ProductType::AVAILABLE_TYPES,
                'operators' => ['equal', 'not_equal'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'attributes',
                'field' => 'pa.`id_product_attribute`',
                'label' => $this->trans('Product Attributes', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'input' => 'select',
                'multiple' => true,
                'values' => $productAttrArr,
                'operators' => ['in', 'not_in'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'reference',
                'field' => 'p.`reference`',
                'label' => $this->trans('Reference', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'type' => 'string',
                'size' => 30,
                'operators' => ['contains', 'not_contains'],
                'data' => ['autocomplete' => 'off'],
            ],
            [
                'id' => 'quantity',
                'field' => 'p.`quantity`',
                'label' => $this->trans('Quantity', [], 'Modules.Prestamassedit.Admin'),
                'type' => 'integer',
                'unique' => true,
                'operators' => ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between'],
            ],
            [
                'id' => 'price',
                'field' => 'p.`price`',
                'label' => $this->trans('Price', [], 'Modules.Prestamassedit.Admin'),
                'type' => 'double',
                'validation' => [
                    'min' => 0,
                    'step' => 'any',
                ],
                'unique' => true,
                'operators' => ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between'],
            ],
            [
                'id' => 'available_for_order',
                'field' => 'p.`available_for_order`',
                'label' => $this->trans('Available For Order', [], 'Modules.Prestamassedit.Admin'),
                'input' => 'select',
                'unique' => true,
                'values' => $availableForOrder,
                'operators' => ['equal', 'not_equal'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'feature',
                'field' => 'fp.`id_feature_value`',
                'label' => $this->trans('Features', [], 'Modules.Prestamassedit.Admin'),
                'input' => 'select',
                'unique' => true,
                'values' => $featureValuesArr,
                'multiple' => true,
                'operators' => ['equal', 'not_equal'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'categories',
                'field' => 'cp.`id_category`',
                'label' => $this->trans('Category', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'input' => 'select',
                'values' => $categoryArr,
                'multiple' => true,
                'operators' => ['in', 'not_in'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'supplier',
                'field' => 'p.`id_supplier`',
                'label' => $this->trans('Supplier', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'input' => 'select',
                'values' => $supplierArr,
                'operators' => ['in', 'not_in'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'supplier_reference',
                'field' => 'p.`supplier_reference`',
                'label' => $this->trans('Supplier Reference', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'type' => 'string',
                'size' => 30,
                'operators' => ['contains', 'not_contains', 'is_empty', 'is_not_empty'],
                'data' => ['autocomplete' => 'off'],
            ],
            [
                'id' => 'manufacturer',
                'field' => 'p.`id_manufacturer`',
                'label' => $this->trans('Brand', [], 'Modules.Prestamassedit.Admin'),
                'unique' => true,
                'input' => 'select',
                'values' => $manufacturerArr,
                'operators' => ['in', 'not_in'],
                'data' => ['class' => 'chosen'],
            ],
            [
                'id' => 'date_add',
                'field' => 'p.`date_add`',
                'label' => $this->trans('Add Date', [], 'Modules.Prestamassedit.Admin'),
                'type' => 'date',
                'unique' => true,
                'plugin' => 'datepicker',
                'plugin_config' => [
                    'dateFormat' => 'yy-mm-dd',
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => '-80:+00',
                    'maxDate' => 0,
                ],
                'placeholder' => $this->trans('YYYY-MM-DD', [], 'Modules.Prestamassedit.Admin'),
                'operators' => ['less_or_equal', 'greater_or_equal', 'between'],
                'data' => ['autocomplete' => 'off'],
            ],
            [
                'id' => 'date_upd',
                'field' => 'p.`date_upd`',
                'label' => $this->trans('Update Date', [], 'Modules.Prestamassedit.Admin'),
                'type' => 'date',
                'unique' => true,
                'plugin' => 'datepicker',
                'plugin_config' => [
                    'dateFormat' => 'yy-mm-dd',
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => '-80:+00',
                    'maxDate' => 0,
                ],
                'placeholder' => $this->trans('YYYY-MM-DD', [], 'Modules.Prestamassedit.Admin'),
                'operators' => ['less_or_equal', 'greater_or_equal', 'between'],
                'data' => ['autocomplete' => 'off'],
            ],
        ];

        Media::addJsDef(
            [
                '$filterData' => $filterData,
            ]
        );
    }

    public function setUpdateFormData()
    {
        $idLang = $this->context->language->id;
        $treeCategoriesHelperDefault = new HelperTreeCategories('categories-treeview-default');
        $treeCategoriesHelper = new HelperTreeCategories('categories-treeview-multiple');
        $idCat = Category::getRootCategory()->id_category;
        $treeCategoriesHelperDefault->setRootCategory(Shop::getContext() == Shop::CONTEXT_SHOP ? $idCat : 0)
            ->setInputName('presta_mass_edit{category}{default_category}{value}');
        $treeCategoriesHelper->setRootCategory(Shop::getContext() == Shop::CONTEXT_SHOP ? $idCat : 0)
            ->setUseCheckBox(true)->setInputName('presta_mass_edit{category}{categories}{value}');

        $brands = Manufacturer::getManufacturers(false, $idLang);
        $taxRules = TaxRulesGroup::getTaxRulesGroupsForOptions();

        $attributeGroups = AttributeGroup::getAttributesGroups($idLang);
        $attributes = [];
        foreach ($attributeGroups as $attributeGroup) {
            $key = str_replace(' ', '-', $attributeGroup['name']);
            $attributes[$key] = AttributeGroup::getAttributes($idLang, $attributeGroup['id_attribute_group']);
        }

        $currencies = Currency::getCurrencies();
        $countries = Country::getCountries($idLang);
        $groups = Group::getGroups($idLang);
        $availableCarriers = Carrier::getCarriers($idLang);

        $this->context->smarty->assign([
            'categories_tree_default' => $treeCategoriesHelperDefault->render(),
            'categories_tree' => $treeCategoriesHelper->render(),
            'brands' => $brands,
            'tax_rules' => $taxRules,
            'attributes' => $attributes,
            'currencies' => $currencies,
            'countries' => $countries,
            'groups' => $groups,
            'conditionOptions' => $this->conditionOptions,
            'deliveryTimeOptions' => $this->deliveryTimeOptions,
            'visibilityTypeOptions' => $this->visibilityType,
            'availabilityPreferenceOptions' => $this->availabilityPreferenceOptions,
            'availableCarriers' => $availableCarriers,
            'current_currency_symbol' => $this->context->currency->symbol,
        ]);
    }

    public function ajaxProcessGetFilterProducts()
    {
        $condition = Tools::getValue('whereCond');
        $joinFp = Tools::getValue('joinFp');
        $joinPa = Tools::getValue('joinPa');
        $joinCp = Tools::getValue('joinCp');
        $idLang = $this->context->language->id;
        $idShop = $this->context->shop->id;
        $sql = 'SELECT p.*, img.`id_image` AS id_image, pl.`name`, pl.`link_rewrite` FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $idLang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` img
                ON (img.`id_product` = p.`id_product` AND img.`cover` = 1 AND img.`id_shop` = ' . (int) $idShop . ')';

        if ($joinCp) {
            $sql .= 'INNER JOIN `' . _DB_PREFIX_ . 'category_product` cp
                ON (cp.`id_product` = p.`id_product`)';
        }
        if ($joinPa) {
            $sql .= 'INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                ON (pa.`id_product` = p.`id_product`)';
        }
        if ($joinFp) {
            $sql .= 'INNER JOIN `' . _DB_PREFIX_ . 'feature_product` fp
                ON (fp.`id_product` = p.`id_product`)';
        }
        $sql .= ' WHERE ' . $condition['sql'] . ' Group By p.`id_product`';

        $data = Db::getInstance()->executeS($sql);

        if ($data) {
            $dataWithImg = [];
            foreach ($data as $row) {
                if (isset($row['id_image']) && $row['id_image']) {
                    $row['image_path'] = $this->context->link->getImageLink($row['link_rewrite'], $row['id_image'], 'home_default');
                } else {
                    // Default image fallback
                    $row['image_path'] = _THEME_PROD_DIR_ . Language::getIsoById($idLang) . '-default-home_default.jpg';
                }
                // $id_image = (int) $row['id_image'];
                // $image = new Image($id_image);
                // $img_size = '-' . ImageType::getFormattedName('small') . '.jpg';
                // $path = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getImgPath() . $img_size;
                // $row['image_path'] = $path;
                $dataWithImg[] = $row;
            }

            $result = [
                'status' => 'ok',
                'data' => $dataWithImg,
            ];
        } else {
            $result = [
                'status' => 'ko',
                'msg' => $this->trans('No Product Found', [], 'Modules.Prestamassedit.Admin'),
            ];
        }

        $this->ajaxRender(json_encode($result));
        exit;
    }

    public function ajaxProcessGetFeatureRow()
    {
        $result = [];
        $features = Feature::getFeatures($this->context->language->id);
        $index = Tools::getValue('index');
        $this->context->smarty->assign(
            [
                'features' => $features,
                'index' => $index,
            ]
        );
        $tplPath = 'views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_ajax/feature_row.tpl';
        $tpl = $this->context->smarty->fetch($this->module->getLocalPath() . $tplPath);

        if ($tpl) {
            $result = [
                'status' => 'ok',
                'tpl_data' => $tpl,
            ];
        } else {
            $result = [
                'status' => 'ko',
                'tpl_data' => $this->trans('Something went wrong.', [], 'Modules.Prestamassedit.Admin'),
            ];
        }
        $this->ajaxRender(json_encode($result));
        exit;
    }

    public function ajaxProcessGetFeatureValue()
    {
        $idFeature = Tools::getValue('idFeature');
        $feature_values = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int) $idFeature);
        if ($feature_values) {
            $result = [
                'status' => 'ok',
                'feature_values' => $feature_values,
            ];
        } else {
            $result = [
                'status' => 'ko',
                'feature_values' => $this->trans('Something went wrong.', [], 'Modules.Prestamassedit.Admin'),
            ];
        }
        $this->ajaxRender(json_encode($result));
        exit;
    }

    public function ajaxProcessSearchRelatedProd()
    {
        $keyword = Tools::getValue('keyword');
        $products = Product::searchByName($this->context->language->id, $keyword);

        if ($products) {
            $result = [
                'status' => 'ok',
                'data' => $products,
            ];
        } else {
            $result = [
                'status' => 'ko',
                'msg' => $this->trans('No Product Found', [], 'Modules.Prestamassedit.Admin'),
            ];
        }
        $this->ajaxRender(json_encode($result));
        exit;
    }

    public function ajaxProcessSearchCustomer()
    {
        $keyword = trim(Tools::getValue('keyword'));
        $customers = Customer::searchByName(pSQL($keyword), 15);
        if ($customers) {
            $result = [
                'status' => 'ok',
                'data' => $customers,
            ];
        } else {
            $result = [
                'status' => 'ko',
                'msg' => $this->trans('No Customer Found', [], 'Modules.Prestamassedit.Admin'),
            ];
        }
        $this->ajaxRender(json_encode($result));
        exit;
    }

    public function ajaxProcessGetNewCustomizationField()
    {
        $result = [];
        $field_index = Tools::getValue('field_index');
        $this->context->smarty->assign(['field_index' => $field_index]);

        $tplPath = 'views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_ajax/customization_field.tpl';
        $tpl = $this->context->smarty->fetch($this->module->getLocalPath() . $tplPath);
        if ($tpl) {
            $result = [
                'status' => 'ok',
                'tpl_data' => $tpl,
            ];
        } else {
            $result = [
                'status' => 'ko',
                'tpl_data' => $this->trans('Something went wrong.', [], 'Modules.Prestamassedit.Admin'),
            ];
        }
        $this->ajaxRender(json_encode($result));
        exit;
    }

    public function ajaxProcessGetActiveFormFields()
    {
        $result = [];
        $fields = Tools::getValue('fields');
        $idLang = $this->context->language->id;

        // Check basic fields
        $basicFields = ['name', 'description_short', 'description', 'reference'];
        foreach ($basicFields as $field) {
            $action = $fields['basic'][$field]['action'];
            $values = $fields['basic'][$field]['value'];
            if ($action !== 'off') {
                if (!$this->checkFieldValues($values, $field === 'reference')) {
                    $result['tab_id'] = 'base_update';
                    $result['title'] = $this->trans('Basic Settings', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values'] = $this->setValueForPreview($values);
                }
            }
        }
        $statusAction = $fields['basic']['status']['action'];
        if ($statusAction !== 'off') {
            $result['data']['status']['field'] = $this->fieldNameText['status'];
            $result['data']['status']['values']['lang'][] = '';
            $result['data']['status']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
            if ($statusAction == 1) {
                $result['data']['status']['action'] = $this->trans('Enable All', [], 'Modules.Prestamassedit.Admin');
            } else {
                $result['data']['status']['action'] = $this->trans('Disable All', [], 'Modules.Prestamassedit.Admin');
            }
        }

        // Check category fields
        $categoryFields = ['default_category', 'categories'];
        foreach ($categoryFields as $field) {
            $action = $fields['category'][$field]['action'];
            if ($action !== 'off' && $action !== 'remove_all') {
                if (empty($fields['category'][$field]['value'])) {
                    $result['tab_id'] = 'category_update';
                    $result['title'] = $this->trans('Category', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $values = $fields['category'][$field]['value'];
                    if (is_array($values)) {
                        $catInfo = Category::getCategoryInformation($values);
                    } else {
                        $catInfo = Category::getCategoryInformation([$values]);
                    }
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values']['lang'][] = '';
                    foreach ($catInfo as $cat) {
                        $result['data'][$field]['values']['value'][] = $cat['name'];
                    }
                }
            } elseif ($action == 'remove_all') {
                $result['data'][$field]['field'] = $this->fieldNameText[$field];
                $result['data'][$field]['action'] = $this->actionBtnText[$action];
                $result['data'][$field]['values']['lang'][] = '';
                $result['data'][$field]['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
            }
        }

        // Check brands fields
        $brandAction = $fields['brand_feature']['brands']['action'];
        if ($brandAction !== 'off') {
            $result['data']['brands']['field'] = $this->fieldNameText['brands'];
            $result['data']['brands']['action'] = $this->actionBtnText[$brandAction];
            $result['data']['brands']['values']['lang'][] = '';
            $brandValue = (int) $fields['brand_feature']['brands']['value'];
            if ($brandValue) {
                $result['data']['brands']['values']['value'][] = Manufacturer::getNameById((int) $brandValue);
            } else {
                $result['data']['brands']['values']['value'][] = $this->trans('No brand', [], 'Modules.Prestamassedit.Admin');
            }
        }

        // Check features fields
        $featuresAction = $fields['brand_feature']['features']['action'];
        if ($featuresAction !== 'off' && $featuresAction !== 'remove_all') {
            if (empty($fields['brand_feature']['features']['value'])) {
                $result['tab_id'] = 'brand_and_feature_update';
                $result['title'] = $this->trans('Brands & Features', [], 'Modules.Prestamassedit.Admin');
                $result['error'] = $this->trans('Please fill all active fields', [], 'Modules.Prestamassedit.Admin');
            } else {
                $values = $fields['brand_feature']['features']['value'];

                $featureName = array_key_exists('feature_name', $values) ? (array) $values['feature_name'] : [];
                $featureValue = array_key_exists('feature_value', $values) ? (array) $values['feature_value'] : [];
                $featureCustomValue = array_key_exists('feature_custom_value', $values)
                    ? (array) $values['feature_custom_value'] : [];

                // Re-arrange index keys;
                $featureCustomValues = array_values($featureCustomValue);

                if (!(count($featureName) == count($featureValue)
                    && count($featureValue) == count($featureCustomValues))
                ) {
                    $result['tab_id'] = 'brand_and_feature_update';
                    $result['title'] = $this->trans('Brands & Features', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Choose a value or provide a customized one.', [], 'Modules.Prestamassedit.Admin');
                } else {
                    $result['data']['features']['field'] = $this->fieldNameText['features'];
                    $result['data']['features']['action'] = $this->actionBtnText[$featuresAction];
                    $result['data']['features']['values']['lang'][] = '';

                    foreach ($featureName as $key => $idFeature) {
                        if (!$featureValue[$key] && !$featureCustomValues[$key]) {
                            $result['tab_id'] = 'brand_and_feature_update';
                            $result['title'] = $this->trans('Brands & Features', [], 'Modules.Prestamassedit.Admin');
                            $result['error'] = $this->trans('Choose a value or provide a customized one.', [], 'Modules.Prestamassedit.Admin');
                            break;
                        } else {
                            $feature = Feature::getFeature($idLang, (int) $idFeature);
                            if (trim($featureCustomValues[$key]) !== '') {
                                $value = $feature['name'] . ' : ' . $featureCustomValues[$key];
                            } else {
                                $featureVal = Db::getInstance()->getValue(
                                    'SELECT `value` FROM `' . _DB_PREFIX_ . 'feature_value_lang`
                                    WHERE `id_feature_value` = ' . (int) $featureValue[$key] . '
                                    AND `id_lang` = ' . (int) $idLang
                                );
                                $value = $feature['name'] . ' : ' . $featureVal;
                            }
                            $result['data']['features']['values']['value'][] = $value;
                        }
                    }
                }
            }
        } elseif ($featuresAction == 'remove_all') {
            $result['data']['features']['field'] = $this->fieldNameText['features'];
            $result['data']['features']['action'] = $this->actionBtnText[$featuresAction];
            $result['data']['features']['values']['lang'][] = '';
            $result['data']['features']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }

        // Check related products fields
        $relatedProdAction = $fields['related_prods']['related_prod']['action'];
        if ($relatedProdAction !== 'off' && $relatedProdAction !== 'remove_all') {
            if (empty($fields['related_prods']['related_prod']['value'])) {
                $result['tab_id'] = 'related_prod_update';
                $result['title'] = $this->trans('Related Products', [], 'Modules.Prestamassedit.Admin');
                $result['error'] = $this->trans('Add atleast one related product', [], 'Modules.Prestamassedit.Admin');
            } else {
                $result['data']['related_prod']['field'] = $this->fieldNameText['related_prod'];
                $result['data']['related_prod']['action'] = $this->actionBtnText[$relatedProdAction];
                $result['data']['related_prod']['values']['lang'][] = '';
                $values = $fields['related_prods']['related_prod']['value'];
                if (is_array($values)) {
                    foreach ($values as $value) {
                        $pName = Product::getProductName((int) $value);
                        $result['data']['related_prod']['values']['value'][] = '(' . (int) $value . ') ' . $pName;
                    }
                } else {
                    $pName = Product::getProductName((int) $values);
                    $result['data']['related_prod']['values']['value'][] = '(' . (int) $values . ') ' . $pName;
                }
            }
        } elseif ($relatedProdAction == 'remove_all') {
            $result['data']['related_prod']['field'] = $this->fieldNameText['related_prod'];
            $result['data']['related_prod']['action'] = $this->actionBtnText[$relatedProdAction];
            $result['data']['related_prod']['values']['lang'][] = '';
            $result['data']['related_prod']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }

        // Check pricing fields
        $pricingFields = ['price', 'unit_price', 'wholesale_price'];
        foreach ($pricingFields as $field) {
            $action = $fields['pricing'][$field]['action'];
            if ($action !== 'off') {
                $value = $fields['pricing'][$field]['value'];
                if (empty($value)) {
                    $result['tab_id'] = 'pricing_update';
                    $result['title'] = $this->trans('Pricing', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values']['lang'][] = '';
                    $shopCurrencySymbol = $this->context->currency->symbol;
                    if ($action == 'add_perc') {
                        $value = $value . '% (' . $this->trans('increase in old value', [], 'Modules.Prestamassedit.Admin') . ')';
                    } elseif ($action == 'sub_perc') {
                        $value = $value . '% (' . $this->trans('decrease in old value', [], 'Modules.Prestamassedit.Admin') . ')';
                    } elseif ($action == 'add_amount') {
                        $value = $shopCurrencySymbol . $value . ' (' . $this->trans('increase in old value', [], 'Modules.Prestamassedit.Admin') . ')';
                    } elseif ($action == 'sub_amount') {
                        $value = $shopCurrencySymbol . $value . ' (' . $this->trans('decrease in old value', [], 'Modules.Prestamassedit.Admin') . ')';
                    } else {
                        $value = $shopCurrencySymbol . $value;
                    }
                    $result['data'][$field]['values']['value'][] = $value;
                }
            }
        }
        // tax rule
        $taxRuleAction = $fields['pricing']['id_tax_rules_group']['action'];
        if ($taxRuleAction !== 'off') {
            $result['data']['id_tax_rules_group']['field'] = $this->fieldNameText['id_tax_rules_group'];
            $result['data']['id_tax_rules_group']['action'] = $this->actionBtnText[$taxRuleAction];
            $result['data']['id_tax_rules_group']['values']['lang'][] = '';
            $taxRuleValue = (int) $fields['pricing']['id_tax_rules_group']['value'];
            foreach (TaxRulesGroup::getTaxRulesGroupsForOptions() as $taxRules) {
                if ($taxRules['id_tax_rules_group'] == $taxRuleValue) {
                    $result['data']['id_tax_rules_group']['values']['value'][] = $taxRules['name'];
                    break;
                }
            }
        }
        $onSaleAction = $fields['pricing']['on_sale']['action'];
        if ($onSaleAction !== 'off') {
            $result['data']['on_sale']['field'] = $this->fieldNameText['on_sale'];
            $result['data']['on_sale']['action'] = $this->actionBtnText[$onSaleAction];
            $result['data']['on_sale']['values']['lang'][] = '';
            $result['data']['on_sale']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }

        // Check for specific price fields
        $spAction = $fields['specific_price']['specific_price']['action'];
        if ($spAction !== 'off' && $spAction !== 'remove_all') {
            $spValue = $fields['specific_price']['specific_price']['value'];
            $spHasErrors = $this->validateSpValues($spValue);
            if ($spHasErrors !== '') {
                $result['tab_id'] = 'specific_price_update';
                $result['title'] = $this->trans('Specific Price', [], 'Modules.Prestamassedit.Admin');
                $result['error'] = $spHasErrors;
            } else {
                $result['data']['specific_price']['field'] = $this->fieldNameText['specific_price'];
                $result['data']['specific_price']['action'] = $this->actionBtnText[$spAction];
                $result['data']['specific_price']['values']['lang'][] = '';
                $result['data']['specific_price']['values']['value'] = $this->getSpDataForPreview($spValue);
            }
        } elseif ($spAction == 'remove_all') {
            $result['data']['specific_price']['field'] = $this->fieldNameText['specific_price'];
            $result['data']['specific_price']['action'] = $this->actionBtnText[$spAction];
            $result['data']['specific_price']['values']['lang'][] = '';
            $result['data']['specific_price']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }

        // check quantity fields
        $qtyFields = ['qty', 'min_qty', 'low_stock', 'availability_preference'];
        foreach ($qtyFields as $field) {
            $action = $fields['quantity'][$field]['action'];
            if ($action !== 'off') {
                if (array_key_exists('value', $fields['quantity'][$field])) {
                    if (empty($fields['quantity'][$field]['value']) && $fields['quantity'][$field]['value'] !== '0') {
                        $result['tab_id'] = 'quantity_update';
                        $result['title'] = $this->trans('Quantity', [], 'Modules.Prestamassedit.Admin');
                        $result['error'] = $this->trans('Fill value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                        break;
                    } else {
                        $result['data'][$field]['field'] = $this->fieldNameText[$field];
                        $result['data'][$field]['action'] = $this->actionBtnText[$action];
                        $result['data'][$field]['values']['lang'][] = '';
                        $value = $fields['quantity'][$field]['value'];

                        if ($field == 'availability_preference') {
                            $result['data'][$field]['values']['value'][] = $this->availabilityPreferenceOptions[$value];
                        } else {
                            $result['data'][$field]['values']['value'][] = $value;
                        }
                    }
                } elseif (empty($fields['quantity'][$field]['value'])) {
                    $result['tab_id'] = 'quantity_update';
                    $result['title'] = $this->trans('Quantity', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Fill value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                }
            }
        }
        $mailOnLowStock = $fields['quantity']['mail_on_low_stock']['action'];
        if ($mailOnLowStock !== 'off') {
            $result['data']['mail_on_low_stock']['field'] = $this->fieldNameText['mail_on_low_stock'];
            $result['data']['mail_on_low_stock']['action'] = $this->actionBtnText[$mailOnLowStock];
            $result['data']['mail_on_low_stock']['values']['lang'][] = '';
            $result['data']['mail_on_low_stock']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }
        // Check quantity fields 2
        $qtyFields2 = ['label_in_stock', 'label_out_stock', 'stock_location'];
        foreach ($qtyFields2 as $field) {
            if ($fields['quantity'][$field]['action'] !== 'off') {
                $action = $fields['quantity'][$field]['action'];
                $values = $fields['quantity'][$field]['value'];
                if (!$this->checkFieldValues($values, $field === 'stock_location')) {
                    $result['tab_id'] = 'quantity_update';
                    $result['title'] = $this->trans('Quantity', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values'] = $this->setValueForPreview($values);
                }
            }
        }

        // Check combination fields
        $combinationAction = $fields['combinations']['combination']['action'];
        if ($combinationAction !== 'off' && $combinationAction !== 'remove_all') {
            if (empty($fields['combinations']['combination']['value'])) {
                $result['tab_id'] = 'combinations_update';
                $result['title'] = $this->trans('combinations', [], 'Modules.Prestamassedit.Admin');
                $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
            } else {
                $result['data']['combination']['field'] = $this->fieldNameText['combination'];
                $result['data']['combination']['action'] = $this->actionBtnText[$combinationAction];
                $result['data']['combination']['values']['lang'][] = '';
                $values = (array) $fields['combinations']['combination']['value'];

                $attributes = PrestaProductCombinationHelper::getAttributeDetails($values);
                $combinations = PrestaProductCombinationHelper::crossMultiplyAttributes($attributes);

                foreach ($combinations as $combination) {
                    // @hint:- ['self', 'combineKeyValue'] === self::combineKeyValue()
                    $result['data']['combination']['values']['value'][] = implode(', ', array_map(
                        ['self', 'combineKeyValue'],
                        array_keys($combination),
                        $combination
                    ));
                }
            }
        } elseif ($combinationAction == 'remove_all') {
            $result['data']['combination']['field'] = $this->fieldNameText['combination'];
            $result['data']['combination']['action'] = $this->actionBtnText[$combinationAction];
            $result['data']['combination']['values']['lang'][] = '';
            $result['data']['combination']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }

        // Check shipping fields
        $shippingFields = [
            'width',
            'height',
            'depth',
            'weight',
            'additional_delivery_times',
            'shipping_fee',
            'available_carrier',
        ];
        foreach ($shippingFields as $field) {
            $action = $fields['shipping'][$field]['action'];
            if ($action !== 'off' && $action !== 'remove_all') {
                if (array_key_exists('value', $fields['shipping'][$field])) {
                    if (empty($fields['shipping'][$field]['value']) && $fields['shipping'][$field]['value'] !== '0') {
                        $result['tab_id'] = 'shipping_update';
                        $result['title'] = $this->trans('Shipping', [], 'Modules.Prestamassedit.Admin');
                        $result['error'] = $this->trans('Fill value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                        break;
                    } else {
                        $result['data'][$field]['field'] = $this->fieldNameText[$field];
                        $result['data'][$field]['action'] = $this->actionBtnText[$action];
                        $result['data'][$field]['values']['lang'][] = '';
                        $value = $fields['shipping'][$field]['value'];

                        if ($field == 'additional_delivery_times') {
                            $result['data'][$field]['values']['value'][] = $this->deliveryTimeOptions[$value];
                        } elseif ($field == 'available_carrier') {
                            if (!is_array($value)) {
                                $value = [$value];
                            }
                            foreach ($value as $id_carrier_reference) {
                                $carrier = Carrier::getCarrierByReference(
                                    (int) $id_carrier_reference,
                                    (int) $this->context->language->id
                                );
                                $carrierValue = $carrier->name . ' (' . $carrier->delay . ')';
                                $result['data'][$field]['values']['value'][] = $carrierValue;
                            }
                        } else {
                            $result['data'][$field]['values']['value'][] = $value;
                        }
                    }
                } else {
                    $result['tab_id'] = 'shipping_update';
                    $result['title'] = $this->trans('Shipping', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Fill value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                }
            } elseif ($action == 'remove_all') {
                $result['data'][$field]['field'] = $this->fieldNameText[$field];
                $result['data'][$field]['action'] = $this->actionBtnText[$action];
                $result['data'][$field]['values']['lang'][] = '';
                $result['data'][$field]['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
            }
        }
        $shippingFields2 = ['delivery_in_stock', 'delivery_out_stock'];
        foreach ($shippingFields2 as $field) {
            if ($fields['shipping'][$field]['action'] !== 'off') {
                $action = $fields['shipping'][$field]['action'];
                $values = $fields['shipping'][$field]['value'];
                if (!$this->checkFieldValues($values)) {
                    $result['tab_id'] = 'shipping_update';
                    $result['title'] = $this->trans('Shipping', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values'] = $this->setValueForPreview($values);
                }
            }
        }

        // Check seo fields
        $seoFields = ['meta_title', 'meta_description', 'meta_url'];
        foreach ($seoFields as $field) {
            if ($fields['seo'][$field]['action'] !== 'off') {
                $action = $fields['seo'][$field]['action'];
                $values = $fields['seo'][$field]['value'];
                if (!$this->checkFieldValues($values)) {
                    $result['tab_id'] = 'seo_update';
                    $result['title'] = $this->trans('SEO', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values'] = $this->setValueForPreview($values);
                }
            }
        }

        // Check for options fields
        $optionsFields = ['visibility', 'condition'];
        foreach ($optionsFields as $field) {
            $action = $fields['options'][$field]['action'];
            if ($action !== 'off') {
                if (empty($fields['options'][$field]['value'])) {
                    $result['tab_id'] = 'options_update';
                    $result['title'] = $this->trans('Options', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Fill value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $value = $fields['options'][$field]['value'];
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values']['lang'][] = '';
                    if ($field == 'visibility') {
                        $result['data'][$field]['values']['value'][] = $this->visibilityType[$value];
                    } else {
                        $result['data'][$field]['values']['value'][] = $this->conditionOptions[$value];
                    }
                }
            }
        }
        $availableForOrder = $fields['options']['available_for_order']['action'];
        if ($availableForOrder !== 'off') {
            $result['data']['available_for_order']['field'] = $this->fieldNameText['available_for_order'];
            $result['data']['available_for_order']['action'] = $this->actionBtnText[$availableForOrder];
            $result['data']['available_for_order']['values']['lang'][] = '';
            $result['data']['available_for_order']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }
        $onlineOnly = $fields['options']['online_only']['action'];
        if ($onlineOnly !== 'off') {
            $result['data']['online_only']['field'] = $this->fieldNameText['online_only'];
            $result['data']['online_only']['action'] = $this->actionBtnText[$onlineOnly];
            $result['data']['online_only']['values']['lang'][] = '';
            $result['data']['online_only']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }
        $showCondition = $fields['options']['show_condition']['action'];
        if ($showCondition !== 'off') {
            $result['data']['show_condition']['field'] = $this->fieldNameText['show_condition'];
            $result['data']['show_condition']['action'] = $this->actionBtnText[$showCondition];
            $result['data']['show_condition']['values']['lang'][] = '';
            $result['data']['show_condition']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }
        $optionsFields2 = ['isbn', 'ean13', 'upc', 'mpn', 'tags'];
        foreach ($optionsFields2 as $field) {
            $action = $fields['options'][$field]['action'];
            if ($action !== 'off' && $action !== 'remove_all') {
                $values = $fields['options'][$field]['value'];
                if ($field == 'isbn' && !Validate::isIsbn($values)) {
                    $result['tab_id'] = 'options_update';
                    $result['title'] = $this->trans('Options', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Invalid ISBN', [], 'Modules.Prestamassedit.Admin');
                    break;
                } elseif ($field == 'ean13' && !Validate::isEan13($values)) {
                    $result['tab_id'] = 'options_update';
                    $result['title'] = $this->trans('Options', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Invalid EAN-13', [], 'Modules.Prestamassedit.Admin');
                    break;
                } elseif ($field == 'upc' && !Validate::isUpc($values)) {
                    $result['tab_id'] = 'options_update';
                    $result['title'] = $this->trans('Options', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Invalid UPC Barcode', [], 'Modules.Prestamassedit.Admin');
                    break;
                } elseif ($field == 'mpn' && !Validate::isMpn($values)) {
                    $result['tab_id'] = 'options_update';
                    $result['title'] = $this->trans('Options', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Invalid MPN', [], 'Modules.Prestamassedit.Admin');
                    break;
                } elseif (!$this->checkFieldValues(
                    $values,
                    $field == 'isbn' || $field == 'ean13' || $field == 'upc' || $field == 'mpn'
                )) {
                    $result['tab_id'] = 'options_update';
                    $result['title'] = $this->trans('Options', [], 'Modules.Prestamassedit.Admin');
                    $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
                    break;
                } else {
                    $result['data'][$field]['field'] = $this->fieldNameText[$field];
                    $result['data'][$field]['action'] = $this->actionBtnText[$action];
                    $result['data'][$field]['values'] = $this->setValueForPreview($values);
                }
            } elseif ($action == 'remove_all') {
                $result['data'][$field]['field'] = $this->fieldNameText[$field];
                $result['data'][$field]['action'] = $this->actionBtnText[$action];
                $result['data'][$field]['values']['lang'][] = '';
                $result['data'][$field]['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
            }
        }
        $customizingAction = $fields['options']['customizing']['action'];
        if ($customizingAction !== 'off' && $customizingAction !== 'remove_all') {
            if (empty($fields['options']['customizing']['label'])) {
                $result['tab_id'] = 'options_update';
                $result['title'] = $this->trans('Options', [], 'Modules.Prestamassedit.Admin');
                $result['error'] = $this->trans('Enter value for all selected actions', [], 'Modules.Prestamassedit.Admin');
            } else {
                $result['data']['customizing']['field'] = $this->fieldNameText['customizing'];
                $result['data']['customizing']['action'] = $this->actionBtnText[$customizingAction];
                $result['data']['customizing']['values']['lang'][] = '';

                $labels = $fields['options']['customizing']['label'];
                $types = $fields['options']['customizing']['type'];
                $requireds = $fields['options']['customizing']['required'];
                if (!is_array($labels)) {
                    $labels = [$labels];
                    $types = [$types];
                    $requireds = [$requireds];
                }

                foreach ($labels as $key => $label) {
                    $value = $this->trans('Label', [], 'Modules.Prestamassedit.Admin') . ' : ' . $label;
                    $type = $this->trans('Type', [], 'Modules.Prestamassedit.Admin') . ' : ' . (
                        $types[$key]
                        ? $this->trans('Text', [], 'Modules.Prestamassedit.Admin')
                        : $this->trans('File', [], 'Modules.Prestamassedit.Admin')
                    );
                    $required = $this->trans('Required', [], 'Modules.Prestamassedit.Admin') . ' : ' . (
                        $requireds[$key]
                        ? $this->trans('Yes', [], 'Modules.Prestamassedit.Admin')
                        : $this->trans('No', [], 'Modules.Prestamassedit.Admin')
                    );
                    $result['data']['customizing']['values']['value'][] = $value . ' | ' . $type . ' | ' . $required;
                }
            }
        } elseif ($customizingAction == 'remove_all') {
            $result['data']['customizing']['field'] = $this->fieldNameText['customizing'];
            $result['data']['customizing']['action'] = $this->actionBtnText[$customizingAction];
            $result['data']['customizing']['values']['lang'][] = '';
            $result['data']['customizing']['values']['value'][] = $this->trans('N/A', [], 'Modules.Prestamassedit.Admin');
        }

        if (empty($result)) {
            $result['invalid'] = $this->trans('Nothing to update.', [], 'Modules.Prestamassedit.Admin');
        } else {
            $result['headers'] = [
                'field' => $this->trans('Field', [], 'Modules.Prestamassedit.Admin'),
                'action' => $this->trans('Action', [], 'Modules.Prestamassedit.Admin'),
                'value' => $this->trans('Value', [], 'Modules.Prestamassedit.Admin'),
            ];
        }
        $this->ajaxRender(json_encode($result));
        exit;
    }

    public function combineKeyValue($key, $value)
    {
        return $key . ' : ' . $value;
    }

    public function ajaxProcessUpdateProducts()
    {
        $formData = Tools::getValue('formData');

        // Filter product ids form SQL injections
        $idProducts = json_decode($formData['product_id_list']);
        $idProductList = implode(',', array_map('intval', $idProducts));

        // Basic
        $name = $formData['basic']['name'];
        $description_short = $formData['basic']['description_short'];
        $description = $formData['basic']['description'];
        $reference = $formData['basic']['reference'];
        $status = $formData['basic']['status'];
        if ($name['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $name, 'name');
        }
        if ($description_short['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $description_short, 'description_short');
        }
        if ($description['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $description, 'description');
        }
        if ($reference['action'] !== 'off') {
            PrestaProductUpdate::updateField($idProductList, $reference, 'reference', false);
        }
        if ($status['action'] !== 'off') {
            PrestaProductUpdate::updateToggleField($idProductList, $status, 'active');
        }

        // Category
        $default_category = $formData['category']['default_category'];
        $categories = $formData['category']['categories'];
        if ($default_category['action'] !== 'off') {
            PrestaProductUpdate::updateDefaultCategoryField($idProductList, $default_category);
        }
        if ($categories['action'] !== 'off') {
            PrestaProductUpdate::updateCategoriesField($idProductList, $categories);
        }

        // Brand feature
        $brands = $formData['brand_feature']['brands'];
        $features = $formData['brand_feature']['features'];
        if ($brands['action'] !== 'off') {
            PrestaProductUpdate::updateSelectBoxField($idProductList, $brands, 'id_manufacturer', false);
        }
        if ($features['action'] !== 'off') {
            PrestaProductUpdate::updateFeaturesField($idProductList, $features);
        }

        // Related products
        $related_prods = $formData['related_prods']['related_prod'];
        if ($related_prods['action'] !== 'off') {
            PrestaProductUpdate::updateRelatedProdField($idProductList, $related_prods);
        }

        // Pricing
        $price = $formData['pricing']['price'];
        $unit_price = $formData['pricing']['unit_price'];
        $wholesale_price = $formData['pricing']['wholesale_price'];
        $tax_rule = $formData['pricing']['id_tax_rules_group'];
        $on_sale = $formData['pricing']['on_sale'];
        if ($price['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $price, 'price');
        }
        if ($unit_price['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $unit_price, 'unit_price');
        }
        if ($wholesale_price['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $wholesale_price, 'wholesale_price');
        }
        if ($tax_rule['action'] !== 'off') {
            PrestaProductUpdate::updateSelectBoxField($idProductList, $tax_rule, 'id_tax_rules_group');
        }
        if ($on_sale['action'] !== 'off') {
            PrestaProductUpdate::updateToggleField($idProductList, $on_sale, 'on_sale');
        }

        // Specific price
        $specific_price = $formData['specific_price']['specific_price'];
        if ($specific_price['action'] !== 'off') {
            PrestaProductUpdate::updateSpecificPriceField($idProductList, $specific_price);
        }

        // Shipping
        $width = $formData['shipping']['width'];
        $height = $formData['shipping']['height'];
        $depth = $formData['shipping']['depth'];
        $weight = $formData['shipping']['weight'];
        $additional_delivery_times = $formData['shipping']['additional_delivery_times'];
        $delivery_in_stock = $formData['shipping']['delivery_in_stock'];
        $delivery_out_stock = $formData['shipping']['delivery_out_stock'];
        $additional_shipping_cost = $formData['shipping']['shipping_fee'];
        $available_carrier = $formData['shipping']['available_carrier'];
        if ($width['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $width, 'width', false);
        }
        if ($height['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $height, 'height', false);
        }
        if ($depth['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $depth, 'depth', false);
        }
        if ($weight['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $weight, 'weight', false);
        }
        if ($additional_delivery_times['action'] !== 'off') {
            PrestaProductUpdate::updateSelectBoxField(
                $idProductList,
                $additional_delivery_times,
                'additional_delivery_times',
                false
            );
        }
        if ($delivery_in_stock['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $delivery_in_stock, 'delivery_in_stock');
        }
        if ($delivery_out_stock['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $delivery_out_stock, 'delivery_out_stock');
        }
        if ($additional_shipping_cost['action'] !== 'off') {
            PrestaProductUpdate::updateAmountField($idProductList, $additional_shipping_cost, 'additional_shipping_cost');
        }
        if ($available_carrier['action'] !== 'off') {
            PrestaProductUpdate::updateAvailableCarriersField($idProductList, $available_carrier);
        }

        // Seo
        $meta_title = $formData['seo']['meta_title'];
        $meta_description = $formData['seo']['meta_description'];
        $link_rewrite = $formData['seo']['meta_url'];
        if ($meta_title['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $meta_title, 'meta_title');
        }
        if ($meta_description['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $meta_description, 'meta_description');
        }
        if ($link_rewrite['action'] !== 'off') {
            PrestaProductUpdate::updateLangField($idProductList, $link_rewrite, 'link_rewrite');
        }

        // Options
        $visibility = $formData['options']['visibility'];
        $condition = $formData['options']['condition'];
        $available_for_order = $formData['options']['available_for_order'];
        $online_only = $formData['options']['online_only'];
        $show_condition = $formData['options']['show_condition'];
        $isbn = $formData['options']['isbn'];
        $mpn = $formData['options']['mpn'];
        $upc = $formData['options']['upc'];
        $ean13 = $formData['options']['ean13'];
        $tags = $formData['options']['tags'];
        $customizing = $formData['options']['customizing'];
        if ($visibility['action'] !== 'off') {
            PrestaProductUpdate::updateSelectBoxField($idProductList, $visibility, 'visibility');
        }
        if ($condition['action'] !== 'off') {
            PrestaProductUpdate::updateSelectBoxField($idProductList, $condition, 'condition');
        }
        if ($available_for_order['action'] !== 'off') {
            PrestaProductUpdate::updateToggleField($idProductList, $available_for_order, 'available_for_order');
        }
        if ($online_only['action'] !== 'off') {
            PrestaProductUpdate::updateToggleField($idProductList, $online_only, 'online_only');
        }
        if ($show_condition['action'] !== 'off') {
            PrestaProductUpdate::updateToggleField($idProductList, $show_condition, 'show_condition');
        }
        if ($isbn['action'] !== 'off') {
            PrestaProductUpdate::updateField($idProductList, $isbn, 'isbn', false);
        }
        if ($mpn['action'] !== 'off') {
            PrestaProductUpdate::updateField($idProductList, $mpn, 'mpn', false);
        }
        if ($upc['action'] !== 'off') {
            PrestaProductUpdate::updateField($idProductList, $upc, 'upc', false);
        }
        if ($ean13['action'] !== 'off') {
            PrestaProductUpdate::updateField($idProductList, $ean13, 'ean13', false);
        }
        if ($tags['action'] !== 'off') {
            PrestaProductUpdate::updateTagsField($idProductList, $tags);
        }
        if ($customizing['action'] !== 'off') {
            PrestaProductUpdate::updateCustomizingField($idProductList, $customizing);
        }

        // Combinations
        $combination = $formData['combinations']['combination'];
        if ($combination['action'] !== 'off') {
            PrestaProductUpdate::updateCombinationField($idProductList, $combination);
        }

        // Quantities
        $quantity = $formData['quantity']['qty'];
        $minQuantity = $formData['quantity']['min_qty'];
        $lowStockThreshold = $formData['quantity']['low_stock'];
        $lowStockAlert = $formData['quantity']['mail_on_low_stock'];
        $availabilityPreference = $formData['quantity']['availability_preference'];
        $stockLocation = $formData['quantity']['stock_location'];
        $labelInStock = $formData['quantity']['label_in_stock'];
        $labelOutStock = $formData['quantity']['label_out_stock'];
        if ($quantity['action'] !== 'off') {
            PrestaProductUpdate::updateQuantityField($idProductList, $quantity, 'quantity');
        }
        if ($minQuantity['action'] !== 'off') {
            PrestaProductUpdate::updateQuantityField($idProductList, $minQuantity, 'minimal_quantity');
        }
        if ($lowStockThreshold['action'] !== 'off') {
            PrestaProductUpdate::updateQuantityField($idProductList, $lowStockThreshold, 'low_stock_threshold');
        }
        if ($lowStockAlert['action'] !== 'off') {
            PrestaProductUpdate::updateLowStockAlertField($idProductList, $lowStockAlert);
        }
        if ($availabilityPreference['action'] !== 'off') {
            PrestaProductUpdate::updateAvailabilityPreferenceField($idProductList, $availabilityPreference);
        }
        if ($stockLocation['action'] !== 'off') {
            PrestaProductUpdate::updateStockLocationField($idProductList, $stockLocation, 'location');
        }
        if ($labelInStock['action'] !== 'off') {
            PrestaProductUpdate::updateStockLabelField($idProductList, $labelInStock, 'available_now');
        }
        if ($labelOutStock['action'] !== 'off') {
            PrestaProductUpdate::updateStockLabelField($idProductList, $labelOutStock, 'available_later');
        }

        $this->ajaxRender(json_encode(['message' => $this->trans('Successfully Updated', [], 'Modules.Prestamassedit.Admin')]));
        exit;
    }

    public function checkFieldValues($fieldValues, $singleValue = false)
    {
        if ($singleValue) {
            return trim($fieldValues) !== '';
        } else {
            foreach ($fieldValues as $value) {
                if (trim($value) !== '') {
                    return true;
                }
            }
            return false;
        }
    }

    public function setValueForPreview($values)
    {
        $res = [];
        if (is_array($values)) {
            foreach ($values as $idLang => $value) {
                if (trim($value) !== '') {
                    $lang = Language::getLanguage($idLang);
                    $res['lang'][] = $lang['name'];
                    $res['value'][] = $value;
                }
            }
        } else {
            if (trim($values) !== '') {
                $res['lang'][] = '';
                $res['value'][] = $values;
            }
        }
        return $res;
    }

    public function validateSpValues($spValue)
    {
        $errorMsg = '';
        if (!$spValue['all_customers'] && !array_key_exists('id_customer', $spValue)) {
            $errorMsg = $this->trans('Please select a customer', [], 'Modules.Prestamassedit.Admin');
        }
        if ($spValue['from_quantity'] < 1 || empty($spValue['from_quantity'])) {
            $errorMsg = $this->trans('Minimum number of units must be 1 or above', [], 'Modules.Prestamassedit.Admin');
        }
        if (!$spValue['reduction'] && $spValue['leave_initial_price']) {
            $errorMsg = $this->trans('Atleast one of Impact on price must be set', [], 'Modules.Prestamassedit.Admin');
        }
        if ($spValue['reduction']) {
            if (empty($spValue['reduction_value']) || $spValue['reduction_value'] == 0) {
                $errorMsg = $this->trans('Discount price should be greater than 0', [], 'Modules.Prestamassedit.Admin');
            }
        }
        if (!$spValue['leave_initial_price'] && empty($spValue['price'])) {
            $errorMsg = $this->trans('Retail price should be greater than 0', [], 'Modules.Prestamassedit.Admin');
        }
        return $errorMsg;
    }

    public function getSpDataForPreview($spValue)
    {
        $spPreview = [];
        $shopCurrencySymbol = $this->context->currency->symbol;
        $idLang = $this->context->language->id;

        if (!$spValue['id_currency']) {
            $spPreview[] = $this->trans('Currency', [], 'Modules.Prestamassedit.Admin') . ' : ' . $this->trans('All Currencies', [], 'Modules.Prestamassedit.Admin');
        } else {
            $currencyIsoCode = Currency::getIsoCodeById((int) $spValue['id_currency']);
            $spPreview[] = $this->trans('Currency', [], 'Modules.Prestamassedit.Admin') . ' : ' . $currencyIsoCode;
        }
        if (!$spValue['id_country']) {
            $spPreview[] = $this->trans('Country', [], 'Modules.Prestamassedit.Admin') . ' : ' . $this->trans('All Countries', [], 'Modules.Prestamassedit.Admin');
        } else {
            $country = Db::getInstance()->getValue(
                'SELECT `name` FROM `' . _DB_PREFIX_ . 'country_lang`
                WHERE `id_country` = ' . (int) $spValue['id_country'] . '
                AND `id_lang` = ' . (int) $idLang
            );
            $spPreview[] = $this->trans('Country', [], 'Modules.Prestamassedit.Admin') . ' : ' . $country;
        }
        if (!$spValue['id_group']) {
            $spPreview[] = $this->trans('Group', [], 'Modules.Prestamassedit.Admin') . ' : ' . $this->trans('All Groups', [], 'Modules.Prestamassedit.Admin');
        } else {
            $group = Db::getInstance()->getValue(
                'SELECT `name` FROM `' . _DB_PREFIX_ . 'group_lang`
                WHERE `id_group` = ' . (int) $spValue['id_group'] . '
                AND `id_lang` = ' . (int) $idLang
            );
            $spPreview[] = $this->trans('Group', [], 'Modules.Prestamassedit.Admin') . ' : ' . $group;
        }
        if ($spValue['all_customers']) {
            $spPreview[] = $this->trans('Customer', [], 'Modules.Prestamassedit.Admin') . ' : ' . $this->trans('All Customers', [], 'Modules.Prestamassedit.Admin');
        } else {
            $customer = Db::getInstance()->getValue(
                'SELECT CONCAT(`firstname`, \' \', lastname, \' (\', email, \')\') FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `id_customer` = ' . (int) $spValue['id_customer'] . '
                AND `id_lang` = ' . (int) $idLang
            );
            $spPreview[] = $this->trans('Customer', [], 'Modules.Prestamassedit.Admin') . ' : ' . $customer;
        }
        $spPreview[] = $this->trans('Combination', [], 'Modules.Prestamassedit.Admin') . ' : ' . $this->trans('All Combinations', [], 'Modules.Prestamassedit.Admin');
        $spPreview[] = $this->trans('Units', [], 'Modules.Prestamassedit.Admin') . ' : ' . $spValue['from_quantity'];
        if ($spValue['reduction']) {
            if ($spValue['reduction_type'] == 'amount') {
                $spPreview[] = $this->trans('Discount', [], 'Modules.Prestamassedit.Admin') . ' : '
                    . $shopCurrencySymbol . $spValue['reduction_value'] . ' '
                    . ($spValue['reduction_tax'] ? $this->trans('(tax incl.)', [], 'Modules.Prestamassedit.Admin') : $this->trans('(tax excl.)', [], 'Modules.Prestamassedit.Admin'));
            } else {
                $spPreview[] = $this->trans('Discount', [], 'Modules.Prestamassedit.Admin') . ' : ' . $spValue['reduction_value'] . '%';
            }
        }
        if (!$spValue['leave_initial_price']) {
            $spPreview[] = $this->trans('Retail price (tax excl.)', [], 'Modules.Prestamassedit.Admin') . ' : ' . $shopCurrencySymbol . $spValue['price'];
        }
        if ($spValue['date_from'] == '' && !array_key_exists('date_to', $spValue)) {
            $spDuration = $this->trans('Unlimited', [], 'Modules.Prestamassedit.Admin');
        } elseif (array_key_exists('date_from', $spValue) && !array_key_exists('date_to', $spValue)) {
            $spDuration = $this->trans('From', [], 'Modules.Prestamassedit.Admin') . ' ' . $spValue['date_from'] . ' ' . $this->trans('To Always', [], 'Modules.Prestamassedit.Admin');
        } elseif (!array_key_exists('date_from', $spValue) && array_key_exists('date_to', $spValue)) {
            $spDuration = $this->trans('From Always', [], 'Modules.Prestamassedit.Admin') . ' ' . $this->trans('To', [], 'Modules.Prestamassedit.Admin') . ' ' . $spValue['date_to'];
        } else {
            $spDuration = $this->trans('From', [], 'Modules.Prestamassedit.Admin') . ' ' . $spValue['date_from'] . ' '
                . $this->trans('To', [], 'Modules.Prestamassedit.Admin') . ' ' . $spValue['date_to'];
        }
        $spPreview[] = $this->trans('Duration', [], 'Modules.Prestamassedit.Admin') . ' : ' . $spDuration;

        return $spPreview;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia(false);
        Media::addJsDef([
            'presta_current_url' => $this->context->link->getAdminLink($this->tabClassName),
            'shopCurrencySymbol' => $this->context->currency->symbol,
        ]);
        $this->context->smarty->assign(
            [
                'presta_ajax_loader' => _MODULE_DIR_ . $this->module->name . '/views/img/ajax_loader.svg',
                'presta_success_icon' => _MODULE_DIR_ . $this->module->name . '/views/img/success_icon.svg',
            ]
        );
        $this->context->controller->addCSS(
            [
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/style.css',
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/plugins/dataTables.dataTables.css',
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/plugins/select.dataTables.min.css',
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/plugins/query-builder.default.css',
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/plugins/smart_wizard_all.min.css',
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/plugins/tagify-style.css',
            ]
        );
        $this->context->controller->addJs(
            [
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/main.js',
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/plugins/dataTables.js',
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/plugins/dataTables.select.min.js',
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/plugins/query-builder.standalone.js',
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/plugins/jquery.smartWizard.min.js',
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/plugins/jquery.tagify.js',
                _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            ]
        );
    }
}
