<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsPositionsModel.php');
require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsFont.php');

class LeoElementsProfilesModel extends ObjectModel
{
    public $name;
    public $friendly_url;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $group_box;
    public $params;
    public $page;
    public $profile_key;
    public $header;
    public $content;
    public $footer;
    public $product;
    public $active;
    public $active_mobile;
    public $active_tablet;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements_profiles',
        'primary' => 'id_leoelements_profiles',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'friendly_url' => array('type' => self::TYPE_STRING, 'size' => 255, 'lang' => true, 'validate' => 'isLinkRewrite'),
            'meta_title' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true),
            'group_box' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'page' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'profile_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'header' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'content' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'footer' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'product' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true),
            'active_mobile' => array('type' => self::TYPE_BOOL, 'shop' => true),
            'active_tablet' => array('type' => self::TYPE_BOOL, 'shop' => true),
            'params' => array('type' => self::TYPE_HTML)
        )
    );

    public function loadDataShop()
    {
        if ($this->def['multishop'] == true) {
            $sql = 'SELECT * FROM ' ._DB_PREFIX_.$this->def['table'] . '_shop WHERE ' .$this->def['primary'] . ' =' .(int)$this->id;
            $this->data_shop = Db::getInstance()->getRow($sql);
            
            if (isset($this->data_shop['active'])) {
                $this->active = $this->data_shop['active'];
            }
        }
    }

    public function toggleStatus()
    {
        $this->deActiveAll($this->page);
        return true;
    }

    public function deActiveAll($page)
    {
        // validate module
        unset($page);
        $id_shop = Context::getContext()->shop->id;
        //$where = ' WHERE ps.id_shop='.$id_shop." AND ps.id_leoelements_profiles != '".(int)$this->id."'";
        $where = ' WHERE ps.id_shop='.(int)$id_shop;
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'leoelements_profiles_shop` ps set ps.active = 0 '.$where);
        $where = ' WHERE ps.id_shop='.$id_shop." AND ps.id_leoelements_profiles = '".(int)$this->id."'";
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'leoelements_profiles_shop` ps set ps.active = 1 '.$where);
    }

    public function toggleStatusMT($field)
    {
        $id_shop = Context::getContext()->shop->id;
        $where = ' WHERE id_shop='.$id_shop.' AND id_leoelements_profiles = "'.(int)$this->id.'"';
        $where1 = ' WHERE id_leoelements_profiles = "'.(int)$this->id.'"';
        $result = Db::getInstance()->getRow('SELECT '.$field.' from  `'._DB_PREFIX_.'leoelements_profiles_shop` '.$where);
        $value = $result[$field]==1?0:1;

        if ($value == 1) {
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'leoelements_profiles_shop` set '.$field.' = "0" WHERE id_shop="'.$id_shop.'"');
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'leoelements_profiles` set '.$field.' = "0"');
        }
        
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'leoelements_profiles_shop` set '.$field.' = "'.$value.'" '.$where);
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'leoelements_profiles` set '.$field.' = "'.$value.'" '.$where1);

        return true;
    }

    public static function getProfileBackOffice()
    {
        $id_shop = Context::getContext()->shop->id;
        $id_lang = Context::getContext()->language->id;

        $sql = 'SELECT p.*, ps.*,pl.*
                        FROM '._DB_PREFIX_.'leoelements_profiles p
                        INNER JOIN '._DB_PREFIX_.'leoelements_profiles_lang pl ON (p.id_leoelements_profiles = pl.id_leoelements_profiles)
                        INNER JOIN '._DB_PREFIX_.'leoelements_profiles_shop ps ON (ps.id_leoelements_profiles = p.id_leoelements_profiles)
                        WHERE id_shop='.(int)$id_shop.' AND id_lang='.(int)$id_lang.' AND ps.active = 1';
        $result = Db::getInstance()->executes($sql);
        if(isset($result[0])) {
            return $result[0];
        } else {
            return array();
        }
    }

    public static function getAllProductList()
    {
        try {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
            $id_lang = $context->language->id;

            $cacheId = 'leoelements::getAllProductList_' . md5(
                (int) $id_shop .
                (int) $id_lang
            );

            if (!Cache::isStored($cacheId)) {

                $context = Context::getContext();
                $id_shop = $context->shop->id;
                $where = ' WHERE id_shop='.(int)$id_shop;
                $sql = 'SELECT p.id_leoelements_product_list, p.plist_key, p.name 
                         FROM '._DB_PREFIX_.'leoelements_product_list p
                         INNER JOIN '._DB_PREFIX_.'leoelements_product_list_shop ps ON (ps.id_leoelements_product_list = p.id_leoelements_product_list)'
                        .$where;
                $result = Db::getInstance()->executes($sql);
            } else {
                $result = Cache::retrieve($cacheId);
            }
            Cache::store($cacheId, $result);
            return $result;
        } catch (Exception $exc) {
            return array(); // OLD MODULE, NOT NEED SHOW DATA
        }
    }

    public static function getAllCategory()
    {
        try {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
            $id_lang = $context->language->id;

            $cacheId = 'leoelements::getAllCategory' . md5(
                (int) $id_shop .
                (int) $id_lang
            );

            if (!Cache::isStored($cacheId)) {

                $context = Context::getContext();
                $id_shop = $context->shop->id;
                $where = ' WHERE id_shop='.(int)$id_shop;
                $sql = 'SELECT p.name, p.clist_key, p.id_leoelements_category
                         FROM '._DB_PREFIX_.'leoelements_category p
                         INNER JOIN '._DB_PREFIX_.'leoelements_category_shop ps ON (ps.id_leoelements_category = p.id_leoelements_category)'
                        .$where;
                $result = Db::getInstance()->executes($sql);
            } else {
                $result = Cache::retrieve($cacheId);
            }
            Cache::store($cacheId, $result);
            return $result;
        } catch (Exception $exc) {
            return array(); // OLD MODULE, NOT NEED SHOW DATA
        }
    }

    public static function getAllCategoryFull()
    {
        try {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
            $id_lang = $context->language->id;

            $cacheId = 'leoelements::getAllCategoryFull' . md5(
                (int) $id_shop .
                (int) $id_lang
            );

            if (!Cache::isStored($cacheId)) {

                $context = Context::getContext();
                $id_shop = $context->shop->id;
                $where = ' WHERE id_shop='.(int)$id_shop;
                $sql = 'SELECT p.*, ps.*
                         FROM '._DB_PREFIX_.'leoelements_category p
                         INNER JOIN '._DB_PREFIX_.'leoelements_category_shop ps ON (ps.id_leoelements_category = p.id_leoelements_category)'
                        .$where;
                $result = Db::getInstance()->executes($sql);
            } else {
                $result = Cache::retrieve($cacheId);
            }
            Cache::store($cacheId, $result);
            return $result;
        } catch (Exception $exc) {
            return array(); // OLD MODULE, NOT NEED SHOW DATA
        }
    }

    public static function getAllPosition()
    {
        try {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
            $id_lang = $context->language->id;

            $cacheId = 'leoelements::getAllPosition';

            if (!Cache::isStored($cacheId)) {
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_positions`';
                $result = Db::getInstance()->executes($sql);
                $postions = array();
                foreach ($result as $pos) {
                    $postions[$pos['position']][] = $pos;
                }
                
                Cache::store($cacheId, $postions);
                return $postions;
            } else {
                $result = Cache::retrieve($cacheId);
            }
            return $result;
        } catch (Exception $exc) {
            return array(); // OLD MODULE, NOT NEED SHOW DATA
        }
    }


    public static function getAllProductDetail()
    {
        try {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
            $id_lang = $context->language->id;

            $cacheId = 'leoelements::getAllProductDetail_' . md5(
                (int) $id_shop .
                (int) $id_lang
            );

            if (!Cache::isStored($cacheId)) {

                $context = Context::getContext();
                $id_shop = $context->shop->id;
                $where = ' WHERE id_shop='.(int)$id_shop;
                $sql = 'SELECT p.id_leoelements_products, p.plist_key, p.name
                         FROM '._DB_PREFIX_.'leoelements_products p
                         INNER JOIN '._DB_PREFIX_.'leoelements_products_shop ps ON (ps.id_leoelements_products = p.id_leoelements_products)'
                        .$where;
                $result = Db::getInstance()->executes($sql);
                Cache::store($cacheId, $result);
                return $result;
            } else {
                $result = Cache::retrieve($cacheId);
            }
            return $result;
        } catch (Exception $exc) {
            return array(); // OLD MODULE, NOT NEED SHOW DATA
        }
    }

    public static function getAllProfileByShop()
    {
        try {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
            $id_lang = $context->language->id;

            $cacheId = 'leoelements::getAllProfileByShop_' . md5(
                (int) $id_shop .
                (int) $id_lang
            );

            if (!Cache::isStored($cacheId)) {

                $sql = 'SELECT p.*, ps.*,pl.*
                        FROM '._DB_PREFIX_.'leoelements_profiles p
                        INNER JOIN '._DB_PREFIX_.'leoelements_profiles_lang pl ON (p.id_leoelements_profiles = pl.id_leoelements_profiles)
                        INNER JOIN '._DB_PREFIX_.'leoelements_profiles_shop ps ON (ps.id_leoelements_profiles = p.id_leoelements_profiles)
                        WHERE id_shop='.(int)$id_shop.' AND id_lang='.(int)$id_lang;
                $result = Db::getInstance()->executes($sql);

                $sql = 'SELECT *
                        FROM '._DB_PREFIX_.'leoelements_positions p';
                $resultp = Db::getInstance()->executes($sql);

                $sql = 'SELECT *
                        FROM '._DB_PREFIX_.'leoelements_positions p';
                $resultp = Db::getInstance()->executes($sql);
                $position_key = array();
                foreach($resultp as &$p) {
                    
                    $position_key[$p['position_key']] = $p;
                }
                foreach($result as &$pr) {
                    foreach (array('header', 'content', 'footer', 'product') as $val) {
                        $pkey = $pr[$val];
                        if(isset($position_key[$pkey])) {
                            $pr[$val.'_content'] = $position_key[$pkey];
                            if($position_key[$pkey]['params']){
                                $params = json_decode($position_key[$pkey]['params'] , 1);
                                foreach($params as $pa=>$pv) {
                                    $pr[$pa] = $pv;
                                }
                            }
                        }
                    }
                }
                Cache::store($cacheId, $result);
                return $result;
            } else {
                $result = Cache::retrieve($cacheId);
            }
            return $result;
        } catch (Exception $exc) {
            return array(); // OLD MODULE, NOT NEED SHOW DATA
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = Context::getContext()->shop->id;
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'leoelements_profiles_shop` (`id_shop`, `id_leoelements_profiles`, `active_mobile`, `active_tablet`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.', '.(int)$this->active_mobile.', '.(int)$this->active_tablet.')');
        if (Db::getInstance()->getValue('SELECT COUNT(p.`id_leoelements_profiles`) AS total FROM `'._DB_PREFIX_.'leoelements_profiles` p
                INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_shop` ps ON(p.id_leoelements_profiles = ps.id_leoelements_profiles)
                WHERE id_shop='.(int)$id_shop) <= 1) {
                $this->deActiveAll($this->page);
        } else if ($this->active) {
            $this->deActiveAll($this->page);
        }
        return $res;
    }

    public function update($null_values = false)
    {
        // validate module
        unset($null_values);
        if ($this->active) {
            $this->deActiveAll($this->page);
        }

        return parent::update();
    }

    public function getProfilesInPage($id = 0)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $where = ' WHERE ps.id_shop='.(int)$id_shop." AND p.page='".pSQL($this->page)."'";
        if ($id) {
            $where .= ' AND p.id_leoelements_profiles !='.(int)$id;
        }
        $inner_join = 'INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_shop` ps ON (ps.id_leoelements_profiles = p.id_leoelements_profiles)';
        $sql = 'SELECT p.* from `'._DB_PREFIX_.'leoelements_profiles` p '.$inner_join.$where;
        return Db::getInstance()->executes($sql);
    }

    public function getProfileById($all_profile, $id_profile)
    {
        foreach ($all_profile as $profile) {
            if ($profile['id_leoelements_profiles'] == $id_profile) {
                return $profile;
            }
        }
    }

    public static function getActiveProfile($page, $all_profile = array(), $use_mobile_theme = 1)
    {
        // validate module
        unset($page);

        # Fix bug http://screencast.com/t/flCEjya6
        $updatePositions = Tools::getValue('action');
        $ajax = Tools::getValue('ajax');

        if ($updatePositions == 'updatePositions' && $ajax == '1') {
            return null;
        }
        $result = array();

        $context = Context::getContext();
        $is_use_co = Configuration::get('leoelements_COOKIE_PROFILE');
        //not apply for mobile view
        if ($context->isMobile()) {
            $is_use_co = 0;
        }

        # ACCESS BY id_leoelements_profiles
        if (Tools::getIsset('id_leoelements_profiles') && Tools::getValue('id_leoelements_profiles')) {
            $id_profile = Tools::getValue('id_leoelements_profiles');
            foreach ($all_profile as $profile) {
                if ($profile['id_leoelements_profiles'] == $id_profile) {
                    $result = $profile;
                    break;
                }
            }
        } else {
            # ACCESS BY friendly_url
            $linkRewrive = explode('/', $_SERVER['REQUEST_URI']);
            $linkRewrive = rtrim(end($linkRewrive), '.html');
            if (strpos($linkRewrive, '?')) {
                // REMOVE ? FROM URL
                $temp_str = explode("?", $linkRewrive);
                $linkRewrive = $temp_str[0];
                $linkRewrive = rtrim($linkRewrive, '.html');
            }
            if (!empty($linkRewrive)) {
                foreach ($all_profile as $profile) {
                    if ($profile['friendly_url'] == $linkRewrive) {
                        $result = $profile;
                        break;
                    }
                }
            }

            //no 1: from cookie
            if ($is_use_co && $context->cookie->leo_profile) {
                foreach ($all_profile as $profile) {
                    if ($profile['id_leoelements_profiles'] == $context->cookie->leo_profile) {
                        $result = $profile;
                        break;
                    }
                }
            }

            //no 2: from config in profile by group
            if (!$result) {
                # SET PROFILE FOLLOW GROUP_CUSTOMER
                $current_group_id = Group::getCurrent()->id;
                if (isset($current_group_id) && $current_group_id == 3) {
                    $customer = Context::getContext()->customer;
                    $sql = 'SELECT id_group FROM `'._DB_PREFIX_.'customer_group` WHERE id_customer='.(int)$customer->id;
                    $array_group_id = Db::getInstance()->executeS($sql);
                    foreach ($array_group_id as $group_id) {
                        if ($group_id !=1 && $group_id !=2 && $group_id !=3) {
                            $current_group_id = $group_id['id_group'];
                        }
                    }
                }

                foreach ($all_profile as $profile) {
                    $group_boxs = $profile['group_box'];
                    $aray_group_box = explode(',', $group_boxs);
                    foreach ($aray_group_box as $group_box) {
                        if (isset($current_group_id) && $current_group_id == $group_box) {
                            $result = $profile;
                            break;
                        }
                    }
                }
            }

            //no 3 get from active profile and for mobile
            if (!$result) {
                $desktop = array();
                foreach ($all_profile as $profile) {
                    if ($use_mobile_theme && $context->isMobile()) {
                        if ($profile['active_mobile'] == 1) {
                            $result = $profile;
                            break;
                        }
                    }
                    if ($use_mobile_theme && $context->isTablet()) {
                        if ($profile['active_tablet'] == 1) {
                            $result = $profile;
                            break;
                        }
                    }

                    if ($profile['active'] == 1) {
                        $desktop = $profile;
                    }
                }

                if (!$result) {
                    $result = $desktop;
                }
            }
        }

        if ($is_use_co) {
            $context->cookie->leo_profile = $result['id_leoelements_profiles'];
        }
        
        //pass header in url
        foreach (array('header', 'content', 'footer', 'product') as $val) {
            $pos_key = 'ap_'.$val;
            if (Tools::getIsset($val)) {
                $result[$val] = Tools::getValue($val);
                
                if (Tools::isSubmit('controller') && Tools::getValue('controller') === 'AdminNewsletterPro') {
                     // Conflict with Newsletter Pro module
                } else {
                    $context->cookie->{$pos_key} = $result[$val];
                }
            } else if ($is_use_co && $context->cookie->{$pos_key}) {
                $result[$val] = $context->cookie->{$pos_key};
            }
        }

        return $result;
    }

    public function getProfile($id)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_profiles` WHERE id_leoelements_profiles='.(int)$id;
        $object = Db::getInstance()->getRow($sql);
        return $object ? $object : null;
    }

    public function duplicateProfile($id, $name, $profile_key, $id_shop)
    {
        $new_id = 0;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_profiles` WHERE id_leoelements_profiles='.(int)$id;
        $object_duplicated = Db::getInstance()->getRow($sql);
        if ($object_duplicated) {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'leoelements_profiles`(name, profile_key, page) VALUES("'
                    .pSQL($name).pSQL($object_duplicated['name']).'", "'.pSQL($profile_key).'", "index")';
            Db::getInstance()->execute($sql);
            $new_id = Db::getInstance()->Insert_ID();
            $sql = 'INSERT INTO `'._DB_PREFIX_.'leoelements_profiles_shop`(id_leoelements_profiles, id_shop, active) VALUES('
                    .(int)$new_id.', '.(int)$id_shop.', 0)';
            Db::getInstance()->execute($sql);
            return $new_id;
        }
        return 0;
    }

    public function customDuplicateObject($message)
    {
        $object_duplicated = parent::duplicateObject();
        $object_duplicated->active = 0;
        $object_duplicated->name = $message.' '.$object_duplicated->name;
        return $object_duplicated;
    }

    public function save($null_values = false, $autodate = true)
    {
        // validate module
        unset($null_values);
        unset($autodate);
        $context = Context::getContext();
        $this->id_shop = $context->shop->id;
        if ($this->active) {
            $this->deActiveAll($this->page);
        }

        return parent::save();
    }

    public static function deleteById($id)
    {
        $id = (int)$id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_profiles` WHERE id_leoelements_profiles='.(int)$id;
        $object_duplicated = Db::getInstance()->getRow($sql);
        if ($object_duplicated) {
            $sql = 'DELETE FROM `'._DB_PREFIX_.'leoelements_profiles` WHERE id_leoelements_profiles='.(int)$id;
            Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'leoelements_profiles_lang` WHERE id_leoelements_profiles='.(int)$id;
            Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'leoelements_profiles_shop` WHERE id_leoelements_profiles='.(int)$id;
            Db::getInstance()->execute($sql);
            return $object_duplicated;
        }
        return array();
    }

    public function getPositionsForProfile($id_positions)
    {
        if ($id_positions) {
            $id_positions = implode(',', array_map('intval', $id_positions));
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_positions` WHERE id_leoelements_positions IN('.pSQL($id_positions).')';
            return Db::getInstance()->executes($sql);
        }
        return array();
    }

    public function delete($params = array())
    {
        $result = parent::delete();
        
        if ($result) {
            if (isset($this->def['multishop']) && $this->def['multishop'] == true) {
                # DELETE RECORD FORM TABLE _SHOP
                $id_shop_list = Shop::getContextListShopID();
                if (count($this->id_shop_list)) {
                    $id_shop_list = $this->id_shop_list;
                }

                $id_shop_list = array_map('intval', $id_shop_list);

                Db::getInstance()->delete($this->def['table'].'_shop', '`'.$this->def['primary'].'`='.
                    (int)$this->id.' AND id_shop IN ('.pSQL(implode(', ', $id_shop_list)).')');
                
                if (isset($params['import_sample']) && $params['import_sample'] == true) {
                    $position = new leoelementsPositionsModel((int)$this->header);
                    $position->delete();
                    $position = new leoelementsPositionsModel((int)$this->content);
                    $position->delete();
                    $position = new leoelementsPositionsModel((int)$this->footer);
                    $position->delete();
                    $position = new leoelementsPositionsModel((int)$this->product);
                    $position->delete();
                }
            }
        }
        
        return $result;
    }
    
    /**
     * $id_profile = leoelementsProfilesModel::getIdProfileFromRewrite();
     */
    public static function getIdProfileFromRewrite($linkRewrive = '')
    {
        static $id_leoelements_profiles = null;
        
        if ($id_leoelements_profiles === null) {
            $context = Context::getContext();
            $id_shop = (int)$context->shop->id;
            $id_lang = (int)$context->language->id;
                
            # ACCESS BY id_leoelements_profiles
            $id_temp = Tools::getIsset('id_leoelements_profiles') ? Tools::getValue('id_leoelements_profiles') : false;
            if ($id_temp !== false) {
                $id_leoelements_profiles = $id_temp;
                return $id_leoelements_profiles;
            }

            # ACCESS BY friendly_url
            $linkRewrive = explode('/', $_SERVER['REQUEST_URI']);
            $linkRewrive = rtrim(end($linkRewrive), '.html');
            if (strpos($linkRewrive, '?')) {
                // REMOVE ? FROM URL
                $temp_str = explode("?", $linkRewrive);
                $linkRewrive = $temp_str[0];
                $linkRewrive = rtrim($linkRewrive, '.html');
            }
            if (!empty($linkRewrive)) {
                $sql = 'SELECT p.`id_leoelements_profiles` FROM `'._DB_PREFIX_.'leoelements_profiles` p';
                $sql .= ' INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_lang` pl ON(pl.id_leoelements_profiles = p.id_leoelements_profiles AND pl.friendly_url=\''.pSQL($linkRewrive).'\' AND id_lang='.(int)$id_lang.' ) ';
                $sql .= ' INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_shop` ps ON(ps.id_leoelements_profiles = p.id_leoelements_profiles AND ps.id_shop ='.(int)$id_shop.')';

                $id_leoelements_profiles = Db::getInstance()->getValue($sql);
                return $id_leoelements_profiles;
            }
            
            
            
            # ACESS BY active_profile
            $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND ps.active=1 ';

            $inner_join = ' INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_lang` pl ON (pl.id_leoelements_profiles = p.id_leoelements_profiles) AND id_lang='.(int)$id_lang;
            $inner_join .= ' INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_shop` ps ON (ps.id_leoelements_profiles = p.id_leoelements_profiles)';
            $sql = 'SELECT p.id_leoelements_profiles, p.name, p.profile_key, pl.id_lang, ps.id_shop, ps.active from `'._DB_PREFIX_.'leoelements_profiles` p '.$inner_join.$where;

            $active_profile = Db::getInstance()->getRow($sql);
            
            if ($active_profile) {
                return $active_profile['id_leoelements_profiles'];
            }
            
            return false;
        }
        return $id_leoelements_profiles;
    }
    
    public static function getAllProfileRewrite($id_profile = null)
    {
        if ($id_profile) {
            $id_shop = Context::getContext()->shop->id;
        
            if ($id_profile) {
                $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND p.id_leoelements_profiles='.(int)$id_profile;
            }

            $inner_join = ' INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_lang` pl ON (pl.id_leoelements_profiles = p.id_leoelements_profiles)';
            $inner_join .= ' INNER JOIN `'._DB_PREFIX_.'leoelements_profiles_shop` ps ON (ps.id_leoelements_profiles = p.id_leoelements_profiles)';
            $sql = 'SELECT p.id_leoelements_profiles, p.name, pl.id_lang, pl.friendly_url from `'._DB_PREFIX_.'leoelements_profiles` p '.$inner_join.$where;
            $result = Db::getInstance()->executeS($sql);
            
            foreach ($result as $key => $value) {
                $result[$key]['iso_code'] = Language::getIsoById($result[$key]['id_lang']);
            }
            // validate module
            unset($value);
            
            return $result;
        }
        return array();
    }

    public static function getFontFamily($default=false)
    {
        if ($default == 'default') {
            return '';
        }
        $result = array(
            array( 'id' => '0', 'name' => '----- Select -----'),
            array( 'id' => 'arial', 'name' => 'Arial'),
            array( 'id' => 'verdana', 'name' => 'Verdana, Geneva'),
            array( 'id' => 'trebuchet', 'name' => 'Trebuchet'),
            array( 'id' => 'georgia', 'name' => 'Georgia'),
            array( 'id' => 'times', 'name' => 'Times New Roman'),
            array( 'id' => 'tahoma', 'name' => 'Tahoma, Geneva'),
            array( 'id' => 'palatino', 'name' => 'Palatino'),
            array( 'id' => 'helvetica', 'name' => 'Helvetica'),
        );
        
        return $result;
    }

    public static function getFontFamilyGoogle()
    {
        $result = array();
        $uploaded_fonts = LeoElementsFont::getAllFonts(2); //getAllFonts($type) -> $type = 2 ->get GG font
        if ($uploaded_fonts) {
            foreach ($uploaded_fonts as &$font) {
                $result[] = array(
                    'id' => $font['id_leoelements_fonts'],
                    'name' => $font['name'],
                    'font_family' => $font['font_family'],
                    'file' => $font['file'],
                    'type' => $font['type'],
                    'font_style' => $font['font_style'],
                    'font_weight' => $font['font_weight'],
                );
            }
        }
        return $result;
    }

    public static function getFontFamilyUploaded()
    {
        $result = array();
        $uploaded_fonts = LeoElementsFont::getAllFonts(1); //getAllFonts($type) -> $type = 1 ->get uploaded font
        if ($uploaded_fonts) {
            foreach ($uploaded_fonts as &$font) {
                $result[] = array(
                    'id' => $font['id_leoelements_fonts'],
                    'name' => $font['name'],
                    'font_family' => $font['font_family'],
                    'file' => $font['file'],
                    'type' => $font['type'],
                    'font_style' => $font['font_style'],
                    'font_weight' => $font['font_weight'],
                );
            }
        }
        return $result;
    }

    public static function getFontWeight($default = false)
    {
        if ($default == 'default') {
            return '';
        }
        $result = array(
            array( 'id' => '', 'name' => '----- Select -----'),
            array( 'id' => '400', 'name' => '400 (Normal)'),
            array( 'id' => '700', 'name' => '700 (Bold)'),
            array( 'id' => '100', 'name' => '100'),
            array( 'id' => '200', 'name' => '200'),
            array( 'id' => '300', 'name' => '300'),
            array( 'id' => '500', 'name' => '500'),
            array( 'id' => '600', 'name' => '600'),
            array( 'id' => '800', 'name' => '800'),
            array( 'id' => '900', 'name' => '900'),
        );
        return $result;
    }

    public static function getFontStyle($default = false)
    {
        if ($default == 'default') {
            return '';
        }
        $result = array(
            array( 'id' => '', 'name' => '----- Select -----'),
            array( 'id' => 'normal', 'name' => 'Normal'),
            array( 'id' => 'italic', 'name' => 'Italic'),
        );
        return $result;
    }

    public static function renderCSSFont($tag)
    {
        $html = '';
        $classTag = array('h1','h2','h3','h4','h5','h6');
        if (Tools::getValue($tag . '_font_family')) {
            $html .= ' font-family:' . ((int)Tools::getValue($tag . '_font_family') ? (new LeoElementsFont(Tools::getValue($tag . '_font_family')))->font_family : Tools::getValue($tag . '_font_family')) . ';';
        }
        if ((int)Tools::getValue($tag . '_font_size')) {
            $html .= ' font-size:' . (int)Tools::getValue($tag . '_font_size') . 'px;';
        }
        if ((int)Tools::getValue($tag . '_line_height')) {
            $html .= ' line-height:' . (int)Tools::getValue($tag . '_line_height') . 'px;';
        }
        if ((int)Tools::getValue($tag . '_font_weight')) {
            $html .= ' font-weight:' . (int)Tools::getValue($tag . '_font_weight') . ';';
        }
        if (Tools::getValue($tag . '_font_style')) {
            $html .= ' font-style:' . Tools::getValue($tag . '_font_style') . ';';
        }
        
        $output = '';
        if (!empty($html)) {
            $output = $tag.(in_array($tag, $classTag) ? ', .'.$tag : '') . ' {'.$html.' }'."\n";
        }
        
        return $output;
    }
}
