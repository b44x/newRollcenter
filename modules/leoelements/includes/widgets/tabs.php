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

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Widget_Tabs extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve tabs widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tabs';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve tabs widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return Leo_Helper::__( 'Tabs', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve tabs widget icon.
	 *
	 * @since 1.0.0
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
		return [ 'tabs', 'accordion', 'toggle' ];
	}

	/**
	 * Register tabs widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => Leo_Helper::__( 'Tabs', 'elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => Leo_Helper::__( 'Title & Description', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => Leo_Helper::__( 'Tab Title', 'elementor' ),
				'placeholder' => Leo_Helper::__( 'Tab Title', 'elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => Leo_Helper::__( 'Content', 'elementor' ),
				'default' => Leo_Helper::__( 'Tab Content', 'elementor' ),
				'placeholder' => Leo_Helper::__( 'Tab Content', 'elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'show_label' => false,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => Leo_Helper::__( 'Tabs Items', 'elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => Leo_Helper::__( 'Tab #1', 'elementor' ),
						'tab_content' => Leo_Helper::__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
					],
					[
						'tab_title' => Leo_Helper::__( 'Tab #2', 'elementor' ),
						'tab_content' => Leo_Helper::__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'view',
			[
				'label' => Leo_Helper::__( 'View', 'elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->add_control(
			'type',
			[
				'label' => Leo_Helper::__( 'Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => Leo_Helper::__( 'Horizontal', 'elementor' ),
					'vertical' => Leo_Helper::__( 'Vertical', 'elementor' ),
				],
				'prefix_class' => 'elementor-tabs-view-',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => Leo_Helper::__( 'Tabs', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
                
//                $this->add_responsive_control(
//                        'tab_wrapper_align',
//                        [
//                                'label' => Leo_Helper::__( 'Alignment', 'elementor' ),
//                                'type' => Controls_Manager::CHOOSE,
//                                'options' => [
//                                        'left' => [
//                                                'title' => Leo_Helper::__( 'Left', 'elementor' ),
//                                                'icon' => 'eicon-text-align-left',
//                                        ],
//                                        'center' => [
//                                                'title' => Leo_Helper::__( 'Center', 'elementor' ),
//                                                'icon' => 'eicon-text-align-center',
//                                        ],
//                                        'right' => [
//                                                'title' => Leo_Helper::__( 'Right', 'elementor' ),
//                                                'icon' => 'eicon-text-align-right',
//                                        ],
//                                        'justify' => [
//                                                'title' => Leo_Helper::__( 'Justified', 'elementor' ),
//                                                'icon' => 'eicon-text-align-justify',
//                                        ],
//                                ],
//                                'default' => '',
//                                'selectors' => [
//                                        '{{WRAPPER}} .widget-tabs-wrapper' => 'text-align: {{VALUE}};',
//                                ],
//                        ]
//                );

		$this->add_control(
			'navigation_width',
			[
				'label' => Leo_Helper::__( 'Navigation Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-tabs-wrapper' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'type' => 'vertical',
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => Leo_Helper::__( 'Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title, {{WRAPPER}} .elementor-tab-title:before, {{WRAPPER}} .elementor-tab-title:after, {{WRAPPER}} .elementor-tab-content, {{WRAPPER}} .elementor-tabs-content-wrapper' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => Leo_Helper::__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-mobile-title, {{WRAPPER}} .elementor-tab-desktop-title.elementor-active, {{WRAPPER}} .elementor-tab-title:before, {{WRAPPER}} .elementor-tab-title:after, {{WRAPPER}} .elementor-tab-content, {{WRAPPER}} .elementor-tabs-content-wrapper' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-desktop-title.elementor-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tabs-content-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

//		$this->add_control(
//			'heading_title',
//			[
//				'label' => Leo_Helper::__( 'Title', 'elementor' ),
//				'type' => Controls_Manager::HEADING,
//				'separator' => 'before',
//			]
//		);




                
                $this->start_controls_tabs( 'tabs_background_overlay' );
                $this->end_controls_tabs();
                
                
//		$this->add_control(
//			'heading_content',
//			[
//				'label' => Leo_Helper::__( 'Content', 'elementor' ),
//				'type' => Controls_Manager::HEADING,
//				'separator' => 'before',
//			]
//		);



		$this->end_controls_section();
                
            /************************************************************/
            $this->start_controls_section(
                    'title_section_style',
                    [
                            'label' => Leo_Helper::__( 'Title', 'elementor' ),
                            'tab'   => Controls_Manager::TAB_STYLE,
                    ]
            );
            
            $this->start_controls_tabs( 'title_style' );    /**************************** 2 ********************************/
            
            $this->start_controls_tab(                      /**************************** 2A ********************************/
                'title_normal',
                [
                        'label' => Leo_Helper::__( 'Normal', 'elementor' ),
                ]
            );
            $this->add_control(
                    'tab_color',
                    [
                            'label' => Leo_Helper::__( 'Color', 'elementor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-title' => 'color: {{VALUE}};',
                            ],
                            'scheme' => [
                                    'type' => Scheme_Color::get_type(),
                                    'value' => Scheme_Color::COLOR_1,
                            ],
//                                'default' => '#6ec1e4'
                    ]
            );
            $this->add_control(
                    'tab_background_color',
                    [
                            'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
//                                    '{{WRAPPER}} .elementor-tabs-content-wrapper' => 'background-color: {{VALUE}};',
                            ],
                    ]
            );

		
            $this->end_controls_tab();                      /**************************** 2A ********************************/
            
            $this->start_controls_tab(                      /**************************** 2B ********************************/
                'title_active',
                [
                        'label' => Leo_Helper::__( 'Active', 'elementor' ),
                ]
            );
            $this->add_control(
                    'tab_active_color',
                    [
                            'label' => Leo_Helper::__( 'Color', 'elementor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-title.elementor-active' => 'color: {{VALUE}};',
                            ],
                            'scheme' => [
                                    'type' => Scheme_Color::get_type(),
                                    'value' => Scheme_Color::COLOR_4,
                            ],
//                                'default' => '#61ce70'
                    ]
            );
            $this->add_control(
                    'tab_active_background_color',
                    [
                            'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-title.elementor-active' => 'background-color: {{VALUE}};',
//                                    '{{WRAPPER}} .elementor-tabs-content-wrapper' => 'background-color: {{VALUE}};',
                            ],
                    ]
            );
            $this->end_controls_tab();                     /**************************** 2B ********************************/
            
            $this->end_controls_tabs();                     /**************************** 2 ********************************/
            
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
//                            'prefix_class' => 'leo%s-align-',
                            'separator' => 'before',
                            'selectors' => [
                                '{{WRAPPER}} .elementor-tabs-wrapper' => 'display: flex; justify-content: {{VALUE}}; text-align: {{VALUE}};',
                            ],
                            'condition' => [
                                    'type' => 'horizontal',
                            ]
                    ]
            );
                
            $this->add_responsive_control(
                    'tab_space',
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
                                    '{{WRAPPER}} .elementor-tab-title' => 'padding-bottom: {{SIZE}}{{UNIT}} !important;',
                            ],
//                            'separator' => 'before',
                    ]
            );
            
//            $this->add_control(
//                    'tab_color',
//                    [
//                            'label' => Leo_Helper::__( 'Color', 'elementor' ),
//                            'type' => Controls_Manager::COLOR,
//                            'selectors' => [
//                                    '{{WRAPPER}} .elementor-tab-title' => 'color: {{VALUE}};',
//                            ],
//                            'scheme' => [
//                                    'type' => Scheme_Color::get_type(),
//                                    'value' => Scheme_Color::COLOR_1,
//                            ],
////                                'default' => '#6ec1e4'
//                    ]
//            );
//            
//            $this->add_control(
//			'tab_active_color',
//			[
//				'label' => Leo_Helper::__( 'Active Color', 'elementor' ),
//				'type' => Controls_Manager::COLOR,
//				'selectors' => [
//					'{{WRAPPER}} .elementor-tab-title.elementor-active' => 'color: {{VALUE}};',
//				],
//				'scheme' => [
//					'type' => Scheme_Color::get_type(),
//					'value' => Scheme_Color::COLOR_4,
//				],
////                                'default' => '#61ce70'
//			]
//		);
            
            
            $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                            'name' => 'tab_typography',
                            'selector' => '{{WRAPPER}} .elementor-tab-title',
                            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                    ]
            );
            
            $this->add_responsive_control(
                    'title_padding',
                    [
                            'label' => Leo_Helper::__( 'Padding', 'elementor' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', 'em', '%' ],
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                    ]
            );

            $this->end_controls_section();
            
            /************************************************************/
            $this->start_controls_section(
                    'content_section_style',
                    [
                            'label' => Leo_Helper::__( 'Content', 'elementor' ),
                            'tab'   => Controls_Manager::TAB_STYLE,
                    ]
            );
            
            $this->add_control(
                    'content_color',
                    [
                            'label' => Leo_Helper::__( 'Color', 'elementor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-content *' => 'color: {{VALUE}};',
                            ],
                            'scheme' => [
                                    'type' => Scheme_Color::get_type(),
                                    'value' => Scheme_Color::COLOR_3,
                            ],
                    ]
            );

            $this->add_responsive_control(
                    'content_space',
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
                                    '{{WRAPPER}} .elementor-tab-content' => 'padding-top: {{SIZE}}{{UNIT}};',
                            ],
                    ]
            );
            
            $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                            'name' => 'content_typography',
                            'selector' => '{{WRAPPER}} .elementor-tab-content *',
                            'scheme' => Scheme_Typography::TYPOGRAPHY_3,
                    ]
            );
            
            $this->add_control(
                    'content_border_color',
                    [
                            'label' => Leo_Helper::__( 'Border Color', 'elementor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-content' => 'border-color: {{VALUE}};',
                            ],
                    ]
            );
            
            $this->add_control(
                    'content_border_width',
                    [
                            'label' => Leo_Helper::__( 'Border Width', 'elementor' ),
                            'type' => Controls_Manager::SLIDER,
                            'default' => [
                                    'size' => 1,
                            ],
                            'range' => [
                                    'px' => [
                                            'min' => 0,
                                            'max' => 10,
                                    ],
                            ],
                            'selectors' => [
                                    '{{WRAPPER}} .elementor-tab-content' => 'border-width: {{SIZE}}{{UNIT}}; border-top-style: solid',
                            ],
                    ]
            );
            $this->end_controls_section();
	}

	/**
	 * Render tabs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$tabs = $this->get_settings_for_display( 'tabs' );

		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>
		<div class="elementor-tabs" role="tablist">
			<div class="elementor-tabs-wrapper">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

					$this->add_render_attribute( $tab_title_setting_key, [
						'id' => 'elementor-tab-title-' . $id_int . $tab_count,
						'class' => [ 'elementor-tab-title', 'elementor-tab-desktop-title' ],
						'data-tab' => $tab_count,
						'role' => 'tab',
						'aria-controls' => 'elementor-tab-content-' . $id_int . $tab_count,
					] );
					?>
					<div <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>><a href=""><?php echo $item['tab_title']; ?></a></div>
				<?php endforeach; ?>
			</div>
			<div class="elementor-tabs-content-wrapper">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

					$tab_title_mobile_setting_key = $this->get_repeater_setting_key( 'tab_title_mobile', 'tabs', $tab_count );

					$this->add_render_attribute( $tab_content_setting_key, [
						'id' => 'elementor-tab-content-' . $id_int . $tab_count,
						'class' => [ 'elementor-tab-content', 'elementor-clearfix' ],
						'data-tab' => $tab_count,
						'role' => 'tabpanel',
						'aria-labelledby' => 'elementor-tab-title-' . $id_int . $tab_count,
					] );

					$this->add_render_attribute( $tab_title_mobile_setting_key, [
						'class' => [ 'elementor-tab-title', 'elementor-tab-mobile-title' ],
						'data-tab' => $tab_count,
						'role' => 'tab',
					] );

					$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
					?>
					<div <?php echo $this->get_render_attribute_string( $tab_title_mobile_setting_key ); ?>><?php echo $item['tab_title']; ?></div>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] ); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render tabs widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<div class="elementor-tabs" role="tablist">
			<#
			if ( settings.tabs ) {
				var tabindex = view.getIDInt().toString().substr( 0, 3 );
				#>
				<div class="elementor-tabs-wrapper">
					<#
					_.each( settings.tabs, function( item, index ) {
						var tabCount = index + 1;
						#>
						<div id="elementor-tab-title-{{ tabindex + tabCount }}" class="elementor-tab-title elementor-tab-desktop-title" data-tab="{{ tabCount }}" role="tab" aria-controls="elementor-tab-content-{{ tabindex + tabCount }}"><a href="">{{{ item.tab_title }}}</a></div>
					<# } ); #>
				</div>
				<div class="elementor-tabs-content-wrapper">
					<#
					_.each( settings.tabs, function( item, index ) {
						var tabCount = index + 1,
							tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs',index );

						view.addRenderAttribute( tabContentKey, {
							'id': 'elementor-tab-content-' + tabindex + tabCount,
							'class': [ 'elementor-tab-content', 'elementor-clearfix', 'elementor-repeater-item-' + item._id ],
							'data-tab': tabCount,
							'role' : 'tabpanel',
							'aria-labelledby' : 'elementor-tab-title-' + tabindex + tabCount
						} );

						view.addInlineEditingAttributes( tabContentKey, 'advanced' );
						#>
						<div class="elementor-tab-title elementor-tab-mobile-title" data-tab="{{ tabCount }}" role="tab">{{{ item.tab_title }}}</div>
						<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
					<# } ); #>
				</div>
			<# } #>
		</div>
		<?php
	}
}