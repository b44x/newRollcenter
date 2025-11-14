<?php
/**
* 2007-2024 PrestaShop
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
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

@ini_set('max_execution_time', 0);
@ini_set('memory_limit', '-1');


require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/DWMappingOP.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/DWSaveMappingOP.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/DWProcessOP.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/DWDataOP.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/DWMigratedDataOP.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/DWMigratedIdsOP.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/OPClient.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/OPQuery.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/OPImport.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/DWOP.php');
require_once(_PS_MODULE_DIR_ . 'migrateopencartdw/classes/PasswordEncryptOp.php');

class Migrateopencartdw extends Module
{
    protected $wizard_steps;

    public function __construct()
    {
        $this->name = 'migrateopencartdw';
        $this->tab = 'migration_tools';
        $this->version = '1.1.0';
        $this->author = 'Digital Workforce';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '5faad0842eeefe0bd94f0495283dee53';

        parent::__construct();

        $this->displayName = $this->l('OpenCart To PrestaShop Migration Tool');
        $this->description = $this->l('Migration entites from OpenCart');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminMigrateOpenCart';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'DigitalWorkforce';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;

        include(dirname(__FILE__) . '/sql/install.php');

        if (!$tab->add() ||
            !parent::install() ||
            !$this->registerHook('actionBeforeAuthentication') ||
            !$this->registerHook('displayBackOfficeHeader')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');


        $id_tab = (int)Tab::getIdFromClassName('AdminMigrateOpenCart');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function initWizard()
    {
        $this->wizard_steps = array(
            'name' => 'dw_wizard',
            'steps' => array(
                array(
                    'title' => $this->l('Connection'),
                ),
                array(
                    'title' => $this->l('Configuration'),
                ),
                array(
                    'title' => $this->l('Migration'),
                )
            )
        );
    }

    public function hookActionBeforeAuthentication()
    {
        $mail = Tools::getValue('email');
        $pass = Tools::getValue('passwd');
        if (Validate::isEmail($mail)) {
            $result = DWOP::getOpUser($mail);
            if (!empty($result)) {
                $hashedpass = $result[0]['passwd'];
                $salt = $result[0]['salt'];
                $id_customer = (int)$result[0]['id_customer'];

                $opPasswordEncryptor = new PasswordEncryptOp($hashedpass, $salt, 'sha1');

                if ($opPasswordEncryptor->comparePassword($pass)) {
                    $customer = new Customer($id_customer);
                    $customer->passwd = Tools::encrypt($pass);
                }

                $afterLogin = DWOP::checkPassUpdated($mail);
                if (!empty($afterLogin)) {
                    DWOP::deleteUserById($id_customer);
                }
            }
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            Media::addJsDef(array('warningLogPath' => $this->_path . 'classes/loggers/warning_logs.txt'));
            Media::addJsDef(array('validate_url' => $this->context->link->getAdminLink('AdminMigrateOpenCart')));
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . '/views/js/dw_wizard.js');
            $this->context->controller->addJS($this->_path . '/views/js/dw_smart_wizard.js');
            $this->context->controller->addJqueryPlugin('typewatch');
            $this->context->controller->addCSS($this->_path . '/views/css/front.css');
            $this->context->controller->addCSS($this->_path . '/views/css/all.min.css');
        }
    }

    public function renderConnectionStep()
    {
        $connection_details = array(
            'source_shop_url' => Configuration::get($this->name . '_url'),
            'source_shop_token' => Configuration::get($this->name . '_token'),
        );
        $this->context->smarty->assign('connection_details', $connection_details);
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/connection.tpl');
    }

    public function renderConfigurationStep()
    {
        $mappings = DWMappingOP::listMapping(true);

        if (empty($mappings)) {
            return false;
        }
        $multiShopsInputs = array();
        $currenciesInputs = array();
        $languagesInputs = array();
        $ordersStatusInputs = array();
        $customerGroupInputs = array();
        $taxRulesInputs = array();

        // foreach ($mappings['multi_shops'] as $key => $val) {
        //     $multiShopsInputs[] = array(
        //         'label' => $val['source_name'],
        //         'local_id' => $val['local_id'],
        //         'name' => "map[multi_shops][$key]",
        //         'options' => Shop::getShops(),
        //     );
        // }

        foreach ($mappings['currencies'] as $key => $val) {
            $currenciesInputs[] = array(
                'label' => $val['source_name'],
                'local_id' => $val['local_id'],
                'name' => "map[currencies][$key]",
                'options' => Currency::getCurrencies(),
            );
        }

        foreach ($mappings['languages'] as $key => $val) {
            $languagesInputs[] = array(
                'label' => $val['source_name'],
                'local_id' => $val['local_id'],
                'name' => "map[languages][$key]",
                'options' => array_merge(array(array('id_lang' => 0, 'name' => 'none')), Language::getLanguages())
            );
        }

        foreach ($mappings['order_states'] as $key => $val) {
            $ordersStatusInputs[] = array(
                'label' => $val['source_name'],
                'local_id' => $val['local_id'],
                'name' => "map[order_states][$key]",
                'options' => OrderState::getOrderStates($this->context->language->id),
            );
        }

        if (!empty($mappings['customer_groups'])) {
            foreach ($mappings['customer_groups'] as $key => $val) {
                $customerGroupInputs[] = array(
                    'label' => $val['source_name'],
                    'local_id' => $val['local_id'],
                    'name' => "map[customer_groups][$key]",
                    'options' => Group::getGroups($this->context->language->id),
                );
            }
        }

        foreach ($mappings['tax_class'] as $key => $val) {
            $customerGroupInputs[] = array(
                'label' => $val['source_name'],
                'local_id' => $val['local_id'],
                'name' => "map[customer_groups][$key]",
                'options' => TaxRulesGroup::getTaxRulesGroupsForOptions($this->context->language->id),
            );
        }

        $this->context->smarty->assign('multiShopsInputs', $multiShopsInputs);
        $this->context->smarty->assign('currenciesInputs', $currenciesInputs);
        $this->context->smarty->assign('languagesInputs', $languagesInputs);
        $this->context->smarty->assign('ordersStatusInputs', $ordersStatusInputs);
        $this->context->smarty->assign('customerGroupInputs', $customerGroupInputs);
        $this->context->smarty->assign('taxRulesInputs', $taxRulesInputs);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configuration.tpl');
    }

    public function renderMigrationStep()
    {
        if (count($lastExecutingProcesses = DWProcessOP::getAll())) {
            $this->context->smarty->assign('processes', $lastExecutingProcesses);
        }

        $this->context->smarty->assign(array(
            'percent' => DWProcessOP::calculateImportedDataPercent(),
        ));

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/view.tpl');

        return $output;
    }

    public function getContent()
    {
        $this->initWizard();
        $this->context->smarty->assign('module_dir', $this->_path);
        $currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $this->context->smarty->assign('currentIndex', $currentIndex);
        $this->context->smarty->assign(array(
            'wizard_steps' => $this->wizard_steps,
            'wizard_contents' => array(
                'contents' => array(
                    0 => $this->renderConnectionStep(),
                    1 => '',
                    2 => ''
                )
            ),
            'labels' => array(
                'next' => $this->l('Next'),
                'previous' => $this->l('Previous'),
                'finish' => $this->l('Migrate')
            )
        ));

        $output = '';
        $processObject = DWProcessOP::getActiveProcessObject();
        if (Validate::isLoadedObject($processObject) && $lastExecutingProcesses = DWProcessOP::getAll()) {
            if (count($lastExecutingProcesses = DWProcessOP::getAll())) {
                $this->context->smarty->assign('processes', $lastExecutingProcesses);
            }
        }

        $this->context->smarty->assign(array(
            'percent' => DWProcessOP::calculateImportedDataPercent(),
        ));

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/wizard.tpl');

        return $output;
    }
}
