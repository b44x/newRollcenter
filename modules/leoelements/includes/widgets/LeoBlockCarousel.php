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

class Widget_LeoBlockCarousel extends Widget_Base
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
        return 'LeoBlockCarousel';
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
        return Leo_Helper::__( 'Block Carousel', 'elementor' );
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
            return [ 'leo', 'ap', 'block', 'carousel', 'blockcarousel' ];
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
                        'label' => Leo_Helper::__( 'Block Carousel', 'elementor' ),
                )
        );

        $this->add_control(
                'form_id',
                [
                        'label'   => 'form_id',
                        'type' => Controls_Manager::HIDDEN,
                        'default' => $this->get_name() . '_' . Leo_Helper::getRandomNumber(),
                ]
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
                'item_sub_title',
                [
                        'label' => Leo_Helper::__( 'Item Sub Title', 'elementor' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => Leo_Helper::__( 'Sub Title', 'elementor' ),
                        'placeholder' => Leo_Helper::__( 'Sub Title', 'elementor' ),
                        'label_block' => true,
                ]
        );

        $repeater->add_control(
                'item_description',
                [
                        'label' => Leo_Helper::__( 'Item Description', 'elementor' ),
                        'type' => Controls_Manager::TEXTAREA,
                        'rows' => '10',
                        'default' => 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
                ]
        );

        $repeater->add_control(
                'item_link',
                [
                        'label' => Leo_Helper::__( 'Item Link', 'elementor' ),
                        'type' => Controls_Manager::URL,
                        'autocomplete' => false,
                        'placeholder' => Leo_Helper::__( 'https://your-link.com', 'elementor' ),
                ]
        );

        $repeater->add_control(
                'item_image',
                [
                        'label' => Leo_Helper::__( 'Item Choose Image', 'elementor' ),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                                'url' => Utils::get_placeholder_image_src(),
                        ],
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
                                [
                                        'item_title' => Leo_Helper::__( 'Item #3', 'elementor' ),
                                        'item_link' => '#',
                                ],
                                [
                                        'item_title' => Leo_Helper::__( 'Item #4', 'elementor' ),
                                        'item_link' => '#',
                                ],
                        ],
                        'title_field' => '{{{ item_title }}}',
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
                        'default'     => 2,
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
                                '{{WRAPPER}} .item' => 'padding-left: calc({{VALUE}}px/2);padding-right: calc({{VALUE}}px/2);',
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
                'section_style_item_show_title',
                [
                        'label' => Leo_Helper::__( 'Item Title', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
//                        'condition' => [
//                                'show_title' => '1',
//                        ],
                ]
        );
        $this->add_control(
                'item_title_align',
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
                                '{{WRAPPER}} .slick-row .item-title' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'item_title_bottom_space',
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
                                '{{WRAPPER}} .slick-row .item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'item_title_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .slick-row .item-title, {{WRAPPER}} .slick-row .item-title *' => 'color: {{VALUE}};',
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
                        'name' => 'item_title_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .slick-row .item-title',
                ]
        );
        $this->add_responsive_control(
                'item_title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .slick-row .item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_item_show_sub_title',
                [
                        'label' => Leo_Helper::__( 'Item Sub Title', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
//                        'condition' => [
//                                'show_title' => '1',
//                        ],
                ]
        );
        $this->add_control(
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
                                'justify' => [
                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
                                        'icon' => 'eicon-text-align-justify',
                                ],
                        ],
                        'default' => 'left',
                        'selectors' => [
                                '{{WRAPPER}} .slick-row .item-sub-title' => 'text-align: {{VALUE}};',
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
                                '{{WRAPPER}} .slick-row .item-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'item_sub_title_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .slick-row .item-sub-title, {{WRAPPER}} .slick-row .item-sub-title *' => 'color: {{VALUE}};',
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
                        'name' => 'item_sub_title_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .slick-row .item-sub-title',
                ]
        );
        $this->add_responsive_control(
                'item_sub_title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .slick-row .item-sub-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_item_image',
                [
                        'label' => Leo_Helper::__( 'Item Image', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
//                        'condition' => [
//                                'bleoblogs_sima' => '1',
//                        ],
                ]
        );
        $this->add_responsive_control(
			'item_image_width',
			[
				'label' => Leo_Helper::__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-row img.img-fluid' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_image_space',
			[
				'label' => Leo_Helper::__( 'Max Width', 'elementor' ) . ' (%)',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-row img.img-fluid' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_image_separator_panel_style',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'item_image_effects' );

		$this->start_controls_tab( 'item_image_normal',
			[
				'label' => Leo_Helper::__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'item_image_opacity',
			[
				'label' => Leo_Helper::__( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-row img.img-fluid' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .slick-row img.img-fluid',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'item_image_hover',
			[
				'label' => Leo_Helper::__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'item_image_opacity_hover',
			[
				'label' => Leo_Helper::__( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-row img.img-fluid:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} ..slick-row img.img-fluid',
			]
		);

		$this->add_control(
			'item_image_background_hover_transition',
			[
				'label' => Leo_Helper::__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-row img.img-fluid' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'item_image_hover_animation',
			[
				'label' => Leo_Helper::__( 'Hover Animation', 'elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);
                
                $this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_item_description',
                [
                        'label' => Leo_Helper::__( 'Item Description', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
//                        'condition' => [
//                                'show_desc' => '1',
//                        ],
                ]
        );
        $this->add_control(
                'item_description_align',
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
                                '{{WRAPPER}} .slick-row .item-description' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'item_description_bottom_space',
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
                                '{{WRAPPER}} .slick-row .item-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'item_description_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .slick-row .item-description, {{WRAPPER}} .slick-row .item-description *' => 'color: {{VALUE}};',
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
                        'name' => 'item_description_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .slick-row .item-description',
                ]
        );
        $this->add_responsive_control(
                'item_description_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .slick-row .item-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            
            $settings['lib_has_error'] = '';
            $settings['lib_error'] = '';
            $settings['class'] = '';
            $settings['carousel_type'] = 'slickcarousel';
            $settings['slides'] = $items;
            
            $settings['slick_custom_status'] = '0';
            $settings['slick_vertical'] = '0';
            $settings['form_id'] = $this->get_name() . '_' . Leo_Helper::getRandomNumber();
            

            $out_put  = '';
            
            $context->smarty->assign(
                    array(
                        'formAtts' => $settings,
                        'apLiveEdit' => '',
                        'apLiveEditEnd' => '',
                        'leo_include_file' => 'module:/leoelements/views/templates/front/LeoBlockCarouselSlick.tpl',
                            'items' => $items,
                            'settings' => $settings,

                    )
            );
                
                
            $template_file_name = 'module:/leoelements/views/templates/front/LeoBlockCarousel.tpl';

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