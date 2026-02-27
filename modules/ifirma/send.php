<?php

require_once '../../config/config.inc.php';
require_once 'ifirma.php';

// Initialize context
$context = Context::getContext();
$context->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
$context->currency->precision = 2;
$context->language = new Language(Configuration::get('PS_LANG_DEFAULT'));
$context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
$context->country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));

$id = Tools::getValue('id');
$type = Tools::getValue('type');
$hash = Tools::getValue('h');

if ($hash != Configuration::get(\Ifirma::API_HASH)) {
    Tools::redirectAdmin('index.php?controller=AdminOrders');
}

$sendResult = \ifirma\ApiManager::getInstance()->sendInvoice($id, $type);
\ifirma\InternalComunicationManager::getInstance()->{\ifirma\InternalComunicationManager::KEY_SEND_RESULT} = $sendResult;

Tools::redirect($_SERVER['HTTP_REFERER']);