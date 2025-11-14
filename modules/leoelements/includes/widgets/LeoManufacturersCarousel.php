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

class Widget_LeoManufacturersCarousel extends Widget_Base
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
        return 'LeoManufacturersCarousel';
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
        return Leo_Helper::__( 'Manufacturers Carousel', 'elementor' );
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
            return [ 'leo', 'ap', 'Manufacturers', 'carousel', 'blockcarousel' ];
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
            'a' => Leo_Helper::__('All manufacturers', 'elementor'),
            's' => Leo_Helper::__('Select manufacturers', 'elementor'),
            ];
        
        
        $this->start_controls_section(
                'section_title',
                array(
                        'label' => Leo_Helper::__( 'Manufacturers Carousel', 'elementor' ),
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
                        'label' => Leo_Helper::__('Source', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'a',
                        'options' => $source,
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
            
        $image_sizes = [];
        $product_images = \ImageType::getImagesTypes('manufacturers');	
        foreach( $product_images as $key => $product_image ) {
                $image_sizes[ $product_image['name'] ] = $product_image['name'];
        }
        
        $this->add_control(
                'imagetype',
                [
                        'label' => Leo_Helper::__('Image', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'small' . '_default',
//                        'options' => [
//                            'small_default' => 'small_default',
//                            'medium_default' => 'medium_default',
//                            'large_default' => 'large_default',
//                        ],
                        'options' => $image_sizes,
                ]
        );
        
        $this->add_control(
                'order_by',
                array(
                        'label'   => Leo_Helper::__( 'Order by', 'elementor' ),
                        'type'    => Controls_Manager::SELECT,
                        'options' => array(
                                'id_manufacturer'   => Leo_Helper::__( 'ID', 'elementor' ),
                                'name'              => Leo_Helper::__( 'Name', 'elementor' ),
                                'date_add'          => Leo_Helper::__( 'Date add', 'elementor' ),
                                'date_upd'          => Leo_Helper::__( 'Date update', 'elementor' ),
                        ),
                        'default' => 'id_manufacturer',
                )
        );
        
        $this->add_control(
                'order_way',
                array(
                        'label'   => Leo_Helper::__( 'Order way', 'elementor' ),
                        'type'    => Controls_Manager::SELECT,
                        'options' => array(
                                'DESC' => Leo_Helper::__( 'DESC', 'elementor' ),
                                'ASC'  => Leo_Helper::__( 'ASC', 'elementor' ),
                        ),
                        'default' => 'ASC',
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
        
        $module = \Module::getInstanceByName('leoelements');
        $context = \Context::getContext();
        $categoriesSource = $module->getCategories();
        $manufacturers = array();
        $manufacturers_temp = \Manufacturer::getManufacturers(false, 0, true, 1, '1000000', false, true, null);
        foreach ($manufacturers_temp as $key => $value) {
            $id = $value['id_manufacturer'];
            $manufacturers[$id] = $value['name'];
        }
        
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

            $settings = (array)$this->get_settings_for_display();
            $items = (array)$this->get_settings_for_display( 'items' );
            
            
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
            
            $manufacturers = array();
            if( $settings['source'] == 'a' )
            {
                $manufacturers = $this->getManufacturersSelect(false, 0, true, 1, $settings['limit'], false, true, null, $settings);
            } else {
                $manufacturers = $this->getManufacturersSelect(false, 0, true, 1, $settings['limit'], false, true, null, $settings);
            }
            
            
            $context->smarty->assign(
                    array(
                        'formAtts' => $settings,
                        'apLiveEdit' => '',
                        'apLiveEditEnd' => '',
                        'leo_include_file' => 'module:/leoelements/views/templates/front/LeoManufacturersCarouselSlick.tpl',
                            'manufacturers' => $manufacturers,
                            'settings' => $settings,
                            'img_manu_dir' => _THEME_MANU_DIR_,
                    )
            );
                
                
            $template_file_name = 'module:/leoelements/views/templates/front/LeoManufacturersCarousel.tpl';

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
    
    public function getManufacturersSelect($getNbProducts = false, $idLang = 0, $active = true, $p = false, $n = false, $allGroup = false, $group_by = false, $withProduct = false, $params = array())
    {
        //fix for previos version
        if ($params['order_by'] == 'position') {
            $params['order_by'] = 'id_manufacturer';
        }
        if (isset($params['order_way']) && $params['order_way'] == 'random') {
            $order = ' RAND()';
        } else {
            $order = (isset($params['order_by']) ? ' '.pSQL($params['order_by']) : '').(isset($params['order_way']) ? ' '.pSQL($params['order_way']) : '');
        }
        
        if (!$idLang) {
            $idLang = (int) \Configuration::get('PS_LANG_DEFAULT');
        }
        if (!\Group::isFeatureActive()) {
            $allGroup = true;
        }

        
        $sql = '
		SELECT m.*, ml.`description`, ml.`short_description`
		FROM `' . _DB_PREFIX_ . 'manufacturer` m'
        . \Shop::addSqlAssociation('manufacturer', 'm') .
        'INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = ' . (int) $idLang . ')' .
        'WHERE 1 ' .
        ($active ? 'AND m.`active` = 1 ' : '') .
        (isset($params['manu_ids']) && $params['manu_ids'] ? 'AND m.`id_manufacturer` IN ('.pSQL(implode(",",$params['manu_ids'])).')' : '') .
        ($withProduct ? 'AND m.`id_manufacturer` IN (SELECT `id_manufacturer` FROM `' . _DB_PREFIX_ . 'product`) ' : '') .
        ($group_by ? ' GROUP BY m.`id_manufacturer`' : '') .
        'ORDER BY ' . $order . ' 
		' . ($p ? ' LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n : '');
        
        
        $manufacturers = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($manufacturers === false) {
            return false;
        }

        if ($getNbProducts) {
            $sqlGroups = '';
            if (!$allGroup) {
                $groups = \FrontController::getCurrentCustomerGroups();
                $sqlGroups = (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) \Group::getCurrent()->id);
            }

            $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                '
					SELECT  p.`id_manufacturer`, COUNT(DISTINCT p.`id_product`) as nb_products
					FROM `' . _DB_PREFIX_ . 'product` p USE INDEX (product_manufacturer)
					' . \Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
					WHERE p.`id_manufacturer` != 0 AND product_shop.`visibility` NOT IN ("none")
					' . ($active ? ' AND product_shop.`active` = 1 ' : '') . '
					' . (\Group::isFeatureActive() && $allGroup ? '' : ' AND EXISTS (
						SELECT 1
						FROM `' . _DB_PREFIX_ . 'category_group` cg
						LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` ' . $sqlGroups . '
					)') . '
					GROUP BY p.`id_manufacturer`'
                );

            $counts = [];
            foreach ($results as $result) {
                $counts[(int) $result['id_manufacturer']] = (int) $result['nb_products'];
            }

            foreach ($manufacturers as $key => $manufacturer) {
                if (array_key_exists((int) $manufacturer['id_manufacturer'], $counts)) {
                    $manufacturers[$key]['nb_products'] = $counts[(int) $manufacturer['id_manufacturer']];
                } else {
                    $manufacturers[$key]['nb_products'] = 0;
                }
            }
        }

        $totalManufacturers = count($manufacturers);
        $rewriteSettings = (int) \Configuration::get('PS_REWRITING_SETTINGS');
        for ($i = 0; $i < $totalManufacturers; ++$i) {
            $manufacturers[$i]['link_rewrite'] = ($rewriteSettings ? \Tools::link_rewrite($manufacturers[$i]['name']) : 0);
        }

        return $manufacturers;
    }
    
}