<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Adds image, text or video to your homepage.
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */
class CmsController extends CmsControllerCore
{
    /*
    * module: leoslideshow
    * date: 2025-03-27 14:42:22
    * version: 1.0.6
    */
    public function display()
    {
        if ((bool) Module::isEnabled('leoslideshow')) {
            $leoslideshow = Module::getInstanceByName('leoslideshow');
            $leoslideshow->processCMS();
        }
        return parent::display();
    }
}
