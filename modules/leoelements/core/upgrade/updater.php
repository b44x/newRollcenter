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

namespace LeoElements\Core\Upgrade;

use LeoElements\Core\Base\Background_Task;

defined( '_PS_VERSION_' ) || exit;

class Updater extends Background_Task {

	protected function format_callback_log( $item ) {
		return $this->manager->get_plugin_label() . '/Upgrades - ' . $item['callback'][1];
	}
}
