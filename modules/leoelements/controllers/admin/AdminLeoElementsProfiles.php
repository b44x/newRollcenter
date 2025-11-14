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
    # module validation
    exit;
}

require_once(_PS_MODULE_DIR_.'leoelements/leoECHelper.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsPositionsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsContentsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductListModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsCategoryModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsFont.php');
require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoFrameworkHelper.php');

class AdminLeoElementsProfilesController extends ModuleAdminController
{
    private $theme_name = '';
    public $profile_js_folder = '';
    public $profile_css_folder = '';
    public $module = 'leoelements';
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $theme_dir;
    public $all_postions = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'leoelements_profiles';
        $this->className = 'LeoElementsProfilesModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = false;
        $this->override_folder = 'leo_elementor_config_profiles';

        parent::__construct();
        $this->theme_dir = _PS_THEME_DIR_;

        $this->context = Context::getContext();

        $this->order_by = 'page';
        $this->order_way = 'DESC';
        $alias = 'sa';

        $id_shop = (int)$this->context->shop->id;
        $this->_join .= ' JOIN `'._DB_PREFIX_.'leoelements_profiles_shop`
                sa ON (a.`id_leoelements_profiles` = sa.`id_leoelements_profiles` AND sa.id_shop = '.$id_shop.')';
        $this->_select .= ' sa.active as active, sa.active_mobile as active_mobile, sa.active_tablet as active_tablet';

        $this->fields_list = array(
            'id_leoelements_profiles' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 50,
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!name'
            ),
            'profile_key' => array(
                'title' => $this->l('Key'),
                'filter_key' => 'a!profile_key',
                'type' => 'text',
                'width' => 140,
            ),
            'active' => array(
                'title' => $this->l('Is Default'),
                'active' => 'status',
                'filter_key' => $alias.'!active',
                'align' => 'text-center',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false
            ),
            'active_mobile' => array(
                'title' => $this->l('Is Mobile'),
                'active' => 'active_mobile',
                'filter_key' => $alias.'!active_mobile',
                'align' => 'text-center',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false
            ),
            'active_tablet' => array(
                'title' => $this->l('Is Tablet'),
                'active' => 'active_tablet',
                'filter_key' => $alias.'!active_tablet',
                'align' => 'text-center',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            ),
            'insertLang' => array(
                'text' => $this->l('Auto Input Data for New Lang'),
                'confirm' => $this->l('Auto insert data for new language?'),
                'icon' => 'icon-edit'
            )
        );

        $this->_where = ' AND sa.id_shop='.(int)$this->context->shop->id;
        $this->theme_name = _THEME_NAME_;
        
        $this->profile_css_folder = _PS_THEME_DIR_.leoECHelper::getCssDir().'profiles/';
        $this->profile_js_folder = _PS_THEME_DIR_.leoECHelper::getJsDir().'profiles/';
        
        if (!is_dir($this->profile_css_folder)) {
            mkdir($this->profile_css_folder, 0755, true);
        }
        if (!is_dir($this->profile_js_folder)) {
            mkdir($this->profile_js_folder, 0755, true);
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        
        # SAVE AND STAY
        if ($this->display == 'add' || $this->display == 'edit') {
            // $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/function.js');

            $this->page_header_toolbar_btn['SaveAndStay'] = array(
                'href' => 'javascript:void(0);',
                'desc' => $this->l('Save and stay'),
                'js' => 'TopSaveAndStay()',
                'icon' => 'process-icon-save',
            );
            Media::addJsDef(array('TopSaveAndStay_Name' => 'submitAdd'.$this->table.'AndStay'));
            
            $this->page_header_toolbar_btn['Save'] = array(
                'href' => 'javascript:void(0);',
                'desc' => $this->l('Save'),
                'js' => 'TopSave()',
                'icon' => 'process-icon-save',
            );
            Media::addJsDef(array('TopSave_Name' => 'submitAdd'.$this->table));
        }
    }
    
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('tagify');
        Context::getContext()->controller->addJs(leoECHelper::getJsAdminDir().'admin/function.js');
        Context::getContext()->controller->addCss(leoECHelper::getCssAdminDir().'back.css');
    }

    public function processDelete()
    {
        $object = $this->loadObject();
        $object->loadDataShop();
        
        if ($object && !$object->active) {
            $object = parent::processDelete();
            if ($object->profile_key) {
                Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
                Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
            }
        } else {
            $this->errors[] = Tools::displayError('Can not delete Default Profile.');
        }
        return $object;
    }

    public function ajaxProcessPosition()
    {
        $type = Tools::getValue('type');
        $id_leoelements_profiles = Tools::getValue('id_leoelements_profiles',0);
        
        //create new position: header, content, footer 
        if($type == "position") {
            $position_type = Tools::getValue('position_type');
            $name = Tools::getValue('position_name');

            $sql = 'SELECT name FROM `'._DB_PREFIX_.'leoelements_positions` WHERE name="'.$name.'"';
            $result = Db::getInstance()->getRow($sql);
            if($result) {
                echo json_encode(array('error' => $this->l('This position with the name already exists')));
                die();
            }

            $id_leoelements_profiles = Tools::getValue('id_leoelements_profiles');
            $position = new LeoElementsPositionsModel();

            //create new
            $key = LeoECSetting::getRandomNumber();
            $position->position_key = 'position'.$key;
            $position->name = $name;
            $position->position = $position_type;
            $position->save();
            //insert profile
            $sql = 'UPDATE `'._DB_PREFIX_.'leoelements_profiles` SET `'.$position_type.'` = "'.pSQL($position->position_key).'" WHERE `id_leoelements_profiles` = "'.pSQL($id_leoelements_profiles).'"';
            Db::getInstance()->execute($sql);

            $result = array( 'id' =>$position->position_key, 'title' => $name );
            echo json_encode($result);
            die();
        }
        //create new hook content of possition
        else {
            $hook = Tools::getValue('hook');
            $position = Tools::getValue('position');
            $languages = Language::getLanguages();
            $title = Tools::getValue('titlehook');
            $sql = 'SELECT name FROM `'._DB_PREFIX_.'leoelements_contents` WHERE name="'.$title.'"';
            $result = Db::getInstance()->getRow($sql);
            if($result) {
                echo json_encode(array('error' => $this->l('This hook with the name already exists')));
                die();
            }

            $content = new LeoElementsContentsModel();
            $content->name = $title;
            $content->active = 1;
            $key = LeoECSetting::getRandomNumber();
            $content->content_key = 'content'.$key;
            $content->hook = $hook;
            $content->type = 'hook';
            
            if($content->add()){
                $params = array(
                    'post_type' => 'hook',
                    'id_post' => $content->id,
                    'id_lang' => (int)$this->context->language->id,
                    'id_profile' => $id_leoelements_profiles,
                );
                $url_params = http_build_query($params);
                
                $url = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . $url_params;

                $result = array( 'id' =>$content->id, 'title' => $title, 'url' => $url, 'content_key' =>$content->content_key );
                $id_shop = $this->context->shop->id;
                if (!isset($_SERVER['p'.'r'.'o'.'cessImport'])) {
                    $query = 'INSERT INTO '._DB_PREFIX_.'leoelements_contents_shop (`id_leoelements_contents`, `id_shop`, `active`) VALUES('.(int)$content->id.', '.(int)$id_shop.', 1)';
                    Db::getInstance()->execute($query);
                }
                if($position) {
                    $sql = 'SELECT params FROM '._DB_PREFIX_.'leoelements_positions WHERE position_key="'.pSQL($position).'"';
                    $params = Db::getInstance()->getValue($sql);
                    if($params) {
                        $params = json_decode($params,1);
                        $params[$hook] = $content->content_key;
                        $params = json_encode($params);
                    } else {
                        $params = array($hook => $content->content_key);
                        $params = json_encode($params);
                    }

                    $sql = 'UPDATE '._DB_PREFIX_.'leoelements_positions SET `params` = "'.pSQL($params).'" WHERE `position_key` ="'.pSQL($position).'"';
                    Db::getInstance()->execute($sql);
                } else {
                    //update params profiles
                    $sql = 'SELECT params FROM `'._DB_PREFIX_.'leoelements_profiles` WHERE id_leoelements_profiles="'.$id_leoelements_profiles.'"';
                    $result1 = Db::getInstance()->getRow($sql);
                    $params = json_decode($result1['params'],1);
                    $params[$hook] = $content->content_key;
                    $params = json_encode($params);

                    $sql = 'UPDATE `'._DB_PREFIX_.'leoelements_profiles` SET `params` = "'.pSQL($params).'" WHERE `id_leoelements_profiles` = "'.pSQL($id_leoelements_profiles).'"';
                    Db::getInstance()->execute($sql);
                }
                echo json_encode($result);
                die();
            } else {
                die('error can not create hook');
            }
        }
    }

    public function processBulkDelete()
    {
        $arr = $this->boxes;
        if (!$arr) {
            return;
        }
        foreach ($arr as $id) {
            $object = new $this->className($id);
            $object->loadDataShop();
            if ($object && !$object->active) {
                $object->delete();
                if ($object->profile_key) {
                    Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
                    Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
                }
            } else {
                $this->errors[] = Tools::displayError('Can not delete Default Profile.');
            }
        }
        if (empty($this->errors)) {
            $this->confirmations[] = $this->_conf[1];
        }
    }

    public function renderView()
    {
        $object = $this->loadObject();
        if ($object->page == 'product_detail') {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsProductDetail');
        } else {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsProfiles');
        }
        $this->redirect_after .= '&id_leoelements_profiles='.$object->id;
        $this->redirect();
    }
    
    public function displayPreviewLink($token = null, $id = null, $name = null)
    {
        $lang = '';
        $admin_dir = dirname($_SERVER['PHP_SELF']);
        $admin_dir = Tools::substr($admin_dir, strrpos($admin_dir, '/') + 1);
        $dir = str_replace($admin_dir, '', dirname($_SERVER['SCRIPT_NAME']));
        if (Configuration::get('PS_REWRITING_SETTINGS') && count(Language::getLanguages(true)) > 1) {
            $lang = Language::getIsoById(Context::getContext()->employee->id_lang).'/';
        }
        $href = Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().$dir.$lang.
                Dispatcher::getInstance()->createUrl('index', (int)Context::getContext()->language->id, ['id_leoelements_profiles' => $id]);
        
        
        // validate module
        unset($name);
        $html = '<a target="_blank" href="'.$href.'" class="" title="Preview"><i class="icon-search-plus"></i> Preview</a>';
        return $html;
    }

    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->toggleStatus()) {
                $matches = array();
                if (preg_match('/[\?|&]controller=([^&]*)/', (string)$_SERVER['HTTP_REFERER'], $matches) !== false && Tools::strtolower($matches[1]) != Tools::strtolower(preg_replace('/controller/i', '', get_class($this)))) {
                    $this->redirect_after = preg_replace('/[\?|&]conf=([^&]*)/i', '', (string)$_SERVER['HTTP_REFERER']);
                } else {
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                }
            } else {
                $this->errors[] = Tools::displayError('You can not disable default profile, Please select other profile as default');
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                    .'<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
        return $object;
    }

    public function postProcess()
    {

        parent::postProcess();
        if (count($this->errors) > 0) {
            return;
        }
        if (Tools::getIsset('active_mobileleoelements_profiles') || Tools::getIsset('active_tabletleoelements_profiles')) {
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $result = Tools::getIsset('active_mobileleoelements_profiles')?$object->toggleStatusMT('active_mobile'):$object->toggleStatusMT('active_tablet');
                if ($result) {
                   // $this->mesage[] = Tools::displayError('You should enebale mobile theme in theme config');

                    $matches = array();
                    if (preg_match('/[\?|&]controller=([^&]*)/', (string)$_SERVER['HTTP_REFERER'], $matches) !== false && Tools::strtolower($matches[1]) != Tools::strtolower(preg_replace('/controller/i', '', get_class($this)))) {
                        $this->redirect_after = preg_replace('/[\?|&]conf=([^&]*)/i', '', (string)$_SERVER['HTTP_REFERER']);
                    } else {
                        $this->redirect_after = self::$currentIndex.'&token='.$this->token.'&mobiletheme';
                    }
                } else {
                    $this->errors[] = Tools::displayError('You can not disable default profile, Please select other profile as default');
                }
            } else {
                $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                        .'<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
            }
        }
        if (Tools::getIsset('duplicateleoelements_profiles')) {
            $id = Tools::getValue('id_leoelements_profiles');
            $model = new LeoElementsProfilesModel($id);
            
            if ($model) {
                $old_key = $model->profile_key;
                $model->profile_key = $profile_key = 'profile'.LeoECSetting::getRandomNumber();
                $model->id = null;
                $model->name = $this->l('Duplicate of ') . $model->name;
                $model->active = '';
                $model->friendly_url = array();
                $duplicate_object = $model->save();
                
                if ($duplicate_object) {
                    //duplicate shortCode
                    $id_new = $model->id;
                    LeoECSetting::writeFile($this->profile_js_folder, $profile_key.'.js', Tools::file_get_contents($this->profile_js_folder.$old_key.'.js'));
                    LeoECSetting::writeFile($this->profile_css_folder, $profile_key.'.css', Tools::file_get_contents($this->profile_css_folder.$old_key.'.css'));
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                    $this->redirect();
                } else {
                    Tools::displayError('Can not create new profile');
                }
            } else {
                Tools::displayError('Profile is not exist to duplicate');
            }
        }
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('preview');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        $guide_box = $this->context->smarty->fetch($this->getTemplatePath().'guide-profile.tpl');
        return $guide_box.parent::renderList();
    }

    public function getLiveEditUrl($live_edit_params)
    {
        $lang = '';
        $admin_dir = dirname($_SERVER['PHP_SELF']);
        $admin_dir = Tools::substr($admin_dir, strrpos($admin_dir, '/') + 1);
        $dir = str_replace($admin_dir, '', dirname($_SERVER['SCRIPT_NAME']));
        if (Configuration::get('PS_REWRITING_SETTINGS') && count(Language::getLanguages(true)) > 1) {
            $lang = Language::getIsoById(Context::getContext()->employee->id_lang).'/';
        }
        $url = Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().$dir.$lang.
                Dispatcher::getInstance()->createUrl('index', (int)Context::getContext()->language->id, $live_edit_params);
        return $url;
    }

    public function renderForm()
    {
        $id_leoelements_profiles = (int)Tools::getValue('id_leoelements_profiles', 0);
        $this->initToolbar();
        $header[] = $content[] = $footer[] = array('id' => '0', 'name' => $this->l('No Use'));
        $header[] = $content[] = $footer[] = array('id' => 'createnew', 'name' => $this->l('Create a new position'));
        $this->all_postions = LeoElementsPositionsModel::getAllPosition();
        $hook_header = LeoECSetting::getHook('header');
        $hook_content = LeoECSetting::getHook('content');
        $hook_footer = LeoECSetting::getHook('footer');
        $hook_product = LeoECSetting::getHook('product');
        $hook_category = LeoECSetting::getHook('category');
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;

        $sql = 'SELECT c.id_leoelements_contents, c.name , c.hook, c.content_key FROM '._DB_PREFIX_.'leoelements_contents c INNER JOIN '._DB_PREFIX_.'leoelements_contents_shop cs ON (c.id_leoelements_contents = cs.id_leoelements_contents) WHERE cs.id_shop = '. (int)$id_shop . ' AND c.type != "hook_product_list" ';
        $all_leoelements_contents = Db::getInstance()->executes($sql);
        $leoelements_contents_hook = array();
        foreach ($all_leoelements_contents as $ccontent){
            if(isset($ccontent['hook']) && in_array($ccontent['hook'], $hook_category))
            {
                $params = array(
                    'post_type' => 'hook_category_layout',
                    'id_post' => $ccontent['id_leoelements_contents'],
                    'id_lang' => (int)$id_lang,
                    'id_profile' => $id_leoelements_profiles,
                );
                $url_params = http_build_query($params);
                $ccontent['url'] = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . $url_params;
                $leoelements_contents_hook[$ccontent['hook']][] = $ccontent;
            }elseif (isset($ccontent['hook']) && in_array($ccontent['hook'], $hook_product)) {
                $params = array(
                    'post_type' => 'hook_product_layout',
                    'id_post' => $ccontent['id_leoelements_contents'],
                    'id_lang' => (int)$id_lang,
                    'id_profile' => $id_leoelements_profiles,
                );
                $url_params = http_build_query($params);
                $ccontent['url'] = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . $url_params;
                $leoelements_contents_hook[$ccontent['hook']][] = $ccontent;
            }else{
                $params = array(
                    'post_type' => 'hook',
                    'id_post' => $ccontent['id_leoelements_contents'],
                    'id_lang' => (int)$id_lang,
                    'id_profile' => $id_leoelements_profiles,
                );
                $url_params = http_build_query($params);
                $ccontent['url'] = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . $url_params;
                $leoelements_contents_hook[$ccontent['hook']][] = $ccontent;
            }
        }
        
        $postions = array('header' => [], 'content' => [], 'footer' => []);
        foreach ($this->all_postions as $position) {
            $position['params'] = json_decode($position['params'],1);
            $postions[$position['position']][] = $position;
            if ($position['position'] == 'header') {
                $header[] = array('id' => $position['position_key'], 'name' => $position['name']);
            }
            else if($position['position'] == 'content') {
                $content[] = array('id' => $position['position_key'], 'name' => $position['name']);
            } else {
                $footer[] = array('id' => $position['position_key'], 'name' => $position['name']);
            }
        }

        $is_edit = Tools::getValue('id_leoelements_profiles');
        $layout_mode = array();
        $layout = LeoFrameworkHelper::getLayoutSettingByTheme(_THEME_NAME_);

        if(isset($layout['layout']['layout_mode']['option'])) {
            foreach ($layout['layout']['layout_mode']['option'] as $lay) {
                $layout_mode[] = array('id'=>$lay['id'], 'name'=>$lay['name']);
            }    
        }
        $icon_url = _MODULE_DIR_.'leoelements/views/img/logo.png';
        
        //get category layout
        $catprofile = new LeoElementsCategoryModel();
        $catprofile_list = $catprofile->getAllCategoryProfileByShop();
        $catprofile_list_array = array(
            array('id' => '0', 'name' => $this->l('Default'))
        );
        foreach ($catprofile_list as $pro) {
            $catprofile_list_array[] = array('id' => $pro['clist_key'], 'name' => $pro['name'], 'id_link' => $pro['id_leoelements_category']);
        }
        //get product list layout
        $plist = new LeoElementsProductListModel();
        $product_list = $plist->getAllProductListProfileByShop();
        $product_list_array = array(
            array('id' => '0', 'name' => $this->l('Default'))
        );
        
        foreach ($product_list as $pro) {
            $product_list_array[] = array('id' => $pro['plist_key'], 'name' => $pro['name'], 'id_link' => $pro['id_leoelements_product_list']);
        }
        //get product detail layout
        $proprofile = new LeoElementsProductsModel();
        $proprofile_list = $proprofile->getAllProductProfileByShop();
        $proprofile_list_array = array(
            array('id' => '0', 'name' => $this->l('Default'))
        );
        $position_url = $this->context->link->getAdminLink('AdminLeoElementsPositions').'&updateleoelements_positions=&id_leoelements_positions=';
        foreach ($proprofile_list as $pro) {
            $proprofile_list_array[] = array('id' => $pro['plist_key'], 'name' => $pro['name'], 'id_link' => $pro['id_leoelements_products']);
        }

        $option_group_font_family = array(
            'optiongroup' => array(
                'label' => 'label',
                'query' => array(
                    array(
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
                    ),
                ),

            ),
            'options' => array(
                'query' => 'options',
                'id' => 'id',
                'name' => 'name'
            )
        );

        $fields_form1 = array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'form_group_class' => 'leofieldset fieldset_general',
                    'required' => true,
                    'hint' => $this->l('Invalid characters:'),' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'friendly_url',
                    'desc' => $this->l('This fields to build url for homepage or landing page'),
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                    'form_group_class' => 'leofieldset fieldset_general',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    // 'required' => true,
                    'class' => 'copyMeta2friendlyURL',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                    'form_group_class' => 'leofieldset fieldset_general',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                    'form_group_class' => 'leofieldset fieldset_general',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'form_group_class' => 'leofieldset fieldset_general',
                    'hint' => array(
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."')
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Profile Key'),
                    'name' => 'profile_key',
                    'readonly' => 'readonly',
                    'desc' => $this->l('Use it to save as file name of css and js of profile'),
                    'form_group_class' => 'leofieldset fieldset_general',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Profile List In Site Map'),
                    'name' => 'show_sitemap',
                    'form_group_class' => 'leofieldset fieldset_general',
                    'values' => LeoECSetting::returnYesNo()
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Back to Top'),
                    'name' => 'backtop',
                    'default' => 0,
                    'values' => LeoECSetting::returnYesNo(),
                    'desc' => $this->l('Show a Scroll To Top button.'),
                    'form_group_class' => 'leofieldset fieldset_general',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Layout Mode'),
                    'name' => 'layout_mode',
                    'default' => 'default',
                    'options' => array(
                        'query' => $layout_mode,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'leofieldset fieldset_general',
                ),
                
                array(
                    'type' => 'switch',
                    'label' => $this->l('Header Sticky'),
                    'name' => 'header_sticky',
                    'is_bool' => true,
                    'form_group_class' => 'leofieldset fieldset_header',
                    'values' => LeoECSetting::returnYesNo()
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Footer Fixed'),
                    'name' => 'footer_fixed',
                    'is_bool' => true,
                    'form_group_class' => 'leofieldset fieldset_footer',
                    'values' => LeoECSetting::returnYesNo()
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Close Footer Menu Link'),
                    'name' => 'footer_clinks',
                    'is_bool' => true,
                    'form_group_class' => 'leofieldset fieldset_footer',
                    'desc' => $this->l('Close in Desktop when Footer Fixed'),
                    'values' => LeoECSetting::returnYesNo()
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'fullwidth_index_hook',
                    'label' => $this->l('Fullwidth Homepage'),
                    'class' => 'checkbox-group',
                    'form_group_class' => 'leofieldset fieldset_general',
                    'desc' => $this->l('The setting full-width for above HOOKS, apply for Home page'),
                    'values' => array(
                        'query' => self::getCheckboxIndexHook(),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'fullwidth_other_hook',
                    'label' => $this->l('Fullwidth other Pages'),
                    'class' => 'checkbox-group',
                    'form_group_class' => 'leofieldset fieldset_general',
                    'desc' => $this->l('The setting full-width for above HOOKS, apply for all OTHER pages ( not Home page )'),
                    'values' => array(
                        'query' => self::getCheckboxOtherHook(),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'html',
                    'name' => 'dump_name',
                    'form_group_class' => 'leofieldset fieldset_general',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Fullwidth Function: is only for develop')
                    .'<br/>'.$this->l('To use this function, you have to download')
                    .'<br/><a href="https://drive.google.com/file/d/1sjZB-bbaMqvopZ5Dj6eW_hftRr48HZji/view?usp=sharing" title="'.$this->l('Header file').'">'
                    .'<b>header.tpl</b></a>'
                    .'<br/><a href="https://drive.google.com/file/d/12_gOtxXaXPGM3Dx7aea8GGo_67r7LDPB/view?usp=sharing" title="'.$this->l('Footer file').'">'
                    .'<b>footer.tpl</b></a><br/>'
                    .$this->l('file and compare or override in themes folder').'</div>'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'disable_cache_hook',
                    'label' => $this->l('Disable cache Hooks'),
                    'class' => 'checkbox-group',
                    'form_group_class' => 'leofieldset fieldset_general',
                    'desc' => $this->l('Some modules always update data, disable cache for those modules show correct info.'),
                    'values' => array(
                        'query' => self::getCheckboxCacheHook(),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'product_list_data',
                    'id' => 'product_list_data',
                    'name' => 'product_list_data',
                    'product_list' => json_encode($product_list_array),
                    'title' => $this->l('Edit Layout'),
                    'url' => $this->context->link->getAdminLink('AdminLeoElementsProductList').'&updateleoelements_product_list&id_leoelements_product_list=',
                    'form_group_class' => 'hide',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List Layout'),
                    'name' => 'productlist_layout',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('You can create new Product list layout in Leo Elements Creator > Product Lists Builder. Then you can config conlum per row in Product List'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List Layout Mobile'),
                    'name' => 'productlist_layout_mobile',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('You can create new Product list layout in Leo Elements Creator > Product Lists Builder. Then you can config conlum per row in Product List'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List Layout Tablet'),
                    'name' => 'productlist_layout_tablet',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('You can create new Product list layout in Leo Elements Creator > Product Lists Builder. Then you can config conlum per row in Product List'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Config for Default Product List Layout').'</div>',
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Products Listing Mode'),
                    'name' => 'listing_product_mode',
                    'default' => 'grid',
                    'options' => array('query' => array(
                            array('id' => 'grid', 'name' => $this->l('Grid Mode')),
                            array('id' => 'list', 'name' => $this->l('List Mode')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('Display Products In List Mode Or Grid Mode In Category Page....'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Columns in Default Module On Desktop'),
                    'name' => 'listing_product_column_module',
                    'default' => ' ',
                    'options' => array('query' => array(
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns')),
                            array('id' => '5', 'name' => $this->l('5 Columns')),
                            array('id' => '6', 'name' => $this->l('6 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in default module of prestashop.'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Columns in Product List page On Desktop'),
                    'name' => 'listing_product_column',
                    'default' => ' ',
                    'options' => array('query' => array(
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns')),
                            array('id' => '5', 'name' => $this->l('5 Columns')),
                            array('id' => '6', 'name' => $this->l('6 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist load-edit-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Large devices (>=992px)'),
                    'name' => 'listing_product_largedevice',
                    'default' => ' ',
                    'options' => array('query' => array(
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns')),
                            array('id' => '5', 'name' => $this->l('5 Columns')),
                            array('id' => '6', 'name' => $this->l('6 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Medium devices - Tablet (>=768px)'),
                    'name' => 'listing_product_tablet',
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Small devices (>=576px)'),
                    'name' => 'listing_product_smalldevice',
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Extra Small devices (<567px)'),
                    'name' => 'listing_product_extrasmalldevice',
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Smart Phone (<480px)'),
                    'name' => 'listing_product_mobile',
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'leofieldset productlist_layout fieldset_productlist',
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('You can select Product list Layout for each Page').'</div>',
                    'form_group_class' => 'leofieldset fieldset_productlist',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List in Manufacture'),
                    'name' => 'manufacture_layout',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List in Search Page'),
                    'name' => 'search_layout',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List in Prices drop'),
                    'name' => 'pricedrop_layout',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List in New Products'),
                    'name' => 'newproduct_layout',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product List in Best sellers'),
                    'name' => 'bestsales_layout',
                    'default' => ' ',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_productlist plist-link',
                ),
                array(
                    'type' => 'product_list_data',
                    'id' => 'category_list_data',
                    'name' => 'category_list_data',
                    'product_list' => json_encode($catprofile_list_array),
                    'title' => $this->l('Edit Layout'),
                    'url' => $this->context->link->getAdminLink('AdminLeoElementsCategory').'&updateleoelements_category&id_leoelements_category=',
                    'form_group_class' => 'hide',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category Default Layout'),
                    'name' => 'category_layout',
                    'default' => ' ',
                    'options' => array('query' => $catprofile_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('You can create new category layout in Leo Elements Creator > Categories Builder.'),
                    'form_group_class' => 'leofieldset fieldset_category category-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category Default Layout Mobile'),
                    'name' => 'category_layout_mobile',
                    'default' => ' ',
                    'options' => array('query' => $catprofile_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('You can create new category layout in Leo Elements Creator > Categories Builder.'),
                    'form_group_class' => 'leofieldset fieldset_category category-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category Default Layout Tablet'),
                    'name' => 'category_layout_tablet',
                    'default' => ' ',
                    'options' => array('query' => $catprofile_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('You can create new category layout in Leo Elements Creator > Categories Builder.'),
                    'form_group_class' => 'leofieldset fieldset_category category-link',
                ),
                array(
                    'type' => 'product_list_data',
                    'id' => 'product_detail_data',
                    'name' => 'product_detail_data',
                    'product_list' => json_encode($proprofile_list_array),
                    'title' => $this->l('Edit Layout'),
                    'url' => $this->context->link->getAdminLink('AdminLeoElementsProducts').'&updateleoelements_products&id_leoelements_products=',
                    'form_group_class' => 'hide',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Detail Layout'),
                    'name' => 'productdetail_layout',
                    'default' => 'default',
                    'options' => array('query' => $proprofile_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_product pdetail-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Detail Layout Mobile'),
                    'name' => 'productdetail_layout_mobile',
                    'default' => 'default',
                    'options' => array('query' => $proprofile_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_product pdetail-link',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Detail Layout Tablet'),
                    'name' => 'productdetail_layout_tablet',
                    'default' => 'default',
                    'options' => array('query' => $proprofile_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_product pdetail-link',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use Image Gallery From Gallary Module'),
                    'name' => 'use_leo_gallery_default',
                    'default' => 0,
                    'desc' => $this->l('This config is only for default layout.'),
                    'values' => LeoECSetting::returnYesNo(),
                    'form_group_class' => 'leofieldset fieldset_product',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use background for breadcrumb'),
                    'name' => 'breadcrumb_use_background',
                    'default' => 0,
                    'desc' => $this->l('If no, we will use breadcrumb like prestashop default theme.'),
                    'values' => LeoECSetting::returnYesNo(),
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Background Color'),
                    'name' => 'background_color',
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'chose_image',
                    'name' => 'background_image',
                    'label' => $this->l('Background image'),
                    'size' => 20,
                    'required' => false,
                    'lang' => false,
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'select',
                    'name' => 'background_repeat',
                    'label' => $this->l('Background repeat'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'value' => 'repeat-x',
                                'name' => 'Repeat-X'
                            ),
                            array(
                                'value' => 'repeat-y',
                                'name' => 'Repeat-Y'
                            ),
                            array(
                                'value' => 'repeat',
                                'name' => 'Repeat Both'
                            ),
                            array(
                                'value' => 'no-repeat',
                                'name' => 'No Repeat'
                            )
                        ),
                        'id' => 'value',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'select',
                    'name' => 'background_attachment',
                    'label' => $this->l('Background attachment'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'value' => 'scroll',
                                'name' => 'Scroll'
                            ),
                            array(
                                'value' => 'fixed',
                                'name' => 'Fixed'
                            )
                        ),
                        'id' => 'value',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'select',
                    'name' => 'background_size',
                    'label' => $this->l('Background size'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'value' => 'auto',
                                'name' => 'Auto'
                            ),
                            array(
                                'value' => 'cover',
                                'name' => 'Cover'
                            )
                        ),
                        'id' => 'value',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'text',
                    'name' => 'background_position',
                    'label' => $this->l('Background position'),
                    'required' => false,
                    'lang' => false,
                    'desc' => $this->l('Property Values: center, center top, center center, center bottom, left top, left center, left bottom, right top, right center, right bottom, x% y%, x(px) y(px).'),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'html',
                    'name' => 'dump_name_box',
                    'form_group_class' => 'leofieldset fieldset_background',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Box Mode:').'</div>'
                ),
                array(
                    'type' => 'color',
                    'name' => 'background_box_color',
                    'label' => $this->l('Body background color'),
                    'desc' => $this->l('Body background color only visible in "Boxed" mode.'),
                    'size' => 20,
                    'required' => false,
                    'lang' => false,
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'chose_image',
                    'name' => 'background_box_image',
                    'label' => $this->l('Body background image'),
                    'size' => 20,
                    'required' => false,
                    'lang' => false,
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'select',
                    'name' => 'background_box_repeat',
                    'label' => $this->l('Body background repeat'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => array(
                        array(
                            'value' => 'repeat-x',
                            'name' => 'Repeat-X'
                        ),
                        array(
                            'value' => 'repeat-y',
                            'name' => 'Repeat-Y'
                        ),
                        array(
                            'value' => 'repeat',
                            'name' => 'Repeat Both'
                        ),
                        array(
                            'value' => 'no-repeat',
                            'name' => 'No Repeat'
                        )
                    ),
                        'id' => 'value',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'select',
                    'name' => 'background_box_attachment',
                    'label' => $this->l('Body background attachment'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'value' => 'scroll',
                                'name' => 'Scroll'
                            ),
                            array(
                                'value' => 'fixed',
                                'name' => 'Fixed'
                            )
                        ),
                        'id' => 'value',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'select',
                    'name' => 'background_box_size',
                    'label' => $this->l('Body background size'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => array(
                        array(
                            'value' => 'auto',
                            'name' => 'Auto'
                        ),
                        array(
                            'value' => 'cover',
                            'name' => 'Cover'
                        )
                    ),
                        'id' => 'value',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'text',
                    'name' => 'background_box_position',
                    'label' => $this->l('Body background position'),
                    'required' => false,
                    'lang' => false,
                    'desc' => $this->l('Property Values: center, center top, center center, center bottom, left top, left center, left bottom, right top, right center, right bottom, x% y%, x(px) y(px).'),
                    'form_group_class' => 'leofieldset fieldset_background',
                ),
                array(
                    'type' => 'chose_image',
                    'name' => 'breadcrumb_bg',
                    'label' => $this->l('Breadcrumb Background for all page'),
                    'size' => 20,
                    'required' => false,
                    'lang' => false,
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Breadcrumb Background Color'),
                    'name' => 'breadcrumb_bgcolor',
                    'desc' => $this->l('Put image url has https if you use https.'),
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use full breadcrumb background'),
                    'name' => 'breadcrumb_bgfull',
                    'default' => 0,
                    'desc' => $this->l('Breadcrumb background will show full web.'),
                    'values' => LeoECSetting::returnYesNo(),
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Breadcrumb Min Height'),
                    'name' => 'breadcrumb_height',
                    'desc' => $this->l('Put height for desktop. Example: 200px. Mobile and table edit in module/appagebuilder/css/uniqute.css or custom.js file'),
                    'default' => '200px',
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Breadcrumb Text Position'),
                    'name' => 'breadcrumb_textposition',
                    'default' => 'default',
                    'options' => array('query' => array(
                            array('id' => 'center', 'name' => $this->l('Center')),
                            array('id' => 'left', 'name' => $this->l('Left')),
                            array('id' => 'right', 'name' => $this->l('Right')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Breadcrumb on Category Page'),
                    'name' => 'breadcrumb_category',
                    'default' => 'default',
                    'options' => array('query' => array(
                            array('id' => 'default', 'name' => $this->l('Use default Breadcrumb')),
                            array('id' => 'catimg', 'name' => $this->l('Use Category Image for Breadcrumb')),
                            array('id' => 'breadcrumbimg', 'name' => $this->l('Put image with category id')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('Option 3: please put image with category id.jpg in folder img/breadcrumb/category/ID_CATEGORY.jpg'),
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l(''),
                    'name' => 'breadcrumbhtml',
                    'html_content' => $this->l('If you want to use different image for each controller.
                     Put image example: product.jpg, contact.jpg, category.jpg in img/breadcrumb/
                     more guide access blog.leotheme.com'),
                    'form_group_class' => 'leofieldset fieldset_breadcrumb',
                ),
                // begin font
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Family Base'),
                    'name' => 'font_family_base',
                    'default' => LeoElementsProfilesModel::getFontFamily('default'),
                    'options' => $option_group_font_family,
                    'form_group_class' => 'leofieldset fieldset_font',
                    'desc' => '--font-family-base:'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Family Menu'),
                    'name' => 'font_family_megamenu',
                    'default' => LeoElementsProfilesModel::getFontFamily('default'),
                    'options' => $option_group_font_family,
                    'form_group_class' => 'leofieldset fieldset_font',
                    'desc' => '--font-family-megamenu:'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Family Slider'),
                    'name' => 'font_family_slider',
                    'default' => LeoElementsProfilesModel::getFontFamily('default'),
                    'options' => $option_group_font_family,
                    'form_group_class' => 'leofieldset fieldset_font',
                    'desc' => '--font-family-slider:'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Family Heading'),
                    'name' => 'font_family_heading',
                    'default' => LeoElementsProfilesModel::getFontFamily('default'),
                    'options' => $option_group_font_family,
                    'form_group_class' => 'leofieldset fieldset_font',
                    'desc' => '--font-family-heading:'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Family Senary'),
                    'name' => 'font_family_senary',
                    'default' => LeoElementsProfilesModel::getFontFamily('default'),
                    'options' => $option_group_font_family,
                    'form_group_class' => 'leofieldset fieldset_font',
                    'desc' => '--font-family-senary:'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Family Septenary'),
                    'name' => 'font_family_septenary',
                    'default' => LeoElementsProfilesModel::getFontFamily('default'),
                    'options' => $option_group_font_family,
                    'form_group_class' => 'leofieldset fieldset_font',
                    'desc' => '--font-family-septenary:'
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H1 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_h1',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'h1_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'h1_font_size',
                            'default' => '36',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'h1_line_height',
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'h1_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'h1_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H2 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_h2',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'h2_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'h2_font_size',
                            'default' => '30',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'h2_line_height',
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'h2_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'h2_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H3 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_h3',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'h3_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'h3_font_size',
                            'default' => '24',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'h3_line_height',
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'h3_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'h3_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H4 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_h4',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'h4_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'h4_font_size',
                            'default' => '18',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'h4_line_height',
                            'default' => '28',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'h4_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'h4_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H5 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_h5',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'h5_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'h5_font_size',
                            'default' => '14',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'h5_line_height',
                            'default' => '20',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'h5_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'h5_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H6 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_h6',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'h6_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'h6_font_size',
                            'default' => '12',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'h6_line_height',
                            'default' => '20',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'h6_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'h6_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('P Tag'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_p',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'p_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'p_font_size',
                            'default' => '36',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'p_line_height',
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'p_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'p_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('A Tag'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_a',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'a_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'a_font_size',
                            'default' => '36',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'a_line_height',
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'a_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'a_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'leofieldset fieldset_font',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('Span Tag'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => 'font_span',
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'span_font_family',
                            'default' => LeoElementsProfilesModel::getFontFamily('default'),
                            'options' => $option_group_font_family,
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => 'span_font_size',
                            'default' => '12',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => 'span_line_height',
                            'default' => '20',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => 'span_font_weight',
                            'default' => LeoElementsProfilesModel::getFontWeight('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => 'span_font_style',
                            'default' => LeoElementsProfilesModel::getFontStyle('default'),
                            'options' => array(
                                'query' => LeoElementsProfilesModel::getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'leofieldset fieldset_font',
                ),
                // end font

                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Theme Color').'</div>',
                    'form_group_class' => 'leofieldset fieldset_variable_css',
                ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Theme Color Default'),
                        'name' => 'theme_color_default',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--theme-color-default:'
                    ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Text Color').'</div>',
                    'form_group_class' => 'leofieldset fieldset_variable_css',
                ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Headings Color'),
                        'name' => 'headings_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--headings-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Link Color'),
                        'name' => 'link_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--link-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Link Hover Color'),
                        'name' => 'link_color_hover',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--link-color-hover:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Text Color'),
                        'name' => 'text_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--text-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Price Color'),
                        'name' => 'price_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--price-color:'
                    ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Button Color').'</div>',
                    'form_group_class' => 'leofieldset fieldset_variable_css',
                ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Button Background Color'),
                        'name' => 'btn_bg',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--btn-bg:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Button Hover Background Color'),
                        'name' => 'btn_bg_hover',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--btn-bg-hover:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Button Color'),
                        'name' => 'btn_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--btn-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Button Hover Color'),
                        'name' => 'btn_color_hover',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--btn-color-hover:'
                    ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Product Items').'</div>',
                    'form_group_class' => 'leofieldset fieldset_variable_css',
                ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Background'),
                        'name' => 'product_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Name Color'),
                        'name' => 'product_name_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-name-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Name Hover Color'),
                        'name' => 'product_name_color_hover',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-name-color-hover:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Price Color'),
                        'name' => 'product_price_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-price-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Regular Price Color'),
                        'name' => 'product_regular_price_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-regular-price-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Button Background'),
                        'name' => 'product_button_bg',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-button-bg:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Button Hover Background'),
                        'name' => 'product_button_bg_hover',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-button-bg-hover:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Button Color'),
                        'name' => 'product_button_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-button-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Product Button Hover Color'),
                        'name' => 'product_button_color_hover',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--product-button-color-hover:'
                    ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Product Flags').'</div>',
                    'form_group_class' => 'leofieldset fieldset_variable_css',
                ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('On Sale Badge Background'),
                        'name' => 'on_sale_badge_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--on-sale-badge-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('On Sale Badge Color'),
                        'name' => 'on_sale_badge_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--on-sale-badge-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('New Badge Background'),
                        'name' => 'new_badge_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--new-badge-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('New Badge Color'),
                        'name' => 'new_badge_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--new-badge-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Sale Badge Background'),
                        'name' => 'sale_badge_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--sale-badge-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Sale Badge Color'),
                        'name' => 'sale_badge_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--sale-badge-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Online Only Background'),
                        'name' => 'online_only_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--online-only-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Online Only Color'),
                        'name' => 'online_only_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--online-only-color:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Pack Badge Background'),
                        'name' => 'pack_badge_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--pack-badge-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Pack Badge Color'),
                        'name' => 'pack_badge_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--pack-badge-color:'
                    ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Boxes (including Sidebars)').'</div>',
                    'form_group_class' => 'leofieldset fieldset_variable_css',
                ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Block Background'),
                        'name' => 'block_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--block-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Block Inner Background'),
                        'name' => 'block_inner_background',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--block-inner-background:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Block Heading Background'),
                        'name' => 'block_heading_bg',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--block-heading-bg:'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Block Heading Color'),
                        'name' => 'block_heading_color',
                        'form_group_class' => 'leofieldset fieldset_variable_css',
                        'desc' => '--block-heading-color:'
                    ),
                array(
                    'type' => 'hidden',
                    'id' => 'controller_url',
                    'name' => 'controller_url',
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );
        $header_input = array(
            'type' => 'mutiple_position_hook',
            'name' => 'header_content',
            'leoelements_contents_hook' => $leoelements_contents_hook,
            'icon_url' => $icon_url,
            'is_edit' => $is_edit,
            'content' => $postions['header'],
            'hook_list' => $hook_header,
            'form_group_class' => 'leofieldset fieldset_header',
        );

        $content_input = array(
            'type' => 'mutiple_position_hook',
            'name' => 'content_content',
            'leoelements_contents_hook' => $leoelements_contents_hook,
            'icon_url' => $icon_url,
            'is_edit' => $is_edit,
            'content' => $postions['content'],
            'hook_list' => $hook_content,
            'form_group_class' => 'leofieldset fieldset_content',
        );
        $footer_input = array(
            'type' => 'mutiple_position_hook',
            'name' => 'footer_content',
            'leoelements_contents_hook' => $leoelements_contents_hook,
            'icon_url' => $icon_url,
            'is_edit' => $is_edit,
            'content' => $postions['footer'],
            'hook_list' => $hook_footer,
            'form_group_class' => 'leofieldset fieldset_footer',
        );
        
        $cat_hook = array(
            'type' => 'position_hook',
            'name' => 'category_content',
            'leoelements_contents_hook' => $leoelements_contents_hook,
            'icon_url' => $icon_url,
            'is_edit' => $is_edit,
            'hook_list' => $hook_category,
            'form_group_class' => 'leofieldset fieldset_category',
        );
        
        $product_hook = array(
            'type' => 'position_hook',
            'name' => 'product_content',
            'leoelements_contents_hook' => $leoelements_contents_hook,
            'icon_url' => $icon_url,
            'is_edit' => $is_edit,
            'hook_list' => $hook_product,
            'form_group_class' => 'leofieldset fieldset_product',
        );
        
        if ($is_edit) {
            $fields_form1['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Header'),
                'name' => 'header',
                'id' => 'header-select',
                'class' => 'position-select',
                'options' => array(
                    'query' => $header,
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => '0',
                'desc' => $this->l('Select avail Header or create new'),
                'form_group_class' => 'leofieldset fieldset_header loading-wraper',
            );
            $fields_form1['input'][] = $header_input;

            $fields_form1['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Content'),
                'name' => 'content',
                'id' => 'content-select',
                'class' => 'position-select',
                'options' => array(
                    'query' => $content,
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => '0',
                'form_group_class' => 'leofieldset fieldset_content loading-wraper',
            );
            $fields_form1['input'][] = $content_input;

            $fields_form1['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Footer'),
                'name' => 'footer',
                'id' => 'footer-select',
                'class' => 'position-select',
                'leoelements_contents_hook' => $leoelements_contents_hook,
                'options' => array(
                    'query' => $footer,
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => '0',
                'form_group_class' => 'leofieldset fieldset_footer loading-wraper',
            );
            $fields_form1['input'][] = $footer_input;
            
            $fields_form1['input'][] = $cat_hook;
            $fields_form1['input'][] = $product_hook;
        } else {
            $fields_form1['input'][] = $header_input;
            $fields_form1['input'][] = $content_input;
            $fields_form1['input'][] = $footer_input;
            $fields_form1['input'][] = $cat_hook;
            $fields_form1['input'][] = $product_hook;
        }
        
        $this->fields_form = $fields_form1;
        
        $this->fields_form['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('Custom Css'),
            'name' => 'css',
            'form_group_class' => 'leofieldset fieldset_custom',
            'rows' => 100,
            'desc' => sprintf($this->l('Please set write Permission for folder %s'), $this->profile_css_folder),
        );
        $this->fields_form['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('Custom Js'),
            'name' => 'js',
            'rows' => 100,
            'form_group_class' => 'leofieldset fieldset_custom',
            'desc' => sprintf($this->l('Please set write Permission for folder %s'), $this->profile_js_folder),
        );
        // Display link view if it existed
        if ($is_edit) {
            // $profile_link = $this->context->link->getAdminLink('AdminLeoElementsProfiles').'&id_leoelements_profiles='.$is_edit;
            // $this->fields_form['input'][] = array(
            //     'type' => 'html',
            //     'name' => 'default_html',
            //     'name' => 'dess',
            //     'html_content' => '<a class="btn btn-info" href="'.$profile_link.'">
            //         <i class="icon icon-table"></i> '.$this->l('View and edit use mode Layout design').' >></a>'
            // );
        }

        // $path_guide = $this->getTemplatePath().'guide.tpl';
        // $guide_box = LeoECSetting::buildGuide($this->context, $path_guide, 2);
        // return $guide_box.parent::renderForm();
        // $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/function.js');

        $forms = parent::renderForm();

        $this->context->smarty->assign([
            'leoformcontent' => $forms
        ]);
        $tabs = $this->context->smarty->fetch($this->getTemplatePath().'tab-profile.tpl');
        return $tabs;
    }

    /**
     * Read file css + js to form when add/edit
     */
    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);
        if ($obj->id && $obj->profile_key) {
            $file_value['css'] = Tools::file_get_contents($this->profile_css_folder.$obj->profile_key.'.css');
            $file_value['js'] = Tools::file_get_contents($this->profile_js_folder.$obj->profile_key.'.js');
        } else {
            $file_value['profile_key'] = 'profile'.LeoECSetting::getRandomNumber();
        }
        
        $file_value['controller_url'] = $this->context->link->getAdminLink('AdminLeoElementsProfiles');
        
        if($obj->id) {
            $params = json_decode($obj->params,1);
            foreach($params as $k1 => $v1) {
                if(is_array($v1) && strpos($k1, 'font') === 0) {
                    // font value
                    $tag = explode('_', $k1)[1];
                    if (!is_array($file_value[$k1])) {
                        $file_value[$k1] = [];
                    }
                    foreach ($v1 as $k2 => $v2) {
                        $file_value[$k1][$tag.'_'.$k2] = $v2;
                    }
                } elseif (is_array($v1)) {
                    foreach($v1 as $k2=>$v2) {
                        $file_value[$k1.'_'.$k2] = $v2;
                    }
                } else {
                    $file_value[$k1] = $v1;
                }
            }
            // $file_value['header'] = ($obj->header == "createnew")?'none':$obj->header;
            // $file_value['content'] = ($obj->header == "createnew")?'none':$obj->content;
            // $file_value['footer'] = ($obj->header == "createnew")?'none':$obj->footer;

            foreach($this->all_postions as $position) {
                $poparams = json_decode($position['params'], 1);
                if($position['position_key'] == $obj->header) {
                    $file_value['header_content'] = $poparams;
                }
                if($position['position_key'] == $obj->content) {
                    $file_value['content_content'] = $poparams;
                }
                if($position['position_key'] == $obj->footer) {
                    $file_value['footer_content'] = $poparams;
                }
            }
            //other hook
            foreach (array('product', 'category') as $pos) {
                $hooks = LeoECSetting::getHook($pos);
                $poparams = array();
                foreach ($hooks as $hook) {
                    if(isset($params[$hook])) {
                        $poparams[$hook] = $params[$hook];
                    }
                }
                $file_value[$pos.'_content'] = $poparams;
            }
        }
        
        return $file_value;
    }

    public function processAdd()
    {
        parent::validateRules();
        if (count($this->errors)) {
            return false;
        }
        if ($this->object = parent::processAdd()) {
            $this->saveCustomJsAndCss($this->object->profile_key, '');
        }
        $this->processParams();
        $this->processHooks();
        $this->saveBackgroundAndFont();
        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsProfiles');
            $this->redirect_after .= '&id_leoelements_profiles='.($this->object->id);
            $this->redirect();
        }
    }

    public function processUpdate()
    {
        parent::validateRules();
        if (count($this->errors)) {
            return false;
        }
        if ($this->object = parent::processUpdate()) {
            $this->saveCustomJsAndCss($this->object->profile_key, $this->object->profile_key);
        }

        $this->processParams();
        $this->processHooks();
        $this->saveBackgroundAndFont();
        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsProfiles');
            $this->redirect_after .= '&id_leoelements_profiles='.($this->object->id);
            $this->redirect();
        }
    }

    public function processHooks()
    {
        foreach (array('header', 'content', 'footer') as $pos) {
            $position = Tools::getValue($pos);
            if($position) {
                $hooks = LeoECSetting::getHook($pos);
                $data = array();
                foreach ($hooks as $hook) {
                    if(Tools::getValue($hook.'_'.$position)) {
                        $data[$hook] = Tools::getValue($hook.'_'.$position);
                    }
                }
                
                $params = json_encode($data);
                $sql = 'UPDATE '._DB_PREFIX_.'leoelements_positions SET `params` = "'.pSQL($params).'" WHERE `position_key` ="'.pSQL($position).'"';
                Db::getInstance()->execute($sql);
            }
        }
    }

    /**
     * Get fullwidth hook, save to params
     */
    public function processParams()
    {
        $post = $GLOBALS['_POST'];
        
        $params = json_decode($this->object->params);
        $params_unset = array('id_leoelements_profiles', 'submitAddleoelements_profiles', 'submitAddleoelements_profilesAndStay', 'name', 'profile_key', 'controller_url', 'header', 'content', 'footer', 'css', 'js');

        if ($params === null) {
            // add new profile
            $params = new stdClass();
            foreach ($post as $key => $value) {
                if (strpos($key, 'listing_product') !== false) { // merge config listing product
                    $params->listing_product[str_replace('listing_product_', '', $key)] = Tools::getValue($key);
                } elseif (strpos($key, 'breadcrumb') === 0) { // merge config breadcrumb
                    $params->breadcrumb[str_replace('breadcrumb_', '', $key)] = Tools::getValue($key);
                } elseif (strpos($key, 'background') === 0) { // merge config background
                    $params->background[str_replace('background_', '', $key)] = Tools::getValue($key);
                } elseif ($this->strposa($key, ['h1','h2','h3','h4','h5','h6','a_','p_','span_']) === 0 ) {// merge config font
                    $tag = explode('_', $key)[0];
                    $params->{'font_'.$tag}[str_replace($tag.'_', '', $key)] = Tools::getValue($key);
                } elseif (!in_array($key, $params_unset) && strpos($key, 'meta_title') === false && strpos($key, 'friendly_url') === false && strpos($key, 'meta_description') === false
                    && strpos($key, 'meta_keywords') === false && strpos($key, 'fullwidth_index_hook') === false) {
                    $params->{$key} = Tools::getValue($key);
                }
            }
        } else {
            // update profile
            foreach ($post as $key => $value) {
                if (strpos($key, 'listing_product') !== false) { // merge config listing product
                    $params->listing_product->{str_replace('listing_product_', '', $key)} = Tools::getValue($key);
                } elseif (strpos($key, 'breadcrumb') === 0) { // merge config breadcrumb
                    $params->breadcrumb->{str_replace('breadcrumb_', '', $key)} = Tools::getValue($key);
                } elseif (strpos($key, 'background') === 0) { // merge config background
                    $params->background->{str_replace('background_', '', $key)} = Tools::getValue($key);
                } elseif ($this->strposa($key, ['h1','h2','h3','h4','h5','h6','a_','p_','span_']) === 0 ) {
                    $tag = explode('_', $key)[0];
                    $params->{'font_'.$tag}->{str_replace($tag.'_', '', $key)} = Tools::getValue($key);
                } elseif (!in_array($key, $params_unset) && strpos($key, 'meta_title') === false && strpos($key, 'friendly_url') === false && strpos($key, 'meta_description') === false
                    && strpos($key, 'meta_keywords') === false && strpos($key, 'fullwidth_index_hook') === false) {
                    $params->{$key} = Tools::getValue($key);
                }
            }
        }

        //save data for left/right thumb
        $layoutDetail = array('productdetail_layout', 'productdetail_layout_tablet', 'productdetail_layout_mobile');
        $layout_detail_data = array();
        $use_leo_gallery = 0;
        foreach ($layoutDetail as $layout) {
            $data_column = '';
            if (Tools::getValue($layout)) {
                $layout_data = LeoElementsProductsModel::getProductProfileByKey(Tools::getValue($layout));
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
            } elseif (isset($layout_detail_data[0])) {
                $data_column = $layout_detail_data[0];
            }

            $thumb = str_replace('productdetail', 'thumb_product', $layout);
            $use_leo_gallery_layout = (isset($data_column['use_leo_gallery']) && $data_column['use_leo_gallery']) ? 1 : 0;

            if ($use_leo_gallery != 1) {
                $use_leo_gallery = $use_leo_gallery_layout;
            }
            if (isset($params->{$thumb}) && $params->{$thumb} && $data_column && isset($data_column['templateview'])) {
                $params->{$thumb}->use_leo_gallery = $use_leo_gallery_layout;
                $params->{$thumb}->mode = $data_column['templateview'] == 'bottom' ? 'horizontal' : 'vertical';
                $params->{$thumb}->layout = $data_column['templateview'];
                $params->{$thumb}->zoom_type = $data_column['templatezoomtype'];
                $params->{$thumb}->column = $data_column['numberimage1200'];
                $params->{$thumb}->column_t = $data_column['numberimage768'];
                $params->{$thumb}->column_m = $data_column['numberimage480'];
            } elseif ($data_column && isset($data_column['templateview'])) {
                $params->{$thumb}['use_leo_gallery'] = $use_leo_gallery_layout;
                $params->{$thumb}['mode'] = $data_column['templateview'] == 'bottom' ? 'horizontal' : 'vertical';
                $params->{$thumb}['layout'] = $data_column['templateview'];
                $params->{$thumb}['zoom_type'] = $data_column['templatezoomtype'];
                $params->{$thumb}['column'] = $data_column['numberimage1200'];
                $params->{$thumb}['column_t'] = $data_column['numberimage768'];
                $params->{$thumb}['column_m'] = $data_column['numberimage480'];
            } elseif (isset($params->{$thumb}) && $params->{$thumb}) {
                if(leoECHelper::isLeo()){
                    
                }else{
                    $params->{$thumb}->use_leo_gallery = 0;
                }
            } else {
                if(isset($params->{$thumb}) && is_string($params->{$thumb})){
                    $params->{$thumb} = array();
                }
                $params->{$thumb}['use_leo_gallery'] = 0;
            }
        }
        if ($use_leo_gallery != 1 && !$params->productdetail_layout && !$params->productdetail_layout_tablet && !$params->productdetail_layout_mobile) {
            $use_leo_gallery = $params->use_leo_gallery_default;
        }
        # disable leogallery module if not use (use slick thumb, zoom by theme)
        if (Module::isInstalled('leogallery')) {
            $leogallery_module = Module::getInstanceByName('leogallery');
            if (!$use_leo_gallery && Module::isEnabled('leogallery')) {
                $leogallery_module->disable();
            } elseif ($use_leo_gallery && !Module::isEnabled('leogallery')) {
                $leogallery_module->enable();
            }
        }

        # get post index hook
        $index_hook = LeoECSetting::getIndexHook();
        $post_index_hooks = array();
        foreach ($index_hook as $key => $value) {
            // validate module
            $post_index_hooks[$value] = Tools::getValue('fullwidth_index_hook_'.$value) ?
                    Tools::getValue('fullwidth_index_hook_'.$value) : LeoECSetting::HOOK_BOXED;
            // validate module
            unset($key);
        }
        $params->fullwidth_index_hook = $post_index_hooks;

        # get post other hook
        $other_hook = LeoECSetting::getOtherHook();
        $post_other_hooks = array();
        foreach ($other_hook as $key => $value) {
            // validate module
            $post_other_hooks[$value] = Tools::getValue('fullwidth_other_hook_'.$value) ? Tools::getValue('fullwidth_other_hook_'.$value) : LeoECSetting::HOOK_BOXED;
            // validate module
            unset($key);
            if(isset($params->{'fullwidth_other_hook_'.$value})) {
                unset($params->{'fullwidth_other_hook_'.$value});
            }
        }
        $params->fullwidth_other_hook = $post_other_hooks;
        
        # get post disable hook
        $cache_hooks = LeoECSetting::getCacheHook();
        $post_disable_hooks = array();
        foreach ($cache_hooks as $key => $value) {
            // validate module
            $post_disable_hooks[$value] = Tools::getValue('disable_cache_hook_'.$value) ? Tools::getValue('disable_cache_hook_'.$value) : LeoECSetting::HOOK_BOXED;
            // validate module
            unset($key);
            
            if(isset($params->{'disable_cache_hook_'.$value})) {
                unset($params->{'disable_cache_hook_'.$value});
            }
        }
        $params->disable_cache_hook = $post_disable_hooks;

//        echo '<pre>' . "\n";
//        print_r($params);
//        echo '</pre>' . "\n";
//        die();

        # Save to params
        $this->object->params = json_encode($params);    
        
        # Save group_box
        if (Tools::getValue('groupBox')) {
            $this->object->group_box = implode(',', Tools::getValue('groupBox'));
        } else {
            $this->object->group_box = '';
        }
        
        $this->object->save();
    }

    /**
     * Auto create new position
     */
    public function processPosition()
    {
        $save = 0;
        if ($this->object->header == "createnew") {
            $obj = new LeoElementsPositionsModel();
            $obj->name = "Header of ".$this->object->name;
            $obj->position = "header";
            $obj->position_key = 'position'.LeoECSetting::getRandomNumber();
            $obj->params = '';
            $obj->save();
            $save = 1;
            $this->object->header = $obj->position_key;
        }
        if ($this->object->content == "createnew") {
            $obj = new LeoElementsPositionsModel();
            $obj->name = "Content of ".$this->object->name;
            $obj->position = "content";
            $obj->position_key = 'position'.LeoECSetting::getRandomNumber();
            $obj->params = '';
            $obj->save();
            $save = 1;
            $this->object->content = $obj->position_key;
        }
        if ($this->object->footer == "createnew") {
            $obj = new LeoElementsPositionsModel();
            $obj->name = "Footer of ".$this->object->name;
            $obj->position = "footer";
            $obj->position_key = 'position'.LeoECSetting::getRandomNumber();
            $obj->params = '';
            $obj->save();
            $save = 1;
            $this->object->footer = $obj->position_key;
        }
        if($save) {
            $this->object->save();    
        }
    }

    public function saveBackgroundAndFont()
    {
        // SET COOKIE AGAIN
        $theme_cookie_name = 'LEOELEMENTS_PANEL_CONFIG';
        $arrayConfig = array('default_skin', 'layout_mode', 'header_style', 'enable_fheader', 'sidebarmenu');
        # Remove value in cookie
        foreach ($arrayConfig as $value) {
            unset($_COOKIE[$theme_cookie_name.'_'.$value]);
            setcookie($theme_cookie_name.'_'.$value, '', 0, '/');
        }

        $content = '';
        $font_fields = array('font_family_base', 'font_family_septenary', 'font_family_slider', 'font_family_megamenu', 'font_family_heading', 'font_family_senary', 'h1_font_family', 'h2_font_family', 'h3_font_family', 'h4_font_family', 'h5_font_family', 'h6_font_family', 'p_font_family', 'a_font_family', 'span_font_family');
        $font_face = LeoElementsFont::getAllFonts('all');
        if ($font_face) {
            $font_add = array();
            foreach($font_fields as $font_field) {
                if ((int)Tools::getValue($font_field) && !in_array(Tools::getValue($font_field), $font_add)) {
                    $font = new LeoElementsFont(Tools::getValue($font_field));
                    if ($font->type == 1) {
                        $files = explode(',', $font->file);
                        $content .= "@font-face {\n";
                        $content .= "font-family:'".$font->font_family."';\n";
                        $content .= "font-style:".$font->font_style.";\n";
                        $content .= "font-weight:".$font->font_weight.";\n";
                        $content .= "src:local('".$font->font_family."'),";
                        foreach ($files as $file_k => $file) {
                            $content .=  "url(../../../../assets/fonts/".$file.") format('".explode('.', $file)[count(explode('.', $file)) - 1]."')";
                            $content .= ($file_k == count($files) - 1) ? ';' : ',';
                        }
                        $content .= "}\n";
                    } else {
                        $content .= '@import url("//fonts.googleapis.com/css?family='.$font->file.':wght@'.($font->font_weight != 400 ? '400;'.$font->font_weight : '400').'");'."\n";
                    }
                    $font_add[] = Tools::getValue($font_field);
                }
            }
        }

        # WRITE ATTRIBUTE FONT
        $content .= LeoElementsProfilesModel::renderCSSFont('h1');
        $content .= LeoElementsProfilesModel::renderCSSFont('h2');
        $content .= LeoElementsProfilesModel::renderCSSFont('h3');
        $content .= LeoElementsProfilesModel::renderCSSFont('h4');
        $content .= LeoElementsProfilesModel::renderCSSFont('h5');
        $content .= LeoElementsProfilesModel::renderCSSFont('h6');
        $content .= LeoElementsProfilesModel::renderCSSFont('p');
        $content .= LeoElementsProfilesModel::renderCSSFont('a');
        $content .= LeoElementsProfilesModel::renderCSSFont('span');

        // save background
        if (Tools::getValue('background_color') || Tools::getValue('background_image')) {
            $content .= 'body {';
            if (Tools::getValue('background_color')) {
                $content .= ' background-color:' . Tools::getValue('background_color') . ';';
            }
            if (Tools::getValue('background_image')) {
                $content .= ' background-image:url(' . Tools::getValue('background_image') . ');';
                $content .= ' background-attachment:' . Tools::getValue('background_attachment') . ';';
                $content .= ' background-repeat:' . Tools::getValue('background_repeat') . ';';
                $content .= ' background-size:' . Tools::getValue('background_size') . ';';
                if (Tools::getValue('background_position')) {
                    $content .= ' background-position:' . Tools::getValue('background_position') . ';';
                }
            }
            $content .= " }\n";
        }
        if (Tools::getValue('background_box_color') || Tools::getValue('background_box_image')) {
            $content .= 'body.layout-boxed-lg {';
            if (Tools::getValue('background_box_color')) {
                $content .= ' background-color:' . Tools::getValue('background_box_color') . ';';
            }
            if (Tools::getValue('background_box_image')) {
                $content .= ' background-image:url(' . Tools::getValue('background_box_image') . ');';
                $content .= ' background-attachment:' . Tools::getValue('background_box_attachment') . ';';
                $content .= ' background-repeat:' . Tools::getValue('background_box_repeat') . ';';
                $content .= ' background-size:' . Tools::getValue('background_box_size') . ';';
                if (Tools::getValue('background_box_position')) {
                    $content .= ' background-position:' . Tools::getValue('background_box_position') . ';';
                }
            }
            $content .= " }\n";
        }

        $theme_base = array('font_family_base', 'font_family_septenary', 'font_family_slider', 'font_family_megamenu', 'font_family_heading', 'font_family_senary', 'theme_color_default', 'theme_color_secondary', 'theme_color_tertiary', 'theme_color_senary', 'link_color', 'link_color_hover', 'text_color', 'price_color', 'btn_bg', 'btn_bg_hover', 'btn_color', 'btn_color_hover', 'product_background', 'product_name_color', 'product_name_color_hover', 'product_price_color', 'product_regular_price_color', 'product_button_bg', 'product_button_bg_hover', 'product_button_color', 'product_button_color_hover', 'on_sale_badge_background', 'on_sale_badge_color', 'new_badge_background', 'new_badge_color', 'sale_badge_background', 'sale_badge_color', 'online_only_background', 'online_only_color', 'pack_badge_background', 'pack_badge_color', 'block_background', 'block_inner_background', 'block_heading_bg', 'block_heading_color');
        $variables_css = '';
        foreach ($theme_base as $vcss) {
            $val_css = Tools::getValue($vcss);
            $key_css = '--'.str_replace('_', '-', $vcss); // font_family_base -> --font-family-base
            if ($val_css && strpos($vcss, 'font') === 0) { // font family variables
                $variables_css .= $key_css.": '".((int)$val_css ? (new LeoElementsFont($val_css))->font_family : $val_css)."', sans-serif;\n";
            } elseif ($val_css) {
                $variables_css .= $key_css.": ".$val_css.";\n";
            }
        }
        if ($variables_css) {
            $content .= ":root {\n";
            $content .= $variables_css;
            $content .= "}\n";
        }

        $css_dir = $this->theme_dir.'modules/leoelements/views/css/';
        $font_custom_file_name  = 'font-custom-'.Tools::getValue('profile_key').'-'.Context::getContext()->shop->id.'.css';
        if (!$content) {
            if (file_exists($css_dir.$font_custom_file_name)) {
                unlink($css_dir.$font_custom_file_name);
            }
        } else {
            LeoECSetting::writeFile($css_dir, $font_custom_file_name, $content);
        }

        # SAVING GOOGLE FONT
        $gfont_items = Tools::getValue('gfont_items');
        if ($gfont_items) {
            $str_gfont_items = implode('__________', $gfont_items);
            Configuration::updateValue(leoECHelper::getConfigName('google_font'), $str_gfont_items);
        } else {
            Configuration::updateValue(leoECHelper::getConfigName('google_font'), '');
        }
        
        # SAVING SUBSET
        $gfonts_subsets = Tools::getValue('gfonts_subsets');
        if ($gfonts_subsets) {
            $gfonts_subsets = implode(',', $gfonts_subsets);
            Configuration::updateValue(leoECHelper::getConfigName('google_subset'), $gfonts_subsets);
        } else {
            Configuration::updateValue(leoECHelper::getConfigName('google_subset'), '');
        }
        
    }

    public function saveCustomJsAndCss($key, $old_key = '')
    {
        # DELETE OLD FILE
        if ($old_key) {
            Tools::deleteFile($this->profile_css_folder.$old_key.'.css');
            Tools::deleteFile($this->profile_js_folder.$old_key.'.js');
        }

        if (Tools::getValue('js') != '') {
            LeoECSetting::writeFile($this->profile_js_folder, $key.'.js', Tools::getValue('js'));
        }
        
        if (Tools::getValue('css') != '') {
            # FIX CUSTOMER CAN NOT TYPE "\"
            $temp = Tools::getAllValues();
            $css = $temp['css'];
            LeoECSetting::writeFile($this->profile_css_folder, $key.'.css', $css);
        }
    }

    public function renderGoogleLinkFont($gfont_name, $attribute)
    {
        $output = '';
        if (is_array($attribute) && $attribute) {
            $str_att = '';
            foreach ($attribute as $value) {
                $str_att .= ','.$value;
            }
            $str_att = trim($str_att, ',');
            
            $output = $gfont_name . ':' . $str_att;
        } else {
            $output = $gfont_name;
        }
        
        return $output;
    }

    /**
     * Generate form : create checkbox in admin form ( add/edit profile )
     */
    public static function getCheckboxIndexHook()
    {
        $ids = LeoECSetting::getIndexHook();
        $names = LeoECSetting::getIndexHook();
        return leoECHelper::getArrayOptions($ids, $names);
    }

    /**
     * Generate form : create checkbox in admin form ( add/edit profile )
     */
    public static function getCheckboxOtherHook()
    {
        $ids = LeoECSetting::getOtherHook();
        $names = LeoECSetting::getOtherHook();
        return leoECHelper::getArrayOptions($ids, $names);
    }

    /**
     * Generate form : create checkbox in admin form ( add/edit profile )
     */
    public static function getCheckboxCacheHook()
    {
        $ids = LeoECSetting::getCacheHook();
        $names = LeoECSetting::getCacheHook();
        return leoECHelper::getArrayOptions($ids, $names);
    }

    public function strposa($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
                $res = strpos($haystack, $needle, $offset);
                if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
    }

    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        
        if (count($this->errors) <= 0) {
            if (Tools::isSubmit('duplicate'.$this->table)) {
                if ($this->id_object) {
                    if (!$this->access('add')) {
                        $this->errors[] = $this->trans('You do not have permission to duplicate this.', array(), 'Admin.Notifications.Error');
                    }
                }
            }
        }
    }
}
