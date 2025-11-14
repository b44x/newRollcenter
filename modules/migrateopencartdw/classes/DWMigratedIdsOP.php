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

class DWMigratedIdsOP extends ObjectModel
{

    public $id_recent_data;
    public $type_recent_data;
    public $source_id;

    public static $definition = array(
        'table'   => 'dw_migrated_ids',
        'primary' => 'id_data',
        'fields'  => array(
            'type_recent_data'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'migarted_ids'  => array('type' => self::TYPE_STRING, 'required' => true)
        ),
    );


    public static function import($type_recent_data, $migarted_ids)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'dw_migrated_ids SET type_recent_data="' . pSQL($type_recent_data) . '" , migarted_ids="' . pSQL(implode(',', $migarted_ids)) . '" ON DUPLICATE KEY UPDATE migarted_ids="' . pSQL(implode(',', $migarted_ids)) . '"';

        return Db::getInstance()->execute($sql);
    }

    public static function deleteSavedRecord($type_recent_data)
    {
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'dw_migrated_ids WHERE type_recent_data = "' . pSQL($type_recent_data) . '"');
    }

    public static function getImportedIds($type_recent_data)
    {
        $row = Db::getInstance()->executeS('SELECT migarted_ids FROM ' . _DB_PREFIX_ . 'dw_migrated_ids WHERE type_recent_data = "' . pSQL($type_recent_data) . '"');

        return $row[0]['migarted_ids'];
    }
}
