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

namespace LeoElements\Core\Debug\Classes;

use LeoElements\Leo_Helper; 

abstract class Inspection_Base {

	/**
	 * @return bool
	 */
	abstract public function run();

	/**
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * @return string
	 */
	abstract public function get_message();

	/**
	 * @return string
	 */
	public function get_header_message() {
		return Leo_Helper::__( 'The preview could not be loaded', 'elementor' );
	}

	/**
	 * @return string
	 */
	abstract public function get_help_doc_url();
}
