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

namespace LeoElements\Core\Debug;

use LeoElements\Core\Debug\Classes\Inspection_Base;
use LeoElements\Core\Debug\Classes\Theme_Missing;
use LeoElements\Core\Debug\Classes\Htaccess;
use LeoElements\Leo_Helper;

class Loading_Inspection_Manager {

	public static $_instance = null;

	public static function instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new Loading_Inspection_Manager();
		}
		return self::$_instance;
	}

	/** @var Inspection_Base[] */
	private $inspections = [];

	public function register_inspections() {
		$this->inspections['theme-missing'] = new Theme_Missing();
		$this->inspections['htaccess'] = new Htaccess();
	}

	/**
	 * @param Inspection_Base $inspection
	 */
	public function register_inspection( $inspection ) {
		$this->inspections[ $inspection->get_name() ] = $inspection;
	}

	public function run_inspections() {
		$debug_data = [
			'message' => Leo_Helper::__( 'We\'re sorry, but something went wrong. Click on \'Learn more\' and follow each of the steps to quickly solve it.', 'elementor' ),
			'header' => Leo_Helper::__( 'The preview could not be loaded', 'elementor' ),
			'doc_url' => 'https://subdomain.leoelements.com/preview-not-loaded/',
		];
		foreach ( $this->inspections as $inspection ) {
			if ( ! $inspection->run() ) {
				$debug_data = [
					'message' => $inspection->get_message(),
					'header' => $inspection->get_header_message(),
					'doc_url' => $inspection->get_help_doc_url(),
					'error' => true,
				];
				break;
			}
		}

		return $debug_data;
	}
}
