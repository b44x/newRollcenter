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

require dirname(__FILE__) . '/vendor/autoload.php';

class Alcaurlcanonical extends Module
{
    public $form_values = [];

    public $has_front_hooks = false;

    public $redirections_types = [];

    public $default_routes = [];

    public $alca_dirname = '';
    public $alca_url_module = '';
    public $languages = [];
    public $language_default = '';
    public static $final_canonical = false;

    public function __construct()
    {
        $this->name = 'alcaurlcanonical';
        $this->tab = 'administration';
        $this->version = '1.2.2';
        $this->author = 'ALCALINK E-COMMERCE & SEO, S.L.L.';
        $this->need_instance = 0;
        $this->module_key = '49fedd6340c652cd20a493212b8be3ad';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Canonical + SEO Redirects');
        $this->description = $this->l('Create canonical URLs and SEO redirects on your store objects');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->languages = Language::getLanguages();
        $this->language_default = Configuration::get('PS_LANG_DEFAULT');

        $this->alca_url_module = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $this->alca_dirname = dirname(__FILE__);

        $this->context->smarty->assign('urlmodule', $this->alca_url_module);
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->redirections_types = [
            '301' => $this->l('Moved Permanently'),
            '302' => $this->l('Moved'),
            '307' => $this->l('Temporary Redirect'),
            '410' => $this->l('Gone'),
        ];
    }

    /**
     * Install
     * Step 1 - Create the configuration option values in the database
     * Step 2 - Create the database
     * Step 3 - Uninstallation of the Tab
     *
     * @return bool Installation result
     */
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';

        $alca_utils = new AlcaurlcanonicalFormUtils();
        $alca_utils->install();

        if (!parent::install()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayBackofficeHeader')
            || !$this->registerHook('actionBuildFrontEndObject')
            || !$this->registerHook('filterProductContent')
            || !$this->registerHook('displayAdminProductsExtra')
            || !$this->registerHook('actionDispatcherBefore')
            || !$this->registerHook('overrideLayoutTemplate')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Uninstall
     * Step 1 - Remove Configuration option values from database
     * Step 2 - Remove the database
     * Step 3 - Uninstallation of the Tab
     *
     * @return bool Uninstallation result
     */
    public function uninstall()
    {
        $this->form_values = $this->getConfigFormFieldstemplate();

        $alca_utils = new AlcaurlcanonicalFormUtils();
        $alca_utils->uninstall();

        if (!$this->form_values) {
            return false;
        }

        foreach ((is_array($this->form_values) ? $this->form_values : []) as $key => $form_value) {
            if (isset($form_value['input'])) {
                foreach ($form_value['input'] as $k => $input) {
                    if (isset($input['name'])) {
                        Configuration::deleteByName($input['name']);
                    }
                }
            }
        }

        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall();
    }

    public $paramsCache = [];

    public function getParams($type, $id)
    {
        if (isset($this->paramsCache[constant('AlcaurlcanonicalConfClass::' . $type)]) && isset($this->paramsCache[constant('AlcaurlcanonicalConfClass::' . $type)][$id])) {
            return $this->paramsCache[constant('AlcaurlcanonicalConfClass::' . $type)][$id];
        }
        $id_lang = Context::getContext()->cookie ? Context::getContext()->cookie->id_lang : $this->context->language->id;
        $core = new AlcaurlcanonicalConfClass();
        $where = 'type=' . constant('AlcaurlcanonicalConfClass::' . $type);
        $where .= ' AND id_object =' . $id . ' AND t2.id_lang = ' . (int) $id_lang;
        $core->querySelect($where);
        $this->paramsCache[constant('AlcaurlcanonicalConfClass::' . $type)][$id] = $core;

        return $core;
    }

    public function hookActionBuildFrontEndObject($params)
    {
        $v = $this->context->smarty->tpl_vars;

        if (isset($v['page']) && property_exists($v['page'], 'value')) {
            $property = strtoupper($this->context->controller->php_self);

            // if (defined('AlcaurlcanonicalConfClass::' . $property) && $property != 'PRODUCT') {
            if (defined('AlcaurlcanonicalConfClass::' . $property)) {
                /*
                $core = new AlcaurlcanonicalConfClass();
                $where = 'type=' . constant('AlcaurlcanonicalConfClass::' . $property);
                if ($property == 'CATEGORY') {
                $id = Tools::getValue('id_category');
                } elseif ($property == 'PRODUCT') {
                $id = Tools::getValue('id_product');
                }
                $where .= ' AND id_object =' . $id;
                $this->paramsCache[constant('AlcaurlcanonicalConfClass::' . $property)][$id] = $core->querySelect($where);
                 */
                $id = 0;

                if ($property == 'CATEGORY') {
                    $id = Tools::getValue('id_category');
                } elseif ($property == 'PRODUCT') {
                    $id = Tools::getValue('id_product');
                } elseif ($property == 'CMS') {
                    $id = (int) Tools::getValue('id_cms');
                }

                $core = $this->getParams($property, $id);
                if ($core->url) {
                    $page = $v['page']->value;
                    $base_link = $this->context->link->getPageLink('index');
                    $core->url = str_replace($base_link, '', $core->url);
                    $page['canonical'] = (!strpos(' ' . $core->url, 'http') > 0 ? rtrim($base_link, '/') . '/' : '') . ltrim($core->url, '/');
                    self::$final_canonical = $page['canonical'];
                    $this->context->smarty->tpl_vars['page']->value = $page;

                    if ($property == 'PRODUCT') {
                        // $this->context->smarty->tpl_vars['product']->value->offsetSet('canonical_url', $page['canonical']);
                    }
                }
            }
            // if ($property == 'CMS') {
            // $page = $v['page']->value;
            // $base_link = $this->context->link->getBaseLink();
            // $page['canonical'] = $v['request_uri']->value;
            // $this->context->smarty->tpl_vars['page']->value = $page;
            // }
        }
    }

    public function hookFilterProductContent($params)
    {
        if ($this->context->controller->php_self != 'product') {
            return;
        }

        $id_product = $params['object']->id;

        if (!$id_product) {
            return;
        }

        $where = 'type= ' . AlcaurlcanonicalConfClass::PRODUCT . ' AND id_object =' . $id_product . ' AND t2.id_lang = ' . (int) $this->context->language->id;
        $core = new AlcaurlcanonicalConfClass();
        $core->querySelect($where);
        if ($core->url) {
            $base_link = $this->context->link->getBaseLink();
            self::$final_canonical = $core->url;
            $params['object']->offsetSet('canonical_url', (!strpos(' ' . $core->url, 'http') > 0 ? rtrim($base_link, '/') . '/' : '') . ltrim($core->url, '/'), true);
        }

        return $params;
    }

    public function hookActionDispatcherBefore($params)
    {
        $this->checkRedirect();
    }

    public function checkRedirect()
    {
        $property = Tools::strtoupper(Tools::getValue('controller'));
        $id = 0;
        if ($property == 'CATEGORY') {
            $id = Tools::getValue('id_category');
        } elseif ($property == 'PRODUCT') {
            $id = Tools::getValue('id_product');
        }

        if ($id > 0) {
            $core = $this->getParams($property, $id);
            if ($core->redirect > 0 && (($core->url_redirect && $core->url_redirect != '') || $core->id_object_redirect > 0 || $core->redirect == 410)) {
                if ($core->object_redirect == 1) {
                    $core->url_redirect = $this->context->link->getProductLink($core->id_object_redirect);
                }
                if ($core->object_redirect == 2) {
                    $core->url_redirect = $this->context->link->getCategoryLink($core->id_object_redirect);
                }
                if ($core->object_redirect == 4) {
                    $core->url_redirect = $this->context->link->getCMSLink($core->id_object_redirect);
                }
                if ($core->object_redirect == 5) {
                    $core->url_redirect = '';
                }
                if (isset($core->url_redirect)) {
                    $this->redirection($core->redirect, $core->url_redirect);
                }
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (
            Tools::getValue('configure') == $this->name
        ) {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryPlugin('colorpicker');
            $this->context->controller->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
            $this->context->controller->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
            $this->context->controller->addJqueryUI('ui.datepicker');
            $this->context->controller->addJqueryUi('ui.widget');
            $this->context->controller->addJqueryPlugin('tagify');
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->context->smarty->assign([
            'redirection_types' => $this->redirections_types,
        ]);
        $alca_utils = new AlcaurlcanonicalFormUtils();
        $alca_utils->initiate();
        $this->getVars();
        $ConfClass = new AlcaurlcanonicalConfClass();
        $ConfClass->initiate($this);
        $ConfClass->getCard();
        $ConfClass->getList();
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output;
    }

    public $routes = [];

    public function addRoute($route_id, $rule, $controller, $id_lang = null, array $keywords = [], array $params = [], $id_shop = null)
    {
        $context = Context::getContext();

        if (isset($context->language) && $id_lang === null) {
            $id_lang = (int) $context->language->id;
        }

        if (isset($context->shop) && $id_shop === null) {
            $id_shop = (int) $context->shop->id;
        }

        $dis = Dispatcher::getInstance();

        if (count($this->routes) == 0) {
            $default_routes = $this->default_routes;

            foreach ($dis->default_routes as $k => $l) {
                $default_routes[$k] = $dis->computeRoute(
                    $l['rule'],
                    $l['controller'],
                    $l['keywords'],
                    isset($l['params']) ? $l['params'] : []
                );

                // RUTAS PERSONALIZADAS EN TRÁFICO Y SEO
                if ($custom_route = Configuration::get('PS_ROUTE_' . $k, null, null, $id_shop)) {
                    $default_routes[$k] = $dis->computeRoute(
                        $custom_route,
                        $l['controller'],
                        $l['keywords'],
                        isset($l['params']) ? $l['params'] : []
                    );
                }
            }

            foreach (Language::getLanguages() as $l) {
                $this->routes[$id_shop][$l['id_lang']] = $default_routes;
            }
        }

        $route = $dis->computeRoute($rule, $controller, $keywords, $params);

        if (!isset($this->routes[$id_shop])) {
            $this->routes[$id_shop] = [];
        }

        if (!isset($this->routes[$id_shop][$id_lang])) {
            $this->routes[$id_shop][$id_lang] = [];
        }

        $this->routes[$id_shop][$id_lang][$route_id] = $route;
    }

    public function getController($url, $check_language = false)
    {
        $request_uri = '/' . str_replace($this->context->link->getBaseLink(), '', $url);
        $request_uri = explode('#', $request_uri)[0];
        $id_shop = $this->context->shop->id;
        $sql = 'SELECT m.page, ml.url_rewrite, ml.id_lang
        FROM `' . _DB_PREFIX_ . 'meta` m
        LEFT JOIN `' . _DB_PREFIX_ . 'meta_lang` ml ON (m.id_meta = ml.id_meta' . Shop::addSqlRestrictionOnLang('ml', (int) $id_shop) . ')
        ORDER BY LENGTH(ml.url_rewrite) DESC';

        if ($results = Db::getInstance()->executeS($sql)) {
            foreach ($results as $row) {
                if ($row['url_rewrite']) {
                    $this->addRoute(
                        $row['page'],
                        $row['url_rewrite'],
                        $row['page'],
                        $row['id_lang'],
                        [],
                        [],
                        $id_shop
                    );
                }
            }
        }
        $id_lang = $this->context->language->id;
        if (
            Language::isMultiLanguageActivated()
            && preg_match('#^/([a-z]{2})(?:/.*)?$#', $request_uri, $matches)
        ) {
            $id_lang = (int) Language::getIdByIso($matches[1]);
            $request_uri = substr($request_uri, 3);
        }

        $test_request_uri = preg_replace('/(=http:\/\/)/', '=', $request_uri);

        // If the request_uri matches a static file, then there is no need to check the routes, we keep
        // "controller_not_found" (a static file should not go through the dispatcher)
        $results = [];

        if (!preg_match(
            '/\.(gif|jpe?g|png|css|js|ico)$/i',
            parse_url($test_request_uri, PHP_URL_PATH)
        )) {
            list($uri) = explode('?', $request_uri);

            if (isset($this->routes[$id_shop][Context::getContext()->language->id])) {
                foreach ($this->routes[$id_shop][Context::getContext()->language->id] as $route) {
                    if (preg_match($route['regexp'], $uri, $m)) {
                        // Route found ! Now fill $_GET with parameters of uri
                        foreach ($m as $k => $v) {
                            if (!is_numeric($k)) {
                                $results[$k] = $v;
                            }
                        }
                        $table = $route['controller'];
                        if (!isset($results['id_' . $table])) {

                            return false;
                        }
                        if ($check_language) {
                            $results['id_lang'] = $id_lang;
                        } else {
                            $results['id_lang'] = Context::getContext()->language->id;
                        }
                        $results['id'] = isset($results['id_' . $table]) ? $results['id_' . $table] : 0;
                        $results['controller'] = $route['controller'];

                        return $results;
                    }
                }
            }
        }

        return false;
    }

    public function getVars()
    {
        $ConfClass = new AlcaurlcanonicalConfClass();
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $this->context->smarty->assign([
            'cms' => CMS::getCMSPages((int) $id_lang, null, true, (int) $id_shop),
            'categories' => Db::getInstance()->executeS('
                SELECT id_category, name FROM ' . _DB_PREFIX_ . 'category_lang
                WHERE `id_lang` = ' . (int) $id_lang . ' AND `id_shop` = ' . (int) $id_shop . ' ORDER BY `id_category` ASC
            '),
            'page_types' => $ConfClass->page_types,
        ]);
    }

    public function setProduct($target = '#productset', $id = 'id_object')
    {
        $id_lang = $this->context->language->id;
        $id_product = Tools::getValue('id');
        Product::getProductName($id_product, $id_lang);

        $this->context->smarty->assign([
            'idproduct' => $id_product,
            'id' => $id,
            'name' => Product::getProductName($id_product, 0, $id_lang),
        ]);
        $tpl = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/product.tpl');

        return [
            'textos' => [
                $target => $tpl,
            ],
            'closeobjects' => ['#biteateAjaxSearchProductList', '#type_object_product'],
        ];
    }

    public function setProductObject()
    {
        return $this->setProduct('#setProductObject', 'id_object_redirect');
    }

    /**
     * Generate array form
     *
     * @return array $this->loadValues($form) with value for each field of form
     */
    protected function getConfigFormFieldstemplate()
    {
        $alcaurlcanonicalLibs = new AlcaurlcanonicalLibs();
        $alcaurlcanonicalLibs->objecto = $this;
        $form = [
            [
                'legend' => [
                    'title' => $this->l('URL'),
                    'icon' => 'icon-cogs',
                    // 'image' => $this->alca_url_module.'/logo.png',
                ],
                'tpl' => [
                    'file' => 'card',
                    'id' => 'cardForm',
                ],
            ],
            [
                'legend' => [
                    'title' => $this->l('List'),
                    'icon' => 'icon-list',
                    // 'image' => $this->alca_url_module.'/logo.png',
                ],
                'tpl' => [
                    'file' => 'list',
                    'id' => 'listForm',
                ],
            ],
            Tools::getValue('debug')
                ?
                [
                    'legend' => [
                        'title' => $this->l('Herramientas'),
                        'icon' => 'icon-cogs',
                        // 'image' => $this->alca_url_module.'/logo.png',
                    ],
                    'tpl' => [
                        'file' => 'tools',
                        'id' => 'toolsForm',
                    ],
                ]
                : null,
            $alcaurlcanonicalLibs->getSetCsv($this),
        ];
        $this->checkModules($form);
        $alca_utils = new AlcaurlcanonicalFormUtils();

        return $alca_utils->loadValues($form, $this->languages);
    }

    public function checkModules(&$form)
    {
        if (Tools::getValue('ajaxaction')) {
            return false;
        }

        $named = 'alcaurl';
        $moduleList = ModuleCore::getModulesOnDisk();

        foreach ($moduleList as $module) {
            if ($module->installed && strpos(' ' . $module->name, $named) && $module->name != $this->name) {
                $url = $this->context->link->getAdminLink('AdminModules', false) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $module->name . '&tab_module=' . $module->tab . '&module_name=' . $module->name;
                $form[] = [
                    'legend' => [
                        'title' => $module->displayName,
                        'icon' => 'icon-cogs',
                        'href' => $url,
                        // 'image' => $this->alca_url_module.'/logo.png',
                    ],
                ];
            }
        }
    }

    public function getConfigFormValues()
    {
        return [
            'ALCA_BASE_CONF1' => Configuration::get('ALCA_BASE_CONF1'),
        ];
    }

    public function getTools()
    {
        $accion = Tools::getValue('accion');
        $AlcaurlcanonicalTools = new AlcaurlcanonicalTools();
        $results = $AlcaurlcanonicalTools->$accion();

        return $results;
    }

    public function translations()
    {
        $this->l('Module positions');
        $this->l('Documentation');
        $this->l('Translate');
        $this->l('Manage translations');
        $this->l('Valid extensions: ');
        $this->l('Size exceeded. Maximum size allowed: ');
        $this->l('An error occurred while uploading the temporary file.');
        $this->l('An error occurred during the process of uploading the file.');
        $this->l('Size exceeded. Maximum size allowed: ');
        $this->l('Invalid value for: ');
        $this->l('Background color');
        $this->l('Text color');
        $this->l('Field select');
        $this->l('Position');
        $this->l('Select an option');
        $this->l('Top');
        $this->l('Botton');
        $this->l('Left');
        $this->l('Right');
        $this->l('Align');
        $this->l('Design');
        $this->l('External margin');
        $this->l('Internal margin');
        $this->l('Text align');
        $this->l('Element align');
        $this->l('Upload CSV');
        $this->l('The file is not a CSV');
        $this->l('Settings updated');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_lang = $this->context->language->id;

        if (isset($params['product'])) {
            if (property_exists($params['product'], 'id')) {
                $id_product = $params['product']->id;
            } elseif ($params['product']['id_product']) {
                $id_product = $params['product']['id_product'];
            } else {
                return false;
            }
        } elseif ($params['id_product']) {
            $id_product = $params['id_product'];
        } else {
            return false;
        }

        if (!$this->context->smarty) {
            $this->context = Context::getContext();
        }

        $core = new AlcaurlcanonicalConfClass();
        $res = $core->querySelect('type = ' . AlcaurlcanonicalConfClass::PRODUCT . ' AND id_object = ' . $id_product, false);
        $this->context->smarty->assign([
            'results' => $res,
            'url' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name]),
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/admin_product.tpl');
    }

    protected function redirection($type, $url = false)
    {
        if (Tools::getValue('alcaurlisreading')) {
            return false;
        }

        $base = $this->context->link->getBaseLink();

        if ($url) {
            $url_final = str_replace($base, '', $url);
            $url_final = rtrim($base, '/') . '/' . ltrim($url_final, '/');
        } else {
            $url_final = $base;
        }

        if ($type == 403) {
            // nothing
        } elseif ($type == 301) {
            // If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
            $this->context->cookie->disallowWriting();
            header('HTTP/1.1 ' . $type . ' Moved Permanently');
            header('Cache-Control: no-cache');
            Tools::redirect($url_final);

            exit;
        } elseif ($type == 302) {
            // If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
            $this->context->cookie->disallowWriting();
            header('HTTP/1.1 302 Moved');
            header('Cache-Control: no-cache');
            Tools::redirect($url_final);

            exit;
        } elseif ($type == 307) {
            // If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
            $this->context->cookie->disallowWriting();
            header('HTTP/1.1 307 Temporary Redirect');
            header('Cache-Control: no-cache');
            Tools::redirect($url_final);

            exit;
        } elseif ($type == 410) {
            // If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
            include dirname(__FILE__) . '/controllers/front/nofound.php';

            $controller = Controller::getController('alcaurlcanonicalnofoundModuleFrontController');

            // Running controller
            return $controller->run();
        }
    }

    public function hookDisplayHeader($params)
    {
        $controller = $this->context->controller->php_self;
        $id = false;

        if ($controller == 'product') {
            $id = Tools::getValue('id_product');
        } elseif ($controller == 'category') {
            $id = Tools::getValue('id_category');
        } elseif ($controller == 'cms') {
            $id = Tools::getValue('id_cms');
        }

        if (Tools::getValue('alcaurlisreading')) {
            ob_clean();
            echo json_encode([
                'controller' => $controller,
                'id_object' => (int) $id,
            ]);

            exit;
        }
    }

    public function hookOverrideLayoutTemplate($params)
    {
        if (self::$final_canonical && isset($this->context->smarty->tpl_vars['page'])) {
            $this->context->smarty->tpl_vars['page']->value['canonical'] = self::$final_canonical;
        }
    }
}
