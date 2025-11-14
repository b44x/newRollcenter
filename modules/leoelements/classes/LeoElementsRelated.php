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

class LeoElementsRelated extends ObjectModel
{
    public $id_leoelements_related;
    public $id_post;
    public $post_type;
    public $key_related;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements_related',
        'primary' => 'id_leoelements_related',
        'fields' => array(
            'id_post' 		=>  array('type' => self::TYPE_INT, 	'validate' => 'isUnsignedId'),
            'post_type' 	=>  array('type' => self::TYPE_STRING, 	'required' => true),
            'key_related' 	=>  array('type' => self::TYPE_STRING, 	'required' => true),
        ),
    );
	
    public function __construct( $id = null, $id_lang = null, $id_shop = null )
    {		
        parent::__construct( $id, $id_lang, $id_shop );
		
        Shop::addTableAssociation( 'leoelements_related', array('type' => 'shop') );
    }
}
