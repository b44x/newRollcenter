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

class Connect extends Common_App {

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function get_slug() {
		return 'connect';
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function render_admin_widget() {
		if ( $this->is_connected() ) {
			$remote_user = $this->get( 'user' );
			$title = sprintf( Leo_Helper::__( 'Connected to Elementor as %s', 'elementor' ), '<strong>' . $remote_user->email . '</strong>' ) . get_avatar( $remote_user->email, 20, '' );
			$label = Leo_Helper::__( 'Disconnect', 'elementor' );
			$url = $this->get_admin_url( 'disconnect' );
			$attr = '';
		} else {
			$title = Leo_Helper::__( 'Connect to Elementor', 'elementor' );
			$label = Leo_Helper::__( 'Connect', 'elementor' );
			$url = $this->get_admin_url( 'authorize' );
			$attr = 'class="elementor-connect-popup"';
		}

		echo '<h1>' . Leo_Helper::__( 'Connect', 'elementor' ) . '</h1>';

		echo sprintf( '%s <a %s href="%s">%s</a>', $title, $attr, Leo_Helper::esc_attr( $url ), Leo_Helper::esc_html( $label ) );
	}
}