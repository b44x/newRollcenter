<?php
/**
 * Module: Order Number Synchronizer
 * Synchronizuje numerację zamówień między dwoma sklepami PrestaShop
 * Czyta konfigurację bezpośrednio z parameters.php
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderNumberSync extends Module
{
    private $shopPaths = [
        '/home/rolety24/dev.rollcenter.pl',
        '/home/rolety24/mojaroleta.pl'
    ];

    public function __construct()
    {
        $this->name = 'ordernumbersync';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Michell Hoduń';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Order Number Synchronizer');
        $this->description = $this->l('Synchronizuje numerację zamówień między sklepami - czyta config z parameters.php');
    }

public function install()
{
    return parent::install() 
        && $this->registerHook('actionObjectOrderAddAfter')
        && $this->registerHook('actionValidateOrder')
        && Configuration::updateValue('ORDER_SYNC_ENABLED', 1)
        && Configuration::updateValue('ORDER_SYNC_LAST_SYNC', 0);
}

public function hookActionValidateOrder($params)
{
    if (!Configuration::get('ORDER_SYNC_ENABLED')) {
        return;
    }

    $order = $params['order'];
    if (!Validate::isLoadedObject($order)) {
        return;
    }

    $currentPath = _PS_ROOT_DIR_;
    $prefix = (strpos($currentPath, 'rollcenter.pl') !== false) ? 'RC-' : 'R24-';

    // Tworzymy czysty string - rzutowanie (string) jest kluczowe
    $newReference = (string)$prefix . sprintf('%05d', (int)$order->id);

    // 1. Aktualizacja w bazie danych
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'orders` 
        SET `reference` = "' . pSQL($newReference) . '" 
        WHERE `id_order` = ' . (int)$order->id
    );

    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'order_payment` 
        SET `order_reference` = "' . pSQL($newReference) . '" 
        WHERE `order_reference` = "' . pSQL($order->reference) . '"
    ');

    $order->reference = $newReference;

    if (method_exists('Cache', 'clean')) {
        Cache::clean('Order::*');
    }
}

    public function uninstall()
    {
        return Configuration::deleteByName('ORDER_SYNC_ENABLED')
            && Configuration::deleteByName('ORDER_SYNC_LAST_SYNC')
            && parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitOrderSyncConfig')) {
            Configuration::updateValue('ORDER_SYNC_ENABLED', Tools::getValue('ORDER_SYNC_ENABLED'));
            $output .= $this->displayConfirmation($this->l('Ustawienia zapisane'));
        }

        if (Tools::isSubmit('testConnection')) {
            $result = $this->testAllConnections();
            $output .= $result['output'];
        }

        if (Tools::isSubmit('syncNow')) {
            $result = $this->syncOrderNumbers();
            if ($result['success']) {
                $output .= $this->displayConfirmation($result['message']);
            } else {
                $output .= $this->displayError($result['message']);
            }
        }

        return $output . $this->displayForm() . $this->displayInfo();
    }

    public function displayForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Konfiguracja synchronizacji'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Włącz synchronizację'),
                        'name' => 'ORDER_SYNC_ENABLED',
                        'is_bool' => true,
                        'desc' => $this->l('Synchronizacja uruchamia się automatycznie przy każdym nowym zamówieniu'),
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Tak')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->l('Nie')]
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Zapisz'),
                    'class' => 'btn btn-default pull-right'
                ],
                'buttons' => [
                    [
                        'title' => $this->l('Test połączeń'),
                        'name' => 'testConnection',
                        'type' => 'submit',
                        'class' => 'btn btn-info',
                        'icon' => 'process-icon-refresh'
                    ],
                    [
                        'title' => $this->l('Synchronizuj teraz'),
                        'name' => 'syncNow',
                        'type' => 'submit',
                        'class' => 'btn btn-warning',
                        'icon' => 'process-icon-refresh'
                    ]
                ]
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitOrderSyncConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => [
                'ORDER_SYNC_ENABLED' => Configuration::get('ORDER_SYNC_ENABLED'),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    private function displayInfo()
    {
        $lastSync = Configuration::get('ORDER_SYNC_LAST_SYNC');
        $lastSyncDate = $lastSync > 0 ? date('Y-m-d H:i:s', $lastSync) : 'Nigdy';
        
        $html = '<div class="panel"><div class="panel-heading"><i class="icon-info"></i> ' . $this->l('Informacje') . '</div>';
        $html .= '<div class="panel-body">';
        $html .= '<p><strong>' . $this->l('Ścieżki sklepów:') . '</strong></p>';
        $html .= '<ul>';
        
        foreach ($this->shopPaths as $path) {
            $configPath = $path . '/app/config/parameters.php';
            $exists = file_exists($configPath);
            $icon = $exists ? '✅' : '❌';
            $html .= '<li>' . $icon . ' ' . htmlspecialchars($path) . ($exists ? '' : ' <span style="color:red;">(nie znaleziono)</span>') . '</li>';
        }
        
        $html .= '</ul>';
        $html .= '<p><strong>' . $this->l('Ostatnia synchronizacja:') . '</strong> ' . $lastSyncDate . '</p>';
        $html .= '</div></div>';
        
        return $html;
    }

    private function getShopConfigs()
    {
        $configs = [];
        
        foreach ($this->shopPaths as $path) {
            $configFile = $path . '/app/config/parameters.php';
            
            if (!file_exists($configFile)) {
                continue;
            }
            
            $parameters = require $configFile;
            
            if (isset($parameters['parameters'])) {
                $params = $parameters['parameters'];
                
                $configs[] = [
                    'path' => $path,
                    'host' => $params['database_host'] ?? 'localhost',
                    'name' => $params['database_name'] ?? '',
                    'user' => $params['database_user'] ?? '',
                    'pass' => $params['database_password'] ?? '',
                    'prefix' => $params['database_prefix'] ?? 'ps_',
                    'port' => $params['database_port'] ?? 3306,
                ];
            }
        }
        
        return $configs;
    }

    private function getRemoteShopConfig()
    {
        $currentPath = _PS_ROOT_DIR_;
        $configs = $this->getShopConfigs();
        
        foreach ($configs as $config) {
            // Znajdź config dla drugiego sklepu (nie tego w którym jesteśmy)
            if (strpos($currentPath, $config['path']) === false) {
                return $config;
            }
        }
        
        return null;
    }

    private function connectToDatabase($config)
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8',
                $config['host'],
                $config['port'],
                $config['name']
            );
            
            $pdo = new PDO(
                $dsn,
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            
            return $pdo;
        } catch (Exception $e) {
            return false;
        }
    }

    private function testAllConnections()
    {
        $output = '';
        $configs = $this->getShopConfigs();
        
        if (empty($configs)) {
            return [
                'success' => false,
                'output' => $this->displayError($this->l('Nie znaleziono plików konfiguracyjnych!'))
            ];
        }
        
        foreach ($configs as $config) {
            $connection = $this->connectToDatabase($config);
            
            if ($connection) {
                $stmt = $connection->query('SELECT MAX(id_order) as max_id FROM ' . $config['prefix'] . 'orders');
                $result = $stmt->fetch();
                $maxId = $result['max_id'] ?? 0;
                
                $output .= $this->displayConfirmation(
                    sprintf(
                        $this->l('✅ Sklep: %s - Połączenie OK - Najwyższy numer zamówienia: %d'),
                        basename($config['path']),
                        $maxId
                    )
                );
            } else {
                $output .= $this->displayError(
                    sprintf(
                        $this->l('❌ Sklep: %s - Błąd połączenia'),
                        basename($config['path'])
                    )
                );
            }
        }
        
        return ['success' => true, 'output' => $output];
    }

    public function hookActionObjectOrderAddAfter($params)
    {
        if (!Configuration::get('ORDER_SYNC_ENABLED')) {
            return;
        }

        // Synchronizuj z małym opóźnieniem żeby zamówienie było już w bazie
        $this->syncOrderNumbers();
    }

    private function syncOrderNumbers()
    {
        try {
            $configs = $this->getShopConfigs();
            
            if (count($configs) < 2) {
                return [
                    'success' => false, 
                    'message' => $this->l('Nie znaleziono konfiguracji obu sklepów')
                ];
            }
            
            $maxId = 0;
            $shopData = [];
            
            // Pobierz MAX ID z każdego sklepu
            foreach ($configs as $config) {
                $connection = $this->connectToDatabase($config);
                
                if (!$connection) {
                    return [
                        'success' => false,
                        'message' => sprintf(
                            $this->l('Nie można połączyć ze sklepem: %s'),
                            basename($config['path'])
                        )
                    ];
                }
                
                $stmt = $connection->query('SELECT MAX(id_order) as max_id FROM ' . $config['prefix'] . 'orders');
                $result = $stmt->fetch();
                $currentMax = (int)($result['max_id'] ?? 0);
                
                $shopData[] = [
                    'connection' => $connection,
                    'config' => $config,
                    'current_max' => $currentMax
                ];
                
                if ($currentMax > $maxId) {
                    $maxId = $currentMax;
                }
            }
            
            // Ustaw AUTO_INCREMENT dla wszystkich sklepów
            $nextId = $maxId + 1;
            
            foreach ($shopData as $data) {
                $sql = sprintf(
                    'ALTER TABLE %sorders AUTO_INCREMENT = %d',
                    $data['config']['prefix'],
                    $nextId
                );
                $data['connection']->exec($sql);
            }
            
            Configuration::updateValue('ORDER_SYNC_LAST_SYNC', time());
            
            return [
                'success' => true,
                'message' => sprintf(
                    $this->l('✅ Synchronizacja zakończona! Najwyższy numer: %d, następne zamówienie: %d'),
                    $maxId,
                    $nextId
                )
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $this->l('Błąd: ') . $e->getMessage()
            ];
        }
    }
}
