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

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor select control.
 *
 * A base control for creating select control. Displays a simple select box.
 * It accepts an array in which the `key` is the option value and the `value` is
 * the option name.
 *
 * @since 1.0.0
 */
class Control_Listchecks extends Base_Data_Control {

	/**
	 * Get select control type.
	 *
	 * Retrieve the control type, in this case `select`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'listchecks';
	}

	/**
	 * Get select control default settings.
	 *
	 * Retrieve the default settings of the select control. Used to return the
	 * default settings while initializing the select control.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'options' => [],
                        'checked' => 0,
		];
	}

	/**
	 * Render select control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
                
//		$more_input_control_uid = $this->get_control_uid( 'more-input' );
//
//		$is_external_control_uid = $this->get_control_uid( 'is_external' );
//
//		$nofollow_control_uid = $this->get_control_uid( 'nofollow' );
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper">
				<#
					var printOptions = function( options ) {
						_.each( options, function( option_title, option_value ) { #>
                                                                <div class="elementor-control-url-option">
                                                                        <input id="" type="checkbox" value="{{ option_value }}">
                                                                        <label for="{{ option_value }}">{{{ option_title }}}</label>
                                                                </div>
						<# } );
					};

					if ( data.groups ) {
						for ( var groupIndex in data.groups ) {
							var groupArgs = data.groups[ groupIndex ];
								if ( groupArgs.options ) { #>
									<optgroup label="{{ groupArgs.label }}">
										<# printOptions( groupArgs.options ) #>
									</optgroup>
								<# } else if ( _.isString( groupArgs ) ) { #>
									<option value="{{ groupIndex }}">{{{ groupArgs }}}</option>
								<# }
						}
					} else {
						printOptions( data.options );
					}
				#>
			</div>
		</div>
<style>

.elementor-control-type-listchecks .elementor-control-field {
    flex-wrap: wrap;
}

.elementor-control-type-listchecks .elementor-control-url-option {
    padding: 5px;
    font-size: 12px;
}

.elementor-control-type-listchecks .elementor-control-input-wrapper {
    width: 100% !important;
    margin-top: 10px;
}

</style>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
