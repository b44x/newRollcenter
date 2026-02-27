<?php
// feed_dynamicproduct.php
// Generator feedu XML dla Google Merchant z cenami z modułu Dynamic Product

use DynamicProduct\classes\module\DynamicCalculator;

require __DIR__ . '/config/config.inc.php';
require __DIR__ . '/init.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

// Ustaw kontekst sklepu (dla multishop można tu wymusić konkretny sklep)
$shopId = (int)Configuration::get('PS_SHOP_DEFAULT');
Shop::setContext(Shop::CONTEXT_SHOP, $shopId);

$context = Context::getContext();

// Język i waluta feedu
$idLangDefault   = (int)Configuration::get('PS_LANG_DEFAULT');
$idCurrency      = (int)Configuration::get('PS_CURRENCY_DEFAULT');
$idCountry       = (int)Configuration::get('PS_COUNTRY_DEFAULT');

$context->language = new Language($idLangDefault);
$context->currency = new Currency($idCurrency);
$context->country  = new Country($idCountry);
$context->customer = new Customer(); // gość, domyślna grupa

// Pobierz moduł Dynamic Product
/** @var Module $dpModule */
$dpModule = Module::getInstanceByName('dynamicproduct');

if (!$dpModule || !$dpModule->active) {
    header('Content-Type: text/plain; charset=utf-8');
    die('Moduł dynamicproduct nie jest dostępny lub jest wyłączony.');
}

// Upewnij się, że moduł ma zainicjalizowany kalkulator
if (!isset($dpModule->calculator) || !$dpModule->calculator) {
    $dpModule->calculator = new DynamicCalculator($dpModule, $context);
}

// Nagłówek XML
header('Content-Type: application/xml; charset=utf-8');

// Ustawienia podstawowe
$shopDomain = Tools::getShopDomainSsl(true);
$currencyIso = $context->currency->iso_code ?: 'PLN';

// Pobierz listę produktów
$idLang = $idLangDefault;

// Pobieramy wszystkie produkty (możesz tu dodać paginację, jeśli masz ich tysiące)
$products = Product::getProducts(
    $idLang,
    0,      // offset
    0,      // limit = 0 -> wszystkie
    'id_product',
    'ASC'
);

function getProductCategoryPath(Product $productObj, $id_lang)
{
    $paths = [];

    // 1) Priorytet: kategoria domyślna produktu
    $id_default_cat = (int)$productObj->id_category_default;

    $category = null;

    if ($id_default_cat) {
        $candidate = new Category($id_default_cat, $id_lang);

        if (Validate::isLoadedObject($candidate)
            && $candidate->active
            && $candidate->isAssociatedToShop(Shop::getContextShopID())
        ) {
            $category = $candidate;
        }
    }

    // 2) Jeśli kategoria domyślna jest nieaktywna / nie przypisana – szukamy innej aktywnej
    if (!$category) {
        $cats = Product::getProductCategories($productObj->id);
        if ($cats && is_array($cats)) {
            foreach ($cats as $id_cat) {
                $candidate = new Category((int)$id_cat, $id_lang);
                if (Validate::isLoadedObject($candidate)
                    && $candidate->active
                    && $candidate->isAssociatedToShop(Shop::getContextShopID())
                ) {
                    $category = $candidate;
                    break;
                }
            }
        }
    }

    if (!$category) {
        return '';
    }

    // 3) Budujemy ścieżkę kategorii (bez "Home")
    $parents = $category->getParentsCategories($id_lang);

    if (is_array($parents)) {
        foreach (array_reverse($parents) as $cat) {
            if (!isset($cat['name']) || $cat['name'] === 'Home' || (int)$cat['id_category'] === 1) {
                continue;
            }
            $paths[] = trim($cat['name']);
        }
    }

    return implode(' > ', $paths);
}


// Start XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
  <channel>
    <title><?php echo htmlspecialchars(Configuration::get('PS_SHOP_NAME'), ENT_XML1 | ENT_COMPAT, 'UTF-8'); ?></title>
    <link><?php echo htmlspecialchars($shopDomain, ENT_XML1 | ENT_COMPAT, 'UTF-8'); ?></link>
    <description>Feed produktów z dynamicznymi cenami (Dynamic Product)</description>

<?php
foreach ($products as $p) {
    $id_product = (int)$p['id_product'];
    $productObj = new Product($id_product, false, $idLang);

    // Filtry - pomijamy nieaktywne i niedostępne do zamówienia
    if (!$productObj->active || !$productObj->available_for_order) {
        continue;
    }

    // Domyślny atrybut (kombinacja) - dla "ceny od"
    $id_attribute = (int)Product::getDefaultAttribute($id_product);
    $productRow = [
        'id_product' => $id_product,
        'id_product_attribute' => $id_attribute ?: 0,
    ];

    // Oblicz ceny przez DynamicCalculator
    $resultPrices = [];

    // display_price na wejściu = 0, bo i tak zostanie nadpisany dla produktów dynamicznych
    $dpModule->calculator->assignProductPrices($productRow, 0, $resultPrices);

    if (empty($resultPrices['price'])) {
        // jeżeli z jakiegoś powodu moduł nie policzył ceny, można spróbować standardowo:
        $fallbackPrice = Product::getPriceStatic(
            $id_product,
            true,
            $id_attribute ?: null
        );
        if ($fallbackPrice <= 0) {
            // całkiem bez ceny – pomijamy z feedu
            continue;
        }
        $priceTtc = $fallbackPrice;
    } else {
        $priceTtc = (float)$resultPrices['price']; // brutto
    }

    // Format ceny wg Google (2 miejsca po przecinku, kropka jako separator)
    $priceFormatted = number_format($priceTtc, 2, '.', '') . ' ' . $currencyIso;

    // Link do produktu
    $link = $context->link->getProductLink($productObj);

    // Obrazek (cover)
    $cover = Product::getCover($id_product);
    $imageLink = '';
    if ($cover && isset($cover['id_image'])) {
        $imageLink = $context->link->getImageLink(
            $productObj->link_rewrite,
            $cover['id_image'],
            ImageType::getFormattedName('large')
        );
        // dopilnuj, aby był pełny URL
        if (strpos($imageLink, 'http') !== 0) {
            $imageLink = $shopDomain . $imageLink;
        }
    }

    // Tytuł
    $title = $productObj->name;
// Opis – preferuj description_short, ale jeśli po oczyszczeniu jest pusty, użyj description
$shortRaw = (string)$productObj->description_short;
$fullRaw  = (string)$productObj->description;

$short = trim(html_entity_decode(strip_tags($shortRaw), ENT_QUOTES, 'UTF-8'));
$short = preg_replace('/\x{00A0}+/u', ' ', $short); // usuń &nbsp; (NBSP)

$full = trim(html_entity_decode(strip_tags($fullRaw), ENT_QUOTES, 'UTF-8'));
$full = preg_replace('/\x{00A0}+/u', ' ', $full);

$desc = $short !== '' ? $short : $full;

// Ostateczny fallback (gdyby oba były puste) – żeby nie generować pustego <description>
if ($desc === '') {
    $desc = $title; // albo: 'Produkt wykonywany na wymiar.'
}

// Limit długości
if (Tools::strlen($desc) > 5000) {
    $desc = Tools::substr($desc, 0, 4970) . '...';
}
    // Availability
    $quantity = (int)StockAvailable::getQuantityAvailableByProduct($id_product, $id_attribute ?: null);
    $availability = ($quantity > 0) ? 'in stock' : 'out of stock';

    // Brand (manufacturer)
    $brand = '';
    if ($productObj->id_manufacturer) {
        $man = new Manufacturer((int)$productObj->id_manufacturer, $idLang);
        if (Validate::isLoadedObject($man)) {
            $brand = $man->name;
        }
    }

    // GTIN / MPN
    $gtin = $productObj->ean13;       // lub ean13/upc
    $mpn  = $productObj->reference;   // Twój symbol produktu

	// Ścieżka kategorii jako product_type – na podstawie DOMYŚLNEJ kategorii produktu
	$productType = getProductCategoryPath($productObj, $idLang);

    // google_product_category – tu możesz wstawić stałą kategorię Google,
    // np. "Home & Garden > Linens & Bedding > Window Treatments"
    // albo ID kategorii Google (np. 1901). Na razie damy pusty, do uzupełnienia.
    $googleProductCategory = '2885'; // uzupełnij docelowo ręcznie / mapowaniem

    // ID feedu – jeśli używasz kombinacji, możesz zbudować np. id_product-id_attribute
    $feedId = $id_product;
    if ($id_attribute) {
        $feedId .= '-' . $id_attribute;
    }
    ?>

    <item>
      <g:id><?php echo htmlspecialchars($feedId, ENT_XML1 | ENT_COMPAT, 'UTF-8'); ?></g:id>
      <title><![CDATA[<?php echo $title; ?>]]></title>
      <description><![CDATA[<?php echo $desc; ?>]]></description>
      <link><![CDATA[<?php echo $link; ?>]]></link>
<?php if ($imageLink) { ?>
      <g:image_link><![CDATA[<?php echo $imageLink; ?>]]></g:image_link>
<?php } ?>
      <g:availability><?php echo $availability; ?></g:availability>
      <g:price><?php echo $priceFormatted; ?></g:price>
      <g:condition>new</g:condition>
<?php if ($brand) { ?>
      <g:brand><![CDATA[<?php echo $brand; ?>]]></g:brand>
<?php } ?>
<?php if ($gtin) { ?>
      <g:gtin><![CDATA[<?php echo $gtin; ?>]]></g:gtin>
<?php } ?>
<?php if ($mpn) { ?>
      <g:mpn><![CDATA[<?php echo $mpn; ?>]]></g:mpn>
<?php } ?>
<?php if ($googleProductCategory) { ?>
      <g:google_product_category><![CDATA[<?php echo $googleProductCategory; ?>]]></g:google_product_category>
<?php } ?>
<?php if ($productType) { ?>
      <g:product_type><![CDATA[<?php echo $productType; ?>]]></g:product_type>
<?php } ?>
    </item>

<?php
} // end foreach products
?>

  </channel>
</rss>
