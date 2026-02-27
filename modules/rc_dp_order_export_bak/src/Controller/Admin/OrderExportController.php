<?php

namespace RcDpOrderExport\Controller\Admin;

use Db;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class OrderExportController extends FrameworkBundleAdminController
{
    public function download(int $orderId): Response
    {
        // uprawnienia do odczytu zamówień
        $this->denyAccessUnlessGranted('read', 'AdminOrders');

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
            WHERE od.id_order = '.(int)$orderId.'
              AND dif.visible = 1
              AND dif.id_field <> 0
              AND dif.name NOT IN (\'quantity\',\'product_price\',\'product_weight\',\'preview\',\'changed\')
              AND dif.name NOT LIKE \'cena\_%\'
            ORDER BY od.id_order_detail, dif.position
        ';

        $rows = Db::getInstance()->executeS($sql);

        $handle = fopen('php://temp', 'r+');

        // BOM żeby Excel poprawnie czytał UTF-8
        fwrite($handle, "\xEF\xBB\xBF");

        $delimiter = ',';

        fputcsv($handle, [
            'id_order','id_order_detail','id_input','product_id','product_reference',
            'id_field','field_type','field_name',
            'value','value_raw','value_secondary','selected_options_json','ref'
        ], $delimiter);

        foreach ($rows as $r) {
            fputcsv($handle, [
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

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $response = new Response($csv);

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'dp-order-'.$orderId.'.csv'
        );

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
