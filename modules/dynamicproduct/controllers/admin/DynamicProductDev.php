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

/** @noinspection PhpUnusedPrivateMethodInspection */
class DynamicProductDevController extends ModuleAdminController
{
    /** @var DynamicProduct */
    public $module;

    public $bootstrap = true;

    private $id_product;

    public function __construct()
    {
        parent::__construct();
        $controller_name = 'AdminProducts';
        $_GET['controller'] = $_POST['controller'] = $_REQUEST['controller'] = $controller_name;
        $this->context->controller->controller_name = $controller_name;
        $this->id_product = (int) Tools::getValue('id_product');
    }

    public function setMedia($isNewTheme = true)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin(['fancybox']);
        $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        $this->addJS(_PS_JS_DIR_ . 'admin/tinymce_loader.js');
    }

    public function initContent()
    {
        $extra_content = $this->module->hookDisplayAdminProductsExtra(['id_product' => $this->id_product]);
        $cls = "module-render-container module-{$this->module->name}";
        $this->context->smarty->assign([
            'id_product' => $this->id_product,
            'module_name' => $this->module->name,
            'cls' => $cls,
            'extra_content' => $extra_content,
        ]);

        $content = $this->context->smarty->fetch($this->module->getDir() . 'views/templates/admin/product-dev.tpl');

        $this->context->smarty->assign([
            'content' => $content,
        ]);
    }
}
