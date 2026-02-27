<?php
$aliasClassesDir = __DIR__ . '/alias_classes';

// Zarejestruj autoloader JAKO PIERWSZY
spl_autoload_register(function ($class) use ($aliasClassesDir) {
    // Tylko dla Prestashow i PShow
    if (strpos($class, 'Prestashow\\') !== 0 && strpos($class, 'PShow') !== 0) {
        return false;
    }

    // Konwertuj namespace do ścieżki
    $file = $aliasClassesDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')) . '.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }

    return false;
}, true, true); // prepend=true, throw=true
