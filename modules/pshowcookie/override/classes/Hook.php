<?php

class Hook extends HookCore
{

    public static function getHookModuleExecList($hook_name = null)
    {
        $modulesToInvoke = parent::getHookModuleExecList($hook_name);

        $cookieModule = Module::getInstanceByName('pshowcookie');
        if (!empty($modulesToInvoke) && $cookieModule) {
            foreach ($modulesToInvoke as $key => $module) {
                if (!$cookieModule->isModuleGranted($module['module'])) {
                    unset($modulesToInvoke[$key]);
                }
            }
        }

        return $modulesToInvoke;
    }

}
