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
namespace DynamicProduct\classes\module;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DynamicViewer
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getCurrentAction()
    {
        $keys = array_keys($_GET);
        foreach ($keys as $key) {
            if (0 === strpos($key, 'view_')) {
                return str_replace('view_', '', $key);
            }
        }

        return false;
    }

    public function display($view_action)
    {
        $namescape = 'DynamicProduct\classes\viewers\\';
        $class_name = $namescape . \Tools::toCamelCase($view_action, true) . 'Viewer';
        /* @var DynamicViewer $viewer */
        if (class_exists($class_name)) {
            $viewer = new $class_name($this->module, $this->context);

            return $viewer->display();
        }

        return null;
    }
}
