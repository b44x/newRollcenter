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

class EdroneSubscribeEvent extends EdroneEvent
{

    public function init()
    {
        $this->field['action_type'] = 'subscribe';
    }

    public function setSubscriberStatus(): self
    {
        $this->field['subscription_status_type_id'] = 1;

        return $this;
    }

    /**
    *
    * @param type $value
    * @return \EdroneEventOrder
    */
    public function userEmail($value): self
    {
        parent::userEmail($value);

        return $this;
    }

    /**
    *
    * @param type $value
    * @return \EdroneEventOrder
    */
    public function userFirstName($value): self
    {
        parent::userFirstName($value);

        return $this;
    }
}