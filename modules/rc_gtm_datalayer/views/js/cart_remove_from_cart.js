console.log('RC remove_from_cart loaded');

(function () {
  window.dataLayer = window.dataLayer || [];

  function parsePrice(text) {
    if (!text) return null;
    text = String(text).replace(/\s|\u00A0/g, '').replace(/[^\d,\.]/g, '');
    if (text.includes('.') && text.includes(',')) text = text.replace(/\./g, '');
    text = text.replace(',', '.');
    var v = parseFloat(text);
    return isNaN(v) ? null : v;
  }

  function getCurrency() {
    try { return (window.prestashop && prestashop.currency && prestashop.currency.iso_code) || 'PLN'; }
    catch (e) { return 'PLN'; }
  }

  function findLine(el) {
    return el.closest('.cart-item, .js-cart-line-product, .product-line, .cart-product');
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.remove-from-cart, a.remove-from-cart, .js-remove-from-cart');
    if (!btn) return;

    var line = findLine(btn);
    if (!line) return;

    var nameEl = line.querySelector('.product-name, .label, .product-line-info a, a.product-name');
    var name = nameEl ? (nameEl.innerText || '').trim() : '';
    if (!name) name = 'Produkt';

    var qtyEl = line.querySelector('input.js-cart-line-product-quantity, input[name*="qty"], .qty input');
    var qty = qtyEl ? parseInt(qtyEl.value, 10) : 1;
    if (isNaN(qty) || qty < 1) qty = 1;

    var priceEl = line.querySelector('.product-price, .current-price, .price');
    var price = priceEl ? parsePrice(priceEl.innerText) : null;

    var itemId = line.getAttribute('data-id-product') || line.dataset.idProduct || null;

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
      event: 'remove_from_cart',
      ecommerce: {
        currency: getCurrency(),
        items: [{
          item_name: name,
          item_id: itemId ? String(itemId) : undefined,
          price: price !== null ? price : undefined,
          quantity: qty
        }]
      }
    });
  }, true);
})();
