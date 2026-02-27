<?php
class ComposerAutoloaderInit4a36bd95dbc64b3eb6f8da9d240cd159
{
    private static $loader;
    public static function loadClassLoader($class)
    {
        if ("PShowEditorScoped\\Composer\\Autoload\\ClassLoader" === $class) {
            require __DIR__ . "/ClassLoader.php";
        }
    }
    public static function getLoader()
    {
        if (NULL !== self::$loader) {
            return self::$loader;
        }
        require __DIR__ . "/platform_check.php";
        spl_autoload_register(["ComposerAutoloaderInit4a36bd95dbc64b3eb6f8da9d240cd159", "loadClassLoader"], true, true);
        self::$loader = $loader = new PShowEditorScoped\Composer\Autoload\ClassLoader(dirname(__DIR__));
        spl_autoload_unregister(["ComposerAutoloaderInit4a36bd95dbc64b3eb6f8da9d240cd159", "loadClassLoader"]);
        require __DIR__ . "/autoload_static.php";
        call_user_func(PShowEditorScoped\Composer\Autoload\ComposerStaticInit4a36bd95dbc64b3eb6f8da9d240cd159::getInitializer($loader));
        $loader->setClassMapAuthoritative(true);
        $loader->register(true);
        return $loader;
    }
}

