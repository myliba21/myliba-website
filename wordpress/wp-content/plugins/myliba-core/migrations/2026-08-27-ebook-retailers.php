<?php

if (!defined('ABSPATH')) {
    exit;
}

// Migrate the existing hardcoded Gen-Z retailer list into the PageContent system.
// Both the TR and EN versions of the post get the same retailer collection.

$ebook_slugs = [
    'gen-z-ile-yuksek-performans-kulturu-yaratmak',
    'creating-a-high-performance-culture-with-gen-z',
];

$retailers = [
    ['name' => 'Kitapyurdu',    'url' => 'https://www.kitapyurdu.com/kitap/genz-ile-yuksek-performans-kulturu-yaratmak-nasrettin-hoca-yonetim-danismani-olursa/751529.html'],
    ['name' => 'Kitapsepeti',   'url' => 'https://www.kitapsepeti.com/ceres-yayinlari'],
    ['name' => 'Ekin Kitap',    'url' => 'https://www.ekinkitap.com/gen-z-ile-yuksek-performans-kulturu-yaratmak'],
    ['name' => 'Kitapstore',    'url' => 'https://www.kitapstore.com/urun/770536/kitap/ceres-yayinlari/dilek-mete/gen-z-ile-yuksek-performans-kulturu-yaratmak/'],
    ['name' => 'Ravza Kitap',   'url' => 'https://www.ravzakitap.com/gen-z-ile-yuksek-performans-kulturu-yaratmak'],
    ['name' => 'İlla Kitap',    'url' => 'https://www.illakitap.com/gen-z-ile-yuksek-performans-kulturu-yaratmak'],
    ['name' => 'Simurg Kitabevi', 'url' => 'https://www.simurgkitabevi.com/gen-z-ile-yuksek-performans-kulturu-yaratmak-nasrettin-hoca-yonetim-danismani-olursa-2026'],
    ['name' => 'Kitapzen',      'url' => 'https://www.kitapzen.com/dilek-mete/gen-z-ile-yuksek-performans-kulturu-yaratmak.htm'],
    ['name' => 'Şehadet Kitap', 'url' => 'https://www.sehadetkitap.com/urun/gen-z-ile-yuksek-performans-kulturu-yaratmak-nasrettin-hoca-yonetim-danismani-olursa'],
    ['name' => 'NadirKitap',    'url' => 'https://www.nadirkitap.com/gen-z-ile-yuksek-performans-kulturu-yaratmak-nasrettin-hoca-yonetim-danismani-olursa-dilek-mete-ceres-yayinlari-kitap46770916.html'],
    ['name' => 'Kitap Ambarı',  'url' => 'https://www.kitapambari.com/dilek-mete'],
];

$updated = 0;

foreach ($ebook_slugs as $slug) {
    $post = get_page_by_path($slug, OBJECT, 'myliba_ebook');
    if (!$post instanceof WP_Post) {
        WP_CLI::warning('e-Book not found: ' . $slug);
        continue;
    }

    $document = \Myliba\Core\PageContent\document($post->ID, 'ebook');
    $document['collections']['retailers'] = $retailers;

    $encoded = wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    update_post_meta($post->ID, \Myliba\Core\PageContent\META_KEY, wp_slash($encoded));

    WP_CLI::success('Retailers written for: ' . $slug . ' (ID: ' . $post->ID . ')');
    $updated++;
}

if ($updated === 0) {
    WP_CLI::warning('No e-book posts were updated. Make sure the post slugs match.');
} else {
    WP_CLI::success($updated . ' e-book(s) updated with retailer data.');
}
