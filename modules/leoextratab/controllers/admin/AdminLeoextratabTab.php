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

require_once _PS_MODULE_DIR_ . 'leoextratab/LeoTab.php';

class AdminLeoextratabTabController extends ModuleAdminController
{
    protected $position_identifier = 'id_tab';
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->table = 'leo_tab';
        $this->identifier = 'id_tab';
        $this->className = 'LeoTab';
        $this->lang = true;
        $this->_defaultOrderBy = 'position';
        $this->bootstrap = true;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->fields_list = array(
            'id_tab' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'filter' => false,
                'search' => false
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
                'filter' => false,
                'search' => false
            ),
            'type_product' => array(
                'title' => $this->l('Type with product'),
                'type' => 'text',
                'filter' => false,
                'search' => false
            ),
            'product' => array(
                'title' => $this->l('product ID'),
                'type' => 'text',
                'filter' => false,
                'search' => false
            ),
            'type' =>  array(
                'title' => $this->l('Type'),
                'type' => 'text',
                'filter' => false,
                'search' => false
            ),
            'id_category' => array(
                'title' => $this->l('Category'),
                'type' => 'text',
                'filter' => false,
                'search' => false
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-md'
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'bool',
                'active' => 'status',
                'filter' => false,
                'search' => false
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
            ),
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->context->controller->addJS(__PS_BASE_URI__.'modules/leoextratab/views/js/back.js');
    }

    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        

        $this->getList($this->context->language->id);

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->trans('Bad SQL query', array(), 'Admin.Notifications.Error') . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        

        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;
        $skip_list = array();

        $modules = new Leoextratab();
        foreach ($this->_list as &$row) {
            switch ($row['type_product']) {
                case '1':
                    $row['type_product'] = $this->l('Show in all product');
                    break;
                case '2':
                    $row['type_product'] = $this->l('Show in product of Selecting category');
                    break;
                case '3':
                    $row['type_product'] = $this->l('Show with product ID');
                    break;
            }
            switch ($row['type']) {
                case '1':
                    $row['type'] = $this->l('Content');
                    break;
                case '2':
                    $row['type'] = $this->l('Cms');
                    break;
            }
            if ($row['id_category'] == '0') {
                $row['id_category'] = $modules->l('All Category');
            } else {
                $array_cates = explode(',', $row['id_category']);
                $count = 1;
                $name_cate = '';

                foreach ($array_cates as $array_cate) {
                    $obj_cate = new Category($array_cate, $this->context->language->id, $this->context->shop->id);

                    if ($count == 1) {
                        $name_cate .= $obj_cate->name;
                    } else {
                        $name_cate .= ',' . $obj_cate->name;
                    }

                    $count ++;
                }

                $row['id_category'] = $name_cate;
            }
        }
        
        if (array_key_exists('delete', $helper->list_skip_actions)) {
            $helper->list_skip_actions['delete'] = array_merge($helper->list_skip_actions['delete'], (array) $skip_list);
        } else {
            $helper->list_skip_actions['delete'] = (array) $skip_list;
        }

        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    public function renderForm()
    {
        $switch = array(
            array(
                'id' => 'active_on',
                'value' => '1',
                'label' => $this->l('Yes')
            ),
            array(
                'id' => 'active_off',
                'value' => '0',
                'label' => $this->l('No')
            )
        );

        $cmss = CMS::listCms($this->context->language->id, false, true);

        $querys = array(
            array(
                'id' => '0',
                'name' => $this->l('Select Cms')
            )
        );

        if (!empty($cmss)) {
            foreach ($cmss as $cms) {
                $querys[] = array(
                    'id' => $cms['id_cms'],
                    'name' => $cms['meta_title']
                );
            }
        }
        $input_check_ap = array(
            'type' => 'hidden',
            'name' => 'input_check_ap'
        );
        if (!(bool)Module::isEnabled('appagebuilder')) {
            $input_check_ap = array(
                    'type' => 'html_leotab',
                    'name' => 'html_leotab',
                    'text' => $this->module->l('You can not use tab Shortcode. Click this link to download'),
                );
        }

        $categories = Category::getCategories($this->context->language->id, true);
        $categories_li = array();
        $this->module->recurseCategory($categories_li, $categories, null);

        $obj_tab = new LeoTab();
        $checked = 1;

        if (Tools::getIsset('id_tab')) {
            $id_tab = Tools::getVAlue('id_tab');
            $obj_tab = new LeoTab((int)$id_tab, (int)$this->context->language->id);

            if ($obj_tab->id_category == '0') {
                foreach ($categories_li as $key_li => $val_li) {
                    $categories_li[$key_li]['checked'] = 1;
                }
            } else {
                $checked = 0;
                $array_cate = explode(',', $obj_tab->id_category);

                foreach ($categories_li as $key_li => $val_li) {
                    if (in_array($val_li['id_category'], $array_cate)) {
                        $categories_li[$key_li]['checked'] = 1;
                    } else {
                        $categories_li[$key_li]['checked'] = 0;
                    }
                }
            }
        } else {
            $checked = 0;
            
            foreach ($categories_li as $key_li => $val_li) {
                $categories_li[$key_li]['checked'] = 0;
            }
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add New Tab'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                $input_check_ap,
                array(
                    'type' => 'select',
                    'label' => $this->l('Displaying Type'),
                    'name' => 'type_product',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '1',
                                'name' => $this->l('Show in all product')
                            ),
                            array(
                                'id' => '2',
                                'name' => $this->l('Show in product of Selecting category')
                            ),
                            array(
                                'id' => '3',
                                'name' => $this->l('Show with product ID')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'label' => $this->l('Select Category'),
                    'type' => 'html_category',
                    'name' => 'html_category',
                    'html_category' => $this->module->leoCategory($categories_li, $checked),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Category'),
                    'name' => 'id_category',
                    'class' => 'leo-id-category',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product ID'),
                    'name' => 'product',
                    'desc' => $this->l('Show in products. Ex: 1,2,3,4,5,...'),
                    'class' => 'product'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '1',
                                'name' => $this->l('Content')
                            ),
                            array(
                                'id' => '2',
                                'name' => $this->l('Cms')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select CMS'),
                    'name' => 'cms',
                    'options' => array(
                        'query' => $querys,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'class' => $obj_tab->type == 1 || $obj_tab->type == '' ? 'leo_hide leo_type_2' : 'leo_type_2',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name Tab'),
                    'name' => 'name',
                    'lang' => true,
                    'class' => $obj_tab->type == 2 ? 'leo_hide leo_type_1' : 'leo_type_1',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Content Tab'),
                    'lang' => true,
                    'name' => 'content',
                    'autoload_rte' => true,
                    'class' => $obj_tab->type == 2 ? 'leo_hide leo_type_1' : 'leo_type_1',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => $switch,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        return parent::renderForm();
    }

    public function processAdd()
    {
        $obj_tab = new LeoTab();
        parent::validateRules();

        if (count($this->errors) <= 0) {
            $this->copyFromPost($obj_tab, 'tab');
            $position = DB::getInstance()->executeS('SELECT MAX(position)
                FROM `'._DB_PREFIX_.'leo_tab` WHERE 1');
            $obj_tab->position = (int)$position[0]['MAX(position)'] + 1;
            if (!$obj_tab->add(false)) {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.')
                .' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            }

            if (Tools::getValue('save_and_add') === '' || ToolsCore::getValue('save_and_add')) {
                $this->redirect_after = self::$currentIndex.'&conf=3&add'.$this->table.'&token='.$this->token;
            }
        }

        $this->errors = array_unique($this->errors);

        if (!empty($this->errors)) {
             $this->display = 'edit';
            return false;
        }

        $this->display = 'list';

        if (empty($this->errors)) {
                $this->confirmations[] = $this->_conf[3];
        }
    }

    public function updatePosition($way, $position, $id)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT `id_tab`, `position`
            FROM `'._DB_PREFIX_.'leo_tab`
            ORDER BY `position` ASC')) {
            return false;
        }

        foreach ($res as $tab) {
            if ((int)$tab['id_tab'] == (int)$id) {
                $moved_tab = $tab;
            }
        }

        if (!isset($moved_tab) || !isset($position))
            return false;
        var_dump($moved_tab['position']);
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'leo_tab`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way
                ? '> '.(int)$moved_tab['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_tab['position'].' AND `position` >= '.(int)$position.'
            ').';UPDATE `'._DB_PREFIX_.'leo_tab`
            SET `position` = '.(int)$position.'
            WHERE `id_tab` = '.(int)$moved_tab['id_tab']);
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int)Tools::getValue('way');
        $id_tab = (int)Tools::getValue('id');
        $positions = Tools::getValue('tab');
        if (is_array($positions)) {
            foreach ($positions as $position => $value)
            {
                $pos = explode('_', $value);

                if (isset($pos[2]) && (int)$pos[2] === $id_tab)
                {
                    if (isset($position) && $this->updatePosition($way, $position, $id_tab)) {
                        echo 'ok position '.(int)$position.' for id '.(int)$pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update id '.(int)$id_tab.' to position '.(int)$position.' "}';
                    }
                    break;
                }
            }
        }
    }
}
