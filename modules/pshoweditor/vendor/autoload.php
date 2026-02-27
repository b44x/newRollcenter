<?php

if (file_exists(__DIR__ . '/mhodun_loader.php')) {
    require_once __DIR__ . '/mhodun_loader.php';
}



if (PHP_VERSION_ID >= 70100 && function_exists('ioncube_loader_version')) {
    if (file_exists(dirname(__DIR__) . "/deps/autoload.php")) {
        require_once dirname(__DIR__) . "/deps/autoload.php";
    }
    require_once __DIR__ . "/autoload_.php";
    return;
}


require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/classes.php";

spl_autoload_register(function ($classFullName) {
    if (class_exists($classFullName, false)) {
        return;
    }

    if (
        stripos($classFullName, 'Prestashow\\') !== 0
        && stripos($classFullName, 'PShow') !== 0
    ) {
        return;
    }

    $className = explode('\\', $classFullName);
    $className = end($className);

    if ($className === 'AbstractModule' || $className === 'Module') {
        class_alias(__AbstractModule::class, $classFullName);
        return;
    }

    if (
        $className === 'AbstractAdminController'
        || substr($className, -10) == 'Controller'
    ) {
        class_alias(__AbstractAdminController::class, $classFullName);
        return;
    }

    @class_alias(__GenericClass::class, $classFullName);
});



