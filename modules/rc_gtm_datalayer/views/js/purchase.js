/* RC GTM purchase.js - Rollcenter (PrestaShop) */
console.log('RC PURCHASE JS loaded');

(function () {
  window.dataLayer = window.dataLayer || [];


  // === DEBUG ===
  var RC_DEBUG = false; // ustaw na false gdy skończysz testy
  function dlog() {
    if (!RC_DEBUG) return;
    try { console.log.apply(console, arguments); } catch (e) {}
  }
function trySendPurchaseFromBackend() {
  if (!window.rcPurchasePayload || !window.rcPurchasePayload.transaction_id) return false;

  var p = window.rcPurchasePayload;
  var sentKey = '__rc_purchase_sent_' + p.transaction_id;

  try {
    if (sessionStorage.getItem(sentKey) === '1') return true;
  } catch (e) {}

  window.dataLayer.push({ ecommerce: null });
  window.dataLayer.push({
    event: 'purchase',
    ecommerce: {
      transaction_id: String(p.transaction_id),
      currency: p.currency || 'PLN',
      value: p.value,
      tax: p.tax,
      shipping: p.shipping,
      items: Array.isArray(p.items) ? p.items : []
    }
  });

  try { sessionStorage.setItem(sentKey, '1'); } catch (e) {}
  return true;
}

  function parsePrice(text) {
    if (!text) return null;
    text = String(text)
      .replace(/\s|\u00A0/g, '')
      .replace(/[^\d,\.]/g, '');
    if (text.indexOf('.') !== -1 && text.indexOf(',') !== -1) text = text.replace(/\./g, '');
    text = text.replace(',', '.');
    var v = parseFloat(text);
    return isNaN(v) ? null : v;
  }

  function getCurrency() {
    try {
      return (window.prestashop && prestashop.currency && prestashop.currency.iso_code) || 'PLN';
    } catch (e) {
      return 'PLN';
    }
  }

  function getQueryParam(name) {
    try { return new URL(window.location.href).searchParams.get(name); }
    catch (e) { return null; }
  }

  function getOrderReferenceFromDom() {
    // <li id="order-reference-value">Numer zamówienia: CMRVSWBWI</li>
    var el = document.querySelector('#order-reference-value');
    var t = el ? (el.innerText || '') : '';
    var m = t.match(/Numer zamówienia:\s*([A-Z0-9\-]+)/i);
    return m ? m[1] : null;
  }

  function getOrderReferenceFromTextFallback() {
    var main = document.querySelector('main') || document.body;
    var t = main ? (main.innerText || '') : '';
    var m = t.match(/Numer zamówienia:\s*([A-Z0-9\-]+)/i);
    return m ? m[1] : null;
  }

  // --- totals (podsumowanie) ---
  function findSummaryRowValue(labelRegex) {
    // w podsumowaniu na stronie potwierdzenia jest zwykle tabela <table> z wierszami
    var rows = document.querySelectorAll('#order-items table tr, main tr');
    for (var i = 0; i < rows.length; i++) {
      var rowText = (rows[i].innerText || '').trim();
      if (!rowText) continue;
      if (!labelRegex.test(rowText)) continue;

      var tds = rows[i].querySelectorAll('td, th');
      if (tds && tds.length >= 2) {
        return ((tds[tds.length - 1].innerText || '').trim());
      }
      return rowText;
    }
    return null;
  }

  function getTotalsFromDom() {
    var mainEl = document.querySelector('main');
    var mainText = mainEl ? (mainEl.innerText || '') : '';

    var valueText = findSummaryRowValue(/RAZEM\s*\(BRUTTO\)/i) ||
                    findSummaryRowValue(/Razem\s*\(brutto\)/i) ||
                    findSummaryRowValue(/RAZEM/i);

    var value = parsePrice(valueText);

    // tax: preferuj element w tabeli podatków
    var tax = null;
    var taxEl = document.querySelector('tr.sub.taxes .value');
    if (taxEl) tax = parsePrice(taxEl.innerText);

    if (tax === null || tax === undefined) {
      var taxMatch = mainText.match(/Podatek:\s*([0-9\.,]+\s*zł)/i);
      if (taxMatch) tax = parsePrice(taxMatch[1]);
    }

    // shipping
    var shippingText = findSummaryRowValue(/Wysyłka i doręczenie/i) || findSummaryRowValue(/Wysyłka/i);
    var shipping = 0;
    if (shippingText) {
      if (/za darmo/i.test(shippingText)) shipping = 0;
      else {
        var s = parsePrice(shippingText);
        if (s !== null) shipping = s;
      }
    }

    // fallback value z tekstu main
    if (value === null || value === undefined) {
      var m = mainText.match(/RAZEM\s*\(BRUTTO\)\s*([0-9\.,]+\s*zł)/i) ||
              mainText.match(/Razem\s*\(brutto\)\s*([0-9\.,]+\s*zł)/i);
      if (m) value = parsePrice(m[1]);
    }

    return {
      value: value !== null ? value : undefined,
      tax: tax !== null ? tax : undefined,
      shipping: shipping
    };
  }

  // --- items (DIV-based; u Ciebie produkty nie są w tabeli) ---
  function getItemsFromDom() {
    var box = document.querySelector('#order-items');
    if (!box) return null; // DOM jeszcze nie gotowy

    var lines = box.querySelectorAll('.order-confirmation-table .order-line.row');
    if (!lines || !lines.length) return [];

    var items = [];

    for (var i = 0; i < lines.length; i++) {
      var line = lines[i];

      // nazwa produktu: .details > span
      var nameEl = line.querySelector('.details > span');
      var name = nameEl ? (nameEl.innerText || '').trim() : '';
      if (!name) continue;

      // qty + ceny: .qty .col-xs-4 => [unit, qty, total]
      var cols = line.querySelectorAll('.qty .col-xs-4');
      var unitText = cols[0] ? (cols[0].innerText || '').trim() : '';
      var qtyText  = cols[1] ? (cols[1].innerText || '').trim() : '';
      var totalText= cols[2] ? (cols[2].innerText || '').trim() : '';

      var qty = parseInt(qtyText, 10);
      if (isNaN(qty) || qty < 1) qty = 1;

      var unitPrice = parsePrice(unitText);

      // fallback: total/qty
      if ((unitPrice === null || unitPrice === undefined) && qty > 0) {
        var lineTotal = parsePrice(totalText);
        if (lineTotal !== null) unitPrice = lineTotal / qty;
      }

      var item = {
        item_name: name,
        quantity: qty
      };
      if (unitPrice !== null && unitPrice !== undefined) item.price = +unitPrice.toFixed(2);

      items.push(item);

	// item_id: spróbuj wyciągnąć ID z obrazka /xxxx-home_default/
	var img = line.querySelector('img[src*="-home_default/"]');
	if (img && img.getAttribute('src')) {
 	 var src = img.getAttribute('src');
	  var m = src.match(/\/(\d+)-home_default\//);
	  if (m && m[1]) item.item_id = m[1];
	}

    }

    return items;
  }

  function pushPurchase(transaction_id, totals, items) {
    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
      event: 'purchase',
      ecommerce: {
        transaction_id: String(transaction_id),
        currency: getCurrency(),
        value: totals.value,
        tax: totals.tax,
        shipping: totals.shipping,
        items: Array.isArray(items) ? items : []
      }
    });
  }

  function trySendPurchase() {
    // transaction_id: preferuj referencję zamówienia z DOM, fallback id_order
    var orderRef = getOrderReferenceFromDom() || getOrderReferenceFromTextFallback();
    var idOrder = getQueryParam('id_order');
    var transaction_id = orderRef || idOrder;
    if (!transaction_id) return false;

    // anti-duplicate per transaction
    var sentKey = '__rc_purchase_sent_' + transaction_id;
    try {
      if (sessionStorage.getItem(sentKey) === '1') {
        dlog('RC purchase: already sent for', transaction_id);
        return true;
      }
    } catch (e) {}

    // items: czekamy aż DOM będzie gotowy
    var items = getItemsFromDom();
    if (items === null) return false; // brak #order-items jeszcze

    var totals = getTotalsFromDom();

    dlog('RC purchase parsed:', { transaction_id: transaction_id, totals: totals, items: items });

    pushPurchase(transaction_id, totals, items);

    try { sessionStorage.setItem(sentKey, '1'); } catch (e) {}
    return true;
  }

function trySendPurchaseFromBackend() {
  if (!window.rcPurchasePayload || !window.rcPurchasePayload.transaction_id) return false;

  var p = window.rcPurchasePayload;
  var sentKey = '__rc_purchase_sent_' + p.transaction_id;

  try { if (sessionStorage.getItem(sentKey) === '1') return true; } catch(e){}

  window.dataLayer.push({ ecommerce: null });
  window.dataLayer.push({
    event: 'purchase',
    ecommerce: {
      transaction_id: String(p.transaction_id),
      currency: p.currency || 'PLN',
      value: p.value,
      tax: p.tax,
      shipping: p.shipping,
      items: Array.isArray(p.items) ? p.items : []
    }
  });

  try { sessionStorage.setItem(sentKey, '1'); } catch(e){}
  return true;
}

  function start() {

	if (trySendPurchaseFromBackend()) return;
	// fallback do DOM parsingu (twoja obecna logika)

    // 1) spróbuj od razu
    if (trySendPurchase()) return;
    
    // 2) polling do 5s – na wypadek, gdyby sekcja produktów dochodziła po JS
    var tries = 0;
    var t = setInterval(function () {
      tries++;
      if (trySendPurchase() || tries > 25) clearInterval(t);
    }, 200);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
