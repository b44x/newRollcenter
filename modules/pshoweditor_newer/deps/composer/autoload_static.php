<?php

namespace PShowEditorScoped\Composer\Autoload;

class ComposerStaticInit4a36bd95dbc64b3eb6f8da9d240cd159
{
    public static $classMap;
    public static $prefixLengthsPsr4 = ["P" => ["PShowEditorScoped\\Prestashow\\PrestaUpdate\\" => 42, "PShowEditorScoped\\Prestashow\\PrestaCore\\" => 40, "PShowEditorScoped\\Prestashow\\PrestaBlockEditor\\" => 47]];
    public static $prefixDirsPsr4;
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4a36bd95dbc64b3eb6f8da9d240cd159::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4a36bd95dbc64b3eb6f8da9d240cd159::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4a36bd95dbc64b3eb6f8da9d240cd159::$classMap;
        }, NULL, "PShowEditorScoped\\Composer\\Autoload\\ClassLoader");
    }
}
