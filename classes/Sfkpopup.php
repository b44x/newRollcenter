<?php
/**
* This module helps administrator to add popup ads on page of products,offers,promotions from back-office and showcase in front-office.
*
* NOTICE OF LICENSE
* 
* Each copy of the software must be used for only one production website, it may be used on additional
* test servers. You are not permitted to make copies of the software without first purchasing the
* appropriate additional licenses. This license does not grant any reseller privileges.
* 
* @author    Shahab
* @copyright 2007-2024 Shahab-FK Enterprises
* @license   Prestashop Commercial Module License
*/

if (!defined('_PS_VERSION_')) { exit; }

class SfkpopupCore extends ObjectModel
{
    public $sfk_title;
    public $sfk_status;
    public $sfk_image_name;
    public $sfk_url;
    public $sfk_height;
    public $sfk_width;
    public $sfk_dates;

    /**
    * @see ObjectModel::$definition
    */
    public static $definition = array('table'=>'sfkpopup','primary' =>'id_sfkpopup','multilang'=>false,'fields'=>array(
    'sfk_title' =>array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isName', 'required' => true, 'size' => 500),
    'sfk_url' =>array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isAbsoluteUrl', 'required' => true, 'size' => 500),
    'sfk_height' =>array('type' => self::TYPE_INT, 'lang' => false, 'validate' => 'isInt', 'required' => true, 'size' => 500),
    'sfk_width' =>array('type' => self::TYPE_INT, 'lang' => false, 'validate' => 'isInt', 'required' => true, 'size' => 500),
    'sfk_status' =>array('type' => self::TYPE_BOOL, 'lang' => false, 'validate' => 'isString', 'required' => true),
    'sfk_dates' =>array('type' => self::TYPE_DATE, 'lang' => false, 'validate' => 'isDateFormat', 'copy_post' => false)));

    public static function getSfkpopup($id_lang = null)
    {
        if (is_null($id_lang))
                $id_lang = Context::getContext()->language->id;
        $sfkpopup = new Collection('Sfkpopup', $id_lang);
        return $sfkpopup;
    }
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->image_dir = _PS_STORE_IMG_DIR_;
    }
    public function getImage()
    {
        if (!isset($this->id) || empty($this->id) || !file_exists(_PS_STORE_IMG_DIR_.$this->id.'.jpg'))
                return _PS_STORE_IMG_DIR_.'Unknown.jpg';
        return _PS_STORE_IMG_DIR_.$this->id.'.jpg';
    }
}
