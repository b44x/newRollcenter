<?php
class Tools extends ToolsCore
{
    
    public static function encrypt($data)
    {
        return md5(_COOKIE_KEY_ . $data);
    }
    /*
    * module: pshoweditor
    * date: 2025-11-28 11:16:55
    * version: 1.7.3
    */
    public static function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        
        if (
            ($module = Module::getInstanceByName('pshoweditor'))
            && $module->isEnabledForShopContext()
        ) {
            return $module->override__Tools_purifyHTML($html, $uri_unescape, $allow_style);
        }
        return parent::purifyHTML($html, $uri_unescape, $allow_style);
    }
}
