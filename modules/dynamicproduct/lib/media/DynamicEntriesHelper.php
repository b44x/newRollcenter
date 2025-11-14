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
namespace DynamicProduct\lib\media;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DynamicEntriesHelper
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    private static $entries;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getEntry($name)
    {
        if (!self::$entries) {
            self::$entries = json_decode(\Tools::file_get_contents(__DIR__ . '/dist/.vite/manifest.json'), true);
        }

        if (isset(self::$entries[$name])) {
            return 'lib/media/dist/' . self::$entries[$name]['file'];
        }

        return false;
    }

    public function getCSS($name)
    {
        if (!self::$entries) {
            self::$entries = json_decode(\Tools::file_get_contents(__DIR__ . '/dist/.vite/manifest.json'), true);
        }

        if (isset(self::$entries[$name])) {
            return self::$entries[$name]['css'];
        }

        return false;
    }
}
