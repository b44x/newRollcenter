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

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class AdminLeoElementsModuleController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        $url = 'index.php?controller=AdminModules&configure=leoelements&token='.Tools::getAdminTokenLite('AdminModules');
        Tools::redirectAdmin($url);
        parent::__construct();
    }
}
