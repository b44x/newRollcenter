<?php

class Module extends ModuleCore
{

    public static function preCall($module_name)
    {
        /** @var PShowCookie $cookieModule */
        $cookieModule = Module::getInstanceByName('pshowcookie');
        return !$cookieModule || $cookieModule->isModuleGranted($module_name);
    }

}