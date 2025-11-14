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

class Widget_LeoGenCode extends Widget_Base
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
        return 'LeoGenCode';
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
        return Leo_Helper::__( 'Generate Code', 'elementor' );
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
            return 'eicon-code';
    }
    
    public function get_keywords() {
            return [ 'leo', 'ap', 'code', 'LeoGenCode' ];
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
    protected function _register_controls() {
        
        $this->_register_content_controls();
        
    }

    protected function _register_content_controls()
    {
            $this->start_controls_section(
                    'section_title',
                    array(
                            'label' => Leo_Helper::__( 'Generate Code TPL', 'elementor' ),
                    )
            );
            
            $this->add_control(
                    'html',
                    [
                            'label' => '',
                            'type' => Controls_Manager::CODE,
                            'default' => '',
                            'placeholder' => Leo_Helper::__( 'Enter your code', 'elementor' ),
                            'show_label' => false,
                    ]
            );
            
            $this->add_control(
                    'form_id',
                    [
                            'label'   => 'form_id',
                            'type' => Controls_Manager::HIDDEN,
                            'render_type' => 'none',
                            'default' => '',
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
        $html = $this->get_settings_for_display( 'html' );
        $html = $this->generateFile( $html );
        $html = $this->loadFile($html);
        
        echo $html;
    }
    
    public function generateFile($html = '')
    {
        if ( Leo_Helper::is_admin() ) {
            # Backend
            return $html;
        }
        
        if ( Leo_Helper::is_admin() ) {
            return 'LeoGenCode';
        }
        
        // condia_id
        $form_id = $this->get_name() . '_' . ($this->get_raw_data())['id'];
        $file = _PS_MODULE_DIR_. 'leoelements/views/templates/front/LeoGenCode'.$form_id.'.tpl';
        $value = $html;
        
        $handle = fopen($file, 'w+');
        fwrite($handle, ($value));
        fclose($handle);
        
        try {
                $context = \Context::getContext();
                $context->smarty->assign(array(
                    'currency' => $context->controller->getTemplateVarCurrency(),
                    'tpl_dir'             => _PS_THEME_DIR_,            // for show_more button
                    'tpl_uri'             => _THEME_DIR_,
                    'link' => $context->link,
                    'leolink' => $context->link,
                    'customer' => $context->controller->getTemplateVarCustomer(),
                    'language' => $context->controller->objectPresenter->present($context->language),
                    'page' => $context->controller->getTemplateVarPage(),
                    'shop' => $context->controller->getTemplateVarShop(),
                    'urls' => $context->controller->getTemplateVarUrls(),
                    'configuration' => $context->controller->getTemplateVarConfiguration(),
                    
                ));
                $out_put           = $context->smarty->fetch( $file );
        } catch (Exception $exc) {
            $out_put = '
                <div class="elementor-alert elementor-alert-warning">'. $exc->getMessage() .'</div>
                ';
        }

        @unlink($file);
        
        
        if (!is_dir(_PS_MODULE_DIR_.'leoelements/gencode/')) {
            mkdir(_PS_MODULE_DIR_.'leoelements/gencode/', 0755, true);
        }
        
        Leo_Helper::writeFile(_PS_MODULE_DIR_.'leoelements/gencode/', $form_id . '.html', $out_put);
                
        return $out_put;
    }
    
    public function loadFile($html = '')
    {
        if ( !Leo_Helper::is_admin() ) {
            # Frontend
            return $html;
        }
        
        $form_id = $this->get_name() . '_' . ($this->get_raw_data())['id'];
        if (file_exists(_PS_MODULE_DIR_.'leoelements/gencode/'. $form_id . '.html')) {
            $html = \Tools::file_get_contents( _PS_MODULE_DIR_.'leoelements/gencode/'. $form_id . '.html');
        }else{
        }
        
        return $html;
    }
}