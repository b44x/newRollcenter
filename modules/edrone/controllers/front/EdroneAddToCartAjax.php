<?php
/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EdroneEdroneAddToCartAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $info = NotificationTemp::getNotification($this->context->cookie->getName());

        die($info[0]);
    }
}
