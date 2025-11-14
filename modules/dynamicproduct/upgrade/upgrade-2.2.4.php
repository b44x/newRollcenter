<?php
/**
 * 2007-2025 TuniSoft
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    TuniSoft (tunisoft.solutions@gmail.com)
 * @copyright 2007-2025 TuniSoft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param DynamicProduct $module
 *
 * @return bool
 */
function upgrade_module_2_2_4($module)
{
    /** @noinspection UnnecessaryCastingInspection */
    $queries = [
        'UPDATE `ps_dynamicproduct_unit_value` SET `required` = `step` 
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_TEXT_ . ')',
        'UPDATE `ps_dynamicproduct_unit_value` SET `required` = `step`
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_TEXTAREA_ . ')',
        'UPDATE `ps_dynamicproduct_unit_value` SET `required` = `step`
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_FEATURE_ . ')',
        'UPDATE `ps_dynamicproduct_unit_value` SET `required` = `step`
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_DATE_ . ')',
        'UPDATE `ps_dynamicproduct_unit_value` SET `multiselect` = `init` 
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_THUMBNAILS_ . ')',
        'UPDATE `ps_dynamicproduct_unit_value` SET `color` = `extra` 
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_COLORPICKER_ . ')',
        'UPDATE `ps_dynamicproduct_unit_value` 
        SET `required` = `step`, `min_width` = `min`, `min_height` = `max`, `max_size` = `init` 
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_IMAGE_ . ')',
        'UPDATE `ps_dynamicproduct_unit_value` 
        SET `required` = `step`, `max_size` = `init`, `extensions` = `extra` 
        WHERE `id_field` IN (SELECT `id_field` FROM 
            ps_dynamicproduct_field WHERE `type` = ' . (int) _DP_FILE_ . ')',
    ];

    $success = true;

    foreach ($queries as $query) {
        $query = str_replace(
            ['ps_dynamicproduct', 'InnoDb'],
            [_DB_PREFIX_ . $module->name, _MYSQL_ENGINE_],
            $query
        );
        $success &= Db::getInstance()->execute($query);
    }

    return $success;
}
