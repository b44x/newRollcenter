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
	exit;
}

class Widget_LeoInstagram extends Widget_Base
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
        return 'LeoInstagram';
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
        return Leo_Helper::__( 'Instagram', 'elementor' );
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
            return 'eicon-carousel';
    }
    
    public function get_keywords() {
            return [ 'leo', 'ap', 'Instagram', 'carousel', 'blockcarousel' ];
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
                        'label'   => Leo_Helper::__( 'Sub Title', 'elementor' ),
                        'type'    => Controls_Manager::TEXTAREA,
                        'rows'    => '1',
                        'default' => '',
                        'condition' => [
                                'show_sub_title' => '1',
                        ]
                ]
        );
        
        $this->add_control(
                'accordion_type',
                [
                        'label' => Leo_Helper::__('Accordion Type', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'frontend_available' => true,
                        'default' => 'full',
                        'tablet_default' => 'full',
                        'mobile_default' => 'full',
                        'options' => [
                            'full' => 'Always Full',
                            'accordion' => 'Always Accordion',
                            'accordion_small_screen' => 'Accordion at small screen'
                        ],
                        'frontend_available' => true,
                ]
        );
        
        $this->add_control(
                'access_token',
                [
                        'label'   => Leo_Helper::__( 'Access Token', 'elementor' ),
                        'type'    => Controls_Manager::TEXTAREA,
                        'rows'    => '5',
                        'default' => 'IGQVJVUlJvT2FZAWVZAmeEJuX0YxeXJLZAERIdi1NU3N5bE1vMDE3SHJ3ZA3pkd0o4V294S21ETGlDanBRN2wzZA2xub3BMeFUzYlR0di1QRnhwQ0p1SnhiX052Y1F3MUgwdDRDTGlnUGxOUUU0ajg4VUFaeQZDZD',
                ]
        );
        
        $this->add_control(
                'links',
                [
                        'label'   => Leo_Helper::__( 'Links', 'elementor' ),
                        'type'    => Controls_Manager::TEXTAREA,
                        'rows'    => '1',
                        'default' => '',
                ]
        );
        
        $this->add_control(
                'manu_ids',
                [
                        'label'       => Leo_Helper::__( 'Select', 'elementor' ),
                        'type'        => Controls_Manager::AUTOCOMPLETE,
                        'search'      => 'leo_get_manu_by_query',
                        'render'      => 'leo_get_manu_title_by_id',
                        'multiple'    => true,
                        'label_block' => true,
                        'condition' => [
                                'source' => 's',
                        ]
                ]
        );
        
        $this->add_control(
                'limit',
                [
                        'label' => Leo_Helper::__('Limit', 'elementor'),
                        'type' => Controls_Manager::NUMBER,
                        'min' => 1,
                        'default' => 10,
                ]
        );
        
        $this->add_control(
                'show_view_all',
                array(
                        'label'              => Leo_Helper::__( 'Show View All', 'elementor' ),
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
                'profile_link',
                [
                        'label'   => Leo_Helper::__( 'Profile Link', 'elementor' ),
                        'type'    => Controls_Manager::TEXTAREA,
                        'rows'    => '1',
                        'default' => '',
                        'condition' => [
                                'show_view_all' => '1',
                        ]
                ]
        );
        
        $this->end_controls_section();
            
        $this->start_controls_section(
                'carousal_settings',
                array(
                        'label' => Leo_Helper::__( 'View Settings', 'elementor' ),
                )
        );
        
        $this->add_control(
                'view_type',
                [
                        'label'   => Leo_Helper::__( 'View type', 'elementor' ),
                        'type'    => Controls_Manager::SELECT,
                        'default' => 'slickcarousel',
                        'options' => [
                                'slickcarousel' => Leo_Helper::__( 'Carousel', 'elementor' ),
                        ],
                ]
        );

        $this->add_responsive_control(
                'slides_to_show',
                array(
                        'label'              => Leo_Helper::__( 'Number of items per line', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'     => 4,
                        'tablet_default' => 2,
                        'mobile_default' => 1,
                        'options'     => [ 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10 ],
                        'frontend_available' => true,
                        'selectors' => [
                                '{{WRAPPER}} .elementor-ApProductCarousel.grid .item' => '-ms-flex: 0 0 calc(100%/{{VALUE}}); flex: 0 0 calc(100%/{{VALUE}}); max-width: calc(100%/{{VALUE}});'
                        ],
                        'render_type' => 'template',
                )
        );

        $this->add_control(
                'per_col',
                [
                        'label'       => Leo_Helper::__( 'Number of items per column', 'elementor' ),
                        'type'        => Controls_Manager::SELECT,
                        'default'     => 1,
                        'options'     => [ 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10 ],
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                        ],
                ]
        );

        $this->add_responsive_control(
                'spacing',
                [
                        'label'       => Leo_Helper::__( 'Items Spacing', 'elementor' ),
                        'type'        => Controls_Manager::SELECT,
                        'default'     => 0,
                        'tablet_default' => 0,
                        'mobile_default' => 0,
                        'options'     => [ 0 => 0, 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50 ],
                        'selectors' => [
                                '{{WRAPPER}} .slick-list' => 'margin-left: calc(-{{VALUE}}px/2);margin-right: calc(-{{VALUE}}px/2);',
                                '{{WRAPPER}} .leo-instagram-size' => 'padding-left: calc({{VALUE}}px/2);padding-right: calc({{VALUE}}px/2); padding-top: calc(25px/2); padding-bottom: calc(25px/2);',
                        ],
                ]
        );

        $this->add_responsive_control(
                'slides_to_scroll',
                [
                        'label'       => Leo_Helper::__( 'Slides to Scroll', 'elementor' ),
                        'type'        => Controls_Manager::SELECT,
                        'description'        => Leo_Helper::__( 'Set how many slides are scrolled per swipe.', 'elementor' ),
                        'default'     => 4,
                        'tablet_default' => 2,
                        'mobile_default' => 1,
                        'options'     => [ 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10 ],
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                        ],
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'slick_arrows',
                array(
                        'label'              => Leo_Helper::__( 'Prev/Next Arrows', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1'   => Leo_Helper::__( 'Yes', 'elementor' ),
                                '0'   => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                        ],
                        'frontend_available' => true,
                )
        );

        $this->add_control(
                'slick_dot',
                array(
                        'label'              => Leo_Helper::__( 'Show dot indicators', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1'   => Leo_Helper::__( 'Yes', 'elementor' ),
                                '0'   => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                        ],
                        'frontend_available' => true,
                )
        );

        $this->add_control(
                'infinite',
                array(
                        'label'              => Leo_Helper::__( 'Infinite Loop', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Yes', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => true,
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                        ],
                )
        );

        $this->add_control(
                'speed',
                array(
                        'label'              => Leo_Helper::__( 'Animation Speed', 'elementor' ),
                        'type'               => Controls_Manager::NUMBER,
                        'default'            => 500,
                        'frontend_available' => true,
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                        ],
                )
        );

        $this->add_control(
                'autoplay',
                array(
                        'label'              => Leo_Helper::__( 'Autoplay', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Yes', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => true,
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                        ],
                )
        );

        $this->add_control(
                'pause_on_hover',
                array(
                        'label'              => Leo_Helper::__( 'Pause on Hover', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Yes', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                        'frontend_available' => true,
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                                'autoplay' => '1',
                        ],
                )
        );

        $this->add_control(
                'autoplay_speed',
                array(
                        'label'              => Leo_Helper::__( 'Autoplay Speed', 'elementor' ),
                        'type'               => Controls_Manager::NUMBER,
                        'default'            => 5000,
                        'frontend_available' => true,
                        'condition'   => [
                                'view_type' => 'slickcarousel',
                                'autoplay' => '1',
                        ],
                )
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
                'section_style_show_view_all',
                [
                        'label' => Leo_Helper::__( 'Show View All', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'show_view_all' => '1',
                        ],
                ]
        );
        $this->add_responsive_control(
                'view_all_show_align',
                [
                        'label' => Leo_Helper::__( 'Alignment', 'elementor' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                                'left'    => [
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
                                '{{WRAPPER}} .instagram-viewall' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'view_all_show_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_4,
                        'selector' => '{{WRAPPER}} .btn-viewall, {{WRAPPER}} .btn-viewall *',
                ]
        );

        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                        'name' => 'view_all_show_text_shadow',
                        'selector' => '{{WRAPPER}} .btn-viewall, {{WRAPPER}} .btn-viewall *',
                ]
        );

        $this->add_control(
                'separator_panel_style',
                [
                        'type' => Controls_Manager::DIVIDER,
                        'style' => 'thick',
                ]
        );
        $this->start_controls_tabs( 'view_all_show_button_style' );

        $this->start_controls_tab(
                'view_all_show_button_normal',
                [
                        'label' => Leo_Helper::__( 'Normal', 'elementor' ),
                ]
        );

        $this->add_control(
                'view_all_show_button_text_color',
                [
                        'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .btn-viewall' => 'color: {{VALUE}};',
                        ],
                ]
        );

        $this->add_control(
                'view_all_show_background_color',
                [
                        'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
//                        'scheme' => [
//                                'type' => Scheme_Color::get_type(),
//                                'value' => Scheme_Color::COLOR_4,
//                        ],
                        'selectors' => [
                                '{{WRAPPER}} .btn-viewall, {{WRAPPER}} .btn-viewall *' => 'background-color: {{VALUE}};',
                        ],
                ]
        );
        
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                        'name' => 'view_all_show_border',
                        'selector' => '{{WRAPPER}} .btn-viewall',
//                        'separator' => 'before',
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'view_all_show_button_hover',
                [
                        'label' => Leo_Helper::__( 'Hover', 'elementor' ),
                ]
        );

        $this->add_control(
                'view_all_show_hover_color',
                [
                        'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                                '{{WRAPPER}} .btn-viewall:hover, ' .
                                '{{WRAPPER}} .btn-viewall:focus' => 'color: {{VALUE}};',
                        ],
                ]
        );

        $this->add_control(
                'view_all_show_button_background_hover_color',
                [
                        'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                                '{{WRAPPER}} .btn-viewall:hover, ' .
                                '{{WRAPPER}} .btn-viewall:focus' => 'background-color: {{VALUE}};',
                        ],
                ]
        );

//        $this->add_control(
//                'view_all_show_button_hover_border_color',
//                [
//                        'label' => Leo_Helper::__( 'Border Color', 'elementor' ),
//                        'type' => Controls_Manager::COLOR,
//                        'condition' => [
//                                'border_border!' => '',
//                        ],
//                        'selectors' => [
//                                '{{WRAPPER}} .btn-viewall:hover, ' .
//                                '{{WRAPPER}} .btn-viewall:focus' => 'border-color: {{VALUE}};',
//                        ],
//                ]
//        );
        
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                        'name' => 'view_all_show_button_hover_border_color',
                        'selector' => '{{WRAPPER}} .btn-viewall:hover',
//                        'selectors' =>
//                        [
//                            '{{WRAPPER}} .btn-viewall:hover, ' .
//                            '{{WRAPPER}} .btn-viewall:focus' => 'border-color: {{VALUE}};',
//                        ]
                ]
        );

//        $this->add_control(
//                'view_all_show_hover_animation',
//                [
//                        'label' => Leo_Helper::__( 'Hover Animation', 'elementor' ),
//                        'type' => Controls_Manager::HOVER_ANIMATION,
//                ]
//        );
        
        

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_control(
                'view_all_show_border_radius',
                [
                        'label' => Leo_Helper::__( 'Border Radius', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .btn-viewall' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                        'separator' => 'before',
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
        if ( Leo_Helper::is_admin() )
        {
                return;
        }
            
        $settings = (array)$this->get_settings_for_display();
        $context = \Context::getContext();

        $settings['lib_has_error'] = '';
        $settings['lib_error'] = '';
        $settings['class'] = '';
        $settings['carousel_type'] = 'slickcarousel';
        $settings['slick_custom_status'] = '0';
        $settings['slick_vertical'] = '0';
        $settings['form_id'] = $this->get_name() . '_' . Leo_Helper::getRandomNumber();
        
        if(isset($settings['access_token']) && $settings['access_token']) {
            
        } else {
            $settings['lib_has_error'] = true;
            $settings['lib_error'] = 'Can not show Instragram. Please type Access Token.';
        }
        

        $out_put  = '';

        $context->smarty->assign(
                array(
                    'formAtts' => $settings,
                    'apLiveEdit' => '',
                    'apLiveEditEnd' => '',
                    'leo_include_file' => 'module:/leoelements/views/templates/front/LeoManufacturersCarouselSlick.tpl',
                    'settings' => $settings,
                    'img_manu_dir' => _THEME_MANU_DIR_,
                )
        );


        $template_file_name = 'module:/leoelements/views/templates/front/LeoInstagram.tpl';

        $out_put           .= $context->smarty->fetch( $template_file_name );

        echo $out_put;
    }
    
}