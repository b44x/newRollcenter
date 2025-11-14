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

namespace LeoElements\Core\Logger\Loggers;

use LeoElements\Core\Logger\Items\Log_Item_Interface as Log_Item;
use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

class Db extends Base {

	public function save_log( Log_Item $item ) {
		$log = $this->maybe_truncate_log();

		$id = $item->get_fingerprint();

		if ( empty( $log[ $id ] ) ) {
			$log[ $id ] = $item;
		}

		$log[ $id ]->increase_times( $item );

		Leo_Helper::update_option( self::LOG_NAME, $log, 'no' );
	}

	private function maybe_truncate_log() {
		/** @var Log_Item[] $log */
		$log = $this->get_log();

		if ( Log_Item::MAX_LOG_ENTRIES < count( $log ) ) {
			$log = array_slice( $log, -Log_Item::MAX_LOG_ENTRIES );
		}

		return $log;
	}

	protected function get_log() {
		// Clear cache.
		wp_cache_delete( self::LOG_NAME, 'options' );

		return Leo_Helper::get_option( self::LOG_NAME, [] );
	}
}
