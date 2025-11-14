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

/**
 * @param DynamicProduct $module
 *
 * @return bool
 */
function upgrade_module_2_2_0($module)
{
    $queries = [
        'CREATE TABLE IF NOT EXISTS `ps_dynamicproduct_input_field` (
          `id_input_field` int(11) NOT NULL AUTO_INCREMENT,
          `id_input` int(11) NOT NULL,
          `id_product` int(11) NOT NULL,
          `id_field` int(11) NOT NULL,
          `name` varchar(50) NOT NULL,
          `value` varchar(200) NOT NULL,
          `options` varchar(200) NOT NULL,
          `type` int(11) NOT NULL,
          `visible` tinyint(1) NOT NULL DEFAULT 1,
          `disabled` tinyint(1) NOT NULL DEFAULT 1,
          `width` int(11) NOT NULL,
          `height` int(11) NOT NULL,
          `size` int(11) NOT NULL,
          PRIMARY KEY (`id_input_field`)
        ) ENGINE=InnoDb DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;',
    ];

    $errors = '';
    foreach ($queries as $query) {
        try {
            $query = str_replace('ps_dynamicproduct', _DB_PREFIX_ . $module->name, $query);
            $query = str_replace('InnoDb', _MYSQL_ENGINE_, $query);
            Db::getInstance()->execute($query);
        } catch (Exception $e) {
            $errors .= $e->getMessage() . '<br>';
        }
    }

    return true;
}
