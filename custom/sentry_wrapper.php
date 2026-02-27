<?php
// Zapisz aktualny autoloader
$originalAutoloaders = spl_autoload_functions();

// Wyłącz wszystkie autoloadery
foreach ($originalAutoloaders as $autoloader) {
    spl_autoload_unregister($autoloader);
}

// Załaduj Sentry w izolacji
require_once '/home/rolety24/sentry/vendor/autoload.php';

\Sentry\init([
    'dsn' => 'https://0f20efbae4fee2eadae94c12a5de0495@o4510466990014465.ingest.de.sentry.io/4510467091857488',
  'traces_sample_rate' => 1.0,
//  'enable_logs' => true,

    'environment' => 'production',
]);

// Przywróć oryginalne autoloadery
foreach ($originalAutoloaders as $autoloader) {
    spl_autoload_register($autoloader);
}
