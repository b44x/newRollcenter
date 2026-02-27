<?php
// sitemap-blog.php — Sitemap XML dla LeoBlog (tylko wpisy, PL)
// URL wpisu: /blog/<slug>-b<ID>.html

@ini_set('display_errors', '0');
@ini_set('html_errors', '0');
@error_reporting(0);

// Czyścimy wszystko co mogłoby się “wysypać” przed XML
while (ob_get_level()) { @ob_end_clean(); }
@ob_start();

require __DIR__ . '/config/config.inc.php';
require __DIR__ . '/init.php';

// Kasujemy ewentualny output z init/config
if (ob_get_length()) { @ob_clean(); }

header('Content-Type: application/xml; charset=utf-8');

$context = Context::getContext();
$idShop  = (int)$context->shop->id;
$idLang  = (int)Configuration::get('PS_LANG_DEFAULT'); // PL
$base    = Tools::getShopDomainSsl(true);

$db = Db::getInstance();

// Tabele LeoBlog (standard)
$tblBlog = _DB_PREFIX_ . 'leoblog_blog';
$tblLang = _DB_PREFIX_ . 'leoblog_blog_lang';
$tblShop = _DB_PREFIX_ . 'leoblog_blog_shop';

// Właściwe zapytanie: tylko aktywne wpisy, przypisane do sklepu i języka
$sql = "
SELECT
  b.id_leoblog_blog AS id_blog,
  bl.link_rewrite   AS slug,
  COALESCE(b.date_upd, b.date_add) AS lastmod
FROM `$tblBlog` b
INNER JOIN `$tblLang` bl
  ON bl.id_leoblog_blog = b.id_leoblog_blog
INNER JOIN `$tblShop` bs
  ON bs.id_leoblog_blog = b.id_leoblog_blog
WHERE
  bl.id_lang = " . (int)$idLang . "
  AND bs.id_shop = " . (int)$idShop . "
  AND b.active = 1
  AND bl.link_rewrite IS NOT NULL
  AND bl.link_rewrite != ''
ORDER BY b.id_leoblog_blog DESC
";

try {
    $rows = $db->executeS($sql);
} catch (Exception $e) {
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<!-- LeoBlog: błąd wykonania zapytania. Sprawdź czy tabele istnieją: $tblBlog, $tblLang, $tblShop -->\n";
    echo "<!-- " . htmlspecialchars($e->getMessage(), ENT_XML1 | ENT_COMPAT, 'UTF-8') . " -->\n";
    @ob_end_flush();
    exit;
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

foreach ($rows as $r) {
    $id   = (int)$r['id_blog'];
    $slug = trim((string)$r['slug']);
    if ($id <= 0 || $slug === '') continue;

    // LeoBlog: /blog/slug-bID.html
    $loc = $base . '/blog/' . $slug . '-b' . $id . '.html';

    $lastmod = $r['lastmod'] ? substr((string)$r['lastmod'], 0, 10) : null;

    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</loc>\n";
    if ($lastmod) {
        echo "    <lastmod>" . htmlspecialchars($lastmod, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</lastmod>\n";
    }
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.6</priority>\n";
    echo "  </url>\n";
}

echo "</urlset>\n";
@ob_end_flush();
