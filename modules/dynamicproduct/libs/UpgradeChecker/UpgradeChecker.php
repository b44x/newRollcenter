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
namespace DynamicProduct\libs\UpgradeChecker;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;

class UpgradeChecker
{
    /** @var \DynamicProduct */
    private $module;
    /** @var \Context */
    private $context;

    private $messages = [];
    private $warnings = [];
    private $errors = [];

    private $last_upgrade = [
        'version' => '',
        'success' => false,
    ];

    public function __construct($module)
    {
        $this->context = \Context::getContext();
        $this->module = $module;
    }

    public function display()
    {
        $source = basename(__FILE__, '.php');

        $this->postProcess();
        $needs_upgrade = $this->needsUpgrade();
        $upgrades = $needs_upgrade ? [] : $this->getUpgrades();

        $is_active = \Module::isEnabled($this->module->name);
        if (!$is_active) {
            $this->warnings[] = $this->module->l(
                'The module is not active. You can activate it from the Modules Manager.',
                $source
            );
        }

        $version = $this->module->version;
        $array_search = array_search($version, array_column($upgrades, 'version'), true);
        if (!$array_search) {
            $upgrades[] = [
                'version' => $version,
                'success' => true,
            ];
        }

        $this->context->smarty->assign([
            'module' => $this->module,
            'module_link' => $this->getModuleLink(),
            'messages' => $this->messages,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
            'needs_upgrade' => $needs_upgrade,
            'upgrades' => $upgrades,
            'active' => $is_active,
        ]);

        return $this->context->smarty->fetch(dirname(__FILE__) . '/template.tpl');
    }

    public function needsUpgrade()
    {
        $source = basename(__FILE__, '.php');

        $this->module->database_version = $this->module->getDbVersion();

        if (\Module::needUpgrade($this->module)) {
            $this->warnings[] = $this->module->l('The module needs to be upgraded first', $source);

            return true;
        }

        return false;
    }

    public function getUpgrades()
    {
        $source = basename(__FILE__, '.php');

        $db_version = $this->module->getDbVersion();
        if (version_compare($db_version, $this->module->version, '<')) {
            $this->warnings[] = $this->module->l('The module needs to be upgraded first', $source);

            return [];
        }
        $upgrades = $this->module->getUpgrades();
        foreach ($upgrades as $index => &$upgrade) {
            $content = \Tools::file_get_contents($upgrade['file']);
            if (isset($upgrade['upgrade_function'][0])) {
                $check_function = str_replace('upgrade_module', 'check_upgrade', $upgrade['upgrade_function'][0]);
                $version = $upgrade['version'];
                if (function_exists($check_function)) {
                    $upgrade['duplicate'] = true;
                    if ($this->last_upgrade['version'] === $version) {
                        $upgrade['success'] = $this->last_upgrade['success'];
                    }
                    continue;
                }

                if (!function_exists($upgrade['upgrade_function'][0])) {
                    include $upgrade['file'];
                }

                $upgrade['success'] = true;
                if (function_exists($check_function)) {
                    $upgrade['success'] = $check_function($this->module);
                } else {
                    $checker_file = $this->module->installer->getUpgradeCheckerFile($version);
                    if (is_file($checker_file)) {
                        $upgrade['success'] = $this->module->installer->checkUpgrade($version);
                    } else {
                        unset($upgrades[$index]);
                        continue;
                    }
                }
                if (!$upgrade['success']) {
                    $upgrade['retry_link'] = DynamicTools::addQueryToUrl(
                        $this->module->provider->getModuleAdminLink('view_upgrade_checker'),
                        [
                            'retry' => $version,
                        ]
                    ) . "#version-{$version}";
                }
            }
        }

        return $upgrades;
    }

    private function postProcess()
    {
        $source = basename(__FILE__, '.php');
        if (\Tools::isSubmit('activate')) {
            $success = $this->module->enable();
            if ($success) {
                $this->messages[] = $this->module->l('The module has been activated', $source);
                $this->messages = array_merge($this->messages, $this->module->getConfirmations());
            } else {
                $this->errors[] = $this->module->l('The module could not be activated', $source);
                $this->errors = array_merge($this->errors, $this->module->getErrors());
            }
        }

        if (\Tools::isSubmit('upgrade')) {
            $this->module->installed = true;
            $this->module->database_version = $this->module->getDbVersion();
            \Module::initUpgradeModule($this->module);
            \Module::needUpgrade($this->module);
            \Module::getInstanceByName($this->module->name);
            $upgrade = $this->module->runUpgradeModule();
            if (isset($upgrade['version_fail']) && $upgrade['version_fail']) {
                $this->errors[] = $this->module->l('The module upgrade failed, please retry from the Modules Manager', $source);
                $this->errors = array_merge($this->errors, $this->module->getErrors());
            } else {
                $this->messages[] = $this->module->l('The module was upgraded successfully', $source);
                $this->messages = array_merge($this->messages, $this->module->getConfirmations());
            }

            return;
        }

        if (\Tools::getIsset('retry')) {
            $version = \Tools::getValue('retry');
            $upgrades = $this->module->getUpgrades();
            $index = array_search($version, array_column($upgrades, 'version'));
            if ($index) {
                $upgrade = $upgrades[$index];
                if (isset($upgrade['upgrade_function'][0])) {
                    $upgrade_function = $upgrade['upgrade_function'][0];

                    if (!function_exists($upgrade_function)) {
                        include $upgrade['file'];
                    }

                    if (function_exists($upgrade_function)) {
                        $success = $upgrade_function($this->module);

                        $check_function = str_replace(
                            'upgrade_module',
                            'check_upgrade',
                            $upgrade['upgrade_function'][0]
                        );

                        if (function_exists($check_function)) {
                            $success = $check_function($this->module);
                        }
                        $this->last_upgrade = [
                            'version' => $version,
                            'success' => $success,
                        ];

                        if ($success) {
                            $database_version = $this->module->getDbVersion();

                            if (version_compare($version, $database_version, '>')) {
                                \Db::getInstance()->execute(
                                    'UPDATE ' . _DB_PREFIX_ . "module 
                                    SET version='" . pSQL($version) . "' 
                                    WHERE name='" . pSQL($this->module->name) . "'"
                                );
                            }
                        }

                        if ($success) {
                            $this->messages[] = $this->module->l('Upgrade succeeded', $source) . " ({$version})";
                        } else {
                            $this->errors[] = $this->module->l('Upgrade failed', $source) . " ({$version})";
                        }
                    }
                }
            }
        }
    }

    private function getModuleLink()
    {
        $token = \Tools::getAdminTokenLite('AdminModules');

        return 'index.php?controller=AdminModules&token=' . $token . '&configure=' . $this->module->name;
    }
}
