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

class Widget_LeoProductCarousel extends Widget_Base
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
        return 'LeoProductCarousel';
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
        return 'Products <br/> ( Carousel / Grid )';
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
            return 'eicon-slider-push';
    }
    
    public function get_keywords() {
            return [ 'leo', 'ap', 'product', 'carousel' ];
    }
    
    public function get_script_depends() {
            return [ 'jquery-slick' ];
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

    protected function _register_content_controls() {
        $source = [
            's' => Leo_Helper::__('Select products', 'elementor'),
            'n' => Leo_Helper::__('New products', 'elementor'),
            'p' => Leo_Helper::__('Price drops', 'elementor'),
            'b' => Leo_Helper::__('Best sellers', 'elementor'),
            'c' => Leo_Helper::__('Products in Category', 'elementor'),
            'm' => Leo_Helper::__('Products in Brands', 'elementor'),
            ];
        
        $module = \Module::getInstanceByName('leoelements');
        $context = \Context::getContext();
        $categoriesSource = $module->getCategories();

        $manufacturers = \Manufacturer::getManufacturers(false, \Context::getContext()->language->id, true, false, false, false, true);
        $manufacturersSource = [];
        foreach ( $manufacturers as $key => $manufacturer ) {			
            $manufacturersSource[$manufacturer['id_manufacturer']] =  $manufacturer['name'];
        }
        
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

        
        $this->start_controls_section(
                'section_title',
                array(
                        'label' => Leo_Helper::__( 'Product Options', 'elementor' ),
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
                'source',
                [
                        'label' => Leo_Helper::__('Source of products', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 's',
                        'options' => $source,
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

        $this->add_control(
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

        $this->add_control(
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

        $this->add_control(
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

        $this->add_control(
                'order_by',
                array(
                        'label'   => Leo_Helper::__( 'Order by', 'elementor' ),
                        'type'    => Controls_Manager::SELECT,
                        'options' => array(
                                'id_product'   => Leo_Helper::__( 'Product Id', 'elementor' ),
                                'price'        => Leo_Helper::__( 'Price', 'elementor' ),
                                'date_add'     => Leo_Helper::__( 'Published Date', 'elementor' ),
                                'name'         => Leo_Helper::__( 'Product Name', 'elementor' ),
                                'position'     => Leo_Helper::__( 'Position', 'elementor' ),
                                'manufacturer' => Leo_Helper::__( 'Manufacturer', 'elementor' ),
                        ),
                        'default' => 'id_product',
                            'condition' => [
                                        'source!' => 's',
                                ]
                )
        );

        $this->add_control(
                'order_way',
                array(
                        'label'   => Leo_Helper::__( 'Order', 'elementor' ),
                        'type'    => Controls_Manager::SELECT,
                        'options' => array(
                                'DESC' => Leo_Helper::__( 'DESC', 'elementor' ),
                                'ASC'  => Leo_Helper::__( 'ASC', 'elementor' ),
                        ),
                        'default' => 'ASC',
                            'condition' => [
                                        'source!' => 's',
                                ]
                )
        );

        $this->add_control(
                'source_pl',
                [
                        'label' => Leo_Helper::__('Template', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'default',
                        'options' => $source_pl,
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
                                '{{WRAPPER}} .elementor-LeoProductCarousel.grid .item' => '-ms-flex: 0 0 calc(100%/{{VALUE}}); flex: 0 0 calc(100%/{{VALUE}}); max-width: calc(100%/{{VALUE}});'
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
                                'autoplay' => 'yes',
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
                                'autoplay' => 'yes',
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
    }
    

    protected function _register_styling_controls() {
        $this->start_controls_section(
                    'section_style_product_box',
                    array(
                            'label' => Leo_Helper::__( 'Product Box', 'elementor' ),
                            'tab'   => Controls_Manager::TAB_STYLE,

                    )
            );
            $this->add_responsive_control(
                    'padding',
                    array(
                            'label'      => Leo_Helper::__( 'Padding', 'elecounter' ),
                            'type'       => Controls_Manager::DIMENSIONS,
                            'size_units' => array( 'px', '%', 'em' ),
                            'devices'    => array( 'desktop', 'tablet', 'mobile' ),
                            'selectors'  => array(
                                    '{{WRAPPER}} .product-miniature' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ),
                    )
            );
            $this->add_responsive_control(
                    'margin',
                    array(
                            'label'      => Leo_Helper::__( 'Margin', 'elecounter' ),
                            'type'       => Controls_Manager::DIMENSIONS,
                            'size_units' => array( 'px', '%', 'em' ),
                            'devices'    => array( 'desktop', 'tablet', 'mobile' ),
                            'selectors'  => array(
                                    '{{WRAPPER}} .product-miniature' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ),
                    )
            );
            $this->end_controls_section();
            $this->start_controls_section(
                    'section_style_product_image',
                    array(
                            'label' => Leo_Helper::__( 'Product Image', 'elementor' ),
                            'tab'   => Controls_Manager::TAB_STYLE,

                    )
            );
            $this->add_responsive_control(
                    'img_padding',
                    array(
                            'label'      => Leo_Helper::__( 'Padding', 'elecounter' ),
                            'type'       => Controls_Manager::DIMENSIONS,
                            'size_units' => array( 'px', '%', 'em' ),
                            'devices'    => array( 'desktop', 'tablet', 'mobile' ),
                            'selectors'  => array(
                                    '{{WRAPPER}} .product-miniature .thumbnail-container .product-thumbnail img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ),
                    )
            );
            $this->add_group_control(
                    Group_Control_Border::get_type(),
                    array(
                            'name'     => 'border',
                            'label'    => Leo_Helper::__( 'Border', 'plugin-domain' ),
                            'selector' => '{{WRAPPER}} .product-miniature .thumbnail-container .product-thumbnail img',
                    )
            );
            $this->end_controls_section();
            $this->start_controls_section(
                    'section_style_product_content',
                    array(
                            'label' => Leo_Helper::__( 'Content', 'elementor' ),
                            'tab'   => Controls_Manager::TAB_STYLE,
                    )
            );
            $this->add_responsive_control(
                    'alignment',
                    array(
                            'label'        => Leo_Helper::__( 'Alignment', 'elecounter' ),
                            'type'         => Controls_Manager::CHOOSE,
                            'devices'      => array( 'desktop', 'tablet', 'mobile' ),
                            'options'      => array(
                                    'left'    => array(
                                            'title' => Leo_Helper::__( 'Left', 'elecounter' ),
                                            'icon'  => 'fa fa-align-left',
                                    ),
                                    'center'  => array(
                                            'title' => Leo_Helper::__( 'Center', 'elecounter' ),
                                            'icon'  => 'fa fa-align-center',
                                    ),
                                    'right'   => array(
                                            'title' => Leo_Helper::__( 'Right', 'elecounter' ),
                                            'icon'  => 'fa fa-align-right',
                                    ),
                                    'justify' => array(
                                            'title' => Leo_Helper::__( 'Justify', 'elecounter' ),
                                            'icon'  => 'fa fa-align-justify',
                                    ),
                            ),
                            'prefix_class' => 'alignment%s',
                    )
            );
            $this->start_controls_tabs( 'product_style' );
            $this->start_controls_tab(
                    'product_title',
                    array(
                            'label' => Leo_Helper::__( 'Title', 'elementor' ),
                    )
            );
            $this->add_control(
                    'title_color',
                    array(
                            'label'     => Leo_Helper::__( 'Title Color', 'elementor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => array(
                                    '{{WRAPPER}} .product-miniature .product-title a' => 'color: {{VALUE}};',
                            ),
                    )
            );
            $this->add_control(
                    'title_hover_color',
                    array(
                            'label'     => Leo_Helper::__( 'Title Hover Color', 'elementor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => array(
                                    '{{WRAPPER}} .product-miniature .product-title a:hover' => 'color: {{VALUE}};',
                            ),
                    )
            );
            $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                            'name'     => 'title_typography',
                            'label'    => Leo_Helper::__( 'Title Typography', 'elementor' ),
                            'selector' => '{{WRAPPER}} .product-miniature .product-title a',
                    )
            );
            $this->end_controls_tab();
            $this->start_controls_tab(
                    'product_price',
                    array(
                            'label' => Leo_Helper::__( 'Price', 'elementor' ),
                    )
            );

            $this->add_control(
                    'price_color',
                    array(
                            'label'     => Leo_Helper::__( 'Price Color', 'elementor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => array(
                                    '{{WRAPPER}} .product-miniature .product-price-and-shipping' => 'color: {{VALUE}};',
                            ),
                    )
            );
            $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                            'name'     => 'product_price_typography',
                            'label'    => Leo_Helper::__( 'Price Typography', 'elementor' ),
                            'selector' => '{{WRAPPER}} .product-miniature .product-price-and-shipping',
                    )
            );
            $this->end_controls_tab();
            $this->start_controls_tab(
                    'product_quick_view',
                    array(
                            'label' => Leo_Helper::__( 'Quickview', 'elementor' ),
                    )
            );

            $this->add_control(
                    'product_quick_view_color',
                    array(
                            'label'     => Leo_Helper::__( 'Quickview Color', 'elementor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => array(
                                    '{{WRAPPER}} .highlighted-informations .quick-view' => 'color: {{VALUE}};',
                            ),
                    )
            );
            $this->add_control(
                    'product_quick_view_hover_color',
                    array(
                            'label'     => Leo_Helper::__( 'Quickview Hover Color', 'elementor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => array(
                                    '{{WRAPPER}} .highlighted-informations .quick-view:hover' => 'color: {{VALUE}};',
                            ),
                    )
            );
            $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                            'name'     => 'product_quick_view_typography',
                            'label'    => Leo_Helper::__( 'Quickview Typography', 'elementor' ),
                            'selector' => '{{WRAPPER}} .highlighted-informations .quick-view',
                    )
            );
            $this->end_controls_tab();

            $this->end_controls_section();

            $this->start_controls_section(
                    'section_style_navigation',
                    array(
                            'label'     => Leo_Helper::__( 'Navigation', 'elementor' ),
                            'tab'       => Controls_Manager::TAB_STYLE,
                            'condition' => array(
                                    'navigation' => array( 'arrows', 'dots', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'heading_style_arrows',
                    array(
                            'label'     => Leo_Helper::__( 'Arrows', 'elementor' ),
                            'type'      => Controls_Manager::HEADING,
                            'separator' => 'before',
                            'condition' => array(
                                    'navigation' => array( 'arrows', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'arrows_position',
                    array(
                            'label'     => Leo_Helper::__( 'Position', 'elementor' ),
                            'type'      => Controls_Manager::SELECT,
                            'default'   => 'inside',
                            'options'   => array(
                                    'inside'  => Leo_Helper::__( 'Inside', 'elementor' ),
                                    'outside' => Leo_Helper::__( 'Outside', 'elementor' ),
                            ),
                            'condition' => array(
                                    'navigation' => array( 'arrows', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'arrows_size',
                    array(
                            'label'     => Leo_Helper::__( 'Size', 'elementor' ),
                            'type'      => Controls_Manager::SLIDER,
                            'range'     => array(
                                    'px' => array(
                                            'min' => 20,
                                            'max' => 60,
                                    ),
                            ),
                            'selectors' => array(
                                    '{{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
                            ),
                            'condition' => array(
                                    'navigation' => array( 'arrows', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'arrows_color',
                    array(
                            'label'     => Leo_Helper::__( 'Color', 'elementor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => array(
                                    '{{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:before' => 'color: {{VALUE}};',
                            ),
                            'condition' => array(
                                    'navigation' => array( 'arrows', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'heading_style_dots',
                    array(
                            'label'     => Leo_Helper::__( 'Dots', 'elementor' ),
                            'type'      => Controls_Manager::HEADING,
                            'separator' => 'before',
                            'condition' => array(
                                    'navigation' => array( 'dots', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'dots_position',
                    array(
                            'label'     => Leo_Helper::__( 'Position', 'elementor' ),
                            'type'      => Controls_Manager::SELECT,
                            'default'   => 'outside',
                            'options'   => array(
                                    'outside' => Leo_Helper::__( 'Outside', 'elementor' ),
                                    'inside'  => Leo_Helper::__( 'Inside', 'elementor' ),
                            ),
                            'condition' => array(
                                    'navigation' => array( 'dots', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'dots_size',
                    array(
                            'label'     => Leo_Helper::__( 'Size', 'elementor' ),
                            'type'      => Controls_Manager::SLIDER,
                            'range'     => array(
                                    'px' => array(
                                            'min' => 5,
                                            'max' => 10,
                                    ),
                            ),
                            'selectors' => array(
                                    '{{WRAPPER}} .elementor-image-carousel-wrapper .elementor-image-carousel .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
                            ),
                            'condition' => array(
                                    'navigation' => array( 'dots', 'both' ),
                            ),
                    )
            );

            $this->add_control(
                    'dots_color',
                    array(
                            'label'     => Leo_Helper::__( 'Color', 'elementor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => array(
                                    '{{WRAPPER}} .elementor-image-carousel-wrapper .elementor-image-carousel .slick-dots li button:before' => 'color: {{VALUE}};',
                            ),
                            'condition' => array(
                                    'navigation' => array( 'dots', 'both' ),
                            ),
                    )
            );

            $this->end_controls_section();
    }
    
    
    protected function getIdFromTitle($ids) {
            $array=array();
            foreach($ids as $id){
                    $exp=explode('_',$id);
                    $array[]=$exp[0];
            }
            return $array;
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
        $settings = $this->_render_view_setting_attributes( $settings, [ 'products product-type-1' ], [] );

        $attrs = $settings;

        $module = \Module::getInstanceByName('leoelements');
        $context = \Context::getContext();
        $out_put  = '';

        $id_lang = $context->language->id;
        $front   = true;
        if ( ! in_array( $context->controller->controller_type, array( 'front', 'modulefront' ) ) ) {
                $front = false;
        }

        $data = array();
        if( $settings['source'] != 's' ){
            $data = $module->_prepProducts( $attrs );
        }else{
            $data = $module->_prepProductsSelected( $attrs );
        }
        
        $theme_template_path = _PS_THEME_DIR_ . 'templates/catalog/_partials/miniatures/product.tpl';
        if( $settings['source_pl'] != 'default' ){
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
                        'page' => $context->controller->getTemplateVarPage(),
                        'leo_products'         => $products_for_template,
                        'elementprefix'       => 'single-product',
                        'theme_template_path' => $theme_template_path,
                        'settings' => $settings,
                    )
            );
            $template_file_name = 'module:/leoelements/views/templates/front/LeoProductCarousel.tpl';

            $out_put           .= $context->smarty->fetch( $template_file_name );

            echo $out_put;

            return;
        }else {
            echo '
            <div class="alert alert-warning leo-lib-error">
                No products at this time.
            </div>
            ';
        }
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
}