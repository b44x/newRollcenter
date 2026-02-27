<?php return array(
    'root' => array(
        'name' => 'prestashow-module/pshowcookie',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => null,
        'type' => 'prestashop-module',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'prestashow-module/pshowcookie' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'prestashop-module',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'prestashow/presta-core' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => 'cfef8ce2507bc27d9c22920684ddcd067311797a',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/../prestashow/presta-core',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
        'prestashow/presta-update' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '72587e93115b38af1aff1e2769a79a328e4c66ce',
            'type' => 'library',
            'install_path' => __DIR__ . '/../prestashow/presta-update',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
    ),
);
