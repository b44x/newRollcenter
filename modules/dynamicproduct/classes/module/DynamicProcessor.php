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
namespace DynamicProduct\classes\module;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\DynamicField;
use DynamicProduct\classes\models\DynamicFieldGroup;
use DynamicProduct\classes\models\DynamicStep;
use DynamicProduct\classes\models\DynamicUnit;

class DynamicProcessor
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getCurrentAction()
    {
        $request = array_merge($_GET, $_POST);
        $keys = array_keys($request);
        foreach ($keys as $key) {
            if (strpos($key, 'submit_') === 0) {
                // return current action
                return str_replace('submit_', '', $key);
            }
        }

        return false;
    }

    public function processAddUnit()
    {
        $unit = new DynamicUnit();
        $unit->saveFromPost();
        DynamicTools::redirect('#units-tab');
    }

    public function processEditUnit()
    {
        $id_unit = (int) \Tools::getValue('id_unit');
        if (DynamicTools::checkRestricted($id_unit, $this->module->restricted_units)) {
            $this->module->html_content .= $this->module->displayError('You do not have permission to edit this.');

            return;
        }
        $unit = new DynamicUnit($id_unit);
        $unit->saveFromPost();
        DynamicTools::redirect('#units-tab');
    }

    public function processDeleteUnit()
    {
        $id_unit = (int) \Tools::getValue('id_unit');
        if (DynamicTools::checkRestricted($id_unit, $this->module->restricted_units)) {
            $this->module->html_content .= $this->module->displayError('You do not have permission to edit this.');
        } else {
            $unit = new DynamicUnit($id_unit);
            $unit->delete();
            DynamicTools::redirect('#units-tab');
        }
    }

    public function processAddFieldGroup()
    {
        $field_group = new DynamicFieldGroup();
        $field_group->saveFromPost();
        DynamicTools::redirect('#field-groups-tab');
    }

    public function processEditFieldGroup()
    {
        $id_field_group = (int) \Tools::getValue('id_field_group');
        $field_group = new DynamicFieldGroup($id_field_group);
        $field_group->saveFromPost();
        DynamicTools::redirect('#field-groups-tab');
    }

    public function processEditStep()
    {
        $id_step = (int) \Tools::getValue('id_step');
        $step = new DynamicStep($id_step);
        $step->saveFromPost();
        DynamicTools::redirect('#steps-tab');
    }

    public function processDeleteFieldGroup()
    {
        $id_field_group = (int) \Tools::getValue('id_field_group');
        $field_group = new DynamicFieldGroup($id_field_group);
        $field_group->delete();
        DynamicTools::redirect('#field-groups-tab');
    }

    public function processDeleteStep()
    {
        $id_step = (int) \Tools::getValue('id_step');
        $step = new DynamicStep($id_step);
        $step->delete();
        DynamicTools::redirect('#steps-tab');
    }

    public function processRemoveFavoriteField()
    {
        $id_favorite_field = (int) \Tools::getValue('id_favorite_field');
        $favorite_field = new DynamicField($id_favorite_field);
        $favorite_field->favorite = false;
        $favorite_field->save();
        DynamicTools::redirect('#favorite-fields-tab');
    }

    public function processRemoveCommonField()
    {
        $id_common_field = (int) \Tools::getValue('id_common_field');
        $common_field = new DynamicField($id_common_field);
        $common_field->common = false;
        $common_field->save();
        DynamicTools::redirect('#common-fields-tab');
    }
}
