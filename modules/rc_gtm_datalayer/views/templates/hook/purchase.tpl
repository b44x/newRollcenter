<script>
(function () {
  // RC PURCHASE LOADED
  window.dataLayer = window.dataLayer || [];

  // anti-duplicate per transaction
  function getQueryParam(name) {
    var url = new URL(window.location.href);
    return url.searchParams.get(name);
  }

  function getCurrency() {
    try { return (window.prestashop && prestashop.currency && prestashop.currency.iso_code) || 'PLN'; }
    catch(e) { return 'PLN'; }
  }

  function parsePrice(text) {
    if (!text) return null;
    text = String(text).replace(/\s|\u00A0/g, '').replace(/[^\d,\.]/g, '');
    if (text.includes('.') && text.includes(',')) text = text.replace(/\./g, '');
    text = text.replace(',', '.');
    var v = parseFloat(text);
    return isNaN(v) ? null : v;
  }

  function getTransactionIdFromDom() {
    var el =
      document.querySelector('.order-reference') ||
      document.querySelector('#order-reference-value') ||
      document.querySelector('[data-order-reference]');

    if (el) {
      var t = (el.getAttribute('data-order-reference') || el.textContent || '').trim();
      if (t) return t;
    }

    var bodyText = document.body ? document.body.textContent : '';
    var m = bodyText && bodyText.match(/(Nr zamówienia|Numer zamówienia|Order reference|Reference)\s*[:#]?\s*([A-Z0-9\-]+)/i);
    return m ? m[2] : undefined;
  }

  var transaction_id = getQueryParam('id_order') || getTransactionIdFromDom();

  if (!transaction_id) return;

  var sentKey = '__rc_purchase_sent_' + transaction_id;
  try {
    if (sessionStorage.getItem(sentKey) === '1') return;
    sessionStorage.setItem(sentKey, '1');
  } catch(e) {}

  function getTotals() {
    var out = { value: undefined, tax: undefined, shipping: undefined };

    try {
      if (window.prestashop && prestashop.cart && prestashop.cart.totals) {
        var totals = prestashop.cart.totals;

        if (totals.total_including_tax && typeof totals.total_including_tax.amount === 'number') {
          out.value = totals.total_including_tax.amount;
        } else if (totals.total && typeof totals.total.amount === 'number') {
          out.value = totals.total.amount;
        }

        if (totals.shipping && typeof totals.shipping.amount === 'number') {
          out.shipping = totals.shipping.amount;
        }

        if (totals.tax && typeof totals.tax.amount === 'number') {
          out.tax = totals.tax.amount;
        }
      }
    } catch(e) {}

    if (out.value === undefined) {
      var totalEl =
        document.querySelector('.order-confirmation-table .total-value') ||
        document.querySelector('.order-confirmation .total-value') ||
        document.querySelector('.order-summary .total-value') ||
        document.querySelector('.cart-summary-totals .value');
      var v = totalEl ? parsePrice(totalEl.textContent) : null;
      if (v !== null) out.value = v;
    }

    if (out.shipping === undefined) {
      var shipEl =
        document.querySelector('.order-confirmation-table .shipping-value') ||
        document.querySelector('.order-summary .shipping .value') ||
        document.querySelector('.cart-summary-line.shipping .value');
      var s = shipEl ? parsePrice(shipEl.textContent) : null;
      if (s !== null) out.shipping = s;
    }

    return out;
  }

  function getItems() {
    var items = [];

    try {
      var products = (window.prestashop && prestashop.cart && prestashop.cart.products) ? prestashop.cart.products : null;
      if (products && products.length) {
        products.forEach(function(p) {
          items.push({
            item_id: p.id_product ? String(p.id_product) : undefined,
            item_name: p.name,
            price: (typeof p.price_amount === 'number') ? p.price_amount : undefined,
            quantity: (typeof p.quantity === 'number') ? p.quantity : undefined
          });
        });
        return items;
      }
    } catch(e) {}

    var rows = document.querySelectorAll('.order-confirmation-table table tbody tr');
    rows.forEach(function(row) {
      var nameEl = row.querySelector('.product-name, .details, td:nth-child(1)');
      var qtyEl  = row.querySelector('.product-quantity, .qty, td:nth-child(2)');
      var priceEl= row.querySelector('.product-price, .price, td:nth-child(3)');

      var name = nameEl ? nameEl.textContent.trim() : null;
      var qty = qtyEl ? parseInt(qtyEl.textContent, 10) : 1;
      if (isNaN(qty) || qty < 1) qty = 1;

      var price = priceEl ? parsePrice(priceEl.textContent) : null;

      if (name) {
        items.push({
          item_name: name,
          price: price !== null ? price : undefined,
          quantity: qty
        });
      }
    });

    return items;
  }

  var totals = getTotals();
  var items = getItems();

  window.dataLayer.push({ ecommerce: null });
  window.dataLayer.push({
    event: 'purchase',
    ecommerce: {
      transaction_id: String(transaction_id),
      currency: getCurrency(),
      value: totals.value,
      tax: totals.tax,
      shipping: totals.shipping,
      items: items
    }
  });
})();
</script>
