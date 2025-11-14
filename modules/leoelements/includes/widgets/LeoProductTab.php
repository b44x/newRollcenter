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

class Widget_LeoProductTab extends Widget_Base
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
        return 'LeoProductTab';
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
        return 'Products Tabs <br/> ( Carousel / Grid )';
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
            return 'eicon-tabs';
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
        return [ 'leo', 'ap', 'product', 'carousel', 'tab' ];
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
		
        $this->_register_styling_controls();
    }
    
    protected function _register_content_controls()
    {
        $source = [
                's' => Leo_Helper::__('Select products', 'elementor'),
                'n' => Leo_Helper::__('New products', 'elementor'),
                'p' => Leo_Helper::__('Price drops', 'elementor'),
                'b' => Leo_Helper::__('Best sellers', 'elementor'),
                'c' => Leo_Helper::__('Products in Category', 'elementor'),
                'm' => Leo_Helper::__('Products in Brands', 'elementor'),
        ];
        
        $module = \Module::getInstanceByName('leoelements');
		
        $categoriesSource = $module->getCategories();

        $manufacturers = \Manufacturer::getManufacturers(false, \Context::getContext()->language->id, true, false, false, false, true);

        $manufacturersSource = [];

        foreach ( $manufacturers as $key => $manufacturer ) {			
                $manufacturersSource[$manufacturer['id_manufacturer']] =  $manufacturer['name'];
        }

        $this->start_controls_section(
                'section_options',
                [
                        'label' => Leo_Helper::__( 'Product Tabs', 'elementor' ),
                ]
        );
        
        $repeater = new Repeater();
		
        $repeater->add_control(
                'section_heading_title',
                [
                        'label' => Leo_Helper::__('Configuration', 'elementor'),
                        'type' => Controls_Manager::HEADING,
                ]
        );

        $repeater->add_control(
                'item_title',
                [
                        'label'     => Leo_Helper::__( 'Title', 'elementor' ),
                        'type'      => Controls_Manager::TEXTAREA,
                        'rows'      => '1',
                        'default'   => 'Tab #1',
                ]
        );

        $repeater->add_control(
                'icon_type',
                [
                        'label' => Leo_Helper::__('Icon type', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'none',
                        'options' => [
                                'none' => Leo_Helper::__('None', 'elementor'),
                                'icon' => Leo_Helper::__('Icon', 'elementor'),
                                'image' => Leo_Helper::__('Image', 'elementor'),
                        ],
                ]
        );
        $repeater->add_control(
                'item_icon_title',
                [
                        'label'       => Leo_Helper::__( 'Icon', 'elementor' ),
                        'type'        => Controls_Manager::ICONS,
                        'label_block' => 'true',
                        'condition' => [
                                'icon_type' => 'icon',
                        ],
                ]
        );

        $repeater->add_control(
                'item_image_title',
                [
                        'label' => Leo_Helper::__( 'Choose Image', 'elementor' ),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                                'url' => Utils::get_placeholder_image_src(),
                        ],
                        'condition' => [
                                'icon_type' => 'image',
                        ],
                ]
        );

//        $repeater->add_control(
//                'section_heading_content',
//                [
//                        'label' => Leo_Helper::__('Content', 'elementor'),
//                        'type' => Controls_Manager::HEADING,
//                        'separator' => 'before'
//                ]
//        );

        $repeater->add_control(
                'source',
                [
                        'label' => Leo_Helper::__('Source of products', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'n',
                        'options' => $source,
                ]
        );

        $repeater->add_control(
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

        $repeater->add_control(
                'manufacturer',
                [
                        'label' => Leo_Helper::__('Select brand', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => '',
                        'options' => $manufacturersSource,
                        'condition' => [
                                'source' => 'm',
                        ]
                ]
        );

        $repeater->add_control(
                'product_ids',
                [
                        'label'       => Leo_Helper::__( 'Select products', 'elementor' ),
                        'type'        => Controls_Manager::AUTOCOMPLETE,
                        'search'      => 'leo_get_products_by_query',
                        'render'      => 'leo_get_products_title_by_id',
                        'multiple'    => true,
                        'label_block' => true,
                        'condition' => [
                                'source' => 's',
                        ]
                ]
        );

        $repeater->add_control(
                'limit',
                [
                        'label' => Leo_Helper::__('Product Limit', 'elementor'),
                        'type' => Controls_Manager::NUMBER,
                        'min' => 1,
                        'default' => 10,
                        'condition' => [
                                'source!' => 's',
                        ]
                ]
        );

        $repeater->add_control(
                'order_by',
                [
                        'label' => Leo_Helper::__('Order By', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'position',
                        'options' => [
                                'position' => Leo_Helper::__('Position', 'elementor'),
                                'name' => Leo_Helper::__('Name', 'elementor'),
                                'date_add' => Leo_Helper::__('Date add', 'elementor'),
                                'price' => Leo_Helper::__('Price', 'elementor'),
                        ],
                        'condition' => [
                                'source!' => 's',
                                'randomize!' => 'yes',
                        ]
                ]
        );

        $repeater->add_control(
                'order_way',
                [
                        'label' => Leo_Helper::__('Order Direction', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'ASC',
                        'options' => [
                                'ASC' => Leo_Helper::__('Ascending', 'elementor'),
                                'DESC' => Leo_Helper::__('Descending', 'elementor'),
                        ],
                        'condition' => [
                                'source!' => 's',
                                'randomize!' => 'yes',
                        ]
                ]
        );

        # PRODUCT_LIST
        $id_shop = (int)\Context::getContext()->shop->id;
        
        $sql = new \DbQuery();
        $sql->select('pl.*');
        $sql->from('leoelements_product_list', 'pl');
        $sql->innerJoin('leoelements_product_list_shop', 'pl_s', 'pl_s.`id_leoelements_product_list` = pl.`id_leoelements_product_list`');
        $sql->where('pl_s.`id_shop` = ' . $id_shop);
        $pl_db = \Db::getInstance()->executeS($sql);
        $source_pl = array('default'=>'default');
        foreach ($pl_db as $key => $value) {
            $source_pl[ $value['plist_key'] ]=$value['name'];
        }
        # PRODUCT_LIST
        
        $repeater->add_control(
                'source_pl',
                [
                        'label' => Leo_Helper::__('Template', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'default',
                        'options' => $source_pl,
                ]
        );

        /**
        * Repeater settings
        */
       $this->add_control(
               'items',
               [
                       'label' => Leo_Helper::__( 'Tabs Items', 'elementor' ),
                       'type'    => Controls_Manager::REPEATER,
                       'fields'  => $repeater->get_controls(),
                       'default' => [
                               [
                                       'item_title' => 'Tab #1',
                                       'icon_type' => 'none',
                                       'source' => 'n',
                                       'category' => '',
                                       'manufacturer' => '',
                                       'product_ids' => '',
                                       'limit'	=> 10,
                                       'randomize'	=> '',
                                       'order_by'	=> 'position',
                                       'order_way'	=> 'ASC'
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
            
            
            $image_sizes = [];
            $product_images = \ImageType::getImagesTypes('products');	

            foreach( $product_images as $key => $product_image ) {
                    $image_sizes[ $product_image['name'] ] = $product_image['name'];
            }
            
            $this->add_control(
                    'view_type',
                    [
                            'label'   => Leo_Helper::__( 'View type', 'elementor' ),
                            'type'    => Controls_Manager::SELECT,
                            'default' => 'carousel',
                            'options' => [
                                    'carousel' => Leo_Helper::__( 'Carousel', 'elementor' ),
                                    'grid'     => Leo_Helper::__( 'Grid', 'elementor' ),
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
                                    'view_type' => 'carousel',
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
                                    'view_type' => 'carousel',
                            ],
                            'frontend_available' => true,
                    ]
            );

            $this->add_control(
                    'navigation',
                    array(
                            'label'              => Leo_Helper::__( 'Navigation', 'elementor' ),
                            'type'               => Controls_Manager::SELECT,
                            'default'            => 'both',
                            'options'            => array(
                                    'both'   => Leo_Helper::__( 'Arrows and Dots', 'elementor' ),
                                    'arrows' => Leo_Helper::__( 'Arrows', 'elementor' ),
                                    'dots'   => Leo_Helper::__( 'Dots', 'elementor' ),
                                    'none'   => Leo_Helper::__( 'None', 'elementor' ),
                            ),
                            'frontend_available' => true,
                            'condition'   => [
                                    'view_type' => 'carousel',
                            ],
                    )
            );

            $this->add_control(
                    'pause_on_hover',
                    array(
                            'label'              => Leo_Helper::__( 'Pause on Hover', 'elementor' ),
                            'type'               => Controls_Manager::SELECT,
                            'default'            => 'yes',
                            'options'            => array(
                                    'yes' => Leo_Helper::__( 'Yes', 'elementor' ),
                                    'no'  => Leo_Helper::__( 'No', 'elementor' ),
                            ),
                            'frontend_available' => true,
                            'condition'   => [
                                    'view_type' => 'carousel',
                            ],
                    )
            );

            $this->add_control(
                    'autoplay',
                    array(
                            'label'              => Leo_Helper::__( 'Autoplay', 'elementor' ),
                            'type'               => Controls_Manager::SELECT,
                            'default'            => 'yes',
                            'options'            => array(
                                    'yes' => Leo_Helper::__( 'Yes', 'elementor' ),
                                    'no'  => Leo_Helper::__( 'No', 'elementor' ),
                            ),
                            'frontend_available' => true,
                            'condition'   => [
                                    'view_type' => 'carousel',
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
                                    'view_type' => 'carousel',
                            ],
                    )
            );

            $this->add_control(
                    'infinite',
                    array(
                            'label'              => Leo_Helper::__( 'Infinite Loop', 'elementor' ),
                            'type'               => Controls_Manager::SELECT,
                            'default'            => 'yes',
                            'options'            => array(
                                    'yes' => Leo_Helper::__( 'Yes', 'elementor' ),
                                    'no'  => Leo_Helper::__( 'No', 'elementor' ),
                            ),
                            'frontend_available' => true,
                            'condition'   => [
                                    'view_type' => 'carousel',
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
                                    'view_type' => 'carousel',
                            ],
                    )
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
        
//        $this->add_responsive_control(
//                'tab_wrapper_align',
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
//                        'default' => '',
//                        'selectors' => [
//                                '{{WRAPPER}} .widget-tabs-wrapper' => 'text-align: {{VALUE}};',
//                        ],
//                ]
//        );
		
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                        'name' => 'tab_wrapper_border',
                        'selector' => '{{WRAPPER}} .widget-tabs-wrapper',
                ]
        );

        $this->add_responsive_control(
                'tab_wrapper_border_radius',
                [
                        'label' => Leo_Helper::__( 'Border Radius', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .widget-tabs-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ]
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                        'name' => 'tab_wrapper_box_shadow',
                        'selector' => '{{WRAPPER}} .widget-tabs-wrapper',
                ]
        );

        $this->add_control(
            'tab_wrapper_background_color',
            [
                'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
                'tab_wrapper_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'axiosy' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .widget-tabs-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );

        $this->add_responsive_control(
                'tab_wrapper_margin',
                [
                        'label' => Leo_Helper::__( 'Margin', 'axiosy' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px' ],
                        'selectors' => [
                                '{{WRAPPER}} .widget-tabs-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        
        $this->end_controls_section();
        
        
        /************************************************************/
        $this->start_controls_section(
                'section_style_title',
                [
                        'label' => Leo_Helper::__( 'Title', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
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
                        'default' => 'center',
                        'selectors' => [
                                '{{WRAPPER}} .widget-tabs-wrapper' => 'text-align: {{VALUE}};',
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
                                '{{WRAPPER}} .widget-tab-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
                                '{{WRAPPER}} .widget-tab-title' => 'color: {{VALUE}};',
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
                        'selector' => '{{WRAPPER}} .widget-tab-title',
                ]
        );
        $this->add_responsive_control(
                'title_padding',
                [
                        'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'selectors' => [
                                '{{WRAPPER}} .widget-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                ]
        );
        $this->add_control(
                'active_title_color',
                [
                        'label' => Leo_Helper::__( 'Active Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                                '{{WRAPPER}} .widget-tab-title.active' => 'color: {{VALUE}};',
                        ],
//                        'scheme' => [
//                                'type' => Scheme_Color::get_type(),
//                                'value' => Scheme_Color::COLOR_1,
//                        ],
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
        if ( Leo_Helper::is_admin() ) {
                return;
        }

        $settings = $this->get_settings_for_display();
        unset($settings['items']);
        $settings = $this->_render_view_setting_attributes( $settings, [ 'products product-type-1' ], [] );
        $items = $this->get_settings_for_display( 'items' );
        
        $module = \Module::getInstanceByName('leoelements');
        $context = \Context::getContext();
        
        $context->smarty->assign(
            array(
                'items' => $items,
                'settings' => $settings,
                'apwidget' => $this,
            )
        );
        
        $template_file_name = 'module:/leoelements/views/templates/front/LeoProductTab.tpl';
        $out_put = '';
        $out_put           .= $context->smarty->fetch( $template_file_name );

        echo $out_put;

        return '';
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
        
        $theme_template_path = _PS_THEME_DIR_ . 'templates/catalog/_partials/miniatures/product.tpl';
        if( $settings['source_pl'] != 'default' && file_exists(_PS_THEME_DIR_ . 'modules/leoelements/views/templates/front/products/'.$settings['source_pl'].'.tpl')){
            $theme_template_path = _PS_THEME_DIR_ . 'modules/leoelements/views/templates/front/products/'.$settings['source_pl'].'.tpl';
        }
        
        if($data) {
            
            $id_shop = (int)\Context::getContext()->shop->id;

            $sql = new \DbQuery();
            $sql->select('pl.*');
            $sql->from('leoelements_product_list', 'pl');
            $sql->innerJoin('leoelements_product_list_shop', 'pl_s', 'pl_s.`id_leoelements_product_list` = pl.`id_leoelements_product_list`');
            $sql->where('pl_s.`id_shop` = ' . $id_shop . ' AND pl.`plist_key` = ' . "'{$settings['source_pl']}'" );
            $pl_db = \Db::getInstance()->getRow($sql);

            $settings['pl_class'] = '';
            if($pl_db){
                $settings['pl_class'] = $pl_db['class'];
            }
            
            $products_for_template = $data;
            $context->smarty->assign(
                    array(
                        'formAtts' => $settings,
                        'leo_products'         => $products_for_template,
                        'elementprefix'       => 'single-product',
                        'theme_template_path' => $theme_template_path,
                        'settings' => $settings,
                    )
            );
            $template_file_name = 'module:/leoelements/views/templates/front/LeoProductTab2.tpl';
            $out_put           = $context->smarty->fetch( $template_file_name );

            return $out_put;
        }else {
            echo '
            <div class="alert alert-warning leo-lib-error">
                No products at this time.
            </div>
            ';
        }
        
        return '';
    }
}