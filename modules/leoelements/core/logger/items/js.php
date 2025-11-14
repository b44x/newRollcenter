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

namespace LeoElements\Core\Logger\Items;

use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

class JS extends File {

	const FORMAT = 'JS: date [type X times][file:line:column] message [meta]';

	protected $column;

	public function __construct( $args ) {
		parent::__construct( $args );
		$this->column = $args['column'];
		$this->file = $args['url'];
		$this->date = date( 'Y-m-d H:i:s', $args['timestamp'] );
	}

	public function jsonSerialize() {
		$json_arr = parent::jsonSerialize();
		$json_arr['column'] = $this->column;
		return $json_arr;
	}

	public function deserialize( $properties ) {
		parent::deserialize( $properties );
		$this->column = ! empty( $properties['column'] ) && is_string( $properties['column'] ) ? $properties['column'] : '';
	}

	public function get_name() {
		return 'JS';
	}
}
