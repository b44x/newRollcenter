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

require_once dirname(__FILE__) . "/config.php";

class PShowEditor extends Prestashow\PShowEditor\Module
{

    public function override__Tools_purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        static $use_html_purifier = null;
        static $purifier = null;

        if (defined('PS_INSTALLATION_IN_PROGRESS') || !Configuration::configurationIsLoaded()) {
            return $html;
        }

        if ($use_html_purifier === null) {
            $use_html_purifier = (bool)Configuration::get('PS_USE_HTMLPURIFIER');
        }

        if ($use_html_purifier) {
            if ($purifier === null) {
                $config = HTMLPurifier_Config::createDefault();

                $config->set('Attr.EnableID', true);
                $config->set('Attr.AllowedRel', ['nofollow']);
                $config->set('HTML.Trusted', true);
                $config->set('Cache.SerializerPath', _PS_CACHE_DIR_ . 'purifier');
                $config->set('Attr.AllowedFrameTargets', ['_blank', '_self', '_parent', '_top']);
                if (is_array($uri_unescape)) {
                    $config->set('URI.UnescapeCharacters', implode('', $uri_unescape));
                }

                if (Configuration::get('PS_ALLOW_HTML_IFRAME')) {
                    $config->set('HTML.SafeIframe', true);
                    $config->set('HTML.SafeObject', true);
                    $config->set('URI.SafeIframeRegexp', '/.*/');
                }

                /** @var HTMLPurifier_HTMLDefinition|HTMLPurifier_HTMLModule $def */
                // http://developers.whatwg.org/the-video-element.html#the-video-element
                if ($def = $config->getHTMLDefinition(true)) {
                    $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                        'src' => 'URI',
                        'type' => 'Text',
                        'width' => 'Length',
                        'height' => 'Length',
                        'poster' => 'URI',
                        'preload' => 'Enum#auto,metadata,none',
                        'controls' => 'Bool',
                    ]);
                    $def->addElement('source', 'Block', 'Flow', 'Common', [
                        'src' => 'URI',
                        'type' => 'Text',
                    ]);
                    if ($allow_style) {
                        $def->addElement('style', 'Block', 'Flow', 'Common', ['type' => 'Text']);
                    }
                    
                    $def->addAttribute('div', 'data-target-url', 'URI');
                    $def->addAttribute('div', 'data-product-id', 'Text');
                }

                Hook::exec('actionHtmlPurifierCreateBefore', [
                    'config' => &$config,
                    'html' => &$html,
                    'allow_style' => &$allow_style,
                    'uri_unescape' => &$uri_unescape,
                ]);

                $purifier = new HTMLPurifier($config);
            }

            $html = $purifier->purify($html);
        }

        return $html;
    }

}

//$this->l('PShowEditor')
//$this->l('Index Action')
//$this->l('Block Editor')
//$this->l('Replace standard editor with Rich Block Editor.')