<?php
/**
 * Targets non-branded OKR coach, OKR coaching, and OKR consulting searches.
 *
 * Run with:
 * wp eval-file wp-content/plugins/myliba-core/migrations/2026-09-19-generic-okr-discovery.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$directories = [
    'tr/egitmenlerimiz' => [
        'content' => '<h2>OKR koçluğu ve OKR danışmanlığı uzmanları</h2><p>Myliba OKR koçları; şirketlerin stratejik önceliklerini ölçülebilir hedeflere dönüştürmesine, ekiplerin doğru OKR yazmasına ve hedefler etrafında hizalanmasına destek olur. Kurumsal OKR koçluğu sürecinde liderler ve ekipler yalnızca bir hedef sistemi kurmaz; düzenli takip, geri bildirim ve öğrenme alışkanlıkları da geliştirir.</p><h3>OKR koçu ne yapar?</h3><p>OKR koçu; Objective ve Key Result kalitesini geliştirir, ekipler arası bağımlılıkları görünür kılar, check-in ve CFR ritimlerini kolaylaştırır ve hedeflerin günlük kararlarla bağını güçlendirir. <a href="/tr/okr-koclugu/">Kurumsal OKR koçluğu yaklaşımımızı inceleyin.</a></p><h3>OKR danışmanlığı hangi konuları kapsar?</h3><p>Myliba’nın OKR danışmanlığı; stratejik hizalanma, OKR sistemi tasarımı, hedef yazımı, KPI ve performans yönetimi entegrasyonu, liderlik gelişimi ve yüksek performans kültürünü kapsar. İhtiyacınıza uygun uzmanı aşağıdan inceleyebilir, kurumsal uygulama için <a href="/tr/cozumler/danismanlik/">OKR danışmanlığı hizmetimizi</a>, uygulamalı gelişim için <a href="/tr/okr-kultur-akademisi/">OKR ve Kültür Akademisi’ni</a> keşfedebilirsiniz.</p>',
        'excerpt' => 'Kurumsal OKR koçluğu, OKR danışmanlığı, performans yönetimi, liderlik ve kültürel dönüşüm alanlarında çalışan Myliba OKR koçları ve danışmanları.',
        'meta' => [
            '_myliba_hero_title' => 'OKR Koçları, OKR Danışmanları ve Performans Uzmanları',
            '_myliba_hero_subtitle' => 'Kurumsal OKR koçluğu, OKR danışmanlığı, performans yönetimi, liderlik ve kültürel dönüşüm alanlarında çalışan Myliba uzmanlarıyla tanışın.',
            '_myliba_eyebrow' => 'OKR koçları ve danışmanları',
            '_myliba_trainers_directory_title' => 'Deneyimli OKR koçları ve OKR danışmanlarımızla tanışın.',
            '_myliba_seo_title' => 'OKR Koçları ve OKR Danışmanlığı Uzmanları | Myliba',
            '_myliba_seo_description' => 'Kurumsal OKR koçluğu ve OKR danışmanlığı için Myliba uzmanlarını keşfedin. OKR, performans yönetimi, hedef hizalama ve liderlik desteği alın.',
        ],
    ],
    'en/our-trainers' => [
        'content' => '<h2>OKR coaching and OKR consulting experts</h2><p>Myliba OKR coaches help organizations translate strategic priorities into measurable goals, write effective OKRs, and align teams around shared outcomes. Through corporate OKR coaching, leaders and teams build not only a goal system but also sustainable habits for review, feedback, and learning.</p><h3>What does an OKR coach do?</h3><p>An OKR coach improves the quality of Objectives and Key Results, makes cross-team dependencies visible, facilitates check-ins and CFR routines, and strengthens the connection between goals and everyday decisions. <a href="/en/okr-coaching/">Explore our corporate OKR coaching approach.</a></p><h3>What does OKR consulting cover?</h3><p>Myliba OKR consulting covers strategic alignment, OKR system design, goal setting, KPI and performance management integration, leadership development, and high-performance culture. Explore the right expert below, learn about our <a href="/en/solutions/advisory-and-consulting/">OKR consulting services</a>, or discover the <a href="/en/okr-culture-academy/">OKR &amp; Culture Academy</a>.</p>',
        'excerpt' => 'Meet Myliba OKR coaches and consultants specializing in corporate OKR coaching, OKR consulting, performance management, leadership, and cultural transformation.',
        'meta' => [
            '_myliba_hero_title' => 'OKR Coaches, OKR Consultants and Performance Experts',
            '_myliba_hero_subtitle' => 'Meet Myliba experts in corporate OKR coaching, OKR consulting, performance management, leadership, and cultural transformation.',
            '_myliba_eyebrow' => 'OKR coaches & consultants',
            '_myliba_trainers_directory_title' => 'Meet our experienced OKR coaches and consultants.',
            '_myliba_seo_title' => 'OKR Coaches & OKR Consulting Experts | Myliba',
            '_myliba_seo_description' => 'Discover Myliba OKR coaches and consultants for OKR implementation, performance management, strategic alignment, leadership, and culture transformation.',
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

$consulting_pages = [
    'tr' => [
        'slug' => 'danismanlik',
        'seo_title' => 'OKR Danışmanlığı ve Kurumsal OKR Koçluğu | Myliba',
        'seo_description' => 'Myliba OKR danışmanlığı ve kurumsal OKR koçluğu ile stratejinizi ölçülebilir hedeflere dönüştürün; ekiplerinizi hizalayın ve performansı geliştirin.',
        'excerpt' => 'OKR danışmanlığı ve kurumsal OKR koçluğu ile stratejiyi ölçülebilir hedeflere, ekip hizalanmasına ve sürdürülebilir performans rutinlerine dönüştürün.',
        'content' => '<h2>OKR danışmanlığı mı, OKR koçluğu mu?</h2><p>OKR sistemini sıfırdan tasarlamak, stratejiyi hedeflere dönüştürmek ve organizasyon genelinde uygulama modeli kurmak istiyorsanız <strong>OKR danışmanlığı</strong> doğru başlangıçtır. Mevcut OKR sisteminizin hedef kalitesini, ekip hizalanmasını ve düzenli takip alışkanlıklarını geliştirmek istiyorsanız <a href="/tr/okr-koclugu/"><strong>kurumsal OKR koçluğu</strong></a> daha uygun olabilir.</p><p>Sürece eşlik edecek kişileri görmek için <a href="/tr/egitmenlerimiz/">OKR koçları ve danışmanlarımızı</a> inceleyebilirsiniz.</p>',
        'fields' => [
            'kicker' => 'OKR danışmanlığı ve kurumsal OKR koçluğu',
            'hero_title' => 'OKR Danışmanlığıyla Stratejiyi Hedeflere ve Sonuçlara Dönüştürün',
            'hero_summary' => 'Myliba OKR danışmanlığı; şirket stratejisini ölçülebilir hedeflere, ekiplerin sahiplendiği OKR’lara ve sürdürülebilir bir performans sistemine dönüştürür.',
            'hero_supporting' => 'Myliba OKR koçları; doğru OKR yazımı, stratejik hizalanma, CFR rutinleri, KPI bağlantısı ve ilerleme takibini liderler ve ekiplerle birlikte kuruma yerleştirir.',
            'intro_title' => 'OKR Koçluğu ve OKR Danışmanlığı Nasıl Çalışır?',
            'intro' => 'OKR danışmanlığı yalnızca hedef listeleri hazırlamak değildir. Stratejik öncelikleri netleştirir, ekiplerin nitelikli Objective ve Key Result’lar yazmasını kolaylaştırır, hedefler arası hizalanmayı kurar ve düzenli kontrol ritimlerini geliştiririz.',
            'process_title' => 'OKR Danışmanlığı ve Koçluğu Sürecimiz',
            'process_lead' => 'Stratejik önceliklerden doğru OKR yazımına, ekip hizalanmasından izleme ve performans rutinlerine uzanan süreci işin içinde tasarlar ve uygularız.',
            'experts_title' => 'OKR Koçları ve Danışmanlarımızla Tanışın',
            'experts_lead' => 'OKR danışmanlığı, stratejik hizalanma, performans yönetimi, kültür ve liderlik alanlarında ekiplerle çalışan uzmanlarımızı inceleyin.',
        ],
    ],
    'en' => [
        'slug' => 'advisory-and-consulting',
        'seo_title' => 'OKR Consulting and Corporate OKR Coaching | Myliba',
        'seo_description' => 'Turn strategy into measurable goals with Myliba OKR consulting and corporate OKR coaching. Align teams and build sustainable performance routines.',
        'excerpt' => 'Turn strategy into measurable goals, team alignment, and sustainable performance routines with Myliba OKR consulting and corporate OKR coaching.',
        'content' => '<h2>OKR consulting or OKR coaching?</h2><p><strong>OKR consulting</strong> is the right starting point when you need to design an OKR system, translate strategy into goals, and establish an organization-wide operating model. If you already use OKRs and want to improve goal quality, team alignment, and review habits, <a href="/en/okr-coaching/"><strong>corporate OKR coaching</strong></a> may be the better fit.</p><p>Meet the people who support the journey on our <a href="/en/our-trainers/">OKR coaches and consultants</a> page.</p>',
        'fields' => [
            'kicker' => 'OKR consulting and corporate OKR coaching',
            'hero_title' => 'Turn Strategy into Goals and Results with OKR Consulting',
            'hero_summary' => 'Myliba OKR consulting turns company strategy into measurable goals, team-owned OKRs, and a sustainable performance management system.',
            'hero_supporting' => 'Myliba OKR coaches work with leaders and teams to establish effective OKR writing, strategic alignment, CFR routines, KPI connections, and progress reviews.',
            'intro_title' => 'How Do OKR Coaching and OKR Consulting Work?',
            'intro' => 'OKR consulting is more than preparing a list of goals. We clarify strategic priorities, help teams write effective Objectives and Key Results, align goals across the organization, and establish regular review routines.',
            'process_title' => 'Our OKR Consulting and Coaching Process',
            'process_lead' => 'We design and implement the journey from strategic priorities and effective OKR writing to team alignment, reviews, and performance routines.',
            'experts_title' => 'Meet Our OKR Coaches and Consultants',
            'experts_lead' => 'Explore experts who work with teams on OKR consulting, strategic alignment, performance management, culture, and leadership.',
        ],
    ],
];

foreach ($consulting_pages as $language => $data) {
    $matches = get_posts([
        'post_type' => 'myliba_solution',
        'post_status' => 'publish',
        'name' => $data['slug'],
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'meta_query' => [
            ['key' => '_myliba_language', 'value' => $language],
        ],
    ]);
    if (!$matches) {
        WP_CLI::warning("Consulting page not found: {$language}/{$data['slug']}");
        continue;
    }

    $page = $matches[0];
    wp_update_post([
        'ID' => $page->ID,
        'post_excerpt' => $data['excerpt'],
        'post_content' => $data['content'],
    ]);
    update_post_meta($page->ID, '_myliba_seo_title', $data['seo_title']);
    update_post_meta($page->ID, '_myliba_seo_description', $data['seo_description']);

    $document = \Myliba\Core\PageContent\document($page->ID, 'solution');
    foreach ($data['fields'] as $key => $value) {
        $document['fields'][$key] = $value;
    }
    update_post_meta(
        $page->ID,
        \Myliba\Core\PageContent\META_KEY,
        wp_slash(wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
    );
}

$coaching_pages = [
    'tr' => [
        'parent' => 'tr',
        'slug' => 'okr-koclugu',
        'title' => 'Kurumsal OKR Koçluğu',
        'excerpt' => 'Kurumsal OKR koçluğu ile hedef kalitesini geliştirin, ekipleri strateji etrafında hizalayın ve sürdürülebilir OKR çalışma ritimleri oluşturun.',
        'content' => '<h2>OKR koçluğu sürecinde ne yapıyoruz?</h2><p>Myliba OKR koçları, kurumun mevcut hedef sistemini ve OKR olgunluğunu değerlendirir. Liderler ve ekiplerle gerçek iş hedefleri üzerinde çalışarak Objective ve Key Result kalitesini geliştirir, ekipler arası bağlantıları görünür kılar ve düzenli takip alışkanlıklarının oluşmasına eşlik eder.</p><h2>Örnek 90 günlük OKR koçluğu yol haritası</h2><ol><li><strong>İlk 30 gün — Fotoğraf ve hizalanma:</strong> Stratejik öncelikler, mevcut hedefler, roller ve gelişim alanları değerlendirilir.</li><li><strong>31–60. gün — Uygulama:</strong> Ekiplerle OKR yazımı, hizalanma, bağımlılıklar ve check-in ritimleri üzerinde çalışılır.</li><li><strong>61–90. gün — Kalıcılaştırma:</strong> İlerleme, öğrenme, CFR ve dönem kapatma pratikleri kurumun çalışma sistemine yerleştirilir.</li></ol><h2>OKR koçluğu sonunda oluşturulan çalışma çıktıları</h2><ul><li>Stratejik önceliklerle bağlantılı Objective ve Key Result setleri</li><li>Ekipler arası hedef ve bağımlılık görünürlüğü</li><li>OKR check-in, CFR ve gözden geçirme ritimleri</li><li>Liderler ve ekipler için uygulama rehberliği</li><li>Sonraki OKR döngüsü için gelişim alanları</li></ul><p>OKR sistemini sıfırdan kurmak veya performans yönetimiyle bütünleştirmek istiyorsanız <a href="/tr/cozumler/danismanlik/">OKR danışmanlığı hizmetimizi</a>; sürece eşlik eden uzmanlar için <a href="/tr/egitmenlerimiz/">OKR koçlarımızı</a>; kurum içi yetkinlik geliştirmek için <a href="/tr/okr-kultur-akademisi/">OKR ve Kültür Akademisi’ni</a> inceleyin.</p>',
        'meta' => [
            '_myliba_language' => 'tr',
            '_myliba_translation_key' => 'okr-coaching',
            '_wp_page_template' => 'template-landing.php',
            '_myliba_eyebrow' => 'Kurumsal OKR koçluğu',
            '_myliba_hero_title' => 'OKR’ları Yazmakla Kalmayın, Kurumunuzda Çalıştırın',
            '_myliba_hero_subtitle' => 'Myliba OKR koçlarıyla hedef kalitesini geliştirin, ekipleri strateji etrafında hizalayın ve sürdürülebilir OKR çalışma ritimleri oluşturun.',
            '_myliba_label' => 'OKR koçluğu yaklaşımı',
            '_myliba_problem' => 'OKR kullanan birçok kurumda hedefler görev listesine dönüşür, ekipler arası bağlantılar zayıf kalır ve düzenli takip ritimleri sürdürülemez.',
            '_myliba_solution' => 'Myliba OKR koçluğu, liderler ve ekiplerle gerçek hedefler üzerinde çalışarak OKR kalitesini, hizalanmayı, check-in disiplinini ve öğrenme kültürünü birlikte geliştirir.',
            '_myliba_benefits' => "Daha nitelikli Objective ve Key Result’lar\nStratejiyle hizalanan ekip hedefleri\nSürdürülebilir check-in ve CFR ritimleri\nOKR döngüsünü yönetebilen liderler",
            '_myliba_related_modules' => "OKR ve hedef haritası\nKPI ve ilerleme takibi\nCFR ve 1:1 görüşmeler\nMyliba OKR & Kültür Akademisi",
            '_myliba_faq_items' => "OKR koçluğu nedir? | OKR koçluğu, liderlerin ve ekiplerin etkili OKR yazmasına, hedefleri stratejiyle hizalamasına ve düzenli takip alışkanlıkları geliştirmesine eşlik eden uygulamalı bir gelişim sürecidir.\nOKR danışmanlığı ile OKR koçluğu arasındaki fark nedir? | OKR danışmanlığı sistem tasarımı ve organizasyon çapındaki dönüşüme odaklanır. OKR koçluğu ise liderlerin ve ekiplerin mevcut sistemi daha etkili uygulama becerisini geliştirir.\nOKR koçluğu kimler için uygundur? | OKR kullanan veya kullanmaya hazırlanan liderlik, strateji, dönüşüm, insan ve kültür ekipleri ile hedef kalitesini geliştirmek isteyen takımlar için uygundur.\nOKR koçluğu ne kadar sürer? | Süre kurumun büyüklüğüne, mevcut OKR olgunluğuna ve kapsama göre belirlenir. İlk gelişim döngüsü çoğunlukla analiz, uygulama ve kalıcılaştırma aşamalarından oluşur.\nOKR koçluğu online yapılabilir mi? | Süreç kurumun ihtiyacına göre çevrim içi, yüz yüze veya hibrit olarak tasarlanabilir.",
            '_myliba_cta_label' => 'OKR koçluğu görüşmesi planlayın',
            '_myliba_cta_url' => '/tr/iletisim/',
            '_myliba_seo_title' => 'Kurumsal OKR Koçluğu ve OKR Koçları | Myliba',
            '_myliba_seo_description' => 'Kurumsal OKR koçluğu ile etkili OKR yazımı, stratejik hizalanma, check-in ve CFR ritimleri geliştirin. Myliba OKR koçlarıyla tanışın.',
        ],
    ],
    'en' => [
        'parent' => 'en',
        'slug' => 'okr-coaching',
        'title' => 'Corporate OKR Coaching',
        'excerpt' => 'Improve goal quality, align teams around strategy, and build sustainable OKR routines with corporate OKR coaching.',
        'content' => '<h2>What do we do during OKR coaching?</h2><p>Myliba OKR coaches assess the organization’s current goal system and OKR maturity. Working with leaders and teams on real business goals, they improve the quality of Objectives and Key Results, make cross-team connections visible, and help establish sustainable review habits.</p><h2>A sample 90-day OKR coaching roadmap</h2><ol><li><strong>Days 1–30 — Assessment and alignment:</strong> Strategic priorities, current goals, roles, and development areas are reviewed.</li><li><strong>Days 31–60 — Application:</strong> Teams work on OKR writing, alignment, dependencies, and check-in routines.</li><li><strong>Days 61–90 — Sustainability:</strong> Progress reviews, learning, CFR, and cycle-closing practices are embedded into the operating rhythm.</li></ol><h2>Working outputs created through OKR coaching</h2><ul><li>Objectives and Key Results connected to strategic priorities</li><li>Visibility into goals and dependencies across teams</li><li>OKR check-in, CFR, and review routines</li><li>Practical guidance for leaders and teams</li><li>Development priorities for the next OKR cycle</li></ul><p>If you need to design an OKR system from the ground up or integrate it with performance management, explore our <a href="/en/solutions/advisory-and-consulting/">OKR consulting service</a>. You can also meet our <a href="/en/our-trainers/">OKR coaches</a> or develop internal capability through the <a href="/en/okr-culture-academy/">OKR &amp; Culture Academy</a>.</p>',
        'meta' => [
            '_myliba_language' => 'en',
            '_myliba_translation_key' => 'okr-coaching',
            '_wp_page_template' => 'template-landing.php',
            '_myliba_eyebrow' => 'Corporate OKR coaching',
            '_myliba_hero_title' => 'Do More Than Write OKRs—Make Them Work',
            '_myliba_hero_subtitle' => 'Improve goal quality, align teams around strategy, and build sustainable OKR routines with Myliba OKR coaches.',
            '_myliba_label' => 'Our OKR coaching approach',
            '_myliba_problem' => 'In many organizations, OKRs become task lists, cross-team connections remain weak, and review routines are difficult to sustain.',
            '_myliba_solution' => 'Myliba OKR coaching works with leaders and teams on real goals to improve OKR quality, alignment, check-in discipline, and a culture of learning.',
            '_myliba_benefits' => "Higher-quality Objectives and Key Results\nTeam goals aligned with strategy\nSustainable check-in and CFR routines\nLeaders who can manage the OKR cycle",
            '_myliba_related_modules' => "OKRs and goal maps\nKPIs and progress tracking\nCFR and 1:1 conversations\nMyliba OKR & Culture Academy",
            '_myliba_faq_items' => "What is OKR coaching? | OKR coaching is an applied development process that helps leaders and teams write effective OKRs, align goals with strategy, and build consistent review habits.\nWhat is the difference between OKR consulting and OKR coaching? | OKR consulting focuses on system design and organization-wide transformation. OKR coaching develops the ability of leaders and teams to apply and improve the existing system.\nWho is OKR coaching for? | It is suitable for leadership, strategy, transformation, and people teams preparing to use or already using OKRs, as well as teams seeking to improve goal quality.\nHow long does OKR coaching take? | Duration depends on organization size, current OKR maturity, and scope. An initial development cycle typically includes assessment, application, and sustainability phases.\nCan OKR coaching be delivered online? | The engagement can be designed as online, in-person, or hybrid based on the organization’s needs.",
            '_myliba_cta_label' => 'Plan an OKR coaching conversation',
            '_myliba_cta_url' => '/en/contact/',
            '_myliba_seo_title' => 'Corporate OKR Coaching and OKR Coaches | Myliba',
            '_myliba_seo_description' => 'Improve OKR writing, strategic alignment, check-ins, and CFR routines with corporate OKR coaching. Meet Myliba OKR coaches.',
        ],
    ],
];

foreach ($coaching_pages as $data) {
    $parent = get_page_by_path($data['parent']);
    if (!$parent instanceof WP_Post) {
        WP_CLI::warning("Locale parent not found: {$data['parent']}");
        continue;
    }

    $path = $data['parent'] . '/' . $data['slug'];
    $page = get_page_by_path($path);
    $post_data = [
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_parent' => $parent->ID,
        'post_name' => $data['slug'],
        'post_title' => $data['title'],
        'post_excerpt' => $data['excerpt'],
        'post_content' => $data['content'],
    ];

    if ($page instanceof WP_Post) {
        $post_data['ID'] = $page->ID;
        $page_id = wp_update_post($post_data, true);
    } else {
        $page_id = wp_insert_post($post_data, true);
    }

    if (is_wp_error($page_id)) {
        WP_CLI::warning("Could not save coaching page {$path}: " . $page_id->get_error_message());
        continue;
    }

    foreach ($data['meta'] as $key => $value) {
        update_post_meta((int) $page_id, $key, $value);
    }
}

flush_rewrite_rules(false);

WP_CLI::success('Generic OKR coach, coaching, and consulting search targets updated.');
