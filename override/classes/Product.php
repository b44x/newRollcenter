<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class Product extends ProductCore
{
	 
	 
	 
	/*
    * module: leoelements
    * date: 2025-11-17 21:25:14
    * version: 1.0.4
    */
    public $leoe_layout_mobile;
	/*
    * module: leoelements
    * date: 2025-11-17 21:25:14
    * version: 1.0.4
    */
    public $leoe_layout_tablet;
	/*
    * module: leoelements
    * date: 2025-11-17 21:25:14
    * version: 1.0.4
    */
    public $leoe_extra_1;
	/*
    * module: leoelements
    * date: 2025-11-17 21:25:14
    * version: 1.0.4
    */
    public $leoe_extra_2;
	 
	/*
    * module: leoelements
    * date: 2025-11-17 21:25:14
    * version: 1.0.4
    */
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null){
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
	        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
	}
    /*
    * module: dynamicproduct
    * date: 2026-01-18 16:56:38
    * version: 3.22.19
    */
    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
        $result = parent::getProductProperties($id_lang, $row, $context);
        
        $module = Module::getInstanceByName('dynamicproduct');
        if (Module::isEnabled('dynamicproduct') && $module->provider->isAfter1730()) {
            $id_product = (int) ($row['id_product'] ?? $row['id']);
            $dynamic_config = DynamicProduct\classes\models\DynamicConfig::getByProduct($id_product);
            if ($dynamic_config->active) {
                $displayed_price = DynamicProduct\classes\models\DynamicConfig::getDisplayedPrice($id_product);
                if ($displayed_price || $dynamic_config->display_dynamic_price) {
                    $module->calculator->assignProductPrices($row, $displayed_price, $result);
                }
            }
        }
        return $result;
    }
}
