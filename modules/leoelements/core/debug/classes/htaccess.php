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

class Htaccess extends Inspection_Base {

	private $message = '';

	public function __construct() {
		$this->message = Leo_Helper::__( 'Your site\'s .htaccess file appears to be missing.', 'elementor' );
	}

	public function run() {
		$permalink_structure = Leo_Helper::get_option( 'permalink_structure' );
		if ( empty( $permalink_structure ) ) {
			return true;
		}

		if ( empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
			$this->message = Leo_Helper::__( 'We failed to recognize your Server software. Please contact your hosting provider.', 'elementor' );
			return false;
		}
		$server = strtoupper( $_SERVER['SERVER_SOFTWARE'] );

		if ( strstr( $server, 'APACHE' ) ) {
			$htaccess_file = get_home_path() . '.htaccess';
			return file_exists( $htaccess_file );
		}
		return true;
	}

	public function get_name() {
		return 'apache-htaccess';
	}

	public function get_message() {
		return $this->message;
	}

	public function get_help_doc_url() {
		return 'https://subdomain.leoelements.com/preview-not-loaded/#htaccess';
	}
}
