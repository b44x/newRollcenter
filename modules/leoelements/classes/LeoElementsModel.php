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

require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoECSetting.php');

class LeoElementsModel extends ObjectModel
{
    public $hook_name;
    public $params;
    public $id_leoelements_positions;
    public $id_leoelements_shortcode;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements',
        'primary' => 'id_leoelements',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'hook_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'id_leoelements_positions' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'params' => array('type' => self::TYPE_HTML, 'lang' => true),
            'id_leoelements_shortcode' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        # validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
    }
    
    //DONGND:: get id by id shortcode
    public static function getIdByIdShortCode($id_shortcode)
    {
        if (!$id_shortcode) {
            return false;
        }
        $sql = 'SELECT id_leoelements FROM '._DB_PREFIX_.'leoelements WHERE id_leoelements_shortcode = ' . (int)$id_shortcode;
        $result = Db::getInstance()->getRow($sql);
        
        if (!$result) {
            return false;
        }
        
        return $result['id_leoelements'];
    }

    public function getIdbyHookName($hook_name, $position_id)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $sql = 'SELECT p.id_leoelements
                FROM '._DB_PREFIX_.'leoelements p
                    LEFT JOIN '._DB_PREFIX_.'leoelements_shop ps ON (ps.id_leoelements = p.id_leoelements)
                    WHERE ps.id_shop = '.(int)$id_shop.'
                        AND hook_name=\''.pSql($hook_name).'\'
                        AND p.id_leoelements_positions='.(int)$position_id.' ORDER BY p.id_leoelements';
        $result = Db::getInstance()->executeS($sql);
        if (!$result) {
            return false;
        }

        foreach ($result as $value) {
            $sql = 'DELETE FROM '._DB_PREFIX_.'leoelements_shop
                    WHERE id_leoelements IN(SELECT id_leoelements
                        FROM '._DB_PREFIX_.'leoelements
                        WHERE hook_name=\''.pSql($hook_name).'\'
                        AND id_leoelements_positions='.(int)$position_id.'
                        AND id_leoelements != '.(int)$value['id_leoelements'].')';
            Db::getInstance()->execute($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_.'leoelements
                    WHERE hook_name=\''.pSql($hook_name).'\'
                        AND id_leoelements_positions='.(int)$position_id.'
                        AND id_leoelements != '.(int)$value['id_leoelements'];
            Db::getInstance()->execute($sql);

            return $value['id_leoelements'];
        }
    }

    public function getIdbyHookNameAndProfile($hook_name, $profile, $id_lang)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;

        //$id_lang = (int)$id_lang;
        if (!$profile->header && !$profile->content && !$profile->footer && !$profile->product) {
            return array();
        }

        $arr = array($profile->header, $profile->content, $profile->footer, $profile->product);

        $sql = 'SELECT p.id_leoelements, pl.params
                FROM '._DB_PREFIX_.'leoelements p
                    LEFT JOIN '._DB_PREFIX_.'leoelements_shop ps ON (ps.id_leoelements = p.id_leoelements AND id_shop='.(int)$id_shop.')
                    LEFT JOIN '._DB_PREFIX_.'leoelements_lang pl ON (p.id_leoelements = pl.id_leoelements AND pl.id_lang='.(int)$id_lang.')
                WHERE p.`hook_name`=\''.$hook_name.'\'
                    AND ps.id_shop='.(int)$id_shop.'
                    AND pl.id_lang='.(int)$id_lang.'
                    AND p.id_leoelements_positions IN ('. pSQL(implode(',', array_map('intval', $arr))).')
                    ORDER BY p.id_leoelements';
        return Db::getInstance()->getRow($sql);
    }

    /**
     * getListPositisionByType
     * @param type $type = {all, header, content, footer, product}
     * @return type
     */
    public function getListPositisionByType($type = 'all', $id_shop = null)
    {
        $str = Tools::strtolower($type);
        $sql = 'SELECT p.* FROM `'._DB_PREFIX_.'leoelements_positions` p';
        if ($type != 'all') {
            $sql .= ' WHERE p.position=\''.pSQL($str).'\'';
        }

        return Db::getInstance()->executeS($sql, 1);
    }

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = $context->shop->id;
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'leoelements_shop` (`id_shop`, `id_leoelements`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        return $res;
    }

    public function save($null_values = false, $autodate = true)
    {
        # validate module
        unset($null_values);
        unset($autodate);
        $context = Context::getContext();
        $this->id_shop = $context->shop->id;
        return parent::save();
    }

    public function parseData($hook_name, $data, $profile_param)
    {
        ApShortCodesBuilder::$is_front_office = 1;
        ApShortCodesBuilder::$is_gen_html = 1;
        ApShortCodesBuilder::$profile_param = $profile_param;
        $ap_helper = new ApShortCodesBuilder();
        ApShortCodesBuilder::$hook_name = $hook_name;
        $result = $ap_helper->parse($data);
        return $result;
    }

    /**
     * Get all item by position include information: hooks postion and data
     * @param type $pos
     * @param type $id_position
     * @param type $id_profile
     * @param type $is_font
     * @param type $id_lang
     * @return type
     */
    public function getAllItemsByPosition($pos, $id_position, $id_profile = 0, $is_font = 0, $id_lang = 0)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $id_position = (int)$id_position;
        $id_profile = (int)$id_profile;
        $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND pp.id_leoelements_positions='.(int)$id_position;
        if ($id_profile) {
            $where .= ' AND ppr.id_leoelements_profiles='.(int)$id_profile;
        }
        if ($id_lang) {
            $where .= ' AND pl.id_lang = '.(int)$id_lang;
        } else {
//            $id_lang = $context->language->id;  // default in admin account
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }
        $sql = 'SELECT p.*, pl.params, pl.id_lang
                FROM `'._DB_PREFIX_.'leoelements` p
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_shop` ps ON (ps.id_leoelements = p.id_leoelements)
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_lang` pl ON (pl.id_leoelements = p.id_leoelements)
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_positions` pp ON (p.id_leoelements_positions=pp.id_leoelements_positions)
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_profiles` ppr ON (ppr.`'.bqSQL($pos).'`=pp.id_leoelements_positions)
                '.pSql($where).' ORDER BY p.id_leoelements';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
        # FIX - Only get language data valid
//        $id_langs = Language::getLanguages(true, false, true);
//        foreach ($result as $key => $val) {
//            if (isset($val['id_lang']) && !in_array($val['id_lang'], $id_langs)) {
//                unset($result[$key]);
//            }
//        }
        
        $data_lang = array();
        if ($is_font) {
            foreach ($result as $row) {
                $data_lang[$row['hook_name']] = $row['params'];
            }
            return $data_lang;
        }
        $ap_helper = new ApShortCodesBuilder();
        ApShortCodesBuilder::$is_front_office = $is_font;
        ApShortCodesBuilder::$is_gen_html = 1;
        foreach ($result as $row) {
            if (isset($data_lang[$row['id_leoelements']])) {
                $data_lang[$row['id_leoelements']]['params'][$row['id_lang']] = $row['params'];
            } else {
                $data_lang[$row['id_leoelements']] = array(
                    'id' => $row['id_leoelements'],
                    'hook_name' => $row['hook_name'],
                );
                $data_lang[$row['id_leoelements']]['params'][$row['id_lang']] = $row['params'];
            }
        }
        //$data_hook = array_flip(LeoECSetting::getHookHome());
        $hook_config = Configuration::get('APPAGEBUILDER_' . Tools::strtoupper($pos).'_HOOK');
        $hook_config = explode(',', $hook_config ? $hook_config : LeoECSetting::getHook($pos));
        $data_hook = array_flip($hook_config);
        foreach ($data_lang as $row) {
            //process params
            foreach ($row['params'] as $key => $value) {
                ApShortCodesBuilder::$lang_id = $key;
                if ($key == $id_lang) {
                    ApShortCodesBuilder::$is_gen_html = 1;
                    $row['content'] = $ap_helper->parse($value);
                } else {
                    ApShortCodesBuilder::$is_gen_html = 0;
                    $ap_helper->parse($value);
                }
            }
            $data_hook[$row['hook_name']] = $row;
        }
        return array('content' => $data_hook, 'dataForm' => ApShortCodesBuilder::$data_form);
    }

    /**
     * Get all items - datas of all hooks by shop Id, lang Id for front-end or back-end
     * @param type $list_pos_id array
     */
    public function getAllItemsByPositionId($list_pos_id)
    {
        if ($list_pos_id) {
            $sql = 'SELECT DISTINCT(id_leoelements) as id FROM `'._DB_PREFIX_.'leoelements` p
                    WHERE id_leoelements_positions IN('. pSQL(implode(',', array_map('intval', $list_pos_id))).')';
            return Db::getInstance()->executes($sql);
        }
        return array();
    }

    /**
     * Get all items - datas of all hooks by shop Id, lang Id for front-end or back-end
     * @param type $id_profiles
     * @param type $is_font (=0: for back-end; =1: for front-end)
     * @param type $id_lang
     * @return type
     */
    public function getAllItems($profile, $is_font = 0, $id_lang = 0)
    {
        $context = Context::getContext();
//        $id_profiles = (int)$profile['id_leoelements_profiles'];
        $id_shop = (int)$context->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)$context->language->id;
        if (!$profile['header'] && !$profile['content'] && !$profile['footer'] && !$profile['product']) {
            return array();
        }

        $arr = array($profile['header'], $profile['content'], $profile['footer'], $profile['product']);

        $sql = 'SELECT p.*, pl.params, pl.id_lang
                FROM '._DB_PREFIX_.'leoelements p
                    LEFT JOIN '._DB_PREFIX_.'leoelements_shop ps ON (ps.id_leoelements = p.id_leoelements AND id_shop='.(int)$id_shop.')
                    LEFT JOIN '._DB_PREFIX_.'leoelements_lang pl ON (pl.id_leoelements = p.id_leoelements)
                WHERE
                    pl.id_lang='.(int)$id_lang.'
                    AND ps.id_shop='.(int)$id_shop.'
                    AND p.id_leoelements_positions IN ('. pSQL(implode(',', array_map('intval', $arr))).')
                ORDER BY p.id_leoelements';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $data_lang = array();
        if ($is_font) {
            foreach ($result as $row) {
                $data_lang[$row['hook_name']] = $row['params'];
            }
            return $data_lang;
        }
        $ap_helper = new ApShortCodesBuilder();
        ApShortCodesBuilder::$is_front_office = $is_font;
        ApShortCodesBuilder::$is_gen_html = 1;
        foreach ($result as $row) {
            if (isset($data_lang[$row['id_leoelements']])) {
                $data_lang[$row['id_leoelements']]['params'][$row['id_lang']] = $row['params'];
            } else {
                $data_lang[$row['id_leoelements']] = array(
                    'id' => $row['id_leoelements'],
                    'hook_name' => $row['hook_name'],
                );
                $data_lang[$row['id_leoelements']]['params'][$row['id_lang']] = $row['params'];
            }
        }
        $data_hook = array_flip(LeoECSetting::getHookHome());
        foreach ($data_lang as $row) {
            //process params
            foreach ($row['params'] as $key => $value) {
                ApShortCodesBuilder::$lang_id = $key;
                if ($key == $id_lang) {
                    ApShortCodesBuilder::$is_gen_html = 1;
                    $row['content'] = $ap_helper->parse($value);
                } else {
                    ApShortCodesBuilder::$is_gen_html = 0;
                    $ap_helper->parse($value);
                }
            }
            $data_hook[$row['hook_name']] = $row;
        }

        return array('content' => $data_hook, 'dataForm' => ApShortCodesBuilder::$data_form);
    }

    public function getAllStoreByShop()
    {
        return Store::getStores((int)Context::getContext()->language->id);
//        $context = Context::getContext();
//        $id_shop = (int)$context->shop->id;
//        $id_lang = (int)$context->language->id;
//        //$where = ' WHERE id_shop="'.$id_shop.'"';
//        $sql = '
//            SELECT  a.*, cl.name country, st.name state
//            FROM '._DB_PREFIX_.'store a
//                LEFT JOIN '._DB_PREFIX_.'country_lang cl
//                ON (cl.id_country = a.id_country
//                AND cl.id_lang = '.(int)$id_lang.')
//                LEFT JOIN '._DB_PREFIX_.'state st
//                ON (st.id_state = a.id_state)
//            WHERE a.id_store IN (
//                SELECT sa.id_store
//                FROM '._DB_PREFIX_.'store_shop sa
//                WHERE sa.id_shop = '.(int)$id_shop.'
//            )';
//        return Db::getInstance()->executes($sql);
    }

    public function findOtherProfileUsePosition($id_position, $id_profile)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'leoelements_profiles ap
                WHERE (ap.`header`='.(int)$id_position.' OR ap.`content`='.(int)$id_position.'
                    OR ap.`footer`='.(int)$id_position.' OR ap.`product`='.(int)$id_position.')
                    AND ap.`id_leoelements_profiles`<>'.(int)$id_profile;
        return Db::getInstance()->executes($sql);
    }

    public function updateAppagebuilderLang($id, $id_lang, $params)
    {
        //can not use psql, because pramram is import function
        $data = array('params' => $params);
        Db::getInstance()->update('leoelements_lang', $data, 'id_leoelements='.(int)$id.' AND id_lang='.(int)$id_lang);
    }
    
    public function delete()
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
                    (int)$this->id.' AND id_shop IN ('. pSQL(implode(', ', $id_shop_list)).')');
            }
        }
        
        return $result;
    }
}
