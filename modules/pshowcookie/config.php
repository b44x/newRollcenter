<?php

/**
 * File from http://PrestaShow.pl
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    PrestaShow.pl <kontakt@prestashow.pl>
 * @copyright Since 2014 PrestaShow.pl
 * @license   https://prestashow.pl/license
 */

use Prestashow\PShowCookie\Entity\Cookie;
use Prestashow\PShowCookie\Entity\Group;
use Prestashow\PShowCookie\Service\CookieService;

require_once dirname(__FILE__) . "/vendor/autoload.php";

Shop::addTableAssociation(Group::$definition['table'], array('type' => 'shop'));
Shop::addTableAssociation(Cookie::$definition['table'], array('type' => 'shop'));

function smarty_block_ifConsentGranted($params, $content, $smarty, &$repeat)
{
    if ($content === null) {
        return '';
    }

    if (!isset($params['group']) && !isset($params['cookie'])) {
        return 'Error: `group` or `cookie` parameter is required for ifConsentGranted block';
    }

    $groupId = $params['group'] ?? null;
    if ($groupId && !CookieService::getInstance()->isGroupGranted($groupId)) {
        return '';
    }

    $cookieName = $params['cookie'] ?? null;
    if ($cookieName && !CookieService::getInstance()->isCookieGranted($cookieName)) {
        return '';
    }

    return $content;
}
