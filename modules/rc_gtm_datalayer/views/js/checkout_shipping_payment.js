console.log('RC checkout shipping/payment loaded');

(function () {
  window.dataLayer = window.dataLayer || [];

  function getCurrency() {
    try { return (window.prestashop && prestashop.currency && prestashop.currency.iso_code) || 'PLN'; }
    catch (e) { return 'PLN'; }
  }

  function pushOnce(key, payload) {
    try {
      if (sessionStorage.getItem(key) === '1') return;
      sessionStorage.setItem(key, '1');
    } catch (e) {}
    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push(payload);
  }

  function getCartItemsFromDataLayer() {
    // bierzemy ostatni begin_checkout / view_cart jako źródło items
    var last = null;
    for (var i = window.dataLayer.length - 1; i >= 0; i--) {
      var x = window.dataLayer[i];
      if (x && (x.event === 'begin_checkout' || x.event === 'view_cart') && x.ecommerce && x.ecommerce.items) {
        last = x;
        break;
      }
    }
    return last && last.ecommerce && last.ecommerce.items ? last.ecommerce.items : [];
  }

  function wireShipping() {
    document.addEventListener('change', function (e) {
      var inp = e.target;
      if (!inp || inp.type !== 'radio') return;
      if (!inp.name || inp.name.indexOf('delivery_option') === -1) return;

      var label = inp.closest('.delivery-option')?.querySelector('.carrier-name, .h6, label') || null;
      var shippingTier = label ? (label.innerText || '').trim() : (inp.value || 'shipping');

      pushOnce('__rc_add_shipping_' + shippingTier, {
        event: 'add_shipping_info',
        ecommerce: {
          currency: getCurrency(),
          shipping_tier: shippingTier,
          items: getCartItemsFromDataLayer()
        }
      });
    }, true);
  }

  function wirePayment() {
    document.addEventListener('change', function (e) {
      var inp = e.target;
      if (!inp || inp.type !== 'radio') return;

      // Prestashop: payment-option id często jako name="payment-option" albo podobnie
      if (!(inp.name && (inp.name.indexOf('payment-option') !== -1 || inp.name === 'payment-option'))) return;

      var label = document.querySelector('label[for="' + inp.id + '"]');
      var paymentType = label ? (label.innerText || '').trim() : (inp.value || 'payment');

      pushOnce('__rc_add_payment_' + paymentType, {
        event: 'add_payment_info',
        ecommerce: {
          currency: getCurrency(),
          payment_type: paymentType,
          items: getCartItemsFromDataLayer()
        }
      });
    }, true);
  }

  function start() {
    wireShipping();
    wirePayment();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
