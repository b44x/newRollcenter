<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class OrderComments extends Module
{
    public function __construct()
    {
        $this->name = 'ordercomments';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'b4x';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Uwagi do zamówienia');
        $this->description = $this->l('');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayCarrierExtraContent')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('displayAdminOrderTabContent')
            || !$this->registerHook('displayAdminOrderTabLink')
            || !$this->registerHook('displayOrderConfirmation')
            || !$this->registerHook('additionalCustomerFormFields')
            || !$this->alterOrdersTable()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->removeFromOrdersTable()) {
            return false;
        }
        return true;
    }

    private function alterOrdersTable()
    {
        return Db::getInstance()->execute('
            ALTER TABLE `'._DB_PREFIX_.'orders` 
            ADD `order_comments` TEXT NULL,
            ADD `courier_notes` TEXT NULL');
    }

    private function removeFromOrdersTable()
    {
        return Db::getInstance()->execute('
            ALTER TABLE `'._DB_PREFIX_.'orders` 
            DROP COLUMN `order_comments`,
            DROP COLUMN `courier_notes`');
    }

    public function hookDisplayCarrierExtraContent($params)
    {
        $this->context->smarty->assign([
            'order_comments' => Tools::getValue('order_comments', ''),
            'courier_notes' => Tools::getValue('courier_notes', '')
        ]);
        return $this->display(__FILE__, 'views/templates/hook/carrier_extra_content.tpl');
    }

    public function hookDisplayAdminOrderTabLink($params)
    {
        $order = new Order($params['id_order']);
        $this->context->smarty->assign([
            'id_order' => $order->id,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/admin_order_tab_link.tpl');
    }

    public function hookDisplayAdminOrderTabContent($params)
    {
        $order = new Order($params['id_order']);
        $comments = Db::getInstance()->getRow('
            SELECT order_comments, courier_notes 
            FROM `'._DB_PREFIX_.'orders` 
            WHERE id_order = '.(int)$order->id);

        $this->context->smarty->assign([
            'order_comments' => isset($comments['order_comments']) ? $comments['order_comments'] : '',
            'courier_notes' => isset($comments['courier_notes']) ? $comments['courier_notes'] : '',
        ]);
        return $this->display(__FILE__, 'views/templates/hook/admin_order_tab_content.tpl');
    }
    
    public function hookDisplayOrderConfirmation($params)
    {
        $order = $params['order'];
        $comments = Db::getInstance()->getRow('
            SELECT order_comments, courier_notes 
            FROM `'._DB_PREFIX_.'orders` 
            WHERE id_order = '.(int)$order->id);

        $this->context->smarty->assign([
            'order_comments' => isset($comments['order_comments']) ? $comments['order_comments'] : '',
            'courier_notes' => isset($comments['courier_notes']) ? $comments['courier_notes'] : '',
        ]);
        return $this->display(__FILE__, 'views/templates/hook/order_confirmation.tpl');
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        $formFields = [];
        
/*
        // Dodaj pole komentarza do zamówienia
        $formFields[] = (new FormField())
            ->setName('order_comments')
            ->setType('textarea')
            ->setLabel($this->l('Uwagi do zamówienia'))
            ->setRequired(false)
            ->setValue(Tools::getValue('order_comments', ''));
        
        // Dodaj pole uwag dla kuriera
        $formFields[] = (new FormField())
            ->setName('courier_notes')
            ->setType('textarea')
            ->setLabel($this->l('Informacje dla kuriera'))
            ->setRequired(false)
            ->setValue(Tools::getValue('courier_notes', ''));
        
        return $formFields;

*/
    }

    public function hookActionValidateOrder($params)
    {
        try {
            $order = $params['order'];
            $cart = $params['cart'];
            
            // Domyślne wartości
            $order_comments = '';
            $courier_notes = '';
            
            // Pobierz wartości z formularza
            $order_comments = Tools::getValue('order_comments', '');
            $courier_notes = Tools::getValue('courier_notes', '');
            
            // Jeśli order_comments jest puste, spróbuj pobrać z różnych źródeł
            if (empty($order_comments)) {
                // 1. Sprawdź sesję
                if ($this->hasSessionValue('order_comments')) {
                    $order_comments = $this->getSessionValue('order_comments');
                } 
                // 2. Sprawdź wiadomość powiązaną z koszykiem (thecheckout)
                else {
                    $cartMessage = $this->getCartMessage($cart->id);
                    if (!empty($cartMessage)) {
                        $order_comments = $cartMessage;
                    }
                }
            }
            
            // Pobierz informacje dla kuriera z sesji, jeśli formularz jest pusty
            if (empty($courier_notes) && $this->hasSessionValue('courier_message')) {
                $courier_notes = $this->getSessionValue('courier_message');
            }
            
            // Zapisz wartości do bazy danych, jeśli którakolwiek z nich jest niepusta
            if ($order && ($order_comments || $courier_notes)) {
                Db::getInstance()->update('orders', [
                    'order_comments' => pSQL($order_comments, true),
                    'courier_notes' => pSQL($courier_notes, true)
                ], 'id_order = ' . (int)$order->id);
                
                // Wyczyść dane z sesji po zapisaniu
                if ($this->hasSessionValue('order_comments')) {
                    $this->removeSessionValue('order_comments');
                }
                if ($this->hasSessionValue('courier_message')) {
                    $this->removeSessionValue('courier_message');
                }
            }
        } catch (Exception $e) {
            // Złap wszystkie wyjątki, aby nie blokować procesu składania zamówienia
            PrestaShopLogger::addLog('OrderComments error: ' . $e->getMessage(), 3);
        }
    }
    
    /**
     * Pobiera wiadomość koszyka (używane przez TheCheckout)
     *
     * @param int $cartId ID koszyka
     * @return string
     */
    private function getCartMessage($cartId)
    {
        $message = '';
        try {
            $messageData = Message::getMessageByCartId((int)$cartId);
            if ($messageData) {
                $message = $messageData['message'];
            }
        } catch (Exception $e) {
            PrestaShopLogger::addLog('OrderComments error getting cart message: ' . $e->getMessage(), 3);
        }
        return $message;
    }

    /**
     * Pobiera wartość z sesji
     *
     * @param string $key Klucz
     * @param mixed $default Wartość domyślna
     * @return mixed
     */
    private function getSessionValue($key, $default = null)
    {
        try {
            if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
                $container = SymfonyContainer::getInstance();
                if (null !== $container && $container->has('session')) {
                    $session = $container->get('session');
                    if ($session->has($key)) {
                        return $session->get($key, $default);
                    }
                }
            }
        } catch (Exception $e) {
            // Fallback to cookie if Symfony container is not available
        }
        
        // Używaj cookie jako fallback
        return isset($this->context->cookie->{$key}) ? $this->context->cookie->{$key} : $default;
    }

    /**
     * Sprawdza czy klucz istnieje w sesji
     *
     * @param string $key Klucz
     * @return bool
     */
    private function hasSessionValue($key)
    {
        try {
            if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
                $container = SymfonyContainer::getInstance();
                if (null !== $container && $container->has('session')) {
                    $session = $container->get('session');
                    return $session->has($key);
                }
            }
        } catch (Exception $e) {
            // Fallback to cookie if Symfony container is not available
        }
        
        // Używaj cookie jako fallback
        return isset($this->context->cookie->{$key});
    }

    /**
     * Usuwa wartość z sesji
     *
     * @param string $key Klucz
     */
    private function removeSessionValue($key)
    {
        try {
            if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
                $container = SymfonyContainer::getInstance();
                if (null !== $container && $container->has('session')) {
                    $session = $container->get('session');
                    $session->remove($key);
                    return true;
                }
            }
        } catch (Exception $e) {
            // Fallback to cookie if Symfony container is not available
        }
        
        // Używaj cookie jako fallback
        unset($this->context->cookie->{$key});
        $this->context->cookie->write();
        return true;
    }
}
