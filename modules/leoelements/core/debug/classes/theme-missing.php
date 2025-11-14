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

class Theme_Missing extends Inspection_Base {

	public function run() {
		$theme = wp_get_theme();
		return $theme->exists();
	}

	public function get_name() {
		return 'theme-missing';
	}

	public function get_message() {
		return Leo_Helper::__( 'Some of your theme files are missing.', 'elementor' );
	}

	public function get_help_doc_url() {
		return 'https://subdomain.leoelements.com/preview-not-loaded/#theme-files';
	}
}
