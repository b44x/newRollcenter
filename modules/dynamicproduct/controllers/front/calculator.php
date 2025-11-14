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

/* @noinspection PhpUnusedPrivateMethodInspection */

use DynamicProduct\classes\controllers\front\DynamicFrontController;
use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\DynamicCalculatorHelper;
use DynamicProduct\classes\models\DynamicInputField;

/** @noinspection PhpUnused */
class DynamicProductCalculatorModuleFrontController extends DynamicFrontController
{
    /** @noinspection PhpUnused */
    protected function processCalculateResult()
    {
        $calculator_helper = new DynamicCalculatorHelper($this->module, $this->context);

        $fields = Tools::getValue('fields');
        $adapter_data = Tools::getValue('adapter_data');

        $input_fields = [];
        $fields_visibility = [];
        $grouped_fields = [];

        $calculator_helper->resetDebugMessages();

        try {
            list($input_fields, $fields_visibility, $grouped_fields) = DynamicInputField::getInputFieldsFromData(
                $this->id_product,
                $this->id_attribute,
                $fields,
                empty($fields['changed']['value']) ? DynamicInputField::LOAD_ALL : DynamicInputField::LOAD_NONE
            );
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                throw $e;
            }

            $this->respond([
                'error' => 1,
                'message' => DynamicTools::reportException($e),
            ]);
        }

        try {
            $calculation = $calculator_helper->processCalculation(
                $this->id_product,
                $this->id_attribute,
                $input_fields,
                $fields_visibility,
                $adapter_data
            );
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                throw $e;
            }
            $calculation = [
                'input_fields' => $input_fields,
                'visibility' => $fields_visibility,
                'debug_messages' => DynamicProduct::$debug_messages,
            ];
            $this->respond([
                'error' => 1,
                'message' => DynamicTools::reportException($e),
            ]);
        }

        $calculation['grouped_fields'] = $grouped_fields;
        $this->respond($calculation);
    }
}
