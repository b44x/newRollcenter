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

use LeoElements\Leo_Helper;
use LeoElements\Plugin;

if (!defined('_PS_VERSION_')) {
	exit;
}

require_once(_PS_MODULE_DIR_.'leoelements/leoECHelper.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsContentsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/src/Leo_Helper.php');
require_once(_PS_MODULE_DIR_.'leoelements/src/Leo_Helper.php');
require_once(_PS_MODULE_DIR_.'leoelements/includes/plugin.php');

class AdminLeoElementsCreatorController extends ModuleAdminController
{
    public $name = 'AdminLeoElementsCreatorController';

    public $display_header = false;

    public $content_only = true;

    public function initContent()
    {
        if ( ( !Tools::getValue('id_post') && !Tools::getValue('key_related') ) || !Tools::getValue('post_type') ) {
            $configure = $this->context->link->getAdminLink('AdminModules', false);
            $configure .= '&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name.'&token='.Tools::getAdminTokenLite('AdminModules');
            Tools::redirectAdmin( $configure );
        }
        
        $GLOBALS['gb_leoelements'] = array(
            'url' => array(
                'link_leoslideshow' => Context::getContext()->link->getAdminLink('AdminLeoSlideshowMenuModule'),
                'link_leobootstrapmenu' => Context::getContext()->link->getAdminLink('AdminLeoBootstrapMenuModule'),
                'link_leoblog' => Context::getContext()->link->getAdminLink('AdminLeoblogCategories'),
            ),
            '_PS_ADMIN_DIR_' => _PS_ADMIN_DIR_,
        );
        Configuration::updateValue('GBLEOELEMENTS', json_encode($GLOBALS['gb_leoelements']));
        
        if( Leo_Helper::set_global_var() ){
                // leoelements/core/editor/editor.php
                Plugin::instance()->editor->init();
        }
        die();
    }
    
    public function initProcess() {}

    public function initBreadcrumbs( $tab_id = null, $tabs = null ) {}

    public function initModal() {}

    public function initToolbarFlags() {}

    public function initNotifications() {}
	
}
