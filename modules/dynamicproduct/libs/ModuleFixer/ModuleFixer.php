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
/**
 * 2007-2023 TuniSoft
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
 * @copyright 2007-2023 TuniSoft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace DynamicProduct\libs\ModuleFixer;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\models\DynamicInput;
use DynamicProduct\classes\models\DynamicMainConfig;
use DynamicProduct\libs\UpgradeChecker\UpgradeChecker;

class ModuleFixer
{
    /** @var \DynamicProduct */
    private $module;
    /** @var \Context */
    private $context;

    private $errors = [];

    public function __construct($module)
    {
        $this->context = \Context::getContext();
        $this->module = $module;
    }

    public function display()
    {
        $this->postProcess();
        $module_hooks = $this->getModuleHooks();
        $unregistered_hooks = $this->getHooksList($module_hooks);
        $this->context->smarty->assign([
            'module' => $this->module,
            'config' => DynamicMainConfig::getConfig(),
            'module_link' => $this->getModuleLink(),
            'module_hooks' => $module_hooks,
            'unregistered_hooks' => $unregistered_hooks,
            'errors' => $this->errors,
            'templates_fixed' => $this->areTemplatesFixed(),
            'cron_link' => $this->context->link->getModuleLink(
                $this->module->name,
                'cleanup',
                ['min_age' => '_min_age_', 'cron_key' => '_cron_key_', 'action' => 'cleanup']
            ),
        ]);

        return $this->context->smarty->fetch(dirname(__FILE__) . '/ModuleFixer.tpl');
    }

    private function getModuleHooks(): array
    {
        return $this->module->installer->getHooks();
    }

    private function getHooksList($module_hooks)
    {
        $hooks_list = [];

        foreach ($module_hooks as $hook_name) {
            if (!\Hook::isModuleRegisteredOnHook($this->module, $hook_name, $this->context->shop->id)) {
                $id_hook = (int) \Hook::getIdByName($hook_name);
                $hook = new \Hook($id_hook, $this->context->language->id);
                $hooks_list[] = [
                    'id_hook' => $id_hook,
                    'name' => $hook_name,
                    'description' => $hook->description,
                ];
            }
        }

        return $hooks_list;
    }

    private function postProcess()
    {
        if (\Tools::isSubmit('restore_hooks')) {
            $this->restoreHooks();
        }
        if (\Tools::isSubmit('fix_templates')) {
            $this->fixTemplates();
        }
        if (\Tools::isSubmit('cleanup')) {
            $this->cleanUp();
        }
    }

    private function restoreHooks()
    {
        $hooks = (array) \Tools::getValue('hooks');
        if (is_array($hooks)) {
            $hook_names = array_keys($hooks);
            foreach ($hook_names as $hook_name) {
                if ($hook_name) {
                    $this->module->registerHook($hook_name);
                }
            }
        }
    }

    public function fixTemplates()
    {
        $fixes = $this->getFixes();

        foreach ($fixes as $fix) {
            if (is_file($fix['path'])) {
                $contents = \Tools::file_get_contents($fix['path']);
                if ($contents) {
                    $new_contents = str_replace($fix['search'], $fix['replace'], $contents);
                    file_put_contents($fix['path'], $new_contents);
                }
            }
        }
    }

    private function areTemplatesFixed()
    {
        $fixes = $this->getFixes();
        foreach ($fixes as $fix) {
            if (is_file($fix['path'])) {
                $contents = \Tools::file_get_contents($fix['path']);
                if (strpos($contents, $fix['replace']) === false) {
                    if (strpos($contents, $fix['search']) === false) {
                        continue;
                    }

                    return false;
                }
            }
        }

        return true;
    }

    private function getFixes(): array
    {
        return [
            [
                'path' => _PS_ROOT_DIR_ .
                    '/src/PrestaShopBundle/Resources/views/Admin/Sell/Order/Cart/Blocks/View/cart_summary.html.twig',
                'search' => '{{ customizationField.value }}',
                'replace' => '{{ customizationField.value | raw }}',
            ],
            [
                'path' => _PS_ROOT_DIR_ .
                    '/src/PrestaShopBundle/Resources/views/Admin/Sell/Order/Order/Blocks/View/product.html.twig',
                'search' => '{{ customization.value }}',
                'replace' => '{{ customization.value | raw }}',
            ],
            [
                'path' => _PS_ROOT_DIR_ .
                    '/mails/_partials/order_conf_product_list.tpl',
                'search' => "{\$customization['customization_text']}",
                'replace' => "{\$customization['customization_text'] nofilter}",
            ],
            [
                'path' => _PS_ROOT_DIR_ .
                    '/mails/en/order_conf_product_list.tpl',
                'search' => "{\$customization['customization_text']}",
                'replace' => "{\$customization['customization_text'] nofilter}",
            ],
            [
                'path' => _PS_THEME_DIR_ .
                    '/mails/en/order_conf_product_list.tpl',
                'search' => "{\$customization['customization_text']}",
                'replace' => "{\$customization['customization_text'] nofilter}",
            ],
            [
                'path' => _PS_ROOT_DIR_ .
                    '/modules/ets_ordermanager/views/PrestaShop/Admin/Sell/Order/Order/Blocks/View/product.html.twig',
                'search' => '{{ customization.value }}',
                'replace' => '{{ customization.value | raw }}',
            ],
            [
                'path' => _PS_ROOT_DIR_ .
                    '/modules/ets_ordermanager/views/templates/admin/_configure/templates/orders/_customized_data.tpl',
                'search' => '{$data[\'value\']|escape:\'html\':\'UTF-8\'}',
                'replace' => '{$data[\'value\']}',
            ],
            [
                'path' => _PS_ROOT_DIR_ .
                    '/modules/orderedit/views/PrestaShop/Admin/Sell/Order/Order/Blocks/View/product.html.twig',
                'search' => '{{ customization.value }}',
                'replace' => '{{ customization.value | raw }}',
            ],
            [
                'path' => _PS_ROOT_DIR_ .
                    '/modules/pms_admin_order/views/Module/Admin/Sell/Order/Order/Blocks/View/product.html.twig',
                'search' => '{{ customization.value }}',
                'replace' => '{{ customization.value | raw }}',
            ],
        ];
    }

    private function getModuleLink()
    {
        $token = \Tools::getAdminTokenLite('AdminModules');

        return 'index.php?controller=AdminModules&token=' . $token . '&configure=' . $this->module->name;
    }

    public function displayDiagnostics()
    {
        $templates_fixed = $this->areTemplatesFixed();

        $has_failed_upgrades = false;
        $upgrade_checker = new UpgradeChecker($this->module);
        $upgrades = $upgrade_checker->getUpgrades();
        $failed_upgrades = in_array(false, array_column($upgrades, 'success'));
        if ($failed_upgrades) {
            $has_failed_upgrades = true;
        }
        $needs_upgrade = $upgrade_checker->needsUpgrade();
        $display_diagnostics = !$templates_fixed || $has_failed_upgrades || $needs_upgrade;

        $this->context->smarty->assign([
            'templates_fixed' => $templates_fixed,
            'has_failed_upgrades' => $has_failed_upgrades,
            'needs_upgrade' => $needs_upgrade,
            'module_link' => $this->getModuleLink(),
        ]);

        if (!$display_diagnostics) {
            return null;
        }

        return $this->context->smarty->fetch(dirname(__FILE__) . '/diagnostics.tpl');
    }

    public function cleanUp()
    {
        $start_time = microtime(true);
        $max_execution_time = (int) ini_get('max_execution_time');

        $min_age = \Tools::getValue('min_age');

        $saved_inputs_delete_count = 0;

        $old_inputs = \Db::getInstance()->executeS('SELECT i.id_input FROM ' . _DB_PREFIX_ . 'dynamicproduct_input i
                LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_customization = i.id_customization
                WHERE i.date_upd < NOW() - INTERVAL ' . (int) $min_age . ' DAY
                AND ISNULL(od.id_order_detail) AND NOT i.is_admin AND NOT i.is_bookmarked;');

        foreach ($old_inputs as $old_input) {
            $id_input = (int) $old_input['id_input'];
            $input = new DynamicInput($id_input);
            if (\Validate::isLoadedObject($input)) {
                $input->delete();
                ++$saved_inputs_delete_count;
            }

            $elapsed_time = (microtime(true) - $start_time);
            if ($elapsed_time > $max_execution_time - 1) {
                $this->context->smarty->assign([
                    'time_limit_exceeded' => true,
                    'min_age' => $min_age,
                    'saved_inputs_delete_count' => $saved_inputs_delete_count,
                ]);

                return [
                    'success' => true,
                    'saved_inputs_delete_count' => $saved_inputs_delete_count,
                ];
            }
        }

        $this->context->smarty->assign([
            'min_age' => $min_age,
            'saved_inputs_delete_count' => $saved_inputs_delete_count,
        ]);

        return [
            'success' => true,
            'saved_inputs_delete_count' => $saved_inputs_delete_count,
        ];
    }
}
