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

class OPEntityTypeMapper
{
    public static function getEntityTypeNameByAlias($alias)
    {
        $entityTypesAndAliases = self::entityTypes();

        if (array_key_exists($alias, $entityTypesAndAliases)) {
            return $entityTypesAndAliases[$alias];
        }

        return 'Common';
    }

    private static function entityTypes()
    {
        $entityTypeAliasesAndNames = array(
            't' => 'Tax',
            'trg' => 'Tax Rules Group',
            'tr' => 'Tax Rule',
            'co' => 'Country',
            'st' => 'State',
            'c' => 'Category',
            'crr' => 'Carrier',
            'p' => 'Product',
            'atc' => 'Attachment',
            'prd' => 'Product Download',
            'spr' => 'Specific Price Rule',
            'spg' => 'Specific Price Rule Condition Group',
            'spc' => 'Specific Price Rule Condition',
            'ag' => 'Attribute Group',
            'a' => 'Attribute',
            'com' => 'Combination',
            's' => 'Supplier',
            'm' => 'Manufacturer',
            'sp' => 'Specific Price',
            'i' => 'Image',
            'f' => 'Feature',
            'fv' => 'Feature Value',
            'cf' => 'Customization Field',
            'tag' => 'Tag',
            'cus' => 'Customer',
            'ct' => 'Customer Thread',
            'cm' => 'Customer Message',
            'car' => 'Cart',
            'e' => 'Employee',
            'adr' => 'Address',
            'o' => 'Order',
            'od' => 'Order Detail',
            'ort' => 'Order Return',
            'oh' => 'Order History',
            'osp' => 'Order Slip',
            'oi' => 'Order Invoice',
            'oc' => 'Order Carrier',
            'ocr' => 'Order Cart Rule',
            'op' => 'Order Payment',
            'om' => 'Order Message',
            'mes' => 'Message',
            'sa' => 'Stock Available',
            'ps' => 'Product Supplier',
            'cms' => 'CMS',
            'cro' => 'CMS Role',
            'ctg' => 'CMS Category',
            'cbl' => 'CMS Block',
            'cr' => 'Cart Rule',
            'cpg' => 'Cart Rule Product Rule Group',
            'cpr' => 'Cart Rule Product Rule',
            'met' => 'Meta',
            'war' => 'Warehouse',
            'stk' => 'Stock',
            'wpl' => 'Ware House Location',
            'zn' => 'Zone',
            'dlv' => 'Delivery',
            'rn' => 'Range Price',
            'rw' => 'Range Weight',
        );

        return $entityTypeAliasesAndNames;
    }
}
