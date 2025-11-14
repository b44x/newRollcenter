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

abstract class Base_User_App extends Base_App {

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function update_settings() {
		update_user_meta( get_current_user_id(), $this->get_option_name(), $this->data );
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function init_data() {
		$this->data = get_user_meta( get_current_user_id(), $this->get_option_name(), true );

		if ( ! $this->data ) {
			$this->data = [];
		}
	}
}
