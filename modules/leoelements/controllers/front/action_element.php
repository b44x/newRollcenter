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

use LeoElements\Leo_Helper;
use LeoElements\Plugin;

require_once(_PS_MODULE_DIR_.'leoelements/src/Leo_Helper.php');
require_once(_PS_MODULE_DIR_.'leoelements/includes/plugin.php');

class LeoElementsAction_ElementModuleFrontController extends ModuleFrontController {
	
    public function init() {
        parent::init();
		
        Leo_Helper::add_action( 'wp_ajax_leo_get_products_title_by_id', [ $this, 'leo_get_products_title_by_id' ] );
        Leo_Helper::add_action( 'wp_ajax_leo_get_products_by_query', [ $this, 'leo_get_products_by_query' ] );
        
        Leo_Helper::add_action( 'wp_ajax_leo_get_manu_title_by_id', [ $this, 'leo_get_manu_title_by_id' ] );
        Leo_Helper::add_action( 'wp_ajax_leo_get_manu_by_query', [ $this, 'leo_get_manu_by_query' ] );
    }

    public function postProcess() {
        parent::initContent();
        $post = $GLOBALS['_POST'];
				
        define( 'DOING_AJAX', true );
        
        $GLOBALS['gb_leoelements'] = json_decode( Configuration::get('GBLEOELEMENTS', ''), true);

        if( Leo_Helper::set_global_var() ){
            
                Plugin::instance()->on_rest_api_init();

                if ( isset( $post['action'] ) ) {
                        $action = $post['action'];

                        Leo_Helper::do_action( 'wp_ajax_' . $action );
                } elseif ( Tools::getIsset('action') ) {
                        $action = Tools::getValue('action');

                        Leo_Helper::do_action( 'wp_ajax_' . $action );
                }
        }
				
        die( 'exit' );
    }
	
    public function leo_get_products_title_by_id()
    {
        header('Content-Type: application/json');
		
        $product_ids = Tools::getValue( 'ids' );

        if ( !$product_ids ) {
			die();
        }
		
        $id_lang = (int) Leo_Helper::$id_lang;
        $id_shop = (int) Leo_Helper::$id_shop;

        $sql = 'SELECT p.`id_product`, product_shop.`id_product`,
				    pl.`name`, pl.`link_rewrite`,
					image_shop.`id_image` id_image
				FROM  `' . _DB_PREFIX_ . 'product` p 
				' . Shop::addSqlAssociation('product', 'p') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				)
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $id_shop . ')
	  
				WHERE p.id_product IN (' . $product_ids . ')' . '
				ORDER BY FIELD(product_shop.id_product, ' . $product_ids . ')';

        if ( !$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( $sql ) ) {
            return;
        }
		
		$results   = [];
		
		foreach ( $items as $item ) {
			$results[ (int)$item['id_product'] ] = '(Id: ' . (int)( $item['id_product'] ) . ') ' . $item['name'];
		}

        die( json_encode( $results ) );
    }

    public function leo_get_products_by_query() {
        header('Content-Type: application/json');
		
        $query = Tools::getValue( 'q', false );
		
        if ( !$query or $query == '' or Tools::strlen( $query ) < 1 ) {
            die();
        }
		
        if ( $pos = strpos( $query, ' (ref:' ) ) {
            $query = Tools::substr( $query, 0, $pos );
        }
		
        $excludeIds = Tools::getValue( 'excludeIds', false );
		
        if ( $excludeIds && $excludeIds != 'NaN' ) {
            $excludeIds = implode( ',', array_map( 'intval', explode( ',', $excludeIds ) ) );
        } else {
            $excludeIds = '';
        }
		
        $excludeVirtuals = false;
		
        $exclude_packs = false;
		
        $context = \Context::getContext();
        $id_lang = (int) $context->language->id;
        $id_shop = (int) Leo_Helper::$id_shop;
		
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, p.`cache_default_attribute`
        FROM `' . _DB_PREFIX_ . 'product` p
        ' . Shop::addSqlAssociation('product', 'p') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . ')
        WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\') AND p.`active` = 1' .
            ( !empty( $excludeIds ) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ' ) .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);

        if ( $items ) {
            $results = [];
			
            foreach ( $items as $item ) {
                $product = [
                    'id' => (int)($item['id_product']),
                    'text' => '(Id: ' . (int)( $item['id_product'] ) . ') ' . $item['name'],
                ];
                array_push( $results, $product );
            }
			
            $results = array_values( $results );
			
            die( json_encode( $results ) );
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function leo_get_manu_by_query()
    {
        header('Content-Type: application/json');
		
        $query = Tools::getValue( 'q', false );
		
        if ( !$query or $query == '' or Tools::strlen( $query ) < 1 ) {
            die();
        }
		
        if ( $pos = strpos( $query, ' (ref:' ) ) {
            $query = Tools::substr( $query, 0, $pos );
        }
		
        $excludeIds = Tools::getValue( 'excludeIds', false );
		
        if ( $excludeIds && $excludeIds != 'NaN' ) {
            $excludeIds = implode( ',', array_map( 'intval', explode( ',', $excludeIds ) ) );
        } else {
            $excludeIds = '';
        }
		
//        $excludeVirtuals = false;
		
//        $exclude_packs = false;
		
//        $context = \Context::getContext();
//        $id_lang = (int) $context->language->id;
//        $id_shop = (int) Leo_Helper::$id_shop;
        
        $withProduct = false;
        $group_by = false;
        $getNbProducts = false;
        $idLang = 0;
        $active = false;
        $p = false;
        $n = false;
        $allGroup = false;
        $group_by = false;
        $withProduct = false;
        
        if (!$idLang) {
            $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        if (!Group::isFeatureActive()) {
            $allGroup = true;
        }

        $sql = '
		SELECT m.*, ml.`description`, ml.`short_description`
		FROM `' . _DB_PREFIX_ . 'manufacturer` m'
        . Shop::addSqlAssociation('manufacturer', 'm') .
        'INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = ' . (int) $idLang . ')' .
        'WHERE 1 AND (m.name LIKE \'%' . pSQL($query) . '%\') ' .
        ($active ? 'AND m.`active` = 1 ' : '') .
//        ($withProduct ? 'AND m.`id_manufacturer` IN (SELECT `id_manufacturer` FROM `' . _DB_PREFIX_ . 'product`) ' : '') .
        ($group_by ? ' GROUP BY m.`id_manufacturer`' : '') .
        'ORDER BY m.`name` ASC
		' . ($p ? ' LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n : '');
        

        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
        
//WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\') AND p.`active` = 1' .
//            ( !empty( $excludeIds ) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ' ) .
//            ' GROUP BY p.id_product';


//        $items = Db::getInstance()->executeS($sql);

        if ( $items ) {
            $results = [];
			
            foreach ( $items as $item ) {
                $product = [
                    'id' => (int)($item['id_manufacturer']),
                    'text' => '(Id: ' . (int)( $item['id_manufacturer'] ) . ') ' . $item['name'],
                ];
                array_push( $results, $product );
            }
			
            $results = array_values( $results );
			
            die( json_encode( $results ) );
        }
    }

    public function leo_get_manu_title_by_id()
    {
        header('Content-Type: application/json');
		
        $manu_ids = Tools::getValue( 'ids' );

        if ( !$manu_ids ) {
            die();
        }
		
        $id_lang = (int) Leo_Helper::$id_lang;
        $id_shop = (int) Leo_Helper::$id_shop;

        
        $withProduct = false;
        $group_by = false;
        $getNbProducts = false;
        $idLang = 0;
        $active = false;
        $p = false;
        $n = false;
        $allGroup = false;
        $group_by = false;
        $withProduct = false;
        
        if (!$idLang) {
            $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        if (!Group::isFeatureActive()) {
            $allGroup = true;
        }

        $sql = '
		SELECT m.*, ml.`description`, ml.`short_description`
		FROM `' . _DB_PREFIX_ . 'manufacturer` m'
        . Shop::addSqlAssociation('manufacturer', 'm') .
        'INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = ' . (int) $idLang . ')' .
        'WHERE 1 ' .
        ($active ? 'AND m.`active` = 1 ' : '') .
        (isset($params['ids']) && $params['ids'] ? 'AND m.`id_manufacturer` IN ('.pSQL(implode(",",$params['ids'])).')' : '') .
//        ($withProduct ? 'AND m.`id_manufacturer` IN (SELECT `id_manufacturer` FROM `' . _DB_PREFIX_ . 'product`) ' : '') .
        ($group_by ? ' GROUP BY m.`id_manufacturer`' : '') .
        'ORDER BY m.`name` ASC
		' . ($p ? ' LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n : '');

        if ( !$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( $sql ) ) {
            return;
        }
		
		$results   = [];
		
		foreach ( $items as $item ) {
			$results[ (int)$item['id_product'] ] = '(Id: ' . (int)( $item['id_manufacturer'] ) . ') ' . $item['name'];
		}

        die( json_encode( $results ) );
    }
		
}
