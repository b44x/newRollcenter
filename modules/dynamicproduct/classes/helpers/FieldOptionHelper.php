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

use DynamicProduct\classes\models\DynamicDropdownOption;
use DynamicProduct\classes\models\DynamicPreviewOption;
use DynamicProduct\classes\models\DynamicRadioOption;
use DynamicProduct\classes\models\DynamicThumbnailsOption;

class FieldOptionHelper
{
    private static $types = [
        _DP_DROPDOWN_ => DynamicDropdownOption::class,
        _DP_THUMBNAILS_ => DynamicThumbnailsOption::class,
        _DP_RADIO_ => DynamicRadioOption::class,
        _DP_PREVIEW_ => DynamicPreviewOption::class,
    ];

    public static function getOptionInstance($id, $type)
    {
        if (isset(self::$types[$type])) {
            $class = self::$types[$type];
            $option = new $class($id);
            $option->id = $id;

            return $option;
        }

        return null;
    }
}
