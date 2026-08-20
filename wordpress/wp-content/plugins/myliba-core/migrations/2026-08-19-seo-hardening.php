<?php

if (!defined('ABSPATH')) {
    exit;
}

$updates = [
    ['myliba_solution', 'kurumsal-gelisim-programlari', 'tr', 'Kurumsal Gelişim Programları | Myliba', 'Kuruma özel gelişim programlarıyla liderlik, performans ve kültür dönüşümünü sürdürülebilir bir sisteme dönüştürün.'],
    ['myliba_solution', 'simulasyonlar-ve-takim-koclugu', 'tr', 'Simülasyonlar ve Takım Koçluğu | Myliba', 'Simülasyonlar ve takım koçluğuyla iş birliği, liderlik ve yüksek performans davranışlarını gerçek çalışma ortamına taşıyın.'],
    ['myliba_solution', 'danismanlik', 'tr', 'Stratejik Danışmanlık | Myliba', 'Stratejik hedeflerinizi netleştirin ve kurumunuza özel performans gelişim sistemini Myliba ile birlikte kurun.'],
    ['myliba_solution', 'kultur-analizi', 'tr', 'Kültür Analizi | Myliba', 'Kültür analiziyle bağlılık, iş birliği ve performansı etkileyen davranış kalıplarını veriye dayalı içgörülerle görünür kılın.'],
    ['myliba_academy', 'icf-okr-culture-coaching', 'tr', 'ICF Onaylı OKR ve Kültür Koçluğu | Myliba', null],
    ['myliba_academy', 'leadership-performance-development', 'tr', 'İşbaşı Liderlik ve Performans Programı | Myliba', null],
    ['myliba_academy', 'goal-cultural-transformation', 'tr', 'Hedef ve Kültürel Dönüşüm Danışmanlığı | Myliba', null],
    ['myliba_event', 'myliba-culture-session', 'tr', 'Myliba Kültür ve Performans Oturumu', 'OKR, kültür analitiği ve sürekli diyalogla hesap verebilir ve çevik organizasyonlar kurmayı Myliba oturumunda keşfedin.'],
    ['myliba_event', 'myliba-culture-session', 'en', null, 'Join Myliba’s interactive session on building accountability, agility, and continuous dialogue with OKRs and cultural analytics.'],
];

foreach ($updates as [$post_type, $translation_key, $language, $title, $description]) {
    $posts = get_posts([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'meta_query' => [
            ['key' => '_myliba_translation_key', 'value' => $translation_key],
            ['key' => '_myliba_language', 'value' => $language],
        ],
    ]);

    if (!$posts) {
        WP_CLI::warning(sprintf('SEO target not found: %s / %s / %s', $post_type, $translation_key, $language));
        continue;
    }

    $post_id = (int) $posts[0]->ID;
    if ($title !== null && trim((string) get_post_meta($post_id, '_myliba_seo_title', true)) === '') {
        update_post_meta($post_id, '_myliba_seo_title', $title);
    }
    if ($description !== null && trim((string) get_post_meta($post_id, '_myliba_seo_description', true)) === '') {
        update_post_meta($post_id, '_myliba_seo_description', $description);
    }
}

$concise_titles = [
    ['myliba_academy', 'icf-accredited-okr-culture-coaching', 'en', 'ICF-Accredited OKR & Culture Coaching | Myliba'],
    ['post', 'okr-and-cfr-5-cultural-steps-to-transform-your-organization', 'en', 'OKR and CFR: 5 Steps to Transform Culture | Myliba'],
    ['post', 'accountability-in-okrs', 'en', 'Accountability in OKRs: A New Performance Dynamic | Myliba'],
    ['post', 'how-to-build-modern-talent-management', 'en', 'Modern Talent Management with OKRs and CFRs | Myliba'],
    ['post', 'cultural-transformation-with-okrs', 'en', 'Where Cultural Transformation with OKRs Starts | Myliba'],
    ['post', 'feedback-vs-feedforward', 'en', 'Feedback vs Feedforward in High-Performance Cultures | Myliba'],
    ['post', 'what-is-cfr-goal-performance-management', 'en', 'What Is CFR in Goal and Performance Management? | Myliba'],
    ['post', 'choosing-the-right-tools-on-the-road-to-success-okr-and-kpi', 'en', 'OKR vs KPI: Choosing the Right Tool | Myliba'],
];

foreach ($concise_titles as [$post_type, $slug, $language, $title]) {
    $title_posts = get_posts([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'name' => $slug,
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'meta_key' => '_myliba_language',
        'meta_value' => $language,
    ]);
    if ($title_posts) {
        update_post_meta($title_posts[0]->ID, '_myliba_seo_title', $title);
    }
}

$descriptions = get_posts([
    'post_type' => ['page', 'post', 'myliba_product', 'myliba_solution', 'myliba_academy', 'myliba_landing', 'myliba_event', 'myliba_ebook', 'myliba_report'],
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'no_found_rows' => true,
    'meta_key' => '_myliba_seo_description',
]);

foreach ($descriptions as $description_post) {
    $description = trim((string) get_post_meta($description_post->ID, '_myliba_seo_description', true));
    if (mb_strlen($description) <= 160) {
        continue;
    }

    $short = rtrim(mb_substr($description, 0, 157));
    $last_space = mb_strrpos($short, ' ');
    if ($last_space !== false && $last_space > 120) {
        $short = rtrim(mb_substr($short, 0, $last_space));
    }
    update_post_meta($description_post->ID, '_myliba_seo_description', rtrim($short, '.,;:') . '…');
}

$development_center = get_page_by_path('tr/gelisim-merkezi');
if ($development_center instanceof WP_Post) {
    if (trim((string) get_post_meta($development_center->ID, '_myliba_seo_title', true)) === '') {
        update_post_meta($development_center->ID, '_myliba_seo_title', 'Gelişim Merkezi | Myliba');
    }
    if (trim((string) get_post_meta($development_center->ID, '_myliba_seo_description', true)) === '') {
        update_post_meta($development_center->ID, '_myliba_seo_description', 'Yüksek performans kültürü, liderlik ve organizasyonel gelişim için Myliba e-kitaplarını, raporlarını, yazılarını ve etkinliklerini keşfedin.');
    }
}

global $wpdb;
$legacy_product_meta_ids = $wpdb->get_col(
    "SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key LIKE '_myliba_%' AND meta_value LIKE '%/en/our-products/%'"
);
$replace_legacy_product_url = static function ($value) use (&$replace_legacy_product_url) {
    if (is_string($value)) {
        return str_replace('/en/our-products/', '/en/software/', $value);
    }
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = $replace_legacy_product_url($item);
        }
    }
    return $value;
};

foreach ($legacy_product_meta_ids as $meta_id) {
    $metadata = get_metadata_by_mid('post', (int) $meta_id);
    if ($metadata) {
        update_metadata_by_mid('post', (int) $meta_id, $replace_legacy_product_url($metadata->meta_value));
    }
}

WP_CLI::success('Missing SEO titles and descriptions were completed.');
