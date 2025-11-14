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

class LeoElementsTemplate extends ObjectModel
{
    public $id_leoelements_template;
    public $id_employee;
    public $title;
    public $type;
    public $content;
	public $page_settings;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements_template',
        'primary' => 'id_leoelements_template',
        'fields' => array(
            'id_employee' 		=> 	array('type' => self::TYPE_INT, 	'validate' => 'isUnsignedId'),
            'title' 			=>  array('type' => self::TYPE_STRING, 	'required' => true),
			'type' 				=>  array('type' => self::TYPE_STRING),
            'content' 			=>  array('type' => self::TYPE_HTML, 	'validate' => 'isJson'),
            'page_settings' 	=>  array('type' => self::TYPE_HTML, 	'validate' => 'isJson'),
            'date_add' 			=> 	array('type' => self::TYPE_DATE,	'validate' => 'isDate'),
        ),
    );
}
