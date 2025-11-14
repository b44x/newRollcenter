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

use DynamicProduct\classes\helpers\DynamicCustomizationHelper;
use DynamicProduct\classes\models\DynamicInput;

/** @noinspection PhpUnused */
class DynamicProductProductsModuleFrontController extends ModuleFrontControllerCore
{
    /** @var ProductDesigner */
    public $module;

    public $auth = true;

    public function postProcess()
    {
        $action = Tools::getValue('action');
        if ($action === 'delete') {
            $id_input = (int) Tools::getValue('id_input');
            $input = new DynamicInput($id_input);
            if ($input->checkAuth()) {
                $input->delete();
                Tools::redirect($this->context->link->getModuleLink(
                    $this->module->name,
                    'products',
                    ['conf' => 4]
                ));
            }
            Tools::redirect($this->context->link->getModuleLink(
                $this->module->name,
                'products',
                ['conf' => 3]
            ));
        }
    }

    public function setMedia()
    {
        $this->context->controller->registerStylesheet(
            'dp-products',
            'modules/dynamicproduct/views/css/front/products.css'
        );

        return parent::setMedia();
    }

    public function initContent()
    {
        parent::initContent();

        $source = basename(__FILE__, '.php');
        $conf = (int) Tools::getValue('conf');
        $confirmations = [
            3 => [
                'type' => 'warning',
                'message' => $this->module->l('The customization could not be deleted', $source),
            ],
            4 => [
                'type' => 'success',
                'message' => $this->module->l('Customization deleted successfully', $source),
            ],
        ];

        $confirmation = $confirmations[$conf] ?? null;

        $customization_helper = new DynamicCustomizationHelper($this->module, $this->context);

        $this->context->smarty->assign([
            'module' => $this->module,
            'inputs' => $customization_helper->getCustomerProducts(
                $this->context->customer->id,
                $this->context->cookie->id_guest
            ),
            'confirmation' => $confirmation,
            'link' => $this->context->link,
        ]);

        $this->setTemplate('module:dynamicproduct/views/templates/front/products.tpl');
    }
}
