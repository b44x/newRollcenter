<script>
(function () {
  window.dataLayer = window.dataLayer || [];

  // Anti-duplicate: 1 view_item na wejściu w produkt
  if (window.__rc_view_item_sent) return;
  window.__rc_view_item_sent = true;

  function parsePrice(text) {
    if (!text) return null;
    // usuń spacje, NBSP, "zł", itp.
    text = String(text).replace(/\s|\u00A0/g, '');
    // wyciągnij liczby z przecinkiem/kropką
    // np. "1.234,56" => "1234,56"
    text = text.replace(/[^\d,\.]/g, '');
    // jeśli jest i kropka i przecinek, zakładamy PL: kropka tysiące, przecinek dziesiętne
    if (text.includes('.') && text.includes(',')) text = text.replace(/\./g, '');
    // zamień przecinek na kropkę
    text = text.replace(',', '.');
    var val = parseFloat(text);
    return isNaN(val) ? null : val;
  }

  function getDisplayedPrice() {
    // Najczęściej w PS8 cena jest w elementach z klasami .product-price lub [itemprop=price]
    // Cel: złapać FINALNĄ cenę widoczną klientowi po DP.
    var el =
      document.querySelector('[itemprop="price"]') ||
      document.querySelector('.product-price [content]') ||
      document.querySelector('.current-price span') ||
      document.querySelector('.product-price') ||
      document.querySelector('.current-price');

    if (!el) return null;

    // część motywów trzyma liczbę w atrybucie content
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
    // spróbujmy z prestashop.product jeśli istnieje
    try {
      if (window.prestashop && prestashop.product && prestashop.product.id_product) {
        return String(prestashop.product.id_product);
      }
    } catch (e) {}
    return null;
  }

  // DP potrafi aktualizować cenę kilka razy po wejściu.
  // Weźmy cenę dopiero gdy przez ~500ms jest taka sama i nie-null.
  var last = null;
  var stableCount = 0;
  var start = Date.now();

  var timer = setInterval(function () {
    var price = getDisplayedPrice();

    if (price !== null && price === last) {
      stableCount++;
    } else {
      stableCount = 0;
      last = price;
    }

    // 3x po 250ms = ~750ms stabilności
    if (price !== null && stableCount >= 3) {
      clearInterval(timer);

      window.dataLayer.push({
        event: 'view_item',
        ecommerce: {
          currency: 'PLN',
          value: price,
          items: [{
            item_id: getProductId() || undefined,
            item_name: getProductName(),
            price: price,
            quantity: 1
          }]
        }
      });

      return;
    }

    // timeout bezpieczeństwa: 6s
    if (Date.now() - start > 6000) {
      clearInterval(timer);
      // fallback: wyślij nawet jeśli nie udało się ustabilizować, ale tylko jeśli mamy cokolwiek
      if (last !== null) {
        window.dataLayer.push({
          event: 'view_item',
          ecommerce: {
            currency: 'PLN',
            value: last,
            items: [{
              item_id: getProductId() || undefined,
              item_name: getProductName(),
              price: last,
              quantity: 1
            }]
          }
        });
      }
    }
  }, 250);
})();
</script>
