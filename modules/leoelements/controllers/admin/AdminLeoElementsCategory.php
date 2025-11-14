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

require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsCategoryModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProductListModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/leoECHelper.php');

class AdminLeoElementsCategoryController extends ModuleAdminControllerCore
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
//    public $module_path_resource;
    public $str_search = array();
    public $str_relace = array();
    public $theme_dir;
    public $params_field = array();

   public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'leoelements_category';
        $this->className = 'LeoElementsCategoryModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;
        $this->can_import = true;
        $this->context = Context::getContext();
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'leoelements_category_shop` ps ON (ps.`id_leoelements_category` = a.`id_leoelements_category`)';
        $this->_select .= ' ps.active as active, ps.active_mobile as active_mobile, ps.active_tablet as active_tablet';

        $this->order_by = 'id_leoelements_category';
        $this->order_way = 'DESC';
        parent::__construct();
        $this->fields_list = array(
            'id_leoelements_category' => array(
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
            'clist_key' => array(
                'title' => $this->l('Category Key'),
                'filter_key' => 'a!clist_key',
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
        $this->theme_dir = leoECHelper::getConfigDir('_PS_THEME_DIR_');
        $this->theme_dir = _PS_THEME_DIR_;

        $this->_where = ' AND ps.id_shop='.(int)$this->context->shop->id;
        $this->theme_name = _THEME_NAME_;
        $this->profile_css_folder = $this->theme_dir.'modules/'.$this->module_name.'/';
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
        $this->str_search = array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APOCBRACKET_', '_APCCBRACKET_', '_APDOLA_');
        $this->str_relace = array('&', '\"', '\'', '\t', '\r', '\n', '[', ']', '{', '}', '$');
        $this->params_field = leoECHelper::defaultConfig()['category'];
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
    
    public function processExport($text_delimiter = '"')
    {
        $record_id = Tools::getValue('record_id');
        $file_name = 'ap_category_detail_all.xml';
        # VALIDATE MODULE
        unset($text_delimiter);
        
        if ($record_id) {
            $record_id_str = implode(", ", $record_id);
            $this->_where = ' AND a.id_leoelements_category IN ( '.pSQL($record_id_str).' )';
            $file_name = 'ap_category_detail_all.xml';
        }
        
        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        $data = $this->_list;
        $this->file_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->file_content .= '<data>' . "\n";
        $this->file_content .= '<category_details>' . "\n";
        
        if ($data) {
            foreach ($data as $category_detail) {
                $this->file_content .= '<record>' . "\n";
                foreach ($category_detail as $key => $value) {
                    $this->file_content .= '    <'.$key.'>';
                    $this->file_content .= '<![CDATA['.$value.']]>';
                    $this->file_content .= '</'.$key.'>' . "\n";
                }
                $this->file_content .= '</record>' . "\n";
            }
        }
        $this->file_content .= '</category_details>' . "\n";
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
        
        if (isset($files_content->category_details) && $files_content->category_details) {
            foreach ($files_content->category_details->children() as $category_details) {
                if (!$override) {
                    $num = LeoECSetting::getRandomNumber();
                    $obj_model = new LeoElementsCategoryModel();
                    $obj_model->clist_key = 'category'.$num;
                    $obj_model->name = $category_details->name->__toString();
                    $obj_model->class = $category_details->class->__toString();
                    $obj_model->params = $category_details->params->__toString();
                    $obj_model->type = $category_details->type->__toString();
                    $obj_model->active = 0;
                    $obj_model->active_mobile = 0;
                    $obj_model->active_tablet = 0;
                    $obj_model->save();
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
        $this->redirect_after .= '&id_leoelements_category='.$object->id;
        $this->redirect();
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::getIsset('active_mobileleoelements_category') || Tools::getIsset('active_tableleoelements_category')) {
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $result = Tools::getIsset('active_mobileleoelements_category')?$object->toggleStatusMT('active_mobile'):$object->toggleStatusMT('active_tablet');
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

        if (Tools::getIsset('duplicateleoelements_category')) {
            $id = Tools::getValue('id_leoelements_category');
            $model = new LeoElementsCategoryModel($id);
            $duplicate_object = $model->duplicateObject();
            $duplicate_object->name = $this->l('Duplicate of').' '.$duplicate_object->name;
            $old_key = $duplicate_object->clist_key;
            $duplicate_object->clist_key = 'category'.LeoECSetting::getRandomNumber();
            $duplicate_object->params = $model->params;
            $duplicate_object->type = $model->type;
            $duplicate_object->add();
        }
        if (Tools::isSubmit('saveELement')) {
            $filecontent = Tools::getValue('filecontent');
            $fileName = Tools::getValue('fileName');
            leoECHelper::createDir(leoECHelper::getConfigDir('theme_category'));
            LeoECSetting::writeFile(leoECHelper::getConfigDir('theme_category'), $fileName.'.tpl', $filecontent);
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
        $tpl_dir = leoECHelper::getConfigDir('theme_category').$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = leoECHelper::getConfigDir('module_category').$pelement.'.tpl';
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
        $tpl_dir = leoECHelper::getConfigDir('theme_category').$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = leoECHelper::getConfigDir('module_category').$pelement.'.tpl';
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
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/category.js');
        $this->context->controller->addCss(leoECHelper::getCssAdminDir().'admin/form.css');
        $this->context->controller->addCss(leoECHelper::getCssAdminDir().'admin/style.css');
        
        if (is_dir(leoECHelper::getConfigDir('theme_category'))) {
            $source_file = Tools::scandir(leoECHelper::getConfigDir('theme_category'), 'tpl');
            $source_template_file = Tools::scandir(leoECHelper::getConfigDir('theme_category'), 'tpl');
            $source_file = array_merge($source_file, $source_template_file);
        }
        
        $params = array();

        $this->object->params = str_replace($this->str_search, $this->str_relace, $this->object->params);

        $plist = new LeoElementsProductListModel();
        $product_list = $plist->getAllProductListProfileByShop();
        $product_list_array = array(
            array('id' => '0', 'name' => $this->l('Default'))
        );
        foreach ($product_list as $pro) {
            $product_list_array[] = array('id' => $pro['plist_key'], 'name' => $pro['name']);
        }
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Leo Elements Category Manage'),
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
                    'label' => $this->l('Category Key'),
                    'name' => 'clist_key',
                    'readonly' => 'readonly',
                    'desc' => $this->l('Tpl File name'),
                ),
                array(
                    'label' => $this->l('Class'),
                    'type' => 'text',
                    'name' => 'class',
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
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('1. Filter').'</div>',
                ),
                array(
                    'label' => $this->l('Filter Possition'),
                    'type' => 'select',
                    'name' => 'filter_position',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Left')),
                            array('id' => '2', 'name' => $this->l('Top'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use button toggle'),
                    'name' => 'use_button_toggle',
                    'values' => LeoECSetting::returnYesNo(),
                ),
                array(
                    'type' => 'hidden',
                    'value' => 0,
                    'name' => 'check_filter',
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('2. Category Information Box').'</div>',
                ),
                array(
                    'label' => $this->l('Category Information Box Position'),
                    'type' => 'select',
                    'name' => 'category_position',
                    'desc' => $this->l('Category Name, Image, Description'),
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Top Of Product')),
                            array('id' => '2', 'name' => $this->l('Bottom of Product')),
                            array('id' => '0', 'name' => $this->l('No')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Category Image'),
                    'type' => 'select',
                    'name' => 'category_image',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Display as default')),
                            array('id' => '0', 'name' => $this->l('Disable')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Category Description'),
                    'type' => 'select',
                    'name' => 'category_des',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Display as default')),
                            array('id' => '2', 'name' => $this->l('Show with read more')),
                            array('id' => '0', 'name' => $this->l('Disable')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Category Description Length'),
                    'type' => 'text',
                    'name' => 'category_dleng',
                    'width' => 140
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('3. Sub Category').'</div>',
                ),
                array(
                    'label' => $this->l('SubCategory Position'),
                    'type' => 'select',
                    'name' => 'scategory_position',
                    'desc' => $this->l('Category Name, Image, Description'),
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Top Of Product')),
                            array('id' => '2', 'name' => $this->l('Bottom of Product')),
                            array('id' => '0', 'name' => $this->l('No')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('SubCategory Image'),
                    'type' => 'select',
                    'name' => 'scategory_image',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Display as default')),
                            array('id' => '0', 'name' => $this->l('Disable')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('SubCategory Description'),
                    'type' => 'select',
                    'name' => 'scategory_des',
                    'options' => array('query' => array(
                            array('id' => '1', 'name' => $this->l('Display as default')),
                            array('id' => '2', 'name' => $this->l('Show with read more')),
                            array('id' => '0', 'name' => $this->l('Disable')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('SubCategory Description Length'),
                    'type' => 'text',
                    'name' => 'scategory_dleng',
                    'width' => 140
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">'.$this->l('4. Product List').'</div>',
                ),
                array(
                    'label' => $this->l('Product List Desktop'),
                    'type' => 'select',
                    'name' => 'product_list',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Product List Mobile'),
                    'type' => 'select',
                    'name' => 'product_list_mobile',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'label' => $this->l('Product List Tablet'),
                    'type' => 'select',
                    'name' => 'product_list_tablet',
                    'options' => array('query' => $product_list_array,
                        'id' => 'id',
                        'name' => 'name'),
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

    /**
     * Read file css + js to form when add/edit
     */
    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);
        if($obj->id) {
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
        } else {
            $num = LeoECSetting::getRandomNumber();
            $file_value['clist_key'] = 'category'.$num;
            $file_value['name'] = $file_value['clist_key'];
            $file_value['class'] = 'category-'.$num;

            foreach ($this->params_field as $k=>$v) {
                $file_value[$k] = $v;
            }
        }
        
        return $file_value;
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
                'action' => Context::getContext()->link->getAdminLink('AdminLeoElementsCategoryController'),
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

    public function processAdd()
    {
        if ($obj = parent::processAdd()) {
            $this->processParams();
        }
    }

    public function processUpdate()
    {
        if ($obj = parent::processUpdate()) {
            $this->processParams();
        }
    }

    public function processDelete()
    {
        $object = $this->loadObject();
        // Tools::deleteFile(leoECHelper::getConfigDir('theme_category').$object->clist_key.'.tpl');
        parent::processDelete();
    }

    /**
     * Get fullwidth hook, save to params
     */
    public function processParams()
    {
        $params = json_decode($this->object->params);
        if ($params === null) {
            $params = new stdClass();
        }
        foreach ($this->params_field as $k=>$v) {
            if($k != "check_filter") {
                $params->{$k} = Tools::getValue($k);
            }
        }
        
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'leoelements_category` SET `params` = "'.pSQL(json_encode($params), true).'" WHERE `id_leoelements_category` = "'.$this->object->id.'"';
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
    
    /**
     * SHOW LINK DUPLICATE FOR EACH ROW
     */
    public function displayDuplicateLink($token = null, $id = null, $name = null)
    {
        $controller = 'AdminLeoElementsCategory';
        $token = Tools::getAdminTokenLite($controller);
        $html = '<a href="#" title="Duplicate" onclick="confirm_link(\'\', \'Duplicate Category Details ID '.$id.'. If you wish to proceed, click &quot;Yes&quot;. If not, click &quot;No&quot;.\', \'Yes\', \'No\', \'index.php?controller='.$controller.'&amp;id_leoelements_category='.$id.'&amp;duplicateleoelements_category&amp;token='.$token.'\', \'#\')">
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
