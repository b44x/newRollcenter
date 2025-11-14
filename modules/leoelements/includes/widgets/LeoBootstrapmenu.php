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

namespace LeoElements;
use LeoElements\Leo_Helper;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_LeoBootstrapmenu extends Widget_Base
{
    /**
     * Get widget name.
     *
     * Retrieve accordion widget name.
     *
     * @since  1.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'LeoBootstrapmenu';
    }

    /**
     * Get widget title.
     *
     * Retrieve accordion widget title.
     *
     * @since  1.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return Leo_Helper::__( 'Leo Megamenu Module', 'elementor' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve accordion widget icon.
     *
     * @since  1.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
            return 'eicon-nav-menu';
    }
    
    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return [ 'leo', 'ap', 'menu', 'megamenu', 'bootstrapmenu'];
    }

    public function get_categories() {
            return [ 'leoelements' ];
    }


    /**
     * Register accordion widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->_register_content_controls();
		
    }
    
    protected function _register_content_controls()
    {
        $source = [
                'n' => Leo_Helper::__('None', 'elementor'),
        ];
                
        $module = \Module::getInstanceByName( 'leobootstrapmenu' );
        $megamenus = $module->getGroups();
        if ($megamenus && count($megamenus) > 0) {
            foreach( $megamenus as $slideshow ) {
                $value = $slideshow['randkey'];
                $title = $slideshow['title'];
                $source[$value] = $title;
            }
        }
            
        $this->start_controls_section(
                'section_options',
                [
                        'label' => Leo_Helper::__( 'Megamenu Options', 'elementor' ),
                ]
        );
        
        $this->add_control(
                'source',
                [
                        'label' => Leo_Helper::__('Source of Megamenu', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'frontend_available' => true,
                        'default' => 'n',
                        'tablet_default' => 'n',
                        'mobile_default' => 'n',
                        'options' => $source,
                        'frontend_available' => true,
                ]
        );
        
        $link_leobootstrapmenu = '';
        if(isset($GLOBALS['gb_leoelements']['url'])) {
            $link_leobootstrapmenu = $GLOBALS['gb_leoelements']['url']['link_leobootstrapmenu'];
        }
        
        $this->add_control(
                'anchor_note',
                [
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' => sprintf( 'Click to the link to manage Megamenu <br/> <a href="%s" target="_blank"> Leo Megamenu Module</a> ', $link_leobootstrapmenu ),
                ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Render accordion widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0
     * @access protected
     */
    protected function render()
    {
        $return = '';
        if ( Leo_Helper::is_admin() ) {
                return $return;
        }

        $settings = $this->get_settings_for_display();
        
        $assign = array(
            'apContent' => '',
            'formAtts' => array(
                'form_id' => 'form_'.$settings['source'],
                'megamenu_group' => $settings['source'], // 'ac70e5b81cccd4671f8c75a464e569bd',
                'class' => 'LeoBootstrapmenu',
                'override_folder' => '',
                'rtl' => '0',
            ),
            'homeSize' => array(
                'width' => '500',
                'height' => '563',
            ),
            'mediumSize' => array(
                'width' => '378',
                'height' => '472',
            ),
            'img_manu_dir' => '/task_live_editor/leo_1782_elements_free/img/m/',
            'comparator_max_item' => '0',
            'compared_products' => array(),
            'tpl_dir' => 'D:\HOST\localhost\leo_tuanvu\task_live_editor\leo_1782_elements_free/themes/classic/',
            'PS_CATALOG_MODE' => '0',
            'priceDisplay' => '1',
            'PS_STOCK_MANAGEMENT' => '1',
            'page_name' => 'index',
        );
        
        if (\Module::isInstalled('leobootstrapmenu') && \Module::isEnabled('leobootstrapmenu')) {
            $module = \Module::getInstanceByName( 'leobootstrapmenu' );
            $id_shop = (int)\Context::getContext()->shop->id;
            $assign['formAtts']['isEnabled'] = true;

            $link_array = explode(',', $assign['formAtts']['megamenu_group']);
            if ($link_array && !is_numeric($link_array['0'])) {
                $result = array();

                foreach ($link_array as $val) {
                    //my module call this function from menu and we import it
                    $temp = \BtmegamenuGroup::cacheGroupsByFields(array('randkey' => $val));

                    if ($temp) {
                        $result[] = $temp;
                    }
                }

                if (is_array($result) && !empty($result)) {
                    $where = '';
                    foreach ($result as $group) {
                        // validate module
                        $where .= ($where == '') ? $group['id_btmegamenu_group'] : ','.$group['id_btmegamenu_group'];
                        $where .= ',0';
                    }
                    $assign['formAtts']['megamenu_group'] = $where;
                    $form_id = explode("_", $assign['formAtts']['form_id']);
                    $assign['content_megamenu'] = $module->processHookCallBack($assign['formAtts']['megamenu_group'], $form_id[1]);

                }else{
                    $assign['formAtts']['isEnabled'] = false;
                    $assign['formAtts']['lib_has_error'] = true;
                    $assign['formAtts']['lib_error'] = 'Can not show LeoBootstrapMenu via Leoelements. Please check that The Group of LeoBootstrapMenu is exist.';
                }
            }
        } else {
            // validate module
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show LeoBootstrapmenu via Leoelements. Please enable LeoBootstrapmenu module.';
        }
        
        $context = \Context::getContext();
        
        if ($assign) {
            foreach ($assign as $key => $ass) {
                $context->smarty->assign(array($key => $ass));
            }
        }
        
        $template_file_name = 'module:/leoelements/views/templates/front/LeoBootstrapmenu.tpl';
        $out_put = '';
        $out_put           .= $context->smarty->fetch( $template_file_name );

        echo $out_put;

        return $return;
    }

    /**
     * Render accordion widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since  1.0
     * @access protected
     */
    protected function _content_template()
    {
        # here
    }
    
    protected function _render_view_setting_attributes( $settings, $attr_class_section = [], $attr_class_wrapper = [] )
    {
        $options = $settings;
        return $options;
    }
        
    public function wp_parse_args( $args, $defaults = [] ) {
            if ( is_object( $args ) ) {
                    $parsed_args = get_object_vars( $args );
            } elseif ( is_array( $args ) ) {
                    $parsed_args =& $args;
            } else {
                    self::wp_parse_str( $args, $parsed_args );
            }

            if ( is_array( $defaults ) && $defaults ) {
                    return array_merge( $defaults, $parsed_args );
            }
            return $parsed_args;
    }

    public function wp_parse_str( $string, &$array ) {
            parse_str( $string, $array );
            $array = Leo_Helper::apply_filters( 'wp_parse_str', $array );
    }
        
        
    public function renderProduct($settings = array(), $item)
    {
        $module = \Module::getInstanceByName('leoelements');
        $context = \Context::getContext();
        
        $settings = $this->wp_parse_args( $settings, $item );
        
        $data = array();
        if( $settings['source'] != 's' ){
            $data = $module->_prepProducts( $settings );
        }else{
            $data = $module->_prepProductsSelected( $settings );
        }
        
        if($data) {
            $products_for_template = $data;
            $context->smarty->assign(
                    array(
                            'leo_products'         => $products_for_template,
                            'elementprefix'       => 'single-product',
                            'theme_template_path' => _PS_THEME_DIR_ . 'templates/catalog/_partials/miniatures/product.tpl',
                            'settings' => $settings,

                    )
            );
            $template_file_name = 'module:/leoelements/views/templates/front/ApProductTab2.tpl';
            $out_put           = $context->smarty->fetch( $template_file_name );

            return $out_put;
        }
        
        return '';
    }
        
        
}