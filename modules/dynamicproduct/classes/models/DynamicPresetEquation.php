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

class DynamicPresetEquation extends DynamicObject
{
    public $id_product;
    public $formula;

    public static $definition = [
        'table' => 'dynamicproduct_preset_equation',
        'primary' => 'id_preset_equation',
        'fields' => [
            'formula' => ['type' => self::TYPE_HTML],
        ],
    ];

    public static function getAll($id_lang = null)
    {
        $objects = [];
        $sql = new \DbQuery();
        $sql->select('id_preset_equation');
        $sql->from(self::$definition['table']);
        $sql->orderBy('id_preset_equation DESC');
        $rows = \Db::getInstance()->executeS($sql);

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $id_object = $row['id_preset_equation'];
                $object = new self($id_object, $id_lang);
                $objects[] = $object;
            }
        }

        return $objects;
    }
}
