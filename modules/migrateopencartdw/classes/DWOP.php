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

class DWOP extends ObjectModel
{
    public $id;
    public $mail;
    public $passwd;
    public $salt;

    public static $definition = array(
        'table'   => 'dw_op',
        'primary' => 'id_op',
        'fields'  => array(
            'mail'        => array(
                'type'     => self::TYPE_STRING,
                'validate' => 'isEmail',
                'required' => true,
                'size'     => 255
            ),
            'id_customer' => array(
                'type'     => self::TYPE_STRING,
                'validate' => 'isUnsignedInt',
                'required' => true,
                'size'     => 11
            ),
            'passwd'      => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255),
            'salt'        => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255),
        ),
    );

    public static function checkCustomer($mail)
    {
        $sql = 'SELECT mail FROM ' . _DB_PREFIX_ . 'dw_op WHERE mail=\'' . pSQL($mail) . '\'';

        return Db::getInstance()->getRow($sql);
    }

    public static function checkPassUpdated($mail)
    {
        (int)$staticPass = Tools::encrypt(123456);
        $sql = 'SELECT email FROM ' . _DB_PREFIX_ . 'customer WHERE email = \''. pSQL($mail) .'\' AND passwd=\'' . (int)$staticPass . '\'';

        return Db::getInstance()->executeS($sql);
    }

    public static function storeCustomerPass($id_customer, $mail, $pass, $salt)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'dw_op SET id_customer=' . (int)$id_customer . ', mail=\'' . pSQL($mail) . '\',
        passwd=\'' . pSQL($pass) . '\', salt=\'' .pSql($salt). '\'';

        return Db::getInstance()->execute($sql);
    }


    public static function getOpUser($mail)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'dw_op where mail=\'' . pSQL($mail) . '\'';

        return Db::getInstance()->executeS($sql);
    }

    public static function deleteUserById($id)
    {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'dw_op where id_customer=' . (int)$id;

        return Db::getInstance()->execute($sql);
    }
}
