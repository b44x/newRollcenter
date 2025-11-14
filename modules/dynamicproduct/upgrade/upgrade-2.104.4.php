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
function upgrade_module_2_104_4($module)
{
    $dropdown_options = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'dynamicproduct_dropdown_option`');

    foreach ($dropdown_options as $dropdown_option) {
        if (!empty($dropdown_option['image'])) {
            continue;
        }
        $id = $dropdown_option['id_dropdown_option'];
        $file = $module->provider->getDataFile('images/dropdown/' . $id . '.jpg');
        if (is_file($file)) {
            Db::getInstance()->execute(
                '
            UPDATE `' . _DB_PREFIX_ . 'dynamicproduct_dropdown_option` 
            SET `image` = "' . (int) $id . '.jpg" 
            WHERE `id_dropdown_option` = ' . (int) $id
            );
        }
    }

    $thumbnails_options = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'dynamicproduct_thumbnails_option`');

    foreach ($thumbnails_options as $thumbnails_option) {
        if (!empty($thumbnails_option['image'])) {
            continue;
        }
        $id = $thumbnails_option['id_thumbnails_option'];
        $file = $module->provider->getDataFile('images/thumbnails/' . $id . '.jpg');
        if (is_file($file)) {
            Db::getInstance()->execute(
                '
            UPDATE `' . _DB_PREFIX_ . 'dynamicproduct_thumbnails_option` 
            SET `image` = "' . (int) $id . '.jpg" 
            WHERE `id_thumbnails_option` = ' . (int) $id
            );
        }
    }

    return true;
}
