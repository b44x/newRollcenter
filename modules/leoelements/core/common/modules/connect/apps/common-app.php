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

namespace LeoElements\Core\Common\Modules\Connect\Apps;

use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

abstract class Common_App extends Base_User_App {

	protected static $common_data = null;

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_name() {
		return static::OPTION_NAME_PREFIX . 'common_data';
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function init_data() {
		if ( is_null( self::$common_data ) ) {
			self::$common_data = get_user_meta( get_current_user_id(), static::get_option_name(), true );

			if ( ! self::$common_data ) {
				self::$common_data = [];
			};
		}

		$this->data = & self::$common_data;
	}
}