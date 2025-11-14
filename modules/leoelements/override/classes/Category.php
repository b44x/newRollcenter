<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2019 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */
class Category extends CategoryCore {
	public $leoe_layout;
	public $leoe_layout_mobile;
	public $leoe_layout_tablet;
	public $leoe_extra_1;
	public $leoe_extra_2;

	 
	public function __construct($idCategory = null, $idLang = null, $idShop = null){
	 		
	 		self::$definition['fields']['leoe_layout'] = [
	            'type' => self::TYPE_HTML,
	            'required' => false,
	            'shop' => true,
	            'validate' => 'isCleanHtml'
	        ];

	        self::$definition['fields']['leoe_layout_mobile'] = [
	            'type' => self::TYPE_HTML,
	            'required' => false,
	            'shop' => true,
	            'validate' => 'isCleanHtml'
	        ];

	        self::$definition['fields']['leoe_layout_tablet'] = [
	            'type' => self::TYPE_HTML,
	            'required' => false,
	            'shop' => true,
	            'validate' => 'isCleanHtml'
	        ];
	        
	        self::$definition['fields']['leoe_extra_1'] = [
	            'type' => self::TYPE_HTML,
	            'lang' => true,
	            'required' => false,
	            'validate' => 'isCleanHtml'
	        ];

	        self::$definition['fields']['leoe_extra_2'] = [
	            'type' => self::TYPE_HTML,
	            'lang' => true,
	            'required' => false,
	            'validate' => 'isCleanHtml'
	        ];

	        parent::__construct($idCategory, $idLang, $idShop);
	}
}