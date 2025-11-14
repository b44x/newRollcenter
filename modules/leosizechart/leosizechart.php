<?php
/**
 * 2007-2016 PrestaShop
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
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/src/SizeGuideModel.php';

class LeoSizeChart extends Module
{
    private $_html = '';
	private $templateFile;
    private $numberhook;
    public $config_name;
    public $defaults;

	
    public function __construct()
    {
        $this->name = 'leosizechart';
		$this->tab = 'front_office_features';
        $this->version = '1.0.0';
		$this->author = 'LeoTheme';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();		
        $this->displayName = $this->l('Size guide chart');
        $this->description = $this->l('Show popup with size guide');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
		
        $this->config_name = 'LEOSIZECHART';
        $this->numberhook = 0;
        $this->defaults = array(
            'sh_popup' => 1,
            'width' => 600,
            'sh_measure' => 0,
            'sh_global' => 1,
            'content' => 'Content of newsletter popup',
            'global' => 'Content of newsletter popup',
        );
		
		$this->templateFile = 'module:leosizechart/views/templates/hook/sizechart.tpl';
		
    }

    public function install()
    {
		$this->installModuleTab();
        if (parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayAdminProductsExtra') &&
			$this->registerHook('actionProductSave') &&
            $this->registerHook('actionProductDelete') &&
            $this->registerHook('leoProductSizeGuide') &&
            $this->createTables()) {
				$this->setDefaults();
				return true;
			} else {
				return false;
			}

    }

    public function uninstall()
    {
		$this->uninstallModuleTab();
        foreach ($this->defaults as $default => $value) {
            Configuration::deleteByName($this->config_name . '_' . $default);
        }

        return parent::uninstall() && $this->deleteTables();
    }
    /* ------------------------------------------------------------- */
    /*  CREATE THE TAB MENU
    /* ------------------------------------------------------------- */
    private function installModuleTab()
    {
        $class = 'AdminLeoSizechart';
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = Tab::getIdFromClassName('AdminParentModulesSf');
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            # validate module
            $tab1->name[$l['id_lang']] = $this->l('Leo Size Chart');
        }
        return $tab1->add(true, false);
    }

    /* ------------------------------------------------------------- */
    /*  DELETE THE TAB MENU
    /* ------------------------------------------------------------- */
    private function uninstallModuleTab()
    {
        $id_tab = Tab::getIdFromClassName('AdminLeoSizechart');
        $tab = new Tab($id_tab);
        $tab->delete();
    }
    /**
     * Creates tables
     */
    protected function createTables()
    {
        /* guides */
        $res = (bool) Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leosizechart` (
				`id_leosizechart_guides` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_leosizechart_guides`, `id_shop`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        /* guides configuration */
        $res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leosizechart_guides` (
			  `id_leosizechart_guides` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id_leosizechart_guides`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        /* guides lang configuration */
        $res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leosizechart_guides_lang` (
			  `id_leosizechart_guides` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  PRIMARY KEY (`id_leosizechart_guides`,`id_lang`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        /* guides product association */
        $res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leosizechart_product` (
				`id_product` int(10) unsigned NOT NULL,
				`id_guide` int(10) unsigned NOT NULL,
				 PRIMARY KEY (`id_product`)
				) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        return $res;
    }

    /**
     * deletes tables
     */
    protected function deleteTables()
    {
        $guides = $this->getGuides();
        foreach ($guides as $guide) {
            $to_del = new SizeGuideModel($guide['id_guide']);
            $to_del->delete();
        }

        return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'leosizechart`, `' . _DB_PREFIX_ . 'leosizechart_guides`, `' . _DB_PREFIX_ . 'leosizechart_product`, `' . _DB_PREFIX_ . 'leosizechart_guides_lang`;
		');
    }

    public function setDefaults()
    {
        foreach ($this->defaults as $default => $value) {
            if ($default == 'content') {
                $message_trads = array();
                foreach (Language::getLanguages(false) as $lang) {
                    $message_trads[(int) $lang['id_lang']] = 'PGRpdiBjbGFzcz0iZm9udC13ZWlnaHQtYm9sZCI+SG93IFRvIE1lYXN1cmUgWW91ciBCdXN0PC9kaXY+DQo8cD5XaXRoIHlvdXIgYXJtcyByZWxheGVkIGF0IHlvdXIgc2lkZXMsIG1lYXN1cmUgYXJvdW5kIHRoZSBmdWxsZXN0IHBhcnQgb2YgeW91ciBjaGVzdC48L3A+DQo8ZGl2IGNsYXNzPSJmb250LXdlaWdodC1ib2xkIj5Ib3cgVG8gTWVhc3VyZSBZb3VyIFdhaXN0PC9kaXY+DQo8cD5NZWFzdXJlIGFyb3VuZCB0aGUgbmFycm93ZXN0IHBhcnQgb2YgeW91ciBuYXR1cmFsIHdhaXN0LCBnZW5lcmFsbHkgYXJvdW5kIHRoZSBiZWxseSBidXR0b24uIFRvIGVuc3VyZSBhIGNvbWZvcnRhYmxlIGZpdCwga2VlcCBvbmUgZmluZ2VyIGJldHdlZW4gdGhlIG1lYXN1cmluZyB0YXBlIGFuZCB5b3VyIGJvZHkuPC9wPg==';
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } elseif ($default == 'global') {
                $message_trads = array();
                foreach (Language::getLanguages(false) as $lang) {
                    $message_trads[(int) $lang['id_lang']] = 'PHRhYmxlIGNsYXNzPSJ0YWJsZSB0YWJsZS1zdHJpcGVkIj4NCjx0aGVhZD4NCjx0cj4NCjx0aD5TaXplPC90aD4NCjx0aD5YUzwvdGg+DQo8dGg+UzwvdGg+DQo8dGg+TTwvdGg+DQo8dGg+TDwvdGg+DQo8L3RyPg0KPC90aGVhZD4NCjx0Ym9keT4NCjx0cj4NCjx0aCBzY29wZT0icm93Ij5FdXJvPC90aD4NCjx0ZD4zMi8zNDwvdGQ+DQo8dGQ+MzY8L3RkPg0KPHRkPjM4PC90ZD4NCjx0ZD40MDwvdGQ+DQo8L3RyPg0KPHRyPg0KPHRoIHNjb3BlPSJyb3ciPlVTQTwvdGg+DQo8dGQ+MC8yPC90ZD4NCjx0ZD40PC90ZD4NCjx0ZD42PC90ZD4NCjx0ZD44PC90ZD4NCjwvdHI+DQo8dHI+DQo8dGggc2NvcGU9InJvdyI+QnVzdChpbik8L3RoPg0KPHRkPjMxLTMyPC90ZD4NCjx0ZD4zMzwvdGQ+DQo8dGQ+MzQ8L3RkPg0KPHRkPjM2PC90ZD4NCjwvdHI+DQo8dHI+DQo8dGggc2NvcGU9InJvdyI+QnVzdChjbSk8L3RoPg0KPHRkPjgwLjUtODIuNTwvdGQ+DQo8dGQ+ODQuNTwvdGQ+DQo8dGQ+ODc8L3RkPg0KPHRkPjkyPC90ZD4NCjwvdHI+DQo8dHI+DQo8dGggc2NvcGU9InJvdyI+V2Fpc3QoaW4pPC90aD4NCjx0ZD4yNC0yNTwvdGQ+DQo8dGQ+MjY8L3RkPg0KPHRkPjI3PC90ZD4NCjx0ZD4yOTwvdGQ+DQo8L3RyPg0KPHRyPg0KPHRoIHNjb3BlPSJyb3ciPldhaXN0KGNtKTwvdGg+DQo8dGQ+NjIuNS02NC41PC90ZD4NCjx0ZD42Ni41PC90ZD4NCjx0ZD42OTwvdGQ+DQo8dGQ+NzQ8L3RkPg0KPC90cj4NCjx0cj4NCjx0aCBzY29wZT0icm93Ij5IaXBzKGluKTwvdGg+DQo8dGQ+MzQtMzU8L3RkPg0KPHRkPjM2PC90ZD4NCjx0ZD4zNzwvdGQ+DQo8dGQ+Mzk8L3RkPg0KPC90cj4NCjx0cj4NCjx0aCBzY29wZT0icm93Ij5IaXBzKGNtKTwvdGg+DQo8dGQ+ODcuNS04OS41PC90ZD4NCjx0ZD45MS41PC90ZD4NCjx0ZD45NDwvdGQ+DQo8dGQ+OTk8L3RkPg0KPC90cj4NCjwvdGJvZHk+DQo8L3RhYmxlPg==';
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } else {
                Configuration::updateValue($this->config_name . '_' . $default, $value);
            }

        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (Tools::isSubmit('submitGuide') || Tools::isSubmit('delete_id_guide')) {
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderForm();
                $this->_html .= $this->renderList();
            } else {
                $this->_html .= $this->renderAddForm();
            }

        } elseif (Tools::isSubmit('addGuide') || (Tools::isSubmit('id_guide') && $this->guideExists((int) Tools::getValue('id_guide')))) {
            return $this->renderAddForm();
        } elseif (Tools::isSubmit('submitleosizechartModule')) {
            $this->_postProcess2();

            $this->context->smarty->assign('module_dir', $this->_path);

            $this->_html .= $this->renderForm() . $this->renderList();
        } else {

            $this->context->smarty->assign('module_dir', $this->_path);

            $this->_html .= $this->renderForm() . $this->renderList();
        }
        return $this->_html;
    }

    private function _postValidation()
    {
        $errors = array();

        /* Validation for guide */
        if (Tools::isSubmit('submitGuide')) {
            /* If edit : checks id_guide */
            if (Tools::isSubmit('id_guide')) {
                if (!Validate::isInt(Tools::getValue('id_guide')) && !$this->guideExists(Tools::getValue('id_guide'))) {
                    $errors[] = $this->l('Invalid id_guide');
                }

            }
            /* Checks title/description/*/
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (Tools::strlen(Tools::getValue('title_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->l('The title is too long.');
                }

            }

            /* Checks title/description for default lang */
            $id_lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
            if (Tools::strlen(Tools::getValue('title_' . $id_lang_default)) == 0) {
                $errors[] = $this->l('The title is not set.');
            }
			
            /* Checks title/description for default lang */
            $id_lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
            if (Tools::strlen(Tools::getValue('description_' . $id_lang_default)) == 0) {
                $errors[] = $this->l('The description is not set.');
            }

        }
        /* Validation for deletion */
        elseif (Tools::isSubmit('delete_id_guide') && (!Validate::isInt(Tools::getValue('delete_id_guide')) || !$this->guideExists((int) Tools::getValue('delete_id_guide')))) {
            $errors[] = $this->l('Invalid id_guide');
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));

            return false;
        }

        /* Returns if validation is ok */

        return true;
    }

    private function _postProcess()
    {
        $errors = array();

        /* Processes guide */
        if (Tools::isSubmit('submitGuide')) {
            /* Sets ID if needed */
            if (Tools::getValue('id_guide')) {
                $guide = new SizeGuideModel((int) Tools::getValue('id_guide'));
                if (!Validate::isLoadedObject($guide)) {
                    $this->_html .= $this->displayError($this->l('Invalid id_guide'));

                    return false;
                }
            } else {
                $guide = new SizeGuideModel();
            }

            $guide->active = 1;

            /* Sets each langue fields */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $guide->title[$language['id_lang']] = Tools::getValue('title_' . $language['id_lang']);
                $guide->description[$language['id_lang']] =  call_user_func('base64_encode', Tools::getValue('description_' . $language['id_lang']));

            }

            /* Processes if no errors  */
            if (!$errors) {
                /* Adds */
                if (!Tools::getValue('id_guide')) {
                    if (!$guide->add()) {
                        $errors[] = $this->displayError($this->l('The guide could not be added.'));
                    }

                }
                /* Update */
                elseif (!$guide->update()) {
                    $errors[] = $this->displayError($this->l('The guide could not be updated.'));
                }

                $this->clearCache();
            }
        } /* Deletes */
        elseif (Tools::isSubmit('delete_id_guide')) {
            $guide = new SizeGuideModel((int) Tools::getValue('delete_id_guide'));
            $res = $guide->delete();
            $this->clearCache();
            if (!$res) {
                $this->_html .= $this->displayError('Could not delete.');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=1&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }

        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitGuide') && Tools::getValue('id_guide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=4&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        } elseif (Tools::isSubmit('submitGuide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=3&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        }

    }

    public function renderList()
    {
        $guides = $this->getGuides();

        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'guides' => $guides,
            )
        );

        return $this->display(__FILE__, 'list.tpl');
    }

    public function renderAddForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Guide informations'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
						'required' => true,
                        'lang' => true,
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'name' => 'description',
						'required' => true,
                        'autoload_rte' => true,
                        'lang' => true,
                    )
                ),
			   'buttons' => array(
					'cancelBlock' => array(
						'title' => $this->trans('Cancel', array(), 'Admin.Actions'),
						'href' => (Tools::safeOutput(Tools::getValue('back', false)))
									?: $this->context->link->getAdminLink('AdminLeoSizechart'),
						'icon' => 'process-icon-cancel'
					)
				),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        if (Tools::isSubmit('id_guide') && $this->guideExists((int) Tools::getValue('id_guide'))) {
            $guide = new SizeGuideModel((int) Tools::getValue('id_guide'));
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_guide');
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGuide';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code,
            ),
            'fields_value' => $this->getAddLeoValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'module_path' => $this->_path
        );

        $helper->override_folder = '/';

        return $helper->generateForm(array($fields_form));
    }

    public function getGuides($active = null)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_leosizechart_guides` as id_guide, hssl.`title`, hssl.`description`
			FROM ' . _DB_PREFIX_ . 'leosizechart hs
			LEFT JOIN ' . _DB_PREFIX_ . 'leosizechart_guides hss ON (hs.id_leosizechart_guides = hss.id_leosizechart_guides)
			LEFT JOIN ' . _DB_PREFIX_ . 'leosizechart_guides_lang hssl ON (hss.id_leosizechart_guides = hssl.id_leosizechart_guides)
			WHERE id_shop = ' . (int) $id_shop . '
			AND hssl.id_lang = ' . (int) $id_lang
        );
    }

    public function getAddLeoValues()
    {
        $fields = array();

        if (Tools::isSubmit('id_guide') && $this->guideExists((int) Tools::getValue('id_guide'))) {
            $guide = new SizeGuideModel((int) Tools::getValue('id_guide'));
            $fields['id_guide'] = (int) Tools::getValue('id_guide', $guide->id);
        } else {
            $guide = new SizeGuideModel();
        }

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $fields['title'][$lang['id_lang']] = Tools::getValue('title_' . (int) $lang['id_lang'], isset($guide->title[$lang['id_lang']]) ? $guide->title[$lang['id_lang']] : '');
            $fields['description'][$lang['id_lang']] = Tools::getValue('description_' . (int) $lang['id_lang'], isset($guide->description[$lang['id_lang']]) ?  call_user_func('base64_decode', $guide->description[$lang['id_lang']]) : '');
        }

        return $fields;
    }

    public function guideExists($id_guide)
    {
        $req = 'SELECT hs.`id_leosizechart_guides` as id_guide
				FROM `' . _DB_PREFIX_ . 'leosizechart` hs
				WHERE hs.`id_leosizechart_guides` = ' . (int) $id_guide;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

        return ($row);
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitleosizechartModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'module_path' => $this->_path,
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }
    public function getAttributes()
    {
        $attributes = AttributeGroup::getAttributesGroups($this->context->language->id);

        $selectAttributes = array();

        foreach ($attributes as $attribute) {
            $selectAttributes[$attribute['id_attribute_group']]['id_option'] = $attribute['id_attribute_group'];
            $selectAttributes[$attribute['id_attribute_group']]['name'] = $attribute['name'];
        }

        return $selectAttributes;
    }
    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'how_to_run',
                        'name' => 'width',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show on popup'),
                        'name' => 'sh_popup',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Width'),
                        'name' => 'width',
                        'suffix' => 'px',
                        'desc' => $this->l('Popup window width.'),
                        'class' => 'fixed-width-sm',
                        'size' => 20,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show how to measure tab'),
                        'name' => 'sh_measure',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('How to measure tab'),
                        'name' => 'content',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 30,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show global size guide'),
                        'name' => 'sh_global',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Global size guide'),
                        'name' => 'global',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 30,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $var = array();

        foreach ($this->defaults as $default => $value) {

            if ($default == 'content' || $default == 'global') {
                foreach (Language::getLanguages(false) as $lang) {
                    $var[$default][(int) $lang['id_lang']] = html_entity_decode(call_user_func('base64_decode', Configuration::get($this->config_name . '_' . $default, (int) $lang['id_lang'])));
                }

            } else {
                $var[$default] = Configuration::get($this->config_name . '_' . $default);
            }

        }
        return $var;

    }

    /**
     * Save form data.
     */
    protected function _postProcess2()
    {
        foreach ($this->defaults as $default => $value) {
            if ($default == 'content' || $default == 'global') {
                $message_trads = array();
				
				foreach (Language::getLanguages(false) as $lang) {
					$message_trads[(int) $lang['id_lang']] = call_user_func('base64_encode', Tools::getValue($default.'_'.$lang['id_lang']));
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } else {
                Configuration::updateValue($this->config_name . '_' . $default, Tools::getValue($default));
            }

        }
        $this->clearCache();
    }

    public function clearCache()
    {
        $this->_clearCache($this->templateFile);
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        if ($this->context->controller->php_self == 'product') {
            $this->context->controller->addCSS($this->_path . 'views/css/front.css');
            $this->context->controller->addJS($this->_path . 'views/js/front.js');
            
            $id_guide = SizeGuideModel::getProductGuide((int) Tools::getValue('id_product'));
            $sh_global = Configuration::get($this->config_name . '_sh_global');
            Media::addJsDef(array(
                'sh_popup' => Configuration::get($this->config_name . '_sh_popup'),
            ));
            if ($id_guide || $sh_global) {
			    $this->smarty->assignGlobal('has_sizeguide', true);
            }
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {

        if (Validate::isLoadedObject($product = new Product((int)$params["id_product"]))) {
            $guides = $this->getGuides();

            $this->context->smarty->assign(array(
                'guides' => $this->getGuides(),
                'selectedGuide' => (int) SizeGuideModel::getProductGuide((int)$params["id_product"]),
            ));
            return $this->display(__FILE__, 'views/templates/admin/addtab.tpl');
        } else {
            return $this->displayError($this->l('You must save this product before adding tabs'));
        }
    }

    public function hookdisplayProductListReviews($params)
    {
        $prodtid = (int) $params['product']['id_product'];

        return $this->hookdisplayProductAttributesPL($prodtid);

    }
    public function hookActionProductSave($params)
    {
        $id_product = (int) Tools::getValue('id_product');
        $id_guide = (int) Tools::getValue('id_leosizechart');

        if ($id_guide) {
            SizeGuideModel::assignProduct($id_product, $id_guide);
        } else {
            SizeGuideModel::unassignProduct($id_product);
        }

    }
    public function hookActionProductDelete($params)
    {
        $id_product = (int)$params["id_product"];
        SizeGuideModel::unassignProduct($id_product);
    }

    public function hookLeoProductSizeGuide($params)
    {
        if ($this->context->controller->php_self != 'product') {
            return;
        }
        $this->numberhook++;
        $id_guide = SizeGuideModel::getProductGuide((int) Tools::getValue('id_product'));
        $sh_global = Configuration::get($this->config_name . '_sh_global');

        if ($id_guide || $sh_global) {
            if ($id_guide) {
                $guide = new SizeGuideModel((int) $id_guide, $this->context->language->id);
                $guide->description = html_entity_decode(call_user_func('base64_decode', $guide->description));
                $cache_id = 'leosizechart|' . (int) $id_guide;
            } else {
                $cache_id = 'leosizechart';
            }
            
            if (!$this->isCached($this->templateFile, $this->getCacheId($cache_id))) {
                if ($id_guide) {
                    $this->smarty->assign(
                        array(
                            'guide' => $guide,
                        )
                    );
                }
                $this->smarty->assign(
                    array(
                        'numberhook' => $this->numberhook,
                        'width' => Configuration::get($this->config_name . '_width', null, null, $this->context->shop->id),
                        'sh_popup' => Configuration::get($this->config_name . '_sh_popup'),
                        'howto' =>html_entity_decode(call_user_func('base64_decode', Configuration::get($this->config_name . '_content', $this->context->language->id))),
                        'sh_measure' => Configuration::get($this->config_name . '_sh_measure'),
                        'sh_global' => $sh_global,  
                        'global' =>html_entity_decode(call_user_func('base64_decode', Configuration::get($this->config_name . '_global', $this->context->language->id))),
                    )
                );
            }
            
            return $this->fetch($this->templateFile, $this->getCacheId($cache_id));
        }
    }
}
