<?php
/**
 * 2007-2022 LeoTheme
 *
 * NOTICE OF LICENSE
 *
 * LeoGallery is module help you can displays amazing gallery with many features on product page
 *
 * DISCLAIMER
 *
 *  @author    LeoTheme <leotheme@gmail.com>
 *  @copyright 2007-2022 LeoTheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

include_once dirname(__FILE__).'/classes/LeoGalleryConfigForm.php';
include_once dirname(__FILE__).'/classes/LeoGallerySliderConfigForm.php';
include_once dirname(__FILE__).'/classes/LeoGallerySliderTabletConfigForm.php';
include_once dirname(__FILE__).'/classes/LeoGallerySliderMobileConfigForm.php';
include_once dirname(__FILE__).'/classes/LeoGalleryMagnifyConfigForm.php';

class LeoGallery extends Module
{   
    protected $configModel;
    protected $sliderConfig;
    protected $sliderTabletConfig;
    protected $sliderMobileConfig;
    protected $magnifyConfig;


    protected $html;
    
    protected $isTablet;
    protected $isMobile;
    protected $layout_from_profile;
    public $fields_form;

    public function __construct()
    {
        $this->name = 'leogallery';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
       
        $this->author = 'Leo Theme';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        parent::__construct();

        $this->displayName = $this->l('Leo Gallery');
        $this->description = $this->l('Displays amazing gallery with many features on product page.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete all data?');
        
        $this->configModel = new LeoGalleryConfigForm($this, 'leogallery_');
        $this->sliderConfig = new LeoGallerySliderConfigForm($this, 'leogallery_slide_');
        $this->sliderTabletConfig = new LeoGallerySliderTabletConfigForm($this, 'leogallery_slidet_');
        $this->sliderMobileConfig = new LeoGallerySliderMobileConfigForm($this, 'leogallery_slidem_');
        $this->magnifyConfig = new LeoGalleryMagnifyConfigForm($this, 'leogallery_magnify_');
        $this->configModel->loadFromConfig();
    }
    
    public function uninstall()
    {
        if (!parent::uninstall() || !$this->clearConfig() || !$this->uninstallTab()) {
            return false;
        }
        return true;
    }
    
    public function installTab()
    {
        // Prepare tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminLeogalleryManagement';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Leo Gallery');
        }
        $tab->id_parent = Tab::getIdFromClassName('AdminParentModulesSf');
        $tab->module = $this->name;
        return $tab->add();
    }
    
    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminLeogalleryManagement');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        } else {
            return false;
        }
    }
    
    public function install()
    {
        if (!parent::install()
                || !$this->installHook()
                || !$this->installDefaults()
                || !$this->installTab()) {
            return (false);
        }
        return (true);
    }
    
    protected function installDefaults()
    {
        foreach ($this->getForms() as $model) {
            $model->loadDefaults();
            $model->saveToConfig(false);
        }
        return true;
    }
    
    protected function clearConfig()
    {
        foreach ($this->getForms() as $model) {
            $model->clearConfig();
        }
        return true;
    }
    
    protected function installHook()
    {
        return $this->registerHook('displayHeader') && $this->registerHook('displayAfterProductThumbs');
    }
    
    public function getContent()
    {
        // FIX BUG 1.7.3.3 auto Restore Sample Data
        if(Hook::isModuleRegisteredOnHook($this, 'actionAdminBefore', (int)Context::getContext()->shop->id)) {
            $theme_manager = new stdclass();
            $theme_manager->theme_manager = 'theme_manager';
            $this->hookActionAdminBefore(array(
                'controller' => $theme_manager,
            ));
        }
        if ((bool)Tools::getValue('empty_smarty_cache')) {
            Tools::clearSmartyCache();
            Tools::clearXMLCache();
            Media::clearCache();
            Tools::generateIndex();
        }
        $this->context->controller->addCss($this->_path.'views/css/styles.css');
        if ($this->isSubmit()) {
            if ($this->postValidate()) {
                $this->postProcess();
            }
        }
        $this->html .= $this->renderForm();
        return $this->html;
    }
    
    public function isSubmit()
    {
        foreach ($this->getAllowedSubmits() as $submit) {
            if (Tools::isSubmit($submit)) {
                return true;
            }
        }
    }
    
    public function getAllowedSubmits()
    {
        $submits = array();
        foreach ($this->getForms() as $model) {
            $submits[] = get_class($model);
        }
        return $submits;
    }
    
    public function postProcess()
    {
        foreach ($this->getForms() as $model) {
            if (Tools::isSubmit(get_class($model))) {
                $model->populate();
                if ($model->saveToConfig()) {
                    $this->html .= $this->displayConfirmation($this->l('Settings updated'));
                } else {
                    $this->postValidate();
                }
            }
        }
        $this->_clearCache('head.tpl');
    }
    
    public function postValidate()
    {
        foreach ($this->getForms() as $model) {
            if (Tools::isSubmit(get_class($model))) {
                $model->loadFromConfig();
                $model->populate();
                if (!$model->validate()) {
                    foreach ($model->getErrors() as $errors) {
                        foreach ($errors as $error) {
                            $this->html .= $this->displayError($error);
                        }
                    }
                    return false;
                }
                return true;
            }
        }
    }
    
    public function render($file, $params = array())
    {
        $this->smarty->assign($params);
        return $this->display(__FILE__, $file);
    }
    
    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='
            .$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'path' => $this->getPath(),
        );
        // $helper->base_folder =  dirname(__FILE__);
        // $helper->base_tpl = '/views/templates/admin/leogallery/helpers/form/form.tpl';
        
        $this->smarty->assign(array(
            'form' => $helper,
            'formParams' => array($this->getForm($this->configModel)),
            'sliderFormParams' => array($this->getForm($this->sliderConfig)),
            'sliderTabletFormParams' => array($this->getForm($this->sliderTabletConfig)),
            'sliderMobileFormParams' => array($this->getForm($this->sliderMobileConfig)),
            'magnifyFormParams' => array($this->getForm($this->magnifyConfig)),
            'link' => $this->context->link,
            'path' => $this->getPath(),
            'active_tab' => $this->getActiveTab(),
            'name' => $this->displayName,
            'version' => $this->version,
        ));
        return $this->display(__FILE__, 'configuration.tpl');
    }
    
    public function getActiveTab()
    {
        foreach ($this->getForms() as $model) {
            if (Tools::isSubmit(get_class($model))) {
                return get_class($model);
            }
        }
        return null;
    }
    
    public function getConfigFieldsValues()
    {
        $values = array();
        foreach ($this->getForms() as $model) {
            $model->loadFromConfig();
            $model->populate();
            foreach ($model->getAttributes() as $attr => $value) {
                $values[$model->getConfigAttribueName($attr)] = $value;
            }
        }
        return $values;
    }
    
    public function getForms()
    {
        return array(
            $this->configModel,
            $this->sliderConfig,
            $this->sliderTabletConfig,
            $this->sliderMobileConfig,
            $this->magnifyConfig
        );
    }
    
    public function getConfigModel()
    {
        return $this->configModel;
    }
    
    public function getFormConfigs()
    {
        $configs = array();
        foreach ($this->getForms() as $form) {
            $configs[] = $this->getForm($form);
        }
        return $configs;
    }
    
    public function getForm($model)
    {
        $model->populate();
        $model->validate(false);
        $config = $model->getFormHelperConfig();
        return array(
            'form' => array(
                'name' => get_class($model),
                'legend' => array(
                    'title' => $model->getFormTitle(),
                    'icon' => $model->getFormIcon()
                ),
                'input' => $config,
                'submit' => array(
                    'name' => get_class($model),
                    'class' => null,
                    'title' => $this->l('Save'),
                )
            )
        );
    }
    
    public function hookDisplayHeader($params)
    {
        $this->registerHook('displayAfterProductThumbs');
        if ($this->isTablet()) {
            if (!$this->sliderTabletConfig->isLoaded()) {
                $this->sliderTabletConfig->loadFromConfig();
            }
        } elseif ($this->isMobile()) {
            if (!$this->sliderMobileConfig->isLoaded()) {
                $this->sliderMobileConfig->loadFromConfig();
            }
        } else {
            if (!$this->sliderConfig->isLoaded()) {
                $this->sliderConfig->loadFromConfig();
            }
        }

        if (!$this->magnifyConfig->isLoaded()) {
            $this->magnifyConfig->loadFromConfig();
        }
        
        if ($this->isMobile() || $this->isTablet()) {
            $this->magnifyConfig->enable = false;
        }

        $this->context->controller->addCss($this->_path.'views/css/lightgallery.min.css');
        if ($this->configModel->mousewheel) {
            $this->context->controller->addJs($this->_path.'views/js/jquery.mousewheel.min.js');
        }
        $this->context->controller->addJs($this->_path.'views/js/lightgallery.min.js');
        if ($this->configModel->thumbnails) {
            $this->context->controller->addJs($this->_path.'views/js/lg-thumbnail.min.js');
        }
        if ($this->configModel->fullscreen) {
            $this->context->controller->addJs($this->_path.'views/js/lg-fullscreen.min.js');
        }
        if ($this->configModel->zoom) {
            $this->context->controller->addJs($this->_path.'views/js/lg-zoom.min.js');
        }
        if ($this->configModel->autoplay) {
            $this->context->controller->addJs($this->_path.'views/js/lg-autoplay.min.js');
        }
        if ($this->configModel->hash) {
            $this->context->controller->addJs($this->_path.'views/js/lg-hash.min.js');
        }

        $this->layout_from_profile = $this->getLayoutFromProfile();
        $sliderConfig = '';
        if (
            (($this->isMobile() && $this->sliderMobileConfig->enabled)
                || ($this->isTablet() && $this->sliderTabletConfig->enabled)
                || (!$this->isMobile() && !$this->isTablet() && $this->sliderConfig->enabled)
            )
            && (!Module::isEnabled('leoelements') || (Module::isEnabled('leoelements') && is_array($this->layout_from_profile) && $this->layout_from_profile))
        ) {
            $this->context->controller->addJs($this->_path.'views/js/lightslider.min.js');
            $this->context->controller->addCss($this->_path.'views/css/lightslider.min.css');
        }
        if ($this->magnifyConfig->enable || $this->layout_from_profile) {
            if ($this->magnifyConfig->type == 'out' || $this->magnifyConfig->type == 'in' || (isset($this->layout_from_profile['zoom_type']) && $this->layout_from_profile['zoom_type'] == 'out') || (isset($this->layout_from_profile['zoom_type']) && $this->layout_from_profile['zoom_type'] == 'in')) {
                $this->context->controller->addJs($this->_path.'views/js/zoomsl-3.0.js');
            } else {
                $this->context->controller->addJs($this->_path.'views/js/jquery.magnify.js');
                $this->context->controller->addJs($this->_path.'views/js/jquery.magnify-mobile.js');
                $this->context->controller->addCss($this->_path.'views/css/magnify.css');
            }
        }
    }
    
    public function isMobile()
    {
        if ($this->isMobile === null) {
            $this->isMobile = Context::getContext()->getMobileDetect()->isMobile();
        }
        return $this->isMobile;
    }

    public function isTablet()
    {
        if ($this->isTablet === null) {
            $this->isTablet = Context::getContext()->getMobileDetect()->isTablet();
        }
        return $this->isTablet;
    }
    
    public function hookDisplayAfterProductThumbs($params)
    {
        if (Tools::getValue('content_only')) {
            return null;
        }
        if (!$this->configModel->isLoaded()) {
            $this->configModel->loadFromConfig();
        }
        $controller = Context::getContext()->controller;
        if (($controller instanceof ProductControllerCore || $controller instanceof ProductController) && is_array($this->layout_from_profile)) {
            $product = $controller->getProduct();
            $config = $this->loadConfigBefore();
            
            $link = Context::getContext()->link;
            $ir = new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($link);
            $pr = $controller->getTemplateVarProduct();
            $ipa = $pr['id_product_attribute'];
            $images = $ir->getProductImages(array(
                'id_product' => $product->id,
                'id_product_attribute' => !$config['allImages']? $ipa : 0
            ), Context::getContext()->language);
            if (($this->isMobile() && $this->sliderMobileConfig->enabled) || ($this->isTablet() && $this->sliderTabletConfig->enabled)
                || (!$this->isMobile() && !$this->isTablet() && $this->sliderConfig->enabled)) {
                return $this->render('gallery.tpl', array_merge(array(
                    'images' => $images,
                    'quickview' => Tools::getValue('action') == 'quickview' ? 1 : 0,
                ), $config));
            }
        }
        return null;
    }

    /**
     * Run only one when install/change Theme_of_Leo
     */
    public function hookActionAdminBefore($params)
    {
        if (isset($params) && isset($params['controller']) && isset($params['controller']->theme_manager)) {
            // Validate : call hook from theme_manager
        } else {
            // Other module call this hook -> duplicate data
            return;
        }
        
        $this->unregisterHook('actionAdminBefore');
        
        # FIX : update Prestashop by 1-Click module -> NOT NEED RESTORE DATABASE
        $ap_version = Configuration::get('AP_CURRENT_VERSION');
        if ($ap_version != false) {
            $ps_version = Configuration::get('PS_VERSION_DB');
            $versionCompare = version_compare($ap_version, $ps_version);
            if ($versionCompare != 0) {
                // Just update Prestashop
                Configuration::updateValue('AP_CURRENT_VERSION', $ps_version);
                return;
            }
        }
        
        # WHENE INSTALL THEME, INSERT HOOK FROM DATASAMPLE IN THEME
        $primary_module = 'appagebuilder';
        if (file_exists(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php')) {
            $primary_module = 'leoelements';
        }
        $hook_from_theme = false;
        if (file_exists(_PS_MODULE_DIR_ .$primary_module.'/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_ .$primary_module.'/libs/LeoDataSample.php');
            $sample = new Datasample();
            if ($sample->processHook($this->name)) {
                $hook_from_theme = true;
            };
        }
        
        # INSERT HOOK FROM MODULE_DATASAMPLE
        if ($hook_from_theme == false) {
            $this->installHook();
        }
        
        # WHEN INSTALL MODULE, NOT NEED RESTORE DATABASE IN THEME
        $install_module = (int) Configuration::get('AP_INSTALLED_LEOGALLERY', 0);
        if ($install_module) {
            Configuration::updateValue('AP_INSTALLED_LEOGALLERY', '0');    // next : allow restore sample
            return;
        }

        # INSERT DATABASE FROM THEME_DATASAMPLE
        if (file_exists(_PS_MODULE_DIR_ .$primary_module.'/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_ .$primary_module.'/libs/LeoDataSample.php');
            $sample = new Datasample();
            $sample->processImport($this->name);
        }
    }

    public function loadConfigBefore()
    {
        $sliderConfig = '';
        if ($this->isTablet()) {
            if (!$this->sliderTabletConfig->isLoaded()) {
                $this->sliderTabletConfig->loadFromConfig();
            }
            $sliderConfig = $this->sliderTabletConfig;
            $allImages = $this->sliderTabletConfig->all_images;
        } elseif ($this->isMobile()) {
            if (!$this->sliderMobileConfig->isLoaded()) {
                $this->sliderMobileConfig->loadFromConfig();
            }
            $sliderConfig = $this->sliderMobileConfig;
            $allImages = $this->sliderMobileConfig->all_images;
        } else {
            if (!$this->sliderConfig->isLoaded()) {
                $this->sliderConfig->loadFromConfig();
            }
            $sliderConfig = $this->sliderConfig;
            $allImages = $this->sliderConfig->all_images;
        }

        $thumbSize = ($this->isMobile() || $this->isTablet()) ? $this->configModel->m_thumb_size : $this->configModel->thumb_size;
        $row = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'image_type`
            WHERE `name` = "'.pSQL($thumbSize).'"');
        $this->configModel->thumbHeight = $row['height'];
        $this->configModel->thumbWidht = $row['width'];

        // print_r($this->layout_from_profile);die;
        return array(
            'config' => $this->configModel,
            'magnify' => $this->magnifyConfig,
            'isMobile' => $this->isMobile(),
            'path' => $this->getPathUri(), 
            'sliderConfig' => $sliderConfig,
            'layout_from_profile' => $this->layout_from_profile,
            'allImages' => $allImages,
        );
    }

    public function getLayoutFromProfile()
    {
        $layout_from_profile = array();
        if (Module::isEnabled('leoelements') && Dispatcher::getInstance()->getController() == 'product') {
            if (Tools::getIsset('layout')) {
                $pdeail_layout = Tools::getValue('layout');
            } else {
                $product = new Product(Tools::getValue('id_product'));
                if(isset($product->leoe_layout) && $product->leoe_layout && $product->leoe_layout != 'default') {
                    $pdeail_layout = $product->leoe_layout;
                }
                if($this->isTablet() && isset($product->leoe_layout_tablet) && $product->leoe_layout_tablet && $product->leoe_layout_tablet != 'default') {
                    $pdeail_layout = $product->leoe_layout_tablet;
                }
                if ($this->isMobile() && isset($product->leoe_layout_mobile) && $product->leoe_layout_mobile && $product->leoe_layout_mobile != 'default') {
                    $pdeail_layout = $product->leoe_layout_mobile;
                }
            }
            if (isset($pdeail_layout) && $pdeail_layout) {
                // get from pdeail_layout data
                require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductsModel.php');
                $layout_detail_data = array();
                $use_leo_gallery = 0;
                $data_column = '';
                $layout_data = LeoElementsProductsModel::getProductProfileByKey($pdeail_layout);
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

                $use_leo_gallery_layout = (isset($data_column['use_leo_gallery']) && $data_column['use_leo_gallery']) ? 1 : 0;

                if ($use_leo_gallery != 1) {
                    $use_leo_gallery = $use_leo_gallery_layout;
                }
                if ($data_column && isset($data_column['templateview'])){
                    $layout_from_profile['use_leo_gallery'] = $use_leo_gallery_layout;
                    $layout_from_profile['mode'] = $data_column['templateview'] == 'bottom' ? 'horizontal' : 'vertical';
                    $layout_from_profile['layout'] = $data_column['templateview'];
                    $layout_from_profile['zoom_type'] = $data_column['templatezoomtype'];
                    $layout_from_profile['column'] = $data_column['numberimage1200'];
                    $layout_from_profile['column_t'] = $data_column['numberimage768'];
                    $layout_from_profile['column_m'] = $data_column['numberimage480'];
                } else {
                    return 0;
                }
            } else {
                // get from profile data
                if (!class_exists('LeoElementsProfilesModel')) {
                    require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');
                }
                $model = new LeoElementsProfilesModel();
                $use_profiles = LeoElementsProfilesModel::getActiveProfile('index', $model->getAllProfileByShop());
                $params_profile = ($use_profiles && isset($use_profiles['params'])) ? json_decode($use_profiles['params'], true) : array();
                $device = '';
                if ($this->isTablet()) {
                    $device = '_tablet';
                } elseif ($this->isMobile()) {
                    $device = '_mobile';
                }
                $layout_from_profile = ($params_profile['thumb_product_layout'.$device] && $params_profile['thumb_product_layout'.$device]['use_leo_gallery']) ? $params_profile['thumb_product_layout'.$device] : 0;
            }
        } elseif (Module::isEnabled('leoelements')) {
            // get from profile data
            if (!class_exists('LeoElementsProfilesModel')) {
                require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');
            }
            $model = new LeoElementsProfilesModel();
            $use_profiles = LeoElementsProfilesModel::getActiveProfile('index', $model->getAllProfileByShop());
            $params_profile = ($use_profiles && isset($use_profiles['params'])) ? json_decode($use_profiles['params'], true) : array();
            $device = '';
            if ($this->isTablet()) {
                $device = '_tablet';
            } elseif ($this->isMobile()) {
                $device = '_mobile';
            }
            if ($params_profile && isset($params_profile['thumb_product_layout'.$device])) {
                $layout_from_profile = ($params_profile['thumb_product_layout'.$device] && $params_profile['thumb_product_layout'.$device]['use_leo_gallery']) ? $params_profile['thumb_product_layout'.$device] : 0;
            }
        }
        if ($layout_from_profile) {
            if ($this->isTablet()) {
                $layout_from_profile['column'] = $layout_from_profile['column_t'];
            } elseif ($this->isMobile()) {
                $layout_from_profile['column'] = $layout_from_profile['column_m'];
            }
        }
        return $layout_from_profile;
    }
    
    public function getPath()
    {
        return $this->_path;
    }

    public function getConfigName()
    {
        return Tools::strtoupper(self::getThemeKey().'_'.$name);
    }

    public static function getThemeKey($module_key = 'leo_module')
    {
        static $theme_key;
        if (!$theme_key) {
            #CASE : load theme_key from ROOT/THEMES/THEME_NAME/config.xml
            $xml = self::getThemeInfo(self::getThemeName());
            if (isset($xml->theme_key)) {
                $theme_key = trim((string)$xml->theme_key);
            }
        }
        if (!$theme_key && !empty($module_key)) {
            #CASE : default load from module_key
            $theme_key = $module_key;
        }
        return $theme_key;
    }

    /*
     * get theme in SINGLE_SHOP or MULTI_SHOP
     */
    public static function getThemeName()
    {
        static $theme_name;
        if (!$theme_name) {
            # DEFAULT SINGLE_SHOP
            $theme_name = _THEME_NAME_;

            # GET THEME_NAME MULTI_SHOP
            if (Shop::getTotalShops(false, null) >= 2) {
                $id_shop = Context::getContext()->shop->id;

                $shop_arr = Shop::getShop($id_shop);
                if (is_array($shop_arr) && !empty($shop_arr)) {
                    $theme_name = $shop_arr['theme_name'];
                }
            }
        }
        
        return $theme_name;
    }

    public static function getThemeInfo($theme)
    {
        $xml = _PS_ALL_THEMES_DIR_.$theme.'/config.xml';

        $output = array();

        if (file_exists($xml)) {
            $output = simplexml_load_file($xml);
        }

        return $output;
    }
}
