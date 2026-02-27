<?php

class Tools extends ToolsCore
{

    public static function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        /** @var PShowEditor $module */
        if (
            ($module = Module::getInstanceByName('pshoweditor'))
            && $module->isEnabledForShopContext()
        ) {
            return $module->override__Tools_purifyHTML($html, $uri_unescape, $allow_style);
        }
        return parent::purifyHTML($html, $uri_unescape, $allow_style);
    }

}