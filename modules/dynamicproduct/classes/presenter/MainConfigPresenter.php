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
namespace DynamicProduct\classes\presenter;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\DynamicMainConfig;
use DynamicProduct\classes\module\DynamicPresenter;

class MainConfigPresenter extends DynamicPresenter
{
    protected $model = 'config';

    protected function getFormFields()
    {
        $source = DynamicTools::getSource();

        $switch_debug_mode = [
            'type' => 'switch',
            'label' => $this->module->l('Debug mode', $source),
            'desc' => $this->module->l('Enable this option to debug formulas in the browser console', $source),
            'name' => 'debug_mode',
            'values' => [
                [
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->module->l('Yes', $source),
                ],
                [
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->module->l('No', $source),
                ],
            ],
        ];

        return [
            'form' => [
                'input' => [
                    $switch_debug_mode,
                ],
                'buttons' => [
                    [
                        'title' => $this->module->l('Troubleshooting', $source),
                        'href' => $this->module->provider->getModuleAdminLink('view_troubleshooter'),
                        'class' => 'pull-left btn-success',
                    ],
                    [
                        'title' => $this->module->l('Upgrade verification', $source),
                        'href' => $this->module->provider->getModuleAdminLink('view_upgrade_checker'),
                        'class' => 'pull-left tn-margin-left',
                    ],
                ],
            ],
        ];
    }

    protected function getFormValues()
    {
        $result = [];
        $values = DynamicMainConfig::getConfigValues();
        foreach ($values as $name => $value) {
            $result[$name] = $value;
        }

        return $result;
    }
}
