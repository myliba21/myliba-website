<?php
/**
 * Strengthens the bilingual trainer directory and individual expert profiles
 * for OKR, performance-management, and cultural-transformation discovery.
 *
 * Run with:
 * wp eval-file wp-content/plugins/myliba-core/migrations/2026-09-19-trainer-search-visibility.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$directories = [
    'tr/egitmenlerimiz' => [
        'content' => '<h2>OKR, performans yönetimi ve kültürel dönüşüm uzmanları</h2><p>Myliba’nın OKR danışmanları ve performans uzmanları; stratejik önceliklerin hedeflere dönüştürülmesi, OKR ve KPI sistemlerinin birlikte yönetilmesi, liderlik gelişimi ve yüksek performans kültürünün günlük işleyişe yerleşmesi için kurumlarla çalışır.</p><h3>Hangi alanlarda destek oluyoruz?</h3><ul><li>OKR sistemi tasarımı, hedef yazımı ve stratejik hizalanma</li><li>Performans yönetimi, KPI, CFR ve sürekli gelişim rutinleri</li><li>Liderlik, yönetici koçluğu ve takım koçluğu</li><li>Kültürel dönüşüm, çalışan bağlılığı ve yüksek performans kültürü</li></ul><p>İhtiyacınıza uygun uzmanı inceleyebilir; kurumsal dönüşüm için <a href="/tr/cozumler/danismanlik/">Myliba danışmanlık yaklaşımını</a>, uygulamalı gelişim yolculukları için <a href="/tr/okr-kultur-akademisi/">OKR ve Kültür Akademisi’ni</a> keşfedebilirsiniz.</p>',
        'excerpt' => 'Myliba’nın OKR, performans yönetimi, hedef sistemleri, liderlik ve kültürel dönüşüm alanlarında çalışan danışman, koç ve eğitmenleriyle tanışın.',
        'meta' => [
            '_myliba_hero_title' => 'OKR Danışmanları ve Performans Yönetimi Uzmanları',
            '_myliba_hero_subtitle' => 'Myliba’nın OKR, performans yönetimi, hedef sistemleri, liderlik ve kültürel dönüşüm alanlarında çalışan danışman, koç ve eğitmenleriyle tanışın.',
            '_myliba_eyebrow' => 'OKR ve performans uzmanları',
            '_myliba_trainers_directory_title' => 'Deneyimli OKR ve performans danışmanlarımızla tanışın.',
            '_myliba_seo_title' => 'OKR Danışmanları ve Performans Uzmanları | Myliba',
            '_myliba_seo_description' => 'OKR, performans yönetimi, hedef sistemleri, liderlik ve kültürel dönüşüm alanlarında uzman Myliba danışmanları, koçları ve eğitmenleriyle tanışın.',
        ],
    ],
    'en/our-trainers' => [
        'content' => '<h2>Experts in OKRs, performance management, and cultural transformation</h2><p>Myliba’s OKR consultants and performance management experts work with organizations to translate strategic priorities into goals, manage OKRs and KPIs together, develop leaders, and embed a high-performance culture into everyday work.</p><h3>How we support organizations</h3><ul><li>OKR system design, goal setting, and strategic alignment</li><li>Performance management, KPI, CFR, and continuous development routines</li><li>Leadership development, executive coaching, and team coaching</li><li>Cultural transformation, employee engagement, and high-performance culture</li></ul><p>Explore the expert who best matches your needs, learn about <a href="/en/solutions/advisory-and-consulting/">Myliba strategic advisory and consulting</a>, or discover hands-on development journeys at the <a href="/en/okr-culture-academy/">OKR &amp; Culture Academy</a>.</p>',
        'excerpt' => 'Meet Myliba consultants, coaches, and trainers specializing in OKRs, performance management, leadership, goal systems, and cultural transformation.',
        'meta' => [
            '_myliba_hero_title' => 'OKR Consultants and Performance Management Experts',
            '_myliba_hero_subtitle' => 'Meet Myliba consultants, coaches, and trainers specializing in OKRs, performance management, leadership, goal systems, and cultural transformation.',
            '_myliba_eyebrow' => 'OKR & performance experts',
            '_myliba_trainers_directory_title' => 'Meet our experienced OKR and performance consultants.',
            '_myliba_seo_title' => 'OKR Consultants & Performance Experts | Myliba',
            '_myliba_seo_description' => 'Meet Myliba consultants, coaches, and trainers specializing in OKRs, performance management, leadership, goal systems, and cultural transformation.',
        ],
    ],
];

foreach ($directories as $path => $data) {
    $page = get_page_by_path($path);
    if (!$page instanceof WP_Post) {
        WP_CLI::warning("Directory page not found: {$path}");
        continue;
    }

    wp_update_post([
        'ID' => $page->ID,
        'post_content' => $data['content'],
        'post_excerpt' => $data['excerpt'],
    ]);
    foreach ($data['meta'] as $key => $value) {
        update_post_meta($page->ID, $key, $value);
    }
}

$profiles = [
    'tr' => [
        'trainer-dilek-mete' => [
            'name' => 'Dilek Mete',
            'excerpt' => 'Dilek Mete; OKR, stratejik hizalanma, liderlik ve kültürel dönüşüm alanlarında çalışan kültürel dönüşüm danışmanı, yönetici koçu ve OKR koçudur.',
            'title' => 'Dilek Mete | OKR ve Kültürel Dönüşüm Danışmanı',
            'description' => 'Dilek Mete’nin OKR, stratejik hizalanma, liderlik, yönetici koçluğu ve kültürel dönüşüm alanlarındaki uzmanlığını ve çalışmalarını keşfedin.',
        ],
        'trainer-aysel-eker' => [
            'name' => 'Aysel Eker',
            'excerpt' => 'Aysel Eker; OKR, performans yönetimi, liderlik gelişimi ve takım koçluğu alanlarında çalışan PCC yönetici koçu ve OKR koçudur.',
            'title' => 'Aysel Eker | OKR ve Performans Yönetimi Koçu',
            'description' => 'Aysel Eker’in OKR, performans yönetimi, liderlik gelişimi, yönetici koçluğu ve takım koçluğu alanlarındaki uzmanlığını keşfedin.',
        ],
        'trainer-huri-sankur' => [
            'name' => 'Huri Şankur',
            'excerpt' => 'Huri Şankur; OKR, insan ve kültür, hedef hizalanması ve çalışan deneyimi alanlarında çalışan İnsan ve Kültür Danışmanı ve OKR koçudur.',
            'title' => 'Huri Şankur | OKR ve İnsan & Kültür Danışmanı',
            'description' => 'Huri Şankur’un OKR, insan ve kültür, hedef hizalanması, çalışan deneyimi ve kültürel dönüşüm alanlarındaki uzmanlığını keşfedin.',
        ],
    ],
    'en' => [
        'trainer-dilek-mete' => [
            'name' => 'Dilek Mete',
            'excerpt' => 'Dilek Mete is an OKR coach, executive coach, and cultural transformation consultant specializing in strategic alignment, leadership, and agile teams.',
            'title' => 'Dilek Mete | OKR & Cultural Transformation Consultant',
            'description' => 'Explore Dilek Mete’s expertise in OKRs, strategic alignment, executive coaching, leadership, agile teams, and cultural transformation.',
        ],
        'trainer-aysel-eker' => [
            'name' => 'Aysel Eker',
            'excerpt' => 'Aysel Eker is a PCC executive coach and OKR coach specializing in performance management, leadership development, goal clarity, and team coaching.',
            'title' => 'Aysel Eker | OKR & Performance Management Coach',
            'description' => 'Explore Aysel Eker’s expertise in OKRs, performance management, leadership development, executive coaching, and team coaching.',
        ],
        'trainer-huri-sankur' => [
            'name' => 'Huri Şankur',
            'excerpt' => 'Huri Şankur is a People & Culture Consultant and OKR Coach specializing in goal alignment, employee experience, and cultural transformation.',
            'title' => 'Huri Şankur | OKR & People and Culture Consultant',
            'description' => 'Explore Huri Şankur’s expertise in OKRs, people and culture, goal alignment, employee experience, and cultural transformation.',
        ],
    ],
];

foreach ($profiles as $language => $people) {
    foreach ($people as $translation_key => $data) {
        $matches = get_posts([
            'post_type' => 'myliba_team',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'meta_query' => [
                ['key' => '_myliba_language', 'value' => $language],
                ['key' => '_myliba_translation_key', 'value' => $translation_key],
            ],
        ]);
        if (!$matches) {
            WP_CLI::warning("Trainer not found: {$language}/{$translation_key}");
            continue;
        }

        $person_id = (int) $matches[0]->ID;
        wp_update_post([
            'ID' => $person_id,
            'post_title' => $data['name'],
            'post_excerpt' => $data['excerpt'],
        ]);
        update_post_meta($person_id, '_myliba_seo_title', $data['title']);
        update_post_meta($person_id, '_myliba_seo_description', $data['description']);
    }
}

WP_CLI::success('Trainer directory copy, profile snippets, and expert-focused SEO metadata updated.');
