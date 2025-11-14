<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2019 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductListModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/leoECHelper.php');

class AdminLeoElementsProductListController extends ModuleAdminControllerCore
{
    private $theme_name = '';
    public $module_name = 'leoelements';
    public $tpl_save = '';
    public $file_content = array();
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $profile_css_folder;
    public $module_path;
    public $str_search = array();
    public $str_relace = array();
    public $theme_dir;
    public $params_field = array();
    public $folder_save = '';
    public $folder_scan_module = '';
    public $folder_scan_theme = '';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'leoelements_product_list';
        $this->className = 'LeoElementsProductListModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'leoelements_product_list_shop` ps ON (ps.`id_leoelements_product_list` = a.`id_leoelements_product_list`)';
        $this->_select .= ' ps.active as active, ps.active_mobile as active_mobile, ps.active_tablet as active_tablet';

        $this->order_by = 'id_leoelements_product_list';
        $this->order_way = 'DESC';
        parent::__construct();
        $this->fields_list = array(
            'id_leoelements_product_list' => array(
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
            'plist_key' => array(
                'title' => $this->l('Product List Key'),
                'filter_key' => 'a!plist_key',
                'type' => 'text',
                'width' => 140,
            ),
            'class' => array(
                'title' => $this->l('Class'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!class',
                'orderby' => false
            )
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->theme_dir = _PS_THEME_DIR_;
        $this->folder_save = _PS_THEME_DIR_.'modules/leoelements/views/templates/front/products/';
        $this->folder_scan_module = _PS_MODULE_DIR_.'/leoelements/views/templates/front/products/';
        $this->folder_scan_theme = _PS_THEME_DIR_.'/modules/leoelements/views/templates/front/products/';


        $this->_where = ' AND ps.id_shop='.(int)$this->context->shop->id;
        $this->theme_name = _THEME_NAME_;
        $this->profile_css_folder = $this->theme_dir.'modules/'.$this->module_name.'/';
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
//        $this->module_path_resource = $this->module_path.'views/';
        $this->str_search = array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APOCBRACKET_', '_APCCBRACKET_', '_APDOLA_');
        $this->str_relace = array('&', '\"', '\'', '\t', '\r', '\n', '[', ']', '{', '}', '$');
        $this->params_field = leoECHelper::defaultConfig()['product_list'];
    }
    
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('tagify');
        Context::getContext()->controller->addJs(leoECHelper::getJsAdminDir().'admin/AdminLeoElementsProductList.js');
        Context::getContext()->controller->addCss(leoECHelper::getCssAdminDir().'back.css');
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->module_name) {
            $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/function.js');
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        
        # SAVE AND STAY
        if ($this->display == 'add' || $this->display == 'edit') {

            

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
        
        # SHOW LINK EXPORT ALL FOR TOOLBAR
        switch ($this->display) {
            default:
                $this->toolbar_btn['new'] = array(
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->l('Add new'),
                    'class' => 'btn_add_new',
                );
                if (!$this->display && $this->can_import) {
                    $this->toolbar_btn['import'] = array(
                        'href' => self::$currentIndex . '&import' . $this->table . '&token=' . $this->token,
                        'desc' => $this->trans('Import', array(), 'Admin.Actions'),
                        'class' => 'btn_xml_import',
                    );
                }
                if ($this->allow_export) {
                    $this->toolbar_btn['export'] = array(
                        'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
                        'desc' => $this->l('Export'),
                        'class' => 'btn_xml_export',
                    );
                    Media::addJsDef(array('record_id' => 'leoelements_product_listBox[]'));
                }
        }
    }
    
    /**
     * OVERRIDE CORE
     */
    public function processExport($text_delimiter = '"')
    {
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        $record_id = Tools::getValue('record_id');
        $file_name = 'ap_product_list_all.xml';
        // validate module
        unset($text_delimiter);
        
        if ($record_id) {
            $record_id_str = implode(", ", $record_id);
            $this->_where = ' AND a.'.$this->identifier.' IN ( '.pSQL($record_id_str).' )';
            $file_name = 'ap_product_list.xml';
        }

        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        $data = $this->_list;
        
        $data_all = array();
        foreach (Language::getLanguages() as $key => $lang) {
            $this->getList($lang['id_lang'], null, null, 0, false);
            $data_all[$lang['iso_code']] = $this->_list;
        }
        
        $this->file_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->file_content .= '<data>' . "\n";
        $this->file_content .= '<product_list>' . "\n";
        
        if ($data) {
            foreach ($data as $product_detail) {
                $this->file_content .= '<record>' . "\n";
                foreach ($product_detail as $key => $value) {
                    if (isset($definition['fields'][$key]['lang']) && $definition['fields'][$key]['lang']) {
                        # MULTI LANG
                        $this->file_content .= '    <'.$key.'>'. "\n";
                        foreach (Language::getLanguages() as $key_lang => $lang) {
                            // validate module
                            unset($key_lang);
                            $this->file_content .= '        <'.$lang['iso_code'].'>';
                            $this->file_content .= '<![CDATA['.$value.']]>';
                            $this->file_content .= '</'.$lang['iso_code'].'>' . "\n";
                        }
                        $this->file_content .= '    </'.$key.'>' . "\n";
                    } else {
                        # SINGLE LANG
                        $this->file_content .= '    <'.$key.'>';
                        $this->file_content .= '<![CDATA['.$value.']]>';
                        $this->file_content .= '</'.$key.'>' . "\n";
                    }
                }
                $this->file_content .= '</record>' . "\n";
            }
        }
        $this->file_content .= '</product_list>' . "\n";
        $this->file_content .= '</data>' . "\n";
        call_user_func('header', 'Content-type: text/xml');
        call_user_func('header', 'Content-Disposition: attachment; filename="'.$file_name.'"');
        echo $this->file_content;
        die();
    }
    
    public function processImport()
    {
        $upload_file = new Uploader('importFile');
        $upload_file->setAcceptTypes(array('xml'));
        $file = $upload_file->process();
        if (!isset($file[0])) {
            $this->errors[]        = $this->trans('Please add file upload for import.', array(), 'Admin.Notifications.Error');
            return;
        }
        $file = $file[0];
        
        if (!isset($file['save_path'])) {
            $this->errors[]        = $this->trans('Failed to import.', array(), 'Admin.Notifications.Error');
            return;
        }
        $files_content = simplexml_load_file($file['save_path']);
        $override = Tools::getValue('override');
        
        if (isset($files_content->product_list) && $files_content->product_list) {
            foreach ($files_content->product_list->children() as $product_details) {
                if (!$override) {
                    $obj_model = new LeoElementsProductListModel();
                    $obj_model->plist_key = 'plist'.LeoECSetting::getRandomNumber();
                    $obj_model->name = $product_details->name->__toString();
                    $obj_model->class = $product_details->class->__toString();
                    $obj_model->params = $product_details->params->__toString();
                    $obj_model->type = $product_details->type->__toString();
                    $obj_model->active = 0;

                    if ($obj_model->save()) {
                        $this->saveTplFile($obj_model->plist_key, $obj_model->params);
                    }
                }
            }
            $this->confirmations[] = $this->trans('Successful importing.', array(), 'Admin.Notifications.Success');
        } else {
            $this->errors[]        = $this->trans('Wrong file to import.', array(), 'Admin.Notifications.Error');
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
        $this->redirect_after .= '&id_leoelements_product_list='.$object->id;
        $this->redirect();
    }

    public function postProcess()
    {
        parent::postProcess();
        
        if (Tools::getIsset('active_mobileleoelements_product_list') || Tools::getIsset('active_tabletleoelements_product_list')) {
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $result = Tools::getIsset('active_mobileleoelements_product_list')?$object->toggleStatusMT('active_mobile'):$object->toggleStatusMT('active_tablet');
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
        
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::getIsset('duplicateleoelements_product_list')) {
            $id = Tools::getValue('id_leoelements_product_list');
            $model = new LeoElementsProductListModel($id);
            $duplicate_object = $model->duplicateObject();
            if (isset($model->params)) {
                # FIX : insert code can not duplicate
                $duplicate_object->params = $model->params;
            }
            $duplicate_object->name = $this->l('Duplicate of').' '.$duplicate_object->name;
            $old_key = $duplicate_object->plist_key;
            $duplicate_object->plist_key = 'plist'.LeoECSetting::getRandomNumber();
            if ($duplicate_object->add()) {
                //duplicate shortCode
                $filecontent = Tools::file_get_contents($this->folder_save.$old_key.'.tpl');
                LeoECSetting::writeFile($this->folder_save, $duplicate_object->plist_key.'.tpl', $filecontent);
                $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                $this->redirect();
            } else {
                Tools::displayError('Can not duplicate Profiles');
            }
        }
        //edit elment
        if (Tools::isSubmit('saveELement')) {
            $filecontent = Tools::getValue('filecontent');
            $fileName = Tools::getValue('fileName');
            leoECHelper::createDir($this->folder_scan_theme);
            LeoECSetting::writeFile($this->folder_scan_theme, $fileName.'.tpl', $filecontent);
        }
    }

    public function convertObjectToTpl($object_form)
    {
        $tpl = '';
        foreach ($object_form as $object) {
            if ($object['name'] == 'functional_buttons') {
                $tpl .= LeoECSetting::getProductFunctionalButtons();
                $tpl .= $this->convertObjectToTpl($object['element']);
                $tpl .= '</div>';
            } else if ($object['name'] == 'code') {
                $tpl .= $object['code'];
            } else {
                if (!isset($this->file_content[$object['name']])) {
                    $this->returnFileContent($object['name']);
                }
                $tpl .= $this->file_content[$object['name']];
            }
        }
        return $tpl;
    }

    public function returnFileContent($pelement)
    {
        $tpl_dir = $this->folder_scan_theme.$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = $this->folder_scan_module.$pelement.'.tpl';
        }
        $this->file_content[$pelement] = Tools::file_get_contents($tpl_dir);
        return $this->file_content[$pelement];
    }

    public function renderList()
    {
        if (Tools::getIsset('pelement')) {
            $helper = new HelperForm();
            $helper->submit_action = 'saveELement';
            $inputs = array(
                array(
                    'type' => 'textarea',
                    'name' => 'filecontent',
                    'label' => $this->l('File Content'),
                    'desc' => $this->l('Please carefully when edit tpl file'),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'fileName',
                )
            );
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => sprintf($this->l('You are Editing file: %s'), Tools::getValue('pelement').'.tpl'),
                        'icon' => 'icon-cogs'
                    ),
                    'action' => Context::getContext()->link->getAdminLink('AdminLeoElementsShortcodes'),
                    'input' => $inputs,
                    'name' => 'importData',
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'button btn btn-default pull-right'
                    ),
                    'tinymce' => false,
                ),
            );
            $helper->tpl_vars = array(
                'fields_value' => $this->getFileContent()
            );
            return $helper->generateForm(array($fields_form));
        }
        $this->initToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        return $this->importForm() . parent::renderList();
    }
    
    public function importForm()
    {
        $helper = new HelperForm();
        $helper->submit_action = 'import' . $this->table;
        $inputs = array(
            array(
                'type' => 'file',
                'name' => 'importFile',
                'label' => $this->l('File'),
                'desc' => $this->l('Only accept xml file'),
            ),
        );
        $fields_form = array(
            'form' => array(
                'action' => Context::getContext()->link->getAdminLink('AdminLeoElementsShortcodeController'),
                'input' => $inputs,
                'submit' => array('title' => $this->l('Import'), 'class' => 'button btn btn-success'),
                'tinymce' => false,
            ),
        );
        $helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
        $helper->identifier = $this->identifier;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = 'xml_import';
        $html = $helper->generateForm(array($fields_form));

        return $html;
    }

    public function getFileContent()
    {
        $pelement = Tools::getValue('pelement');
        $tpl_dir = $this->folder_scan_theme.$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = $this->folder_scan_module.$pelement.'.tpl';
        }
        return array('fileName' => $pelement, 'filecontent' => Tools::file_get_contents($tpl_dir));
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        $this->helper->module = Leoelements::getInstance();
    }

    public function renderForm()
    {
        $id_leoelements_product_list = (int)Tools::getValue('id_leoelements_product_list', 0);
        
        $icon_url = _MODULE_DIR_.'leoelements/views/img/logo.png';
        $is_edit = Tools::getValue('id_leoelements_product_list');
        $hook_header = LeoECSetting::getHook('header');
        
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;
        
        $sql = 'SELECT c.id_leoelements_contents, c.name, c.hook, c.type, c.content_key FROM '._DB_PREFIX_.'leoelements_contents c INNER JOIN '._DB_PREFIX_.'leoelements_contents_shop cs ON (c.id_leoelements_contents = cs.id_leoelements_contents) WHERE cs.id_shop = '. (int)$id_shop . ' AND c.type like "hook_product_list"';
        $all_leoelements_contents = Db::getInstance()->executes($sql);
        $leoelements_contents_hook = array();
        foreach ($all_leoelements_contents as $ccontent){
            $params = array(
                'post_type' => 'hook_product_list',
                'id_post' => $ccontent['id_leoelements_contents'],
                'id_lang' => (int)$id_lang,
                'id_product_list' => $id_leoelements_product_list,
            );
            $url_params = http_build_query($params);
            $ccontent['url'] = $this->context->link->getAdminLink('AdminLeoElementsCreator') . '&' . $url_params;
            $leoelements_contents_hook[$ccontent['hook']][] = $ccontent;
        }
        
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/form.js');
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/function.js');
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/product-list.js');
        $this->context->controller->addCss(leoECHelper::getCssAdminDir().'admin/form.css');
        $source_file = Tools::scandir($this->folder_scan_module, 'tpl');
        if (is_dir($this->folder_scan_theme)) {
            $source_template_file = Tools::scandir($this->folder_scan_theme, 'tpl');
            $source_file = array_merge($source_file, $source_template_file);
        }
        foreach($source_file as $k => $file) {
            if (strrpos($file, "plist") !== false) {
                unset($source_file[$k]);
            }
        }
        $elements = array();
        $icon_list = LeoECSetting::getProductElementIcon();
        foreach ($source_file as $value) {
            $fileName = basename($value, '.tpl');
            if ($fileName == 'index') {
                continue;
            }
            $elements[$fileName] = array(
                'name' => str_replace('_', ' ', $fileName),
                'icon' => (isset($icon_list[$fileName]) ? $icon_list[$fileName] : 'icon-sun'));
        }
        $params = array('gridLeft' => array(), 'gridRight' => array());

        $this->object->params = str_replace($this->str_search, $this->str_relace, $this->object->params);

        if (isset($this->object->params)) {
            //add sample data
            if (Tools::getIsset('sampleplist')) {
                switch (Tools::getValue('sampleplist')) {
                    case 'type1':
                        $this->object->class = 'leo-plist-style';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"functional_buttons","element":{"0":{"name":"quickview"},"1":{"name":"add_to_cart"},"2":{"name":"wishlist"},"3":{"name":"compare"}}}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"code","code":"{if isset(_APDOLA_product.category_name) && isset(_APDOLA_product.id_category_default)}_APENTER__APTAB_<div class=_APQUOT_category-default_APQUOT_>_APENTER__APTAB__APTAB_<a href=_APQUOT_{_APDOLA_link->getCategoryLink(_APDOLA_product.id_category_default)|escape:_APAPOST_html_APAPOST_:_APAPOST_UTF-8_APAPOST_}_APQUOT_ title=_APQUOT_{_APDOLA_product.category_name}_APQUOT_>{_APDOLA_product.category_name}</a>_APENTER__APTAB_</div>_APENTER_{/if}"},"2":{"name":"code","code":"<div class=_APQUOT_wr-price-reviews_APQUOT_>"},"3":{"name":"product_price_and_shipping"},"4":{"name":"reviews"},"5":{"name":"code","code":"</div>"}}}';
                        break;
                    case 'type2':
                        $this->object->class = 'leo-plist-style-2';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"quickview"}},"gridRight":{"0":{"name":"code","code":"<div class=_APQUOT_pro-info_APQUOT_>"},"1":{"name":"product_variants"},"2":{"name":"code","code":"</div>"},"3":{"name":"product_name"},"4":{"name":"reviews"},"5":{"name":"product_price_and_shipping"},"6":{"name":"functional_buttons","element":{"0":{"name":"add_to_cart"},"1":{"name":"wishlist"},"2":{"name":"compare"}}}}}';
                        break;
                    case 'type3':
                        $this->object->class = 'leo-plist-style-1';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"functional_buttons","element":{"0":{"name":"quickview"},"1":{"name":"wishlist"},"2":{"name":"compare"},"3":{"name":"add_to_cart"}}}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"reviews"},"2":{"name":"product_price_and_shipping"}}}';
                        break;
                    case 'type4':
                        $this->object->class = 'leo-plist-style-4';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_flags"},"1":{"name":"product_thumbnail"},"2":{"name":"functional_buttons","element":{"0":{"name":"wishlist"},"1":{"name":"compare"}}}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"product_size"},"2":{"name":"product_price_and_shipping"},"3":{"name":"code","code":"<div class=_APQUOT_button-review_APQUOT_>"},"4":{"name":"reviews"},"5":{"name":"add_to_cart"},"6":{"name":"quickview"},"7":{"name":"code","code":"</div>"}}}';
                        break;
                    case 'type5':
                        $this->object->class = 'leo-plist-style-1-1';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"functional_buttons","element":{"0":{"name":"quickview"},"1":{"name":"add_to_cart"},"2":{"name":"wishlist"},"3":{"name":"compare"}}}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"product_price_and_shipping"},"2":{"name":"reviews"},"3":{"name":"code","code":"<div class=_APQUOT_wr-price-reviews_APQUOT_>"},"4":{"name":"code","code":"</div>"}}}';
                        break;
                    case 'type6':
                        $this->object->class = 'leo-plist-style-2-1';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"code","code":"{if isset(_APDOLA_product.embedded_attributes.quantity_all_versions) && _APDOLA_product.embedded_attributes.quantity_all_versions == 0}_APENTER_    <span class=_APQUOT_label-stock out-stock-label_APQUOT_><span>{l s=_APAPOST_out stock_APAPOST_ d=_APAPOST_Shop.Theme.Global_APAPOST_}</span></span>_APENTER_ {else}_APENTER_        <span class=_APQUOT_label-stock in-stock-label_APQUOT_>{l s=_APAPOST_In stock_APAPOST_ d=_APAPOST_Shop.Theme.Global_APAPOST_}</span>_APENTER_{/if}_APENTER_"},"3":{"name":"functional_buttons","element":{"0":{"name":"add_to_cart"},"1":{"name":"wishlist"},"2":{"name":"compare"},"3":{"name":"quickview"}}},"4":{"name":"code","code":"<div class=_APQUOT_full_attribute_APQUOT_>"},"5":{"name":"product_full_attribute"},"6":{"name":"code","code":"</div>"}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"code","code":"{if isset(_APDOLA_product.category_name) && isset(_APDOLA_product.id_category_default)}_APENTER__APTAB_<div class=_APQUOT_category-default_APQUOT_>_APENTER__APTAB__APTAB_<a href=_APQUOT_{_APDOLA_link->getCategoryLink(_APDOLA_product.id_category_default)|escape:_APAPOST_html_APAPOST_:_APAPOST_UTF-8_APAPOST_}_APQUOT_ title=_APQUOT_{_APDOLA_product.category_name}_APQUOT_>{_APDOLA_product.category_name}</a>_APENTER__APTAB_</div>_APENTER_{/if}"},"2":{"name":"code","code":"<div class=_APQUOT_wr-price-reviews_APQUOT_>"},"3":{"name":"product_price_and_shipping"},"4":{"name":"reviews"},"5":{"name":"code","code":"</div>"}}}';
                        break;
                    case 'type7':
                        $this->object->class = 'leo-plist-style-5';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"code","code":"{if _APDOLA_product.has_discount}_APENTER_<div class=_APQUOT_leo-more-cdown_APQUOT_ data-idproduct=_APQUOT_{_APDOLA_product.id_product}_APQUOT_></div>_APENTER_{/if}"},"2":{"name":"product_flags"},"3":{"name":"add_to_cart"}},"gridRight":{"0":{"name":"code","code":"<div class=_APQUOT_group-transition_APQUOT_>_APENTER_<div class=_APQUOT_wr-sale-stock_APQUOT_>_APENTER__APTAB_<div class=_APQUOT_leo-sale_APQUOT_>_APENTER__APTAB__APTAB_{if _APDOLA_product.has_discount}_APENTER__APTAB_      _APTAB_{if _APDOLA_product.discount_type === _APAPOST_percentage_APAPOST_}_APENTER__APTAB_       _APTAB__APTAB_<label class=_APQUOT_label product-flag discount-percentage_APQUOT_>{_APDOLA_product.discount_percentage}</label>_APENTER__APTAB__APTAB_    {elseif _APDOLA_product.discount_type === _APAPOST_amount_APAPOST_}_APENTER__APTAB__APTAB__APTAB__APTAB_<label class=_APQUOT_label product-flag discount-amount discount-product_APQUOT_>{_APDOLA_product.discount_amount_to_display}</label>_APENTER__APTAB__APTAB__APTAB_{/if}_APENTER__APTAB_    {/if}_APENTER__APTAB_</div>_APENTER_    {if _APDOLA_product.show_availability}_APENTER_      {if _APDOLA_product.availability == _APAPOST_available_APAPOST_}_APENTER_        <span class=_APQUOT_product-available_APQUOT_>{l s=_APAPOST_In stock_APAPOST_ d=_APAPOST_Shop.Theme.Global_APAPOST_}</span>_APENTER_      {elseif _APDOLA_product.availability == _APAPOST_last_remaining_items_APAPOST_}_APENTER_        <span class=_APQUOT_product-unavailable_APQUOT_>{l s=_APAPOST_Last product_APAPOST_ d=_APAPOST_Shop.Theme.Global_APAPOST_}</span>_APENTER_      {else}_APENTER_        <span class=_APQUOT_product-last-items_APQUOT_>{l s=_APAPOST_Out of stock_APAPOST_ d=_APAPOST_Shop.Theme.Global_APAPOST_}</span>_APENTER_      {/if}_APENTER_    {/if}_APENTER_</div>"},"1":{"name":"functional_buttons","element":{"0":{"name":"wishlist"},"1":{"name":"compare"},"2":{"name":"quickview"}}},"2":{"name":"code","code":"</div>"},"3":{"name":"product_name"},"4":{"name":"code","code":"{if isset(_APDOLA_product.category_name) && isset(_APDOLA_product.id_category_default)}_APENTER__APTAB_<div class=_APQUOT_category-default_APQUOT_>_APENTER__APTAB__APTAB_<a href=_APQUOT_{_APDOLA_link->getCategoryLink(_APDOLA_product.id_category_default)|escape:_APAPOST_html_APAPOST_:_APAPOST_UTF-8_APAPOST_}_APQUOT_ title=_APQUOT_{_APDOLA_product.category_name}_APQUOT_>{_APDOLA_product.category_name}</a>_APENTER__APTAB_</div>_APENTER_{/if}"},"5":{"name":"code","code":"<div class=_APQUOT_wr-price-reviews_APQUOT_>"},"6":{"name":"product_price_and_shipping"},"7":{"name":"reviews"},"8":{"name":"code","code":"</div>"}}}';
                        break;
                    case 'type8':
                        $this->object->class = 'leo-plist-style-6';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"functional_buttons","element":{"0":{"name":"quickview"},"1":{"name":"wishlist"},"2":{"name":"compare"}}}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"code","code":"<div class=_APQUOT_wr-price-reviews_APQUOT_>"},"2":{"name":"product_price_and_shipping"},"3":{"name":"add_to_cart"},"4":{"name":"code","code":"</div>"}}}';
                        break;
                    case 'type9':
                        $this->object->class = 'leo-plist-style-7';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"code","code":"{if isset(_APDOLA_product.embedded_attributes.quantity_all_versions) && _APDOLA_product.embedded_attributes.quantity_all_versions == 0}_APENTER_    <div class=_APQUOT_wr-sale-stock_APQUOT_><span class=_APQUOT_product-last-items_APQUOT_><span>{l s=_APAPOST_sold out_APAPOST_ d=_APAPOST_Shop.Theme.Global_APAPOST_}</span></span></div>_APENTER_{/if}"},"3":{"name":"code","code":"<div class=_APQUOT_full_attribute_APQUOT_>"},"4":{"name":"product_full_attribute"},"5":{"name":"code","code":"</div>"}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"code","code":"<div class=_APQUOT_wr-price-reviews_APQUOT_>"},"2":{"name":"product_price_and_shipping"},"3":{"name":"reviews"},"4":{"name":"code","code":"</div>"},"5":{"name":"functional_buttons","element":{"0":{"name":"quickview"},"1":{"name":"add_to_cart"},"2":{"name":"wishlist"},"3":{"name":"compare"}}}}}';
                        break;
                    case 'type10':
                        $this->object->class = 'leo-plist-style-8';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"functional_buttons","element":{"0":{"name":"quickview"},"1":{"name":"wishlist"},"2":{"name":"compare"}}}},"gridRight":{"0":{"name":"product_name"},"1":{"name":"reviews"},"2":{"name":"code","code":"<div class=_APQUOT_box-card-info_APQUOT_>"},"3":{"name":"add_to_cart"},"4":{"name":"product_price_and_shipping"},"5":{"name":"code","code":"</div>"}}}';
                        break;
                    case 'type11':
                        $this->object->class = 'leo-plist-style-9';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"}},"gridRight":{"0":{"name":"reviews"},"1":{"name":"product_name"},"2":{"name":"product_price_and_shipping"}}}';
                        break;
                    case 'type12':
                        $this->object->class = 'leo-plist-style-3';
                        $this->object->params = '{"gridLeft":{"0":{"name":"product_thumbnail"},"1":{"name":"product_flags"},"2":{"name":"functional_buttons","element":{"0":{"name":"wishlist"}}}},"gridRight":{"0":{"name":"code","code":"<div class=_APQUOT_p-name_APQUOT_>"},"1":{"name":"product_name"},"2":{"name":"quickview"},"3":{"name":"code","code":"</div>"},"4":{"name":"code","code":"<div class=_APQUOT_p-price_APQUOT_>"},"5":{"name":"add_to_cart"},"6":{"name":"product_price_and_shipping"},"7":{"name":"code","code":"</div>"}}}';
                        break;
                    default:
                        break;
                }
            }
            $this->object->params = str_replace($this->str_search, $this->str_relace, $this->object->params);
            $params = json_decode($this->object->params, true);
        }

        //$params['gridLeft'] = $this->replaceSpecialStringToHtml($params['gridLeft']);
        //$params['gridRight'] = $this->replaceSpecialStringToHtml($params['gridRight']);

        $block_list = array(
            'gridLeft' => array('title' => 'Product-Image', 'class' => 'left-block'),
            'gridRight' => array('title' => 'Product-Meta', 'class' => 'right-block'),
        );

        $file_link = '';
        $file_content = '';
        if(file_exists($this->folder_save.$this->object->plist_key.'.tpl')) {
            $file_link = $this->folder_save.$this->object->plist_key.'.tpl';
            $file_content = Tools::file_get_contents($this->folder_save.$this->object->plist_key.'.tpl');
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Leo Elements Product List Manage'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Product List can apply for a widget in leoelementor, apply for a category, manufacturer, search, price drop, new product').'</div>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product List Key'),
                    'name' => 'plist_key',
                    'readonly' => 'readonly',
                    'desc' => $this->l('Tpl File name'),
                ),
                array(
                    'title' => $this->l('Product List File Content. Click to see file content.'),
                    'type' => 'leoelement_file',
                    'name' => 'file_link',
                    'file_link' => $file_link,
                    'file_content' => $file_content,
                    'width' => 140
                ),
                array(
                    'label' => $this->l('Class'),
                    'type' => 'text',
                    'name' => 'class',
                    'width' => 140
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
                    'desc' => $this->l('Display Number Products In List Mode Or Grid Mode In Category Page....'),
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
                    'desc' => $this->l('How many column display in widget of module.'),
                    'form_group_class' => 'productlist grid',
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
                    'form_group_class' => 'productlist grid',
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
                    'form_group_class' => 'productlist grid',
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
                    'form_group_class' => 'productlist grid',
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
                    'form_group_class' => 'productlist grid',
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
                    'form_group_class' => 'productlist grid',
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
                    'form_group_class' => 'productlist grid',
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Top function Config: Category, manufacturer, search').'</div>',
                ),
                array(
                    'label' => $this->l('Show Total Products'),
                    'type' => 'select',
                    'name' => 'top_total',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Yes')),
                            array('id' => '0', 'name' => $this->l('No')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Show Grid Select'),
                    'type' => 'select',
                    'name' => 'top_grid',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Yes')),
                            array('id' => '0', 'name' => $this->l('No')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Show Sort by'),
                    'type' => 'select',
                    'name' => 'top_sortby',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Yes')),
                            array('id' => '0', 'name' => $this->l('No')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Paging: Category, manufacturer, search').'</div>',
                ),
                array(
                    'label' => $this->l('Showing Pagination Count'),
                    'type' => 'select',
                    'name' => 'pg_count',
                    'options' => array('query' => array(
                            array('id' => '0', 'name' => $this->l('No')),
                            array('id' => '1', 'name' => $this->l('Yes')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Pagination Type'),
                    'type' => 'select',
                    'name' => 'pg_type',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Default')),
                            array('id' => '2', 'name' => $this->l('scroll')),
                            array('id' => '3', 'name' => $this->l('Load More Button')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Run Ajax').'</div>',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('AJAX Show More Product Image'),
                    'name' => 'plist_load_more_product_img',
                    'values' => LeoECSetting::returnYesNo(),
                ),
                array(
                    'type' =>'select',
                    'label' => $this->l('Show More Product Image'),
                    'name' => 'plist_load_more_product_img_option',
                    'options' => array(
                        'query' => array(
                            array('id' => 1, 'name' => $this->l('Get second image of product except image_cover.')),
                            array('id' => 2, 'name' => $this->l('Get second image of product_attribute ( if product not attribute get second image of product ).'),),
                            array('id' => 3, 'name' => $this->l('Get second image of product except image_showed.'),),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => 1,
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('AJAX Show Multiple Product Images'),
                    'name' => 'plist_load_multi_product_img',
                    'values' => LeoECSetting::returnYesNo(),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('AJAX Show Count Down Product'),
                    'name' => 'plist_load_cdown',
                    'values' => LeoECSetting::returnYesNo(),
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Other features').'</div>',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Use Swipe image (Mobile)'),
                    'name' => 'lmobile_swipe',
                    'desc' => $this->l('You can swipe the product photo in product list.'),
                    'values' => LeoECSetting::returnYesNo(),
                ),
                array(
                    'type' => 'leoelement_Sample',
                    'name' => 'leoelement_Sample',
                    'demoplistlink' => 'index.php?controller=AdminLeoElementsProductList'.'&amp;token='.Tools::getAdminTokenLite('AdminLeoElementsProductList').'&amp;addleoelements_product_list',
                ),
                array(
                    'type' => 'leoelement_Grid',
                    'name' => 'leoelement_Grid',
                    'label' => $this->l('Layout'),
                    'elements' => $elements,
                    'params' => $params,
                    'blockList' => $block_list
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'params'
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
                    'icon' => 'process-icon-save')
            )
        );
        return parent::renderForm();
    }

    public function replaceSpecialStringToHtml($arr)
    {
        foreach ($arr as &$v) {
            if ($v['name'] == 'code') {
                // validate module
                $v['code'] = str_replace($this->str_search, $this->str_relace, $v['code']);
            } else {
                if ($v['name'] == 'functional_buttons') {
                    foreach ($v as &$f) {
                        if ($f['name'] == 'code') {
                            // validate module
                            $f['code'] = str_replace($this->str_search, $this->str_relace, $f['code']);
                        }
                    }
                }
            }
        }
        return $arr;
    }

    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);

        if (!$obj->id) {
            $num = LeoECSetting::getRandomNumber();
            $file_value['plist_key'] = 'plist'.$num;
            $file_value['name'] = $file_value['plist_key'];
            if(!$obj->class) {
                $file_value['class'] = 'product-list-'.$num;
            }
            foreach ($this->params_field as $k=>$v) {
                if($k != 'class') {
                    $file_value[$k] = $v;
                }
            }
            //import data
            if($obj->params) {
                $params = json_decode($obj->params,1);
                foreach ($params as $k=>$v) {
                    if($k != 'class') {
                        $file_value[$k] = $v;
                    }
                }
            }
        } else {
            $params = json_decode($obj->params,1);
            if($params) {
                foreach($params as $k1=>$v1) {
                    if(is_array($v1)) {
                        foreach($v1 as $k2=>$v2) {
                            $file_value[$k1.'_'.$k2] = $v2;
                        }
                    } else {
                        $file_value[$k1] = $v1;
                    }
                }
            }
        }
        return $file_value;
    }

    public function processAdd()
    {
        if ($obj = parent::processAdd()) {
            $this->saveTplFile($obj->plist_key, $obj->params);
            $this->processParams($obj->params);
        }
    }

    public function processUpdate()
    {
        if ($obj = parent::processUpdate()) {
            $this->saveTplFile($obj->plist_key, $obj->params);
            $this->processParams($obj->params);
        }
    }

    public function processDelete()
    {
        $object = $this->loadObject();

        $profiles = new LeoElementsProfilesModel();
        $all_profile = $profiles->getAllProfileByShop();
        $exist = 0;

        foreach($all_profile as $profile) {
            $params = json_decode($profile['params'],1);
            if($params['productlist_layout'] == $object->plist_key || $params['productlist_layout_mobile'] == $object->plist_key || $params['productlist_layout_tablet'] == $object->plist_key || $params['manufacture_layout'] == $object->plist_key || $params['search_layout'] == $object->plist_key || $params['pricedrop_layout'] == $object->plist_key || $params['newproduct_layout'] == $object->plist_key || $params['bestsales_layout'] == $object->plist_key) {
                $exist = $profile['name'];
                break;
            }
        }
        $all_category = LeoElementsProfilesModel::getAllCategoryFull();
        foreach($all_category as $profile) {
            $params = json_decode($profile['params'],1);
            if($params['product_list'] == $object->plist_key || $params['product_list_mobile'] == $object->plist_key || $params['product_list_tablet'] == $object->plist_key) {
                $exist = $profile['name'];
                break;
            }
        }

        if($exist) {
            $this->errors[] = sprintf($this->l('Can not delete this Product List Layout. Because this layout used in %s'), $exist);
        } else {
            Tools::deleteFile($this->folder_save.$object->plist_key.'.tpl');
            parent::processDelete();
        }
    }

    public function saveTplFile($plist_key, $params = '')
    {
        $data_form = str_replace($this->str_search, $this->str_relace, $params);
        $data_form = json_decode($data_form, true);

        // $config = $data_form['config'];
        $grid_left = $data_form['gridLeft'];
        $grid_right = $data_form['gridRight'];

        $tpl_grid = LeoECSetting::getProductContainer();
        $tpl_grid .= LeoECSetting::getProductLeftBlock().$this->convertObjectToTpl($grid_left)."</div>\n";
        $tpl_grid .= LeoECSetting::getProductRightBlock().$this->convertObjectToTpl($grid_right)."</div>\n";
        $tpl_grid .= LeoECSetting::getProductContainerEnd();
        $folder = $this->folder_save;
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        $file = $plist_key.'.tpl';
        $tpl_grid = preg_replace('/\{\*[\s\S]*?\*\}/', '', $tpl_grid);
        $tpl_grid = str_replace(" mod='leoelements'", '', $tpl_grid);
        LeoECSetting::writeFile($folder, $file, leoECHelper::getLicenceTPL().$tpl_grid);
    }

    public function processParams($params)
    {
        $params = json_decode($params);
        if ($params === null) {
            $params = new stdClass();
        }
        foreach ($this->params_field as $k=>$v) {
            if($k != "check_filter") {
                $params->{$k} = Tools::getValue($k);
            }
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'leoelements_product_list` SET `params` = "'.pSQL(json_encode($params), true).'" WHERE `id_leoelements_product_list` = "'.$this->object->id.'"';
        Db::getInstance()->execute($sql);
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
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                    .'<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
        return $object;
    }
    
    public function displayDuplicateLink($token = null, $id = null, $name = null)
    {
        $controller = 'AdminLeoElementsProductList';
        $token = Tools::getAdminTokenLite($controller);
        $html = '<a href="#" title="Duplicate" onclick="confirm_link(\'\', \'Duplicate Product List ID '.$id.'. If you wish to proceed, click &quot;Yes&quot;. If not, click &quot;No&quot;.\', \'Yes\', \'No\', \'index.php?controller='.$controller.'&amp;id_leoelements_product_list='.$id.'&amp;duplicateleoelements_product_list&amp;token='.$token.'\', \'#\')">
            <i class="icon-copy"></i> Duplicate
        </a>';
        
        // validate module
        unset($name);
        
        return $html;
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function access($action, $disable = false)
    {
        if (Tools::getIsset('update'.$this->table) && Tools::getIsset($this->identifier)) {
            // Allow person see "EDIT" form
            $action = 'view';
        }
        return parent::access($action, $disable);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        # SET ACTION : IMPORT DATA
        if ($this->can_import && Tools::getIsset('import' . $this->table)) {
            if ($this->access('edit')) {
                $this->action = 'import';
            }
        }
        
        if (count($this->errors) <= 0) {
            if (Tools::isSubmit('duplicate'.$this->table)) {
                if ($this->id_object) {
                    if (!$this->access('add')) {
                        $this->errors[] = $this->trans('You do not have permission to duplicate this.', array(), 'Admin.Notifications.Error');
                    }
                }
            } elseif ($this->can_import && Tools::getIsset('import' . $this->table)) {
                if (!$this->access('edit')) {
                    $this->errors[] = $this->trans('You do not have permission to import data.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
