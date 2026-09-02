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

if ($trainers_page instanceof WP_Post) {
    $trainers_page_data['ID'] = $trainers_page->ID;
}

$trainers_page_id = isset($trainers_page_data['ID'])
    ? wp_update_post($trainers_page_data, true)
    : wp_insert_post($trainers_page_data, true);
if (is_wp_error($trainers_page_id)) {
    WP_CLI::error($trainers_page_id->get_error_message());
}
update_post_meta($trainers_page_id, '_myliba_language', 'tr');
update_post_meta($trainers_page_id, '_myliba_translation_key', 'trainers');
update_post_meta($trainers_page_id, '_myliba_hero_title', 'Eğitmenlerimiz');
update_post_meta($trainers_page_id, '_myliba_hero_subtitle', 'Stratejiyi, kültürü ve yüksek performansı günlük işin içine taşıyan eğitmen, koç ve danışmanlarımızla tanışın.');
update_post_meta($trainers_page_id, '_myliba_seo_title', 'Eğitmenlerimiz ve Danışmanlarımız | Myliba');
update_post_meta($trainers_page_id, '_myliba_seo_description', 'Myliba’nın strateji, kültür, liderlik, OKR ve yüksek performans alanlarında çalışan eğitmen, koç ve danışmanlarıyla tanışın.');

$people = [
    [
        'name' => 'Dilek Mete',
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
        'slug' => 'huri-sankur-tr',
        'headline' => 'İnsan, Kültür ve Hedefler Arasındaki Bağı Güçlendiren Danışman',
        'role' => 'İnsan ve Kültür Danışmanı · OKR Koçu',
        'bio' => '<p>Huri Şankur, kurumlarda insan, kültür ve hedefler arasındaki bağı güçlendirmeye odaklanan İnsan ve Kültür Danışmanı ve OKR Koçudur.</p><p>Stratejinin çalışanlar tarafından anlaşılması ve sahiplenilmesi, hedeflerin ortak bir yön etrafında şekillenmesi ve bu sürecin kurum kültürüne yerleşmesi için ekiplerle çalışır. İnsan odaklı yönetim anlayışının, günlük iş yapış biçimlerine ve ekiplerin çalışma deneyimine yansımasına odaklanır.</p><p>Doktora çalışmalarını da sürdüren Huri Şankur, akademik çalışmalarından beslenen perspektifini uygulama deneyimiyle bir araya getirerek kurumların insan ve kültür odaklı dönüşüm süreçlerine katkı sunmaktadır.</p>',
        'website' => 'https://www.hurisankur.com/',
        'website_label' => 'Huri Şankur hakkında daha fazlası',
        'order' => 30,
    ],
];

foreach ($people as $person) {
    $matches = get_posts([
        'post_type' => 'myliba_team',
        'post_status' => ['publish', 'draft', 'private', 'trash'],
        'posts_per_page' => 1,
        'title' => $person['name'],
        'meta_key' => '_myliba_language',
        'meta_value' => 'tr',
    ]);
    $post_data = [
        'post_type' => 'myliba_team',
        'post_status' => 'publish',
        'post_title' => $person['name'],
        'post_name' => $person['slug'],
        'post_content' => $person['bio'],
    ];
    if (!empty($matches)) {
        $post_data['ID'] = $matches[0]->ID;
    }
    $person_id = isset($post_data['ID'])
        ? wp_update_post($post_data, true)
        : wp_insert_post($post_data, true);
    if (is_wp_error($person_id)) {
        WP_CLI::warning($person_id->get_error_message());
        continue;
    }
    update_post_meta($person_id, '_myliba_language', 'tr');
    update_post_meta($person_id, '_myliba_person_headline', $person['headline']);
    update_post_meta($person_id, '_myliba_person_role', $person['role']);
    update_post_meta($person_id, '_myliba_person_website_url', $person['website']);
    update_post_meta($person_id, '_myliba_person_website_label', $person['website_label']);
    update_post_meta($person_id, '_myliba_order', (string) $person['order']);
}

$consulting = get_page_by_path('danismanlik', OBJECT, 'myliba_solution');
if ($consulting instanceof WP_Post) {
    $current = \Myliba\Core\PageContent\document($consulting->ID, 'solution');
    $fresh = \Myliba\Core\PageContent\solution_defaults($consulting->ID);
    foreach (['redirect_url', 'hero_image', 'hero_image_alt'] as $preserve_key) {
        if (!empty($current['fields'][$preserve_key])) {
            $fresh['fields'][$preserve_key] = $current['fields'][$preserve_key];
        }
    }
    update_post_meta(
        $consulting->ID,
        \Myliba\Core\PageContent\META_KEY,
        wp_slash(wp_json_encode(['schema' => 'solution'] + $fresh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
    );
    update_post_meta($consulting->ID, '_myliba_seo_title', 'Stratejiyi Eyleme İndiren Danışmanlık | Myliba');
    update_post_meta($consulting->ID, '_myliba_seo_description', 'Myliba danışmanları stratejinizi hedeflere, aksiyonlara ve kalıcı bir yüksek performans kültürüne dönüştürmek için ekibinizle sahada çalışır.');
} else {
    WP_CLI::warning('Danışmanlık çözüm kaydı bulunamadı; profil ve Eğitmenlerimiz sayfası yine oluşturuldu.');
}

flush_rewrite_rules();
WP_CLI::success('Hakkımızda menüsü hedefleri, dinamik eğitmen profilleri ve danışmanlık metni hazırlandı.');
