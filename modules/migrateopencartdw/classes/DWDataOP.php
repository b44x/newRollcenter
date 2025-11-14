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

class DWDataOP extends ObjectModel
{
    const TYPE_TAX = 't';
    const TYPE_TAXRULESGROUP = 'trg';
    const TYPE_TAXRULE = 'tr';
    const TYPE_COUNTRY = 'co';
    const TYPE_STATE = 'st';
    const TYPE_CATEGORY = 'c';
    const TYPE_CARRIER = 'crr';
    const TYPE_PRODUCT = 'p';
    const TYPE_ATTACHMENT = 'atc';
    const TYPE_PRODUCTDOWNLOAD = 'prd';
    const TYPE_SPECIFICPRICERULE = 'spr';
    const TYPE_SPECIFICPRICERULECONDITIONGROUP = 'spg';
    const TYPE_SPECIFICPRICERULECONDITION = 'spc';
    const TYPE_ATTRIBUTEGROUP = 'ag';
    const TYPE_ATTRIBUTE = 'a';
    const TYPE_COMBINATION = 'com'; //PRODUCT_ATTRIBUTE
    const TYPE_SUPPLIER = 's';
    const TYPE_MANUFACTURER = 'm';
    const TYPE_SPECIFICPRICE = 'sp';
    const TYPE_IMAGE = 'i';
    const TYPE_FEATURE = 'f';
    const TYPE_FEATUREVALUE = 'fv';
    const TYPE_CUSTOMIZATIONFIELD = 'cf';
    const TYPE_TAG = 't';
    const TYPE_CUSTOMER = 'cus';
    const TYPE_CUSTOMERTHREAD = 'ct';
    const TYPE_CUSTOMERMESSAGE = 'cm';
    const TYPE_CART = 'car';
    const TYPE_EMPLOYEE = 'e';
    const TYPE_ADDRESS = 'adr';
    const TYPE_ORDER = 'o';
    const TYPE_ORDERDETAIL = 'od';
    const TYPE_ORDERHISTORY = 'oh';
    const TYPE_ORDERINVOICE = 'oi';
    const TYPE_ORDERCARRIER = 'oc';
    const TYPE_ORDERCARTRULE = 'ocr';
    const TYPE_ORDERPAYMENT = 'op';
    const TYPE_ORDERMESSAGE = 'om';
    const TYPE_STOCKAVAILABLE = 'sa';
    const TYPE_PRODUCTSUPPLIER = 'ps';
    const TYPE_CMS = 'cms';
    const TYPE_CMSROLE = 'cro';
    const TYPE_CMSCATEGORY = 'ctg';
    const TYPE_CMSBLOCK = 'cbl';
    const TYPE_CARTRULE = 'cr';
    const TYPE_CARTRULEPRODUCTRULEGROUP = 'cpg';
    const TYPE_CARTRULEPRODUCTRULE = 'cpr';
    const TYPE_META = 'met';
    const TYPE_WAREHOUSE = 'war';
    const TYPE_STOCK = 'stk';
    const TYPE_WAREHOUSEPRODUCTLOCATION = 'wpl';

    public $id;

    public $type;

    public $source_id;

    public $local_id;

    public static $definition = array(
        'table'   => 'dw_data',
        'primary' => 'id_data',
        'fields'  => array(
            'type'      => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'source_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'local_id'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true)
        ),
    );


    public static function exist($typeString, $sourceId)
    {
        $type = constant("self::TYPE_" . Tools::strtoupper($typeString));

        if (!is_null($type)) {
            return Db::getInstance()->getValue(
                'SELECT 1 FROM ' . _DB_PREFIX_ . 'dw_data WHERE type=\'' . pSQL($type) . '\' AND source_id=' . (int)$sourceId
            );
        }

        return false;
    }

    public static function import($typeString, $sourceID, $localID)
    {
        $type = constant("self::TYPE_" . Tools::strtoupper($typeString));
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'dw_data SET type=\'' . pSQL($type) . '\', source_id=' . (int)$sourceID . ', local_id=' . (int)$localID . ' ON DUPLICATE KEY UPDATE local_id=' . (int)$localID;

        return Db::getInstance()->execute($sql);
    }

    public static function getLocalID($typeString, $sourceID)
    {

        $type = constant("self::TYPE_" . Tools::strtoupper($typeString));


        $sql = 'SELECT local_id FROM ' . _DB_PREFIX_ . 'dw_data WHERE type=\'' . pSQL($type) . '\' AND source_id=' . (int)$sourceID . ';';

        return Db::getInstance()->getValue($sql);
    }
}
