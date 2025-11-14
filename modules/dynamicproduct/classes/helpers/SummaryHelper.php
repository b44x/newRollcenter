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
/**
 * 2007-2024 TuniSoft
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
 * @copyright 2007-2024 TuniSoft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace DynamicProduct\classes\helpers;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\DynamicInput;

class SummaryHelper
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

    /**
     * @param $summary_name
     * @param DynamicInput $dynamic_input
     * @param $id_lang
     * @param $is_pdf
     * @param $is_order_detail
     *
     * @return bool|string
     */
    public function getCachedSummary($summary_name, $dynamic_input, $id_lang, $is_pdf, $is_order_detail)
    {
        if (!DynamicTools::isCacheEnabled()) {
            return false;
        }
        $cache_file = $this->getCacheFile($summary_name, $dynamic_input, $id_lang, $is_pdf, $is_order_detail);
        if (is_file($cache_file)) {
            return \Tools::file_get_contents($cache_file);
        }

        return false;
    }

    /**
     * @param $summary_name
     * @param DynamicInput $dynamic_input
     * @param $id_lang
     * @param $is_pdf
     * @param $is_order_detail
     * @param $summary
     *
     * @return false|int
     */
    public function cacheSummary($summary_name, $dynamic_input, $id_lang, $is_pdf, $is_order_detail, $summary)
    {
        $cache_file = $this->getCacheFile($summary_name, $dynamic_input, $id_lang, $is_pdf, $is_order_detail);

        if (!is_dir(dirname($cache_file))) {
            mkdir(dirname($cache_file), 0777, true);
        }

        return file_put_contents($cache_file, $summary);
    }

    /**
     * @param $summary_name
     * @param DynamicInput $dynamic_input
     * @param $id_lang
     * @param $is_pdf
     * @param $is_order_detail
     *
     * @return string
     */
    private function getCacheFile($summary_name, $dynamic_input, $id_lang, $is_pdf, $is_order_detail)
    {
        $date_upd = md5($dynamic_input->date_upd);
        $version = $this->module->version;

        return $this->module->provider->getDataFile(
            "cache/$dynamic_input->id/$summary_name-$version-{$date_upd}-$id_lang-$is_pdf-$is_order_detail.html"
        );
    }

    /**
     * @param int $id_input
     *
     * @return bool
     */
    public function clearCache($id_input)
    {
        $cache_dir = $this->module->provider->getDataDir("cache/$id_input");
        if (!is_dir($cache_dir)) {
            return true;
        }

        return \Tools::deleteDirectory($cache_dir, false);
    }
}
