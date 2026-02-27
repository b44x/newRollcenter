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

class AlcaurlcanonicalFormUtils extends Alcaurlcanonical
{
    public static $module_class_loaded = false;

    public $alca_dirname = '';
    public $alca_url_module = '';
    public $languages = [];

    const ALCA_INPUT_HIDDEN = 'ALCA_BASE_INPUT_HIDDEN';
    const ALCA_INPUT_TEXT_LANG = 'ALCA_BASE_INPUT_TEXT_LANG';
    const ALCA_INPUT_TEXT = 'ALCA_BASE_INPUT_TEXT';
    const ALCA_INPUT_TAGS_LANG = 'ALCA_BASE_INPUT_TAGS_LANG';
    const ALCA_INPUT_TAGS = 'ALCA_BASE_INPUT_TAGS';
    const ALCA_INPUT_TEXTBUTTON = 'ALCA_BASE_INPUT_TEXTBUTTON';
    const ALCA_INPUT_NUMBER = 'ALCA_BASE_INPUT_NUMBER';
    const ALCA_INPUT_COLOR = 'ALCA_BASE_INPUT_COLOR';
    const ALCA_INPUT_DATE = 'ALCA_BASE_INPUT_DATE';
    const ALCA_INPUT_DATETIME = 'ALCA_BASE_INPUT_DATETIME';
    const ALCA_INPUT_SWAP = 'ALCA_BASE_INPUT_SWAP';
    const ALCA_INPUT_SELECT_BLOCK = 'ALCA_BASE_INPUT_SELECT_BLOCK';
    const ALCA_INPUT_SELECT_GROUP_BLOCK = 'ALCA_BASE_INPUT_SELECT_GROUP_BLOCK';
    const ALCA_INPUT_SELECT = 'ALCA_BASE_INPUT_SELECT';
    const ALCA_INPUT_RADIO = 'ALCA_BASE_INPUT_RADIO';
    const ALCA_INPUT_SWITCH = 'ALCA_BASE_INPUT_SWITCH';
    const ALCA_INPUT_TEXTAREA = 'ALCA_BASE_INPUT_TEXTAREA';
    const ALCA_INPUT_TEXTAREA_LANG = 'ALCA_BASE_INPUT_TEXTAREA_LANG';
    const ALCA_INPUT_CHECKBOX = 'ALCA_BASE_INPUT_CHECKBOX';
    const ALCA_INPUT_PASSWORD = 'ALCA_BASE_INPUT_PASSWORD';
    const ALCA_INPUT_FORM_GROUP = 'ALCA_BASE_INPUT_FORM_GROUP';
    const ALCA_INPUT_FILE_LANG = 'ALCA_BASE_INPUT_FILE_LANG';
    const ALCA_INPUT_FILE = 'ALCA_BASE_INPUT_FILE';
    const ALCA_INPUT_CATEGORIES = 'ALCA_BASE_INPUT_CATEGORIES';
    const ALCA_INPUT_SEARCH_PRODUCTS = 'ALCA_INPUT_SEARCH_PRODUCTS';
    const ALCA_INPUT_SEARCH_MANUFACTURERS = 'ALCA_INPUT_SEARCH_MANUFACTURERS';
    const ALCA_INPUT_SEARCH_SUPPLIERS = 'ALCA_INPUT_SEARCH_SUPPLIERS';
    const ALCA_INPUT_HTML = 'ALCA_BASE_INPUT_HTML';

    public $context;

    public function initiate()
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        $nclass = $this->loadClass();

        // If there is no database driver, we load the basic one
        if ($nclass == 0) {
            $class = Tools::ucfirst($this->name) . 'Libs';
            $classLibs = new $class();
            $classLibs->initiate($this);
        }

        $urlform = 'index.php?controller=AdminModules&amp;configure=' . $this->name . '&amp;
        tab_module=administration&amp;module_name=' . $this->name . '&amp;token=' . Tools::getAdminTokenLite('AdminModules');
        $this->context->smarty->assign([
            'modulepath' => _PS_MODULE_DIR_ . '/' . $this->name,
            'urlform' => $urlform,
            'ps_version' => _PS_VERSION_,
            'module_version' => $this->version,
            'module_name' => $this->name,
            'module_name_display' => $this->displayName,
            'fields' => $this->getBasicConigFormFieldsTemplate(),
            'languages' => Language::getLanguages(),
            'defaultFormLanguage' => (int) $this->context->employee->id_lang,
            'iso' => $this->context->language->iso_code,
            'path_css' => file_exists(_PS_CORE_DIR_ . '/js/tiny_mce/langs/' . $this->context->language->iso_code . '.js') ? $this->context->language->iso_code : 'en',
            'ad' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
            'baseDir' => __PS_BASE_URI__,
            'tinymce' => true,
            'urlimages' => $this->alca_url_module . 'img/',
            'urlfiles' => $this->alca_url_module . 'files/',
        ]);
        $this->context->controller->addCSS($this->_path . '/views/css/back.css');
        $this->context->controller->addCSS($this->_path . '/views/css/flags.css');
    }

    public function getBasicConigFormFieldsTemplate()
    {
        $array = $this->getConfigFormFieldstemplate();

        return $array;
    }

    /**
     * Generate Tree Categories
     *
     * @param array $selected_cat
     * @return template
     */
    public static function generateTreeCategories($name, $selected_cat)
    {
        $root = Category::getRootCategory();
        $categories = [];

        if ($selected_cat) {
            foreach ($selected_cat as $category) {
                $categories[] = $category;
            }
        }

        $tree = new HelperTreeCategories('categories-treeview');
        $tree->setRootCategory((int) $root->id)
            ->setInputName($name)
            ->setUseCheckBox(true)
            ->setUseSearch(true)
            ->setSelectedCategories($categories);

        return $tree->render();
    }

    /**
     * Method for fix default value of method get of class Configuration in version PS < 1.7.0.0
     *
     * @param string $name
     * @param array|string|null $default_value
     * @param int $id_lang
     *
     * @return array|string
     */
    protected function getValue($name, $default_value, $id_lang = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return Configuration::get($name, $id_lang, null, null, $default_value);
        } else {
            if (Configuration::hasKey($name, $id_lang, null, null)) {
                return Configuration::get($name, $id_lang, null, null);
            }
            return $default_value;
        }
    }

    /**
     * Load value associative for field of form
     * @param array $input
     * @param int $id_lang
     * @param string|int $default_value
     *
     * @return string|int $value
     */
    protected function loadValueAssociative($value, $input)
    {
        $context = Context::getContext();
        $values = [];

        if (isset($value) && is_array($value) && !empty($value)) {
            foreach ($value as $val) {
                if (isset($input['ajax_method']) && $input['ajax_method'] == 'products_list') {
                    $obj = new Product((int) $val, false, $context->language->id);

                    if (Validate::isLoadedObject($obj)) {
                        $id_image = Product::getCover($obj->id);
                        $url_image = (isset($id_image['id_image'])) ? $context->link->getImageLink($obj->link_rewrite, $id_image['id_image'], version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ImageType::getFormattedName('small') : ImageType::getFormattedName('small')) : $this->alca_url_module . 'views/img/icon_default.jpg';
                        $values[] = [
                            'id' => $obj->id,
                            'name' => $obj->name,
                            'image' => $url_image,
                            'reference' => isset($obj->reference) ? $obj->reference : null,
                        ];
                    }
                } elseif (isset($input['ajax_method']) && $input['ajax_method'] == 'manufacturers_list') {
                    $obj = new Manufacturer((int) $val, $context->language->id);

                    if (Validate::isLoadedObject($obj)) {
                        if (!file_exists(_PS_MANU_IMG_DIR_ . $obj->id . version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? '-' . ImageType::getFormattedName('small') . '.jpg' : '-' . ImageType::getFormattedName('small') . '.jpg')) {
                            $url_image = $this->alca_url_module . 'views/img/icon_default.jpg';
                        } else {
                            $url_image = str_replace('http://', Tools::getShopProtocol(), version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? $context->link->getManufacturerImageLink($obj->id, version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ImageType::getFormattedName('small') : ImageType::getFormattedName('small')) : _THEME_MANU_DIR_ . $obj->id . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? '-' . ImageType::getFormattedName('small') . '.jpg' : '-' . ImageType::getFormattedName('small') . '.jpg'));
                        }

                        $values[] = [
                            'id' => $obj->id,
                            'name' => $obj->name,
                            'image' => $url_image,
                        ];
                    }
                } elseif (isset($input['ajax_method']) && ($input['ajax_method'] == 'suppliers_list')) {
                    $obj = new Supplier((int) $val, $context->language->id);

                    if (Validate::isLoadedObject($obj)) {
                        if (!file_exists(_PS_SUPP_IMG_DIR_ . $obj->id . version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? '-' . ImageType::getFormattedName('small') . '.jpg' : '-' . ImageType::getFormattedName('small') . '.jpg')) {
                            $url_image = $this->alca_url_module . 'views/img/icon_default.jpg';
                        } else {
                            $url_image = str_replace('http://', Tools::getShopProtocol(), version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? $context->link->getSupplierImageLink($obj->id, version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ImageType::getFormattedName('small') : ImageType::getFormattedName('small')) : _THEME_SUP_DIR_ . $obj->id . (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? '-' . ImageType::getFormattedName('small') . '.jpg' : '-' . ImageType::getFormattedName('small') . '.jpg'));
                        }

                        $values[] = [
                            'id' => $obj->id,
                            'name' => $obj->name,
                            'image' => $url_image,
                        ];
                    }
                } else {
                    return null;
                }
            }

            if (!empty($values)) {
                $value = $values;

                return $value;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Load value for field of form
     *
     * @param array $input
     * @param int $id_lang
     * @param string|int $default_value
     *
     * @return string|int $value
     */
    protected function loadValue($input, $id_lang = null, $default_value = null)
    {
        if (isset($id_lang)) {
            if (Configuration::hasKey($input['name'], $id_lang)) {
                $value = $this->getValue($input['name'], null, $id_lang);
            } else {
                if ($default_value == null) {
                    $value = $this->getValue($input['name'], $default_value, $id_lang);
                } else {
                    $value = $this->getValue($input['name'], $default_value, $id_lang);
                }
            }
        } else {
            if (in_array($input['type'], ['swap', 'select', 'checkbox', 'group', 'categories'])) {
                if (Configuration::hasKey($input['name'])) {
                    $value = explode(',', Configuration::get($input['name']));
                } else {
                    if ($default_value == null) {
                        $value = $this->getValue($input['name'], $default_value);
                    } else {
                        $value = $this->getValue($input['name'], $default_value);
                    }
                }
            } elseif ($input['type'] == 'search') {
                if (Configuration::hasKey($input['name'])) {
                    $value = $this->loadValueAssociative(explode(',', $this->getValue($input['name'], $default_value)), $input);
                } else {
                    $value = $this->loadValueAssociative($this->getValue($input['name'], $default_value), $input);
                }
            } else {
                $value = $this->getValue($input['name'], $default_value);
            }
        }

        return $value;
    }

    /**
     * Load values for fields of form
     *
     * @param array $form_values
     *
     * @return array $form_values with value
     */
    protected function loadValues($form_values, $languages = [])
    {
        foreach ($form_values as $key => $form_value) {
            if (isset($form_value['input'])) {
                foreach ($form_value['input'] as $k => $input) {
                    if (isset($input['lang']) && $input['lang']) {
                        foreach ($languages as $language) {
                            $field_value = null;

                            if (isset($input['default_value'][$input['name']]) && is_array($input['default_value'][$input['name']]) && isset($input['default_value'][$input['name']][$language['id_lang']])) {
                                $field_value = $this->loadValue($input, $language['id_lang'], $input['default_value'][$input['name']][$language['id_lang']]);
                                $form_values[$key]['input'][$k]['value'][$input['name']][$language['id_lang']] = $field_value;
                            } else {
                                $field_value = $this->loadValue($input, $language['id_lang'], $input['default_value'][$input['name']][$language['id_lang']]);
                                $form_values[$key]['input'][$k]['value'][$input['name']][$language['id_lang']] = $field_value;
                            }
                        }
                    } else {
                        $field_value = null;

                        if ($input['type'] == 'categories') {
                            $field_value = $this->loadValue($input, null, $input['default_value'][$input['name']]);
                            $form_values[$key]['input'][$k]['categories'] = self::generateTreeCategories($input['name'], $field_value);
                            $form_values[$key]['input'][$k]['value'][$input['name']] = $field_value;
                        } else {
                            if (isset($input['default_value'][$input['name']]) && !is_array($input['default_value'][$input['name']])) {
                                $field_value = $this->loadValue($input, null, $input['default_value'][$input['name']]);
                            } elseif (isset($input['default_value'][$input['name']]) && is_array($input['default_value'][$input['name']])) {
                                $field_value = $this->loadValue($input, null, $input['default_value'][$input['name']]);
                            } else {
                                $field_value = $this->loadValue($input, null, null);
                            }

                            $form_values[$key]['input'][$k]['value'][$input['name']] = $field_value;
                        }
                    }
                }
            }
        }

        return $form_values;
    }

    /**
     * UploadImages
     *
     * @param array $value ($_FILE)
     * @param array $field
     * @param int $id_lang
     *
     * @return array|string
     */
    protected function uploadFiles($file, $input, $id_lang = null)
    {
        $values = [];
        $errors = [];
        $imagesize = [];

        if (isset($id_lang)) {
            $values[$input['name']][$id_lang] = '';
            $type = Tools::strtolower(Tools::substr(strrchr($file[$input['name'] . '_' . $id_lang]['name'], '.'), 1));
            $mime = $file[$input['name'] . '_' . $id_lang]['type'];

            if (isset($input['is_image']) && $input['is_image']) {
                $imagesize = @getimagesize($file[$input['name'] . '_' . $id_lang]['tmp_name']);
            }

            if (isset($input['is_image']) && $input['is_image'] && empty($imagesize)) {
                $values[$input['name']][$id_lang] = $this->getValue($input['name'], null, $id_lang);
            } elseif (!isset($file[$input['name'] . '_' . $id_lang]['tmp_name']) || empty($file[$input['name'] . '_' . $id_lang]['tmp_name'])) {
                $values[$input['name']][$id_lang] = $this->getValue($input['name'], null, $id_lang);
            } else {
                if (isset($input['extensions']) && $input['extensions']) {
                    if (!in_array(
                        Tools::strtolower(
                            Tools::substr(
                                strrchr($mime, '/'),
                                1
                            )
                        ),
                        $input['extensions']
                    ) && !in_array($type, $input['extensions'])) {
                        $errors[] = $this->l('Valid extensions: ') . implode(', ', $input['extensions']);
                    }
                }

                if (isset($input['max_size']) && $input['max_size']) {
                    if ((int) $file[$input['name'] . '_' . $id_lang]['size'] > $input['max_size']) {
                        $errors[] = $this->l('Size exceeded. Maximum size allowed: ') . Tools::formatBytes($input['max_size']);
                    }
                }

                if (empty($errors)) {
                    $temp_name = null;

                    if (isset($input['is_image']) && $input['is_image']) {
                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    }

                    do {
                        $salt = sha1(microtime());
                    } while (file_exists($this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . $salt . '_' . $file[$input['name'] . '_' . $id_lang]['name']));

                    if (isset($input['is_image']) && $input['is_image']) {
                        if (!$temp_name || !move_uploaded_file($file[$input['name'] . '_' . $id_lang]['tmp_name'], $temp_name)) {
                            $errors[] = $this->l('An error occurred while uploading the temporary file.');

                            return $errors;
                        } elseif (!ImageManager::resize($temp_name, $this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . $salt . '_' . $file[$input['name'] . '_' . $id_lang]['name'], null, null, $type)) {
                            $errors[] = $this->l('An error occurred during the process of uploading the file.');

                            return $errors;
                        }

                        if ($temp_name) {
                            if ($temp_name != null) {
                                @unlink($temp_name);
                            }
                        }
                    } else {
                        if (!copy($file[$input['name'] . '_' . $id_lang]['tmp_name'], $this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . $salt . '_' . $file[$input['name'] . '_' . $id_lang]['name'])) {
                            $errors[] = $this->l('An error occurred during the process of uploading the file.');

                            return $errors;
                        }
                    }

                    $values[$input['name']][$id_lang] = $salt . '_' . $file[$input['name'] . '_' . $id_lang]['name'];
                    if (isset($input['value']) && pathinfo($input['value'][$input['name']][$id_lang])['basename'] !== 'default.jpg') {
                        @unlink($this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . pathinfo($input['value'][$input['name']][$id_lang])['basename']);
                    }
                } else {
                    return $errors;
                }
            }

            return $values[$input['name']][$id_lang];
        } else {
            $values[$input['name']] = '';
            $type = Tools::strtolower(Tools::substr(strrchr($file[$input['name']]['name'], '.'), 1));
            $mime = $file[$input['name']]['type'];

            if (isset($input['is_image']) && $input['is_image']) {
                $imagesize = @getimagesize($file[$input['name']]['tmp_name']);
            }

            if (isset($input['is_image']) && $input['is_image'] && empty($imagesize)) {
                $values[$input['name']] = self::getValue($input['name'], null);
            } elseif (!isset($file[$input['name']]['tmp_name']) || empty($file[$input['name']]['tmp_name'])) {
                $values[$input['name']] = self::getValue($input['name'], null);
            } else {
                if (isset($input['extensions']) && $input['extensions']) {
                    if (!in_array(
                        Tools::strtolower(
                            Tools::substr(
                                strrchr($mime, '/'),
                                1
                            )
                        ),
                        $input['extensions']
                    ) && !in_array($type, $input['extensions'])) {
                        $errors[] = 'Valid extensions: ' . implode(
                            ', ',
                            $input['extensions']
                        );
                    }
                }

                if (isset($input['max_size']) && $input['max_size']) {
                    if ((int) $file[$input['name']]['size'] > $input['max_size']) {
                        $errors[] = $this->l('Size exceeded. Maximum size allowed: ') . Tools::formatBytes($input['max_size']);
                    }
                }

                if (empty($errors)) {
                    $temp_name = null;

                    if (isset($input['is_image']) && $input['is_image']) {
                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    }

                    do {
                        $salt = sha1(microtime());
                    } while (file_exists($this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . $salt . '_' . $file[$input['name']]['name']));

                    if (isset($input['is_image']) && $input['is_image']) {
                        if (!$temp_name || !move_uploaded_file($file[$input['name']]['tmp_name'], $temp_name)) {
                            $errors[] = $this->l('An error occurred while uploading the temporary file.');

                            return $errors;
                        } elseif (!ImageManager::resize($temp_name, $this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . $salt . '_' . $file[$input['name']]['name'], null, null, $type)) {
                            $errors[] = $this->l('An error occurred during the process of uploading the file.');

                            return $errors;
                        }

                        if ($temp_name) {
                            if ($temp_name != null) {
                                @unlink($temp_name);
                            }
                        }
                    } else {
                        if (!copy($file[$input['name']]['tmp_name'], $this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . $salt . '_' . $file[$input['name']]['name'])) {
                            $errors[] = $this->l('An error occurred during the process of uploading the file.');

                            return $errors;
                        }
                    }

                    $values[$input['name']] = $salt . '_' . $file[$input['name']]['name'];

                    if (isset($input['value']) && pathinfo($input['value'][$input['name']])['basename'] !== 'default.jpg') {
                        @unlink($this->alca_dirname . '/' . ((isset($input['is_image']) && $input['is_image']) ? 'img' : 'files') . '/' . pathinfo($input['value'][$input['name']])['basename']);
                    }
                } else {
                    return $errors;
                }
            }

            return $values[$input['name']];
        }
    }

    /**
     * InstallTab - Add Tab inside
     *
     * @param string $name (Module Admin Controller name)
     * @param string $module (Name of module)
     * @param string $display_name (Public name of module)
     * @param bool $active
     * @param string $parent_class_name (See column class_name table '._DB_PREFIX_.'tab of database)
     *
     * @return bool
     */
    protected function installTab($name, $module, $display_name, $active = 1, $parent_class_name = null)
    {
        $tab = new Tab();
        $tab->active = $active;
        $tab->class_name = $name;
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $display_name;
        }

        if ($parent_class_name == null) {
            $tab->id_parent = 0;
        } else {
            $tab->id_parent = (int) Tab::getIdFromClassName($parent_class_name);
        }

        $tab->module = $module;

        if (!$tab->add()) {
            return false;
        }

        return true;
    }

    /**
     * UninstallTab
     * @param int $id_tab
     *
     * @return bool
     */
    protected function uninstallTab($id_tab)
    {
        $tab = new Tab($id_tab);

        if (!$tab->delete()) {
            return false;
        }

        return true;
    }

    /**
     * Save form data.
     */
    public function postProcess()
    {
        $form_values = $this->getConfigFormFieldstemplate();
        $display_errors = [];

        foreach ($form_values as $key => $form) {
            if (isset($form['input'])) {
                foreach ($form['input'] as $k => $input) {
                    $field_value = $form['input'][$k]['value'];
                    $validate = isset($input['validate']) && $input['validate'] ? $input['validate'] : false;

                    if (isset($input['lang']) && $input['lang'] == true) {
                        // MULTILANGUAGE
                        foreach ($this->languages as $language) {
                            if ($input['type'] == 'file') {
                                $value_field = $this->uploadFiles($_FILES, $input, $language['id_lang']);

                                if (is_array($value_field)) {
                                    foreach ($value_field as $error) {
                                        $display_errors[$input['name']][] = $this->l('Invalid value for: ') . '<strong>' . $input['label'] . ' (' . Tools::strtoupper($language['iso_code']) . ')</strong> => <strong>"' . $error . '"</strong>';
                                    }
                                } else {
                                    $field_value[$input['name']][$language['id_lang']] = $value_field;
                                }
                            } else {
                                $value_field = Tools::getValue($input['name'] . '_' . $language['id_lang']);

                                if (isset($value_field)) {
                                    if (property_exists('Validate', $validate) && !Validate::$validate($value_field)) {
                                        $display_errors[$input['name']][] = $this->l('Invalid value for: ') . '<strong>' . $input['label'] . ' (' . Tools::strtoupper($language['iso_code']) . ')</strong> => <strong>"' . $value_field . '"</strong>';
                                    } else {
                                        $field_value[$input['name']][$language['id_lang']] = $value_field;
                                    }
                                }
                            }
                        }

                        if (!isset($display_errors[$input['name']])) {
                            Configuration::updateValue($input['name'], $field_value[$input['name']], isset($input['html']) && $input['html'] ? true : false);
                            Configuration::clearConfigurationCacheForTesting();
                        }
                    } else {
                        if ($input['type'] == 'file') {
                            $value_field = $this->uploadFiles($_FILES, $input);

                            if (is_array($value_field)) {
                                foreach ($value_field as $error) {
                                    $display_errors[$input['name']][] = $this->l($error);
                                }
                            } else {
                                $field_value[$input['name']] = $value_field;
                            }
                        } elseif (in_array($input['type'], ['swap', 'select', 'checkbox', 'group', 'categories'])) {
                            $value_field = Tools::getValue($input['name']);

                            if (isset($value_field) && $value_field != false) {
                                if (property_exists('Validate', $validate) && !Validate::$validate($value_field)) {
                                    $display_errors[$input['name']][] = $this->l('Invalid value for: ') . '<strong>' . $input['label'] . '</strong> => <strong>"' . (is_array($value_field) && isset($value_field[0]) ? $value_field[0] : $value_field) . '"</strong>';
                                } else {
                                    $field_value[$input['name']] = implode(',', is_array($value_field) ? $value_field : explode(' ', $value_field));
                                }
                            } else {
                                $field_value[$input['name']] = null;
                            }
                        } else {
                            $value_field = Tools::getValue($input['name']);

                            if (isset($value_field)) {
                                if (property_exists('Validate', $validate) && !Validate::$validate($value_field)) {
                                    $display_errors[$input['name']][] = $this->l('Invalid value for: ') . '<strong>' . $input['label'] . '</strong> => <strong>"' . $value_field . '"</strong>';
                                } else {
                                    $field_value[$input['name']] = $value_field;
                                }
                            } else {
                                $field_value[$input['name']] = null;
                            }
                        }

                        if (!isset($display_errors[$input['name']])) {
                            Configuration::updateValue($input['name'], $field_value[$input['name']], isset($input['html']) && $input['html'] ? true : false);
                        }
                    }
                }
            }
        }

        if (count($display_errors) == 0) {
            $this->context->smarty->assign('form_e', '0');
            $this->generateCSS($form_values);
            $this->generateJS($form_values);
        } else {
            $this->context->smarty->assign('form_e', '1');
            $this->context->smarty->assign('disp_e', $display_errors);
        }

        return true;
    }

    /**
     * Generate CSS
     *
     * @param array $form_values
     */
    protected function generateCSS($form_values)
    {
        $values = [];

        foreach ($form_values as $key => $form) {
            if (isset($form['input'])) {
                foreach ($form['input'] as $k => $input) {
                    $values[$input['name']] = Configuration::get($input['name'], null);
                }
            }
        }

        if (!empty($values)) {
            $keys = array_map(function ($a) {
                return '{{' . $a . '}}';
            }, array_keys($values));
            $str = Tools::file_get_contents($this->alca_dirname . '/views/css/base.css');
            $str = str_replace($keys, $values, $str);
            file_put_contents($this->alca_dirname . '/views/css/front.css', $str);
        }
    }

    /**
     * Generate JS
     *
     * @param array $form_values
     */
    protected function generateJS($form_values)
    {
        $values = [];

        foreach ($form_values as $key => $form) {
            if (isset($form['input'])) {
                foreach ($form['input'] as $k => $input) {
                    $values[$input['name']] = Configuration::get($input['name'], null);
                }
            }
        }

        if (!empty($values)) {
            $keys = array_map(function ($a) {
                return '{{' . $a . '}}';
            }, array_keys($values));
            $str = Tools::file_get_contents($this->alca_dirname . '/views/js/base.js');
            $str = str_replace($keys, $values, $str);
            file_put_contents($this->alca_dirname . '/views/js/front.js', $str);
        }
    }

    public function install()
    {
        $this->loadClass();
        $path = version_compare(PHP_VERSION, '5.3.0', '>=') ? dirname(dirname(__FILE__)) : dirname(__FILE__);

        foreach (scandir($path . '/classes/') as $l) {
            if (strpos($l, '.class.php') && $l != 'libs.class.php') {
                $class = Tools::ucfirst($this->name) . Tools::ucfirst(str_replace('.class.php', '', $l)) . 'Class';
                $r = $class::createTable();
            }
        }

        return true;
        // return parent::install();
    }

    public function uninstall()
    {
        $this->loadClass();
        $path = version_compare(PHP_VERSION, '5.3.0', '>=') ? dirname(dirname(__FILE__)) : dirname(__FILE__);

        foreach (scandir($path . '/classes/') as $l) {
            if (strpos($l, '.class.php') && $l != 'libs.class.php') {
                $class = Tools::ucfirst($this->name) . Tools::ucfirst(str_replace('.class.php', '', $l)) . 'Class';
                $r = $class::removeTables();
            }
        }

        return true;
        // return parent::uninstall();
    }

    /**
     * Load basif files
     */
    public function loadClass()
    {
        return true;
    }
}
