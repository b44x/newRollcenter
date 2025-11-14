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
namespace DynamicProduct\classes\models;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DynamicUnit extends DynamicObject
{
    public $symbol;
    public $displayed = 1;
    public $name;

    private static $units = [];

    public static $definition = [
        'table' => 'dynamicproduct_unit',
        'primary' => 'id_unit',
        'multilang' => true,
        'fields' => [
            'symbol' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'displayed' => ['type' => self::TYPE_INT],
            /* Lang fields */
            'name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
                'validate' => 'isGenericName',
                'size' => 64,
            ],
        ],
    ];

    public static function getUnitSymbol($id_unit)
    {
        return (new self($id_unit))->symbol;
    }

    public static function getUnit($id_unit, $id_lang = null)
    {
        $key = $id_unit . '_' . $id_lang;
        if (isset(self::$units[$key])) {
            return self::$units[$key];
        }

        return self::$units[$key] = new self($id_unit, $id_lang);
    }
}
