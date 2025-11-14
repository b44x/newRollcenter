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

if (!defined('_PS_VERSION_')) {
    exit;
}

class LeoElementsAjaxModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function postProcess()
    {
        parent::initContent();
        if (Tools::getValue('leoajax') == 1) {
	        $module = $this->module;
		    # process category
		    $list_cat = Tools::getValue('cat_list');
		    $product_list_image = Tools::getValue('product_list_image');
		    $product_one_img = Tools::getValue('product_one_img');
		    $product_attribute_one_img = Tools::getValue('product_attribute_one_img');
		    $product_all_one_img = Tools::getValue('product_all_one_img');
		    $leo_pro_cdown = Tools::getValue('pro_cdown');

		//    $ap_extra = Tools::getValue('product_apextra');
		    //add function wishlist compare
		    $wishlist_compare = Tools::getValue('wishlist_compare');

		    $result = array();
		    // get number product of compare + wishlist
		    if ($wishlist_compare) {
		        $wishlist_products = 0;
		        if (Configuration::get('LEOFEATURE_ENABLE_PRODUCTWISHLIST') && isset(Context::getContext()->cookie->id_customer)) {
		            $current_user = (int)Context::getContext()->cookie->id_customer;
		            $list_wishlist = Db::getInstance()->executeS("SELECT id_wishlist FROM `"._DB_PREFIX_."leofeature_wishlist` WHERE id_customer = '" . (int)$current_user."'");
		            foreach ($list_wishlist as $list_wishlist_item) {
		                $number_product_wishlist = Db::getInstance()->getValue("SELECT COUNT(id_wishlist_product) FROM `"._DB_PREFIX_."leofeature_wishlist_product` WHERE id_wishlist = ".(int)$list_wishlist_item['id_wishlist']);
		                $wishlist_products += $number_product_wishlist;
		            }
		        }

		        $compared_products = array();
		        if (Configuration::get('LEOFEATURE_ENABLE_PRODUCTCOMPARE') && Configuration::get('LEOFEATURE_COMPARATOR_MAX_ITEM') > 0 && isset(Context::getContext()->cookie->id_compare)) {
		            $compared_products = Db::getInstance()->executeS('
		                SELECT DISTINCT `id_product`
		                FROM `'._DB_PREFIX_.'leofeature_compare` c
		                LEFT JOIN `'._DB_PREFIX_.'leofeature_compare_product` cp ON (cp.`id_compare` = c.`id_compare`)
		                WHERE cp.`id_compare` = '.(int)(Context::getContext()->cookie->id_compare));
		        }
		        $result['wishlist_products'] = $wishlist_products;
		        $result['compared_products'] = count($compared_products);
		    }

		    if ($list_cat) {
		        $list_cat = explode(',', $list_cat);
		        $list_cat = array_filter($list_cat);
		        $list_cat = array_unique($list_cat);
		        $list_cat = array_map('intval', $list_cat); // fix sql injection
		        $list_cat = implode(',', $list_cat);

		        $sql = 'SELECT COUNT(cp.`id_product`) AS total, cp.`id_category` FROM `'._DB_PREFIX_.'product` p '.Shop::addSqlAssociation('product', 'p').'
		                LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
		                WHERE cp.`id_category` IN ('.pSQL($list_cat).')
		                AND product_shop.`visibility` IN ("both", "catalog")
		                AND product_shop.`active` = 1
		                GROUP BY cp.`id_category`';
		        $cat = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		        if ($cat) {
		            $result['cat'] = $cat;
		        }
		    }

		    if ($leo_pro_cdown) {
		        $leo_pro_cdown = explode(',', $leo_pro_cdown);
		        $leo_pro_cdown = array_unique($leo_pro_cdown);
		        $leo_pro_cdown = array_map('intval', $leo_pro_cdown); // fix sql injection
		        $leo_pro_cdown = implode(',', $leo_pro_cdown);
		        $result['pro_cdown'] = $module->hookProductCdown($leo_pro_cdown);
		    }

		    if ($product_list_image) {
		        $product_list_image = explode(',', $product_list_image);
		        $product_list_image = array_unique($product_list_image);
		        $product_list_image = array_map('intval', $product_list_image); // fix sql injection
		        $product_list_image = implode(',', $product_list_image);

		        # $leocustomajax = new Leocustomajax();
		        $result['product_list_image'] = $module->hookProductMoreImg($product_list_image);
		    }
		    
		    
		    if ($product_one_img) {
		        $product_one_img = explode(',', $product_one_img);
		        $product_one_img = array_unique($product_one_img);
		        $product_one_img = array_map('intval', $product_one_img); // fix sql injection
		        $product_one_img = implode(',', $product_one_img);
		        
		        $result['product_one_img'] = $module->hookProductOneImg($product_one_img);
		    }
		    if ($product_attribute_one_img) {
		        $result['product_attribute_one_img'] = $module->hookProductAttributeOneImg($product_attribute_one_img);
		    }
		    if ($product_all_one_img) {
		    	// Get second image of product except image_showed.
		        $product_all_one_img = explode(',', $product_all_one_img);
		        $product_all_one_img = array_unique($product_all_one_img);
		        $product_all_one_img = array_map('intval', $product_all_one_img); // fix sql injection
		        $product_all_one_img = implode(',', $product_all_one_img);

		        $result['product_all_one_img'] = $module->hookProductAllOneImg($product_all_one_img);
		    }
		    if (Tools::getIsset('product_size') || Tools::getIsset('product_attribute')) {
		        $product_size = $product_attribute = array();
		        if (Tools::getIsset('product_size') && Tools::getValue('product_size')) {
		            $product_size = explode(',', Tools::getValue('product_size'));
		        }
		        if (Tools::getIsset('product_attribute') && Tools::getValue('product_attribute')) {
		            $product_attribute = explode(',', Tools::getValue('product_attribute'));
		        }

		        $result['product_attribute'] = $module->hookGetProductAttribute($product_attribute, $product_size);
		    }
		    if (Tools::getIsset('product_manufacture')) {
		        $result['product_manufacture'] = $module->hookGetProductManufacture(Tools::getValue('product_manufacture'));
		    }
		    if ($result) {
		        die(json_encode($result));
		    }
		}
        die('LeoElementsAjaxModuleFrontController');
        if (Tools::getValue('type') == 'product') {
            $this->_renderProducts();
        }elseif (Tools::getValue('type') == 'blog') {
            $this->_renderBlogs();
        }
    }

    public function _renderProducts()
    {
		header('Content-Type: application/json');
		$options = Tools::getValue('options');
		
		if($options['source'] != 's'){
			$data = $this->module->_prepProducts($options);		
		}else{
			$data = $this->module->_prepProductsSelected($options);	
		}
		
		$content = array_merge($options, $data);
		
       	$this->context->smarty->assign(array('content' => $content));
		
		$template = LEOELEMENTS_PATH . 'views/templates/widgets/products.tpl';
								
		if (!$template){
			$template = $this->module->l('No template found', 'ajax');
		}

        $this->ajaxDie(json_encode(array(
			'lastPage' => $content['lastPage'],
            'html' => $this->context->smarty->fetch($template)
        )));	
		
	}
	
    public function _renderBlogs()
    {
		header('Content-Type: application/json');
		$options = Tools::getValue('options');

		$data = $this->module->_prepBlogs($options);		
		
		$content = array_merge($options, $data);
		
       	$this->context->smarty->assign(array('content' => $content));
		
		$template = LEOELEMENTS_PATH . 'views/templates/widgets/blogs.tpl';
								
		if (!$template){
			$template = $this->module->l('No template found', 'ajax');
		}

        $this->ajaxDie(json_encode(array(
            'html' => $this->context->smarty->fetch($template)
        )));	
		
	}
			
}
