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

class LeoElementsShortcodeModel extends ObjectModel
{
    public $shortcode_key;
    // public $id_leoelements;
    public $shortcode_name;
    public $active = true;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements_shortcode',
        'primary' => 'id_leoelements_shortcode',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'shortcode_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            // 'id_leoelements' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            
            'shortcode_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true, 'required' => true),
                    
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
        )
    );
    
    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
    }
    
    public function add($autodate = true, $null_values = false)
    {
        $id_shop = $context->shop->id;
        $res = parent::add($autodate, $null_values);
        
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'leoelements_shortcode_shop` (`id_shop`, `id_leoelements_shortcode`, `active`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.', '.(int)$this->active.')');
        return $res;
    }
    
    public function update($nullValues = false)
    {
        $id_shop = $context->shop->id;
        $res = parent::update($nullValues);
                    
        $res &= Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'leoelements_shortcode_shop` ps set ps.active = '.(int)$this->active.'  WHERE ps.id_shop='.(int)$id_shop.' AND ps.id_leoelements_shortcode = '.(int)$this->id);
    
        return $res;
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
                    (int)$this->id.' AND id_shop IN ('.pSQL(implode(', ', $id_shop_list)).')');
            }
            
            //DONGND:: delete leoelements related shortcode
            $id_leoelements = LeoElementsModel::getIdByIdShortCode((int)$this->id);
            
            if ($id_leoelements) {
                $obj = new LeoElementsModel($id_leoelements);
                $obj->delete();
            }
        }
        
        return $result;
    }
    
    public function getShortCodeContent($id_leoelements = 0, $is_font = 0, $id_lang = 0)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND p.id_leoelements='.(int)$id_leoelements;
   
        if ($id_lang) {
            $where .= ' AND pl.id_lang = '.(int)$id_lang;
        } else {
            $id_lang = $context->language->id;
        }
        $sql = 'SELECT p.*, pl.params, pl.id_lang
                FROM `'._DB_PREFIX_.'leoelements` p
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_shop` ps ON (ps.id_leoelements = p.id_leoelements)
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_lang` pl ON (pl.id_leoelements = p.id_leoelements)
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_shortcode` pp ON (p.id_leoelements_shortcode = pp.id_leoelements_shortcode)
                    
                '.pSql($where).' ORDER BY p.id_leoelements';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
//        # FIX - Only get language data valid
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
        $data_hook = array();
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
     * @param type $id_profiles
     * @param type $is_font (=0: for back-end; =1: for front-end)
     * @param type $id_lang
     * @return type
     */
    public static function getAllItems($id_leoelements, $is_font = 0, $id_lang = 0)
    {
        $context = Context::getContext();
//        $id_profiles = (int)$profile['id_leoelements_profiles'];
        $id_shop = (int)$context->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)$context->language->id;

        $sql = 'SELECT p.*, pl.params, pl.id_lang
                FROM '._DB_PREFIX_.'leoelements p
                    LEFT JOIN '._DB_PREFIX_.'leoelements_shop ps ON (ps.id_leoelements = p.id_leoelements AND id_shop='.(int)$id_shop.')
                    LEFT JOIN '._DB_PREFIX_.'leoelements_lang pl ON (pl.id_leoelements = p.id_leoelements)
                    LEFT JOIN `'._DB_PREFIX_.'leoelements_shortcode` pp ON (p.id_leoelements_shortcode = pp.id_leoelements_shortcode)
                WHERE
                    pl.id_lang='.(int)$id_lang.'
                    AND ps.id_shop='.(int)$id_shop.'
                    AND p.id_leoelements = '.(int)$id_leoelements.'
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
    
    public static function getShortCode($shortcode_key)
    {
        $id_shop = Context::getContext()->shop->id;
       
        $sql = 'SELECT p.*, pp.`id_leoelements` FROM `'._DB_PREFIX_.'leoelements_shortcode` p
                INNER JOIN `'._DB_PREFIX_.'leoelements_shortcode_shop` ps on(p.`id_leoelements_shortcode` = ps.`id_leoelements_shortcode`)
                INNER JOIN `'._DB_PREFIX_.'leoelements` pp on(p.`id_leoelements_shortcode` = pp.`id_leoelements_shortcode`) WHERE
                p.`shortcode_key` = "'.pSQL($shortcode_key).'" AND ps.`active`= 1 AND ps.`id_shop` = '.(int)$id_shop;
        
        return Db::getInstance()->getRow($sql);
    }
    
    public static function getListShortCode()
    {
        $id_shop = Context::getContext()->shop->id;
        $id_lang = Context::getContext()->language->id;
       
        $sql = 'SELECT p.*, ps.*, pl.* FROM `'._DB_PREFIX_.'leoelements_shortcode` p
                INNER JOIN `'._DB_PREFIX_.'leoelements_shortcode_shop` ps on(p.`id_leoelements_shortcode` = ps.`id_leoelements_shortcode`)
                INNER JOIN `'._DB_PREFIX_.'leoelements_shortcode_lang` pl on(p.`id_leoelements_shortcode` = pl.`id_leoelements_shortcode`) WHERE
                ps.`id_shop` = '.(int)$id_shop.' AND pl.`id_lang` = '.(int)$id_lang;

        return Db::getInstance()->executeS($sql);
    }
}
