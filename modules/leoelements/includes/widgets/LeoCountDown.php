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

if ( ! defined( '_PS_VERSION_' ) )
{
    exit;
}

class Widget_LeoCountDown extends Widget_Base
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
        return 'LeoCountDown';
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
        return Leo_Helper::__( 'CountDown', 'elementor' );
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
            return 'eicon-countdown';
    }
    
    public function get_keywords() {
            return [ 'leo', 'ap', 'countdown', 'count', 'down' ];
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
                        'label' => Leo_Helper::__( 'Configuration', 'elementor' ),
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
                        'label' => Leo_Helper::__( 'Title', 'elementor' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => '',
                        'placeholder' => Leo_Helper::__( 'Title', 'elementor' ),
                        'label_block' => true,
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
                'type',
                [
                        'label'   => Leo_Helper::__( 'Type', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                                'horizontal' => Leo_Helper::__( 'Horizontal', 'elementor' ),
                                'vertical' => Leo_Helper::__( 'Vertical', 'elementor' ),
                        ],
                        'default' => 'vertical',
                        'prefix_class' => 'LeoCountDown-type-',
                ]
        ); 
        
        $this->add_responsive_control(
                'align',
                [
                        'label'        => Leo_Helper::__( 'Alignment', 'elementor' ),
                        'type'         => Controls_Manager::CHOOSE,
                        'options'      => [
                                'left'    => [
                                        'title' => Leo_Helper::__( 'Left', 'elementor' ),
                                        'icon'  => 'eicon-h-align-left',
                                ],
                                'center'  => [
                                        'title' => Leo_Helper::__( 'Center', 'elementor' ),
                                        'icon'  => 'eicon-h-align-center',
                                ],
                                'right'   => [
                                        'title' => Leo_Helper::__( 'Right', 'elementor' ),
                                        'icon'  => 'eicon-h-align-right',
                                ],

                        ],
                        'default'      => 'left',
                        'prefix_class' => 'leo%s-align-',
                        'separator' => 'after',
                ]
        );
        
        $this->add_control(
                'time_from',
                array(
                        'label'          => Leo_Helper::__( 'Time From', 'elecounter' ),
                        'type'           => Controls_Manager::DATE_TIME,
                        'picker_options' => array(
                            'enableTime' => true,
                        ),
                        'default'        => '2022-08-01',
                )
        );
        
        $this->add_control(
                'time_to',
                array(
                        'label'          => Leo_Helper::__( 'Time To', 'elecounter' ),
                        'type'           => Controls_Manager::DATE_TIME,
                        'picker_options' => array(
                            'enableTime' => true,
                        ),
                        'default'        => '2022-12-30',
                )
        );
                
//        $this->add_control(
//                'new_tab',
//                [
//                        'label' => Leo_Helper::__( 'Open new tab', 'elementor' ),
//                        'type' => Controls_Manager::SWITCHER,
//                        'default' => 'on',
//                        'label_on' => 'Yes',
//                        'label_off' => 'No',
//                        'return_value' => 'new_tab',
//                ]
//        );
        
        $this->add_control(
                'link_label',
                [
                        'label'   => Leo_Helper::__( 'Link Label', 'elementor' ),
                        'type'    => Controls_Manager::TEXTAREA,
                        'rows'    => '1',
                        'default' => '',
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
        
        $this->add_control(
                'description',
                [
                        'label' => Leo_Helper::__( 'Description', 'elementor' ),
                        'type' => Controls_Manager::TEXTAREA,
                        'rows' => '3',
                        'default' => '',
                ]
        );
        
        
        $this->add_control(
                'show_day',
                array(
                        'label'              => Leo_Helper::__( 'Show Day', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => false,
                )
        );
        
        $this->add_control(
                'show_hour',
                array(
                        'label'              => Leo_Helper::__( 'Show Hour', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => false,
                )
        );
        
        $this->add_control(
                'show_minute',
                array(
                        'label'              => Leo_Helper::__( 'Show Minute', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => false,
                )
        );
        
        $this->add_control(
                'show_second',
                array(
                        'label'              => Leo_Helper::__( 'Show Second', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => false,
                )
        );
        
        $this->end_controls_section();
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
        $this->add_control(
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
                                'justify' => [
                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
                                        'icon' => 'eicon-text-align-justify',
                                ],
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
                                '{{WRAPPER}} .title_block' => 'color: {{VALUE}};',
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
        $this->add_control(
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
                                'justify' => [
                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
                                        'icon' => 'eicon-text-align-justify',
                                ],
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
                'section_style_show_link_label',
                [
                        'label' => Leo_Helper::__( 'Link Label', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
                'link_label_align',
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
                        'default' => 'left',
                        'selectors' => [
                                '{{WRAPPER}} .ap-countdown-link a' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'link_label_bottom_space',
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
                                '{{WRAPPER}} .ap-countdown-link a' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'link_label_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .ap-countdown-link a' => 'color: {{VALUE}};',
                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'link_label_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .ap-countdown-link a',
                ]
        );
        $this->add_responsive_control(
                'link_label_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .ap-countdown-link a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/

        $this->start_controls_section(
                'section_style_show_description',
                [
                        'label' => Leo_Helper::__( 'Show Description', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
                'description_align',
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
                        'default' => 'left',
                        'selectors' => [
                                '{{WRAPPER}} .description-widget' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'description_bottom_space',
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
                                '{{WRAPPER}} .description-widget' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'description_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .description-widget, {{WRAPPER}} .description-widget *' => 'color: {{VALUE}};',
                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'description_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .description-widget',
                ]
        );
        $this->add_responsive_control(
                'description_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .description-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_show_day',
                [
                        'label' => Leo_Helper::__( 'Show Day', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'show_day' => '1',
                        ],
                ]
        );
//        $this->add_control(
//                'show_day_align',
//                [
//                        'label' => Leo_Helper::__( 'Alignment', 'elementor' ),
//                        'type' => Controls_Manager::CHOOSE,
//                        'options' => [
//                                'left' => [
//                                        'title' => Leo_Helper::__( 'Left', 'elementor' ),
//                                        'icon' => 'eicon-text-align-left',
//                                ],
//                                'center' => [
//                                        'title' => Leo_Helper::__( 'Center', 'elementor' ),
//                                        'icon' => 'eicon-text-align-center',
//                                ],
//                                'right' => [
//                                        'title' => Leo_Helper::__( 'Right', 'elementor' ),
//                                        'icon' => 'eicon-text-align-right',
//                                ],
//                                'justify' => [
//                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                        'icon' => 'eicon-text-align-justify',
//                                ],
//                        ],
//                        'default' => 'left',
//                        'selectors' => [
//                                '{{WRAPPER}} .show_day' => 'text-align: {{VALUE}};',
//                        ],
//                ]
//        );
        $this->add_responsive_control(
                'show_day_bottom_space',
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
                                '{{WRAPPER}} .show_day' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'show_day_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .show_day, {{WRAPPER}} .show_day *' => 'color: {{VALUE}};',
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
                        'name' => 'show_day_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .show_day',
                ]
        );
        $this->add_responsive_control(
                'show_day_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .show_day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        /************************************************************/
        $this->start_controls_section(
                'section_style_show_hour',
                [
                        'label' => Leo_Helper::__( 'Show Hour', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'show_hour' => '1',
                        ],
                ]
        );
//        $this->add_control(
//                'show_hour_align',
//                [
//                        'label' => Leo_Helper::__( 'Alignment', 'elementor' ),
//                        'type' => Controls_Manager::CHOOSE,
//                        'options' => [
//                                'left' => [
//                                        'title' => Leo_Helper::__( 'Left', 'elementor' ),
//                                        'icon' => 'eicon-text-align-left',
//                                ],
//                                'center' => [
//                                        'title' => Leo_Helper::__( 'Center', 'elementor' ),
//                                        'icon' => 'eicon-text-align-center',
//                                ],
//                                'right' => [
//                                        'title' => Leo_Helper::__( 'Right', 'elementor' ),
//                                        'icon' => 'eicon-text-align-right',
//                                ],
//                                'justify' => [
//                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                        'icon' => 'eicon-text-align-justify',
//                                ],
//                        ],
//                        'default' => 'left',
//                        'selectors' => [
//                                '{{WRAPPER}} .show_hour' => 'text-align: {{VALUE}};',
//                        ],
//                ]
//        );
        $this->add_responsive_control(
                'show_hour_bottom_space',
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
                                '{{WRAPPER}} .show_hour' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'show_hour_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .show_hour, {{WRAPPER}} .show_hour *' => 'color: {{VALUE}};',
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
                        'name' => 'show_hour_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .show_hour',
                ]
        );
        $this->add_responsive_control(
                'show_hour_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .show_hour' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        /************************************************************/
        $this->start_controls_section(
                'section_style_show_minute',
                [
                        'label' => Leo_Helper::__( 'Show Minute', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                            'show_minute' => '1',
                        ],
                ]
        );
//        $this->add_control(
//                'show_minute_align',
//                [
//                        'label' => Leo_Helper::__( 'Alignment', 'elementor' ),
//                        'type' => Controls_Manager::CHOOSE,
//                        'options' => [
//                                'left' => [
//                                        'title' => Leo_Helper::__( 'Left', 'elementor' ),
//                                        'icon' => 'eicon-text-align-left',
//                                ],
//                                'center' => [
//                                        'title' => Leo_Helper::__( 'Center', 'elementor' ),
//                                        'icon' => 'eicon-text-align-center',
//                                ],
//                                'right' => [
//                                        'title' => Leo_Helper::__( 'Right', 'elementor' ),
//                                        'icon' => 'eicon-text-align-right',
//                                ],
//                                'justify' => [
//                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                        'icon' => 'eicon-text-align-justify',
//                                ],
//                        ],
//                        'default' => 'left',
//                        'selectors' => [
//                                '{{WRAPPER}} .show_minute' => 'text-align: {{VALUE}};',
//                        ],
//                ]
//        );
        $this->add_responsive_control(
                'show_minute_bottom_space',
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
                                '{{WRAPPER}} .show_minute' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'show_minute_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .show_minute, {{WRAPPER}} .show_minute *' => 'color: {{VALUE}};',
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
                        'name' => 'show_minute_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .show_minute',
                ]
        );
        $this->add_responsive_control(
                'show_minute_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .show_minute' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        /************************************************************/
        $this->start_controls_section(
                'section_style_show_second',
                [
                        'label' => Leo_Helper::__( 'Show Second', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                            'show_second' => '1',
                        ],
                ]
        );
//        $this->add_control(
//                'show_second_align',
//                [
//                        'label' => Leo_Helper::__( 'Alignment', 'elementor' ),
//                        'type' => Controls_Manager::CHOOSE,
//                        'options' => [
//                                'left' => [
//                                        'title' => Leo_Helper::__( 'Left', 'elementor' ),
//                                        'icon' => 'eicon-text-align-left',
//                                ],
//                                'center' => [
//                                        'title' => Leo_Helper::__( 'Center', 'elementor' ),
//                                        'icon' => 'eicon-text-align-center',
//                                ],
//                                'right' => [
//                                        'title' => Leo_Helper::__( 'Right', 'elementor' ),
//                                        'icon' => 'eicon-text-align-right',
//                                ],
//                                'justify' => [
//                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                        'icon' => 'eicon-text-align-justify',
//                                ],
//                        ],
//                        'default' => 'left',
//                        'selectors' => [
//                                '{{WRAPPER}} .show_second' => 'text-align: {{VALUE}};',
//                        ],
//                ]
//        );
        $this->add_responsive_control(
                'show_second_bottom_space',
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
                                '{{WRAPPER}} .show_second' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'show_second_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .show_second, {{WRAPPER}} .show_second *' => 'color: {{VALUE}};',
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
                        'name' => 'show_second_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .show_second',
                ]
        );
        $this->add_responsive_control(
                'show_second_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .show_second' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            
            $context = \Context::getContext();
            
            $settings['lib_has_error'] = '';
            $settings['lib_error'] = '';
            $settings['class'] = '';
            $settings['form_id'] = $this->get_name() . '_' . Leo_Helper::getRandomNumber();
            
            
            $from = strtotime($settings['time_from']);
            $now = time();
            $end = strtotime($settings['time_to']);

            if (($from <= $now) && ($now < $end)) {
                $start = true;
            } else {
                $start = false;
            }
            
            if ($start) {
                # RUNNING
                $settings['time_to'] = str_replace('-', '/', $settings['time_to']);
                $settings['active'] = 1;
            } else {
                $settings['active'] = 0;
            }

            $out_put  = '';
            
//            $settings['DisplayFormat'] = '<li class="z-depth-1">';
//            $settings['DisplayFormat'] .= '%%D%%<span>+text_d+';
//            $settings['DisplayFormat'] .= '</li>';
            
            $context->smarty->assign(
                    array(
                        'formAtts' => $settings,
                        'apLiveEdit' => '',
                        'apLiveEditEnd' => '',
                        'settings' => $settings,
                    )
            );
                
                
            $template_file_name = 'module:/leoelements/views/templates/front/LeoCountDown.tpl';

            $out_put           .= $context->smarty->fetch( $template_file_name );

            echo $out_put;
    }

}