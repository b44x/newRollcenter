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
$hash = Tools::getValue('h');

if ($hash != Configuration::get(\Ifirma::API_HASH)) {
    Tools::redirectAdmin('index.php?controller=AdminOrders');
}

$pdfContent = \ifirma\ApiManager::getInstance()->getDocumentAsPdf($id);
if ($pdfContent === null) {
    // Add flash error
    Tools::redirect($_SERVER['HTTP_REFERER']);
}

$filename = \ifirma\ApiManager::getInstance()->getDocumentPdfName($id);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $pdfContent;
exit;