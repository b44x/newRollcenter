<?php
if (!defined('_PS_VERSION_')) { exit; }

class AdminRcDpOrderExportController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }

    public function postProcess()
    {
        $id_order = (int)Tools::getValue('id_order');
        if ($id_order <= 0) {
            die('Missing id_order');
        }

        // (Opcjonalnie) kontrola uprawnień
        if (!$this->context->employee || !$this->context->employee->isLoggedBack()) {
            die('Unauthorized');
        }

        $sql = '
            SELECT
                od.id_order                         AS id_order,
                od.id_order_detail                  AS id_order_detail,
                od.id_customization                 AS id_input,
                od.product_id                       AS product_id,
                od.product_reference                AS product_reference,

                dif.id_field                        AS id_field,
                dif.type                            AS field_type,
                dif.name                            AS field_name,
                dif.secondary_value                 AS value,

                dif.value                           AS value_raw,
                dif.secondary_value                 AS value_secondary,
                dif.options                         AS selected_options_json,

                dto.sku                             AS ref
            FROM '._DB_PREFIX_.'order_detail od
            JOIN '._DB_PREFIX_.'dynamicproduct_input_field dif
                ON dif.id_input = od.id_customization
            LEFT JOIN '._DB_PREFIX_.'dynamicproduct_thumbnails_option dto
                ON dto.id_thumbnails_option = CAST(
                    REPLACE(REPLACE(dif.options, \'[\', \'\'), \']\', \'\')
                    AS UNSIGNED
                )
                AND dif.type = 12
            WHERE od.id_order = '.(int)$id_order.'
              AND dif.visible = 1
              AND dif.id_field <> 0
              AND dif.name NOT IN (\'quantity\',\'product_price\',\'product_weight\',\'preview\',\'changed\')
              AND dif.name NOT LIKE \'cena\_%\'
            ORDER BY od.id_order_detail, dif.position
        ';

        $rows = Db::getInstance()->executeS($sql);

        // Nagłówki CSV
        $filename = 'dp-order-'.$id_order.'.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // BOM pod Excela (zostawiamy mimo separatora ",")
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        $delimiter = ','; // zgodnie z Twoim życzeniem

        fputcsv($out, [
            'id_order','id_order_detail','id_input','product_id','product_reference',
            'id_field','field_type','field_name',
            'value','value_raw','value_secondary','selected_options_json','ref'
        ], $delimiter);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id_order'],
                $r['id_order_detail'],
                $r['id_input'],
                $r['product_id'],
                $r['product_reference'],
                $r['id_field'],
                $r['field_type'],
                $r['field_name'],
                $r['value'],
                $r['value_raw'],
                $r['value_secondary'],
                $r['selected_options_json'],
                $r['ref'],
            ], $delimiter);
        }

        fclose($out);
        exit;
    }
}
