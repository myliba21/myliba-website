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
    add_action('save_post', __NAMESPACE__ . '\\save', 10, 3);
}

function register_meta(): void
{
    foreach (['page', 'myliba_solution'] as $post_type) {
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

    if ($post->post_type !== 'page') {
        return null;
    }

    $slug = (string) $post->post_name;
    $uri = (string) get_page_uri($post);

    return match (true) {
        in_array($slug, ['yazilim', 'urunler'], true) || str_contains($uri, 'yazilim') => 'software',
        in_array($slug, ['cozumler', 'solutions'], true) || str_contains($uri, 'cozumler') => 'solutions',
        in_array($slug, ['gelisim-merkezi', 'development-center'], true) || str_contains($uri, 'gelisim-merkezi') => 'development',
        in_array($slug, ['hikayemiz', 'our-story', 'biz-kimiz', 'about', 'about-us', 'felsefemiz'], true) || str_contains($uri, 'hikayemiz') => 'story',
        in_array($slug, ['etik-hat', 'etik-danismanlik', 'ethics-counsel', 'etik', 'ethics', 'whistleblowing'], true) || str_contains($slug, 'etik') || str_contains($slug, 'ethics') || str_contains($uri, 'etik') || str_contains($uri, 'ethics') => 'ethics',
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
                ],
                'collections' => [
                    'hero_proof' => ['label' => 'Hero kısa faydaları', 'fields' => ['label' => ['text', 'Fayda']]],
                ],
            ],
            'dashboard' => [
                'label' => 'Hero Analitik Görseli',
                'fields' => [
                    'dashboard_aria_label' => ['text', 'Erişilebilirlik açıklaması'],
                    'dashboard_brand' => ['text', 'Marka'],
                    'dashboard_product' => ['text', 'Ürün etiketi'],
                    'dashboard_analysis_label' => ['text', 'Analiz etiketi'],
                    'dashboard_title' => ['text', 'Analiz başlığı'],
                    'dashboard_status' => ['text', 'Analiz durumu'],
                    'dashboard_axis_x' => ['text', 'Yatay eksen'],
                    'dashboard_axis_y' => ['text', 'Dikey eksen'],
                    'dashboard_score_value' => ['text', 'Skor değeri'],
                    'dashboard_score_label' => ['text', 'Skor etiketi'],
                    'dashboard_ai_label' => ['text', 'AI etiketi'],
                    'dashboard_ai_text' => ['text', 'AI içgörüsü'],
                ],
                'collections' => [
                    'dashboard_nav' => ['label' => 'Görsel menüsü', 'fields' => ['label' => ['text', 'Menü etiketi']]],
                    'dashboard_boxes' => ['label' => 'NineBox hücreleri', 'fields' => ['label' => ['text', 'Hücre etiketi'], 'count' => ['text', 'Kişi sayısı']]],
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
                        'number' => ['text', 'Numara'],
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
            'assurance' => [
                'label' => 'Kurulum ve Entegrasyon',
                'fields' => [
                    'assurance_eyebrow' => ['text', 'Üst etiket'],
                    'assurance_title' => ['textarea', 'Başlık'],
                    'assurance_text' => ['textarea', 'Açıklama'],
                    'assurance_rail_aria' => ['text', 'Entegrasyon listesi erişilebilirlik açıklaması'],
                ],
                'collections' => [
                    'assurance_cards' => ['label' => 'Kurulum kartları', 'fields' => [
                        'number' => ['text', 'Numara'],
                        'icon' => ['text', 'Simge'],
                        'title' => ['text', 'Başlık'],
                        'text' => ['textarea', 'Açıklama'],
                    ]],
                    'assurance_rail' => ['label' => 'Entegrasyon alanları', 'fields' => ['label' => ['text', 'Etiket']]],
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
                    'final_button_label' => ['text', 'Buton etiketi'],
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
            'hero_title_start' => 'Formları Tarihe Gömün:',
            'hero_title_emphasis' => '%100 Objektif Verilerle',
            'hero_title_end' => 'Anlık Performans Yönetimi',
            'hero_lead' => 'Performans değerlendirmeyi yılda bir kez yapılan öznel bir notlama süreci olmaktan çıkarın. Myliba Yazılım ile hedef ve performans yönetimini canlı analitik verilerle takip edin. Terfi, ücret ve gelişim gibi kritik lider kararlarını adil, şeffaf ve güven veren verilere dayandırın.',
            'hero_primary_label' => 'Demo Talep Edin',
            'hero_secondary_label' => 'Modülleri Keşfedin',
            'dashboard_aria_label' => 'Çalışan sıralaması ve NineBox analiz ekranı',
            'dashboard_brand' => 'Myliba',
            'dashboard_product' => 'Analytics',
            'dashboard_analysis_label' => 'Analiz',
            'dashboard_title' => 'Çalışan Sıralaması',
            'dashboard_status' => '2026 · Canlı',
            'dashboard_axis_x' => 'Performans',
            'dashboard_axis_y' => 'Potansiyel',
            'dashboard_score_value' => '94',
            'dashboard_score_label' => 'Adil Karar Skoru',
            'dashboard_ai_label' => 'AI İçgörüsü',
            'dashboard_ai_text' => '3 kritik yetenek yükselişte',
            'trust_label' => 'Canlı performans kültürü kuran ekipler',
            'trust_title' => 'Veriyle daha adil kararlar alan kurumların yanında.',
            'trust_text' => 'Farklı sektörlerden ekipler hedef, performans ve gelişim ritimlerini Myliba ile tek noktada buluşturuyor.',
            'modules_eyebrow' => 'Tek platform. Dört güçlü odak.',
            'modules_title' => 'Adil Karar Yönetimi',
            'modules_text' => 'Terfi, ücret, prim ve liderlik kararlarını; OKR, KPI, aksiyonlar, 360° analizler, kültür, bağlılık ve yapay zekâ içgörüleriyle destekleyin. Dedikodu, mobbing ve adaletsizlik gibi kültürel virüsleri erkenden tespit edin.',
            'modules_text_strong' => 'Kararlarınızı adil, şeffaf ve %100 objektif verilere dayandırın.',
            'workflow_eyebrow' => 'Tek ve sürekli bir çalışma döngüsü',
            'workflow_title' => 'Stratejiden insan kararlarına kesintisiz veri akışı.',
            'workflow_text' => 'Yıl sonunu beklemeden hedefleri, aksiyonları, görüşmeleri ve gelişim sinyallerini aynı ritimde yönetin.',
            'why_eyebrow' => 'Neden Myliba?',
            'why_title' => 'Myliba Yazılım: Şirketinizin Verimliliğini Katlayan Stratejik İş Ortağı',
            'why_formula_label' => 'Formülümüz',
            'why_formula_left' => 'Performans',
            'why_formula_first' => 'Potansiyel',
            'why_formula_second' => 'Müdahale',
            'why_text' => 'İnsanlara daha fazla güven, net bir odak ve gelişim alanı sunduğunuzda performans doğal bir ritimle ortaya çıkar. Myliba Yazılım bu anlayışı canlı verilere dönüştürür.',
            'assurance_eyebrow' => 'Kurumsal ölçekte güvenli geçiş',
            'assurance_title' => 'Sistemlerinize uyum sağlayan kontrollü bir kurulum.',
            'assurance_text' => 'Organizasyon yapısı, hedef döngüsü, veri aktarımı ve entegrasyon kapsamı ihtiyaç analiziyle belirlenir; devreye alma süreci ekiplerinizle birlikte planlanır.',
            'assurance_rail_aria' => 'Entegrasyon kapsamları',
            'faq_eyebrow' => 'Merak Edilenler',
            'faq_title' => 'Myliba Yazılım Hakkında Sıkça Sorulan Sorular',
            'faq_text' => 'Yeni nesil performans yönetimine geçerken bilmek isteyeceğiniz temel noktalar.',
            'final_eyebrow' => 'Dönüşüm için ilk adım',
            'final_title' => 'Şirketinizin “Görünmez İşletim Sistemini” Güncelleme Vakti Geldi.',
            'final_text' => 'Her organizasyonun performans yolculuğu farklıdır. İhtiyaçlarınıza özel kişiselleştirilmiş bir demo ile Myliba’nın şirketinizde nasıl değer yaratacağını birlikte keşfedelim.',
            'final_button_label' => 'Kişiselleştirilmiş Demo Talep Edin',
        ],
        'collections' => [
            'hero_proof' => [['label' => 'Canlı veri'], ['label' => 'Adil karar'], ['label' => 'İnsan odaklı gelişim']],
            'dashboard_nav' => [['label' => 'NineBox'], ['label' => 'Çalışanlar'], ['label' => 'Performans'], ['label' => 'İçgörüler']],
            'dashboard_boxes' => [
                ['label' => 'Gelişen Potansiyel', 'count' => '2'], ['label' => 'Yüksek Potansiyel', 'count' => '5'], ['label' => 'Geleceğin Liderleri', 'count' => '3'],
                ['label' => 'Güvenilir Oyuncu', 'count' => '7'], ['label' => 'Güçlü Performans', 'count' => '9'], ['label' => 'Yıldızlar', 'count' => '4'],
                ['label' => 'Destek Gerekli', 'count' => '2'], ['label' => 'Uzman Katkı', 'count' => '6'], ['label' => 'Kritik Yetenek', 'count' => '3'],
            ],
            'modules' => [
                ['number' => '01', 'title' => 'Strateji ve Hedef Yönetimi', 'text' => 'Çalışanlarınızı organizasyonunuzun strateji ve hedeflerine hizalayın. Siloları yıkın, herkes Kutup Yıldızı’na odaklansın.', 'items' => "Native OKR\nHedef Haritası\nAnlık İlerleme Takibi\nHedef Zorluk Analizi\nStratejik Aksiyonların İzlenmesi"],
                ['number' => '02', 'title' => 'Performans Yönetimi', 'text' => 'Gerçek zamanlı performans yönetimi artık mümkün! AI destekli aksiyon ve KPI kartlarıyla prim hakedişlerini gerçek zamanlı takip ederek süreçlerinizi şeffaflaştırın.', 'items' => "AI Destekli Görev ve Aksiyon Yönetimi\nKPI Kartları ve Veri Entegrasyonu"],
                ['number' => '03', 'title' => 'Sürekli Diyalog ve Kültür Yönetimi', 'text' => 'Yılda bir kez yapılan notlamaları unutun. 1:1 görüşmeler, anlık geri bildirim, ileri bildirim ve takdir kültürüyle gelişimi günlük işin bir parçası haline getirin.', 'items' => "Diyalog (1:1 Görüşmeler)\nGeri Bildirim & İleri Bildirim\nTakdir ve Oyunlaştırma"],
                ['number' => '04', 'title' => 'Adil Kararlar', 'text' => 'İnsan yönetiminde tahmin devrini kapatın. Hedef, performans, kültürel uyum ve liderlik verilerini tek noktada birleştirerek %100 veriye dayalı kararlar alın.', 'items' => "360°, 45° ve 90° Değer ve Yetkinlik Analizleri\nKültür, Bağlılık ve İsteklilik Analizi\nLider Kararı & Keeper Test\nAI Destekli İçgörüler"],
            ],
            'workflow_steps' => [
                ['title' => 'Hedefleri belirle', 'text' => 'Stratejiyi OKR, KPI ve sorumluluklarla görünür hale getirin.'],
                ['title' => 'Canlı takip et', 'text' => 'İlerleme, aksiyon ve risk sinyallerini tek ekranda izleyin.'],
                ['title' => 'Görüş ve geliştir', 'text' => '1:1, geri bildirim ve takdiri günlük çalışma akışına taşıyın.'],
                ['title' => 'Adil karar al', 'text' => 'Terfi, ücret ve gelişim kararlarını yıl boyunca oluşan kanıtlara dayandırın.'],
            ],
            'stats' => [
                ['value' => '%85', 'label' => 'Tasarruf', 'text' => 'Farklı modülleri tek noktada sunarak toplam İK bütçenizde %85’e varan maliyet tasarrufu.'],
                ['value' => '+40', 'label' => 'Gün Kazanç', 'text' => 'Çalışanların operasyonel yükünü azaltarak stratejik çalışmalara 40 güne varan ek zaman.'],
                ['value' => '2×', 'label' => 'Performans', 'text' => 'Sosyal taahhüt yönetimi ve anlık geri bildirim döngüleri ile ekiplerinizde 2x performans artışı.'],
                ['value' => '%67', 'label' => 'Daha Güçlü Hedefler', 'text' => 'Geleneksel yöntemlere kıyasla %67 daha güçlü ve dönüşüme öncülük eden hedefler.'],
            ],
            'assurance_cards' => [
                ['number' => '01', 'icon' => '↔', 'title' => 'API ve veri bağlantıları', 'text' => 'KPI ve organizasyon verilerini mevcut İK, iş zekâsı ve operasyon araçlarınızla ilişkilendirin.'],
                ['number' => '02', 'icon' => '◎', 'title' => 'Kurumunuza özel yapı', 'text' => 'Ekip, hedef, değerlendirme ve yetkilendirme modelini organizasyonunuzun çalışma biçimine göre kurgulayın.'],
                ['number' => '03', 'icon' => '✓', 'title' => 'Kontrollü devreye alma', 'text' => 'İhtiyaç analizinden veri aktarımına kadar kurulum adımlarını kademeli ve izlenebilir biçimde ilerletin.'],
            ],
            'assurance_rail' => [['label' => 'İK sistemleri'], ['label' => 'İş zekâsı'], ['label' => 'Operasyon verileri'], ['label' => 'API bağlantıları']],
            'faqs' => [
                ['question' => 'Myliba hangi şirketler için uygundur?', 'answer' => 'Myliba; hedeflerini, performansını ve kültürünü tek bir sistemde yönetmek isteyen büyüme aşamasındaki şirketlerden çok lokasyonlu büyük organizasyonlara kadar ölçeklenebilir. Yapı, sektör ve ekip büyüklüğüne göre özelleştirilebilir.'],
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
                'cta_button_label' => ['text', 'Buton etiketi'],
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
        $prefix . '_principles' => ['label' => 'Temel özellikler', 'fields' => ['label' => ['text', 'Etiket']]],
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
            'archive_principles_aria' => ['text', 'Temel özellikler erişilebilirlik açıklaması'],
            'archive_nav_aria' => ['text', 'İçerik türleri menüsü erişilebilirlik açıklaması'],
            'archive_report_item_label' => ['text', 'Rapor içerik türü etiketi'],
            'archive_ebook_item_label' => ['text', 'e-Kitap içerik türü etiketi'],
            'archive_item_link_label' => ['text', 'İçerik kartı bağlantı etiketi'],
            'archive_empty_eyebrow' => ['text', 'Boş durum üst etiketi'],
            'archive_empty_button_label' => ['text', 'Boş durum butonu'],
            'archive_topics_aria' => ['text', 'Yaklaşan konular erişilebilirlik açıklaması'],
        ]],
        'reports' => ['label' => 'Raporlar ve Trendler Arşivi', 'fields' => $archive_fields('reports'), 'collections' => $archive_collections('reports')],
        'ebooks' => ['label' => 'e-Kitaplar Arşivi', 'fields' => $archive_fields('ebooks'), 'collections' => $archive_collections('ebooks')],
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
        'archive_principles_aria' => 'Kaynakların temel özellikleri',
        'archive_nav_aria' => 'Gelişim Merkezi içerik türleri',
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
    ], 'collections' => [
        'reports_journey' => [['label' => 'Araştır'], ['label' => 'Yorumla'], ['label' => 'Uygula']],
        'reports_principles' => [['label' => 'Güncel araştırmalar'], ['label' => 'Uygulanabilir içgörüler'], ['label' => 'Kurumsal perspektif']],
        'reports_topics' => [['label' => 'Performans kültürü'], ['label' => 'Liderlik ve dönüşüm'], ['label' => 'İş dünyasının geleceği']],
        'ebooks_journey' => [['label' => 'Keşfet'], ['label' => 'İndir'], ['label' => 'Uygula']],
        'ebooks_principles' => [['label' => 'Pratik rehberler'], ['label' => 'Kullanıma hazır araçlar'], ['label' => 'Ekip uygulamaları']],
        'ebooks_topics' => [['label' => 'OKR ve hedef yönetimi'], ['label' => 'Liderlik pratikleri'], ['label' => 'Kültür ve performans']],
    ]];
}

function solution_definition(): array
{
    return ['label' => 'Çözüm Detay İçeriği', 'groups' => [
        'hero' => ['label' => 'Hero ve Tanıtım', 'fields' => [
            'kicker' => ['text', 'Üst etiket'],
            'hero_title' => ['textarea', 'Hero başlığı'],
            'hero_summary' => ['textarea', 'Hero açıklaması'],
            'intro' => ['textarea', 'Yaklaşım açıklaması'],
        ]],
        'benefits' => ['label' => 'Kazanımlar ve Hedef Kitle', 'collections' => [
            'benefits' => ['label' => 'Beklenen kazanımlar', 'fields' => ['text' => ['textarea', 'Kazanım']]],
            'audiences' => ['label' => 'Kimler için?', 'fields' => ['text' => ['text', 'Hedef kitle']]],
            'metrics' => ['label' => 'Ölçüm alanları', 'fields' => ['title' => ['text', 'Başlık'], 'text' => ['textarea', 'Açıklama']]],
        ]],
        'process' => ['label' => 'Çalışma Modeli', 'collections' => [
            'steps' => ['label' => 'Süreç adımları', 'fields' => ['title' => ['text', 'Başlık'], 'text' => ['textarea', 'Açıklama']]],
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
            'benefits' => ['Hedef Mars Simülasyonu — oyunlaştırılmış dijital laboratuvar', 'Radikal Samimiyet Simülasyonu — geri bildirim isteyen ekipler yaratın', 'Başarı Sahnesi Simülasyonu — otonom ekipleri anlamlı hedefler etrafında geliştirin'],
            'audiences' => ['Birlikte çalışma ritmini güçlendiren ekipler', 'Yeni kurulan veya dönüşen ekipler', 'Geri bildirim kültürü geliştiren liderler'],
            'steps' => [['Senaryoyu Yaşayın', 'Ekipler gerçek iş yaşamını temsil eden karar ve iletişim anlarını deneyimler.'], ['Davranışı Görün', 'Koç eşliğinde güçlü yönler, engeller ve takım örüntüleri görünür hale gelir.'], ['Yeni Ritmi Kurun', 'Öğrenilenler somut takım anlaşmalarına ve takip aksiyonlarına dönüşür.']],
        ],
        'danismanlik' => [
            'kicker' => 'Stratejiden sürdürülebilir sisteme',
            'summary' => 'Stratejik hedeflerinizi netleştirin ve kurumunuza özel performans gelişim sistemini birlikte kurun.',
            'intro' => 'Danışmanlık çalışmalarımız, hedef belirlemeden uygulama ritmine kadar organizasyonunuzun ihtiyaçlarına göre yapılandırılır.',
            'benefits' => ['Stratejik Hedef Haritası Oluşturma — şirket tepe hedeflerinin belirlenmesi ve otonom ekiplerin oluşturulması', 'Performans Gelişim Sistemi Kurulumu — performans gelişim altyapısının kurumunuza özel yapılandırılması', 'Uygulama, iletişim ve liderlik rutinlerinin organizasyonla birlikte tasarlanması'],
            'audiences' => ['Üst yönetim ekipleri', 'İnsan ve kültür liderleri', 'Strateji ve dönüşüm ekipleri'],
            'steps' => [['Mevcut Durum', 'Hedef, performans ve liderlik süreçlerinin bugünkü resmi çıkarılır.'], ['Hedef Sistem', 'Organizasyona uygun model, roller ve çalışma ritimleri tasarlanır.'], ['Kurulum', 'Sistem ekiplerle birlikte devreye alınır ve gelişim göstergeleri takip edilir.']],
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
    $title = $post instanceof \WP_Post ? get_the_title($post) : '';

    return ['fields' => [
        'kicker' => (string) ($item['kicker'] ?? ''),
        'hero_title' => $title,
        'hero_summary' => (string) ($item['summary'] ?? ''),
        'intro' => (string) ($item['intro'] ?? ''),
    ], 'collections' => [
        'benefits' => array_map(static fn (string $text): array => ['text' => $text], $item['benefits'] ?? []),
        'audiences' => array_map(static fn (string $text): array => ['text' => $text], $item['audiences'] ?? []),
        'metrics' => array_map(static fn (array $row): array => ['title' => $row[0], 'text' => $row[1]], $item['metrics'] ?? []),
        'steps' => array_map(static fn (array $row): array => ['title' => $row[0], 'text' => $row[1]], $item['steps'] ?? []),
    ]];
}

function story_definition(): array
{
    return [
        'label' => 'Biz Kimiz / Hikayemiz Sayfası İçeriği',
        'groups' => [
            'hero' => [
                'label' => 'Hero & Manifesto',
                'fields' => [
                    'hero_eyebrow' => ['text', 'Üst etiket (Pill)'],
                    'hero_title' => ['textarea', 'Hero başlığı'],
                    'hero_lead' => ['textarea', 'Hero açıklaması'],
                    'hero_primary_label' => ['text', 'Ana buton etiketi'],
                    'hero_secondary_label' => ['text', 'İkincil buton etiketi'],
                ],
                'collections' => [
                    'hero_badges' => ['label' => 'Hero rozetleri', 'fields' => ['label' => ['text', 'Rozet metni']]],
                ],
            ],
            'formula' => [
                'label' => 'Yönetim Formülü (Performans = Potansiyel − Müdahale)',
                'fields' => [
                    'formula_eyebrow' => ['text', 'Üst etiket'],
                    'formula_title' => ['textarea', 'Bölüm başlığı'],
                    'formula_lead' => ['textarea', 'Bölüm açıklaması'],
                    'formula_badge' => ['text', 'Formül kartı rozeti'],
                    'formula_meta' => ['text', 'Formül kaynak/meta bilgisi'],
                    'formula_result_tag' => ['text', 'Performans kutusu etiketi'],
                    'formula_result_title' => ['text', 'Performans kutusu başlığı'],
                    'formula_result_desc' => ['textarea', 'Performans kutusu açıklaması'],
                    'formula_potential_tag' => ['text', 'Potansiyel kutusu etiketi'],
                    'formula_potential_title' => ['text', 'Potansiyel kutusu başlığı'],
                    'formula_potential_desc' => ['textarea', 'Potansiyel kutusu açıklaması'],
                    'formula_interference_tag' => ['text', 'Müdahale kutusu etiketi'],
                    'formula_interference_title' => ['text', 'Müdahale kutusu başlığı'],
                    'formula_interference_desc' => ['textarea', 'Müdahale kutusu açıklaması'],
                    'formula_leverage_title' => ['text', 'Kaldıraç başlığı'],
                    'formula_leverage_text' => ['textarea', 'Kaldıraç açıklaması'],
                ],
            ],
            'why' => [
                'label' => 'Neden Yola Çıktık (Problem vs. Çözüm)',
                'fields' => [
                    'why_eyebrow' => ['text', 'Üst etiket'],
                    'why_title' => ['textarea', 'Bölüm başlığı'],
                    'why_lead' => ['textarea', 'Bölüm açıklaması'],
                    'why_manifesto_text' => ['textarea', 'Vurgulu manifesto notu'],
                ],
                'collections' => [
                    'comparisons' => ['label' => 'Karşılaştırma kartları', 'fields' => [
                        'problem_label' => ['text', 'Problem üst etiketi'],
                        'problem_title' => ['text', 'Problem başlığı'],
                        'problem_desc' => ['textarea', 'Problem açıklaması'],
                        'solution_label' => ['text', 'Çözüm üst etiketi'],
                        'solution_title' => ['text', 'Çözüm başlığı'],
                        'solution_desc' => ['textarea', 'Çözüm açıklaması'],
                    ]],
                ],
            ],
            'pillars' => [
                'label' => 'Bütünleşik Ekosistem (4 Dikey)',
                'fields' => [
                    'pillars_eyebrow' => ['text', 'Üst etiket'],
                    'pillars_title' => ['textarea', 'Bölüm başlığı'],
                    'pillars_lead' => ['textarea', 'Bölüm açıklaması'],
                ],
                'collections' => [
                    'pillars' => ['label' => 'Dikey kartları', 'fields' => [
                        'number' => ['text', 'Numara (01, 02...)'],
                        'badge' => ['text', 'Rozet etiketi'],
                        'icon' => ['text', 'Simge / Emoji'],
                        'title' => ['text', 'Dikey başlığı'],
                        'desc' => ['textarea', 'Dikey açıklaması'],
                        'tags' => ['textarea', 'Etiketler (her satıra bir etiket)'],
                        'link_label' => ['text', 'Bağlantı metni'],
                        'link_target' => ['text', 'Hedef sayfa anahtarı (products, academy, development, solutions)'],
                    ]],
                ],
            ],
            'proof' => [
                'label' => 'Sosyal Kanıt ve Sayaçlar',
                'fields' => [
                    'proof_eyebrow' => ['text', 'Üst etiket'],
                    'proof_title' => ['textarea', 'Bölüm başlığı'],
                    'proof_lead' => ['textarea', 'Bölüm açıklaması'],
                ],
                'collections' => [
                    'stats' => ['label' => 'Sayaç kartları', 'fields' => [
                        'value' => ['text', 'Sayı / Değer (25+, 44+...)'],
                        'unit' => ['text', 'Birim (Yıl, Şirket...)'],
                        'label' => ['textarea', 'Açıklama'],
                        'is_highlight' => ['text', 'Vurgulu mu? (1 veya 0)'],
                    ]],
                ],
            ],
            'values' => [
                'label' => 'Değerlerimiz ve İlkelerimiz',
                'fields' => [
                    'values_eyebrow' => ['text', 'Üst etiket'],
                    'values_title' => ['textarea', 'Bölüm başlığı'],
                    'values_lead' => ['textarea', 'Bölüm açıklaması'],
                ],
                'collections' => [
                    'values' => ['label' => 'Değer kartları', 'fields' => [
                        'icon' => ['text', 'Simge / Emoji'],
                        'tag' => ['text', 'Üst etiket'],
                        'title' => ['text', 'Başlık'],
                        'desc' => ['textarea', 'Açıklama'],
                    ]],
                ],
            ],
            'final' => [
                'label' => 'Final Manifesto ve CTA',
                'fields' => [
                    'final_pill' => ['text', 'Üst rozet'],
                    'final_title' => ['textarea', 'Başlık'],
                    'final_text' => ['textarea', 'Açıklama'],
                    'final_primary_label' => ['text', 'Ana buton etiketi'],
                    'final_secondary_label' => ['text', 'İkincil buton etiketi'],
                ],
            ],
        ],
    ];
}

function story_defaults(): array
{
    return [
        'fields' => [
            'hero_eyebrow' => 'BİZ KİMİZ? · BÜTÜNLEŞİK KÜLTÜR & PERFORMANS',
            'hero_title' => 'Geleceğin Organizasyonlarını İnsan ve Teknolojiyi Birleştirerek İnşa Ediyoruz.',
            'hero_lead' => 'Myliba; hantal hiyerarşileri esneten, organizasyonları geleceğin esnek çalışma dünyasına hazırlayan ve yapay zekâ destekli altyapıyı ICF onaylı kültürel yönetim modeliyle birleştiren dünyanın ilk ve tek bütünleşik platformudur.',
            'hero_primary_label' => 'Myliba Demosu Planlayın',
            'hero_secondary_label' => 'Yönetim Formülümüzü Keşfedin ↓',
            'formula_eyebrow' => 'YAKLAŞIMIMIZ · İMZA FORMÜLÜMÜZ',
            'formula_title' => 'Rakamların Arkasındaki Yönetim Anlayışı',
            'formula_lead' => 'Performans, insanları daha çok zorlamakla değil; aradaki sistemsel parazit ve engelleri kaldırmakla artar.',
            'formula_badge' => 'Temel Yönetim Denklemi',
            'formula_meta' => 'Tim Gallwey / Inner Game Model × Myliba Systems',
            'formula_result_tag' => 'ÇIKTI',
            'formula_result_title' => 'Performans',
            'formula_result_desc' => 'Ekiplerin ve kurumun ulaştığı gerçek, ölçülebilir ve sürdürülebilir başarı.',
            'formula_potential_tag' => 'İÇSEL GÜÇ',
            'formula_potential_title' => 'Potansiyel',
            'formula_potential_desc' => 'Çalışanların doğal yeteneği, inovasyon gücü, motivasyonu ve kolektif zekâsı.',
            'formula_interference_tag' => 'ENGEL & PARAZİT',
            'formula_interference_title' => 'Müdahale',
            'formula_interference_desc' => 'Belirsiz hedefler, mikro-yönetim, güvensizlik, bürokrasi ve geri bildirimsizlik.',
            'formula_leverage_title' => 'Myliba Kaldıracı:',
            'formula_leverage_text' => 'Myliba, teknolojiyi insan odaklı bir yüksek performans kültürü inşa etmek için bir kaldıraç olarak kullanır. Tüm ürünleri ve metodolojisi bu düşünce üzerine kuruludur.',
            'why_eyebrow' => 'HİKAYEMİZ · DÖNÜŞÜM İHTİYACI',
            'why_title' => 'Neden Yola Çıktık?',
            'why_lead' => 'Geleneksel hiyerarşiler ve bürokratik engeller en başarılı ekiplerin bile potansiyelini sınırlar. Bu yüzden performansı insanları zorlayarak değil, sistemleri iyileştirerek geliştiriyoruz.',
            'why_manifesto_text' => 'Performansı insanları zorlayarak değil, sistemleri iyileştirerek geliştiriyoruz. Bu unsurlar en başarılı ekiplerin bile potansiyelini sınırlar; Myliba bu engelleri kaldırarak kurumların kendi yüksek performans işletim sistemini kurmasını sağlar.',
            'pillars_eyebrow' => 'BÜTÜNLEŞİK ÇÖZÜM MİMARİSİ',
            'pillars_title' => 'Ne Yapıyoruz?',
            'pillars_lead' => 'Myliba; Yazılım, Akademi, Danışmanlık ve Simülasyon dikeylerini tek bir çatı altında birleştirir.',
            'proof_eyebrow' => 'SOMUT VERİLER & GÜVEN',
            'proof_title' => 'Rakamlarla Sosyal Kanıt',
            'proof_lead' => 'Yıllara dayanan saha deneyimi ve onlarca sektörde kanıtlanmış kurumsal dönüşüm gücü.',
            'values_eyebrow' => 'DEĞERLERİMİZ VE İLKELERİMİZ',
            'values_title' => 'Bizi Yönlendiren Temel İlkeler',
            'values_lead' => 'Geleceğin organizasyonlarını inşa ederken taviz vermediğimiz 3 temel sütun.',
            'final_pill' => 'GELECEĞİN ÇALIŞMA DÜNYASI',
            'final_title' => 'Şirketinizin “Görünmez İşletim Sistemini” Yeniden Kodlayın.',
            'final_text' => 'Organizasyonunuzu geleceğin çalışma dünyasına hazırlamak, ekiplerinizi otonom kılmak ve canlı verilerle yönetmek için Myliba ile tanışın.',
            'final_primary_label' => 'Uzmanlarımızla Görüşün',
            'final_secondary_label' => 'Myliba Demosu Planlayın',
        ],
        'collections' => [
            'hero_badges' => [
                ['label' => 'Dünyanın İlk & Tek Bütünleşik Modeli'],
                ['label' => 'ICF Onaylı Kültür & OKR Koçluğu'],
                ['label' => 'Yapay Zekâ & 9-Box Destekli Canlı Altyapı'],
            ],
            'comparisons' => [
                [
                    'problem_label' => 'Geleneksel Problem',
                    'problem_title' => 'Belirsiz Hedefler',
                    'problem_desc' => 'Kimin neye koştuğunun net olmadığı, stratejiden kopuk ve silolara hapsolmuş hedefler.',
                    'solution_label' => 'Myliba Çözümü',
                    'solution_title' => 'Net & Canlı OKR / KPI Hizalanması',
                    'solution_desc' => 'Şirket stratejisini tüm çalışanlarla şeffaf ve anlık senkronize eden yaşayan hedef ritmi.',
                ],
                [
                    'problem_label' => 'Geleneksel Problem',
                    'problem_title' => 'Sürekli Müdahale',
                    'problem_desc' => 'Her adımda onay bekleyen, inisiyatif almayı engelleyen ve güveni yıpratan hantal kontrol.',
                    'solution_label' => 'Myliba Çözümü',
                    'solution_title' => 'Otonomi ve Güven Mimarisi',
                    'solution_desc' => 'Kendi performansının sorumluluğunu alan kurum içi girişimciler ve hedef koçluğu modeli.',
                ],
                [
                    'problem_label' => 'Geleneksel Problem',
                    'problem_title' => 'Güvensizlik Kültürü',
                    'problem_desc' => 'Hata yapmaktan çekinilen, fikirlerin saklandığı ve süreçlerin formlara hapsolduğu yapılar.',
                    'solution_label' => 'Myliba Çözümü',
                    'solution_title' => 'Sürekli Diyalog & Psikolojik Güvenlik',
                    'solution_desc' => 'Haftalık 1:1 görüşmeler, şeffaf kültür analitiği ve psikolojik güven ortamı.',
                ],
                [
                    'problem_label' => 'Geleneksel Problem',
                    'problem_title' => 'Geri Bildirimsizlik',
                    'problem_desc' => 'Yılda bir kez geçmişi yargılayan, not veren ve çalışan motivasyonunu düşüren seanslar.',
                    'solution_label' => 'Myliba Çözümü',
                    'solution_title' => 'Anlık İleri Bildirim (Feedforward)',
                    'solution_desc' => 'Geçmişe değil geleceğe odaklanan, anlık takdir ve gelişim koçluğunu besleyen ritim.',
                ],
            ],
            'pillars' => [
                [
                    'number' => '01',
                    'badge' => 'Canlı SaaS Platformu',
                    'icon' => '💻',
                    'title' => 'Myliba Yazılım',
                    'desc' => 'Anlık performans verileri, 9-Box kültür ve yetenek analizi, yapay zekâ içgörüleri, OKR/KPI yönetimi, sürekli diyalog döngüsü ve adil karar mekanizmalarıyla performansı yaşayan bir sürece dönüştürür.',
                    'tags' => "Anlık Performans\n9-Box Matrisi\nYapay Zekâ İçgörüleri\nCanlı OKR & KPI",
                    'link_label' => 'Yazılımı İnceleyin',
                    'link_target' => 'products',
                ],
                [
                    'number' => '02',
                    'badge' => 'Dünyada İlk & Tek ICF Onaylı',
                    'icon' => '🎓',
                    'title' => 'Myliba Akademi',
                    'desc' => 'Dünyanın ilk ve tek ICF onaylı (40 Saat CCE) OKR & Kültür Koçluğu sertifikasyonu ve diğer işbaşı liderlik programlarıyla dönüşümü yönetecek liderleri yetiştirir.',
                    'tags' => "40 Saat CCE / ICF\nOKR & Kültür Koçluğu\nLiderlik Gelişimi",
                    'link_label' => 'Akademi Programlarını Keşfedin',
                    'link_target' => 'academy',
                ],
                [
                    'number' => '03',
                    'badge' => 'Deneyimsel Öğrenme',
                    'icon' => '🚀',
                    'title' => 'Simülasyonlar',
                    'desc' => '“Hedef Mars Simülasyonu” gibi deneyimsel öğrenme araçlarıyla belirsizlik ve kriz anlarında liderlik becerilerini geliştirir. “Radikal Samimiyet Simülasyonu” ile açık geri ve ileri bildirim isteyen güçlü ekipler yaratır.',
                    'tags' => "Hedef Mars Simülasyonu\nRadikal Samimiyet\nKriz ve Liderlik",
                    'link_label' => 'Gelişim Merkezini Görün',
                    'link_target' => 'development',
                ],
                [
                    'number' => '04',
                    'badge' => 'Strateji & Dönüşüm',
                    'icon' => '🤝',
                    'title' => 'Danışmanlık & Kültür Tasarımı',
                    'desc' => 'Hantal organizasyonel yapıları geleceğin çevik çalışma modellerine uyarlayan, stratejiyle kurum kültürünü hizalayan ve sürdürülebilir yüksek performans ritmi inşa eden uzman saha danışmanlığı.',
                    'tags' => "Çevik Organizasyonel Tasarım\nKültür Yapılandırma\nStratejik Hizalanma",
                    'link_label' => 'Çözümlerimizi İnceleyin',
                    'link_target' => 'solutions',
                ],
            ],
            'stats' => [
                ['value' => '25+', 'unit' => 'Yıl', 'label' => 'Yazılım, insan kaynakları ve organizasyonel gelişim tecrübesi.', 'is_highlight' => '0'],
                ['value' => '44+', 'unit' => 'Şirket', 'label' => '16 farklı sektörde kurumsal dönüşüm ve kültür yapılandırma tecrübesi.', 'is_highlight' => '0'],
                ['value' => '500+', 'unit' => 'Lider', 'label' => 'Yetkinleştirilmiş, koçluk yaklaşımı kazandırılmış yönetici ve lider ağı.', 'is_highlight' => '0'],
                ['value' => '40', 'unit' => 'Saat', 'label' => 'CCE / ICF Akreditasyonu: Dünyadaki ilk ve tek sertifika programı.', 'is_highlight' => '0'],
                ['value' => '%100', 'unit' => 'Canlı Kültür', 'label' => 'Süreçleri formlarda bırakmayan, yaşayan sürdürülebilir iş akışı.', 'is_highlight' => '1'],
            ],
            'values' => [
                [
                    'icon' => '📊',
                    'tag' => 'Şeffaflık & Objektiflik',
                    'title' => 'Veriyle Konuşan Adil Yönetim',
                    'desc' => 'Kararları kişisel algılar veya sübjektif kanaatlerle değil; şeffaf, objektif ve herkes için güven veren canlı verilere dayandırmak.',
                ],
                [
                    'icon' => '🌱',
                    'tag' => 'Gelişim Odaklılık',
                    'title' => 'Not Veren Değil, Geliştiren Yaklaşım',
                    'desc' => 'Yılda bir kez geçmişi yargılayan geleneksel performans görüşmeleri yerine, anlık geri ve ileri bildirim (feedforward) kültürüyle sürekli büyütmek.',
                ],
                [
                    'icon' => '🛡️',
                    'tag' => 'Yetkilendirme',
                    'title' => 'Otonomi ve Güven',
                    'desc' => 'Çalışanları kendi performansının sorumluluğunu alan kurum içi girişimcilere, liderleri ise ekibinin önünü açan birer hedef koçuna dönüştürmek.',
                ],
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
                'label' => 'Hero (Giriş Başlığı)',
                'fields' => [
                    'hero_title' => ['textarea', 'Hero Başlığı'],
                    'hero_lead' => ['textarea', 'Hero Açıklaması'],
                    'hero_primary_label' => ['text', 'Ana Buton Etiketi'],
                    'hero_secondary_label' => ['text', 'İkincil Buton Etiketi'],
                ],
            ],
            'intro' => [
                'label' => 'Etik Hat ve Etik İhlal Bildirimi (Tanıtım)',
                'fields' => [
                    'intro_eyebrow' => ['text', 'Üst Etiket (Opsiyonel)'],
                    'intro_title' => ['text', 'Bölüm Başlığı'],
                    'intro_lead' => ['textarea', 'Açıklama Metni'],
                    'intro_highlight' => ['text', 'Vurgulu Metin'],
                ],
            ],
            'why' => [
                'label' => 'Neden Myliba’nın Etik Hat Hizmeti?',
                'fields' => [
                    'why_eyebrow' => ['text', 'Üst Etiket (Opsiyonel)'],
                    'why_title' => ['textarea', 'Bölüm Başlığı'],
                ],
                'collections' => [
                    'why_items' => ['label' => 'Özellik Maddeleri', 'fields' => [
                        'title' => ['text', 'Madde Başlığı (Örn: Gizlilik ve Anonimlik)'],
                        'desc' => ['textarea', 'Açıklama (Örn: Çalışanlar bildirimlerini güvenle yapabilir.)'],
                    ]],
                ],
            ],
            'scope' => [
                'label' => 'Hizmet Kapsamı ve Özellikleri',
                'fields' => [
                    'scope_eyebrow' => ['text', 'Üst Etiket (Opsiyonel)'],
                    'scope_title' => ['text', 'Bölüm Başlığı (Hizmet Kapsamı)'],
                    'scope_subtitle' => ['text', 'Alt Başlık (Etik Hattı Özellikleri)'],
                ],
                'collections' => [
                    'scope_items' => ['label' => 'Kapsam Maddeleri', 'fields' => [
                        'text' => ['text', 'Özellik Metni (Örn: 7/24 erişilebilir bildirim hattı)'],
                    ]],
                ],
            ],
            'cta' => [
                'label' => 'İletişim ve Aksiyon Çağrısı (CTA)',
                'fields' => [
                    'cta_title' => ['textarea', 'CTA Başlığı'],
                    'cta_text' => ['textarea', 'CTA Açıklaması'],
                    'cta_button_label' => ['text', 'Buton Etiketi'],
                ],
            ],
        ],
    ];
}

function ethics_defaults(): array
{
    return [
        'fields' => [
            'hero_title' => 'Etik Hat',
            'hero_lead' => 'Etik Hat ve Etik İhlal Bildirimi, şirketinizin sürdürülebilir başarısı ve çalışan bağlılığı için hayati bir unsurdur. Myliba, bağımsız ve tarafsız araştırmacılarla bu süreci sizin için yönetir.',
            'hero_primary_label' => 'İletişime Geçin',
            'hero_secondary_label' => 'Hizmet Kapsamını İnceleyin',

            'intro_eyebrow' => 'Güvenli ve Tarafsız',
            'intro_title' => 'Etik Hat ve Etik İhlal Bildirimi',
            'intro_lead' => 'Etik Hat ve Etik İhlal Bildirimi, şirketinizin sürdürülebilir başarısı ve çalışan bağlılığı için hayati bir unsurdur.',
            'intro_highlight' => 'Myliba, bağımsız ve tarafsız araştırmacılarla bu süreci sizin için yönetir.',

            'why_eyebrow' => 'Avantajlar',
            'why_title' => 'Neden Myliba’nın Etik Hat Hizmeti?',

            'scope_eyebrow' => 'Süreç ve İletişim',
            'scope_title' => 'Hizmet Kapsamı',
            'scope_subtitle' => 'Etik Hattı Özellikleri',

            'cta_title' => 'İhtiyacınıza uygun çözümü birlikte değerlendirelim.',
            'cta_text' => 'Şirketinizin etik bildirim ve uyum süreçlerini güvenle yapılandırmak için bizimle iletişime geçin.',
            'cta_button_label' => 'İletişime Geçin',
        ],
        'collections' => [
            'why_items' => [
                [
                    'title' => 'Gizlilik ve Anonimlik',
                    'desc' => 'Çalışanlar bildirimlerini güvenle yapabilir.',
                ],
                [
                    'title' => 'Bağımsızlık',
                    'desc' => 'ICF ACC ve PCC seviyesindeki uzmanlarla tarafsız inceleme.',
                ],
                [
                    'title' => 'Yasal Uyum',
                    'desc' => 'Etik ihlaller zamanında tespit edilerek riskler azaltılır.',
                ],
                [
                    'title' => 'Çalışan Güveni',
                    'desc' => 'Güçlü etik kültürle çalışan bağlılığı artar.',
                ],
            ],
            'scope_items' => [
                ['text' => '7/24 erişilebilir bildirim hattı (Telefon, WhatsApp, E-posta)'],
                ['text' => 'Türkçe ve İngilizce dil desteği'],
                ['text' => 'Düzenli aylık raporlar'],
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
        'story' => story_definition(),
        'ethics' => ethics_definition(),
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
        'story' => story_defaults(),
        'ethics' => ethics_defaults(),
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
        foreach ($group['fields'] ?? [] as $key => [$type, $label]) {
            render_field($key, $type, $label, $doc['fields'][$key] ?? '');
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

function render_field(string $key, string $type, string $label, mixed $value, string $name_prefix = 'myliba_page_content[fields]'): void
{
    $name = $name_prefix . '[' . $key . ']';
    echo '<p class="myliba-page-content__field"><label><strong>' . esc_html($label) . '</strong></label><br>';
    if ($type === 'textarea') {
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
    foreach ($fields as $key => [$type, $label]) {
        render_field($key, $type, $label, $row[$key] ?? '', 'myliba_page_content[collections][' . $collection_key . '][' . $index . ']');
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
}

