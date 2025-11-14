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
namespace DynamicProduct\classes\controllers\front;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DynamicFrontController extends \ModuleFrontController
{
    /** @var \DynamicProduct */
    public $module;
    protected $action;
    protected $id_product;
    protected $id_attribute;

    public function __construct()
    {
        parent::__construct();
        $this->context = \Context::getContext();
        $this->action = \Tools::getValue('action');
        $this->id_product = (int) \Tools::getValue('id_product');
        $this->id_attribute = $this->getAttributeID();
    }

    private function getAttributeID()
    {
        $id_attribute = (int) \Tools::getValue('id_attribute');
        $attributes = \Tools::getValue('attributes');
        if ($attributes) {
            $id_attribute = $this->module->provider->getAttributeID($this->id_product, $attributes);
        }

        return $id_attribute;
    }

    public function initContent()
    {
        if (empty($this->action)) {
            exit('no_action');
        }
        $method = 'process' . \Tools::toCamelCase($this->action, true);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }
        exit;
    }

    protected function respond($data = [], $success = 1)
    {
        if (array_key_exists('error', $data)) {
            $success = 0;
        }
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
