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

class LeoElementsContactModuleFrontController extends ModuleFrontController
{
	
    public function init()
    {
        parent::init();
        die('LeoElementsContactModuleFrontController');
    }

    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
		
		$notifications = false;

        if (Tools::isSubmit('submitMessage') || $this->ajax) {
			if(Module::isEnabled('contactform'))
			{
				$module = Module::getInstanceByName('contactform');
				$module->sendMessage();

				if (!empty($this->context->controller->errors)) {
					$notifications['messages'] = $this->context->controller->errors;
					$notifications['nw_error'] = true;
				} elseif (!empty($this->context->controller->success)) {
					$notifications['messages'] = $this->context->controller->success;
					$notifications['nw_error'] = false;
				}

				if ($this->ajax) {
					header('Content-Type: application/json');
					$this->ajaxDie(json_encode($notifications));
				}

			}
        }
		
		die();
    }
}
