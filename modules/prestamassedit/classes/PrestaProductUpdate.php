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

class PrestaProductUpdate
{
    public static function updateLangField($idProductList, $data, $column)
    {
        $action = $data['action'];
        if ($action !== 'off') {
            $values = $data['value'];
            foreach ($values as $idLang => $value) {
                $trimmedValue = trim($value);
                if ($trimmedValue !== '') {
                    $newValue = self::createUpdateValue($action, $column, $trimmedValue);
                    if ($newValue) {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . 'product_lang` SET `' . pSQL($column) . '` = ' . $newValue . '
                            WHERE id_product IN (' . pSQL($idProductList) . ')
                            AND `id_lang` = ' . (int) $idLang
                        );
                    }
                }
            }
        }
    }

    public static function createUpdateValue($action, $column, $value)
    {
        switch ($action) {
            case 'prepend':
                return 'CONCAT(\'' . $value . '\', \' \', `' . pSQL($column) . '`)';
            case 'append':
                return 'CONCAT(`' . pSQL($column) . '`, \' \', \'' . $value . '\')';
            case 'replace':
                return '\'' . $value . '\'';
            default:
                return null;
        }
    }

    public static function updateField($idProductList, $data, $column, $inShop = true)
    {
        $action = $data['action'];
        if ($action !== 'off') {
            $value = trim($data['value']);
            $newValue = self::createUpdateValue($action, $column, $value);
            if ($newValue) {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'product` SET `' . pSQL($column) . '` = ' . $newValue . '
                    WHERE id_product IN (' . pSQL($idProductList) . ')'
                );
                if ($inShop) {
                    Db::getInstance()->execute(
                        'UPDATE `' . _DB_PREFIX_ . 'product_shop` SET `' . pSQL($column) . '` = ' . $newValue . '
                        WHERE id_product IN (' . pSQL($idProductList) . ')'
                    );
                }
            }
        }
    }

    public static function updateAmountField($idProductList, $data, $column, $inShop = true)
    {
        $action = $data['action'];
        $value = (float) trim($data['value']);

        if ($action !== 'off' && $value !== '') {
            $idProductList = explode(',', $idProductList); // Convert CSV string to array
            $newValues = [];

            foreach ($idProductList as $idProduct) {
                $idProduct = (int) $idProduct;
                $sql = 'SELECT `' . pSQL($column) . '` FROM `' . _DB_PREFIX_ . 'product` WHERE id_product = ' . $idProduct;
                $oldPrice = (float) Db::getInstance()->getValue($sql);
                $newValue = null;

                switch ($action) {
                    case 'add_perc':
                        $newValue = $oldPrice + (($oldPrice * $value) / 100);
                        break;
                    case 'sub_perc':
                        $newValue = max(0, $oldPrice - (($oldPrice * $value) / 100));
                        break;
                    case 'add_amount':
                        $newValue = $oldPrice + $value;
                        break;
                    case 'sub_amount':
                        $newValue = max(0, $oldPrice - $value);
                        break;
                    case 'replace':
                        $newValue = max(0, $value);
                        break;
                }

                if ($newValue !== null) {
                    $newValues[$idProduct] = (float) $newValue;
                }
            }

            if (!empty($newValues)) {
                foreach ($newValues as $idProduct => $newValue) {
                    Db::getInstance()->execute(
                        'UPDATE `' . _DB_PREFIX_ . 'product` SET `' . pSQL($column) . '` = ' . $newValue . '
                        WHERE id_product = ' . (int) $idProduct
                    );

                    if ($inShop) {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . 'product_shop` SET `' . pSQL($column) . '` = ' . $newValue . '
                            WHERE id_product = ' . (int) $idProduct
                        );
                    }
                }
            }
        }
    }

    public static function updateToggleField($idProductList, $data, $column, $inShop = true)
    {
        $action = $data['action'];
        if ($action !== 'off' && ($action == '0' || $action == '1')) {
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'product` SET `' . pSQL($column) . '` = ' . (int) $action . '
                WHERE id_product IN (' . pSQL($idProductList) . ')'
            );
            if ($inShop) {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'product_shop` SET `' . pSQL($column) . '` = ' . (int) $action . '
                    WHERE id_product IN (' . pSQL($idProductList) . ')'
                );
            }
        }
    }

    public static function updateSelectBoxField($idProductList, $data, $column, $inShop = true)
    {
        $action = $data['action'];
        if ($action !== 'off') {
            $value = $data['value'];
            if (is_numeric($value) && (int) $value == $value) {
                $value = (int) $value;
            } else {
                $value = '\'' . pSQL($value) . '\'';
            }
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'product` SET `' . pSQL($column) . '` = ' . $value . '
                WHERE id_product IN (' . pSQL($idProductList) . ')'
            );
            if ($inShop) {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'product_shop` SET `' . pSQL($column) . '` = ' . $value . '
                    WHERE id_product IN (' . pSQL($idProductList) . ')'
                );
            }
        }
    }

    public static function updateDefaultCategoryField($idProductList, $data)
    {
        $action = $data['action'];
        if ($action == 'replace') {
            $value = (int) $data['value'];
            $idProductArray = array_map('intval', explode(',', $idProductList));

            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'product` SET `id_category_default` = ' . (int) $value . '
                WHERE id_product IN (' . pSQL($idProductList) . ')'
            );
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'product_shop` SET `id_category_default` = ' . (int) $value . '
                WHERE id_product IN (' . pSQL($idProductList) . ')'
            );
            $sqlValueArr = [];
            foreach ($idProductArray as $idProduct) {
                $sqlValueArr[] = '(' . (int) $value . ',' . (int) $idProduct . ')';
            }
            $sqlValueStr = implode(',', $sqlValueArr);
            if ($sqlValueStr !== '') {
                Db::getInstance()->execute(
                    'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'category_product`
                    (id_category, id_product) VALUES ' . $sqlValueStr
                );
            }
        }
    }

    public static function updateCategoriesField($idProductList, $data)
    {
        $action = $data['action'];
        $idProductArray = array_map('intval', explode(',', $idProductList));
        $idProductListEscaped = implode(',', $idProductArray);

        if (array_key_exists('value', $data)) {
            $values = array_map('intval', (array) $data['value']);
            if ($action == 'add') {
                $sqlValueArr = [];
                foreach ($values as $value) {
                    foreach ($idProductArray as $idProduct) {
                        $sqlValueArr[] = '(' . (int) $value . ',' . (int) $idProduct . ')';
                    }
                }
                if (!empty($sqlValueArr)) {
                    $sqlValueStr = implode(',', $sqlValueArr);
                    Db::getInstance()->execute(
                        'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'category_product`
                        (id_category, id_product) VALUES ' . $sqlValueStr
                    );
                }
            }

            if ($action == 'remove') {
                foreach ($values as $value) {
                    Db::getInstance()->execute(
                        'DELETE FROM `' . _DB_PREFIX_ . 'category_product`
                        WHERE `id_category` = ' . (int) $value . '
                        AND `id_product` IN (' . pSQL($idProductListEscaped) . ')'
                    );
                }
            }

            if ($action == 'replace_all') {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'category_product`
                    WHERE `id_product` IN (' . pSQL($idProductListEscaped) . ')'
                );
                $sqlValueArr = [];
                foreach ($values as $value) {
                    foreach ($idProductArray as $idProduct) {
                        $sqlValueArr[] = '(' . (int) $value . ',' . (int) $idProduct . ')';
                    }
                }
                if (!empty($sqlValueArr)) {
                    $sqlValueStr = implode(',', $sqlValueArr);
                    Db::getInstance()->execute(
                        'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'category_product`
                        (id_category, id_product) VALUES ' . pSQL($sqlValueStr)
                    );
                }
            }
        } elseif ($action == 'remove_all') {
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_product` IN (' . pSQL($idProductListEscaped) . ')'
            );
        }
    }

    public static function updateFeaturesField($idProductList, $data)
    {
        $action = $data['action'];

        if ($action !== 'off' && $action !== 'remove_all') {
            $values = $data['value'];
            $featureName = (array) $values['feature_name'];
            $featureValue = (array) $values['feature_value'];
            $featureCustomValues = array_values((array) $values['feature_custom_value']); // Re-arrange index keys

            $languages = Language::getLanguages(false);
            $productIds = explode(',', $idProductList);

            if ($action == 'add') {
                foreach ($featureName as $key => $idFeature) {
                    if (trim($featureCustomValues[$key]) !== '') {
                        $customValue = trim($featureCustomValues[$key]);
                        if (Db::getInstance()->execute(
                            'INSERT INTO `' . _DB_PREFIX_ . 'feature_value` (id_feature, custom)
                            VALUES (' . (int) $idFeature . ', 1)'
                        )) {
                            $idFeatureValue = Db::getInstance()->Insert_ID();
                            $insertFeatureValueLang = [];
                            foreach ($languages as $language) {
                                $insertFeatureValueLang[] = '(
                                    ' . (int) $idFeatureValue . ',
                                    ' . (int) $language['id_lang'] . ',
                                    \'' . pSQL($customValue) . '\'
                                )';
                            }
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'feature_value_lang` (id_feature_value, id_lang, value)
                                VALUES ' . implode(', ', $insertFeatureValueLang)
                            );
                            $insertFeatureProduct = [];
                            foreach ($productIds as $idProduct) {
                                $insertFeatureProduct[] = '(
                                    ' . (int) $idFeature . ',
                                    ' . (int) $idProduct . ',
                                    ' . (int) $idFeatureValue . '
                                )';
                            }
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'feature_product`
                                (id_feature, id_product, id_feature_value)
                                VALUES ' . implode(', ', $insertFeatureProduct)
                            );
                        }
                    } else {
                        $idFeatureValue = (int) $featureValue[$key];
                        $insertFeatureProduct = [];
                        foreach ($productIds as $idProduct) {
                            $exists = Db::getInstance()->getValue(
                                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'feature_product`
                                WHERE `id_feature` = ' . (int) $idFeature . '
                                AND `id_product` = ' . (int) $idProduct . '
                                AND `id_feature_value` = ' . (int) $idFeatureValue
                            );
                            if (!$exists) {
                                $insertFeatureProduct[] = '(
                                    ' . (int) $idFeature . ',
                                    ' . (int) $idProduct . ',
                                    ' . (int) $idFeatureValue . '
                                )';
                            }
                        }
                        if ($insertFeatureProduct) {
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'feature_product`
                                (id_feature, id_product, id_feature_value)
                                VALUES ' . implode(', ', $insertFeatureProduct)
                            );
                        }
                    }
                }
            } elseif ($action == 'remove') {
                foreach ($featureName as $key => $idFeature) {
                    if (trim($featureCustomValues[$key]) !== '') {
                        $customValue = trim($featureCustomValues[$key]);

                        // Get the id_feature_value for the custom value
                        $idFeatureValue = Db::getInstance()->getValue(
                            'SELECT fv.id_feature_value
                            FROM `' . _DB_PREFIX_ . 'feature_value` fv
                            JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl
                            ON fv.id_feature_value = fvl.id_feature_value
                            WHERE fv.id_feature = ' . (int) $idFeature . '
                            AND fvl.value = \'' . pSQL($customValue) . '\''
                        );

                        if ($idFeatureValue) {
                            // Remove the feature from the products
                            Db::getInstance()->execute(
                                'DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
                                WHERE `id_feature` = ' . (int) $idFeature . '
                                AND `id_product` IN (' . pSQL($idProductList) . ')
                                AND `id_feature_value` = ' . (int) $idFeatureValue
                            );

                            // Optionally, remove the custom feature value if it's not used by any other products
                            $count = Db::getInstance()->getValue(
                                'SELECT COUNT(*)
                                FROM `' . _DB_PREFIX_ . 'feature_product`
                                WHERE `id_feature_value` = ' . (int) $idFeatureValue
                            );

                            if ($count == 0) {
                                Db::getInstance()->execute(
                                    'DELETE FROM `' . _DB_PREFIX_ . 'feature_value`
                                    WHERE `id_feature_value` = ' . (int) $idFeatureValue
                                );
                                Db::getInstance()->execute(
                                    'DELETE FROM `' . _DB_PREFIX_ . 'feature_value_lang`
                                    WHERE `id_feature_value` = ' . (int) $idFeatureValue
                                );
                            }
                        }
                    } else {
                        $idFeatureValue = (int) $featureValue[$key];
                        // Remove the feature from the products
                        Db::getInstance()->execute(
                            'DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
                            WHERE `id_feature` = ' . (int) $idFeature . '
                            AND `id_product` IN (' . pSQL($idProductList) . ')
                            AND `id_feature_value` = ' . (int) $idFeatureValue
                        );
                    }
                }
            } elseif ($action == 'replace_all') {
                // Remove feature associations for the given products
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
                    WHERE `id_product` IN (' . pSQL($idProductList) . ')'
                );

                // Select all custom feature values to be deleted
                $customFeatureValues = Db::getInstance()->executeS(
                    'SELECT id_feature_value
                    FROM `' . _DB_PREFIX_ . 'feature_value`
                    WHERE `custom` = 1'
                );

                if (!empty($customFeatureValues)) {
                    $customFeatureValueIds = array_map(
                        'intval',
                        array_column($customFeatureValues, 'id_feature_value')
                    );
                    $customFeatureValueIds = implode(',', $customFeatureValueIds);

                    // Remove custom feature values from the feature_value_lang table
                    Db::getInstance()->execute(
                        'DELETE FROM `' . _DB_PREFIX_ . 'feature_value_lang`
                        WHERE `id_feature_value` IN (' . pSQL($customFeatureValueIds) . ')'
                    );

                    // Remove custom feature values from the feature_value table
                    Db::getInstance()->execute(
                        'DELETE FROM `' . _DB_PREFIX_ . 'feature_value`
                        WHERE `id_feature_value` IN (' . pSQL($customFeatureValueIds) . ')'
                    );
                }

                foreach ($featureName as $key => $idFeature) {
                    if (trim($featureCustomValues[$key]) !== '') {
                        $customValue = trim($featureCustomValues[$key]);
                        if (Db::getInstance()->execute(
                            'INSERT INTO `' . _DB_PREFIX_ . 'feature_value` (id_feature, custom)
                            VALUES (' . (int) $idFeature . ', 1)'
                        )) {
                            $idFeatureValue = Db::getInstance()->Insert_ID();
                            $insertFeatureValueLang = [];
                            foreach ($languages as $language) {
                                $insertFeatureValueLang[] = '(
                                    ' . (int) $idFeatureValue . ',
                                    ' . (int) $language['id_lang'] . ',
                                    \'' . pSQL($customValue) . '\'
                                )';
                            }
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'feature_value_lang` (id_feature_value, id_lang, value)
                                VALUES ' . implode(', ', $insertFeatureValueLang)
                            );
                            $insertFeatureProduct = [];
                            foreach ($productIds as $idProduct) {
                                $insertFeatureProduct[] = '(
                                    ' . (int) $idFeature . ',
                                    ' . (int) $idProduct . ',
                                    ' . (int) $idFeatureValue . '
                                )';
                            }
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'feature_product`
                                (id_feature, id_product, id_feature_value)
                                VALUES ' . implode(', ', $insertFeatureProduct)
                            );
                        }
                    } else {
                        $idFeatureValue = (int) $featureValue[$key];
                        $insertFeatureProduct = [];
                        foreach ($productIds as $idProduct) {
                            $idProduct = (int) $idProduct;
                            $exists = Db::getInstance()->getValue(
                                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'feature_product`
                                WHERE `id_feature` = ' . (int) $idFeature . '
                                AND `id_product` = ' . $idProduct . '
                                AND `id_feature_value` = ' . $idFeatureValue
                            );
                            if (!$exists) {
                                $insertFeatureProduct[] = '(
                                    ' . (int) $idFeature . ',
                                    ' . $idProduct . ',
                                    ' . $idFeatureValue . '
                                )';
                            }
                        }
                        if ($insertFeatureProduct) {
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'feature_product`
                                (id_feature, id_product, id_feature_value)
                                VALUES ' . implode(', ', $insertFeatureProduct)
                            );
                        }
                    }
                }
            }
        } elseif ($action == 'remove_all') {
            // Remove feature associations for the given products
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
                WHERE `id_product` IN (' . pSQL($idProductList) . ')'
            );

            // Select all custom feature values to be deleted
            $customFeatureValues = Db::getInstance()->executeS(
                'SELECT id_feature_value
                FROM `' . _DB_PREFIX_ . 'feature_value`
                WHERE `custom` = 1'
            );

            if (!empty($customFeatureValues)) {
                $customFeatureValueIds = array_map(
                    'intval',
                    array_column($customFeatureValues, 'id_feature_value')
                );
                $customFeatureValueIds = implode(',', $customFeatureValueIds);

                // Remove custom feature values from the feature_value_lang table
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'feature_value_lang`
                    WHERE `id_feature_value` IN (' . pSQL($customFeatureValueIds) . ')'
                );

                // Remove custom feature values from the feature_value table
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'feature_value`
                    WHERE `id_feature_value` IN (' . pSQL($customFeatureValueIds) . ')'
                );
            }
        }
    }

    public static function updateRelatedProdField($idProductList, $data)
    {
        $action = $data['action'];
        if (array_key_exists('value', $data) && $action !== 'off' && $action !== 'remove_all') {
            $value = (array) $data['value'];
            $relatedProduct = array_map('intval', $value);
            $relatedProductIds = implode(',', $relatedProduct);

            if ($action == 'add') {
                // Prepare the values for insertion using UNION ALL
                $unionValues = [];
                foreach (explode(',', $idProductList) as $id_product_1) {
                    foreach (explode(',', $relatedProductIds) as $id_product_2) {
                        $unionValues[] = 'SELECT ' . (int) $id_product_1 . ' AS id_product_1,
                            ' . (int) $id_product_2 . ' AS id_product_2';
                    }
                }
                // Combine all UNION queries with UNION ALL
                $unionQuery = implode(' UNION ALL ', $unionValues);

                // insert only if not exists
                Db::getInstance()->execute(
                    'INSERT INTO ' . _DB_PREFIX_ . 'accessory (id_product_1, id_product_2)
                    SELECT v.id_product_1, v.id_product_2
                    FROM (' . $unionQuery . ') AS v
                    LEFT JOIN ' . _DB_PREFIX_ . 'accessory a
                    ON v.id_product_1 = a.id_product_1 AND v.id_product_2 = a.id_product_2
                    WHERE a.id_product_1 IS NULL AND a.id_product_2 IS NULL'
                );
            } elseif ($action == 'remove') {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'accessory`
                    WHERE `id_product_1` IN (' . pSQL($idProductList) . ')
                    AND `id_product_2` IN (' . pSQL($relatedProductIds) . ')'
                );
            } elseif ($action == 'replace_all') {
                foreach (explode(',', $idProductList) as $idProduct) {
                    $product = new Product((int) $idProduct);
                    $product->setWsAccessories(array_map('intval', $relatedProduct));
                }
            }
        } elseif ($action == 'remove_all') {
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'accessory`
                WHERE `id_product_1` IN (' . pSQL($idProductList) . ')'
            );
        }
    }

    public static function updateSpecificPriceField($idProductList, $data)
    {
        $action = $data['action'];
        if (array_key_exists('value', $data) && $action !== 'off' && $action !== 'remove_all') {
            $defaultDateFormat = SpecificPrice::ORDER_DEFAULT_DATE;
            $defaultQty = SpecificPrice::ORDER_DEFAULT_FROM_QUANTITY;
            $context = Context::getContext();
            $idSpecificPriceRule = 0;
            $idCart = 0;
            $idShop = $context->shop->id;
            $idShopGroup = $context->shop->id_shop_group;
            $idCurrency = $data['value']['id_currency'];
            $idCountry = $data['value']['id_country'];
            $idGroup = $data['value']['id_group'];
            $idProductAttr = 0;
            $fromQuantity = (int) $data['value']['from_quantity'] > 0 ? $data['value']['from_quantity'] : $defaultQty;

            $idCustomer = array_key_exists('all_customer', $data['value']) ? 0
                : (array_key_exists('id_customer', $data['value']) ? $data['value']['id_customer'] : 0);

            $reductionValue = $data['value']['reduction'] == '1' ? (float) $data['value']['reduction_value'] : 0;
            $reductionType = $data['value']['reduction'] == '1' ? $data['value']['reduction_type'] : 'amount';
            $reductionTax = $data['value']['reduction'] == '1' ? $data['value']['reduction_tax'] : 0;
            $reduction = $reductionType == 'percentage' ? $reductionValue / 100 : $reductionValue;

            $price = $data['value']['leave_initial_price'] == '1' ? (float) -1 : $data['value']['price'];

            $from = empty($data['value']['date_from']) || !Validate::isDateFormat($data['value']['date_from'])
                ? $defaultDateFormat : $data['value']['date_from'];
            $to = empty($data['value']['date_to']) || !Validate::isDateFormat($data['value']['date_to'])
                ? $defaultDateFormat : $data['value']['date_to'];

            $idProductListArray = explode(',', $idProductList);
            $db = Db::getInstance();

            if ($action == 'add') {
                $values = [];
                foreach ($idProductListArray as $idProduct) {
                    $values[] = '(
                        ' . (int) $idSpecificPriceRule . ',
                        ' . (int) $idCart . ',
                        ' . (int) $idProduct . ',
                        ' . (int) $idShop . ',
                        ' . (int) $idShopGroup . ',
                        ' . (int) $idCurrency . ',
                        ' . (int) $idCountry . ',
                        ' . (int) $idGroup . ',
                        ' . (int) $idCustomer . ',
                        ' . (int) $idProductAttr . ',
                        ' . (float) $price . ',
                        ' . (int) $fromQuantity . ',
                        ' . (float) $reduction . ',
                        \'' . pSQL($reductionType) . '\',
                        ' . (int) $reductionTax . ',
                        \'' . pSQL($from) . '\',
                        \'' . pSQL($to) . '\'
                    )';
                }
                $query = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'specific_price`
                    (
                        `id_specific_price_rule`,
                        `id_cart`,
                        `id_product`,
                        `id_shop`,
                        `id_shop_group`,
                        `id_currency`,
                        `id_country`,
                        `id_group`,
                        `id_customer`,
                        `id_product_attribute`,
                        `price`,
                        `from_quantity`,
                        `reduction`,
                        `reduction_type`,
                        `reduction_tax`,
                        `from`,
                        `to`
                    ) VALUES ' . implode(', ', $values);
                $db->execute($query);
            } elseif ($action == 'replace') {
                foreach ($idProductListArray as $idProduct) {
                    $oldSp = self::isSpecificPriceExists(
                        $idProduct,
                        $idProductAttr,
                        $idShop,
                        $idGroup,
                        $idCountry,
                        $idCurrency,
                        $idCustomer,
                        $fromQuantity
                    );
                    if ($oldSp) {
                        $db->execute(
                            'DELETE FROM `' . _DB_PREFIX_ . 'specific_price`
                            WHERE `id_specific_price` = ' . (int) $oldSp['id_specific_price']
                        );
                    }
                    $values = '(
                        ' . (int) $idSpecificPriceRule . ',
                        ' . (int) $idCart . ',
                        ' . (int) $idProduct . ',
                        ' . (int) $idShop . ',
                        ' . (int) $idShopGroup . ',
                        ' . (int) $idCurrency . ',
                        ' . (int) $idCountry . ',
                        ' . (int) $idGroup . ',
                        ' . (int) $idCustomer . ',
                        ' . (int) $idProductAttr . ',
                        ' . (float) $price . ',
                        ' . (int) $fromQuantity . ',
                        ' . (float) $reduction . ',
                        \'' . pSQL($reductionType) . '\',
                        ' . (int) $reductionTax . ',
                        \'' . pSQL($from) . '\',
                        \'' . pSQL($to) . '\'
                    )';
                    $query = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'specific_price`
                        (
                            `id_specific_price_rule`,
                            `id_cart`, `id_product`,
                            `id_shop`,
                            `id_shop_group`,
                            `id_currency`,
                            `id_country`,
                            `id_group`,
                            `id_customer`,
                            `id_product_attribute`,
                            `price`,
                            `from_quantity`,
                            `reduction`,
                            `reduction_type`,
                            `reduction_tax`,
                            `from`,
                            `to`
                        ) VALUES ' . $values;
                    $db->execute($query);
                }
            }
        } elseif ($action == 'remove_all') {
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `id_product` IN (' . pSQL($idProductList) . ')'
            );
        }
    }

    private static function isSpecificPriceExists(
        $id_product,
        $id_product_attribute,
        $id_shop,
        $id_group,
        $id_country,
        $id_currency,
        $id_customer,
        $from_quantity
    ) {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'specific_price`
            WHERE `id_product` = ' . (int) $id_product . '
            AND id_product_attribute = ' . (int) $id_product_attribute . '
            AND id_shop = ' . (int) $id_shop . '
            AND id_group = ' . (int) $id_group . '
            AND id_country = ' . (int) $id_country . '
            AND id_currency = ' . (int) $id_currency . '
            AND id_customer = ' . (int) $id_customer . '
            AND from_quantity = ' . (int) $from_quantity;

        return Db::getInstance()->getRow($sql);
    }

    public static function updateAvailableCarriersField($idProductList, $data)
    {
        $action = $data['action'];
        $id_shop = Context::getContext()->shop->id;
        $idProductArray = array_map('intval', explode(',', $idProductList));

        if (array_key_exists('value', $data) && $action !== 'off' && $action !== 'remove_all') {
            $value = (array) $data['value'];

            if ($action == 'add') {
                foreach ($idProductArray as $idProduct) {
                    foreach ($value as $id_carrier_reference) {
                        if (!Db::getInstance()->getRow(
                            'SELECT * FROM `' . _DB_PREFIX_ . 'product_carrier`
                            WHERE id_carrier_reference = ' . (int) $id_carrier_reference . '
                            AND `id_product` = ' . (int) $idProduct . '
                            AND id_shop = ' . (int) $id_shop
                        )) {
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'product_carrier`
                                (`id_product`, `id_carrier_reference`, `id_shop`) VALUES
                                (' . (int) $idProduct . ', ' . (int) $id_carrier_reference . ', ' . (int) $id_shop . ')'
                            );
                        }
                    }
                }
            }

            if ($action == 'remove') {
                foreach ($idProductArray as $idProduct) {
                    foreach ($value as $id_carrier_reference) {
                        Db::getInstance()->execute(
                            'DELETE FROM `' . _DB_PREFIX_ . 'product_carrier`
                            WHERE id_product = ' . (int) $idProduct . '
                            AND id_carrier_reference = ' . (int) $id_carrier_reference . '
                            AND id_shop = ' . (int) $id_shop
                        );
                    }
                }
            }

            if ($action == 'replace_all') {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'product_carrier`
                    WHERE id_product IN (' . pSQL($idProductList) . ')
                    AND id_shop = ' . (int) $id_shop
                );
                foreach ($idProductArray as $idProduct) {
                    foreach ($value as $id_carrier_reference) {
                        Db::getInstance()->execute(
                            'INSERT INTO `' . _DB_PREFIX_ . 'product_carrier`
                            (`id_product`, `id_carrier_reference`, `id_shop`) VALUES
                            (' . (int) $idProduct . ', ' . (int) $id_carrier_reference . ', ' . (int) $id_shop . ')'
                        );
                    }
                }
            }
        } elseif ($action == 'remove_all') {
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'product_carrier`
                WHERE id_product IN (' . pSQL($idProductList) . ')
                AND id_shop = ' . (int) $id_shop
            );
        }
    }

    public static function updateTagsField($idProductList, $data)
    {
        $action = $data['action'];
        if ($action !== 'off' && $action !== 'remove_all') {
            $values = $data['value'];
            if ($action == 'replace_all') {
                Db::getInstance()->execute(
                    'DELETE pt FROM `' . _DB_PREFIX_ . 'product_tag` pt
                    WHERE pt.`id_product` IN (' . pSQL($idProductList) . ')'
                );
                Db::getInstance()->execute(
                    'DELETE t FROM `' . _DB_PREFIX_ . 'tag` t
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_tag` pt
                    ON t.`id_tag` = pt.`id_tag` AND t.`id_lang` = pt.`id_lang`
                    WHERE pt.`id_tag` IS NULL'
                );
            }
            if ($action == 'replace_all' || $action == 'add') {
                foreach ($values as $idLang => $value) {
                    $tags = array_map('trim', explode(',', $value));
                    foreach ($tags as $tag) {
                        if ($tag !== '') {
                            $tagId = (int) Db::getInstance()->getValue(
                                'SELECT `id_tag` FROM `' . _DB_PREFIX_ . 'tag`
                                WHERE `id_lang` = ' . (int) $idLang . '
                                AND `name` = \'' . pSQL($tag) . '\''
                            );
                            if (!$tagId) {
                                Db::getInstance()->execute(
                                    'INSERT INTO ' . _DB_PREFIX_ . 'tag (`id_lang`, `name`)
                                    VALUES (' . (int) $idLang . ', \'' . pSQL($tag) . '\')'
                                );
                                $tagId = Db::getInstance()->Insert_ID();
                            }
                            Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'product_tag` (id_product, id_tag, id_lang)
                                SELECT p.`id_product`, ' . (int) $tagId . ', ' . (int) $idLang . '
                                FROM `' . _DB_PREFIX_ . 'product` p
                                WHERE p.id_product IN (' . pSQL($idProductList) . ')
                                AND NOT EXISTS (
                                    SELECT 1 FROM `' . _DB_PREFIX_ . 'product_tag` pt
                                    WHERE pt.`id_product` = p.`id_product`
                                    AND pt.`id_tag` = ' . (int) $tagId . '
                                    AND pt.`id_lang` = ' . (int) $idLang . '
                                )'
                            );
                        }
                    }
                }
            }
            if ($action == 'remove') {
                foreach ($values as $idLang => $value) {
                    $tags = array_map('trim', explode(',', $value));
                    foreach ($tags as $tag) {
                        if ($tag !== '') {
                            $tagId = (int) Db::getInstance()->getValue(
                                'SELECT `id_tag` FROM `' . _DB_PREFIX_ . 'tag`
                                WHERE `id_lang` = ' . (int) $idLang . '
                                AND `name` = \'' . pSQL($tag) . '\''
                            );
                            if ($tagId) {
                                Db::getInstance()->execute(
                                    'DELETE FROM `' . _DB_PREFIX_ . 'product_tag`
                                    WHERE `id_tag` = ' . (int) $tagId . '
                                    AND `id_lang` = ' . (int) $idLang . '
                                    AND `id_product` IN (' . pSQL($idProductList) . ')'
                                );
                                $tagStillUsed = (int) Db::getInstance()->getValue(
                                    'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'product_tag`
                                    WHERE `id_tag` = ' . (int) $tagId
                                );
                                if ($tagStillUsed === 0) {
                                    Db::getInstance()->execute(
                                        'DELETE FROM `' . _DB_PREFIX_ . 'tag`
                                        WHERE `id_tag` = ' . (int) $tagId . '
                                        AND `id_lang` = ' . (int) $idLang
                                    );
                                }
                            }
                        }
                    }
                }
            }
        } elseif ($action == 'remove_all') {
            Db::getInstance()->execute(
                'DELETE pt FROM `' . _DB_PREFIX_ . 'product_tag` pt
                WHERE pt.`id_product` IN (' . pSQL($idProductList) . ')'
            );
            Db::getInstance()->execute(
                'DELETE t FROM `' . _DB_PREFIX_ . 'tag` t
                LEFT JOIN `' . _DB_PREFIX_ . 'product_tag` pt
                ON t.`id_tag` = pt.`id_tag` AND t.`id_lang` = pt.`id_lang`
                WHERE pt.`id_tag` IS NULL'
            );
        }
    }

    public static function updateCustomizingField($idProductList, $data)
    {
        $action = $data['action'];
        if ($action == 'remove_all' || $action == 'replace_all') {
            // Delete customization fields for all products in the list
            $cf_ids = Db::getInstance()->executeS(
                'SELECT `id_customization_field`, `id_product` FROM `' . _DB_PREFIX_ . 'customization_field`
                WHERE `id_product` IN (' . pSQL($idProductList) . ')'
            );

            if ($cf_ids) {
                $cf_id_list = array_column($cf_ids, 'id_customization_field');
                $cf_id_list_str = implode(',', array_map('intval', $cf_id_list));

                // Delete customization fields
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'customization_field`
                    WHERE `id_customization_field` IN (' . $cf_id_list_str . ')'
                );

                // Delete customization field languages
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'customization_field_lang`
                    WHERE `id_customization_field` IN (' . $cf_id_list_str . ')'
                );
            }

            // Update product customization fields
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'product`
                SET `customizable` = 0, `uploadable_files` = 0, `text_fields` = 0
                WHERE `id_product` IN (' . pSQL($idProductList) . ')'
            );
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'product_shop`
                SET `customizable` = 0, `uploadable_files` = 0, `text_fields` = 0
                WHERE `id_product` IN (' . pSQL($idProductList) . ')'
            );
        }

        if ($action == 'add' || $action == 'replace_all') {
            if (array_key_exists('label', $data)) {
                $labels = (array) $data['label'];
                $types = (array) $data['type'];
                $requireds = (array) $data['required'];

                if (count($labels) === count($types) && count($types) === count($requireds)) {
                    foreach (explode(',', $idProductList) as $idProduct) {
                        for ($key = 0; $key < count($types); $key = $key + 1) {
                            if (Db::getInstance()->execute(
                                'INSERT INTO `' . _DB_PREFIX_ . 'customization_field`
                                (id_product, type, required, is_module, is_deleted )
                                VALUES (
                                    ' . (int) $idProduct . ',
                                    ' . (int) $types[$key] . ',
                                    ' . (int) $requireds[$key] . ',
                                    0,
                                    0
                                )'
                            )) {
                                $context = Context::getContext();
                                $insertedID = Db::getInstance()->Insert_ID();
                                $id_shop = $context->shop->id;

                                $languages = Language::getLanguages(false);
                                $lang_values = [];
                                foreach ($languages as $lang) {
                                    $lang_values[] = '(
                                        ' . (int) $insertedID . ',
                                        ' . (int) $lang['id_lang'] . ',
                                        ' . (int) $id_shop . ',
                                        \'' . pSQL(trim($labels[$key])) . '\'
                                    )';
                                }

                                Db::getInstance()->execute(
                                    'INSERT INTO `' . _DB_PREFIX_ . 'customization_field_lang`
                                    (id_customization_field, id_lang, id_shop, name)
                                    VALUES ' . implode(',', $lang_values)
                                );

                                $isUploadableFiles = Db::getInstance()->getValue(
                                    'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'customization_field`
                                    WHERE `id_product` = ' . (int) $idProduct . '
                                    AND `type` = 0'
                                );
                                $isTextFields = Db::getInstance()->getValue(
                                    'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'customization_field`
                                    WHERE `id_product` = ' . (int) $idProduct . '
                                    AND `type` = 1'
                                );

                                Db::getInstance()->execute(
                                    'UPDATE `' . _DB_PREFIX_ . 'product`
                                    SET `customizable` = 1,
                                        `uploadable_files` = ' . (int) $isUploadableFiles . ',
                                        `text_fields` = ' . (int) $isTextFields . '
                                    WHERE `id_product` = ' . (int) $idProduct
                                );
                                Db::getInstance()->execute(
                                    'UPDATE `' . _DB_PREFIX_ . 'product_shop`
                                    SET `customizable` = 1,
                                        `uploadable_files` = ' . (int) $isUploadableFiles . ',
                                        `text_fields` = ' . (int) $isTextFields . '
                                    WHERE `id_product` = ' . (int) $idProduct
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    public static function updateCombinationField($idProductList, $data)
    {
        $action = $data['action'];
        $idProductListFiltered = self::filterCombinations($idProductList);
        $idProducts = explode(',', $idProductListFiltered);

        if ($action !== 'off' && $action !== 'remove_all') {
            $values = (array) $data['value'];
            if ($action == 'add') {
                foreach ($idProducts as $idProduct) {
                    PrestaProductCombinationHelper::createCombinationsForProduct($idProduct, $values);
                }
            } elseif ($action == 'remove') {
                foreach ($idProducts as $idProduct) {
                    PrestaProductCombinationHelper::removeCombinationsForProduct($idProduct, $values);
                }
            } elseif ($action !== 'replace_all') {
                foreach ($idProducts as $idProduct) {
                    PrestaProductCombinationHelper::replaceAllCombinationsForProduct($idProduct, $values);
                }
            }
        } elseif ($action == 'remove_all') {
            foreach ($idProducts as $idProduct) {
                PrestaProductCombinationHelper::removeAllCombinationsForProduct($idProduct);
            }
        }
    }

    public static function filterCombinations($idProductList)
    {
        // Convert the comma-separated string to an array
        $idProducts = explode(',', $idProductList);

        // Prepare the query to check for combinations or a specific product type
        $sql = 'SELECT DISTINCT p.`id_product` FROM `' . _DB_PREFIX_ . 'product` p
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON p.`id_product` = pa.`id_product`
                WHERE p.`id_product` IN (' . implode(',', array_map('intval', $idProducts)) . ')
                AND (pa.`id_product` IS NOT NULL OR p.`product_type` = "combinations")';

        $result = Db::getInstance()->executeS($sql);

        // Extract the product IDs that are combinations or specific product types
        $combinationProductIds = array_column($result, 'id_product');

        // Return the list of combination product IDs as a comma-separated string
        return implode(',', $combinationProductIds);
    }

    public static function updateQuantityField($idProductList, $data, $column)
    {
        $action = $data['action'];
        $idProducts = explode(',', $idProductList);
        if ($action !== 'off') {
            $amount = (int) $data['value'];
            foreach ($idProducts as $idProduct) {
                if ($column == 'quantity') {
                    PrestaProductCombinationHelper::updateProductQuantity($idProduct, $amount, $action);
                } elseif ($column == 'minimal_quantity') {
                    PrestaProductCombinationHelper::updateProductAttribute($idProduct, $amount, $action, $column);
                } elseif ($column == 'low_stock_threshold') {
                    PrestaProductCombinationHelper::updateProductAttribute($idProduct, $amount, $action, $column);
                }
            }
        }
    }

    public static function updateLowStockAlertField($idProductList, $data)
    {
        $action = $data['action'];
        if ($action !== 'off') {
            $value = (int) $data['action'];
            $idProducts = explode(',', $idProductList);

            foreach ($idProducts as $idProduct) {
                $combinationIds = PrestaProductCombinationHelper::getExistingCombinationIds($idProduct);

                if (empty($combinationIds)) {
                    PrestaProductCombinationHelper::updateLowStockAlert($idProduct, 0, $value, 'low_stock_alert');
                } else {
                    foreach ($combinationIds as $combinationId) {
                        PrestaProductCombinationHelper::updateLowStockAlert(
                            $idProduct,
                            $combinationId,
                            $value,
                            'low_stock_alert'
                        );
                    }
                }
            }
        }
    }

    public static function updateAvailabilityPreferenceField($idProductList, $data)
    {
        $action = $data['action'];
        if ($action !== 'off') {
            $value = (int) $data['value'];

            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'stock_available`
                SET `out_of_stock` = ' . (int) $value . '
                WHERE `id_product` IN (' . pSQL($idProductList) . ')'
            );
        }
    }

    public static function updateStockLocationField($idProductList, $data, $column)
    {
        $action = $data['action'];
        $value = trim($data['value']);

        if ($action !== 'off' && $value !== '') {
            $newValue = static::createUpdateValue($action, $column, $value);
            if ($newValue) {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'stock_available`
                    SET `location` = ' . $newValue . '
                    WHERE `id_product` IN (' . pSQL($idProductList) . ')'
                );
                $idProducts = explode(',', $idProductList);
                foreach ($idProducts as $idProduct) {
                    $combinationIds = PrestaProductCombinationHelper::getExistingCombinationIds($idProduct);
                    if (empty($combinationIds)) {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . 'product`
                            SET `location` = ' . $newValue . '
                            WHERE `id_product` = ' . (int) $idProduct
                        );
                    }
                }
            }
        }
    }

    public static function updateStockLabelField($idProductList, $data, $column)
    {
        $action = $data['action'];
        if ($action !== 'off') {
            $values = $data['value'];
            foreach ($values as $idLang => $value) {
                $trimmedValue = trim($value);
                if ($trimmedValue !== '') {
                    $newValue = static::createUpdateValue($action, $column, $trimmedValue);

                    if ($newValue) {
                        foreach (explode(',', $idProductList) as $idProduct) {
                            PrestaProductCombinationHelper::updateStockLabel($idProduct, $column, $idLang, $newValue);
                        }
                    }
                }
            }
        }
    }
}
