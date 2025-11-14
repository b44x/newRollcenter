<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'leoextratab/LeoTab.php';

class Leoextratab extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'leoextratab';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Leotheme';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Leo Extra Tab');
        $this->description = $this->l('This is great module for create more Tab');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst('Management');
        $id_parent = Tab::getIdFromClassName('IMPROVE');
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = $id_parent;
        $langs = Language::getLanguages(false);

        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $this->l('Leo Extra Tab');
        }

        $tab1->add(true, false);

        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'tab`
            SET `icon` = "tab"
            WHERE `id_tab` = "'.(int)$tab1->id.'"'
        );

        $position = Tab::getIdFromClassName('AdminLeoextratabManagement');

        $class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst('setting');
        $id_parent = (int)$position;
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = $id_parent;
        $langs = Language::getLanguages(false);

        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $this->l('Tab Setting');
        }

        $tab1->add(true, false);

        $class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst('tab');
        $id_parent = (int)$position;
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = $id_parent;
        $langs = Language::getLanguages(false);

        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $this->l('Tab List');
        }

        $tab1->add(true, false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('actionAdminControllerSetMedia');
    }

    private function uninstallModuleTab($class_sfx = '')
    {
        $tab_class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);
        $id_tab = Tab::getIdFromClassName($tab_class);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }

    public function uninstall()
    {
        $this->uninstallModuleTab('setting');
        $this->uninstallModuleTab('tab');
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall()
            && $this->unregisterHook('header')
            && $this->unregisterHook('backOfficeHeader')
            && $this->unregisterHook('displayBackOfficeHeader')
            && $this->unregisterHook('actionAdminControllerSetMedia');
    }

    public function getContent()
    {
        $output = '';
        $errors = array();
        
        if (((bool)Tools::isSubmit('submitLeoextratabModule')) == true) {
            if (isset($errors) && count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                $this->postProcess();
                $output = $this->displayConfirmation(
                    $this->trans(
                        'The settings have been updated.',
                        array(),
                        'Admin.Notifications.Success'
                    )
                );
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLeoextratabModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $content = $helper->generateForm($this->getConfigForm());

        $this->context->smarty->assign(array(
            'content' => $content,
            'url_admin' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
            'baseurl' => $this->context->shop->getBaseURL(true, true),
        ));

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/setting.tpl');
    }

    protected function getConfigForm()
    {
        $fields_form = array();
        $tab_theme = '';
        if (Module::isInstalled('appagebuilder') && Module::isEnabled('appagebuilder')) {
            include_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderDetailsModel.php');
            $apDetailModel = new ApPageBuilderDetailsModel();
            $profile = $apDetailModel->getActive();
            if (Tools::strpos($profile['params'], 'product_more_info_tab')) {
                $tab_theme = 'Horizontal';
            } elseif (Tools::strpos($profile['params'], 'product_more_info_default')) {
                $tab_theme = 'Vertical';
            } elseif (Tools::strpos($profile['params'], 'product_more_info_accordions')) {
                $tab_theme = 'Accordion';
            }
        }
        $switch = array(
            array(
                'id' => 'active_on',
                'value' => '1',
                'label' => $this->l('Yes')
            ),
            array(
                'id' => 'active_off',
                'value' => '0',
                'label' => $this->l('No')
            )
        );

        $fields_form[0]['form'] = array(
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active Module Tab'),
                    'name' => 'LEOEXTRATAB_ACTIVE',
                    'is_bool' => true,
                    'values' => $switch
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Empty Tab'),
                    'name' => 'LEOEXTRATAB_SHOW_EMPTY_TAB',
                    'is_bool' => true,
                    'values' => $switch
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'LEOEXTRATAB_TYPE_TAB',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '1',
                                'name' => $this->l('Horizontal')
                            ),
                            array(
                                'id' => '2',
                                'name' => $this->l('Vertical')
                            ),
                            array(
                                'id' => '3',
                                'name' => $this->l('Accordion')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'desc' => $tab_theme ? $this->l('You are using type tab').': '.$tab_theme.'. Please choose tab '.$tab_theme.' for option.' : '',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default'
            )
        );

        return $fields_form;
    }

    protected function getConfigFormValues()
    {
        return array(
            'LEOEXTRATAB_ACTIVE' => Configuration::get('LEOEXTRATAB_ACTIVE'),
            'LEOEXTRATAB_SHOW_EMPTY_TAB' => Configuration::get('LEOEXTRATAB_SHOW_EMPTY_TAB'),
            'LEOEXTRATAB_TYPE_TAB' => Configuration::get('LEOEXTRATAB_TYPE_TAB'),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $tabs = Tools::getValue('leo_tab');

        if (!empty($tabs)) {
            $list_id = implode(',', $tabs);

            Configuration::updateValue('LEOEXTRATAB_LIST_ID', $list_id);
        }

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (_PS_VERSION_ >= '1.7.7.0'){
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        } else {
            if (Tools::getValue('module_name') == $this->name) {
                $this->context->controller->addJS($this->_path.'views/js/back.js');
            }
            if (Tools::getValue('module_name') == $this->name) {
                $this->context->controller->addCSS($this->_path.'views/css/back.css');
            }
        }
    }

    public function hookHeader()
    {
        if (Configuration::get('LEOEXTRATAB_ACTIVE') == 1) {
            if (Tools::getIsset('controller') && Tools::getValue('controller') == 'product') {
                $this->context->controller->addJS($this->_path.'/views/js/front.js');
                $this->context->controller->addCSS($this->_path.'/views/css/front.css');
                $content = '';
                if (Configuration::get('LEOEXTRATAB_TYPE_TAB') == 1 ) {
                    $title = $this->createTabHtml('title');
                    $content = $this->createTabHtml('content');
                }else{
                    $content = $this->createTabHtml('tab');
                }
                
                Media::addJsDef(array(
                    'leo_type' => Configuration::get('LEOEXTRATAB_TYPE_TAB'),
                    'leo_title' => isset($title) ? $title : '',
                    'leo_content' => isset($content) ? $content : '',
                    'theme_name' => $this->getThemeName()
                ));
            }
        }
    }

    public function leoCategory($categories, $checked)
    {
        $this->smarty->assign(array(
            'categories' => $categories,
            'checked' => $checked,
        ));

        return $this->display(__FILE__, 'views/templates/admin/category.tpl');
    }

    public function createTabHtml($type)
    {
        $id_product = Tools::getValue('id_product');
        $obj_tab = new LeoTab();
        $tabs = $obj_tab->getTabProduct($id_product);

        if (empty($tabs)) {
            return;
        }
        $this->context->smarty->assign(array(
            'tabs' => $tabs,
            'type' => $type,
        ));
        return $this->display(__FILE__, 'views/templates/admin/leo-tab.tpl');
    }

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

    public static function recurseCategory(
        &$array_categories,
        $categories,
        $current,
        $with_li = true,
        $id_category = 1,
        $id_selected = -1,
        $id_lang = 1,
        $id_shop = 1
    ) {
        if ($id_category != 1) {
            if ($current != null) {
                if ($with_li) {
                    $option = array(
                        'id_category' => $id_category,
                        'category' => str_repeat(
                            '&nbsp;',
                            $current['infos']['level_depth'] * 5
                        ).Tools::stripslashes($current['infos']['name'])
                    );
                } else {
                    $category   = new Category($current['infos']['id_parent'], $id_lang, $id_shop);
                    $option     = array(
                        'id_category' => $id_category,
                        'category' => $category->name.' > '.Tools::stripslashes($current['infos']['name'])
                    );
                }

                if ($id_selected == $id_category) {
                    $option['selected'] = 'selected';
                }

                array_push($array_categories, $option);
            }
        }
        if (isset($categories[$id_category])) {
            foreach (array_keys($categories[$id_category]) as $key) {
                self::recurseCategory(
                    $array_categories,
                    $categories,
                    $categories[$id_category][$key],
                    $with_li,
                    $key,
                    $id_selected,
                    $id_lang,
                    $id_shop
                );
            }
        }
    }
    
    public function hookActionAdminControllerSetMedia()
    {	
    }
}
