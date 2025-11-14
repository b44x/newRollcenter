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
if (!defined('_PS_VERSION_')) {
    exit;
}
 return array(
    'root' => array(
        'name' => '__root__',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => '6903822d2c39c9c00ce3d38f41e3e2809993e4c7',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '6903822d2c39c9c00ce3d38f41e3e2809993e4c7',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'maennchen/zipstream-php' => array(
            'pretty_version' => '2.1.0',
            'version' => '2.1.0.0',
            'reference' => 'c4c5803cc1f93df3d2448478ef79394a5981cc58',
            'type' => 'library',
            'install_path' => __DIR__ . '/../maennchen/zipstream-php',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'myclabs/php-enum' => array(
            'pretty_version' => '1.8.4',
            'version' => '1.8.4.0',
            'reference' => 'a867478eae49c9f59ece437ae7f9506bfaa27483',
            'type' => 'library',
            'install_path' => __DIR__ . '/../myclabs/php-enum',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/http-message' => array(
            'pretty_version' => '1.1',
            'version' => '1.1.0.0',
            'reference' => 'cb6ce4845ce34a8ad9e68117c10ee90a29919eba',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/http-message',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => array(
            'pretty_version' => 'v1.31.0',
            'version' => '1.31.0.0',
            'reference' => '85181ba99b2345b0ef10ce42ecac37612d9fd341',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-mbstring',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
