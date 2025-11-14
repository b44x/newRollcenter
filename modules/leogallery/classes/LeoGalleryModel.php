<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoGallery is module help you can displays amazing gallery with many features on product page
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

abstract class LeoGalleryModel
{
    protected $errors = array();
    protected $module;
    protected $configPrefix = null;
    protected $defaultAttributeType = 'text';

    protected $isLoaded = false;

    public function __construct($module, $configPrefix = null)
    {
        $this->module = $module;
        $this->configPrefix = $configPrefix;
    }
    
    public function isLoaded()
    {
        return $this->isLoaded;
    }
    
    protected function l($string, $specific = false)
    {
        return $this->module->l($string, $specific);
    }
    
    public function attributeLabels()
    {
        return array();
    }
    
    public function attributeHints()
    {
        return array();
    }
    
    public function attributeDescriptions()
    {
        return array();
    }
    
    public function rules()
    {
        return array();
    }
    
    public function getAttributeLabel($attribute)
    {
        $labels = $this->attributeLabels();
        if (isset($labels[$attribute])) {
            return $labels[$attribute];
        }
        return $attribute;
    }
    
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        if (isset($hints[$attribute])) {
            return $hints[$attribute];
        }
        return null;
    }
    
    public function getAttributeDescription($attribute)
    {
        $descriptions = $this->attributeDescriptions();
        if (isset($descriptions[$attribute])) {
            return $descriptions[$attribute];
        }
        return null;
    }
    
    public function isAttributeSafe($attribute)
    {
        $rules = $this->rules();
        foreach ($rules as $rule) {
            if (isset($rule[0]) && isset($rule[1]) && in_array($attribute, $rule[0]) && $rule[1] != 'unsafe') {
                return true;
            }
        }
        return false;
    }
    
    public function isAttributeRequired($attribute)
    {
        $rules = $this->rules();
        foreach ($rules as $rule) {
            if (isset($rule[0]) && isset($rule[1]) && in_array($attribute, $rule[0]) && $rule[1] == 'validateRequired') {
                return true;
            }
        }
        return false;
    }
    
    public function getAttributes()
    {
        $attributes = array();
        foreach ($this as $attribute => $value) {
            if ($this->isAttributeSafe($attribute)) {
                $attributes[$attribute] = $value;
            }
        }
        return $attributes;
    }
    
    public function saveToConfig($runValidation = true, $attributes = array())
    {
        if (($runValidation && $this->validate()) || !$runValidation) {
            foreach ($this->getAttributes() as $attr => $value) {
                if (($attributes && in_array($attr, $attributes)) || empty($attributes)) {
                    $a = $this->getConfigAttribueName($attr);
                    Configuration::updateValue($a, $value);
                }
            }
            return true;
        }
        return false;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function addError($attribute, $error)
    {
        if (isset($this->errors[$attribute])) {
            $this->errors[$attribute][] = $error;
        } else {
            $this->errors[$attribute] = array($error);
        }
    }
    
    public function validate($addErrors = true)
    {
        if ($addErrors) {
            $this->errors = array();
        }
        $valid = true;
        foreach ($this->getAttributes() as $attr => $value) {
            if ($validators = $this->getAttributeValidators($attr)) {
                foreach ($validators as $validator) {
                    $method = $validator['validator'];
                    if ((isset($validator['on']) && $validator['on']) || (!isset($validator['on']) || $validator['on'] === null)) {
                        if (method_exists('Validate', $method)) {
                            if (Validate::$method($value)) {
                                $valid = $valid && Validate::$method($value);
                            } else {
                                if ($addErrors) {
                                    if (isset($validator['message'])) {
                                        $this->addError($attr, $this->getMessage($validator['message'], $attr, $value));
                                    } else {
                                        $this->addError($attr, sprintf($this->l('Incorrect "%s" value'), $this->getAttributeLabel($attr)));
                                    }
                                }
                                $valid = false;
                            }
                        } elseif (method_exists($this, $method)) {
                            if ($this->$method($value)) {
                                $valid = $valid && $this->$method($value);
                            } else {
                                if ($addErrors) {
                                    if (isset($validator['message'])) {
                                        $this->addError($attr, $this->getMessage($validator['message'], $attr, $value));
                                    } else {
                                        $this->addError($attr, sprintf($this->l('Incorrect "%s" value'), $this->getAttributeLabel($attr)));
                                    }
                                }
                                $valid = false;
                            }
                        }
                    } else {
                        $valid = $valid && true;
                    }
                }
            }
        }
        return $valid;
    }
    
    protected function getMessage($message, $attribute, $value)
    {
        return strtr($message, array(
            '{attribute}' => $attribute,
            '{label}' => $this->getAttributeLabel($attribute),
            '{value}' => $value
        ));
    }


    public function getAttributeValidators($attribute)
    {
        $rules = $this->rules();
        $validators = array();
        foreach ($rules as $rule) {
            if (isset($rule[0]) && isset($rule[1]) && in_array($attribute, $rule[0]) && $rule[1] != 'unsafe') {
                $validator = array(
                    'validator' => $rule[1],
                    'params' => isset($rule['params'])? $rule['params'] : array(),
                    'message' => isset($rule['message'])? $rule['message'] : null,
                );
                if (isset($rule['on'])) {
                    $validator['on'] = $rule['on'];
                }
                $validators[] = $validator;
            }
        }
        return $validators;
    }
    
    public function loadFromConfig()
    {
        $attributes = array();
        foreach ($this->getAttributeNames() as $attr) {
            $attributes[] = $this->getConfigAttribueName($attr);
        }
        if ($attributes) {
            $config = Configuration::getMultiple($attributes);
            if ($config) {
                foreach ($config as $k => $v) {
                    $a = $this->getModelAttributeName($k);
                    if ($this->isAttributeSafe($a)) {
                        $this->$a = $v;
                    }
                }
            }
        }
        $this->isLoaded = true;
    }
    
    public function populate()
    {
        foreach ($this->getAttributes() as $attribute => $value) {
            $name = $this->getConfigAttribueName($attribute);
            $this->$attribute = Tools::getValue($name, $value);
        }
    }
    
    public function isAttributeHasErrors($attribute)
    {
        if (isset($this->errors[$attribute])) {
            return true;
        }
        return false;
    }
    
    public function getModelAttributeName($attribute)
    {
        $attr = Tools::strtolower($attribute);
        if ($this->configPrefix) {
            return str_replace($this->configPrefix, '', $attr);
        }
        return $attr;
    }
    
    public function getConfigAttribueName($attribute)
    {
        $attribute = $this->configPrefix . $attribute;
        return Tools::strtoupper($attribute);
    }
    
    public function validateRequired($value)
    {
        return !empty($value);
    }
    
    public function getFormHelperConfig()
    {
        $config = array();
        foreach ($this->getAttributeNames() as $attr) {
            $name = $this->getConfigAttribueName($attr);
            $config[$name] = array(
                'type' => $this->getAttributeType($attr),
                'label' => $this->getAttributeLabel($attr),
                'name' => $name,
                'placeholder' => $this->getAttributePlaceholder($attr),
                'form_group_class' => $this->getFormGroupClass($attr),
                'hint' => $this->getAttributeHint($attr),
                'desc' => $this->getAttributeDescription($attr),
                'required' => $this->isAttributeRequired($attr)
            );
            if ($this->getAttributeType($attr) == 'switch') {
                $config[$name]['values'] = array(
                    array(
                        'id' => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    )
                );
            }
            if ($this->getAttributeType($attr) == 'select') {
                $config[$name]['options'] = array(
                    'query' => $this->getSelectOptions($attr),
                    'id' => 'id',
                    'name' => 'name',
                );
            }
        }
        return $config;
    }
    
    public function attributePlaceholders()
    {
        return array();
    }
    
    public function getAttributePlaceholder($attribute)
    {
        $pls = $this->attributePlaceholders();
        if (isset($pls[$attribute])) {
            return $pls[$attribute];
        }
        return null;
    }


    public function getSelectOptions($attribute)
    {
        $method = Tools::toCamelCase("{$attribute}SelectOptions");
        if (method_exists(get_called_class(), $method)) {
            return $this->$method();
        }
    }
    
    public function getFormTitle()
    {
        return null;
    }
    
    public function getFormIcon()
    {
        return 'icon-cog';
    }
    
    public function attributeTypes()
    {
        return array();
    }
    
    public function getAttributeType($attribute)
    {
        $types = $this->attributeTypes();
        if (isset($types[$attribute])) {
            return $types[$attribute];
        }
        return $this->defaultAttributeType;
    }
    
    public function getFormGroupClass($attr)
    {
        $addClass = 'field_' . Tools::strtolower($attr);
        return $this->isAttributeHasErrors($attr)? ('has-error ' . $addClass) : $addClass;
    }
    
    public function attributeDefaults()
    {
        return array();
    }
    
    public function getAttributeDefault($attribute)
    {
        $defaults = $this->attributeDefaults();
        return isset($defaults[$attribute])? $defaults[$attribute] : null;
    }
    
    public function loadAttributeDefault($attribute)
    {
        if (!empty($attribute)) {
            $this->$attribute = $this->getAttributeDefault($attribute);
        }
    }
    
    public function loadDefaults()
    {
        foreach ($this->getAttributeNames() as $attribute) {
            $this->loadAttributeDefault($attribute);
        }
    }
    
    public function clearConfig()
    {
        foreach ($this->getAttributeNames() as $attribute) {
            $a = $this->getConfigAttribueName($attribute);
            Configuration::deleteByName($a);
        }
    }
    
    public function getAttributeNames()
    {
        return array_keys($this->getAttributes());
    }
}
