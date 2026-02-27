<script>
(function () {
  window.dataLayer = window.dataLayer || [];
  if (window.__rc_begin_checkout_sent) return;
  window.__rc_begin_checkout_sent = true;

  function getCurrency() {
    try { return (window.prestashop && prestashop.currency && prestashop.currency.iso_code) || 'PLN'; }
    catch(e) { return 'PLN'; }
  }

  function getValue() {
    try {
      if (window.prestashop && prestashop.cart && prestashop.cart.totals && prestashop.cart.totals.total_including_tax) {
        var a = prestashop.cart.totals.total_including_tax.amount;
        if (typeof a === 'number') return a;
      }
    } catch (e) {}
    return undefined;
  }

  // na checkout często prestashop.cart już ma produkty
  var items = [];
  try {
    var products = (window.prestashop && prestashop.cart && prestashop.cart.products) ? prestashop.cart.products : [];
    products.forEach(function(p) {
      items.push({
        item_name: p.name,
        item_id: p.id_product ? String(p.id_product) : undefined,
        price: (typeof p.price_amount === 'number') ? p.price_amount : undefined,
        quantity: (typeof p.quantity === 'number') ? p.quantity : undefined
      });
    });
  } catch(e) {}

  window.dataLayer.push({ ecommerce: null });
  window.dataLayer.push({
    event: 'begin_checkout',
    ecommerce: {
      currency: getCurrency(),
      value: getValue(),
      items: items.length ? items : undefined
    }
  });
})();
</script>
