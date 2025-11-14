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

namespace LeoElements\System_Info\Helpers;

use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor model helper.
 *
 * Elementor model helper handler class is responsible for filtering properties.
 *
 * @since 1.0.0
 */
final class Model_Helper {

	/**
	 * Model helper constructor.
	 *
	 * Initializing the model helper class.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Filter possible properties.
	 *
	 * Retrieve possible properties filtered by property intersect key.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $possible_properties All the possible properties.
	 * @param array $properties          Properties to filter.
	 *
	 * @return array Possible properties filtered by property intersect key.
	 */
	public static function filter_possible_properties( $possible_properties, $properties ) {
		$properties_keys = array_flip( $possible_properties );

		return array_intersect_key( $properties, $properties_keys );
	}

	/**
	 * Prepare properties.
	 *
	 * Combine the possible properties with the user properties and filter them.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $possible_properties All the possible properties.
	 * @param array $user_properties     User properties.
	 *
	 * @return array Possible properties and user properties filtered by property intersect key.
	 */
	public static function prepare_properties( $possible_properties, $user_properties ) {
		$properties = array_fill_keys( $possible_properties, null );

		$properties = array_merge( $properties, $user_properties );

		return self::filter_possible_properties( $possible_properties, $properties );
	}
}
