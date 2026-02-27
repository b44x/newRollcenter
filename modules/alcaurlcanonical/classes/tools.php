<?php
/**
 * 2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author ALCALINK E-COMMERCE & SEO, S.L.L. <info@alcalink.com>
 * @copyright  2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * Registered Trademark & Property of ALCALINK E-COMMERCE & SEO, S.L.L.
 **/
if (!defined('_PS_VERSION_')) {
    exit;
}

class AlcaurlcanonicalTools
{
    public $context = false;

    public function checkDestination($id_first = false, $limit = 20)
    {
        if ($id_first === false) {
            $id_first = (int) Tools::getValue('id_first');
        }

        $output = 'Redirect 301 /home http://ejemplo.com';

        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $id_lang = $this->context->language->id;
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_lang l ' .
            ' INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = l.id_product  ' .
            ' WHERE ' .
            ' l.id_lang = ' . $id_lang . ' AND p.active = 1 ' .
            ' AND l.id_product >= ' . $id_first .
            ' LIMIT ' . $limit;
        $results = Db::getInstance()->executeS($sql);

        if (count($results) == 0) {
            return [
                'alert' => 'Finished',
            ];
        }

        $output_errors = '';
        $output_htacces = '';
        $last_id_product = 0;

        foreach ($results as $line) {
            $url_destination = $this->checkSeveralsDestination($line);

            if ($url_destination) {
                $output_htacces .= 'Redirect 301 ' . $url_destination . ' ' . $this->context->link->getProductLink($line['id_product'], $line['link_rewrite']) . PHP_EOL;
            } else {
                $output_errors .= 'id:' . $line['id_product'] . '  ' . $this->context->link->getProductLink($line['id_product'], $line['link_rewrite']) . ' destination ' . $url_destination . PHP_EOL;
            }

            $last_id_product = (int) $line['id_product'];
        }

        return [
            'textosAdd' => [
                '#consolehtacces' => $output_htacces,
                '#consoleerrors' => $output_errors,
            ],
            'callajax' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => 'alcaurlcanonical', 'functionLibs' => 'getTools', 'accion' => 'checkDestination']) . '&id_first=' . (int) $last_id_product + 1 . '&construct=' . urlencode(Tools::getValue('construct')),
        ];
    }

    public function checkSeveralsDestination($line)
    {
        $todas = [];
        $todas[] = $url_destination = $this->getParseDestination($line);

        $response = $this->checkResponse($url_destination);

        if ($response) {
            return $url_destination;
        } else {
            $line2 = $line;
            $line2['link_rewrite'] = Tools::strtoupper($line['link_rewrite']);
            $todas[] = $url_destination = $this->getParseDestination($line2);
            $response = $this->checkResponse($url_destination);

            if ($response) {
                return $url_destination;
            } else {
                $line2['link_rewrite'] = Tools::str2url(str_replace(
                    '.',
                    'unpuntounpunto',
                    Tools::substr($line['name'], 0, Tools::strlen($line['link_rewrite']) + 1)
                ));
                $line2['link_rewrite'] = str_replace('unpuntounpunto', '.', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = Tools::str2url(str_replace(
                    ',',
                    'unpuntounpunto',
                    Tools::substr($line['name'], 0, Tools::strlen($line['link_rewrite']) + 1)
                ));
                $line2['link_rewrite'] = str_replace('unpuntounpunto', ',', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = Tools::str2url(str_replace(
                    ',',
                    'unpuntounpunto',
                    $line['name']
                ));
                $line2['link_rewrite'] = str_replace('unpuntounpunto', ',', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = Tools::str2url(str_replace(
                    'º',
                    'cerocero',
                    $line['name']
                ));
                $line2['link_rewrite'] = str_replace('cerocero', 'º', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = Tools::str2url(str_replace(
                    'º',
                    'cerocero',
                    Tools::substr($line['name'], 0, Tools::strlen($line['link_rewrite']) + 4)
                ));
                $line2['link_rewrite'] = str_replace('cerocero', 'º', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = $line['name'] . '-' . $line2['reference'];
                $line2['link_rewrite'] = Tools::strtoupper(
                    str_replace(
                        '/',
                        '',
                        str_replace(' ', '-', $line2['link_rewrite'])
                    )
                );
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                if (strpos($line['name'], '"')) {
                    $line2['link_rewrite'] = $line['name'];
                    $line2['link_rewrite'] = str_replace(
                        ' ',
                        '-',
                        Tools::substr($line['name'], 0, strpos($line['name'], '"'))
                    );
                    $todas[] = $url_destination = $this->getParseDestination($line2);
                    $response = $this->checkResponse($url_destination);

                    if ($response) {
                        return $url_destination;
                    }
                }

                $line2['link_rewrite'] = $line['name'] . '-' . $line2['reference'];
                $line2['link_rewrite'] = Tools::strtoupper(
                    $this->clean($line2['link_rewrite'])
                );
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = str_replace('grados', 'º', $line['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = Tools::str2url(
                    str_replace(',', 'unpuntounpunto',
                        Tools::substr(str_replace(' y ', ', ', $line['name']),
                            0,
                            Tools::strlen($line['link_rewrite']) + 1
                        )
                    )
                );
                $line2['link_rewrite'] = str_replace('unpuntounpunto', ',', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = str_replace('baobab', 'baobad',
                    Tools::str2url(
                        str_replace(',', 'unpuntounpunto',
                            str_replace('(con ', '',
                                str_replace(' y ', ', ', $line['name'])
                            )
                        )
                    )
                );
                $line2['link_rewrite'] = str_replace('unpuntounpunto', ',', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = str_replace(',-jalea-real', '',
                    Tools::str2url(
                        str_replace('+', '-',
                            str_replace(',', 'unpuntounpunto',
                                str_replace(', JALEA REAL', '',
                                    str_replace(' y ', ', ', $line['name'])
                                )
                            )
                        )
                    )
                );
                $line2['link_rewrite'] = str_replace('unpuntounpunto', ',', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }

                $line2['link_rewrite'] = str_replace(',-jalea-real', '',
                    Tools::str2url(
                        str_replace('+', '-',
                            str_replace(',', 'unpuntounpunto',
                                str_replace(', JALEA REAL', '',
                                    str_replace(' y ', ', ', $line['name'] . ' ' . $line2['reference'])
                                )
                            )
                        )
                    )
                );
                $line2['link_rewrite'] = str_replace('unpuntounpunto', ',', $line2['link_rewrite']);
                $todas[] = $url_destination = $this->getParseDestination($line2);
                $response = $this->checkResponse($url_destination);

                if ($response) {
                    return $url_destination;
                }
            }
        }

        return false;
    }

    public function clean($string)
    {
        $string = str_replace('%', '',
            str_replace('/', '',
                str_replace(' ', '-', $string)
            )
        );

        return $string;
    }

    public function getParseDestination($params, $contruct = false)
    {
        if (!$contruct) {
            $contruct = Tools::getValue('construct');
        }

        $final = $contruct;

        foreach ($params as $k => $l) {
            $final = str_replace('{' . $k . '}', $l, $final);
        }

        return $final;
        /* $construct_array = explode('{', $contruct);

        foreach ($construct_array as $k => $l) {
        $l = str_replace('}', '', $l); */
        /*
        if (strpos($l, '|replace:') > 0) {
        $param_l = explode('|');
        $l = str_replace($l, '|replace:', $l);
        }
         */
        // }
    }

    public function checkDestinationCategory($id_first = false, $limit = 5)
    {
        if ($id_first === false) {
            $id_first = (int) Tools::getValue('id_first');
        }

        $output = 'Redirect 301 /home http://ejemplo.com';

        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $id_lang = $this->context->language->id;
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang l ' .
            ' INNER JOIN ' . _DB_PREFIX_ . 'category p ON p.id_category = l.id_category  ' .
            ' WHERE ' .
            ' l.id_lang = ' . $id_lang . ' AND p.active = 1 ' .
            ' AND l.id_category >= ' . $id_first . ' AND l.id_category <> 2 AND l.id_category <> 1 ' .
            ' LIMIT ' . $limit;
        $results = Db::getInstance()->executeS($sql);

        if (count($results) == 0) {
            return [
                'alert' => 'Finished',
            ];
        }

        $output_errors = '';
        $output_htacces = '';
        $last_id_category = 0;

        foreach ($results as $line) {
            $url_destination = $this->checkSeveralsDestination($line);

            if ($this->checkResponse($url_destination)) {
                $output_htacces .= 'Redirect 301 ' . $url_destination . ' ' . $this->context->link->getCategoryLink($line['id_category'], $line['link_rewrite']) . PHP_EOL;
            } else {
                $output_errors .= 'id:' . $line['id_category'] . '  ' . $this->context->link->getCategoryLink($line['id_category'], $line['link_rewrite']) . ' destination ' . $url_destination . PHP_EOL;
            }

            $last_id_category = (int) $line['id_category'];
        }

        return [
            'textosAdd' => [
                '#formcategory #consolehtacces' => $output_htacces,
                '#formcategory #consoleerrors' => $output_errors,
            ],
            'callajax' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => 'alcaurlcanonical', 'functionLibs' => 'getTools', 'accion' => 'checkDestinationCategory']) . '&id_first=' . (int) $last_id_category + 1 . '&construct=' . urlencode(Tools::getValue('construct')),
        ];
    }

    public function checkResponse($url, $code = '200')
    {
        $ch = curl_init();
        $headers = [];
        $headers[] = 'Content-Type:multipart/form-data';
        $headers[] = 'Expect:';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $head = curl_exec($ch);
        $date_array = explode(chr(13), $head);
        $is_200 = false;

        foreach ($date_array as $k => $l) {
            if (strpos(' ' . $l, 'HTTP/1.1 ' . $code) > 0) {
                $is_200 = true;

                break;
            }
        }

        return $is_200;
    }
}
