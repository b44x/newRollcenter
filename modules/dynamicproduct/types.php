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

if (!defined('_DP_PRICE_')) {
    define('_DP_INPUT_', 1);
    define('_DP_FIXED_', 2);
    define('_DP_PRICE_', 3);
    define('_DP_TEXT_', 4);
    define('_DP_DATE_', 5);
    define('_DP_IMAGE_', 6);
    define('_DP_PHP_', 7);
    define('_DP_DROPDOWN_', 8);
    define('_DP_CHECKBOX_', 9);
    define('_DP_FILE_', 10);
    define('_DP_SLIDER_', 11);
    define('_DP_THUMBNAILS_', 12);
    define('_DP_TEXTAREA_', 13);
    define('_DP_FEATURE_', 14);
    define('_DP_DIVIDER_', 15);
    define('_DP_RADIO_', 16);
    define('_DP_COLORPICKER_', 17);
    define('_DP_HTML_', 18);
    define('_DP_ERROR_', 19);
    define('_DP_SWITCH_', 20);
    define('_DP_CUSTOM_', 21);
    define('_DP_PREVIEW_', 22);
    define('_DP_COUNTRY_', 23);

    define('_DP_PRICE_EQ_', 0);
    define('_DP_WEIGHT_EQ_', 1);
}
if (!defined('_DP_CACHE_')) {
    define('_DP_CACHE_', true);
}

if (!defined('_DP_THUMB_')) {
    define('_DP_THUMB_', 256);
}
