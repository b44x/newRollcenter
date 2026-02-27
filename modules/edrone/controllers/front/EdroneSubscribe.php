<?php
/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

/** @noinspection ALL */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EdroneEdroneSubscribeModuleFrontController extends ModuleFrontController
{
    public function initContent(): string
    {
        header("Cache-Control: no-cache, max-age=0");
        header("Content-type: application/json");

        $module = Module::getInstanceByName('edrone');
        $context = Context::getContext();
        $customer = new Customer();
        $customer = $customer->getByEmail(Tools::getValue('email'));

        if (!empty($context->language->language_code) && Configuration::get(Edrone::APP_ID_FIELD . '_' . $context->language->language_code)) {
            $appId = Configuration::get(Edrone::APP_ID_FIELD . '_' . $context->language->language_code);
        } else {
            $appId = Configuration::get(Edrone::APP_ID_FIELD);
        }

        $currentTime = new DateTime();
        $currentTimeDate = $currentTime->format('Y-m-d H:i:s');
        $eventId = uniqid();
        $subscriptionReason = 'EMAIL';
        $subscriberStatus = is_object($customer) ? $customer->newsletter : 1;
        $subscriberEmail = urlencode(Tools::getValue('email'));
        $subscriptionStatusType = 1;
        $hash = hash(
            "sha256",
            $subscriberEmail . (string)$subscriptionStatusType . $subscriptionReason . $currentTimeDate . $eventId . $appId,
            false
        );

        if (!is_object($customer)) {
            // In case user uses non standard module and base module was uninstalled, this will throw an error
            try {
                $newsletterQuery = "SELECT active FROM " . _DB_PREFIX_ . "emailsubscription WHERE email = '" . Tools::getValue('email') . "'";
                $subscriberStatus = Db::getInstance()->getValue($newsletterQuery);
            } catch (Exception $e) {
                $subscriberStatus = '';
                PrestaShopLogger::addLog(
                    'There was an error when trying to sign user: "' . $subscriberEmail . ' to the newsletter. We have marked him as inactive in Edrone system. Detailed error: ' . $e->getMessage())
                ;
            }
        }

        $userData = [];
        $userData['email'] = $subscriberEmail;
        $userData['subscription_status_type_id'] = 1;
        $userData['subscriber_status'] = $subscriberStatus;
        $userData['subscription_status_reason'] = 'EMAIL';
        $userData['event_date'] = $currentTimeDate;
        $userData['event_id'] = $eventId;
        $userData['signature'] = base64_encode($hash);
        if ($customer instanceof Customer && $customer->isLogged()) {
            $userData['user_id'] = $module->generateUserUID($customer);
        }
        $userData['app_id'] = !empty($appId) ? $appId : '';

        if ($subscriberStatus == 1) {
            PrestaShopLogger::addLog('Subscribe user to newletter: ' . $subscriberEmail, 1);
        } else {
            PrestaShopLogger::addLog('Unsubscribe user to newletter: ' . $subscriberEmail, 1);
        }

        die(json_encode($userData));
    }
}
