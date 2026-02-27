<script>
(function () {
  window.dataLayer = window.dataLayer || [];

  function parsePrice(text) {
    if (!text) return null;
    text = String(text).replace(/\s|\u00A0/g, '');
    text = text.replace(/[^\d,\.]/g, '');
    if (text.includes('.') && text.includes(',')) text = text.replace(/\./g, '');
    text = text.replace(',', '.');
    var val = parseFloat(text);
    return isNaN(val) ? null : val;
  }

  function getDisplayedPrice() {
    var el =
      document.querySelector('[itemprop="price"]') ||
      document.querySelector('.current-price span') ||
      document.querySelector('.product-price') ||
      document.querySelector('.current-price');

    if (!el) return null;

    var content = el.getAttribute && el.getAttribute('content');
    if (content) {
      var num = parseFloat(String(content).replace(',', '.'));
      return isNaN(num) ? null : num;
    }

    return parsePrice(el.textContent);
  }

  function getProductName() {
    var h1 = document.querySelector('h1');
    return h1 ? h1.textContent.trim() : document.title;
  }

  function getProductId() {
    try {
      if (window.prestashop && prestashop.product && prestashop.product.id_product) {
        return String(prestashop.product.id_product);
      }
    } catch (e) {}
    return null;
  }

  function getQty() {
    var qtyEl =
      document.querySelector('input[name="qty"]') ||
      document.querySelector('#quantity_wanted') ||
      document.querySelector('input.quantity');

    if (!qtyEl) return 1;
    var q = parseInt(qtyEl.value, 10);
    return isNaN(q) || q < 1 ? 1 : q;
  }

  // lock anty-dubel (PS czasem triggeruje kilka razy)
  var lastAtcTs = 0;

  function sendAddToCart() {
    var now = Date.now();
    if (now - lastAtcTs < 800) return;
    lastAtcTs = now;

    var price = getDisplayedPrice();
    var qty = getQty();
    var name = getProductName();
    var id = getProductId();

    // GA4 best practice: wyczyść ecommerce przed nowym eventem
    window.dataLayer.push({ ecommerce: null });

    window.dataLayer.push({
      event: 'add_to_cart',
      ecommerce: {
        currency: 'PLN',
        value: (price !== null ? price * qty : undefined),
        items: [{
          item_id: id || undefined,
          item_name: name,
          price: price !== null ? price : undefined,
          quantity: qty
        }]
      }
    });
  }

  // Delegacja: łapiemy klik na button "Dodaj do koszyka"
  document.addEventListener('click', function (e) {
    var btn = e.target && (e.target.closest ? e.target.closest('button, a') : null);
    if (!btn) return;

    // typowe selektory PS / motywów
    var isAdd =
      btn.matches('.add-to-cart, [data-button-action="add-to-cart"], button[name="add-to-cart"]') ||
      btn.getAttribute('data-button-action') === 'add-to-cart';

    if (isAdd) {
      sendAddToCart();
    }
  }, true);
})();
</script>
