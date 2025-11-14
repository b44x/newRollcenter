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

namespace LeoElements\Core\Common\Modules\Connect;

use LeoElements\Plugin;
use LeoElements\Settings;
use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Admin {

	const PAGE_ID = 'elementor-connect';

	public static $url = '';

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function register_admin_menu() {
		$submenu_page = add_submenu_page(
			Settings::PAGE_ID,
			Leo_Helper::__( 'Connect', 'elementor' ),
			Leo_Helper::__( 'Connect', 'elementor' ),
			'manage_options',
			self::PAGE_ID,
			[ $this, 'render_page' ]
		);

		Leo_Helper::add_action( 'load-' . $submenu_page, [ $this, 'on_load_page' ] );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function hide_menu_item() {
		remove_submenu_page( Settings::PAGE_ID, self::PAGE_ID );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function on_load_page() {
		if ( Tools::getIsset('action') && Tools::getIsset('app') ) {
			$manager = Plugin::$instance->common->get_component( 'connect' );
			$app_slug = Tools::getValue('app');
			$app = $manager->get_app( $app_slug );
			$nonce_action = Tools::getValue('app') . Tools::getValue('action');

			if ( ! $app ) {
				wp_die( 'Unknown app: ' . $app_slug );
			}

			if ( empty( Tools::getValue('nonce') ) || ! wp_verify_nonce(Tools::getValue('nonce'), $nonce_action ) ) {
				wp_die( 'Invalid Nonce', 'Invalid Nonce', [
					'back_link' => true,
				] );
			}

			$method = 'action_' . Tools::getValue('action');

			if ( method_exists( $app, $method ) ) {
				call_user_func( [ $app, $method ] );
			}
		}
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function render_page() {
		$apps = Plugin::$instance->common->get_component( 'connect' )->get_apps();
		?>
		<style>
			.elementor-connect-app-wrapper{
				margin-bottom: 50px;
				overflow: hidden;
			}
		</style>
		<div class="wrap">
			<?php

			/** @var \Elementor\Core\Common\Modules\Connect\Apps\Base_App $app */
			foreach ( $apps as $app ) {
				echo '<div class="elementor-connect-app-wrapper">';
				$app->render_admin_widget();
				echo '</div>';
			}

			?>
		</div><!-- /.wrap -->
		<?php
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		self::$url = Leo_Helper::admin_url( 'admin.php?page=' . self::PAGE_ID );

		Leo_Helper::add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 206 );
		Leo_Helper::add_action( 'admin_head', [ $this, 'hide_menu_item' ] );
	}
}