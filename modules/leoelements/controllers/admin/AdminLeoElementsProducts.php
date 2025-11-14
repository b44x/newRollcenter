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

require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/leoECHelper.php');

class AdminLeoElementsProductsController extends ModuleAdminControllerCore
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
    public $folder_save = '';
    public $folder_scan_module = '';
    public $folder_scan_theme = '';


    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'leoelements_products';
        $this->className = 'LeoElementsProductsModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;
        $this->can_import = true;
        $this->context = Context::getContext();
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'leoelements_products_shop` ps ON (ps.`id_leoelements_products` = a.`id_leoelements_products`)';
        $this->_select .= ' ps.active as active, ps.active_mobile as active_mobile, ps.active_tablet as active_tablet';

        $this->order_by = 'id_leoelements_products';
        $this->order_way = 'DESC';
        parent::__construct();
        $this->fields_list = array(
            'id_leoelements_products' => array(
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
            'class_detail' => array(
                'title' => $this->l('Class'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!class_detail',
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
        $this->folder_save = _PS_THEME_DIR_.'modules/leoelements/views/templates/front/details/';
        $this->folder_scan_module = _PS_MODULE_DIR_.'/leoelements/views/templates/front/details/';
        $this->folder_scan_theme = _PS_THEME_DIR_.'/modules/leoelements/views/templates/front/details/';

        $this->_where = ' AND ps.id_shop='.(int)$this->context->shop->id;
        $this->theme_name = _THEME_NAME_;
        $this->profile_css_folder = $this->theme_dir.'modules/'.$this->module_name.'/';
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
        $this->str_search = array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APOCBRACKET_', '_APCCBRACKET_', '_APDOLA_');
        $this->str_relace = array('&', '\"', '\'', '\t', '\r', '\n', '[', ']', '{', '}', '$');
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
                    Media::addJsDef(array('record_id' => 'leoelements_productsBox[]'));
                }
        }
    }
    
    /**
     * OVERRIDE CORE
     */
    public function processExport($text_delimiter = '"')
    {
        $record_id = Tools::getValue('record_id');
        $file_name = 'ap_product_detail_all.xml';
        # VALIDATE MODULE
        unset($text_delimiter);
        
        if ($record_id) {
            $record_id_str = implode(", ", $record_id);
            $this->_where = ' AND a.id_leoelements_products IN ( '.pSQL($record_id_str).' )';
            $file_name = 'ap_product_detail.xml';
        }
        
        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        $data = $this->_list;
        $this->file_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->file_content .= '<data>' . "\n";
        $this->file_content .= '<product_details>' . "\n";
        
        if ($data) {
            foreach ($data as $product_detail) {
                $this->file_content .= '<record>' . "\n";
                foreach ($product_detail as $key => $value) {
                    $this->file_content .= '    <'.$key.'>';
                    $this->file_content .= '<![CDATA['.$value.']]>';
                    $this->file_content .= '</'.$key.'>' . "\n";
                }
                $this->file_content .= '</record>' . "\n";
            }
        }
        $this->file_content .= '</product_details>' . "\n";
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
        
        if (isset($files_content->product_details) && $files_content->product_details) {
            foreach ($files_content->product_details->children() as $product_details) {
                if (!$override) {
                    $num = LeoECSetting::getRandomNumber();
                    $obj_model = new LeoElementsProductsModel();
                    $obj_model->plist_key = 'detail'.$num;
                    $obj_model->name = $product_details->name->__toString();
                    $obj_model->class_detail = $product_details->class_detail->__toString();
                    $obj_model->params = $product_details->params->__toString();
                    $obj_model->type = $product_details->type->__toString();
                    $obj_model->active = 0;
                    $obj_model->url_img_preview = $product_details->url_img_preview->__toString();
                    if ($obj_model->save()) {
                        $this->saveTplFile($obj_model->plist_key, $obj_model->params);
                    }
                }
            }
            $this->confirmations[] = $this->trans('Successful importing.', array(), 'Admin.Notifications.Success');
        } else {
            $this->errors[]        = $this->trans('Failed to import.', array(), 'Admin.Notifications.Error');
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
        $this->redirect_after .= '&id_leoelements_products='.$object->id;
        $this->redirect();
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::getIsset('active_mobileleoelements_products') || Tools::getIsset('active_tableleoelements_products')) {
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $result = Tools::getIsset('active_mobileleoelements_products')?$object->toggleStatusMT('active_mobile'):$object->toggleStatusMT('active_tablet');
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

        if (Tools::getIsset('duplicateleoelements_products')) {
            $id = Tools::getValue('id_leoelements_products');
            $model = new LeoElementsProductsModel($id);
            $duplicate_object = $model->duplicateObject();
            $duplicate_object->name = $this->l('Duplicate of').' '.$duplicate_object->name;
            $old_key = $duplicate_object->plist_key;
            $duplicate_object->plist_key = 'detail'.LeoECSetting::getRandomNumber();
            # FIX 1751 : empty
            $duplicate_object->params = $model->params;
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
                //DONGND:: fix can save group column when change class
                if (isset($object['form']['class']) && $object['form']['class'] != '') {
                    $tpl .= '<div class="'.$object['form']['class'].'">';
                } else {
                    $tpl .= '<div class="row">';
                }
                foreach ($object['columns'] as $objectC) {
                    $tpl .= '<div class="'.$this->convertToColumnClass($objectC['form']).'">';
                                $tpl .= $this->convertObjectToTpl($objectC['sub']);
                                $tpl .= '
                            </div>';
                }
                
                $tpl .= '</div>';
            } else if ($object['name'] == 'code') {
                $tpl .= $object['code'];
            } else {
                if (!isset($this->file_content[$object['name']])) {
                    $this->returnFileContent($object['name']);
                    //DONGND:: add config to type gallery
                    if ($object['name'] == "product_image_with_thumb" || $object['name'] == "product_image_show_all") {
                        $strdata = '';
                        foreach ($object['form'] as $key => $value) {
                            $strdata .= ' data-'.$key.'="'.$value.'"';
                        }
                        $this->file_content[$object['name']] = str_replace('id="content">', 'id="content"'.$strdata.'>', $this->file_content[$object['name']]);
                    }
                }
                //add class
                $tpl .= $this->file_content[$object['name']];
            }
        }
        return $tpl;
    }

    public function convertToColumnClass($form)
    {
        $class = '';
        foreach ($form as $key => $val) {
            //DONGND:: check class name of column
            if ($key == 'class') {
                if ($val != '') {
                    $class .= ($class=='')?$val:' '.$val;
                }
            } else {
                $class .= ($class=='')?'col-'.$key.'-'.$val:' col-'.$key.'-'.$val;
            }
        }
        return $class;
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
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/form.js');
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/function.js');
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/detail.js');
        $this->context->controller->addCss(leoECHelper::getCssAdminDir().'admin/form.css');
        $this->context->controller->addCss(leoECHelper::getCssAdminDir().'admin/style.css');
        
        $source_file = Tools::scandir($this->folder_scan_module, 'tpl');
        if (is_dir($this->folder_scan_theme)) {
            $source_template_file = Tools::scandir($this->folder_scan_theme, 'tpl');
            $source_file = array_merge($source_file, $source_template_file);
        }
        
        $params = array('gridLeft' => array(), 'gridRight' => array());

        $this->object->params = str_replace($this->str_search, $this->str_relace, $this->object->params);

        $config_dir = $this->folder_scan_theme.'config.json';
        if (!file_exists($config_dir)) {
            $config_dir = $this->folder_scan_module.'config.json';
        }
        
        $config_file = json_decode(Tools::file_get_contents($config_dir), true);
        $element_by_name = array();
        foreach ($config_file as $k1 => $groups) {
            foreach ($groups['group'] as $k2 => $group) {
                $config_file[$k1]['group'][$k2]['dataForm'] = (!isset($group['data-form']))?'':json_encode($group['data-form']);
                if (isset($group['file'])) {
                    $element_by_name[$group['file']] = $group;
                }
            }
        }
       
        if (isset($this->object->params)) {
            //add sample data
            if (Tools::getIsset('sampledetail')) {
                switch (Tools::getValue('sampledetail')) {
                    case 'product_image_thumbs_bottom':
                        $this->object->url_img_preview = 'https://i.pinimg.com/originals/8c/16/f9/8c16f9f024af16977adc1f618872eb8b.jpg';
                        $this->object->params = '{"gridLeft":{"0":{"name":"functional_buttons","form":"","columns":{"0":{"form":{"form_id":"form_9367402777406408","md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_image_with_thumb","form":{"templateview":"bottom","numberimage":"5","numberimage1200":"5","numberimage992":"4","numberimage768":"3","numberimage576":"3","numberimage480":"2","numberimage360":"2","templatemodal":"1","templatethumb":"1","templatezoomtype":"out","zoomposition":"right","zoomwindowwidth":"400","zoomwindowheight":"400","use_leo_gallery":"1"}}}},"1":{"form":{"form_id":"form_15874367062488778","md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_detail_name","form":""},"1":{"name":"hook_display_product_additional_info","form":""},"2":{"name":"hook_display_leo_product_review_extra","form":""},"3":{"name":"product_price","form":""},"4":{"name":"product_description_short","form":""},"5":{"name":"product_customization","form":""},"6":{"name":"product_actions_form","form":""},"7":{"name":"hook_display_reassurance","form":""}}},"2":{"form":{"form_id":"form_4666379129988496","md":12,"lg":12,"xl":12,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_more_info_tab","form":""},"1":{"name":"product_accessories","form":""},"2":{"name":"hook_display_footer_product","form":""}}}}}},"class":"product-image-thumbs product-thumbs-bottom"}';
                        break;
                    case 'product_image_thumbs_left':
                        $this->object->url_img_preview = 'https://i.pinimg.com/originals/98/b4/b0/98b4b05fef8913b2a37cbb592b921e7b.jpg';
                        $this->object->params = '{"gridLeft":{"0":{"name":"functional_buttons","form":"","columns":{"0":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_image_with_thumb","form":{"templateview":"left","numberimage":"5","numberimage1200":"4","numberimage992":"4","numberimage768":"3","numberimage576":"3","numberimage480":"2","numberimage360":"2","templatemodal":"1","templatethumb":"1","templatezoomtype":"in","zoomposition":"right","zoomwindowwidth":"400","zoomwindowheight":"400","use_leo_gallery":"1"}}}},"1":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_detail_name","form":""},"1":{"name":"hook_display_product_additional_info","form":""},"2":{"name":"hook_display_leo_product_review_extra","form":""},"3":{"name":"product_price","form":""},"4":{"name":"product_description_short","form":""},"5":{"name":"product_customization","form":""},"6":{"name":"product_actions_form","form":""},"7":{"name":"hook_display_reassurance","form":""}}},"2":{"form":{"md":12,"lg":12,"xl":12,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_more_info_accordions","form":""},"1":{"name":"product_accessories","form":""},"2":{"name":"hook_display_footer_product","form":""}}}}}},"class":"product-image-thumbs product-thumbs-left"}';
                        break;
                    case 'product_image_thumbs_right':
                        $this->object->url_img_preview = 'https://i.pinimg.com/originals/81/c4/41/81c441c1b2f6c3e56b3da56b65324423.jpg';
                        $this->object->params = '{"gridLeft":{"0":{"name":"functional_buttons","form":"","columns":{"0":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_image_with_thumb","form":{"templateview":"right","numberimage":"5","numberimage1200":"4","numberimage992":"4","numberimage768":"3","numberimage576":"3","numberimage480":"2","numberimage360":"2","templatemodal":"1","templatethumb":"1","templatezoomtype":"in","zoomposition":"right","zoomwindowwidth":"400","zoomwindowheight":"400","use_leo_gallery":"1"}}}},"1":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_detail_name","form":""},"1":{"name":"hook_display_product_additional_info","form":""},"2":{"name":"hook_display_leo_product_review_extra","form":""},"3":{"name":"product_price","form":""},"4":{"name":"product_description_short","form":""},"5":{"name":"product_customization","form":""},"6":{"name":"product_actions_form","form":""},"7":{"name":"hook_display_reassurance","form":""}}},"2":{"form":{"md":12,"lg":12,"xl":12,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_more_info_default","form":""},"1":{"name":"product_accessories","form":""},"2":{"name":"hook_display_footer_product","form":""}}}}}},"class":"product-image-thumbs product-thumbs-right"}';
                        break;
                    case 'product_image_no_thumbs':
                        $this->object->url_img_preview = 'https://i.pinimg.com/originals/60/ca/57/60ca570f6a8254c3741d8c9db78eb3d5.jpg';
                        $this->object->params = '{"gridLeft":{"0":{"name":"functional_buttons","form":"","columns":{"0":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_image_with_thumb","form":{"templateview":"none","numberimage":"5","numberimage1200":"5","numberimage992":"4","numberimage768":"3","numberimage576":"3","numberimage480":"2","numberimage360":"2","templatemodal":"1","templatethumb":"1","templatezoomtype":"in","zoomposition":"right","zoomwindowwidth":"400","zoomwindowheight":"400","use_leo_gallery":"0"}}}},"1":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_detail_name","form":""},"1":{"name":"hook_display_product_additional_info","form":""},"2":{"name":"hook_display_leo_product_review_extra","form":""},"3":{"name":"product_price","form":""},"4":{"name":"product_description_short","form":""},"5":{"name":"product_customization","form":""},"6":{"name":"product_actions_form","form":""},"7":{"name":"hook_display_reassurance","form":""}}},"2":{"form":{"md":12,"lg":12,"xl":12,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_more_info_tab","form":""},"1":{"name":"product_accessories","form":""},"2":{"name":"hook_display_footer_product","form":""}}}}}},"class":"product-image-thumbs no-thumbs"}';
                        break;
                    case 'product_image_no_thumbs_fullwidth':
                        $this->object->url_img_preview = 'https://i.pinimg.com/originals/c5/d9/02/c5d9025b68250832a31eac3b6d344955.jpg';
                        $this->object->params = '{"gridLeft":{"0":{"name":"functional_buttons","form":"","columns":{"0":{"form":{"class":"","xl":"12","lg":"12","md":"12","sm":"12","xs":"12","sp":"12"},"element":"column","sub":{"0":{"name":"product_image_with_thumb","form":{"templateview":"none","numberimage":"5","numberimage1200":"5","numberimage992":"4","numberimage768":"3","numberimage576":"3","numberimage480":"2","numberimage360":"2","templatemodal":"1","templatethumb":"1","templatezoomtype":"in","zoomposition":"right","zoomwindowwidth":"400","zoomwindowheight":"400","use_leo_gallery":"0"}}}},"1":{"form":{"class":"offset-lg-2 offset-xl-2","xl":"8","lg":"8","md":"12","sm":"12","xs":"12","sp":"12"},"element":"column","sub":{"0":{"name":"product_detail_name","form":""},"1":{"name":"hook_display_product_additional_info","form":""},"2":{"name":"hook_display_leo_product_review_extra","form":""},"3":{"name":"product_price","form":""},"4":{"name":"product_description_short","form":""},"5":{"name":"product_customization","form":""},"6":{"name":"product_actions_form","form":""},"7":{"name":"hook_display_reassurance","form":""},"8":{"name":"product_more_info_tab","form":""}}},"2":{"form":{"class":"","xl":"12","lg":"12","md":"12","sm":"12","xs":"12","sp":"12"},"element":"column","sub":{"0":{"name":"product_accessories","form":""},"1":{"name":"hook_display_footer_product","form":""}}}}}},"class":"product-image-thumbs no-thumbs"}';
                        break;
                    case 'product_image_gallery':
                        $this->object->url_img_preview = 'https://i.pinimg.com/originals/b1/a8/b9/b1a8b9381d8d3e3c4d13dfe24231581f.jpg';
                        $this->object->params = '{"gridLeft":{"0":{"name":"functional_buttons","form":"","columns":{"0":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_image_show_all","form":{"templatezoomtype":"in","zoomposition":"right","zoomwindowwidth":"400","zoomwindowheight":"400","use_leo_gallery":"0"}}}},"1":{"form":{"md":6,"lg":6,"xl":6,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_detail_name","form":""},"1":{"name":"hook_display_product_additional_info","form":""},"2":{"name":"hook_display_leo_product_review_extra","form":""},"3":{"name":"product_price","form":""},"4":{"name":"product_description_short","form":""},"5":{"name":"product_customization","form":""},"6":{"name":"product_actions_form","form":""},"7":{"name":"hook_display_reassurance","form":""}}},"2":{"form":{"md":12,"lg":12,"xl":12,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_more_info_tab","form":""},"1":{"name":"product_accessories","form":""},"2":{"name":"hook_display_footer_product","form":""}}}}}},"class":"product-image-gallery"}';
                        break;
                    case 'product_image_no_thumbs_center':
                        $this->object->url_img_preview = 'https://i.pinimg.com/originals/38/99/1a/38991a8c1582669d29abe889bc0d5f52.jpg';
                        $this->object->params = '{"gridLeft":{"0":{"name":"functional_buttons","form":"","columns":{"0":{"form":{"class":"","xl":"4","lg":"4","md":"12","sm":"12","xs":"12","sp":"12"},"element":"column","sub":{"0":{"name":"product_detail_name","form":""},"1":{"name":"hook_display_product_additional_info","form":""},"2":{"name":"hook_display_leo_product_review_extra","form":""},"3":{"name":"product_price","form":""},"4":{"name":"product_description_short","form":""},"5":{"name":"hook_display_reassurance","form":""}}},"1":{"form":{"class":"","xl":"5","lg":"5","md":"12","sm":"12","xs":"12","sp":"12"},"element":"column","sub":{"0":{"name":"product_image_with_thumb","form":{"templateview":"none","numberimage":"5","numberimage1200":"5","numberimage992":"4","numberimage768":"3","numberimage576":"3","numberimage480":"2","numberimage360":"2","templatemodal":"1","templatethumb":"1","templatezoomtype":"in","zoomposition":"right","zoomwindowwidth":"400","zoomwindowheight":"400","use_leo_gallery":"0"}}}},"2":{"form":{"class":"","xl":"3","lg":"3","md":"12","sm":"12","xs":"12","sp":"12"},"element":"column","sub":{"0":{"name":"product_customization","form":""},"1":{"name":"product_actions_form","form":""}}},"3":{"form":{"md":12,"lg":12,"xl":12,"sm":12,"xs":12,"sp":12},"element":"column","sub":{"0":{"name":"product_more_info_tab","form":""},"1":{"name":"product_accessories","form":""},"2":{"name":"hook_display_footer_product","form":""}}}}}},"class":"product-image-thumbs no-thumbs"}';
                        break;
                    default:
                        break;
                }
            }
            
            $params = json_decode($this->object->params, true);
            if (isset($params['gridLeft']) && $params['gridLeft']) {
                foreach ($params['gridLeft'] as $key => $value) {
                    $params['gridLeft'][$key]['dataForm'] = (!isset($value['form']))?'':json_encode($value['form']);

                    if (isset($element_by_name[$value['name']])) {
                        $params['gridLeft'][$key]['config'] = $element_by_name[$value['name']];
                    }
                    if ($value['name'] == "functional_buttons") {
                        foreach ($value['columns'] as $k => $v) {
                            $params['gridLeft'][$key]['columns'][$k]['dataForm'] = (!isset($v['form']))?'':json_encode($v['form']);
                            foreach ($v['sub'] as $ke => $ve) {
                                $params['gridLeft'][$key]['columns'][$k]['sub'][$ke]['dataForm'] = (!isset($ve['form']))?'':json_encode($ve['form']);
                                if (isset($element_by_name[$ve['name']])) {
                                    $params['gridLeft'][$key]['columns'][$k]['sub'][$ke]['config'] = $element_by_name[$ve['name']];
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $block_list = array(
            'gridLeft' => array('title' => 'Product-Layout', 'class' => 'left-block'),
        );

        $file_link = '';
        $file_content = '';
        
        if(file_exists($this->folder_save.$this->object->plist_key.'.tpl')) {
            $file_link = $this->folder_save.$this->object->plist_key.'.tpl';
            $file_content = Tools::file_get_contents($this->folder_save.$this->object->plist_key.'.tpl');
        }
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Leo Elements Products Manage'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
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
                    'title' => $this->l('Product Detail File Content. Click to see file content.'),
                    'type' => 'leoelement_file',
                    'name' => 'file_link',
                    'file_link' => $file_link,
                    'file_content' => $file_content,
                    'width' => 140
                ),
                array(
                    'label' => $this->l('Class'),
                    'type' => 'text',
                    'name' => 'class_detail',
                    'width' => 140
                ),
                array(
                    'label' => $this->l('Url Image Preview'),
                    'type' => 'text',
                    'name' => 'url_img_preview',
                    'desc' => $this->l('Only for developers'),
                    'width' => 140
                ),
                array(
                    'type' => 'leoelement_Grid',
                    'name' => 'leoelement_Grid',
                    'params' => $params,
                    'blockList' => $block_list,
                    'elements' => $config_file,
                    'demodetaillink' => 'index.php?controller=AdminLeoElementsProducts'.'&amp;token='.Tools::getAdminTokenLite('AdminLeoElementsProducts').'&amp;addleoelements_products',
                    'element_by_name' => $element_by_name,
                    'widthList' => LeoECSetting::returnWidthList(),
                    'columnGrids' => LeoECSetting::getColumnGrid(),
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
                'action' => Context::getContext()->link->getAdminLink('AdminLeoElementsProductsController'),
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
            $file_value['plist_key'] = 'detail'.$num;
            $file_value['name'] = $file_value['plist_key'];
            if (Tools::getIsset('sampledetail')) {
                $file_value['class_detail'] = Tools::getValue('sampledetail');
            } else {
                $file_value['class_detail'] = 'detail-'.$num;                
            }
        }
        return $file_value;
    }

    public function processAdd()
    {
        if ($obj = parent::processAdd()) {
            $this->saveTplFile($obj->plist_key, $obj->params);
        }
    }

    public function processUpdate()
    {
        if ($obj = parent::processUpdate()) {
            $this->saveTplFile($obj->plist_key, $obj->params);
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
            if($params['productdetail_layout'] == $object->plist_key || $params['productdetail_layout_mobile'] == $object->plist_key || $params['productdetail_layout_tablet'] == $object->plist_key) {
                $exist = $profile['name'];
                break;
            }
        }

        if($exist) {
            $this->errors[] = sprintf($this->l('Can not delete this Product Detail Layout. Because this layout used in %s'), $exist);
        } else {
            Tools::deleteFile($this->folder_save.$object->plist_key.'.tpl');
            parent::processDelete();
        }
    }

    //save file
    public function saveTplFile($plist_key, $params = '')
    {
        $data_form = str_replace($this->str_search, $this->str_relace, $params);
        $data_form = json_decode($data_form, true);
        $grid_left = $data_form['gridLeft'];
        $tpl_grid = $this->returnFileContent('header_product');
        $tpl_grid = str_replace('class="product-detail', 'class="product-detail '.Tools::getValue('class_detail', '').' '.Tools::getValue('main_class', ''), $tpl_grid);
        $tpl_grid .= $this->convertObjectToTpl($grid_left);
        $tpl_grid .= $this->returnFileContent('footer_product');
        
        $tpl_grid = preg_replace('/\{\*[\s\S]*?\*\}/', '', $tpl_grid);

        $folder = $this->folder_save;
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        $file = $plist_key.'.tpl';
        //fixbug image cover prestashop < 1.7.7.0
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            $tpl_grid = str_replace('$product.default_image', '$product.cover', $tpl_grid);
        }
        //$tpl_grid = preg_replace('/\{\*[\s\S]*?\*\}/', '', $tpl_grid);
        //$tpl_grid = str_replace(" mod='leoelements'", '', $tpl_grid);
        LeoECSetting::writeFile($folder, $file, leoECHelper::getLicenceTPL().$tpl_grid);
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
    
    /**
     * SHOW LINK DUPLICATE FOR EACH ROW
     */
    public function displayDuplicateLink($token = null, $id = null, $name = null)
    {
        $controller = 'AdminLeoElementsProducts';
        $token = Tools::getAdminTokenLite($controller);
        $html = '<a href="#" title="Duplicate" onclick="confirm_link(\'\', \'Duplicate Product Details ID '.$id.'. If you wish to proceed, click &quot;Yes&quot;. If not, click &quot;No&quot;.\', \'Yes\', \'No\', \'index.php?controller='.$controller.'&amp;id_leoelements_products='.$id.'&amp;duplicateleoelements_products&amp;token='.$token.'\', \'#\')">
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
            } elseif (Tools::getIsset('saveELement') && Tools::getValue('saveELement')) {
                if (!$this->access('edit')) {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            } elseif ($this->can_import && Tools::getIsset('import' . $this->table)) {
                if (!$this->access('edit')) {
                    $this->errors[] = $this->trans('You do not have permission to import data.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
