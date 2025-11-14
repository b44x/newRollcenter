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

class LeoTab extends ObjectModel
{
    public $id_tab;
    public $name;
    public $type_product;
    public $product;
    public $content;
    public $type;
    public $cms;
    public $id_category;
    public $position;
    public $active;
    
    public static $definition = array(
        'table' => 'leo_tab',
        'primary' => 'id_tab',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'name' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate'),
            'content' =>     array('type' => self::TYPE_HTML, 'lang' => true, 'validate'),
            'type_product' => array('type' => self::TYPE_INT),
            'product' =>     array('type' => self::TYPE_STRING),
            'type' =>        array('type' => self::TYPE_INT),
            'cms' =>         array('type' => self::TYPE_INT),
            'id_category' => array('type' => self::TYPE_STRING),
            'position' =>    array('type' => self::TYPE_INT),
            'active' =>      array('type' => self::TYPE_INT),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->context = Context::getContext();

        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }

    public function getTab($active = 1)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leo_tab`';

        if ($active == 1) {
            $sql .= 'WHERE `active` = ' . (int)$active;
        }

        $tabs = Db::getInstance()->executeS($sql);

        return $this->fomatTab($tabs);
    }

    public function fomatTab($tabs)
    {
        $modules =  new Leoextratab();

        $appagebuilder = Module::getInstanceByName('appagebuilder');
        if (!empty($tabs)) {
            foreach ($tabs as $key_t => $tab) {
                if ($tab['type'] == 1) {
                    $obj_tabs = new LeoTab(
                        (int)$tab['id_tab'],
                        (int)$this->context->language->id,
                        (int)$this->context->shop->id
                    );

                    $tabs[$key_t]['name'] = $obj_tabs->name;
                    $tabs[$key_t]['content'] = $obj_tabs->content;
                    $tabs[$key_t]['leo_type'] = $modules->l('Content');
                }

                if ($tab['type'] == 2) {
                    $cms = new CMS(
                        (int)$tab['cms'],
                        (int)$this->context->language->id,
                        (int)$this->context->shop->id
                    );

                    $tabs[$key_t]['name'] = $cms->meta_title;
                    $tabs[$key_t]['content'] = $cms->content;
                    $tabs[$key_t]['leo_type'] = $modules->l('Cms');
                }
                if ((bool)Module::isEnabled('appagebuilder')) {
                    $tabs[$key_t]['content'] = $appagebuilder->buildShortCode($obj_tabs->content);
                }
                
                if ($tab['id_category'] == '0') {
                    $tabs[$key_t]['category'] = $modules->l('All Category');
                } else {
                    $array_cates = explode(',', $tab['id_category']);
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

                    $tabs[$key_t]['category'] = $name_cate;
                }
            }
        }

        return $tabs;
    }

    public function getTabProduct($id_product)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leo_tab` WHERE `active` = 1 order by `position` ASC';
        $id_category = Product::getProductCategories($id_product);
        $tabs = $this->fomatTab(Db::getInstance()->executeS($sql));
        foreach ($tabs as $key => $value) {
            if ($value['type_product'] == 1) {
                if (in_array($id_product, explode(',', $value['product']))) {
                    unset($tabs[$key]);
                }
            } elseif ($value['type_product'] == 3) {
                if (!in_array($id_product, explode(',', $value['product']))) {
                    unset($tabs[$key]);
                }
            } else {
                if ($value['id_category'] == 0 && in_array($id_product, explode(',', $value['product']))) {
                    unset($tabs[$key]);
                } else {
                    if (!array_intersect($id_category, explode(',', $value['id_category'])) || in_array($id_product, explode(',', $value['product']))) {
                        unset($tabs[$key]);
                    }
                }
            }

            if (!Configuration::get('LEOEXTRATAB_SHOW_EMPTY_TAB') && $value['content'] == '') {
                unset($tabs[$key]);
            }
        }
        return $tabs;
    }
}
