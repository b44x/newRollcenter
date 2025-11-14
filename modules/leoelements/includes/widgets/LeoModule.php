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

class Widget_LeoModule extends Widget_Base
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
        return 'LeoModule';
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
        return Leo_Helper::__( 'Module', 'elementor' );
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
            return 'eicon-parallax';
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
        return [ 'leo', 'ap', 'module', 'LeoModule'];
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
        $modules = $this->_getModules();
        
        $this->start_controls_section(
                'section_options',
                [
                        'label' => Leo_Helper::__( 'Module Options', 'elementor' ),
                ]
        );
        
        $this->add_control(
                'module',
                [
                        'label' => Leo_Helper::__('Module', 'elementor'),
                        'label_block' => true,
                        'type' => Controls_Manager::SELECT,
                        'default' => '0',
                        'options' => $modules,
                ]
        );
        
        $this->end_controls_section();
    }
    
    protected function _register_styling_controls()
    {
        $this->start_controls_section(
                'section_tabs_style',
                [
                        'label' => Leo_Helper::__( 'Tabs', 'elementor' ),
                        'type' => Controls_Manager::SECTION,
                        'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        
        $this->add_control(
                'section_tab_wrapper_heading',
                [
                        'label' => Leo_Helper::__('Tab', 'elementor'),
                        'type' => Controls_Manager::HEADING
                ]
        );
        
        $this->add_responsive_control(
                'tab_wrapper_align',
                [
                        'label' => Leo_Helper::__( 'Alignment', 'elementor' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                                'left' => [
                                        'title' => Leo_Helper::__( 'Left', 'elementor' ),
                                        'icon' => 'eicon-text-align-left',
                                ],
                                'center' => [
                                        'title' => Leo_Helper::__( 'Center', 'elementor' ),
                                        'icon' => 'eicon-text-align-center',
                                ],
                                'right' => [
                                        'title' => Leo_Helper::__( 'Right', 'elementor' ),
                                        'icon' => 'eicon-text-align-right',
                                ],
                                'justify' => [
                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
                                        'icon' => 'eicon-text-align-justify',
                                ],
                        ],
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .widget-tabs .widget-tabs-wrapper' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
		
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                        'name' => 'tab_wrapper_border',
                        'selector' => '{{WRAPPER}} .widget-tabs .widget-tabs-wrapper',
                ]
        );

        $this->add_responsive_control(
                'tab_wrapper_border_radius',
                [
                        'label' => Leo_Helper::__( 'Border Radius', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .widget-tabs .widget-tabs-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ]
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                        'name' => 'tab_wrapper_box_shadow',
                        'selector' => '{{WRAPPER}} .widget-tabs .widget-tabs-wrapper',
                ]
        );

        $this->add_control(
            'tab_wrapper_background_color',
            [
                'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-tabs .widget-tabs-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );
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
        if ( Leo_Helper::is_admin() ) {
                return;
        }
        
        $settings = $this->get_settings_for_display();
        $module_name = $settings['module'];
        $content = empty($settings['module']) ? '' : $this->_renderModule('displayViaLeoelements', [], $module_name);
        
        echo $content;
    }
    
    public function _renderModule($hook_name, $hook_args = array(), $module = null)
    {
        $res = '';
        try {
            $mod = \Module::getInstanceByName($module);

            if (\Validate::isLoadedObject($mod) && method_exists($mod, 'renderWidget')) {
                $res = $mod->renderWidget($hook_name, $hook_args);
            }
        } catch (\Exception $ex) {
            // TODO
        }
        return $res;
    }

    protected function _getModules()
    {
        $modules = array();
        $modules2 = array();
        
        $modules = [
            '0' => Leo_Helper::__('- Select Module -', 'elementor')
        ];
        
        $excludeModules = [ 'leoelements', 'appagebuilder', 'crazyelements', 'leocrazyextras', 'axoncreator',
                'leobootstrapmenu',
                'leoslideshow',
                'leofeature',
                'leoblog',
            ];
		
        $table = _DB_PREFIX_ . 'module';
        $excludeModules = implode("','", $excludeModules);
        
        $rows = \Db::getInstance()->executeS(
            "SELECT m.name FROM $table AS m " . \Shop::addSqlAssociation('module', 'm') .
            " WHERE m.active = 1 AND m.name NOT IN ('$excludeModules')"
        );

        
        foreach ($rows as $row) {
            try {
                $module = \Module::getInstanceByName($row['name']);

                if (\Validate::isLoadedObject($module) && (strtolower($module->author) == 'leotheme' || strtolower($module->author) == 'apollotheme')) {
                    $modules[$module->name] = 'AP - ' . $module->name;
                }elseif (\Validate::isLoadedObject($module) && method_exists($module, 'renderWidget') && $module->author !== 'Leotheme') {
                    $modules2[$module->name] = $module->name;
                }
            } catch (Exception $ex) {
                // TODO
            }
        }
        
        $modules += $modules2;
        
        return $modules;
    }
}