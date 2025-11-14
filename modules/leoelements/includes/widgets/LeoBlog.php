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

class Widget_LeoBlog extends Widget_Base
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
        return 'LeoBlog';
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
        return Leo_Helper::__( 'Leo Blog Module', 'elementor' );
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
        return [ 'leo', 'ap', 'blog', 'blogcarousel'];
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
                'n' => Leo_Helper::__('Latest Blogs', 'elementor'),
        ];
        
        $categoriesSource = array();
        if (\Module::isInstalled('leoblog') && \Module::isEnabled('leoblog')) {
            $categoriesSource = $this->getAllCategory();
        }
        
        $this->start_controls_section(
                'section_options',
                [
                        'label' => Leo_Helper::__( 'Blog Options', 'elementor' ),
                ]
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
                'source',
                [
                        'label' => Leo_Helper::__('Source of Blogs', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'frontend_available' => true,
                        'default' => 'n',
                        'tablet_default' => 'n',
                        'mobile_default' => 'n',
                        'options' => $source,
                        'frontend_available' => true,
                ]
        );
        
        $this->add_control(
                'category',
                [
                    'label' => Leo_Helper::__('Select category', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => $categoriesSource,
                    'condition' => [
                        'source' => 'c',
                    ]
                ]
        );
        
        $link_leoblog = '';
        if(isset($GLOBALS['gb_leoelements']['url'])) {
            $link_leoblog = $GLOBALS['gb_leoelements']['url']['link_leoblog'];
        }
        
        $this->add_control(
                'anchor_note',
                [
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' => sprintf( 'Click to the link to manage <br/> <a href="%s" target="_blank"> Leo Blog Module</a> ', $link_leoblog ),
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
                'order_way',
                array(
                        'label'   => Leo_Helper::__( 'Order Way', 'elementor' ),
                        'type'    => Controls_Manager::SELECT,
                        'options' => array(
                                'asc'   => Leo_Helper::__( 'Asc', 'elementor' ),
                                'desc'        => Leo_Helper::__( 'Desc', 'elementor' ),
                                'random'     => Leo_Helper::__( 'Random', 'elementor' ),
                        ),
                        'default' => 'desc',
                        'condition' => [
                                'source!' => 'n',
                        ]
                )
        );
        
        $this->add_control(
                'order_by',
                array(
                        'label'   => Leo_Helper::__( 'Order By', 'elementor' ),
                        'type'    => Controls_Manager::SELECT,
                        'options' => array(
                                'id_leoblogcat'     => Leo_Helper::__( 'Category', 'elementor' ),
                                'id_leoblog_blog'   => Leo_Helper::__( 'ID', 'elementor' ),
                                'meta_title'        => Leo_Helper::__( 'Title', 'elementor' ),
                                'date_add'          => Leo_Helper::__( 'Date add', 'elementor' ),
                                'date_upd'          => Leo_Helper::__( 'Date update', 'elementor' ),
                        ),
                        'default' => 'id_leoblog_blog',
                        'condition' => [
                                'source!' => 'n',
                        ]
                )
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
                'bleoblogs_sima',
                array(
                        'label'              => Leo_Helper::__( 'Show Image', 'elementor' ),
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
                'show_blog_name',
                array(
                        'label'              => Leo_Helper::__( 'Show Blog Name', 'elementor' ),
                        'type'               => Controls_Manager::SELECT,
                        'default'            => '1',
                        'options'            => array(
                                '1' => Leo_Helper::__( 'Show', 'elementor' ),
                                '0'  => Leo_Helper::__( 'No', 'elementor' ),
                        ),
                )
        );
        
        $this->add_control(
                'bleoblogs_saut',
                array(
                        'label'              => Leo_Helper::__( 'Show Author', 'elementor' ),
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
                'bleoblogs_scat',
                array(
                        'label'              => Leo_Helper::__( 'Show Category', 'elementor' ),
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
                'bleoblogs_scre',
                array(
                        'label'              => Leo_Helper::__( 'Show Created Date', 'elementor' ),
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
                'bleoblogs_shits',
                array(
                        'label'              => Leo_Helper::__( 'Show Hits', 'elementor' ),
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
                'show_desc',
                array(
                        'label'              => Leo_Helper::__( 'Show Description', 'elementor' ),
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
                'bleoblogs_readmore',
                array(
                        'label'              => Leo_Helper::__( 'Show Read More', 'elementor' ),
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
                'bleoblogs_show',
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
                            'default'     => 10,
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
        
        
        $this->start_controls_tabs( 'title_button_style' );
        $this->start_controls_tab(
            'title_button_normal',
            [
                    'label' => Leo_Helper::__( 'Normal', 'elementor' ),
            ]
        );
        $this->add_control(
            'title_color',
            [
                    'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                            '{{WRAPPER}} .title_block, {{WRAPPER}} .title_block *' => 'color: {{VALUE}};',
                    ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'title_button_hover',
            [
                    'label' => Leo_Helper::__( 'Hover', 'elementor' ),
            ]
        );
        $this->add_control(
            'title_hover_color',
            [
                    'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                            '{{WRAPPER}} .title_block:hover, {{WRAPPER}} .title_block *:hover' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .title_block:focus, {{WRAPPER}} .title_block *:focus' => 'color: {{VALUE}};',
                    ],
            ]
        );
        $this->end_controls_tab();
        
        
                
        $this->end_controls_tabs();
        

        $this->add_responsive_control(
                'title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .title_block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                        'separator' => 'before',
                ]
        );
        
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'title_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .title_block',
//                        'separator' => 'before',
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
                'section_style_bleoblogs_sima',
                [
                        'label' => Leo_Helper::__( 'Show Image', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'bleoblogs_sima' => '1',
                        ],
                ]
        );
        $this->add_responsive_control(
			'bleoblogs_sima_width',
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
					'{{WRAPPER}} .blog-image-container img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'bleoblogs_sima_space',
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
					'{{WRAPPER}} .blog-image-container img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'bleoblogs_sima_separator_panel_style',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'bleoblogs_sima_image_effects' );

		$this->start_controls_tab( 'bleoblogs_sima_normal',
			[
				'label' => Leo_Helper::__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'bleoblogs_sima_opacity',
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
					'{{WRAPPER}} .blog-image-container img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .blog-image-container img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'bleoblogs_sima_hover',
			[
				'label' => Leo_Helper::__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'bleoblogs_sima_opacity_hover',
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
					'{{WRAPPER}} .blog-image-container:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .blog-image-container:hover img',
			]
		);

		$this->add_control(
			'bleoblogs_sima_background_hover_transition',
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
					'{{WRAPPER}} .blog-image-container img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'bleoblogs_sima_hover_animation',
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
                'section_style_blog_title',
                [
                        'label' => Leo_Helper::__( 'Show Blog Name', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'show_blog_name' => '1',
                        ],
                ]
        );
        $this->add_control(
                'blog_title_align',
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
                                '{{WRAPPER}} .blog-title' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'blog_title_bottom_space',
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
                                '{{WRAPPER}} .blog-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        
        $this->start_controls_tabs( 'blog_title_button_style' );
        $this->start_controls_tab(
            'blog_title_button_normal',
            [
                    'label' => Leo_Helper::__( 'Normal', 'elementor' ),
            ]
        );
        $this->add_control(
            'blog_title_color',
            [
                    'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                            '{{WRAPPER}} .blog-title, {{WRAPPER}} .blog-title *' => 'color: {{VALUE}};',
                    ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'blog_title_button_hover',
            [
                    'label' => Leo_Helper::__( 'Hover', 'elementor' ),
            ]
        );
        $this->add_control(
            'blog_title_hover_color',
            [
                    'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                            '{{WRAPPER}} .blog-title:hover, {{WRAPPER}} .blog-title *:hover' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .blog-title:focus, {{WRAPPER}} .blog-title *:focus' => 'color: {{VALUE}};',
                    ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        
        $this->add_responsive_control(
                'blog_title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .blog-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                        'separator' => 'before',
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'blog_title_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .blog-title',
                ]
        );

        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_bleoblogs_saut',
                [
                        'label' => Leo_Helper::__( 'Show Author', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'bleoblogs_saut' => '1',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_saut_align',
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
                                '{{WRAPPER}} .author' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_saut_bottom_space',
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
                                '{{WRAPPER}} .author' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_saut_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .author, {{WRAPPER}} .author *' => 'color: {{VALUE}};',
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
                        'name' => 'bleoblogs_saut_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .author',
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_saut_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .author' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_bleoblogs_scat',
                [
                        'label' => Leo_Helper::__( 'Show Category', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'bleoblogs_scat' => '1',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_scat_align',
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
                                '{{WRAPPER}} .cat' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_scat_bottom_space',
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
                                '{{WRAPPER}} .cat' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_scat_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .cat, {{WRAPPER}} .cat *' => 'color: {{VALUE}};',
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
                        'name' => 'bleoblogs_scat_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .cat',
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_scat_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .cat' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_bleoblogs_scre',
                [
                        'label' => Leo_Helper::__( 'Show Created Date', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'bleoblogs_scre' => '1',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_scre_align',
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
                                '{{WRAPPER}} .created' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_scre_bottom_space',
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
                                '{{WRAPPER}} .created' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_scre_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .created, {{WRAPPER}} .created *' => 'color: {{VALUE}};',
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
                        'name' => 'bleoblogs_scre_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .created',
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_scre_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .created' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_bleoblogs_shits',
                [
                        'label' => Leo_Helper::__( 'Show Hits', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'bleoblogs_shits' => '1',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_shits_align',
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
                                '{{WRAPPER}} .hits' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_shits_bottom_space',
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
                                '{{WRAPPER}} .hits' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'bleoblogs_shits_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .hits, {{WRAPPER}} .hits *' => 'color: {{VALUE}};',
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
                        'name' => 'bleoblogs_shits_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .hits',
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_shits_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .hits' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_show_desc',
                [
                        'label' => Leo_Helper::__( 'Show Description', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'show_desc' => '1',
                        ],
                ]
        );
        $this->add_control(
                'show_desc_align',
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
                                '{{WRAPPER}} .blog-desc' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_responsive_control(
                'show_desc_bottom_space',
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
                                '{{WRAPPER}} .blog-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'show_desc_color',
                [
                        'label' => Leo_Helper::__( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .blog-desc, {{WRAPPER}} .blog-desc *' => 'color: {{VALUE}};',
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
                        'name' => 'show_desc_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                        'selector' => '{{WRAPPER}} .blog-desc',
                ]
        );
        $this->add_responsive_control(
                'show_desc_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .blog-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->end_controls_section();
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_bleoblogs_readmore',
                [
                        'label' => Leo_Helper::__( 'Show Read More', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'bleoblogs_readmore' => '1',
                        ],
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_readmore_align',
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
                                '{{WRAPPER}} .blog-readmore' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'bleoblogs_readmore_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_4,
                        'selector' => '{{WRAPPER}} .blog-readmore, {{WRAPPER}} .blog-readmore *',
                ]
        );

        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                        'name' => 'bleoblogs_readmore_text_shadow',
                        'selector' => '{{WRAPPER}} .blog-readmore, {{WRAPPER}} .blog-readmore *',
                ]
        );

        $this->add_control(
                'bleoblogs_readmore_separator_panel_style',
                [
                        'type' => Controls_Manager::DIVIDER,
                        'style' => 'thick',
                ]
        );
        $this->start_controls_tabs( 'bleoblogs_readmore_button_style' );

        $this->start_controls_tab(
                'bleoblogs_readmore_button_normal',
                [
                        'label' => Leo_Helper::__( 'Normal', 'elementor' ),
                ]
        );

        $this->add_control(
                'bleoblogs_readmore_button_text_color',
                [
                        'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .blog-readmore, {{WRAPPER}} .blog-readmore *' => 'fill: {{VALUE}}; color: {{VALUE}};',
                        ],
                ]
        );

        $this->add_control(
                'bleoblogs_readmore_background_color',
                [
                        'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
//                        'scheme' => [
//                                'type' => Scheme_Color::get_type(),
//                                'value' => Scheme_Color::COLOR_4,
//                        ],
                        'selectors' => [
                                '{{WRAPPER}} .blog-readmore' => 'background-color: {{VALUE}};',
                        ],
                ]
        );
        
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                        'name' => 'bleoblogs_readmore_border',
                        'selector' => '{{WRAPPER}} .blog-readmore',
//                        'separator' => 'before',
                ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
                'bleoblogs_readmore_button_hover',
                [
                        'label' => Leo_Helper::__( 'Hover', 'elementor' ),
                ]
        );

        $this->add_control(
                'bleoblogs_readmore_hover_color',
                [
                        'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                                '{{WRAPPER}} .blog-readmore:hover, {{WRAPPER}} .blog-readmore *:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .blog-readmore:focus, {{WRAPPER}} .blog-readmore *:focus' => 'color: {{VALUE}};',
                        ],
                ]
        );

        $this->add_control(
                'bleoblogs_readmore_button_background_hover_color',
                [
                        'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                                '{{WRAPPER}} .blog-readmore:hover, {{WRAPPER}} .blog-readmore *:hover' => 'background-color: {{VALUE}};',
                                '{{WRAPPER}} .blog-readmore:focus, {{WRAPPER}} .blog-readmore *:focus' => 'background-color: {{VALUE}};',
                        ],
                ]
        );

//        $this->add_control(
//                'bleoblogs_readmore_button_hover_border_color',
//                [
//                        'label' => Leo_Helper::__( 'Border Color', 'elementor' ),
//                        'type' => Controls_Manager::COLOR,
//                        'condition' => [
//                                'border_border!' => '',
//                        ],
//                        'selectors' => [
//                                '{{WRAPPER}} .blog-readmore:hover, ' .
//                                '{{WRAPPER}} .blog-readmore:focus' => 'border-color: {{VALUE}};',
//                        ],
//                ]
//        );
        
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                        'name' => 'bleoblogs_readmore_button_hover_border_color',
                        'selector' => '{{WRAPPER}} .blog-readmore:hover',
//                        'selectors' =>
//                        [
//                            '{{WRAPPER}} .blog-readmore:hover, ' .
//                            '{{WRAPPER}} .blog-readmore:focus' => 'border-color: {{VALUE}};',
//                        ]
                ]
        );
//
//        $this->add_control(
//                'bleoblogs_readmore_hover_animation',
//                [
//                        'label' => Leo_Helper::__( 'Hover Animation', 'elementor' ),
//                        'type' => Controls_Manager::HOVER_ANIMATION,
//                ]
//        );
//        
//        
//
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
                'bleoblogs_readmore_border_radius',
                [
                        'label' => Leo_Helper::__( 'Border Radius', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .blog-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                        'separator' => 'before',
                ]
        );
        
        $this->add_responsive_control(
                'bleoblogs_readmore_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .blog-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        
//        $this->add_responsive_control(
//                'bleoblogs_readmore_space',
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
//                                '{{WRAPPER}} .blog-readmore' => 'max-width: {{VALUE}}px;',
//                        ],
//                ]
//        );
        
        $this->add_responsive_control(
                'bleoblogs_readmoree_space',
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
                                '{{WRAPPER}} .blog-readmore' => 'max-width: {{SIZE}}{{UNIT}};',
                        ],
                ]
        );
        
        $this->end_controls_section();
        /************************************************************/
        $this->start_controls_section(
                'section_style_bleoblogs_show',
                [
                        'label' => Leo_Helper::__( 'Show View All', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'bleoblogs_show' => '1',
                        ],
                ]
        );
        $this->add_responsive_control(
                'bleoblogs_show_align',
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
                                '{{WRAPPER}} .blog-viewall' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'bleoblogs_show_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_4,
                        'selector' => '{{WRAPPER}} .blog-viewall, {{WRAPPER}} .blog-viewall *',
                ]
        );

        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                        'name' => 'bleoblogs_show_text_shadow',
                        'selector' => '{{WRAPPER}} .blog-viewall, {{WRAPPER}} .blog-viewall *',
                ]
        );

        $this->add_control(
                'bleoblogs_show_separator_panel_style',
                [
                        'type' => Controls_Manager::DIVIDER,
                        'style' => 'thick',
                ]
        );
        $this->start_controls_tabs( 'bleoblogs_show_button_style' );

        $this->start_controls_tab(
                'bleoblogs_show_button_normal',
                [
                        'label' => Leo_Helper::__( 'Normal', 'elementor' ),
                ]
        );

        $this->add_control(
                'bleoblogs_show_button_text_color',
                [
                        'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}} .blog-viewall, {{WRAPPER}} .blog-viewall *' => 'fill: {{VALUE}}; color: {{VALUE}};',
                        ],
                ]
        );

        $this->add_control(
                'bleoblogs_show_background_color',
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
                        'name' => 'bleoblogs_show_border',
                        'selector' => '{{WRAPPER}} .btn-viewall',
//                        'separator' => 'before',
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'bleoblogs_show_button_hover',
                [
                        'label' => Leo_Helper::__( 'Hover', 'elementor' ),
                ]
        );

        $this->add_control(
                'bleoblogs_show_hover_color',
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
                'bleoblogs_show_button_background_hover_color',
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
//                'bleoblogs_show_button_hover_border_color',
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
                        'name' => 'bleoblogs_show_button_hover_border_color',
                        'selector' => '{{WRAPPER}} .btn-viewall:hover',
//                        'selectors' =>
//                        [
//                            '{{WRAPPER}} .btn-viewall:hover, ' .
//                            '{{WRAPPER}} .btn-viewall:focus' => 'border-color: {{VALUE}};',
//                        ]
                ]
        );

//        $this->add_control(
//                'bleoblogs_show_hover_animation',
//                [
//                        'label' => Leo_Helper::__( 'Hover Animation', 'elementor' ),
//                        'type' => Controls_Manager::HOVER_ANIMATION,
//                ]
//        );
        
        

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_control(
                'bleoblogs_show_border_radius',
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
        $return = '';
        if ( Leo_Helper::is_admin() ) {
                return $return;
        }
        
        $assign = array(
            'apContent' => '',
            'formAtts' => array(
                'form_id' => 'form_5846351677680492',
                'class' => 'LeoBlog',
                'chk_cat' => '5577844800e55bda2c0540af22ba96ce,0abc8c406b64fa2f13f5a7cbecbfb67f,1dcae6f22c5962b687451c98c27946f0',
                'bleoblogs_width' => '1000',
                'bleoblogs_height' => '682',
                'bleoblogs_show' => '0',
                'bleoblogs_readmore' => '0',
                'show_title' => '1',
                'show_sub_title' => '1',
                'show_blog_name' => '1',
                'show_desc' => '1',
                'bleoblogs_sima' => '1',
                'bleoblogs_saut' => '0',
                'bleoblogs_scat' => '1',
                'bleoblogs_scre' => '1',
                'bleoblogs_scoun' => '0',
                'bleoblogs_shits' => '0',
                'order_way' => 'desc',
                'order_by' => 'id_leoblog_blog',
                'nb_blogs' => '10',
                'view_type' => 'slickcarousel',
                'items' => '3',
                'itemsdesktop' => '3',
                'itemsdesktopsmall' => '3',
                'itemstablet' => '2',
                'itemsmobile' => '1',
                'itemscustom' => '[[0,1],[481,2],[576,3]]',
                'itempercolumn' => '1',
                'autoplay' => '0',
                'stoponhover' => '0',
                'responsive' => '1',
                'autoheight' => '0',
                'mousedrag' => '1',
                'touchdrag' => '1',
                'lazyload' => '0',
                'lazyfollow' => '0',
                'lazyeffect' => 'fade',
                'pagination' => '0',
                'paginationnumbers' => '0',
                'scrollperpage' => '0',
                'paginationspeed' => '800',
                'slidespeed' => '200',
                'nbitemsperpage' => '12',
                
                
                'nbitemsperline_desktop' => '',
                'nbitemsperline_smalldesktop' => '',
                'nbitemsperline_tablet' => '',
                'nbitemsperline_smalldevices' => '',
                'nbitemsperline_extrasmalldevices' => '',
                'nbitemsperline_smartphone' => '',
                
                
                
                'interval' => '5000',
                'slick_vertical' => '0',
                'autoplay' => '1',
                'pause_on_hover' => '1',
                'slick_loopinfinite' => '0',
                'slick_arrows' => '1',
                'slick_dot' => '0',
                'slick_centermode' => '0',
                'slick_centerpadding' => '60',
                'per_col' => '1',
                'slides_to_show' => '4',
                'slides_to_scroll' => '1',
                'slick_items_custom' => '_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_',
                'slick_custom_status' => '0',
                'slick_custom' => '_APOCBRACKET__APENTER_  dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_',
                'active' => '1',
                'title' => 'Our Blogs',
                'sub_title' => 'Find a bright ideal to suit your taste with our great selection',
                'class' => 'LeoBlog',
                'rtl' => '0',
                'override_folder' => '',
                '' => '',
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
        
        
        if (\Module::isInstalled('leoblog') && \Module::isEnabled('leoblog')) {
            $id_shop = (int)\Context::getContext()->shop->id;
            $assign['formAtts']['isEnabled'] = true;
            
            $module = \Module::getInstanceByName( 'leoblog' );
            $assign['formAtts']['leo_blog_helper'] = \LeoBlogHelper::getInstance();
            
            $settings = $this->get_settings_for_display();
            $settings['form_id'] = $this->get_name() . '_' . Leo_Helper::getRandomNumber();
            
            if( $settings['source'] == 'n' )
            {
                # GET LATEST BLOG
                $this->getLatesBlog($assign);
            }
            
            $this->setSlick($assign, $settings);
            $this->setParams($assign, $settings);
            
            $config = \LeoBlogConfig::getInstance();
            
            $assign['formAtts']['bleoblogs_width'] = $config->get('listing_leading_img_width');
            $assign['formAtts']['bleoblogs_height'] = $config->get('listing_leading_img_height');
            
            $config->setVar('listing_leading_img_width', $assign['formAtts']['bleoblogs_width']);
            $config->setVar('listing_leading_img_height', $assign['formAtts']['bleoblogs_height']);
            $assign['products'] = $this->getBlogsFont($assign['formAtts'], $module);
            $assign['carouselName'] = 'carousel-'.Leo_Helper::getRandomNumber();
            if ($assign['formAtts']['view_type'] == 'boostrap') {
                if (isset($assign['formAtts']['nbitemsperline']) && $assign['formAtts']['nbitemsperline']) {
                    $assign['formAtts']['nbitemsperline_desktop'] = $assign['formAtts']['nbitemsperline'];
                    $assign['formAtts']['nbitemsperline_smalldesktop'] = $assign['formAtts']['nbitemsperline'];
                    $assign['formAtts']['nbitemsperline_tablet'] = $assign['formAtts']['nbitemsperline'];
                }
                if (isset($assign['formAtts']['nbitemsperlinetablet']) && $assign['formAtts']['nbitemsperlinetablet']) {
                    $assign['formAtts']['nbitemsperline_smalldevices'] = $assign['formAtts']['nbitemsperlinetablet'];
                }
                if (isset($assign['formAtts']['nbitemsperlinemobile']) && $assign['formAtts']['nbitemsperlinemobile']) {
                    $assign['formAtts']['nbitemsperline_extrasmalldevices'] = $assign['formAtts']['nbitemsperlinemobile'];
                    $assign['formAtts']['nbitemsperline_smartphone'] = $assign['formAtts']['nbitemsperlinemobile'];
                }

                $assign['formAtts']['nbitemsperline_desktop'] = isset($assign['formAtts']['nbitemsperline_desktop']) && $assign['formAtts']['nbitemsperline_desktop']  ? (int)$assign['formAtts']['nbitemsperline_desktop'] : 4;
                $assign['formAtts']['nbitemsperline_smalldesktop'] = isset($assign['formAtts']['nbitemsperline_smalldesktop']) && $assign['formAtts']['nbitemsperline_smalldesktop'] ? (int)$assign['formAtts']['nbitemsperline_smalldesktop'] : 4;
                $assign['formAtts']['nbitemsperline_tablet'] = isset($assign['formAtts']['nbitemsperline_tablet']) && $assign['formAtts']['nbitemsperline_tablet'] ? (int)$assign['formAtts']['nbitemsperline_tablet'] : 3;
                $assign['formAtts']['nbitemsperline_smalldevices'] = isset($assign['formAtts']['nbitemsperline_smalldevices']) && $assign['formAtts']['nbitemsperline_smalldevices'] ? (int)$assign['formAtts']['nbitemsperline_smalldevices'] : 2;
                $assign['formAtts']['nbitemsperline_extrasmalldevices'] = isset($assign['formAtts']['nbitemsperline_extrasmalldevices']) && $assign['formAtts']['nbitemsperline_extrasmalldevices'] ? (int)$assign['formAtts']['nbitemsperline_extrasmalldevices'] : 1;
                $assign['formAtts']['nbitemsperline_smartphone'] = isset($assign['formAtts']['nbitemsperline_smartphone']) && $assign['formAtts']['nbitemsperline_smartphone'] ? (int)$assign['formAtts']['nbitemsperline_smartphone'] : 1;

                $assign['tabname'] = 'carousel-'.Leo_Helper::getRandomNumber();
                $assign['itemsperpage'] = (int)$assign['formAtts']['nbitemsperpage'];
                $assign['nbItemsPerLine'] = (int)$assign['formAtts']['nbitemsperline_desktop'];

                $assign['scolumn'] = '';

                if ($assign['formAtts']['nbitemsperline_desktop'] == '5') {
                    $assign['scolumn'] .= ' col-xl-2-4';
                } else {
                    $assign['scolumn'] .= ' col-xl-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_desktop']));
                }

                if ($assign['formAtts']['nbitemsperline_smalldesktop'] == '5') {
                    $assign['scolumn'] .= ' col-lg-2-4';
                } else {
                    $assign['scolumn'] .= ' col-lg-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smalldesktop']));
                }

                if ($assign['formAtts']['nbitemsperline_tablet'] == '5') {
                    $assign['scolumn'] .= ' col-md-2-4';
                } else {
                    $assign['scolumn'] .= ' col-md-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_tablet']));
                }

                if ($assign['formAtts']['nbitemsperline_smalldevices'] == '5') {
                    $assign['scolumn'] .= ' col-sm-2-4';
                } else {
                    $assign['scolumn'] .= ' col-sm-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smalldevices']));
                }

                if ($assign['formAtts']['nbitemsperline_extrasmalldevices'] == '5') {
                    $assign['scolumn'] .= ' col-xs-2-4';
                } else {
                    $assign['scolumn'] .= ' col-xs-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_extrasmalldevices']));
                }

                if ($assign['formAtts']['nbitemsperline_smartphone'] == '5') {
                    $assign['scolumn'] .= ' col-sp-2-4';
                } else {
                    $assign['scolumn'] .= ' col-sp-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smartphone']));
                }
            }
                    
            //DONGND:: create data for owl carousel with item custom
            if ($assign['formAtts']['view_type'] == 'owlcarousel') {
                //DONGND:: build data for fake item loading
                $assign['formAtts']['number_fake_item'] = $assign['formAtts']['items'];
                $array_fake_item = array();
                $array_fake_item['m'] = $assign['formAtts']['itemsmobile'];
                $array_fake_item['sm'] = $assign['formAtts']['itemstablet'];
                $array_fake_item['md'] = $assign['formAtts']['itemsdesktopsmall'];
                $array_fake_item['lg'] = $assign['formAtts']['itemsdesktop'];
                $array_fake_item['xl'] = $assign['formAtts']['items'];
                $assign['formAtts']['array_fake_item'] = $array_fake_item;
                if (isset($assign['formAtts']['itemscustom']) && $assign['formAtts']['itemscustom'] != '') {
                    $array_item_custom = json_decode($assign['formAtts']['itemscustom']);
                    $array_item_custom_tmp = array();
                    $array_number_item = array();
                    foreach ($array_item_custom as $array_item_custom_val) {
                        $size_window = $array_item_custom_val[0];
                        $number_item = $array_item_custom_val[1];
                        if (0 <= $size_window && $size_window < 576) {
                            $array_item_custom_tmp['m'] = $number_item;
                        } else if (576 <= $size_window && $size_window < 768) {
                            $array_item_custom_tmp['sm'] = $number_item;
                        } else if (768 <= $size_window && $size_window < 992) {
                            $array_item_custom_tmp['md'] = $number_item;
                        } else if (992 <= $size_window && $size_window < 1200) {
                            $array_item_custom_tmp['lg'] = $number_item;
                        } else if ($size_window >= 1200) {
                            $array_item_custom_tmp['xl'] = $number_item;
                        }
                        $array_item_custom_tmp[$size_window] = $number_item;
                        $array_number_item[] = $number_item;
                    };
                    $assign['formAtts']['array_fake_item'] = array_merge($array_fake_item, $array_item_custom_tmp);
                    
                    if (max($array_number_item) > $assign['formAtts']['items']) {
                        $assign['formAtts']['number_fake_item'] = max($array_number_item);
                    }
                }
            };
            
            if ($assign['formAtts']['view_type'] == 'slickcarousel') {
                if (isset($assign['formAtts']['slick_items_custom'])) {
                    $assign['formAtts']['slick_items_custom'] = str_replace($this->str_search, $this->str_relace, $assign['formAtts']['slick_items_custom']);
                }
                if (isset($assign['formAtts']['slick_custom'])) {
                    $str_relace = array('&', '\"', '\'', '', '', '', '[', ']', '+', '{', '}');
                    $assign['formAtts']['slick_custom'] = str_replace($this->str_search, $str_relace, $assign['formAtts']['slick_custom']);
                }
                if (isset($assign['formAtts']['slick_items_custom'])) {
                    $assign['formAtts']['slick_items_custom'] = json_decode($assign['formAtts']['slick_items_custom']);
                }
            
                //DONGND:: build data for fake item loading
                $assign['formAtts']['number_fake_item'] = $assign['formAtts']['slides_to_show']*$assign['formAtts']['per_col'];
                
                if (isset($assign['formAtts']['slick_items_custom']) && $assign['formAtts']['slick_items_custom'] != '') {
                    $array_item_custom = $assign['formAtts']['slick_items_custom'];
                    $array_item_custom_tmp = array();
                    $array_number_item = array();
                    foreach ($array_item_custom as $array_item_custom_val) {
                        $size_window = $array_item_custom_val[0];
                        $number_item = $array_item_custom_val[1];
                        if (0 <= $size_window && $size_window < 576) {
                            $array_item_custom_tmp['m'] = $number_item;
                        } else if (576 <= $size_window && $size_window < 768) {
                            $array_item_custom_tmp['sm'] = $number_item;
                        } else if (768 <= $size_window && $size_window < 992) {
                            $array_item_custom_tmp['md'] = $number_item;
                        } else if (992 <= $size_window && $size_window < 1200) {
                            $array_item_custom_tmp['lg'] = $number_item;
                        } else if ($size_window >= 1200) {
                            $array_item_custom_tmp['xl'] = $assign['formAtts']['slides_to_show'];
                        }
                        $number_item = $number_item*$assign['formAtts']['per_col'];
                        $array_item_custom_tmp[$size_window] = $number_item;
                        $array_number_item[] = $number_item;
                    };
                    $assign['formAtts']['array_fake_item'] = $array_item_custom_tmp;
                    
                    if (max($array_number_item) > $assign['formAtts']['slides_to_show']) {
                        $assign['formAtts']['number_fake_item'] = max($array_number_item);
                    }
                }
            }
        } else {
            // validate module
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show LeoBlog via Leoelements. Please enable LeoBlog module.';
        }
        
        $context = \Context::getContext();
        if ($assign) {
            foreach ($assign as $key => $ass) {
                $context->smarty->assign(array($key => $ass));
            }
        }
        $template_file_name = 'module:/leoelements/views/templates/front/LeoBlog.tpl';
        $out_put = '';
        $out_put           .= $context->smarty->fetch( $template_file_name );

        echo $out_put;

        return $return;
    }
    
    protected function render2()
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
                'slideshow_group' => $settings['source'],               // Ex: '66b973e6e6e8d38f781384537e295392',
                'slideshow_group_tablet' => $settings['source'],        // Ex: '66b973e6e6e8d38f781384537e295392',
                'slideshow_group_mobile' => $settings['source'],        // Ex: '66b973e6e6e8d38f781384537e295392',
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
        
        if (\Module::isInstalled('leoblog') && \Module::isEnabled('leoblog')) {
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
                    $assign['formAtts']['lib_error'] = 'Can not show Blog via Leoelements. Please check that The Group of Leo Blog is exist.';
                }
            }
        } else {
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show Blog via Leoelements. Please enable Leo Blog module.';
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
        
    public function getLatesBlog(&$assign)
    {
        $id_shop = (int)\Context::getContext()->shop->id;
        
        $sql = '';
        $sql .= 'SELECT cat.id_leoblogcat FROM `'._DB_PREFIX_.'leoblogcat` cat';
        $sql .= ' JOIN `'._DB_PREFIX_.'leoblogcat_shop` cat_s ON cat.id_leoblogcat = cat_s.id_leoblogcat  AND id_shop = ' . (int)$id_shop;
            
        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $where = '-2';
        foreach ($result as $blog) {
            $where .= ($where == '') ? $blog['id_leoblogcat'] : ','.$blog['id_leoblogcat'];
        }
        $assign['formAtts']['chk_cat'] = $where;
        
        
        $assign['formAtts']['order_by'] = 'id_leoblog_blog';
        $assign['formAtts']['order_way'] = 'DESC';
        
        return $assign;
    }
    
    public function setSlick(&$assignview_type, $settings)
    {
        // REMOVE OWlCAROUSEL
    }
    
    public function setParams(&$assign, &$settings)
    {
        $assign['formAtts'] = array_merge($assign['formAtts'], $settings);
        
        $assign['formAtts']['nb_blogs'] = $assign['formAtts']['limit'];
    }


    public $str_search = array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APPLUS_', '_APOCBRACKET_', '_APCCBRACKET_', '_AP2F_');
    public $str_relace = array('&', '"', '\'', '\t', '\r', '\n', '[', ']', '+', '{', '}', '%2F');
    
    public function getAllCategory()
    {
        $result = $this->getChild();     // All category
        
        $maxdepth = 10;
        $resultIds = array();
        $resultParents = array();
        $categoriesSource = array();
        foreach ($result as &$row) {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_leoblogcat']] = &$row;
        }
        
        $this->getTree($resultParents, $resultIds, $maxdepth, 1, 0, $categoriesSource);
        
        return $categoriesSource;
    }
    
    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0, &$categoriesSource)
    {
        if (isset($resultIds[$id_category])) {
            
            $name = str_repeat('&nbsp;&nbsp;', 1 * $currentDepth).$resultIds[$id_category]['title'];
        } else {
            $name = '';
        }
		
        $categoriesSource[$currentDepth . '_' . $id_category] = $name;
		
        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth)) {
            foreach ($resultParents[$id_category] as $subcat) {
                $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_leoblogcat'], $currentDepth + 1, $categoriesSource);
            }
        }
    }


    public function getChild($id_leoblogcat = null, $id_lang = null, $id_shop = null, $active = false)
    {
        if (!$id_lang) {
            $id_lang = \Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = \Context::getContext()->shop->id;
        }

        $sql = ' SELECT m.*, md.*
                FROM '._DB_PREFIX_.'leoblogcat m
                LEFT JOIN '._DB_PREFIX_.'leoblogcat_lang md ON m.id_leoblogcat = md.id_leoblogcat AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'leoblogcat_shop bs ON m.id_leoblogcat = bs.id_leoblogcat AND bs.id_shop = '.(int)($id_shop);
        if ($active) {
            $sql .= ' WHERE m.`active`=1 ';
        }

        if ($id_leoblogcat != null) {
            # validate module
            $sql .= ' WHERE id_parent='.(int)$id_leoblogcat;
        }
        $sql .= ' ORDER BY `position` ';
        return \Db::getInstance()->executeS($sql);
    }
    
    public function getBlogsFont($params)
    {
        $config = \LeoBlogConfig::getInstance();
        $id_categories = '';
        if (isset($params['chk_cat'])) {
            # validate module
            $id_categories = $params['chk_cat'];
        }
        $order_by = isset($params['order_by']) ? $params['order_by'] : 'id_leoblog_blog';
        $order_way = isset($params['order_way']) ? $params['order_way'] : 'DESC';
        $helper = \LeoBlogHelper::getInstance();
        $limit = (int)$params['nb_blogs'];
        $blogs = \LeoBlogBlog::getListBlogsForApPageBuilder($id_categories, \Context::getContext()->language->id, $limit, $order_by, $order_way, array(), true);
        // $authors = array(); #validate module
        $image_w = (int)$config->get('listing_leading_img_width', 690);
        $image_h = (int)$config->get('listing_leading_img_height', 300);
        foreach ($blogs as $key => &$blog) {
            $blog = \LeoBlogHelper::buildBlog($helper, $blog, $image_w, $image_h, $config, true);
            
            if ((bool)\Module::isEnabled('appagebuilder')) {
                $appagebuilder = \Module::getInstanceByName('appagebuilder');
                $blog['description'] = $appagebuilder->buildShortCode($blog['description']);
                $blog['content'] = $appagebuilder->buildShortCode($blog['content']);
            }
            
            if ($blog['author_name']) {
                # HAVE AUTHOR IN BO
                $blog['author'] = $blog['author_name'];
                $blog['author_link'] = $helper->getBlogAuthorLink($blog['author_name']);
            } elseif ($blog['id_employee']) {
                # AUTO GENERATE AUTHOR
                $employee = new \Employee($blog['id_employee']);
                $blog['author'] = $employee->firstname.' '.$employee->lastname;
                $blog['author_link'] = $helper->getBlogAuthorLink($employee->id);
            } else {
                $blog['author'] = '';
                $blog['author_link'] = '';
            }
            
            # validate module
            unset($key);
        }
        return $blogs;
    }
}