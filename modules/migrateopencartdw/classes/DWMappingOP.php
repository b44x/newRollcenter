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

class DWMappingOP extends ObjectModel
{
    public $id;
    public $type;
    public $source_id;
    public $source_name;
    public $local_id;

    public static $definition = array(
        'table'   => 'dw_mapping',
        'primary' => 'id_mapping',
        'fields'  => array(
            'type'        => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'source_id'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'source_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'local_id'    => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId')
        ),
    );

    public static function listMapping($list = false, $keyAsSourceId = false)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('dw_mapping');
        $mappings = array();
        $rows = Db::getInstance()->executeS($sql);
        if (!$list) {
            return $rows;
        }

        if ($keyAsSourceId) {
            foreach ($rows as $row) {
                    $mappings[$row['type']][$row['source_id']] = $row['local_id'];
            }
        } else {
            foreach ($rows as $row) {
                $mappings[$row['type']][$row['id_mapping']] = array(
                    'id_mapping'  => $row['id_mapping'],
                    'source_id'   => $row['source_id'],
                    'source_name' => $row['source_name'],
                    'local_id'    => $row['local_id']
                );
            }
        }

        return $mappings;
    }
}
