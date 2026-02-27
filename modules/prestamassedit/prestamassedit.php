<?php
/**
 * 2008-2025 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2025 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

include 'classes/PrestaMassEditClasses.php';

class PrestaMassEdit extends Module
{
    public function __construct()
    {
        $this->name = 'prestamassedit';
        $this->tab = 'others';
        $this->version = '7.0.3';
        $this->author = 'presta_world';
        parent::__construct();
        $this->bootstrap = true;
        $this->module_key = 'cb9f251cd645a8218c7d6a262b35bb6f';
        $this->displayName = $this->trans('Product Catalog – Bulk Edit | Mass Edit | Quick Edit', [], 'Modules.Prestamassedit.Admin');
        $this->description = $this->trans('Make swift changes, bulk edits, customizable product listings, instant filters,
            and more! Product Catalog Manager, a PrestaShop module, provides unparalleled efficiency
            and effectiveness in product management tools.', [], 'Modules.Prestamassedit.Admin');
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Prestamassedit.Admin');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (!parent::install() || !$this->callInstallTab()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }
        return true;
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPrestaMassEditManager'));
    }

    public function callInstallTab()
    {
        $this->installTab('AdminPrestaMassEditManager', 'Mass Edit', 'AdminCatalog');
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = $class_name;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }
        if ($tab_parent_name) {
            // $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
            $tabRepository = $this->getContainer()->get('prestashop.core.admin.tab.repository');
            $tab->id_parent = (int) $tabRepository->findOneIdByClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;
        return $tab->add();
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }
}
