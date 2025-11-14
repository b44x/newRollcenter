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

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leoelements_profiles` (
    `id_leoelements_profiles` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255),
    `group_box` varchar(255),
    `profile_key` varchar(255),
    `page` varchar(255),
    `params` text,
    `header` varchar(255),
    `content` varchar(255),
    `footer` varchar(255),
    `product` varchar(255),
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_profiles`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leoelements_profiles_lang` (
    `id_leoelements_profiles` int(11) NOT NULL AUTO_INCREMENT,
    `id_lang` int(10) unsigned NOT NULL,
    `friendly_url` varchar(255),
    `meta_title` varchar(255),
    `meta_description` varchar(255),
    `meta_keywords` varchar(255),
PRIMARY KEY (`id_leoelements_profiles`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leoelements_profiles_shop` (
    `id_leoelements_profiles` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(10) unsigned NOT NULL,
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_profiles`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_category` (
    `id_leoelements_category` int(11) NOT NULL AUTO_INCREMENT,
    `clist_key` varchar(255),
    `name` varchar(255),
    `class` varchar(255),
    `params` text,
    `type` TINYINT(1),
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_category`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_category_shop` (
    `id_leoelements_category` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(10) unsigned NOT NULL,
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_category`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_product_list` (
    `id_leoelements_product_list` int(11) NOT NULL AUTO_INCREMENT,
    `plist_key` varchar(255),
    `name` varchar(255),
    `class` varchar(255),
    `params` text,
    `type` TINYINT(1),
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_product_list`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_product_list_shop` (
    `id_leoelements_product_list` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(10) unsigned NOT NULL,
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_product_list`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_products` (
    `id_leoelements_products` int(11) NOT NULL AUTO_INCREMENT,
    `plist_key` varchar(255),
    `name` varchar(255),
    `class_detail` varchar(255),
    `url_img_preview` varchar(255),
    `params` text,
    `type` TINYINT(1),
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_products`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_products_shop` (
    `id_leoelements_products` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(10) unsigned NOT NULL,
    `active` TINYINT(1),
    `active_mobile` TINYINT(1),
    `active_tablet` TINYINT(1),
PRIMARY KEY (`id_leoelements_products`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_positions` (
    `id_leoelements_positions` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `position` varchar(255) NOT NULL,
    `position_key` varchar(255) NOT NULL,
    `params` text,
PRIMARY KEY (`id_leoelements_positions`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leoelements_contents` (
    `id_leoelements_contents` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255),
    `type` varchar(255),
    `content_key` varchar(255),
    `hook` varchar(255),
    `active` TINYINT(1),
PRIMARY KEY (`id_leoelements_contents`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leoelements_contents_lang` (
    `id_leoelements_contents` int(11) NOT NULL,
    `id_lang` int(10) NOT NULL,
    `content` longtext DEFAULT NULL,
    `content_autosave` longtext DEFAULT NULL,
PRIMARY KEY (`id_leoelements_contents`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leoelements_contents_shop` (
    `id_leoelements_contents` int(11) NOT NULL,
    `id_shop` int(10) unsigned NOT NULL,
    `active` TINYINT(1),
PRIMARY KEY (`id_leoelements_contents`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


//$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_post` (
//                `id_leoelements_post` int(10) NOT NULL auto_increment,
//                `id_employee` int(10) unsigned NOT NULL,
//                `title` varchar(40) NOT NULL,
//                `post_type` varchar(40) NOT NULL,
//                `active` tinyint(1) unsigned NOT NULL DEFAULT 0,
//                `date_add` datetime NOT NULL,
//                `date_upd` datetime NOT NULL,
//                PRIMARY KEY (`id_leoelements_post`)
//            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
//
//
//$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_post_lang` (
//                `id_leoelements_post` int(10) NOT NULL,
//                `id_lang` int(10) NOT NULL ,
//                `content` longtext default NULL,
//                `content_autosave` longtext default NULL,
//                PRIMARY KEY (`id_leoelements_post`, `id_lang`)
//            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_related` (
                `id_leoelements_related` int(10) NOT NULL auto_increment,
                `id_post` int(10) unsigned NOT NULL,
                `post_type` varchar(255) NOT NULL,
                `key_related` varchar(255) NOT NULL,
                PRIMARY KEY (`id_leoelements_related`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_related_shop` (
                `id_leoelements_related` int(10) NOT NULL,
                `id_shop` int(10) NOT NULL ,
                PRIMARY KEY (`id_leoelements_related`, `id_shop`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_template` (
                `id_leoelements_template` int(10) NOT NULL auto_increment,
                `id_employee` int(10) unsigned NOT NULL,
                `title` varchar(40) NOT NULL,
                `type` varchar(40) NOT NULL,
                `content` longtext default NULL,
                `page_settings` longtext default NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_leoelements_template`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_meta` (
                `id_leoelements_meta` int(10) NOT NULL auto_increment,
                `id` int(10) unsigned NOT NULL,
                `name` varchar(255) DEFAULT NULL,
                `value` longtext,
                PRIMARY KEY (`id_leoelements_meta`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_revisions` (
                `id_leoelements_revisions` int(10) NOT NULL auto_increment,
                `id_post` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `id_employee` int(10) unsigned NOT NULL,
                `content` longtext default NULL,
                `page_settings` longtext default NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_leoelements_revisions`, `id_post`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;';
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leoelements_fonts` (
                `id_leoelements_fonts` int(10) NOT NULL auto_increment,
                `name` varchar(50) NOT NULL,
                `type` tinyint(1) NOT NULL,
                `font_family` varchar(50) NOT NULL,
                `font_style` varchar(50) default "normal",
                `font_weight` int(3) default 400,
                `file` varchar(255) NOT NULL,
                PRIMARY KEY (`id_leoelements_fonts`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;';
//extrafield for product and category
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category ". "ADD leoe_layout varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category ". "ADD leoe_layout_mobile varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category ". "ADD leoe_layout_tablet varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category_shop ". "ADD leoe_layout varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category_shop ". "ADD leoe_layout_mobile varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category_shop ". "ADD leoe_layout_tablet varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category_lang ". "ADD leoe_extra_1 TEXT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "category_lang ". "ADD leoe_extra_2 TEXT NULL";

$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product ". "ADD leoe_layout varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product ". "ADD leoe_layout_mobile varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product ". "ADD leoe_layout_tablet varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product_shop ". "ADD leoe_layout varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product_shop ". "ADD leoe_layout_mobile varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product_shop ". "ADD leoe_layout_tablet varchar(40) NOT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product_lang ". "ADD leoe_extra_1 TEXT NULL";
$sql[] = "ALTER TABLE " . _DB_PREFIX_ . "product_lang ". "ADD leoe_extra_2 TEXT NULL";

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
