<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

$sql = array();

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'leoelements_profiles`, `' . _DB_PREFIX_ . 'leoelements_profiles_lang`, `' . _DB_PREFIX_ . 'leoelements_profiles_shop`, `' . _DB_PREFIX_ . 'leoelements_category`, `' . _DB_PREFIX_ . 'leoelements_category_shop`,`' . _DB_PREFIX_ . 'leoelements_product_list`,`'. _DB_PREFIX_ . 'leoelements_product_list_shop`,`'. _DB_PREFIX_ . 'leoelements_products`,`'. _DB_PREFIX_ . 'leoelements_products_shop`,`' . _DB_PREFIX_ . 'leoelements_positions`,`'. _DB_PREFIX_ . 'leoelements_contents`,`'. _DB_PREFIX_ . 'leoelements_contents_lang`,`'. _DB_PREFIX_ . 'leoelements_contents_shop`,`'. _DB_PREFIX_ . 'leoelements_related`,`'. _DB_PREFIX_ . 'leoelements_related_shop`,`'. _DB_PREFIX_ . 'leoelements_template`,`'. _DB_PREFIX_ . 'leoelements_meta`,`'. _DB_PREFIX_ . 'leoelements_revisions`';


//$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'leoelements_post`;';
//
//
//$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'leoelements_post_lang`;';


$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'leoelements_related`;';


$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'leoelements_related_shop`;';


$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'leoelements_template`;';


$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'leoelements_meta` ;';


$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'leoelements_revisions`;';

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'category` DROP `leoe_layout`, DROP `leoe_layout_mobile`, DROP `leoe_layout_tablet`;';
$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'category_shop` DROP `leoe_layout`, DROP `leoe_layout_mobile`, DROP `leoe_layout_tablet`;';
$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'category_lang` DROP `leoe_extra_1`,DROP `leoe_extra_2`;';

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product` DROP `leoe_layout`,DROP `leoe_layout_mobile`,DROP `leoe_layout_tablet`;';
$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_shop` DROP `leoe_layout`,DROP `leoe_layout_mobile`,DROP `leoe_layout_tablet`;';
$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_lang` DROP `leoe_extra_1`,DROP `leoe_extra_2`;';



foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
