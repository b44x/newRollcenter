console.log('RC select_item loaded');

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

  function getListName() {
    var h1 = document.querySelector('h1');
    return h1 ? (h1.innerText || '').trim() : '';
  }

  function findCard(el) {
    return el.closest('article.product-miniature, .product-miniature, .ajax_block_product, .product-item, .product-card');
  }

  function extractItem(card) {
    if (!card) return null;

    var nameEl = card.querySelector('.product-title a, .product-title, h2 a, h3 a, a.product-name, .product-name a');
    var name = nameEl ? (nameEl.innerText || '').trim() : '';
    if (!name) return null;

    var priceEl = card.querySelector('.product-price-and-shipping .price, .current-price .price, .price');
    var price = priceEl ? parsePrice(priceEl.innerText) : null;

    var itemId =
      card.getAttribute('data-id-product') ||
      (card.dataset ? (card.dataset.idProduct || card.dataset.id) : null);

    if (!itemId && nameEl && nameEl.getAttribute('href')) {
      var href = nameEl.getAttribute('href');
      var m = href.match(/\/(\d+)[-_]/);
      if (m && m[1]) itemId = m[1];
    }

    return {
      item_name: name,
      item_id: itemId ? String(itemId) : undefined,
      price: price !== null ? price : undefined,
      item_list_name: getListName() || undefined
    };
  }

  document.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!a) return;

    // klik w tytuł / miniaturę produktu
    if (!(a.matches('.product-title a, a.product-thumbnail, a.product-name, .thumbnail a') || a.closest('.product-miniature, article.product-miniature, .ajax_block_product, .product-item'))) {
      return;
    }

    var card = findCard(a);
    var item = extractItem(card);
    if (!item) return;

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
      event: 'select_item',
      ecommerce: {
        currency: getCurrency(),
        item_list_name: item.item_list_name,
        items: [item]
      }
    });
  }, true);
})();
