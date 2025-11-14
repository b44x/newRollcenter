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

namespace LeoElements\Core\Base;

use LeoElements\Plugin;
use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

abstract class DB_Upgrades_Manager extends Background_Task_Manager {
	protected $current_version = null;

	abstract public function get_new_version();
	abstract public function get_version_option_name();
	abstract public function get_upgrades_class();
	abstract public function get_updater_label();

	public function get_task_runner_class() {
		return 'LeoElements\Core\Upgrade\Updater';
	}

	public function get_query_limit() {
		return 100;
	}

	public function get_current_version() {
		if ( null === $this->current_version ) {
			$this->current_version = Leo_Helper::get_option( $this->get_version_option_name() );
		}

		return $this->current_version;
	}

	public function should_upgrade() {
		$current_version = $this->get_current_version();

		// It's a new install.
		if ( ! $current_version ) {
			$this->update_db_version();
			return false;
		}

		return version_compare( $this->get_new_version(), $current_version, '>' );
	}

	public function on_runner_start() {
		parent::on_runner_start();

		define( 'IS_LEOELEMENTS_UPGRADE', true );
	}

	public function on_runner_complete( $did_tasks = false ) {
		$logger = Plugin::$instance->logger->get_logger();

		$logger->info( 'Elementor data updater process has been completed.', [
			'meta' => [
				'plugin' => $this->get_plugin_label(),
				'from' => $this->current_version,
				'to' => $this->get_new_version(),
			],
		] );

		Plugin::$instance->files_manager->clear_cache();

		$this->update_db_version();

		if ( $did_tasks ) {
			$this->add_flag( 'completed' );
		}
	}

	public function admin_notice_start_upgrade() {
		$upgrade_link = $this->get_start_action_url();
		$message = '<p>' . sprintf( Leo_Helper::__( '%s Your site database needs to be updated to the latest version.', 'elementor' ), $this->get_updater_label() ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, Leo_Helper::__( 'Update Now', 'elementor' ) ) . '</p>';

		echo '<div class="notice notice-error">' . $message . '</div>';
	}

	public function admin_notice_upgrade_is_running() {
		$upgrade_link = $this->get_continue_action_url();
		$message = '<p>' . sprintf( Leo_Helper::__( '%s Database update process is running in the background.', 'elementor' ), $this->get_updater_label() ) . '</p>';
		$message .= '<p>' . sprintf( 'Taking a while? <a href="%s" class="button-primary">Click here to run it now</a>', $upgrade_link ) . '</p>';

		echo '<div class="notice notice-warning">' . $message . '</div>';
	}

	public function admin_notice_upgrade_is_completed() {
		$this->delete_flag( 'completed' );

		$message = '<p>' . sprintf( Leo_Helper::__( '%s The database update process is now complete. Thank you for updating to the latest version!', 'elementor' ), $this->get_updater_label() ) . '</p>';

		echo '<div class="notice notice-success">' . $message . '</div>';
	}

	/**
	 * @access protected
	 */
	protected function start_run() {
		$updater = $this->get_task_runner();

		if ( $updater->is_running() ) {
			return;
		}

		$upgrade_callbacks = $this->get_upgrade_callbacks();

		if ( empty( $upgrade_callbacks ) ) {
			$this->on_runner_complete();
			return;
		}

		foreach ( $upgrade_callbacks as $callback ) {
			$updater->push_to_queue( [
				'callback' => $callback,
			] );
		}

		$updater->save()->dispatch();

		Plugin::$instance->logger->get_logger()->info( 'Elementor data updater process has been queued.', [
			'meta' => [
				'plugin' => $this->get_plugin_label(),
				'from' => $this->current_version,
				'to' => $this->get_new_version(),
			],
		] );
	}

	protected function update_db_version() {
		Leo_Helper::update_option( $this->get_version_option_name(), $this->get_new_version() );
	}

	public function get_upgrade_callbacks() {
		$prefix = '_v_';
		$upgrades_class = $this->get_upgrades_class();
		$upgrades_reflection = new \ReflectionClass( $upgrades_class );

		$callbacks = [];

		foreach ( $upgrades_reflection->getMethods() as $method ) {
			$method_name = $method->getName();
			if ( false === strpos( $method_name, $prefix ) ) {
				continue;
			}

			if ( ! preg_match_all( "/$prefix(\d+_\d+_\d+)/", $method_name, $matches ) ) {
				continue;
			}

			$method_version = str_replace( '_', '.', $matches[1][0] );

			if ( ! version_compare( $method_version, $this->current_version, '>' ) ) {
				continue;
			}

			$callbacks[] = [ $upgrades_class, $method_name ];
		}

		return $callbacks;
	}

	public function __construct() {
		if ( ! $this->should_upgrade() ) {
			return;
		}

		$updater = $this->get_task_runner();

		$this->start_run();

		if ( $updater->is_running() ) {
			Leo_Helper::add_action( 'admin_notices', [ $this, 'admin_notice_upgrade_is_running' ] );
		}

		parent::__construct();
	}
}