<?php
/**
* 2007-2023 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/loggers/OPDWDBWarningLogger.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/loggers/OPDWDBErrorLogger.php');
class AdminMigrateOpenCartController extends AdminController
{
    // --- response vars:

    public $errors;
    protected $response;

    // --- request vars:

    protected $stepNumber;
    protected $toStepNumber;

    // -- dynamic vars:

    protected $forceIds = true;
    protected $forceManufacturerIds = false;
    protected $forceCategoryIds = false;
    protected $forceCarrierIds = false;
    protected $forceProductIds = false;
    protected $forceCatalogPriceRuleIds = false;
    protected $forceCustomerIds = false;
    protected $forceOrderIds = false;
    protected $forceCMSIds = false;

    protected $ps_validation_errors = true;

    protected $truncate = false;

    // --- source cart vars:

    protected $url_cart;
    protected $server_path = '/connector/server.php';
    protected $token_cart;
    protected $cms;
    protected $image_category;
    protected $image_carrier;
    protected $image_product;
    protected $image_manufacturer;
    protected $image_supplier;
    protected $image_employee;
    protected $table_prefix;
    protected $version;
    protected $charset;
    protected $blowfish_key;
    protected $cookie_key;
    protected $mapping;
    protected $languagesForQuery;
    protected $speed;

    // --- helper objects

    protected $query;
    protected $client;

    public function __construct()
    {
        $this->display = 'edit';
        parent::__construct();
        $this->controller_type = 'moduleadmin'; //instead of AdminController’s admin
        $tab = new Tab($this->id); // an instance with your tab is created; if the tab is not attached to the module, the exception will be thrown
        if (!$tab->module) {
            throw new PrestaShopException('Admin tab ' . get_class($this) . ' is not a module tab');
        }
        $this->module = Module::getInstanceByName($tab->module);
        if (!$this->module->id) {
            throw new PrestaShopException("Module {$tab->module} not found");
        }
        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminMigrateOpenCart'));

        $this->stepNumber = (int)Tools::getValue('step_number');
        $this->toStepNumber = (int)Tools::getValue('to_step_number');

        $this->initParams();
        if ($this->stepNumber > 1 || empty($this->stepNumber)) {
            $this->mapping = DWMappingOP::listMapping(true, true);

            if (isset($this->mapping['languages'])) {
                // -- unset language where value = 0
                if (($key = array_search(0, $this->mapping['languages'])) !== false) {
                    unset($this->mapping['languages'][$key]);
                }
                $keys = array_keys($this->mapping['languages']);
                $this->languagesForQuery = implode(',', $keys);
            }

            if (!empty($this->url_cart) && !empty($this->token_cart)) {
                $this->client = new OPClient($this->url_cart . $this->server_path, $this->token_cart);
                if (Configuration::get($this->module->name . '_debug_mode')) {
                    $this->client->debugOn();
                } else {
                    $this->client->debugOff();
                }
            }
            $this->query = new OPQuery();
            $this->query->setVersion($this->version);
            $this->query->setCart($this->cms);
            $this->query->setPrefix($this->table_prefix);
            $this->query->setLanguages($this->languagesForQuery);
            $this->query->setRowCount($this->speed);
        }


        if (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP) {
            $allShops = Shop::getCompleteListOfShopsID();
            Shop::setContext(Shop::CONTEXT_SHOP, $allShops[0]);
        }
    }


    private function initHelperObjects()
    {
        $this->mapping = DWMappingOP::listMapping(true, true);

        if (isset($this->mapping['languages'])) {
            // -- unset language where value = 0
            if (($key = array_search(0, $this->mapping['languages'])) !== false) {
                unset($this->mapping['languages'][$key]);
            }
            $keys = array_keys($this->mapping['languages']);
            $this->languagesForQuery = implode(',', $keys);
        }

        if (!empty($this->url_cart) && !empty($this->token_cart)) {
            $this->client = new OPClient($this->url_cart . $this->server_path, $this->token_cart);
            if (Configuration::get($this->module->name . '_debug_mode')) {
                $this->client->debugOn();
            } else {
                $this->client->debugOff();
            }
        }
        $this->query = new OPQuery();
        $this->query->setVersion($this->version);
        $this->query->setCart($this->cms);
        $this->query->setPrefix($this->table_prefix);
        $this->query->setLanguages($this->languagesForQuery);
        $this->query->setRowCount($this->speed);
    }

    // --- request processes
    public function postProcess()
    {
        parent::postProcess();
    }

    public function clearSmartyCache()
    {
        Tools::enableCache();
        Tools::clearCache($this->context->smarty);
        Tools::restoreCacheSettings();
    }

    public function ajaxProcessValidateStep()
    {
        $this->response = array('has_error' => false, 'has_warning' => false);

        if (!$this->tabAccess['edit']) {
            $this->errors[] = 'You do not have permission to use this wizard.';
        } else {
            // -- reset old data and response url to start new migration
            if (Tools::getIsset('resume')) {
                $this->response['step_form'] = $this->module->renderMigrationStep();
            } elseif (Tools::getValue('to_step_number') >= Tools::getValue('step_number')) {
                $this->validateStepFieldsValue();
            }
        }

        if (!empty($this->errors)) {
            $this->response['has_error'] = true;
            $this->response['errors'] = $this->errors;
        }
        if (!empty($this->warnings)) {
            $this->response['has_warning'] = true;
            $this->response['warnings'] = $this->warnings;
        }

        die(json_encode($this->response));
    }

    // --- validate functions

    private function validateStepFieldsValue()
    {
        if ($this->stepNumber == 1) {
            // validate url
            if (!Tools::getValue('source_shop_url')) {
                $this->errors[] = $this->l('The Url field is required.');
            } elseif (!Validate::isAbsoluteUrl(Tools::getValue('source_shop_url'))) {
                $this->errors[] = $this->l('Please enter a valid URL. Protocol is required (http://, https:// or ftp://)');
            }

            // validate token
            if (!Tools::getValue('source_shop_token')) {
                $this->errors[] = $this->l('The token field is required.');
            }

            if ($this->truncateModuleTables()) {
                $this->errors[] = $this->l('Can not truncate helper tables.');
            }

            if (empty($this->errors)) {
                $this->client = new OPClient(Tools::getValue('source_shop_url') . $this->server_path, Tools::getValue('source_shop_token'));
                if (Configuration::get($this->module->name . '_debug_mode')) {
                    $this->client->debugOn();
                } else {
                    $this->client->debugOff();
                }
                if ($this->client->check()) {
                    $content = $this->client->getContent();
                    if (!isset($content['cms'])) {
                        $this->errors[] = 'Please check URL' . ' - ' . $this->client->getMessage();
                    }
                    if (isset($content['cms'])) {
                        $this->saveParamsToConfiguration($content);
                        $this->initHelperObjects();
                        if ($this->requestToCartDetails()) {
                            $this->response['step_form'] = $this->module->renderConfigurationStep();
                        }
                    }
                } else {
                    $this->errors[] = 'Please check URL' . ' - ' . $this->client->getMessage();
                }
            }
        } elseif ($this->stepNumber == 2) {
            $maps = Tools::getValue('map');
            $languageSumValue = array_sum($maps['languages']);
            $languageDiffResArray = array_diff_assoc($maps['languages'], array_unique($maps['languages']));
            if (!($languageSumValue > 0 && empty($languageDiffResArray))) {
                $this->errors[] = $this->l('You must selecet different languages for each source shop language on Target.');
            }

//            $shopSumValue = array_sum($maps['multi_shops']);
//            $shopDiffResArray = array_diff_assoc($maps['multi_shops'], array_unique($maps['multi_shops']));
//            if (!($shopSumValue > 0 && empty($shopDiffResArray))) {
//                $this->errors[] = $this->l('Target shops must be different.');
//            }
            Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_data`');
            if (empty($this->errors)) {
                $this->initHelperObjects();
                Configuration::updateValue($this->module->name . '_force_manufacturer_ids', Tools::getValue('force_manufacturer_ids'));
                Configuration::updateValue($this->module->name . '_force_category_ids', Tools::getValue('force_category_ids'));
                Configuration::updateValue($this->module->name . '_force_carrier_ids', Tools::getValue('force_carrier_ids'));
                Configuration::updateValue($this->module->name . '_force_product_ids', Tools::getValue('force_product_ids'));
                Configuration::updateValue($this->module->name . '_force_catalogPriceRule_ids', Tools::getValue('force_catalogPriceRule_ids'));
                Configuration::updateValue($this->module->name . '_force_customer_ids', Tools::getValue('force_customer_ids'));
                Configuration::updateValue($this->module->name . '_force_order_ids', Tools::getValue('force_order_ids'));
                Configuration::updateValue($this->module->name . '_force_cms_ids', Tools::getValue('force_cms_ids'));
                Configuration::updateValue($this->module->name . '_ps_validation_errors', Tools::getValue('ps_validation_errors'));
                Configuration::updateValue($this->module->name . '_clear_data', Tools::getValue('clear_data'));
                Configuration::updateValue($this->module->name . '_migrate_recent_data', Tools::getValue('migrate_recent_data'));
                Configuration::updateValue($this->module->name . '_query_row_count', self::convertSpeedNameToNumeric(Tools::getValue('speed')));
                if ($this->createMapping($maps) && $this->createProcess()) {
                    $this->saveMappingValues(DWMappingOP::listMapping(true, true));
                    // turn on allow html iframe on
                    if (!Configuration::get('PS_ALLOW_HTML_IFRAME')) {
                        Configuration::updateValue('PS_ALLOW_HTML_IFRAME', 1);
                        Configuration::updateValue($this->module->name . '_allow_html_iframe', 1);
                    }

                    $this->response['step_form'] = $this->module->renderMigrationStep();
                } else {
                    $this->errors[] = $this->l('You must select minimum one data type for start migration.');
                }
            }
        }
    }


    public function ajaxProcessClearCache()
    {
        if (Tools::getValue('clear_cache')) {
            ini_set('max_execution_time', 0);
            Tools::clearSmartyCache();
            Tools::clearXMLCache();
            Media::clearCache();
            Tools::generateIndex();
            Search::indexation(true);

            $this->response['has_error'] = false;
            $this->response['has_warning'] = false;
            if (!empty($this->errors)) {
                $this->response['has_error'] = true;
                $this->response['errors'] = $this->errors;
            }
            if (!empty($this->warnings)) {
                $this->response['has_warning'] = true;
                $this->response['warnings'] = $this->warnings;
            }

            die(Tools::jsonEncode($this->response));
        }
    }

    public function ajaxProcessDebugOn()
    {
        if (Tools::getValue('turn') == 1) {
            Configuration::updateValue($this->module->name . '_debug_mode', 1);
        } else {
            Configuration::updateValue($this->module->name . '_debug_mode', 0);
        }
    }

    public function ajaxProcessImportProcess($die = true)
    {
        $this->response = array('has_error' => false, 'has_warning' => false);

        if (!$this->tabAccess['edit']) {
            $this->errors[] = 'You do not have permission to use this wizard.';
        } else {
            $activeProcess = DWProcessOP::getActiveProcessObject();
            $migrateRecentData = Configuration::get($this->module->name . '_migrate_recent_data');
            if (Validate::isLoadedObject($activeProcess)) {
                $this->query->setOffset($activeProcess->imported);
                if ($activeProcess->imported == 0) {
                    // dump($this->truncate);
                    // dump(!$this->truncateTables($activeProcess->type));
                    // return;
                    if ($this->truncate && !$this->truncateTables($activeProcess->type)) {
                        $this->errors[] = 'Can\'t clear current data on Target shop ' . Db::getInstance()->getMsgError();
                    }


                    $activeProcess->time_start = date('Y-m-d H:i:s', time());
                    $activeProcess->save();

                    if ($activeProcess->type == 'orders') {
                        Configuration::updateValue('PS_TAX', 0);
                    }
                }

                if ($activeProcess->type == 'manufacturers') {
                    $this->importManufacturers($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'categories') {
                    $this->importCategories($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'products') {
                    $this->importProducts($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'customers') {
                    $this->importCustomers($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'orders') {
                    $this->importOrders($activeProcess);
                    $this->clearSmartyCache();
                }
            } else {
                die('no process.');
            }
            // dump(DWProcessOP::calculateImportedDataPercent());
            $this->response['percent'] = DWProcessOP::calculateImportedDataPercent();
            if ($this->response['percent'] == 100) {
                // turn off allow html iframe feature
                if (Configuration::get($this->module->name . '_allow_html_iframe')) {
                    Configuration::updateValue('PS_ALLOW_HTML_IFRAME', 0, null, 0, 0);
                    Configuration::updateValue($this->module->name . '_allow_html_iframe', 0, null, 0, 0);
                }
            }
        }

        // if (count($this->errors)) {
        //     $this->response['has_error'] = true;
        //     $this->response['errors'] = $this->errors;
        // }
        // if (count($this->warnings)) {
        //     $this->response['has_warning'] = true;
        //     $this->response['warnings'] = $this->warnings;
        // }

        if ($die) {
            die(json_encode($this->response));
        }
    }

    // --- import functions

    private function importManufacturers($process)
    {
        $this->client->setPostData($this->query->manufactures());
        if ($this->client->query()) {
            $manufacturers = $this->client->getContent();
            $import = new OPImport($process, $this->version, $this->url_cart, $this->forceManufacturerIds);
            $import->setImagePath($this->image_manufacturer);
            $import->setPsValidationErrors($this->ps_validation_errors);
            $import->manufacturers($manufacturers);
            $this->errors = $import->getErrorMsg();
            $this->warnings = $import->getWarningMsg();
            $this->response = $import->getResponse();
        } else {
            $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
        }
    }

    private function importCategories($process)
    {
        //@TODO find fix for PS 1.4 for category id 2 WHERE is ID 2 standart category from list
        $this->client->json_encodeOff();
        $this->client->setPostData($this->query->category());
        if ($this->client->query()) {
            $categories = $this->client->getContent();
            $this->client->setPostData($this->query->categorySqlSecond(self::getCleanIDs($categories, 'category_id')));
            if ($this->client->query()) {
                $categoriesAdditionalSecond = $this->client->getContent();
                $import = new OPImport($process, $this->version, $this->url_cart, $this->forceCategoryIds, $this->client, $this->query);
                $import->setImagePath($this->image_category);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->categories($categories, $categoriesAdditionalSecond);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            }
        } else {
            $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
        }
    }

    private function importProducts($process)
    {
        $this->client->setPostData($this->query->product());
        if ($this->client->query()) {
            $products = $this->client->getContent();
            $productIds = self::getCleanIDs($products, 'product_id');
            $this->client->json_encodeOn();
            $this->client->setPostData($this->query->productSqlSecond($productIds));
            if ($this->client->query()) {
                $productAdditionalSecond = $this->client->getContent();
                $options_id = self::getCleanIDs($productAdditionalSecond['product_attribute'], 'option_id');
                $options_values_id = self::getCleanIDs($productAdditionalSecond['product_attribute'], 'option_value_id');
                $this->client->setPostData($this->query->productSqlThird($options_id, $options_values_id));
                if ($this->client->query()) {
                    $productAdditionalThird = $this->client->getContent();
                    $import = new OPImport($process, $this->version, $this->url_cart, $this->forceProductIds, $this->client, $this->query);
                    $import->setImagePath($this->image_category);
                    $import->setPsValidationErrors($this->ps_validation_errors);
                    $import->products($products, $productAdditionalSecond, $productAdditionalThird);
                    $this->errors = $import->getErrorMsg();
                    $this->warnings = $import->getWarningMsg();
                    $this->response = $import->getResponse();
                } else {
                    $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
                }
            } else {
                $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
        }
    }

    private function importCustomers($process)
    {

        $this->client->setPostData($this->query->customers());
        $this->client->json_encodeOff();
        if ($this->client->query()) {
            $customers = $this->client->getContent();
            $this->client->setPostData($this->query->address(self::getCleanIDs($customers, 'customer_id')));
            if ($this->client->query()) {
                $addresses = $this->client->getContent();
                $import = new OPImport($process, $this->version, $this->url_cart, $this->forceCustomerIds);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->customers($customers, $addresses);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();

            } else {
                $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
        }
    }

    private function importOrders($process)
    {
        $this->client->setPostData($this->query->order());
        if ($this->client->query()) {
            $orders = $this->client->getContent();
            $id_order = self::getCleanIDs($orders, 'order_id');
            $this->client->json_encodeOn();
            $this->client->setPostData($this->query->orderSqlSecond($id_order));
            if ($this->client->query()) {
                $ordersAdditionalSecond = $this->client->getContent();
                $import = new OPImport($process, $this->version, $this->url_cart, $this->forceOrderIds);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->orders($orders, $ordersAdditionalSecond);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            } else {
                $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->l('Can\'t execute query to source Shop. ' . $this->client->getMessage());
        }
    }

    // --- Internal helper methods:

    private function truncateModuleTables()
    {
        $res = Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_data`');
        $res &= Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_process`');
        $res &= Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_mapping`');

        if (!$res) {
            return false;
        }
    }

    private function createMapping($maps)
    {
        $res = true;
        foreach ($maps as $map) {
            foreach ($map as $key => $val) {
                $mapping = new DWMappingOP($key);
                $mapping->local_id = $val;
                $res &= $mapping->save();
            }
        }

        return $res;
    }

    private function createProcess()
    {
        $res = Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_process`');
        $res &= Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_data`');
        OPDWDBErrorLogger::removeErrorLogs();
        OPDWDBWarningLogger::removeWarningLogs();
        OPDWDBWarningLogger::removeLogFile();
        $this->client->setPostData($this->query->getCountInfo());
        $this->client->json_encodeOn();
        if ($this->client->query()) {
            $processes = $this->client->getContent();
            foreach ($processes as $processKey => $processCount) {
                if (isset($processCount[0]['c']) && !empty($processCount[0]['c'])) {
                    $process = new DWProcessOP();
                    $process->type = $processKey;
                    $process->total = (int)$processCount[0]['c'];
                    $process->imported = 0;
                    $process->id_source = 0;
                    $process->error = 0;
                    $process->point = 0;
                    $process->time_start = 0;
                    $process->finish = 0;
                    $res &= $process->add();
                }
            }

            return $res;
        }

        return false;
    }

    private function saveParamsToConfiguration($content)
    {
        Configuration::updateValue($this->module->name . '_url', Tools::getValue('source_shop_url'));
        Configuration::updateValue($this->module->name . '_token', Tools::getValue('source_shop_token'));
        Configuration::updateValue($this->module->name . '_cms', $content['cms']);
//        Configuration::updateValue($this->module->name . '_image_category', $content['image_category']);
//        Configuration::updateValue($this->module->name . '_image_manufacturer', $content['image_manufacturer']);
        Configuration::updateValue($this->module->name . '_table_prefix', $content['table_prefix']);
        Configuration::updateValue($this->module->name . '_charset', $content['charset']);

        $this->initParams();
    }

    private function initParams()
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP) {
            //fix the osl php version issue
            $allShops = Shop::getCompleteListOfShopsID();
            Shop::setContext(Shop::CONTEXT_SHOP, $allShops[0]);
        }

        $this->url_cart = Configuration::get($this->module->name . '_url');
        $this->token_cart = Configuration::get($this->module->name . '_token');
        $this->cms = Configuration::get($this->module->name . '_cms');
        $this->image_category = Configuration::get($this->module->name . '_image_category');
        $this->image_carrier = Configuration::get($this->module->name . '_image_carrier');
        $this->image_product = Configuration::get($this->module->name . '_image_product');
        $this->image_manufacturer = Configuration::get($this->module->name . '_image_manufacturer');
        $this->image_supplier = Configuration::get($this->module->name . '_image_supplier');
        $this->image_employee = Configuration::get($this->module->name . '_image_employee');
        $this->table_prefix = Configuration::get($this->module->name . '_table_prefix');
        $this->version = Configuration::get($this->module->name . '_version');
        $this->charset = Configuration::get($this->module->name . '_charset');
        $this->blowfish_key = Configuration::get($this->module->name . '_blowfish_key');
        $this->cookie_key = Configuration::get($this->module->name . '_cookie_key');
        $this->forceManufacturerIds = Configuration::get($this->module->name . '_force_manufacturer_ids');
        $this->forceCategoryIds = Configuration::get($this->module->name . '_force_category_ids');
        $this->forceCarrierIds = Configuration::get($this->module->name . '_force_carrier_ids');
        $this->forceProductIds = Configuration::get($this->module->name . '_force_product_ids');
        $this->forceCatalogPriceRuleIds = Configuration::get($this->module->name . '_force_catalogPriceRule_ids');
        $this->forceCustomerIds = Configuration::get($this->module->name . '_force_customer_ids');
        $this->forceOrderIds = Configuration::get($this->module->name . '_force_order_ids');
        $this->forceCMSIds = Configuration::get($this->module->name . '_force_cms_ids');
        $this->ps_validation_errors = Configuration::get($this->module->name . '_ps_validation_errors');
        $this->truncate = Configuration::get($this->module->name . '_clear_data');
        $this->speed = Configuration::get($this->module->name . '_query_row_count');
    }

    private function requestToCartDetails()
    {
        // --- get default values from source cart

        $this->client->setPostData($this->query->getDefaultShopValues());
        $this->client->json_encodeOn();
        $this->client->query();
        $resultDefaultShopValues = $this->client->getContent();
//        $this->query->setVersion($this->version);
        $this->client->setPostData($this->query->getMappingInfo($resultDefaultShopValues));
        $this->client->query();
        $mappingInformation = $this->client->getContent();
        $mapping_value = DWSaveMappingOP::listMapping(true,true);
        if (is_array($mappingInformation)) {
            if ($this->checkMappingIsEmpty(DWMappingOP::listMapping())) {
                if (Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_mapping`')) {
                    foreach ($mappingInformation as $mappingType => $mappingObject) {
                        foreach ($mappingObject as $value) {
                            if (!empty($value['source_name'])) {
                                $mapping = new DWMappingOP();
                                $mapping->type = $mappingType;
                                $mapping->source_id = $value['source_id'];
                                $mapping->source_name = $value['source_name'];
                                if (!empty($mapping_value)) {
                                    $mapping->local_id = $mapping_value[$mappingType][$value['source_id']];
                                }
                                if (!$mapping->save()) {
                                    $this->errors[] = 'Can\'t save to datebase mapping information. ';
                                }
                            }
                        }
                    }
                } else {
                    $this->errors[] = 'Can\'t truncate mapping table';
                }
            }
        }
        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    protected function checkMappingIsEmpty($mapping)
    {

        if (empty($mapping)) {
            return true;
        }

        return true;
    }

    public function saveMappingValues($mapping_values)
    {
        if (Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'dw_save_mappingop`')) {
            foreach ($mapping_values as $mappingType => $mappingObject) {
                foreach ($mappingObject as $source_id => $local_id) {
                    $mapping = new DWSaveMappingOP();
                    $mapping->type = $mappingType;
                    $mapping->source_id = $source_id;
                    $mapping->source_name = $mappingType;
                    $mapping->local_id = $local_id;
                    if (!$mapping->save()) {
                        $this->errors[] = $this->module->l('Can\'t save to database mapping information. ');
                    }
                } 
            }
        }
    }

    protected function truncateTables($case)
    {
        $res = false;
        switch ($case) {
            case 'taxes':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_rules_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_rules_group_shop`');
                $res &= DWMigratedDataOP::deleteMigratedIds('taxrulesgroup');
                break;
            case 'manufacturers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_shop`');
                $res &= DWMigratedDataOP::deleteMigratedIds('manufacturer');
                foreach (scandir(_PS_MANU_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_MANU_IMG_DIR_ . $d);
                    }
                }
                break;
            case 'categories':
                $res &= Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category`
					WHERE id_category NOT IN (' . (int)Configuration::get('PS_HOME_CATEGORY') . ', ' . (int)Configuration::get('PS_ROOT_CATEGORY') . ')');
                $res &= Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category_lang`
					WHERE id_category NOT IN (' . (int)Configuration::get('PS_HOME_CATEGORY') . ', ' . (int)Configuration::get('PS_ROOT_CATEGORY') . ')');
                $res &= Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category_shop`
					WHERE `id_category` NOT IN (' . (int)Configuration::get('PS_HOME_CATEGORY') . ', ' . (int)Configuration::get('PS_ROOT_CATEGORY') . ')');
//                $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'category` AUTO_INCREMENT = 3');
                $res &= DWMigratedDataOP::deleteMigratedIds('category');
                foreach (scandir(_PS_CAT_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_CAT_IMG_DIR_ . $d);
                    }
                }
                break;
            case 'carriers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_zone`');
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'module_carrier`');
                }
                $res &= DWMigratedDataOP::deleteMigratedIds('carrier');
                foreach (scandir(_PS_SHIP_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_SHIP_IMG_DIR_ . $d);
                    }
                }
                break;
            case 'warehouse':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_product_location`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_shop`');
                break;
            case 'products':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_product`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_tag`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tag`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tag_count`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'category_product`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_priority`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_product`');
                if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . 'favorite_product\' '))) { //check if table exist
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'favorite_product`');
                }
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attachment`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'accessory`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_country_tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_download`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_group_reduction_cache`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_sale`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_supplier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_product_location`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock_available`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock_mvt`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization_field`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization_field_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supply_order_detail`');
                // $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_impact`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_combination`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_image`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'pack`');
                $res &= DWMigratedDataOP::deleteMigratedIds('product');
                Image::deleteAllImages(_PS_PROD_IMG_DIR_);
                if (!file_exists(_PS_PROD_IMG_DIR_)) {
                    mkdir(_PS_PROD_IMG_DIR_);
                }
//                break;
//            case 'combinations':
                // $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_impact`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_combination`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_image`');
                if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE`' . _DB_PREFIX_ . 'attribute`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE`' . _DB_PREFIX_ . 'attribute_group`');
                } else {
                    $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'attribute`');
                    $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'attribute_group`');
                }
                $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'stock_available` WHERE id_product_attribute != 0');
//            case 'suppliers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier_shop`');
                foreach (scandir(_PS_SUPP_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_SUPP_IMG_DIR_ . $d);
                    }
                }
//                break;
                break;
            case 'catalog price rules':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_rule_condition_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_rule_condition`');
                $res &= DWMigratedDataOP::deleteMigratedIds('specificpricerule');
                break;
            case 'customers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer_group`');
//                break;
//            case 'addresses':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'address`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_product`');

                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_combination`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_country`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_product_rule_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_product_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_product_rule_value`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_shop`');
                $res &= DWMigratedDataOP::deleteMigratedIds('customer');
                break;
            case 'orders':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'orders`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_detail`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_history`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_invoice`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_cart_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_payment`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_invoice_payment`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_invoice_tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer_message`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer_thread`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_message`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_message_lang`');
                $res &= DWMigratedDataOP::deleteMigratedIds('order');
                break;
            case 'cms':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_category`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_category_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_category_shop`');
                if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block_lang`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block_page`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block_shop`');
                }
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_role`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_role_lang`');
                $res &= DWMigratedDataOP::deleteMigratedIds('cms');
                break;
            case 'seo':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'meta`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'meta_lang`');
                $res &= DWMigratedDataOP::deleteMigratedIds('meta');
                break;
        }
        Image::clearTmpDir();
        return $res;
    }

    // --- Static utility methods:

    public static function getCleanIDs($rows, $key)
    {
        $result = array();
        if (is_array($rows) && !empty($rows)) {
            foreach ($rows as $row) {
                if (!isset($row[$key])) {
                    return null;
                }
                if (is_numeric($row[$key])) {
                    $result[] = $row[$key];
                } else {
                    $result[] = '\'' . $row[$key] . '\'';
                }
            }
            $result = array_unique($result);
            $result = implode(',', $result);

            return $result;
        } else {
            return 'null';
        }
    }

    public static function convertSpeedNameToNumeric($speed)
    {
        switch ((string)$speed) {
            case 'VerySlow':
                $row_count = 2;
                break;
            case 'Slow':
                $row_count = 5;
                break;
            case 'Normal':
                $row_count = 10;
                break;
            case 'Fast':
                $row_count = 25;
                break;
            case 'VeryFast':
                $row_count = 85;
                break;
            case 'MigrationProSpeed':
                $row_count = 100;
                break;
            default:
                $row_count = 10;
                break;
        }

        return $row_count;
    }
}
