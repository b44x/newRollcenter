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

require_once(_PS_MODULE_DIR_.'leoelements/leoECHelper.php');
class LeoElementsPositionsModel extends ObjectModel
{
    public $name;
    public $params;
    public $position;
    public $position_key;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoelements_positions',
        'primary' => 'id_leoelements_positions',
        'multilang' => false,
        'multishop' => false,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'position' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'position_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'params' => array('type' => self::TYPE_HTML)
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getProfileUsingPosition($id)
    {
        $id = (int)$id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_profiles` P
                WHERE
                    P.`header`='.(int)$id.'
                    OR P.`content`='.(int)$id.'
                    OR P.`footer`='.(int)$id.'
                    OR P.`product`='.(int)$id;
        return Db::getInstance()->executes($sql);
    }

    public function addAuto($data)
    {
        $id_shop = Context::getContext()->shop->id;
        
        $sql = 'INSERT INTO `'._DB_PREFIX_.'leoelements_positions` (name, position, position_key)
                VALUES("'.pSQL($data['name']).'", "'.pSQL($data['position']).'", "'.pSQL($data['position_key']).'")';
        Db::getInstance()->execute($sql);
        
        $id = Db::getInstance()->Insert_ID();
        
        return $id;
    }

    public static function getAllPosition()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_positions`';
        return Db::getInstance()->executes($sql);
    }

    public static function getPositionById($id)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoelements_positions` WHERE id_leoelements_positions='.(int)$id;
        return Db::getInstance()->getRow($sql);
    }

    public static function updateName($id, $name)
    {
        $id = (int)$id;
        if ($id && $name) {
            $sql = 'UPDATE '._DB_PREFIX_.'leoelements_positions SET name=\''.pSQL($name).'\' WHERE id_leoelements_positions='.(int)$id;
            return Db::getInstance()->execute($sql);
        }
        return false;
    }

    public static function duplicate($id)
    {
        require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsContentsModel.php');

        $id_shop = Context::getContext()->shop->id;
        $model = new LeoElementsPositionsModel($id);
        $model->position_key = 'position'.LeoECSetting::getRandomNumber();
        $model->id = null;
        $model->name = 'Duplicate of '. $model->name;
        
        $contents = json_decode($model->params);
        $ca = array();
        foreach ($contents as &$content) {
            $sql = 'SELECT id_leoelements_contents FROM '._DB_PREFIX_.'leoelements_contents WHERE content_key = "'.$content.'"';
            $id_content = Db::getInstance()->getRow($sql)['id_leoelements_contents'];
            $content = LeoElementsContentsModel::duplicate($id_content, 1);
        }
        $model->params = json_encode($contents);

        $duplicate_object = $model->save();
        $id_new = 0;
        if ($duplicate_object) {
            $id_new = $model->id;
        }

        return $id_new;
    }
    
    public function delete()
    {
        $result = parent::delete();
        
        if ($result) {
            # Profile not use this position
            if (in_array($this->position, array('header', 'content', 'footer', 'product'))) {
                $sql = 'UPDATE '._DB_PREFIX_.'leoelements_profiles SET `'.bqSQL($this->position).'`=0 WHERE `'.bqSQL($this->position).'`='.(int)$this->id;
                Db::getInstance()->execute($sql);
            }
        }
        return $result;
    }
}
