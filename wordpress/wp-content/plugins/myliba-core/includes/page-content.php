<?php

namespace Myliba\Core\PageContent;

if (!defined('ABSPATH')) {
    exit;
}

const META_KEY = '_myliba_page_content';
const SCHEMA_VERSION = 1;

function boot(): void
{
    add_action('init', __NAMESPACE__ . '\\register_meta');
    add_action('add_meta_boxes_page', __NAMESPACE__ . '\\register_page_box');
    add_action('add_meta_boxes_myliba_solution', __NAMESPACE__ . '\\register_page_box');
    add_action('add_meta_boxes_myliba_report', __NAMESPACE__ . '\\register_page_box');
    add_action('add_meta_boxes_myliba_ebook', __NAMESPACE__ . '\\register_page_box');
    add_action('save_post', __NAMESPACE__ . '\\save', 10, 3);
}

function register_meta(): void
{
    foreach (['page', 'myliba_solution', 'myliba_report', 'myliba_ebook'] as $post_type) {
        register_post_meta($post_type, META_KEY, [
            'single' => true,
            'type' => 'string',
            'show_in_rest' => true,
            'revisions_enabled' => true,
            'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
            'sanitize_callback' => static fn ($value): string => is_string($value) ? $value : '',
        ]);
    }
}

function schema_for_post(\WP_Post|int $post): ?string
{
    $post = is_int($post) ? get_post($post) : $post;
    if (!$post instanceof \WP_Post) {
        return null;
    }

    if ($post->post_type === 'myliba_solution') {
        return 'solution';
    }

    if ($post->post_type === 'myliba_report') {
        return 'report';
    }

    if ($post->post_type === 'myliba_ebook') {
        return 'ebook';
    }

    if ($post->post_type !== 'page') {
        return null;
    }

    $slug = (string) $post->post_name;
    $uri = (string) get_page_uri($post);

    return match (true) {
        in_array($slug, ['yazilim', 'urunler', 'software', 'our-products'], true) || str_contains($uri, 'yazilim') || str_contains($uri, 'software') || str_contains($uri, 'our-products') => 'software',
        in_array($slug, ['cozumler', 'solutions'], true) || str_contains($uri, 'cozumler') || str_contains($uri, 'solutions') => 'solutions',
        in_array($slug, ['gelisim-merkezi', 'development-center', 'raporlar-ve-trendler', 'reports', 'e-kitaplar', 'ebooks'], true)
            || str_contains($uri, 'gelisim-merkezi')
            || str_contains($uri, 'development-center')
            || str_contains($uri, 'raporlar-ve-trendler')
            || str_contains($uri, 'reports')
            || str_contains($uri, 'e-kitaplar')
            || str_contains($uri, 'ebooks') => 'development',
        in_array($slug, ['hikayemiz', 'our-story', 'biz-kimiz', 'about', 'about-us', 'felsefemiz'], true) || str_contains($uri, 'hikayemiz') || str_contains($uri, 'our-story') => 'story',
        in_array($slug, ['etik-hat', 'etik-danismanlik', 'ethics-counsel', 'etik', 'ethics', 'whistleblowing'], true) || str_contains($slug, 'etik') || str_contains($slug, 'ethics') || str_contains($uri, 'etik') || str_contains($uri, 'ethics') => 'ethics',
        in_array($slug, ['sss', 'faq', 'faqs', 'sikca-sorulan-sorular'], true) || str_contains($slug, 'sss') || str_contains($slug, 'faq') || str_contains($uri, 'sss') || str_contains($uri, 'faq') => 'faq',
        default => null,
    };
}

function register_page_box(\WP_Post $post): void
{
    $schema = schema_for_post($post);
    if ($schema === null) {
        return;
    }

    remove_meta_box('myliba_hero', $post->post_type, 'normal');
    remove_meta_box('myliba_conversion_content', $post->post_type, 'normal');
    remove_meta_box('myliba_homepage_sections', $post->post_type, 'normal');
    remove_meta_box('myliba_academy_page', $post->post_type, 'normal');
    remove_meta_box('myliba_development_center', $post->post_type, 'normal');

    $definition = definition($schema);

    add_meta_box(
        'myliba_page_content',
        sprintf(__('Myliba — %s', 'myliba'), $definition['label']),
        __NAMESPACE__ . '\\render_page_box',
        $post->post_type,
        'normal',
        'high'
    );
}

function software_definition(): array
{
    return [
        'label' => 'Yazılım Sayfası İçeriği',
        'groups' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    'hero_eyebrow_primary' => ['text', 'Üst etiket'],
                    'hero_eyebrow_secondary' => ['text', 'Üst etiket açıklaması'],
                    'hero_title_start' => ['text', 'Başlık başlangıcı'],
                    'hero_title_emphasis' => ['text', 'Başlığın vurgulu bölümü'],
                    'hero_title_end' => ['text', 'Başlık devamı'],
                    'hero_lead' => ['textarea', 'Hero açıklaması'],
                    'hero_primary_label' => ['text', 'Ana buton etiketi'],
                    'hero_secondary_label' => ['text', 'İkincil buton etiketi'],
                    'hero_image' => ['media', 'Hero Görseli'],
                    'hero_image_alt' => ['text', 'Hero Görseli Alt Metni (Opsiyonel)'],
                ],
                'collections' => [
                    'hero_proof' => ['label' => 'Hero kısa faydaları', 'fields' => ['label' => ['text', 'Fayda']]],
                ],
            ],
            'trust' => [
                'label' => 'Referans Alanı',
                'fields' => [
                    'trust_label' => ['text', 'Üst etiket'],
                    'trust_title' => ['textarea', 'Başlık'],
                    'trust_text' => ['textarea', 'Açıklama'],
                ],
            ],
            'modules' => [
                'label' => 'Modüller',
                'fields' => [
                    'modules_eyebrow' => ['text', 'Üst etiket'],
                    'modules_title' => ['textarea', 'Başlık'],
                    'modules_text' => ['textarea', 'Açıklama'],
                    'modules_text_strong' => ['textarea', 'Vurgulu açıklama'],
                ],
                'collections' => [
                    'modules' => ['label' => 'Modül kartları', 'fields' => [
                        'image' => ['media', 'Görsel'],
                        'image_alt' => ['text', 'Görsel Alt Metni (Opsiyonel)'],
                        'title' => ['text', 'Başlık'],
                        'text' => ['textarea', 'Açıklama'],
                        'items' => ['textarea', 'Özellikler (her satıra bir özellik)'],
                    ]],
                ],
            ],
            'workflow' => [
                'label' => 'Çalışma Döngüsü',
                'fields' => [
                    'workflow_eyebrow' => ['text', 'Üst etiket'],
                    'workflow_title' => ['textarea', 'Başlık'],
                    'workflow_text' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'workflow_steps' => ['label' => 'Adımlar', 'fields' => [
                        'title' => ['text', 'Başlık'],
                        'text' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'why' => [
                'label' => 'Neden Myliba ve İstatistikler',
                'fields' => [
                    'why_eyebrow' => ['text', 'Üst etiket'],
                    'why_title' => ['textarea', 'Başlık'],
                    'why_formula_label' => ['text', 'Formül etiketi'],
                    'why_formula_left' => ['text', 'Formül sol tarafı'],
                    'why_formula_first' => ['text', 'Formül ilk değeri'],
                    'why_formula_second' => ['text', 'Formül ikinci değeri'],
                    'why_text' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'stats' => ['label' => 'İstatistik kartları', 'fields' => [
                        'value' => ['text', 'Değer'],
                        'label' => ['text', 'Etiket'],
                        'text' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'faq' => [
                'label' => 'Sıkça Sorulan Sorular',
                'fields' => [
                    'faq_eyebrow' => ['text', 'Üst etiket'],
                    'faq_title' => ['textarea', 'Başlık'],
                    'faq_text' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'faqs' => ['label' => 'Sorular', 'fields' => [
                        'question' => ['text', 'Soru'],
                        'answer' => ['textarea', 'Yanıt'],
                    ]],
                ],
            ],
            'final' => [
                'label' => 'Final CTA',
                'fields' => [
                    'final_eyebrow' => ['text', 'Üst etiket'],
                    'final_title' => ['textarea', 'Başlık'],
                    'final_text' => ['textarea', 'Açıklama'],
                    'final_button_label' => ['text', 'Ana buton etiketi'],
                    'final_button_url' => ['text', 'Ana buton bağlantısı (Örn: /tr/demo/)'],
                    'final_secondary_label' => ['text', 'İkincil buton etiketi'],
                    'final_secondary_url' => ['text', 'İkincil buton bağlantısı (Örn: /tr/iletisim/)'],
                    'final_cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
                ],
            ],
        ],
    ];
}

function software_defaults(): array
{
    return [
        'fields' => [
            'hero_eyebrow_primary' => 'Myliba Yazılım',
            'hero_eyebrow_secondary' => 'Veriyle Konuşan, Gelişim ve İnsan Odaklı Yazılım',
            'hero_title_start' => 'Yüksek Performanslı',
            'hero_title_emphasis' => 'Organizasyonlar Yaratmak',
            'hero_title_end' => 'için Geliştirildi',
            'hero_lead' => 'Formları tarihe gömün: % 100 objektif verilerle anlık performansı anlık yönetin. Kurumsal stratejiyi eyleme indiren ve performansı geliştiren bir kültür yaratmak için davranışlarda kararlılık gerekir. Myliba yazılım kurumiçi girişimciliği, sinerjik/çapraz ekipleri ve yeni nesil liderliği geliştiren bir alt yapıyı garanti etmenizi sağlar.',
            'hero_primary_label' => 'Demo Talep Edin',
            'hero_secondary_label' => 'Modülleri Keşfedin',
            'hero_image' => '',
            'hero_image_alt' => 'Myliba Yazılım',
            'trust_label' => 'Canlı performans kültürü kuran ekipler',
            'trust_title' => 'Veriyle daha adil kararlar alan kurumların yanında.',
            'trust_text' => 'Farklı sektörlerden ekipler hedef, performans ve gelişim ritimlerini Myliba ile tek noktada buluşturuyor.',
            'modules_eyebrow' => 'Tek platform. Dört güçlü odak.',
            'modules_title' => 'Adil Kararlar Verin',
            'modules_text' => 'Terfi, ücret, prim ve liderlik kararlarını; OKR, KPI, aksiyonlar, 360° analizler, kültür, bağlılık ve yapay zekâ içgörüleriyle destekleyin. Dedikodu, mobbing ve adaletsizlik gibi kültürel virüsleri erkenden tespit edin.',
            'modules_text_strong' => 'Kararlarınızı adil, şeffaf ve %100 objektif verilere dayandırın.',
            'workflow_eyebrow' => 'Tek ve sürekli bir çalışma döngüsü',
            'workflow_title' => 'Stratejiden Sonuçlara Kesintisiz Bir Yönetim Teknolojisi',
            'workflow_text' => 'Yıl sonunu beklemeden hedefleri, aksiyonları, gelişim odaklı geri-ileri bildirimleri ve risk sinyallerini tek bir çalışma ritminde yönetin.',
            'why_eyebrow' => 'Neden Myliba?',
            'why_title' => 'Myliba Yazılım: Şirketinizin Verimliliğini Katlayan Stratejik İş Ortağı',
            'why_formula_label' => 'Formülümüz',
            'why_formula_left' => 'Performans',
            'why_formula_first' => 'Potansiyel',
            'why_formula_second' => 'Müdahale',
            'why_text' => 'İnsanlara daha fazla güven, net bir odak ve gelişim alanı sunduğunuzda potansiyel performansa dönüşür. Myliba Yazılım bu anlayışı canlı verilere dönüştürür.',
            'faq_eyebrow' => 'Merak Edilenler',
            'faq_title' => 'Myliba Yazılım Hakkında Sıkça Sorulan Sorular',
            'faq_text' => 'Yeni nesil performans yönetimine geçerken bilmek isteyeceğiniz temel noktalar.',
            'final_eyebrow' => 'Dönüşüm için ilk adım',
            'final_title' => 'Şirketinizin “Görünmez İşletim Sistemini” Güncelleme Vakti Geldi.',
            'final_text' => 'Her organizasyonun performans yolculuğu farklıdır. İhtiyaçlarınıza özel kişiselleştirilmiş bir demo ile Myliba’nın şirketinizde nasıl değer yaratacağını birlikte keşfedelim.',
            'final_button_label' => 'Kişiselleştirilmiş Demo Talep Edin',
            'final_button_url' => '/tr/demo/',
            'final_secondary_label' => 'İletişime Geçin',
            'final_secondary_url' => '/tr/iletisim/',
            'final_cta_hide' => '0',
        ],
        'collections' => [
            'hero_proof' => [['label' => 'Canlı veri'], ['label' => 'Adil karar ekranı'], ['label' => 'İnsan odaklı gelişim']],
            'dashboard_nav' => [['label' => 'NineBox'], ['label' => 'Çalışanlar'], ['label' => 'Performans'], ['label' => 'İçgörüler']],
            'dashboard_boxes' => [
                ['label' => 'Gelişen Potansiyel', 'count' => '2'], ['label' => 'Yüksek Potansiyel', 'count' => '5'], ['label' => 'Geleceğin Liderleri', 'count' => '3'],
                ['label' => 'Güvenilir Oyuncu', 'count' => '7'], ['label' => 'Güçlü Performans', 'count' => '9'], ['label' => 'Yıldızlar', 'count' => '4'],
                ['label' => 'Destek Gerekli', 'count' => '2'], ['label' => 'Uzman Katkı', 'count' => '6'], ['label' => 'Kritik Yetenek', 'count' => '3'],
            ],
            'modules' => [
                ['image' => '', 'image_alt' => '', 'title' => 'Strateji ve Hedef Yönetim Modülü', 'text' => 'Çalışanlarınızı organizasyonunuzun strateji ve hedeflerine hizalayın. Siloları yıkın, herkes Kutup Yıldızı’na odaklansın.', 'items' => "Native OKR\nHedef Haritası\nAnlık İlerleme Takibi\nHedef Zorluk Analizi\nStratejik Aksiyonların İzlenmesi"],
                ['image' => '', 'image_alt' => '', 'title' => 'Performansı Geliştirme Modülü', 'text' => 'Gerçek zamanlı performans yönetimi artık mümkün! Kendi performansını izleyen ve geliştiren çalışanlar yaratarak mikro yönetim ihtiyacını ortadan kaldırın. Potansiyel ve Performans analizine anlık ulaşan çalışanlar liderlerinden geri- ileri bildirim ve koçluk istesin.', 'items' => "AI Destekli Görev ve Aksiyon Yönetimi\nKPI Kartları ve Veri Entegrasyonu\nLider Kararı & Keeper Test"],
                ['image' => '', 'image_alt' => '', 'title' => 'Sürekli Diyalog ve Kültür Yönetimi Modülleri', 'text' => 'Yılda bir kez yapılan notlamaları unutun. Yeni nesil liderliği geliştirecek gelişim odaklı diyalogları besleyin.', 'items' => "Diyalog (1:1 Görüşmeler)\nGeri Bildirim & İleri Bildirim\nTakdir ve Oyunlaştırma\n360°, 45° ve 90° Değer ve Yetkinlik Analizleri\nKültür, Bağlılık ve İsteklilik Analizi"],
                ['image' => '', 'image_alt' => '', 'title' => 'Adil Karar Modülü', 'text' => 'İnsan yönetiminde subjektif görüşlerin devrini kapatın. Kurumunuz ne kadar büyük ve işiniz ne kadar karmaşık olursa olsun, en doğru ve adil kararı verin.', 'items' => "AI Destekli İçgörüler\nPotansiyel Sıralaması\nPerformans Sıralaması\nDepartmanlar Arası Karşılaştırma\nKişiler Arası Karşılaştırma"],
            ],
            'workflow_steps' => [
                ['title' => 'Hedefleri belirleyin', 'text' => 'Stratejik önceliklerinizi OKR\'leri ve KPI\'ları organizasyonun her seviyesine taşıyın.'],
                ['title' => 'Anlık takip edin', 'text' => 'Hedef ilerlemelerini, aksiyonları ve risk sinyallerini anlık olarak görünür kılın.'],
                ['title' => 'Sürekli gelişimi destekleyin', 'text' => '1:1 görüşmeleri, geri bildirim, ileri bildirim ve takdiri günlük çalışma akışına taşıyın.'],
                ['title' => 'Veriye dayalı adil kararlar alın', 'text' => 'Terfi, ücret, prim ve gelişim kararlarını yıl boyunca oluşan objektif verilere dayandırın.'],
            ],
            'stats' => [
                ['value' => '% 85', 'label' => 'Tasarruf', 'text' => 'Farklı modülleri tek noktada sunarak toplam İK bütçenizde ’e varan maliyet tasarrufu.'],
                ['value' => '+40', 'label' => 'Gün Kazanç', 'text' => 'Çalışanların operasyonel yükünü azaltarak stratejik çalışmalara 40 güne varan ek zaman.'],
                ['value' => '2×', 'label' => 'Performans', 'text' => 'Sosyal taahhüt yönetimi ve anlık geri bildirim döngüleri ile ekiplerinizde 2x performans artışı.'],
                ['value' => "% 90'a", 'label' => 'Varan Daha Güçlü Hedefler', 'text' => "Geleneksel yöntemlere kıyasla % 90'a varan daha güçlü ve dönüşüme öncülük eden hedefler."],
            ],
            'faqs' => [
                ['question' => 'Myliba hangi şirketler için uygundur?', 'answer' => 'Myliba, yüksek performansı geliştirmeye odaklanan büyüme aşamasındaki şirketlerden çok lokasyonlu büyük organizasyonlara kadar her ölçekte kurum için ölçeklenebilir bir çözümdür. Yapı, sektör ve ekip büyüklüğüne göre özelleştirilebilen Myliba; stratejik hedefleri çalışanların hedefleriyle buluşturmak, odağı güçlendirmek ve sürekli gelişim kültürü oluşturmak isteyen şirketler için uygundur.'],
                ['question' => 'Myliba Yazılım, geleneksel performans sistemlerinden nasıl ayrılır?', 'answer' => 'Geleneksel sistemler çoğunlukla yılda bir kez doldurulan formlara ve geriye dönük yorumlara dayanır. Myliba ise hedef, KPI, aksiyon, diyalog ve kültür verilerini canlı olarak birleştirir; liderlere güncel ve karşılaştırılabilir karar verisi sunar.'],
                ['question' => 'Performans değerlendirme formlarının yerine geçebilir mi?', 'answer' => 'Evet. Sürekli hedef ilerlemesi, KPI sonuçları, aksiyonlar, geri bildirimler ve yetkinlik analizleri aynı çalışan görünümünde birleşir. Böylece dönem sonu formları yerine yıl boyunca oluşan kanıtlarla değerlendirme yapılabilir.'],
                ['question' => 'Myliba Yazılım içinde hem OKR (Hedef Yönetimi) hem de geleneksel KPI takibi aynı anda yapılabilir mi?', 'answer' => 'Evet. Stratejik yönü OKR’larla, operasyonel başarı ölçütlerini KPI kartlarıyla aynı yapı içinde takip edebilirsiniz. İki yaklaşım birbirine bağlanabilir ve ortak analitik görünümde raporlanabilir.'],
                ['question' => 'Myliba kültürel virüsleri ve riskleri nasıl tespit ediyor?', 'answer' => 'Bağlılık, isteklilik, kültürel uyum, geri bildirim ve liderlik verilerindeki eğilimler birlikte analiz edilir. Riskli değişimler ve tutarsızlıklar görünür hale getirilerek liderlerin erken aksiyon alması desteklenir.'],
                ['question' => 'Kurulum ne kadar sürer?', 'answer' => 'Kurulum süresi organizasyon yapısına, veri aktarımına ve entegrasyon kapsamına göre değişir. İhtiyaç analizi sonrasında ekip yapısı, hedef döngüsü ve yetkilendirmeler planlanarak kademeli ve kontrollü bir devreye alma süreci yürütülür.'],
                ['question' => 'Myliba Yazılım mevcut araçlarla nasıl entegre olur?', 'answer' => 'KPI ve organizasyon verileri API ve uygun veri bağlantıları üzerinden mevcut İK, iş zekâsı ve operasyon araçlarıyla ilişkilendirilebilir. Entegrasyon kapsamı kurumunuzun kullandığı sistemlere göre birlikte belirlenir.'],
            ],
        ],
    ];
}

function solutions_definition(): array
{
    return [
        'label' => 'Çözümler Sayfası İçeriği',
        'groups' => [
            'hero' => ['label' => 'Hero', 'fields' => [
                'hero_eyebrow' => ['text', 'Üst etiket'],
                'hero_title_start' => ['text', 'Başlık ilk satır'],
                'hero_title_end' => ['text', 'Başlık ikinci satır'],
                'hero_text' => ['textarea', 'Açıklama'],
            ]],
            'index' => ['label' => 'Çözüm Listesi', 'fields' => [
                'index_eyebrow' => ['text', 'Üst etiket'],
                'index_title' => ['textarea', 'Başlık'],
                'index_text' => ['textarea', 'Açıklama'],
                'card_link_label' => ['text', 'Kart bağlantı etiketi'],
            ]],
            'cta' => ['label' => 'Final CTA', 'fields' => [
                'cta_eyebrow' => ['text', 'Üst etiket'],
                'cta_title' => ['textarea', 'Başlık'],
                'cta_text' => ['textarea', 'Açıklama'],
                'cta_button_label' => ['text', 'Ana buton etiketi'],
                'cta_button_url' => ['text', 'Ana buton bağlantısı (Örn: /tr/iletisim/)'],
                'cta_secondary_label' => ['text', 'İkincil buton etiketi'],
                'cta_secondary_url' => ['text', 'İkincil buton bağlantısı (Örn: /tr/demo/)'],
                'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
            ]],
        ],
    ];
}

function solutions_defaults(): array
{
    return ['fields' => [
        'hero_eyebrow' => 'Myliba Çözümlerimiz',
        'hero_title_start' => 'Birbiriyle entegre,',
        'hero_title_end' => 'bütünleşik çözümler',
        'hero_text' => 'Myliba’nın özel geliştirdiği model ile yazılımı, akademiyi ve organizasyonel dönüşümü tek çatı altında buluşturarak “Yüksek Performans Kültürü” inşa edin.',
        'index_eyebrow' => 'İhtiyacınıza uygun çözümü seçin',
        'index_title' => 'İster tek tek kullanın, ister bütünleştirin ve kültürünüzü dönüştürün.',
        'index_text' => 'Neye ihtiyacınız varsa Myliba çözümleriyle kültürünüzü geliştirin. “Hedef Mars Simülasyonu” ile liderlerinizin yetkinliklerini laboratuvar ortamında görün ya da “Başarı Sahnesi Simülasyonu” ile şirketi geleceğe taşıyacak hedefleri yönetecek ekipleri kurun ve geliştirin.',
        'card_link_label' => 'Çözümü inceleyin',
        'cta_eyebrow' => 'Birlikte belirleyelim',
        'cta_title' => 'Hangi Çözüm Size Uygun, Birlikte Belirleyelim.',
        'cta_text' => 'İhtiyacınıza en uygun programı veya danışmanlık modelini bulmak için Myliba ile tanışın.',
        'cta_button_label' => 'Uzmanlarımızla görüşün',
        'cta_button_url' => '/tr/iletisim/',
        'cta_secondary_label' => 'Demo Talep Edin',
        'cta_secondary_url' => '/tr/demo/',
        'cta_hide' => '0',
    ], 'collections' => []];
}

function development_definition(): array
{
    $archive_fields = static fn (string $prefix): array => [
        $prefix . '_kicker' => ['text', 'Hero üst etiketi'],
        $prefix . '_lead' => ['textarea', 'Hero açıklaması'],
        $prefix . '_visual_label' => ['text', 'Görsel etiketi'],
        $prefix . '_visual_title' => ['textarea', 'Görsel başlığı'],
        $prefix . '_list_kicker' => ['text', 'Liste üst etiketi'],
        $prefix . '_list_title' => ['textarea', 'Liste başlığı'],
        $prefix . '_list_text' => ['textarea', 'Liste açıklaması'],
        $prefix . '_empty_title' => ['textarea', 'Boş durum başlığı'],
        $prefix . '_empty_text' => ['textarea', 'Boş durum açıklaması'],
    ];
    $archive_collections = static fn (string $prefix): array => [
        $prefix . '_journey' => ['label' => 'Görsel adımları', 'fields' => ['label' => ['text', 'Etiket']]],
        $prefix . '_topics' => ['label' => 'Yaklaşan içerik konuları', 'fields' => ['label' => ['text', 'Konu']]],
    ];

    return ['label' => 'Gelişim Merkezi İçeriği', 'groups' => [
        'landing' => ['label' => 'Gelişim Merkezi', 'fields' => [
            'hero_eyebrow' => ['text', 'Hero üst etiketi'],
            'hero_title' => ['textarea', 'Hero başlığı'],
            'hero_text' => ['textarea', 'Hero açıklaması'],
            'section_eyebrow' => ['text', 'Kartlar üst etiketi'],
            'section_title' => ['textarea', 'Kartlar bölüm başlığı'],
            'section_text' => ['textarea', 'Kartlar bölüm açıklaması'],
            'card_cta' => ['text', 'Kart bağlantı etiketi'],
            'latest_prefix' => ['text', 'Son içerik etiketi'],
            'card_ebooks_label' => ['text', 'e-Kitaplar kart başlığı'],
            'card_ebooks_text' => ['textarea', 'e-Kitaplar kart açıklaması'],
            'card_reports_label' => ['text', 'Raporlar kart başlığı'],
            'card_reports_text' => ['textarea', 'Raporlar kart açıklaması'],
            'card_blog_label' => ['text', 'Blog kart başlığı'],
            'card_blog_text' => ['textarea', 'Blog kart açıklaması'],
            'card_events_label' => ['text', 'Etkinlikler kart başlığı'],
            'card_events_text' => ['textarea', 'Etkinlikler kart açıklaması'],
        ]],
        'archive_shared' => ['label' => 'Arşiv Ortak Etiketleri', 'fields' => [
            'archive_back_label' => ['text', 'Geri bağlantısı etiketi'],
            'archive_discover_label' => ['text', 'Kaynakları keşfet butonu'],
            'archive_all_content_label' => ['text', 'Tüm içerikler bağlantısı'],
            'archive_report_item_label' => ['text', 'Rapor içerik türü etiketi'],
            'archive_ebook_item_label' => ['text', 'e-Kitap içerik türü etiketi'],
            'archive_item_link_label' => ['text', 'İçerik kartı bağlantı etiketi'],
            'archive_empty_eyebrow' => ['text', 'Boş durum üst etiketi'],
            'archive_empty_button_label' => ['text', 'Boş durum butonu'],
            'archive_topics_aria' => ['text', 'Yaklaşan konular erişilebilirlik açıklaması'],
        ]],
        'reports' => ['label' => 'Raporlar ve Trendler Arşivi', 'fields' => $archive_fields('reports'), 'collections' => $archive_collections('reports')],
        'ebooks' => ['label' => 'e-Kitaplar Arşivi', 'fields' => $archive_fields('ebooks'), 'collections' => $archive_collections('ebooks')],
        'cta' => ['label' => 'Final CTA', 'fields' => [
            'cta_eyebrow' => ['text', 'Üst etiket'],
            'cta_title' => ['textarea', 'Başlık'],
            'cta_text' => ['textarea', 'Açıklama'],
            'cta_button_label' => ['text', 'Ana buton etiketi'],
            'cta_button_url' => ['text', 'Ana buton bağlantısı (Örn: /tr/iletisim/)'],
            'cta_secondary_label' => ['text', 'İkincil buton etiketi'],
            'cta_secondary_url' => ['text', 'İkincil buton bağlantısı (Örn: /tr/demo/)'],
            'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
        ]],
    ]];
}

function development_defaults(): array
{
    return ['fields' => [
        'hero_eyebrow' => 'Sürekli gelişim ve dönüşüm merkezi',
        'hero_title' => 'Gelişim zihniyetini geliştirin!',
        'hero_text' => 'Gelişim zihniyeti sürekli yeni bilgi ve tecrübe ile beslenmeyi gerektirir. Myliba, “Yüksek Performans Kültürü” inşa ederken performans geliştirme zihniyetine geçiş için sürekli içerikler üretir.',
        'section_eyebrow' => 'Alt Sayfalar',
        'section_title' => 'Gelişim zihniyetini sürekli yeni bilgi ve tecrübeyle besleyin.',
        'section_text' => 'e-Kitaplar, raporlar, blog yazıları ve etkinliklerle gelişim yolculuğunuzu sürdürün.',
        'card_cta' => 'İçerikleri inceleyin',
        'latest_prefix' => 'Son içerik:',
        'card_ebooks_label' => 'e-Kitaplar',
        'card_ebooks_text' => 'Yüksek performans kültürü ve yönetim pratikleri üzerine indirilebilir kaynaklar.',
        'card_reports_label' => 'Raporlar ve Trendler',
        'card_reports_text' => 'İçerik planlanıyor.',
        'card_blog_label' => 'Blog',
        'card_blog_text' => 'Eski yazılar buraya taşınacak.',
        'card_events_label' => 'Etkinlikler',
        'card_events_text' => 'Webinar ve workshop duyuruları.',
        'archive_back_label' => 'Gelişim Merkezi',
        'archive_discover_label' => 'Kaynakları keşfedin',
        'archive_all_content_label' => 'Tüm gelişim içerikleri',
        'archive_report_item_label' => 'Rapor ve trend',
        'archive_ebook_item_label' => 'e‑Kitap',
        'archive_item_link_label' => 'İçeriği inceleyin',
        'archive_empty_eyebrow' => 'Çok yakında',
        'archive_empty_button_label' => 'Blog yazılarını keşfedin',
        'archive_topics_aria' => 'Yaklaşan içerik konuları',
        'reports_kicker' => 'Araştırma, veri ve gelecek sinyalleri',
        'reports_lead' => 'İş dünyasını, performans kültürünü ve liderliği şekillendiren güncel araştırmaları uygulanabilir içgörülere dönüştürün.',
        'reports_visual_label' => 'Myliba trend radarı',
        'reports_visual_title' => 'Sinyalleri okuyun. Değişime yön verin.',
        'reports_list_kicker' => 'Seçili araştırmalar',
        'reports_list_title' => 'Bugünün verisiyle yarının çalışma kültürünü okuyun.',
        'reports_list_text' => 'Raporları, araştırma notlarını ve öne çıkan trend analizlerini tek yerde keşfedin.',
        'reports_empty_title' => 'İlk araştırma dosyaları hazırlanıyor.',
        'reports_empty_text' => 'Yeni raporlar yayınlandığında bu alanda otomatik olarak yerini alacak. Bu sırada güncel yazılarımızı keşfedebilirsiniz.',
        'ebooks_kicker' => 'Rehberler, araçlar ve uygulama kaynakları',
        'ebooks_lead' => 'Hedef, liderlik ve kültür pratiklerini günlük işe taşımanıza yardımcı olacak indirilebilir kaynakları keşfedin.',
        'ebooks_visual_label' => 'Myliba uygulama kütüphanesi',
        'ebooks_visual_title' => 'Bilgiyi alın. Ekibinizle uygulayın.',
        'ebooks_list_kicker' => 'Kaynak kütüphanesi',
        'ebooks_list_title' => 'Gelişimi günlük çalışma ritmine taşıyan kaynaklar.',
        'ebooks_list_text' => 'İhtiyacınıza uygun rehberi seçin, ekibinizle paylaşın ve uygulamaya başlayın.',
        'ebooks_empty_title' => 'İlk e‑kitaplar hazırlanıyor.',
        'ebooks_empty_text' => 'Yeni kaynaklar yayınlandığında bu alanda otomatik olarak yerini alacak. Bu sırada güncel yazılarımızı keşfedebilirsiniz.',
        'cta_eyebrow' => 'Gelişim Yolculuğu',
        'cta_title' => 'Kültürü ve Performansı Birlikte Geliştirelim',
        'cta_text' => 'Kurumunuza özel gelişim programları ve kaynaklar için bizimle iletişime geçin.',
        'cta_button_label' => 'İletişime Geçin',
        'cta_button_url' => '/tr/iletisim/',
        'cta_secondary_label' => 'Demo Talep Edin',
        'cta_secondary_url' => '/tr/demo/',
        'cta_hide' => '0',
    ], 'collections' => [
        'reports_journey' => [['label' => 'Araştır'], ['label' => 'Yorumla'], ['label' => 'Uygula']],
        'reports_topics' => [['label' => 'Performans kültürü'], ['label' => 'Liderlik ve dönüşüm'], ['label' => 'İş dünyasının geleceği']],
        'ebooks_journey' => [['label' => 'Keşfet'], ['label' => 'İndir'], ['label' => 'Uygula']],
        'ebooks_topics' => [['label' => 'OKR ve hedef yönetimi'], ['label' => 'Liderlik pratikleri'], ['label' => 'Kültür ve performans']],
    ]];
}

function solution_definition(): array
{
    return ['label' => 'Çözüm Detay İçeriği', 'groups' => [
        'redirect' => ['label' => 'Yönlendirme / Hedef Sayfa', 'fields' => [
            'redirect_url' => ['text', 'Özel Yönlendirme / Hedef URL (Boş bırakılırsa standart çözüm sayfası kullanılır. Örn: /tr/okr-kultur-akademisi/)'],
        ]],
        'hero' => ['label' => 'Hero ve Tanıtım', 'fields' => [
            'kicker' => ['text', 'Üst etiket (Pill)'],
            'hero_title' => ['textarea', 'Hero başlığı'],
            'hero_summary' => ['textarea', 'Hero açıklaması'],
            'hero_primary_label' => ['text', 'Ana buton etiketi (Örn: Programı birlikte tasarlayalım)'],
            'hero_secondary_label' => ['text', 'İkincil buton etiketi (Örn: Çalışma modelini inceleyin)'],
            'hero_image' => ['media', 'Hero Sağ Görseli (Görsel seçildiğinde sağ tarafta bu görsel görünür; boş bırakılırsa yolculuk kutusu görünür)'],
            'hero_image_alt' => ['text', 'Hero Sağ Görseli Alt Metni (Opsiyonel)'],
            'journey_eyebrow' => ['text', 'Yolculuk kutusu üst etiketi (Örn: Myliba gelişim yolculuğu)'],
            'journey_title' => ['textarea', 'Yolculuk kutusu sloganı (Örn: Kuruma özel.\nİşin içinde.\nÖlçülebilir.)'],
        ], 'collections' => [
            'steps' => ['label' => 'Yolculuk kutusu ve süreç adımları', 'fields' => ['title' => ['text', 'Başlık (Örn: Mevcut Durum)'], 'text' => ['textarea', 'Süreç bölümündeki açıklama']]],
        ]],
        'intro' => ['label' => 'Myliba Yaklaşımı', 'fields' => [
            'intro_eyebrow' => ['text', 'Üst etiket (Örn: Myliba yaklaşımı)'],
            'intro_title' => ['textarea', 'Bölüm başlığı (Örn: Kültürü, hedefleri ve iş sonuçlarını birlikte geliştirin.)'],
            'intro' => ['textarea', 'Yaklaşım açıklaması'],
            'intro_link_label' => ['text', 'Bağlantı metni (Örn: İhtiyacınızı birlikte değerlendirelim)'],
        ]],
        'audiences' => ['label' => 'Kimler İçin?', 'fields' => [
            'audiences_eyebrow' => ['text', 'Üst etiket (Örn: Kimler için?)'],
            'audiences_title' => ['textarea', 'Bölüm başlığı (Örn: Değişimi birlikte yöneten ekipler için.)'],
        ], 'collections' => [
            'audiences' => ['label' => 'Hedef kitle kartları', 'fields' => ['text' => ['text', 'Hedef kitle']]],
        ]],
        'benefits' => ['label' => 'Beklenen Kazanımlar (Ne Değişir?)', 'fields' => [
            'outcomes_eyebrow' => ['text', 'Üst etiket (Örn: Beklenen kazanımlar)'],
            'outcomes_title' => ['textarea', 'Bölüm başlığı (Örn: Programla birlikte ne değişir?)'],
            'outcomes_lead' => ['textarea', 'Açıklama (Örn: Gelişimi tek seferlik bir müdahaleden çıkarıp, kurumun çalışma biçimine yerleştirin.)'],
        ], 'collections' => [
            'benefits' => ['label' => 'Kazanım maddeleri', 'fields' => ['text' => ['textarea', 'Kazanım']]],
        ]],
        'metrics_section' => ['label' => 'Ölçüm Alanları', 'fields' => [
            'metrics_eyebrow' => ['text', 'Üst etiket (Örn: Ölçüm alanları)'],
            'metrics_title' => ['textarea', 'Bölüm başlığı (Örn: Kültürü dört kritik göstergeyle görünür kılın.)'],
        ], 'collections' => [
            'metrics' => ['label' => 'Ölçüm alanları', 'fields' => ['title' => ['text', 'Başlık'], 'text' => ['textarea', 'Açıklama']]],
        ]],
        'process' => ['label' => 'Çalışma Modeli & Süreç', 'fields' => [
            'process_eyebrow' => ['text', 'Üst etiket (Örn: Çalışma modeli)'],
            'process_title' => ['textarea', 'Bölüm başlığı (Örn: Süreç adımları)'],
            'process_lead' => ['textarea', 'Açıklama (Örn: Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim ritminin parçasıdır.)'],
        ]],
        'cta' => ['label' => 'Final Aksiyon Çağrısı (CTA)', 'fields' => [
            'cta_eyebrow' => ['text', 'Üst etiket (Örn: 30 dakikalık keşif görüşmesi)'],
            'cta_title' => ['textarea', 'Başlık (Örn: İhtiyacınıza uygun yolculuğu birlikte tasarlayalım.)'],
            'cta_text' => ['textarea', 'Açıklama (Örn: Kurumunuzun hedeflerini dinleyelim; doğru programı, kapsamı ve çalışma modelini birlikte netleştirelim.)'],
            'cta_button_label' => ['text', 'Ana buton etiketi (Örn: Görüşme planlayın)'],
            'cta_button_url' => ['text', 'Ana buton bağlantısı (Örn: /tr/iletisim/)'],
            'cta_secondary_label' => ['text', 'İkincil buton etiketi (Örn: Tüm çözümleri görün)'],
            'cta_secondary_url' => ['text', 'İkincil buton bağlantısı (Örn: /tr/cozumler/)'],
            'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
        ]],
    ]];
}

function solution_defaults(int $post_id): array
{
    $post = get_post($post_id);
    $slug = $post instanceof \WP_Post ? $post->post_name : '';
    $catalog = [
        'kurumsal-gelisim-programlari' => [
            'kicker' => 'İşbaşı gelişim programları',
            'summary' => '“Hedefleri değerlerle yönetmek” işbaşı gelişim programlarıyla kurumunuza özel, uygulamalı gelişim yolculukları tasarlayın.',
            'intro' => 'Stratejik hedefleri kurum kültürü ve liderlik davranışlarıyla buluşturan programlarla öğrenmeyi günlük iş akışının parçası haline getirin.',
            'benefits' => ['Kurum hedefleri ve değerleriyle bağlantılı gelişim tasarımı', 'Canlı öğrenme, işbaşı uygulama ve ölçülebilir takip', 'Liderler ve ekipler için sürdürülebilir gelişim ritmi'],
            'audiences' => ['İnsan ve kültür ekipleri', 'Liderlik ekipleri', 'Dönüşüm ve gelişim ekipleri'],
            'steps' => [['İhtiyaç Analizi', 'Kurumun hedefleri, kültürü ve gelişim öncelikleri birlikte değerlendirilir.'], ['Program Tasarımı', 'İçerik, gerçek iş hedefleri ve ekip dinamikleri etrafında yapılandırılır.'], ['Uygulama ve Takip', 'Öğrenme işbaşında uygulanır, gelişim göstergeleri düzenli olarak izlenir.']],
        ],
        'simulasyonlar-ve-takim-koclugu' => [
            'kicker' => 'Deneyimleyerek öğrenme',
            'summary' => 'Gerçek iş senaryolarını güvenli bir laboratuvar ortamında deneyimleyin, ekip davranışlarını görünür hale getirin.',
            'intro' => 'Simülasyonlar ve takım koçluğu, ekiplerin hedef, bağlılık ve iş birliği pratiklerini birlikte keşfetmesini ve geliştirmesini sağlar.',
            'hero_primary_label' => 'İhtiyacınıza Uygun Simülasyonu Birlikte Tasarlayalım',
            'outcomes_title' => 'Simülasyon ve koçlukla birlikte ne değişir?',
            'process_lead' => 'Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim döngüsünün parçasıdır.',
            'benefits' => ['Hedef Mars Simülasyonu — oyunlaştırılmış dijital laboratuvar', 'Radikal Samimiyet Simülasyonu — geri bildirim ve ileri bildirim isteyen ekipler yaratın', 'Başarı Sahnesi Simülasyonu — otonom ekipleri anlamlı hedefler etrafında geliştirin'],
            'audiences' => ['Birlikte çalışma ritmini güçlendiren ekipler', 'Yeni kurulan veya dönüşen ekipler', 'Geri bildirim kültürü geliştiren liderler'],
            'steps' => [['Senaryoyu Yaşayın', 'Ekipler gerçek iş yaşamını temsil eden karar ve iletişim anlarını deneyimler.'], ['Davranışı Görün', 'Koç eşliğinde güçlü yönler, engeller ve takım örüntüleri görünür hale gelir.'], ['Dönüşümü Hayata Geçirin', 'Öğrenilenler somut takım anlaşmalarına ve aksiyonlara dönüşür.']],
        ],
        'danismanlik' => [
            'kicker' => 'Stratejiden sürdürülebilir sisteme',
            'summary' => 'Stratejik hedeflerinizi netleştirin ve kurumunuza özel performans gelişim sistemini birlikte kurun.',
            'hero_primary_label' => 'Dönüşümüzü birlikte tasarlayalım',
            'intro_eyebrow' => 'MYLIBA yaklaşımı',
            'intro' => 'Danışmanlık çalışmalarımız, hedef belirlemeden uygulama ve izleme sürecine kadar organizasyonunuzun ihtiyaçlarına göre yapılandırılır.',
            'benefits' => ['Stratejik Hedef Haritası Oluşturma — şirket tepe hedeflerinin belirlenmesi ve otonom ekiplerin oluşturulması', 'Performans Gelişim Sistemi Kurulumu — performans gelişim altyapısının kurumunuza özel yapılandırılması', 'Uygulama, iletişim ve liderlik rutinlerinin organizasyonla birlikte tasarlanması'],
            'audiences' => ['Üst yönetim ekipleri', 'İnsan ve kültür liderleri', 'Strateji ve dönüşüm ekipleri'],
            'steps' => [['İhtiyaç Analizi', 'Organizasyonunuzun mevcut yapısını, ihtiyaçlarını ve gelişim alanlarını birlikte ele alınır.'], ['Hedeflenen Yapı', 'Organizasyona uygun model, roller ve dönüşüm noktaları tasarlanır.'], ['Uygulama ve İzleme', 'Tasarlanan yaklaşımı işin içine taşır, ekiplerle birlikte hayata geçirir ve gelişim izlenir.']],
        ],
        'kultur-analizi' => [
            'kicker' => 'Veriye dayalı kültür dönüşümü',
            'summary' => 'Kurum kültürünüzü derinlemesine analiz edin, potansiyel engelleri belirleyin ve çalışan bağlılığını güçlendirin.',
            'intro' => 'Şirketinizin mevcut kültürünü derinlemesine analiz ederek potansiyel engelleri belirlemenize ve çalışanlarınızın gerçekten “çalışmaktan keyif aldığı” bir ortam yaratmanıza yardımcı oluyoruz.',
            'benefits' => ['Mevcut kültürün güçlü ve zayıf yönlerinin keşfedilmesi', 'Çalışan bağlılığı ve iş performansının artması', 'Kurum içi sinerji ve iletişimin güçlenmesi', 'Stratejik dönüşüm için veriye dayalı içgörüler edinilmesi'],
            'audiences' => ['İnsan ve kültür liderleri', 'Üst yönetim ekipleri', 'Değişim ve dönüşüm ekipleri'],
            'metrics' => [['Employee NPS', 'Çalışan tavsiye skoru'], ['Culture Fit', 'Departmanlar arası kültürel uyum'], ['Willingness', 'Çalışanların işe olan isteği'], ['Engagement', 'Kuruma, işe ve lidere bağlılık']],
            'steps' => [['Anket Aşaması', 'Kültür analizi, bağlılık analizi ve isteklilik analizi uygulanır.'], ['Saha Araştırması', 'Odak grup görüşmeleri, yönetici birebir görüşmeleri, doküman analizi ve opsiyonel gözlem yapılır.'], ['Gelişim Planı', 'Detaylı Kültür Analizi Raporu hazırlanır; öncelikli gelişim alanları belirlenir; OKR ve KPI uyumlu hedef setleri, uygulama adımları ve zaman çizelgesi oluşturulur; eğitim, iletişim ve liderlik gelişim önerileri sunulur; ölçüm bir yıl sonra tekrarlanır.']],
        ],
    ];
    $item = $catalog[$slug] ?? [];
    $title = (string) (get_post_meta($post_id, '_myliba_hero_title', true) ?: ($post instanceof \WP_Post ? get_the_title($post) : ''));
    $kicker = (string) (get_post_meta($post_id, '_myliba_eyebrow', true) ?: get_post_meta($post_id, '_myliba_label', true) ?: ($item['kicker'] ?? 'Myliba Çözümü'));
    $summary = (string) (get_post_meta($post_id, '_myliba_hero_subtitle', true) ?: ($post instanceof \WP_Post ? get_post_field('post_excerpt', $post) : '') ?: ($item['summary'] ?? ''));
    $intro = (string) (get_post_meta($post_id, '_myliba_solution', true) ?: get_post_meta($post_id, '_myliba_problem', true) ?: ($post instanceof \WP_Post ? get_post_field('post_content', $post) : '') ?: ($item['intro'] ?? ''));

    $meta_benefits = function_exists('myliba_lines') ? \myliba_lines((string) get_post_meta($post_id, '_myliba_benefits', true)) : [];
    $benefits_raw = !empty($meta_benefits) ? $meta_benefits : ($item['benefits'] ?? []);

    $meta_audiences = function_exists('myliba_lines') ? \myliba_lines((string) get_post_meta($post_id, '_myliba_audiences', true)) : [];
    $audiences_raw = !empty($meta_audiences) ? $meta_audiences : ($item['audiences'] ?? ['İnsan ve kültür ekipleri', 'Liderlik ekipleri', 'Dönüşüm ve gelişim ekipleri']);

    $metrics_raw = $item['metrics'] ?? [];
    $steps_raw = $item['steps'] ?? [];

    $redirect_url = (string) (get_post_meta($post_id, '_myliba_redirect_url', true) ?: '');
    if ($redirect_url === '') {
        if ($slug === 'kurumsal-gelisim-programlari') {
            $redirect_url = '/tr/okr-kultur-akademisi/';
        } elseif ($slug === 'corporate-development-programs') {
            $redirect_url = '/en/okr-culture-academy/';
        }
    }

    return ['fields' => [
        'redirect_url' => $redirect_url,
        'kicker' => $kicker,
        'hero_title' => $title,
        'hero_summary' => $summary,
        'hero_primary_label' => (string) (get_post_meta($post_id, '_myliba_cta_label', true) ?: ($item['hero_primary_label'] ?? 'Programı birlikte tasarlayalım')),
        'hero_secondary_label' => 'Çalışma modelini inceleyin',
        'hero_image' => (string) (get_post_meta($post_id, '_myliba_hero_image', true) ?: ''),
        'hero_image_alt' => (string) (get_post_meta($post_id, '_myliba_hero_image_alt', true) ?: ''),
        'journey_eyebrow' => (string) (get_post_meta($post_id, '_myliba_journey_eyebrow', true) ?: 'Myliba gelişim yolculuğu'),
        'journey_title' => (string) (get_post_meta($post_id, '_myliba_journey_title', true) ?: "Kuruma özel.\nİşin içinde.\nÖlçülebilir."),
        'intro_eyebrow' => (string) (get_post_meta($post_id, '_myliba_intro_eyebrow', true) ?: ($item['intro_eyebrow'] ?? 'Myliba yaklaşımı')),
        'intro_title' => (string) (get_post_meta($post_id, '_myliba_intro_title', true) ?: 'Kültürü, hedefleri ve iş sonuçlarını birlikte geliştirin.'),
        'intro' => $intro,
        'intro_link_label' => 'İhtiyacınızı birlikte değerlendirelim',
        'audiences_eyebrow' => (string) (get_post_meta($post_id, '_myliba_audiences_eyebrow', true) ?: 'Kimler için?'),
        'audiences_title' => (string) (get_post_meta($post_id, '_myliba_audiences_title', true) ?: 'Değişimi birlikte yöneten ekipler için.'),
        'outcomes_eyebrow' => (string) (get_post_meta($post_id, '_myliba_outcomes_eyebrow', true) ?: 'Beklenen kazanımlar'),
        'outcomes_title' => (string) (get_post_meta($post_id, '_myliba_outcomes_title', true) ?: ($item['outcomes_title'] ?? 'Programla birlikte ne değişir?')),
        'outcomes_lead' => (string) (get_post_meta($post_id, '_myliba_outcomes_lead', true) ?: 'Gelişimi tek seferlik bir müdahaleden çıkarıp, kurumun çalışma biçimine yerleştirin.'),
        'metrics_eyebrow' => (string) (get_post_meta($post_id, '_myliba_metrics_eyebrow', true) ?: 'Ölçüm alanları'),
        'metrics_title' => (string) (get_post_meta($post_id, '_myliba_metrics_title', true) ?: 'Kültürü dört kritik göstergeyle görünür kılın.'),
        'process_eyebrow' => (string) (get_post_meta($post_id, '_myliba_process_eyebrow', true) ?: 'Çalışma modeli'),
        'process_title' => (string) (get_post_meta($post_id, '_myliba_process_title', true) ?: ($title !== '' ? $title . ' süreci' : 'Çalışma Süreci')),
        'process_lead' => (string) (get_post_meta($post_id, '_myliba_process_lead', true) ?: ($item['process_lead'] ?? 'Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim ritminin parçasıdır.')),
        'cta_eyebrow' => (string) (get_post_meta($post_id, '_myliba_cta_eyebrow', true) ?: '30 dakikalık keşif görüşmesi'),
        'cta_title' => (string) (get_post_meta($post_id, '_myliba_cta_title', true) ?: 'İhtiyacınıza uygun yolculuğu birlikte tasarlayalım.'),
        'cta_text' => (string) (get_post_meta($post_id, '_myliba_cta_text', true) ?: 'Kurumunuzun hedeflerini dinleyelim; doğru programı, kapsamı ve çalışma modelini birlikte netleştirelim.'),
        'cta_button_label' => (string) (get_post_meta($post_id, '_myliba_cta_button_label', true) ?: 'Görüşme planlayın'),
        'cta_button_url' => (string) (get_post_meta($post_id, '_myliba_cta_url', true) ?: '/tr/iletisim/'),
        'cta_secondary_label' => 'Tüm çözümleri görün',
        'cta_secondary_url' => '/tr/cozumler/',
        'cta_hide' => '0',
    ], 'collections' => [
        'benefits' => array_map(static fn (string $text): array => ['text' => $text], $benefits_raw),
        'audiences' => array_map(static fn (string $text): array => ['text' => $text], $audiences_raw),
        'metrics' => array_map(static fn (array $row): array => ['title' => $row[0], 'text' => $row[1]], $metrics_raw),
        'steps' => array_map(static fn (array $row): array => ['title' => $row[0], 'text' => $row[1]], $steps_raw),
    ]];
}

function report_definition(): array
{
    return [
        'label' => 'Rapor & Trend İçeriği',
        'groups' => [
            'hero' => [
                'label' => 'Hero ve Genel Bilgiler',
                'fields' => [
                    'kicker' => ['text', 'Üst etiket (Pill)'],
                    'hero_title' => ['textarea', 'Rapor başlığı'],
                    'hero_summary' => ['textarea', 'Yönetici özeti / Lead'],
                    'read_time' => ['text', 'Okuma süresi / Sayfa sayısı (Örn: 10 dk okuma &middot; 24 Sayfa)'],
                    'primary_cta_label' => ['text', 'Ana buton etiketi (Örn: Raporu İnceleyin / İndirin)'],
                    'primary_cta_url' => ['text', 'Ana buton bağlantısı'],
                ],
            ],
            'overview' => [
                'label' => 'Araştırma Kapsamı & Arka Plan',
                'fields' => [
                    'overview_eyebrow' => ['text', 'Üst etiket'],
                    'overview_title' => ['textarea', 'Bölüm başlığı'],
                    'overview_text' => ['textarea', 'Açıklama metni'],
                ],
            ],
            'findings' => [
                'label' => 'Öne Çıkan Bulgular & Veriler',
                'fields' => [
                    'findings_eyebrow' => ['text', 'Üst etiket'],
                    'findings_title' => ['textarea', 'Bölüm başlığı'],
                    'findings_lead' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'key_insights' => ['label' => 'Veri & İçgörü kartları', 'fields' => [
                        'stat' => ['text', 'Oran / Sayı (Örn: %78, 2.4x)'],
                        'title' => ['text', 'Bulgu başlığı'],
                        'text' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'takeaways' => [
                'label' => 'Stratejik Öneriler & Çıkarımlar',
                'fields' => [
                    'takeaways_eyebrow' => ['text', 'Üst etiket'],
                    'takeaways_title' => ['textarea', 'Bölüm başlığı'],
                    'takeaways_lead' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'takeaways_list' => ['label' => 'Öneri maddeleri', 'fields' => [
                        'title' => ['text', 'Madde başlığı'],
                        'text' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'faq' => [
                'label' => 'Sıkça Sorulan Sorular',
                'fields' => [
                    'faq_eyebrow' => ['text', 'Üst etiket'],
                    'faq_title' => ['textarea', 'Başlık'],
                ],
                'collections' => [
                    'faqs' => ['label' => 'Sorular', 'fields' => [
                        'question' => ['text', 'Soru'],
                        'answer' => ['textarea', 'Yanıt'],
                    ]],
                ],
            ],
            'cta' => [
                'label' => 'Final Aksiyon Çağrısı (CTA)',
                'fields' => [
                    'cta_eyebrow' => ['text', 'Üst etiket'],
                    'cta_title' => ['textarea', 'Başlık'],
                    'cta_text' => ['textarea', 'Açıklama'],
                    'cta_button_label' => ['text', 'Ana buton etiketi'],
                    'cta_button_url' => ['text', 'Ana buton bağlantısı'],
                    'cta_secondary_label' => ['text', 'İkincil buton etiketi'],
                    'cta_secondary_url' => ['text', 'İkincil buton bağlantısı'],
                    'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
                ],
            ],
        ],
    ];
}

function report_defaults(int $post_id): array
{
    $post = get_post($post_id);
    $title = (string) (get_post_meta($post_id, '_myliba_hero_title', true) ?: ($post instanceof \WP_Post ? get_the_title($post) : ''));
    $kicker = (string) (get_post_meta($post_id, '_myliba_eyebrow', true) ?: get_post_meta($post_id, '_myliba_label', true) ?: 'Araştırma & Trend Raporu');
    $summary = (string) (get_post_meta($post_id, '_myliba_hero_subtitle', true) ?: ($post instanceof \WP_Post ? get_post_field('post_excerpt', $post) : '') ?: 'İş dünyasını, performans kültürünü ve liderliği şekillendiren güncel araştırmalar ve uygulanabilir içgörüler.');
    $overview_text = (string) (get_post_meta($post_id, '_myliba_problem', true) ?: ($post instanceof \WP_Post ? get_post_field('post_content', $post) : '') ?: 'Bu araştırma; kurumların performans, hedef hizalanması ve liderlik pratiklerini veriye dayalı olarak nasıl dönüştürdüğünü kapsamlı bir çerçevede inceler.');
    $findings_lead = (string) (get_post_meta($post_id, '_myliba_solution', true) ?: 'Farklı sektörlerden elde edilen veriler ve saha araştırmaları doğrultusunda öne çıkan kritik içgörüler.');
    $cta_label = (string) (get_post_meta($post_id, '_myliba_cta_label', true) ?: 'İletişime Geçin');
    $cta_url = (string) (get_post_meta($post_id, '_myliba_cta_url', true) ?: '/tr/iletisim/');

    $meta_benefits = function_exists('myliba_lines') ? \myliba_lines((string) get_post_meta($post_id, '_myliba_benefits', true)) : [];
    $takeaways = [];
    if (!empty($meta_benefits)) {
        foreach ($meta_benefits as $b) {
            $takeaways[] = ['title' => $b, 'text' => ''];
        }
    } else {
        $takeaways = [
            ['title' => 'Veriyle Konuşan Karar Mekanizmaları', 'text' => 'Performans ve terfi kararlarını anlık kanıtlara dayandırın.'],
            ['title' => 'Sürekli Diyalog ve İleri Bildirim', 'text' => 'Gelişimi yıl sonu formlarından çıkarıp haftalık rutinlere taşıyın.'],
            ['title' => 'Şeffaf Hedef ve OKR Uyumu', 'text' => 'Tüm ekipleri tek bir stratejik kutup yıldızı etrafında hizalayın.'],
        ];
    }

    $meta_faqs = function_exists('myliba_faq_pairs') ? \myliba_faq_pairs((string) get_post_meta($post_id, '_myliba_faq_items', true)) : [];
    $faqs = [];
    if (!empty($meta_faqs)) {
        foreach ($meta_faqs as $f) {
            $faqs[] = ['question' => $f['question'] ?? '', 'answer' => $f['answer'] ?? ''];
        }
    }

    return [
        'fields' => [
            'kicker' => $kicker,
            'hero_title' => $title,
            'hero_summary' => $summary,
            'read_time' => '10 dk okuma',
            'primary_cta_label' => 'Raporu İnceleyin',
            'primary_cta_url' => '#arastirma-detayi',
            'overview_eyebrow' => 'Yönetici Özeti',
            'overview_title' => 'Araştırmanın Amacı ve Kapsamı',
            'overview_text' => $overview_text,
            'findings_eyebrow' => 'Veri ve Trendler',
            'findings_title' => 'Öne Çıkan Bulgular',
            'findings_lead' => $findings_lead,
            'takeaways_eyebrow' => 'Uygulama Adımları',
            'takeaways_title' => 'Kurumlar İçin Stratejik Öneriler',
            'takeaways_lead' => 'Araştırma sonuçlarını organizasyonunuzda somut gelişim aksiyonlarına dönüştürme rehberi.',
            'faq_eyebrow' => 'Merak Edilenler',
            'faq_title' => 'Sıkça Sorulan Sorular',
            'cta_eyebrow' => 'Dönüşüm Yolculuğu',
            'cta_title' => 'Bu İçgörüleri Kurumunuzda Hayata Geçirin.',
            'cta_text' => 'Kurumunuza özel yüksek performans kültürü modelini birlikte tasarlayalım.',
            'cta_button_label' => $cta_label,
            'cta_button_url' => $cta_url,
            'cta_secondary_label' => 'Tüm Raporları Görün',
            'cta_secondary_url' => '/tr/raporlar-ve-trendler/',
            'cta_hide' => '0',
        ],
        'collections' => [
            'key_insights' => [
                ['stat' => '%78', 'title' => 'Anlık Geri Bildirim İhtiyacı', 'text' => 'Çalışanların %78’i yıllık notlama yerine sürekli ve yapıcı ileri bildirim bekliyor.'],
                ['stat' => '2.4×', 'title' => 'Hizalanma ve Verimlilik Artışı', 'text' => 'OKR ve canlı hedef takibi yapan şirketler hedeflerine 2.4 kat daha hızlı ulaşıyor.'],
                ['stat' => '%85', 'title' => 'Psikolojik Güvenlik Etkisi', 'text' => 'Açık iletişim kültürüne sahip ekiplerde inovasyon ve problem çözme hızı %85 artıyor.'],
            ],
            'takeaways_list' => $takeaways,
            'faqs' => $faqs,
        ],
    ];
}

function ebook_definition(): array
{
    return [
        'label' => 'e-Kitap İçeriği',
        'groups' => [
            'hero' => [
                'label' => 'Hero ve İndirme Bilgileri',
                'fields' => [
                    'kicker' => ['text', 'Üst etiket'],
                    'hero_title' => ['textarea', 'e-Kitap başlığı'],
                    'hero_summary' => ['textarea', 'e-Kitap açıklaması / Lead'],
                    'download_cta_label' => ['text', 'İndirme buton etiketi (Örn: e-Kitabı Ücretsiz İndirin)'],
                    'download_file_url' => ['text', 'İndirme linki / PDF URL'],
                ],
            ],
            'details' => [
                'label' => 'Kitap Özeti & Bölümler',
                'fields' => [
                    'details_eyebrow' => ['text', 'Üst etiket'],
                    'details_title' => ['textarea', 'Bölüm başlığı'],
                    'details_text' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'chapters' => ['label' => 'Bölüm başlıkları', 'fields' => [
                        'number' => ['text', 'Bölüm no (Örn: 01)'],
                        'title' => ['text', 'Bölüm başlığı'],
                        'text' => ['textarea', 'Bölüm açıklaması'],
                    ]],
                    'key_takeaways' => ['label' => 'Kazanımlar', 'fields' => [
                        'text' => ['text', 'Kazanım maddesi'],
                    ]],
                ],
            ],
            'cta' => [
                'label' => 'Final CTA',
                'fields' => [
                    'cta_eyebrow' => ['text', 'Üst etiket'],
                    'cta_title' => ['textarea', 'Başlık'],
                    'cta_text' => ['textarea', 'Açıklama'],
                    'cta_button_label' => ['text', 'Ana buton etiketi'],
                    'cta_button_url' => ['text', 'Ana buton bağlantısı'],
                    'cta_secondary_label' => ['text', 'İkincil buton etiketi'],
                    'cta_secondary_url' => ['text', 'İkincil buton bağlantısı'],
                    'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
                ],
            ],
        ],
    ];
}

function ebook_defaults(int $post_id): array
{
    $post = get_post($post_id);
    $title = (string) (get_post_meta($post_id, '_myliba_hero_title', true) ?: ($post instanceof \WP_Post ? get_the_title($post) : ''));
    $kicker = (string) (get_post_meta($post_id, '_myliba_eyebrow', true) ?: get_post_meta($post_id, '_myliba_label', true) ?: 'e-Kitap & Rehber');
    $summary = (string) (get_post_meta($post_id, '_myliba_hero_subtitle', true) ?: ($post instanceof \WP_Post ? get_post_field('post_excerpt', $post) : '') ?: 'Yüksek performans kültürü ve modern yönetim pratikleri üzerine indirilebilir uygulama rehberi.');
    $details_text = (string) (get_post_meta($post_id, '_myliba_problem', true) ?: ($post instanceof \WP_Post ? get_post_field('post_content', $post) : '') ?: 'Bu e-kitap, organizasyonlarda performansı parazitlerden arındırarak potansiyeli açığa çıkarma yöntemlerini pratik örneklerle sunar.');

    $meta_benefits = function_exists('myliba_lines') ? \myliba_lines((string) get_post_meta($post_id, '_myliba_benefits', true)) : [];
    $takeaways = array_map(static fn (string $b): array => ['text' => $b], $meta_benefits);
    if (empty($takeaways)) {
        $takeaways = [
            ['text' => 'Performansı not vermeden canlı verilerle yönetme metodolojisi'],
            ['text' => 'OKR ve KPI dengesini kuran stratejik hedef mimarisi'],
            ['text' => 'Ekiplerde psikolojik güvenlik ve geri-ileri bildirim ortamı yaratma'],
        ];
    }

    return [
        'fields' => [
            'kicker' => $kicker,
            'hero_title' => $title,
            'hero_summary' => $summary,
            'download_cta_label' => 'e-Kitabı İndirin',
            'download_file_url' => (string) (get_post_meta($post_id, '_myliba_cta_url', true) ?: '/tr/iletisim/'),
            'details_eyebrow' => 'İçerik Detayları',
            'details_title' => 'Bu Kitapta Neler Bulacaksınız?',
            'details_text' => $details_text,
            'cta_eyebrow' => 'Dönüşüm Adımı',
            'cta_title' => 'Yüksek Performans Kültürünü Şirketinizde İnşa Edin',
            'cta_text' => 'Uygulama ve dönüşüm yolculuğunuzu uzmanlarımızla birlikte planlayın.',
            'cta_button_label' => 'Uzmanlarımızla Görüşün',
            'cta_button_url' => '/tr/iletisim/',
            'cta_secondary_label' => 'Tüm e-Kitapları Görün',
            'cta_secondary_url' => '/tr/e-kitaplar/',
            'cta_hide' => '0',
        ],
        'collections' => [
            'chapters' => [
                ['number' => '01', 'title' => 'Yönetim Formülü', 'text' => 'Performans = Potansiyel − Müdahale denklemi.'],
                ['number' => '02', 'title' => 'Canlı Hedef Ritmi', 'text' => 'Stratejiden günlük aksiyonlara kesintisiz hizalanma.'],
                ['number' => '03', 'title' => 'Adil Karar Mekanizması', 'text' => '%100 objektif verilerle çalışanları geliştirme.'],
            ],
            'key_takeaways' => $takeaways,
        ],
    ];
}

function story_definition(): array
{
    return [
        'label' => 'Biz Kimiz / Hikayemiz Sayfası İçeriği',
        'groups' => [
            'hero' => [
                'label' => 'Hero & Manifesto',
                'fields' => [
                    'hero_eyebrow' => ['text', 'Üst etiket'],
                    'hero_title' => ['textarea', 'Başlık'],
                    'hero_lead' => ['textarea', 'Açıklama'],
                    'hero_primary_label' => ['text', 'Ana buton etiketi'],
                    'hero_secondary_label' => ['text', 'İkincil buton etiketi'],
                ],
                'collections' => [
                    'hero_badges' => ['label' => 'Rozetler', 'fields' => ['label' => ['text', 'Rozet etiketi']]],
                ],
            ],
            'formula' => [
                'label' => 'Yönetim Formülü',
                'fields' => [
                    'formula_eyebrow' => ['text', 'Üst etiket'],
                    'formula_title' => ['textarea', 'Başlık'],
                    'formula_lead' => ['textarea', 'Açıklama'],
                    'formula_badge' => ['text', 'Formül rozeti'],
                    'formula_meta' => ['text', 'Formül kaynak/meta'],
                    'formula_result_tag' => ['text', 'Sonuç etiketi'],
                    'formula_result_title' => ['text', 'Sonuç başlığı'],
                    'formula_result_desc' => ['textarea', 'Sonuç açıklaması'],
                    'formula_potential_tag' => ['text', 'Potansiyel etiketi'],
                    'formula_potential_title' => ['text', 'Potansiyel başlığı'],
                    'formula_potential_desc' => ['textarea', 'Potansiyel açıklaması'],
                    'formula_interference_tag' => ['text', 'Müdahale etiketi'],
                    'formula_interference_title' => ['text', 'Müdahale başlığı'],
                    'formula_interference_desc' => ['textarea', 'Müdahale açıklaması'],
                    'formula_leverage_title' => ['text', 'Kaldıraç başlığı'],
                    'formula_leverage_text' => ['textarea', 'Kaldıraç açıklaması'],
                ],
            ],
            'why' => [
                'label' => 'Karşılaştırma (Neden Myliba)',
                'fields' => [
                    'why_eyebrow' => ['text', 'Üst etiket'],
                    'why_title' => ['textarea', 'Başlık'],
                    'why_lead' => ['textarea', 'Açıklama'],
                    'why_manifesto_text' => ['textarea', 'Manifesto metni'],
                ],
                'collections' => [
                    'comparisons' => ['label' => 'Karşılaştırma kartları', 'fields' => [
                        'problem_label' => ['text', 'Problem etiketi'],
                        'problem_title' => ['text', 'Problem başlığı'],
                        'problem_desc' => ['textarea', 'Problem açıklaması'],
                        'solution_label' => ['text', 'Çözüm etiketi'],
                        'solution_title' => ['text', 'Çözüm başlığı'],
                        'solution_desc' => ['textarea', 'Çözüm açıklaması'],
                    ]],
                ],
            ],
            'pillars' => [
                'label' => '4 Temel Sütun (Neler Yapıyoruz)',
                'fields' => [
                    'pillars_eyebrow' => ['text', 'Üst etiket'],
                    'pillars_title' => ['textarea', 'Başlık'],
                    'pillars_lead' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'pillars' => ['label' => 'Sütun kartları', 'fields' => [
                        'badge' => ['text', 'Rozet'],
                        'number' => ['text', 'Numara'],
                        'icon' => ['text', 'İkon'],
                        'title' => ['text', 'Başlık'],
                        'desc' => ['textarea', 'Açıklama'],
                        'tags' => ['textarea', 'Etiketler (her satıra bir etiket)'],
                        'link_label' => ['text', 'Link etiketi'],
                        'link_target' => ['text', 'Link hedefi (products, academy, development, solutions)'],
                    ]],
                ],
            ],
            'stats' => [
                'label' => 'Etki ve İstatistikler',
                'fields' => [
                    'stats_eyebrow' => ['text', 'Üst etiket'],
                    'stats_title' => ['textarea', 'Başlık'],
                    'stats_lead' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'stats' => ['label' => 'İstatistikler', 'fields' => [
                        'value' => ['text', 'Değer'],
                        'label' => ['text', 'Etiket'],
                        'desc' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'values' => [
                'label' => 'Değerlerimiz',
                'fields' => [
                    'values_eyebrow' => ['text', 'Üst etiket'],
                    'values_title' => ['textarea', 'Başlık'],
                    'values_lead' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'values' => ['label' => 'Değer kartları', 'fields' => [
                        'badge' => ['text', 'Rozet'],
                        'title' => ['text', 'Başlık'],
                        'desc' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'cta' => [
                'label' => 'Dönüşüm Alanı (CTA)',
                'fields' => [
                    'cta_eyebrow' => ['text', 'Üst etiket'],
                    'cta_title' => ['textarea', 'Başlık'],
                    'cta_lead' => ['textarea', 'Açıklama'],
                    'cta_primary_label' => ['text', 'Ana buton etiketi'],
                    'cta_primary_url' => ['text', 'Ana buton bağlantısı (Örn: /tr/iletisim/)'],
                    'cta_secondary_label' => ['text', 'İkincil buton etiketi'],
                    'cta_secondary_url' => ['text', 'İkincil buton bağlantısı (Örn: /tr/demo/)'],
                    'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
                ],
            ],
        ],
    ];
}

function story_defaults(): array
{
    return [
        'fields' => [
            'hero_eyebrow' => 'Biz Kimiz? / Myliba Felsefesi',
            'hero_title' => "Geleceğin Organizasyonlarını\nİnsan ve Teknolojiyi Birleştirerek İnşa Ediyoruz.",
            'hero_lead' => 'Myliba; hantal hiyerarşileri esneten, organizasyonları geleceğin esnek çalışma dünyasına hazırlayan ve yapay zekâ destekli altyapıyı ICF onaylı kültürel yönetim modeliyle birleştiren dünyanın ilk ve tek bütünleşik platformudur.',
            'hero_primary_label' => 'Demo Talep Edin',
            'hero_secondary_label' => 'Felsefemizi İnceleyin',
            'formula_eyebrow' => 'Yönetim Yaklaşımımız',
            'formula_title' => 'Performansı Artırmak İçin Baskıyı Değil, Engelleri Azaltıyoruz',
            'formula_lead' => 'Geleneksel yönetim modelleri performansı artırmak için çalışan üzerindeki baskıyı ve kontrolü artırır. Biz ise performansı ortaya çıkaran formüle inanıyoruz.',
            'formula_badge' => 'Performans Formülü',
            'formula_meta' => 'Timothy Gallwey & Modern Yönetim Bilimi',
            'formula_result_tag' => 'Çıktı',
            'formula_result_title' => 'Performans',
            'formula_result_desc' => 'Ekiplerin gerçek iş çıktısı ve değer üretme kapasitesi.',
            'formula_potential_tag' => 'Girdi',
            'formula_potential_title' => 'Potansiyel',
            'formula_potential_desc' => 'Çalışanın ve ekibin sahip olduğu bilgi, beceri ve içsel motivasyon.',
            'formula_interference_tag' => 'Filtre',
            'formula_interference_title' => 'Müdahale',
            'formula_interference_desc' => 'Bürokrasi, belirsizlik, mikroyönetim, dedikodu ve korku kültürü.',
            'formula_leverage_title' => 'Temel Kaldıracımız:',
            'formula_leverage_text' => 'Potansiyeli artırmak yetmez; performansı patlatmak için müdahaleyi sıfıra indirmek gerekir. Myliba Yazılım ve Çözümleri tam olarak bu engelleri kaldırmak için tasarlandı.',
            'why_eyebrow' => 'Neden Myliba?',
            'why_title' => 'Geleneksel Yönetim vs. Myliba Yaklaşımı',
            'why_lead' => 'Eski nesil İK süreçleri ile yeni nesil yüksek performans kültürü arasındaki farklar.',
            'why_manifesto_text' => "Biz şuna inanıyoruz: Doğru hedefler (OKR), sürekli diyalog (CFR) ve şeffaf bir kültür olduğunda; çalışanlar yönetilmeye ihtiyaç duymaz, liderlik eder.",
            'pillars_eyebrow' => 'Bütünleşik Ekosistem',
            'pillars_title' => 'Neler Yapıyoruz? 4 Temel Sütun Üzerinde Dönüşüm',
            'pillars_lead' => 'Yazılım, akademi, analiz ve danışmanlığı tek bir çatı altında birleştirerek kurumunuzun görünmez işletim sistemini kuruyoruz.',
            'stats_eyebrow' => 'Kanıtlanmış Etki',
            'stats_title' => 'Rakamlarla Myliba Dönüşümü',
            'stats_lead' => 'Birlikte çalıştığımız organizasyonlarda elde ettiğimiz somut iş sonuçları.',
            'values_eyebrow' => 'Değerlerimiz',
            'values_title' => 'Bizi Biz Yapan İlkeler',
            'values_lead' => 'Her gün kararlarımızı ve ürünlerimizi şekillendiren temel inançlarımız.',
            'cta_eyebrow' => 'Dönüşüm Zamanı',
            'cta_title' => 'Şirketinizin Geleceğini Birlikte İnşa Edelim',
            'cta_lead' => 'Gelin, kurumunuzun performans ve kültür yolculuğunu birlikte başlatalım.',
            'cta_primary_label' => 'Hemen İletişime Geçin',
            'cta_primary_url' => '/tr/iletisim/',
            'cta_secondary_label' => 'Demo Talep Edin',
            'cta_secondary_url' => '/tr/demo/',
            'cta_hide' => '0',
        ],
        'collections' => [
            'hero_badges' => [
                ['label' => 'Yapay Zekâ Destekli Platform'],
                ['label' => 'ICF Onaylı Kültür Modeli'],
                ['label' => 'Bütünleşik Ekosistem'],
            ],
            'comparisons' => [
                [
                    'problem_label' => 'Geleneksel Sistem',
                    'problem_title' => 'Yılda Bir Kez Notlama',
                    'problem_desc' => 'Yıl sonu doldurulan sübjektif formlar ve geçmişe dönük yargılayıcı puanlar.',
                    'solution_label' => 'Myliba Modeli',
                    'solution_title' => 'Canlı & Sürekli Gelişim',
                    'solution_desc' => '%100 objektif veriler, anlık geri bildirimler ve haftalık aksiyon takibi.',
                ],
                [
                    'problem_label' => 'Geleneksel Sistem',
                    'problem_title' => 'Kopuk Araçlar ve Silolar',
                    'problem_desc' => 'Farklı yazılımlarda kaybolan hedefler, anketler ve eğitim süreçleri.',
                    'solution_label' => 'Myliba Modeli',
                    'solution_title' => 'Tek Çatı Altında Entegrasyon',
                    'solution_desc' => 'OKR, KPI, 1:1 görüşmeler, kültür analizi ve akademi tek platformda.',
                ],
            ],
            'pillars' => [
                [
                    'badge' => 'Teknoloji',
                    'number' => '01',
                    'icon' => '⚡',
                    'title' => 'Myliba Yazılım',
                    'desc' => 'Formsuz, canlı verilerle hedef, performans ve adil karar yönetimi sunan yeni nesil platform.',
                    'tags' => "Native OKR\nKPI & Aksiyonlar\nNineBox Analitiği\n1:1 & Geri Bildirim",
                    'link_label' => 'Yazılımı İnceleyin',
                    'link_target' => 'products',
                ],
                [
                    'badge' => 'Gelişim',
                    'number' => '02',
                    'icon' => '🎓',
                    'title' => 'OKR & Kültür Akademisi',
                    'desc' => 'Dünyanın ilk ve tek ICF onaylı OKR ve kültür koçluğu sertifika programı ve liderlik yolculukları.',
                    'tags' => "ICF Akreditasyonu\nİşbaşı Liderlik\nTakım Koçluğu",
                    'link_label' => 'Akademiye Katılın',
                    'link_target' => 'academy',
                ],
                [
                    'badge' => 'İçgörü',
                    'number' => '03',
                    'icon' => '📊',
                    'title' => 'Kültür & Bağlılık Analizi',
                    'desc' => 'Dedikodu, mobbing ve adaletsizlik gibi kültürel virüsleri erken tespit eden ölçümleme sistemi.',
                    'tags' => "eNPS & İsteklilik\nKültürel Uyum\n360° Değerlendirme",
                    'link_label' => 'Analizi Keşfedin',
                    'link_target' => 'solutions',
                ],
                [
                    'badge' => 'Dönüşüm',
                    'number' => '04',
                    'icon' => '🤝',
                    'title' => 'Stratejik Dönüşüm Danışmanlığı',
                    'desc' => 'Stratejik hedef haritası oluşturma ve yüksek performans kültürünü kuruma özel tasarlama.',
                    'tags' => "Hedef Haritası\nOtonom Ekipler\nYönetim Ritmi",
                    'link_label' => 'Danışmanlık Alın',
                    'link_target' => 'development',
                ],
            ],
            'stats' => [
                ['value' => '%85', 'label' => 'Maliyet Tasarrufu', 'desc' => 'Farklı araçları tek sistemde birleştirerek İK bütçelerinde sağlanan tasarruf.'],
                ['value' => '+40', 'label' => 'Gün / Yıl', 'desc' => 'Bürokratik formları kaldırarak ekiplere kazandırılan stratejik çalışma süresi.'],
                ['value' => '2×', 'label' => 'Performans Artışı', 'desc' => 'Sosyal taahhüt ve anlık geri bildirim döngüleriyle sağlanan verimlilik.'],
            ],
            'values' => [
                ['badge' => 'Şeffaflık', 'title' => 'Radikal Dürüstlük ve Netlik', 'desc' => 'Hedeflerin ve geri bildirimlerin açık olduğu yerde siyaset biter, başarı başlar.'],
                ['badge' => 'İnsan Odaklılık', 'title' => 'Psikolojik Güvenlik', 'desc' => 'Hata yapmaktan korkmayan, fikirlerini özgürce söyleyen ekipler dönüşümü yönetir.'],
                ['badge' => 'Süreklilik', 'title' => 'Günlük Çalışma Ritmi', 'desc' => 'Gelişim yılda bir kez yaşanan bir olay değil; her gün tekrarlanan bir alışkanlıktır.'],
            ],
        ],
    ];
}

function ethics_definition(): array
{
    return [
        'label' => 'Etik Hat Sayfası İçeriği',
        'groups' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    'hero_title' => ['textarea', 'Başlık'],
                    'hero_lead' => ['textarea', 'Açıklama'],
                    'hero_primary_label' => ['text', 'Ana buton etiketi'],
                    'hero_secondary_label' => ['text', 'İkincil buton etiketi'],
                ],
            ],
            'intro' => [
                'label' => 'Giriş',
                'fields' => [
                    'intro_eyebrow' => ['text', 'Üst etiket'],
                    'intro_title' => ['textarea', 'Başlık'],
                    'intro_lead' => ['textarea', 'Açıklama'],
                ],
            ],
            'why' => [
                'label' => 'Neden Etik Hat?',
                'fields' => [
                    'why_eyebrow' => ['text', 'Üst etiket'],
                    'why_title' => ['textarea', 'Başlık'],
                    'why_lead' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'why_items' => ['label' => 'Nedenler', 'fields' => [
                        'number' => ['text', 'Numara'],
                        'title' => ['text', 'Başlık'],
                        'text' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'scope' => [
                'label' => 'Kapsam',
                'fields' => [
                    'scope_eyebrow' => ['text', 'Üst etiket'],
                    'scope_title' => ['textarea', 'Başlık'],
                    'scope_lead' => ['textarea', 'Açıklama'],
                ],
                'collections' => [
                    'scope_items' => ['label' => 'Kapsam maddeleri', 'fields' => [
                        'icon' => ['text', 'İkon'],
                        'title' => ['text', 'Başlık'],
                        'text' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'cta' => [
                'label' => 'Dönüşüm (CTA)',
                'fields' => [
                    'cta_eyebrow' => ['text', 'Üst etiket'],
                    'cta_title' => ['textarea', 'Başlık'],
                    'cta_lead' => ['textarea', 'Açıklama'],
                    'cta_primary_label' => ['text', 'Ana buton etiketi'],
                    'cta_primary_url' => ['text', 'Ana buton bağlantısı (Örn: /tr/iletisim/)'],
                    'cta_secondary_label' => ['text', 'İkincil buton etiketi'],
                    'cta_secondary_url' => ['text', 'İkincil buton bağlantısı (Örn: /tr/demo/)'],
                    'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
                ],
            ],
        ],
    ];
}

function ethics_defaults(): array
{
    return [
        'fields' => [
            'hero_title' => 'Kurumunuz İçin Güvenli, Bağımsız ve Gizli Etik Bildirim Hattı',
            'hero_lead' => 'Çalışanlarınızın ve paydaşlarınızın etik ihlalleri, mobbing, usulsüzlük ve suiistimalleri güvenle bildirebileceği tarafsız çözüm.',
            'hero_primary_label' => 'Etik Danışmanlarımızla Görüşün',
            'hero_secondary_label' => 'Kapsamı İnceleyin',
            'intro_eyebrow' => 'Güvenilir Yönetişim',
            'intro_title' => 'Kurumsal İtibarı Korumanın En Etkili Yolu',
            'intro_lead' => 'Etik hat, şirket içinde şeffaflığı ve adaleti tesis ederken olası riskleri kriz haline gelmeden önce tespit etmenizi sağlar.',
            'why_eyebrow' => 'Neden Etik Hat?',
            'why_title' => 'Kurumunuza Sağladığı Temel Faydalar',
            'why_lead' => 'Bağımsız etik hattı kurarak çalışan bağlılığını ve kurumsal güveni artırın.',
            'scope_eyebrow' => 'Bildirim Kapsamı',
            'scope_title' => 'Hangi Konularda Bildirim Alınabilir?',
            'scope_lead' => 'Uluslararası standartlarda etik ve uyum yönetimi.',
            'cta_eyebrow' => 'İletişime Geçin',
            'cta_title' => 'Kurumunuza Özel Etik Hat Sürecini Birlikte Kuralım',
            'cta_lead' => 'Uzmanlarımızla görüşerek gizlilik, mevzuat ve devreye alma adımlarını planlayın.',
            'cta_primary_label' => 'Bize Ulaşın',
            'cta_primary_url' => '/tr/iletisim/',
            'cta_secondary_label' => 'Demo Talep Edin',
            'cta_secondary_url' => '/tr/demo/',
            'cta_hide' => '0',
        ],
        'collections' => [
            'why_items' => [
                ['number' => '01', 'title' => 'Tam Gizlilik ve Güvenlik', 'text' => 'Bildirim sahiplerinin kimliği korunur, misilleme riski ortadan kalkar.'],
                ['number' => '02', 'title' => 'Erken Uyarı Sistemi', 'text' => 'Hukuki ve finansal riskler büyümeden tespit edilip çözüme kavuşturulur.'],
                ['number' => '03', 'title' => 'Kurumsal Şeffaflık', 'text' => 'Adil ve hesap verebilir bir yönetim kültürü inşa edilir.'],
            ],
            'scope_items' => [
                ['icon' => '⚖️', 'title' => 'Mobbing ve Ayrımcılık', 'text' => 'İşyerinde psikolojik taciz, ayrımcılık ve haksız uygulamalar.'],
                ['icon' => '🔒', 'title' => 'Mali ve Hukuki Usulsüzlükler', 'text' => 'Yolsuzluk, rüşvet, zimmet ve çıkar çatışması durumları.'],
                ['icon' => '🛡️', 'title' => 'Bilgi Güvenliği İhlalleri', 'text' => 'Gizli şirket bilgilerinin ve kişisel verilerin sızdırılması.'],
            ],
        ],
    ];
}

function faq_definition(): array
{
    return [
        'label' => 'Sıkça Sorulan Sorular',
        'groups' => [
            'hero' => [
                'label' => 'Hero Bölümü',
                'fields' => [
                    'hero_eyebrow' => ['text', 'Üst etiket (Örn: Sıkça Sorulan Sorular)'],
                    'hero_title' => ['textarea', 'Ana Başlık (Örn: Myliba Hakkında Merak Ettiğiniz Her Şey)'],
                    'hero_lead' => ['textarea', 'Açıklama'],
                    'hero_search_placeholder' => ['text', 'Arama kutusu yer tutucu metni (Örn: Soru veya konu arayın...)'],
                ],
            ],
            'categories' => [
                'label' => 'Kategori Filtreleri',
                'fields' => [
                    'category_all' => ['text', 'Tümü Sekmesi Etiketi (Örn: Tümü)'],
                    'category_1' => ['text', '1. Kategori (Örn: Genel & Platform)'],
                    'category_2' => ['text', '2. Kategori (Örn: Yazılım & OKR)'],
                    'category_3' => ['text', '3. Kategori (Örn: Akademi & Eğitim)'],
                    'category_4' => ['text', '4. Kategori (Örn: Danışmanlık & Çözümler)'],
                    'category_5' => ['text', '5. Kategori (Örn: Güvenlik & Entegrasyon)'],
                ],
            ],
            'faqs' => [
                'label' => 'Sorular ve Yanıtlar',
                'collections' => [
                    'faqs' => ['label' => 'Soru & Yanıt Listesi', 'fields' => [
                        'category' => ['text', 'Kategori (Örn: Genel & Platform, Yazılım & OKR, Akademi & Eğitim, Danışmanlık & Çözümler, Güvenlik & Entegrasyon)'],
                        'question' => ['text', 'Soru'],
                        'answer' => ['textarea', 'Yanıt (HTML / Paragraf destekler)'],
                        'tag' => ['text', 'Opsiyonel Etiket / Rozet (Örn: Popüler, ICF Akredite, Güvenlik)'],
                    ]],
                ],
            ],
            'cta' => [
                'label' => 'İletişim & Destek Kartı',
                'fields' => [
                    'cta_eyebrow' => ['text', 'Üst etiket (Örn: Başka Bir Sorunuz mu Var?)'],
                    'cta_title' => ['textarea', 'Başlık (Örn: Uzman Ekibimiz Size Yardımcı Olmaya Hazır)'],
                    'cta_lead' => ['textarea', 'Açıklama'],
                    'cta_primary_label' => ['text', 'Ana buton etiketi (Örn: Demo Talep Edin)'],
                    'cta_primary_url' => ['text', 'Ana buton URL (Boş bırakılırsa demo sayfasına gider)'],
                    'cta_secondary_label' => ['text', 'İkincil buton etiketi (Örn: Bize Ulaşın)'],
                    'cta_secondary_url' => ['text', 'İkincil buton URL (Boş bırakılırsa iletişim sayfasına gider)'],
                    'cta_contact_title' => ['text', 'İletişim kutusu başlığı (Örn: Doğrudan İletişim)'],
                    'cta_contact_text' => ['textarea', 'İletişim açıklaması'],
                    'cta_hide' => ['text', 'CTA Bannerı Gizle (1: gizle, 0: göster)'],
                ],
            ],
        ],
    ];
}

function faq_defaults(int $post_id = 0): array
{
    $lang = '';
    if ($post_id > 0) {
        $lang = (string) get_post_meta($post_id, '_myliba_language', true);
        if ($lang === '') {
            $post = get_post($post_id);
            if ($post instanceof \WP_Post) {
                if (str_contains((string) $post->post_name, 'faq') || str_contains(get_page_uri($post), 'en/')) {
                    $lang = 'en';
                }
            }
        }
    }
    if ($lang === '' && function_exists('myliba_current_language')) {
        $lang = \myliba_current_language();
    }
    if ($lang === '') {
        $lang = 'tr';
    }

    if ($lang === 'en') {
        return [
            'fields' => [
                'hero_eyebrow' => 'Frequently Asked Questions',
                'hero_title' => 'Everything You Need to Know About Myliba',
                'hero_lead' => 'Explore comprehensive answers about Myliba OKR & Performance software, Academy programs, culture & strategy solutions, and enterprise security.',
                'hero_search_placeholder' => 'Search for questions, topics, or features...',
                'category_all' => 'All',
                'category_1' => 'General & Platform',
                'category_2' => 'Software & OKRs',
                'category_3' => 'Academy & Learning',
                'category_4' => 'Consulting & Solutions',
                'category_5' => 'Security & Integration',
                'cta_eyebrow' => 'Still Have Questions?',
                'cta_title' => 'Our Expert Team is Here to Help',
                'cta_lead' => "Didn't find the answer you were looking for? Schedule a meeting with our specialists or request an interactive demo.",
                'cta_primary_label' => 'Request a Demo',
                'cta_primary_url' => '',
                'cta_secondary_label' => 'Contact Us',
                'cta_secondary_url' => '',
                'cta_contact_title' => 'Direct Support',
                'cta_contact_text' => 'We typically respond to inquiries within 24 hours on business days.',
            ],
            'collections' => [
                'faqs' => [
                    [
                        'category' => 'General & Platform',
                        'question' => 'What is Myliba and what core problems does it solve?',
                        'answer' => 'Myliba is an integrated performance, OKR management, and organizational culture ecosystem. It bridges the gap between high-level company strategy and day-to-day execution by combining modern goal tracking (OKRs), continuous feedback, 9-box talent matrix, and ICF-accredited coaching programs.',
                        'tag' => 'Popular',
                    ],
                    [
                        'category' => 'General & Platform',
                        'question' => 'What size organizations is Myliba designed for?',
                        'answer' => 'Myliba scales seamlessly from fast-growing scale-ups (50+ employees) to large enterprise organizations with thousands of employees across multiple subsidiaries and regions.',
                        'tag' => '',
                    ],
                    [
                        'category' => 'General & Platform',
                        'question' => 'How does the trial or demo onboarding process work?',
                        'answer' => 'When you request a demo, our solution consultants conduct a discovery call to understand your organizational structure and goals. We then provide a customized interactive platform demonstration tailored to your workflows.',
                        'tag' => '',
                    ],
                    [
                        'category' => 'Software & OKRs',
                        'question' => 'How does Myliba combine OKRs with Performance Management?',
                        'answer' => 'Unlike rigid legacy HR tools, Myliba unifies ambitious goal setting (OKRs) with continuous performance reviews, 1-on-1 check-ins, and 360-degree feedback in one intuitive dashboard, ensuring alignment without burdensome bureaucracy.',
                        'tag' => 'Core Feature',
                    ],
                    [
                        'category' => 'Software & OKRs',
                        'question' => 'How does the 9-Box Talent Matrix work in Myliba?',
                        'answer' => 'The 9-Box Matrix visually maps employee performance against growth potential. It helps leadership identify top talent, recognize flight risks, and design targeted development paths with actionable objective data.',
                        'tag' => '',
                    ],
                    [
                        'category' => 'Software & OKRs',
                        'question' => 'Can Myliba integrate with our existing HRIS, ERP, and SSO systems?',
                        'answer' => 'Yes. Myliba offers REST APIs, webhook triggers, and turnkey integrations with major enterprise platforms including SAP, Workday, Microsoft Teams, Slack, Azure AD, and Google Workspace for automated user provisioning and Single Sign-On (SSO).',
                        'tag' => 'Enterprise',
                    ],
                    [
                        'category' => 'Academy & Learning',
                        'question' => 'Who is the Myliba OKR & Culture Coaching Program for?',
                        'answer' => 'The program is designed for C-level executives, HR and People & Culture leaders, Agile coaches, transformation leads, and managers who want to master goal-setting leadership and organizational culture design.',
                        'tag' => 'ICF Accredited',
                    ],
                    [
                        'category' => 'Academy & Learning',
                        'question' => 'What value does the ICF 40 CCE accreditation provide?',
                        'answer' => 'Our program provides 40 Continuing Coach Education (CCE) units accredited by the International Coaching Federation (ICF), accelerating your path toward ACC, PCC, or MCC credentials while validating your organizational coaching competence.',
                        'tag' => '',
                    ],
                    [
                        'category' => 'Academy & Learning',
                        'question' => 'Can programs and business simulations be customized for our company?',
                        'answer' => 'Absolutely. We design bespoke corporate cohorts incorporating real company business cases alongside experiential simulation labs like Target Mars and Radical Candor.',
                        'tag' => '',
                    ],
                    [
                        'category' => 'Consulting & Solutions',
                        'question' => 'How does the Culture Analysis & Diagnostic study work?',
                        'answer' => 'Our scientific diagnostic framework evaluates psychological safety, feedback openness, alignment, and core values through surveys, focus groups, and leadership interviews, delivering concrete roadmap reports.',
                        'tag' => '',
                    ],
                    [
                        'category' => 'Consulting & Solutions',
                        'question' => 'How does the independent Whistleblowing & Ethics Line work?',
                        'answer' => 'Myliba provides a secure, anonymous, and encrypted reporting platform compliant with international standards, allowing employees to report misconduct, harassment, or irregularities without fear of retaliation.',
                        'tag' => 'Compliance',
                    ],
                    [
                        'category' => 'Security & Integration',
                        'question' => 'How are data security, KVKK, and GDPR compliances handled?',
                        'answer' => 'Myliba adheres to ISO/IEC 27001 standards, end-to-end TLS 1.3 / AES-256 encryption, role-based access control (RBAC), and localized sovereign cloud hosting ensuring strict KVKK and GDPR compliance.',
                        'tag' => 'Security',
                    ],
                    [
                        'category' => 'Security & Integration',
                        'question' => 'What is the implementation and rollout timeline?',
                        'answer' => 'Standard cloud deployments take 1 to 3 weeks, including data onboarding, SSO setup, admin configuration, and initial champion user training.',
                        'tag' => '',
                    ],
                ],
            ],
        ];
    }

    return [
        'fields' => [
            'hero_eyebrow' => 'Sıkça Sorulan Sorular',
            'hero_title' => 'Myliba Hakkında Merak Ettiğiniz Her Şey',
            'hero_lead' => 'Myliba OKR & Performans Yazılımı, Akademi programları, kültür ve strateji çözümleri, veri güvenliği ve entegrasyon süreçlerine dair en çok merak edilen sorular ve yanıtları.',
            'hero_search_placeholder' => 'Soru, konu veya özellik arayın...',
            'category_all' => 'Tümü',
            'category_1' => 'Genel & Platform',
            'category_2' => 'Yazılım & OKR',
            'category_3' => 'Akademi & Eğitim',
            'category_4' => 'Danışmanlık & Çözümler',
            'category_5' => 'Güvenlik & Entegrasyon',
            'cta_eyebrow' => 'Başka Bir Sorunuz mu Var?',
            'cta_title' => 'Uzman Ekibimiz Size Yardımcı Olmaya Hazır',
            'cta_lead' => 'Aradığınız cevabı bulamadıysanız veya kurumunuza özel ihtiyaçları değerlendirmek isterseniz uzmanlarımızla hemen iletişime geçin.',
            'cta_primary_label' => 'Demo Talep Edin',
            'cta_primary_url' => '',
            'cta_secondary_label' => 'Bize Ulaşın',
            'cta_secondary_url' => '',
            'cta_contact_title' => 'Doğrudan İletişim',
            'cta_contact_text' => 'Mesai saatleri içindeki tüm sorularınıza en geç 24 saat içinde dönüş yapıyoruz.',
        ],
        'collections' => [
            'faqs' => [
                [
                    'category' => 'Genel & Platform',
                    'question' => 'Myliba nedir ve kurumlara ne kazandırır?',
                    'answer' => 'Myliba; strateji, OKR (Hedef ve Temel Sonuçlar), sürekli performans yönetimi, yetenek matrisi ve kurum kültürünü tek bir ekosistemde buluşturan bütünleşik bir platformdur. Hedeflerin sadece panolarda kalmasını engeller; günlük iş ritmine, geri bildirim kültürüne ve ölçülebilir çıktılara dönüştürür.',
                    'tag' => 'Popüler',
                ],
                [
                    'category' => 'Genel & Platform',
                    'question' => 'Myliba hangi büyüklükteki şirketler için uygundur?',
                    'answer' => 'Myliba, 50 kişilik hızlı büyüyen teknoloji ve ölçeklenme şirketlerinden (scale-up), binlerce çalışanı olan çok lokasyonlu holding ve kurumsal şirketlere kadar her ölçekte esnek ve güvenli biçimde çalışacak mimariye sahiptir.',
                    'tag' => '',
                ],
                [
                    'category' => 'Genel & Platform',
                    'question' => 'Demo talebinde bulunduğumda süreç nasıl işliyor?',
                    'answer' => 'Demo formunu doldurduğunuzda danışman ekibimiz 24 saat içinde sizinle iletişime geçer. Kurumunuzun mevcut yapısını ve önceliklerini dinleyerek ihtiyaçlarınıza özel canlı bir platform demosu ve yol haritası sunar.',
                    'tag' => '',
                ],
                [
                    'category' => 'Yazılım & OKR',
                    'question' => 'Myliba OKR ve Performans Yönetimini nasıl entegre eder?',
                    'answer' => 'Geleneksel, yılda bir kez yapılan hantal performans değerlendirmeleri yerine; çeyreklik dinamik OKR hedefleri, sürekli 1:1 görüşmeler, anlık takdir/geri bildirim ve 360 derece yetkinlik değerlendirmelerini tek bir akışta birleştirir.',
                    'tag' => 'Temel Özellik',
                ],
                [
                    'category' => 'Yazılım & OKR',
                    'question' => '9-Kutu (9-Box) Yetenek Matrisi ve Değerlendirme Modülü nasıl çalışır?',
                    'answer' => 'Çalışanların performans çıktıları ile gelişim potansiyellerini çapraz eksende görselleştirir. Liderlerin kritik yetenekleri tespit etmesini, yedekleme planı yapmasını ve terfi/eğitim kararlarını objektif verilere dayandırmasını sağlar.',
                    'tag' => '',
                ],
                [
                    'category' => 'Yazılım & OKR',
                    'question' => 'Mevcut İK (HRIS), ERP ve SSO sistemlerimizle entegrasyon yapılabiliyor mu?',
                    'answer' => 'Evet. Myliba; REST API, webhook ve hazır entegrasyon katmanları sayesinde SAP, Workday, Microsoft Teams, Slack, Azure Active Directory ve Google Workspace gibi kurumsal sistemlerle tam uyumlu çalışır; kullanıcı senkronizasyonu ve Tek Noktadan Giriş (SSO) sağlar.',
                    'tag' => 'Entegrasyon',
                ],
                [
                    'category' => 'Akademi & Eğitim',
                    'question' => 'Myliba OKR & Kültür Koçluğu Programı kimler için uygundur?',
                    'answer' => 'C-level yöneticiler, İnsan ve Kültür liderleri, Agile koçlar, dönüşüm ofisi liderleri, strateji yöneticileri ve kurumunda hedef odaklı ortak çalışma ritmi kurmak isteyen tüm profesyoneller için uygundur.',
                    'tag' => 'ICF Akredite',
                ],
                [
                    'category' => 'Akademi & Eğitim',
                    'question' => 'ICF 40 CCE sertifikası bana ve kurumuma ne kazandırır?',
                    'answer' => 'Program, Uluslararası Koçluk Federasyonu (ICF) tarafından akredite edilmiş 40 CCE kredisi sağlar. Bu kredi hem profesyonel koçluk unvanlama (ACC/PCC/MCC) sürecinizde geçerlidir hem de kurum içi koçluk yetkinliğinizi uluslararası standartta belgeler.',
                    'tag' => '',
                ],
                [
                    'category' => 'Akademi & Eğitim',
                    'question' => 'Kurumumuz için özel sınıf ve simülasyon atölyesi tasarlanabilir mi?',
                    'answer' => 'Evet. Şirketinizin gerçek hedefleri ve dinamikleri doğrultusunda kurum içi özel kohortlar, “Hedef Mars” ve “Radikal Samimiyet” gibi deneyimsel simülasyonlarla zenginleştirilmiş atölyeler tasarlıyoruz.',
                    'tag' => '',
                ],
                [
                    'category' => 'Danışmanlık & Çözümler',
                    'question' => 'Kültür Analizi ve Teşhis çalışması nasıl yürütülür?',
                    'answer' => 'Bilimsel temelli anketler, odak grup görüşmeleri ve liderlik mülakatları ile kurumun psikolojik güvenlik, geri bildirim açıklığı, hedef uyumu ve değer yaşatma dinamikleri ölçümlenir; somut aksiyon raporuna dönüştürülür.',
                    'tag' => '',
                ],
                [
                    'category' => 'Danışmanlık & Çözümler',
                    'question' => 'Bağımsız Etik Bildirim Hattı (Whistleblowing) nasıl çalışır?',
                    'answer' => 'Çalışanların ve paydaşların usulsüzlük, mobbing, ayrımcılık veya suiistimal durumlarını %100 gizli ve güvenli biçimde bildirebilecekleri, bağımsız ve tarafsız bir etik hat çözümüdür. Misilleme riskini ortadan kaldırarak yasal uyumluluk sağlar.',
                    'tag' => 'Uyum & Etik',
                ],
                [
                    'category' => 'Güvenlik & Entegrasyon',
                    'question' => 'Veri güvenliği, KVKK ve GDPR standartlarınız nelerdir?',
                    'answer' => 'Tüm veriler TLS 1.3 ve AES-256 şifreleme standartlarıyla korunur. ISO/IEC 27001 uyumlu altyapımız, yerel veri merkezlerinde barındırma seçeneği ve rol bazlı erişim denetimi (RBAC) ile KVKK ve GDPR mevzuatlarına %100 uyumludur.',
                    'tag' => 'Güvenlik',
                ],
                [
                    'category' => 'Güvenlik & Entegrasyon',
                    'question' => 'Kurulum ve canlıya geçiş ne kadar sürer?',
                    'answer' => 'Bulut (SaaS) kurulumlarımız; veri yükleme, SSO entegrasyonu ve lider/kullanıcı eğitimleri dahil genellikle 1 ila 3 hafta içerisinde başarıyla tamamlanır.',
                    'tag' => '',
                ],
            ],
        ],
    ];
}

function definition(string $schema): array
{
    return match ($schema) {
        'software' => software_definition(),
        'solutions' => solutions_definition(),
        'development' => development_definition(),
        'solution' => solution_definition(),
        'report' => report_definition(),
        'ebook' => ebook_definition(),
        'story' => story_definition(),
        'ethics' => ethics_definition(),
        'faq' => faq_definition(),
        default => [],
    };
}

function defaults(string $schema, int $post_id = 0): array
{
    return match ($schema) {
        'software' => software_defaults(),
        'solutions' => solutions_defaults(),
        'development' => development_defaults(),
        'solution' => solution_defaults($post_id),
        'report' => report_defaults($post_id),
        'ebook' => ebook_defaults($post_id),
        'story' => story_defaults(),
        'ethics' => ethics_defaults(),
        'faq' => faq_defaults($post_id),
        default => ['fields' => [], 'collections' => []],
    };
}

function document(int $post_id, string $schema): array
{
    $definition = definition($schema);
    $defaults = defaults($schema, $post_id);
    
    $target_id = $post_id;
    if (is_preview() || !empty($_GET['preview'])) {
        $autosave = wp_get_post_autosave($post_id);
        if ($autosave instanceof \WP_Post) {
            $raw_preview = get_post_meta($autosave->ID, META_KEY, true);
            if (is_string($raw_preview) && $raw_preview !== '') {
                $target_id = $autosave->ID;
            }
        }
    }

    $raw = get_post_meta($target_id, META_KEY, true);
    if ((!is_string($raw) || $raw === '') && $target_id !== $post_id) {
        $raw = get_post_meta($post_id, META_KEY, true);
    }
    $saved = is_string($raw) && $raw !== '' ? json_decode($raw, true) : [];

    if (!is_array($saved) || ($saved['schema'] ?? $schema) !== $schema) {
        $saved = [];
    }

    $sections = [];
    $order_step = 10;
    foreach ($definition['groups'] ?? [] as $g_key => $g_def) {
        $sections[$g_key] = [
            'key' => $g_key,
            'label' => $g_def['label'] ?? $g_key,
            'enabled' => true,
            'order' => $order_step,
        ];
        $order_step += 10;
    }

    if (isset($saved['sections']) && is_array($saved['sections'])) {
        foreach ($saved['sections'] as $sec) {
            if (is_array($sec) && !empty($sec['key']) && isset($sections[$sec['key']])) {
                $k = $sec['key'];
                $sections[$k]['enabled'] = !empty($sec['enabled']);
                if (isset($sec['order']) && is_numeric($sec['order'])) {
                    $sections[$k]['order'] = (int) $sec['order'];
                }
            }
        }
    }

    uasort($sections, static fn ($a, $b) => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));

    return [
        'schema' => $schema,
        'version' => SCHEMA_VERSION,
        'fields' => array_replace($defaults['fields'], is_array($saved['fields'] ?? null) ? $saved['fields'] : []),
        'collections' => array_replace($defaults['collections'], is_array($saved['collections'] ?? null) ? $saved['collections'] : []),
        'sections' => array_values($sections),
    ];
}

function text(int $post_id, string $schema, string $key): string
{
    $doc = document($post_id, $schema);
    return is_string($doc['fields'][$key] ?? null) ? $doc['fields'][$key] : '';
}

function collection(int $post_id, string $schema, string $key): array
{
    $doc = document($post_id, $schema);
    return is_array($doc['collections'][$key] ?? null) ? $doc['collections'][$key] : [];
}

function sections(int $post_id, string $schema): array
{
    $doc = document($post_id, $schema);
    return $doc['sections'] ?? [];
}

function materialize(int $post_id): bool
{
    $schema = schema_for_post($post_id);
    if ($schema === null) {
        return false;
    }

    $encoded = wp_json_encode(document($post_id, $schema), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $current = (string) get_post_meta($post_id, META_KEY, true);
    if ($current === $encoded) {
        return false;
    }

    return update_post_meta($post_id, META_KEY, wp_slash($encoded)) !== false;
}

function render_page_box(\WP_Post $post): void
{
    $schema = schema_for_post($post);
    if ($schema === null) {
        return;
    }

    $definition = definition($schema);
    $doc = document($post->ID, $schema);
    $sections = $doc['sections'] ?? [];
    wp_nonce_field('myliba_page_content_' . $post->ID, 'myliba_page_content_nonce');

    echo '<p class="description">Bileşenleri sürükleyip bırakarak (veya ▲ ▼ oklarıyla) sıralayabilir, onay kutularıyla dilediğiniz bölümü gizleyebilir ya da gösterebilirsiniz.</p>';
    echo '<input type="hidden" name="myliba_page_content_schema" value="' . esc_attr($schema) . '">';
    echo '<div class="myliba-page-builder" id="myliba-page-builder-list">';

    foreach ($sections as $section) {
        $group_key = $section['key'];
        $group = $definition['groups'][$group_key] ?? null;
        if (!$group) {
            continue;
        }

        $enabled = !empty($section['enabled']);
        $order = (int) ($section['order'] ?? 10);
        $panel_id = 'myliba-page-group-' . sanitize_html_class($group_key);
        $field_count = count($group['fields'] ?? []) + count($group['collections'] ?? []);

        echo '<div class="myliba-builder-card' . ($enabled ? '' : ' is-disabled') . '" data-group-key="' . esc_attr($group_key) . '" draggable="true">';
        
        // Card Head
        echo '<div class="myliba-builder-card__head">';
        echo '<span class="myliba-builder-card__handle" title="Sıralamak için sürükleyin" aria-hidden="true">⋮⋮</span>';
        echo '<div class="myliba-builder-card__main">';
        echo '<div class="myliba-builder-card__title-row">';
        printf(
            '<label class="myliba-builder-card__enabled"><input type="checkbox" name="myliba_page_content[sections][%1$s][enabled]" value="1" %2$s> <span class="myliba-builder-card__title-text">%3$s</span></label>',
            esc_attr($group_key),
            checked($enabled, true, false),
            esc_html($group['label'])
        );
        echo '</div>';
        echo '<span class="myliba-builder-card__summary">' . esc_html(sprintf('%d alan/ayar', $field_count)) . '</span>';
        echo '</div>';

        // Sort buttons and Order
        echo '<div class="myliba-builder-card__controls">';
        echo '<button type="button" class="button button-small myliba-btn-move-up" title="Yukarı taşı">▲</button>';
        echo '<button type="button" class="button button-small myliba-btn-move-down" title="Aşağı taşı">▼</button>';
        printf(
            '<input class="myliba-builder-card__order" type="number" name="myliba_page_content[sections][%1$s][order]" value="%2$d" aria-label="Bileşen sırası">',
            esc_attr($group_key),
            $order
        );
        echo '<input type="hidden" name="myliba_page_content[sections][' . esc_attr($group_key) . '][key]" value="' . esc_attr($group_key) . '">';
        echo '<button type="button" class="myliba-builder-card__toggle" aria-expanded="false" aria-controls="' . esc_attr($panel_id) . '" title="İçeriği Genişlet/Daralt">▼</button>';
        echo '</div>';
        echo '</div>'; // end head

        // Card Body
        echo '<div class="myliba-builder-card__body" id="' . esc_attr($panel_id) . '" hidden>';
        foreach ($group['fields'] ?? [] as $key => $field_def) {
            $type = $field_def[0] ?? 'text';
            $label = $field_def[1] ?? '';
            $options = $field_def[2] ?? [];
            render_field($key, $type, $label, $doc['fields'][$key] ?? '', 'myliba_page_content[fields]', $options);
        }
        foreach ($group['collections'] ?? [] as $key => $config) {
            render_collection($key, $config, $doc['collections'][$key] ?? []);
        }
        echo '</div>'; // end body

        echo '</div>'; // end card
    }

    echo '</div>';
    render_admin_assets();
}

function render_field(string $key, string|array $type, string $label, mixed $value, string $name_prefix = 'myliba_page_content[fields]', array $options = []): void
{
    $name = $name_prefix . '[' . $key . ']';
    $type_str = is_array($type) ? ($type[0] ?? 'text') : $type;
    $field_options = is_array($type) ? ($type[1] ?? $options) : $options;

    if ($type_str === 'media' || $type_str === 'image') {
        \Myliba\Core\Meta\field_media($name, $label, $value);
        return;
    }
    if ($type_str === 'select') {
        echo '<p class="myliba-page-content__field"><label><strong>' . esc_html($label) . '</strong></label><br>';
        echo '<select class="widefat" name="' . esc_attr($name) . '">';
        foreach ($field_options as $opt_val => $opt_label) {
            echo '<option value="' . esc_attr((string) $opt_val) . '" ' . selected((string) $value, (string) $opt_val, false) . '>' . esc_html($opt_label) . '</option>';
        }
        echo '</select></p>';
        return;
    }
    echo '<p class="myliba-page-content__field"><label><strong>' . esc_html($label) . '</strong></label><br>';
    if ($type_str === 'textarea') {
        echo '<textarea class="widefat" rows="4" name="' . esc_attr($name) . '">' . esc_textarea((string) $value) . '</textarea>';
    } else {
        echo '<input class="widefat" type="text" name="' . esc_attr($name) . '" value="' . esc_attr((string) $value) . '">';
    }
    echo '</p>';
}

function render_collection(string $key, array $config, mixed $rows): void
{
    $rows = is_array($rows) ? array_values($rows) : [];
    echo '<section class="myliba-repeater" data-collection="' . esc_attr($key) . '">';
    echo '<h4>' . esc_html($config['label']) . '</h4><div class="myliba-repeater__rows">';
    foreach ($rows as $index => $row) {
        render_collection_row($key, (int) $index, $config['fields'], is_array($row) ? $row : []);
    }
    echo '</div><button type="button" class="button myliba-repeater__add">Satır ekle</button>';
    echo '<template>';
    render_collection_row($key, '__INDEX__', $config['fields'], []);
    echo '</template></section>';
}

function render_collection_row(string $collection_key, int|string $index, array $fields, array $row): void
{
    echo '<div class="myliba-repeater__row"><div class="myliba-repeater__row-head"><strong>Satır <span class="myliba-repeater__number">' . esc_html(is_int($index) ? (string) ($index + 1) : '') . '</span></strong><button type="button" class="button-link-delete myliba-repeater__remove">Sil</button></div>';
    foreach ($fields as $key => $field_def) {
        $type = $field_def[0] ?? 'text';
        $label = $field_def[1] ?? '';
        $options = $field_def[2] ?? [];
        render_field($key, $type, $label, $row[$key] ?? '', 'myliba_page_content[collections][' . $collection_key . '][' . $index . ']', $options);
    }
    echo '</div>';
}

function render_admin_assets(): void
{
    ?>
    <style>
        .myliba-page-builder{display:grid;gap:12px;margin-top:16px}
        .myliba-builder-card{background:#fff;border:1px solid #c3c4c7;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.04);transition:opacity .15s ease,border-color .15s ease,box-shadow .15s ease}
        .myliba-builder-card.is-disabled{background:#f9fafb;opacity:.65}
        .myliba-builder-card.is-dragging{opacity:.4;border:2px dashed #2271b1}
        .myliba-builder-card.drag-over{border-top:3px solid #2271b1}
        .myliba-builder-card__head{align-items:center;display:grid;gap:12px;grid-template-columns:auto 1fr auto;padding:12px 16px;user-select:none}
        .myliba-builder-card__handle{cursor:grab;color:#8c8f94;font-size:18px;font-weight:700;letter-spacing:1px;padding:4px}
        .myliba-builder-card__handle:active{cursor:grabbing}
        .myliba-builder-card__main{align-items:center;display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between}
        .myliba-builder-card__title-row{align-items:center;display:flex;gap:8px}
        .myliba-builder-card__title-text{font-size:14px;font-weight:700;color:#1d2327;cursor:pointer}
        .myliba-builder-card__summary{color:#646970;font-size:12px}
        .myliba-builder-card__controls{align-items:center;display:flex;gap:6px}
        .myliba-builder-card__order{width:56px;text-align:center}
        .myliba-builder-card__toggle{background:none;border:none;color:#50575e;cursor:pointer;font-size:12px;padding:6px;transition:transform .18s ease}
        .myliba-builder-card__toggle.is-open{transform:rotate(180deg)}
        .myliba-builder-card__body{border-top:1px solid #dcdcde;padding:14px 18px 20px;background:#fcfcfc}
        .myliba-page-content__field{margin:12px 0}
        .myliba-repeater{background:#f0f0f1;border:1px solid #dcdcde;border-radius:6px;margin:16px 0;padding:14px}
        .myliba-repeater h4{margin:0 0 10px;font-size:13px;font-weight:700}
        .myliba-repeater__rows{display:grid;gap:10px;margin-bottom:10px}
        .myliba-repeater__row{background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:12px}
        .myliba-repeater__row-head{align-items:center;display:flex;justify-content:space-between;border-bottom:1px solid #f0f0f1;padding-bottom:6px;margin-bottom:8px}
    </style>
    <script>
        (function(){
            var builder = document.getElementById('myliba-page-builder-list');
            if(!builder) return;

            // Toggle Expand / Collapse
            builder.addEventListener('click', function(e){
                var toggle = e.target.closest('.myliba-builder-card__toggle');
                if(toggle){
                    var card = toggle.closest('.myliba-builder-card');
                    var body = card.querySelector('.myliba-builder-card__body');
                    var isOpen = !body.hidden;
                    body.hidden = isOpen;
                    toggle.classList.toggle('is-open', !isOpen);
                    toggle.setAttribute('aria-expanded', String(!isOpen));
                    return;
                }

                // Checkbox toggle class
                var check = e.target.closest('.myliba-builder-card__enabled input[type="checkbox"]');
                if(check){
                    var card = check.closest('.myliba-builder-card');
                    card.classList.toggle('is-disabled', !check.checked);
                    return;
                }

                // Move Up button
                var moveUp = e.target.closest('.myliba-btn-move-up');
                if(moveUp){
                    var card = moveUp.closest('.myliba-builder-card');
                    var prev = card.previousElementSibling;
                    if(prev && prev.classList.contains('myliba-builder-card')){
                        builder.insertBefore(card, prev);
                        renumberSections();
                    }
                    return;
                }

                // Move Down button
                var moveDown = e.target.closest('.myliba-btn-move-down');
                if(moveDown){
                    var card = moveDown.closest('.myliba-builder-card');
                    var next = card.nextElementSibling;
                    if(next && next.classList.contains('myliba-builder-card')){
                        builder.insertBefore(next, card);
                        renumberSections();
                    }
                    return;
                }

                // Repeater add
                var add = e.target.closest('.myliba-repeater__add');
                if(add){
                    var repeater = add.closest('.myliba-repeater');
                    var rows = repeater.querySelector('.myliba-repeater__rows');
                    var index = rows.children.length;
                    var html = repeater.querySelector('template').innerHTML.replaceAll('__INDEX__', String(index));
                    rows.insertAdjacentHTML('beforeend', html);
                    renumberRepeater(rows);
                    return;
                }

                // Repeater remove
                var remove = e.target.closest('.myliba-repeater__remove');
                if(remove){
                    var currentRows = remove.closest('.myliba-repeater__rows');
                    remove.closest('.myliba-repeater__row').remove();
                    renumberRepeater(currentRows);
                    return;
                }
            });

            // Drag and Drop
            var draggedCard = null;
            builder.addEventListener('dragstart', function(e){
                var card = e.target.closest('.myliba-builder-card');
                if(!card) return;
                draggedCard = card;
                card.classList.add('is-dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            builder.addEventListener('dragover', function(e){
                e.preventDefault();
                var card = e.target.closest('.myliba-builder-card');
                if(!card || card === draggedCard) return;
                builder.querySelectorAll('.myliba-builder-card').forEach(function(c){ c.classList.remove('drag-over'); });
                card.classList.add('drag-over');
            });

            builder.addEventListener('drop', function(e){
                e.preventDefault();
                var targetCard = e.target.closest('.myliba-builder-card');
                builder.querySelectorAll('.myliba-builder-card').forEach(function(c){
                    c.classList.remove('drag-over');
                    c.classList.remove('is-dragging');
                });
                if(targetCard && draggedCard && targetCard !== draggedCard){
                    var all = Array.from(builder.children);
                    var draggedIdx = all.indexOf(draggedCard);
                    var targetIdx = all.indexOf(targetCard);
                    if(draggedIdx < targetIdx){
                        targetCard.after(draggedCard);
                    } else {
                        targetCard.before(draggedCard);
                    }
                    renumberSections();
                }
                draggedCard = null;
            });

            builder.addEventListener('dragend', function(){
                builder.querySelectorAll('.myliba-builder-card').forEach(function(c){
                    c.classList.remove('drag-over');
                    c.classList.remove('is-dragging');
                });
                draggedCard = null;
            });

            function renumberSections(){
                builder.querySelectorAll('.myliba-builder-card').forEach(function(card, idx){
                    var orderInput = card.querySelector('.myliba-builder-card__order');
                    if(orderInput){
                        orderInput.value = String((idx + 1) * 10);
                    }
                });
            }

            function renumberRepeater(rows){
                rows.querySelectorAll('.myliba-repeater__row').forEach(function(row, index){
                    row.querySelector('.myliba-repeater__number').textContent = String(index + 1);
                    row.querySelectorAll('[name]').forEach(function(field){
                        field.name = field.name.replace(/\[collections\]\[([^\]]+)\]\[\d+\]/, '[collections][$1][' + index + ']');
                    });
                });
            }
        })();
    </script>
    <?php
}

function save(int $post_id, ?\WP_Post $post = null, ?bool $update = null): void
{
    unset($update);
    
    $real_post_id = $post_id;
    if ($parent_id = wp_is_post_revision($post_id)) {
        $real_post_id = (int) $parent_id;
    }
    if ($parent_id = wp_is_post_autosave($post_id)) {
        $real_post_id = (int) $parent_id;
    }

    $nonce = isset($_POST['myliba_page_content_nonce']) ? sanitize_text_field(wp_unslash($_POST['myliba_page_content_nonce'])) : '';
    $valid_nonce = wp_verify_nonce($nonce, 'myliba_page_content_' . $post_id) || wp_verify_nonce($nonce, 'myliba_page_content_' . $real_post_id);
    if (!$valid_nonce) {
        return;
    }

    if (!current_user_can('edit_post', $real_post_id)) {
        return;
    }

    $post = $post instanceof \WP_Post ? $post : get_post($real_post_id);
    if (!$post instanceof \WP_Post) {
        return;
    }

    $schema = schema_for_post($post);
    $posted_schema = isset($_POST['myliba_page_content_schema']) ? sanitize_key(wp_unslash($_POST['myliba_page_content_schema'])) : '';
    if ($schema === null || $posted_schema !== $schema) {
        return;
    }

    $raw = isset($_POST['myliba_page_content']) && is_array($_POST['myliba_page_content']) ? wp_unslash($_POST['myliba_page_content']) : [];
    $definition = definition($schema);
    $fields = [];
    $collections = [];

    // Fields & collections
    foreach ($definition['groups'] as $group) {
        foreach ($group['fields'] ?? [] as $key => [$type]) {
            $value = is_string($raw['fields'][$key] ?? null) ? $raw['fields'][$key] : '';
            $fields[$key] = $type === 'textarea' ? sanitize_textarea_field($value) : sanitize_text_field($value);
        }
        foreach ($group['collections'] ?? [] as $collection_key => $config) {
            $collections[$collection_key] = [];
            $rows = is_array($raw['collections'][$collection_key] ?? null) ? $raw['collections'][$collection_key] : [];
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $clean_row = [];
                foreach ($config['fields'] as $key => [$type]) {
                    $value = is_string($row[$key] ?? null) ? $row[$key] : '';
                    $clean_row[$key] = $type === 'textarea' ? sanitize_textarea_field($value) : sanitize_text_field($value);
                }
                if (array_filter($clean_row, static fn ($value): bool => $value !== '')) {
                    $collections[$collection_key][] = $clean_row;
                }
            }
        }
    }

    // Sections order & enabled toggle
    $posted_sections = is_array($raw['sections'] ?? null) ? $raw['sections'] : [];
    $sections = [];
    $default_order = 10;
    foreach ($definition['groups'] as $group_key => $group) {
        $sec_entry = $posted_sections[$group_key] ?? [];
        $enabled = !empty($sec_entry['enabled']);
        $order = isset($sec_entry['order']) && is_numeric($sec_entry['order']) ? (int) $sec_entry['order'] : $default_order;
        $sections[] = [
            'key' => $group_key,
            'enabled' => $enabled,
            'order' => $order,
        ];
        $default_order += 10;
    }
    usort($sections, static fn ($a, $b) => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));

    $document = [
        'schema' => $schema,
        'version' => SCHEMA_VERSION,
        'fields' => $fields,
        'collections' => $collections,
        'sections' => $sections,
    ];
    $json = wp_slash(wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    update_post_meta($real_post_id, META_KEY, $json);
    if ($post_id !== $real_post_id) {
        update_post_meta($post_id, META_KEY, $json);
    }

    if (isset($fields['redirect_url'])) {
        update_post_meta($real_post_id, '_myliba_redirect_url', $fields['redirect_url']);
        if ($post_id !== $real_post_id) {
            update_post_meta($post_id, '_myliba_redirect_url', $fields['redirect_url']);
        }
    }
}
