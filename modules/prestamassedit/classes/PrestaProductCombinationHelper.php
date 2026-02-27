<?php
/**
 * 2008-2025 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2025 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaProductCombinationHelper
{
    public static function createCombinationsForProduct($idProduct, $attributeIds)
    {
        $attributes = self::getAttributeDetails($attributeIds);
        $combinations = self::crossMultiplyAttributes($attributes);

        foreach ($combinations as $combination) {
            $existingCombinationId = self::checkIfCombinationExists($idProduct, $combination);
            if (!$existingCombinationId) {
                $combinationId = self::insertProductAttribute($idProduct);
                self::insertProductAttributeShop($combinationId, $idProduct);
                self::insertStockAvailable($idProduct, $combinationId);

                foreach ($combination as $group => $value) {
                    $attributeId = self::getAttributeIdByName($group, $value);
                    self::insertProductAttributeCombination($combinationId, $attributeId);
                }
            }
        }
    }

    public static function removeCombinationsForProduct($idProduct, $attributeIds)
    {
        $attributes = self::getAttributeDetails($attributeIds);
        // Generate all possible combinations
        $combinations = self::crossMultiplyAttributes($attributes);

        foreach ($combinations as $combination) {
            $existingCombinationId = self::checkIfCombinationExists($idProduct, $combination);

            if ($existingCombinationId) {
                // Remove from all associated tables
                self::deleteProductAttributeCombination($existingCombinationId);
                self::deleteProductAttributeShop($existingCombinationId);
                self::deleteStockAvailable($idProduct, $existingCombinationId);
                self::updateTotalQuantity($idProduct);
                self::deleteProductAttribute($existingCombinationId);
            }
        }
    }

    public static function replaceAllCombinationsForProduct($idProduct, $attributeIds)
    {
        // Remove all existing combinations and their associations
        self::removeAllCombinationsForProduct($idProduct);
        // Create new combinations
        self::createCombinationsForProduct($idProduct, $attributeIds);
    }

    public static function removeAllCombinationsForProduct($idProduct)
    {
        $existingCombinationIds = self::getExistingCombinationIds($idProduct);

        foreach ($existingCombinationIds as $combinationId) {
            // Remove from all associated tables
            self::deleteProductAttributeCombination($combinationId);
            self::deleteProductAttributeShop($combinationId);
            self::deleteStockAvailable($idProduct, $combinationId);
            self::updateTotalQuantity($idProduct);
            self::deleteProductAttribute($combinationId);
        }
    }

    public static function updateProductQuantity($idProduct, $amount, $option)
    {
        $combinationIds = self::getExistingCombinationIds($idProduct);

        if (empty($combinationIds)) {
            // No combinations, update the quantity of the product itself
            self::updateStockAvailable($idProduct, 0, $amount, $option);
        } else {
            // Update the quantity of each combination
            foreach ($combinationIds as $combinationId) {
                self::updateStockAvailable($idProduct, $combinationId, $amount, $option);
            }
        }
    }

    public static function updateProductAttribute($idProduct, $value, $option, $column)
    {
        $combinationIds = self::getExistingCombinationIds($idProduct);

        if (empty($combinationIds)) {
            self::updateColumn($idProduct, 0, $value, $option, $column);
        } else {
            foreach ($combinationIds as $combinationId) {
                self::updateColumn($idProduct, $combinationId, $value, $option, $column);
            }
        }
    }

    public static function getAttributeDetails($attributeIds)
    {
        $idLang = Context::getContext()->language->id;

        $sql = 'SELECT agl.name AS group_name, al.name AS attribute_name
                FROM ' . _DB_PREFIX_ . 'attribute a
                JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON a.id_attribute = al.id_attribute
                JOIN ' . _DB_PREFIX_ . 'attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
                WHERE a.id_attribute IN (' . implode(',', array_map('intval', $attributeIds)) . ')
                AND al.id_lang = ' . (int) $idLang . '
                AND agl.id_lang = ' . (int) $idLang;

        $results = Db::getInstance()->executeS($sql);

        $attributes = [];
        foreach ($results as $result) {
            $attributes[$result['group_name']][] = $result['attribute_name'];
        }

        return $attributes;
    }

    public static function crossMultiplyAttributes($attributes)
    {
        $keys = array_keys($attributes);
        $combinations = [[]];

        foreach ($keys as $key) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($attributes[$key] as $attribute) {
                    $newCombinations[] = array_merge($combination, [$key => $attribute]);
                }
            }
            $combinations = $newCombinations;
        }
        return $combinations;
    }

    public static function updateLowStockAlert($idProduct, $idProductAttribute, $value, $column)
    {
        $queries = [];
        if ($idProductAttribute == 0) {
            // Update the column for the product itself
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product`
                        SET `' . pSQL($column) . '` = ' . (int) $value . '
                        WHERE id_product = ' . (int) $idProduct;
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product_shop`
                        SET `' . pSQL($column) . '` = ' . (int) $value . '
                        WHERE id_product = ' . (int) $idProduct;
        } else {
            // Update the column for the product attribute (combination)
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute`
                        SET `' . pSQL($column) . '` = ' . (int) $value . '
                        WHERE id_product = ' . (int) $idProduct . '
                        AND id_product_attribute = ' . (int) $idProductAttribute;
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute_shop`
                        SET `' . pSQL($column) . '` = ' . (int) $value . '
                        WHERE id_product = ' . (int) $idProduct . '
                        AND id_product_attribute = ' . (int) $idProductAttribute;
        }

        foreach ($queries as $sql) {
            Db::getInstance()->execute($sql);
        }
    }

    public static function updateStockLabel($idProduct, $column, $idLang, $newValue)
    {
        $combinationIds = self::getExistingCombinationIds($idProduct);

        if (empty($combinationIds)) {
            self::updateStockColumn($idProduct, 0, $idLang, $newValue, $column);
        } else {
            foreach ($combinationIds as $combinationId) {
                PrestaProductCombinationHelper::updateStockColumn(
                    $idProduct,
                    $combinationId,
                    $idLang,
                    $newValue,
                    $column
                );
            }
        }
    }

    public static function updateStockColumn($idProduct, $idProductAttribute, $idLang, $newValue, $column)
    {
        $sql = [];
        if ($idProductAttribute == 0) {
            // Update the column for the product itself
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
                    SET `' . pSQL($column) . '` = ' . $newValue . '
                    WHERE `id_product` = ' . (int) $idProduct . '
                    AND `id_lang` = ' . (int) $idLang;
        } else {
            // Update the column for the product attribute (combination)
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute_lang`
                    SET `' . pSQL($column) . '` = ' . $newValue . '
                    WHERE `id_product_attribute` = ' . (int) $idProductAttribute . '
                    AND `id_lang` = ' . (int) $idLang;
        }

        Db::getInstance()->execute($sql);
    }

    public static function getExistingCombinationIds($idProduct)
    {
        $sql = 'SELECT DISTINCT pa.`id_product_attribute`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                WHERE pa.`id_product` = ' . (int) $idProduct;

        $results = Db::getInstance()->executeS($sql);
        return array_column($results, 'id_product_attribute');
    }

    private static function checkIfCombinationExists($idProduct, $combination)
    {
        $existingCombinationIds = self::getExistingCombinationIds($idProduct);

        foreach ($existingCombinationIds as $combinationId) {
            $attributes = self::getCombinationAttributes($combinationId);

            // Check if the number of attributes matches
            if (count($attributes) !== count($combination)) {
                continue;
            }
            // Check if this combination ID matches the current combination
            $matches = true;
            foreach ($combination as $group => $value) {
                if (!isset($attributes[$group]) || $attributes[$group] !== $value) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                return $combinationId;
            }
        }
        return false;
    }

    private static function getCombinationAttributes($idProductAttribute)
    {
        $idLang = Context::getContext()->language->id;

        $sql = 'SELECT agl.`name` AS group_name, al.`name` AS attribute_name
                FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                JOIN `' . _DB_PREFIX_ . 'attribute` a ON pac.`id_attribute` = a.`id_attribute`
                JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON a.`id_attribute_group` = agl.`id_attribute_group`
                JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
                WHERE pac.`id_product_attribute` = ' . (int) $idProductAttribute . '
                AND al.`id_lang` = ' . (int) $idLang . '
                AND agl.`id_lang` = ' . (int) $idLang;

        $results = Db::getInstance()->executeS($sql);

        $attributes = [];
        foreach ($results as $result) {
            $attributes[$result['group_name']] = $result['attribute_name'];
        }
        return $attributes;
    }

    private static function insertProductAttribute($idProduct)
    {
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'product_attribute`
            (`id_product`, `reference`, `supplier_reference`, `ean13`,
            `isbn`, `upc`, `mpn`, `low_stock_threshold`, `available_date`)
            VALUES (' . (int) $idProduct . ', \'\', \'\', \'\', \'\', \'\', \'\', 0, \'0000-00-00\')'
        );

        return Db::getInstance()->Insert_ID();
    }

    private static function insertProductAttributeShop($combinationId, $idProduct)
    {
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_shop`
            (`id_product_attribute`,`id_product`,`id_shop`, `price`, `weight`, `low_stock_threshold`, `available_date`)
            VALUES (' . (int) $combinationId . ', ' . (int) $idProduct . ', 1, 0, 0, 0, \'0000-00-00\')'
        );
    }

    private static function insertStockAvailable($idProduct, $combinationId)
    {
        $idShop = Context::getContext()->shop->id;
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'stock_available`
                (`id_product`, `id_product_attribute`, `id_shop`, `quantity`)
                VALUES (' . (int) $idProduct . ', ' . (int) $combinationId . ', ' . (int) $idShop . ', 0)';

        Db::getInstance()->execute($sql);
    }

    private static function insertProductAttributeCombination($combinationId, $attributeId)
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_combination` (`id_product_attribute`, `id_attribute`)
                VALUES (' . (int) $combinationId . ', ' . (int) $attributeId . ')';

        Db::getInstance()->execute($sql);
    }

    private static function updateStockAvailable($idProduct, $idProductAttribute, $amount, $option)
    {
        $idShop = Context::getContext()->shop->id;
        if ($option == 'replace') {
            StockAvailable::setQuantity($idProduct, $idProductAttribute, $amount, $idShop);
        } else {
            $deltaQuantity = 0;
            if ($option == 'add_amount') {
                $deltaQuantity = $deltaQuantity + $amount;
            } elseif ($option == 'sub_amount') {
                $deltaQuantity = $deltaQuantity - $amount;
            }
            StockAvailable::updateQuantity($idProduct, $idProductAttribute, $deltaQuantity, $idShop, true);
        }
    }

    private static function updateColumn($idProduct, $idProductAttribute, $value, $option, $column)
    {
        if ($idProductAttribute == 0) {
            // Get the current value for the product itself
            $sqlSelect = 'SELECT ' . pSQL($column) . ' FROM `' . _DB_PREFIX_ . 'product`
                    WHERE id_product = ' . (int) $idProduct;
        } else {
            // Get the current value for the product attribute (combination)
            $sqlSelect = 'SELECT ' . pSQL($column) . ' FROM `' . _DB_PREFIX_ . 'product_attribute`
                    WHERE id_product = ' . (int) $idProduct . '
                    AND id_product_attribute = ' . (int) $idProductAttribute;
        }

        $currentValue = (int) Db::getInstance()->getValue($sqlSelect);

        // Calculate the new value based on the option
        switch ($option) {
            case 'add_amount':
                $newValue = $currentValue + $value;
                break;
            case 'sub_amount':
                $newValue = max(0, $currentValue - $value);
                break;
            case 'replace':
                $newValue = max(0, $value);
                break;
            default:
                $newValue = $currentValue;
        }

        $queries = [];
        if ($idProductAttribute == 0) {
            // Update the column for the product itself
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product`
                        SET ' . pSQL($column) . ' = ' . (int) $newValue . '
                        WHERE id_product = ' . (int) $idProduct;
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product_shop`
                        SET ' . pSQL($column) . ' = ' . (int) $newValue . '
                        WHERE id_product = ' . (int) $idProduct;
        } else {
            // Update the column for the product attribute (combination)
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute`
                        SET ' . pSQL($column) . ' = ' . (int) $newValue . '
                        WHERE id_product = ' . (int) $idProduct . '
                        AND id_product_attribute = ' . (int) $idProductAttribute;
            $queries[] = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute_shop`
                        SET ' . pSQL($column) . ' = ' . (int) $newValue . '
                        WHERE id_product = ' . (int) $idProduct . '
                        AND id_product_attribute = ' . (int) $idProductAttribute;
        }

        foreach ($queries as $sql) {
            Db::getInstance()->execute($sql);
        }
    }

    private static function updateTotalQuantity($idProduct)
    {
        $sql = 'SELECT SUM(quantity) as total_quantity FROM ' . _DB_PREFIX_ . 'stock_available
                WHERE id_product = ' . (int) $idProduct . '
                AND id_product_attribute != 0';

        $totalQuantity = (int) Db::getInstance()->getValue($sql);

        $sql = 'UPDATE ' . _DB_PREFIX_ . 'stock_available
                SET quantity = ' . (int) $totalQuantity . '
                WHERE id_product = ' . (int) $idProduct . '
                AND id_product_attribute = 0';

        Db::getInstance()->execute($sql);
    }

    private static function getAttributeIdByName($groupName, $attributeName)
    {
        $idLang = Context::getContext()->language->id;

        $sql = 'SELECT a.id_attribute
                FROM ' . _DB_PREFIX_ . 'attribute a
                JOIN ' . _DB_PREFIX_ . 'attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
                JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON a.id_attribute = al.id_attribute
                WHERE agl.name = \'' . pSQL($groupName) . '\'
                AND al.name = \'' . pSQL($attributeName) . '\'
                AND al.id_lang = ' . (int) $idLang . '
                AND agl.id_lang = ' . (int) $idLang;

        $result = Db::getInstance()->getRow($sql);
        return $result['id_attribute'];
    }

    private static function deleteProductAttributeCombination($combinationId)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'product_attribute_combination`
                WHERE `id_product_attribute` = ' . (int) $combinationId;

        Db::getInstance()->execute($sql);
    }

    private static function deleteProductAttributeShop($combinationId)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'product_attribute_shop`
                WHERE `id_product_attribute` = ' . (int) $combinationId;

        Db::getInstance()->execute($sql);
    }

    private static function deleteStockAvailable($idProduct, $combinationId)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'stock_available`
                WHERE `id_product` = ' . (int) $idProduct . '
                AND `id_product_attribute` = ' . (int) $combinationId;

        Db::getInstance()->execute($sql);
    }

    private static function deleteProductAttribute($combinationId)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `id_product_attribute` = ' . (int) $combinationId;

        Db::getInstance()->execute($sql);
    }
}
