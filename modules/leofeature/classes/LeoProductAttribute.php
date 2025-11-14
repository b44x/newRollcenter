<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class LeoProductAttribute 
{
    public function getReqQuantity($product) {
        $quantity = (int)$this->getMinimalQuantity($product);
        if ($quantity < $product['minimal_quantity']) {
            $quantity = $product['minimal_quantity'];
        }
        return $quantity;
    }
    
    public function getMinimalQuantity($product) {
        $minimalQuantity = 1;
        if ($product['id_product_attribute']) {
            $combination = $this->getCombinationsByID($product['id_product_attribute'], $product['id']);
            if ($combination['minimal_quantity']) {
                $minimalQuantity = $combination['minimal_quantity'];
            }
        } else {
            $minimalQuantity = $product['minimal_quantity'];
        }
        return $minimalQuantity;
    }
    
    public function getCombinationsByID($combinationId, $productId) {
        $product = new Product((int)$productId);
        $findedCombination = null;
        $combinations = $product->getAttributesGroups(Context::getContext()->language->id);
        foreach ($combinations as $combination) {
            if ((int) ($combination['id_product_attribute']) === $combinationId) {
                $findedCombination = $combination;
                break;
            }
        }
        return $findedCombination;
    }
    
    public static function getProductAttributesID($productId, $attributesId) {
        if (!is_array($attributesId)) {
            return 0;
        }
    
        $productAttributeId =  Db::getInstance()->getValue('
        SELECT pac.`id_product_attribute`
        FROM `'._DB_PREFIX_.'product_attribute_combination` pac
        INNER JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
        WHERE id_product = '.(int)$productId.' AND id_attribute IN ('.implode(',', array_map('intval', $attributesId)).')
        GROUP BY id_product_attribute
        HAVING COUNT(id_product) = '.count($attributesId));
    
        if ($productAttributeId === false) {
            return false;
            $ordered = array();
            $result = Db::getInstance()->executeS('SELECT `id_attribute` FROM `'._DB_PREFIX_.'attribute` a
            INNER JOIN `'._DB_PREFIX_.'attribute_group` g ON a.`id_attribute_group` = g.`id_attribute_group`
            WHERE `id_attribute` IN ('.implode(',', array_map('intval', $attributesId)).') ORDER BY g.`position` ASC');
    
            foreach ($result as $row) {
                $ordered[] = $row['id_attribute'];
            }
    
            while ($productAttributeId === false && count($ordered) > 0) {
                array_pop($ordered);
                $productAttributeId =  Db::getInstance()->getValue('
                SELECT pac.`id_product_attribute`
                FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                INNER JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                WHERE id_product = '.(int)$productId.' AND id_attribute IN ('.implode(',', array_map('intval', $ordered)).')
                GROUP BY id_product_attribute
                HAVING COUNT(id_product) = '.count($ordered));
            }
        }
        return $productAttributeId;
    }
    
    public static function getAttributesParams($productId, $productAttributeId) {
    	$langId = (int)Context::getContext()->language->id;
		return Db::getInstance()->executeS('
			SELECT a.`id_attribute`, a.`id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute` a
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = '.(int)$langId.')
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (pac.`id_attribute` = a.`id_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
			'.Shop::addSqlAssociation('product_attribute', 'pa').'
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$langId.')
			WHERE pa.`id_product` = '.(int)$productId.'
				AND pac.`id_product_attribute` = '.(int)$productAttributeId.'
				AND agl.`id_lang` = '.(int)$langId);
    }
    
    public static function getProductCombinationsSeparatelly($idProduct, $idProductAttribute) {
        $product = new Product((int)$idProduct, true, (int)Context::getContext()->language->id, (int)Context::getContext()->shop->id);
        $attributes = self::getAttributesParams((int)$idProduct, (int)$idProductAttribute);
        $productAttributes = array();
        foreach ($attributes as $attribute) {
            $productAttributes['attributes'][$attribute['id_attribute_group']] = $attribute;
        }
        $groups = array();
        $attributesGroups = $product->getAttributesGroups(Context::getContext()->language->id);
        if (is_array($attributesGroups) && $attributesGroups) {
            foreach ($attributesGroups as $k => $row) {   
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }
                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = array(
                    'name' => $row['attribute_name'],
                    'html_color_code' => $row['attribute_color'],
                    'texture' => (@filemtime(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')) ? _THEME_COL_DIR_.$row['id_attribute'].'.jpg' : '',
                    'selected' => (isset($productAttributes['attributes'][$row['id_attribute_group']]['id_attribute']) && $productAttributes['attributes'][$row['id_attribute_group']]['id_attribute'] == $row['id_attribute']) ? true : false,
                );
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];
            }
            $currentSelectedAttributes = array();
            $count = 0;
            foreach ($groups as &$group) {
                $count++;
                if ($count > 1) {
                	$attributesId = Db::getInstance()->executeS('SELECT `id_attribute` FROM `'._DB_PREFIX_.'product_attribute_combination` pac2
                      INNER JOIN (
                          SELECT pac.`id_product_attribute`
                              FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                              INNER JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                              WHERE id_product = '.(int)$product->id.' AND id_attribute IN ('.implode(',', array_map('intval', $currentSelectedAttributes)).')
                              GROUP BY id_product_attribute
                              HAVING COUNT(id_product) = '.count($currentSelectedAttributes).'
                      )pac on pac.`id_product_attribute` = pac2.`id_product_attribute`
                      AND id_attribute NOT IN ('.implode(',', array_map('intval', $currentSelectedAttributes)).')');
                	foreach ($attributesId as $k => $row) {
                		$attributesId[$k] = (int)$row['id_attribute'];
                	}
                	foreach ($group['attributes'] as $key => $attribute) {
                		if (!in_array((int)$key, $attributesId)) {
                			unset($group['attributes'][$key]);
                			unset($group['attributes_quantity'][$key]);
                		}
                	}
                }
                $index = 0;
                $currentSelectedAttribute = 0;
                foreach ($group['attributes'] as $key => $attribute) {
                    if ($index === 0) {
                        $currentSelectedAttribute = $key;
                    }
                    if ($attribute['selected']) {
                        $currentSelectedAttribute = $key;
                        break;
                    }
                }
                if ($currentSelectedAttribute > 0) {
                    $currentSelectedAttributes[] = $currentSelectedAttribute;
                }
            }
            if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => &$quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }
            }
        }
        return $groups;
    }
    
    public static function getProductCombinationsList($productID, $showOutOfStock, $idProductAttribute = 0) {
    	$product = new Product($productID);
    	$combinations = $product->getAttributeCombinations((int)Context::getContext()->language->id);
    	$productsVariants = array();
    	if (is_array($combinations)) {
    		foreach ($combinations as $k => $combination) {
    			if ((!$showOutOfStock && (int)$combination['quantity'] > 0) || $showOutOfStock) {
    				$productsVariants[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
    				$productsVariants[$combination['id_product_attribute']]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], $combination['id_attribute'], $combination['id_attribute_group']);
    				$productsVariants[$combination['id_product_attribute']]['price'] = $product->getPrice(true, $combination['id_product_attribute'], (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION'));
    				if ($idProductAttribute > 0) {
    					$productsVariants[$combination['id_product_attribute']]['default_on'] = ($idProductAttribute == $combination['id_product_attribute']);
    				} else {
    					$productsVariants[$combination['id_product_attribute']]['default_on'] = $combination['default_on'];
    				}
    			}
    		}
    	}
    	if (isset($productsVariants)) {
    		foreach ($productsVariants as $id_product_attribute => $product_attribute) {
    			$list = '';
    			asort($product_attribute['attributes']);
    			foreach ($product_attribute['attributes'] as $attribute) {
    				$list .= $attribute[0].' - '.$attribute[1].', ';
    			}
    			$list = rtrim($list, ', ');
    			$productsVariants[$id_product_attribute]['name'] = $list;
    		}
    	}
    	return $productsVariants;
    }
}