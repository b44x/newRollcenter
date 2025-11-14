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
function upgrade_module_3_4_5($module)
{
    $thumbs = Db::getInstance()->executeS('
        SELECT id_thumbnails_option, image, image_width, image_height
        FROM `' . _DB_PREFIX_ . 'dynamicproduct_thumbnails_option`');

    foreach ($thumbs as $thumb) {
        $image = $thumb['image'];
        if (!$image) {
            continue;
        }

        if ($thumb['image_width'] && $thumb['image_height']) {
            continue;
        }

        $image_path = $module->provider->getDataDir('images/thumbnails') . $image;
        if (!file_exists($image_path)) {
            continue;
        }

        list($width, $height) = getimagesize($image_path);
        if (!$width || !$height) {
            continue;
        }

        Db::getInstance()->update(
            'dynamicproduct_thumbnails_option',
            [
                'image_width' => $width,
                'image_height' => $height,
            ],
            'id_thumbnails_option = ' . (int) $thumb['id_thumbnails_option']
        );
    }

    $options = Db::getInstance()->executeS('
        SELECT id_dropdown_option, image, image_width, image_height
        FROM `' . _DB_PREFIX_ . 'dynamicproduct_dropdown_option`');

    foreach ($options as $option) {
        $image = $option['image'];
        if (!$image) {
            continue;
        }

        if ($option['image_width'] && $option['image_height']) {
            continue;
        }

        $image_path = $module->provider->getDataDir('images/dropdown') . $image;
        if (!file_exists($image_path)) {
            continue;
        }

        list($width, $height) = getimagesize($image_path);
        if (!$width || !$height) {
            continue;
        }

        Db::getInstance()->update(
            'dynamicproduct_dropdown_option',
            [
                'image_width' => $width,
                'image_height' => $height,
            ],
            'id_dropdown_option = ' . (int) $option['id_dropdown_option']
        );
    }

    return true;
}
