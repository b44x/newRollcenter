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

class DynamicMedia
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    private $hash;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
        $this->hash = $this->module->version;
    }

    public function addJS($js_uri)
    {
        if (!$js_uri) {
            return;
        }
        $controller = \Context::getContext()->controller;
        if (!is_array($js_uri)) {
            $js_uri = [$js_uri];
        }
        if (method_exists($controller, 'registerJavascript')) {
            foreach ($js_uri as $js_file) {
                if (preg_match("/^(views|lib)\//", $js_file)) {
                    $js_file = 'modules/dynamicproduct/' . $js_file;
                }

                $controller->registerJavascript(
                    md5($js_file),
                    $js_file,
                    ['priority' => 100, 'position' => 'bottom']
                );
            }
        } else {
            foreach ($js_uri as $key => $js_file) {
                if (preg_match("/^(views|lib)\//", $js_file)) {
                    $js_uri[$key] = $this->module->getPath() . $js_file;
                }
            }
            $controller->addJS($js_uri);
        }
    }

    public function addCSS($css_uri)
    {
        $controller = \Context::getContext()->controller;
        if (!is_array($css_uri)) {
            $css_uri = [$css_uri];
        }
        if (method_exists($controller, 'registerStylesheet')) {
            foreach ($css_uri as $css_file) {
                if (preg_match("/^(views|lib)\//", $css_file)) {
                    $css_file = $this->module->getModuleDir() . $css_file;
                }
                $controller->registerStylesheet(md5($css_file), $css_file);
            }
        } else {
            foreach ($css_uri as $key => $css_file) {
                if (preg_match("/^(views|lib)\//", $css_file)) {
                    $css_uri[$key] = $this->module->getPath() . $css_file;
                }
            }
            $controller->addCSS($css_uri);
        }
    }

    public function getJSDir()
    {
        return 'js/';
    }

    public function getCSSDir()
    {
        return 'css/';
    }

    public function getThemeJSDir()
    {
        return '/themes/' . _THEME_NAME_ . '/assets/js/';
    }

    public function getThemeCSSDir()
    {
        return '/themes/' . _THEME_NAME_ . '/assets/css/';
    }

    public function addHash($files)
    {
        if (is_array($files)) {
            foreach ($files as &$file) {
                $file = $file . '?' . $this->hash;
            }

            return $files;
        }

        if (is_string($files)) {
            return $files . '?' . $this->hash;
        }

        return $files;
    }
}
