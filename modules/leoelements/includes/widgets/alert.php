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
 * Elementor alert widget.
 *
 * Elementor widget that displays a collapsible display of content in an toggle
 * style, allowing the user to open multiple items.
 *
 * @since 1.0.0
 */
class Widget_Alert extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve alert widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'alert';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve alert widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return Leo_Helper::__( 'Alert', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve alert widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-alert';
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
		return [ 'alert', 'notice', 'message' ];
	}

	/**
	 * Register alert widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_alert',
			[
				'label' => Leo_Helper::__( 'Alert', 'elementor' ),
			]
		);

		$this->add_control(
			'alert_type',
			[
				'label' => Leo_Helper::__( 'Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'info',
				'options' => [
					'info' => Leo_Helper::__( 'Info', 'elementor' ),
					'success' => Leo_Helper::__( 'Success', 'elementor' ),
					'warning' => Leo_Helper::__( 'Warning', 'elementor' ),
					'danger' => Leo_Helper::__( 'Danger', 'elementor' ),
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'alert_title',
			[
				'label' => Leo_Helper::__( 'Title & Description', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => Leo_Helper::__( 'Enter your title', 'elementor' ),
				'default' => Leo_Helper::__( 'This is an Alert', 'elementor' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'alert_description',
			[
				'label' => Leo_Helper::__( 'Content', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => Leo_Helper::__( 'Enter your description', 'elementor' ),
				'default' => Leo_Helper::__( 'I am a description. Click the edit button to change this text.', 'elementor' ),
				'separator' => 'none',
				'show_label' => false,
			]
		);

		$this->add_control(
			'show_dismiss',
			[
				'label' => Leo_Helper::__( 'Dismiss Button', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'show',
				'options' => [
					'show' => Leo_Helper::__( 'Show', 'elementor' ),
					'hide' => Leo_Helper::__( 'Hide', 'elementor' ),
				],
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
			'section_type',
			[
				'label' => Leo_Helper::__( 'Alert', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background',
			[
				'label' => Leo_Helper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => Leo_Helper::__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_left-width',
			[
				'label' => Leo_Helper::__( 'Left Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-alert' => 'border-left-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			[
				'label' => Leo_Helper::__( 'Title', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'alert_title',
				'selector' => '{{WRAPPER}} .elementor-alert-title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_description',
			[
				'label' => Leo_Helper::__( 'Description', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'alert_description',
				'selector' => '{{WRAPPER}} .elementor-alert-description',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render alert widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['alert_title'] ) ) {
			return;
		}

		if ( ! empty( $settings['alert_type'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-alert elementor-alert-' . $settings['alert_type'] );
		}

		$this->add_render_attribute( 'wrapper', 'role', 'alert' );

		$this->add_render_attribute( 'alert_title', 'class', 'elementor-alert-title' );

		$this->add_inline_editing_attributes( 'alert_title', 'none' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<span <?php echo $this->get_render_attribute_string( 'alert_title' ); ?>><?php echo $settings['alert_title']; ?></span>
			<?php
			if ( ! empty( $settings['alert_description'] ) ) :
				$this->add_render_attribute( 'alert_description', 'class', 'elementor-alert-description' );

				$this->add_inline_editing_attributes( 'alert_description' );
				?>
				<span <?php echo $this->get_render_attribute_string( 'alert_description' ); ?>><?php echo $settings['alert_description']; ?></span>
			<?php endif; ?>
			<?php if ( 'show' === $settings['show_dismiss'] ) : ?>
				<button type="button" class="elementor-alert-dismiss">
					<span aria-hidden="true">&times;</span>
					<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Dismiss alert', 'elementor' ); ?></span>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render alert widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<# if ( settings.alert_title ) {
			view.addRenderAttribute( {
				alert_title: { class: 'elementor-alert-title' },
				alert_description: { class: 'elementor-alert-description' }
			} );

			view.addInlineEditingAttributes( 'alert_title', 'none' );
			view.addInlineEditingAttributes( 'alert_description' );
			#>
			<div class="elementor-alert elementor-alert-{{ settings.alert_type }}" role="alert">
				<span {{{ view.getRenderAttributeString( 'alert_title' ) }}}>{{{ settings.alert_title }}}</span>
				<span {{{ view.getRenderAttributeString( 'alert_description' ) }}}>{{{ settings.alert_description }}}</span>
				<# if ( 'show' === settings.show_dismiss ) { #>
					<button type="button" class="elementor-alert-dismiss">
						<span aria-hidden="true">&times;</span>
						<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Dismiss alert', 'elementor' ); ?></span>
					</button>
				<# } #>
			</div>
		<# } #>
		<?php
	}
}