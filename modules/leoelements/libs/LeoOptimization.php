<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */
 
if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

if (!class_exists('LeoOptimization')) {
    
    class LeoOptimization {

        public static function getInstance() {
            static $_instance;
            if (!$_instance) {
                $_instance = new LeoOptimization();
            }
            return $_instance;
        }

        public function isEnable()
        {
            return false; # turn off optimization
        }

        public function processOptimization(&$params)
        {
            return $params;
        }

        public function gtmetrix($html='')
        {
            return $html;
        }

        /**
         * Replace link from DOMAIN to CDN
         */
        public function setCDN($html='')
        {
            return $html;
        }

        public function checkGoogle($html='')
        {
            return $html;
        }
    }
}