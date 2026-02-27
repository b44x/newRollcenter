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

use Prestashop\ModuleLibMboInstaller\DependencyBuilder;
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

require_once _PS_MODULE_DIR_ . '/ldcodeeditor/classes/Filesystem.php';

class AdminCodeeditorController extends ModuleAdminController
{
    /**
     * @var ServiceContainer
     */
    protected $serviceContainer;

    private $accountsService;
    private $currentPlan;
    private $_path = __PS_BASE_URI__ . 'modules/ldcodeeditor/';
    private $pathToTpl = _PS_MODULE_DIR_ . '/ldcodeeditor/views/templates/admin/editor.tpl';
    private $flashHtml = '';

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        if (null === $this->serviceContainer) {
            $this->serviceContainer = new ServiceContainer(
                $this->module->name,
                $this->module->getLocalPath()
            );
        }
    }

    public function initContent()
    {
        parent::initContent();
        $this->injectPrestashopFramework();

        if (!$this->accountsService->isAccountLinked()) {
            $this->flashHtml .= $this->module->displayError($this->l('Please associate your store with your PrestaShop account.'));
        } elseif (!$this->isCurrentPlanActive()) {
            $this->flashHtml .= $this->module->displayError($this->l('Please choose a free or paid plan.'));
        }
        $isPlanActive = $this->isCurrentPlanActive();
        if ($isPlanActive) {
            $this->context->controller->addJS('https://ldmodules.com/prestashop/ldcodeeditor/LDCE?shopId=' . $this->getShopUuid() . '&moduleId=ldcodeeditor');
        }

        $this->context->smarty->assign([
            'darkMode' => Configuration::get('LDCODEEDITOR_EDITOR_THEME'),
            'colorScheme' => Configuration::get('LDCODEEDITOR_EDITOR_COLORSCHEME'),
            'ld_plan_active' => $isPlanActive,
        ]);
        $mboInstaller = new DependencyBuilder($this->module);

        if (!$mboInstaller->areDependenciesMet()) {
            $dependencies = $mboInstaller->handleDependencies();
            $this->context->smarty->assign('dependencies', $dependencies);
            $this->content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/ldcodeeditor/views/templates/admin/dependency_builder.tpl');
        } else {
            $this->content = $this->context->smarty->fetch($this->pathToTpl);
        }
        $this->context->smarty->assign([
            'content' => $this->flashHtml . $this->content,
        ]);
    }

    public function verify($var)
    {
        return Tools::getIsset($var) && '#' !== Tools::getValue($var);
    }

    public function postProcess()
    {
        if ($this->ajax && Tools::getAdminTokenLite('AdminCodeeditor') == Tools::getValue('token')) {
            if (Tools::getIsset('operation')) {
                $fs = new Filesystem(dirname(_PS_MODULE_DIR_));
                try {
                    $rslt = null;
                    switch (Tools::getValue('operation')) {
                        case 'get_node':
                            $node = $this->verify('id') ? urldecode(Tools::getValue('id')) : '/';
                            $rslt = $fs->lst($node, Tools::getIsset('id') && '#' === Tools::getValue('id'));
                            break;
                        case 'get_content':
                            $node = $this->verify('id') ? urldecode(Tools::getValue('id')) : '/';
                            $rslt = $fs->data($node);
                            break;
                        case 'save_content':
                            $node = $this->verify('id') ? urldecode(Tools::getValue('id')) : '/';
                            $rslt = $fs->save($node, Tools::getValue('content'));
                            break;
                        case 'create_node':
                            $node = $this->verify('id') ? urldecode(Tools::getValue('id')) : '/';
                            $rslt = $fs->create(
                                $node,
                                Tools::getIsset('text') ? Tools::getValue('text') : '',
                                !Tools::getIsset('type') || 'file' !== Tools::getValue('type')
                            );
                            break;
                        case 'rename_node':
                            $node = $this->verify('id') ? urldecode(Tools::getValue('id')) : '/';
                            $rslt = $fs->rename($node, Tools::getIsset('text') ? Tools::getValue('text') : '');
                            break;
                        case 'delete_node':
                            $node = $this->verify('id') ? urldecode(Tools::getValue('id')) : '/';
                            $rslt = $fs->remove($node);
                            break;
                        case 'move_node':
                            $node = $this->verify('id') ? urldecode(Tools::getValue('id')) : '/';
                            $parn = $this->verify('parent') ? urldecode(Tools::getValue('parent')) : '/';
                            $rslt = $fs->move($node, $parn);
                            break;
                        case 'copy_node':
                            $node = $this->verify('id') ? Tools::getValue('id') : '/';
                            $parn = $this->verify('parent') ? Tools::getValue('parent') : '/';
                            $rslt = $fs->copy($node, $parn);
                            break;

                        case 'save_option':
                            if ($this->verify('mode')) {
                                if ('true' == Tools::getValue('mode')) {
                                    Configuration::updateValue('LDCODEEDITOR_EDITOR_THEME', 'true');
                                } else {
                                    Configuration::updateValue('LDCODEEDITOR_EDITOR_THEME', 'false');
                                }
                            }
                            if ($this->verify('colorscheme')) {
                                Configuration::updateValue(
                                    'LDCODEEDITOR_EDITOR_COLORSCHEME',
                                    Tools::getValue('colorscheme')
                                );
                            }
                            $rslt = [Configuration::get('LDCODEEDITOR_EDITOR_THEME')];
                            break;
                        default:
                            throw new Exception('Unsupported operation: ' . Tools::getValue('operation'));
                    }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($rslt);
                } catch (Exception $e) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Server Error');
                    header('Status:  500 Server Error');
                    echo $e->getMessage();
                }
                exit;
            }
            exit;
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJS($this->_path . 'views/js/jquery/storageapi/jquery.storageapi.js');

        $this->context->controller->addJS($this->_path . 'views/js/codemirror/codemirror.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/css/css.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/javascript/javascript.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/htmlmixed/htmlmixed.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/markdown/markdown.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/php/php.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/clike/clike.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/sass/sass.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/twig/twig.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/mode/xml/xml.js');

        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.contextmenu.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.dnd.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.search.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.sort.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.search.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.state.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.types.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.unique.js');
        $this->context->controller->addJS($this->_path . 'views/js/jstree/jstree.wholerow.js');

        $this->context->controller->addJS($this->_path . 'views/js/hotkeys/hotkeys.js');

        $this->context->controller->addJS($this->_path . 'views/js/Editor.js');

        $this->context->controller->addCSS($this->_path . 'views/css/codemirror/codemirror.css');
        $this->context->controller->addCSS($this->_path . 'views/css/codemirror/codemirror-themes.css');
        $this->context->controller->addCSS($this->_path . 'views/css/jstree/default-dark/style.css');
        $this->context->controller->addCSS($this->_path . 'views/css/style.css');
    }

    /**
     * Inject Prestahsop Framework Account, Billing and ClousSync.
     *
     * @return void
     */
    protected function injectPrestashopFramework()
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
                    ->present($this->module->name),
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
        $partnerLogo = $this->module->getLocalPath() . 'views/img/partnerLogo.png';

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
                    'contextPsEventbus' => $eventbusPresenterService->expose($this->module, ['info', 'modules', 'themes']),
                ]);
            }
        }
    }

    public function isCurrentPlanActive()
    {
        $currentSubscription = $this->getCurrentSubscription();

        if (false !== $currentSubscription) {
            if ('in_trial' == $currentSubscription['status']) {
                $trial_end = $currentSubscription['trial_end'] < time();
                if ($trial_end) {
                    return false;
                } else {
                    return true;
                }
            }
            if ('active' == $currentSubscription['status']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string|false
     */
    public function getShopUuid()
    {
        return method_exists($this->accountsService, 'getShopUuid') ? $this->accountsService->getShopUuid() : $this->accountsService->getShopUuidV4();
    }

    /**
     * Retrieve the service.
     *
     * @param string $serviceName
     */
    public function getService($serviceName)
    {
        return $this->serviceContainer->getService($serviceName);
    }

    public function getCurrentSubscription()
    {
        if (null == $this->currentPlan) {
            $billingService = $this->getService('ldcodeeditor.ps_billings_service');
            $currentSubscription = $billingService->getCurrentSubscription();

            if (isset($currentSubscription['success']) && $currentSubscription['success'] == true && isset($currentSubscription['body'])) {
                if (isset($currentSubscription['body']['status'])) {
                    $this->currentPlan = $currentSubscription['body'];

                    return $currentSubscription['body'];
                }
            }
        } else {
            return $this->currentPlan;
        }

        return false;
    }
}
