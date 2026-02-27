<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

// Autoload (jeśli używasz namespace / Symfony controller)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class Rc_Dp_Order_Export extends Module
{
    public function __construct()
    {
        $this->name = 'rc_dp_order_export';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Rollcenter';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'DP Order Export (CSV)';
        $this->description = 'Eksport konfiguracji Dynamic Product do CSV + mapowanie SKU (ref) dla thumbnails.';
    }

    /* =========================================================
     * INSTALL / UNINSTALL
     * ========================================================= */

    public function install()
    {
        return parent::install()
            // przycisk w widoku zamówienia (Symfony)
            && $this->registerHook('displayAdminOrderSide')
            && $this->registerHook('displayBackOfficeHeader')

            // zakładki w menu "Moduły"
            && $this->installTab('AdminRcDpSkuMapper', 'DP SKU Mapper');
    }

    public function uninstall()
    {
        return $this->uninstallTab('AdminRcDpSkuMapper')
            && parent::uninstall();
    }

    /* =========================================================
     * TABS (MENU)
     * ========================================================= */

    private function installTab(string $className, string $title): bool
    {
        $idTab = (int)Tab::getIdFromClassName($className);
        if ($idTab) {
            return true; // już istnieje
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->module = $this->name;

        // ✅ PS 8.x – Moduły (Symfony)
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentModulesSf');
        if ($tab->id_parent <= 0) {
            // fallback (różne instalacje)
            $tab->id_parent = (int)Tab::getIdFromClassName('AdminModulesSf');
        }

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int)$lang['id_lang']] = $title;
        }

        return (bool)$tab->add();
    }

    private function uninstallTab(string $className): bool
    {
        $idTab = (int)Tab::getIdFromClassName($className);
        if (!$idTab) {
            return true;
        }

        $tab = new Tab($idTab);
        return (bool)$tab->delete();
    }

    /* =========================================================
     * HOOKS
     * ========================================================= */

    public function hookDisplayBackOfficeHeader()
    {
        // zostawione celowo – miejsce na ewentualne assety JS/CSS
    }

    /**
     * Panel boczny w widoku zamówienia (Symfony /sell/orders/{id}/view)
     * Dodaje przycisk "Eksport DP CSV"
     */
    public function hookDisplayAdminOrderSide($params)
    {
        $id_order = (int)($params['id_order'] ?? 0);
        if ($id_order <= 0) {
            return '';
        }

        /** @var \Symfony\Component\Routing\RouterInterface $router */
        $router = $this->get('router');

        $url = $router->generate('rc_dp_order_export_download', [
            'orderId' => $id_order,
        ]);

        return '
        <div class="card mt-2">
          <div class="card-header">
            Dynamic Product
          </div>
          <div class="card-body">
            <a class="btn btn-outline-primary" href="'.$url.'" target="_blank" rel="noopener">
              Eksport DP CSV
            </a>
          </div>
        </div>';
    }
}
