<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Leopopupsale extends Module
{
    protected $config_form = false;
    public $id_lang;

    public function __construct()
    {
        $this->name = 'leopopupsale';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Leotheme';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Leo Popup Sale');
        $this->description = $this->l('This is great module for create sale popup');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->context = Context::getContext();
        $this->id_lang = $this->context->language->id;
    }

    public function install()
    {
        Configuration::updateValue('LEOPOPUPSALE_CONFIG_ACTIVE', 0);
        Configuration::updateValue('LEOPOPUPSALE_SELECT_THEME', 'smart');
        Configuration::updateValue('LEOPOPUPSALE_TIME', '1000');
        Configuration::updateValue('LEOPOPUPSALE_INVERVAL', '2000');
        Configuration::updateValue('LEOPOPUPSALE_SELECT_TYPE', '2');
        Configuration::updateValue('LEOPOPUPSALE_CATEGORY', '2');
        Configuration::updateValue('LEOPOPUPSALE_CAT_SORT', 'DESC');
        Configuration::updateValue('LEOPOPUPSALE_CAT_LIMIT', '10');
        Configuration::updateValue('LEOPOPUPSALE_ORDER_LIMIT', '10');
        Configuration::updateValue('LEOPOPUPSALE_ORDER_SORT', 'DESC');
        Configuration::updateValue('LEOPOPUPSALE_POSITION', '3');
        Configuration::updateValue('LEOPOPUPSALE_LABLE_BG', '');
        Configuration::updateValue('LEOPOPUPSALE_LABLE_TEXT', '');
        Configuration::updateValue('LEOPOPUPSALE_BG', '');
        Configuration::updateValue('LEOPOPUPSALE_TEXT_COLOR', '');
        Configuration::updateValue('LEOPOPUPSALE_SHOWNAME', '1');
        Configuration::updateValue('LEOPOPUPSALE_SHOWPHONE', '1');
        Configuration::updateValue('LEOPOPUPSALE_DELEPHONE', '1');
        Configuration::updateValue('LEOPOPUPSALE_SHOWADDRESS', '1');
        Configuration::updateValue('LEOPOPUPSALE_IMG_SIZE', 'cart');
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('ActionAdminControllerSetMedia') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayFooterAfter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LEOPOPUPSALE_CONFIG_ACTIVE');
        Configuration::deleteByName('LEOPOPUPSALE_SELECT_THEME');
        Configuration::deleteByName('LEOPOPUPSALE_TIME');
        Configuration::deleteByName('LEOPOPUPSALE_INVERVAL');
        Configuration::deleteByName('LEOPOPUPSALE_SELECT_TYPE');
        Configuration::deleteByName('LEOPOPUPSALE_CATEGORY');
        Configuration::deleteByName('LEOPOPUPSALE_CAT_SORT');
        Configuration::deleteByName('LEOPOPUPSALE_CAT_LIMIT');
        Configuration::deleteByName('LEOPOPUPSALE_ORDER_LIMIT');
        Configuration::deleteByName('LEOPOPUPSALE_ORDER_SORT');
        Configuration::deleteByName('LEOPOPUPSALE_POSITION');
        Configuration::deleteByName('LEOPOPUPSALE_LABLE_BG');
        Configuration::deleteByName('LEOPOPUPSALE_LABLE_TEXT');
        Configuration::deleteByName('LEOPOPUPSALE_BG');
        Configuration::deleteByName('LEOPOPUPSALE_TEXT_COLOR');
        Configuration::deleteByName('LEOPOPUPSALE_DELEPHONE');
        Configuration::deleteByName('LEOPOPUPSALE_SHOWNAME');
        Configuration::deleteByName('LEOPOPUPSALE_SHOWPHONE');
        Configuration::deleteByName('LEOPOPUPSALE_SHOWADDRESS');
        Configuration::deleteByName('LEOPOPUPSALE_IMG_SIZE');
        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';
        $errors = array();

        if (((bool)Tools::isSubmit('submitLeopopupsaleModule')) == true) {
            $time = Tools::getValue('LEOPOPUPSALE_TIME');

            if (!Validate::isInt($time)) {
                $errors[] = $this->trans(
                    'Invalid value for the Time popup',
                    array(),
                    'Modules.Leopopupsale.Admin'
                );
            }

            if (isset($errors) && count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                $this->postProcess();

                $output = $this->displayConfirmation(
                    $this->trans(
                        'The settings have been updated.',
                        array(),
                        'Admin.Notifications.Success'
                    )
                );
            }
            $this->clearCache();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLeopopupsaleModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    public function getProduct($id_product = '')
    {
        if ($id_product != '') {
            return Db::getInstance()->executeS(
                'SELECT DISTINCT p.`id_product`, pl.`name`, pl.`link_rewrite`
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON p.`id_product` = pl.`id_product`
                LEFT JOIN `'._DB_PREFIX_.'product_shop` pshop
                ON p.`id_product` = pshop.`id_product`
                WHERE p.`active` = 1 AND p.`id_product` IN ('.pSQL($id_product).')
                AND pshop.`id_shop` = '.(int)$this->context->shop->id.' 
                AND pl.`id_lang` = '.(int)$this->id_lang
            );
        } else {
            return array();
        }
    }

    protected function getConfigForm()
    {
        $query1 = array(
            array(
                'id_option' => 1,
                'name' => $this->l('Top-Left')
            ),
            array(
                'id_option' => 2,
                'name' => $this->l('Top-Right')
            ),
            array(
                'id_option' => 3,
                'name' => $this->l('Bottom-Left')
            ),
            array(
                'id_option' => 4,
                'name' => $this->l('Bottom-Right')
            ),
        );

        $query2 = array(
            array(
                'id_option' => 'DESC',
                'name' => $this->l('DESC')
            ),
            array(
                'id_option' => 'ASC',
                'name' => $this->l('ASC')
            ),
            array(
                'id_option' => 'RAND()',
                'name' => $this->l('RANDOM')
            )
        );
        $query3 = array();
        foreach (ImageType::getImagesTypes() as $value) {
            if ($value['products'] == 1) {
                $query3[] = array('id_option' => str_replace('_default', '', $value['name']), 'name' => $value['name'].':'.$value['width'].'x'.$value['height']);
            }
        };
        $this->smarty->assign(array(
            'products' => $this->getProduct(Configuration::get('LEOPOPUPSALE_LIST_PRODUCT')),
            'customers' => unserialize($this->base64Decode(
                Configuration::get('LEOPOPUPSALE_CUSTOMER')
            )),
            'cat_customers' => unserialize($this->base64Decode(
                Configuration::get('LEOPOPUPSALE_CATCUSTOMER')
            ))
        ));
        $selected_categories = array();
        $html = $this->fetch('module:leopopupsale/views/templates/admin/leo-form.tpl');
        $html_category = $this->fetch('module:leopopupsale/views/templates/admin/leo-category.tpl');
        $id_root_category = Context::getContext()->shop->getCategory();
        $selected_categories[] = Configuration::get('LEOPOPUPSALE_CATEGORY');
        
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show config in front end'),
                        'name' => 'LEOPOPUPSALE_CONFIG_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('This config is only for demo'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select THEME'),
                        'name' => 'LEOPOPUPSALE_SELECT_THEME',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'basic',
                                    'name' => $this->l('Basic'),
                                ),
                                array(
                                    'id_option' => 'model',
                                    'name' => $this->l('Model'),
                                ),
                                array(
                                    'id_option' => 'smart',
                                    'name' => $this->l('Smart'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'LEOPOPUPSALE_INVERVAL',
                        'label' => $this->l('Time Interval popup'),
                        'desc' => $this->l('Open pop-up in every Time Interval. Default 2000'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'LEOPOPUPSALE_TIME',
                        'label' => $this->l('Popup Timing'),
                        'desc' => $this->l('Duration popup Time display in home page. Should be lower than Time Interval. Default 1000'),
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'LEOPOPUPSALE_LABLE_BG',
                        'label' => $this->l('label Background color'),
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'LEOPOPUPSALE_LABLE_TEXT',
                        'label' => $this->l('label color'),
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'LEOPOPUPSALE_BG',
                        'label' => $this->l('Box Background color'),
                    ),
                    array(
                        'type' => 'color',
                        'name' => 'LEOPOPUPSALE_TEXT_COLOR',
                        'label' => $this->l('Text box color'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show customer name'),
                        'name' => 'LEOPOPUPSALE_SHOWNAME',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show customer Phone'),
                        'name' => 'LEOPOPUPSALE_SHOWPHONE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Add xxx to customer phone'),
                        'name' => 'LEOPOPUPSALE_DELEPHONE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('Do not show full customer phone'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show customer address'),
                        'name' => 'LEOPOPUPSALE_SHOWADDRESS',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('Do not show full customer phone'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Product Image Size'),
                        'name' => 'LEOPOPUPSALE_IMG_SIZE',
                        'options' => array(
                            'query' => $query3,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Position'),
                        'name' => 'LEOPOPUPSALE_POSITION',
                        'options' => array(
                            'query' => $query1,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),

                    array(
                        'type' => 'html',
                        'name' => 'html_data',
                        'html_content' =>  '<hr>',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Type'),
                        'name' => 'LEOPOPUPSALE_SELECT_TYPE',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Categories'),
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => $this->l('Orders'),
                                ),
                                array(
                                    'id_option' => 3,
                                    'name' => $this->l('Custom'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Select Category'),
                        'class' => 'leotype type1',
                        'name' => 'LEOPOPUPSALE_CATEGORY',
                        'desc' => $this->l('Product in category selected will display'),
                        'tree' => array(
                            'root_category' => $id_root_category,
                            'use_search' => false,
                            'class' => 'leotype type1',
                            'id' => 'LEOPOPUPSALE_CATEGORY',
                            'use_checkbox' => false,
                            'selected_categories' => $selected_categories,
                        ),
                        'form_group_class' => 'value_by_categories',
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'class' => 'leotype type1',
                        'default' => 10,
                        'name' => 'LEOPOPUPSALE_CAT_LIMIT',
                        'label' => $this->l('Limit'),
                        'desc' => $this->l('How many order you will get'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select order way by position'),
                        'class' => 'leotype type1',
                        'name' => 'LEOPOPUPSALE_CAT_SORT',
                        'options' => array(
                            'query' => $query2,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'class' => 'leotype type2',
                        'default' => 10,
                        'name' => 'LEOPOPUPSALE_ORDER_LIMIT',
                        'label' => $this->l('Limit'),
                        'desc' => $this->l('How many order you will get'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select order sort'),
                        'class' => 'leotype type2',
                        'name' => 'LEOPOPUPSALE_ORDER_SORT',
                        'options' => array(
                            'query' => $query2,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'html_data',
                        'html_content' =>  $html_category,
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'html_data',
                        'html_content' =>  $html,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'LEOPOPUPSALE_CONFIG_ACTIVE' => Configuration::get('LEOPOPUPSALE_CONFIG_ACTIVE'),
            'LEOPOPUPSALE_SELECT_THEME' => Configuration::get('LEOPOPUPSALE_SELECT_THEME'),
            'LEOPOPUPSALE_TIME' => Configuration::get('LEOPOPUPSALE_TIME'),
            'LEOPOPUPSALE_INVERVAL' => Configuration::get('LEOPOPUPSALE_INVERVAL'),
            'LEOPOPUPSALE_POSITION' => Configuration::get('LEOPOPUPSALE_POSITION'),
            'LEOPOPUPSALE_LABLE_BG' => Configuration::get('LEOPOPUPSALE_LABLE_BG'),
            'LEOPOPUPSALE_LABLE_TEXT' => Configuration::get('LEOPOPUPSALE_LABLE_TEXT'),
            'LEOPOPUPSALE_BG' => Configuration::get('LEOPOPUPSALE_BG'),
            'LEOPOPUPSALE_TEXT_COLOR' => Configuration::get('LEOPOPUPSALE_TEXT_COLOR'),
            'LEOPOPUPSALE_SHOWNAME' => Configuration::get('LEOPOPUPSALE_SHOWNAME'),
            'LEOPOPUPSALE_SHOWPHONE' => Configuration::get('LEOPOPUPSALE_SHOWPHONE'),
            'LEOPOPUPSALE_DELEPHONE' => Configuration::get('LEOPOPUPSALE_DELEPHONE'),
            'LEOPOPUPSALE_SHOWADDRESS' => Configuration::get('LEOPOPUPSALE_SHOWADDRESS'),
            'LEOPOPUPSALE_IMG_SIZE' => Configuration::get('LEOPOPUPSALE_IMG_SIZE'),
            'LEOPOPUPSALE_SELECT_TYPE' => Configuration::get('LEOPOPUPSALE_SELECT_TYPE'),
            'LEOPOPUPSALE_CATEGORY' => Configuration::get('LEOPOPUPSALE_CATEGORY'),
            'LEOPOPUPSALE_CAT_SORT' => Configuration::get('LEOPOPUPSALE_CAT_SORT'),
            'LEOPOPUPSALE_CAT_LIMIT' => Configuration::get('LEOPOPUPSALE_CAT_LIMIT'),
            'LEOPOPUPSALE_ORDER_LIMIT' => Configuration::get('LEOPOPUPSALE_ORDER_LIMIT'),
            'LEOPOPUPSALE_ORDER_SORT' => Configuration::get('LEOPOPUPSALE_ORDER_SORT', 'DESC'),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        if (Tools::getValue('LEOPOPUPSALE_SELECT_TYPE') == 3) {
            Configuration::updateValue('LEOPOPUPSALE_LIST_PRODUCT', Tools::getValue('LEOPOPUPSALE_LIST_PRODUCT'));

            $customer = array();
            $name = Tools::getValue('leo_name');
            $phone = Tools::getValue('leo_phone');
            $adress = Tools::getValue('leo_address');

            if (!empty($name)) {
                for ($i=0; $i < count($name); $i++) {
                    $customer[$i]['name'] = $name[$i];
                    $customer[$i]['phone'] = $phone[$i];
                    $customer[$i]['address'] = $adress[$i];
                }
            }

            Configuration::updateValue(
                'LEOPOPUPSALE_CUSTOMER',
                $this->base64Encode(serialize($customer))
            );
        }
        if (Tools::getValue('LEOPOPUPSALE_SELECT_TYPE') == 1) {
            $customer = array();
            $name = Tools::getValue('leo_catname');
            $phone = Tools::getValue('leo_catphone');
            $adress = Tools::getValue('leo_cataddress');

            if (!empty($name)) {
                for ($i=0; $i < count($name); $i++) {
                    $customer[$i]['name'] = $name[$i];
                    $customer[$i]['phone'] = $phone[$i];
                    $customer[$i]['address'] = $adress[$i];
                }
            }

            Configuration::updateValue(
                'LEOPOPUPSALE_CATCUSTOMER',
                $this->base64Encode(serialize($customer))
            );
        }
    }

    public function hookHeader()
    {
        //create data popup
        $products = array();
        $customers = array();

        if (Configuration::get('LEOPOPUPSALE_SELECT_TYPE') == 2) {
            $limit = Configuration::get('LEOPOPUPSALE_ORDER_LIMIT');
            $order = Configuration::get('LEOPOPUPSALE_ORDER_SORT')=="RAND()"?"RAND()":"`id_order` ".Configuration::get('LEOPOPUPSALE_ORDER_SORT');

            $array_orders = Db::getInstance()->executeS(
                'SELECT `id_order`
                FROM `'._DB_PREFIX_.'orders`
                WHERE 1 ORDER BY '.$order.' LIMIT '.$limit
            );

            $id_order = '';

            if (!empty($array_orders)) {
                foreach ($array_orders as $key => $array_order) {
                    if ($key == 0) {
                        $id_order .= $array_order['id_order'];
                    } else {
                        $id_order .= ',' . $array_order['id_order'];
                    }
                }
            }
            if ($id_order) {
                $products = Db::getInstance()->executeS(
                    'SELECT DISTINCT p.`id_product`, pl.`name`, pl.`link_rewrite`
                    FROM `'._DB_PREFIX_.'product` p
                    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                    ON p.`id_product` = pl.`id_product`
                    LEFT JOIN `'._DB_PREFIX_.'product_shop` pshop
                    ON p.`id_product` = pshop.`id_product`
                    LEFT JOIN `'._DB_PREFIX_.'category_product` pc
                    ON p.`id_product` = pc.`id_product`
                    WHERE pc.`id_product`
                    IN (SELECT DISTINCT `product_id` FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` IN('.$id_order.'))
                    AND pl.`id_lang` = '.(int)$this->id_lang.'
                    AND pshop.`active` = 1'
                );

                $products = $this->getImageProduct($products);

                $customers = Db::getInstance()->executeS(
                    'SELECT CONCAT(LEFT(`firstname`, 1), `lastname`) AS `name`, `phone`, CONCAT(`address1`," ",`city`) AS `address`
                    FROM `'._DB_PREFIX_.'address`
                    WHERE id_customer
                    IN (SELECT DISTINCT `id_customer` FROM `'._DB_PREFIX_.'orders` WHERE `id_order` IN('.$id_order.'))'
                );
            }
            
        } else if (Configuration::get('LEOPOPUPSALE_SELECT_TYPE') == 3) {
            $products = $this->getProduct(Configuration::get('LEOPOPUPSALE_LIST_PRODUCT'));
            $products = $this->getImageProduct($products);

            $customers = unserialize($this->base64Decode(
                Configuration::get('LEOPOPUPSALE_CUSTOMER')
            ));
        } else {
            $category = new Category((int) Configuration::get('LEOPOPUPSALE_CATEGORY'));
            $sort = Configuration::get('LEOPOPUPSALE_CAT_SORT');

            $products = $category->getProducts((int)$this->id_lang, 1, (int)Configuration::get('LEOPOPUPSALE_CAT_LIMIT'), ($sort=="RAND()"?null:'position'), ($sort=="RAND()"?null:$sort), false, 1, ($sort=="RAND()"?true:false), (int)Configuration::get('LEOPOPUPSALE_CAT_LIMIT'));
            $products = $this->getImageProduct($products);

            $customers = unserialize($this->base64Decode(
                Configuration::get('LEOPOPUPSALE_CATCUSTOMER')
            ));
        }

        if (!empty($customers)) {
            $customer = $customers[0];
            if (Configuration::get('LEOPOPUPSALE_DELEPHONE')) {
                $customer['phone'] = 'xxx' . Tools::substr($customer['phone'], 3);

                foreach ($customers as $key_c => $val_c) {
                    $customers[$key_c]['phone'] = 'xxx' . Tools::substr($val_c['phone'], 3);
                }
            }
        } else {
            $customer = array(
                'name' => 'John',
                'phone' => 'xxx3456789'
            );

            $customers = array(
                array(
                    'name' => 'John',
                    'phone' => 'xxx3456789',
                ),
                array(
                    'name' => 'Doe',
                    'phone' => 'xxx8888888',
                )
            );
        }

        $data_product_popup = array();
        $i = 0;
        foreach ($customers as $customer) {
            foreach ($products as $product) {
                $data_product_popup[$i]['image'] = $product['img'];
                $data_product_popup[$i]['title'] = $product['name'];
                $data_product_popup[$i]['url'] = $product['url'];
                $data_product_popup[$i]['id_product'] = $product['id_product'];
                $time = Configuration::get('LEOPOPUPSALE_SHOWNAME') ? $this->l('Purchased By').' '.$customer['name'] : $this->l('Have a customer purchase product');
                $data_product_popup[$i]['time'] =  $time.' '.rand(1,59).' '.$this->l('minutes ago');
                $data_product_popup[$i]['phone'] = $customer['phone'];
                $data_product_popup[$i]['address'] = $customer['address'];
                $i++;
            }
        }
        Media::addJsDef(array(
           'data_product_popup' => $data_product_popup,
        ));

        $this->context->controller->addJS($this->_path.'/views/js/cookie.js');
        $this->context->controller->addJS($this->_path.'/views/js/front.js');

        

        if (Configuration::get('LEOPOPUPSALE_CONFIG_ACTIVE', 0) != 0) {
            $this->context->controller->addJqueryPlugin('colorpicker');

            Media::addJsDef(array(
               'baseAdminDir' => __PS_BASE_URI__.'/',
            ));
        }

        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addJS($this->_path.'/views/js/back.js');
        $this->context->controller->addCSS($this->_path.'/views/css/back.css');

        Media::addJsDef(array(
            'url' => $this->context->shop->getBaseURL(true, true) . 'modules/leopopupsale/ajax.php',
            'leo_token' => Tools::getAdminToken('AdminDashboard'),
        ));
    }

    public function hookdisplayFooter()
    {
        if (Configuration::get('LEOPOPUPSALE_CONFIG_ACTIVE') == 0) {
            return;
        }

        
        $theme = Configuration::get('LEOPOPUPSALE_SELECT_THEME');
        $time = 1000;
        $interval = 2000;
        $position = 3;

        if (Tools::getIsset('leopopupconfig')) {
            $time = Tools::getValue('time');
            $interval = Tools::getValue('interval');
            $position = Tools::getValue('position');
            $theme = Tools::getValue('theme');
        } else {
            $time = Configuration::get('LEOPOPUPSALE_TIME');
            $interval = Configuration::get('LEOPOPUPSALE_INVERVAL');

            if (Configuration::get('LEOPOPUPSALE_POSITION') != '') {
                $position = Configuration::get('LEOPOPUPSALE_POSITION');
            }
        }
        
        $this->smarty->assign(array(
            'theme' => $theme,
            'leointerval' => $interval,
            'leotime' => $time,
            'position' => $position,
        ));

        return $this->display(__FILE__, 'views/templates/hook/leo-panel.tpl');
    }

    public function clearCache()
    {
        $this->_clearCache('module:leopopupsale/views/templates/hook/popup.tpl');
    }

    public function hookdisplayFooterAfter($params)
    {
        if (Tools::getIsset('leopopupconfig') || !$this->isCached('module:leopopupsale/views/templates/hook/popup.tpl', $this->getCacheId())) {
            $products = array();
            $customers = array();

            if (Configuration::get('LEOPOPUPSALE_SELECT_TYPE') == 2) {
                $limit = Configuration::get('LEOPOPUPSALE_ORDER_LIMIT');
                $order = Configuration::get('LEOPOPUPSALE_ORDER_SORT')=="RAND()"?"RAND()":"`id_order` ".Configuration::get('LEOPOPUPSALE_ORDER_SORT');

                $array_orders = Db::getInstance()->executeS(
                    'SELECT `id_order`
                    FROM `'._DB_PREFIX_.'orders`
                    WHERE 1 ORDER BY '.$order.' LIMIT '.$limit
                );

                $id_order = '';

                if (!empty($array_orders)) {
                    foreach ($array_orders as $key => $array_order) {
                        if ($key == 0) {
                            $id_order .= $array_order['id_order'];
                        } else {
                            $id_order .= ',' . $array_order['id_order'];
                        }
                    }
                }
                if ($id_order) {
                    $products = Db::getInstance()->executeS(
                        'SELECT DISTINCT p.`id_product`, pl.`name`, pl.`link_rewrite`
                        FROM `'._DB_PREFIX_.'product` p
                        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                        ON p.`id_product` = pl.`id_product`
                        LEFT JOIN `'._DB_PREFIX_.'product_shop` pshop
                        ON p.`id_product` = pshop.`id_product`
                        LEFT JOIN `'._DB_PREFIX_.'category_product` pc
                        ON p.`id_product` = pc.`id_product`
                        WHERE pc.`id_product`
                        IN (SELECT DISTINCT `product_id` FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` IN('.$id_order.'))
                        AND pl.`id_lang` = '.(int)$this->id_lang.'
                        AND pshop.`active` = 1'
                    );

                    $products = $this->getImageProduct($products);

                    $customers = Db::getInstance()->executeS(
                        'SELECT CONCAT(LEFT(`firstname`, 1), `lastname`) AS `name`, `phone`, CONCAT(`address1`," ",`city`) AS `address`
                        FROM `'._DB_PREFIX_.'address`
                        WHERE id_customer
                        IN (SELECT DISTINCT `id_customer` FROM `'._DB_PREFIX_.'orders` WHERE `id_order` IN('.$id_order.'))'
                    );
                }
                
            } else if (Configuration::get('LEOPOPUPSALE_SELECT_TYPE') == 3) {
                $products = $this->getProduct(Configuration::get('LEOPOPUPSALE_LIST_PRODUCT'));
                $products = $this->getImageProduct($products);

                $customers = unserialize($this->base64Decode(
                    Configuration::get('LEOPOPUPSALE_CUSTOMER')
                ));
            } else {
                $category = new Category((int) Configuration::get('LEOPOPUPSALE_CATEGORY'));
                $sort = Configuration::get('LEOPOPUPSALE_CAT_SORT');

                $products = $category->getProducts((int)$this->id_lang, 1, (int)Configuration::get('LEOPOPUPSALE_CAT_LIMIT'), ($sort=="RAND()"?null:'position'), ($sort=="RAND()"?null:$sort), false, 1, ($sort=="RAND()"?true:false), (int)Configuration::get('LEOPOPUPSALE_CAT_LIMIT'));
                $products = $this->getImageProduct($products);

                $customers = unserialize($this->base64Decode(
                    Configuration::get('LEOPOPUPSALE_CATCUSTOMER')
                ));
            }

            if (!empty($customers)) {
                $customer = $customers[0];
                if (Configuration::get('LEOPOPUPSALE_DELEPHONE')) {
                    $customer['phone'] = 'xxx' . Tools::substr($customer['phone'], 3);

                    foreach ($customers as $key_c => $val_c) {
                        $customers[$key_c]['phone'] = 'xxx' . Tools::substr($val_c['phone'], 3);
                    }
                }
            } else {
                $customer = array(
                    'name' => 'John',
                    'phone' => 'xxx3456789'
                );

                $customers = array(
                    array(
                        'name' => 'John',
                        'phone' => 'xxx3456789',
                    ),
                    array(
                        'name' => 'Doe',
                        'phone' => 'xxx8888888',
                    )
                );
            }

            if (!empty($products)) {
                $products = $products[0];
            }

            $time = Configuration::get('LEOPOPUPSALE_TIME');
            $interval = Configuration::get('LEOPOPUPSALE_INVERVAL');
            $position = Configuration::get('LEOPOPUPSALE_POSITION');
            $lable_bg = Configuration::get('LEOPOPUPSALE_LABLE_BG');
            $lable_text = Configuration::get('LEOPOPUPSALE_LABLE_TEXT');
            $bg_color = Configuration::get('LEOPOPUPSALE_BG');
            $text_color = Configuration::get('LEOPOPUPSALE_TEXT_COLOR');
            $theme = Configuration::get('LEOPOPUPSALE_SELECT_THEME');

            if (Tools::getIsset('leopopupconfig')) {
                $time = Tools::getValue('time');
                $interval = Tools::getValue('interval');
                $position = Tools::getValue('position');
                $theme = Tools::getValue('theme');
            }

            $lable_style = ($lable_bg?'background-color:'.$lable_bg:'').($lable_text?';color:'.$lable_text:'');
            $box_style = ($bg_color?'background-color:'.$bg_color:'').($text_color?';color:'.$text_color:'');
            $text_style = ($text_color?';color:'.$text_color:'');
            $this->smarty->assign(array(
                'theme' => $theme,
                'lable_style' => $lable_style,
                'box_style' => $box_style,
                'text_style' => $text_style,
                'type' => Configuration::get('LEOPOPUPSALE_SELECT_TYPE'),
                'leointerval' => $interval,
                'leotime' => $time,
                'products' => $products,
                'leoprosize' => Configuration::get('LEOPOPUPSALE_IMG_SIZE'),
                'product' => $products,
                'showname' => Configuration::get('LEOPOPUPSALE_SHOWNAME'),
                'showphone' => Configuration::get('LEOPOPUPSALE_SHOWPHONE'),
                'showaddress' => Configuration::get('LEOPOPUPSALE_SHOWADDRESS'),
                'position' => $position,
                'customers' => $customers,
                'customer' => $customer,
            ));
        }
        if (Tools::getIsset('leopopupconfig')) {
            return $this->fetch('module:leopopupsale/views/templates/hook/popup.tpl');
        } else {
            return $this->fetch('module:leopopupsale/views/templates/hook/popup.tpl', $this->getCacheId());
        }
    }

    public function getImageProduct($products)
    {
        if (!empty($products)) {
            foreach ($products as $key_p => $product) {
                $products[$key_p]['url'] = $this->context->link->getProductLink($product['id_product']);
                $image = Image::getCover($product['id_product']);
                $products[$key_p]['img'] = $this->context->link->getImageLink(
                    $product['link_rewrite'],
                    $image['id_image'],
                    ImageType::getFormattedName(Configuration::get('LEOPOPUPSALE_IMG_SIZE'))
                );
            }
        }

        return $products;
    }

    public function base64Decode($data)
    {
        return call_user_func('base64_decode', $data);
    }

    public function base64Encode($data)
    {
        return call_user_func('base64_encode', $data);
    }

    public function displayProductSearch($products)
    {
        $this->smarty->assign(array(
            'products' => $products,
        ));

        return $this->fetch('module:leopopupsale/views/templates/admin/leo-product.tpl');
    }
}
