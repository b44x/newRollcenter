<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use LeoElements\Leo_Helper;
use LeoElements\Plugin;

require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/src/Leo_Helper.php');
require_once(_PS_MODULE_DIR_.'leoelements/includes/plugin.php');
require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoFrameworkHelper.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsContentsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/leoECHelper.php');

use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TextWithRecommendedLengthType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\NewProducts\NewProductsProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\PricesDrop\PricesDropProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;


class Leoelements extends Module implements WidgetInterface
{
    
    public static $leo_txt = [
        'header' => null,
        'header_sticky' => null,
        'home' => null, 'footer' => null,
        'hooks' => [
            'displayBanner' => null,
            'displayNav1' => null,
            'displayNav2' => null,
            'displayTop' => null,
            'displayNavFullWidth' => null,
            'displayHome' => null,
            'displayLeftColumn' => null,
            'displayRightColumn' => null,
            
            'displayFooterBefore' => null,
            'displayFooter' => null,
            'displayFooterAfter' => null,
            
            'displayProductAccessories' => null,
            'displayProductSameCategory' => null,
            'displayLeftColumnProduct' => null,
            'displayRightColumnProduct' => null,
            'displayContactPageBuilder' => null,
            'displayShoppingCartFooter' => null,
            'displayHeaderCategory' => null,
            'displayFooterCategory' => null,
            'displayReassurance' => null,
            'displayFooterProduct' => null,
            'display404PageBuilder' => null],
        'hook_product_list' => [
            'displayBanner' => null,
            'displayNav1' => null,
            'displayNav2' => null,
            'displayTop' => null,
            'displayNavFullWidth' => null,
            'displayHome' => null,
            'displayLeftColumn' => null,
            'displayRightColumn' => null,
            
            'displayFooterBefore' => null,
            'displayFooter' => null,
            'displayFooterAfter' => null,
            
            'displayProductAccessories' => null,
            'displayProductSameCategory' => null,
            'displayFooterProduct' => null,
            'displayLeftColumnProduct' => null,
            'displayRightColumnProduct' => null,
            'displayContactPageBuilder' => null,
            'displayShoppingCartFooter' => null,
            'display404PageBuilder' => null],
            'id_editor' => null
        ];
    
    protected $config_form = false;
    protected $all_active_profile = array();
    protected $use_profiles = array();
    protected $profile_param;

    public function __construct()
    {
        $this->name = 'leoelements';
        $this->tab = 'front_office_features';
        $this->version = '1.0.4';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Leo Elements - Pages And Content Builder');
        $this->description = $this->l('This Module build Multiple Profile to use for: Homepage, Landing Page, Product Page, Category Page, CMS Page and content anywhere with live edit. Manage Hook and live to change style of shop');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->leo_templateFile = 'module:' . $this->name . '/views/templates/hook/page_content.tpl';
        
        if (!file_exists( _PS_MODULE_DIR_. 'leoelements/assets' )) {
            \Tools::ZipExtract(_PS_MODULE_DIR_ . 'leoelements/override/assets.zip', _PS_MODULE_DIR_. 'leoelements');
        }
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if ( !parent::install()) {
            return false;
        }
                
        $langs    = Language::getLanguages();
        $tabvalue = array(
            array(
                'class_name' => 'AdminLeoElements',
                'id_parent'  => Tab::getIdFromClassName('IMPROVE'),
                'module'     => 'leoelements',
                'name'       => 'Leo Elements Creator',
            ),
        );
        foreach ($tabvalue as $tab) {
            $newtab             = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->module     = $tab['module'];
            $newtab->id_parent  = $tab['id_parent'];
            foreach ($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->add(true, false);
            Db::getInstance()->execute(' UPDATE `'._DB_PREFIX_.'tab` SET `icon` = "brush" WHERE `id_tab` = "'.(int)$newtab->id.'"');
        }
        $tabvalue = array();
        include_once dirname(__FILE__) . '/sql/install_tab.php';
        foreach ($tabvalue as $tab) {
            $newtab             = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->module     = $tab['module'];
            $newtab->id_parent  = $tab['id_parent'];
            if(isset($tab['active'])) {
                $newtab->active  = $tab['active'];
            }
            foreach ($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->add(true, false);
            if (isset($tab['icon'])) {
                Db::getInstance()->execute(' UPDATE `' . _DB_PREFIX_ . 'tab` SET `icon` = "' . $tab['icon'] . '" WHERE `id_tab` = "' . (int) $newtab->id . '"');
            }
        }
        include(dirname(__FILE__).'/sql/install.php');

        $this->registerLeoHook();
        
        return true;
    }
    
    public function registerLeoHook()
    {
        $res = true;
        $res &= $this->registerHook('displayHeader');
        $res &= $this->registerHook('displayBanner');
        $res &= $this->registerHook('displayNav1');
        $res &= $this->registerHook('displayNav2');
        $res &= $this->registerHook('displayTop');
        $res &= $this->registerHook('displayNavFullWidth');
        $res &= $this->registerHook('displayLeftColumn');
        $res &= $this->registerHook('displayHome');
        $res &= $this->registerHook('displayRightColumn');
        $res &= $this->registerHook('displayFooterBefore');
        $res &= $this->registerHook('displayFooter');
        $res &= $this->registerHook('displayFooterAfter');
        $res &= $this->registerHook('displayDashboardTop');
        $res &= $this->registerHook('displayBackOfficeHeader');
        $res &= $this->registerHook('backOfficeFooter');
        $res &= $this->registerHook('actionObjectAddAfter');
        $res &= $this->registerHook('actionObjectUpdateAfter');
        $res &= $this->registerHook('actionCmsPageFormBuilderModifier');
        $res &= $this->registerHook('overrideLayoutTemplate');
        $res &= $this->registerHook('moduleRoutes');
        $res &= $this->registerHook('actionAdminControllerSetMedia');
        $res &= $this->registerHook('leoElementConfig');
        
        $res &= $this->registerHook('filterCmsContent');
        $res &= $this->registerHook('filterLeoGuideContent');
        //category layout
        $res &= $this->registerHook('filterCategoryContent');
        $res &= $this->registerHook('displayHeaderCategory');
        $res &= $this->registerHook('displayFooterCategory');
        $res &= $this->registerHook('leoECatConfig');
        $res &= $this->registerHook('actionCategoryFormBuilderModifier');
        $res &= $this->registerHook('actionAfterUpdateCategoryFormHandler');
        $res &= $this->registerHook('actionAfterCreateCategoryFormHandler');
        //product layout
        $res &= $this->registerHook('actionObjectProductUpdateAfter');
        $res &= $this->registerHook('filterProductContent');
        $res &= $this->registerHook('displayReassurance');
        $res &= $this->registerHook('displayFooterProduct');
        $res &= $this->registerHook('displayAdminProductsExtra');
        
        $this->registerHook('actionOutputHTMLBefore');
        return $res;
    }

    public function uninstall()
    {
        Configuration::deleteByName('LEOELEMENTS_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    public static function getInstance()
    {
        static $_instance;
        if (!$_instance) {
            $_instance = new Leoelements();
        }
        return $_instance;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->registerHook('actionOutputHTMLBefore');
        $this->registerHook('leoElementConfig');
        $dashboard = $this->context->link->getAdminLink('AdminLeoElementsDashboard');
        Tools::redirectAdmin( $dashboard );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        Leo_Helper::autoUpdateModule();
        
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
        
        
        $request = $GLOBALS['kernel']->getContainer()->get('request_stack')->getCurrentRequest();
                        
        $controller_name = $this->context->controller->controller_name;
        $controllers = [ 'AdminCategories', 'AdminProducts', 'AdminCmsContent', 'AdminManufacturers', 'AdminSuppliers', 'AdminBlogPost', 'AdminLeoguideGuides' ];
        
        $id_page = '';
        if ( in_array( $controller_name, $controllers ) ) {
            
            if($controller_name == 'AdminCmsContent')
            {
                $id_page = (int) Tools::getValue('id_cms');
                if( !$id_page ){
                        if ( !isset( $request->attributes ) ) { return; }
                        $id_page = (int) $request->attributes->get('cmsPageId');
                }
                $post_type = 'cms';
            }elseif ($controller_name == 'AdminCategories') {
                $id_page = (int) Tools::getValue('id_category');
                if( !$id_page ){
                    if ( !isset( $request->attributes ) ) { return; }
                    $id_page = (int) $request->attributes->get('categoryId');
                }
                $post_type = 'category';
            }elseif ($controller_name == 'AdminProducts') {
                $id_page = (int) Tools::getValue('id_product');
                if( !$id_page ){
                    if ( !isset( $request->attributes ) ) { return; }
                    $id_page = (int) $request->attributes->get('id');
                }
                $post_type = 'product';
            } elseif ($controller_name == 'AdminManufacturers') {
                $id_page = (int) Tools::getValue('id_manufacturer');
                if ( !$id_page ) {
                    if ( !isset( $request->attributes ) ) { return; }
                    $id_page = (int) $request->attributes->get('manufacturerId');
                }
                $post_type = 'manufacturer';
            }elseif($controller_name == 'AdminLeoguideGuides') {
                $id_page = (int) Tools::getValue('id_leoguide_guide');
                if( !$id_page ){
                    return;
                }
                $post_type = 'leoguide';
            }
            
        }
        
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        if (!$id_page) {
                $this->context->controller->addCSS($this->_path.'views/css/back.css');
            
                $url = $this->context->link->getAdminLink('AdminLeoElementsCreator');
                
                $this->context->smarty->assign(array(
                    'urlPageBuilder' => $url,
                    'icon_url' => _MODULE_DIR_.'leoelements/views/img/logo.png',
                ));
        } else {
                $this->context->controller->addCSS($this->_path.'views/css/back.css');
            
                $url = $this->context->link->getAdminLink('AdminLeoElementsCreator').'&post_type=' . $post_type . '&key_related=' . $id_page . '&id_lang=' . $id_lang;

                $this->context->smarty->assign(array(
                        'urlPageBuilder' => $url,
                        'icon_url' => _MODULE_DIR_.'leoelements/views/img/logo.png',
                ));
        }
        
        return $this->fetch(_PS_MODULE_DIR_ .'/'. $this->name . '/views/templates/admin/backoffice_header.tpl');
        
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->autoRestoreSampleData();
        $this->unregisterHook('actionAdminControllerSetMedia');
    }
    
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        Leo_Helper::reset_post_var();
        $GLOBALS['leoelements'] = array();
        $post_type = Tools::getValue( 'post_type' );
        if( (int)Tools::getValue( 'id_post' ) && Leo_Helper::is_preview_mode() && in_array( $post_type, array( 'header', 'home', 'footer', 'hook', 'hook_product_list', 'hook_category_layout', 'hook_product_layout' ) ) ){
            $id_post = (int)Tools::getValue( 'id_post' );
//            self::$leo_txt['id_editor'] = $id_post;
//            Leo_Helper::$id_editor = self::$leo_txt['id_editor'];
            
            Leo_Helper::$id_editor = $id_post;
            Leo_Helper::$post_var['id_editor'] = $id_post;
            
        }
        if (!$this->all_active_profile) {
            $model = new LeoElementsProfilesModel();
            $this->all_active_profile = $model->getAllProfileByShop();
        }
        $this->use_profiles = LeoElementsProfilesModel::getActiveProfile('index', $this->all_active_profile);
        if (!$this->use_profiles) {
            return '';
        }
        if (!isset($this->use_profiles['params'])) {
            return '';
        }
        $this->profile_param = json_decode($this->use_profiles['params'], true);
        if (Tools::getValue('controller') == 'index' || Tools::getValue('controller') == 'appagebuilderhome') {
            if (!empty($this->use_profiles) && isset($this->use_profiles['profile_key'])) {
                $this->context->smarty->assign(array(
                    'leo_class' => $this->use_profiles['profile_key'],
                    'leo_class_home' => (isset($this->use_profiles['active']) && $this->use_profiles['active'] == 1) ? 'leo-active' : '',
                ));
            }
        }
        
        $id_profile = (int)Tools::getValue('id_profile',0);
        if($id_profile){
            foreach ($this->all_active_profile as $key => $profile) {
                if($profile['id_leoelements_profiles'] == $id_profile) {
                    # GET FROM DATABASE
                    $this->use_profiles = $this->all_active_profile[$key];
                }
            }
        }
        
        if(isset($this->use_profiles['params'])) {
            $params = json_decode($this->use_profiles['params'], true);
        } else {
            return '';
        }
        
        $this->use_profiles['displayBanner'] = isset($this->use_profiles['displayBanner']) ? $this->use_profiles['displayBanner'] : 0;
        $this->use_profiles['displayNav1'] = isset($this->use_profiles['displayNav1']) ? $this->use_profiles['displayNav1'] : 0;
        $this->use_profiles['displayNav2'] = isset($this->use_profiles['displayNav2']) ? $this->use_profiles['displayNav2'] : 0;
        $this->use_profiles['displayTop'] = isset($this->use_profiles['displayTop']) ? $this->use_profiles['displayTop'] : 0;
        $this->use_profiles['displayNavFullWidth'] = isset($this->use_profiles['displayNavFullWidth']) ? $this->use_profiles['displayNavFullWidth'] : 0;
        $this->use_profiles['displayHome'] = isset($this->use_profiles['displayHome']) ? $this->use_profiles['displayHome'] : 0;
        $this->use_profiles['displayLeftColumn'] = isset($this->use_profiles['displayLeftColumn']) ? $this->use_profiles['displayLeftColumn'] : 0;
        $this->use_profiles['displayRightColumn'] = isset($this->use_profiles['displayRightColumn']) ? $this->use_profiles['displayRightColumn'] : 0;
        $this->use_profiles['displayFooterBefore'] = isset($this->use_profiles['displayFooterBefore']) ? $this->use_profiles['displayFooterBefore'] : 0;
        $this->use_profiles['displayFooter'] = isset($this->use_profiles['displayFooter']) ? $this->use_profiles['displayFooter'] : 0;
        $this->use_profiles['displayFooterAfter'] = isset($this->use_profiles['displayFooterAfter']) ? $this->use_profiles['displayFooterAfter'] : 0;
        $params['displayHeaderCategory'] = isset($params['displayHeaderCategory']) ? $params['displayHeaderCategory'] : 0;
        $params['displayFooterCategory'] = isset($params['displayFooterCategory']) ? $params['displayFooterCategory'] : 0;
        
        $params['displayReassurance'] = isset($params['displayReassurance']) ? $params['displayReassurance'] : 0;
        $params['displayLeftColumnProduct'] = isset($params['displayLeftColumnProduct']) ? $params['displayLeftColumnProduct'] : 0;
        $params['displayFooterProduct'] = isset($params['displayFooterProduct']) ? $params['displayFooterProduct'] : 0;
        
        self::$leo_txt['hooks']['displayBanner'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayBanner']);
        self::$leo_txt['hooks']['displayNav1'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayNav1']);
        self::$leo_txt['hooks']['displayNav2'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayNav2']);
        self::$leo_txt['hooks']['displayTop'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayTop']);
        self::$leo_txt['hooks']['displayNavFullWidth'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayNavFullWidth']);
        self::$leo_txt['hooks']['displayHome'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayHome']);
        self::$leo_txt['hooks']['displayLeftColumn'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayLeftColumn']);
        self::$leo_txt['hooks']['displayRightColumn'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayRightColumn']);
        self::$leo_txt['hooks']['displayFooterBefore'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayFooterBefore']);
        self::$leo_txt['hooks']['displayFooter'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayFooter']);
        self::$leo_txt['hooks']['displayFooterAfter'] = LeoElementsContentsModel::getIdByKey($this->use_profiles['displayFooterAfter']);
        self::$leo_txt['hooks']['displayHeaderCategory'] = LeoElementsContentsModel::getIdByKey($params['displayHeaderCategory']);
        self::$leo_txt['hooks']['displayFooterCategory'] = LeoElementsContentsModel::getIdByKey($params['displayFooterCategory']);
        
        self::$leo_txt['hooks']['displayLeftColumnProduct'] = LeoElementsContentsModel::getIdByKey($params['displayLeftColumnProduct']);
        self::$leo_txt['hooks']['displayReassurance'] = LeoElementsContentsModel::getIdByKey($params['displayReassurance']);
        self::$leo_txt['hooks']['displayFooterProduct'] = LeoElementsContentsModel::getIdByKey($params['displayFooterProduct']);
        
        if(Tools::getIsset('cw_hook') && Tools::getValue('cw_hook') && Tools::getIsset('id_post') && in_array(Tools::getValue('id_post'), self::$leo_txt['hooks'])) {
            $this->use_profiles[ Tools::getValue('cw_hook')] = 0;
            self::$leo_txt['hooks'][ Tools::getValue('cw_hook')] = null;
            foreach (self::$leo_txt['hooks'] as $key => &$hook) {
                if($hook == Tools::getValue('id_post')){
                    $hook = null;
                }
            }
        }
        
        $post_type = Tools::getValue( 'post_type' );
        if( (int)Tools::getValue( 'id_post' ) && Leo_Helper::is_preview_mode() && in_array( $post_type, array( 'hook' ) ) ){
            $id_post = (int)Tools::getValue( 'id_post' );
            $key_related = Tools::getValue( 'key_related' );
            
            self::$leo_txt['hooks'][$key_related] = $id_post;
            self::$leo_txt['id_editor'] = $id_post;
        }
   
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
//        $this->context->controller->addCSS($this->_path.'views/css/front.css');
        $uri = 'modules/leoelements/views/css/common.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8001));
        
        $uri = 'modules/leoelements/views/css/front.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8002));

        // cms
        $this->context->controller->addCSS($this->_path.'assets/lib/eicons/css/elementor-icons.min.css');
        
        $this->context->controller->addCSS($this->_path.'assets/lib/font-awesome/css/font-awesome.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/font-awesome/css/fontawesome.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/font-awesome/css/regular.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/font-awesome/css/solid.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/font-awesome/css/brands.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/line-awesome/line-awesome.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/pe-icon/Pe-icon-7-stroke.min.css');
        
        $this->context->controller->addCSS($this->_path.'assets/lib/animations/animations.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/flatpickr/flatpickr.min.css');
        
        $this->context->controller->addCSS($this->_path.'assets/css/widgets.min.css');
        $this->context->controller->addCSS($this->_path.'assets/lib/e-select2/css/e-select2.min.css');
        
        $this->context->controller->addCSS($this->_path.'assets/css/frontend.css');
        $this->context->controller->addCSS($this->_path.'assets/css/editor-preview.min.css');
        $this->context->controller->addCSS($this->_path.'assets/css/leo-preview.min.css');
        $this->context->controller->addCSS($this->_path.'assets/css/function.css');
        
        
        $this->context->controller->addJS($this->_path.'assets/js/frontend-modules.min.js');
        $this->context->controller->addJS($this->_path.'assets/lib/waypoints/waypoints.min.js');
        $this->context->controller->addJS($this->_path.'assets/lib/flatpickr/flatpickr.min.js');
        $this->context->controller->addJS($this->_path.'assets/lib/imagesloaded/imagesloaded.min.js');
        $this->context->controller->addJS($this->_path.'assets/lib/jquery-numerator/jquery-numerator.min.js');
        $this->context->controller->addJS($this->_path.'assets/lib/swiper/swiper.min.js');
        $this->context->controller->addJS($this->_path.'assets/lib/dialog/dialog.min.js');
        $this->context->controller->addJS($this->_path.'assets/lib/countdown/countdown.min.js');
        $this->context->controller->addJS($this->_path.'assets/js/widgets.js');
        
        // leotheme add more
        $this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll'));
        $this->context->controller->addJS($this->_path.'assets/lib/slick/slick.js');
        $this->context->controller->addJS($this->_path.'assets/js/frontend-modules.min.js');
        $this->context->controller->addJS($this->_path.'assets/js/frontend.js');
        $this->context->controller->addJS($this->_path.'assets/lib/inline-editor/js/inline-editor.min.js');
        $this->context->controller->addJS($this->_path.'assets/js/instafeed.min.js');
        $this->context->controller->addJS($this->_path.'assets/js/function.js');
        $this->context->controller->addJS($this->_path.'views/js/countdown.js');
        
        
        
        $languages = [];
        $data_languages = $this->getListLanguages();

        $currencies = [];
        $data_currencies = $this->getListCurrencies();

        if( $data_languages ){
                foreach( $data_languages['languages'] as $language ){
                        $languages[$language['id_lang']] = $this->context->link->getLanguageLink($language['id_lang']);
                }
                $languages['length'] = count( $data_languages['languages'] );
        }

        if( $data_currencies ){
                foreach( $data_currencies['currencies'] as $currency ){
                        $currencies[$currency['id']] = $currency['url'];
                }
                $currencies['length'] = count( $data_currencies['currencies'] );
        }
        
        Media::addJsDef([
            'elementorFrontendConfig' => Plugin::instance()->frontend->get_init_settings(),
            'opLeoElements' => [
                'ajax' => $this->context->link->getModuleLink('leoelements', 'ajax', ['token'=>Tools::getToken()], null, null, null, true),
                'contact' => $this->context->link->getModuleLink('leoelements', 'contact', [], null, null, null, true),
                'subscription' => $this->context->link->getModuleLink('leoelements', 'subscription', [], null, null, null, true),
                'cart' => $this->context->link->getModuleLink('leoelements', 'cart', [], null, null, null, true),
                'all_results_product' => $this->l('View all product results'),
                'no_products' => $this->l('No products found'),
                'languages' => $languages,
                'currencies' => $currencies
            ]
        ]);
        
        
        $controller = Dispatcher::getInstance()->getController();
        Context::getContext()->cookie->leoelement_controller = $controller;
        Context::getContext()->cookie->leoelement_controller_id = Tools::getValue('id_'.$controller);
        $pl_layout = '';
        //check product list
        switch ($controller) {
          case "category":
            $id_category = Tools::getValue('id_category');
            $category = new Category($id_category);
            $clayout = '';
            //get category layout
            if(Tools::getIsset('layout')) {
                $clayout = Tools::getValue('layout');
            } else {
                if (Context::getContext()->isMobile() && isset($category->leoe_layout_mobile) && $category->leoe_layout_mobile) {
                    $clayout = $category->leoe_layout_mobile;
                } else if(Context::getContext()->isTablet() && isset($category->leoe_layout_tablet) && $category->leoe_layout_tablet) {
                    $clayout = $category->leoe_layout_tablet;
                } else {
                    if(isset($category->leoe_layout) && $category->leoe_layout) {
                        $clayout = $category->leoe_layout;
                    } else {
                        //get from profile
                        if (Context::getContext()->isMobile() && isset($params['category_layout_mobile']) && $params['category_layout_mobile']) {
                            $clayout = $params['category_layout_mobile'];
                        } else if(Context::getContext()->isTablet() && isset($params['category_layout_tablet']) && $params['category_layout_tablet']) {
                            $clayout = $params['category_layout_tablet'];
                        } else {
                            $clayout = $params['category_layout'];
                        }
                    }
                }
            }

            //found category layout
            if($clayout) {
                $params['c_config'] = $this->getCategoryConfig($clayout);
                if (Context::getContext()->isMobile() && $params['c_config']['product_list_mobile']) {
                    $pl_layout = $params['c_config']['product_list_mobile'];
                } else if(Context::getContext()->isTablet() && $params['c_config']['product_list_tablet']) {
                    $pl_layout = $params['c_config']['product_list_tablet'];
                } else {
                    $pl_layout = $params['c_config']['product_list'];
                }
            }

            if(!isset($params['c_config']) || !$params['c_config']) {
                $params['c_config'] = leoECHelper::defaultConfig()['category'];
            }
            break;
          case "pricesdrop":
            if ($params['pricedrop_layout']) {
                $pl_layout = $params['pricedrop_layout'];
            }
            break;
          case "newproducts":
            if ($params['newproduct_layout']) {
                $pl_layout = $params['newproduct_layout'];
            }
            break;
          case "bestsales":
            if ($params['bestsales_layout']) {
                $pl_layout = $params['bestsales_layout'];
            }
            break;
          case "search":
            if ($params['search_layout']) {
                $pl_layout = $params['search_layout'];
            }
            break;
          case "manufacture":
            if ($params['manufacture_layout']) {
                $pl_layout = $params['manufacture_layout'];
            }
            break;  
          default:
            
        }
        //find from profile
        if(!$pl_layout) {
            if (Context::getContext()->isMobile() && $params['productlist_layout_mobile']) {
                $pl_layout = $params['productlist_layout_mobile'];
            } else if(Context::getContext()->isTablet() && $params['productlist_layout_tablet']) {
                $pl_layout = $params['productlist_layout_tablet'];
            } else {
                $pl_layout = $params['productlist_layout'];
            }
        }

        if($pl_layout || Tools::getIsset('plist_key')) {
            if(Tools::getIsset('plist_key')) {
               $pl_layout = Tools::getValue('plist_key');
            }

            $params['pl_config'] = $this->getProductListConfig($pl_layout);
            $params['pl_key'] = $pl_layout;
            if (file_exists(_PS_THEME_DIR_.'modules/leoelements/views/templates/front/products/'.$pl_layout.'.tpl')) {
                $params['pl_url'] = 'module:/leoelements/views/templates/front/products/'.$pl_layout.'.tpl';
            }
        }

        if (!isset($params['pl_config']) || !$params['pl_config']) {
            $params['pl_config'] = leoECHelper::defaultConfig()['product_list'];
        }
        Media::addJsDef([
            'opLeoElementsList' => $params['pl_config'],
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
        ]);

        $use_leo_gallery = 0;
        if (isset($params['thumb_product_layout']['use_leo_gallery'])) {
            $use_leo_gallery = $params['thumb_product_layout']['use_leo_gallery'];
        } elseif (Context::getContext()->isTablet() && isset($params['thumb_product_layout_tablet']['use_leo_gallery'])) {
            $use_leo_gallery = $params['thumb_product_layout_tablet']['use_leo_gallery'];
        } elseif (Context::getContext()->isMobile() && isset($params['thumb_product_layout_mobile']['use_leo_gallery'])) {
            $use_leo_gallery = $params['thumb_product_layout_mobile']['use_leo_gallery'];
        }
        if ($controller == 'product') {
            if(Tools::getIsset('layout')) {
                $params['pdeail_layout'] = Tools::getValue('layout');
                if (Module::isInstalled('leogallery')) {
                    // check for demo layout when gallery layout is used
                    require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductsModel.php');
                    $layout_data = LeoElementsProductsModel::getProductProfileByKey($params['pdeail_layout']);
                    if (strpos($layout_data['class_detail'], 'product_image_gallery') === false) {
                        $params_layout = json_decode($layout_data['params'], true);
                        if (isset($params_layout['gridLeft'])) {
                            foreach ($params_layout['gridLeft'] as $group) {
                                if (isset($group['columns'])) {
                                    foreach ($group['columns'] as $columns) {
                                        if (isset($columns['sub'])) {
                                            foreach ($columns['sub'] as $sub) {
                                                $layout_detail_data[] = $data_column = $sub['form'];
                                                break;
                                            }
                                        }
                                        if ($data_column) break;
                                    }
                                }
                                if ($data_column) break;
                            }
                        }
                        $use_leo_gallery = (isset($data_column['use_leo_gallery']) && $data_column['use_leo_gallery']) ? 1 : 0;
                    } else {
                        $use_leo_gallery = 0;
                    }
                }
            } else {
                $product = new Product(Tools::getValue('id_product'));
                if(isset($product->leoe_layout) && $product->leoe_layout && $product->leoe_layout != 'default') {                    
                    $params['pdeail_layout'] = $product->leoe_layout;
                }
                if(Context::getContext()->isTablet() && isset($product->leoe_layout_tablet) && $product->leoe_layout_tablet && $product->leoe_layout_tablet != 'default') {
                    $params['pdeail_layout'] = $product->leoe_layout_tablet;
                }
                if (Context::getContext()->isMobile() && isset($product->leoe_layout_mobile) && $product->leoe_layout_mobile && $product->leoe_layout_mobile != 'default') {
                    $params['pdeail_layout'] = $product->leoe_layout_mobile;
                }
            }
        }
        if(!isset($params['pdeail_layout']) || !$params['pdeail_layout']) {
            $params['pdeail_layout'] = $params['productdetail_layout'];
            if(Context::getContext()->isTablet() && $params['productdetail_layout_tablet']) {
                $params['pdeail_layout'] = $params['productdetail_layout_tablet'];
            }
            if (Context::getContext()->isMobile() && $params['productdetail_layout_mobile']) {
                $params['pdeail_layout'] = $params['productdetail_layout_mobile'];
            }
        }
        $params['pdetail_url'] = '';
        if(isset($params['pdeail_layout']) && $params['pdeail_layout'] && file_exists(_PS_THEME_DIR_.'modules/leoelements/views/templates/front/details/'.$params['pdeail_layout'].'.tpl')) {
            $params['pdetail_url'] = 'module:/leoelements/views/templates/front/details/'.$params['pdeail_layout'].'.tpl';
        }

        if (!$params['pdeail_layout']) {
            $use_leo_gallery = $params['use_leo_gallery_default'];
        }
        if ($use_leo_gallery && !Module::isInstalled('leogallery')) {
            $use_leo_gallery = 0;
        }
        if (!$use_leo_gallery) {
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->context->controller->addJS($this->_path.'views/js/jquery.elevateZoom-3.0.8.min.js');
        }

        Media::addJsDef([
            'use_leo_gallery' => $use_leo_gallery ? 1 : 0,
        ]);

        # LOAD FONT
        $uri = 'modules/leoelements/views/css/'.'font-custom-'.$this->use_profiles['profile_key'].'-'.Context::getContext()->shop->id.'.css';
        if (file_exists(_PS_THEME_DIR_.$uri)) {
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8001));
        }
        $uri = leoECHelper::getCssDir().'profiles/'.$this->use_profiles['profile_key'].'.css';
        if ((file_exists(_PS_THEME_DIR_.$uri) && filesize(_PS_THEME_DIR_.$uri)) || (file_exists(_PS_THEME_DIR_.'assets/css/'.$uri) && filesize(_PS_THEME_DIR_.'assets/css/'.$uri))) {
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8004));
        }

        $uri = leoECHelper::getJsDir().'profiles/'.$this->use_profiles['profile_key'].'.js';
        if (file_exists(_PS_THEME_DIR_.$uri)) {
            $this->context->controller->addJS($uri);
        }
        foreach (array('header', 'content', 'footer') as $csp) {
            $uri = leoECHelper::getCssDir().'positions/'.$csp.$this->use_profiles[$csp].'.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8004));
            $uri = leoECHelper::getJsDir().'positions/'.$csp.$this->use_profiles[$csp].'.js';
            if ((file_exists(_PS_THEME_DIR_.$uri) && filesize(_PS_THEME_DIR_.$uri)) || (file_exists(_PS_THEME_DIR_.'assets/css/'.$uri) && filesize(_PS_THEME_DIR_.'assets/css/'.$uri))) {
                $this->context->controller->addJS($uri);
            }
        }
        
        if (file_exists(_PS_THEME_DIR_.$uri)) {
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
        }
        if (file_exists(leoECHelper::getConfigDir('_PS_THEME_DIR_').leoECHelper::getCssDir().'custom.css')) {
            $uri = leoECHelper::getCssDir().'custom.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8003));
        }

        // check sitemap
        if ($controller == 'sitemap') {
            $profiles = $this->all_active_profile;
            require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoFriendlyUrl.php');
            if (!empty($profiles)) {
                foreach ($profiles as $key => &$profile) {
                    $leo_friendly_url = LeoFriendlyUrl::getInstance();
                    $link = $this->context->link;
                    $idLang = $this->context->language->id;
                    $idShop = null;
                    $relativeProtocol = false;
                    $params_profile = json_decode($profile['params'], true);
                    if (!isset($params_profile['show_sitemap']) || !$params_profile['show_sitemap'] || !isset($profile['friendly_url']) || !$profile['friendly_url']) {
                        unset($profiles[$key]);
                    } else {
                        $url = $link->getBaseLink($idShop, null, $relativeProtocol).$leo_friendly_url->getLangLink($idLang, null, $idShop).$profile['friendly_url'].'.html';
                        $profile['url'] = $url;

                    }   
                }
            }

            $this->context->smarty->assign('leo_sitemap_profiles', $profiles);
        }

        if ($controller == 'index' || $controller == 'appagebuilderhome') {
            $this->context->smarty->assign(array(
                'fullwidth_hook' => $params['fullwidth_index_hook'],
            ));
        } else {
            $this->context->smarty->assign(array(
                'fullwidth_hook' => $params['fullwidth_other_hook'],
            ));
        }

        // breadcrumbImage
        $params['leobcimg'] = $this->getBreadcrumbImage($params);
        $panel_tool = Configuration::get('LEOELEMENTS_PANEL_TOOL');
        if($panel_tool) {
            $uri = 'modules/leoelements/views/css/paneltool.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            $this->context->controller->addJS($this->_path.'views/js/webfont.js');
            $this->context->controller->addJS($this->_path.'views/js/paneltool.js');
            
            $uri = 'modules/leoelements/views/css/colorpicker/css/colorpicker.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));


            $uri = 'modules/leoelements/views/js/colorpicker/js/colorpicker.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));

            $product_layout_link = '';
            if (Tools::getValue('id_product')) {
                $id_product = Tools::getValue('id_product');
            } else {
                $sql = 'SELECT p.`id_product`
                        FROM `'._DB_PREFIX_.'product` p
                        '.Shop::addSqlAssociation('product', 'p').'
                AND product_shop.`visibility` IN ("both", "catalog")
                AND product_shop.`active` = 1
                ORDER BY p.`id_product` ASC';
                $first_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                $id_product = isset($first_product['id_product']) ? $first_product['id_product'] : '';
            }
            if (Tools::getValue('id_category')) {
                $id_category = Tools::getValue('id_category');
            } else {
                $first_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `id_category` FROM `' . _DB_PREFIX_ . 'category` c
                    WHERE `active` = 1 AND `id_category` > 2');
                $id_category = isset($first_category['id_category']) ? $first_category['id_category'] : '';
            }
            
            $leo_panel['product_link'] = $id_product ? $this->context->link->getProductLink($id_product, null, null, null, null, null) : '#';
            $leo_panel['category_link'] = $id_category ? $this->context->link->getCategoryLink($id_category) : '#';
            $leo_panel['font_url'] = _PS_BASE_URL_.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/assets/fonts/';
            $leo_panel['profile'] = $this->all_active_profile;
            $leo_panel['pdetail'] = LeoElementsProfilesModel::getAllProductDetail();
            $leo_panel['plist'] = LeoElementsProfilesModel::getAllProductList();

            $leo_panel['category'] = LeoElementsProfilesModel::getAllCategory();
            $leo_panel['positions'] = LeoElementsProfilesModel::getAllPosition();
            
            foreach ($leo_panel['positions'] as $key => &$lp_positions) {
                foreach ($lp_positions as $key => &$lp_position) {
                    $lp_id_post = '';
                    $lp_params = json_decode($lp_position['params'], true);
                    if (is_array($lp_params)) {
                        foreach ($lp_params as $lp_key => $lp_param) {
                            $lp_id_post = LeoElementsContentsModel::getIdByKey($lp_param);
                        }
                    }
                    $lp_position['demo_url'] = '';
                    if($lp_id_post)
                    {
                        $lp_position['demo_url'] = Context::getContext()->link->getModuleLink('leoelements', 'creator') . '?post_type=hook&id_post='.$lp_id_post.'&id_lang='.$this->context->language->id;
                    }
                }
                
            }

            $leo_panel['fonts'] = array(array(
                        'label' => $this->l('Available fonts'),
                        'options' => LeoElementsProfilesModel::getFontFamily()
                    ),
                    array(
                        'label' => $this->l('Uploaded fonts'),
                        'options' => LeoElementsProfilesModel::getFontFamilyUploaded()
                    ),
                    array(
                        'label' => $this->l('Google fonts'),
                        'options' => LeoElementsProfilesModel::getFontFamilyGoogle()
                    ));
            $leo_panel['font_configs'] = array(
                'font_family_base'=>$this->l('Font Base'), 'font_family_heading'=>$this->l('Font Family Heading'), 'font_family_slider'=>$this->l('Font Slider'), 'font_family_heading'=>$this->l('Font Heading'), 'font_family_senary'=>$this->l('Font Senary'), 'font_family_septenary'=>$this->l('Font Septenary'));

            $leo_panel['color_configs'] = array(
                array(
                    'label' => $this->l('Theme Color'),
                    'config' => array('theme_color_default'=>$this->l('Color Default'), 
                        // 'theme_color_secondary'=>$this->l('Color Secondary'), 'theme_color_tertiary'=>$this->l('Color Tertiary'), 'theme_color_senary'=>$this->l('Color Senary')
                    ),
                ),
                array(
                    'label' => $this->l('Text Color'),
                    'config' => array('headings_color'=> $this->l('Headings Color'),'link_color'=>$this->l('Link Color'), 'link_color_hover'=>$this->l('Link Hover Color'), 'text_color'=>$this->l('Text Color'), 'price_color'=>$this->l('Price Color')),
                ),
                array(
                    'label' => $this->l('Button Color'),
                    'config' => array('btn_bg'=>$this->l('Button Background'), 'btn_bg_hover'=>$this->l('Button Hover Background'), 'btn_color'=>$this->l('Button Color'), 'btn_color_hover'=>$this->l('Button Hover Color')),
                ),
                array(
                    'label' => $this->l('Product Items'),
                    'config' => array('product_background'=>$this->l('Product Background'), 'product_name_color'=>$this->l('Product Name Color'), 'product_name_color_hover'=>$this->l('Product Name Hover Color'), 'product_price_color'=>$this->l('Product Price Color'), 'product_regular_price_color'=>$this->l('Product Regular Price Color'), 'product_button_bg'=>$this->l('Product Button Background'), 'product_button_bg_hover'=>$this->l('Product Button Hover Background'), 'product_button_color'=>$this->l('Product Button Color'), 'product_button_color_hover'=>$this->l('Product Button Hover Color')),
                ),
                array(
                    'label' => $this->l('Product Flags'),
                    'config' => array('on_sale_badge_background'=>$this->l('On Sale Badge Background'), 'on_sale_badge_color'=>$this->l('On Sale Badge Color'), 'new_badge_background'=>$this->l('New Badge Background'), 'new_badge_color'=>$this->l('New Badge Color'), 'sale_badge_background'=>$this->l('Sale Badge Background'), 'sale_badge_color'=>$this->l('Sale Badge Color'), 'online_only_background'=>$this->l('Online Only Background'), 'online_only_color'=>$this->l('Online Only Color'), 'pack_badge_background'=>$this->l('Pack Badge Background'), 'pack_badge_color'=>$this->l('Pack Badge Color')),
                ),
                array(
                    'label' => $this->l('Boxes (including Sidebars)'),
                    'config' => array('block_background'=>$this->l('Block Background'), 'block_inner_background'=>$this->l('Block Inner Background'), 'block_heading_bg'=>$this->l('Block Heading Background'), 'block_heading_color'=>$this->l('Block Heading Color')),
                )
            );

            $this->context->smarty->assign(array(
                'leo_panel' => $leo_panel
            ));
        }
        
        $page_name = leoECHelper::getPageName();
        if (version_compare(Configuration::get('PS_INSTALL_VERSION'), '8.0.0', '>=')
            || version_compare(Configuration::get('PS_VERSION_DB'), '8.0.0', '>=')
            || version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $page = $this->smarty->smarty->getTemplateVars('page');
        } else {
            $page = $this->smarty->smarty->getVariable('page')->value;
        }
        if (isset($this->use_profiles['meta_title']) && $this->use_profiles['meta_title'] && $page_name == 'index') {
            $page['meta']['title'] = $this->use_profiles['meta_title'];
        }
        if (isset($this->use_profiles['meta_description']) && $this->use_profiles['meta_description'] && $page_name == 'index') {
            $page['meta']['description'] = $this->use_profiles['meta_description'];
        }
        if (isset($this->use_profiles['meta_keywords']) && $this->use_profiles['meta_keywords'] && $page_name == 'index') {
            $page['meta']['keywords'] = $this->use_profiles['meta_keywords'];
        }
        $this->smarty->smarty->assign('page', $page);
        
        # REPLACE LINK FOR MULILANGUAGE
        $controller = Dispatcher::getInstance()->getController();
        if ($controller == 'appagebuilderhome') {
            Media::addJsDef(array('approfile_multilang_url' => LeoElementsProfilesModel::getAllProfileRewrite($this->use_profiles['id_leoelements_profiles'])));
        }
        
        # Set profile parrams global for other module use product list
        Context::getContext()->smarty->assign(array(
            'profile_params' => $params,
        ));
        $this->context->smarty->assign(array(
            'profile' => $this->use_profiles,
            'LEO_PANELTOOL' => $panel_tool,
            'IS_RTL' => $this->context->language->is_rtl,
            'LEO_RTL' => $this->context->language->is_rtl,
            'LEO_THEMENAME' => _THEME_NAME_,
            'tpl_theme_dir' => trim(_PS_THEME_DIR_, '/'),
            'isMobile' => Context::getContext()->isMobile(),
        ));

        $this->header_content = $this->display(__FILE__, 'header.tpl');
        return $this->header_content . ' ' . $this->getWidgetCSS();
    }

    public function hookLeoElementConfig($param)
    {
        $cf = $param['configName'];
        if (!$this->use_profiles) {
            if (!$this->all_active_profile) {
                $model = new LeoElementsProfilesModel();
                $this->all_active_profile = $model->getAllProfileByShop();
            }
            $this->use_profiles = LeoElementsProfilesModel::getActiveProfile('index', $this->all_active_profile);
        }
        if (!$this->use_profiles) {
            return '';
        }
        if (!isset($this->use_profiles['params'])) {
            return '';
        }

        $params = json_decode($this->use_profiles['params'], true);
        $params['isMobile'] = Context::getContext()->isMobile();
        $params['isTablet'] = Context::getContext()->isTablet();
        if ($cf == 'productListParams') {
            $pl_layout = '';
            //check product list
            switch (Context::getContext()->cookie->leoelement_controller) {
              case "category":
                $id_category = Context::getContext()->cookie->leoelement_controller_id;
                $category = new Category($id_category);
                $clayout = '';
                //get category layout
                if(Tools::getIsset('layout')) {
                    $clayout = Tools::getValue('layout');
                } else {
                    if (Context::getContext()->isMobile() && isset($category->leoe_layout_mobile) && $category->leoe_layout_mobile) {
                        $clayout = $category->leoe_layout_mobile;
                    } else if(Context::getContext()->isTablet() && isset($category->leoe_layout_tablet) && $category->leoe_layout_tablet) {
                        $clayout = $category->leoe_layout_tablet;
                    } else {
                        if(isset($category->leoe_layout) && $category->leoe_layout) {
                            $clayout = $category->leoe_layout;
                        } else {
                            //get from profile
                            if (Context::getContext()->isMobile() && isset($params['category_layout_mobile']) && $params['category_layout_mobile']) {
                                $clayout = $params['category_layout_mobile'];
                            } else if(Context::getContext()->isTablet() && isset($params['category_layout_tablet']) && $params['category_layout_tablet']) {
                                $clayout = $params['category_layout_tablet'];
                            } else {
                                $clayout = $params['category_layout'];
                            }
                        }
                    }
                }

                //found category layout
                if($clayout) {
                    $params['c_config'] = $this->getCategoryConfig($clayout);
                    if (Context::getContext()->isMobile() && $params['c_config']['product_list_mobile']) {
                        $pl_layout = $params['c_config']['product_list_mobile'];
                    } else if(Context::getContext()->isTablet() && $params['c_config']['product_list_tablet']) {
                        $pl_layout = $params['c_config']['product_list_tablet'];
                    } else {
                        $pl_layout = $params['c_config']['product_list'];
                    }
                }

                if(!isset($params['c_config']) || !$params['c_config']) {
                    $params['c_config'] = leoECHelper::defaultConfig()['category'];
                }
                break;
              case "pricesdrop":
                if ($params['pricedrop_layout']) {
                    $pl_layout = $params['pricedrop_layout'];
                }
                break;
              case "newproducts":
                if ($params['newproduct_layout']) {
                    $pl_layout = $params['newproduct_layout'];
                }
                break;
              case "bestsales":
                if ($params['bestsales_layout']) {
                    $pl_layout = $params['bestsales_layout'];
                }
                break;
              case "search":
                if ($params['search_layout']) {
                    $pl_layout = $params['search_layout'];
                }
                break;
              case "manufacture":
                if ($params['manufacture_layout']) {
                    $pl_layout = $params['manufacture_layout'];
                }
                break;  
              default:
                
            }
            //find from profile
            if(!$pl_layout) {
                if (Context::getContext()->isMobile() && $params['productlist_layout_mobile']) {
                    $pl_layout = $params['productlist_layout_mobile'];
                } else if(Context::getContext()->isTablet() && $params['productlist_layout_tablet']) {
                    $pl_layout = $params['productlist_layout_tablet'];
                } else {
                    $pl_layout = $params['productlist_layout'];
                }
            }

            if($pl_layout || Tools::getIsset('plist_key')) {
                if(Tools::getIsset('plist_key')) {
                   $pl_layout = Tools::getValue('plist_key');
                }

                $params['pl_config'] = $this->getProductListConfig($pl_layout);
                $params['pl_key'] = $pl_layout;
                if (file_exists(_PS_THEME_DIR_.'modules/leoelements/views/templates/front/products/'.$pl_layout.'.tpl')) {
                    $params['pl_url'] = 'module:/leoelements/views/templates/front/products/'.$pl_layout.'.tpl';
                }
            }

            if (!isset($params['pl_config']) || !$params['pl_config']) {
                $params['pl_config'] = leoECHelper::defaultConfig()['product_list'];
            }
        }

        return json_encode($params);
    }
    
    public function getWidgetCSS ()
    {
        $css_unique = '';
        
        $controller = Dispatcher::getInstance()->getController();
        if (!empty($this->context->controller->php_self)) {
            $controller = $this->context->controller->php_self;
        }
        $controller = Tools::strtolower( $controller );

        if( !Leo_Helper::is_preview_mode() ) {
            # DEFAULT
            $css_unique .= Plugin::instance()->frontend->parse_global_css_code();
        } 
        if( Leo_Helper::$id_post && Validate::isLoadedObject( new LeoElementsContentsModel( Leo_Helper::$id_post, Leo_Helper::$id_lang ) ) ){
            # SPECIAL PAGE
            if( Leo_Helper::$id_post != Leo_Helper::$id_editor ){
                $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
            }
        }
        
        if( isset(self::$leo_txt['hooks']['displayBanner']) && self::$leo_txt['hooks']['displayBanner'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayBanner'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayBanner'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayBanner'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayNav1']) && self::$leo_txt['hooks']['displayNav1'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayNav1'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayNav1'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayNav1'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayNav2']) && self::$leo_txt['hooks']['displayNav2'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayNav2'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayNav2'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayNav2'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayTop']) && self::$leo_txt['hooks']['displayTop'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayTop'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayTop'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayTop'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayNavFullWidth']) && self::$leo_txt['hooks']['displayNavFullWidth'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayNavFullWidth'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayNavFullWidth'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayNavFullWidth'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayHome']) && self::$leo_txt['hooks']['displayHome'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayHome'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayHome'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayHome'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayLeftColumn']) && self::$leo_txt['hooks']['displayLeftColumn'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayLeftColumn'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayLeftColumn'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayLeftColumn'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayRightColumn']) && self::$leo_txt['hooks']['displayRightColumn'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayRightColumn'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayRightColumn'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayRightColumn'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayFooterBefore']) && self::$leo_txt['hooks']['displayFooterBefore'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayFooterBefore'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayFooterBefore'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayFooterBefore'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayFooter']) && self::$leo_txt['hooks']['displayFooter'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayFooter'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayFooter'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayFooter'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayFooterAfter']) && self::$leo_txt['hooks']['displayFooterAfter'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayFooterAfter'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayFooterAfter'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayFooterAfter'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        
        # CATEGORY PAGE - BEGIN
        if( isset(self::$leo_txt['hooks']['displayHeaderCategory']) && self::$leo_txt['hooks']['displayHeaderCategory'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayHeaderCategory'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayHeaderCategory'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayHeaderCategory'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( isset(self::$leo_txt['hooks']['displayFooterCategory']) && self::$leo_txt['hooks']['displayFooterCategory'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayFooterCategory'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayFooterCategory'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayFooterCategory'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        # CATEGORY PAGE - END
        
        # PRODUCT PAGE - BEGIN
        if( $controller == 'product' && isset(self::$leo_txt['hooks']['displayReassurance']) && self::$leo_txt['hooks']['displayReassurance'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayReassurance'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayReassurance'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayReassurance'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        if( $controller == 'product' && isset(self::$leo_txt['hooks']['displayFooterProduct']) && self::$leo_txt['hooks']['displayFooterProduct'] && Validate::isLoadedObject( new LeoElementsContentsModel( self::$leo_txt['hooks']['displayFooterProduct'], Leo_Helper::$id_lang ) ) ){		
                if( self::$leo_txt['hooks']['displayFooterProduct'] != self::$leo_txt['id_editor'] ){
                        Leo_Helper::$id_post = self::$leo_txt['hooks']['displayFooterProduct'];
                        $css_unique .= Plugin::instance()->frontend->parse_post_css_code( Leo_Helper::$id_post );
                }
        }
        # PRODUCT PAGE - END
        
        
        Leo_Helper::reset_post_var();
        
        $this->smarty->assign( ['css_unique' => $css_unique] );
        return $this->fetch( 'module:' . $this->name . '/views/templates/hook/css_unique.tpl' );
    }

    public function getProductListConfig($pllayout) {
        $cacheId = 'leoelements::getProductListConfig_' . md5($pllayout);
        if (!Cache::isStored($cacheId)) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_product_list`'.' WHERE plist_key= "'.pSQL($pllayout, true).'"';
            $result = Db::getInstance()->executeS($sql);
            if($result) {
                $params = json_decode($result[0]['params'], 1);
                $params['class'] = $result[0]['class'];    
            } else {
                $params = array();
            }
            
            Cache::store($cacheId, $params);
        } else {
            $params = Cache::retrieve($cacheId);
        }
        return $params;
    }

    public function getCategoryConfig($clayout) {
        $cacheId = 'leoelements::getCategoryConfig_' . md5($clayout);
        if (!Cache::isStored($cacheId)) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_category`'.' WHERE clist_key= "'.pSQL($clayout, true).'"';
            $result = Db::getInstance()->executeS($sql);
            $params = array();
            if($result) {
                $params = json_decode($result[0]['params'], 1);
                $params['class'] = $result[0]['class'];    
            }
            
            Cache::store($cacheId, $params);
        } else {
            $params = Cache::retrieve($cacheId);
        }
        return $params;
    }

    public function renderWidget($hookName = null, array $config = array())
    {
        $disable_cache = false;
        if (defined('_PS_ADMIN_DIR_')) {
            $disable_cache = true;
        }
        
        //some hook need disable cache get from config of profile
//        $disable_cache_hook = isset($this->profile_param['disable_cache_hook']) ? $this->profile_param['disable_cache_hook'] : LeoECSetting::getCacheHook(3);
        $disable_cache_hook = isset($this->profile_param['disable_cache_hook']) ? $this->profile_param['disable_cache_hook'] : array();
        if (isset($disable_cache_hook[$hookName]) && $disable_cache_hook[$hookName]) {
            $disable_cache = true;
        }
        //disable cache when submit newletter
        if (Tools::isSubmit('submitNewsletter')) {
            $disable_cache = true;
        }
        //disable cache
        if (!Configuration::get('PS_SMARTY_CACHE')) {
            $disable_cache = true;
        }
        
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'leoelements') && strpos($_SERVER['HTTP_REFERER'], 'creator'))
        {
            $disable_cache = true;
        }
        
        if ( Tools::getIsset('front_token')) {
            $disable_cache = true;
        }
        
        //run without cache no create cache
        if ($disable_cache) {
            $this->smarty->assign(array('content' => $this->renderWidget2($hookName, $config)));
            return $this->fetch($this->leo_templateFile);
        } else {
            $cache_id = $this->getCacheId($hookName);
            if (!$this->isCached($this->leo_templateFile, $cache_id)) {
                $this->smarty->assign(array('content' => $this->renderWidget2($hookName, $config)));
            }
            return $this->fetch($this->leo_templateFile, $cache_id);
        }
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        if( !isset( Leo_Helper::$id_post ) || !Leo_Helper::$id_post ){
            return;
        }

        $content = '';

        if( Leo_Helper::$id_post && Validate::isLoadedObject( new LeoElementsContentsModel( Leo_Helper::$id_post, Leo_Helper::$id_lang ) ) )
        {
            $content .= Plugin::instance()->frontend->get_builder_content( Leo_Helper::$id_post, (int) Tools::getValue('content_only') );
        }

        Leo_Helper::reset_post_var();

        return $content;
    }
    
    public function hookOverrideLayoutTemplate($params)
    {
        Leo_Helper::reset_post_var();

        if ( !Leo_Helper::$id_post || isset( self::$leo_overrided[ Leo_Helper::$id_post ] ) ) {
            return;
        }
        
        self::$leo_overrided[ Leo_Helper::$id_post ] = true;
        switch ( Leo_Helper::$post_type ) {
            case 'category':
            case 'product':
                $content = $this->context->smarty->tpl_vars[Leo_Helper::$post_type];
                $content_replace = &$this->context->smarty->tpl_vars[Leo_Helper::$post_type];

                $content->value['description'] .= $this->_filterPageContent();
                $content_replace = $content;
                break;
        }
    }
    
    public $leo_templateFile;
    public static $leo_overrided = [];
    
    public function hookFilterCmsContent($params)
    {
        
//        $params['object']['content'] = '<div class="container container-parent">' . $params['object']['content'] . '</div>' . '<div data-elementor-type="post" data-elementor-id="17" class="elementor elementor-17 elementor-edit-mode" id="elementor">
//					</div>';
//        
//        return $params;
        
        
        Leo_Helper::render_widget();
        Leo_Helper::reset_post_var();
        
        if ( !Leo_Helper::$id_post || isset( self::$leo_overrided[ Leo_Helper::$id_post ] ) ) {
            $params['object']['content'] = '<div class="container container-parent">' . $params['object']['content'] . '</div>';
                
            return $params;
        }
        
        self::$leo_overrided[ Leo_Helper::$id_post ] = true;
                
        $params['object']['content'] = '<div class="container container-parent">' . $params['object']['content'] . '</div>' . $this->_filterPageContent();

        return $params;
    }
    
    public function hookFilterLeoGuideContent($params)
    {
        Leo_Helper::render_widget();
        Leo_Helper::reset_post_var();
        
//        boom_dieObject(\Leo_Helper::getInstance(), true);
        
        if ( !Leo_Helper::$id_post || isset( self::$leo_overrided[ Leo_Helper::$id_post ] ) ) {
            $params['object']->content = '<div class="container container-parent">' . $params['object']->content . '</div>';
                
            return $params;
        }
        
        self::$leo_overrided[ Leo_Helper::$id_post ] = true;
        
//        Leo_Helper::debug();
        
        $params['object']->content = '<div class="container container-parent tuancongtu">' . $params['object']->content . '</div>' . $this->_filterPageContent();

//        return $params;
    }
    
    public function _filterPageContent()
    {	
        $cacheId = 'leoelements';

        $cacheId .= '|' . Leo_Helper::$id_post;	

        if( Leo_Helper::$id_post == Leo_Helper::$id_editor ){
                $cacheId .= '|' . 'editor'; 
        }
        $this->leo_templateFile = 'module:' . $this->name . '/views/templates/hook/page_content.tpl';		
        if (!$this->isCached($this->leo_templateFile, $this->getCacheId($cacheId))){			
            $content = '';

            $get_content = $this->getWidgetVariables();

            if( $get_content ){ $content .= $get_content; }
                        
            $this->smarty->assign(['content' => $content]);
        }
        $html = $this->fetch($this->leo_templateFile, $this->getCacheId($cacheId));
        return $html;
    }
    
    public function getListLanguages()
    {
        $languages = Language::getLanguages( true, $this->context->shop->id );
        
        if( count( $languages ) < 2 ){
            return;
        }
        
        foreach ( $languages as &$lang ) {
            $lang['name_simple'] = preg_replace( '/\s\(.*\)$/', '', $lang['name'] );
        }
                
        $params = [
            'languages' => $languages,
            'current_language' => [
                'id_lang' => $this->context->language->id,
                'name' => $this->context->language->name,
                'name_simple' => preg_replace( '/\s\(.*\)$/', '', $this->context->language->name ),
                'iso_code' => $this->context->language->iso_code
            ]
        ];

        return $params;
    }

    /*
    * moduleRoutes - prestashop version < 1783
    * moduleroutes - prestashop version >= 1783
    */
    public function hookModuleRoutes($params)
    {
        $routes = array();
        $model = new LeoElementsProfilesModel();
        $this->all_active_profile = $model->getAllProfileByShop();

        foreach ($this->all_active_profile as $allProfileItem) {
            if (isset($allProfileItem['friendly_url']) && $allProfileItem['friendly_url']) {
                $routes['module-leoelements-'.$allProfileItem['friendly_url']] = array(
                    'controller' => 'appagebuilderhome',
                    'rule' => $allProfileItem['friendly_url'].'.html',
                    'keywords' => array(
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'leoelements'
                    )
                );
            }
        }
        return $routes;
    }

    public function getBreadcrumbImage($params)
    {
        $bg = '';
        if ($params['breadcrumb']['use_background']) {
            if (Tools::getValue('controller') == 'category') {
                if ($params['breadcrumb']['category'] == "catimg") {
                    $category = new Category(Tools::getValue('id_category'));
                    $link = new Link();
                    $id_lang = Context::getContext()->language->id;
                    if (!$category->id_image) {
                        $bg = $params['breadcrumb']['bg'];
                    } else {
                        $bg = 'catimg';
                    }
                } else if ($params['breadcrumb']['category'] == "breadcrumbimg" && file_exists(_PS_IMG_DIR_.'breadcrumb/category/'.Tools::getValue('id_category').'.jpg')) {
                    $bg = 'img/breadcrumb/category/'.Tools::getValue('id_category').'.jpg';
                } else if ($params['breadcrumb']['category'] == "breadcrumbimg" && file_exists(_PS_IMG_DIR_.'breadcrumb/category/'.Tools::getValue('id_category').'.png')) {
                    $bg = 'img/breadcrumb/category/'.Tools::getValue('id_category').'.png';
                } else {
                    $bg = $params['breadcrumb']['bg'];
                }
            } else if (Tools::getValue('controller') == 'product') {
                if (file_exists(_PS_IMG_DIR_.'breadcrumb/product/'.Tools::getValue('id_product').'.jpg')) {
                    $bg = 'img/breadcrumb/product/'.Tools::getValue('id_product').'.jpg';
                } else if (file_exists(_PS_IMG_DIR_.'breadcrumb/product/'.Tools::getValue('id_product').'.png')) {
                    $bg = 'img/breadcrumb/product/'.Tools::getValue('id_product').'.png';
                } else if (file_exists(_PS_IMG_DIR_.'breadcrumb/product.jpg')) {
                    $bg = 'img/breadcrumb/product.jpg';
                } else {
                    $bg = $params['breadcrumb']['bg'];
                }
            } else if (file_exists(_PS_IMG_DIR_.'breadcrumb/'.Tools::getValue('controller').'.jpg')) {
                $bg = 'img/breadcrumb/'.Tools::getValue('controller').'.jpg';
            } else if (file_exists(_PS_IMG_DIR_.'breadcrumb/'.Tools::getValue('controller').'.png')) {
                $bg = 'img/breadcrumb/'.Tools::getValue('controller').'.png';
            } else {
                $bg = $params['breadcrumb']['bg'];
            }

            if ($bg && $bg != "catimg" && (!(Tools::substr($bg, 0, 7) == 'http://')) && (!(Tools::substr($bg, 0, 8) == 'https://')) && (!(Tools::substr($bg, 0, 10) == 'data:image'))) {
                $bg = Context::getContext()->link->getBaseLink().$bg;
            }
        }
        return $bg;
    }
    
    public function getListCurrencies()
    {
        if( Configuration::isCatalogMode() || !Currency::isMultiCurrencyActivated() ) {
            return;
        }
        
        $current_currency = null;
        $serializer = new ObjectPresenter();
        $currencies = array_map(
            function ($currency) use ($serializer, &$current_currency) {				
                $currencyArray = $serializer->present($currency);

                // serializer doesn't see 'sign' because it is not a regular
                // ObjectModel field.
                $currencyArray['sign'] = $currency->sign;

                $url = $this->context->link->getLanguageLink($this->context->language->id);

                $parsedUrl = parse_url($url);
                $urlParams = [];
                if (isset($parsedUrl['query'])) {
                    parse_str($parsedUrl['query'], $urlParams);
                }
                $newParams = array_merge(
                    $urlParams,
                    [
                        'SubmitCurrency' => 1,
                        'id_currency' => $currency->id,
                    ]
                );
                $newUrl = sprintf('%s://%s%s%s?%s',
                    $parsedUrl['scheme'],
                    $parsedUrl['host'],
                    isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '',
                    $parsedUrl['path'],
                    http_build_query($newParams)
                );

                $currencyArray['url'] = $newUrl;

                if ($currency->id === $this->context->currency->id) {
                    $currencyArray['current'] = true;
                    $current_currency = $currencyArray;
                } else {
                    $currencyArray['current'] = false;
                }

                return $currencyArray;
            },
            Currency::getCurrencies(true, true)
        );
                
        $params = [
            'currencies' => $currencies,
            'current_currency' => $current_currency,
        ];

        return $params;
    }
    
    public function convertProducts( $products )	
    {		
            $assembler = new ProductAssembler($this->context);
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new ProductListingPresenter(
                    new ImageRetriever(
                            $this->context->link
                    ),
                    $this->context->link,
                    new PriceFormatter(),
                    new ProductColorsRetriever(),
                    $this->context->getTranslator()
            );
            $products_for_template = [];
            if( is_array( $products ) ){
                    foreach ($products as $rawProduct) {
                            $product = $presenter->present(
                                    $presentationSettings,
                                    $assembler->assembleProduct($rawProduct),
                                    $this->context->language
                            );
                            $products_for_template[] = $product;				
                    }
            }
            return $products_for_template;
    }
    
    public function getCategories()
    {
        $category = new Category((int)Configuration::get('PS_HOME_CATEGORY'), $this->context->language->id);
            
        $range = '';
        $maxdepth = 0;
        if (Validate::isLoadedObject($category)) {
            if ($maxdepth > 0) {
                $maxdepth += $category->level_depth;
            }
            $range = 'AND nleft >= '.(int)$category->nleft.' AND nright <= '.(int)$category->nright;
        }

        $resultIds = array();
        $resultParents = array();
        $sql = '
            SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
            FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
            WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
            AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
            '.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
            '.$range.'
            ORDER BY `level_depth` ASC, cs.`position` ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($result as &$row) {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }
        
        $categoriesSource = array();
        
        $this->getTree($resultParents, $resultIds, $maxdepth, ($category ? $category->id : null), 0, $categoriesSource);

        return $categoriesSource;
    }
    
    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0, &$categoriesSource)
    {
        if (is_null($id_category)) {
            $id_category = $this->context->shop->getCategory();
        }

        if (isset($resultIds[$id_category])) {
            $link = $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']);
            $name = str_repeat('&nbsp;&nbsp;', 1 * $currentDepth).$resultIds[$id_category]['name'];
            $desc = $resultIds[$id_category]['description'];
        } else {
            $link = $name = $desc = '';
        }
        
        $categoriesSource[$currentDepth . '_' . $id_category] = $name;
        
        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth)) {
            foreach ($resultParents[$id_category] as $subcat) {
                $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1, $categoriesSource);
            }
        }
    }
    
    public function _prepProducts($settings)
    {
        # DEFAULT
        $settings['limit'] = isset($settings['limit']) ? $settings['limit'] : 10;
        $settings['order_by'] = isset($settings['order_by']) ? $settings['order_by'] : 'position';
        $settings['order_way'] = isset($settings['order_way']) ? $settings['order_way'] : 'ASC';
        $settings['paged'] = isset($settings['paged']) ? $settings['paged'] : 1;
        $settings['items_type'] = isset($settings['items_type']) ? $settings['items_type'] : 1;
        $settings['randomize'] = isset($settings['randomize']) ? $settings['randomize'] : 0;
        
        
        
        $content = array();

        $source = $settings['source'];
        $limit = (int)$settings['limit'] <= 0 ? 10 : (int)$settings['limit'];
        $order_by = $settings['order_by'];
        $order_way = $settings['order_way'];

        if($source == 'c'){
            $source = $settings['category'];
            if ($settings['randomize']) {
                $order_by = 'rand';
            }
        }

        $page = $settings['paged'];

        $content['products'] = $this->execProducts($source,  $settings, $limit, $order_by, $order_way, $page);
        
        return $content['products'];
    }
    
    public function _prepProductsSelected($settings)
    {
        return $this->execProducts('s', $settings, 0, null, null, 1);	
    }
    
    public function execProducts($source, $settings, $limit, $order_by, $order_way, $page = 1)
    {
        $products = [];
                
        switch ($source) {
            case 'n':
                        $searchProvider = new NewProductsProductSearchProvider($this->context->getTranslator());

                        $context = new ProductSearchContext($this->context);
                        $query = new ProductSearchQuery();
                        $query->setResultsPerPage($limit)->setPage($page);
                        $query->setQueryType('new-products')->setSortOrder(new SortOrder('product', $order_by, $order_way));
                        $result = $searchProvider->runQuery($context, $query);
                        $products = $result->getProducts();	
                
                break;
            case 'p':
                        $searchProvider = new PricesDropProductSearchProvider($this->context->getTranslator());

                        $context = new ProductSearchContext($this->context);
                        $query = new ProductSearchQuery();
                        $query->setResultsPerPage($limit)->setPage($page);
                        $query->setQueryType('prices-drop')->setSortOrder(new SortOrder('product', $order_by, $order_way));
                        $result = $searchProvider->runQuery($context, $query);
                        $products = $result->getProducts();
                
                break;
            case 'm':			
                        $manufacturer = new Manufacturer($settings['manufacturer']);

                        $searchProvider = new ManufacturerProductSearchProvider($this->context->getTranslator(), $manufacturer);

                        $context = new ProductSearchContext($this->context);
                        $query = new ProductSearchQuery();
                        $query->setResultsPerPage($limit)->setPage($page);
                        $query->setIdManufacturer($manufacturer->id)->setSortOrder(new SortOrder('product', $order_by, $order_way));
                        $result = $searchProvider->runQuery($context, $query);
                        $products = $result->getProducts();
                                
                break;
            case 'sl':			
                $supplier = new Supplier($settings['supplier']);
                                
                $searchProvider = new SupplierProductSearchProvider($this->context->getTranslator(), $supplier);
                
                $context = new ProductSearchContext($this->context);
                $query = new ProductSearchQuery();
                $query->setResultsPerPage($limit)->setPage($page);
                $query->setIdSupplier($supplier->id)->setSortOrder(new SortOrder('product', $order_by, $order_way));
                $result = $searchProvider->runQuery($context, $query);
                $products = $result->getProducts();
                                
                break;
            case 'b':
                        if($order_by == 'position') {
                                $order_by = 'sales';
                        }

                        $searchProvider = new BestSalesProductSearchProvider($this->context->getTranslator());

                        $context = new ProductSearchContext($this->context);
                        $query = new ProductSearchQuery();
                        $query->setResultsPerPage($limit)->setPage($page);
                        $query->setQueryType('best-sales')->setSortOrder(new SortOrder('product', $order_by, $order_way));
                        $result = $searchProvider->runQuery($context, $query);
                        $products = $result->getProducts();		
                break;
            case 's':
                        $id_lang = (int)$this->context->language->id;
                        $id_shop = (int)$this->context->shop->id;
                        if(!is_array($settings['product_ids'])){
                                return $products;
                        }
                        $settings['product_ids'] = $this->getIdFromTitle($settings['product_ids']);
                        foreach($settings['product_ids'] as $product_id){

                                if((int)$product_id){
                                        $id_product = (int)$product_id;
                                        $product =  new Product($id_product, true, $id_lang, $id_shop, $this->context);
                                        if (Validate::isLoadedObject($product)) {
                                                $product->id_product = (int)$id_product;
                                                $products[]= (array)$product;
                                        }
                                }
                        }
                
                break;
            case 'p_s':
                if(isset($this->context->smarty->tpl_vars['product_same_id']) && isset($this->context->smarty->tpl_vars['category_same_id'])){
                    $id_product = $this->context->smarty->tpl_vars['product_same_id'];
                    $id_category = $this->context->smarty->tpl_vars['category_same_id'];
                
                    $category = new Category($id_category);

                    $searchProvider = new CategoryProductSearchProvider($this->context->getTranslator(), $category);

                    $context = new ProductSearchContext($this->context);
                    $query = new ProductSearchQuery();
                    $query->setResultsPerPage((int)$limit + 1)->setPage($page);
                    $query->setIdCategory($category->id)->setSortOrder(
                        $order_by == 'rand'
                        ? SortOrder::random()
                        : new SortOrder('product', $order_by, $order_way)
                    );
                    $result = $searchProvider->runQuery($context, $query);
                    $products = $result->getProducts();
                    $products = $this->convertProductsSame($products, $id_product, $limit);
                }
                
                break;
            case 'p_a':
                if(isset($this->context->smarty->tpl_vars['accessories']->value)){
                    $products = $this->context->smarty->tpl_vars['accessories']->value;
                }
                
                break;
            default:
                $id_category_arr = explode('_', $source);

                if(isset($id_category_arr[1])){
                    $id_category = $id_category_arr[1];
                }else{
                    $id_category = $source;
                }

                $category = new Category((int)$id_category);

                $searchProvider = new CategoryProductSearchProvider($this->context->getTranslator(), $category);

                $context = new ProductSearchContext($this->context);
                $query = new ProductSearchQuery();
                $query->setResultsPerPage($limit)->setPage($page);
                $query->setIdCategory($category->id)->setSortOrder(
                    $order_by == 'rand'
                    ? SortOrder::random()
                    : new SortOrder('product', $order_by, $order_way)
                );
                $result = $searchProvider->runQuery($context, $query);
                $products = $result->getProducts();		
                
                break;
        }

        if($source != 'p_s' && $source != 'p_a') {
                $products = $this->convertProducts($products);
        } else {
                if( PrestaHelper::is_preview_mode() || Dispatcher::getInstance()->getController() == 'ajax_editor' || (int)Tools::getValue( 'wp_preview' ) ){
                        $searchProvider = new NewProductsProductSearchProvider($this->context->getTranslator());
                        $context = new ProductSearchContext($this->context);
                        $query = new ProductSearchQuery();
                        $query->setResultsPerPage($limit)->setPage($page);
                        $query->setSortOrder(new SortOrder('product', $order_by, $order_way));
                        $result = $searchProvider->runQuery($context, $query);
                        $products = $result->getProducts();	
                        $products = $this->convertProducts($products);
                }
        }

        return $products;
    }
        
    protected function getIdFromTitle($ids)
    {
        if(is_array($ids)) {
            return $ids;
        }
            $str='';
            $ids=explode(',',$ids);
            foreach($ids as $id){
                    $exp=explode('_',$id);
                    $str.=$exp[0].",";
            }
            $str=rtrim($str,",");
            return $str;

    }
        
    public function getCms()
    {
        $cms = CMS::listCms(Context::getContext()->language->id, false, true);
        $data = array();
        
        foreach ($cms as $item) {
            $key = $item['id_cms'];
            $value = $item['meta_title'];
            $data[ $key ] = $value;
        }
        
        return $data;
    }
    
    public function getPages()
    {
        $page_controller = array();
        
        foreach (Meta::getPages() as $page) {
            if (strpos($page, 'module') === false) {
                $key = $page;
                $value = $page;
                $page_controller[ $key ] = $value;
            
            }
        }
        
        return $page_controller;
    }

    public function getMediaDir()
    {
        return 'modules/leoelements/views/';
    }
    public function hookDisplayAdminProductsExtra($params)
    {
        try {
            if (Validate::isLoadedObject($product = new Product((int)$params['id_product']))) {
                $id_shop = Context::getContext()->shop->id;

                $sql = 'SELECT a.`plist_key`, a.`name` FROM `'._DB_PREFIX_.'leoelements_products` AS a INNER JOIN `'._DB_PREFIX_.'leoelements_products_shop` AS b ON a.`id_leoelements_products` = b.`id_leoelements_products' .'` WHERE b.id_shop= "'.(int)$id_shop.'"';
                $list = Db::getInstance()->executeS($sql);
                $this->context->smarty->assign(array(
                    'product_layouts' => $list,
                    'id_product' => (int)Tools::getValue('id_product'),
                    'apextras' => $extrafied,
                    'languages' => Language::getLanguages(),
                    'data_fields' => $data_fields,
                    'default_language' =>  Context::getContext()->language->id,
                    'current_layout' => $product->leoe_layout,
                    'current_layout_mobile' => $product->current_layout_mobile,
                    'current_layout_table' => $product->current_layout_table,
                    'leoe_extra_1' => $product->leoe_extra_1,
                    'leoe_extra_2' => $product->leoe_extra_2,
                ));

                return $this->display(__FILE__, 'adminproduct.tpl');
            }
            
        } catch (Exception $exc) {
            
        }
    }

    public function hookActionCategoryFormBuilderModifier(array $params) {
        $id_shop = $this->context->shop->id;
        $categoryId = $params['id'];
        $formBuilder = $params['form_builder'];
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        $translator = $this->context->getTranslator();

        $sql = 'SELECT a.`clist_key`, a.`name` FROM `'._DB_PREFIX_.'leoelements_category` AS a INNER JOIN `'._DB_PREFIX_.'leoelements_category_shop` AS b ON a.`id_leoelements_category` = b.`id_leoelements_category' .'` WHERE b.id_shop= "'.(int)$id_shop.'"';
        $list = Db::getInstance()->executeS($sql);
        $list_cat = array();
        foreach ($list as $category) {
            $list_cat[$category['name']] = $category['clist_key'];
        }

        $formBuilder
        ->add('leoe_layout', ChoiceType::class, [
            'required' => false,
            'choices' => $list_cat,
            'label' => $translator->trans('Category Layout Desktop', [], 'Modules.leoelements.Admin'),
        ])
        ->add('leoe_layout_mobile', ChoiceType::class, [
            'required' => false,
            'choices' => $list_cat,
            'label' => $translator->trans('Category Layout Mobile', [], 'Modules.leoelements.Admin'),
        ])
        ->add('leoe_layout_tablet', ChoiceType::class, [
            'required' => false,
            'choices' => $list_cat,
            'label' => $translator->trans('Category Layout Tablet', [], 'Modules.leoelements.Admin'),
        ])
        ->add('leoe_extra_1', TranslatableType::class, [
            'required' => false,
            'type' => TextWithRecommendedLengthType::class,
            'label' => 'leoe_extra_1',
            'options' => [
                'required' => false,
                'input_type' => 'textarea',
                'recommended_length' => 5000,

            ],
        ])
        ->add('leoe_extra_2', TranslatableType::class, [
            'required' => false,
            'type' => TextWithRecommendedLengthType::class,
            'label' => 'leoe_extra_2',
            'options' => [
                'required' => false,
                'input_type' => 'textarea',
                'recommended_length' => 5000,

            ],
        ]);

        $category = new Category($params['id']);
        $params['data']['leoe_layout'] = $category->leoe_layout;
        $params['data']['leoe_layout_mobile'] = $category->leoe_layout_mobile;
        $params['data']['leoe_layout_tablet'] = $category->leoe_layout_tablet;
        $params['data']['leoe_extra_1'] = $category->leoe_extra_1;
        $params['data']['leoe_extra_2'] = $category->leoe_extra_2;

        $formBuilder->setData($params['data']);
    }

    public function hookActionAfterUpdateCategoryFormHandler(array $params) {
        $this->updateContentCategory($params);
    }

    public function hookActionAfterCreateCategoryFormHandler(array $params) {
        $this->updateContentCategory($params);
    }

    public function updateContentCategory(array $params) {
        $id_category = $params['id'];
        $id_shop = $this->context->shop->id;
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        $formData = $params['form_data'];

        $leoe_layout = $formData['leoe_layout'];
        $leoe_layout_mobile = $formData['leoe_layout_mobile'];
        $leoe_layout_tablet = $formData['leoe_layout_tablet'];

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'category` SET `leoe_layout` = "'.pSQL($leoe_layout, true).'", `leoe_layout_mobile` = "'.pSQL($leoe_layout_mobile, true).'", `leoe_layout_tablet` = "'.pSQL($leoe_layout_tablet, true).'" WHERE `id_category` = "'.$id_category.'"';
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_shop` SET `leoe_layout` = "'.pSQL($leoe_layout, true).'", `leoe_layout_mobile` = "'.pSQL($leoe_layout_mobile, true).'", `leoe_layout_tablet` = "'.pSQL($leoe_layout_tablet, true).'" WHERE `id_category` = "'.$id_category.'" AND `id_shop` = "'.$id_shop.'"';
        Db::getInstance()->execute($sql);

        foreach ($locales as $locale) {
            $id_lang = $locale['id_lang'];
            $leoe_extra_1 = $formData['leoe_extra_1'][$id_lang];
            $leoe_extra_2 = $formData['leoe_extra_2'][$id_lang];

            $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_lang` SET `leoe_extra_1` = "'.pSQL($leoe_extra_1, true).'", `leoe_extra_2` = "'.pSQL($leoe_extra_2, true).'" WHERE `id_category` = "'.$id_category.'" AND `id_shop` = "'.$id_shop.'" AND `id_lang` = "'.$id_lang.'"';

            Db::getInstance()->execute($sql);
        }
    }

    public function hookProductMoreImg($list_pro)
    {
        $id_lang = Context::getContext()->language->id;
        //get product info
        // $product_list = $this->getProducts($list_pro, $id_lang);
        $product_list = $this->execProducts('s', ['product_ids'=> explode(',', $list_pro)], 0, null, null, 1);

        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium'))
        ));

        $obj = array();
        foreach ($product_list as $product) {
            // $this->smarty->assign('product', $product);
            $this->context->smarty->assign(array(
                'product' => $product,
                'leoajax' => 1,
            ));
            $obj[] = array('id' => $product['id_product'], 'content' => ($this->display(__FILE__, 'product.tpl')));
        }
        return $obj;
    }

    public function hookProductOneImg($list_pro)
    {
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);

        $id_lang = Context::getContext()->language->id;
        $where = ' WHERE i.`id_product` IN ('.pSQL($list_pro).') AND (ish.`cover`=0 OR ish.`cover` IS NULL) AND ish.`id_shop` = '.Context::getContext()->shop->id;
        $order = ' ORDER BY i.`id_product`,`position`';
        $limit = ' LIMIT 0,1';
        //get product info
        $list_img = $this->getAllImages($id_lang, $where, $order, $limit);
        $saved_img = array();
        $obj = array();
        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'smallSize' => Image::getSize(ImageType::getFormattedName('small'))
        ));

        $image_name = 'home';
        $image_name .= '_default';
        foreach ($list_img as $product) {
            if (!in_array($product['id_product'], $saved_img)) {
                $obj[] = array(
                    'id' => $product['id_product'],
                    'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                    'name' => $product['name'],
                    );
            }
            $saved_img[] = $product['id_product'];
        }
        return $obj;
    }

    public function hookProductAllOneImg($list_pro)
    {
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);

        $id_lang = Context::getContext()->language->id;
        $image_product = Tools::getValue('image_product');
        $where = ' WHERE i.`id_product` IN ('.implode(', ', array_map('intval', explode(',', $list_pro))).') AND i.`id_image` NOT IN ('.implode(', ', array_map('intval', explode(',', $image_product))).') AND ish.`id_shop` = '.Context::getContext()->shop->id;
        $order = ' ORDER BY i.`id_product`,`position`';
        $limit = ' LIMIT 0,1';
        //get product info
        $list_img = $this->getAllImages($id_lang, $where, $order, $limit);
        $saved_img = array();
        $obj = array();
        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'smallSize' => Image::getSize(ImageType::getFormattedName('small'))
        ));

        $image_name = 'home';
        $image_name .= '_default';
        foreach ($list_img as $product) {
            if (!in_array($product['id_product'], $saved_img)) {
                $obj[] = array(
                    'id' => $product['id_product'],
                    'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                    'name' => $product['name'],
                    );
            }
            $saved_img[] = $product['id_product'];
        }
        return $obj;
    }
    
    public function hookProductAttributeOneImg($list_pro)
    {
        $list_all = explode(',', $list_pro);
        $str_id_product = '';
        $str_id_product_attribute = '';
        
        $data = array(
            'product' => array(
                'arr_id_product' => array(),
                'str_id_product' => '',
            ),
            'attribute' => array(
                'arr_id_product' => array(),
                'str_id_product' => '',
                'str_id_attribute' => '',
            ),
        );
        foreach ($list_all as $item) {
            $temp = explode('-', $item);
            if ((int)$temp[1] < 1) {
                # product
                $str_id_product .= (int)$temp[0] . ',';
                $data['product']['str_id_product'] .= (int)$temp[0] . ',';
                $data['product']['arr_id_product'][(int)$temp[0]] = (int)$temp[1];
            } else {
                # attribute
                $str_id_product_attribute .= (int)$temp[0] . ',';
                $data['attribute']['str_id_product'] .= (int)$temp[0] . ',';
                $data['attribute']['str_id_attribute'] .= (int)$temp[1] . ',';
                $data['attribute']['arr_id_product'][(int)$temp[0]] = (int)$temp[1];
            }
        }
        $data['product']['str_id_product'] = rtrim($data['product']['str_id_product'], ',');
        $data['attribute']['str_id_product'] = rtrim($data['attribute']['str_id_product'], ',');
        $data['attribute']['str_id_attribute'] = rtrim($data['attribute']['str_id_attribute'], ',');
        

        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);

        $saved_img = array();
        $obj = array();
        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'smallSize' => Image::getSize(ImageType::getFormattedName('small'))
        ));

        $image_name = 'home';
        $image_name .= '_default';
        # validate module
//        $limit = '';
        
        if ($data['product']['str_id_product']) {
            # GET IMAGE OF PRODUCT
            $id_lang = Context::getContext()->language->id;
            $where = ' WHERE i.`id_product` IN ('.$data['product']['str_id_product'].') AND (ish.`cover`=0 OR ish.`cover` IS NULL) AND ish.`id_shop` = '.Context::getContext()->shop->id;
            $order = ' ORDER BY i.`id_product`,`position`';
//            $limit = ' LIMIT 0,1';

            $id_shop = Context::getContext()->shop->id;
            
            
            $sql = 'SELECT DISTINCT i.`id_product`, ish.`cover`, i.`id_image`, il.`legend`, i.`position`,pl.`link_rewrite`, pl.`name`
                                    FROM `'._DB_PREFIX_.'image` i
                                    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product`) AND pl.`id_lang` = '.(int)$id_lang.'
                                    LEFT JOIN `'._DB_PREFIX_.'image_shop` ish ON (ish.`id_image` = i.`id_image` AND ish.`id_shop` = '.(int)$id_shop.')
                                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.pSql($where).' '.pSQL($order);
            
            $image_product =  Db::getInstance()->executeS($sql);
            
            if ($image_product) {
                foreach ($image_product as $product) {
                    if (!in_array($product['id_product'], $saved_img)) {
                        $obj[] = array(
                            'id' => $product['id_product'],
                            'id_product_attribute' => $product['id_product']. '-0',
                            'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                            'name' => $product['name'],
                        );
                        $saved_img[] = $product['id_product'];
                    }
                }
            }
        }
        
        if ($data['attribute']['str_id_product'] && $data['attribute']['str_id_attribute']) {
            # GET IMAGE OF ATTRIBUTE
            $sql = 'SELECT DISTINCT i.`id_product`, pai.`id_product_attribute`, ish.`cover`, i.`id_image`, il.`legend`, i.`position`,pl.`link_rewrite`, pl.`name` 
                    FROM `'._DB_PREFIX_.'image` i
                    INNER JOIN `'._DB_PREFIX_.'image_shop` ish ON (i.`id_image` = ish.`id_image` AND ish.`id_shop` = '.(int)$id_shop.')
                    INNER JOIN `'._DB_PREFIX_.'image_lang` il  ON (i.`id_image` = il.`id_image`  AND il.`id_lang` = '.(int)$id_lang.')
                    INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` IN ('.$data['attribute']['str_id_attribute'].'))
                    INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` IN('.$data['attribute']['str_id_product'].'))
                    ORDER BY pai.`id_product_attribute` ASC, i.`position` ASC';
            $image_attribute =  Db::getInstance()->executeS($sql);
            
            if ($image_attribute) {
                $index = array();
                foreach ($image_attribute as $product) {
                    if (isset($index[$product['id_product']])) {
                        $index[$product['id_product']] += 1;
                    } else {
                        $index[$product['id_product']] = 1;
                    }
                    
                    if (!in_array($product['id_product'], $saved_img)) {
                        if ($index[$product['id_product']] == 2) {
                            $obj[] = array(
                                'id' => $product['id_product'],
                                'id_product_attribute' => $product['id_product'] . '-' . $product['id_product_attribute'],
                                'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                                'name' => $product['name'],
                            );
                            $saved_img[] = $product['id_product'];
                        }
                    }
                }
            }
        }
        
        
        return $obj;
    }

    public function hookProductCdown($leo_pro_cdown)
    {
        $id_lang = Context::getContext()->language->id;
        $product_list = $this->execProducts('s', ['product_ids'=> explode(',', $leo_pro_cdown)], 0, null, null, 1);
        $obj = array();
        $now = date('Y-m-d H:i:s');
        $finish = $this->l('Expired');
        foreach ($product_list as &$product) {
            $time = false;
            if (isset($product['specificPrice']['from']) && $product['specificPrice']['from'] > $now) {
                $time = strtotime($product['specificPrice']['from']);
                $product['finish'] = $finish;
                $product['check_status'] = 0;
                $product['lofdate'] = Tools::displayDate($product['specificPrice']['from']);
            } elseif (isset($product['specificPrice']['to']) && $product['specificPrice']['to'] > $now) {
                $time = strtotime($product['specificPrice']['to']);
                $product['finish'] = $finish;
                $product['check_status'] = 1;
                $product['lofdate'] = Tools::displayDate($product['specificPrice']['to']);
            } elseif (isset($product['specificPrice']['to']) && $product['specificPrice']['to'] == '0000-00-00 00:00:00') {
                $product['js'] = 'unlimited';
                $product['finish'] = $this->l('Unlimited');
                $product['check_status'] = 1;
                $product['lofdate'] = $this->l('Unlimited');
            } else if (isset($product['specificPrice']['to'])) {
                $time = strtotime($product['specificPrice']['to']);
                $product['finish'] = $finish;
                $product['check_status'] = 2;
                $product['lofdate'] = Tools::displayDate($product['specificPrice']['from']);
            }
            if ($time) {
                $product['js'] = array(
                    'month' => date('m', $time),
                    'day' => date('d', $time),
                    'year' => date('Y', $time),
                    'hour' => date('H', $time),
                    'minute' => date('i', $time),
                    'seconds' => date('s', $time)
                );
            }
            $this->context->smarty->assign(array(
                'product' => $product,
                'leoajax' => 1,
            ));

            $obj[] = array('id' => $product['id_product'], 'content' => ($this->display(__FILE__, 'cdown.tpl')));
        }
        return $obj;
    }

    public function hookGetProductAttribute($full_attribute, $size)
    {
        $list_pro = array_merge($full_attribute, $size);
        $result = array();
        foreach ($list_pro as $key => $value) {
            $result[$value] = array('id_product' => $value);
        }

        //get product info
        $attribute = $this->getAttributesList($result);
        if (isset($result)) {
            foreach ($result as &$product) {
                if (isset($attribute['render_attr'])) {
                    foreach ($attribute['render_attr'] as $key) {
                        if (isset($attribute[$key][$product['id_product']])) {
                            $product['attribute'][$key] = $attribute[$key][$product['id_product']];
                        }
                    }
                }
                
                if (isset($attribute['Color'][$product['id_product']])) {
                    $product['main_variants'] = $attribute['Color'][$product['id_product']];
                }
            }
        }
        $list_pro = array();
        if ($full_attribute) {
            $list_pro['attribute'] = array();
            foreach ($full_attribute as $value) {
                $this->context->smarty->assign(array(
                    'product' => $result[$value],
                    'leoajax' => 1,
                ));
                $list_pro['attribute'][$value] =  $this->fetch('module:leoelements/views/templates/front/products/product_full_attribute.tpl');
            }
        }
        if ($size) {
            $list_pro['size'] = array();
            foreach ($size as $value) {
                $this->context->smarty->assign(array(
                    'product' => $result[$value],
                    'leoajax' => 1,
                ));
                $list_pro['size'][$value] =  $this->fetch('module:leoelements/views/templates/front/products/product_size.tpl');
            }
        }
        return $list_pro;
    }

    public static function getSpecificPriceById($id_specific_price)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                        SELECT *
                        FROM `'._DB_PREFIX_.'specific_price` sp
                        WHERE `id_specific_price` ='.(int)$id_specific_price);

        return $res;
    }

    public function getAllImages($id_lang, $where, $order)
    {
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT DISTINCT i.`id_product`, ish.`cover`, i.`id_image`, il.`legend`, i.`position`,pl.`link_rewrite`, pl.`name`
                                FROM `'._DB_PREFIX_.'image` i
                                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product`) AND pl.`id_lang` = '.(int)$id_lang.'
                                LEFT JOIN `'._DB_PREFIX_.'image_shop` ish ON (ish.`id_image` = i.`id_image` AND ish.`id_shop` = '.(int)$id_shop.')
                                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.pSql($where).' '.pSQL($order);
        return Db::getInstance()->executeS($sql);
    }

    public static function getAttributesList(array $products, $have_stock = true)
    {
        // validate module
        unset($have_stock);

        if (!count($products)) {
            return array();
        }

        $products_id = array();
        foreach ($products as &$product) {
            $products_id[] = (int) $product['id_product'];
        }
        $id_lang = Context::getContext()->language->id;
        $check_stock = !Configuration::get('PS_DISP_UNAVAILABLE_ATTR');
        if (!$res = Db::getInstance()->executeS(
            'SELECT pa.`id_product`, a.`color`, pac.`id_product_attribute`, ag.`group_type`, agl.`name` AS group_name, '.(version_compare(Configuration::get('PS_VERSION_DB'), '8.0.0', '>=') ? 'stock' : 'pa').'.`quantity` AS quantity,' . ($check_stock ? 'SUM(IF(stock.`quantity` > 0, 1, 0))' : '0') . ' qty, a.`id_attribute`, al.`name`, IF(color = "", a.id_attribute, color) group_by
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') .
            Product::sqlStock('pa', 'pa') . '
            JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = product_attribute_shop.`id_product_attribute`)
            JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
            JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
            JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (a.id_attribute_group = ag.`id_attribute_group`)
            JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.`id_attribute_group`=ag.`id_attribute_group`)
            WHERE pa.`id_product` IN (' . implode(',', array_map('intval', $products_id)) . ') 
            GROUP BY pa.`id_product`, a.`id_attribute`, `group_by`
            ' . ($check_stock ? 'HAVING qty > 0' : '') . '
            ORDER BY a.`position` ASC;'
        )) {
            return false;
        }

        $link = new Link();
        $attribute = array();
        foreach ($res as &$row) {
            $row['url'] = $link->getProductLink($row['id_product'], null, null, null, null, null, $row['id_product_attribute']);

            if ($row['group_name'] == 'Color') {
                //color
                $row['texture'] = '';

                if (@filemtime(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) {
                    $row['texture'] = _THEME_COL_DIR_ . $row['id_attribute'] . '.jpg';
                } elseif (Tools::isEmpty($row['color'])) {
                    continue;
                }

                $attribute[str_replace(' ', '_', trim($row['group_name']))][$row['id_product']][] = array('id_product_attribute' => (int) $row['id_product_attribute'], 'group_type' => $row['group_type'], 'group_name' => $row['group_name'], 'color' => $row['color'], 'texture' => $row['texture'], 'id_product' => $row['id_product'], 'name' => $row['name'], 'id_attribute' => $row['id_attribute'], 'url' => $row['url'] );
                $attribute['render_attr'][]= $row['group_name'];
            } else {
                if ($row['group_name'] == 'Size') {
                    $row['quantity'] = 0;
                    $product = new Product($row['id_product']);
                    $attribute_combination = $product->getAttributeCombinations();
                    foreach ($attribute_combination as $combination) {
                        if ($combination['attribute_name'] == $row['name']) {
                            $row['quantity'] += $combination['quantity'];
                        }
                    }
                }
                $attribute[str_replace(' ', '_', trim($row['group_name']))][$row['id_product']][] = $row;
                $attribute['render_attr'][]= str_replace(' ', '_', trim($row['group_name']));
            }
        }
        $attribute['render_attr'] = array_unique($attribute['render_attr']);

        return $attribute;
    }
    
    /**
     * FIX BUG 1.7.3.3 : install theme lose hook displayHome, displayLeoProfileProduct
     * because ajax not run hookActionAdminBefore();
     */
    public function autoRestoreSampleData()
    {
        $theme_manager = new stdclass();
        $theme_manager->theme_manager = 'theme_manager';
        $this->hookActionAdminBefore(array(
            'controller' => $theme_manager,
        ));
    }
    
    public function hookActionAdminBefore($params)
    {
        if (isset($params) && isset($params['controller']) && isset($params['controller']->theme_manager)) {
            // Validate : call hook from theme_manager
            $this->log('hookActionAdminBefore');
        } else {
            // Other module call this hook -> duplicate data
            return;
        }
        
        $this->unregisterHook('actionAdminBefore');

        # FIX THEME_CHILD NOT EXIST TPL FILE -> AUTO COPY TPL FILE FROM THEME_PARENT
        $assets = Context::getContext()->shop->theme->get('assets');
        $theme_parent = Context::getContext()->shop->theme->get('parent');
        if (is_array($assets) && isset($assets['use_parent_assets']) && $assets['use_parent_assets'] && $theme_parent) {
            $from = _PS_ALL_THEMES_DIR_.$theme_parent.'/modules/leoelements';
            $to =   _PS_ALL_THEMES_DIR_.leoECHelper::getInstallationThemeName().'/modules/leoelements';
            leoECHelper::createDir($to);
            Tools::recurseCopy($from, $to);
        }
        
        # FIX : update Prestashop by 1-Click module -> NOT NEED RESTORE DATABASE
        $ap_version = Configuration::get('AP_CURRENT_VERSION');
        if ($ap_version != false) {
            $ps_version = Configuration::get('PS_VERSION_DB');
            $versionCompare =  version_compare($ap_version, $ps_version);
            if ($versionCompare != 0) {
                // Just update Prestashop
                Configuration::updateValue('AP_CURRENT_VERSION', $ps_version);
                return;
            }
        }
        
        # WHENE INSTALL THEME, INSERT HOOK FROM DATASAMPLE IN THEME
        $hook_from_theme = false;
        if (file_exists(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php');
            $sample = new Datasample();
            if ($sample->processHook($this->name)) {
                $hook_from_theme = true;
            };
        }
        
        # INSERT HOOK FROM MODULE_DATASAMPLE
        if ($hook_from_theme == false) {
            $this->registerLeoHook();
        }
        
        # WHEN INSTALL MODULE, NOT NEED RESTORE DATABASE IN THEME
        $install_module = (int)Configuration::get('AP_INSTALLED_leoelements', 0);
        if ($install_module) {
            Configuration::updateValue('AP_INSTALLED_leoelements', '0');
            return;
        }
        
        # INSERT DATABASE FROM THEME_DATASAMPLE
        if (file_exists(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php');
            $sample = new Datasample();
            $sample->processImport($this->name);
        }
        
        # REMOVE FILE INDEX.PHP FOR TRANSLATE
        if (file_exists(_PS_MODULE_DIR_.'leoelements/libs/setup.php')) {
            require_once(_PS_MODULE_DIR_.'leoelements/libs/setup.php');
            ApPageSetup::processTranslateTheme();
        }
    }
    
    public static function log($msg, $is_ren = true)
    {
        if ($is_ren) {
            if (!is_dir(_PS_ROOT_DIR_.'/log')) {
                mkdir(_PS_ROOT_DIR_.'/log', 0755, true);
            }
            error_log("\r\n".date('m-d-y H:i:s', time()).': '.$msg, 3, _PS_ROOT_DIR_.'/log/leoelements.log');
        }
    }
    
    public function renderWidget2($hookName = null, array $config = array())
    {
        $ids_post = [];
//        $disable_cache = false;
        //some hook need disable cache get from config of profile
//        $disable_cache_hook = isset($this->profile_param['disable_cache_hook']) ? $this->profile_param['disable_cache_hook'] : LeoECSetting::getCacheHook(3);

//        if (!$this->all_active_profile) {
//            $model = new LeoElementsProfilesModel();
//            $this->all_active_profile = $model->getAllProfileByShop();
//        }
//        if (!$this->use_profiles) {
//            $this->use_profiles = LeoElementsProfilesModel::getActiveProfile('index', $this->all_active_profile);
//
//            if (!$this->use_profiles) {
//                return '';
//            }
//        }
                
//        $disable_cache_hook = json_decode($this->use_profiles['params'], true)['disable_cache_hook'];
        
//        if (isset($disable_cache_hook[$hookName]) && $disable_cache_hook[$hookName]) {
//            $disable_cache = true;
//        }
//        //disable cache when submit newletter
//        if (Tools::isSubmit('submitNewsletter')) {
//            $disable_cache = true;
//        }
//        //disable cache
//        if (!Configuration::get('PS_SMARTY_CACHE')) {
//            $disable_cache = true;
//        }

        if (preg_match('/^displayBanner\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayBanner'] && !self::$leo_txt['hooks']['displayBanner'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayBanner'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayNav1\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayNav1'] && !self::$leo_txt['hooks']['displayNav1'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayNav1'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayNav2\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayNav2'] && !self::$leo_txt['hooks']['displayNav2'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayNav2'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayTop\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayTop'] && !self::$leo_txt['hooks']['displayTop'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayTop'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayNavFullWidth\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayNavFullWidth'] && !self::$leo_txt['hooks']['displayNavFullWidth'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayNavFullWidth'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayHome\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayHome'] && !self::$leo_txt['hooks']['displayHome'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayHome'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayLeftColumn\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayLeftColumn'] && !self::$leo_txt['hooks']['displayLeftColumn'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayLeftColumn'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayRightColumn\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayRightColumn'] && !self::$leo_txt['hooks']['displayRightColumn'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayRightColumn'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayFooterBefore\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayFooterBefore'] && !self::$leo_txt['hooks']['displayFooterBefore'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayFooterBefore'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayFooter\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayFooter'] && !self::$leo_txt['hooks']['displayFooter'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayFooter'], 'before' => '', 'after' => '' ];
        }
        if (preg_match('/^displayFooterAfter\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayFooterAfter'] && !self::$leo_txt['hooks']['displayFooterAfter'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayFooterAfter'], 'before' => '', 'after' => '' ];
        }
        
        if (preg_match('/^displayHeaderCategory\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayHeaderCategory'] && !self::$leo_txt['hooks']['displayHeaderCategory'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayHeaderCategory'], 'before' => '', 'after' => '' ];
        }
        
        if (preg_match('/^displayFooterCategory\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayFooterCategory'] && !self::$leo_txt['hooks']['displayFooterCategory'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayFooterCategory'], 'before' => '', 'after' => '' ];
        }
        
        if (preg_match('/^displayReassurance\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayReassurance'] && !self::$leo_txt['hooks']['displayReassurance'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayReassurance'], 'before' => '', 'after' => '' ];
        }
        
        if (preg_match('/^displayFooterProduct\d*$/', $hookName)) {
            if( !self::$leo_txt['hooks']['displayFooterProduct'] && !self::$leo_txt['hooks']['displayFooterProduct'] ){
                return; 
            }
            
            $ids_post[] = [ 'id' => self::$leo_txt['hooks']['displayFooterProduct'], 'before' => '', 'after' => '' ];
        }
            
        
        $content = '';
        
        foreach( $ids_post as $value )
        {
            Leo_Helper::$id_post = $value['id'];
            
            
            $get_content = $this->getWidgetVariables($hookName);
            
            
            
            
            if( $get_content ){
                $content .= $value['before'] . $get_content . $value['after']; 
            }
        }
        
        if ( Tools::getIsset('front_token') && Tools::getValue('key_related') != 'displayFooterAfter') {
            if($hookName == 'displayFooter') {
                $content = $content . '<div style="display:none" data-ax-hidden="1" data-elementor-type="post" data-elementor-id="a" class="elementor elementor-a elementor-edit-mode 2" id="elementor"></div>';
            }
        }
        
//        if(isset($_GET['key_related']))
//        if($hookName == 'displayFooter') {
//            $content = $content . '<div style="display:none" data-ax-hidden="1" data-elementor-type="post" data-elementor-id="a" class="elementor elementor-a elementor-edit-mode 2" id="elementor"></div>';
//        }
        return $content;
    }
    
    protected function getCacheId($hookName = null)
    {
        $cache_array = array();
        
//        echo '<pre>' . "\n";
////        echo 'all_active_profile';
////        print_r($this->all_active_profile);
//        echo 'use_profiles';
//        print_r($this->use_profiles);
//        echo '</pre>' . "\n";
//        die();

        
        //process nomal cache for each hook
        //create folder cache for each home by id or buy home name
        if (!isset($this->use_profiles['id_leoelements_profiles'])) {
            $this->use_profiles = LeoElementsProfilesModel::getActiveProfile('index', $this->all_active_profile);
        }
        $cache_array[] = $this->use_profiles['id_leoelements_profiles'];

        //set cache for each hook in profile
        $cache_array[] = $hookName;
        //kiem tra xem module confg
        if ($this->use_profiles && isset($this->use_profiles[$hookName]) && $this->use_profiles[$hookName]) {
            $current_page = leoECHelper::getPageName();
            $iscached = 0;

            //check cache in sub page
            if (($current_page == "category" || $current_page == 'product')) {
                if (isset($this->use_profiles[$hookName]['nocategory'])) {
                    if (in_array(Tools::getValue('id_category'), $this->use_profiles[$hookName]['nocategory'])) {
                        $cache_array[] = Tools::getValue('id_category');
                        $iscached = 1;
                    }
                }

                if (isset($this->use_profiles[$hookName]['nocategoryproduct'])) {
                    if (in_array(Tools::getValue('id_category'), $this->use_profiles[$hookName]['nocategoryproduct'])) {
                        $cache_array[] = Tools::getValue('id_category');
                        $iscached = 1;
                    }
                }

                if (!$iscached && $current_page == "category" && isset($this->use_profiles[$hookName]['categoryproduct'])) {
                    if (in_array(Tools::getValue('id_category'), $this->use_profiles[$hookName]['categoryproduct'])) {
                        $cache_array[] = Tools::getValue('id_category');
                        $iscached = 1;
                    }
                }

                if (!$iscached && $current_page == "category" && isset($this->use_profiles[$hookName]['categoryproductmain'])) {
                    if (in_array(Tools::getValue('id_category'), $this->use_profiles[$hookName]['categoryproductmain'])) {
                        $cache_array[] = Tools::getValue('id_category');
                        $iscached = 1;
                    }
                }
                //product in no category
                if (!$iscached && $current_page == "product" && isset($this->use_profiles[$hookName]['nocategoryproduct'])) {
                    $procate = Product::getProductCategoriesFull(Tools::getValue('id_product'));
                    $procheck = 0;
                    foreach ($procate as $proc) {
                        if (in_array($proc['id_category'], $this->use_profiles[$hookName]['nocategoryproduct'])) {
                            $procheck = 1;
                        }
                    }
                    if ($procheck == 1) {
                        $cache_array[] = 'product_'.Tools::getValue('id_product');
                        $iscached = 1;
                    }
                }

                //product in category
                if (!$iscached && $current_page == "product" && isset($this->use_profiles[$hookName]['categoryproduct'])) {
                    $procate = Product::getProductCategoriesFull(Tools::getValue('id_product'));
                    $procheck = 0;
                    foreach ($procate as $proc) {
                        if (in_array($proc['id_category'], $this->use_profiles[$hookName]['categoryproduct'])) {
                            $procheck = 1;
                        }
                    }
                    if ($procheck == 1) {
                        $cache_array[] = 'product_'.Tools::getValue('id_product');
                        $iscached = 1;
                    }
                }
                //product in main category
                if (!$iscached && $current_page == "product" && isset($this->use_profiles[$hookName]['categoryproduct'])) {
                    $procate = new Product(Tools::getValue('id_product'));
                    if (in_array($procate['id_category_default'], $this->use_profiles[$hookName]['categoryproduct'])) {
                        $cache_array[] = 'product_'.Tools::getValue('id_product');
                        $iscached = 1;
                    }
                }
            }
            //cache big page
            if (!$iscached && isset($this->use_profiles[$hookName][$current_page])) {
                $cache_array[] = $current_page;
                $iscached = 1;
            }
            //cache big page not show
            if (!$iscached && isset($this->use_profiles[$hookName]['exception']) && in_array($cache_array, $this->use_profiles[$hookName]['exception'])) {
                //show but not in controller
                $cache_array[] = $current_page;
                $iscached = 1;
            }
            //random in product carousel
            if (isset($this->use_profiles[$hookName]['productCarousel'])) {
                $random = round(rand(1, max(Configuration::get('APPAGEBUILDER_PRODUCT_MAX_RANDOM'), 1)));
                $cache_array[] = "p_carousel_$random";
            }
            if (isset($this->use_profiles[$hookName][$current_page])) {
                $cache_array[] = $current_page;
                if ($current_page != 'index' && $cache_id = LeoECSetting::getControllerId($current_page, $this->use_profiles[$hookName][$current_page])) {
                    $cache_array[] = $cache_id;
                }
            } else if (isset($this->use_profiles[$hookName]['nocategory']) || isset($this->use_profiles[$hookName]['categoryproduct'])) {
                if (in_array(Tools::getValue('id_category'), $this->use_profiles[$hookName]['nocategory'])) {
                    $cache_array[] = Tools::getValue('id_category');
                }
            } else if (isset($this->use_profiles[$hookName]['categoryproduct']) && ($current_page == "category" || $current_page == 'product')) {
                if ($current_page == 'category') {
                    if (!LeoECSetting::getControllerId($current_page, $this->use_profiles[$hookName]['categoryproduct'])) {
                        $cache_array[] = Tools::getValue('id_category');
                    }
                } else {
                    $procate = Product::getProductCategoriesFull(Tools::getValue('id_product'));
                    $procheck = 0;
                    foreach ($procate as $proc) {
                        if (in_array($proc['id_category'], $this->use_profiles[$hookName]['categoryproduct'])) {
                            $procheck = 1;
                        }
                    }
                    if ($procheck == 0) {
                        $cache_array[] = Tools::getValue('id_product');
                    }
                }
            }
        }
        if (Tools::getValue('plist_key')&& Tools::getIsset('leopanelchange')) {
            $cache_array[] = 'plist_key_'.Tools::getValue('plist_key');
        }
        if (Tools::getValue('header') && Tools::getIsset('leopanelchange') && (in_array($hookName, LeoECSetting::getHook('header')) || $hookName == 'leoECatConfig|header')) {
            $cache_array[] = 'header_'.Tools::getValue('header');
        }
        if (Tools::getValue('content')&& Tools::getIsset('leopanelchange') && (in_array($hookName, LeoECSetting::getHook('content')) || $hookName == 'leoECatConfig|content')) {
            $cache_array[] = 'content_'.Tools::getValue('content');
        }
        if (Tools::getValue('product')&& Tools::getIsset('leopanelchange') && (in_array($hookName, LeoECSetting::getHook('product')) || $hookName == 'leoECatConfig|product')) {
            $cache_array[] = 'product_'.Tools::getValue('product');
        }
        if (Tools::getValue('footer') && Tools::getIsset('leopanelchange') && (in_array($hookName, LeoECSetting::getHook('footer')) || $hookName == 'leoECatConfig|footer')) {
            $cache_array[] = 'footer_'.Tools::getValue('footer');
        }
        
        if (Context::getContext()->isTablet()) {
            $cache_array[] = "tablet";
        } elseif (Context::getContext()->isMobile()) {
            $cache_array[] = "mobile";
        }else{
            $cache_array[] = "desktop";
        }
        return parent::getCacheId().'|'.implode('|', $cache_array);
    }
    
    public function aProduct( $id_product )	
    {
        $products = [];
        
        $id_lang = (int)$this->context->language->id;
        $id_shop = (int)$this->context->shop->id;
        $product =  new Product( $id_product, false, $id_lang, $id_shop, $this->context );
//        echo '<pre>' . "\n";
//        print_r($product);
//        echo '</pre>' . "\n";
//        die();

//        dump($product);
//        die('a');
        if ( Validate::isLoadedObject($product) ) {
            $product->id_product = (int)$id_product;
            $products[]= (array)$product;
        }else{
            return;
        }
        $products = $this->convertProducts( $products );
        return $products[0];
    }
    
    public function hookActionOutputHTMLBefore(&$params)
    {
        # LEO OPTIMIZE - BEGIN
        if (file_exists(_PS_MODULE_DIR_.'leoelements/libs/LeoOptimization.php')) {
            require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoOptimization.php');
            if (class_exists('LeoOptimization')) {
                $params['runtime'] = 1;
                LeoOptimization::getInstance()->processOptimization($params);
            }
        }
        # LEO OPTIMIZE - END
    }
}
