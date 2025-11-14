<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

class LeoElementsCartModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /**
    * @see FrontController::initContent()
    */
    public function initContent()
    {
        die('LeoElementsCartModuleFrontController');
        parent::initContent();

        $modal = null;

		if(ob_get_contents()){
			ob_end_clean();
		}
        header('Content-Type: application/json');
        die(json_encode([
            'preview' => $this->renderWidget(['cart' => $this->context->cart]),
        ]));
    }
	
    public function renderWidget(array $params)
    {
        if (Configuration::isCatalogMode()) {
            return;
        }
		
		$template = LEOELEMENTS_PATH . 'views/templates/widgets/cart.tpl';

        $this->context->smarty->assign($this->getWidgetVariables($params));

        return $this->context->smarty->fetch($template);
    }

    public function getWidgetVariables(array $params)
    {
        $cart_url = $this->getCartSummaryURL();

        return array(
            'cart' => (new PrestaShop\PrestaShop\Adapter\Cart\CartPresenter())->present(isset($params['cart']) ? $params['cart'] : $this->context->cart),
            'cart_url' => $cart_url,
			'has_ajax' => (bool)Configuration::get('PS_BLOCK_CART_AJAX')
        );
    }
	
    private function getCartSummaryURL()
    {
        return $this->context->link->getPageLink(
            'cart',
            null,
            $this->context->language->id,
            array(
                'action' => 'show'
            ),
            false,
            null,
            true
        );
    }
}
