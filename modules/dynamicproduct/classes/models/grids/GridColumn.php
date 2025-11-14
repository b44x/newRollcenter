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
namespace DynamicProduct\classes\models\grids;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\models\DynamicObject;

class GridColumn extends DynamicObject
{
    public $id_grid;
    public $value;

    public static $definition = [
        'table' => 'dynamicproduct_grid_column',
        'primary' => 'id_grid_column',
        'multilang' => false,
        'fields' => [
            'id_grid' => ['type' => self::TYPE_INT],
            'value' => ['type' => self::TYPE_STRING],
        ],
    ];

    public static function getByGrid($id_grid)
    {
        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_grid = ' . (int) $id_grid);
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new self($id);
            if (\Validate::isLoadedObject($object)) {
                if (is_numeric($object->value)) {
                    $object->value = (float) $object->value;
                }
                $objects[$id] = $object;
            }
        }

        return $objects;
    }

    public function delete()
    {
        $grid_values = GridValue::getByColumn($this->id);
        foreach ($grid_values as $grid_value) {
            $grid_value->delete();
        }

        return parent::delete();
    }
}
