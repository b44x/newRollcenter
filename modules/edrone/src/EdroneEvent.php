<?php
/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

namespace Edrone\EdroneModule;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class EdroneEvent {

    protected $field = array();

    abstract public function init();

    public function pre_init() {
        //preInitObject
    }

    public function userCid($value) {
        $this->field['c_id'] = urlencode(trim('phpsd_' . $value));
    }

    public function userEmail($value) {
        $this->field['email'] = urlencode(trim($value));
    }

    public function userFirstName($value) {
        $this->field['first_name'] = urlencode(trim($value));
    }

    public function userLastName($value) {
        $this->field['last_name'] = urlencode(trim($value));
    }

    public function userSubscriberStatus($value) {
        $this->field['subscriber_status'] = urlencode(trim($value));
    }

    public function userCountry($value) {
        $this->field['country'] = urlencode(trim($value));
    }

    public function userCity($value) {
        $this->field['city'] = urlencode(trim($value));
    }

    public function userPhone($value) {
        $this->field['phone'] = urlencode(trim($value));
    }

    public function userTag($value) {
        $this->field['customer_tags'] = urlencode(trim($value));
    }

    public function userUid($value)
    {
        $this->field['user_id'] = urlencode(trim($value));
    }

    public function get() {
        return $this->field;
    }

}