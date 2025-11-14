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
namespace DynamicProduct\classes;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DynamicTools
{
    public static $name;
    public static $module;
    public static $context;
    public static $languages;

    public static function getCallingFile()
    {
        $trace = debug_backtrace(false);
        $file = '';
        if (isset($trace[1])) {
            $file = $trace[1]['file'];
        }

        return $file;
    }

    public static function getSource()
    {
        $file = self::getCallingFile();

        return pathinfo($file, PATHINFO_FILENAME);
    }

    public static function getGenericSource()
    {
        return 'translations';
    }

    public static function checkRestricted($id, $array)
    {
        $context = self::getContext();

        return (int) $context->employee->id_profile !== 1 && in_array((int) $id, $array, false);
    }

    public static function getRestricted($string)
    {
        if (!defined($string)) {
            return [];
        }
        $value = constant($string);

        return explode('-', $value);
    }

    public static function isDemoMode()
    {
        return defined('_DP_DEMO_') && _DP_DEMO_;
    }

    public static function isModuleDevMode()
    {
        return (\defined('_PS_MODULE_DEV_') && _PS_MODULE_DEV_) || self::isModuleDevModeForced() || getenv('E2E') === 'true';
    }

    public static function isModuleDevModeForced()
    {
        return (\defined('_PS_MODULE_DEV_') && (string) _PS_MODULE_DEV_ === 'force') || isset($_COOKIE['XDEBUG_TRACE']);
    }

    public static function isCacheEnabled()
    {
        return !defined('_PS_MODULE_CACHE_') || _PS_MODULE_CACHE_;
    }

    public static function getHotPort()
    {
        if (!self::isModuleDevMode()) {
            return false;
        }

        return _DP_FRONT_DEV_PORT_;
    }

    public static function isHotMode($port)
    {
        if (self::isModuleDevModeForced()) {
            return true;
        }
        if (!self::isModuleDevMode()) {
            return false;
        }
        if (\Tools::getIsset('hot')) {
            return true;
        }

        $socket_ip = '127.0.0.1';
        if (defined('_PS_SOCK_IP_')) {
            $socket_ip = _PS_SOCK_IP_;
        }

        if (getenv('SOCK_IP')) {
            $socket_ip = getenv('SOCK_IP');
        }

        $fsock = @fsockopen($socket_ip, $port, $errno, $errstr, 1);

        return (bool) $fsock;
    }

    public static function isSuperAdmin()
    {
        $context = self::getContext();

        return (int) $context->employee->id_profile === 1;
    }

    public static function getDir()
    {
        return realpath(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
    }

    public static function organizeBy($key, $source, $select = false)
    {
        $return = [];
        if (is_array($source)) {
            foreach ($source as $row) {
                $akey = $row[$key];
                if (!$select) {
                    $return[$akey] = $row;
                } else {
                    $return[$akey] = $row[$select];
                }
            }
        }

        return $return;
    }

    public static function organizeDoubleBy($key1, $key2, $source, $select = false, $unset = false)
    {
        $return = [];
        if (is_array($source)) {
            foreach ($source as $row) {
                $akey1 = $row[$key1];
                $akey2 = $row[$key2];
                if ($unset) {
                    unset($row[$key1], $row[$key2]);
                }
                if (!$select) {
                    $return[$akey1][$akey2] = $row;
                } else {
                    $return[$akey1][$akey2] = $row[$select];
                }
            }
        }

        return $return;
    }

    public static function mergeRecursive($array1, $array2)
    {
        $keys = array_keys($array2);
        foreach ($keys as $key) {
            if (isset($array1[$key])) {
                $array1[$key] = array_merge($array1[$key], $array2[$key]);
            } else {
                $array1[$key] = $array2[$key];
            }
        }

        return $array1;
    }

    public static function getAdminLink($add = '')
    {
        $module = self::getModule();

        return $module->currentIndex .
            '&configure=' . $module->name .
            '&token=' . \Tools::getAdminTokenLite('AdminModules') .
            $add;
    }

    public static function redirect($add = '')
    {
        \Tools::redirectAdmin(self::getAdminLink($add));
    }

    public static function checkAddress()
    {
        $context = self::getContext();
        $id_cart = $context->cart ? $context->cart->id : 0;
        if (!$id_cart) {
            return;
        }
        $id_customer = $context->cookie ? $context->cookie->id_customer : 0;
        if ($id_customer) {
            $id_address_delivery = $context->cart ? $context->cart->id_address_delivery : 0;
            \Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'customization`
                    SET `id_address_delivery` = ' . (int) $id_address_delivery
                . ' WHERE `id_cart` = ' . (int) $id_cart . ' AND `id_address_delivery`=0'
            );
        }
    }

    public static function translate($string, $iso_lang, $source, $js = false)
    {
        $file = dirname(__FILE__, 2) . '/translations/' . $iso_lang . '.php';
        if (!file_exists($file)) {
            return $string;
        }
        $_MODULE = [];
        if (is_file($file)) {
            include $file;
        }
        $key = md5(str_replace("\'", '\\\'', $string));

        $module = self::getModule();

        $current_key = \Tools::strtolower('<{' . $module->name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
        $default_key = \Tools::strtolower('<{' . $module->name . '}prestashop>' . $source) . '_' . $key;
        $ret = $string;
        if (isset($_MODULE[$current_key])) {
            $ret = stripslashes($_MODULE[$current_key]);
        } elseif (isset($_MODULE[$default_key])) {
            $ret = stripslashes($_MODULE[$default_key]);
        }

        if ($js) {
            $ret = addslashes($ret);
        }

        return $ret;
    }

    public static function addQueryToUrl($link, $values)
    {
        $queries = [];
        foreach ($values as $key => $value) {
            $queries[] = $key . ($value ? '=' . $value : '');
        }
        $query = implode('&', $queries);
        $hash = '';
        if (preg_match("/\#/", $link)) {
            list($link, $hash) = explode('#', $link);
        }
        if (!preg_match("/\?/", $link)) {
            $link .= '?';
        } else {
            $link .= '&';
        }
        $link .= $query;
        if (\Tools::strlen($hash)) {
            $link .= '#' . $hash;
        }

        return $link;
    }

    /**
     * @return false|\DynamicProduct
     */
    public static function getModule()
    {
        if (self::$module) {
            return self::$module;
        }

        return self::$module = \Module::getInstanceByName('dynamicproduct');
    }

    /**
     * @return \Context
     */
    public static function getContext()
    {
        if (self::$context) {
            return self::$context;
        }

        return self::$context = \Context::getContext();
    }

    public static function productHasAttributeGroup($id_product, $id_group)
    {
        return \Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'attribute a
              LEFT JOIN ' . _DB_PREFIX_ . 'attribute_group ag
                ON a.id_attribute_group = ag.id_attribute_group
              LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac
                ON a.id_attribute = pac.id_attribute
              LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa
                ON pa.id_product_attribute = pac.id_product_attribute
              WHERE pa.id_product = ' . (int) $id_product . ' AND ag.id_attribute_group = ' . (int) $id_group
        );
    }

    public static function removeAnchor($product_url)
    {
        return preg_replace('/#.*/', '', $product_url);
    }

    public static function canUseCache()
    {
        return !count($_POST);
    }

    public static function formatPrice($price)
    {
        $context = self::getContext();
        $locale = null;
        if (method_exists($context, 'getCurrentLocale')) {
            $locale = $context->getCurrentLocale();
        } elseif (method_exists('Tools', 'getContextLocale')) {
            $locale = \Tools::getContextLocale($context);
        }
        if ($locale) {
            return $locale->formatPrice($price, $context->currency->iso_code);
        }

        return \Tools::displayPrice($price);
    }

    public static function capitalizeFilename($name)
    {
        $filename = pathinfo($name, PATHINFO_FILENAME);
        $filename = str_replace(['-', '_'], ' ', $filename);

        return \Tools::ucfirst($filename);
    }

    /**
     * Get contents of a URL and cache it
     *
     * @param string $url address to read from
     * @param float $minutes interval between refreshes, false => no cache
     *
     * @return string content of the URL
     *
     * @noinspection PhpUnused
     */
    public static function getURL($url, $minutes = 60)
    {
        $dynamic_product = self::getModule();
        $cache = $dynamic_product->provider->getDataFile('cache/' . md5($url) . '.cache');
        $should_use_cache = (bool) $minutes;
        if ($should_use_cache) {
            if (file_exists($cache)) {
                $is_cache_valid = filemtime($cache) > time() - 60 * (float) $minutes;
                if ($is_cache_valid) {
                    return \Tools::file_get_contents($cache);
                }
            }
        }
        $content = \Tools::file_get_contents($url);
        file_put_contents($cache, $content);

        return $content;
    }

    public static function encodeFile($path)
    {
        $content = \Tools::file_get_contents($path);

        return base64_encode($content);
    }

    public static function decodeData($data)
    {
        return base64_decode($data);
    }

    public static function convertToArray($object)
    {
        return json_decode(json_encode($object), true);
    }

    public static function isString($value)
    {
        return is_string($value) && !is_numeric($value);
    }

    public static function mapAttributes($attributes_old, $attributes_new, $force_mapping = false)
    {
        $mapping = [];
        if (count($attributes_old) !== count($attributes_new) && !$force_mapping) {
            return [];
        }

        foreach ($attributes_old as $index => $attribute) {
            if (isset($attributes_new[$index])) {
                $mapping[(int) $attribute['id_product_attribute']] = (int) $attributes_new[$index]['id_product_attribute'];
            } else {
                $mapping[(int) $attribute['id_product_attribute']] = 0;
            }
        }

        return $mapping;
    }

    public static function reportException($e, $short = false): string
    {
        if (_PS_MODE_DEV_) {
            throw $e;
        }
        if ($short) {
            return $e->getMessage();
        }

        return "{$e->getMessage()} ({$e->getFile()}:{$e->getLine()})";
    }

    public static function addScriptBase(string $script)
    {
        return 'http://localhost:2001/__vite_base__/' . $script;
    }
}
