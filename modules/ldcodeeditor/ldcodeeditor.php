<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

use PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PsAccountsInstaller\Installer\Exception\InstallerException;

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Ldcodeeditor extends Module
{
    /**
     * @var ServiceContainer
     */
    private $container;

    private $accountsService;
    private $currentPlan;

    public $tabs = [
        [
            'name' => 'Code editor',
            'class_name' => 'AdminGamification',
            'visible' => true,
            'parent_class_name' => 'ShopParameters',
        ],
    ];

    protected $defaultConfiguration = [
        'LDCODEEDITOR_EDITOR_COLORSCHEME' => 'monokaipro',
        'LDCODEEDITOR_EDITOR_THEME' => 'dark',
    ];

    public function __construct()
    {
        $this->name = 'ldcodeeditor';
        $this->tab = 'administration';
        $this->version = '2.0.1';
        $this->module_key = '585acef3ea6b0ff03eb502dbd63a10b1';
        $this->author = 'LD Modules';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->displayName = $this->l('Code Editor - Create and edit your files');
        $this->description = $this->l(
            'Add a full features code editor in your back office. Create, edit or delete files.'
        );
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

        parent::__construct();

        if (null === $this->container) {
            $this->container = new ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue(
            'LDCODEEDITOR_EDITOR_COLORSCHEME',
            $this->defaultConfiguration['LDCODEEDITOR_EDITOR_COLORSCHEME']
        );
        Configuration::updateValue(
            'LDCODEEDITOR_EDITOR_THEME',
            $this->defaultConfiguration['LDCODEEDITOR_EDITOR_THEME']
        );

        return parent::install()
            && $this->installTab();
    }

    public function uninstall()
    {
        Configuration::deleteByName('LDCODEEDITOR_EDITOR_COLORSCHEME');
        Configuration::deleteByName('LDCODEEDITOR_EDITOR_THEME');

        return $this->uninstallTab() && parent::uninstall();
    }

    /**
     * Installation du controller dans le backoffice
     *
     * @return bool
     */
    protected function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminCodeeditor';
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('IMPROVE');
        $tab->icon = 'code';
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = 'Code Editor';
        }
        try {
            $tab->save();
        } catch (Exception $e) {
            echo $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Désinstallation du controller admin
     *
     * @return bool
     */
    protected function uninstallTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminCodeeditor');
        if ($idTab) {
            $tab = new Tab($idTab);
            try {
                $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();

                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve the service.
     *
     * @param string $serviceName
     */
    public function getService($serviceName)
    {
        return $this->container->getService($serviceName);
    }

    /**
     * @return string|false
     */
    public function getShopUuid()
    {
        return method_exists($this->accountsService, 'getShopUuid') ? $this->accountsService->getShopUuid() : $this->accountsService->getShopUuidV4();
    }

    /**
     * Inject Prestahsop Framework Account, Billing and ClousSync.
     *
     * @return void
     */
    public function injectPrestashopFramework()
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        $this->accountsService = null;

        try {
            $accountsFacade = $this->getService('ldcodeeditor.ps_accounts_facade');
            $this->accountsService = $accountsFacade->getPsAccountsService();
        } catch (InstallerException $e) {
            $accountsInstaller = $this->getService('ldcodeeditor.ps_accounts_installer');
            $accountsInstaller->install();
            $accountsFacade = $this->getService('ldcodeeditor.ps_accounts_facade');
            $this->accountsService = $accountsFacade->getPsAccountsService();
        }
        try {
            Media::addJsDef([
                'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                    ->present($this->name),
            ]);
            $this->context->smarty->assign('urlAccountsCdn', $this->accountsService->getAccountsCdn());
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();

            return '';
        }

        /**********************
         * PrestaShop Billing *
         * *******************/

        // Load the context for PrestaShop Billing
        $billingFacade = $this->getService('ldcodeeditor.ps_billings_facade');
        $partnerLogo = $this->getLocalPath() . 'views/img/partnerLogo.png';

        // PrestaShop Billing
        Media::addJsDef($billingFacade->present([
            'logo' => $partnerLogo,
            'tosLink' => 'https://ldmodules.com/tos/',
            'privacyLink' => 'https://ldmodules.com/privacy/',
            'emailSupport' => 'leodesigaux@gmail.com',
        ]));

        $this->context->smarty->assign('urlBilling', 'https://unpkg.com/@prestashopcorp/billing-cdc/dist/bundle.js');
        if ($moduleManager->isInstalled('ps_eventbus')) {
            $eventbusModule = Module::getInstanceByName('ps_eventbus');
            if (version_compare($eventbusModule->version, '1.9.0', '>=')) {
                $eventbusPresenterService = $eventbusModule->getService('PrestaShop\Module\PsEventbus\Service\PresenterService');

                $this->context->smarty->assign('urlCloudsync', 'https://assets.prestashop3.com/ext/cloudsync-merchant-sync-consent/latest/cloudsync-cdc.js');

                Media::addJsDef([
                    'contextPsEventbus' => $eventbusPresenterService->expose($this, ['info', 'modules', 'themes']),
                ]);
            }
        }
    }

    protected function injectPrestashopAccountOnly()
    {
        $this->accountsService = null;

        try {
            $accountsFacade = $this->getService('ldcodeeditor.ps_accounts_facade');
            $this->accountsService = $accountsFacade->getPsAccountsService();
        } catch (InstallerException $e) {
            $accountsInstaller = $this->getService('ldcodeeditor.ps_accounts_installer');
            $accountsInstaller->install();
            $accountsFacade = $this->getService('ldcodeeditor.ps_accounts_facade');
            $this->accountsService = $accountsFacade->getPsAccountsService();
        }
    }

    public function loadServiceContainer()
    {
        if (null === $this->container) {
            $this->container = new ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }
    }

    public function getContent()
    {
        return Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCodeeditor') . '&configure=' . Tools::safeOutput($this->name));
    }
}
