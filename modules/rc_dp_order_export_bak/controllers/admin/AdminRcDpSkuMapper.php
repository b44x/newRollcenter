<?php
if (!defined('_PS_VERSION_')) { exit; }

class AdminRcDpSkuMapperController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->meta_title = 'DP SKU Mapper (thumbnails)';
    }

    public function postProcess()
    {
        // Zapis pojedynczego wiersza
        if (Tools::isSubmit('saveSkuRow')) {
            $fieldName = trim((string)Tools::getValue('field_name'));
            $sku = trim((string)Tools::getValue('sku'));

            if ($fieldName === '') {
                $this->errors[] = 'Brak field_name.';
                return;
            }

            // Jeśli puste SKU, traktujemy jako czyszczenie (możesz to zmienić)
            // Bezpiecznie: walidacja sku (litery/cyfry/underscore/dash)
            if ($sku !== '' && !preg_match('/^[a-z0-9_\-]+$/i', $sku)) {
                $this->errors[] = 'SKU może zawierać tylko litery/cyfry/_/-';
                return;
            }

            // Aktualizacja: wszystkie opcje thumbnails dla wszystkich id_field o danym name
            $sql = "
                UPDATE "._DB_PREFIX_."dynamicproduct_thumbnails_option o
                SET o.sku = '".pSQL($sku)."'
                WHERE o.deleted = 0
                  AND o.active = 1
                  AND o.id_field IN (
                      SELECT f.id_field
                      FROM "._DB_PREFIX_."dynamicproduct_field f
                      WHERE f.name = '".pSQL($fieldName)."'
                  )
            ";

            Db::getInstance()->execute($sql);

            $this->confirmations[] = 'Zapisano SKU dla field_name: '.$fieldName;
        }
    }

    public function initContent()
    {
        parent::initContent();

        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Lista unikatowych field_name, tylko dla thumbnails (bo bazujemy na thumbnails_option)
        // Dodatkowo podajemy:
        // - przykładowe SKU (MAX) żeby było widać co już ustawione
        // - ile opcji nie ma SKU
        $rows = Db::getInstance()->executeS("
            SELECT
                f.name AS field_name,
                MAX(fl.label) AS field_label,
                MAX(o.sku) AS current_sku,
                COUNT(DISTINCT o.id_field) AS fields_count,
                COUNT(*) AS options_count,
                SUM(o.sku IS NULL OR o.sku = '') AS missing_sku
            FROM "._DB_PREFIX_."dynamicproduct_thumbnails_option o
            JOIN "._DB_PREFIX_."dynamicproduct_field f
                ON f.id_field = o.id_field
            LEFT JOIN "._DB_PREFIX_."dynamicproduct_field_lang fl
                ON fl.id_field = f.id_field AND fl.id_lang = ".(int)$id_lang."
            WHERE o.deleted = 0 AND o.active = 1
            GROUP BY f.name
            ORDER BY f.name
        ");

        $html = '';

        // Komunikaty BO (errors/confirmations)
        if (!empty($this->errors)) {
            $html .= $this->displayError(implode('<br>', $this->errors));
        }
        if (!empty($this->confirmations)) {
            $html .= $this->displayConfirmation(implode('<br>', $this->confirmations));
        }

        $html .= '
        <div class="panel">
          <h3>DP thumbnails → mapowanie field_name → sku (ref)</h3>
          <p>
            Ustawiasz <b>sku</b> na poziomie <b>field_name</b>. Zapis aktualizuje wszystkie rekordy
            w <code>'._DB_PREFIX_.'dynamicproduct_thumbnails_option</code> dla wszystkich <code>id_field</code>,
            które mają ten sam <code>field_name</code>.
          </p>

          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>field_name</th>
                  <th>label (PL)</th>
                  <th>liczba pól</th>
                  <th>opcji</th>
                  <th>brak SKU</th>
                  <th>sku (ref)</th>
                  <th>akcja</th>
                </tr>
              </thead>
              <tbody>
        ';

        foreach ($rows as $r) {
            $fieldName = (string)$r['field_name'];
            $label = (string)$r['field_label'];
            $currentSku = (string)$r['current_sku'];
            $fieldsCount = (int)$r['fields_count'];
            $optionsCount = (int)$r['options_count'];
            $missingSku = (int)$r['missing_sku'];

            $action = self::$currentIndex.'&token='.$this->token;

            $html .= '
              <tr>
                <td><code>'.htmlspecialchars($fieldName).'</code></td>
                <td>'.htmlspecialchars($label).'</td>
                <td>'.$fieldsCount.'</td>
                <td>'.$optionsCount.'</td>
                <td>'.($missingSku > 0 ? '<span class="badge badge-danger">'.$missingSku.'</span>' : '<span class="badge badge-success">0</span>').'</td>
                <td style="min-width:220px;">
                  <form method="post" action="'.$action.'" class="form-inline" style="display:flex; gap:8px; align-items:center;">
                    <input type="hidden" name="field_name" value="'.htmlspecialchars($fieldName).'">
                    <input type="text" name="sku" value="'.htmlspecialchars($currentSku).'" placeholder="np. fabric, blind_type" class="form-control" style="width:200px;">
                </td>
                <td>
                    <button type="submit" name="saveSkuRow" class="btn btn-primary">
                      Zapisz
                    </button>
                  </form>
                </td>
              </tr>
            ';
        }

        $html .= '
              </tbody>
            </table>
          </div>
        </div>
        ';

        $this->context->smarty->assign([
            'content' => $html,
        ]);

        // PS 8: content poleci normalnie
        $this->setTemplate('content.tpl');
    }
}
