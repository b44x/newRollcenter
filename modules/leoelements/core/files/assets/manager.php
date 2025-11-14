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

namespace LeoElements\Core\Files\Assets;

use LeoElements\Core\Files\Assets\Svg\Svg_Handler;
use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor files manager.
 *
 * Elementor files manager handler class is responsible for creating files.
 *
 * @since 1.0.0
 */
class Manager {

	/**
	 * Holds registered asset types
	 * @var array
	 */
	protected $asset_types = [];

	/**
	 * Assets manager constructor.
	 *
	 * Initializing the Elementor assets manager.
	 *
	 * @access public
	 */
	public function __construct() {
		$this->register_asset_types();
		/**
		 * Elementor files assets registered.
		 *
		 * Fires after Elementor registers assets types
		 *
		 * @since 1.0.0
		 */
		Leo_Helper::do_action( 'elementor/core/files/assets/assets_registered', $this );
	}

	public function get_asset( $name ) {
		return isset( $this->asset_types[ $name ] ) ? $this->asset_types[ $name ] : false;
	}

	/**
	 * Add Asset
	 * @param $instance
	 */
	public function add_asset( $instance ) {
		$this->asset_types[ $instance::get_name() ] = $instance;
	}


	/**
	 * Register Asset Types
	 *
	 * Registers Elementor Asset Types
	 */
	private function register_asset_types() {
		$this->add_asset( new Svg_Handler() );
	}
}
