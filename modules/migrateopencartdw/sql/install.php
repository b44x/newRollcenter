<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
  exit;
}

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dw_data` (
`id_data` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(3) NOT NULL,
  `source_id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  PRIMARY KEY (`id_data`),
  UNIQUE KEY `type_source_id` (`type`,`source_id`),
  KEY `type` (`type`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dw_mapping` (
`id_mapping` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `local_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_mapping`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dw_op` (
`id_op` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  PRIMARY KEY (`id_op`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dw_process` (
`id_process` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `imported` int(11) NOT NULL,
  `id_source` int(11) NOT NULL,
  `error` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `time_start` timestamp NOT NULL,
  `finish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_process`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opdw_migrated_data` (
`id_data` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(100) NOT NULL,
  `source_id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  PRIMARY KEY (`id_data`),
  UNIQUE KEY `entity_type_source_id` (`entity_type`,`source_id`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dw_migrated_ids` (
`id_recent_data` int(11) NOT NULL AUTO_INCREMENT,
  `type_recent_data` varchar(100) NOT NULL,
  `migarted_ids` longtext,
  PRIMARY KEY (`id_recent_data`),
  UNIQUE KEY (`type_recent_data`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opdw_error_logs` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `log_text` varchar(855) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `log_date_add` datetime NOT NULL,
  PRIMARY KEY (`id`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opdw_warning_logs` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `log_text` varchar(855) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `log_date_add` datetime NOT NULL,
  PRIMARY KEY (`id`)
)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dw_save_mappingop` (
`id_mapping` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `local_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_mapping`)
)';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
