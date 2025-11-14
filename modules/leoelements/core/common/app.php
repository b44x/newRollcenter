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

namespace LeoElements\Core\Common;

use LeoElements\Core\Base\App as BaseApp;
use LeoElements\Core\Common\Modules\Ajax\Module as Ajax;
use LeoElements\Core\Common\Modules\Connect\Module as Connect;
use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * App
 *
 * Elementor's common app that groups shared functionality, components and configuration
 *
 * @since 1.0.0
 */
class App extends BaseApp {

	private $templates = [];

	/**
	 * App constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$this->add_default_templates();

		Leo_Helper::add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_scripts' ] );
		Leo_Helper::add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		Leo_Helper::add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );

		Leo_Helper::add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'register_styles' ] );
		Leo_Helper::add_action( 'admin_enqueue_scripts', [ $this, 'register_styles' ] );
		Leo_Helper::add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ], 9 );

		Leo_Helper::add_action( 'elementor/editor/footer', [ $this, 'print_templates' ] );
		Leo_Helper::add_action( 'admin_footer', [ $this, 'print_templates' ] );
		Leo_Helper::add_action( 'wp_footer', [ $this, 'print_templates' ] );
	}

	/**
	 * Init components
	 *
	 * Initializing common components.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init_components() {
		$this->add_component( 'ajax', new Ajax() );

		$this->add_component( 'connect', new Connect() );
	}

	/**
	 * Get name.
	 *
	 * Retrieve the app name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Common app name.
	 */
	public function get_name() {
		return 'common';
	}

	/**
	 * Register scripts.
	 *
	 * Register common scripts.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_scripts() {
		Leo_Helper::wp_register_script(
			'elementor-common-modules',
			$this->get_js_assets_url( 'common-modules' ),
			[],
			LEOELEMENTS_VERSION,
			true
		);

		Leo_Helper::wp_register_script(
			'backbone-marionette',
			$this->get_js_assets_url( 'backbone.marionette', 'assets/lib/backbone/' ),
			[
				'backbone',
			],
			'2.4.5',
			true
		);

		Leo_Helper::wp_register_script(
			'backbone-radio',
			$this->get_js_assets_url( 'backbone.radio', 'assets/lib/backbone/' ),
			[
				'backbone',
			],
			'1.0.4',
			true
		);

		Leo_Helper::wp_register_script(
			'elementor-dialog',
			$this->get_js_assets_url( 'dialog', 'assets/lib/dialog/' ),
			[
				'jquery-ui-position',
			],
			'4.7.1',
			true
		);

		Leo_Helper::wp_enqueue_script(
			'elementor-common',
			$this->get_js_assets_url( 'common' ),
			[
				'jquery',
				'jquery-ui-draggable',
				'backbone-marionette',
				'backbone-radio',
				'elementor-common-modules',
				'elementor-dialog',
			],
			LEOELEMENTS_VERSION,
			true
		);

		$this->print_config();
	}

	/**
	 * Register styles.
	 *
	 * Register common styles.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_styles() {
		Leo_Helper::wp_register_style(
			'elementor-icons',
			$this->get_css_assets_url( 'elementor-icons', 'assets/lib/eicons/css/' ),
			[],
			'5.11.0'
		);

		Leo_Helper::wp_enqueue_style(
			'elementor-common',
			$this->get_css_assets_url( 'common', null, 'default', true ),
			[
				'elementor-icons',
			],
			LEOELEMENTS_VERSION
		);
	}

	/**
	 * Add template.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $template Can be either a link to template file or template
	 *                         HTML content.
	 * @param string $type     Optional. Whether to handle the template as path
	 *                         or text. Default is `path`.
	 */
	public function add_template( $template, $type = 'path' ) {
		if ( 'path' === $type ) {
			ob_start();

			include $template;

			$template = ob_get_clean();
		}

		$this->templates[] = $template;
	}

	/**
	 * Print Templates
	 *
	 * Prints all registered templates.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function print_templates() {
		foreach ( $this->templates as $template ) {
			echo $template;
		}
	}

	/**
	 * Get init settings.
	 *
	 * Define the default/initial settings of the common app.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_init_settings() {
		return [
			'version' => LEOELEMENTS_VERSION,
			'isRTL' => Leo_Helper::is_rtl(),
			'activeModules' => array_keys( $this->get_components() ),
			'urls' => [
				'assets' => LEOELEMENTS_ASSETS_URL,
			],
		];
	}

	/**
	 * Add default templates.
	 *
	 * Register common app default templates.
	 * @since 1.0.0
	 * @access private
	 */
	private function add_default_templates() {
		$default_templates = [
			'includes/editor-templates/library-layout.php',
		];

		foreach ( $default_templates as $template ) {
			$this->add_template( LEOELEMENTS_PATH . $template );
		}
	}
}