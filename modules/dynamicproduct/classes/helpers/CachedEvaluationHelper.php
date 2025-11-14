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
namespace DynamicProduct\classes\helpers;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;

class CachedEvaluationHelper
{
    private static $cache = [];
    private static $cache_touched = [];

    public static function loadEvaluationCache($id_product)
    {
        if (!DynamicTools::isCacheEnabled()) {
            return false;
        }

        if (isset(self::$cache[$id_product])) {
            return self::$cache[$id_product];
        }

        $cache_file = self::getCacheFile($id_product);

        if (is_file($cache_file)) {
            $contents = \Tools::file_get_contents($cache_file);

            self::$cache[$id_product] = json_decode($contents, true);
        }
    }

    public static function getCached($id_product, $hash)
    {
        if (!$hash) {
            return false;
        }

        if (isset(self::$cache[$id_product][$hash])) {
            return self::$cache[$id_product][$hash];
        }

        return false;
    }

    public static function hashExpression($expression, $formula)
    {
        if (strpos($formula, '{') !== false) {
            return false;
        }

        return md5($expression);
    }

    public static function cache($id_product, $hash, $result)
    {
        if (!$hash) {
            return false;
        }

        if (!DynamicTools::isCacheEnabled()) {
            return false;
        }

        if (!isset(self::$cache[$id_product])) {
            self::$cache[$id_product] = [];
        }

        if (self::getCached($id_product, $hash)) {
            return false;
        }

        self::$cache[$id_product][$hash] = $result;
        self::$cache_touched[$id_product] = true;

        return true;
    }

    private static function getCacheFile($id_product)
    {
        $module = DynamicTools::getModule();

        return $module->provider->getDataFile(
            "cache/evaluation-$id_product.json"
        );
    }

    public static function storeEvaluationCache(int $id_product)
    {
        if (!DynamicTools::isCacheEnabled()) {
            return false;
        }

        if (!isset(self::$cache_touched[$id_product])) {
            return false;
        }

        if (!isset(self::$cache[$id_product])) {
            return false;
        }

        $cache_file = self::getCacheFile($id_product);

        $contents = json_encode(self::$cache[$id_product]);

        file_put_contents($cache_file, $contents);
    }
}
