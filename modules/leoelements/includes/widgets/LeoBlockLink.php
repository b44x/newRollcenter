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

class Widget_LeoBlockLink extends Widget_Base
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
        return 'LeoBlockLink';
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
        return Leo_Helper::__( 'Block Link', 'elementor' );
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
            return 'eicon-link';
    }
    
    public function get_keywords() {
            return [ 'leo', 'ap', 'block', 'link', 'blocklink' ];
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
                        'label' => Leo_Helper::__( 'Block Link', 'elementor' ),
                )
        );
        
        $this->add_control(
                'show_title',
                array(
                        'label'              => Leo_Helper::__( 'Show Title', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '0',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => false,
                )
        );

        $this->add_control(
                'title',
                [
                        'show_label' => false,
                        'label'   => Leo_Helper::__( 'Title', 'elementor' ),
                        'type'    => Controls_Manager::TEXTAREA,
                        'rows'    => '1',
                        'default' => Leo_Helper::__( 'Title', 'elementor' ),
                        'condition' => [
                                'show_title' => '1',
                        ]
                ]
        );
        
        $this->add_control(
                'show_sub_title',
                array(
                        'label'              => Leo_Helper::__( 'Show Sub Title', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '0',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => false,
                )
        );
        
        $this->add_control(
                'sub_title',
                [
                        'show_label' => false,
                        'label' => Leo_Helper::__( 'Sub Title', 'elementor' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => '',
                        'placeholder' => Leo_Helper::__( 'Sub Title', 'elementor' ),
                        'label_block' => true,
                        'condition' => [
                                'show_sub_title' => '1',
                        ]
                ]
        );

        $this->add_control(
                'link',
                [
                        'label' => Leo_Helper::__( 'Link', 'elementor' ),
                        'type' => Controls_Manager::URL,
                        'autocomplete' => false,
                        'default' => [
                                'url' => '',
                        ],
                ]
        );

        
//        $this->add_control(
//                'show_title',
//                array(
//                        'label'              => Leo_Helper::__( 'Show Title', 'elementor' ),
//                        'type'               => Controls_Manager::SELECT,
//                        'default'            => '0',
//                        'options'            => array(
//                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
//                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
//                        ),
//                        'frontend_available' => false,
//                )
//        );
        $this->add_control(
                'type',
                array(
                        'label'             => Leo_Helper::__( 'Type', 'elementor' ),
                        'type'              => Controls_Manager::SELECT,
                        'options'           => array(
                            'vertical'      => Leo_Helper::__( 'Vertical', 'elementor' ),
                            'horizontal'    => Leo_Helper::__( 'Horizontal', 'elementor' ),
                        ),
                        'default' => 'vertical',
//                        'prefix_class' => 'LeoBlockLink-type-',
                        'style_transfer' => true,
                        'frontend_available' => false,
                )
        );

        $this->add_control(
                'toggle',
                [
                        'label'   => Leo_Helper::__( 'Toggle', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                                'none' => Leo_Helper::__( 'None', 'elementor' ),
                                'all' => Leo_Helper::__( 'Toggle', 'elementor' ),
                                'mobile' => Leo_Helper::__( 'Toggle on mobile', 'elementor' ),
                        ],
                        'default' => 'none',
                        'prefix_class' => 'LeoBlockLink-toggle-',
                ]
        );

//        $this->add_responsive_control(
//                'align',
//                [
//                        'label'        => Leo_Helper::__( 'Alignment', 'elementor' ),
//                        'type'         => Controls_Manager::CHOOSE,
//                        'options'      => [
//                                'left'    => [
//                                        'title' => Leo_Helper::__( 'Left', 'elementor' ),
//                                        'icon'  => 'eicon-h-align-left',
//                                ],
//                                'center'  => [
//                                        'title' => Leo_Helper::__( 'Center', 'elementor' ),
//                                        'icon'  => 'eicon-h-align-center',
//                                ],
//                                'right'   => [
//                                        'title' => Leo_Helper::__( 'Right', 'elementor' ),
//                                        'icon'  => 'eicon-h-align-right',
//                                ],
//
//                        ],
//                        'default'      => 'left',
//                        'prefix_class' => 'leo%s-align-',
//                        'separator' => 'after',
//                ]
//        );
            
        $repeater = new Repeater();
        $repeater->add_control(
                'item_title',
                [
                        'label' => Leo_Helper::__( 'Item Title', 'elementor' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => Leo_Helper::__( 'Item Title', 'elementor' ),
                        'placeholder' => Leo_Helper::__( 'Item Title', 'elementor' ),
                        'label_block' => true,
                ]
        );
                
        $repeater->add_control(
                'item_type',
                [
                    'label' => Leo_Helper::__('Item Type', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'url',
                    'options' => [
                        'url' => 'URL',
                        'category' => 'Category',
                        'cms' => 'CMS',
                        'page' => 'Page Controller',
                    ],
                ]
        );

        $repeater->add_control(
                'item_link',
                [
                        'label' => Leo_Helper::__( 'Item Link', 'elementor' ),
                        'type' => Controls_Manager::URL,
                        'autocomplete' => false,
                        'default'     => [
                                'url' => '#',
                        ],
                        'placeholder' => Leo_Helper::__( 'https://your-link.com', 'elementor' ),
                        'show_label' => false,
                        'condition' => [
                            'item_type' => 'url',
                        ]
                ]
        );
                
        $module = \Module::getInstanceByName('leoelements');
        $context = \Context::getContext();
        $categoriesSource = $module->getCategories();
        $categoriesDefault = $categoriesSource ? key($categoriesSource) : '';
        $repeater->add_control(
                'item_category',
                [
                    'label' => Leo_Helper::__('Select category', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => $categoriesDefault,
                    'options' => $categoriesSource,
                    'condition' => [
                        'item_type' => 'category',
                    ]
                ]
        );
                
        $cms = $module->getCms();
        $cms_default = $cms ? key($cms) : '';

        $repeater->add_control(
                'item_cms',
                [
                    'label' => Leo_Helper::__('Select CMS', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => $cms_default,
                    'options' => $cms,
                    'condition' => [
                        'item_type' => 'cms',
                    ]
                ]
        );
                
        $page_controller = $module->getPages();
        $page_default = $page_controller ? key($page_controller) : '';

        $repeater->add_control(
                'item_page',
                [
                    'label' => Leo_Helper::__('Page', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => $page_default,
                    'options' => $page_controller,
                    'condition' => [
                        'item_type' => 'page',
                    ]
                ]
        );


        $repeater->add_control(
                'item_page_param',
                [
                        'label'   => Leo_Helper::__( 'Parameter of page', 'elementor' ),
                        'type'    => Controls_Manager::TEXTAREA,
                        'rows'    => '1',
                        'default' => '',
                        'condition' => [
                            'item_type' => 'page',
                        ],
                        'description'        => Leo_Helper::__( '?paramA=1&amp;paramB=2', 'elementor' ),
                ]
        );
                
                
        $this->add_control(
                'items',
                [
                        'label' => Leo_Helper::__( 'Items', 'elementor' ),
                        'type' => Controls_Manager::REPEATER,
                        'fields' => $repeater->get_controls(),
                        'default' => [
                                [
                                        'item_title' => Leo_Helper::__( 'Item #1', 'elementor' ),
                                        'item_link' => '#',
                                ],
                                [
                                        'item_title' => Leo_Helper::__( 'Item #2', 'elementor' ),
                                        'item_link' => '#',
                                ],
                        ],
                        'title_field' => '{{{ item_title }}}',
                ]
        );

        $this->end_controls_section();
//        $this->start_controls_section(
//                'section_style_title',
//                [
//                        'label' => Leo_Helper::__( 'Title', 'elementor' ),
//                        'tab'   => Controls_Manager::TAB_STYLE,
//                ]
//        );
//        $this->add_responsive_control(
//                'title_bottom_space',
//                [
//                        'label' => Leo_Helper::__( 'Spacing', 'elementor' ),
//                        'type' => Controls_Manager::SLIDER,
//                        'range' => [
//                                'px' => [
//                                        'min' => 0,
//                                        'max' => 100,
//                                ],
//                        ],
//                        'selectors' => [
//                                '{{WRAPPER}} .title_block' => 'margin-bottom: {{SIZE}}{{UNIT}};',
//                        ],
//                ]
//        );
//        $this->add_control(
//                'title_color',
//                [
//                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
//                        'type' => Controls_Manager::COLOR,
//                        'default' => '',
//                        'selectors' => [
//                                '{{WRAPPER}} .title_block' => 'color: {{VALUE}};',
//                        ],
////                        'scheme' => [
////                                'type' => Scheme_Color::get_type(),
////                                'value' => Scheme_Color::COLOR_1,
////                        ],
//                ]
//        );
//        $this->add_group_control(
//                Group_Control_Typography::get_type(),
//                [
//                        'name' => 'title_typography',
//                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
//                        'selector' => '{{WRAPPER}} .title_block',
//                ]
//        );
//        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_show_title',
                [
                        'label' => Leo_Helper::__( 'Show Title', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'show_title' => '1',
//                                'title!' => '',
                        ],
                ]
        );
        $this->add_responsive_control(
                'title_align',
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
//                                'justify' => [
//                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                        'icon' => 'eicon-text-align-justify',
//                                ],
                        ],
                        'default' => 'left',
                        'selectors' => [
                                '{{WRAPPER}} .title_block' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'title_bottom_space',
                [
                        'label' => Leo_Helper::__( 'Spacing', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                                'px' => [
                                        'min' => 0,
                                        'max' => 100,
                                ],
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .title_block' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'title_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .title_block, {{WRAPPER}} .title_block *' => 'color: {{VALUE}};',
                        ],
//                        'scheme' => [
//                                'type' => Scheme_Color::get_type(),
//                                'value' => Scheme_Color::COLOR_1,
//                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'title_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .title_block',
                ]
        );
        $this->add_responsive_control(
                'title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .title_block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_show_sub_title',
                [
                        'label' => Leo_Helper::__( 'Show Sub Title', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'show_sub_title' => '1',
//                                'sub_title!' => '',
                        ],
                ]
        );
        $this->add_responsive_control(
                'sub_title_align',
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
//                                'justify' => [
//                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                        'icon' => 'eicon-text-align-justify',
//                                ],
                        ],
                        'default' => 'left',
                        'selectors' => [
                                '{{WRAPPER}} .sub-title-widget' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'sub_title_bottom_space',
                [
                        'label' => Leo_Helper::__( 'Spacing', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                                'px' => [
                                        'min' => 0,
                                        'max' => 100,
                                ],
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .sub-title-widget' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'sub_title_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .sub-title-widget, {{WRAPPER}} .sub-title-widget *' => 'color: {{VALUE}};',
                        ],
//                        'scheme' => [
//                                'type' => Scheme_Color::get_type(),
//                                'value' => Scheme_Color::COLOR_1,
//                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'sub_title_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .sub-title-widget',
                ]
        );
        $this->add_responsive_control(
                'sub_title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .sub-title-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_item_show_sub_title',
                [
                        'label' => Leo_Helper::__( 'Items', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
//                        'condition' => [
//                                'show_title' => '1',
//                        ],
                ]
        );
        $this->add_responsive_control(
                'item_sub_title_align',
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
                            
//                                'justify' => [
//                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                        'icon' => 'eicon-text-align-justify',
//                                ],
                        ],
                        'default' => 'left',
                        'selectors' => [
                                '{{WRAPPER}} .list-items, {{WRAPPER}} .list-items *' => 'text-align: {{VALUE}}; justify-content: {{VALUE}}',
//                                '{{WRAPPER}} .list-items' => 'text-align: {{VALUE}}; justify-content: {{VALUE}}',
                        ],
                ]
        );
        $this->add_responsive_control(
                'item_sub_title_bottom_space',
                [
                        'label' => Leo_Helper::__( 'Spacing', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                                'px' => [
                                        'min' => 0,
                                        'max' => 100,
                                ],
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li.vertical:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
                                '{{WRAPPER}} .linklist-menu li.vertical:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2); padding-top: 0px',
                            
                                '{{WRAPPER}} .linklist-menu li.horizontal' => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2)',
                                '{{WRAPPER}} .linklist-menu ul.horizontal' => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2)',
                                'body:not(.rtl) {{WRAPPER}} .linklist-menu li.horizontal:after' => 'right: calc(-{{SIZE}}{{UNIT}}/2)',
                        ],
                ]
        );
        
    $this->start_controls_tabs( 'title_button_style' );
        $this->start_controls_tab(
                'item_sub_title_button_normal',
                [
                        'label' => Leo_Helper::__( 'Normal', 'elementor' ),
                ]
        );
        $this->add_control(
                'item_sub_title_color',
                [
                        'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu, {{WRAPPER}} .linklist-menu *' => 'color: {{VALUE}};',
                        ],
//                        'scheme' => [
//                                'type' => Scheme_Color::get_type(),
//                                'value' => Scheme_Color::COLOR_1,
//                        ],
                ]
        );
        $this->end_controls_tab();
        
        $this->start_controls_tab(
                'item_sub_title_button_hover',
                [
                        'label' => Leo_Helper::__( 'Hover', 'elementor' ),
                ]
        );
        $this->add_control(
            'item_sub_title_hover_color',
            [
                    'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                            '{{WRAPPER}} .linklist-menu:hover, {{WRAPPER}} .linklist-menu *:hover' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .linklist-menu:focus, {{WRAPPER}} .linklist-menu *:focus' => 'color: {{VALUE}};',
                    ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        
//        $this->add_control(
//                'item_sub_title_color',
//                [
//                        'label' => Leo_Helper::__( 'Color2', 'elementor' ),
//                        'type' => Controls_Manager::COLOR,
//                        'default' => '',
//                        'selectors' => [
//                                '{{WRAPPER}} .linklist-menu, {{WRAPPER}} .linklist-menu *' => 'color: {{VALUE}};',
//                        ],
////                        'scheme' => [
////                                'type' => Scheme_Color::get_type(),
////                                'value' => Scheme_Color::COLOR_1,
////                        ],
//                ]
//        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'item_sub_title_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .linklist-menu li',  /* Cuong yeu cau - #76 trong DOC */
                ]
        );
        $this->add_responsive_control(
                'item_sub_title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        
        
        $this->add_control(
                'divider',
                [
                        'label' => Leo_Helper::__( 'Divider', 'elementor' ),
                        'type' => Controls_Manager::SWITCHER,
                        'label_off' => Leo_Helper::__( 'Off', 'elementor' ),
                        'label_on' => Leo_Helper::__( 'On', 'elementor' ),
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li:not(:last-child):after' => 'content: ""',
                        ],
                        'separator' => 'before',
                ]
        );
        $this->add_control(
                'divider_style',
                [
                        'label' => Leo_Helper::__( 'Style', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                                'solid' => Leo_Helper::__( 'Solid', 'elementor' ),
                                'double' => Leo_Helper::__( 'Double', 'elementor' ),
                                'dotted' => Leo_Helper::__( 'Dotted', 'elementor' ),
                                'dashed' => Leo_Helper::__( 'Dashed', 'elementor' ),
                        ],
                        'default' => 'solid',
                        'condition' => [
                                'divider' => 'yes',
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li.horizontal:not(:last-child):after' => 'border-left-style: {{VALUE}}',
                            
                                '{{WRAPPER}} .linklist-menu li.vertical:not(:last-child):after' => 'border-top-style: {{VALUE}}',
                                '{{WRAPPER}} .linklist-menu li.vertical:not(:last-child):after' => 'border-bottom-style: {{VALUE}}',
//                                '{{WRAPPER}} .linklist-menu li:not(:last-child):after' => 'border-left-style: {{VALUE}}',
                        ],
                ]
        );

        $this->add_control(
                'divider_weight',
                [
                        'label' => Leo_Helper::__( 'Weight', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'default' => [
                                'size' => 1,
                        ],
                        'range' => [
                                'px' => [
                                        'min' => 1,
                                        'max' => 20,
                                ],
                        ],
                        'condition' => [
                                'divider' => 'yes',
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li.vertical:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
                                '{{WRAPPER}} .linklist-menu li.vertical:not(:last-child):after' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                            
                                '{{WRAPPER}} .linklist-menu li.horizontal:not(:last-child):after' => 'border-left-width: {{SIZE}}{{UNIT}}',
                    
//                                    '{{WRAPPER}} ul:not(.elementor-inline-items) .li:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
//                                    '{{WRAPPER}} .ul .li:not(:last-child):after' => 'border-left-width: {{SIZE}}{{UNIT}}',
                        ],
                ]
        );

        $this->add_control(
                'divider_width',
                [
                        'label' => Leo_Helper::__( 'Width', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'default' => [
                                'unit' => '%',
                        ],
                        'condition' => [
                                'divider' => 'yes',
                                'type' => 'vertical',
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
                        ],
                ]
        );

        $this->add_control(
                'divider_height',
                [
                        'label' => Leo_Helper::__( 'Height', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ '%', 'px' ],
                        'default' => [
                                'unit' => '%',
                        ],
                        'range' => [
                                'px' => [
                                        'min' => 1,
                                        'max' => 100,
                                ],
                                '%' => [
                                        'min' => 1,
                                        'max' => 100,
                                ],
                        ],
                        'condition' => [
                                'divider' => 'yes',
                                'type' => 'horizontal',
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );

        $this->add_control(
                'divider_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '#ddd',
                        'scheme' => [
                                'type' => Scheme_Color::get_type(),
                                'value' => Scheme_Color::COLOR_3,
                        ],
                        'condition' => [
                                'divider' => 'yes',
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .linklist-menu li:not(:last-child):after' => 'border-color: {{VALUE}}',
                        ],
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
    protected function render() {

            if ( Leo_Helper::is_admin() ) {
                    return;
            }

            $settings = $this->get_settings_for_display();
            $items = $this->get_settings_for_display( 'items' );
            
            $context = \Context::getContext();
            
            foreach ($items as $key => &$item) {
                if($item['item_type'] == 'category')
                {
                    $item_link = array();
                    $item_link['url'] = '';
                    $item_link['is_external'] = 0;
                    $item_link['nofollow'] = 0;
                    
                    $item_link['url'] = $this->getCategoryLink($item, $settings);
                    
                    $items[$key]['item_link'] = $item_link;
                    
                }elseif ($item['item_type'] == 'cms') {
                    $item_link = array();
                    $item_link['url'] = '';
                    $item_link['is_external'] = 0;
                    $item_link['nofollow'] = 0;
                    
                    $item_link['url'] = $this->getCMSLink($item, $settings);
                    
                    $items[$key]['item_link'] = $item_link;
                    
                }elseif ($item['item_type'] == 'page') {
                    $item_link = array();
                    $item_link['url'] = '';
                    $item_link['is_external'] = 0;
                    $item_link['nofollow'] = 0;
                    
                    $item_link['url'] = $this->getPageLink($item, $settings);
                    
                    $items[$key]['item_link'] = $item_link;
                }
            }
            
            
            $out_put  = '';
            
            $context->smarty->assign(
                    array(
                            'items' => $items,
                            'settings' => $settings,
                            'formAtts' => $settings,

                    )
            );
                
                
            $template_file_name = 'module:/leoelements/views/templates/front/LeoBlockLink.tpl';

            $out_put           .= $context->smarty->fetch( $template_file_name );

            echo $out_put;
    }
    
    public function getCategoryLink($item, $settings)
    {
        $url = '';
        if($item['item_type'] == 'category')
        {
            $str = explode('_', $item['item_category']);
            $id = (int)$str[1];
            
            $category = new \Category($id, \Context::getContext()->language->id, \Context::getContext()->shop->id);
            if ($category->id) {
                $url = \Context::getContext()->link->getCategoryLink($category);
            }
        }
        
        return $url;
    }
    
    public function getCMSLink($item, $settings)
    {
        $url = '';
        if($item['item_type'] == 'cms')
        {
            $id = $item['item_cms'];
            
            $cms = new \CMS($id, \Context::getContext()->language->id, \Context::getContext()->shop->id);
            if ($cms->id) {
                $url = \Context::getContext()->link->getCMSLink($cms);
            }
        }
        return $url;
    }
    
    public function getPageLink($item, $settings)
    {
        $url = '';
        
        if($item['item_type'] == 'page')
        {
            $id = $item['item_page'];
            $param =  $item['item_page_param'];
            
            $url = \Context::getContext()->link->getPageLink( $id, null, \Context::getContext()->language->id, array());
            $url .= $param;
        }
        
        return $url;
    }
    
}