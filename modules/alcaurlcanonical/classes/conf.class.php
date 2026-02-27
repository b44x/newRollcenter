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

class AlcaurlcanonicalConfClass extends AlcaurlcanonicalLibs
{
    public $id_alcaurl_canonical;
    public $type;
    public $id_object;
    public $id_product_attribute;
    public $url;
    public $url_redirect;
    public $object_redirect;
    public $id_object_redirect;
    public $redirect;

    public const PRODUCT = 1;
    public const CATEGORY = 2;
    public const CART = 3;
    public const CMS = 4;
    public const INDEX = 5;

    public $page_types = [
        'product' => self::PRODUCT,
        'category' => self::CATEGORY,
        // 'cart' => self::CART,
        'cms' => self::CMS,
        // 'index' => self::INDEX,
    ];

    public static $definition = [
        'table' => 'alcaurl_canonical',
        'primary' => 'id_alcaurl_canonical',
        'multilang' => true,
        'multilang_shop' => false,
        'fields' => [
            'id_alcaurl_canonical' => ['type' => self::TYPE_INT, 'size' => 8],
            'type' => ['type' => self::TYPE_INT, 'size' => 3],
            'id_object' => ['type' => self::TYPE_INT, 'size' => 8],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'size' => 8],
            'url' => ['type' => self::TYPE_STRING, 'size' => 2500, 'lang' => true],
            'url_redirect' => ['type' => self::TYPE_STRING, 'size' => 2500, 'lang' => true],
            'object_redirect' => ['type' => self::TYPE_INT, 'size' => 4],
            'id_object_redirect' => ['type' => self::TYPE_INT, 'size' => 8],
            'redirect' => ['type' => self::TYPE_INT, 'size' => 4],
        ],
        'after' => [
            'ADD INDEX(`id_alcaurl_canonical`)',
        ],
    ];

    protected $_extra_fields = [
        'name' => [
            'title' => 'name',
            'filter_key' => false,
            'custom_filter' => [
                'p!name',
                'c!name',
                'cm!meta_title',
            ],
        ],
    ];

    // protected $_select = ' IF (c.`name` is null, (IF (cm.`meta_title` is null, p.`name`, cm.`meta_title`)), (IF (cm.`meta_title` is null, c.`name`, cm.`meta_title`))) as name';

    protected $_select = 'CASE a.`type`
        WHEN 1 THEN p.`name`
        WHEN 2 THEN c.`name`
        ELSE cm.`meta_title`
        END as name';
    protected $_join = '';

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        // No modificar los "VAR_XXX" ya que viene de forma dinámica desde el generador de controlador (línea 994-1003)
        $this->_join = '
            LEFT JOIN `VAR_DBPREFIXproduct_lang` p ON (p.`id_product` = a.`id_object` AND a.`type` = 1 AND p.`id_lang` = VAR_ID_LANG AND p.`id_shop` = VAR_ID_SHOP)
            LEFT JOIN `VAR_DBPREFIXcategory_lang` c ON (c.`id_category` = a.`id_object` AND a.`type` = 2 AND c.`id_lang` = VAR_ID_LANG AND c.`id_shop` = VAR_ID_SHOP)
            LEFT JOIN `VAR_DBPREFIXcms_lang` cm ON (cm.`id_cms` = a.`id_object` AND a.`type` = 4 AND cm.`id_lang` = VAR_ID_LANG AND cm.`id_shop` = VAR_ID_SHOP)';
    }

    public function getCard($id = false)
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $contextCore = $this->context;

        if ($id === false) {
            $id = Tools::getValue(static::$definition['primary']);
        }

        $this->get($id);

        if ($id > 0) {
            foreach (static::$definition['fields'] as $key => $line) {
                if (property_exists(get_class($this), $key) == true) {
                    $contextCore->smarty->assign(
                        [
                            $key => $this->$key,
                        ]
                    );
                }
            }
        }

        $contextCore->smarty->assign([
            'object' => $this,
        ]);

        $id_lang = $this->context->language->id;
        $name_redirect = $name = '';

        if ($this->type == 1) {
            $name = Product::getProductName($this->id_object, 0, $id_lang);
        }

        if ($this->object_redirect == 1) {
            $name_redirect = Product::getProductName($this->id_object_redirect, 0, $id_lang);
        }

        $this->context->smarty->assign([
            'name' => $name,
            'name_redirect' => $name_redirect,
        ]);

        if (!$this->objecto) {
            return;
        }

        if (!file_exists(_PS_MODULE_DIR_ . '/' . $this->objecto->name . '/views/templates/admin/card.tpl')) {
            return;
        }

        $output = $contextCore->smarty->fetch(_PS_MODULE_DIR_ . '/' . $this->objecto->name . '/views/templates/admin/card.tpl');
        $this->ajaxresults['textos']['#cardForm'] = $output;

        return $output;
    }

    public function getSetCsv()
    {
        if (Tools::getValue('accionCSV') == 'set' && count(static::$definition['fields']) > 1) {
            if (!Configuration::get('PS_SHOP_ENABLE')) {
                return [
                    'alert' => $this->l('Your store cannot be under maintenance to perform this process.'),
                ];
            }

            $file = Tools::fileAttachment('file');
            $extension = end(explode('.', $file['name']));

            if ($extension != 'csv') {
                return [
                    'showErrorMessage' => [$this->objecto->l('The file is not a csv')],
                ];
            }
            $delimitador = $this->detectarDelimitadorCSV($file['tmp_name']);
            $gestor = fopen($file['tmp_name'], 'r', false);
            $fila = 0;
            $csv = [];
            $title = [];

            while (($datos = fgetcsv($gestor, 0, $delimitador)) !== false) {
                $numero = count($datos);

                for ($c = 0; $c < $numero; ++$c) {
                    if ($fila == 0) {
                        $title = $datos;
                        $change_values = [
                            'canonical' => 'url',
                            'redirect_type' => 'redirect',
                        ];
                        foreach($title as $k => $l) {
                            $title[$k] = isset($change_values[$l]) ? $change_values[$l] : $l;
                        }
                        $title['url'] = $title['canonical'];
                        $title['redirect'] = $title['redirect_type'];
                    } else {
                        foreach ($title as $tk => $tl) {
                            $csv[$fila][$tl] = $datos[$tk];
                        }
                    }
                }

                ++$fila;
            }

            $controllers_type = [
                'product' => 1,
                'category' => 2,
                'cart' => 3,
                'cms' => 4,
                'index' => 5,
            ];

            $fails = [];
            $languages_several = count(Language::getLanguages()) > 1 ? true : false;

            foreach ($csv as $k => $line) {
                // $origin = $line['origin'] . (strpos($line['origin'], '?') ? '&' : '?') . 'alcaurlisreading=true';
                // $res = Tools::jsonDecode($this->curlCall($origin), true);
                $res_redirect = $this->objecto->getController($line['url_redirect'], $languages_several);
                $line['url_redirect'] = $res_redirect ? false : $line['url_redirect'];
                $res = $this->objecto->getController($line['origin'], $languages_several);
                if ($res) {
                    $type = 0;
                    $id_object = 0;

                    if (isset($res['controller']) && $res['controller']) {
                        if (isset($res['id_object'])) {
                            $id_object = $res['id_object'];
                        } else {
                            $key = 'id_' . $res['controller'];
                            $id_object = $res[$key];
                        }
                    }
                    $type = isset($controllers_type[$res['controller']]) ? $controllers_type[$res['controller']] : 0;

                    if ($type) {
                        $this->id_alcaurl_canonical = false;
                        $this->querySelect('type = ' . $type . ' AND id_object =' . $id_object);
                        $this->type = $type;
                        $this->id_object = $id_object;
                        $this->object_redirect = $res_redirect && isset($controllers_type[$res_redirect['controller']]) ? $controllers_type[$res_redirect['controller']] : false;
                        $this->id_object_redirect = $res_redirect && isset($res_redirect['id']) ? $res_redirect['id'] : false;
                        if ($line['url']) {
                            $this->url[$res['id_lang']] = $line['url'];
                        }
                        if ($line['url_redirect']) {
                            $this->url_redirect[$res['id_lang']] = $line['url_redirect'];
                        }
                        $this->redirect = $line['redirect'];

                        if ($this->id_alcaurl_canonical > 0) {
                            $res = $this->queryUpdate();
                        } else {
                            $res = $this->queryCreate();
                            $this->id_alcaurl_canonical = $res;
                        }
                    } else {
                        $fails = $line;
                    }
                } else {
                    $fails = $line;
                }
            }
            $this->getList();
            $this->context->smarty->assign([
                'errors_list' => $fails,
            ]);
            $this->ajaxresults['modal']['#listForm'] = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/' . $this->objecto->name . '/views/templates/admin/errors.tpl');
            $this->ajaxresults['showSuccessMessage'] = [$this->objecto->l('Settings updated')];
        }

        if (Tools::getValue('accionCSV') == 'download' && count(static::$definition['fields']) > 1) {
            $csv = '';
            $csv_line = '';
            $csv_example1 = '';
            $csv_example2 = '';
            $array = ['https://mypage.com/my-product-combination', 'https://mypage.com/my-product', '', ''];

            foreach ($array as $k) {
                if ($k != static::$definition['primary']) {
                    $csv_example1 .= ($csv_example1 != '' ? ';' : '') . $k;
                }
            }

            $array = ['https://mypage.com/my-product-1', '', '302', 'https://mypage.com/my-product-2'];

            foreach ($array as $k) {
                if ($k != static::$definition['primary']) {
                    $csv_example2 .= ($csv_example2 != '' ? ';' : '') . $k;
                }
            }

            $array = ['origin', 'canonical', 'redirect', 'url_redirect'];
            $csv .= 'origin;canonical;redirect_type;url_redirect';

            $csv .= PHP_EOL;
            $csv_line .= PHP_EOL;
            $csv_example1 .= PHP_EOL;
            $csv_example2 .= PHP_EOL;
            ob_clean();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="example.csv"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . (Tools::strlen($csv) + Tools::strlen($csv_line) + Tools::strlen($csv_line)));
            echo $csv;
            echo $csv_example1;
            echo $csv_example2;
            echo $csv_line;
            echo $csv_line;

            exit;
        }

        return [
            'legend' => [
                'title' => $this->objecto->l('Upload CSV'),
                'icon' => 'icon-file',
                // 'image' => $this->alca_url_module.'/logo.png',
            ],
            'tpl' => [
                'file' => 'csv',
                'id' => 'cardCSV',
            ],
        ];
    }

    function detectarDelimitadorCSV($rutaArchivo, $delimitadores = [',', ';', "\t", '|', ':']) {
        $handle = fopen($rutaArchivo, 'r');
        if (!$handle) {
            throw new Exception("No se pudo abrir el archivo: $rutaArchivo");
        }

        $linea = fgets($handle); // Leemos solo la primera línea
        fclose($handle);

        $resultados = [];

        foreach ($delimitadores as $delimitador) {
            // Contamos cuántos "campos" resultan al dividir la línea
            $campos = str_getcsv($linea, $delimitador);
            $resultados[$delimitador] = count($campos);
        }

        // Nos quedamos con el delimitador que generó más columnas (más consistente)
        arsort($resultados);
        $mejor = key($resultados);

        return $mejor;
    }

}
