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

use DynamicProduct\classes\controllers\front\DynamicFrontController;
use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\DynamicCalculatorHelper;
use DynamicProduct\classes\helpers\ScriptsHelper;
use DynamicProduct\classes\models\DynamicConfig;
use DynamicProduct\classes\models\DynamicInputField;
use DynamicProduct\classes\models\DynamicMainConfig;
use DynamicProduct\classes\models\DynamicProductStep;
use DynamicProduct\lib\dp_trans\TranslationHelper;

class DynamicProductLoaderModuleFrontController extends DynamicFrontController
{
    protected function processLoadVariables()
    {
        $source = basename(__FILE__, '.php');

        $is_hot_mode = (int) Tools::getValue('is_hot_mode');
        $id_product = (int) Tools::getValue('id_product');
        $id_source_product = (int) Tools::getValue('id_source_product');
        $id_attribute = (int) Tools::getValue('id_attribute');
        $is_admin_edit = (int) Tools::getValue('is_admin_edit');
        $url_values = Tools::getValue('url_values', []);

        $product_config = DynamicConfig::getByProduct($id_product);

        $id_lang = $this->context->language->id;

        $grouped_fields = [];
        $has_groups = false;

        $is_admin = $this->module->provider->isAdmin();
        $translation_helper = new TranslationHelper($this->module, $this->context);
        $calculator_helper = new DynamicCalculatorHelper($this->module, $this->context);

        $input_fields = [];
        $fields_visibility = [];
        $calculator_helper->resetDebugMessages();
        $error = null;
        try {
            list($input_fields, $fields_visibility, $grouped_fields) = DynamicInputField::getDefaultInputFields(
                $id_product,
                $id_attribute,
                DynamicInputField::LOAD_INITIAL,
                $url_values
            );

            $has_ungrouped_fields = isset($grouped_fields[0]);
            $groups_count = count(array_keys($grouped_fields));
            $has_groups = !$has_ungrouped_fields || $groups_count > 1;

            $calculation = $calculator_helper->processCalculation(
                $id_product,
                $id_attribute,
                $input_fields,
                $fields_visibility,
                null
            );
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                throw $e;
            }
            $error = DynamicTools::reportException($e, true);
            $calculation = [
                'input_fields' => $input_fields,
                'visibility' => $fields_visibility,
                'debug_messages' => DynamicProduct::$debug_messages,
            ];
        }

        if (!$error) {
            foreach ($grouped_fields as $group) {
                foreach ($group['fields'] as $field) {
                    if (!$field['name']) {
                        $error = $this->module->l(
                            'You have a field with an empty name. Please check your configuration.',
                            $source
                        );
                    }
                }
            }
        }

        $source = DynamicTools::getSource();
        $variables = [
            'dp_hot_mode' => $is_hot_mode,
            'dp_hot_mode_port' => DynamicTools::getHotPort(),
            'dp' => [
                'version' => $this->module->version,
                'id_product' => $id_product,
                'id_source_product' => $id_source_product,
                'id_attribute' => $id_attribute,
                'is_admin' => $is_admin,
                'is_admin_edit' => $is_admin_edit && $is_admin,
                'is_create_customization' => (int) Tools::getValue('is_create_customization') && $is_admin,
                'uri' => $this->module->getPathUri(),
                'data_uri' => $this->module->provider->getDataDirUrl(),
                'config' => $product_config,
                'main_config' => DynamicMainConfig::getConfig(),
                'grouped_fields' => $grouped_fields,
                'has_groups' => $has_groups,
                'steps' => DynamicProductStep::getRowsByProductWithLabels($id_product, $id_lang),
                'field_types' => $this->module->field_types,
                'countries' => $this->module->provider->getCountries($id_source_product),
                'calculation' => $calculation,
                'error' => $error,
                'id_cart' => Tools::getValue('dp_cart', 0),
                'id_customer' => Tools::getValue('dp_customer', 0),
                'scripts_hashes' => ScriptsHelper::getHashes(),
                'controllers' => [
                    'calculator' => $this->context->link->getModuleLink($this->module->name, 'calculator'),
                    'customization' => $this->context->link->getModuleLink($this->module->name, 'customization'),
                    'uploader' => $this->context->link->getModuleLink($this->module->name, 'uploader'),
                ],
            ],
            'dp_translations' => $translation_helper->getFrontTranslations(),
            'dp_message' => [
                'short' => $this->module->l(
                    'The value of the field _label_ must be at least _min_ characters long',
                    $source
                ),
                'long' => $this->module->l(
                    'The value of the field _label_ must be at most _max_ characters long',
                    $source
                ),
                'empty' => $this->module->l(
                    'The _label_ field is required',
                    $source
                ),
                'min_max' => $this->module->l(
                    'The _label_ field must be between _min_ and _max_',
                    $source
                ),
                'select' => $this->module->l(
                    'Please select an option for the _label_ field',
                    $source
                ),
                'confirm' => $this->module->l(
                    'Are you sure you want to delete this item?',
                    $source
                ),
                'remove_image_upload' => $this->module->l(
                    'Are you sure you want to delete this image?',
                    $source
                ),
                'remove_file_upload' => $this->module->l(
                    'Are you sure you want to delete this file?',
                    $source
                ),
                'save_success' => $this->module->l(
                    'Your product was added to cart successfully!',
                    $source
                ),
                'save_error' => $this->module->l(
                    'An error occurred while saving your customization, please try again',
                    $source
                ),
                'cart_error' => $this->module->l(
                    'There are not enough products in stock!',
                    $source
                ),
                'attributes_errors' => $this->module->l(
                    'Some product variants could not be added to cart with the selected quantities',
                    $source
                ),
                'uploading' => $this->module->l(
                    'Uploading...',
                    $source
                ),
                'complete' => $this->module->l(
                    'Complete',
                    $source
                ),
            ],
        ];

        $this->respond([
            'variables' => $variables,
        ]);
    }
}
