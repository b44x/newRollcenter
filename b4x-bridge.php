<?php
require_once(dirname(__FILE__).'/config/config.inc.php');
Configuration::set('PS_SHOP_ENABLE', 1);
require_once(dirname(__FILE__).'/init.php');

if (!isset($_GET['token']) || $_GET['token'] != '1j2io3e12meo1o21mo') {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Brak dostępu']);
    exit;
}
header('Content-Type: application/json');

$action = Tools::getValue('action', '');

switch ($action) {
    case 'get_order_data':
        getOrderData();
        break;
    case 'get_orders_list':
        getOrdersList();
        break;
    case 'update_order_status':
        updateOrderStatus();
        break;
    case 'update_external_order_status':
        updateExternalOrderStatus();
        break;
    default:
        echo json_encode(['error' => 'Nieznana akcja']);
        break;
}

function getOrderData() {
    $id_order = (int)Tools::getValue('id_order');
    
    if (!$id_order) {
        echo json_encode(['error' => 'Brak ID zamówienia']);
        return;
    }
    
    $order = new Order($id_order);
    if (!Validate::isLoadedObject($order)) {
        echo json_encode(['error' => 'Zamówienie nie istnieje']);
        return;
    }

    $comments = Db::getInstance()->getRow('
        SELECT order_comments, courier_notes 
        FROM `'._DB_PREFIX_.'orders` 
        WHERE id_order = '.(int)$id_order
    );
    $currency = new Currency($order->id_currency);
    $customer = new Customer($order->id_customer);
    $address_delivery = new Address($order->id_address_delivery);
    $address_invoice = new Address($order->id_address_invoice);
    $carrier = new Carrier($order->id_carrier);
    // Pobierz status zamówienia
    $order_state = new OrderState($order->current_state, Context::getContext()->language->id);
    $status_name = $order_state->name;
    
    // Sprawdź czy klient jest firmą
    $is_company = !empty($address_invoice->company) || !empty($address_invoice->vat_number);
    
    // Sprawdź czy zamówienie wymaga faktury
    $invoice_required = (bool)$order->invoice_number || $is_company;
    
    $order_details = $order->getOrderDetailList();
    $products_data = [];
    foreach ($order_details as $detail) {
        $product_data = [
            'product_id' => $detail['product_id'],
            'product_name' => $detail['product_name'],
            'product_quantity' => $detail['product_quantity'],
            'product_price' => Tools::ps_round($detail['product_price'], 2),
            'unit_price_tax_incl' => Tools::ps_round($detail['unit_price_tax_incl'], 2),
            'product_reference' => $detail['product_reference'],
            'total_price_tax_excl' => Tools::ps_round($detail['total_price_tax_excl'], 2),
            'total_price_tax_incl' => Tools::ps_round($detail['total_price_tax_incl'], 2),
            'customization' => null
        ];
        
        $id_customization = $detail['id_customization'];
        
        if ($id_customization) {
            try {
                $dynamic_input = DynamicProduct\classes\models\DynamicInput::getInputByCustomization($id_customization);
                
                if (Validate::isLoadedObject($dynamic_input)) {
                    $summary = $dynamic_input->getSummaryArrayWithId();
                    
                    // Przekształć tablicę na format klucz-wartość

                    $customization_data = [];
                    foreach ($summary as $item) {
                        if (isset($item['label']) && isset($item['value'])) {
                            $customization_data[] = [
				'id' => $item['id'],
                                'key' => $item['label'],
                                'value' => $item['value'],
				'originalValue' => $item['originalValue'],
				'name' => $item['name'],
                            ];


if (!empty($item['name']) && 'wybor_systemu_rolet' === $item['name']) {
    $system_map = [
        1 => 'mini',
        2 => 'vegas',
        3 => 'profil',
        4 => 'din_mini',
        5 => 'vegas_din',
        6 => 'profil_din'
    ];
    
    if (!empty($item['originalValue'])) {
        $original_value = (int)$item['originalValue'];
        
        if (isset($system_map[$original_value])) {
            $product_data['product_reference'] = $system_map[$original_value];
        }
    }
}

                        }
                    }

                    
                    $product_data['customization'] = $customization_data;
                }
            } catch (Exception $e) {
                $product_data['customization_error'] = $e->getMessage();
            }
        }
        
        $products_data[] = $product_data;
    }
    
    $country_delivery = new Country($address_delivery->id_country);
    $country_invoice = new Country($address_invoice->id_country);
    
    // Sprawdź NIP
    $vat_number = !empty($address_invoice->vat_number) ? $address_invoice->vat_number : '';
    
    // Pobierz informacje o zniżkach
    $discounts = [];
    $total_discounts = 0;
    $discount_description = '';
    
    if ($order->total_discounts > 0) {
        // Pobierz wartość rabatu
        $total_discounts = $order->total_discounts;
        
        // Spróbuj pobrać informacje o zastosowanych kodach rabatowych
        $cart_rules = $order->getCartRules();
        if (!empty($cart_rules)) {
            $discount_names = [];
            foreach ($cart_rules as $rule) {
                $discount_names[] = $rule['name'];
            }
            $discount_description = implode(', ', $discount_names);
        } else {
            $discount_description = 'Rabat';
        }
    }
    
    // Pobierz informacje o płatności
    $transaction_id = '';
    $payment_method = $order->payment;
    
    // Pobierz informacje o transakcji płatności
    try {
        $order_payments = OrderPayment::getByOrderReference($order->reference);
        if (!empty($order_payments)) {
            foreach ($order_payments as $payment) {
                if (!empty($payment->transaction_id)) {
                    $transaction_id = $payment->transaction_id;
                    break;
                }
            }
        }
    } catch (Exception $e) {
        // Ignoruj błędy - transakcja_id pozostanie pusta
    }
    
    $result = [
        'order' => [
            'id' => $order->id,
            'reference' => $order->reference,
            'date_add' => $order->date_add,
            'total_paid' => $order->total_paid,
            'total_paid_tax_excl' => $order->total_paid_tax_excl,
            'total_paid_tax_incl' => $order->total_paid_tax_incl,
            'total_shipping' => $order->total_shipping,
            'total_products' => $order->total_products,
            'total_discounts' => $total_discounts,
            'discount_description' => $discount_description,
            'carrier_name' => $carrier->name,
            'payment' => $payment_method,
            'transaction_id' => $transaction_id,
            'invoice_number' => $order->invoice_number,
            'delivery_number' => $order->delivery_number,
            'currency' => $currency->iso_code,
            'status' => $status_name,
            'is_company' => $is_company,
            'invoice_required' => $invoice_required
        ],
        'customer' => [
            'id' => $customer->id,
            'email' => $customer->email,
            'firstname' => $customer->firstname,
            'lastname' => $customer->lastname,
            'company' => $customer->company,
            'is_company' => $is_company
        ],
        'delivery_address' => [
            'firstname' => $address_delivery->firstname,
            'lastname' => $address_delivery->lastname,
            'company' => $address_delivery->company,
            'address1' => $address_delivery->address1,
            'address2' => $address_delivery->address2,
            'postcode' => $address_delivery->postcode,
            'city' => $address_delivery->city,
            'country' => $country_delivery->name,
            'phone' => $address_delivery->phone,
            'phone_mobile' => $address_delivery->phone_mobile
        ],
        'invoice_address' => [
            'firstname' => $address_invoice->firstname,
            'lastname' => $address_invoice->lastname,
            'company' => $address_invoice->company,
            'vat_number' => $vat_number,
            'address1' => $address_invoice->address1,
            'address2' => $address_invoice->address2,
            'postcode' => $address_invoice->postcode,
            'city' => $address_invoice->city,
            'country' => $country_invoice->name,
            'phone' => $address_invoice->phone,
            'phone_mobile' => $address_invoice->phone_mobile
        ],
        'products' => $products_data,
	'comments' => $comments,
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT);
}

function getOrdersList() {
    // Pobierz parametry filtrowania (opcjonalnie)

    $limit = (int)Tools::getValue('limit', 400); // Domyślnie ograniczenie do 100 zamówień
    $days = (int)Tools::getValue('days', 5); // Domyślnie zamówienia z ostatnich 30 dni
    $date_from = Tools::getValue('date_from'); // Zakres dat - data od
    $date_to = Tools::getValue('date_to'); // Zakres dat - data do

    $order_id = Tools::getValue('order_id', null);

    // Chroń przed zbyt dużymi wartościami
    $limit = min($limit, 500); 
    $days = min($days, 365);


if (!$order_id) {
    if (!empty($date_from) && !empty($date_to)) {
        $date_from_formatted = $date_from . ' 00:00:00';
        $date_to_formatted = $date_to . ' 23:59:59';
    } else {
        $date_from_formatted = date('Y-m-d H:i:s', strtotime("-$days days"));
        $date_to_formatted = date('Y-m-d H:i:s');
    }
    
    // Oblicz datę początkową
//    $date_from = date('Y-m-d H:i:s', strtotime("-$days days"));
    

    // Pobierz listę zamówień
//    $orders = Order::getOrdersIdByDate($date_from, date('Y-m-d H:i:s'));
      $orders = Order::getOrdersIdByDate($date_from_formatted, $date_to_formatted);

  } else {

$orders = []; $orders[] = (int) $order_id;

}
    // Ogranicz do wymaganej liczby
    $orders = array_slice($orders, 0, $limit);
    
    $orders_list = [];
    
    foreach ($orders as $id_order) {
        $order = new Order($id_order);
        
        if (!Validate::isLoadedObject($order)) {
            continue;
        }
        
        // Pobierz podstawowe dane o zamówieniu
        $customer = new Customer($order->id_customer);
        $order_state = new OrderState($order->current_state, Context::getContext()->language->id);
        $carrier = new Carrier($order->id_carrier);
        
        // Pobierz informacje o rabatach
        $discount_description = '';
        if ($order->total_discounts > 0) {
            $cart_rules = $order->getCartRules();
            if (!empty($cart_rules)) {
                $discount_names = [];
                foreach ($cart_rules as $rule) {
                    $discount_names[] = $rule['name'];
                }
                $discount_description = implode(', ', $discount_names);
            }
        }
        
        // Pobierz ID transakcji płatności
        $transaction_id = '';
        try {
            $order_payments = OrderPayment::getByOrderReference($order->reference);
            if (!empty($order_payments)) {
                foreach ($order_payments as $payment) {
                    if (!empty($payment->transaction_id)) {
                        $transaction_id = $payment->transaction_id;
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            // Ignoruj błędy
        }
        
        // Stwórz skrócony obiekt danych zamówienia
        $order_data = [
            'id' => $order->id,
            'reference' => $order->reference,
            'date_add' => $order->date_add,
            'total_paid' => (float)$order->total_paid,
            'total_shipping' => (float)$order->total_shipping,
            'total_products' => (float)$order->total_products,
            'total_discounts' => (float)$order->total_discounts,
            'discount_description' => $discount_description,
            'payment' => $order->payment,
            'transaction_id' => $transaction_id,
            'carrier_name' => $carrier->name,
            'current_state' => $order_state->name,
            'customer_name' => $customer->firstname . ' ' . $customer->lastname,
            'invoice_number' => $order->invoice_number
        ];
        
        $orders_list[] = $order_data;
    }
    
    // Sortuj zamówienia od najnowszych
    usort($orders_list, function($a, $b) {
        return strtotime($b['date_add']) - strtotime($a['date_add']);
    });
    
    $result = [
        'total_orders' => count($orders_list),
        'orders' => $orders_list
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT);
}

function updateOrderStatus() {
    $id_order = (int)Tools::getValue('id_order');
    $status_name = Tools::getValue('status', '');
    $id_status = (int)Tools::getValue('id_status', 0);
    $send_email = (bool)Tools::getValue('send_email', true); // Domyślnie wysyłaj email
    
    if (!$id_order) {
        echo json_encode(['error' => 'Brak ID zamówienia']);
        return;
    }
    
    // Sprawdź czy mamy nazwę statusu lub ID statusu
    if (empty($status_name) && !$id_status) {
        echo json_encode(['error' => 'Brak nazwy statusu lub ID statusu']);
        return;
    }
    
    $order = new Order($id_order);
    
    if (!Validate::isLoadedObject($order)) {
        echo json_encode(['error' => 'Zamówienie nie istnieje']);
        return;
    }
    
    // Pobierz wszystkie statusy
    $language_id = (int)Configuration::get('PS_LANG_DEFAULT');
    $all_statuses = OrderState::getOrderStates($language_id);
    
    // Znajdź ID statusu na podstawie nazwy lub użyj bezpośrednio podanego ID
    $id_order_state = null;
    
    if (!empty($status_name)) {
        // Szukanie po nazwie
        foreach ($all_statuses as $state) {
            if (trim($state['name']) == trim($status_name)) {
                $id_order_state = (int)$state['id_order_state'];
                break;
            }
        }
    } else if ($id_status > 0) {
        // Sprawdź czy podane ID statusu istnieje
        foreach ($all_statuses as $state) {
            if ((int)$state['id_order_state'] === $id_status) {
                $id_order_state = $id_status;
                $status_name = $state['name']; // Przypisz nazwę dla dalszej referencji
                break;
            }
        }
    }
    
    if (!$id_order_state) {
        echo json_encode([
            'error' => 'Nie znaleziono statusu' . (!empty($status_name) ? ' o nazwie: ' . $status_name : ' o ID: ' . $id_status),
            'available_statuses' => array_map(function($state) {
                return ['id' => (int)$state['id_order_state'], 'name' => $state['name']];
            }, $all_statuses)
        ]);
        return;
    }
    
    // Sprawdź, czy status jest już ustawiony
    if ($order->current_state == $id_order_state) {
        echo json_encode([
            'success' => true,
            'message' => 'Zamówienie ma już ten status',
            'order_id' => $id_order,
            'status_id' => $id_order_state,
            'status_name' => $status_name
        ]);
        return;
    }
    
    // Inicjalizacja kontekstu
    $employee = new Employee(1); // Pierwszy pracownik jako domyślny
    Context::getContext()->employee = $employee;
    
    // Aktualizacja statusu zamówienia
    $order_history = new OrderHistory();
    $order_history->id_order = (int)$order->id;
    $order_history->id_employee = (int)$employee->id;
    $order_history->id_order_state = (int)$id_order_state;
    $order_history->changeIdOrderState((int)$id_order_state, $order->id); // Aktualizuje status
    
    // Sprawdź czy nowy status generuje fakturę
    $order_state = new OrderState($id_order_state);
    if ($order_state->invoice && !$order->invoice_number) {
        $order->setInvoice(true);
    }
    
    // Dodaj historię zamówienia i wyślij powiadomienie, jeśli potrzeba
    $res = $order_history->addWithemail($send_email);
    
    // Pobierz zaktualizowany status
    $updated_order = new Order($id_order);
    $updated_state = new OrderState($updated_order->current_state, $language_id);
    
    if ($res) {
        // Jeśli aktualizacja powiodła się, wyślij odpowiedź z sukcesem
        echo json_encode([
            'success' => true,
            'message' => 'Status zamówienia zaktualizowany',
            'order_id' => $id_order,
            'status_id' => $id_order_state,
            'status_name' => $updated_state->name,
            'invoice_number' => $updated_order->invoice_number,
            'email_sent' => $send_email
        ]);
    } else {
        // W przypadku błędu
        echo json_encode([
            'error' => 'Nie udało się zaktualizować statusu zamówienia',
            'order_id' => $id_order
        ]);
    }
}

/**
 * Obsługa aktualizacji statusu zamówienia z zewnętrznego systemu "mojaroleta"
 */
function updateExternalOrderStatus() {
    $external_reference = Tools::getValue('reference');
    $status_id = (int)Tools::getValue('status');
    $user_id = (int)Tools::getValue('user_id', 1); // Domyślny ID użytkownika
    
    if (!$external_reference) {
        echo json_encode(['error' => 'Brak referencji zamówienia zewnętrznego']);
        return;
    }
    
    if (!$status_id) {
        echo json_encode(['error' => 'Brak ID statusu']);
        return;
    }
    
    // Sprawdź, czy to jest prawidłowa referencja - format: mojaroleta_[id]_[typ]
    if (!preg_match('/^mojaroleta_(\d+)_(.+)$/', $external_reference, $matches)) {
        echo json_encode(['error' => 'Nieprawidłowy format referencji zewnętrznej']);
        return;
    }
    
    $id_order = (int)$matches[1];
    $order_type = $matches[2];
    
    $order = new Order($id_order);
    
    if (!Validate::isLoadedObject($order)) {
        echo json_encode(['error' => 'Zamówienie nie istnieje']);
        return;
    }
    
    // Mapowanie statusów z zewnętrznego systemu do PrestaShop
    // (ID statusu z "mojaroleta" => nazwa statusu w PrestaShop)
    $status_mapping = [
        1 => '01 - Przyjęte do realizacji', // przyjęte do realizacji
        2 => '02 - Na produkcji',          // na produkcji
        3 => 'REKLAMACJA',                 // reklamacja
        5 => '03 - Gotowe do wysyłki',     // gotowe do wysyłki
        6 => '06 - Oczekuje na wpłatę',    // oczekuje na wpłatę
        10 => '04- Zakończone',            // zakończone
        11 => '05 - Anulowane'             // anulowane
    ];
    
    // Sprawdź, czy status istnieje w mapowaniu
    if (!isset($status_mapping[$status_id])) {
        echo json_encode([
            'error' => 'Nieznany ID statusu: ' . $status_id,
            'available_statuses' => array_keys($status_mapping)
        ]);
        return;
    }
    
    // Pobierz nazwę statusu
    $status_name = $status_mapping[$status_id];
    
    // Pobierz wszystkie statusy PrestaShop
    $language_id = (int)Configuration::get('PS_LANG_DEFAULT');
    $all_statuses = OrderState::getOrderStates($language_id);
    
    // Znajdź ID statusu w PrestaShop na podstawie jego nazwy
    $id_order_state = null;
    foreach ($all_statuses as $state) {
        if (trim($state['name']) == trim($status_name)) {
            $id_order_state = (int)$state['id_order_state'];
            break;
        }
    }
    
    if (!$id_order_state) {
        echo json_encode([
            'error' => 'Nie znaleziono w PrestaShop statusu o nazwie: ' . $status_name,
            'available_statuses' => array_column($all_statuses, 'name')
        ]);
        return;
    }
    
    // Pobierz stary ID statusu dla zapisu w logach
    $old_status_id = $order->current_state;
    
    // Sprawdź, czy status jest już ustawiony
    if ($order->current_state == $id_order_state) {
        echo json_encode([
            'success' => true,
            'message' => 'Zamówienie ma już ten status',
            'order_id' => $id_order,
            'status_id' => $id_order_state,
            'status_name' => $status_name,
            'external_reference' => $external_reference
        ]);
        return;
    }
    
    // Inicjalizacja kontekstu
    $employee = new Employee($user_id);
    if (!Validate::isLoadedObject($employee)) {
        $employee = new Employee(1); // Domyślny pracownik jeśli podany nie istnieje
    }
    Context::getContext()->employee = $employee;
    
    // Aktualizacja statusu zamówienia
    $order_history = new OrderHistory();
    $order_history->id_order = (int)$order->id;
    $order_history->id_employee = (int)$employee->id;
    $order_history->id_order_state = (int)$id_order_state;
    
    // Sprawdź czy nowy status generuje fakturę
    $order_state = new OrderState($id_order_state);
    if ($order_state->invoice && !$order->invoice_number) {
        $order->setInvoice(true);
    }
    
    // Aktualizuj zamówienie
    $res = $order_history->add();
    
    // Pobierz zaktualizowany status
    $updated_order = new Order($id_order);
    $updated_state = new OrderState($updated_order->current_state, $language_id);
    
    if ($res) {
        // Dodaj zapis o zmianie statusu (podobnie jak w hookStatusChange)
        // Jest to tylko symulacja - rzeczywisty zapis będzie w zewnętrznym systemie
        $device_name = Tools::getValue('device_name', null);
        $child_id = Tools::getValue('child_id', null);
        
        // Tutaj możesz zapisać zmiany w logach lub wywołać API zewnętrzne
        // ...
        
        // Jeśli aktualizacja powiodła się, wyślij odpowiedź z sukcesem
        echo json_encode([
            'success' => true,
            'message' => 'Status zamówienia zaktualizowany',
            'order_id' => $id_order,
            'old_status_id' => $old_status_id,
            'new_status_id' => $id_order_state,
            'status_name' => $updated_state->name,
            'external_reference' => $external_reference,
            'invoice_number' => $updated_order->invoice_number
        ]);
    } else {
        // W przypadku błędu
        echo json_encode([
            'error' => 'Nie udało się zaktualizować statusu zamówienia',
            'order_id' => $id_order,
            'external_reference' => $external_reference
        ]);
    }
}
