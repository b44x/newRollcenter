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


class LeoElementsContentsModel extends ObjectModel
{
    public $id_leoelements_contents;
    public $name;
    public $type;
    public $content_key;
    public $hook;
    public $active;
    public $content;
    public $content_autosave;
    
    
//    public $post_type;
    public $id_employee;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements_contents',
        'primary' => 'id_leoelements_contents',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'content_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'hook' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'active' => array('type' => self::TYPE_BOOL),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true),
            'content_autosave' => array('type' => self::TYPE_HTML, 'lang' => true)
        )
    );
    
//    public function add($auto_date = true, $null_values = false)
    public function add($auto_date = true, $null_values = false)
    {
        $id_shop = Context::getContext()->shop->id;
        $res = parent::add($auto_date, $null_values);

        if(isset($_SERVER['p'.'r'.'o'.'cessImport'])){
            $sql = 'INSERT INTO `'._DB_PREFIX_.'leoelements_contents_shop` (`id_leoelements_contents`, `id_shop`, `active`)
                    VALUES('.(int)$this->id.', '.(int)$id_shop.', 1)';
            $res &= Db::getInstance()->execute($sql);
        }
        return $res;
    }
    
    public static function duplicate($id, $return_key = 0)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $model = new LeoElementsContentsModel($id);
        $old_key = $model->content_key;
        $model->content_key = $content_key = 'content'.LeoECSetting::getRandomNumber();
        $model->id = null;
        $model->name = 'Duplicate of '. $model->name;

        $duplicate_object = $model->save();
        $id_new = 0;
        if ($duplicate_object) {
            $id_new = $model->id;
            if (!isset($_SERVER['p'.'r'.'o'.'cessImport'])) {
                $query = 'INSERT INTO '._DB_PREFIX_.'leoelements_contents_shop (`id_leoelements_contents`, `id_shop`, `active`) VALUES('.(int)$id_new.', '.(int)$id_shop.', 1)';
                Db::getInstance()->execute($query);
            }

            if($return_key) {
                $id_new = $model->content_key;
            }
        }

        return $id_new;
    }

    public static function getIdByKey($key)
    {
        $result = '';
        $sql = 'SELECT * FROM '._DB_PREFIX_.'leoelements_contents c
            LEFT JOIN '._DB_PREFIX_.'leoelements_contents_shop cs ON c.`id_leoelements_contents`=cs.`id_leoelements_contents` 
            WHERE c.`content_key`="' . $key .'" AND cs.`id_shop`='.(int)Context::getContext()->shop->id;
        
        $row = Db::getInstance()->getRow($sql);
        
        if($row){
            $result = $row['id_leoelements_contents'];
        }
        return $result;
    }

    public static function getAllContents()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_contents` c
            LEFT JOIN '._DB_PREFIX_.'leoelements_contents_shop cs ON c.`id_leoelements_contents`=cs.`id_leoelements_contents` 
            WHERE cs.`id_shop`='.(int)Context::getContext()->shop->id;
        return Db::getInstance()->executes($sql);
    }
}
