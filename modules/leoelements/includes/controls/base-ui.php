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
 * Elementor base UI control.
 *
 * An abstract class for creating new UI controls in the panel.
 *
 * @abstract
 */
abstract class Base_UI_Control extends Base_Control {

	/**
	 * Get features.
	 *
	 * Retrieve the list of all the available features.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 *
	 * @return array Features array.
	 */
	public static function get_features() {
		return [ 'ui' ];
	}
}
