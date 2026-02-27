<?php
/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EdroneEdroneUserSessionDataModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        header("Cache-Control: no-cache, max-age=0");
        header("Content-type: application/json");

        $userData = array_merge(
            $this->getCommonData(),
            $this->getCategoryData(),
            $this->getContextData(),
            $this->getTmpFileData()
        );

        $callableReturn = method_exists('Tools', 'jsonEncode') // Support for PS 8
                            ? 'Tools::jsonEncode'
                            : 'json_encode';

        die($callableReturn($userData));
    }

    private function getCommonData(): array
    {
        return [
            'version' => '1.2.3',
            'platform_version' => _PS_VERSION_,
        ];
    }

    private function getCategoryData()
    {
        $userData = [];

        $categoryId = (int) Tools::getValue('id_category');

        if ($categoryId > 0) {
            $category = Category::getCategoryInformation([$categoryId]);
            $userData['category_id'] = $categoryId;
            $userData['category_name'] = $category[$categoryId]["name"];
        }

        return $userData;
    }

    private function getContextData(): array
    {
        $context = Context::getContext();
        $fileData = $this->getTmpFileData();

        return [
            'email' => !empty($fileData['email']) ? $fileData['email'] : ($context->customer->email ?? ''),
            'last_name' => !empty($fileData['last_name']) ? $fileData['last_name'] : ($context->customer->lastname ?? ''),
            'first_name' => !empty($fileData['first_name']) ? $fileData['first_name'] : ($context->customer->firstname ?? ''),
            'country' => !empty($context->customer->geoloc_id_country) ? $context->customer->geoloc_id_country : '',
            'shop_lang' => !empty($fileData['shop_lang']) ? $fileData['shop_lang'] : $context->language->id,
            'app_id' => !empty($context->language->language_code) && Configuration::get(Edrone::APP_ID_FIELD . '_' . $context->language->language_code)
                ? Configuration::get(Edrone::APP_ID_FIELD . '_' . $context->language->language_code)
                : Configuration::get(Edrone::APP_ID_FIELD)
        ];
    }

    private function getTmpFileData(): array
    {
        $context = Context::getContext();
        $fileDataTmp = NotificationTemp::getNotification($context->cookie->getName());

        if (empty($fileDataTmp)) {
            return [];
        }

        $fileData = json_decode($fileDataTmp[0], true);

        return [
            'first_name' => !empty($fileData['first_name']) ? $fileData['first_name'] : '',
            'last_name' => !empty($fileData['last_name']) ? $fileData['last_name'] : '',
            'email' => !empty($fileData['email']) ? $fileData['email'] : '',
            'coupon' => !empty($fileData['coupon']) ? $fileData['coupon'] : '',
            'product_ids' => !empty($fileData['product_ids']) ? $fileData['product_ids'] : '',
            'product_titles' => !empty($fileData['product_titles']) ? $fileData['product_titles'] : '',
            'product_images' => !empty($fileData['product_images']) ? $fileData['product_images'] : '',
            'product_skus' => !empty($fileData['product_skus']) ? $fileData['product_skus'] : '',
            'product_category_ids' => !empty($fileData['product_category_ids']) ? $fileData['product_category_ids'] : '',
            'product_category_names' => !empty($fileData['product_category_names']) ? $fileData['product_category_names'] : '',
            'order_id' => !empty($fileData['order_id']) ? $fileData['order_id'] : '',
            'base_payment_value' => !empty($fileData['base_payment_value']) ? $fileData['base_payment_value'] : '',
            'order_currency' => !empty($fileData['order_currency']) ? $fileData['order_currency'] : '',
            'base_currency' => !empty($fileData['base_currency']) ? $fileData['base_currency'] : '',
            'order_payment_value' => !empty($fileData['order_payment_value']) ? $fileData['order_payment_value'] : '',
            'product_counts' => !empty($fileData['product_counts']) ? $fileData['product_counts'] : '',
            'country' => !empty($fileData['country']) ? $fileData['country'] : '',
            'city' => !empty($fileData['city']) ? $fileData['city'] : '',
        ];
    }

}
