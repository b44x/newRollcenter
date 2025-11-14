<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Next Previous for prestashop 1.7: Displays links to the previous and next product on the product pages
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}


class leonextprevious extends Module
{

    public function __construct()
    {
        $this->name = 'leonextprevious';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'LeoTheme';
        $this->bootstrap = true;
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Leo Next - Previous');
        $this->description = $this->l('Displays links to the previous and next product on the product pages.');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

    }

    public function install()
    {
        Configuration::updateValue('LEONEXTPREVIOUS_POSITION', 0);
        Configuration::updateValue('LEONEXTPREVIOUS_DISPLAY_NAMES', 1);
        Configuration::updateValue('LEONEXTPREVIOUS_DISPLAY_IMAGE', 1);
        Configuration::updateValue('LEONEXTPREVIOUS_DISPLAY_PRICE', 1);
        return parent::install()
            && $this->registerHook('productFooter')
            && $this->registerHook('displayLeoNextPrevious')
            && $this->registerHook('displayLeoNextButton')
            && $this->registerHook('displayLeoPreviousButton')
            && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LEONEXTPREVIOUS_POSITION');
        Configuration::deleteByName('LEONEXTPREVIOUS_DISPLAY_NAMES');
        Configuration::deleteByName('LEONEXTPREVIOUS_DISPLAY_IMAGE');
        Configuration::deleteByName('LEONEXTPREVIOUS_DISPLAY_PRICE');
        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitAddconfiguration') || Tools::isSubmit('submit'.$this->name)) {
            Configuration::updateValue('LEONEXTPREVIOUS_POSITION', Tools::getValue('LEONEXTPREVIOUS_POSITION'));
            Configuration::updateValue('LEONEXTPREVIOUS_DISPLAY_NAMES', Tools::getValue('LEONEXTPREVIOUS_DISPLAY_NAMES'));
            Configuration::updateValue('LEONEXTPREVIOUS_DISPLAY_IMAGE', Tools::getValue('LEONEXTPREVIOUS_DISPLAY_IMAGE'));
            Configuration::updateValue('LEONEXTPREVIOUS_DISPLAY_PRICE', Tools::getValue('LEONEXTPREVIOUS_DISPLAY_PRICE'));
            $output = $this->displayConfirmation($this->l('The module settings have been saved.'));
        }

        return $output.$this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('The position of the buttons on the product card.'),
                        'name' => 'LEONEXTPREVIOUS_POSITION',
                        'desc' => $this->l('Only include active products.').'</br>'.$this->l('"Customize" option: use the hooks (only product page)').'</br>{hook h=\'displayLeoNextPrevious\' product=$product}</br>{hook h=\'displayLeoNextButton\' product=$product}</br>{hook h=\'displayLeoPreviousButton\' product=$product}',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => '0',
                                    'name' => $this->l('Bottom')
                                ),
                                array(
                                    'id' => '1',
                                    'name' => $this->l('Top')
                                ),
                                array(
                                    'id' => '2',
                                    'name' => $this->l('At the bottom and at the top')
                                ),
                                array(
                                    'id' => '3',
                                    'name' => $this->l('Customize')
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display names'),
                        'name' => 'LEONEXTPREVIOUS_DISPLAY_NAMES',
                        'desc' => $this->l('If the option is enabled, apart from the standard buttons, the names of products are also displayed.'),
                        'values' => array(
                            array(
                                'id' => $this->name.'_DISPLAY_NAMES_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => $this->name.'_DISPLAY_NAMES_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Price'),
                        'name' => 'LEONEXTPREVIOUS_DISPLAY_PRICE',
                        'desc' => $this->l('If the option is enabled, apart from the standard buttons, the price of products are also displayed.'),
                        'values' => array(
                            array(
                                'id' => 'LEONEXTPREVIOUS_DISPLAY_PRICE_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'LEONEXTPREVIOUS_DISPLAY_PRICE_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Image'),
                        'name' => 'LEONEXTPREVIOUS_DISPLAY_IMAGE',
                        'desc' => $this->l('If the option is enabled, apart from the standard buttons, the image of products are also displayed.'),
                        'values' => array(
                            array(
                                'id' => 'LEONEXTPREVIOUS_DISPLAY_IMAGE_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'LEONEXTPREVIOUS_DISPLAY_IMAGE_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default'
                )
            ),
        );

        $lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();
        $helper->default_form_language = $lang;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->module = $this;
        $helper->title = $this->displayName;
        $helper->tpl_vars = array(
            'fields_value' => $this->getGroupFieldsValues($fields_form['form']),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getGroupFieldsValues($form_general)
    {
        $result = array();
        foreach ($form_general['input'] as $form) {
            if (Configuration::hasKey($form['name'])) {
                $result[$form['name']] = Tools::getValue($form['name'], Configuration::get($form['name']));
            } else {
                $result[$form['name']] = Tools::getValue($form['name'], isset($form['default']) ? $form['default'] : '');
            }
        }
        return $result;
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    public function hookProductFooter($params)
    {
        if (Configuration::get('LEONEXTPREVIOUS_POSITION') != 3) {
            $product = $params['product'];
            $product = $this->context->controller->getProduct();

            $prevnext = $this->getPrevNext($product);
            $this->assignVariables($prevnext['prev'], $prevnext['next'], 1);
            return $this->fetch('module:leonextprevious/views/templates/front/leonextprevious.tpl');
        }
    }

    public function hookDisplayLeoNextPrevious($params)
    {
        if (Configuration::get('LEONEXTPREVIOUS_POSITION') == 3) {
            $product = $params['product'];
            $product = $this->context->controller->getProduct();

            $prevnext = $this->getPrevNext($product);
            $this->assignVariables($prevnext['prev'], $prevnext['next'], 1);
            return $this->fetch('module:leonextprevious/views/templates/front/leonextprevious.tpl');
        }
    }

    public function hookDisplayLeoNextButton($params)
    {
        if (Configuration::get('LEONEXTPREVIOUS_POSITION') == 3) {
            $product = $params['product'];
            $product = $this->context->controller->getProduct();

            $sql = '
                SELECT `position`
                FROM `'._DB_PREFIX_.'category_product`
                WHERE `id_product` = ' . (int) $product->id . '
                    AND `id_category` = ' . (int) $product->id_category_default.'
            ';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

            if ($result) {
                $next = self::getNext($product, $result['position']);
            } else {
                $next = array();
            }

            $this->assignVariables(array(), $next, 0);
            return $this->fetch('module:leonextprevious/views/templates/front/leonextprevious.tpl');
        }
    }

    public function hookDisplayLeoPreviousButton($params)
    {
        if (Configuration::get('LEONEXTPREVIOUS_POSITION') == 3) {
            $product = $params['product'];
            $product = $this->context->controller->getProduct();

            $sql = '
                SELECT `position`
                FROM `'._DB_PREFIX_.'category_product`
                WHERE `id_product` = ' . (int) $product->id . '
                    AND `id_category` = ' . (int) $product->id_category_default.'
            ';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

            if ($result) {
                $prev = self::getPrev($product, $result['position']);
            } else {
                $prev = array();
            }

            $this->assignVariables($prev, array(), 0);
            return $this->fetch('module:leonextprevious/views/templates/front/leonextprevious.tpl');
        }
    }

    public function assignVariables($prev, $next, $buttons)
    {
        if ($prev) {
            $prev['price_static'] = Tools::displayPrice($prev['price']);
        }
        if ($next) {
            $next['price_static'] = Tools::displayPrice($next['price']);
        }
        $this->context->smarty->assign(array(
            'prev' => $prev,
            'next' => $next,
            'position' => Configuration::get('LEONEXTPREVIOUS_POSITION'),
            'display_names' => Configuration::get('LEONEXTPREVIOUS_DISPLAY_NAMES'),
            'display_price' => Configuration::get('LEONEXTPREVIOUS_DISPLAY_PRICE'),
            'display_image' => Configuration::get('LEONEXTPREVIOUS_DISPLAY_IMAGE'),
            'buttons' => $buttons
        ));
    }

    public function getPrevNext($product)
    {
        $sql = '
            SELECT `position`
            FROM `'._DB_PREFIX_.'category_product`
            WHERE `id_product` = ' . (int) $product->id . '
                AND `id_category` = ' . (int) $product->id_category_default.'
        ';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if ($result) {
            return array(
                'prev' => self::getPrev($product, $result['position']),
                'next' => self::getNext($product, $result['position']),
            );
        }

        return array(
            'prev' => null,
            'next' => null,
        );
    }
    
    public static function getPrev($product, $position)
    {
        $sql = '
            SELECT 
                cp.`id_product`, pl.`link_rewrite`, pl.`name`, ps.`price`, il.`id_image`, il.`legend`
            FROM `'._DB_PREFIX_.'category_product` cp
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (cp.`id_product` = pl.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (cp.`id_product` = ps.`id_product`)
                LEFT JOIN '._DB_PREFIX_.'image_shop image_shop ON (image_shop.id_product = cp.id_product AND image_shop.id_shop = "'.(int)Context::getContext()->shop->id.'" AND image_shop.cover=1)
                LEFT JOIN '._DB_PREFIX_.'image_lang il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = "'.(int)Context::getContext()->language->id.'")
            WHERE ps.`id_category_default` = '.(int)$product->id_category_default.' 
                AND cp.`position` < '.(int)$position.' 
                AND cp.`id_category` = ' . (int)$product->id_category_default .' 
                AND pl.`id_lang` = '.(Context::getContext()->language->id).'
                AND ps.`id_shop` = '.(Context::getContext()->shop->id).'
                AND ps.`active` = 1
                AND ps.`visibility` = \'both\'
            ORDER BY cp.`position` DESC
        ';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }
    
    public static function getNext($product, $position)
    {
        $sql = '
            SELECT 
                cp.id_product, pl.link_rewrite, pl.name, ps.`price`, il.`id_image`, il.`legend`
            FROM '._DB_PREFIX_.'category_product cp
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (cp.id_product = pl.id_product)
                LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (cp.id_product = ps.id_product)
                LEFT JOIN '._DB_PREFIX_.'image_shop image_shop ON (image_shop.id_product = cp.id_product AND image_shop.id_shop = "'.(int)Context::getContext()->shop->id.'" AND image_shop.cover=1)
                LEFT JOIN '._DB_PREFIX_.'image_lang il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = "'.(int)Context::getContext()->language->id.'")
            WHERE ps.id_category_default = '.(int)$product->id_category_default.' 
                AND cp.position > '.(int)$position.'
                AND cp.id_category = ' . (int)$product->id_category_default .' 
                AND pl.id_lang = '.(Context::getContext()->language->id).'
                AND ps.`id_shop` = '.(Context::getContext()->shop->id).'
                AND ps.`active` = 1
                AND ps.`visibility` = \'both\'
            ORDER BY cp.position ASC
        ';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }
}
