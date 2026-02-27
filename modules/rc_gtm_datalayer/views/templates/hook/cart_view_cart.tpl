<script>
(function () {
  window.dataLayer = window.dataLayer || [];
  if (window.__rc_view_cart_sent) return;
  window.__rc_view_cart_sent = true;

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

  function getCartItemsFromDOM() {
    // motywy różnie: próbujemy zebrać linie koszyka
	var root = document.querySelector('#main .cart-overview') || document.querySelector('#main') || document;
	var rows = root.querySelectorAll('.cart-item, .product-line-grid, .cart-overview .product-line-grid');
    var items = [];

    rows.forEach(function(row) {
      // nazwa
      var nameEl = row.querySelector('.product-line-info a, .product-name a, .product-name, .label');
      var name = nameEl ? nameEl.textContent.trim() : null;

      // ilość
      var qtyEl = row.querySelector('input[name*="quantity"], input.js-cart-line-product-quantity, input.qty');
      var qty = qtyEl ? parseInt(qtyEl.value, 10) : 1;
      if (isNaN(qty) || qty < 1) qty = 1;

      // cena jednostkowa (jeśli brak, bierzemy line total / qty)
      var unitEl = row.querySelector('.product-price, .current-price, .price');
      var unit = unitEl ? parsePrice(unitEl.textContent) : null;

      var totalEl = row.querySelector('.product-line-price, .line-total, .product-total, .value');
      var lineTotal = totalEl ? parsePrice(totalEl.textContent) : null;

      if (unit === null && lineTotal !== null && qty > 0) unit = lineTotal / qty;

 if (name) {
    items.push({
      item_name: name,
      price: unit !== null ? unit : undefined,
      quantity: qty
    });
  }
});

	// DEDUPE
	var seen = {};
	var unique = [];
	items.forEach(function(it) {
	var key = (it.item_id ? it.item_id : it.item_name) + '|' + (it.price ?? '') + '|' + (it.quantity ?? '');
	if (!seen[key]) {
		seen[key] = true;
		unique.push(it);
	}
	});
	return unique;
  }

  function getCartValue() {
    // próbuj z prestashop.cart.totals.total_including_tax.amount
    try {
      if (window.prestashop && prestashop.cart && prestashop.cart.totals && prestashop.cart.totals.total_including_tax) {
        var a = prestashop.cart.totals.total_including_tax.amount;
        if (typeof a === 'number') return a;
      }
    } catch (e) {}

    // fallback z DOM: "Razem"
    var totalEl = document.querySelector('.cart-summary-totals .value, .cart-total .value, .cart-summary .cart-total .value, .cart-summary .cart-summary-line.cart-total .value');
    return totalEl ? parsePrice(totalEl.textContent) : undefined;
  }

  var items = getCartItemsFromDOM();
  var value = getCartValue();

  window.dataLayer.push({ ecommerce: null });
  window.dataLayer.push({
    event: 'view_cart',
    ecommerce: {
      currency: getCurrency(),
      value: value,
      items: items
    }
  });
})();
</script>
