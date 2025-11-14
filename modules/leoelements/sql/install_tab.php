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

$id_parent = Tab::getIdFromClassName('AdminLeoElements');
$tabvalue = array(
    array(
        'class_name' => 'AdminLeoElementsDashboard',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Leo Dashboard',
        'active' => 1,
    ),

    array(
        'class_name' => 'AdminLeoElementsProfiles',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Profiles: Home or LandingPage',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsPositions',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Positions: Header Content Footer',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsContents',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Hook And Content Any Where',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsProducts',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Products Builder',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsCategory',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Categories Builder',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsProductList',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Product Lists Builder',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsFont',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Font Configuration',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsHook',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Hook Manage',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminLeoElementsCreator',
        'id_parent' => $id_parent,
        'module' => 'leoelements',
        'name' => 'Elements Creator',
        'active' => 0,
    ),
);