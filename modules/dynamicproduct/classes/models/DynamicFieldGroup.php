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

use DynamicProduct\classes\helpers\ConfigLinkHelper;
use DynamicProduct\classes\helpers\ModelHelper;

class DynamicFieldGroup extends DynamicObject
{
    public $label;
    public $name;
    public $show_label = false;

    public static $definition = [
        'table' => 'dynamicproduct_field_group',
        'primary' => 'id_field_group',
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

    public static function getLabel($id_product_group)
    {
        if ($id_product_group) {
            $product_field_group = new DynamicProductFieldGroup($id_product_group);
            $field_group = new DynamicFieldGroup(
                $product_field_group->id_field_group,
                \Context::getContext()->language->id
            );
            if (\Validate::isLoadedObject($field_group) && $field_group->show_label) {
                return $field_group->label;
            }
        }

        return null;
    }

    public static function getRowsByProduct($id_product, $id_lang): array
    {
        $id_source_product = ConfigLinkHelper::getSourceProduct($id_product);

        $groups = \Db::getInstance()->executeS(
            'SELECT fg.*, fgl.*, fg.id_field_group as id FROM `' . _DB_PREFIX_ . 'dynamicproduct_field_group` fg
            LEFT JOIN `' . _DB_PREFIX_ . 'dynamicproduct_field_group_lang` fgl
            ON (fg.`id_field_group` = fgl.`id_field_group` AND fgl.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'dynamicproduct_product_field_group` pfg
            ON (fg.`id_field_group` = pfg.`id_field_group`)
            WHERE pfg.`id_product` = ' . (int) $id_source_product
        );

        return ModelHelper::castNumericValues($groups, self::class);
    }

    public static function getAllRows($id_lang = null): array
    {
        $groups = \Db::getInstance()->executeS(
            'SELECT fg.*, fgl.*, fg.id_field_group as id 
            FROM `' . _DB_PREFIX_ . 'dynamicproduct_field_group` fg
            LEFT JOIN `' . _DB_PREFIX_ . 'dynamicproduct_field_group_lang` fgl
            ON (fg.`id_field_group` = fgl.`id_field_group`' . ($id_lang ? ' AND fgl.`id_lang` = ' . (int) $id_lang : '') . ')
        '
        );

        $id_default_lang = \Configuration::get('PS_LANG_DEFAULT');
        $groups = ModelHelper::groupByLang($groups, ['label']);
        $groups = ModelHelper::pickLang($groups, $id_lang, $id_default_lang, ['label']);

        return array_values(ModelHelper::castNumericValues($groups, self::class));
    }
}
