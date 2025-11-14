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

class CachedCalculationHelper
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

    public function getCached($id_product, $id_product_attribute)
    {
        if (!DynamicTools::isCacheEnabled()) {
            return false;
        }
        $cache_file = $this->getCacheFile($id_product, $id_product_attribute);
        if (is_file($cache_file)) {
            $contents = \Tools::file_get_contents($cache_file);

            return json_decode($contents);
        }

        return false;
    }

    public function cache($id_product, $id_product_attribute, $calculation)
    {
        $cache_file = $this->getCacheFile($id_product, $id_product_attribute);

        return file_put_contents($cache_file, json_encode($calculation));
    }

    private function getCacheFile($id_product, $id_product_attribute)
    {
        return $this->module->provider->getDataFile(
            "cache/calculation-$id_product-$id_product_attribute.json"
        );
    }
}
