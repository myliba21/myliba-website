<?php
/**
 * 2026-11-23 — Blog yazılarında Türkçe–İngilizce eşleştirme.
 *
 * Kaynak: docs/seo/audit-2026-11-23.md (B4). Blog yazılarında
 * `_myliba_translation_key` tanımlı olmadığı için hreflang yalnız yazının
 * kendisini gösteriyordu. Bu betik aşağıdaki çiftlere ortak anahtar yazar;
 * başlık, içerik ve adreslere dokunmaz. İdempotenttir.
 *
 * Önizleme:  make pairs-preview
 * Uygula:    make pairs-apply
 * Yalnız yerel ortamda çalıştırın; değişiklik canlıya klonla gider.
 */

if (!defined('ABSPATH')) {
    exit;
}

$home_host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
$is_local = in_array(wp_get_environment_type(), ['local', 'development'], true)
    || in_array($home_host, ['localhost', '127.0.0.1'], true);
if (!$is_local) {
    WP_CLI::error('Bu betik yalnız yerel ortamda çalıştırılır.');
}

$apply = isset($args) && is_array($args) && in_array('apply', $args, true);

// Türkçe slug => İngilizce slug
$pairs = [
    'iki-dakikada-okrs-nedir-ogrenin' => 'a-two-minute-crash-course-into-what-okr-are',
    'okrde-hesap-verebilirlik' => 'accountability-in-okrs',
    'okrleriniz-ise-yariyor-mu-bu-3-soruyla-kendiniz-karar-verin' => 'are-your-okrs-working-find-out-for-yourself-with-these-3-questions',
    'en-iyi-okr-yazilimlari' => 'best-okr-software',
    'hedefe-giden-yolda-dogru-araclari-secmek-okr-ve-kpi' => 'choosing-the-right-tools-on-the-road-to-success-okr-and-kpi',
    'surekli-ve-anlamli-geribildirim' => 'continuous-and-meaningful-feedback',
    'surekli-birebir-gorusmeler' => 'continuous-one-on-one-meetings',
    'okr-ile-kulturel-donusum' => 'cultural-transformation-with-okrs',
    'baskalarinin-sorumlulugunu-ustlenmeyin' => 'do-not-take-responsibility-for-others',
    'geribildirim-ileri-bildirim-farki' => 'feedback-vs-feedforward',
    'performans-degerlendirmesine-veda' => 'goodbye-to-performance-appraisal',
    'modern-yetenek-yonetimi' => 'how-to-build-modern-talent-management',
    'ileri-bildirim-nasil-verilir-gelecege-donuk-ileri-bildirimin-pratik-rehberi' => 'how-to-give-feedforward-a-5-step-guide',
    'is-ve-ozel-hayat-dengesi-degil-isin-hayata-entegrasyonu' => 'life-balance-and-work-integration-with-okr-and-myliba',
    'okr-ornekleri' => 'okr-examples',
    'okr-hatalari-okr-ne-degildir-sirketlerin-dustugu-3-buyuk-hedef-yanilgisi' => 'okr-mistakes-what-okrs-are-not-3-major-goal-setting-misconceptions-companies-fall-into',
    'okrin-iki-yuzu-populist-okr-mi-native-okr-mi' => 'populist-okr-vs-native-okr',
    'hedefin-formulu-okr' => 'the-formula-of-the-target-okr',
    'okr-kullanmak-ve-kullanmamak-sirketler-arasindaki-8-kritik-fark' => 'using-okrs-vs-not-using-okrs-8-critical-differences-between-companies',
    'cfr-nedir-hedef-ve-performans-yonetiminde-cfrin-rolu' => 'what-is-cfr-goal-performance-management',
    'cfr-nedir' => 'what-is-cfr',
    'okr-nedir-hedefleri-canli-tutmanin-ve-kurumda-gercek-donusum-baslatmanin-yolu' => 'what-is-okr',
    'cok-degil-verimli-calisin' => 'work-efficiently-not-excessively',
];

$find = static function (string $slug, string $language): ?WP_Post {
    $posts = get_posts([
        'post_type' => 'post',
        'name' => $slug,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'meta_key' => '_myliba_language',
        'meta_value' => $language,
    ]);

    return $posts ? $posts[0] : null;
};

$changed = 0;
$skipped = 0;
foreach ($pairs as $tr_slug => $en_slug) {
    $tr = $find($tr_slug, 'tr');
    $en = $find($en_slug, 'en');
    if (!$tr || !$en) {
        $skipped++;
        WP_CLI::warning(sprintf('Atlandı (%s bulunamadı): %s <> %s', !$tr ? 'TR' : 'EN', $tr_slug, $en_slug));
        continue;
    }

    $tr_key = trim((string) get_post_meta($tr->ID, '_myliba_translation_key', true));
    $en_key = trim((string) get_post_meta($en->ID, '_myliba_translation_key', true));
    if ($tr_key !== '' && $tr_key === $en_key) {
        continue;
    }
    if ($tr_key !== '' && $en_key !== '' && $tr_key !== $en_key) {
        $skipped++;
        WP_CLI::warning(sprintf('Atlandı (iki farklı anahtar var): #%d %s <> #%d %s', $tr->ID, $tr_key, $en->ID, $en_key));
        continue;
    }

    $key = $tr_key !== '' ? $tr_key : ($en_key !== '' ? $en_key : 'post-' . $tr_slug);
    $changed++;
    WP_CLI::log(sprintf("#%d %s\n    <> #%d %s\n    anahtar: %s", $tr->ID, get_the_title($tr), $en->ID, get_the_title($en), $key));

    if ($apply) {
        update_post_meta($tr->ID, '_myliba_translation_key', $key);
        update_post_meta($en->ID, '_myliba_translation_key', $key);
    }
}

$summary = sprintf('%d çift, %d atlandı', $changed, $skipped);
if ($apply) {
    WP_CLI::success($summary . '. Eşleştirme yazıldı.');
} else {
    WP_CLI::log($summary . '. Uygulamak için: make pairs-apply');
}
