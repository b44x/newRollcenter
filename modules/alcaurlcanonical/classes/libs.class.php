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

class AlcaurlcanonicalLibs extends ObjectModel
{
    public static $dbTypes = [
        '1' => 'INT',
        '2' => 'BOOL',
        '3' => 'VARCHAR',
        '4' => 'DOUBLE',
        '5' => 'DATETIME',
        '6' => 'TEXT',
        '7' => 'TEXT',
        '8' => 'TEXT',
    ];

    public $url_ajax;

    public $objecto;

    public $ajaxresults;

    public $context;

    public $pagination = 100;

    public $id_shop = false;

    public $id_lang = false;

    protected $_join = false;

    protected $_select = false;

    protected $fields_list;

    protected $_extra_fields;

    public $_list;
    public $_listTotal = false;
    public $_filter;
    public $last_query;
    public $p;
    public $column_position = false;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (property_exists(get_class($this), 'definition') == true) {
            static::$definition['fields']['id_shop'] = ['type' => self::TYPE_INT, 'size' => 8];

            if (Tools::substr(get_class($this), -4) != 'Libs') {
                parent::__construct($id, $id_lang, $id_shop);
            }
        }

        if (!$this->context) {
            $this->context = Context::getContext();
        }
    }

    public function initiate($obj)
    {
        $this->objecto = $obj;

        if (!$this->checkReady()) {
            return false;
        }

        $contextCore = Context::getContext();
        $this->context = $contextCore;
        $contextCore->controller->addCSS(_PS_MODULE_DIR_ . $obj->name . '/views/css/alca.css');
        $contextCore->controller->addJS(_PS_MODULE_DIR_ . $obj->name . '/views/js/alca.lib.js');
        $accion = Tools::getValue('accion');
        $mensajes = [
            'save' => $obj->l('Save'),
            'saving' => $obj->l('Saving'),
            'saved' => $obj->l('Saved'),
        ];

        if (method_exists($obj, 'getConfigFormValues')) {
            $vars = $obj->getConfigFormValues();

            if (is_array($vars)) {
                foreach ($vars as $key => $var) {
                    $contextCore->smarty->assign(
                        [$key => $var]
                    );
                }
            }
        }

        if ($accion == 'saveConfigFormValues') {
            if (method_exists($obj, 'getConfigFormValues')) {
                $vars = $obj->getConfigFormValues();

                foreach ($vars as $key => $var) {
                    if (Tools::getValue($key) || Tools::getValue($key) == 0) {
                        ConfigurationCore::updateValue($key, Tools::getValue($key, $var));
                    }
                }
            } else {
                $obj->postProcess();
            }

            echo json_encode([
                'showSuccessMessage' => [$this->objecto->l('Settings updated')],
            ]);

            exit;
        }

        /* if ($accion == 'uploadphoto') {
            if (Tools::fileAttachment('file')) {
                $file = Tools::fileAttachment('file');
                $name = Tools::getValue('biteate-file-name');
                $path = Tools::getValue('biteate-file-path');
                $width = Tools::getValue('biteate-file-width');
                $height = Tools::getValue('biteate-file-height');
                $id = Tools::getValue('id_icon');

                $this->uploadImage($file, $name, $path, $width, $height);

                $pathUrl = $this->ingeliaGetBaseLink() . str_replace(_PS_ROOT_DIR_ . '/', '', $path);
                $srcdoc = '<body style="margin:0px !important"><img style="max-width:100%;width:100%;height: 100vh;" src="' . $pathUrl . $name . '.jpg#' . time() . '"><img></body>';
                $result = [
                    'texto' => "<iframe style='margin: -0px -1px;width:100%' srcdoc='" . $srcdoc . "'></iframe>",
                    'donde' => '#' . $id,
                ];

                echo json_encode($result);

                exit;
            }
        } */

        $url_ajax = $contextCore->link->getAdminLink('AdminModules', false) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $obj->name . '&tab_module=' . $obj->tab . '&module_name=' . $obj->name;
        $this->url_ajax = $url_ajax;
        $AD = str_replace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
        // $contextCore->smarty->assign(['url_ajax' => self::biteateGetBaseLink().''.$AD.'/'. $url_ajax));

        $contextCore->smarty->assign(
            [
                'helperapm' => $this,
                'url_ajax' => $url_ajax,
                'modulepath' => _PS_MODULE_DIR_ . '/' . $obj->name,
                'modulepathCard' => _PS_MODULE_DIR_ . '/' . $obj->name . '/views/templates/admin/card.tpl',
                'modulepathList' => _PS_MODULE_DIR_ . '/' . $obj->name . '/views/templates/admin/list.tpl',
                'alcalogo' => Tools::getHttpHost(true) . __PS_BASE_URI__ . '/modules/' . $obj->name . '/views/img/logo.png',
                'alcalang_iso' => Language::getIsoById((int) $this->context->language->id),
            ]
        );

        if (property_exists(get_class($this), 'definition') == true) {
            $contextCore->smarty->assign(
                [
                    'definitionfields' => static::$definition['fields'],
                ]
            );
        }

        $langCore = new LanguageCore();
        $langCore->getLanguages();
        $langs = [];

        foreach ($langCore->getLanguages(false) as $lang) {
            $langs[$lang['id_lang']] = $lang;
        }

        MediaCore::addJsDef([
            'url_ajax' => $url_ajax,
            'biteatemensajes' => $mensajes,
            'languages_I' => $langs,
        ]);
        $functionLibs = Tools::getValue('functionLibs');

        if ($functionLibs) {
            $res = null;

            if (method_exists($this, $functionLibs)) {
                $res = $this->$functionLibs();
            }

            if (method_exists($this->objecto, $functionLibs)) {
                $res = $this->objecto->$functionLibs();
            }

            if ($this->ajaxresults) {
                echo json_encode($this->ajaxresults);
                $res = false;
            }

            if ($res) {
                echo json_encode($res);
            }

            exit;
        }

        return true;
    }

    public static function biteateGetBaseLink($id_shop = null, $ssl = null, $relative_protocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
            }

            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        $base = Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()
        ? Tools::getShopDomainSsl(true) . __PS_BASE_URI__
        : Tools::getShopDomain(true) . __PS_BASE_URI__;

        return $base . $shop->getBaseURI();
    }

    public static function removeTables()
    {
        if (static::$definition['multilang']) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . static::$definition['table'] . '_lang', false);
        }

        return Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . static::$definition['table'], false);
    }

    public static function createTable($definition = false)
    {
        if (property_exists(debug_backtrace()[0]['class'], 'definition') == true) {
            static::$definition['fields']['id_shop'] = ['type' => self::TYPE_INT, 'size' => 8];
        }

        if (!$definition) {
            $definition = static::$definition;
        }

        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . $definition['table'] . '` ( ';
        $definition['index'] = isset($definition['index']) ? $definition['index'] : $definition['primary'];

        foreach ($definition['fields'] as $key => $col) {
            $sql .= '`' . $key . '` '
            . static::$dbTypes[$col['type']]
                . (isset($col['size']) ? '(' . $col['size'] . ')' : '')
                . ' '
                . ($key == $definition['primary'] && !Tools::strpos($definition['index'], ',') ? ' AUTO_INCREMENT ' : '') . ' '
                . ', ';
            // . ($key == $definition['primary'] ? ' PRIMARY KEY ' : '') . ', ';
        }

        $sql .= ' PRIMARY KEY (`' . str_replace(',', '`,`', $definition['index']) . '`), ';
        $sql = trim($sql, ',');
        $sql .= ' INDEX (`' . str_replace(',', '`,`', $definition['index']) . '`)) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8 COLLATE utf8_general_ci;';
        $result = Db::getInstance()->execute($sql);

        if (isset($definition['after'])) {
            foreach ($definition['after'] as $l) {
                $r = Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . $definition['table'] . ' ' . $l, false);
            }
        }

        // Si es multilang debemos crear las otras tablas
        if ($definition['multilang']) {
            $definition = self::setLangDefinition($definition);
            self::createTable($definition);
        }
    }

    public static function setLangDefinition($definition)
    {
        $id_original_table = 'id_' . $definition['table'];
        $definition['table'] .= '_lang';

        foreach ($definition['fields'] as $k => $field) {
            if ($k != $definition['primary']) {
                if (isset($field['lang'])) {
                    if (!$field['lang']) {
                        unset($definition['fields'][$k]);
                    }
                } else {
                    unset($definition['fields'][$k]);
                }
            }
        }

        $definition['fields']['id_lang'] = ['type' => self::TYPE_INT, 'size' => 3];
        $definition['fields'][$id_original_table] = ['type' => self::TYPE_INT, 'size' => 8];
        $definition['multilang'] = false;
        $definition['index'] = $definition['primary'] . ',id_lang';

        return $definition;
    }

    public function get($id)
    {
        if (!$id) {
            return false;
        }

        if (static::$definition['multilang']) {
            $result = $this->querySelect('t1.' . static::$definition['primary'] . '=' . $id);
        } else {
            $result = $this->querySelect(static::$definition['primary'] . '=' . $id);
        }

        if ($result) {
            $this->trySetVarsObject($this, $result);

            return $result;
        }

        return false;
    }

    public static function queryStaticSelect($where)
    {
        if (static::$definition['multilang']) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . static::$definition['table'] . '` '
                . 'WHERE ' .
                ' ' . $where . '';
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . static::$definition['table'] . '` '
                . 'WHERE ' .
                ' ' . $where . '';
        }

        $result = Db::getInstance()->executeS($sql);

        if ($result) {
            return $result[0];
        }

        return false;
    }

    public function querySelect($where, $onlyfirst = true)
    {
        $definition = static::$definition;

        if (!$this->context) {
            $this->context = Context::getContext();
        }

        if ($definition['multilang']) {
            $where = ' ' . $where . ' ';

            foreach ($definition['fields'] as $k => $l) {
                $pre = 't1.';

                if (isset($l['lang'])) {
                    if ($l['lang']) {
                        $pre = 't2.';
                    }
                }

                $where = str_replace(' ' . $k . ' ', ' ' . $pre . $k . ' ', $where);
            }

            $sql = 'SELECT ' . $this->getSelectSQL() . ' FROM `' . _DB_PREFIX_ . $definition['table'] . '` t1 '
            . 'INNER JOIN ' . _DB_PREFIX_ . $definition['table'] . '_lang t2 on '
            . ' t2.id_' . $definition['table'] . '= t1.' . $definition['primary']
            . ' WHERE ' .
            ' (' . $where . ') AND t1.id_shop = ' . $this->context->shop->id;
            $this->last_query = $sql;
            $result_lang = Db::getInstance()->executeS($sql);
            // Creeamos arrary de los idiomas
            $result = [];

            if (!strpos(' ' . $where, 'id_lang')) {
                foreach ($result_lang as $k => $l) {
                    if (!isset($result[$l[static::$definition['primary']]])) {
                        $result[$l[static::$definition['primary']]] = $l;
                    }

                    foreach ($definition['fields'] as $fk => $fl) {
                        $lang = isset($fl['lang']) ? ($fl['lang'] == true ? true : false) : false;

                        if ($lang) {
                            if (!is_array($result[$l[static::$definition['primary']]][$fk])) {
                                $result[$l[static::$definition['primary']]][$fk] = [];
                            }

                            $result[$l[static::$definition['primary']]][$fk][$l['id_lang']] = $l[$fk];
                        }
                    }
                }

                if ($onlyfirst) {
                    $r = [];

                    foreach ($result as $k => $r) {
                        break;
                    }

                    $result[0] = $r;
                }
            } else {
                $result = $result_lang;
            }
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . static::$definition['table'] . '` '
            . ' WHERE ' .
            ' (' . $where . ') AND id_shop = ' . $this->context->shop->id;
            $this->last_query = $sql;

            $result = Db::getInstance()->executeS($sql);
        }

        if ($result) {
            if (!$onlyfirst) {
                return $result;
            }

            $this->trySetVarsObject($this, $result[0]);

            return $result[0];
        }

        return false;
    }

    public function getSelectSQL($definition = false)
    {
        if (!$definition) {
            $definition = static::$definition;
        }

        $select = '';

        if ($definition['multilang']) {
            foreach ($definition['fields'] as $k => $l) {
                $lang = isset($l['lang']) ? ($l['lang'] == true ? true : false) : false;

                if (!$lang) {
                    $select .= ' t1.' . $k . ' as ' . $k . ', ';
                } else {
                    $select .= ' t2.' . $k . ' as ' . $k . ', ';
                }
            }

            $select .= ' t2.id_lang as id_lang ';

            return $select;
        } else {
            foreach ($definition['fields'] as $k => $l) {
                $select .= ' ' . $k . ', ';
            }

            return trim($select, ', ');
        }
    }

    public function queryCreate($definition = false)
    {
        if (!$definition) {
            $definition = static::$definition;
        }

        if (!$this->context) {
            $this->context = Context::getContext();
        }

        if (!$this->id_shop) {
            $this->id_shop = $this->context->shop->id;
        }

        $table = $definition['table'];
        $sqlFields = '';
        $sqlValues = '';

        if ($this->column_position) {
            $colum_position = $this->column_position;

            if (!$this->$colum_position && isset($definition[$colum_position])) {
                $this->$colum_position = (int) Db::getInstance()->getValue('SELECT max(' . $colum_position . ') FROM ' . $table) + 1;
            }
        }

        foreach ($definition['fields'] as $line => $var) {
            if (true == property_exists(get_class($this), $line) && $var && (!$definition['multilang'] || !isset($var['lang']) || !$var['lang'])) {
                if (($definition['primary'] != $line || $this->{$line}) && (null !== $this->{$line} || 'date_update' == $line || 'date_upd' == $line)) {
                    $sqlFields .= '`' . $line . '`,';

                    if ('date_update' == $line) {
                        $this->{$line} = date('Y-m-d H:i:s', time());
                        $sqlValues .= '"' . $this->{$line} . '",';
                    } elseif (('date_create' == $line || 'date_ini' == $line) && ('' == $this->{$line} || null === $this->{$line} || false == $this->{$line})) {
                        if (!$this->{$line}) {
                            $this->{$line} = date('Y-m-d H:i:s', time());
                        } else {
                            $this->{$line} = date('Y-m-d H:i:s', strtotime($this->{$line}));
                        }

                        $sqlValues .= '"' . $this->{$line} . '",';
                    } elseif (0 === strpos($line, 'date_')) {
                        if (!$this->{$line}) {
                            $this->{$line} = date('Y-m-d H:i:s', time());
                        } else {
                            $this->{$line} = date('Y-m-d H:i:s', strtotime($this->{$line}));
                        }

                        $sqlValues .= '"' . date('Y-m-d H:i:s', $this->dateStringTonumber($this->{$line})) . '",';
                    } else {
                        $sqlValues .= '"' . pSQL(is_array($this->{$line}) ? '' : $this->{$line}, true) . '",';
                    }
                }
            }
        }

        $sql = 'INSERT INTO ' . _DB_PREFIX_ . $table . '(' . trim($sqlFields, ',') . ') values (' .
        trim($sqlValues, ',') . ')';

        $this->last_query = $sql;

        $result = Db::getInstance()->execute($sql);

        if (true == $result) {
            $result = Db::getInstance()->Insert_ID();
        }

        // Si es multilang debemos crear las otras tablas
        if ($definition['multilang'] && $result) {
            // $id_for_lang = 'id_' . $definition['table'];
            $id_for_lang = $definition['primary'];
            $definition = self::setLangDefinition($definition);
            $langs = Language::getLanguages(false);
            $copy_fiels = [];

            foreach (static::$definition['fields'] as $line => $var) {
                $copy_fiels[$line] = $this->{$line};
            }

            foreach ($langs as $l) {
                $this->id_lang = $l['id_lang'];
                $v = $definition['primary'];
                $this->{$v} = false;

                foreach ($definition['fields'] as $line => $var) {
                    $this->{$line} = isset($copy_fiels[$line][$l['id_lang']]) ? $copy_fiels[$line][$l['id_lang']] : null;
                }

                $this->id_lang = $l['id_lang'];
                $this->{$id_for_lang} = $result;
                $this->queryCreate($definition);
            }
        }

        return $result;
    }

    public function queryUpdate($table = false, $object = false, $where = false, $definition = false)
    {
        if (!$definition) {
            $definition = static::$definition;
        }

        if (!$this->context) {
            $this->context = Context::getContext();
        }

        if (!$this->id_shop) {
            $this->id_shop = $this->context->shop->id;
        }

        $table = $definition['table'];
        $object = $this;
        $sqlFields = '';

        foreach ($definition['fields'] as $line => $var) {
            if (true == property_exists(get_class($this), $line) && $var && (!$definition['multilang'] || !isset($var['lang']) || !$var['lang'])) {
                // echo $line . ' ==' . $this->$line . '<br>';
                if (null !== $this->{$line} || 'date_update' == $line || 'date_upd' == $line) {
                    if ('date_update' == $line || 'date_upd' == $line) {
                        $sqlFields .= ' ' . $line . ' = "' . date('Y-m-d H:i:s', time()) . '",';
                    } elseif (0 === strpos($line, 'date_')) {
                        if ($this->{$line}) {
                            $sqlFields .= ' ' . $line . ' = "' . date('Y-m-d H:i:s', strtotime($this->{$line})) . '",';
                        } else {
                            $sqlFields .= ' ' . $line . ' = "' . date('Y-m-d H:i:s', time()) . '",';
                        }
                    } else {
                        $sqlFields .= ' `' . $line . '` = "' . pSQL(is_array($this->{$line}) ? '' : $this->{$line}, true) . '",';
                    }
                }
            }
        }

        $primary = $definition['primary'];
        $sql = 'UPDATE ' . _DB_PREFIX_ . $table . ' SET ' . trim($sqlFields, ',') . ' WHERE'
            . ($where ? ' ' . $where . ' ' : ' ' . $primary . '=' . $this->{$primary});

        $result = Db::getInstance()->execute($sql);

        $this->last_query = $sql;

        if (true == $result) {
            $result = $this->{$primary};
        }

        // Si es multilang debemos crear las otras tablas
        if ($definition['multilang']) {
            // $id_for_lang = 'id_' . $definition['table'];
            $id_for_lang = $definition['primary'];
            $original_primary = $this->{$primary};
            $definition = self::setLangDefinition($definition);
            $langs = Language::getLanguages(false);
            $copy_fiels = [];

            foreach (static::$definition['fields'] as $line => $var) {
                $copy_fiels[$line] = $this->{$line};
            }

            $original_where = $where;

            foreach ($langs as $l) {
                $id_primary = $this->{$primary};
                $this->id_lang = $l['id_lang'];
                $v = $definition['primary'];
                $this->{$v} = false;

                foreach ($definition['fields'] as $line => $var) {
                    $this->{$line} = isset($copy_fiels[$line][$l['id_lang']]) ? $copy_fiels[$line][$l['id_lang']] : null;
                }

                $this->id_lang = $l['id_lang'];
                $this->{$id_for_lang} = $result;
                /* unset($this->$primary);
                unset($this->$id_for_lang); */

                $where = ($original_where ?
                    str_replace($primary, $id_for_lang, $original_where) :
                    ' ' . $id_for_lang . '=' . $original_primary)
                    . ' AND id_lang = ' . $l['id_lang'];

                $this->queryUpdate($table, $object, $where, $definition);
                $this->{$primary} = $id_primary;
            }
        }

        return $result;
    }

    public function queryDelete($id)
    {
        $table = static::$definition['table'];
        $where = static::$definition['primary'] . '=' . $id;
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . $table .
            ' WHERE ' . ($where ? ' ' . $where : '');
        $this->last_query = $sql;

        return Db::getInstance()->execute($sql);
    }

    public function trySetVarsObject(&$object, $vars)
    {
        if (is_array($vars)) {
            foreach ($vars as $key => $var) {
                if (property_exists($object, $key) == true) {
                    $object->$key = $var;
                }
            }
        }
    }

    public function dateStringTonumber($datetime)
    {
        $datetime_arr = explode(' ', $datetime);

        if (count($datetime_arr) == 1) {
            return $datetime;
        }

        $hour = explode(':', $datetime_arr[1]);

        if (!isset($hour[1])) {
            $hour[1] = '00';
        }

        if (!isset($hour[2])) {
            $hour[2] = '00';
        }

        $date = $datetime_arr[0];
        $datevars = explode('/', $date);

        if (count($datevars) == 3) {
            return $this->getMktime($hour[0], $hour[1], $hour[2], $datevars[1], $datevars[0], $datevars[2]);
        } else {
            $date = str_replace('-', '/', $date);
            $datevars = explode('/', $date);
            $Y = $datevars[0] > 1000 ? $datevars[0] : $datevars[2];
            $m = $datevars[1];
            $d = $datevars[2] > 1000 ? $datevars[0] : $datevars[2];

            return $this->getMktime($hour[0], $hour[1], $hour[2], $m, $d, $Y);
        }
    }

    public function getMktime($hour, $minute, $second, $month, $day, $year)
    {
        if (Tools::strlen($hour) == 1) {
            $hour = '0' . $hour;
        } elseif (!$hour) {
            $hour = '00';
        }

        if (Tools::strlen($minute) == 1) {
            $minute = '0' . $minute;
        } elseif (!$minute) {
            $minute = '00';
        }

        if (Tools::strlen($second) == 1) {
            $second = '0' . $second;
        } elseif (!$second) {
            $second = '00';
        }

        if (Tools::strlen($month) == 1) {
            $month = '0' . $month;
        } elseif (!$month) {
            $month = '00';
        }

        if (Tools::strlen($day) == 1) {
            $day = '0' . $day;
        } elseif (!$day) {
            $day = '00';
        }

        if (Tools::strlen($year) == 1) {
            $year = '0' . $year;
        } elseif (!$year) {
            $year = '00';
        }

        $r = strtotime(
            $year . '-' .
            $month . '-' .
            $day . ' ' .
            $hour . ':' .
            $minute . ':' .
            $second . ''
        );

        return $r;
    }

    public function saveDessing()
    {
        if (is_object($this->objecto)) {
            $this->objecto->saveDessing();
            $this->ajaxresults['showSuccessMessage'] = [$this->objecto->l('Settings updated')];
        }
    }

    public function postProcess()
    {
        if (is_object($this->objecto)) {
            $this->objecto->postProcess();
            $this->ajaxresults['showSuccessMessage'] = [$this->objecto->l('Settings updated')];
        }
    }

    public function add($auto_date = true, $null_values = false)
    {
        $this->trySetVarsObject($this, Tools::getAllValues());
        $primary = static::$definition['primary'];

        if ($this->$primary > 0) {
            $res = $this->queryUpdate();
        } else {
            $res = $this->queryCreate();
            $this->$primary = $res;
        }

        $this->getList();
        $this->getCard($this->$primary);

        if (is_object($this->objecto)) {
            $this->ajaxresults['showSuccessMessage'] = [$this->objecto->l('Settings updated')];
        }

        return $res;
    }

    public function delete()
    {
        $this->trySetVarsObject($this, Tools::getAllValues());
        $primary = static::$definition['primary'];

        if ((int) $this->$primary > 0) {
            $this->queryDelete((int) $this->$primary);
        }

        $this->getCard(0);
        $this->getList();

        if (is_object($this->objecto)) {
            $this->ajaxresults['showSuccessMessage'] = [$this->objecto->l('Deleted')];
        }

        return true;
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

    public function getAll()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . static::$definition['table'] . '` ';
        $results = Db::getInstance()->executeS($sql);

        return $results;
    }

    public function getListPagination($function = 'getList')
    {
        $definition = static::$definition;

        if (!$this->context) {
            $this->context = Context::getContext();
        }

        if (!$this->objecto) {
            return;
        }

        $p = Tools::getValue('p');

        if (!$p) {
            $p = $this->p;
        }

        if ($p < 1) {
            $p = 1;
        }

        $filter = '';
        $pagination_url = '';

        if (Tools::getValue('filterlist')) {
            foreach ($this->getFieldsList() as $k => $l) {
                if (Tools::getValue($k)) {
                    $filter .= $k . ' LIKE "%' . Tools::getValue($k) . '%"';
                    $pagination_url .= ' AND ' . $k . '=' . urlencode(Tools::getValue($k));
                }
            }
        }

        if ($this->_listTotal === false) {
            $this->getList();
        }

        $this->context->smarty->assign([
            'functionLibs' => $function,
            'pagination_url' => $pagination_url,
            'definition' => $definition,
            'p' => $p,
            'pages' => (int) ($this->_listTotal / $this->pagination),
        ]);

        $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '' . $this->objecto->name . '/views/templates/admin/form/fields/list-pagination.tpl');

        return $output;
    }

    public function getFilterListForm($fields = false)
    {
        if (!property_exists(get_class($this), 'definition')) {
            return 'No definition defined';
        }

        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $definition = static::$definition['fields'];
        $smarty_filters = [];

        if ($fields) {
            foreach ($definition as $k => $l) {
                $smarty_filters[$k] = Tools::getValue($k);

                if (!in_array($k, $fields)) {
                    unset($definition[$k]);
                }
            }
        }

        $this->context->smarty->assign([
            'definition' => $definition,
            'smarty_filters' => $smarty_filters,
        ]);

        $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '' . $this->objecto->name . '/views/templates/admin/form/fields/list-filter.tpl');

        return $output;
    }

    public function getFieldsList()
    {
        $fields_list = [];

        if ($this->fields_list) {
            $fields_list = $this->fields_list;
        } else {
            foreach (static::$definition['fields'] as $k => $l) {
                $fields_list[$k] = [
                    'title' => $this->trans($k, [], 'Admin.Global'),
                    'filter_key' => 'a!' . $k,
                ];
            }
        }

        if ($this->_extra_fields) {
            foreach ($this->_extra_fields as $k => $l) {
                $fields_list[$k] = $l;
            }
        }

        return $fields_list;
    }

    public function getList($filter = false)
    {
        $id_lang = $this->context->language->id;
        $clname = get_class($this) . 'Controller';
        $p = (int) Tools::getValue('p');

        if ($p < 1) {
            $p = 1;
        }

        $fields_list = $this->getFieldsList();
        $path_controller = dirname(dirname(__FILE__)) . '/controllers/admin/' . $clname . '.php';

        if ((!class_exists($clname) && !file_exists($path_controller)) || fileatime($path_controller) != fileatime(__FILE__)) {
            $p1 = Tools::strpos(Tools::file_get_contents(__FILE__), '**') - 1;
            $p2 = Tools::strpos(Tools::file_get_contents(__FILE__), 'class') - 1;
            $html_doc = Tools::substr(Tools::file_get_contents(__FILE__), $p1, $p2 - $p1);
            $space = '    ';
            $spacen = 1;
            $patterns = [
                "/array \(/" => '[',
                "/^([ ]*)\)(,?)$/m" => '$1]$2',
                "/=>[ ]?\n[ ]+\[/" => '=> [',
                "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
            ];
            $join_arr1 = [
                'VAR_DBPREFIX',
                'VAR_ID_LANG',
                'VAR_ID_SHOP',
            ];
            $join_arr2 = [
                '\' . _DB_PREFIX_ . \'',
                '\' . (int) Context::getContext()->language->id . \'',
                '\' . (int) Context::getContext()->shop->id . \'',
            ];
            $join = str_replace($join_arr1, $join_arr2, $this->_join);
            $html = ('class ' . $clname . ' extends AdminControllerCore' . PHP_EOL . '{' . PHP_EOL .
                str_repeat($space, $spacen) . 'public $id = \'Module' . get_class($this) . '\';' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen) . 'public function __construct($forceControllerName = \'\', $default_theme_name = \'default\')' . PHP_EOL .
                str_repeat($space, $spacen) . '{' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$this->fields_list = ' .
                str_replace(
                    PHP_EOL,
                    PHP_EOL . str_repeat($space, $spacen * 2),
                    preg_replace(array_keys($patterns), array_values($patterns), var_export($fields_list, true)) . ';'
                ) . PHP_EOL .
                (static::$definition['multilang'] ? str_repeat($space, $spacen * 2) . '$this->lang = \'true\';' . PHP_EOL : '') .
                str_repeat($space, $spacen * 2) . '$this->_join = \'' . $join . '\';' . PHP_EOL .
                ($this->_select ? str_repeat($space, $spacen * 2) . '$this->_select = \'' . $this->_select . '\';' . PHP_EOL : '') .
                str_repeat($space, $spacen * 2) . '$this->table = \'' . static::$definition['table'] . '\';' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$this->_defaultOrderBy = \'' . static::$definition['primary'] . '\';' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$this->_default_pagination = \'' . $this->pagination . '\';' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$this->_orderWay = \'DESC\';' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$this->_use_found_rows = true;' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$this->_where = \' AND a.id_shop = \' . (int) Context::getContext()->shop->id;' . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'parent::__construct($forceControllerName, $default_theme_name);' . PHP_EOL .
                str_repeat($space, $spacen) . '}' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen) . 'public function trySetVarsObject(&$object, $vars)' . PHP_EOL .
                str_repeat($space, $spacen) . '{' . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'if (!$object) {' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$object = $this;' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '}' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'if (is_array($vars) || is_object($vars)) {' . PHP_EOL .
                str_repeat($space, $spacen * 3) . 'foreach ($vars as $key => $var) {' . PHP_EOL .
                str_repeat($space, $spacen * 4) . 'if (property_exists($object, $key) == true) {' . PHP_EOL .
                str_repeat($space, $spacen * 5) . '$object->$key = $var;' . PHP_EOL .
                str_repeat($space, $spacen * 4) . '}' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '}' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '}' . PHP_EOL .
                str_repeat($space, $spacen) . '}' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen) . 'public function getList(' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$id_lang,' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$order_by = null,' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$order_way = null,' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$start = 0,' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$limit = null,' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$id_lang_shop = false' . PHP_EOL .
                str_repeat($space, $spacen) . ') {' . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'if ($id_lang) {' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$this->_join = str_replace(\'$id_lang\', $id_lang, $this->_join);' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '}' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'parent::getList(' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$id_lang,' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$order_by,' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$order_way,' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$start,' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$limit,' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '$id_lang_shop' . PHP_EOL .
                str_repeat($space, $spacen * 2) . ');' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'return [' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '\'list\' => $this->_list,' . PHP_EOL .
                str_repeat($space, $spacen * 3) . '\'list_total\' => $this->_listTotal,' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '];' . PHP_EOL .
                str_repeat($space, $spacen) . '}' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen) . 'public function setCustomFilter($string)' . PHP_EOL .
                str_repeat($space, $spacen * 1) . '{' . PHP_EOL .
                str_repeat($space, $spacen * 2) . '$this->_filter .= $string;' . PHP_EOL .
                str_repeat($space, $spacen) . '}' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen) . 'public function getFilter()' . PHP_EOL .
                str_repeat($space, $spacen * 1) . '{' . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'return $this->_filter;' . PHP_EOL .
                str_repeat($space, $spacen) . '}' . PHP_EOL . PHP_EOL .
                str_repeat($space, $spacen) . 'public function getListSql()' . PHP_EOL .
                str_repeat($space, $spacen) . '{' . PHP_EOL .
                str_repeat($space, $spacen * 2) . 'return $this->_listsql;' . PHP_EOL .
                str_repeat($space, $spacen) . '}' . PHP_EOL .
                '}');
            file_put_contents($path_controller, '<?php' . PHP_EOL . $html_doc . PHP_EOL . $html . PHP_EOL);
        }

        if (!class_exists($clname)) {
            require_once $path_controller;
        }

        $cls = new $clname();
        $cls->trySetVarsObject($cls, $this);

        if (Tools::getValue('filterlist')) {
            foreach ($fields_list as $k => $l) {
                $value = Tools::getValue($k);

                if ($value && isset($l['filter_key']) && $l['filter_key']) {
                    $key = str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($cls))) . static::$definition['table'] . 'Filter_' . $l['filter_key'];
                    $this->context->cookie->__set($key, $value);
                } elseif ($value && isset($l['custom_filter']) && $l['custom_filter']) {
                    $sql = ' AND (';

                    foreach ($l['custom_filter'] as $kk => $ll) {
                        $sql .= str_replace('!', '.', $ll) . ' LIKE "%' . $value . '%" OR ';
                    }

                    $sql = rtrim($sql, 'OR ');
                    $sql .= ')';
                    $cls->setCustomFilter($sql);
                }
            }

            $cls->processFilter();
            $this->_filter = $cls->getFilter();
        }

        $this->getFilterListForm($fields_list);
        $p = 0;

        if (Tools::isSubmit('submitFilter' . static::$definition['table'])) {
            $p = (int) Tools::getValue('p');
        }

        // $_POST['submitFilter' . static::$definition['table']] = (int) Tools::getValue('p');
        // dump('submitFilter' . static::$definition['table']);
        $results = false;

        if ($cls->table) {
            $list = $cls->getList(
                $id_lang,
                null,
                null,
                (int) $p,
                $this->pagination
            );
            $this->last_query = $cls->getListSql();
            $this->_list = $results = $list['list'];
            $this->_listTotal = $list['list_total'];
            $this->getListPagination();
        }
        if ($results) {
            if (!file_exists(_PS_MODULE_DIR_ . '/' . $this->objecto->name . '/views/templates/admin/list.tpl')) {
                return;
            }

            $this->context->smarty->assign([
                'list' => $results,
            ]);
            $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/' . $this->objecto->name . '/views/templates/admin/list.tpl');
            $this->ajaxresults['textos']['#listForm'] = $output;

            return $output;
        } else {
            $this->context->smarty->assign([
                'list' => false,
            ]);
        }
    }

    public function l($string, $specific = false, $id_lang = null)
    {
        return $this->objecto->l($string);
    }

    public function getCategorySelector($name, $selectmultiple = true, $values = false, $newDir = false, $display = false)
    {
        $tree = new HelperTreeCategoriesCore($name, $this->l('Category'));
        $tree->_id = $name;
        $tree->setInputName($name);

        if ($values != false) {
            $tree->setSelectedCategories($values);
        }

        $tree->setUseSearch(true);
        $tree->setUseCheckBox($selectmultiple);

        if ($newDir == false) {
            $newDir = _PS_ADMIN_DIR_ . '/themes/default/template/helpers/tree/';
        }

        $tree->setTemplateDirectory($newDir);
        $textvalue = $this->getnamecategoriesfromarray($values);
        $tree->setLang($this->context->language->id);
        $input = '<input type="hidden" class="biteatecategoryinput-ID ' . $name . '" id="input-' . $name . '" name="input-' . $name . '" value="' . ($values != false ? implode(';', $values) : '') . '"></intpu>';
        $html = '<div class="biteatetree ' . ($display ? 'DisplayTree' : '') . '" >' . $input .
        $tree->render() . '</div>';

        return $html;
    }

    public function getNameCategoriesFromArray($array)
    {
        $context = new Context();
        $cont = $context->getContext();
        $id_lang = $cont->cookie->id_lang;
        $cats = $this->getCategoryArrayWithNameAndID($id_lang);

        if (!is_array($array)) {
            return '';
        }

        $namecategories = '';

        foreach ($array as $key) {
            if ($key > 2) {
                $namecategories .= ($namecategories == '' ? '' : ';') . $cats[$key]['name_category'];
            } elseif ($key == 2) {
                $namecategories .= 'all';
            }
        }

        return $namecategories;
    }

    public function getCategoryArrayWithNameAndID($id_lang)
    {
        $categories = Category::getCategories($id_lang);
        $result = [];
        $i = 0;

        foreach ($categories as $cagegorie) {
            foreach ($cagegorie as $key => $c) {
                if ($c['infos']['id_category'] != 1 && $c['infos']['id_category'] != 2) {
                    $result[$c['infos']['id_category']] = [
                        'id_category' => $c['infos']['id_category'],
                        'name_category' => $c['infos']['name'],
                    ];
                    ++$i;
                }
            }
        }

        return $result;
    }

    public static function featureSelector($name, $id_selected = 0, $class = '')
    {
        $context = Context::getContext();
        $query = FeatureCore::getFeatures($context->language->id);
        $html = '<select id="' . $name . '" name="' . $name . '" class="biteateAttributeSelector ' . $class . '">';
        $html .= '<option id="0">--</option>';

        foreach ($query as $q) {
            $html .= '<option id= "' . $q['id_feature'] . '" ' . ($id_selected == $q['id_feature'] ? 'SELECTED' : '') . '>' . $q['name'] . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    public function setTabLang($strin_html, $values = false, $togglelang = false)
    {
        $langs = LanguageCore::getLanguages(true);
        $id_lang = $this->context->language->id;
        $html = '
            <div class=form-group">
                <div class="translations tabbable bordered">
                    <div class="translationsFields tab-content bordered ' . ($togglelang ? ' col-xs-10 ' : '') . '">';

        foreach ($langs as $l) {
            $html .= '<div data-locale="' . $l['iso_code'] . '" class="translatable-field lang-' . $l['id_lang'] . ' tab-pane translation-field translation-label-' . $l['iso_code'] . ' ' . ($id_lang == $l['id_lang'] && !$togglelang ? ' show active ' : '') . '" style="' . ($id_lang == $l['id_lang'] && $togglelang ? ' display:block ' : '') . '">' .
            str_replace(
                '$value',
                '' . (isset($values[$l['id_lang']]) ? $values[$l['id_lang']] : '') . '',
                str_replace('$id_lang', $l['id_lang'], $strin_html)
            ) . '</div> ';
        }

        $html .= '</div>' . ($togglelang ? '<div class="col-xs-2">' . $this->toggleLang() . '</div>' : '') . '</div></div>';

        return $html;
    }

    public function toggleLang()
    {
        $langs = LanguageCore::getLanguages(true);
        $lang = $this->context->language;
        $html = '
            <div class="row">
		<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">';

        foreach ($langs as $l) {
            $html .= '<span  class="translatable-field lang-' . $l['id_lang'] . '" style="' . ($lang->id == $l['id_lang'] ? ' display:block ' : 'display:none') . '">' .
                $l['iso_code'] . '<span class="caret"></span>'
                . '</span>';
        }

        $html .= '
                </button>
                <ul class="dropdown-menu">';

        foreach ($langs as $l) {
            $html .= '
                    <li>
                         <a href="javascript:hideOtherLanguage(' . $l['id_lang'] . ')" tabindex="-1">' . $l['iso_code'] . '</a>
                    </li>';
        }

        $html .= '</ul>
            </div>';

        return $html;
    }

    public function searchProductForm($function = false, $params = false, $target = false)
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $obj = $this->objecto;
        $url_ajax = $this->context->link->getAdminLink('AdminModules', false) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $obj->name . '&tab_module=' . $obj->tab . '&module_name=' . $obj->name;

        if ($params) {
            $url_params = '';

            foreach ($params as $k => $l) {
                $url_ajax .= '&' . $k . '=' . $l;
                $url_params .= '&' . $k . '=' . $l;
            }

            $url_ajax .= '&paramssearch=' . urlencode($url_params);
        }

        if ($target) {
            $url_ajax .= '&searchtarget=' . urlencode($target);
        }

        return '<input type="text" '
            . 'href="' . $url_ajax . '&functionLibs=ajaxSearchProduct&functionCall=' . $function . '"'
            . 'class="alca-keypress form-control forced" name="biteateAjaxSearchProduct" />'
            . '<div class="row biteateAjaxSearchProductList" id="' . ($target ? $target : 'biteateAjaxSearchProductList') . '" ></div>';
    }

    public function ajaxSearchProduct()
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $id_lang = $this->context->language->id;
        $string = Tools::getValue('biteateAjaxSearchProduct');
        $functionCall = Tools::getValue('functionCall');

        if ($string) {
            $list = Product::searchByName($id_lang, $string);
        } else {
            $list = [];
        }

        $obj = $this->objecto;
        $url_ajax = $this->context->link->getAdminLink('AdminModules', false) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $obj->name . '&tab_module=' . $obj->tab . '&module_name=' . $obj->name;

        if (Tools::getValue('paramssearch')) {
            $url_ajax .= Tools::getValue('paramssearch');
        }

        $html = '<table class="panelcloseable">';
        $html .= '<tr><th class="closeparent">' . $this->objecto->l('Cerrar') . '</th></tr>';

        foreach ($list as $l) {
            $html .= '<tr><td>' .
            '<a class=" alca-save forced" href="' . $url_ajax . '&functionLibs=' . $functionCall . '&id=' . $l['id_product'] . '&name=' . urlencode($l['name']) . '">' .
                $l['name'] . '(' . $l['reference'] . ')</a></td></tr>';
        }

        $html .= '</table>';

        if (Tools::getValue('searchtarget')) {
            $this->ajaxresults['textos']['#' . Tools::getValue('searchtarget')] = $html;
        } else {
            $this->ajaxresults['textos']['#biteateAjaxSearchProductList'] = $html;
        }
    }

    public $msg_check_error = false;

    public function checkReady()
    {
        return true;
    }

    public function checKey()
    {
        if (!$this->msg_check_error) {
            $this->msg_check_error = [];
        }

        $msg = '';

        foreach ($this->msg_check_error as $l) {
            $msg .= $l . '<br>';
        }

        $obj = $this->objecto;
        $contextCore = Context::getContext();
        $url_ajax = $contextCore->link->getAdminLink('AdminModules', false) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $obj->name . '&tab_module=' . $obj->tab . '&module_name=' . $obj->name;

        return '<form action="' . $url_ajax . '" class="panel" method="post" enctype="multipart/form-data">
                <h3> ' . $this->l('LICENCIA') . ' </h3>
                <label>' . $this->l('Introduzca el número de factura de compra del módulo') . '</label>
                ' . (count($this->msg_check_error) > 0 ?
            '<div class="errors alert-danger btn-sm">' . $msg . '</div>'
            : '')

        . '
                    <div class="form-group row">
                        <input name="key" id="key"/>
                    </div>
                    <div class="form-group row">
                        <button  type="submit" class="btn btn-default">
                            ' . $this->l('Save') . '
                        </button>
                    </div>
                </form>';
    }

    public function curlCall($url, $postFields = false, $key = false, $noWait = false, $method = false, $headers = false, $opts = false)
    {
        $ch = curl_init();

        if (!$headers) {
            $headers = [];
        }

        $headers[] = 'Content-Type:multipart/form-data';
        $headers[] = 'Expect:';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome');

        if ($key != false) {
            curl_setopt($ch, CURLOPT_USERPWD, $key);
        }

        if ($postFields != false) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($noWait == true) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
        } else {
            if ($noWait != false) {
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, $noWait);
            }
        }

        if ($method) {
            curl_setopt($ch, CURLOPT_PUT, true);

            if ($method == CURLOPT_PUT) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, []);
            }
        }

        if ($opts) {
            foreach ($opts as $k => $l) {
                curl_setopt($ch, $k, $l);
            }
        }

        $res = curl_exec($ch);

        return $res;
    }

    public function getInput($id, $value = false, $forcetype = false)
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        if (isset(static::$definition['fields'][$id])) {
            $definition = static::$definition['fields'][$id];
        } else {
            $definition = [
                'type' => self::TYPE_HTML,
            ];
        }

        $input = [
            'language' => [
                'id_lang' => $this->context->language,
            ],
            'input' => [
                'name' => $id,
                'id' => $id,
                'value' => [
                    $id => $value,
                ],
                'type' => 'text',
                'lang' => (isset($definition['lang']) ? ($definition['lang'] ? true : false) : false),
            ],
        ];

        if ($forcetype) {
            $definition['type'] = $forcetype;
        }

        $input['input']['id'] = $id;
        $input['input']['maxlength'] = isset($definition['size']) ? $definition['size'] : false;
        $input['input']['maxchar'] = isset($definition['size']) ? $definition['size'] : false;

        if ($definition['type'] == self::TYPE_INT || $definition['type'] == self::TYPE_FLOAT) {
            $tpl = 'input-number';
            $input['input']['type'] = 'number';
        } elseif ($definition['type'] == self::TYPE_HTML) {
            $tpl = 'input-textarea';
            $input['input']['type'] = 'textarea';
            $input['input']['autoload_rte'] = true;
            $input['input']['id'] = time() . '_' . $id;
        } elseif ($definition['type'] == 'switch') {
            $tpl = 'input-switch';
            $input['input']['type'] = 'switch';
            $input['input']['values'] = [
                [
                    'id' => $id . '_on',
                    'value' => 1,
                    'label' => $this->l('Yes'),
                ],
                [
                    'id' => $id . '_off',
                    'value' => 0,
                    'label' => $this->l('No'),
                ],
            ];
        } else {
            $tpl = 'input-text';
        }
        // $input['input']['id'] = 'ALCA_BASE_INPUT_' . Tools::strtoupper($input['input']['type']);

        $this->context->smarty->assign($input);
        $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/' . $this->objecto->name . '/views/templates/admin/form/fields/' . $tpl . '.tpl');

        return $output;
    }

    public function getTplHooks($filter = false)
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $res = $this->objecto->getPossibleHooksList();

        if (isset($res[0])) {
            if (!isset($res[0]['registered'])) {
                $registeredHookList = Hook::getHookModuleList();

                foreach ($res as $k => $l) {
                    $res[$k]['registered'] = !empty($registeredHookList[$l['id_hook']][$this->objecto->id]);
                }
            }
        }

        foreach ($res as $k => $l) {
            if (strpos($l['name'], 'displayAdmin') === 0 || strpos($l['name'], 'displayBackOffice') === 0 || strpos($l['name'], 'displayDashboard') === 0 || strpos($l['name'], 'Action') === 0) {
                unset($res[$k]);
            }
        }

        $res = $this->shortHooks($res);

        if ($filter) {
            unset($res['nopage']);

            foreach ($res['bypage'] as $k => $l) {
                if (!in_array($k, $filter)) {
                    unset($res['bypage'][$k]);
                }
            }
        }

        $this->context->smarty->assign([
            'hooks' => $res,
        ]);
        $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/' . $this->objecto->name . '/views/templates/admin/hooks.tpl');
        $this->ajaxresults['textos']['#listHooks'] = $output;

        return $output;
    }

    public function shortHooks($res)
    {
        $pages = [
            'header' => [
                'display' => $this->objecto->l('Header'),
                'items' => [
                    'p0' => ['displayNavFullWidth', 'displayNav', 'displayNav1', 'displayNav2'],
                    'p1' => ['displayAfterBodyOpeningTag', 'displayBanner', 'displayAuthenticateFormBottom', 'displayTop'],
                    'p4' => ['displayBeforeHeadClosingTag'],
                ],
            ],
            'footer' => [
                'display' => $this->objecto->l('Footer'),
                'items' => [
                    'p1' => ['displayFooter'],
                    'p2' => ['displayFooterBefore'],
                    'p3' => [''],
                    'p4' => ['displayBeforeBodyClosingTag'],
                ],
            ],
            'home' => [
                'display' => $this->objecto->l('Home'),
                'items' => [
                    'p1' => ['displayHome'],
                    'p4' => [''],
                ],
            ],
            'product' => [
                'display' => $this->objecto->l('Product'),
                'items' => [
                    'p1' => ['displayAfterProductThumbs', 'displayLeftColumnProduct'],
                    'p2' => [''],
                    'p3' => [''],
                    'p4' => ['displayAttributeForm', 'displayAttributeGroupForm', 'displayAttributeGroupPostProcess'],
                    'p5' => ['displayFooterProduct'],
                ],
            ],
            'category' => [
                'display' => $this->objecto->l('Category'),
                'items' => [
                    'p1' => ['displayLeftColumn'],
                ],
            ],
            'createacoount' => [
                'display' => $this->objecto->l('Create account'),
                'items' => [
                    'p1' => [''],
                    'p2' => ['displayCreateAccountEmailFormBottom'],
                    'p5' => ['displayCustomerLoginFormAfter'],
                ],
            ],
            'account' => [
                'display' => $this->objecto->l('Account'),
                'items' => [
                    'p0' => ['displayCustomerAccountFormTop'],
                    'p1' => ['displayCustomerAccount'],
                    'p2' => ['displayCustomerAccountForm'],
                    'p5' => ['displayMyAccountBlock'],
                ],
            ],
            'cart' => [
                'display' => $this->objecto->l('Cart'),
                'items' => [
                    'p1' => ['displayCart'],
                    'p2' => ['displayAfterCarrier', 'displayBeforeCarrier', 'displayCarrierExtraContent', 'displayCarrierList'],
                    'p4' => ['displayCrossSellingShoppingCart'],
                    'p5' => ['displayCartExtraProductActions'],
                ],
            ],
            'orderconfirmation' => [
                'display' => $this->objecto->l('Order Confirmation'),
                'items' => [
                    'p1' => ['displayOrderConfirmation'],
                ],
            ],
        ];

        $results = [
            'bypage' => [],
            'nopage' => [],
        ];

        foreach ($res as $k => $l) {
            foreach ($pages as $k2 => $l2) {
                foreach ($l2['items'] as $pos => $items) {
                    if (in_array($l['name'], $items)) {
                        $results['bypage'][$k2]['page'] = $l2['display'];
                        $results['bypage'][$k2]['items'][$pos][$k] = $l;
                        $results['bypage'][$k2]['items'][$pos][$k]['namepage'] = $k2;
                        $results['bypage'][$k2]['items'][$pos][$k]['page'] = $l2['display'];
                        $results['bypage'][$k2]['items'][$pos][$k][]['pos'] = $pos;
                        unset($res[$k]);
                    }
                }
            }
        }

        $results['nopage'] = $res;

        return $results;
    }

    public function setHooks()
    {
        $hooks = Tools::getValue('hooks');
        $res = $this->objecto->getPossibleHooksList();

        foreach ($res as $r) {
            if ($r['name'] && $r['name'] != '' && strpos($r['name'], 'displayAdmin') !== 0 && strpos($r['name'], 'displayBackOffice') !== 0 && strpos($r['name'], 'displayDashboard') !== 0 && strpos($r['name'], 'Action') !== 0) {
                if (in_array($r['name'], $hooks)) {
                    if (!$r['registered']) {
                        $this->objecto->registerHook($r['name']);
                    }
                } else {
                    if ($r['registered']) {
                        $this->objecto->unregisterHook($r['name']);
                    }
                }
            }
        }

        $this->ajaxresults['showSuccessMessage'] = [$this->objecto->l('Settings updated')];
    }

    public function getSetCsv()
    {
        if (Tools::getValue('accionCSV') == 'set' && count(static::$definition['fields']) > 1) {
            $file = Tools::fileAttachment('file');
            $gestor = fopen($file['tmp_name'], 'r', false);
            $fila = 0;
            $csv = [];
            $title = [];

            while (($datos = fgetcsv($gestor, 0, ';')) !== false) {
                $numero = count($datos);

                for ($c = 0; $c < $numero; ++$c) {
                    if ($fila == 0) {
                        $title = $datos;
                    } else {
                        foreach ($title as $tk => $tl) {
                            $csv[$fila][$tl] = $datos[$tk];
                        }
                    }
                }

                ++$fila;
            }

            foreach ($csv as $k => $l) {
                $primary = static::$definition['primary'];
                $this->$primary = false;
                $this->trySetVarsObject($this, $l);

                if ($this->$primary > 0) {
                    $res = $this->queryUpdate();
                } else {
                    $res = $this->queryCreate();
                    $this->id = $res;
                }
            }

            return $this->getList();
        }

        if (Tools::getValue('accionCSV') == 'download' && count(static::$definition['fields']) > 1) {
            $csv = '';
            $csv_line = '';

            foreach (static::$definition['fields'] as $k => $l) {
                if ($k != 'id') {
                    $vars = $l;
                    $csv .= ($csv != '' ? ';' : '') . $k;
                    $csv_line .= ($csv != '' ? ';' : '') . '';
                }
            }

            $csv .= PHP_EOL;
            $csv_line .= PHP_EOL;
            ob_clean();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="example.csv"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . (Tools::strlen($csv) + Tools::strlen($csv_line) + Tools::strlen($csv_line)));
            echo $csv;
            echo $csv_line;
            echo $csv_line;

            exit;
        }

        return [
            'legend' => [
                'title' => $this->objecto->l('Upload CSV'),
                'icon' => 'icon-file',
                /* 'image' => $this->alca_url_module.'/logo.png', */
            ],
            'tpl' => [
                'file' => 'csv',
                'id' => 'cardCSV',
            ],
        ];
    }

    public function getFileManager()
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $path = $this->context->link->getBaseLink() . basename(_PS_ADMIN_DIR_) . '/filemanager/dialog.php';

        include $path;
    }

    /* public static function checkExecutionTime($params = [], $output = false, $force = false)
    {
        if (function_exists('sys_getloadavg') && !Tools::getValue('ajaxaction')) {
            $carga = sys_getloadavg();

            if ($carga[0] > 30) {
                return false;
                // echo 'Memoria Cargada '. date('Y/m/d', time()) .' ' . date('H:i:s') . ' ' . $carga[0];
            }
        }

        $QUERY_STRING = $_SERVER['QUERY_STRING'];
        $QUERY_STRING_ARRAY = explode('&', $QUERY_STRING);
        $page_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
        $page_url = explode('?', $page_url)[0];
        $parms_url = '';

        foreach ($QUERY_STRING_ARRAY as $k => $l) {
            $var_array = explode('=', $l);

            if (array_key_exists($var_array[0], $params)) {
                $parms_url .= ($parms_url ? '&' : '') . $var_array[0] . '=' . $params[$var_array[0]];
                unset($params[$var_array[0]]);
            } elseif (isset($var_array[1])) {
                $parms_url .= ($parms_url ? '&' : '') . $var_array[0] . '=' . $var_array[1];
            }
        }

        foreach ($params as $k => $l) {
            $parms_url .= ($parms_url ? '&' : '') . $k . '=' . $l;
        }

        if ($parms_url) {
            $page_url .= '?' . $parms_url;
        }

        $max_execution_time = ini_get('max_execution_time');

        if (!$max_execution_time) {
            $max_execution_time = 60;
        }

        if (isset($_SERVER['HTTP_CDN_LOOP']) && $_SERVER['HTTP_CDN_LOOP'] == 'cloudflare') {
            $max_execution_time = 50;
        }

        global $apmtime_checkexecutiontime;

        if (!$apmtime_checkexecutiontime) {
            $apmtime_checkexecutiontime = (int) time();
        }

        $time = $apmtime_checkexecutiontime;

        if ((time() - $time) > $max_execution_time - 15 || $force) {
            if (!$output) {
                $output = [];
            }

            $page_url = str_replace('&liteDisplaying=1', '', $page_url);
            $page_url = str_replace('&ajaxaction=true', '', $page_url);
            $output['callajax'] = $page_url;
            echo json_encode($output);

            exit;
        }

        return false;
    } */
}
