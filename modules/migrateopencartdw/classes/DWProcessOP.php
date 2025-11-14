<?php
/**
* 2007-2024 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class DWProcessOP extends ObjectModel
{
    public $id;
    public $type;
    public $total;
    public $imported;
    public $id_source;
    public $error;
    public $point;
    public $time_start;
    public $finish;

    public static $definition = array(
        'table'   => 'dw_process',
        'primary' => 'id_process',
        'fields'  => array(
            'type'       => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'total'      => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'imported'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_source'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'error'      => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'point'      => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'time_start' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'finish'     => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true)
        ),
    );

    public static function getActiveProcessObject()
    {
        $query = new DbQuery();
        $query->select('p.id_process');
        $query->from('dw_process', 'p');
        $query->where('p.finish = 0');
        $query->orderBy('p.id_process ASC');
        $result = Db::getInstance()->getValue($query);
        if (!$result) {
            return false;
        }

        return new DWProcessOP($result);
    }

    public static function calculateImportedDataPercent()
    {
        $query = 'SELECT SUM(imported) / SUM(total) * 100 AS percent FROM ' . _DB_PREFIX_ . 'dw_process';
        $result = Db::getInstance()->getValue($query);


        if (!$result) {
            return 0;
        } else {
            return (int)$result;
        }
    }

    public static function getAll()
    {
        $query = new DbQuery();
        $query->select('p.*');
        $query->from('dw_process', 'p');
        $query->orderBy('p.id_process ASC');
        $result = Db::getInstance()->executeS($query);

        return $result;
    }
}
