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

class Widget_LeoSlideshow extends Widget_Base
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
        return 'LeoSlideshow';
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
        return Leo_Helper::__( 'Leo Slideshow Module', 'elementor' );
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
            return 'eicon-slider-device';
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
        return [ 'leo', 'ap', 'slide', 'slideshow'];
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
                
        $module = \Module::getInstanceByName( 'leoslideshow' );
        $slideshows = $module->getAllSlides();
        if ($slideshows && count($slideshows) > 0) {
            foreach( $slideshows as $slideshow ) {
                $value = $slideshow['randkey'];
                $title = $slideshow['title'];
                $source[$value] = $title;
            }
        }
            
        $this->start_controls_section(
                'section_options',
                [
                        'label' => Leo_Helper::__( 'Slideshow Options', 'elementor' ),
                ]
        );
/*        
        $this->add_control(
                'source',
                [
                        'label' => Leo_Helper::__('Source of Slideshow', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'frontend_available' => true,
                        'default' => 'n',
                        'tablet_default' => 'n',
                        'mobile_default' => 'n',
                        'options' => $source,
                        'frontend_available' => true,
                ]
        );
*/
        $this->add_control(
                'screens',
                [
                        'label' => Leo_Helper::__('Preview', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'desktop',
                        'options' => [
                            'desktop' => 'Desktop',
                            'tablet' => 'Tablet',
                            'mobile' => 'Mobile',
                        ],
                ]
        );
        $this->add_control(
                'source__desktop',
                [
                        'label' => Leo_Helper::__('Slideshow for Desktop', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'n',
                        'options' => $source,
                        'condition' => [
//                            'screens[value]' => 'desktop',
                        ],
                ]
        );
        $this->add_control(
                'source__tablet',
                [
                        'label' => Leo_Helper::__('Slideshow for Tablet', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'n',
                        'options' => $source,
                        'frontend_available' => true,
                        'condition' => [
//                            'screens[value]' => 'tablet',
                        ],
                ]
        );
        $this->add_control(
                'source__mobile',
                [
                        'label' => Leo_Helper::__('Slideshow for Mobile', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'n',
                        'options' => $source,
                        'frontend_available' => true,
                        'condition' => [
//                            'screens[value]' => 'mobile',
                        ],
                ]
        );
        $link_leoslideshow = '';
        if(isset($GLOBALS['gb_leoelements']['url'])) {
            $link_leoslideshow = $GLOBALS['gb_leoelements']['url']['link_leoslideshow'];
        }
        
        $this->add_control(
                'anchor_note',
                [
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' => sprintf( 'Click to the link to manage slideshow <br/> <a href="%s" target="_blank"> Leo Slideshow Module</a> ', $link_leoslideshow ),
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
                'form_id' => 'form_5846351677680492',
                'slideshow_group'           => $settings['source__desktop'],       // Ex: '66b973e6e6e8d38f781384537e295392',
                'slideshow_group_tablet'    => $settings['source__tablet'],        // Ex: '66b973e6e6e8d38f781384537e295392',
                'slideshow_group_mobile'    => $settings['source__mobile'],        // Ex: '66b973e6e6e8d38f781384537e295392',
                'class' => 'LeoSlideshow',
                'override_folder' => '',
                'rtl' => '0',
            ),
            'homeSize' => array(
                'width' => '250',
                'height' => '250',
            ),
            'mediumSize' => array(
                'width' => '452',
                'height' => '452',
            ),
            'img_manu_dir' => '/task_live_editor/leo_1782_elements_free2/img/m/',
            'comparator_max_item' => '0',
            'compared_products' => array(),
            'tpl_dir' => 'D:\HOST\localhost\leo_tuanvu\task_live_editor\leo_1782_elements_free2/themes/classic/',
            'PS_CATALOG_MODE' => '0',
            'priceDisplay' => '1',
            'PS_STOCK_MANAGEMENT' => '1',
            'page_name' => 'index',
        );
        
        if (\Tools::getIsset('condia_id') && \Tools::getValue('condia_id'))
        {
//            if( isset($settings['screens']) && $settings['screens']=='desktop' ) {
//                $settings['source__desktop'] = $settings['source__desktop'];
//            } elseif( isset($settings['screens']) && $settings['screens']=='tablet' ) {
//                $settings['source__desktop'] = $settings['source__tablet'];
//            } elseif( isset($settings['screens']) && $settings['screens']=='mobile' ) {
//                $settings['source__desktop'] = $settings['source__mobile'];
//            }
            if( isset($settings['screens']) && $settings['screens']=='desktop' ) {
                $assign['formAtts']['slideshow_group'] = $settings['source__desktop'];
            } elseif( isset($settings['screens']) && $settings['screens']=='tablet' ) {
                $assign['formAtts']['slideshow_group'] = $settings['source__tablet'];
            } elseif( isset($settings['screens']) && $settings['screens']=='mobile' ) {
                $assign['formAtts']['slideshow_group'] = $settings['source__mobile'];
            }
        }
        
        if (\Module::isInstalled('leoslideshow') && \Module::isEnabled('leoslideshow')) {
            $id_shop = (int)\Context::getContext()->shop->id;
            $assign['formAtts']['isEnabled'] = true;
            $module = \Module::getInstanceByName( 'leoslideshow' );
            if ( (\Tools::getIsset('action') && \Tools::getValue('action') == 'elementor_ajax')
                    || (\Tools::getIsset('controller') && \Tools::getValue('controller') == 'action_element')
            )
            {
                # ADMIN || EDIT IN LEOELEMENT
                $module->load_from = 'leoelements_backend';
            } else {
                $module->load_from = 'leoelements_frontend';
            }
            
            if (\Context::getContext()->isTablet()) {
                $link_array = explode(',', $assign['formAtts']['slideshow_group_tablet']);
            } elseif (\Context::getContext()->isMobile()) {
                $link_array = explode(',', $assign['formAtts']['slideshow_group_mobile']);
            } else {
                $link_array = explode(',', $assign['formAtts']['slideshow_group']);
            }
            if ($link_array[0] == '') {
                $link_array = explode(',', $assign['formAtts']['slideshow_group']);
            }
            
            if ($link_array && !is_numeric($link_array['0'])) {
                $randkey_group = '';
                foreach ($link_array as $val) {
                    // validate module
                    $randkey_group .= ($randkey_group == '') ? "'".pSQL($val)."'" : ",'".pSQL($val)."'";
                }
                $where = ' WHERE randkey IN ('.$randkey_group.') AND id_shop = ' . (int)$id_shop;
                $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_leoslideshow_groups FROM `'._DB_PREFIX_.'leoslideshow_groups` '.$where);
                $where = '';
                
                if (is_array($result) && !empty($result)) {
                    foreach ($result as $slide) {
                        // validate module
                        $where .= ($where == '') ? $slide['id_leoslideshow_groups'] : ','.$slide['id_leoslideshow_groups'];
                    }
                    if (\Context::getContext()->isTablet()) {
                        $assign['formAtts']['slideshow_group_tablet'] = $where;
                        $assign['content_slider'] = $module->processHookCallBack($assign['formAtts']['slideshow_group_tablet']);
                    } elseif (\Context::getContext()->isMobile()) {
                        $assign['formAtts']['slideshow_group_mobile'] = $where;
                        $assign['content_slider'] = $module->processHookCallBack($assign['formAtts']['slideshow_group_mobile']);
                    } else {
                        $assign['formAtts']['slideshow_group'] = $where;
                        $assign['content_slider'] = $module->processHookCallBack($assign['formAtts']['slideshow_group']);
                    }
                } else {
                    $assign['formAtts']['isEnabled'] = false;
                    $assign['formAtts']['lib_has_error'] = true;
                    $assign['formAtts']['lib_error'] = 'Can not show LeoSlideShow via Leoelements. Please check that The Group of LeoSlideShow is exist.';
                }
            }
        } else {
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show LeoSlideShow via Leoelements. Please enable LeoSlideShow module.';
        }
        
        $context = \Context::getContext();
        
        if ($assign) {
            foreach ($assign as $key => $ass) {
                $context->smarty->assign(array($key => $ass));
            }
        }
        
        $template_file_name = 'module:/leoelements/views/templates/front/LeoSlideshow.tpl';
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
}