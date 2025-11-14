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
namespace DynamicProduct\classes\module;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DynamicProduct\classes\models\DynamicObject;

class DynamicPresenter
{
    /** @var \DynamicProduct */
    public $module;
    /** @var \Context */
    public $context;

    protected $model;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getCurrentAction()
    {
        $keys = array_keys($_GET);
        foreach ($keys as $key) {
            if (0 === strpos($key, 'display_')) {
                return str_replace('display_', '', $key);
            }
        }

        return false;
    }

    protected function getFormFields()
    {
    }

    protected function getFormHelper()
    {
        $default_lang = (int) \Configuration::get('PS_LANG_DEFAULT');
        $helper = new \HelperForm();
        $helper->module = $this->module;
        foreach (\Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => $default_lang === (int) $lang['id_lang'] ? 1 : 0,
            ];
        }
        $helper->default_form_language = $default_lang;
        $helper->title = $this->module->displayName;
        $helper->submit_action = 'submit' . $this->model;

        return $helper;
    }

    protected function getFormValues()
    {
        $values = [];
        $id_object = (int) \Tools::getValue('id_' . $this->model);
        $model_class = DynamicObject::getModelClass($this->model);
        if (class_exists($model_class)) {
            $object = new $model_class($id_object);
            $values = DynamicObject::getValues($object, true);
            $values['id_' . $this->model] = $id_object;
        }

        return $values;
    }

    public function display()
    {
        $helper = $this->getFormHelper();
        $helper->fields_value = $this->getFormValues();
        $helper->fields_value['method'] = 'save';

        /* @noinspection PhpVoidFunctionResultUsedInspection */
        return $helper->generateForm([$this->getFormFields()]);
    }
}
