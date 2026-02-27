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

class EdroneEdroneUpdateSubscriberStatusModuleFrontController extends ModuleFrontController
{
    public const ERROR_RESPONSE_MESSAGE = 'Something went wrong when updating subscription status';

    public const SUCCESSFUL_RESPONSE_MESSAGE = 'Subscription status updated successfully';

    public function initContent(): string
    {
        header("Cache-Control: no-cache, max-age=0");
        header("Content-type: application/json");

        $context = Context::getContext();

        //Get variables
        $subscription_status_type_id = (string) Tools::getValue('subscription_status_type_id');
        $email = (string) Tools::getValue('email');
        $phone = (string) Tools::getValue('phone');
        $subscriber_status = (string) Tools::getValue('subscriber_status');
        $subscription_status_reason = (string) Tools::getValue('subscription_status_reason');
        $event_date = (string) Tools::getValue('event_date');
        $event_id = (string) Tools::getValue('event_id');
        if (!empty($context->language->language_code) && Configuration::get(
            Edrone::APP_ID_FIELD . '_' . $context->language->language_code
        )) {
            $app_id = Configuration::get(Edrone::APP_ID_FIELD . '_' . $context->language->language_code);
        } else {
            $app_id = Configuration::get(Edrone::APP_ID_FIELD);
        }
        $userData['app_id'] = (string) $app_id;
        $signature = (string) Tools::getValue('signature');

        $hash = hash(
            "sha256",
            $email . (string)$subscription_status_type_id . $subscription_status_reason . $event_date . $event_id . $app_id,
            false
        );

        //Check if variables ok
        if ($hash !== $signature) {
            $msg = 'Signature key not match for email: ' . $email;

            PrestaShopLogger::addLog($msg, 3);
            return $this->returnResponse(true, $msg);
        }

        $checkUserInCustomer = "SELECT id_customer FROM " . _DB_PREFIX_ . "customer WHERE email = '" . $email . "' ORDER BY email";

        //check if user exists in ps_customer
        if (Db::getInstance()->executeS($checkUserInCustomer)) {
            //if exists update newsletter
            $updateUserInCustomer = "UPDATE `" . _DB_PREFIX_ . "customer` SET newsletter='" . $subscriber_status . "' WHERE email = '" . $email . "'";
            Db::getInstance()->Execute($updateUserInCustomer);
            PrestaShopLogger::addLog(
                'Updated newsletter information for: ' . $email . ' with value: ' . $subscriber_status,
                1
            );

            return $this->returnResponse(0);
        }

        //if not check if exists in ps_emailsubscription
        if (Module::isEnabled('ps_emailsubscription')) {
            $checkUserInSubscription = "SELECT * FROM " . _DB_PREFIX_ . "emailsubscription WHERE email = '" . $email . "'";

            if (Db::getInstance()->executeS($checkUserInSubscription)) { //if exists update
                $updateUserInSubscribe = "UPDATE `" . _DB_PREFIX_ . "emailsubscription` SET active='" . $subscriber_status . "' WHERE email = '" . $email . "'";
                Db::getInstance()->Execute($updateUserInSubscribe);
                PrestaShopLogger::addLog(
                    'Updated newsletter information for: ' . $email . ' with value: ' . $subscriber_status,
                    1
                );

                return $this->returnResponse(0);
            } elseif (Configuration::get(Edrone::SUBSCRIBER_EXISTS_FIELD)) { //if not check if can be created
                $addUserInSubscribe = "INSERT INTO " . _DB_PREFIX_ . "emailsubscription (email, active) VALUES ('" . $email . "', '" . $subscriber_status . "')";
                Db::getInstance()->Execute($addUserInSubscribe);
                PrestaShopLogger::addLog(
                    'Created newsletter information for: ' . $email . ' with value: ' . $subscriber_status,
                    1
                );

                return $this->returnResponse(0);
            }
        }

        $msg = 'Updated newsletter not updated information for: ' . $email . ' with value: ' . $subscriber_status;

        PrestaShopLogger::addLog(
            $msg,
            3
        );

        return $this->returnResponse(true, $msg);
    }

    private function returnResponse($hasError, $message = '')
    {
        $callableReturn = method_exists('Tools', 'jsonEncode') // Tools::jsonEncode doesn't exist in PS 8
                            ? 'Tools::jsonEncode'
                            : 'json_encode';

        if ($hasError) {
            http_response_code(400);
            die($callableReturn([
                'status' => self::ERROR_RESPONSE_MESSAGE,
                'message' => $message
            ]));
        }

        http_response_code(200);
        die($callableReturn([
            'status' => self::SUCCESSFUL_RESPONSE_MESSAGE
        ]));
    }
}
