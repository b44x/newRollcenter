console.log('RC view_item_list loaded');

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

  function getProductCards() {
    // Leo/PS potrafi mieć różne klasy
    var sels = [
      'article.product-miniature',
      '.product-miniature',
      '.ajax_block_product',
      '.product-item',
      '.product-card'
    ];
    for (var i = 0; i < sels.length; i++) {
      var n = document.querySelectorAll(sels[i]);
      if (n && n.length) return Array.from(n);
    }
    return [];
  }

  function extractItemFromCard(card, index, listName) {
    // name
    var nameEl = card.querySelector('.product-title a, .product-title, h2 a, h3 a, a.product-name, .product-name a');
    var name = nameEl ? (nameEl.innerText || '').trim() : '';
    if (!name) return null;

    // price
    var priceEl = card.querySelector('.product-price-and-shipping .price, .current-price .price, .price');
    var price = priceEl ? parsePrice(priceEl.innerText) : null;

    // item_id (PS często trzyma)
    var itemId =
      card.getAttribute('data-id-product') ||
      (card.dataset ? (card.dataset.idProduct || card.dataset.id) : null);

    // fallback item_id z linku (np. /123-nazwa.htm)
    if (!itemId) {
      var link = card.querySelector('a[href*=".htm"], a[href*="/"]');
      var href = link ? link.getAttribute('href') : '';
      var m = href ? href.match(/\/(\d+)[-_]/) : null;
      if (m && m[1]) itemId = m[1];
    }

    return {
      item_name: name,
      item_id: itemId ? String(itemId) : undefined,
      price: price !== null ? price : undefined,
      item_list_name: listName || undefined,
      index: index
    };
  }

  function pushViewItemList() {
    var cards = getProductCards();
    if (!cards.length) return false;

    var listName = getListName();
    var items = [];
    for (var i = 0; i < cards.length; i++) {
      var it = extractItemFromCard(cards[i], i + 1, listName);
      if (it) items.push(it);
      if (items.length >= 50) break; // limit rozsądny
    }
    if (!items.length) return false;

    var key = '__rc_view_item_list_sent_' + location.pathname + location.search;
    try {
      if (sessionStorage.getItem(key) === '1') return true;
      sessionStorage.setItem(key, '1');
    } catch (e) {}

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
      event: 'view_item_list',
      ecommerce: {
        currency: getCurrency(),
        item_list_name: listName || undefined,
        items: items
      }
    });

    console.log('RC view_item_list pushed:', items.length);
    return true;
  }

  function start() {
    if (pushViewItemList()) return;

    // polling (Leo często dociąga listing AJAX-em)
    var tries = 0;
    var t = setInterval(function () {
      tries++;
      if (pushViewItemList() || tries > 25) clearInterval(t);
    }, 200);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
