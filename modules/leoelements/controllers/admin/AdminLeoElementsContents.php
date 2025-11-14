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
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsContentsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsPositionsModel.php');


class AdminLeoElementsContentsController extends ModuleAdminController
{
    private $theme_name = '';
    public $module = 'leoelements';
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $theme_dir;
    public $all_postions = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'leoelements_contents';
        $this->className = 'LeoElementsContentsModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;
        $this->override_folder = 'leo_elementor_config_profiles';

        parent::__construct();
        $this->theme_dir = _PS_THEME_DIR_;
        $this->context = Context::getContext();
        $this->order_by = 'page';
        $this->order_way = 'DESC';
        $alias = 'sa';

        $id_shop = (int)$this->context->shop->id;
        $this->_join .= ' JOIN `'._DB_PREFIX_.'leoelements_contents_shop`
                sa ON (a.`id_leoelements_contents` = sa.`id_leoelements_contents` AND sa.id_shop = '.$id_shop.')';
        $this->_select .= ' sa.active as active';

        $this->fields_list = array(
            'id_leoelements_contents' => array(
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
            'content_key' => array(
                'title' => $this->l('Content Key'),
                'filter_key' => 'a!content_key',
                'type' => 'text',
                'width' => 140,
            ),
            'hook' => array(
                'title' => $this->l('Hook'),
                'filter_key' => 'a!hook',
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

//    public function processDelete()
//    {
//        $object = $this->loadObject();
//        $object->loadDataShop();
//        
//        if ($object && !$object->active) {
//            $object = parent::processDelete();
//            if ($object->profile_key) {
//                Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
//                Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
//            }
//        } else {
//            $this->errors[] = Tools::displayError('Can not delete Default Profile.');
//        }
//        return $object;
//    }

    public function ajaxProcessPosition()
    {
        $type = Tools::getValue('type');
        //create new position: header, content, footer 
        if($type == "position") {
            $position_type = Tools::getValue('position_type');
            $name = Tools::getValue('position_name');
            $id_leoelements_contents = Tools::getValue('id_leoelements_contents');
            $position = new LeoElementsPositionsModel();

            //create new
            $key = LeoECSetting::getRandomNumber();
            $position->position_key = 'position'.$key;
            $position->name = $name;
            $position->position = $position_type;
            $position->save();
            //insert profile
            $sql = 'UPDATE `'._DB_PREFIX_.'leoelements_contents` SET `'.$position_type.'` = "'.pSQL($position->position_key).'" WHERE `id_leoelements_contents` = "'.pSQL($id_leoelements_contents).'"';
            Db::getInstance()->execute($sql);

            $result = array( 'id' =>$position->position_key, 'title' => $name );
            echo json_encode($result);
            die();
        }
        //create new hook content of possition
        else {
            die('error can not create hook');
        }
    }

//    public function processBulkDelete()
//    {
//        $arr = $this->boxes;
//        if (!$arr) {
//            return;
//        }
//        foreach ($arr as $id) {
//            $object = new $this->className($id);
//            $object->loadDataShop();
//            if ($object && !$object->active) {
//                $object->delete();
//                if ($object->profile_key) {
//                    Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
//                    Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
//                }
//            } else {
//                $this->errors[] = Tools::displayError('Can not delete Default Profile.');
//            }
//        }
//        if (empty($this->errors)) {
//            $this->confirmations[] = $this->_conf[1];
//        }
//    }

    public function renderView()
    {
        $object = $this->loadObject();
        if ($object->page == 'product_detail') {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsProductDetail');
        } else {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsProfiles');
        }
        $this->redirect_after .= '&id_leoelements_contents='.$object->id;
        $this->redirect();
    }
    
    public function displayViewLink($token = null, $id = null, $name = null)
    {
        // validate module
        unset($name);
        $token = Context::getContext()->link->getAdminLink('AdminLeoElementsProfiles');
        $href = $token . '&id_leoelements_contents='.$id;
        $html = '<a href="'.$href.'" class="btn btn-default" title="View"><i class="icon-search-plus"></i> View</a>';
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

    public function renderList()
    {
        $this->initToolbar();
//        $this->addRowAction('view');
        $this->addRowAction('edit');
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
        $this->initToolbar();
        $header[] = $content[] = $footer[] = array('id' => '0', 'name' => $this->l('No Use'));
        $header[] = $content[] = $footer[] = array('id' => 'createnew', 'name' => $this->l('Create a new position'));
        $this->all_postions = LeoElementsPositionsModel::getAllPosition();
        $hook_header = LeoECSetting::getHook('header');
        $hook_content = LeoECSetting::getHook('content');
        $hook_footer = LeoECSetting::getHook('footer');
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;;

        $sql = 'SELECT c.id_leoelements_contents, c.name, c.hook, c.content_key FROM '._DB_PREFIX_.'leoelements_contents c
                
                INNER JOIN '._DB_PREFIX_.'leoelements_contents_shop cl ON (c.id_leoelements_contents = cl.id_leoelements_contents)
                
                WHERE cl.id_shop = '. (int)$id_shop;
        $all_leoelements_contents = Db::getInstance()->executes($sql);
        $leo_content_hook = array();
        
        foreach ($all_leoelements_contents as $ccontent){
            $ccontent['url'] = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&post_type=' . 'hook' . '&id_post=' . $ccontent['id_leoelements_contents'] . '&id_lang='.(int)$id_lang;
            $leo_content_hook[$ccontent['hook']][] = $ccontent;
        }

        foreach ($this->all_postions as $position) {
            $params = json_decode($position['params']);
            if ($position['position'] == 'header') {
                $header[] = array('id' => $position['position_key'], 'name' => $position['name'], '');
            }
            else if($position['position'] == 'content') {
                $content[] = array('id' => $position['position_key'], 'name' => $position['name']);
            } else {
                $footer[] = array('id' => $position['position_key'], 'name' => $position['name']);
            }
        }
        $is_edit = Tools::getValue('id_leoelements_contents');
        
        $url_params = '';
        if($is_edit){
            
            $hook_product = LeoECSetting::getHook('product');
            $hook_category = LeoECSetting::getHook('category');
            $model = new LeoElementsContentsModel($is_edit);
            
            if($model && isset($model->hook) && in_array($model->hook, $hook_category)){
                $params = array(
                    'post_type' => 'hook_category_layout',
                    'id_post' => $is_edit,
                    'id_lang' => (int)$this->context->language->id,
                );
                $url_params = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . http_build_query($params);
            }elseif($model && isset($model->hook) && in_array($model->hook, $hook_product)){
                $params = array(
                    'post_type' => 'hook_product_layout',
                    'id_post' => $is_edit,
                    'id_lang' => (int)$this->context->language->id,
                );
                $url_params = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . http_build_query($params);
            }else{
                $params = array(
                    'post_type' => 'hook',
                    'id_post' => $is_edit,
                    'id_lang' => (int)$this->context->language->id,
                );
                $url_params = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . http_build_query($params);
            }
        }
        
        $list_profiles_default = array(
            array( 'id_leoelements_profiles' => 'default', 'name' => $this->l('Please choose'))
        );
        $sql = 'SELECT c.id_leoelements_contents, c.name, c.hook, c.type, c.content_key FROM '._DB_PREFIX_.'leoelements_contents c INNER JOIN '._DB_PREFIX_.'leoelements_contents_shop cs ON (c.id_leoelements_contents = cs.id_leoelements_contents) WHERE cs.id_shop = '. (int)$id_shop . ' AND c.type like "hook_product_list"';
        $sql = 'SELECT SQL_CALC_FOUND_ROWS  a.*
                    ,  sa.active as active, sa.active_mobile as active_mobile, sa.active_tablet as active_tablet
                    FROM `'._DB_PREFIX_.'leoelements_profiles` a 
                    JOIN `'._DB_PREFIX_.'leoelements_profiles_shop`
                    sa ON (a.`id_leoelements_profiles` = sa.`id_leoelements_profiles` AND sa.id_shop = 1) 
                    WHERE 1  AND sa.id_shop=1 
                    ORDER BY a.id_leoelements_profiles ASC  LIMIT 0, 50';

        $list_profiles = Db::getInstance()->executes($sql);
        $list_profiles = array_merge_recursive( $list_profiles_default, $list_profiles );
        
        
        $list_hooks = array(
            array(
                'name' => $this->l('Please choose'),
                'id' => 'displayTop',
            ),
            array(
                'name' => $this->l('displayTop'),
                'id' => 'displayTop',
            ),
            array(
                'name' => $this->l('displayTopColumn'),
                'id' => 'displayTopColumn',
            ),
            array(
                'name' => $this->l('displayHome'),
                'id' => 'displayHome',
            ),
            array(
                'name' => $this->l('displayBanner'),
                'id' => 'displayBanner',
            ),
            array(
                'name' => $this->l('displayNavFullWidth'),
                'id' => 'displayNavFullWidth',
            ),
            array(
                'name' => $this->l('displayAfterBodyOpeningTag'),
                'id' => 'displayAfterBodyOpeningTag',
            ),
            array(
                'name' => $this->l('displayShoppingCart'),
                'id' => 'displayShoppingCart',
            ),
            array(
                'name' => $this->l('displayHeaderCategory'),
                'id' => 'displayHeaderCategory',
            ),
            array(
                'name' => $this->l('displayFooterCategory'),
                'id' => 'displayFooterCategory',
            ),
            array(
                'name' => $this->l('displayReassurance'),
                'id' => 'displayReassurance',
            ),
            array(
                'name' => $this->l('displayFooterProduct'),
                'id' => 'displayFooterProduct',
            ),
            array(
                'name' => $this->l('displayFooterTop'),
                'id' => 'displayFooterTop',
            ),
            array(
                'name' => $this->l('displayFooterBefore'),
                'id' => 'displayFooterBefore',
            ),
            array(
                'name' => $this->l('displayFooter'),
                'id' => 'displayFooter',
            ),
            array(
                'name' => $this->l('displayFooterAfter'),
                'id' => 'displayFooterAfter',
            ),
            array(
                'name' => $this->l('displayLeftColumn'),
                'id' => 'displayLeftColumn',
            ),
            array(
                'name' => $this->l('displayRightColumn'),
                'id' => 'displayRightColumn',
            ),
        );
        
        $is_edit = Tools::getValue('id_leoelements_contents', 0);
                
        $fields_form1 = array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'name',
                    'form_group_class' => 'leofieldset fieldset_general',
                    'required' => true,
                    'hint' => $this->l('Invalid characters:'),' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'default' => 1,
                    'values' => LeoECSetting::returnYesNo(),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Display Hook'),
                    'name' => 'hook',
                    'options' => array(
                        'query' => $list_hooks,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'disabled' => $is_edit ? 'disabled' : false,
                    'form_group_class' => 'select_hook',
                ),
                array(
                    'type' => 'hidden',
                    'value' => 'hook',
                    'name' => 'type',
                ),
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
        if($is_edit)
        {
            $icon_url = _MODULE_DIR_.'leoelements/views/img/logo.png';
            $fields_form1['input'][] = array(
                'type' => 'hidden',
                'id' => 'controller_url',
                'name' => 'controller_url',
            );
            $fields_form1['input'][] = array(
                    'type' => 'position_hook',
                    'name' => 'header_content',
                    'leoelements_contents_hook' => '',
                    'icon_url' => $icon_url,
                    'url_params' => $url_params,
                    'is_edit' => $is_edit,
                    'hook_list' => '',
            );
            $fields_form1['input'][] = array(
                    'type' => 'select',
                    'label' => $this->l('Select Profiles'),
                    'name' => 'select_profile',
                    'options' => array(
                        'query' => $list_profiles,
                        'id' => 'id_leoelements_profiles',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'select_profile',
            );
            $fields_form1['input'][] = array(
                    'type' => 'select',
                    'label' => $this->l('Select Display Hook'),
                    'name' => 'select_hook',
                    'options' => array(
                        'query' => $list_hooks,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'select_hook',
            );
        }else{
            $key = LeoECSetting::getRandomNumber();
            $content_key = 'content'.$key;
            $fields_form1['input'][] = array(
                    'type' => 'hidden',
                    'value' => $content_key,
                    'name' => 'content_key',
            );
        }
        
        $this->fields_form = $fields_form1;
        
        return parent::renderForm();
    }
    
    public function postProcess()
    {
        $is_edit = Tools::getValue('id_leoelements_contents', 0);
        parent::postProcess();
        if ( !$is_edit && isset($this->object->id) && $this->object->id )
        {
            $id = $this->object->id;
            $id_shop = $this->context->shop->id;
            $query = 'INSERT INTO '._DB_PREFIX_.'leoelements_contents_shop (`id_leoelements_contents`, `id_shop`, `active`) VALUES('.(int)$id.', '.(int)$id_shop.', 1)';
            Db::getInstance()->execute($query);
        }

        if (count($this->errors) > 0) {
            return;
        }
        if (Tools::getIsset('active_mobileleoelements_contents') || Tools::getIsset('active_tabletleoelements_contents')) {
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $result = Tools::getIsset('active_mobileleoelements_contents')?$object->toggleStatusMT('active_mobile'):$object->toggleStatusMT('active_tablet');
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
        if (Tools::getIsset('duplicateleoelements_contents')) {
            $id = Tools::getValue('id_leoelements_contents');
            if (!LeoElementsContentsModel::duplicate($id)) {
                Tools::displayError('Can not duplicate Content');
            }else{
                $this->confirmations[] = 'Duplicate Content successfully.';
            }
        }
    }

    /**
     * Read file css + js to form when add/edit
     */
    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);
        if ($obj->id && isset($obj->profile_key) && $obj->profile_key) {
            $file_value['css'] = Tools::file_get_contents($this->profile_css_folder.$obj->profile_key.'.css');
            $file_value['js'] = Tools::file_get_contents($this->profile_js_folder.$obj->profile_key.'.js');
        } else {
            $file_value['profile_key'] = 'profile'.LeoECSetting::getRandomNumber();
        }
        
        $file_value['controller_url'] = $this->context->link->getAdminLink('AdminLeoElementsProfiles');
        
        
        $is_edit = Tools::getValue('id_leoelements_contents', 0);
        if($is_edit){
            
        } else {
            $file_value['type'] = 'hook';
            
            $key = LeoECSetting::getRandomNumber();
            $content_key = 'content'.$key;
            $file_value['content_key'] = $content_key;
        }
            
//        if($obj->id) {
//            $params = json_decode($obj->params,1);
//            
//            foreach($params as $k1=>$v1) {
//                foreach($v1 as $k2=>$v2) {
//                    $file_value[$k1.'_'.$k2] = $v2;
//                }
//            }
//            // $file_value['header'] = ($obj->header == "createnew")?'none':$obj->header;
//            // $file_value['content'] = ($obj->header == "createnew")?'none':$obj->content;
//            // $file_value['footer'] = ($obj->header == "createnew")?'none':$obj->footer;
//            
//            $file_value['fullwidth_index_hook_displayBanner'] = 1;
//            foreach($this->all_postions as $position) {
//                $params = json_decode($position['params'], 1);
//                if($position['position_key'] == $obj->header) {
//                    $file_value['header_content'] = $params;
//                }
//                if($position['position_key'] == $obj->content) {
//                    $file_value['content_content'] = $params;
//                }
//                if($position['position_key'] == $obj->footer) {
//                    $file_value['footer_content'] = $params;
//                }
//            }    
//        }
        
        return $file_value;
    }

//    public function processAdd()
//    {
//        parent::validateRules();
//        if (count($this->errors)) {
//            return false;
//        }
////        if ($this->object = parent::processAdd()) {
////            $this->saveCustomJsAndCss($this->object->profile_key, '');
////        }
////        $this->processParams();
//        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
//            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsContents');
//            $this->redirect_after .= '&id_leoelements_contents='.($this->object->id);
//            $this->redirect();
//        }
//    }

//    public function processUpdate()
//    {
//        parent::validateRules();
//        if (count($this->errors)) {
//            return false;
//        }
////        if ($this->object = parent::processUpdate()) {
////            $this->saveCustomJsAndCss($this->object->profile_key, $this->object->profile_key);
////        }
//
////        $this->processParams();
//        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
//            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsContents');
//            $this->redirect_after .= '&id_leoelements_contents='.($this->object->id);
//            $this->redirect();
//        }
//    }

    /**
     * Get fullwidth hook, save to params
     */
    public function processParams()
    {
        $params = json_decode($this->object->params);
        if ($params === null) {
            $params = new stdClass();
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
        }
        $params->disable_cache_hook = $post_disable_hooks;
        

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
