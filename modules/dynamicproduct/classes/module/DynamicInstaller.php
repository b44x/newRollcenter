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
namespace DynamicProduct\classes\module;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\helpers\FolderHelper;

class DynamicInstaller
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    private $hooks_front = [
        'displayHeader',
        'actionFrontControllerInitBefore',
        'displayProductAdditionalInfo',
        'actionValidateOrder',
        'displayProductPriceBlock',
        'displayCustomization',
        'displayPaymentTop',
        'displayBeforeShoppingCartBlock',
        'displayShoppingCart',
        'actionCartSave',
        'actionCartUpdateQuantityBefore',
        'displayCustomerAccount',
        'addWebserviceResources',
        'actionProductSearchProviderRunQueryAfter',
    ];
    private $hooks_admin = [
        'displayBackOfficeHeader',
        'displayAdminProductsExtra',
        'actionProductAdd',
        'actionProductSave',
        'actionAdminControllerSetMedia',
        'actionOrderStatusPostUpdate',
        'actionClearCompileCache',
        'actionProductGridDefinitionModifier',
        'actionProductGridQueryBuilderModifier',
        'actionProductGridDataModifier',
        'actionOrderGridDefinitionModifier',
        'actionOrderGridQueryBuilderModifier',
        'actionOrderGridDataModifier',
    ];

    private static $controllers = [
        [
            'name' => 'Dynamic Main Config',
            'class' => 'DynamicMainConfig',
        ],
        [
            'name' => 'Dynamic Product Settings',
            'class' => 'DynamicProductSettings',
        ],
        [
            'name' => 'Dynamic Product Equations',
            'class' => 'DynamicProductEquations',
        ],
        [
            'name' => 'Dynamic Preset Equations',
            'class' => 'DynamicPresetEquations',
        ],
        [
            'name' => 'Dynamic Product Fields',
            'class' => 'DynamicProductFields',
        ],
        [
            'name' => 'Dynamic Product Fields Settings',
            'class' => 'DynamicProductFieldsSettings',
        ],
        [
            'name' => 'Dynamic Product Fields Options',
            'class' => 'DynamicProductFieldsOptions',
        ],
        [
            'name' => 'Dynamic Product Combinations',
            'class' => 'DynamicProductCombinations',
        ],
        [
            'name' => 'Dynamic Product Visibility',
            'class' => 'DynamicProductVisibility',
        ],
        [
            'name' => 'Dynamic Product Proportions',
            'class' => 'DynamicProductProportions',
        ],
        [
            'name' => 'Dynamic Product Conditions',
            'class' => 'DynamicProductConditions',
        ],
        [
            'name' => 'Dynamic Product Field Formulas',
            'class' => 'DynamicProductFieldFormulas',
        ],
        [
            'name' => 'Dynamic Product',
            'class' => 'DpRedirect',
            'parent' => 'AdminParentModulesSf',
        ],
        [
            'name' => 'Dynamic Product Intervals',
            'class' => 'DynamicProductIntervals',
        ],
        [
            'name' => 'Dynamic Product Grids',
            'class' => 'DynamicProductGrids',
        ],
        [
            'name' => 'Dynamic Product Exec Order',
            'class' => 'DynamicProductExecOrder',
        ],
        [
            'name' => 'Dynamic Product Field Groups',
            'class' => 'DynamicProductFieldGroups',
        ],
        [
            'name' => 'Dynamic Product Steps',
            'class' => 'DynamicProductSteps',
        ],
        [
            'name' => 'Dynamic Product Dev',
            'class' => 'DynamicProductDev',
        ],
        [
            'name' => 'Dynamic Product CSV',
            'class' => 'DynamicProductCSV',
        ],
        [
            'name' => 'Dynamic Calculation Items',
            'class' => 'DynamicCalculationItems',
        ],
    ];

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getHooks()
    {
        return array_merge($this->hooks_front, $this->hooks_admin);
    }

    public function getFrontHooks()
    {
        return $this->hooks_front;
    }

    public function installHooks()
    {
        foreach ($this->getHooks() as $hook) {
            $this->module->registerHook($hook);
        }

        return true;
    }

    public function uninstallHooks()
    {
        foreach ($this->getHooks() as $hook) {
            $this->module->unregisterHook($hook);
        }

        return true;
    }

    public function installControllers()
    {
        $success = true;
        foreach (self::$controllers as $controller) {
            $success &= $this->installController($controller);
        }

        return $success;
    }

    public function installController($controller)
    {
        $languages = \Language::getLanguages();
        $tab = new \Tab();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $controller['name'];
        }
        $tab->class_name = $controller['class'];
        if (isset($controller['parent'])) {
            $tab->id_parent = $this->module->provider->getTabID($controller['parent']);
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->module->name;
        $tab->active = 1;

        return $tab->add();
    }

    public function uninstallController($name)
    {
        $tab = new \Tab((int) \Tab::getIdFromClassName($name));

        return $tab->delete();
    }

    public function uninstallControllers()
    {
        $success = true;
        foreach (self::$controllers as $controller) {
            $success &= $this->uninstallController($controller['class']);
        }

        return $success;
    }

    public function installDataDir()
    {
        $result = true;

        $folders = [
            '',
            'allocations',
            'calculator',
            'declarations',
            'scripts',
            'databases',
            'upload',
            'upload_keep',
            'cache',
            'images',
            'images/field',
            'images/thumbnails',
            'images/radio',
            'images/dropdown',
            'images/preview',
        ];

        $index_file = $this->module->getDir() . 'index.php';

        foreach ($folders as $folder) {
            $dir = $this->module->provider->getDataDir($folder);
            if (!is_dir($dir)) {
                $result &= mkdir($dir);
                copy($index_file, $dir . 'index.php');
            }
        }

        $files = [
            'install/calculator/.htaccess' => 'calculator/.htaccess',
            'install/calculator/readme.txt' => 'calculator/readme.txt',

            'install/allocations/.htaccess' => 'allocations/.htaccess',

            'install/declarations/.htaccess' => 'declarations/.htaccess',

            'install/databases/.htaccess' => 'databases/.htaccess',
            'install/databases/readme.txt' => 'databases/readme.txt',

            'install/cache/.htaccess' => 'cache/.htaccess',

            'install/upload_keep/readme.txt' => 'upload_keep/readme.txt',
        ];

        foreach ($files as $src => $dest) {
            $file_path = $this->module->getDir() . $src;
            if (is_file($file_path)) {
                $destination = $this->module->provider->getDataFile($dest);
                if (!is_file($destination)) {
                    copy($file_path, $destination);
                }
            }
        }

        return $result;
    }

    public function copyFiles()
    {
        $folders = [
            'allocations' => 'allocations',
            'calculator' => 'calculator',
            'declarations' => 'declarations',
            'scripts' => 'scripts',
            'upload' => 'upload',
            'views/img/field' => 'images/field',
            'views/img/thumbnails' => 'images/thumbnails',
        ];

        $folder_helper = new FolderHelper($this->module, $this->context);

        foreach ($folders as $src => $dest) {
            $src = $this->module->getFolderPath($src);
            $dest = $this->module->provider->getDataDir($dest);
            $folder_helper->copyFolder($src, $dest);
        }

        return true;
    }

    public function execSQLFile($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        if (!$sql = \Tools::file_get_contents($path)) {
            return false;
        }
        $success = true;
        $sql = str_replace(
            ['ps_dynamicproduct', 'ps_tunisoft', 'InnoDb'],
            [_DB_PREFIX_ . $this->module->name, _DB_PREFIX_ . 'tunisoft', _MYSQL_ENGINE_],
            $sql
        );
        $sql = preg_split('/;\s*[\r\n]+/', $sql);
        foreach ($sql as $query) {
            if (trim($query)) {
                try {
                    $success &= \Db::getInstance()->execute(trim($query));
                } catch (\Exception|\Throwable $e) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    public function execUninstallScript()
    {
        $success = true;
        $uninstall_script = $this->module->getDir() . 'sql/tables.json';
        $contents = \Tools::file_get_contents($uninstall_script);
        $tables = json_decode($contents, true);
        if (is_array($tables)) {
            foreach ($tables as $table) {
                $table = $this->restoreTableName($table);
                $sql = 'DROP TABLE IF EXISTS `' . pSQL($table) . '`;';
                $sql = str_replace('ps_dynamicproduct', pSQL(_DB_PREFIX_ . $this->module->name), $sql);
                $success &= \Db::getInstance()->execute($sql);
            }
        }

        return $success;
    }

    private function restoreTableName($table)
    {
        if (!DynamicTools::isModuleDevMode() && \Tools::substr($table, 0, 1) === '_') {
            $table = \Tools::substr($table, 1);
        }

        return $table;
    }

    public function upgradeSQL($version)
    {
        $path = $this->module->getFolderPath('upgrade/sql') . "upgrade-{$version}.sql";

        return $this->execSQLFile($path);
    }

    public function checkUpgrade($version)
    {
        $path = $this->getUpgradeCheckerFile($version);

        try {
            return $this->execSQLFile($path);
        } catch (\Exception|\Throwable $e) {
            return false;
        }
    }

    /**
     * @param $version
     *
     * @return string
     */
    public function getUpgradeCheckerFile($version)
    {
        return $this->module->getFolderPath('upgrade/sql') . "upgrade-{$version}-check.sql";
    }
}
