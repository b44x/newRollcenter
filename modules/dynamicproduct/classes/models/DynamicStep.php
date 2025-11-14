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

use DynamicProduct\classes\helpers\ModelHelper;

class DynamicStep extends DynamicObject
{
    public $label;
    public $name;
    public $show_label = true;

    public static $definition = [
        'table' => 'dynamicproduct_step',
        'primary' => 'id_step',
        'multilang' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING],
            'show_label' => ['type' => self::TYPE_INT],
            /* Lang fields */
            'label' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
                'validate' => 'isGenericName',
                'size' => 64,
            ],
        ],
    ];

    public static function getAllRows($id_lang = null)
    {
        $steps = \Db::getInstance()->executeS(
            'SELECT s.*, sl.*, s.id_step as id 
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_step` s
            LEFT JOIN `' . _DB_PREFIX_ . 'dynamicproduct_step_lang` sl
            ON (s.`id_step` = sl.`id_step`' . ($id_lang ? ' AND sl.`id_lang` = ' . (int) $id_lang : '') . ')
        '
        );

        $id_default_lang = \Configuration::get('PS_LANG_DEFAULT');
        $steps = ModelHelper::groupByLang($steps, ['label']);
        $steps = ModelHelper::pickLang($steps, $id_lang, $id_default_lang, ['label']);

        return array_values(ModelHelper::castNumericValues($steps, self::class));
    }
}
