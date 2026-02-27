<?php

if (class_exists('__AbstractModule')) {
    return;
}

if (!class_exists('__AbstractModule')) {
abstract class __AbstractModule extends Module
{
    public $version = '1.0.0';
    public $author = 'PrestaShow.com';
    public $need_instance = 0;
    public $bootstrap = true;
    public $is_configurable = false;

    public function __construct()
    {
        $this->name = strtolower(get_class($this));
        $this->displayName = get_class($this);

        parent::__construct();
    }

    public function __call($name, $arguments)
    {
        return null;
    }

    public function __get($name)
    {
        return null;
    }

    public function install()
    {
        $this->__checkRequirements();
        return false;
    }

    public function enable($force_all = false)
    {
        $this->__checkRequirements();
        return false;
    }

    private function __checkRequirements()
    {
        if (PHP_VERSION_ID < 70100) {
            $this->_errors[] = 'This module requires the PHP version 7.1 or higher.';
        }
        if (!function_exists('ioncube_loader_version')) {
            $this->_errors[] = 'This module requires the ionCube loader to be installed on your server.';
        }
    }

}

abstract class __AbstractAdminController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        if (version_compare(_PS_VERSION_, '1.7.0') >= 0) {
            $this->translator = Context::getContext()->getTranslator();
        }

        if (!function_exists('ioncube_loader_version')) {
            $this->errors[] = 'This module requires the ionCube loader to be installed on your server.';
        }

        if (PHP_VERSION_ID < 70100) {
            $this->errors[] = 'This module requires the PHP version 7.1 or higher.';
        }
    }

    public function __call($name, $arguments)
    {
        // pass
    }

    public function __get($name)
    {
        return null;
    }

}
}
class __GenericClass
{

    public function __call($name, $arguments)
    {
        // pass
    }

    public function __get($name)
    {
        return null;
    }

}
