<?php
/**
* 2007-2022 PrestaShop
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
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_'))
    exit;

class LeoElementsFont extends ObjectModel
{
    public $name;
    public $font_family;
    public $file;
    public $type;
    public $font_style;
    public $font_weight;

    public static $definition = array(
        'table' => 'leoelements_fonts',
        'primary' => 'id_leoelements_fonts',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 50),
            'type' => array('type' => self::TYPE_INT),
            'font_family' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 50),
            'font_style' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 50),
            'font_weight' => array('type' => self::TYPE_INT),
            'file' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
        ),
    );

    public function __construct($id_leoelements_fonts = null)
    {
        parent::__construct($id_leoelements_fonts);
    }

    public function update($null_values = false, $auto_date = true)
    {
        return parent::update($null_values, $auto_date);
    }

    public static function getMaxId()
    {
        $sql = 'SELECT MAX(id_leoelements_fonts) FROM `'._DB_PREFIX_.'leoelements_fonts` WHERE 1';
        return ((int) Db::getInstance()->getValue($sql) + 1);
    }

    public static function getAllFonts($type = 'all')
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_fonts` WHERE '.($type != 'all' ? 'type='.$type : '1');
        return Db::getInstance()->executeS($sql);
    }
    
    public static function getAllFontsByName($name = '')
    {
        $type = 1;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_fonts` WHERE '.($type != 'all' ? 'type='.$type : '1') . ' AND name="' .$name.'"';
        return Db::getInstance()->getRow($sql);
    }
    
    public static function getAllFontsByFamily($name = '')
    {
        $type = 1;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_fonts` WHERE '.($type != 'all' ? 'type='.$type : '1') . ' AND font_family="' .$name.'"';
        return Db::getInstance()->getRow($sql);
    }

}
