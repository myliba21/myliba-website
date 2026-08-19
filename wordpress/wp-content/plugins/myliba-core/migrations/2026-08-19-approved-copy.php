<?php

if (!defined('ABSPATH')) {
    exit;
}

$home = get_page_by_path('tr');
if (!$home instanceof WP_Post) {
    WP_CLI::error('Turkish homepage was not found.');
}

$home_meta = [
    '_myliba_home_strategy_flow_eyebrow' => 'Performansı Geliştir',
    '_myliba_home_strategy_flow_title' => 'Strateji, OKR, KPI, aksiyon ve sürekli diyalog : performans ve kültür tek akışta',
    '_myliba_home_strategy_flow_text' => 'Performansı ve kültürü birlikte geliştiren, ortak bir zeminde buluşturan bir yapı kurun.',
    '_myliba_home_strategy_flow_steps' => "Strateji | Misyonuzu hayata geçiren stratejiyi netleştirin | S\nHedef ve KPI'lar | OKR ve KPI'ları şirketten takımlara, takımlardan bireylere hizalayın. | H\nAksiyon | Her hedefi net sorumlular, tarihler ve aksiyon adımlarıyla hayata geçirin. | A\nKültür | 1:1, CFR ve gelişme odaklı rutinleri işin etrafına yerleştirin | K",
    '_myliba_home_performance_eyebrow' => '',
    '_myliba_home_performance_text' => 'Performans değerlendirmeyi yılda bir yapılan stresli ve not odaklı bir süreç olmaktan çıkarın; çalışanların kendi performanslarını hem yönettiği hem de geliştirdiği adil ve veriye dayalı bir kültüre geçin.',
    '_myliba_home_performance_tabs' => "Hedef Yönetimi | Çalışmayı iş sonuçlarıyla hizalayın. | OKR, KPI ve aksiyon yönetimiyle stratejinizi organizasyonun her seviyesine taşıyın.\nPerformans Geliştirme | Gelişimi sürekli hale getirin. | Sürekli geri bildirim, ileri bildirim ve gelişim odaklı performans yaklaşımıyla yeteneklerinizin potansiyelini ortaya çıkarın. Yüksek performans kültürü geliştirin.\nYapay Zekâ Destekli İçgörüler | Organizasyonunuzun DNA’sını okuyun. | Yapay zeka destekli analizlerle işe karşı istekliliği, bağlılığı ve kültürünüzü anlık izleyin. Dedikodu ve mobbing gibi performansı düşüren “kültürel virüsleri” erkenden tespit edin.\nAdil Kararlar İçin Özel Ekran | Terfi, ücret ve prim kararlarını subjektiviteden kurtarın. | OKR, KPI, Aksiyonlar ve tüm içgörüler ile (Lider Kararı, Meydan Okuma, 360° Yetkinlik, 360° Değerler, 360° Nitelik/Beceri analizleriyle) % 100 objektif veriye dayalı kararlar alın.",
    '_myliba_home_final_cta_title' => 'Yüksek Performanslı Organizasyonlar için Bugün Başlayın.',
];

foreach ($home_meta as $key => $value) {
    update_post_meta($home->ID, $key, $value);
}

$software = get_page_by_path('tr/yazilim');
if (!$software instanceof WP_Post) {
    WP_CLI::error('Turkish software page was not found.');
}

$document = \Myliba\Core\PageContent\document($software->ID, 'software');
$approved = \Myliba\Core\PageContent\software_defaults();
$field_keys = [
    'hero_title_start',
    'hero_title_emphasis',
    'hero_title_end',
    'hero_lead',
    'modules_title',
    'workflow_title',
    'workflow_text',
    'why_text',
];

foreach ($field_keys as $key) {
    $document['fields'][$key] = $approved['fields'][$key];
}

foreach (['hero_proof', 'modules', 'workflow_steps', 'stats'] as $collection_key) {
    $existing = $document['collections'][$collection_key] ?? [];
    $replacement = $approved['collections'][$collection_key];

    foreach ($replacement as $index => &$row) {
        if (isset($existing[$index]['image']) && $existing[$index]['image'] !== '') {
            $row['image'] = $existing[$index]['image'];
        }
        if (isset($existing[$index]['image_alt']) && $existing[$index]['image_alt'] !== '') {
            $row['image_alt'] = $existing[$index]['image_alt'];
        }
    }
    unset($row);

    $document['collections'][$collection_key] = $replacement;
}

$encoded = wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
update_post_meta($software->ID, \Myliba\Core\PageContent\META_KEY, wp_slash($encoded));

WP_CLI::success('Approved homepage and software copy was written to WordPress.');
