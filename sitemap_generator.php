<?php
/**
 * sitemap_generator.php (PrestaShop 8.x)
 *
 * Generuje i zapisuje na serwerze:
 *  - sitemap_products.xml (URL-e produktow)
 *  - sitemap_categories.xml (URL-e kategorii)
 *  - sitemap_index.xml (indeks wskazujacy na powyzsze)
 *
 * Uruchomienie:
 *  - przez WWW: https://twojadomena.pl/sitemap_generator.php
 *  - przez CLI/cron: php /sciezka/do/prestashop/sitemap_generator.php
 *
 * Wskazowka: wrzuc plik do katalogu glownego PrestaShop (obok index.php).
 */

require __DIR__ . '/config/config.inc.php';
require __DIR__ . '/init.php';

if (!defined('_PS_VERSION_')) {
    http_response_code(500);
    echo "PrestaShop niezaladowany.\n";
    exit;
}

// (Opcjonalnie) prosta ochrona - ustaw swoj sekret i wywoluj ?key=...
// $secret = 'USTAW_DLUGI_LOSOWY_SEKRET';
// if (php_sapi_name() !== 'cli') {
//     $key = $_GET['key'] ?? '';
//     if ($key !== $secret) {
//         http_response_code(403);
//         echo "Forbidden\n";
//         exit;
//     }
// }

// Kontekst sklepu (jesli kiedys wlaczysz multistore - tu wymusisz konkretny sklep)
$shopId = (int)Configuration::get('PS_SHOP_DEFAULT');
Shop::setContext(Shop::CONTEXT_SHOP, $shopId);

$context = Context::getContext();

$idLangDefault = (int)Configuration::get('PS_LANG_DEFAULT');
$context->language = new Language($idLangDefault);

$shopDomain = Tools::getShopDomainSsl(true);
// Tools::getShopDomainSsl(true) zwraca zwykle "https://domena.pl".

/**
 * Bezpieczny zapis (atomowy) - najpierw .tmp, potem rename.
 */
function writeFileAtomic(string $targetPath, string $content): void
{
    $tmp = $targetPath . '.' . uniqid('tmp_', true);
    if (file_put_contents($tmp, $content) === false) {
        throw new Exception('Nie mozna zapisac pliku tymczasowego: ' . $tmp);
    }
    if (!@rename($tmp, $targetPath)) {
        @unlink($tmp);
        throw new Exception('Nie mozna podmienic pliku docelowego: ' . $targetPath);
    }
}

function isoDate(?string $dateTime): string
{
    // sitemap przyjmuje YYYY-MM-DD lub pelny ISO 8601; dajemy YYYY-MM-DD
    if (!$dateTime) {
        return date('Y-m-d');
    }
    $ts = strtotime($dateTime);
    return $ts ? date('Y-m-d', $ts) : date('Y-m-d');
}

function buildUrlset(array $items): string
{
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    foreach ($items as $u) {
        $loc = $u['loc'];
        $lastmod = $u['lastmod'] ?? null;
        $changefreq = $u['changefreq'] ?? null;
        $priority = $u['priority'] ?? null;

        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</loc>\n";
        if ($lastmod) {
            $xml .= "    <lastmod>" . htmlspecialchars($lastmod, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</lastmod>\n";
        }
        if ($changefreq) {
            $xml .= "    <changefreq>" . htmlspecialchars($changefreq, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</changefreq>\n";
        }
        if ($priority !== null) {
            // priority w sitemap powinno byc 0.0-1.0
            $xml .= "    <priority>" . htmlspecialchars((string)$priority, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</priority>\n";
        }
        $xml .= "  </url>\n";
    }

    $xml .= "</urlset>\n";
    return $xml;
}

function buildSitemapIndex(array $sitemaps): string
{
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    foreach ($sitemaps as $sm) {
        $loc = $sm['loc'];
        $lastmod = $sm['lastmod'] ?? date('Y-m-d');

        $xml .= "  <sitemap>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</loc>\n";
        $xml .= "    <lastmod>" . htmlspecialchars($lastmod, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</lastmod>\n";
        $xml .= "  </sitemap>\n";
    }

    $xml .= "</sitemapindex>\n";
    return $xml;
}

try {
    // =====================
    // 1) Produkty
    // =====================
    $idLang = (int)$context->language->id;

    $products = Product::getProducts(
        $idLang,
        0,  // offset
        0,  // limit=0 -> wszystkie
        'id_product',
        'ASC'
    );

    $productUrls = [];
    foreach ($products as $p) {
        $idProduct = (int)$p['id_product'];
        $productObj = new Product($idProduct, false, $idLang);

        if (!Validate::isLoadedObject($productObj)) {
            continue;
        }

        // Zasada "sitemap = tylko indexowalne":
        // - aktywny
        // - widocznosc != none
        // (nie filtrujemy po stanie magazynowym)
        if (!(bool)$productObj->active) {
            continue;
        }
        if (isset($productObj->visibility) && $productObj->visibility === 'none') {
            continue;
        }

        // Jesli produkt ma ustawione noindex (zalezne od motywu/modulow),
        // PrestaShop trzyma to m.in. w polu "indexation" (w niektorych wersjach).
        if (property_exists($productObj, 'indexation') && (int)$productObj->indexation === 0) {
            continue;
        }

        $loc = $context->link->getProductLink($productObj);
        $productUrls[] = [
            'loc'        => $loc,
            'lastmod'    => isoDate($productObj->date_upd ?? null),
            'changefreq' => 'weekly',
            'priority'   => '0.8',
        ];
    }

    // =====================
    // 2) Kategorie
    // =====================
    // Zwraca liste kategorii (bez budowania calego drzewa na potrzeby BO)
    $categoriesRaw = Category::getCategories($idLang, true, false);

    $categoryUrls = [];

    // Struktura zwracana przez getCategories bywa zagniezdzona; zrobimy prosty iterator po ID.
    $stack = [$categoriesRaw];
    $seenCat = [];

    while ($stack) {
        $node = array_pop($stack);
        if (!is_array($node)) {
            continue;
        }

        // Jezeli tablica wyglada jak pojedyncza kategoria z id_category
        if (isset($node['id_category'])) {
            $idCategory = (int)$node['id_category'];

            if ($idCategory <= 1) { // 1 = Home/root
                continue;
            }
            if (isset($seenCat[$idCategory])) {
                continue;
            }
            $seenCat[$idCategory] = true;

            $catObj = new Category($idCategory, $idLang);
            if (!Validate::isLoadedObject($catObj)) {
                continue;
            }
            if (!(bool)$catObj->active) {
                continue;
            }
            if (method_exists($catObj, 'isAssociatedToShop') && !$catObj->isAssociatedToShop(Shop::getContextShopID())) {
                continue;
            }
            if (property_exists($catObj, 'indexation') && (int)$catObj->indexation === 0) {
                continue;
            }

            $loc = $context->link->getCategoryLink($catObj);

            $categoryUrls[] = [
                'loc'        => $loc,
                'lastmod'    => isoDate($catObj->date_upd ?? null),
                'changefreq' => 'weekly',
                'priority'   => '0.6',
            ];

            // Dzieci
            if (isset($node['children']) && is_array($node['children'])) {
                foreach ($node['children'] as $child) {
                    $stack[] = $child;
                }
            }

            continue;
        }

        // W przeciwnym razie iterujemy po elementach
        foreach ($node as $v) {
            if (is_array($v)) {
                $stack[] = $v;
            }
        }
    }

    // =====================
    // 3) Zapis plikow
    // =====================
    $root = _PS_ROOT_DIR_;

    $productsFile   = $root . '/sitemap_products.xml';
    $categoriesFile = $root . '/sitemap_categories.xml';
    $indexFile      = $root . '/sitemap_index.xml';

    writeFileAtomic($productsFile, buildUrlset($productUrls));
    writeFileAtomic($categoriesFile, buildUrlset($categoryUrls));

    // indeks (polecam ten zgłosic w Search Console)
    $indexXml = buildSitemapIndex([
        [
            'loc' => rtrim($shopDomain, '/') . '/sitemap_products.xml',
            'lastmod' => date('Y-m-d'),
        ],
        [
            'loc' => rtrim($shopDomain, '/') . '/sitemap_categories.xml',
            'lastmod' => date('Y-m-d'),
        ],
    ]);
    writeFileAtomic($indexFile, $indexXml);

    // =====================
    // 4) Output
    // =====================
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: text/plain; charset=utf-8');
    }

    echo "OK\n";
    echo "Zapisano:\n";
    echo "- {$productsFile} (" . count($productUrls) . ")\n";
    echo "- {$categoriesFile} (" . count($categoryUrls) . ")\n";
    echo "- {$indexFile}\n\n";
    echo "Zglos w GSC: " . rtrim($shopDomain, '/') . "/sitemap_index.xml\n";

} catch (Throwable $e) {
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: text/plain; charset=utf-8');
        http_response_code(500);
    }
    echo "BLAD: " . $e->getMessage() . "\n";
    exit(1);
}
