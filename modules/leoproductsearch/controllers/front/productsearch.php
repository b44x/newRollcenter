<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Quick product search by category block
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}
if (file_exists(_PS_MODULE_DIR_ . 'leoproductsearch/classes/LeoSearchProductSearchProvider.php')) {
    require_once(_PS_MODULE_DIR_ . 'leoproductsearch/classes/LeoSearchProductSearchProvider.php');
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
// use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Search\SearchProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;

// use LeoSearch\LeoSearchProductSearchProvider;

class LeoproductsearchproductsearchModuleFrontController extends ModuleFrontController
{

    public $php_self;
    public $instant_search;
    public $ajax_search;
    private $search_string;
    private $search_tag;
    private $search_cate;
    private $search_manufacture;
    private $search_tool = 0; // 0: leosearch, 1: search module, 2: default search

    /**
     * Initialize search controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        $this->instant_search = Tools::getValue('instantSearch');

        $this->ajax_search = Tools::getValue('ajaxSearch');

        $this->search_string = Tools::getValue('q');
        if (!$this->search_string) {
            $this->search_string = Tools::getValue('search_query');
        }
        if (!in_array($this->context->language->iso_code, ['el', 'bg', 'pl'])) {
            preg_match_all('/\w+/', pSQL($this->search_string), $matches);
            if(count($matches) > 0) {
                $this->search_string = implode(' ', $matches[0]);
            } else {
                $this->search_string = '';
            }
            $this->search_string = implode(' ', array_map(function($v){preg_match_all('/[a-zA-Z0-9\s-]+/', $v, $result); return isset($result[0]) ? implode(' ',$result[0]) : '';}, explode(' ', $this->search_string))); 
        }
        $this->search_tag = implode(' ', array_map(function($v){preg_match_all('/[a-zA-Z0-9\s-]+/', $v, $result); return isset($result[0]) ? implode(' ',$result[0]) : '';}, explode(' ', Tools::getValue('tag'))));
        $this->search_cate = (int)Tools::getValue('cate');
        $this->search_manufacture = (int)Tools::getValue('manufac');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->php_self = 'productsearch';

        parent::initContent();

        if ($this->ajax_search && !$this->isTokenValid()) {
            # validate module
            $this->ajaxDie(json_encode(array('products' => array())));
        }
        
//        if (!$this->isTokenValid()) {
//            # validate module
//            Tools::redirect('index.php');
//        }

        if ($this->ajax || $this->ajax_search) {
            $this->ajaxDie(json_encode($this->getAjaxProductSearchVariables()));
        }
        // $products = $this->getProducts();
        $search_products = $this->getProducts();
        // $search_products['products'] = $products;
        $this->context->smarty->assign(array(
            'search_products' => $search_products,
            'search_query' => $this->search_string,
            'nbProducts' => $search_products['pagination']['total_items'],
        ));
        $this->setTemplate('module:leoproductsearch/views/templates/front/search.tpl');
    }

    protected function getAjaxProductSearchVariables()
    {
        $search = $this->getProducts();

        if (!$this->ajax_search) {
            $rendered_products_top = $this->render('catalog/_partials/products-top', array('listing' => $search));
            $rendered_products = $this->render('catalog/_partials/products', array('listing' => $search));
            $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', array('listing' => $search));

            $data = array(
                'rendered_products_top' => $rendered_products_top,
                'rendered_products' => $rendered_products,
                'rendered_products_bottom' => $rendered_products_bottom,
            );
        }

        //DONGND:: update 1.7.2.4
        foreach ($search as $key => $value) {
            if ($key === 'products') {
                $value = $this->prepareProductArrayForAjaxReturn($value);
            }
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Cleans the products array with only whitelisted properties.
     *
     * @return array
     */
    protected function prepareProductArrayForAjaxReturn(array $products)
    {
        $allowed_properties = array('id_product', 'price', 'reference', 'active', 'description_short', 'link',
            'link_rewrite', 'name', 'manufacturer_name', 'position', 'url', 'canonical_url', 'add_to_cart_url',
            'has_discount', 'discount_type', 'discount_percentage', 'discount_percentage_absolute', 'discount_amount',
            'price_amount', 'regular_price_amount', 'regular_price', 'discount_to_display', 'labels', 'main_variants',
            'unit_price', 'tax_name', 'rate', 'cover'
        );
        foreach ($products as $product_key => $product) {
            foreach ($product as $product_property => $data) {
                # VALIDATE MODULE
                unset($data);
                if (!in_array($product_property, $allowed_properties)) {
                    unset($products[$product_key][$product_property]);
                }
            }
        }
        return $products;
    }

    protected function getProducts()
    {
        $searchProvider = new LeoSearchProductSearchProvider(
            $this->context->getTranslator()
        );

        $context = new ProductSearchContext($this->context);
        
        if ($this->ajax_search && Tools::getValue('limit')) {
            $products_per_page = Tools::getValue('limit');
        } else {
            $products_per_page = Configuration::get('PS_PRODUCTS_PER_PAGE');
        }

        //DONGND:: fix to display filter default Sort by
        if (version_compare(_PS_VERSION_, '1.7.3.0', '>')) {
            $sort_oder_by = 'position';
            $sort_oder_way = 'desc';
        } else {
            $sort_oder_by = Tools::getProductsOrder('by');
            $sort_oder_way = Tools::getProductsOrder('way');
        }

        $query = new ProductSearchQuery();
        $query
            ->setSortOrder(new SortOrder('product', $sort_oder_by, $sort_oder_way))
            ->setSearchString($this->search_string)
            ->setSearchTag($this->search_tag)
            ->setResultsPerPage($products_per_page)
            ->setPage(max((int) Tools::getValue('page'), 1))
        ;

        if ($this->search_cate && $this->search_cate != '') {
            $query->setIdCategory((int) $this->search_cate);
        }
        if ($this->search_manufacture && $this->search_manufacture != '') {
            $query->setIdManufacturer((int) $this->search_manufacture);
        }

        // set the sort order if provided in the URL
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::newFromString($encodedSortOrder));
        }

        $result = $searchProvider->runQuery($context, $query);
        
        if(!$result->getProducts()){
            $searchProvider = $this->getProductSearchProviderFromModules($query);
            $this->search_tool = 1;
            if (null === $searchProvider) {
                $searchProvider = new SearchProductSearchProvider(
                    $this->getTranslator()
                );
                $this->search_tool = 2;
            }
            $result = $searchProvider->runQuery($context, $query);
        }
        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());

        $products_for_template = array();

        foreach ($result->getProducts() as $rawProduct) {
            $product_temp = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
            # FIX 1.7.5.0
            if (is_object($product_temp) && method_exists($product_temp, 'jsonSerialize')) {
                $product_temp = $product_temp->jsonSerialize();
            }
            $products_for_template[] = $product_temp;
        }

        if ($this->ajax_search) {
            $searchVariables = array(
                'link' => _PS_BASE_URL_.__PS_BASE_URI__.'index.php?fc=module&module=leoproductsearch&controller=productsearch&cate='.$this->search_cate.'&search_query='.$this->search_string.'&limit='.Configuration::get('PS_PRODUCTS_PER_PAGE').'&leoproductsearch_static_token='.Tools::getValue('leoproductsearch_static_token'),
                'total_items' => isset($this->getTemplateVarPagination($query, $result)['total_items']) ? $this->getTemplateVarPagination($query, $result)['total_items'] : 0,
                'suggest' => $this->getSearchSuggest(),
                'category' => $this->getCategoryProductCount($products_for_template),
                'manufacturer' => $this->getManufacturerProductCount($products_for_template),
                'products' => $products_for_template,
            );
            return $searchVariables;
        }

        // render the facets
        if ($searchProvider instanceof FacetsRendererInterface) {
            // with the provider if it wants to
            $rendered_facets = $searchProvider->renderFacets($context, $result);
            $rendered_active_filters = $searchProvider->renderActiveFilters($context, $result);
        } else {
            // with the core
            $rendered_facets = $this->renderFacets($result);
            $rendered_active_filters = $this->renderActiveFilters($result);
        }

        $pagination = $this->getTemplateVarPagination($query, $result);
        $sort_orders = $this->getTemplateVarSortOrders(
            $result->getAvailableSortOrders(),
            $query->getSortOrder()->toString()
        );

        $sort_selected = false;
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if (isset($order['current']) && true === $order['current']) {
                    $sort_selected = $order['label'];
                    break;
                }
            }
        }

        $searchVariables = array(
            'products' => $products_for_template,
            'pagination' => $pagination,
            'sort_orders' => $sort_orders,
            'sort_selected' => $sort_selected,
            'rendered_facets' => $rendered_facets,
            'rendered_active_filters' => $rendered_active_filters,
            'js_enabled' => $this->ajax,
            'current_url' => $this->updateQueryString(array('q' => $result->getEncodedFacets())),
            'suggest' => $this->getSearchSuggest(),
            'category' => $this->getCategoryProductCount($products_for_template),
            'manufacturer' => $this->getManufacturerProductCount($products_for_template),
        );

        Hook::exec('actionProductSearchComplete', $searchVariables);

        return $searchVariables;
    }
    
    private function getProductSearchProviderFromModules($query)
    {
        $providers = Hook::exec(
            'productSearchProvider',
            ['query' => $query],
            null,
            true
        );

        if (!is_array($providers)) {
            $providers = [];
        }

        foreach ($providers as $provider) {
            if ($provider instanceof ProductSearchProviderInterface) {
                return $provider;
            }
        }
    }

    protected function getTemplateVarPagination(ProductSearchQuery $query, ProductSearchResult $result)
    {
        $pagination = new Pagination();
        $pagination
                ->setPage($query->getPage())
                ->setPagesCount((int) ceil($result->getTotalProductsCount() / $query->getResultsPerPage()));

        $totalItems = $result->getTotalProductsCount();
        $itemsShownFrom = ($query->getResultsPerPage() * ($query->getPage() - 1)) + 1;
        $itemsShownTo = $query->getResultsPerPage() * $query->getPage();
        return array(
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
//            'pages' => array_map(function ($link) {
//                $link['url'] = $this->updateQueryString(array(
//                    'page' => $link['page'],
//                ));
//
//                return $link;
//            }, $pagination->buildLinks()),
            'pages' => $this->leoGetLink($pagination),
            'should_be_displayed' => (count($pagination->buildLinks()) > 3)
        );
    }
    
    public function leoGetLink($pagination)
    {
    
        $links = $pagination->buildLinks();
        foreach ($links as &$link) {
            $link['url'] = $this->updateQueryString(array('page' => $link['page']));
        }

        return $links;
    }

    protected function renderFacets(ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(array($this, 'prepareFacetForTemplate'), $facetCollection->getFacets());

        $activeFilters = array();
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/facets', array(
            'facets' => $facetsVar,
            'js_enabled' => $this->ajax,
            'activeFilters' => $activeFilters,
            'sort_order' => $result->getCurrentSortOrder()->toString(),
            'clear_all_link' => $this->updateQueryString(array('q' => null, 'page' => null))
        ));
    }

    /**
     * Renders an array of active filters.
     *
     * @param array $facets
     *
     * @return string the HTML of the facets
     */
    protected function renderActiveFilters(ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(array($this, 'prepareFacetForTemplate'), $facetCollection->getFacets());

        $activeFilters = array();
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/active_filters', array(
            'activeFilters' => $activeFilters,
            'clear_all_link' => $this->updateQueryString(array('q' => null, 'page' => null))
        ));
    }

    //DONGND:: get layout
    public function getLayout()
    {
        $entity = 'module-leoproductsearch-' . $this->php_self;

        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);

        if ($overridden_layout = Hook::exec('overrideLayoutTemplate', array(
            'default_layout' => $layout,
            'entity' => $entity,
            'locale' => $this->context->language->locale,
            'controller' => $this))) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }

        return $layout;
    }

    protected function getTemplateVarSortOrders(array $sortOrders, $currentSortOrderURLParameter)
    {
        # VALIDATE MODULE
        foreach ($sortOrders as &$sortOrder) {
            $order = $sortOrder->toArray();
            $order['current'] = $order['urlParameter'] === $currentSortOrderURLParameter;
            $order['url'] = $this->updateQueryString(array(
                'order' => $order['urlParameter'],
                'page' => null,
            ));
            $sortOrder = $order;
        }
        return $sortOrders;
//        return array_map(function ($sortOrder) use ($currentSortOrderURLParameter) {
//            $order = $sortOrder->toArray();
//            $order['current'] = $order['urlParameter'] === $currentSortOrderURLParameter;
//            $order['url'] = $this->updateQueryString(array(
//                'order' => $order['urlParameter'],
//                'page' => null,
//            ));
//
//            return $order;
//        }, $sortOrders);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = array(
            'title' => $this->l('Search', 'productsearch'),
            'url' => '',
        );

        return $breadcrumb;
    }
    
    public function isTokenValid()
    {
        if (!Configuration::get('PS_TOKEN_ENABLE')) {
            return true;
        }

        return strcasecmp(Tools::getToken(false), Tools::getValue('leoproductsearch_static_token')) == 0;
    }

    protected function getCategoryProductCount($products){

        $context = new ProductSearchContext($this->context);
        $query = new ProductSearchQuery();

        if (Configuration::get('LEOPRODUCTSEARCH_ENABLE_SUGGEST') || Configuration::get('LEOPRODUCTSEARCH_ENABLE_CATEGORYPRODUCTCOUNT')) {
            $arr_cat = array();
            $i=0;
            foreach ($products as $key => $value) {
                $status = 1;
                for($j=0; $j<count($arr_cat); $j++){
                    if($arr_cat[$j]['id'] == $value['id_category_default']){
                        $status = 0;
                    }
                }
                if ($status) {
                    $arr_cat[$i]['id'] = $value['id_category_default'];
                    $arr_cat[$i]['name'] = $value['category_name'];
                    $arr_cat[$i]['link'] = 'index.php?fc=module&module=leoproductsearch&controller=productsearch&cate='.$value['id_category_default'].'&search_query='.$this->search_string.'&limit='.Configuration::get('PS_PRODUCTS_PER_PAGE').'&leoproductsearch_static_token='.Tools::getValue('leoproductsearch_static_token');
                    $i++;
                }
            }

            for ($i=0; $i < count($arr_cat); $i++) {
                for ($j=0; $j < count($arr_cat); $j++) {
                    if(Tools::strtolower($arr_cat[$i]['name']) < Tools::strtolower($arr_cat[$j]['name'])) {
                        $tg = $arr_cat[$i];
                        $arr_cat[$i] = $arr_cat[$j];
                        $arr_cat[$j] = $tg;
                    }
                }
            }

            for ($i=0; $i < count($arr_cat); $i++) {
                $query
                    ->setSearchString($this->search_string)
                    ->setSearchTag($this->search_tag)
                    ->setIdCategory($arr_cat[$i]['id'])
                ;
                if ($this->search_tool == 0) {
                    $searchProvider = new LeoSearchProductSearchProvider(
                        $this->context->getTranslator()
                    );
                } else {
                    $searchProvider = $this->getProductSearchProviderFromModules($query);
                    if (null === $searchProvider) {
                        $searchProvider = new SearchProductSearchProvider(
                            $this->getTranslator()
                        );
                    }
                }
                $result = $searchProvider->runQuery($context, $query);
                $pagination = $this->getTemplateVarPagination($query, $result);
                $arr_cat[$i]['count'] = $pagination['total_items'];
            }

            return $arr_cat;
        }
        return false;
    }
    protected function getManufacturerProductCount($products){
        $context = new ProductSearchContext($this->context);
        $query = new ProductSearchQuery();

        $arr_manufacturer = array();
        $i=0;
        foreach ($products as $key => $value) {
            $status = 1;
            for($j=0; $j<count($arr_manufacturer); $j++){
                if($arr_manufacturer[$j]['id'] == $value['id_manufacturer']){
                    $status = 0;
                }
            }
            if ($status) {
                $arr_manufacturer[$i]['id'] = $value['id_manufacturer'];
                $arr_manufacturer[$i]['name'] = $value['manufacturer_name'];
                $arr_manufacturer[$i]['link'] = 'index.php?fc=module&module=leoproductsearch&controller=productsearch&manufac='.$value['id_manufacturer'].'&search_query='.$this->search_string.'&limit='.Configuration::get('PS_PRODUCTS_PER_PAGE').'&leoproductsearch_static_token='.Tools::getValue('leoproductsearch_static_token');
                $i++;
            }
        }
        
        for ($i=0; $i < count($arr_manufacturer); $i++) {
            for ($j=0; $j < count($arr_manufacturer); $j++) {
                if(Tools::strtolower($arr_manufacturer[$i]['name']) < Tools::strtolower($arr_manufacturer[$j]['name'])) {
                    $tg = $arr_manufacturer[$i];
                    $arr_manufacturer[$i] = $arr_manufacturer[$j];
                    $arr_manufacturer[$j] = $tg;
                }
            }
        }

        for ($i=0; $i < count($arr_manufacturer); $i++) {
            $query
                ->setSearchString($this->search_string)
                ->setSearchTag($this->search_tag)
                ->setIdManufacturer($arr_manufacturer[$i]['id'])
            ;
            $searchProvider = new LeoSearchProductSearchProvider(
                $this->context->getTranslator()
            );
            $result = $searchProvider->runQuery($context, $query);
            $pagination = $this->getTemplateVarPagination($query, $result);
            $arr_manufacturer[$i]['count'] = $pagination['total_items'];
        }

        return $arr_manufacturer;
    }

    protected function getSearchSuggest(){
        if (Configuration::get('LEOPRODUCTSEARCH_ENABLE_SUGGEST')) {
           $sql = 'SELECT word FROM ' . _DB_PREFIX_ . 'search_word WHERE word = "' . pSQL($this->search_string) . '" GROUP BY word';
            $results = Db::getInstance()->ExecuteS($sql);

            if (!$results) {
                $sql = 'SELECT word FROM ' . _DB_PREFIX_ . 'search_word WHERE word LIKE "' . pSQL($this->search_string) . '%" GROUP BY word ORDER BY word ASC LIMIT 3';
                $results = Db::getInstance()->ExecuteS($sql);
            }
            if (!$results) {
               for ($i=2; $i < Tools::strlen($this->search_string); $i++) {
                    $sql = 'SELECT word FROM ' . _DB_PREFIX_ . 'search_word WHERE word LIKE "' . pSQL(Tools::substr($this->search_string, 0 ,$i * -1)) . '%" GROUP BY word ORDER BY word ASC LIMIT 3';
                    $results = Db::getInstance()->ExecuteS($sql);
                    if ($results) {
                        break;
                    }
                } 
            }
            
            for ($i=0; $i<count($results); $i++){
                $results[$i]['link'] = 'index.php?fc=module&module=leoproductsearch&controller=productsearch&cate='.$this->search_cate.'&search_query='.$results[$i]['word'].'&limit='.Configuration::get('PS_PRODUCTS_PER_PAGE').'&leoproductsearch_static_token='.Tools::getValue('leoproductsearch_static_token');
            }

            return $results; 
        }
        return false;
    }
}
