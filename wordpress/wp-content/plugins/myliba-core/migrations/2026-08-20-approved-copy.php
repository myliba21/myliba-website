<?php

if (!defined('ABSPATH')) {
    exit;
}

$solution_updates = [
    'danismanlik' => [
        'fields' => [
            'hero_primary_label' => 'Dönüşümüzü birlikte tasarlayalım',
            'intro_eyebrow' => 'MYLIBA yaklaşımı',
            'intro' => 'Danışmanlık çalışmalarımız, hedef belirlemeden uygulama ve izleme sürecine kadar organizasyonunuzun ihtiyaçlarına göre yapılandırılır.',
        ],
        'steps' => [
            ['title' => 'İhtiyaç Analizi', 'text' => 'Organizasyonunuzun mevcut yapısını, ihtiyaçlarını ve gelişim alanlarını birlikte ele alınır.'],
            ['title' => 'Hedeflenen Yapı', 'text' => 'Organizasyona uygun model, roller ve dönüşüm noktaları tasarlanır.'],
            ['title' => 'Uygulama ve İzleme', 'text' => 'Tasarlanan yaklaşımı işin içine taşır, ekiplerle birlikte hayata geçirir ve gelişim izlenir.'],
        ],
    ],
    'simulasyonlar-ve-takim-koclugu' => [
        'fields' => [
            'hero_primary_label' => 'İhtiyacınıza Uygun Simülasyonu Birlikte Tasarlayalım',
            'outcomes_title' => 'Simülasyon ve koçlukla birlikte ne değişir?',
            'process_lead' => 'Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim döngüsünün parçasıdır.',
        ],
        'steps' => [
            ['title' => 'Senaryoyu Yaşayın', 'text' => 'Ekipler gerçek iş yaşamını temsil eden karar ve iletişim anlarını deneyimler.'],
            ['title' => 'Davranışı Görün', 'text' => 'Koç eşliğinde güçlü yönler, engeller ve takım örüntüleri görünür hale gelir.'],
            ['title' => 'Dönüşümü Hayata Geçirin', 'text' => 'Öğrenilenler somut takım anlaşmalarına ve aksiyonlara dönüşür.'],
        ],
        'benefits' => [
            ['text' => 'Hedef Mars Simülasyonu — oyunlaştırılmış dijital laboratuvar'],
            ['text' => 'Radikal Samimiyet Simülasyonu — geri bildirim ve ileri bildirim isteyen ekipler yaratın'],
            ['text' => 'Başarı Sahnesi Simülasyonu — otonom ekipleri anlamlı hedefler etrafında geliştirin'],
        ],
    ],
];

foreach ($solution_updates as $path => $updates) {
    $post = get_page_by_path($path, OBJECT, 'myliba_solution');
    if (!$post instanceof WP_Post) {
        WP_CLI::warning('Solution not found: ' . $path);
        continue;
    }

    $document = \Myliba\Core\PageContent\document($post->ID, 'solution');
    foreach ($updates['fields'] as $key => $value) {
        $document['fields'][$key] = $value;
    }
    $document['collections']['steps'] = $updates['steps'];
    if (isset($updates['benefits'])) {
        $document['collections']['benefits'] = $updates['benefits'];
    }

    $encoded = wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    update_post_meta($post->ID, \Myliba\Core\PageContent\META_KEY, wp_slash($encoded));
}

$software = get_page_by_path('tr/yazilim');
if ($software instanceof WP_Post) {
    $document = \Myliba\Core\PageContent\document($software->ID, 'software');
    $document['collections']['faqs'][0]['answer'] = 'Myliba, yüksek performansı geliştirmeye odaklanan büyüme aşamasındaki şirketlerden çok lokasyonlu büyük organizasyonlara kadar her ölçekte kurum için ölçeklenebilir bir çözümdür. Yapı, sektör ve ekip büyüklüğüne göre özelleştirilebilen Myliba; stratejik hedefleri çalışanların hedefleriyle buluşturmak, odağı güçlendirmek ve sürekli gelişim kültürü oluşturmak isteyen şirketler için uygundur.';
    $encoded = wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    update_post_meta($software->ID, \Myliba\Core\PageContent\META_KEY, wp_slash($encoded));
}

$academy = get_page_by_path('tr/okr-kultur-akademisi');
if ($academy instanceof WP_Post) {
    update_post_meta($academy->ID, '_myliba_academy_testimonials_title', '');
}

WP_CLI::success('Approved solution, software FAQ and academy visibility updates were written to WordPress.');
