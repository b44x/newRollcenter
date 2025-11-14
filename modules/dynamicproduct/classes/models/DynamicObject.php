<?php
/**
 * 2007-2025 TuniSoft
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
 * @author    TuniSoft (tunisoft.solutions@gmail.com)
 * @copyright 2007-2025 TuniSoft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace DynamicProduct\classes\models;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\DynamicTools;

class DynamicObject extends \ObjectModel
{
    public static $module_name = 'dynamicproduct';
    /** @var \DynamicProduct */
    protected $module;
    /** @var \Context */
    protected $context;
    public $position;
    protected $dir;
    protected $thumb_suffix = '-thumb.jpg';
    protected $ext = '.jpg';

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->module = DynamicTools::getModule();
        $this->context = DynamicTools::getContext();
        parent::__construct($id, $id_lang, $id_shop);
        $this->castNumericValues();
    }

    public static function getModule()
    {
        return DynamicTools::getModule();
    }

    public function getDir()
    {
        return $this->module->provider->getDataDir($this->dir);
    }

    public function getUrl()
    {
        return $this->module->provider->getDataDirUrl($this->dir);
    }

    /**
     * @param $model
     *
     * @return DynamicObject
     */
    public static function getModelClass($model)
    {
        $namespace = 'classes\models\\';
        $class_name = 'Dynamic' . \Tools::toCamelCase($model, true);

        return $namespace . $class_name;
    }

    /**
     * @param DynamicObject $object
     *
     * @return mixed
     */
    public static function getValues($object, $fill_lang_values = false)
    {
        if ($fill_lang_values) {
            $object = self::fillLangFields($object);
        }

        return json_decode(json_encode($object), true);
    }

    public function getObjectValues($fill_lang_values = false)
    {
        return static::getValues($this, $fill_lang_values);
    }

    /**
     * @param DynamicObject $object
     *
     * @return DynamicObject
     *                       // TODO: remove this
     */
    private static function fillLangFields($object)
    {
        $languages = \Language::getLanguages();
        $lang_fields = self::getLangFields($object);
        foreach ($lang_fields as $lang_field) {
            $value_all_langs = $object->{$lang_field};
            foreach ($languages as $language) {
                $id_lang = $language['id_lang'];
                if (!isset($value_all_langs[$id_lang])) {
                    $value_all_langs[$id_lang] = '';
                }
            }
        }

        return $object;
    }

    public static function getLangFields($object)
    {
        $lang_fields = [];
        $definition = \ObjectModel::getDefinition($object);
        $fields = $definition['fields'];
        foreach ($fields as $field_name => $field) {
            if (self::isLangField($field)) {
                $lang_fields[] = $field_name;
            }
        }

        return $lang_fields;
    }

    public function getPathForCreation($name = 'file')
    {
        if (property_exists($this, $name)) {
            $filename = $this->$name;
            if (!is_string($filename)) {
                return false;
            }
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if ($filename && !$extension) {
                $filename .= $this->ext;
            }

            return $this->getDir() . $filename;
        }

        return false;
    }

    public function getPath($name = 'file')
    {
        $path = $this->getPathForCreation($name);
        if (is_file($path)) {
            return $path;
        }

        return false;
    }

    public function getThumbPathForCreation($name = 'file')
    {
        if (property_exists($this, $name)) {
            $filename = pathinfo($this->$name, PATHINFO_FILENAME);

            return $this->getDir() . $filename . $this->thumb_suffix;
        }

        return false;
    }

    public function getThumbPath($name = 'file')
    {
        $path = $this->getThumbPathForCreation($name);
        if (is_file($path)) {
            return $path;
        }

        return false;
    }

    public function getPixelUrl()
    {
        return $this->module->getUrl() . 'views/img/pixel.png';
    }

    public static function deleteByProduct($id_product)
    {
        return \Db::getInstance()->delete(static::$definition['table'], 'id_product = ' . (int) $id_product);
    }

    public function delete()
    {
        $path = $this->getPath();
        if ($path) {
            unlink($path);
        }

        return parent::delete();
    }

    public function saveFromPost()
    {
        $errors = [];
        $fields = static::$definition['fields'];
        foreach ($fields as $field => $info) {
            $type = $info['type'];
            $is_lang = self::isLangField($info);
            if (!$is_lang) {
                if (\Tools::getIsset($field)) {
                    $value = self::formatValue(\Tools::getValue($field), $type);
                    $this->$field = $value;
                }
                if ((int) $type === self::TYPE_BOOL) {
                    $value = self::formatValue(\Tools::getIsset($field), $type);
                    $this->$field = $value;
                }
            } else {
                $id_default_lang = (int) \Configuration::get('PS_LANG_DEFAULT');
                $translations = \Tools::getValue($field);
                if (!is_array($translations)) {
                    $translations = self::getTranslationsFromArray($field, $_POST);
                }
                if (is_array($translations) && count($translations)) {
                    foreach ($translations as $id_lang => $translation) {
                        if (empty($translation)) {
                            $translation = $translations[$id_default_lang] ?? null;
                        }
                        $translations[$id_lang] = $translation;
                    }
                    $this->$field = $translations;
                }
            }
        }

        if (!(int) $this->id && !(int) $this->position && isset($fields['position'])) {
            $this->position = self::getHighestPosition($this);
        }

        if (is_array($_FILES)) {
            foreach ($_FILES as $name => $file) {
                if (isset($fields[$name]) && !$file['error']) {
                    $field = $fields[$name];
                    if ($field['extensions']) {
                        $path = $this->getDir() . \Tools::replaceAccentedChars($file['name']);
                        $uploader = new \Uploader();
                        $uploader->setName($name);
                        $uploader->setAcceptTypes($field['extensions']);
                        $upload = $uploader->process();
                        if (isset($upload[0])) {
                            if (!$upload[0]['error']) {
                                rename($upload[0]['save_path'], $path);
                                if ($this->getPath($name)) {
                                    unlink($this->getPath($name));
                                }
                                $this->$name = basename($path);
                            } else {
                                $errors[] = \Tools::displayError($upload[0]['error'], false);
                            }
                        }
                    }
                }
            }
        }
        if (!count($errors)) {
            $saved = $this->save();
            if ($saved && isset(static::$definition['complement'])) {
                $this->complement();
            }
        }

        return $errors;
    }

    public function copyFromPost()
    {
        $fields = static::$definition['fields'];
        foreach ($fields as $field => $info) {
            $type = $info['type'];
            if (\Tools::getIsset($field)) {
                $value = self::formatValue(\Tools::getValue($field), $type);
                $this->$field = $value;
            }
        }
    }

    public static function copyFromArray($values, $obj = null, $remove_id = true)
    {
        if ($remove_id && isset($values['id'])) {
            unset($values['id']);
        }
        if (!$obj) {
            $obj = new static();
        }
        $fields = static::$definition['fields'];
        $field_names = array_keys($fields);
        foreach ($field_names as $field) {
            if (isset($values[$field])) {
                $obj->$field = $values[$field];
            }
        }
        if (isset($values['id'])) {
            $obj->id = (int) $values['id'];
        }

        return $obj;
    }

    public static function getHighestPosition($object)
    {
        $sql = new \DbQuery();
        $sql->select('max(position) as maxposition');
        $sql->from(static::$definition['table']);
        if (isset(static::$definition['group_by'])) {
            $group_by = static::$definition['group_by'];
            $sql->where(pSQL($group_by) . ' = ' . (int) $object->$group_by);
        }
        if (isset(static::$definition['fields']['deleted'])) {
            $sql->where('deleted = 0');
        }
        $max = \Db::getInstance()->getValue($sql);

        return (int) $max + 1;
    }

    /**
     * @param DynamicObject[] $objects
     */
    public static function fixPositions($objects)
    {
        $position = 0;
        foreach ($objects as $object) {
            $object->position = $position++;
            $object->save();
        }
    }

    /**
     * @param null $id_lang
     *
     * @return static[]
     */
    public static function getAll($id_lang = null)
    {
        $class_definition = static::$definition;
        $primary = $class_definition['primary'];
        $class = static::class;
        $objects = [];
        $sql = new \DbQuery();
        $sql->select($primary);
        $sql->from($class_definition['table']);
        if (isset($class_definition['fields']['position'])) {
            $sql->orderBy('position ASC');
        }
        $rows = \Db::getInstance()->executeS($sql);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $id_object = $row[$primary];
                $object = new $class($id_object, $id_lang);
                $objects[$id_object] = $object;
            }
        }

        return $objects;
    }

    /**
     * @param $id_product
     * @param bool $order
     * @param null $id_lang
     *
     * @return static[]
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function getByIdProduct($id_product, $order = false, $id_lang = null)
    {
        $objects = [];
        $sql = new \DbQuery();
        $sql->from(static::$definition['table']);
        $sql->where('id_product = ' . (int) $id_product);
        if ($order) {
            $sql->orderBy('position ASC');
        }
        $rows = \Db::getInstance()->executeS($sql, false);
        while ($row = \Db::getInstance()->nextRow($rows)) {
            $id = $row[static::$definition['primary']];
            $object = new static($id, $id_lang);
            if (\Validate::isLoadedObject($object)) {
                $objects[$id] = $object;
            }
        }

        return $objects;
    }

    public static function isLangField($field)
    {
        return isset($field['lang']) && $field['lang'];
    }

    private static function getTranslationsFromArray($field, $array)
    {
        $translations = [];
        foreach ($array as $key => $value) {
            if (preg_match("@{$field}_(\d)+@", $key, $match)) {
                $id_lang = (int) $match[1];
                if ($id_lang) {
                    $translations[$id_lang] = $value;
                }
            }
        }

        return $translations;
    }

    private function complement()
    {
        $definition = static::$definition;
        $complement_field = $definition['complement'];
        if (!(int) $this->$complement_field) {
            return;
        }
        $group_by = $definition['group_by'];
        $data = [
            pSQL($complement_field) => 0,
        ];
        $where = pSQL($group_by) . ' = ' . (int) $this->$group_by;
        $where .= ' AND ' . pSQL($definition['primary']) . ' != ' . (int) $this->id;
        \Db::getInstance()->update($definition['table'], $data, $where);
    }

    private function castNumericValues()
    {
        $fields = static::$definition['fields'];
        foreach ($fields as $field => $info) {
            $type = $info['type'];
            if (in_array((int) $type, [self::TYPE_INT, self::TYPE_FLOAT], true)) {
                $original_value = $this->$field;
                if ($original_value !== null) {
                    $value = self::formatValue($original_value, $type);
                    $this->$field = $value;
                }
            }
        }
    }
}
