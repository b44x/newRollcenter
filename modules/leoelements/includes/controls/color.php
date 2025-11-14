<?php
/**
 * 2007-2022 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2022 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

namespace LeoElements;

use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor color control.
 *
 * A base control for creating color control. Displays a color picker field with
 * an alpha slider. Includes a customizable color palette that can be preset by
 * the user. Accepts a `scheme` argument that allows you to set a value from the
 * active color scheme as the default value returned by the control.
 *
 * @since 1.0.0
 */
class Control_Color extends Base_Data_Control {

	/**
	 * Get color control type.
	 *
	 * Retrieve the control type, in this case `color`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'color';
	}

	/**
	 * Enqueue color control scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by the color
	 * control.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				
		Leo_Helper::wp_register_style(
			'wp-color-picker',
			LEOELEMENTS_ASSETS_URL . 'lib/wp-color-picker/wp-color-picker.min.css',
			[],
			'1.0.7'
		);
		
		Leo_Helper::wp_register_script(
			'wp-iris',
			LEOELEMENTS_ASSETS_URL . 'lib/wp-color-picker/iris.min.js',
			[
				'draggable',
				'slider',
				'touch'
			],
			'2.0.1',
			true
		);
		
		Leo_Helper::wp_register_script(
			'wp-color-picker',
			LEOELEMENTS_ASSETS_URL . 'lib/wp-color-picker/wp-color-picker.min.js',
			[
				'wp-iris',
			],
			'',
			true
		);
		
        Leo_Helper::wp_localize_script(
            'wp-color-picker',
            'wpColorPickerL10n',
            [
				'clear' => Leo_Helper::__('Clear', 'elementor'),
				'defaultString' => Leo_Helper::__('Default', 'elementor'),
				'pick' => Leo_Helper::__('Select Color', 'elementor'),
				'current' => Leo_Helper::__('Current Color', 'elementor'),
			]
        );

		Leo_Helper::wp_register_script(
			'wp-color-picker-alpha',
			LEOELEMENTS_ASSETS_URL . 'lib/wp-color-picker/wp-color-picker-alpha' . $suffix . '.js',
			[
				'wp-color-picker',
			],
			'2.0.1',
			true
		);

		Leo_Helper::wp_enqueue_style( 'wp-color-picker' );
		Leo_Helper::wp_enqueue_script( 'wp-color-picker-alpha' );
	}

	/**
	 * Render color control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		?>
		<# var defaultValue = '', dataAlpha = '';
			if ( data.default ) {
				defaultValue = ' data-default-color=' + data.default; // Quotes added automatically.
			}

			if ( data.alpha ) {
				dataAlpha = ' data-alpha=true';
			} #>
		<div class="elementor-control-field">
			<label class="elementor-control-title">
				<# if ( data.label ) { #>
					{{{ data.label }}}
				<# } #>
				<# if ( data.description ) { #>
					<span class="elementor-control-field-description">{{{ data.description }}}</span>
				<# } #>
			</label>
			<div class="elementor-control-input-wrapper">
				<input data-setting="{{ name }}" type="text" placeholder="<?php echo Leo_Helper::esc_attr( 'Hex/rgba', 'elementor' ); ?>" {{ defaultValue }}{{ dataAlpha }} />
			</div>
		</div>
		<?php
	}

	/**
	 * Get color control default settings.
	 *
	 * Retrieve the default settings of the color control. Used to return the default
	 * settings while initializing the color control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'alpha' => true,
			'scheme' => '',
		];
	}
}
