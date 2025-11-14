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

require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductListModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsCategoryModel.php');

class AdminLeoElementsDashboardController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        $this->addRowAction('view');
        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        
        $this->page_header_toolbar_title = $this->l('Dashboard');
        $this->page_header_toolbar_btn = array();
    }
    
    public function postProcess()
    {
        $dataSample = new Datasample();
        
        if(Tools::getIsset('backup') && Tools::getValue('backup')) {
            $modules = Tools::getValue('bumodule');
            $dataSample->processBackUp();
            $folder = str_replace('\\', '/', _PS_CACHE_DIR_.'backup/themes/');
            $this->confirmations[] = 'Back-up to PHP file is successful. <br/>' . $folder;
        } elseif (Tools::getIsset('restore') && Tools::getValue('restore')) {
            $dataSample->restoreBackUpFile();
            $this->confirmations[] = 'Restore from PHP file is successful.';
        } elseif (Tools::isSubmit('submitSample')) {
            $dataSample->processSample();
            $folder = str_replace('\\', '/', _PS_ALL_THEMES_DIR_.leoECHelper::getThemeName().'/samples/');
            $this->confirmations[] = 'Export Sample Data is successful. <br/>' . $folder;
        } elseif (Tools::isSubmit('submitRestoreSample')) {
            if (!Tools::getValue("leomodule")) {
                $this->errors[] = 'Please select at least one module to Restore Sample Data.';
            } else {
                $dataSample->processImport();
                $this->confirmations[] = 'Restore Sample Data is successful.';
            }
        } elseif (Tools::isSubmit('submitExportDBStruct')) {
            $dataSample->exportDBStruct();
            $dataSample->exportThemeSql();
            $folder = str_replace('\\', '/', _PS_MODULE_DIR_.'leoelements/install');
            $this->confirmations[] = 'Export Data Struct is successful. <br/>' . $folder;
        } elseif (Tools::isSubmit('submitConfig')) {
            Configuration::updateValue('LEOELEMENTS_PANEL_TOOL', Tools::getValue('LEOELEMENTS_PANEL_TOOL'));
            $this->confirmations[] = 'Saved Config. <br/>';
        } elseif (Tools::isSubmit('saveCSS')) {
            LeoECSetting::writeFile(leoECHelper::getConfigDir('_PS_THEME_DIR_').leoECHelper::getCssDir(), 'custom.css', Tools::getValue('customcss'));
            $this->confirmations[] = 'Saved CSS. <br/>';
        }

        if (count($this->errors)) {
            return false;
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('tagify');
        Context::getContext()->controller->addJs(leoECHelper::getJsAdminDir().'admin/function.js');
        Context::getContext()->controller->addCss(leoECHelper::getCssAdminDir().'back.css');
    }

    public function renderView()
    {
        $sample = new Datasample();
        $moduleList = $sample->getModuleList();
        $struct = array();
        if(file_exists(_PS_MODULE_DIR_.'leoelements/install/db_data.sql')) {
            $struct['db_data'] = Context::getContext()->shop->physical_uri.'modules/leoelements/install/db_data.sql';
        }
        if(file_exists(_PS_MODULE_DIR_.'leoelements/install/db_structure.sql')) {
            $struct['db_structure'] = Context::getContext()->shop->physical_uri.'modules/leoelements/install/db_structure.sql';
        }
        $lecount = array();

        $quick_link = array();
        $quick_link['mpr'] = $this->context->link->getAdminLink('AdminLeoElementsProfiles');
        $quick_link['apr'] = $this->context->link->getAdminLink('AdminLeoElementsProfiles').'&addleoelements_profiles';
        $quick_link['position'] = $this->context->link->getAdminLink('AdminLeoElementsPositions');
        $quick_link['addposition'] = $this->context->link->getAdminLink('AdminLeoElementsPositions').'&addleoelements_positions';
        $quick_link['hook'] = $this->context->link->getAdminLink('AdminLeoElementsProfiles');
        $quick_link['addhoook'] = $this->context->link->getAdminLink('AdminLeoElementsProfiles').'&addleoelements_profiles';

        $quick_link['category'] = $this->context->link->getAdminLink('AdminLeoElementsCategory');
        $quick_link['addcategory'] = $this->context->link->getAdminLink('AdminLeoElementsCategory').'&addleoelements_category';

        $quick_link['product'] = $this->context->link->getAdminLink('AdminLeoElementsProducts');
        $quick_link['addproduct'] = $this->context->link->getAdminLink('AdminLeoElementsProducts').'&addleoelements_products';

        $quick_link['productlist'] = $this->context->link->getAdminLink('AdminLeoElementsProductList');
        $quick_link['addproductlist'] = $this->context->link->getAdminLink('AdminLeoElementsProductList').'&addleoelements_product_list';

        $profiles = LeoElementsProfilesModel::getAllProfileByShop();
        $lecount['profile'] = count($profiles);
        $all_po = LeoElementsPositionsModel::getAllPosition();
        $lecount['position'] = count($all_po);
        $colist = array();
        $all_co = LeoElementsContentsModel::getAllContents();
        $lecount['content'] = count($all_co);
        foreach($all_co as $p) {
            $colist[$p['content_key']] = $p;
        }

        $polist = array();
        foreach($all_po as $p) {
            $params = json_decode($p['params']);
            if($params) {
                foreach ($params as $pa=>$pac) {
                    if(isset($colist[$pac])) {
                        $p[$pa] = $colist[$pac];
                    }
                }
            }
            $polist[$p['position_key']] = $p;
        }

        $mpdetail = new LeoElementsProductsModel();
        $all_pdetail = $mpdetail->getAllProductProfileByShop();
        $lecount['detail'] = count($all_pdetail);
        $cpdlist = array();
        foreach($all_pdetail as $p) {
            $cpdlist[$p['plist_key']]['link'] = $this->context->link->getAdminLink('AdminLeoElementsProducts').'&updateleoelements_products&id_leoelements_products='.$p['id_leoelements_products'];
            $cpdlist[$p['plist_key']]['id'] = $p['id_leoelements_products'];
            $cpdlist[$p['plist_key']]['name'] = $p['name'];
        }

        $mplist = new LeoElementsProductListModel();
        $all_plist = $mplist->getAllProductListProfileByShop();
        $lecount['productlist'] = count($all_plist);
        $cplist = array();
        foreach($all_plist as $p) {
            $cplist[$p['plist_key']]['link'] = $this->context->link->getAdminLink('AdminLeoElementsProductList').'&updateleoelements_product_list&id_leoelements_product_list='.$p['id_leoelements_product_list'];
            $cplist[$p['plist_key']]['id'] = $p['id_leoelements_product_list'];
            $cplist[$p['plist_key']]['name'] = $p['name'];
        }

        $mcat = new LeoElementsCategoryModel();
        $all_cat = $mcat->getAllCategoryProfileByShop();
        $lecount['category'] = count($all_cat);
        $ccat = array();
        foreach($all_cat as $p) {
            $ccat[$p['clist_key']]['link'] = $this->context->link->getAdminLink('AdminLeoElementsCategory').'&updateleoelements_category&id_leoelements_category='.$p['id_leoelements_category'];
            $ccat[$p['clist_key']]['id'] = $p['id_leoelements_category'];
            $ccat[$p['clist_key']]['name'] = $p['name'];
        }
        
        foreach($profiles as &$profile) {
            $profile['params'] = json_decode($profile['params']);
            $profile['link'] = $this->context->link->getAdminLink('AdminLeoElementsProfiles').'&updateleoelements_profiles=&id_leoelements_profiles='.$profile['id_leoelements_profiles'];
            $lang = '';
            $admin_dir = dirname($_SERVER['PHP_SELF']);
            $admin_dir = Tools::substr($admin_dir, strrpos($admin_dir, '/') + 1);
            $dir = str_replace($admin_dir, '', dirname($_SERVER['SCRIPT_NAME']));
            $id_lang = (int)$this->context->language->id;
            if (Configuration::get('PS_REWRITING_SETTINGS') && count(Language::getLanguages(true)) > 1) {
                $lang = Language::getIsoById(Context::getContext()->employee->id_lang).'/';
            }
            $profile['preview'] = Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().$dir.$lang.Dispatcher::getInstance()->createUrl('index', (int)Context::getContext()->language->id, ['id_leoelements_profiles' => $profile['id_leoelements_profiles']]);

            foreach(array('header' => $this->l('Header'), 'content' => $this->l('Content'), 'footer' => $this->l('Footer')) as $pk=>$p) {
                $position = array();
                $position['hooks'] = array();
                $position['pos_name'] = $p;
                $position['edit1'] = $profile['link'].'#fieldset_'.$pk;

                if(isset($polist[$profile[$pk]])) {
                    $hooks = LeoECSetting::getHook($pk);
                    
                    $position['id'] = $polist[$profile[$pk]]['id_leoelements_positions'];
                    $position['name'] = $polist[$profile[$pk]]['name'];
                    $position['pos_name'] = $p;
                    
                    $position['edit'] = $this->context->link->getAdminLink('AdminLeoElementsPositions').'&updateleoelements_positions=&id_leoelements_positions='.$position['id'];

                    foreach($hooks as $hook) {
                        if(isset($polist[$profile[$pk]][$hook])) {
                            $row = $polist[$profile[$pk]][$hook];

                            $polist[$profile[$pk]][$hook]['furl'] = $this->context->link->getAdminLink('AdminLeoElementsCreator').'&post_type=hook&id_post='.$row['id_leoelements_contents'].'&id_lang='.$id_lang.'&id_profile='.$profile['id_leoelements_profiles'];
                            $position['hooks'][] = $polist[$profile[$pk]][$hook];
                        }
                    }
                }
                $profile['positions'][] = $position;
            }
            
            foreach(array('category') as $p) {
                $hooks = LeoECSetting::getHook($p);
                foreach($hooks as $hook) {
                    if(isset($profile['params']->$hook) && isset($colist[$profile['params']->$hook])) {
                        $cc = $colist[$profile['params']->$hook];
                        $profile[$p.'_hook'][$hook] = array( 'hook' => $hook, 'furl' => $this->context->link->getAdminLink('AdminLeoElementsCreator').'&post_type=hook_category_layout&id_post='.$cc['id_leoelements_contents'].'&id_lang='.$id_lang.'&id_profile='.$profile['id_leoelements_profiles']);
                    } else {
                        $profile[$p.'_hook'][$hook] = array( 'hook' => $hook, 'burl' => $profile['link'].'#fieldset_'.$p);
                    }
                }
            }
            foreach(array('product') as $p) {
                $hooks = LeoECSetting::getHook($p);
                foreach($hooks as $hook) {
                    if(isset($profile['params']->$hook) && isset($colist[$profile['params']->$hook])) {
                        $cc = $colist[$profile['params']->$hook];
                        $profile[$p.'_hook'][$hook] = array( 'hook' => $hook, 'furl' => $this->context->link->getAdminLink('AdminLeoElementsCreator').'&post_type=hook_product_layout&id_post='.$cc['id_leoelements_contents'].'&id_lang='.$id_lang.'&id_profile='.$profile['id_leoelements_profiles']);
                    } else {
                        $profile[$p.'_hook'][$hook] = array( 'hook' => $hook, 'burl' => $profile['link'].'#fieldset_'.$p);
                    }
                }
            }
            $profile_pc = array('desktop' => array(), 'tablet' => array(), 'mobile' => array());
            if($profile['params']->productdetail_layout && isset($cpdlist[$profile['params']->productdetail_layout])) {
                $profile_pc['desktop'] = $cpdlist[$profile['params']->productdetail_layout];
            }
            if($profile['params']->productdetail_layout_mobile && isset($cpdlist[$profile['params']->productdetail_layout_mobile])) {
                $profile_pc['mobile'] = $cpdlist[$profile['params']->productdetail_layout_mobile];
            }
            if($profile['params']->productdetail_layout_tablet && isset($cpdlist[$profile['params']->productdetail_layout_tablet])) {
                $profile_pc['tablet'] = $cpdlist[$profile['params']->productdetail_layout_tablet];
            }
            $profile['pdetail'] = $profile_pc;

            $profile_pc = array('desktop' => array(), 'tablet' => array(), 'mobile' => array());
            if($profile['params']->productlist_layout && isset($cplist[$profile['params']->productlist_layout])) {
                $profile_pc['desktop'] = $cplist[$profile['params']->productlist_layout];
            }
            if($profile['params']->productlist_layout_mobile && isset($cplist[$profile['params']->productlist_layout_mobile])) {
                $profile_pc['mobile'] = $cplist[$profile['params']->productlist_layout_mobile];
            }
            if($profile['params']->productlist_layout_tablet && isset($cplist[$profile['params']->productlist_layout_tablet])) {
                $profile_pc['tablet'] = $cplist[$profile['params']->productlist_layout_tablet];
            }
            $profile['plist'] = $profile_pc;

            $profile_pc = array('desktop' => array(), 'tablet' => array(), 'mobile' => array());
            if($profile['params']->category_layout && isset($ccat[$profile['params']->category_layout])) {
                $profile_pc['desktop'] = $ccat[$profile['params']->category_layout];
            }
            if($profile['params']->category_layout_mobile && isset($ccat[$profile['params']->category_layout_mobile])) {
                $profile_pc['mobile'] = $ccat[$profile['params']->category_layout_mobile];
            }
            if($profile['params']->category_layout_tablet && isset($ccat[$profile['params']->category_layout_tablet])) {
                $profile_pc['tablet'] = $ccat[$profile['params']->category_layout_tablet];
            }
            $profile['clist'] = $profile_pc;
        }

        /* B Custom CSS */
        if (!file_exists(leoECHelper::getConfigDir('_PS_THEME_DIR_').leoECHelper::getCssDir().'custom.css')) {
            $customCSS = '';
            LeoECSetting::writeFile(leoECHelper::getConfigDir('_PS_THEME_DIR_').leoECHelper::getCssDir(), 'custom.css', $customCSS);
        } else {
            $customCSS = Tools::file_get_contents(leoECHelper::getConfigDir('_PS_THEME_DIR_').leoECHelper::getCssDir().'custom.css');
        }
        /* E Custom CSS */
        
        $this->context->smarty->assign(array(
            'moduleList' => $moduleList,
            'LEOELEMENTS_PANEL_TOOL' => Configuration::get('LEOELEMENTS_PANEL_TOOL'),
            'icon_url' => _MODULE_DIR_.'leoelements/views/img/logo.png',
            'profiles' => $profiles,
            'lecount' => $lecount,
            'quick_link' => $quick_link,
            'le_struct' => $struct,
            'controller_url' => $this->context->link->getAdminLink('AdminLeoElementsDashboard'),
            'backup_dir' => $sample->backup_dir,
            'customCSS' => $customCSS,
        ));

        $output = $this->context->smarty->fetch(_PS_MODULE_DIR_.'/leoelements/views/templates/admin/dashboard.tpl');
        return $output;
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        
        if (count($this->errors) <= 0) {
            # EDIT
            if (!$this->access('edit')) {
                if (Tools::isSubmit('saveConfiguration') &&  Tools::getValue('saveConfiguration')) {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
