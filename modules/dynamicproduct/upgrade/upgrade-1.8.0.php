<?php
/**
 * 2007-2025 TuniSoft
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
 * @author    TuniSoft (tunisoft.solutions@gmail.com)
 * @copyright 2007-2025 TuniSoft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_8_0($module)
{
    $queries = [
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . $module->name . '_proportions` (
          `id_proportion` int(11) NOT NULL AUTO_INCREMENT,
          `id_product` int(11) NOT NULL,
          `id_field` int(11) NOT NULL DEFAULT 0,
          `id_field_src` int(11) NOT NULL DEFAULT 0,
          `value` decimal(20,6) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id_proportion`)
        ) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;',
    ];

    $success = true;

    $errors = '';
    foreach ($queries as $query) {
        try {
            Db::getInstance()->Execute($query);
        } catch (Exception $e) {
            $errors .= $e->getMessage() . '<br>';
        }
    }

    return $success;
}
