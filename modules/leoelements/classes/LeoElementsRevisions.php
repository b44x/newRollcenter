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

class LeoElementsRevisions extends ObjectModel
{
    public $id_leoelements_revisions;
    public $id_post;
    public $id_lang;
    public $id_employee;
    public $content;
	public $page_settings;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements_revisions',
        'primary' => 'id_leoelements_revisions',
        'fields' => array(
            'id_post' 			=> 	array('type' => self::TYPE_INT, 	'validate' => 'isUnsignedId'),
            'id_lang' 			=> 	array('type' => self::TYPE_INT, 	'validate' => 'isUnsignedId'),
            'id_employee' 		=> 	array('type' => self::TYPE_INT, 	'validate' => 'isUnsignedId'),
            'content' 			=>      array('type' => self::TYPE_HTML, 	'validate' => 'isJson'),
            'page_settings'             =>      array('type' => self::TYPE_HTML, 	'validate' => 'isJson'),
            'date_add' 			=> 	array('type' => self::TYPE_DATE,	'validate' => 'isDate'),
        ),
    );
    
    public function add($autodate = true, $null_values = false)
    {
        return false;
    }
}
