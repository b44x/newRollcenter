<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomAdminJs extends Module
{
    public function __construct()
    {
        $this->name = 'customadminjs';
        $this->version = '1.0.0';
        $this->author = 'b4x';
        $this->tab = 'administration';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Custom Admin JS';
        $this->description = 'Loads custom JS from another module into the admin panel';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionAdminControllerSetMedia');
    }


    public function hookActionAdminControllerSetMedia($params)
    {
        $moduleBasePath = _PS_ROOT_DIR_ . '/modules/pshoweditor/vendor/prestashow/presta-block-editor/dist/';

        $jsPath = _MODULE_DIR_ . 'pshoweditor/vendor/prestashow/presta-block-editor/dist/index.js';
        $cssPath = _MODULE_DIR_ . 'pshoweditor/vendor/prestashow/presta-block-editor/dist/index.css';

        if (file_exists($moduleBasePath . 'index.js')) {
            $this->context->controller->addJS($jsPath);
        }

        if (file_exists($moduleBasePath . 'index.css')) {
            $this->context->controller->addCSS($cssPath);
        }
    }

}

