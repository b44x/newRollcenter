<?php
/**
* This module helps administrator to add popup ads on page of products,offers,promotions from back-office and showcase in front-office.
*
* NOTICE OF LICENSE
* 
* Each copy of the software must be used for only one production website, it may be used on additional
* test servers. You are not permitted to make copies of the software without first purchasing the
* appropriate additional licenses. This license does not grant any reseller privileges.
* 
* @author    Shahab
* @copyright 2007-2024 Shahab-FK Enterprises
* @license   Prestashop Commercial Module License
*/

if (!defined('_PS_VERSION_')) { exit; }

class AdminSfkpopupControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table     = 'sfkpopup';
        $this->className = 'Sfkpopup';
        $this->lang      = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->context = Context::getContext();
        if (!Tools::getValue('realedit'))
                $this->deleted = false;
        $this->bulk_actions = array(
                'delete' => array(
                        'text' => $this->l('Delete selected'),
                        'confirm' => $this->l('Delete selected items?')
                )
        );
        $this->field_image_settings = array('name' => 'sfk_image_name','dir' => 'st');
        $this->fields_list = array(
                'id_sfkpopup' => array(
                    'title' => $this->l('ID'),
                    'align' => 'left',
                    'width' => 'auto'
                ),
            'sfk_title' => array('title' => $this->l('Popup Title'),'filter_key' => 'sfk_title','align' => 'left','width' => 'auto'),
            'sfk_url' => array('title' => $this->l('Redirect URL'),'filter_key' => 'sfk_url','align' => 'left','width' => 'auto'),
            'sfk_dates' => array('title' => $this->l('Date'),'filter_key' => 'sfk_dates','align' => 'left','width' => 'auto'),
        );
        
        if(!$this->ajax && !isset($this->display)){
            $this->context->smarty->assign(array(
                'modules_dir' => _MODULE_DIR_
            ));
            $this->content .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'sfkpopup/views/templates/admin/sfkpopup.tpl');
        }
        
        parent::__construct();
    }
    public function renderForm()
    {
        //$languages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'lang WHERE active=1 ');

        if (_PS_VERSION_ < '1.6') {
            $type = 'radio' ;
        } else {
            $type = 'switch' ;
        }
        
        $this->fields_form = array(
            'legend' => array(
                    'title' => $this->l('Home Page Popup Management'),
                    'image' => '../img/admin/tab-sfkpopup.gif'
            ),
            'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title:'),
                        'name' => 'sfk_title',
                        'size' => 33,
                        'desc' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"ï¿½{}_$%:',
                        'required' => true
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Upload Image:'),
                        'name' => 'sfk_image_name',
                        'display_image' => true,
                        'required' => true,
                        'desc' => $this->l('Upload a popup ads image from your computer. If no image uploaded the  old image will be used when doing edit record.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Redirect URL:'),
                        'name' => 'sfk_url',
                        'size' => 33,
                        'desc' => $this->l('Kindly enter redirect url. Example => https://addons.prestashop.com/en/2_community-developer?contributor=301729'),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Image Height:'),
                        'name' => 'sfk_height',
                        'size' => 33,
                        'desc' => $this->l('Only integer values allowed: Example 250'),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Image Width:'),
                        'name' => 'sfk_width',
                        'size' => 33,
                        'desc' => $this->l('Only integer values allowed: Example 500'),
                        'required' => true
                    ),
                    
                    array(
                       'type' => "$type",
                        'label' => $this->l('Active:'),
                        'name' => 'sfk_status',
                        'is_bool' => true,
                        'required' => true,
                        'values' => array(
                            array(
                                'id' => 'sfk_status_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'sfk_status_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'required' => true,
                        'desc' => $this->l('Active or Inactive record status.')
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Date'),
                        'name' => 'sfk_dates',
                        'size' => 20,
                        'search' => false,
                        'desc' => $this->l('The date record added or updated.')
                    ),
            ),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default'
            )
        );

       
        return parent::renderForm();
    }
    
    public function postProcess()
    {
        if(!empty($_FILES['sfk_image_name']['name']))
        {
            $file_ext = NULL;
            $image_extensions = array();
            $extension = NULL;
            $value = NULL;

            $image_extensions = array('jpg','jpeg','png','gif','bmp','tiff');
            $file_ext = Tools::strtolower($_FILES['sfk_image_name']['name']);
            $value = explode(".", $file_ext);
            $extension =  Tools::strtolower(array_pop($value));

            if(!in_array($extension,$image_extensions))
            {
                $this->errors[] = $this->l('An error occurred while uploading the image.Only jpg,jpeg,png,gif,bmp and tiff allowed.');
                return;
            }
	}
        parent::postProcess();
    }
    
    protected function postImage($id)
    {
        if (isset($this->field_image_settings['name']) && isset($this->field_image_settings['dir']))
                return $this->uploadImage($id, $this->field_image_settings['name'], $this->field_image_settings['dir'].'/');
        elseif (!empty($this->field_image_settings))
                foreach ($this->field_image_settings as $image)
                        if (isset($image['name']) && isset($image['dir']))
                                $this->uploadImage($id, $image['name'], $image['dir'].'/');
        return !count($this->errors) ? true : false;
    }
    protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
        {
            // Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject()))
                    $object->deleteImage();
            else
                    return false;
            // Check image validity
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size)))
                    $this->errors[] = $error;
            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name)
                    return false;
            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name))
                    return false;
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name))
                    $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. 
                    Please increase your memory_limit value via your server\'s configuration settings. ');
            
            // Copy new image
            $file_ext=Tools::strtolower(end(explode('.',$_FILES['sfk_image_name']['name'])));
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.'sfk_'.$id.'.'.$file_ext,(int)$width,(int)$height, ($ext ? $ext : $file_ext)))
            {
               $this->errors[] = Tools::displayError('An error occurred while uploading the image.');
            }   

            if (count($this->errors))
                    return false;
            
        if ($this->afterImageUpload())
        {
            $image_name = 'sfk_'.$id.'.'.$file_ext;
            $get_url     = Db::getInstance()->ExecuteS('SELECT domain,physical_uri FROM '._DB_PREFIX_.'shop_url ');
            $protocol = (isset($_SERVER['HTTPS']) ? "https" : "http") ;
            $image_url = "$protocol://".$get_url[0]['domain'].'/'.$get_url[0]['physical_uri']."img/st/$image_name";
            $sql_one = 'UPDATE `'._DB_PREFIX_.'sfkpopup` SET sfk_image_name="'.pSQL($image_url).'" WHERE id_sfkpopup='.pSQL($id).' ';
            Db::getInstance()->Execute($sql_one);
            unlink($tmp_name);
            return true;
        }
            return false;
        }
        return true;
    }
	
    /**
    * Surcharge de la fonction de traduction sur PS 1.7 et supérieur.
    * La fonction globale ne fonctionne pas
    * @param type $string
    * @param type $class
    * @param type $addslashes
    * @param type $htmlentities
    * @return type
    */
    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ( _PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }
}
