<?php

if (!defined('ABSPATH') || !defined('WP_CLI') || !WP_CLI) {
    exit;
}

/**
 * Rebuild the English site from the currently published Turkish content.
 *
 * Run with:
 * wp eval-file wp-content/plugins/myliba-core/migrations/2026-08-20-rebuild-english.php
 */

$post_types = [
    'page',
    'post',
    'myliba_solution',
    'myliba_academy',
    'myliba_event',
    'myliba_ebook',
    'myliba_report',
    'myliba_landing',
];

$slug_map = [
    'page:tr' => 'en',
    'page:okr-kultur-akademisi' => 'okr-culture-academy',
    'page:kultur-analizi' => 'culture-analysis',
    'page:etik-danismanlik' => 'ethics-counsel',
    'page:etkinlikler' => 'events',
    'page:iletisim' => 'contact',
    'page:hikayemiz' => 'our-story',
    'page:sss' => 'faq',
    'page:guvenlik' => 'security',
    'page:gizlilik-politikasi' => 'privacy-policy',
    'page:yazilar' => 'blog',
    'page:cozumler' => 'solutions',
    'page:demo' => 'demo',
    'page:kvkk' => 'kvkk',
    'page:kullanim-sartlari' => 'terms-of-use',
    'page:yazilim' => 'software',
    'page:gelisim-merkezi' => 'development-center',
    'post:okr-rutinini-pratik-hale-getirmek' => 'practical-okr-operating-rhythm',
    'myliba_solution:kurumsal-girisim-programlari' => 'corporate-development-programs',
    'myliba_solution:simulasyonlar-ve-takim-koclugu' => 'simulations-and-team-coaching',
    'myliba_solution:danismanlik' => 'advisory-and-consulting',
    'myliba_solution:kultur-analizi' => 'culture-analysis-solution',
    'myliba_academy:icf-onayli-okr-kultur-koclugu' => 'icf-accredited-okr-culture-coaching',
    'myliba_academy:isbasi-liderlik-performans-gelisim' => 'on-the-job-leadership-performance-development',
    'myliba_academy:hedef-kulturel-donusum-danismanligi' => 'goal-cultural-transformation-advisory',
    'myliba_event:myliba-kultur-oturumu' => 'myliba-culture-session',
    'myliba_landing:okr-yazilimi' => 'okr-software',
    'myliba_landing:okr-yonetimi' => 'okr-management',
    'myliba_landing:performans-yonetimi' => 'performance-management',
    'myliba_landing:geri-bildirim-kulturu' => 'feedback-culture',
    'myliba_landing:kpi-ve-okr' => 'kpi-and-okr',
];

$title_map = [
    'page:tr' => 'Myliba EN',
    'page:okr-kultur-akademisi' => 'OKR & Culture Academy',
    'page:kultur-analizi' => 'Culture Analysis',
    'page:etik-danismanlik' => 'Ethics Hotline',
    'page:etkinlikler' => 'Events',
    'page:iletisim' => 'Contact',
    'page:hikayemiz' => 'Who We Are',
    'page:sss' => 'FAQ',
    'page:guvenlik' => 'Security',
    'page:gizlilik-politikasi' => 'Privacy Notice',
    'page:yazilar' => 'Articles',
    'page:cozumler' => 'Solutions',
    'page:demo' => 'Demo',
    'page:kvkk' => 'Privacy & GDPR',
    'page:kullanim-sartlari' => 'Terms of Use',
    'page:yazilim' => 'Myliba Software',
    'page:gelisim-merkezi' => 'Development Center',
    'post:okr-rutinini-pratik-hale-getirmek' => 'Making the OKR Rhythm Practical',
    'myliba_solution:kurumsal-girisim-programlari' => 'Corporate Development Programs',
    'myliba_solution:simulasyonlar-ve-takim-koclugu' => 'Simulations & Team Coaching',
    'myliba_solution:danismanlik' => 'Advisory & Consulting',
    'myliba_solution:kultur-analizi' => 'Culture Analysis',
    'myliba_academy:icf-onayli-okr-kultur-koclugu' => "The World's First and Only ICF-Accredited OKR & Culture Coaching Certification Program",
    'myliba_academy:isbasi-liderlik-performans-gelisim' => 'On-the-Job Leadership & Performance Development Program',
    'myliba_academy:hedef-kulturel-donusum-danismanligi' => 'Goal & Cultural Transformation Advisory',
    'myliba_event:myliba-kultur-oturumu' => 'Myliba Culture Session',
    'myliba_landing:okr-yazilimi' => 'OKR Software',
    'myliba_landing:okr-yonetimi' => 'OKR Management',
    'myliba_landing:performans-yonetimi' => 'Performance Management',
    'myliba_landing:geri-bildirim-kulturu' => 'Feedback Culture',
    'myliba_landing:kpi-ve-okr' => 'KPI and OKR',
];

// Preserve the best available English wording before removing every old EN record.
$english_snapshots = [];
foreach ($post_types as $post_type) {
    $items = get_posts([
        'post_type' => $post_type,
        'post_status' => ['publish', 'draft', 'pending', 'private', 'trash'],
        'posts_per_page' => -1,
        'meta_key' => '_myliba_language',
        'meta_value' => 'en',
        'suppress_filters' => true,
    ]);
    foreach ($items as $item) {
        $key = trim((string) get_post_meta($item->ID, '_myliba_translation_key', true));
        if ($key === '') {
            $key = $post_type . ':' . preg_replace('/__trashed$/', '', $item->post_name);
        } else {
            $key = $post_type . ':key:' . $key;
        }
        $candidate = [
            'title' => $item->post_title,
            'content' => $item->post_content,
            'excerpt' => $item->post_excerpt,
            'meta' => [],
        ];
        foreach (get_post_meta($item->ID) as $meta_key => $values) {
            $candidate['meta'][$meta_key] = maybe_unserialize($values[0] ?? '');
        }
        if (!isset($english_snapshots[$key])) {
            $english_snapshots[$key] = $candidate;
            continue;
        }
        foreach (['title', 'content', 'excerpt'] as $field) {
            if (strlen((string) $candidate[$field]) > strlen((string) $english_snapshots[$key][$field])) {
                $english_snapshots[$key][$field] = $candidate[$field];
            }
        }
        foreach ($candidate['meta'] as $meta_key => $value) {
            $current = $english_snapshots[$key]['meta'][$meta_key] ?? '';
            if (strlen(is_scalar($value) ? (string) $value : wp_json_encode($value)) > strlen(is_scalar($current) ? (string) $current : wp_json_encode($current))) {
                $english_snapshots[$key]['meta'][$meta_key] = $value;
            }
        }
    }
}

$phrase_map = [
    'Myliba Yazılım' => 'Myliba Software',
    'Veriyle Konuşan, Gelişim ve İnsan Odaklı Yazılım' => 'Data-Driven, Development- and People-Focused Software',
    'Yüksek Performanslı' => 'High-Performance',
    'Organizasyonlar Yaratmak' => 'Organizations',
    'için Geliştirildi' => 'Built to Create',
    'Formları tarihe gömün: % 100 objektif verilerle anlık performansı anlık yönetin. Kurumsal stratejiyi eyleme indiren ve performansı geliştiren bir kültür yaratmak için davranışlarda kararlılık gerekir. Myliba yazılım kurumiçi girişimciliği, sinerjik/çapraz ekipleri ve yeni nesil liderliği geliştiren bir alt yapıyı garanti etmenizi sağlar.' => 'Leave forms behind: manage performance in real time with 100% objective data. Turning corporate strategy into action and building a culture that improves performance requires consistency in behavior. Myliba provides the infrastructure to strengthen intrapreneurship, synergistic cross-functional teams, and next-generation leadership.',
    'Demo Talep Edin' => 'Request a Demo',
    'Modülleri Keşfedin' => 'Explore the Modules',
    'Canlı performans kültürü kuran ekipler' => 'Teams building a live performance culture',
    'Veriyle daha adil kararlar alan kurumların yanında.' => 'Supporting organizations that make fairer decisions with data.',
    'Farklı sektörlerden ekipler hedef, performans ve gelişim ritimlerini Myliba ile tek noktada buluşturuyor.' => 'Teams across industries bring goals, performance, and development rhythms together in Myliba.',
    'Dört güçlü odak' => 'Four powerful focus areas',
    'Adil Kararlar Verin' => 'Make Fair Decisions',
    'Terfi, ücret, prim ve liderlik kararlarını; OKR, KPI, Aksiyonlar, 360° analizler, kültür, bağlılık ve yapay zekâ içgörüleriyle destekleyin. Dedikodu, mobbing ve adaletsizlik gibi kültürel virüsleri erkenden tespit edin.' => 'Support promotion, compensation, bonus, and leadership decisions with OKRs, KPIs, actions, 360° analyses, culture, engagement, and AI-powered insights. Detect cultural risks such as gossip, bullying, and unfairness early.',
    'Kararlarınızı adil, şeffaf ve % 100 objektif verilere dayandırın.' => 'Base your decisions on fair, transparent, and 100% objective data.',
    'Tek ve Sürekli Bir Çalışma Döngüsü' => 'One Continuous Operating Rhythm',
    'Stratejiden Sonuçlara Kesintisiz Bir Yönetim Teknolojisi' => 'Seamless Management Technology from Strategy to Results',
    'Yıl sonunu beklemeden hedefleri, aksiyonları, gelişim odaklı geri-ileri bildirimleri ve risk sinyallerini tek bir çalışma ritminde yönetin.' => 'Manage goals, actions, development-focused feedback and feedforward, and risk signals in one operating rhythm without waiting for year-end.',
    'Neden Myliba?' => 'Why Myliba?',
    'Myliba Yazılım: Şirketinizin Verimliliğini Katlayan Stratejik İş Ortağı' => "Myliba Software: The Strategic Partner That Multiplies Your Company's Productivity",
    'Formülümüz' => 'Our Formula', 'Performans' => 'Performance', 'Potansiyel' => 'Potential', 'Müdahale' => 'Interference',
    'İnsanlara daha fazla güven, net bir odak ve gelişim alanı sunduğunuzda potansiyel performansa dönüşür. Myliba Yazılım bu anlayışı canlı verilere dönüştürür.' => 'Potential becomes performance when people are given greater trust, clear focus, and room to grow. Myliba Software turns this principle into live data.',
    'Merak Edilenler' => 'Frequently Asked Questions',
    'Myliba Yazılım Hakkında Sıkça Sorulan Sorular' => 'Frequently Asked Questions About Myliba Software',
    'Yeni nesil performans yönetimine geçerken bilmek isteyeceğiniz temel noktalar.' => 'The essentials you need to know when moving to next-generation performance management.',
    'Şirketinizin “Görünmez İşletim Sistemini” Güncelleme Vakti Geldi.' => "It Is Time to Upgrade Your Company's “Invisible Operating System.”",
    'Her organizasyonun performans yolculuğu farklıdır. İhtiyaçlarınıza özel kişiselleştirilmiş bir demo ile Myliba’nın şirketinizde nasıl değer yaratacağını birlikte keşfedelim.' => "Every organization's performance journey is different. Let us explore how Myliba can create value for your company through a personalized demo tailored to your needs.",
    'Kişiselleştirilmiş Demo Talep Edin' => 'Request a Personalized Demo', 'İletişime Geçin' => 'Contact Us',
    '/tr/demo/' => '/en/demo/', '/tr/iletisim/' => '/en/contact/',
    'Kurumsal ölçekte güvenli geçiş' => 'Secure enterprise-scale rollout', 'Sistemlerinize uyum sağlayan kurulum.' => 'Implementation that fits your systems.',
    'Organizasyon yapınız, hedef döngünüz, veri aktarımı ve entegrasyon ihtiyaçlarınız analiz edilir; Myliba’nın devreye alma süreci kurumunuza özel olarak planlanır.' => "We analyze your organizational structure, goal cycle, data migration, and integration needs, then plan Myliba's rollout specifically for your organization.",
    'Entegrasyon kapsamları' => 'Integration coverage', 'Canlı veri' => 'Live data', 'Adil karar ekranı' => 'Fair decision dashboard', 'İnsan odaklı gelişim' => 'People-centered development',
    'Çalışanlar' => 'Employees', 'İçgörüler' => 'Insights', 'Gelişen Potansiyel' => 'Emerging Potential', 'Yüksek Potansiyel' => 'High Potential', 'Geleceğin Liderleri' => 'Future Leaders', 'Güvenilir Oyuncu' => 'Reliable Contributor', 'Güçlü Performans' => 'Strong Performance', 'Yıldızlar' => 'Stars', 'Destek Gerekli' => 'Support Needed', 'Uzman Katkı' => 'Expert Contributor', 'Kritik Yetenek' => 'Critical Talent',
    'Strateji ve Hedef Yönetim Modülü' => 'Strategy and Goal Management Module', 'Performansı Geliştirme Modülü' => 'Performance Development Module', 'Sürekli Diyalog ve Kültür Yönetimi Modülleri' => 'Continuous Dialogue and Culture Management Modules', 'Adil Karar Modülü' => 'Fair Decision Module',
    'Hedefleri belirleyin' => 'Set goals', 'Anlık takip edin' => 'Track in real time', 'Sürekli gelişimi destekleyin' => 'Support continuous development', 'Veriye dayalı adil kararlar alın' => 'Make fair, data-driven decisions',
    'Tasarruf' => 'Savings', 'Gün Kazanç' => 'Days Gained', "Varan Daha Güçlü Hedefler" => 'Stronger Goals',
    'Myliba hangi şirketler için uygundur?' => 'Which companies is Myliba suitable for?', 'Myliba Yazılım, geleneksel performans sistemlerinden nasıl ayrılır?' => 'How does Myliba Software differ from traditional performance systems?', 'Performans değerlendirme formlarının yerine geçebilir mi?' => 'Can it replace performance appraisal forms?', 'Myliba Yazılım içinde hem OKR (Hedef Yönetimi) hem de geleneksel KPI takibi aynı anda yapılabilir mi?' => 'Can OKRs and traditional KPI tracking be used together in Myliba Software?', 'Myliba kültürel virüsleri ve riskleri nasıl tespit ediyor?' => 'How does Myliba detect cultural risks?', 'Kurulum ne kadar sürer?' => 'How long does implementation take?', 'Myliba Yazılım mevcut araçlarla nasıl entegre olur?' => 'How does Myliba Software integrate with existing tools?',
    'API ve Veri Bağlantıları' => 'API and Data Connections', 'Kurumunuza Özel Yapı' => 'A Structure Tailored to Your Organization', 'Kontrollü Devreye Alma' => 'Controlled Rollout',
    'Etik Hat ve\r\nEtik İhlal Bildirimi' => 'Ethics Hotline and\r\nEthics Violation Reporting', 'Güvenli ve Tarafsız' => 'Secure and Impartial', 'Etik Hat ve Etik İhlal Bildirimi' => 'Ethics Hotline and Ethics Violation Reporting',
    'Neden Myliba Etik Hat?' => 'Why the Myliba Ethics Hotline?', 'Güvenli, Şeffaf ve Adil Bir Kurum Kültürü İçin.' => 'For a Secure, Transparent, and Fair Corporate Culture.', 'Hizmet Kapsamı' => 'Service Scope', 'Çok Kanallı, 7/24 Kesintisiz Bildirim Altyapısı' => 'Multi-Channel, 24/7 Reporting Infrastructure', 'Etik Hattı Özellikleri' => 'Ethics Hotline Features',
    'Gizlilik ve Anonimlik' => 'Confidentiality and Anonymity', 'Bağımsızlık' => 'Independence', 'Yasal Uyum' => 'Legal Compliance', 'Çalışan Güveni' => 'Employee Trust',
    'Akademi programı' => 'Academy program', 'Öne Çıkan Program' => 'Featured Program', 'Liderlik Gelişim Programı' => 'Leadership Development Program', 'Kurumsal Danışmanlık' => 'Corporate Advisory',
    'Eylül 2026 Dönemi Kayıtları' => 'September 2026 Cohort Registration', 'Eylül 2026' => 'September 2026', 'Kurumunuza Özel Teklif Alın' => 'Request a Tailored Proposal', 'İhtiyacınıza Özel Teklif Alın' => 'Request a Proposal Tailored to Your Needs',
    'Gerçek Kurum Hedefleri' => 'Real Company Goals', 'Canlı Öğrenme' => 'Live Learning', 'Ölçülebilir Gelişim' => 'Measurable Development', 'Uçtan Uca Dönüşüm' => 'End-to-End Transformation', 'Ölçülebilir Kültür' => 'Measurable Culture', 'Deneyimli Uzmanlar' => 'Experienced Experts',
    'Strateji ve hedef yönetimi' => 'Strategy and goal management', 'Liderlik ve kültür dönüşümü' => 'Leadership and cultural transformation', 'Performans ve geri bildirim sistemleri' => 'Performance and feedback systems',
    'Temiz hedef akisi' => 'Clear goal flow', 'Daha iyi takip' => 'Better tracking', 'Demo ile hizli degerlendirme' => 'Fast evaluation with a demo', 'Demo iste' => 'Request a demo',
];

$translate_value = static function ($value) use (&$translate_value, $phrase_map) {
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = $translate_value($item);
        }
        return $value;
    }
    if (!is_string($value) || $value === '') {
        return $value;
    }
    if (isset($phrase_map[$value])) {
        return $phrase_map[$value];
    }
    return strtr($value, $phrase_map);
};

// Retire all old English content and legacy duplicate pages recoverably.
$removed = 0;
foreach ($post_types as $post_type) {
    $items = get_posts([
        'post_type' => $post_type,
        'post_status' => ['publish', 'draft', 'pending', 'private', 'future', 'trash'],
        'posts_per_page' => -1,
        'suppress_filters' => true,
    ]);
    foreach ($items as $item) {
        $language = (string) get_post_meta($item->ID, '_myliba_language', true);
        if ($language === 'en' && $item->post_status !== 'trash') {
            wp_trash_post($item->ID);
            $removed++;
        }
    }
}
foreach (['okr-yazilimi', 'okr-yonetimi', 'performans-yonetimi', 'geri-bildirim-kulturu', 'kpi-ve-okr'] as $legacy_slug) {
    $legacy = get_page_by_path($legacy_slug, OBJECT, 'page');
    if ($legacy instanceof WP_Post && get_post_status($legacy) !== 'trash') {
        wp_trash_post($legacy->ID);
        $removed++;
    }
}

$turkish = get_posts([
    'post_type' => $post_types,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_key' => '_myliba_language',
    'meta_value' => 'tr',
    'orderby' => ['post_type' => 'ASC', 'menu_order' => 'ASC', 'ID' => 'ASC'],
    'suppress_filters' => true,
]);
usort($turkish, static fn (WP_Post $a, WP_Post $b): int => ($a->post_parent <=> $b->post_parent) ?: ($a->ID <=> $b->ID));

$id_map = [];
$created = 0;
foreach ($turkish as $source) {
    $lookup = $source->post_type . ':' . $source->post_name;
    if (!isset($slug_map[$lookup])) {
        WP_CLI::warning('Skipped unmapped Turkish record: ' . $lookup);
        continue;
    }
    $translation_key = trim((string) get_post_meta($source->ID, '_myliba_translation_key', true));
    if ($translation_key === '') {
        $translation_key = $source->post_type . '-' . $source->post_name;
        update_post_meta($source->ID, '_myliba_translation_key', $translation_key);
    }
    $snapshot_key = $source->post_type . ':key:' . $translation_key;
    $snapshot = $english_snapshots[$snapshot_key] ?? $english_snapshots[$source->post_type . ':' . $slug_map[$lookup]] ?? [];
    $postarr = [
        'post_type' => $source->post_type,
        'post_status' => 'publish',
        'post_title' => $title_map[$lookup] ?? ($snapshot['title'] ?? $source->post_title),
        'post_name' => $slug_map[$lookup],
        'post_content' => $snapshot['content'] ?? $translate_value($source->post_content),
        'post_excerpt' => $snapshot['excerpt'] ?? $translate_value($source->post_excerpt),
        'post_parent' => $source->post_parent ? ($id_map[$source->post_parent] ?? 0) : 0,
        'menu_order' => $source->menu_order,
        'comment_status' => $source->comment_status,
        'ping_status' => $source->ping_status,
    ];
    if ($postarr['post_content'] === '' && $source->post_content !== '') {
        $postarr['post_content'] = $translate_value($source->post_content);
    }
    if ($postarr['post_excerpt'] === '' && $source->post_excerpt !== '') {
        $postarr['post_excerpt'] = $translate_value($source->post_excerpt);
    }
    $new_id = wp_insert_post(wp_slash($postarr), true);
    if (is_wp_error($new_id)) {
        WP_CLI::error($new_id->get_error_message());
    }
    $id_map[$source->ID] = (int) $new_id;

    foreach (get_post_meta($source->ID) as $meta_key => $values) {
        if (in_array($meta_key, ['_edit_lock', '_edit_last', '_wp_old_slug'], true)) {
            continue;
        }
        $source_value = maybe_unserialize($values[0] ?? '');
        $value = $snapshot['meta'][$meta_key] ?? $translate_value($source_value);
        if ($meta_key === '_myliba_language') {
            $value = 'en';
        } elseif ($meta_key === '_myliba_translation_key') {
            $value = $translation_key;
        } elseif (str_ends_with($meta_key, '_url') || $meta_key === '_myliba_redirect_url') {
            $value = str_replace(['/tr/', '/tr'], ['/en/', '/en'], (string) $value);
        }
        update_post_meta((int) $new_id, $meta_key, $value);
    }
    update_post_meta((int) $new_id, '_myliba_language', 'en');
    update_post_meta((int) $new_id, '_myliba_translation_key', $translation_key);

    // Rebuild structured content from the Turkish document, then translate/overlay it.
    $schema = \Myliba\Core\PageContent\schema_for_post($source);
    if ($schema !== null) {
        $document = \Myliba\Core\PageContent\document($source->ID, $schema);
        if (is_array($document)) {
            $document = $translate_value($document);
            $old_document = isset($snapshot['meta']['_myliba_page_content']) ? json_decode((string) $snapshot['meta']['_myliba_page_content'], true) : null;
            if (is_array($old_document)) {
                $document['fields'] = array_replace($document['fields'] ?? [], $old_document['fields'] ?? []);
                foreach ($old_document['collections'] ?? [] as $collection_key => $rows) {
                    if (isset($document['collections'][$collection_key]) && count((array) $rows) === count((array) $document['collections'][$collection_key])) {
                        $document['collections'][$collection_key] = $rows;
                    }
                }
            }
            update_post_meta((int) $new_id, '_myliba_page_content', wp_slash(wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
    }

    $terms = wp_get_object_terms($source->ID, get_object_taxonomies($source->post_type), ['fields' => 'all']);
    foreach ($terms as $term) {
        if ($term instanceof WP_Term) {
            wp_set_object_terms((int) $new_id, [(int) $term->term_id], $term->taxonomy, true);
        }
    }
    if (function_exists('pll_set_post_language')) {
        pll_set_post_language((int) $new_id, 'en');
        if (function_exists('pll_save_post_translations')) {
            pll_save_post_translations(['tr' => $source->ID, 'en' => (int) $new_id]);
        }
    }
    $created++;
}

// Exact translations for fields that had no usable former English counterpart.
$manual = [
    'myliba_academy:icf-accredited-okr-culture-coaching' => [
        '_myliba_hero_subtitle' => 'The first and only program in Türkiye and worldwide to combine OKR, KPI, CFR, and coaching in a 40-CCE-accredited structure. Gain hands-on experience in Myliba software and complete every session live.',
        '_myliba_academy_program_benefits' => "Internationally recognized certificate, digital badge, and 40 ICF CCE hours\nThe Myliba model bringing OKR, KPI, CFR, and culture together\nThe capability to build sustainable OKR and performance systems that do not depend on individuals\nLive sessions, real on-the-job goals, and hands-on learning on the Myliba platform\nSupervision and mastery process\nLifetime continuous development, current content, and networking for graduate leaders",
        '_myliba_academy_program_badges' => "40 CCE\nLive Sessions\nHands-on Training\nICF Accredited\nDigital Certificate\nSupervision",
        '_myliba_academy_certificate_info' => '40 ICF CCE hours, digital certificate, and Myliba digital badge',
        '_myliba_seo_title' => 'ICF-Accredited OKR & Culture Coaching | Myliba',
    ],
    'myliba_academy:on-the-job-leadership-performance-development' => [
        '_myliba_hero_subtitle' => 'Build a high-performance culture with your leaders and teams. Develop the skills required for cultural transformation while uncovering inspiring goals.',
        '_myliba_academy_program_benefits' => "Your leaders work with the company's real goals, not theoretical scenarios.\nBuild a culture of real-time recognition, feedback, and feedforward in the daily workflow, eliminating year-end stress.\nGain experience making and managing fair decisions with indicators that measure culture and performance.",
        '_myliba_academy_program_badges' => "Real Company Goals\nLive Learning\nMeasurable Development",
        '_myliba_seo_title' => 'On-the-Job Leadership & Performance Program | Myliba',
    ],
    'myliba_academy:goal-cultural-transformation-advisory' => [
        '_myliba_hero_subtitle' => 'Transform your culture with inspiring goals. Build an end-to-end, people-centered, next-generation high-performance culture with experts experienced in leading goal and cultural transformation journeys across industries and organizations.',
        '_myliba_academy_program_benefits' => "Strategy and goal management\nLeadership and cultural transformation\nPerformance and feedback systems",
        '_myliba_academy_program_badges' => "End-to-End Transformation\nMeasurable Culture\nExperienced Experts",
        '_myliba_seo_title' => 'Goal & Cultural Transformation Advisory | Myliba',
    ],
];
foreach ($manual as $lookup => $meta) {
    [$type, $slug] = explode(':', $lookup, 2);
    $post = get_page_by_path($slug, OBJECT, $type);
    if (!$post instanceof WP_Post) {
        continue;
    }
    foreach ($meta as $key => $value) {
        update_post_meta($post->ID, $key, $value);
        if ($key === '_myliba_hero_subtitle') {
            wp_update_post(['ID' => $post->ID, 'post_content' => '<p>' . esc_html($value) . '</p>', 'post_excerpt' => $value]);
        }
    }
}

$generic_content = [
    'events' => '<p>Explore upcoming webinars, workshops, and community sessions.</p>',
    'blog' => '<p>Explore Myliba articles, guides, and practical notes.</p>',
    'solutions' => '<p>Explore Myliba solutions for measurable, sustainable organizational development.</p>',
    'terms-of-use' => '<p>Review the terms governing the use of the Myliba website and services.</p>',
];
foreach ($generic_content as $slug => $content) {
    $page = get_page_by_path('en/' . $slug);
    if ($page instanceof WP_Post) {
        wp_update_post(['ID' => $page->ID, 'post_content' => $content]);
    }
}

$options = get_option('myliba_options', []);
$options = is_array($options) ? $options : [];
$options['primary_cta_url_en'] = '/en/contact/';
$options['demo_url_en'] = '/en/demo/';
$options['promo_url_en'] = '/en/okr-culture-academy/';
update_option('myliba_options', $options);

// Translate the legacy Culture Analysis HTML while retaining its markup and layout.
$culture = get_page_by_path('en/culture-analysis');
$culture_source = get_page_by_path('tr/kultur-analizi');
if ($culture instanceof WP_Post && $culture_source instanceof WP_Post) {
    $replacements = [
        'Kültür Analizi ve Gelişim Planı' => 'Culture Analysis and Development Plan', 'Neden Kültür Analizi?' => 'Why Culture Analysis?', 'Ne Ölçüyoruz?' => 'What Do We Measure?', 'Myliba’nın Üç Aşamalı Hizmet Süreci' => "Myliba's Three-Stage Service Process", '1. Anket Aşaması' => '1. Survey Stage', '2. Saha Araştırması' => '2. Field Research', '3. Gelişim Planı' => '3. Development Plan', 'Bizimle İletişime Geçin' => 'Contact Us', 'Bültenimize' => 'Subscribe to Our', 'Abone Olun' => 'Newsletter',
        'Mevcut kültürünüzün güçlü ve zayıf yönlerini keşfedin.' => "Discover your culture's strengths and development areas.", 'Çalışan bağlılığı ve iş performansını artırın.' => 'Increase employee engagement and business performance.', 'Kurum içi sinerjiyi ve iletişimi güçlendirin.' => 'Strengthen organizational synergy and communication.', 'Stratejik dönüşüm için veriye dayalı içgörüler edinin.' => 'Gain data-driven insights for strategic transformation.',
        'Çalışan tavsiye skoru' => 'Employee recommendation score', 'Departmanlar arası kültürel uyum' => 'Cultural alignment across departments', 'Çalışanların işe olan isteği' => "Employees' willingness to contribute", 'Kuruma, işe ve lidere bağlılık' => 'Engagement with the organization, work, and leaders',
        'Kültür Analizi' => 'Culture Analysis', 'Bağlılık Analizi' => 'Engagement Analysis', 'İsteklilik Analizi' => 'Willingness Analysis', 'Odak grup görüşmeleri' => 'Focus group interviews', 'Yönetici birebir görüşmeleri' => 'One-on-one executive interviews', 'Doküman analizi' => 'Document analysis', '(Opsiyonel) Gözlem' => '(Optional) Observation', 'Detaylı Kültür Analizi Raporu' => 'Detailed Culture Analysis Report', 'Öncelikli gelişim alanlarının belirlenmesi' => 'Identification of priority development areas', 'OKR ve KPI uyumlu hedef setleri' => 'Goal sets aligned with OKRs and KPIs', 'Uygulama adımları ve zaman çizelgesi' => 'Implementation steps and timeline', 'Eğitim, iletişim ve liderlik gelişim önerileri' => 'Training, communication, and leadership development recommendations', '1 yıl sonra tekrar ölçüm' => 'Follow-up measurement after one year',
        'Hemen bültenimize abone olun, en yeni haber ve etkinlikleri ilk siz keşfedin!' => 'Subscribe to our newsletter and be the first to discover our latest news and events!',
    ];
    $content = strtr($culture_source->post_content, $replacements);
    $content = preg_replace('/<div class="wpcf7[^>]*lang="tr-TR"[^>]*>.*?<\/div>\s*<\/aside>/s', '</aside>', $content) ?: $content;
    wp_update_post(['ID' => $culture->ID, 'post_content' => $content]);
}

flush_rewrite_rules();

WP_CLI::success(sprintf('English site rebuilt from Turkish sources: %d records created, %d obsolete records moved to Trash.', $created, $removed));
