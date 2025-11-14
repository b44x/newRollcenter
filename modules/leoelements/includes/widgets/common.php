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
 * Elementor common widget.
 *
 * Elementor base widget that gives you all the advanced options of the basic
 * widget.
 *
 * @since 1.0.0
 */
class Widget_Common extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve common widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'common';
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the common widget in the panel or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether to show the widget in the panel.
	 */
	public function show_in_panel() {
		return false;
	}

	/**
	 * Register common widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'_section_style',
			[
				'label' => Leo_Helper::__( 'Advanced', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'_title',
			[
				'label' => Leo_Helper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'render_type' => 'none',
			]
		);

		$this->add_responsive_control(
			'_margin',
			[
				'label' => Leo_Helper::__( 'Margin', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} > .elementor-widget-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'_padding',
			[
				'label' => Leo_Helper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} > .elementor-widget-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_z_index',
			[
				'label' => Leo_Helper::__( 'Z-Index', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'selectors' => [
					'{{WRAPPER}}' => 'z-index: {{VALUE}};',
				],
				'label_block' => false,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'_element_id',
			[
				'label' => Leo_Helper::__( 'CSS ID', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'title' => Leo_Helper::__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor' ),
				'label_block' => false,
				'style_transfer' => false,
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->add_control(
			'_css_classes',
			[
				'label' => Leo_Helper::__( 'CSS Classes', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'prefix_class' => '',
				'title' => Leo_Helper::__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor' ),
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_effects',
			[
				'label' => Leo_Helper::__( 'Motion Effects', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_responsive_control(
			'_animation',
			[
				'label' => Leo_Helper::__( 'Entrance Animation', 'elementor' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label' => Leo_Helper::__( 'Animation Duration', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'slow' => Leo_Helper::__( 'Slow', 'elementor' ),
					'' => Leo_Helper::__( 'Normal', 'elementor' ),
					'fast' => Leo_Helper::__( 'Fast', 'elementor' ),
				],
				'prefix_class' => 'animated-',
				'condition' => [
					'_animation!' => '',
				],
			]
		);

		$this->add_control(
			'_animation_delay',
			[
				'label' => Leo_Helper::__( 'Animation Delay', 'elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'condition' => [
					'_animation!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_background',
			[
				'label' => Leo_Helper::__( 'Background', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->start_controls_tabs( '_tabs_background' );

		$this->start_controls_tab(
			'_tab_background_normal',
			[
				'label' => Leo_Helper::__( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => '_background',
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_background_hover',
			[
				'label' => Leo_Helper::__( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => '_background_hover',
				'selector' => '{{WRAPPER}}:hover .elementor-widget-container',
			]
		);

		$this->add_control(
			'_background_hover_transition',
			[
				'label' => Leo_Helper::__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'render_type' => 'ui',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_border',
			[
				'label' => Leo_Helper::__( 'Border', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->start_controls_tabs( '_tabs_border' );

		$this->start_controls_tab(
			'_tab_border_normal',
			[
				'label' => Leo_Helper::__( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => '_border',
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);

		$this->add_responsive_control(
			'_border_radius',
			[
				'label' => Leo_Helper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} > .elementor-widget-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => '_box_shadow',
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_border_hover',
			[
				'label' => Leo_Helper::__( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => '_border_hover',
				'selector' => '{{WRAPPER}}:hover .elementor-widget-container',
			]
		);

		$this->add_responsive_control(
			'_border_radius_hover',
			[
				'label' => Leo_Helper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}}:hover > .elementor-widget-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => '_box_shadow_hover',
				'selector' => '{{WRAPPER}}:hover .elementor-widget-container',
			]
		);

		$this->add_control(
			'_border_hover_transition',
			[
				'label' => Leo_Helper::__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'transition: background {{_background_hover_transition.SIZE}}s, border {{SIZE}}s, border-radius {{SIZE}}s, box-shadow {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_position',
			[
				'label' => Leo_Helper::__( 'Custom Positioning', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_responsive_control(
			'_element_width',
			[
				'label' => Leo_Helper::__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => Leo_Helper::__( 'Default', 'elementor' ),
					'inherit' => Leo_Helper::__( 'Full Width', 'elementor' ) . ' (100%)',
					'auto' => Leo_Helper::__( 'Inline', 'elementor' ) . ' (auto)',
					'initial' => Leo_Helper::__( 'Custom', 'elementor' ),
				],
				'selectors_dictionary' => [
					'inherit' => '100%',
				],
				'prefix_class' => 'elementor-widget%s__width-',
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'_element_custom_width',
			[
				'label' => Leo_Helper::__( 'Custom Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'_element_width' => 'initial',
				],
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'condition' => [
							'_element_width_tablet' => [ 'initial' ],
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'condition' => [
							'_element_width_mobile' => [ 'initial' ],
						],
					],
				],
				'size_units' => [ 'px', '%', 'vw' ],
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'_element_vertical_align',
			[
				'label' => Leo_Helper::__( 'Vertical Align', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'flex-start' => [
						'title' => Leo_Helper::__( 'Start', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => Leo_Helper::__( 'Center', 'elementor' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => Leo_Helper::__( 'End', 'elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'condition' => [
					'_element_width!' => '',
					'_position' => '',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'align-self: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'_position_description',
			[
				'raw' => '<strong>' . Leo_Helper::__( 'Please note!', 'elementor' ) . '</strong> ' . Leo_Helper::__( 'Custom positioning is not considered best practice for responsive web design and should not be used too frequently.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
				'condition' => [
					'_position!' => '',
				],
			]
		);

		$this->add_control(
			'_position',
			[
				'label' => Leo_Helper::__( 'Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => Leo_Helper::__( 'Default', 'elementor' ),
					'absolute' => Leo_Helper::__( 'Absolute', 'elementor' ),
					'fixed' => Leo_Helper::__( 'Fixed', 'elementor' ),
				],
				'prefix_class' => 'elementor-',
				'frontend_available' => true,
			]
		);

		$start = Leo_Helper::is_rtl() ? Leo_Helper::__( 'Right', 'elementor' ) : Leo_Helper::__( 'Left', 'elementor' );
		$end = ! Leo_Helper::is_rtl() ? Leo_Helper::__( 'Right', 'elementor' ) : Leo_Helper::__( 'Left', 'elementor' );

		$this->add_control(
			'_offset_orientation_h',
			[
				'label' => Leo_Helper::__( 'Horizontal Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => $start,
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => $end,
						'icon' => 'eicon-h-align-right',
					],
				],
				'classes' => 'elementor-control-start-end',
				'render_type' => 'ui',
				'condition' => [
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_x',
			[
				'label' => Leo_Helper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_h!' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_x_end',
			[
				'label' => Leo_Helper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 0.1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_h' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->add_control(
			'_offset_orientation_v',
			[
				'label' => Leo_Helper::__( 'Vertical Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => Leo_Helper::__( 'Top', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'end' => [
						'title' => Leo_Helper::__( 'Bottom', 'elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_y',
			[
				'label' => Leo_Helper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_v!' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_y_end',
			[
				'label' => Leo_Helper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_v' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_responsive',
			[
				'label' => Leo_Helper::__( 'Responsive', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'responsive_description',
			[
				'raw' => Leo_Helper::__( 'Responsive visibility will take effect only on preview or live page, and not while editing in Elementor.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'hide_desktop',
			[
				'label' => Leo_Helper::__( 'Hide On Desktop', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'hidden-desktop',
			]
		);

		$this->add_control(
			'hide_tablet',
			[
				'label' => Leo_Helper::__( 'Hide On Tablet', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'hidden-tablet',
			]
		);

		$this->add_control(
			'hide_mobile',
			[
				'label' => Leo_Helper::__( 'Hide On Mobile', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'hidden-phone',
			]
		);

		$this->end_controls_section();
		
		Plugin::$instance->controls_manager->add_custom_attributes_controls( $this );

		Plugin::$instance->controls_manager->add_custom_css_controls( $this );
	}
}