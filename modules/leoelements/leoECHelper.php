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

class leoECHelper
{

    public static function getLicenceTPL()
    {
        return Tools::file_get_contents( _PS_MODULE_DIR_.'leoelements/views/templates/admin/licence_tpl.txt');
    }
    
    /**
     * generate array to use in create helper form
     */
    public static function getArrayOptions($ids = array(), $names = array(), $val = 1)
    {
        $res = array();
        foreach ($names as $key => $value) {
            // module validate
            unset($value);

            $res[] = array(
                'id' => $ids[$key],
                'name' => $names[$key],
                'val' => $val,
            );
        }
        return $res;
    }

    /**
     * Check is Release or Developing
     * Release      : load css in themes/THEME_NAME/modules/MODULE_NAME/ folder
     * Developing   : load css in themes/THEME_NAME/assets/css/ folder
     */
    public static function isRelease()
    {
        if (defined('_LEO_MODE_DEV_') && _LEO_MODE_DEV_ === true) {
            # CASE DEV
            return false;
        }
        
        # Release
        return true;
    }

    public static function defaultConfig() {
        $category = array('filter_position' => '1', 'use_button_toggle' => 0, 'category_position' => '1', 'category_image' => '1', 'category_des' => '1', 'category_dleng' => 140, 'scategory_position' => 1, 'scategory_image' => 1, 'scategory_des' => 1, 'scategory_dleng' => 140, 'product_list' => 0, 'product_list_mobile' => 0, 'product_list_tablet' => 0);
        $product_list = array('listing_product_mode' => 'grid', 'listing_product_column_module' => 3, 'listing_product_column' => 3, 'listing_product_largedevice' => 3, 'listing_product_tablet' => 2, 'listing_product_extrasmalldevice' => 2, 'listing_product_mobile' => 1 ,'top_total' => 1, 'top_sortby' => 1, 'top_grid'=>'1', 'pg_count' => 1, 'pg_type' => 1, 'plist_load_more_product_img' => 0, 'plist_load_more_product_img_option' => 1, 'plist_load_multi_product_img' => 0, 'plist_load_cdown' => 0, 'lmobile_swipe' => 0, 'class' => '');
        return array('category' => $category, 'product_list' => $product_list);
    }

    public static function createDir($path = '')
    {
        if (!file_exists($path))
        {
            if (!mkdir($path, 0755, true))
            {
                die("Please create folder ".$path." and set permission 755");
            }
        }
    }

    public static function getConfigDir($key = '_PS_THEME_DIR_', $value = '')
    {
        static $data;
        if (!$data )
        {
            $folder_theme = _PS_ALL_THEMES_DIR_._THEME_NAME_.'/';
            $data = array(
                'module_img_admin' => _PS_ROOT_DIR_.'/modules/leoelements/img/admin/',
                'module_details' => _PS_ROOT_DIR_.'/modules/leoelements/views/templates/front/products/',
                'module_profiles' => _PS_ROOT_DIR_.'/modules/leoelements/views/templates/front/profiles/',
                
                'theme_ap_image' => $folder_theme.'assets/img/modules/leoelements/images/',          // apPageHelper::getImgThemeDir()
                'theme_ap_icon' => $folder_theme.'assets/img/modules/leoelements/icon/',             // apPageHelper::getImgThemeDir('icon')
                'theme_profile_logo' => $folder_theme.'profiles/images/',
                'theme_profile_js' => $folder_theme.'modules/leoelements/js/profiles/',
                'theme_profile_css' => $folder_theme.'modules/leoelements/css/profiles/',
                'theme_position_js' => $folder_theme.'modules/leoelements/js/positions/',
                'theme_position_css' => $folder_theme.'modules/leoelements/css/positions/',
                'theme_export_profile' => $folder_theme.'profiles_export/',
                'theme_download_profile' => $folder_theme.'profiles_download/',
                'theme_image_leoelements' => $folder_theme.'assets/img/modules/leoelements/',
                'theme_image_leoslideshow' => $folder_theme.'assets/img/modules/leoslideshow/',
                'theme_image_leoblog' => $folder_theme.'assets/img/modules/leoblog/',
                'theme_image_leobootstrapmenu' => $folder_theme.'assets/img/modules/leobootstrapmenu/',
                'theme_details' => $folder_theme.'templates/catalog/_partials/miniatures/',
                'theme_profiles' => $folder_theme.'profiles/',
                '_PS_THEME_DIR_' => $folder_theme,
            );
            if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
                $data['theme_profiles'] = $folder_theme.'modules/leoelements/views/templates/front/profiles/';
            }
        }
        
        if ($value && !array_key_exists($key.$value, $data)) {
            $temp = array(
                'theme_pfdl_ap' => $folder_theme.'profiles_download/'.'_TUANVU_'.'/leoelements/',
                'theme_pfdl_ap_image' => $folder_theme.'profiles_download/'.'_TUANVU_'.'/leoelements/images/',       // apPageHelper::getConfigDir('theme_export_profile') . $this->profile_key . '/leoelements/images/';
                'theme_pfdl_ap_icon' => $folder_theme.'profiles_download/'.'_TUANVU_'.'/leoelements/icon/',          // apPageHelper::getConfigDir('theme_export_profile') . $this->profile_key . '/leoelements/icon/';
            );
            if (!isset($temp[$key])) {
                $temp[$key] = '';
            }
            $data[$key.$value] = str_replace('_TUANVU_', $value, $temp[$key]);
        }
        
        if (isset($data[$key.$value])) {
            return $data[$key.$value];
        } else {
            return '';
        }
    }

    public static function getUriFromPath($fullPath)
    {
        $uri = str_replace(
            _PS_ROOT_DIR_,
            rtrim(__PS_BASE_URI__, '/'),
            $fullPath
        );

        return str_replace(DIRECTORY_SEPARATOR, '/', $uri);
    }

    
    public static function getSkins()
    {
        $folders = array();
        if (!leoECHelper::isRelease()) {
            $folders[] = leoECHelper::getConfigDir('_PS_THEME_DIR_').'assets/css/'.leoECHelper::getCssDir().'skins/*';
        }
        $folders[] = leoECHelper::getConfigDir('_PS_THEME_DIR_').leoECHelper::getCssDir().'skins/*';
        $output = array();
        foreach ($folders as $folder) {
            $dirs = glob($folder, GLOB_ONLYDIR);
            $output = array();
            if ($dirs) {
                $i = 0;
                foreach ($dirs as $dir) {
                    $output[$i]['id'] = basename($dir);
                    $output[$i]['name'] = Tools::ucfirst(basename($dir));
                    $skinFileUrl = leoECHelper::getUriFromPath($dir).'/';

                    if (file_exists($dir.'/icon.png')) {
                        $output[$i]['icon'] = $skinFileUrl.'icon.png';
                    }
                    $output[$i]['css'] = $skinFileUrl;

                    $isRTL = Context::getContext()->language->is_rtl;
                    if ($isRTL && file_exists($dir.'/custom-rtl.css')) {
                        $output[$i]['rtl'] = 1;
                    } else {
                        $output[$i]['rtl'] = 0;
                    }
                    $i++;
                }
            }
            if (!empty($output)) {
                break;
            }
        }
        return $output;
    }

    public static function getInstance()
    {
        static $_instance;
        if (!$_instance) {
            $_instance = new leoECHelper();
        }
        return $_instance;
    }
    
    public static function getConfigName($name)
    {
        return Tools::strtoupper(self::getThemeKey().'_'.$name);
    }

    public static function getThemeKey($module_key = 'leo_module')
    {
        static $theme_key;
        if (!$theme_key) {
            #CASE : load theme_key from ROOT/THEMES/THEME_NAME/config.xml
            $xml = self::getThemeInfo(self::getThemeName());
            if (isset($xml->theme_key)) {
                $theme_key = trim((string)$xml->theme_key);
            }
        }
        if (!$theme_key && !empty($module_key)) {
            #CASE : default load from module_key
            $theme_key = $module_key;
        }
        return $theme_key;
    }

    public static function getCssAdminDir()
    {
        static $css_folder;
        
        if (!$css_folder) {
            if (is_dir(_PS_MODULE_DIR_.'leoelements/views/css/')) {
                $css_folder = __PS_BASE_URI__.'modules/leoelements/views/css/';
            } else {
                $css_folder = __PS_BASE_URI__.'modules/leoelements/css/';
            }
        }
        
        return $css_folder;
    }
    
    public static function getCssDir()
    {
        static $css_folder;
        
        if (!$css_folder) {
            if (is_dir(_PS_MODULE_DIR_.'leoelements/views/css/')) {
                $css_folder = 'modules/leoelements/views/css/';
            } else {
                $css_folder = 'modules/leoelements/css/';
            }
        }
        return $css_folder;
    }
    
    public static function getJsDir()
    {
        static $js_folder;
        
        if (!$js_folder) {
            if (is_dir(_PS_MODULE_DIR_.'leoelements/views/css/')) {
                $js_folder = 'modules/leoelements/views/js/';
            } else {
                $js_folder = 'modules/leoelements/js/';
            }
        }
        return $js_folder;
    }
    
    public static function getJsAdminDir()
    {
        static $js_folder;
        
        if (!$js_folder) {
            if (is_dir(_PS_MODULE_DIR_.'leoelements/views/css/')) {
                $js_folder = __PS_BASE_URI__.'modules/leoelements/views/js/';
            } else {
                $js_folder = __PS_BASE_URI__.'modules/leoelements/js/';
            }
        }
        return $js_folder;
    }
    
    public static function getThemeInfo($theme)
    {
        $xml = _PS_ALL_THEMES_DIR_.$theme.'/config.xml';

        $output = array();

        if (file_exists($xml)) {
            $output = simplexml_load_file($xml);
        }

        return $output;
    }
    
    public static function getPageName()
    {
        static $page_name;
        if (!$page_name) {
            $page_name = Dispatcher::getInstance()->getController();
            $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
        }
        
        if ($page_name == 'appagebuilderhome') {
            $page_name = 'index';
        }
        
        return $page_name;
    }
    
    /**
     * When install theme, still get old_theme
     */
    public static function getInstallationThemeName()
    {
        $theme_name = '';
        if (Tools::getValue('controller') == 'AdminThemes' && Tools::getValue('action') == 'enableTheme') {
            # Case install theme
            $theme_name = Tools::getValue('theme_name');
        } else if (Tools::getValue('controller') == 'AdminShop' && Tools::getValue('submitAddshop')) {
            # Case install theme
            $theme_name = Tools::getValue('theme_name');
        } else if ( preg_match('#/improve/design/themes/(?P<themeName>[a-zA-Z0-9_.-]+)/enable#sD', $_SERVER['REQUEST_URI'], $matches) ) {
            if (isset($matches['themeName']) && $matches['themeName']) {
                $theme_name = $matches['themeName'];
            }
        }
        
        if (empty($theme_name)) {
            $theme_name = self::getThemeName();
        }
        return $theme_name;
    }
    
    static $id_shop;
    /**
     * FIX Install multi theme
     * apPageHelper::getIDShop();
     */
    public static function getIDShop()
    {
        if ((int)self::$id_shop) {
            $id_shop = (int)self::$id_shop;
		} else {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        return $id_shop;
    }
    
    /*
     * get theme in SINGLE_SHOP or MULTI_SHOP
     * apPageHelper::getThemeName()
     */
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
    
    public static function fullCopy( $source, $target )
    {
        if (is_dir($source)) {
            @mkdir($target);
            $d = dir($source);
            while (FALSE !== ( $name = $d->read())) {
                if ($name == '.' || $name == '..' ) {
                    continue;
                }
                $entry = $source . '/' . $name;
                if (is_dir($entry)) {
                    self::fullCopy($entry, $target . '/' . $name);
                    continue;
                }
                
                copy($entry, $target . '/' . $name);
            }

            $d->close();
        } else {
            copy($source, $target);
        }
    }
    
    public static function leoCreateColumn($table_name, $col_name, $data_type)
    {
        $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
        $column = Db::getInstance()->executeS($sql);

        if (empty($column)) {
            $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` ADD COLUMN `'.pSQL($col_name).'` '.pSQL($data_type);
            $res = Db::getInstance()->execute($sql);
        }
    }
    
    public static function leoEditColumn($table_name, $col_name, $data_type)
    {
        $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
        $column = Db::getInstance()->executeS($sql);

        if (!empty($column)) {
            $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` MODIFY `'.pSQL($col_name).'` '.pSQL($data_type);
            $res = Db::getInstance()->execute($sql);
        }
    }
    
    public static function isLeo()
    {
        $result = false;
        if( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'leo_tuanvu')
        {
            $result = true;
        }
        return $result;
    }
}

class LeoECSetting
{
    public static function getHookHome()
    {
        return array(
            'displayTop',
            'displayLeftColumn',
            'displayHome',
            'displayRightColumn',
            'displayFooter'
        );
    }
    const HOOK_BOXED = 0;
    const HOOK_FULWIDTH_INDEXPAGE = 1;
    const HOOK_FULWIDTH_OTHERPAGE = 1;
    const ROW_BOXED = 0;
    const ROW_FULWIDTH_INDEXPAGE = 1;
    const HOOK_DISABLE_CACHE = 1;
    const HOOK_ENABLE_CACHE = 0;
    /**
     * hook for fullwidth and boxed
     */
    public static function getIndexHook($type = 1)
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            if ($type == 1) {
                # get name hook
                return array(
                    'displayBanner',
                    'displayNav1',
                    'displayNav2',
                    'displayTop',
                    'displayHome',
                    'displayFooterBefore',
                    'displayFooter',
                    'displayFooterAfter',
                );
            } else if ($type == 2) {
                # get name hook
                return array(
                    'displayBanner' => 'displayBanner',
                    'displayNav1' => 'displayNav1',
                    'displayNav2' => 'displayNav2',
                    'displayTop' => 'displayTop',
                    'displayHome' => 'displayHome',
                    'displayFooterBefore' => 'displayFooterBefore',
                    'displayFooter' => 'displayFooter',
                    'displayFooterAfter' => 'displayFooterAfter',
                );
            } else if ($type == 3) {
                # get default fullwidth or boxed for each hook
                return array(
                    'displayBanner' => self::HOOK_BOXED,
                    'displayNav1' => self::HOOK_BOXED,
                    'displayNav2' => self::HOOK_BOXED,
                    'displayTop' => self::HOOK_BOXED,
                    'displayHome' => self::HOOK_BOXED,
                    'displayFooterBefore' => self::HOOK_BOXED,
                    'displayFooter' => self::HOOK_BOXED,
                    'displayFooterAfter' => self::HOOK_BOXED,
                );
            }
        }
        
        if ($type == 1) {
            # get name hook
            return array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
            );
        } else if ($type == 2) {
            # get name hook
            return array(
                'displayNav1' => 'displayNav1',
                'displayNav2' => 'displayNav2',
                'displayTop' => 'displayTop',
                'displayHome' => 'displayHome',
                'displayFooterBefore' => 'displayFooterBefore',
                'displayFooter' => 'displayFooter',
                'displayFooterAfter' => 'displayFooterAfter',
            );
        } else if ($type == 3) {
            # get default fullwidth or boxed for each hook
            return array                (
                'displayNav1' => self::HOOK_BOXED,
                'displayNav2' => self::HOOK_BOXED,
                'displayTop' => self::HOOK_BOXED,
                'displayHome' => self::HOOK_BOXED,
                'displayFooterBefore' => self::HOOK_BOXED,
                'displayFooter' => self::HOOK_BOXED,
                'displayFooterAfter' => self::HOOK_BOXED,
            );
        }
    }

    /**
     * hook for fullwidth and boxed
     */
    public static function getOtherHook($type = 1)
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            if ($type == 1) {
                # get name hook
                return array(
                    'displayBanner',
                    'displayNav1',
                    'displayNav2',
                    'displayTop',
                    'displayHome',
                    'displayFooterBefore',
                    'displayFooter',
                    'displayFooterAfter',
                );
            } else if ($type == 2) {
                # get name hook
                return array(
                    'displayBanner' => 'displayBanner',
                    'displayNav1' => 'displayNav1',
                    'displayNav2' => 'displayNav2',
                    'displayTop' => 'displayTop',
                    'displayHome' => 'displayHome',
                    'displayFooterBefore' => 'displayFooterBefore',
                    'displayFooter' => 'displayFooter',
                    'displayFooterAfter' => 'displayFooterAfter',
                );
            } else if ($type == 3) {
                # get default value
                return array(
                    'displayBanner' => self::HOOK_BOXED,
                    'displayNav1' => self::HOOK_BOXED,
                    'displayNav2' => self::HOOK_BOXED,
                    'displayTop' => self::HOOK_BOXED,
                    'displayHome' => self::HOOK_BOXED,
                    'displayFooterBefore' => self::HOOK_BOXED,
                    'displayFooter' => self::HOOK_BOXED,
                    'displayFooterAfter' => self::HOOK_BOXED,
                );
            }
        }
        
        if ($type == 1) {
            # get name hook
            return array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
            );
        } else if ($type == 2) {
            # get name hook
            return array(
                'displayNav1' => 'displayNav1',
                'displayNav2' => 'displayNav2',
                'displayTop' => 'displayTop',
                'displayHome' => 'displayHome',
                'displayFooterBefore' => 'displayFooterBefore',
                'displayFooter' => 'displayFooter',
                'displayFooterAfter' => 'displayFooterAfter',
            );
        } else if ($type == 3) {
            # get default value
            return array(
                'displayNav1' => self::HOOK_BOXED,
                'displayNav2' => self::HOOK_BOXED,
                'displayTop' => self::HOOK_BOXED,
                'displayHome' => self::HOOK_BOXED,
                'displayFooterBefore' => self::HOOK_BOXED,
                'displayFooter' => self::HOOK_BOXED,
                'displayFooterAfter' => self::HOOK_BOXED,
            );
        }
    }

    public static function getCacheHook($type = 1)
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            if ($type == 1) {
                # get name hook
                return array(
                    'displayBanner',
                    'displayNav1',
                    'displayNav2',
                    'displayTop',
                    'displayHome',
                    'displayFooterBefore',
                    'displayFooter',
                    'displayFooterAfter',
                );
            } else if ($type == 2) {
                # get name hook
                return array(
                    'displayTop' => 'displayTop',
                    'displayHome' => 'displayHome',
                    'displayFooter' => 'displayFooter',
                );
            } else if ($type == 3) {
                # get default value
                return array(
                    'displayTop' => self::HOOK_ENABLE_CACHE,
                    'displayHome' => self::HOOK_ENABLE_CACHE,
                    'displayFooter' => self::HOOK_ENABLE_CACHE,
                );
            }
        }
        
        if ($type == 1) {
            # get name hook
            return array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
            );
        } else if ($type == 2) {
            # get name hook
            return array (
                'displayTop' => 'displayTop',
                'displayHome' => 'displayHome',
                'displayFooter' => 'displayFooter',
            );
        } else if ($type == 3) {
            # get default value
            return array (
                'displayTop' => self::HOOK_ENABLE_CACHE,
                'displayHome' => self::HOOK_ENABLE_CACHE,
                'displayFooter' => self::HOOK_ENABLE_CACHE,
            );
        }
    }

    public static function getPositionsName()
    {
        return array('header', 'content', 'footer', 'product');
    }

    /**
     * Get list hooks by type
     * @param type $type: string in {all, header, footer, content, product}
     * @return array
     */
    public static function getHook($type = 'all')
    {
        $list_hook = array();
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            $hook_header_default = array(
                'displayBanner',
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayNavFullWidth',
            );
        } else {
            $hook_header_default = array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayNavFullWidth',
            );
        }
        $hook_content_default = array(
            'displayLeftColumn',
            'displayHome',
            'displayRightColumn',
        );
        $hook_footer_default = array(
            'displayFooterBefore',
            'displayFooter',
            'displayFooterAfter',
        );
        $hook_product_default = array(
            'displayReassurance',
            'displayFooterProduct',
        );
        $hook_category_default = array(
            'displayHeaderCategory',
            'displayFooterCategory'
        );
        if ($type == 'all') {
            $list_hook = array_merge($hook_header_default, $hook_content_default, $hook_footer_default, $hook_product_default, $hook_category_default);
        } else if ($type == 'header') {
            $list_hook = $hook_header_default;
        } else if ($type == 'content') {
            $list_hook = $hook_content_default;
        } else if ($type == 'footer') {
            $list_hook = $hook_footer_default;
        } else if ($type == 'product') {
            $list_hook = $hook_product_default;
        } else if ($type == 'category') {
            $list_hook = $hook_category_default;
        }
        return $list_hook;
    }

    public static function getProductContainer()
    {
        $html = '';
        $html .= '<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">' . "\n";
        $html .= '  <div class="thumbnail-container">' . "\n";
        return $html;
    }
    public static function getProductContainerEnd()
    {
        $html = '';
        $html .= '  </div>' . "\n";
        $html .= '</article>' . "\n";
        return $html;
    }

    public static function getProductFunctionalButtons()
    {
        return '<div class="functional-buttons clearfix">';
    }

    public static function getProductLeftBlock()
    {
        return '    <div class="product-image">';
    }

    public static function getProductRightBlock()
    {
        return '    <div class="product-meta">';
    }

    public static function getProductElementIcon()
    {
//        return array(
//            'add_to_cart' => 'icon-shopping-cart',
//            'color' => 'icon-circle',
//            'compare' => 'icon-bar-chart',
//            'description' => 'icon-file-text',
//            'display_product_price_block' => 'icon-dollar',
//            'flags' => 'icon-flag',
//            'functional_buttons' => 'icon-puzzle-piece',
//            'name' => 'icon-file',
//            'product_delivery_time' => 'icon-time',
//            'reviews' => 'icon-star',
//            'status' => 'icon-question-sign',
//            'view' => 'icon-eye-open',
//            'quick_view' => 'icon-eye-open',
//            'image_container' => 'icon-picture',
//            'price' => 'icon-money',
//            'wishlist' => 'icon-heart',
//        );
        return array(
            'add_to_cart' => 'icon-shopping-cart',
            'add_to_cart_attribute' => 'icon-list',
            'add_to_cart_quantity' => 'icon-sort',
        'product_variants' => 'icon-circle',
            'compare' => 'icon-bar-chart',
            'description' => 'icon-file-text',
            'display_product_price_block' => 'icon-dollar',
        'product_flags' => 'icon-flag',
            'functional_buttons' => 'icon-puzzle-piece',
        'product_name' => 'icon-file',
            'product_delivery_time' => 'icon-time',
            'reviews' => 'icon-star',
            'status' => 'icon-question-sign',
            'view' => 'icon-eye-open',
        'quickview' => 'icon-eye-open',
        'product_thumbnail' => 'icon-picture',
        'product_price_and_shipping' => 'icon-money',
            'wishlist' => 'icon-heart',
        'product_description_short' => 'icon-file-text-o',
        'product_description' => 'icon-file-text',
        );
    }

    public static function writeFile($folder, $file, $value)
    {
        $file = $folder.'/'.$file;
        $handle = fopen($file, 'w+');
        fwrite($handle, ($value));
        fclose($handle);
    }

    public static function getRandomNumber()
    {
        return rand() + time();
    }

    public static function returnYesNo()
    {
        return array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => self::l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => self::l('Disabled')
        ));
    }

    public static function returnTrueFalse()
    {
        return array(array(
                'id' => 'active_on',
                'value' => 'true',
                'label' => self::l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 'false',
                'label' => self::l('Disabled')
        ));
    }

    public static function getOrderByBlog()
    {
        return array(
            array(
                'id' => 'id_leoblogcat', 'name' => self::l('Category')),
            array(
                'id' => 'id_leoblog_blog', 'name' => self::l('ID')),
            array(
                'id' => 'meta_title', 'name' => self::l('Title')),
            array(
                'id' => 'date_add', 'name' => self::l('Date add')),
            array(
                'id' => 'date_upd', 'name' => self::l('Date update')),
        );
    }

    public static function getOrderByManu()
    {
        return array(
            array(
                'id' => 'id_manufacturer', 'name' => self::l('ID')),
            array(
                'id' => 'name', 'name' => self::l('Name')),
            array(
                'id' => 'date_add', 'name' => self::l('Date add')),
            array(
                'id' => 'date_upd', 'name' => self::l('Date update')),
        );
    }

    public static function getOrderBy()
    {
        return array(
//            array(
//                'id' => 'position', 'name' => self::l('Position')),    // remove to increase speed
            array(
                'id' => 'id_product', 'name' => self::l('ID')),
            array(
                'id' => 'name', 'name' => self::l('Name')),
            array(
                'id' => 'reference', 'name' => self::l('Reference')),
            array(
                'id' => 'price', 'name' => self::l('Base price')),
            array(
                'id' => 'position', 'name' => self::l('Position')),
            array(
                'id' => 'date_add', 'name' => self::l('Date add')),
            array(
                'id' => 'date_upd', 'name' => self::l('Date update')),
            array(
                'id' => 'quantity', 'name' => self::l('Sales (only for Best Sales)')),
        );
    }

    public static function getColumnGrid()
    {
        return array(
            'xl' => self::l('Extra large devices - Desktops (≥1200px)'),
            'lg' => self::l('Large devices - Desktops (≥992px)'),
            'md' => self::l('Medium devices - Tablets (≥768px)'),
            'sm' => self::l('Small devices (≥576px)'),
            'xs' => self::l('Extra small devices (<576px)'),
            'sp' => self::l('Smart Phones (< 480px)'),
        );
    }

    public static function l($text)
    {
        return $text;
    }

    public static function getAnimations()
    {
        return array(
            'none' => array(
                'name' => self::l('Turn off'),
                'query' => array(
                    array(
                        'id' => 'none',
                        'name' => self::l('None'),
                    )
                )
            ),
            'attention_seekers' => array(
                'name' => self::l('Attention Seekers'),
                'query' => array(
                    array(
                        'id' => 'bounce',
                        'name' => self::l('bounce'),
                    ),
                    array(
                        'id' => 'flash',
                        'name' => self::l('flash'),
                    ), array(
                        'id' => 'pulse',
                        'name' => self::l('pulse'),
                    ), array(
                        'id' => 'rubberBand',
                        'name' => self::l('rubberBand'),
                    ), array(
                        'id' => 'shake',
                        'name' => self::l('shake'),
                    ), array(
                        'id' => 'swing',
                        'name' => self::l('swing'),
                    ), array(
                        'id' => 'tada',
                        'name' => self::l('tada'),
                    ), array(
                        'id' => 'wobble',
                        'name' => self::l('wobble'),
                    )
                )
            ),
            'Bouncing_Entrances' => array(
                'name' => self::l('Bouncing Entrances'),
                'query' => array(
                    array(
                        'id' => 'bounceIn',
                        'name' => self::l('bounceIn'),
                    ),
                    array(
                        'id' => 'bounceInDown',
                        'name' => self::l('bounceInDown'),
                    ),
                    array(
                        'id' => 'bounceInLeft',
                        'name' => self::l('bounceInLeft'),
                    ),
                    array(
                        'id' => 'bounceInRight',
                        'name' => self::l('bounceInRight'),
                    ),
                    array(
                        'id' => 'bounceInUp',
                        'name' => self::l('bounceInUp'),
                    )
                ),
            ),
            'Bouncing_Exits' => array(
                'name' => self::l('Bouncing Exits'),
                'query' => array(
                    array(
                        'id' => 'bounceOut',
                        'name' => self::l('bounceOut'),
                    ),
                    array(
                        'id' => 'bounceOutDown',
                        'name' => self::l('bounceOutDown'),
                    ),
                    array(
                        'id' => 'bounceOutLeft',
                        'name' => self::l('bounceOutLeft'),
                    ),
                    array(
                        'id' => 'bounceOutRight',
                        'name' => self::l('bounceOutRight'),
                    ),
                    array(
                        'id' => 'bounceOutUp',
                        'name' => self::l('bounceOutUp'),
                    )
                ),
            ),
            'Fading_Entrances' => array(
                'name' => self::l('Fading Entrances'),
                'query' => array(
                    array(
                        'id' => 'fadeIn',
                        'name' => self::l('fadeIn'),
                    ),
                    array(
                        'id' => 'fadeInDown',
                        'name' => self::l('fadeInDown'),
                    ),
                    array(
                        'id' => 'fadeInDownBig',
                        'name' => self::l('fadeInDownBig'),
                    ),
                    array(
                        'id' => 'fadeInLeft',
                        'name' => self::l('fadeInLeft'),
                    ),
                    array(
                        'id' => 'fadeInLeftBig',
                        'name' => self::l('fadeInLeftBig'),
                    ),
                    array(
                        'id' => 'fadeInRight',
                        'name' => self::l('fadeInRight'),
                    ),
                    array(
                        'id' => 'fadeInRightBig',
                        'name' => self::l('fadeInRightBig'),
                    ),
                    array(
                        'id' => 'fadeInRight',
                        'name' => self::l('fadeInRight'),
                    ),
                    array(
                        'id' => 'fadeInRightBig',
                        'name' => self::l('fadeInRightBig'),
                    ),
                    array(
                        'id' => 'fadeInUp',
                        'name' => self::l('fadeInUp'),
                    ),
                    array(
                        'id' => 'fadeInUpBig',
                        'name' => self::l('fadeInUpBig'),
                    ),
                ),
            ),
            'Fading_Exits' => array(
                'name' => self::l('Fading Exits'),
                'query' => array(
                    array(
                        'id' => 'fadeOut',
                        'name' => self::l('fadeOut'),
                    ),
                    array(
                        'id' => 'fadeOutDown',
                        'name' => self::l('fadeOutDown'),
                    ),
                    array(
                        'id' => 'fadeOutDownBig',
                        'name' => self::l('fadeOutDownBig'),
                    ),
                    array(
                        'id' => 'fadeOutLeft',
                        'name' => self::l('fadeOutLeft'),
                    ),
                    array(
                        'id' => 'fadeOutRight',
                        'name' => self::l('fadeOutRight'),
                    ),
                    array(
                        'id' => 'fadeOutRightBig',
                        'name' => self::l('fadeOutRightBig'),
                    ),
                    array(
                        'id' => 'fadeOutUp',
                        'name' => self::l('fadeOutUp'),
                    ),
                    array(
                        'id' => 'fadeOutUpBig',
                        'name' => self::l('fadeOutUpBig'),
                    )
                ),
            ),
            'Flippers' => array(
                'name' => self::l('Flippers'),
                'query' => array(
                    array(
                        'id' => 'flip',
                        'name' => self::l('flip'),
                    ),
                    array(
                        'id' => 'flipInX',
                        'name' => self::l('flipInX'),
                    ),
                    array(
                        'id' => 'flipInY',
                        'name' => self::l('flipInY'),
                    ),
                    array(
                        'id' => 'flipOutX',
                        'name' => self::l('flipOutX'),
                    ),
                    array(
                        'id' => 'flipOutY',
                        'name' => self::l('flipOutY'),
                    )
                ),
            ),
            'Lightspeed' => array(
                'name' => self::l('Lightspeed'),
                'query' => array(
                    array(
                        'id' => 'lightSpeedIn',
                        'name' => self::l('lightSpeedIn'),
                    ),
                    array(
                        'id' => 'lightSpeedOut',
                        'name' => self::l('lightSpeedOut'),
                    )
                ),
            ),
            'Rotating_Entrances' => array(
                'name' => self::l('Rotating Entrances'),
                'query' => array(
                    array(
                        'id' => 'rotateIn',
                        'name' => self::l('rotateIn'),
                    ),
                    array(
                        'id' => 'rotateInDownLeft',
                        'name' => self::l('rotateInDownLeft'),
                    ),
                    array(
                        'id' => 'rotateInDownRight',
                        'name' => self::l('rotateInDownRight'),
                    ),
                    array(
                        'id' => 'rotateInUpLeft',
                        'name' => self::l('rotateInUpLeft'),
                    ),
                    array(
                        'id' => 'rotateInUpRight',
                        'name' => self::l('rotateInUpRight'),
                    )
                ),
            ),
            'Rotating_Exits' => array(
                'name' => self::l('Rotating Exits'),
                'query' => array(
                    array(
                        'id' => 'rotateOut',
                        'name' => self::l('rotateOut'),
                    ),
                    array(
                        'id' => 'rotateOutDownLeft',
                        'name' => self::l('rotateOutDownLeft'),
                    ),
                    array(
                        'id' => 'rotateOutDownRight',
                        'name' => self::l('rotateOutDownRight'),
                    ),
                    array(
                        'id' => 'rotateOutUpLeft',
                        'name' => self::l('rotateOutUpLeft'),
                    ),
                    array(
                        'id' => 'rotateOutUpRight',
                        'name' => self::l('rotateOutUpRight'),
                    )
                ),
            ),
            'Specials' => array(
                'name' => self::l('Specials'),
                'query' => array(
                    array(
                        'id' => 'hinge',
                        'name' => self::l('hinge'),
                    ),
                    array(
                        'id' => 'rollIn',
                        'name' => self::l('rollIn'),
                    ),
                    array(
                        'id' => 'rollOut',
                        'name' => self::l('rollOut'),
                    )
                ),
            ),
            'Zoom Entrances' => array(
                'name' => self::l('Zoom Entrances'),
                'query' => array(
                    array(
                        'id' => 'zoomIn',
                        'name' => self::l('zoomIn'),
                    ),
                    array(
                        'id' => 'zoomInDown',
                        'name' => self::l('zoomInDown'),
                    ),
                    array(
                        'id' => 'zoomInLeft',
                        'name' => self::l('zoomInLeft'),
                    ),
                    array(
                        'id' => 'zoomInRight',
                        'name' => self::l('zoomInRight'),
                    ),
                    array(
                        'id' => 'zoomInUp',
                        'name' => self::l('zoomInUp'),
                    )
                ),
            ),
            'Zoom_Exits' => array(
                'name' => self::l('Zoom Exits'),
                'query' => array(
                    array(
                        'id' => 'zoomOut',
                        'name' => self::l('zoomOut'),
                    ),
                    array(
                        'id' => 'zoomOutDown',
                        'name' => self::l('zoomOutDown'),
                    ),
                    array(
                        'id' => 'zoomOutLeft',
                        'name' => self::l('zoomOutLeft'),
                    ),
                    array(
                        'id' => 'zoomOutRight',
                        'name' => self::l('zoomOutRight'),
                    ),
                    array(
                        'id' => 'zoomOutUp',
                        'name' => self::l('zoomOutUp'),
                    )
                ),
            )
        );
    }
    
    //DONGND:: build list animation for group and column
    public static function getAnimationsColumnGroup()
    {
        return array(
            'none' => array(
                'name' => self::l('Turn off'),
                'query' => array(
                    array(
                        'id' => 'none',
                        'name' => self::l('None'),
                    )
                )
            ),
            'Fading_Entrances' => array(
                'name' => self::l('Fading Entrances'),
                'query' => array(
                    array(
                        'id' => 'fadeIn',
                        'name' => self::l('fadeIn'),
                    ),
                    array(
                        'id' => 'fadeInDown',
                        'name' => self::l('fadeInDown'),
                    ),
                    array(
                        'id' => 'fadeInDownBig',
                        'name' => self::l('fadeInDownBig'),
                    ),
                    array(
                        'id' => 'fadeInLeft',
                        'name' => self::l('fadeInLeft'),
                    ),
                    array(
                        'id' => 'fadeInLeftBig',
                        'name' => self::l('fadeInLeftBig'),
                    ),
                    array(
                        'id' => 'fadeInRight',
                        'name' => self::l('fadeInRight'),
                    ),
                    array(
                        'id' => 'fadeInRightBig',
                        'name' => self::l('fadeInRightBig'),
                    ),
                    array(
                        'id' => 'fadeInUp',
                        'name' => self::l('fadeInUp'),
                    ),
                    array(
                        'id' => 'fadeInUpBig',
                        'name' => self::l('fadeInUpBig'),
                    ),
                ),
            ),
            'Bouncing_Entrances' => array(
                'name' => self::l('Bouncing Entrances'),
                'query' => array(
                    array(
                        'id' => 'bounceIn',
                        'name' => self::l('bounceIn'),
                    ),
                    array(
                        'id' => 'bounceInDown',
                        'name' => self::l('bounceInDown'),
                    ),
                    array(
                        'id' => 'bounceInLeft',
                        'name' => self::l('bounceInLeft'),
                    ),
                    array(
                        'id' => 'bounceInRight',
                        'name' => self::l('bounceInRight'),
                    ),
                    array(
                        'id' => 'bounceInUp',
                        'name' => self::l('bounceInUp'),
                    )
                ),
            ),
            'Zoom Entrances' => array(
                'name' => self::l('Zoom Entrances'),
                'query' => array(
                    array(
                        'id' => 'zoomIn',
                        'name' => self::l('zoomIn'),
                    ),
                    array(
                        'id' => 'zoomInDown',
                        'name' => self::l('zoomInDown'),
                    ),
                    array(
                        'id' => 'zoomInLeft',
                        'name' => self::l('zoomInLeft'),
                    ),
                    array(
                        'id' => 'zoomInRight',
                        'name' => self::l('zoomInRight'),
                    ),
                    array(
                        'id' => 'zoomInUp',
                        'name' => self::l('zoomInUp'),
                    )
                ),
            ),
            'attention_seekers' => array(
                'name' => self::l('Attention Seekers'),
                'query' => array(
                    array(
                        'id' => 'bounce',
                        'name' => self::l('bounce'),
                    ),
                    array(
                        'id' => 'flash',
                        'name' => self::l('flash'),
                    ),
                    array(
                        'id' => 'pulse',
                        'name' => self::l('pulse'),
                    ),
                    array(
                        'id' => 'rubberBand',
                        'name' => self::l('rubberBand'),
                    ),
                    array(
                        'id' => 'shake',
                        'name' => self::l('shake'),
                    ),
                    array(
                        'id' => 'swing',
                        'name' => self::l('swing'),
                    ),
                    array(
                        'id' => 'tada',
                        'name' => self::l('tada'),
                    ),
                    array(
                        'id' => 'wobble',
                        'name' => self::l('wobble'),
                    )
                )
            ),
            'Flippers' => array(
                'name' => self::l('Flippers'),
                'query' => array(
                    array(
                        'id' => 'flip',
                        'name' => self::l('flip'),
                    ),
                    array(
                        'id' => 'flipInX',
                        'name' => self::l('flipInX'),
                    ),
                    array(
                        'id' => 'flipInY',
                        'name' => self::l('flipInY'),
                    ),
                    array(
                        'id' => 'flipOutX',
                        'name' => self::l('flipOutX'),
                    ),
                    array(
                        'id' => 'flipOutY',
                        'name' => self::l('flipOutY'),
                    )
                ),
            ),
            'Lightspeed' => array(
                'name' => self::l('Lightspeed'),
                'query' => array(
                    array(
                        'id' => 'lightSpeedIn',
                        'name' => self::l('lightSpeedIn'),
                    ),
                    array(
                        'id' => 'lightSpeedOut',
                        'name' => self::l('lightSpeedOut'),
                    )
                ),
            ),
            'Rotating_Entrances' => array(
                'name' => self::l('Rotating Entrances'),
                'query' => array(
                    array(
                        'id' => 'rotateIn',
                        'name' => self::l('rotateIn'),
                    ),
                    array(
                        'id' => 'rotateInDownLeft',
                        'name' => self::l('rotateInDownLeft'),
                    ),
                    array(
                        'id' => 'rotateInDownRight',
                        'name' => self::l('rotateInDownRight'),
                    ),
                    array(
                        'id' => 'rotateInUpLeft',
                        'name' => self::l('rotateInUpLeft'),
                    ),
                    array(
                        'id' => 'rotateInUpRight',
                        'name' => self::l('rotateInUpRight'),
                    )
                ),
            ),
            'Specials' => array(
                'name' => self::l('Specials'),
                'query' => array(
                    array(
                        'id' => 'hinge',
                        'name' => self::l('hinge'),
                    ),
                    array(
                        'id' => 'rollIn',
                        'name' => self::l('rollIn'),
                    ),
                    array(
                        'id' => 'rollOut',
                        'name' => self::l('rollOut'),
                    )
                ),
            ),
            'Bouncing_Exits' => array(
                'name' => self::l('Bouncing Exits'),
                'query' => array(
                    array(
                        'id' => 'bounceOut',
                        'name' => self::l('bounceOut'),
                    ),
                    array(
                        'id' => 'bounceOutDown',
                        'name' => self::l('bounceOutDown'),
                    ),
                    array(
                        'id' => 'bounceOutLeft',
                        'name' => self::l('bounceOutLeft'),
                    ),
                    array(
                        'id' => 'bounceOutRight',
                        'name' => self::l('bounceOutRight'),
                    ),
                    array(
                        'id' => 'bounceOutUp',
                        'name' => self::l('bounceOutUp'),
                    )
                ),
            ),
            'Fading_Exits' => array(
                'name' => self::l('Fading Exits'),
                'query' => array(
                    array(
                        'id' => 'fadeOut',
                        'name' => self::l('fadeOut'),
                    ),
                    array(
                        'id' => 'fadeOutDown',
                        'name' => self::l('fadeOutDown'),
                    ),
                    array(
                        'id' => 'fadeOutDownBig',
                        'name' => self::l('fadeOutDownBig'),
                    ),
                    array(
                        'id' => 'fadeOutLeft',
                        'name' => self::l('fadeOutLeft'),
                    ),
                    array(
                        'id' => 'fadeOutRight',
                        'name' => self::l('fadeOutRight'),
                    ),
                    array(
                        'id' => 'fadeOutRightBig',
                        'name' => self::l('fadeOutRightBig'),
                    ),
                    array(
                        'id' => 'fadeOutUp',
                        'name' => self::l('fadeOutUp'),
                    ),
                    array(
                        'id' => 'fadeOutUpBig',
                        'name' => self::l('fadeOutUpBig'),
                    )
                ),
            ),
            'Rotating_Exits' => array(
                'name' => self::l('Rotating Exits'),
                'query' => array(
                    array(
                        'id' => 'rotateOut',
                        'name' => self::l('rotateOut'),
                    ),
                    array(
                        'id' => 'rotateOutDownLeft',
                        'name' => self::l('rotateOutDownLeft'),
                    ),
                    array(
                        'id' => 'rotateOutDownRight',
                        'name' => self::l('rotateOutDownRight'),
                    ),
                    array(
                        'id' => 'rotateOutUpLeft',
                        'name' => self::l('rotateOutUpLeft'),
                    ),
                    array(
                        'id' => 'rotateOutUpRight',
                        'name' => self::l('rotateOutUpRight'),
                    )
                ),
            ),
            'Zoom_Exits' => array(
                'name' => self::l('Zoom Exits'),
                'query' => array(
                    array(
                        'id' => 'zoomOut',
                        'name' => self::l('zoomOut'),
                    ),
                    array(
                        'id' => 'zoomOutDown',
                        'name' => self::l('zoomOutDown'),
                    ),
                    array(
                        'id' => 'zoomOutLeft',
                        'name' => self::l('zoomOutLeft'),
                    ),
                    array(
                        'id' => 'zoomOutRight',
                        'name' => self::l('zoomOutRight'),
                    ),
                    array(
                        'id' => 'zoomOutUp',
                        'name' => self::l('zoomOutUp'),
                    )
                ),
            )
        );
    }

    public static function requireShortCode($short_code, $theme_dir = '')
    {
        if (file_exists($theme_dir.'modules/leoelements/classes/shortcodes/'.$short_code)) {
            return $theme_dir.'modules/leoelements/classes/shortcodes/'.$short_code;
        }
        if (file_exists(_PS_MODULE_DIR_.'leoelements/classes/shortcodes/'.$short_code)) {
            return _PS_MODULE_DIR_.'leoelements/classes/shortcodes/'.$short_code;
        }
        return false;
    }

    public static function getControllerId($controller, $ids)
    {
        switch ($controller) {
            case 'product':
                $current_id = Tools::getValue('id_product');
                if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
                    return $current_id;
                }
                break;
            case 'category':
                $current_id = Tools::getValue('id_category');
                if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
                    return $current_id;
                }
                break;
            case 'cms':
                $current_id = Tools::getValue('id_cms');
                if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
                    return $current_id;
                }
                break;
            default:
                return false;
        }
    }

    public static function getAllowOverrideHook()
    {
        return array('rightcolumn', 'leftcolumn', 'home', 'top', 'footer');
    }

    public static function returnWidthList()
    {
        return array('12', '11', '10', '9.6', '9', '8', '7.2', '7', '6', '4.8', '5', '4', '3', '2.4', '2', '1');
    }

    public static function getDefaultNameImage($type = 'small')
    {
        $sep = '_';
        $arr = array('small' => 'small'.$sep.'default', 'thickbox' => 'thickbox'.$sep.'default');
        return $arr[$type];
    }

    public static function getModeDebugLog()
    {
        return 0;
    }

    public static function buildGuide($context, $path = '', $current_step = 1)
    {
        $skip = Tools::getIsset('skip') ? Tools::getValue('skip') : '';
        $done = Tools::getIsset('done') ? Tools::getValue('done') : '';
        $reset = Tools::getIsset('ap_guide_reset') ? Tools::getValue('ap_guide_reset') : '';
        if ($skip || $done) {
            Configuration::updateValue('leoelements_GUIDE', 4);
            return '';
        }
        if ($reset) {
            Configuration::updateValue('leoelements_GUIDE', 1);
        }
        $status = Configuration::get('leoelements_GUIDE');
        if ($status > 3) {
            return '';
        }
        // Save next step
        if ($status < $current_step) {
            Configuration::updateValue('leoelements_GUIDE', $current_step);
        }
        if ($current_step == 0) {
            $current_step = $status;
        }
        $url1 = 'index.php?controller=adminmodules&configure=leoelements&token='.Tools::getAdminTokenLite('AdminModules')
                .'&tab_module=Home&module_name=leoelements';
        $url2 = '';
        $url3 = '';
        $next_step = '';
        // Add new profile
        if ($current_step == 1) {
            $next_step = $context->link->getAdminLink('AdminLeoElementsProfiles').'&addleoelements_profiles';
        }
        if ($current_step == 2) {
            $url2 = $context->link->getAdminLink('AdminLeoElementsProfiles').'&addleoelements_profiles';
            $next_step = $context->link->getAdminLink('AdminLeoElementsProfiles');
        }
        if ($current_step == 3) {
            $url2 = $context->link->getAdminLink('AdminLeoElementsProfiles').'&addleoelements_profiles';
            $url3 = $context->link->getAdminLink('AdminLeoElementsProfiles');
            $next_step = $context->link->getAdminLink('AdminLeoElementsProfiles');
        }
        $context->smarty->assign(array(
            'is_guide' => 1,
            'url1' => $url1,
            'url2' => $url2,
            'url3' => $url3,
            'next_step' => $next_step,
            'step' => $current_step));
        return $context->smarty->fetch($path);
    }

    public static function listFontAwesome()
    {
        return array(
            array('value' => 'icon-font'),
            array('value' => 'icon-bold'),
            array('value' => 'icon-adjust'),
            array('value' => 'icon-calendar'),
            array('value' => 'icon-bookmark'),
            array('value' => 'icon-bolt'),
            array('value' => 'icon-book'),
            array('value' => 'icon-certificate'),
            array('value' => 'icon-bullhorn'),
            array('value' => 'icon-check'),
            array('value' => 'icon-check-square-o'),
            array('value' => 'icon-comments-o'),
            array('value' => 'icon-comment'),
            array('value' => 'icon-credit-card'),
            array('value' => 'icon-thumbs-up'),
            array('value' => 'icon-thumbs-down'),
            array('value' => 'icon-thumbs-o-up'),
            array('value' => 'icon-thumbs-o-down'),
            array('value' => 'icon-truck'),
            array('value' => 'icon-angle-left'),
            array('value' => 'icon-angle-right'),
            array('value' => 'icon-angle-up'),
            array('value' => 'icon-angle-down'),
            array('value' => 'icon-angle-double-left'),
            array('value' => 'icon-angle-double-right'),
            array('value' => 'icon-angle-double-up'),
            array('value' => 'icon-angle-double-down'),
            array('value' => 'icon-arrow-left'),
            array('value' => 'icon-arrow-right'),
            array('value' => 'icon-arrow-up'),
            array('value' => 'icon-arrow-down'),
            array('value' => 'icon-align-left'),
            array('value' => 'icon-align-right'),
            array('value' => 'icon-align-center'),
            array('value' => 'icon-align-justify'),
            array('value' => 'icon-arrow-circle-o-left'),
            array('value' => 'icon-arrow-circle-o-right'),
            array('value' => 'icon-toggle-left'),
            array('value' => 'icon-toggle-right'),
            array('value' => 'icon-eye'),
            array('value' => 'icon-eye-slash'),
            array('value' => 'icon-smile-o'),
            array('value' => 'icon-spinner'),
            array('value' => 'icon-user'),
            array('value' => 'icon-users'),
            array('value' => 'icon-user-plus'),
            array('value' => 'icon-user-times'),
            array('value' => 'icon-user-md'),
            array('value' => 'icon-user-secret'),
            array('value' => 'icon-female'),
            array('value' => 'icon-male'),
            array('value' => 'icon-quote-left'),
            array('value' => 'icon-quote-right'),
            array('value' => 'icon-html5'),
            array('value' => 'icon-css3'),
            array('value' => 'icon-android'),
            array('value' => 'icon-google'),
            array('value' => 'icon-apple'),
            array('value' => 'icon-windows'),
            array('value' => 'icon-linux'),
            array('value' => 'icon-youtube'),
            array('value' => 'icon-twitter'),
            array('value' => 'icon-yahoo'),
            array('value' => 'icon-skype'),
            array('value' => 'icon-trello'),
            array('value' => 'icon-slack'),
            array('value' => 'icon-wordpress'),
            array('value' => 'icon-drupal'),
            array('value' => 'icon-joomla'),
        );
    }
    
    public static function getOverrideHook()
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            $list_hook = array(
                'displayBanner',
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayLeftColumn',
                'displayRightColumn',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
                'displayFooterProduct',
                'displayRightColumnProduct',
                'displayLeftColumnProduct',
            );

            return $list_hook;
        }
        
        $list_hook = array(
            'displayNav1',
            'displayNav2',
            'displayTop',
            'displayHome',
            'displayLeftColumn',
            'displayRightColumn',
            'displayFooterBefore',
            'displayFooter',
            'displayFooterAfter',
            'displayFooterProduct',
            'displayRightColumnProduct',
            'displayLeftColumnProduct',
        );
        
        return $list_hook;
    }

    public static function ConvertSpecialChar($str = '')
    {
        $result = '';

        if (is_string($str)) {
            $result = str_replace('LEO_BACKSLASH', '\\', $str);
        }
        return $result;
    }

    public static function leoExitsDb($type='', $table_name='', $col_name='')
    {
        if ($type == 'table') {
            # EXITS TABLE
            $sql = 'SELECT COUNT(*) FROM information_schema.tables
                        WHERE table_schema = "'._DB_NAME_.'"
                        AND table_name = "'._DB_PREFIX_.pSQL($table_name).'"';
            $table = Db::getInstance()->getValue($sql);
            if (empty($table)) {
                return false;
            }
            return true;
            
        } else if ($type == 'column') {
            # EXITS COLUMN
            $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
            $column = Db::getInstance()->executeS($sql);
            if (empty($column)) {
                return false;
            }
            return true;
        }
        
        return false;
    }
}