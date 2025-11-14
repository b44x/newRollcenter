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
namespace DynamicProduct\classes\helpers;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\models\DynamicEquation;
use DynamicProduct\classes\models\DynamicInput;

class DynamicProductCost
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function updateCost($id_order)
    {
        $order_details = \OrderDetail::getList($id_order);
        foreach ($order_details as $order_detail) {
            $id_product = (int) $order_detail['product_id'];

            $id_customization = (int) $order_detail['id_customization'];
            if (!$id_customization) {
                continue;
            }

            $dynamic_input = DynamicInput::getInputByCustomization($id_customization);
            if (!$dynamic_input) {
                continue;
            }

            $cost_equation = DynamicEquation::getEquationByIdFormula($id_product, DynamicEquation::_DP_COST_EQ_);
            if (!\Validate::isLoadedObject($cost_equation)) {
                continue;
            }

            $formula = $cost_equation->formula;
            if (!$formula) {
                continue;
            }
            $cost = DynamicEquation::evaluateFormula($formula, $dynamic_input->getInputFields(), 'Cost formula');
            \Db::getInstance()->update(
                'order_detail',
                ['purchase_supplier_price' => (float) $cost],
                'id_order_detail = ' . (int) $order_detail['id_order_detail']
            );
        }
    }
}
