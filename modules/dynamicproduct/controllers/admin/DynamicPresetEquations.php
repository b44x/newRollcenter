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

use DynamicProduct\classes\DynamicTools;
use DynamicProduct\classes\models\DynamicPresetEquation;

class DynamicPresetEquationsController extends ModuleAdminController
{
    /** @var DynamicProduct */
    public $module;
    public $action;

    /** @var Context */
    public $context;
    public $id_product;
    public $id_default_lang;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->action = Tools::getValue('action');
        $this->id_product = (int) Tools::getValue('id_product');
        $this->id_default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
    }

    public function postProcess()
    {
        $source = basename(__FILE__, '.php');
        $restricted = DynamicTools::getRestricted('_DP_RESTRICTED_');
        if ((int) $this->context->employee->id_profile !== 1 && in_array($this->id_product, $restricted, false)) {
            exit(json_encode([
                'error' => true,
                'message' => $this->module->l('This product is for viewing only!', $source),
            ]));
        }

        $method = 'process' . Tools::toCamelCase($this->action, true);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        exit;
    }

    private function processLoadEquations()
    {
        $this->respond([
            'equations' => DynamicPresetEquation::getAll(),
        ]);
    }

    private function processStarEquation()
    {
        $source = basename(__FILE__, '.php');
        $formula = Tools::getValue('formula');

        // check if formula already exists
        $row = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . $this->module->name . '_preset_equation` 
        WHERE `formula` = "' . pSQL($formula) . '"');

        if ($row) {
            $this->respond([
                'error' => true,
                'message' => $this->module->l('This formula already exists!', $source),
            ]);
        }

        $equation = new DynamicPresetEquation();
        $equation->formula = $formula;
        $equation->save();

        $this->respond();
    }

    public function processDelete()
    {
        $id = (int) Tools::getValue('id');

        $equation = new DynamicPresetEquation($id);
        $equation->delete();

        $this->respond([
            'equations' => DynamicPresetEquation::getAll(),
        ]);
    }

    public function respond($data = [], $success = 1)
    {
        $success = $success && (int) !array_key_exists('error', $data);
        $arr = [
            'success' => $success,
        ];
        $arr = array_merge($arr, $data);
        exit(json_encode($arr));
    }
}
