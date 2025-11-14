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
 * Elementor progress widget.
 *
 * Elementor widget that displays an escalating progress bar.
 *
 * @since 1.0.0
 */
class Widget_Progress extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve progress widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'progress';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve progress widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return Leo_Helper::__( 'Progress Bar', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve progress widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-skill-bar';
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
		return [ 'progress', 'bar' ];
	}

	/**
	 * Register progress widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_progress',
			[
				'label' => Leo_Helper::__( 'Progress Bar', 'elementor' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => Leo_Helper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => Leo_Helper::__( 'Enter your title', 'elementor' ),
				'default' => Leo_Helper::__( 'My Skill', 'elementor' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'progress_type',
			[
				'label' => Leo_Helper::__( 'Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => Leo_Helper::__( 'Default', 'elementor' ),
					'info' => Leo_Helper::__( 'Info', 'elementor' ),
					'success' => Leo_Helper::__( 'Success', 'elementor' ),
					'warning' => Leo_Helper::__( 'Warning', 'elementor' ),
					'danger' => Leo_Helper::__( 'Danger', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'percent',
			[
				'label' => Leo_Helper::__( 'Percentage', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'label_block' => true,
			]
		);

		$this->add_control( 'display_percentage', [
			'label' => Leo_Helper::__( 'Display Percentage', 'elementor' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'show',
			'options' => [
				'show' => Leo_Helper::__( 'Show', 'elementor' ),
				'hide' => Leo_Helper::__( 'Hide', 'elementor' ),
			],
		] );

		$this->add_control(
			'inner_text',
			[
				'label' => Leo_Helper::__( 'Inner Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => Leo_Helper::__( 'e.g. Web Designer', 'elementor' ),
				'default' => Leo_Helper::__( 'Web Designer', 'elementor' ),
				'label_block' => true,
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_progress_style',
			[
				'label' => Leo_Helper::__( 'Progress Bar', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bar_color',
			[
				'label' => Leo_Helper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-progress-wrapper .elementor-progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'bar_bg_color',
			[
				'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-progress-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'bar_height',
			[
				'label' => Leo_Helper::__( 'Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-progress-bar' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'bar_border_radius',
			[
				'label' => Leo_Helper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-progress-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_control(
			'inner_text_heading',
			[
				'label' => Leo_Helper::__( 'Inner Text', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'bar_inline_color',
			[
				'label' => Leo_Helper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-progress-bar' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bar_inner_typography',
				'selector' => '{{WRAPPER}} .elementor-progress-bar',
				'exclude' => [
					'line_height',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			[
				'label' => Leo_Helper::__( 'Title Style', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-title' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .elementor-title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render progress widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', [
			'class' => 'elementor-progress-wrapper',
			'role' => 'progressbar',
			'aria-valuemin' => '0',
			'aria-valuemax' => '100',
			'aria-valuenow' => $settings['percent']['size'],
			'aria-valuetext' => $settings['inner_text'],
		] );

		if ( ! empty( $settings['progress_type'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'progress-' . $settings['progress_type'] );
		}

		$this->add_render_attribute( 'progress-bar', [
			'class' => 'elementor-progress-bar',
			'data-max' => $settings['percent']['size'],
		] );

		$this->add_render_attribute( 'inner_text', [
			'class' => 'elementor-progress-text',
		] );

		$this->add_inline_editing_attributes( 'inner_text' );

		if ( ! empty( $settings['title'] ) ) { ?>
			<span class="elementor-title"><?php echo $settings['title']; ?></span>
		<?php } ?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'progress-bar' ); ?>>
				<span <?php echo $this->get_render_attribute_string( 'inner_text' ); ?>><?php echo $settings['inner_text']; ?></span>
				<?php if ( 'hide' !== $settings['display_percentage'] ) { ?>
					<span class="elementor-progress-percentage"><?php echo $settings['percent']['size']; ?>%</span>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render progress widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<#
		view.addRenderAttribute( 'progressWrapper', {
			'class': [ 'elementor-progress-wrapper', 'progress-' + settings.progress_type ],
			'role': 'progressbar',
			'aria-valuemin': '0',
			'aria-valuemax': '100',
			'aria-valuenow': settings.percent.size,
			'aria-valuetext': settings.inner_text
		} );

		view.addRenderAttribute( 'inner_text', {
			'class': 'elementor-progress-text'
		} );

		view.addInlineEditingAttributes( 'inner_text' );
		#>
		<# if ( settings.title ) { #>
			<span class="elementor-title">{{{ settings.title }}}</span><#
		} #>
		<div {{{ view.getRenderAttributeString( 'progressWrapper' ) }}}>
			<div class="elementor-progress-bar" data-max="{{ settings.percent.size }}">
				<span {{{ view.getRenderAttributeString( 'inner_text' ) }}}>{{{ settings.inner_text }}}</span>
				<# if ( 'hide' !== settings.display_percentage ) { #>
					<span class="elementor-progress-percentage">{{{ settings.percent.size }}}%</span>
				<# } #>
			</div>
		</div>
		<?php
	}
}