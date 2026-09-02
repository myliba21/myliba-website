<?php
/**
 * Adds the About mega-menu destinations, trainer records, and approved
 * consulting copy to an existing Myliba WordPress database.
 *
 * Run with:
 * wp eval-file wp-content/plugins/myliba-core/migrations/2026-09-02-about-trainers-consulting.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$tr_home = get_page_by_path('tr');
$trainers_page = get_page_by_path('tr/egitmenlerimiz');
$trainers_page_data = [
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_title' => 'Eğitmenlerimiz',
    'post_name' => 'egitmenlerimiz',
    'post_parent' => $tr_home instanceof WP_Post ? $tr_home->ID : 0,
    'post_content' => '<p>Myliba eğitmen ve danışmanları; strateji, kültür, liderlik, OKR ve yüksek performans alanlarındaki deneyimlerini kurumların gerçek iş gündemleriyle buluşturur.</p>',
    'post_excerpt' => 'Stratejiyi, kültürü ve yüksek performansı günlük işin içine taşıyan eğitmen, koç ve danışmanlarımızla tanışın.',
];

$trainers_page_id = $trainers_page instanceof WP_Post
    ? $trainers_page->ID
    : wp_insert_post($trainers_page_data, true);
if (is_wp_error($trainers_page_id)) {
    WP_CLI::error($trainers_page_id->get_error_message());
}
$page_meta_defaults = [
    '_myliba_language' => 'tr',
    '_myliba_translation_key' => 'trainers',
    '_myliba_hero_title' => 'Eğitmenlerimiz',
    '_myliba_hero_subtitle' => 'Stratejiyi, kültürü ve yüksek performansı günlük işin içine taşıyan eğitmen, koç ve danışmanlarımızla tanışın.',
    '_myliba_eyebrow' => 'Myliba’nın arkasındaki insanlar',
    '_myliba_trainers_directory_eyebrow' => 'Ekibimiz',
    '_myliba_trainers_directory_title' => 'Deneyimli uygulayıcılarla gelişin.',
    '_myliba_trainers_card_kicker' => 'Eğitmen & Danışman',
    '_myliba_trainers_card_overlay_label' => 'Profili incele',
    '_myliba_trainers_card_detail_label' => 'Detaylı profili incele',
    '_myliba_trainers_card_aria_template' => '{name} profilini inceleyin',
    '_myliba_trainers_skills_label' => 'Uzmanlık alanları',
    '_myliba_trainers_empty_text' => 'Eğitmen profilleri yakında yayınlanacak.',
    '_myliba_trainers_profile_back_label' => 'Tüm eğitmenler',
    '_myliba_trainers_profile_kicker' => 'Eğitmen & Danışman',
    '_myliba_trainers_profile_about_eyebrow' => 'Hakkında',
    '_myliba_trainers_profile_about_title' => '{name} hakkında',
    '_myliba_trainers_profile_website_label' => 'Kişisel web sitesi',
    '_myliba_trainers_profile_links_label' => 'Web sitesi ve sosyal medya',
    '_myliba_trainers_related_eyebrow' => 'Ekibimiz',
    '_myliba_trainers_related_title' => 'Diğer uzmanlarımızla tanışın.',
    '_myliba_trainers_related_limit' => '3',
    '_myliba_seo_title' => 'Eğitmenlerimiz ve Danışmanlarımız | Myliba',
    '_myliba_seo_description' => 'Myliba’nın strateji, kültür, liderlik, OKR ve yüksek performans alanlarında çalışan eğitmen, koç ve danışmanlarıyla tanışın.',
];
foreach ($page_meta_defaults as $meta_key => $meta_value) {
    if ((string) get_post_meta($trainers_page_id, $meta_key, true) === '') {
        update_post_meta($trainers_page_id, $meta_key, $meta_value);
    }
}

$en_home = get_page_by_path('en');
$trainers_page_en = get_page_by_path('en/our-trainers');
$trainers_page_en_data = [
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_title' => 'Our Trainers',
    'post_name' => 'our-trainers',
    'post_parent' => $en_home instanceof WP_Post ? $en_home->ID : 0,
    'post_content' => '<p>Myliba trainers and consultants bring their experience in strategy, culture, leadership, OKRs, and high performance into the real business priorities of organizations.</p>',
    'post_excerpt' => 'Meet our trainers, coaches, and consultants who bring strategy, culture, and high performance into daily work.',
];

$trainers_page_en_id = $trainers_page_en instanceof WP_Post
    ? $trainers_page_en->ID
    : wp_insert_post($trainers_page_en_data, true);
if (is_wp_error($trainers_page_en_id)) {
    WP_CLI::error($trainers_page_en_id->get_error_message());
}
$page_en_meta_defaults = [
    '_myliba_language' => 'en',
    '_myliba_translation_key' => 'trainers',
    '_myliba_hero_title' => 'Our Trainers',
    '_myliba_hero_subtitle' => 'Meet our trainers, coaches, and consultants who bring strategy, culture, and high performance into daily work.',
    '_myliba_eyebrow' => 'The people behind Myliba',
    '_myliba_trainers_directory_eyebrow' => 'Our team',
    '_myliba_trainers_directory_title' => 'Learn with experienced practitioners.',
    '_myliba_trainers_card_kicker' => 'Trainer & Consultant',
    '_myliba_trainers_card_overlay_label' => 'View profile',
    '_myliba_trainers_card_detail_label' => 'View profile',
    '_myliba_trainers_card_aria_template' => 'View {name} profile',
    '_myliba_trainers_skills_label' => 'Areas of expertise',
    '_myliba_trainers_empty_text' => 'Trainer profiles will be published soon.',
    '_myliba_trainers_profile_back_label' => 'All trainers',
    '_myliba_trainers_profile_kicker' => 'Trainer & Consultant',
    '_myliba_trainers_profile_about_eyebrow' => 'About',
    '_myliba_trainers_profile_about_title' => 'About {name}',
    '_myliba_trainers_profile_website_label' => 'Personal website',
    '_myliba_trainers_profile_links_label' => 'Website and social media',
    '_myliba_trainers_related_eyebrow' => 'Our team',
    '_myliba_trainers_related_title' => 'Meet other experts.',
    '_myliba_trainers_related_limit' => '3',
    '_myliba_seo_title' => 'Our Trainers and Consultants | Myliba',
    '_myliba_seo_description' => 'Meet Myliba trainers, coaches, and consultants specializing in strategy, culture, leadership, OKRs, and high performance.',
];
foreach ($page_en_meta_defaults as $meta_key => $meta_value) {
    if ((string) get_post_meta($trainers_page_en_id, $meta_key, true) === '') {
        update_post_meta($trainers_page_en_id, $meta_key, $meta_value);
    }
}

$people = [
    [
        'name' => 'Dilek Mete',
        'translation_key' => 'trainer-dilek-mete',
        'slug' => 'dilek-mete-tr',
        'headline' => 'Stratejiyi Eyleme İndiren, Otonom ve Çevik Ekip Mimarı',
        'role' => 'Kültürel Dönüşüm Danışmanı · Yönetici Koçu · OKR Koçu · Yazar · Konuşmacı',
        'bio' => '<p>Dilek Mete, stratejiyi eyleme indiren, otonom ve çevik ekipler geliştiren kültürel dönüşüm danışmanı, yönetici koçu ve OKR koçudur.</p><p>Stratejinin üst yönetim gündeminde kalmaması; tüm organizasyon tarafından anlaşılması, sahiplenilmesi ve sonuç üreten davranışlara dönüşmesi için şirketlerle sahada çalışır. Liderleri ve ekipleri iş başında geliştirerek hedeflerin, iş birliğinin ve yüksek performans kültürünün kalıcı hâle gelmesini sağlar.</p>',
        'website' => 'https://www.dilekmete.com/',
        'website_label' => 'Dilek Mete hakkında daha fazlası',
        'order' => 10,
    ],
    [
        'name' => 'Aysel Eker',
        'translation_key' => 'trainer-aysel-eker',
        'slug' => 'aysel-eker-tr',
        'headline' => 'Hedefleri, İnsanları ve Kültürü Yüksek Performansta Buluşturan Koç',
        'role' => 'Yönetici Koçu (PCC) · OKR Koçu · Takım Koçu',
        'bio' => '<p>İş hayatında koçluk, OKR ve kültür odağında çalışan Aysel Eker; kurumlarda hedef netliği, liderlik gelişimi, takım performansı ve yüksek performans kültürünün geliştirilmesine yönelik çalışmalar yürütmektedir.</p><p>Özellikle OKR’nin yalnızca bir hedef belirleme sistemi olarak değil, çalışanların hedeflerle bağ kurmasını, önceliklerini netleştirmesini ve kurum içinde ortak bir çalışma kültürü oluşturmasını sağlayan bir yönetim yaklaşımı olarak ele alınması üzerine çalışmaktadır.</p><p>Myliba Yazılım ortağı, Myliba Akademi kurucu ortağı ve eğitmeni olan Aysel Eker, OKR, koçluk ve insan odaklı yönetim yaklaşımlarını bir arada ele alarak kurumlarda sürdürülebilir yüksek performans kültürünü desteklemektedir. Geliştirdiği “Hizalanmış Yapısal Kurumsal Koçluk Modeli” ile koçluk çalışmalarında birey ve sistemi birlikte ele alan özgün bir yaklaşım sunmaktadır.</p>',
        'website' => '',
        'website_label' => '',
        'order' => 20,
    ],
    [
        'name' => 'Huri Şankur',
        'legacy_name' => 'Huri Sankur',
        'translation_key' => 'trainer-huri-sankur',
        'slug' => 'huri-sankur-tr',
        'headline' => 'İnsan, Kültür ve Hedefler Arasındaki Bağı Güçlendiren Danışman',
        'role' => 'İnsan ve Kültür Danışmanı · OKR Koçu',
        'bio' => '<p>Huri Şankur, kurumlarda insan, kültür ve hedefler arasındaki bağı güçlendirmeye odaklanan İnsan ve Kültür Danışmanı ve OKR Koçudur.</p><p>Stratejinin çalışanlar tarafından anlaşılması ve sahiplenilmesi, hedeflerin ortak bir yön etrafında şekillenmesi ve bu sürecin kurum kültürüne yerleşmesi için ekiplerle çalışır. İnsan odaklı yönetim anlayışının, günlük iş yapış biçimlerine ve ekiplerin çalışma deneyimine yansımasına odaklanır.</p><p>Doktora çalışmalarını da sürdüren Huri Şankur, akademik çalışmalarından beslenen perspektifini uygulama deneyimiyle bir araya getirerek kurumların insan ve kültür odaklı dönüşüm süreçlerine katkı sunmaktadır.</p>',
        'website' => 'https://www.hurisankur.com/',
        'website_label' => 'Huri Şankur hakkında daha fazlası',
        'order' => 30,
    ],
];

$trainer_ids = [];
foreach ($people as $person) {
    $matches = get_posts([
        'post_type' => 'myliba_team',
        'post_status' => ['publish', 'draft', 'private', 'trash'],
        'posts_per_page' => 1,
        'title' => $person['name'],
        'meta_key' => '_myliba_language',
        'meta_value' => 'tr',
    ]);
    if (!empty($matches)) {
        // Preserve every admin edit, including an intentional move to Trash.
        $person_id = $matches[0]->ID;
    } else {
        $person_id = wp_insert_post([
            'post_type' => 'myliba_team',
            'post_status' => 'publish',
            'post_title' => $person['name'],
            'post_name' => $person['slug'],
            'post_content' => $person['bio'],
            'post_excerpt' => wp_trim_words(wp_strip_all_tags($person['bio']), 28, '…'),
        ], true);
    }
    if (is_wp_error($person_id)) {
        WP_CLI::warning($person_id->get_error_message());
        continue;
    }
    $saved_person = get_post($person_id);
    if ($saved_person instanceof WP_Post && isset($person['legacy_name']) && $saved_person->post_title === $person['legacy_name']) {
        wp_update_post(['ID' => $person_id, 'post_title' => $person['name']]);
    }
    $person_meta_defaults = [
        '_myliba_language' => 'tr',
        '_myliba_translation_key' => $person['translation_key'],
        '_myliba_person_headline' => $person['headline'],
        '_myliba_person_role' => $person['role'],
        '_myliba_person_website_url' => $person['website'],
        '_myliba_person_website_label' => $person['website_label'],
        '_myliba_order' => (string) $person['order'],
        '_myliba_seo_title' => $person['name'] . ' | Myliba Eğitmen ve Danışman',
        '_myliba_seo_description' => wp_trim_words(wp_strip_all_tags($person['bio']), 28, ''),
    ];
    foreach ($person_meta_defaults as $meta_key => $meta_value) {
        if ((string) get_post_meta($person_id, $meta_key, true) === '' && $meta_value !== '') {
            update_post_meta($person_id, $meta_key, $meta_value);
        }
    }
    $saved_person = get_post($person_id);
    if ($saved_person instanceof WP_Post && trim((string) $saved_person->post_excerpt) === '') {
        wp_update_post([
            'ID' => $person_id,
            'post_excerpt' => wp_trim_words(wp_strip_all_tags($person['bio']), 28, '…'),
        ]);
    }
    $trainer_ids[$person['translation_key']] = (int) $person_id;
}

$people_en = [
    [
        'name' => 'Dilek Mete',
        'translation_key' => 'trainer-dilek-mete',
        'slug' => 'dilek-mete',
        'headline' => 'Architect of Autonomous, Agile Teams That Turn Strategy into Action',
        'role' => 'Cultural Transformation Consultant · Executive Coach · OKR Coach · Author · Speaker',
        'bio' => '<p>Dilek Mete is a cultural transformation consultant, executive coach, and OKR coach who develops autonomous, agile teams that turn strategy into action.</p><p>She works alongside organizations to move strategy beyond the executive agenda, helping people across the organization understand it, take ownership, and translate it into behaviors that deliver results. By developing leaders and teams on the job, she helps make goals, collaboration, and a high-performance culture sustainable.</p>',
        'website' => 'https://www.dilekmete.com/',
        'website_label' => 'Learn more about Dilek Mete',
        'order' => 10,
    ],
    [
        'name' => 'Aysel Eker',
        'translation_key' => 'trainer-aysel-eker',
        'slug' => 'aysel-eker',
        'headline' => 'Coach Bringing Goals, People, and Culture Together for High Performance',
        'role' => 'Executive Coach (PCC) · OKR Coach · Team Coach',
        'bio' => '<p>Aysel Eker works at the intersection of coaching, OKRs, and culture, helping organizations strengthen goal clarity, leadership development, team performance, and a high-performance culture.</p><p>Her work focuses on using OKRs not simply as a goal-setting system, but as a management approach that helps employees connect with goals, clarify priorities, and establish a shared way of working across the organization.</p><p>As a partner of Myliba Software and a co-founder and trainer at Myliba Academy, Aysel combines OKRs, coaching, and people-centered management approaches to support sustainable high performance. Her Aligned Structural Corporate Coaching Model offers a distinctive approach that addresses the individual and the system together.</p>',
        'website' => '',
        'website_label' => '',
        'order' => 20,
    ],
    [
        'name' => 'Huri Şankur',
        'translation_key' => 'trainer-huri-sankur',
        'slug' => 'huri-sankur',
        'headline' => 'Consultant Strengthening the Connection Between People, Culture, and Goals',
        'role' => 'People & Culture Consultant · OKR Coach',
        'bio' => '<p>Huri Şankur is a People & Culture Consultant and OKR Coach focused on strengthening the connection between people, culture, and goals in organizations.</p><p>She works with teams to help employees understand and own the strategy, align goals around a shared direction, and embed this process into the organizational culture. Her focus is on reflecting a people-centered management approach in everyday ways of working and in the employee experience.</p><p>Alongside her doctoral studies, Huri combines the perspective gained from her academic work with hands-on experience to contribute to people- and culture-centered transformation journeys.</p>',
        'website' => 'https://www.hurisankur.com/',
        'website_label' => 'Learn more about Huri Şankur',
        'order' => 30,
    ],
];

foreach ($people_en as $person) {
    $matches = get_posts([
        'post_type' => 'myliba_team',
        'post_status' => ['publish', 'draft', 'private', 'trash'],
        'posts_per_page' => 1,
        'meta_query' => [
            ['key' => '_myliba_language', 'value' => 'en'],
            ['key' => '_myliba_translation_key', 'value' => $person['translation_key']],
        ],
    ]);
    if (empty($matches)) {
        $matches = get_posts([
            'post_type' => 'myliba_team',
            'post_status' => ['publish', 'draft', 'private', 'trash'],
            'posts_per_page' => 1,
            'title' => $person['name'],
            'meta_key' => '_myliba_language',
            'meta_value' => 'en',
        ]);
    }
    if (!empty($matches)) {
        // Preserve every admin edit, including an intentional move to Trash.
        $person_id = $matches[0]->ID;
    } else {
        $person_id = wp_insert_post([
            'post_type' => 'myliba_team',
            'post_status' => 'publish',
            'post_title' => $person['name'],
            'post_name' => $person['slug'],
            'post_content' => $person['bio'],
            'post_excerpt' => wp_trim_words(wp_strip_all_tags($person['bio']), 28, '…'),
        ], true);
    }
    if (is_wp_error($person_id)) {
        WP_CLI::warning($person_id->get_error_message());
        continue;
    }
    $person_meta_defaults = [
        '_myliba_language' => 'en',
        '_myliba_translation_key' => $person['translation_key'],
        '_myliba_person_headline' => $person['headline'],
        '_myliba_person_role' => $person['role'],
        '_myliba_person_website_url' => $person['website'],
        '_myliba_person_website_label' => $person['website_label'],
        '_myliba_order' => (string) $person['order'],
        '_myliba_seo_title' => $person['name'] . ' | Myliba Trainer & Consultant',
        '_myliba_seo_description' => wp_trim_words(wp_strip_all_tags($person['bio']), 28, ''),
    ];
    foreach ($person_meta_defaults as $meta_key => $meta_value) {
        if ((string) get_post_meta($person_id, $meta_key, true) === '' && $meta_value !== '') {
            update_post_meta($person_id, $meta_key, $meta_value);
        }
    }
    $saved_person = get_post($person_id);
    if ($saved_person instanceof WP_Post && trim((string) $saved_person->post_excerpt) === '') {
        wp_update_post([
            'ID' => $person_id,
            'post_excerpt' => wp_trim_words(wp_strip_all_tags($person['bio']), 28, '…'),
        ]);
    }

    $source_id = $trainer_ids[$person['translation_key']] ?? 0;
    if ($source_id) {
        foreach (['_thumbnail_id', '_myliba_linkedin_url', '_myliba_instagram_url', '_myliba_twitter_url', '_myliba_youtube_url', '_myliba_facebook_url'] as $shared_meta_key) {
            if ((string) get_post_meta($person_id, $shared_meta_key, true) !== '') {
                continue;
            }
            $shared_value = get_post_meta($source_id, $shared_meta_key, true);
            if ($shared_value !== '') {
                update_post_meta($person_id, $shared_meta_key, $shared_value);
            }
        }
    }
}

$consulting = get_page_by_path('danismanlik', OBJECT, 'myliba_solution');
if ($consulting instanceof WP_Post) {
    $saved_document = (string) get_post_meta($consulting->ID, \Myliba\Core\PageContent\META_KEY, true);
    if ($saved_document === '') {
        $fresh = \Myliba\Core\PageContent\solution_defaults($consulting->ID);
        update_post_meta(
            $consulting->ID,
            \Myliba\Core\PageContent\META_KEY,
            wp_slash(wp_json_encode(['schema' => 'solution'] + $fresh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        );
    }
    if ((string) get_post_meta($consulting->ID, '_myliba_seo_title', true) === '') {
        update_post_meta($consulting->ID, '_myliba_seo_title', 'Stratejiyi Eyleme İndiren Danışmanlık | Myliba');
    }
    if ((string) get_post_meta($consulting->ID, '_myliba_seo_description', true) === '') {
        update_post_meta($consulting->ID, '_myliba_seo_description', 'Myliba danışmanları stratejinizi hedeflere, aksiyonlara ve kalıcı bir yüksek performans kültürüne dönüştürmek için ekibinizle sahada çalışır.');
    }
} else {
    WP_CLI::warning('Danışmanlık çözüm kaydı bulunamadı; profil ve Eğitmenlerimiz sayfası yine oluşturuldu.');
}

flush_rewrite_rules();
WP_CLI::success('Hakkımızda menüsü hedefleri, Türkçe ve İngilizce dinamik eğitmen profilleri ve danışmanlık metni hazırlandı.');
