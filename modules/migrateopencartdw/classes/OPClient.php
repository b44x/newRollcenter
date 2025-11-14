<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class OPClient
{
    // * Request Action const

    const ACTION_CHECK_CONNECT = 'check';
    const ACTION_QUERY = 'query';
    const ACTION_FILE_CONTENT = 'file';

    // * Request vars:

    protected $url;
    protected $token;
    protected $method = 'POST';
    protected $action;
    protected $charset = 'utf8';
    protected $timeout = 5;
    protected $postdata = '';
    protected $debug = false;
    protected $json_encode = false;

    // * Response vars:

    protected $status;
    protected $content = '';
    protected $message;

    // --- Constructor / destructor:

    public function __construct($url, $token)
    {
        $this->url = $url;
        $this->token = $token;
    }

    // --- Query execution methods:

    public function check()
    {
        $this->method = 'GET';
        $this->action = self::ACTION_CHECK_CONNECT;
        $result = $this->doRequest();
        return $result;
    }

    public function query($action = self::ACTION_QUERY)
    {
        $this->method = 'POST';
        $this->action = $action;
        $result = $this->doRequest();

        return $result;
    }

    public function file()
    {
        ddd(__FILE__ . __CLASS__ . __METHOD__);
    }

    // --- Response accessors:

    public function getStatus()
    {
        return $this->status;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getMessage()
    {
        return $this->message;
    }

    // --- Configuration methods:

    public function setCharset($string)
    {
        $this->charset = $string;
    }

    public function setPostData($postdata)
    {
        $this->postdata = $postdata;
    }

    public function setTimeout($time)
    {
        $this->timeout = $time;
    }

    public function debugOn()
    {
        $this->debug = true;
    }

    public function debugOff()
    {
        $this->debug = false;
    }

    public function json_encodeOn()
    {
        $this->json_encode = true;
    }


    public function json_encodeOff()
    {
        $this->json_encode = false;
    }

    // --- Internal helper methods:

    protected function debug($msg, $object = false)
    {
        if ($this->debug) {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                ppp($msg);
                ppp($object);
            } else {
                dump($msg);
                dump($object);
            }
        }
    }

    protected function doRequest()
    {
        if ($this->debug) {
            $this->debug('URL', $this->url);
            $this->debug('Token', $this->token);
        }
        $request = null;
        if (!empty($this->postdata)) {
            $this->debug('Request Content', $this->postdata);
            $request = $this->builtRequest();
            $this->debug('Request', $request);
        }
        $url = $this->url . '?action=' . $this->action . '&token=' . $this->token;
        $response = Tools::file_get_contents($url, false, $request, $this->timeout);

        $this->debug('Response', $response);

        if ($response === false) {
            if (!in_array(ini_get('allow_url_fopen'), array('On', 'on', '1'))) {
                $this->message = 'PHP Fopen (allow_url_fopen) must be On';
            } elseif (!function_exists('curl_init')) {
                $this->message = 'PHP Curl must be enabled.';
            } elseif (!function_exists('base64_decode')) {
                $this->message = 'PHP base64_decode must be enabled.';
            } elseif (!function_exists('base64_encode')) {
                $this->message = 'PHP base64_encode must be enabled.';
            }

            return false;
        }
        
        $responseArray = json_decode(base64_decode($response), true);
        // [For PrestaShop Team] - decode used only for minimized data transfer from source cart. Like text and media
        // files
        $this->debug('Response Content', $responseArray);
        $this->status = $responseArray['status'];
        $this->content = $responseArray['content'];
        $this->message = $responseArray['message'];
        if ($this->status === 'error') {
            $this->message = $responseArray['message'];

            return false;
        }
        // * Reset all the variables that should not persist between requests:
        $this->postdata = '';

        return true;
    }

    protected function builtRequest()
    {
        if ($this->json_encode) {
            // [For PrestaShop Team] - encode used only for minimized data to transfer source cart.
            $this->postdata = json_encode($this->postdata);
        }

        $postdata = http_build_query(
            array(
                'json_encode' => $this->json_encode,
                // [For PrestaShop Team] - encode used only for minimized data to transfer source cart.
                'char_set' => base64_encode($this->charset),
                // [For PrestaShop Team] - encode used only for minimized data to transfer source cart.
                'query' => base64_encode($this->postdata)
            )
        );
        $array = array(
            'http' => array(
                'method'  => 'POST',
                'header'=>"User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n",
                'content' => $postdata
                // [For PrestaShop Team] - encode used only for minimized data to transfer source cart.
                // Like long sql queries to get information from source code.
            ));

        return @stream_context_create($array);
    }
}
