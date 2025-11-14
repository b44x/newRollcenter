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

class DynamicEquationHelper
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

    public static function getGridValue($database, $row_value, $column_value, $default = 0)
    {
        $source = DynamicTools::getSource();
        $module = DynamicTools::getModule();
        $filename = $database . '.csv';
        $database_file = $module->provider->getDataFile('databases/' . $filename);
        if (!is_file($database_file)) {
            \DynamicProduct::$debug_messages['errors'][] = sprintf(
                $module->l('File %s not found in %s', $source),
                $filename,
                $module->provider->getDataDir('databases')
            );

            return $default;
        }

        if (\Cache::isStored($database_file)) {
            $csv = \Cache::retrieve($database_file);
        } else {
            $csv = array_map('str_getcsv', file($database_file));
            \Cache::store($database_file, $csv);
        }

        $idx_first = 0;
        $idx_second = 0;

        if (isset($csv[0])) {
            $first_columns = $csv[0];
            $count_first_columns = count($first_columns);
            for ($i = 1; $i < $count_first_columns - 1; ++$i) {
                $value_comprised = $column_value >= (float) $first_columns[$i]
                    && $column_value < (float) $first_columns[$i + 1];
                $value_identical = DynamicTools::isString($first_columns[$i]) && $column_value === $first_columns[$i];
                if ($value_comprised || $value_identical) {
                    $idx_first = $i;
                    break;
                }
            }
            $last_value = $first_columns[$count_first_columns - 1];
            $bigger = is_numeric($last_value) && $column_value >= $last_value;
            $identical = DynamicTools::isString($last_value) && $column_value === $last_value;
            if ($bigger || $identical) {
                $idx_first = $count_first_columns - 1;
            }
        }

        $count_second_columns = count($csv);

        for ($i = 1; $i < $count_second_columns - 1; ++$i) {
            if (isset($csv[$i][0])) {
                $value_comprised = $row_value >= (float) $csv[$i][0] && $row_value < (float) $csv[$i + 1][0];
                $value_identical = DynamicTools::isString($csv[$i][0]) && $row_value === $csv[$i][0];
                if (isset($csv[$i][0]) && ($value_comprised || $value_identical)) {
                    $idx_second = $i;
                    break;
                }
            }
        }

        $bigger = is_numeric($csv[$count_second_columns - 1][0]) && $row_value >= $csv[$count_second_columns - 1][0];
        $identical = DynamicTools::isString($csv[$count_second_columns - 1][0])
            && $row_value === $csv[$count_second_columns - 1][0];
        if ($bigger || $identical) {
            $idx_second = $count_second_columns - 1;
        }

        if ($idx_first && $idx_second && isset($csv[$idx_second][$idx_first])) {
            return (float) $csv[$idx_second][$idx_first];
        }

        return $default;
    }
}
