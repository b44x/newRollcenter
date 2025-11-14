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
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsFont.php');
require_once(_PS_MODULE_DIR_.'leoelements/libs/google_fonts.php');

/**
 *
 * NOT extends ModuleAdminControllerCore, because override tpl : ROOT/modules/leoelements/views/templates/admin/leo_elements_font/helpers/form/form.tpl
 */
class AdminLeoElementsFontController extends ModuleAdminController
{
    public $module_name = 'leoelements';
    public $img_path;
    public $folder_name;
    public $module_path;
    public $tpl_path;
    public $theme_dir;
    
    /**
     * @var Array $overrideHooks
     */
    protected $themeName;
    
    /**
     * @var Array $overrideHooks
     */
    protected $themePath = '';
    


    public function __construct()
    {
        $this->bootstrap = true;
        $this->show_toolbar = true;
        $this->table = 'leoelements_fonts';
        $this->className = 'LeoElementsFont';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->module_name = 'leoelements';
        $this->theme_name = _THEME_NAME_;
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
        $this->tpl_path = _PS_ROOT_DIR_.'/modules/'.$this->module_name.'/views/templates/admin';
        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->multishop_context = false;
        $this->theme_dir = leoECHelper::getConfigDir('_PS_THEME_DIR_');

        $this->order_by = 'id_leoelements_fonts';
        $this->order_way = 'ASC';

        $id_shop = (int)$this->context->shop->id;
        $this->_select .= ' a.name, a.type, a.font_family, a.file';

        $this->fields_list = array(
            'id_leoelements_fonts' => array(
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
            'type' => array(
                'title' => $this->l('Type'),
                'width' => 140,
                'type' => 'type_text',
                'filter_key' => 'a!type'
            ),
            'font_family' => array(
                'title' => $this->l('Font Family'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!font_family'
            ),
            'file' => array(
                'title' => $this->l('File'),
                'width' => 140,
                'type' => 'filename',
                'filter_key' => 'a!file'
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            ),
        );

    }
    
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJs(leoECHelper::getJsAdminDir().'admin/font_config.js');
        $this->context->controller->addCss(leoECHelper::getCssAdminDir().'admin/font_config.css', 'all');
        
        Media::addJsDef(array(
            'ap_controller'  => 'AdminLeoElementsFontController',
        ));
        
    }

    public function renderForm()
    {
        $this->multiple_fieldsets = true;
        $this->fields_form[0]['form'] = array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => leoECHelper::getConfigName('name'),
                    'required' => 1
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => leoECHelper::getConfigName('type'),
                    'class' => 'form-group-type',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 1,
                                'name' => $this->l('Upload')
                            ),
                            array(
                                'id' => 2,
                                'name' => $this->l('Google')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Font Family'),
                    'name' => leoECHelper::getConfigName('font_family'),
                    'desc' => $this->l('Font Family: Material Icons,..'),
                    'class' => 'form-group-upload',
                    'required' => 1
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Style'),
                    'name' => leoECHelper::getConfigName('font_style'),
                    'desc' => $this->l('Default: normal'),
                    'class' => 'form-group-upload',
                    'default' => 'normal',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'normal',
                                'name' => $this->l('Normal'),
                            ),
                            array(
                                'id' => 'italic',
                                'name' => $this->l('Italic'),
                            ),
                            array(
                                'id' => 'oblique',
                                'name' => $this->l('Oblique'),
                            ),
                            array(
                                'id' => 'initial',
                                'name' => $this->l('Initial'),
                            ),
                            array(
                                'id' => 'inherit',
                                'name' => $this->l('Inherit'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Font Weight'),
                    'name' => leoECHelper::getConfigName('font_weight'),
                    'class' => 'form-group-upload',
                    'desc' => $this->l('Default: 400 (Normal)'),
                    'default' => LeoElementsProfilesModel::getFontWeight('default'),
                    'options' => array(
                        'query' => LeoElementsProfilesModel::getFontWeight(),
                        'id' => 'id',
                        'name' => 'name'),
                ),
                array(
                    'type' => 'font_setup',
                    'label' => $this->l('Upload Font Face'),
                    'name' => leoECHelper::getConfigName('font_face'),
                    'class' => 'form-group-upload',
                    'required' => 1
                ),
                array(
                    'type' => 'font_setup_gg',
                    'label' => $this->l('Google Font'),
                    'name' => leoECHelper::getConfigName('font_face_gg'),
                    'class' => 'form-group-google',
                    'list_google_font' => array_keys(GoogleFont::getAllGoogleFonts()),
                    'required' => 1
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
        
        return parent::renderForm();
    }

    public function renderList()
    {
        $this->toolbar_title = $this->l('Fonts Management');
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new')
        );

        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        // If list has 'active' field, we automatically create bulk action
        if (isset($this->fields_list) && is_array($this->fields_list) && array_key_exists('active', $this->fields_list)
            && !empty($this->fields_list['active'])) {
            if (!is_array($this->bulk_actions)) {
                $this->bulk_actions = [];
            }

            $this->bulk_actions = array_merge([
                'enableSelection' => [
                    'text' => $this->trans('Enable selection'),
                    'icon' => 'icon-power-off text-success',
                ],
                'disableSelection' => [
                    'text' => $this->trans('Disable selection'),
                    'icon' => 'icon-power-off text-danger',
                ],
                'divider' => [
                    'text' => 'divider',
                ],
            ], $this->bulk_actions);
        }

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->trans('Bad SQL query') . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->_default_pagination = $this->_default_pagination;
        $helper->_pagination = $this->_pagination;
        $helper->tpl_vars = $this->getTemplateListVars();
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }

        $helper->is_cms = $this->is_cms;
        $helper->sql = $this->_listsql;
        foreach ($this->_list as &$row) {
            $row['files'] = explode(',', $row['file']);
        }
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    public function processAdd()
    {
        $this->saveDataFont();
    }

    public function processUpdate()
    {
        if (Tools::getValue('delete_fface')) {
            self::deleteFile($this->theme_dir, explode(',', Tools::getValue('delete_fface')));
        }
        
        $this->saveDataFont();
    }

    public function processDelete()
    {
        if (Tools::getValue('id_leoelements_fonts')) {
            $font = new LeoElementsFont(Tools::getValue('id_leoelements_fonts'));
            self::deleteFile($this->theme_dir, array());
            $font->delete();
            
            $this->confirmations[] = $this->l('Font have been deleted successfully.');
        } else {
            $this->errors[] = $this->l('An error occurred while deleting.');
        }
    }
    
    public function getFieldsValue($obj)
    {
        unset($obj);
        $fields_values = array();
        $font = new LeoElementsFont(Tools::getValue('id_leoelements_fonts'));
        $fields_values[leoECHelper::getConfigName('name')] = $font->name;
        $fields_values[leoECHelper::getConfigName('type')] = $font->type;
        $fields_values[leoECHelper::getConfigName('font_family')] = $font->font_family;
        $fields_values[leoECHelper::getConfigName('font_style')] = $font->font_style ? $font->font_style : 'normal';
        $fields_values[leoECHelper::getConfigName('font_weight')] = $font->font_weight ? $font->font_weight : '400';
        
        if (!$font->type || $font->type == 1) {
            $fields_values['font_face'] = $font->file;
            $fields_values['gfont_subset'] = '';
            $fields_values['gfont'] = '';
        } else {
            $google_font  = explode('&', $font->file);
            $fields_values['gfont'] = $font->font_family;
            $fields_values['gfont_subset'] = '';
            if (isset($google_font[1])) {
                $fields_values['gfont_subset'] = $google_font[1];
            }
        }
        // Font setup : list fonts in google
        $fields_values['gfont_api'] = json_encode(GoogleFont::getAllGoogleFonts());
        return $fields_values;
    }

    public static function getDataFont($theme_dir)
    {
        $id_leoelements_fonts = Tools::getValue('id_leoelements_fonts') ? Tools::getValue('id_leoelements_fonts') : LeoElementsFont::getMaxId();
        $font_dir = $theme_dir.'assets/fonts';
        $type = Tools::getValue(leoECHelper::getConfigName('type'));

        $font = new LeoElementsFont($id_leoelements_fonts);
        $font->name = Tools::getValue(leoECHelper::getConfigName('name'));
        $font->font_style = Tools::getValue(leoECHelper::getConfigName('font_style')) ? Tools::getValue(leoECHelper::getConfigName('font_style')) : 'normal';
        $font->font_weight = Tools::getValue(leoECHelper::getConfigName('font_weight')) ? Tools::getValue(leoECHelper::getConfigName('font_weight')) : '400';
        $font->type = $type;

        // validate
        if (($type == 1 && !Tools::getValue('fface_filename')) || ($type == 2 && !Tools::getValue('gfont'))) {
            $font->file = '';
            return $font;
        }
        if ($type == 1) {
            // upload files
            $fontFileName = explode(',', implode(',', Tools::getValue('fface_filename')));
            if (!is_dir($font_dir)) {
                mkdir($font_dir, 0777, true);
            }
            if (isset($_FILES['file_fface'])) {
                $files = $_FILES['file_fface'];
                $fileNameUploaded = array();

                foreach ($files['name'] as $key => &$filename) {
                    if ($filename && !move_uploaded_file($files['tmp_name'][$key], $font_dir.'/'.$filename)) {
//                        $this->errors[] = $this->l('Upload max file size:').ini_get('upload_max_filesize');
                        if (is_int(array_search($filename, $fontFileName))) {
                            unset($fontFileName[array_search($filename, $fontFileName)]); // remove file name can't upload
                        }
                    }
                }
            }

            $font->file = implode(',', $fontFileName);
            $font->font_family = Tools::getValue(leoECHelper::getConfigName('font_family'));
        } else {
            $gfont = Tools::getValue('gfont');
            $font->font_family = $gfont;
            $gfont_name = str_replace(' ', '+', $gfont);

            $font->file = $gfont_name;

        }
        
        return $font;
    }

    public function saveDataFont()
    {
        $font = self::getDataFont($this->theme_dir);

        if (!$font->name || !$font->font_family) {
            $this->errors[] = $this->l('Please enter the required fields.');
        } else if (!$font->file) {
            $this->errors[] = $this->l('"Font Face" field cannot be empty, please upload font or choose a google font.');
        } elseif ($font->save()) {
            $this->confirmations[] = $this->l('Font have been updated successfully.');
            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                $this->redirect_after = Context::getContext()->link->getAdminLink('AdminLeoElementsFont');
                $this->redirect_after .= '&updateleoelements_fonts=&id_leoelements_fonts='.($font->id);
                $this->redirect();
            }
        } else {
            $this->errors[] = $this->l('An error occurred while saving.');
        }
    }

    public static function deleteFile($theme_dir, $files = array())
    {
        $font_dir = $theme_dir.'assets/fonts';
        if ($files) {
            foreach ($files as $file) {
                if (file_exists($font_dir.'/'.$file)) {
                    unlink($font_dir.'/'.$file);
                }
            }
        } else {
            $font = new LeoElementsFont(Tools::getValue('id_leoelements_fonts'));
            if ($font->type == 1) {
                $files = explode(',', $font->file);
                foreach ($files as $file) {
                    if ($file && file_exists($font_dir.'/'.$file)) {
                        unlink($font_dir.'/'.$file);
                    }
                }
            }
            
        }
    }
}
