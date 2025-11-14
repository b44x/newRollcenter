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
 * Elementor HTML widget.
 *
 * Elementor widget that insert a custom HTML code into the page.
 *
 * @since 1.0.0
 */
class Widget_Html extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve HTML widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'html';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve HTML widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return Leo_Helper::__( 'HTML', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve HTML widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-code';
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
		return [ 'html', 'code' ];
	}

	/**
	 * Register HTML widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => Leo_Helper::__( 'HTML Code', 'elementor' ),
			]
		);

		$this->add_control(
			'html',
			[
				'label' => '',
				'type' => Controls_Manager::CODE,
				'default' => '',
				'placeholder' => Leo_Helper::__( 'Enter your code', 'elementor' ),
				'show_label' => false,
			]
		);
                
		$this->end_controls_section();
                
        /************************************************************/
        $this->start_controls_section(
                'section_style_html',
                [
                        'label' => Leo_Helper::__( 'Html Code', 'elementor' ),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'html!' => '',
                        ],
                ]
        );
        $this->add_responsive_control(
                'html_show_align',
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
                                'justify' => [
                                        'title' => Leo_Helper::__( 'Justified', 'elementor' ),
                                        'icon' => 'eicon-text-align-justify',
                                ],
                        ],
                        'default' => 'left',
                        'selectors' => [
                                '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                        ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                        'name' => 'html_show_typography',
                        'scheme' => Scheme_Typography::TYPOGRAPHY_4,
                        'selector' => '{{WRAPPER}}',
                ]
        );

        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                        'name' => 'html_show_text_shadow',
                        'selector' => '{{WRAPPER}}',
                ]
        );

//        $this->add_control(
//                'separator_panel_style',
//                [
//                        'type' => Controls_Manager::DIVIDER,
//                        'style' => 'thick',
//                ]
//        );
//        $this->start_controls_tabs( 'html_show_button_style' );

//        $this->start_controls_tab(
//                'html_show_button_normal',
//                [
//                        'label' => Leo_Helper::__( 'Normal', 'elementor' ),
//                ]
//        );

        $this->add_control(
                'html_show_button_text_color',
                [
                        'label' => Leo_Helper::__( 'Text Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                                '{{WRAPPER}}' => 'fill: {{VALUE}}; color: {{VALUE}};',
                        ],
                ]
        );

//        $this->end_controls_tab();
        $this->end_controls_section();
	}

	/**
	 * Render HTML widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
                echo $this->get_settings_for_display( 'html' );
	}

	/**
	 * Render HTML widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		{{{ settings.html }}}
		<?php
	}
}
