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
    add_action('save_post_page', __NAMESPACE__ . '\\save', 10, 3);
    add_action('save_post_myliba_solution', __NAMESPACE__ . '\\save', 10, 3);
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

    return match (true) {
        in_array($post->post_name, ['yazilim', 'urunler'], true) => 'software',
        in_array($post->post_name, ['cozumler', 'solutions'], true) => 'solutions',
        in_array($post->post_name, ['gelisim-merkezi', 'development-center'], true) => 'development',
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
            'final_title' => 'Performansı yılda bir kez değil, her gün geliştirin.',
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
        'hero_title_start' => 'Birbiri ile entegre,',
        'hero_title_end' => 'bütünleşik çözümler',
        'hero_text' => 'Myliba’nın özel geliştirdiği modeli ile yazılımı, akademiyi ve organizasyonel dönüşümü tek çatı altında buluşturarak yüksek performans kültürü inşa edin.',
        'index_eyebrow' => 'İhtiyacınıza uygun çözümü seçin',
        'index_title' => 'İster tek tek kullanın, ister bütünleştirin.',
        'index_text' => 'Neye ihtiyacınız varsa Myliba çözümleri ile kültürünüzü geliştirin.',
        'card_link_label' => 'Çözümü inceleyin',
        'cta_eyebrow' => 'Birlikte belirleyelim',
        'cta_title' => 'Hangi çözüm size uygun?',
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
        'hero_title' => 'Gelişim Merkezi',
        'hero_text' => 'Güncel içerikler, araştırmalar ve etkinliklerle gelişim yolculuğunuzu besleyin.',
        'section_eyebrow' => 'Gelişim kaynakları',
        'section_title' => 'Gelişim zihniyetini sürekli yeni bilgi ve tecrübeyle besleyin.',
        'section_text' => 'e-Kitaplar, raporlar, blog yazıları ve etkinliklerle gelişim yolculuğunuzu sürdürün.',
        'card_cta' => 'İçerikleri inceleyin',
        'latest_prefix' => 'Son içerik:',
        'card_ebooks_label' => 'e-Kitaplar',
        'card_ebooks_text' => 'Yüksek performans kültürü ve yönetim pratikleri üzerine indirilebilir kaynaklar.',
        'card_reports_label' => 'Raporlar ve Trendler',
        'card_reports_text' => 'İş dünyasını ve performans kültürünü şekillendiren güncel araştırmalar.',
        'card_blog_label' => 'Blog',
        'card_blog_text' => 'Myliba yazıları, rehberleri ve uygulama notları.',
        'card_events_label' => 'Etkinlikler',
        'card_events_text' => 'Webinar, workshop ve topluluk buluşmaları.',
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
            'summary' => 'Hedefleri değerlerle yönetmek için kurumunuza özel, uygulamalı gelişim yolculukları tasarlayın.',
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
            'intro' => 'Myliba Kültür Analizi, çalışanların gerçekten çalışmaktan keyif aldığı bir ortam oluşturmak için kültürü ölçülebilir içgörülere dönüştürür.',
            'benefits' => ['Mevcut kültürün güçlü ve zayıf yönlerinin keşfedilmesi', 'Çalışan bağlılığı ve iş performansının artması', 'Kurum içi sinerji ve iletişimin güçlenmesi', 'Stratejik dönüşüm için veriye dayalı içgörüler edinilmesi'],
            'audiences' => ['İnsan ve kültür liderleri', 'Üst yönetim ekipleri', 'Değişim ve dönüşüm ekipleri'],
            'metrics' => [['Employee NPS', 'Çalışan tavsiye skoru'], ['Culture Fit', 'Departmanlar arası kültürel uyum'], ['Willingness', 'Çalışanın işe olan isteği'], ['Engagement', 'Kuruma, işe ve lidere bağlılık']],
            'steps' => [['Anket Aşaması', 'Kültür analizi, bağlılık analizi ve isteklilik analizi uygulanır.'], ['Saha Araştırması', 'Odak grup, yönetici görüşmeleri, doküman analizi ve gözlem yapılır.'], ['Gelişim Planı', 'Detaylı rapor, öncelikli alanlar, OKR/KPI hedefleri ve uygulama takvimi oluşturulur.']],
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

function definition(string $schema): array
{
    return match ($schema) {
        'software' => software_definition(),
        'solutions' => solutions_definition(),
        'development' => development_definition(),
        'solution' => solution_definition(),
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
        default => ['fields' => [], 'collections' => []],
    };
}

function document(int $post_id, string $schema): array
{
    $defaults = defaults($schema, $post_id);
    $raw = get_post_meta($post_id, META_KEY, true);
    $saved = is_string($raw) && $raw !== '' ? json_decode($raw, true) : [];

    if (!is_array($saved) || ($saved['schema'] ?? $schema) !== $schema) {
        $saved = [];
    }

    return [
        'schema' => $schema,
        'version' => SCHEMA_VERSION,
        'fields' => array_replace($defaults['fields'], is_array($saved['fields'] ?? null) ? $saved['fields'] : []),
        'collections' => array_replace($defaults['collections'], is_array($saved['collections'] ?? null) ? $saved['collections'] : []),
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

    return update_post_meta($post_id, META_KEY, $encoded) !== false;
}

function render_page_box(\WP_Post $post): void
{
    $schema = schema_for_post($post);
    if ($schema === null) {
        return;
    }

    $definition = definition($schema);
    $doc = document($post->ID, $schema);
    wp_nonce_field('myliba_page_content_' . $post->ID, 'myliba_page_content_nonce');

    echo '<p class="description">Bu alanlar yalnızca bu sayfanın içeriğini yönetir. Değişiklikler tasarım yapısını etkilemez.</p>';
    echo '<input type="hidden" name="myliba_page_content_schema" value="' . esc_attr($schema) . '">';
    echo '<div class="myliba-page-content">';

    foreach ($definition['groups'] as $group_key => $group) {
        echo '<details class="myliba-page-content__group"' . ($group_key === 'hero' ? ' open' : '') . '><summary>' . esc_html($group['label']) . '</summary><div class="myliba-page-content__body">';
        foreach ($group['fields'] ?? [] as $key => [$type, $label]) {
            render_field($key, $type, $label, $doc['fields'][$key] ?? '');
        }
        foreach ($group['collections'] ?? [] as $key => $config) {
            render_collection($key, $config, $doc['collections'][$key] ?? []);
        }
        echo '</div></details>';
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
        .myliba-page-content{display:grid;gap:10px;margin-top:16px}.myliba-page-content__group{background:#fff;border:1px solid #dcdcde;border-radius:7px}.myliba-page-content__group>summary{cursor:pointer;font-size:14px;font-weight:700;padding:14px}.myliba-page-content__body{border-top:1px solid #dcdcde;padding:4px 14px 16px}.myliba-page-content__field{margin:14px 0}.myliba-repeater{background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;margin:18px 0;padding:12px}.myliba-repeater h4{margin:0 0 10px}.myliba-repeater__rows{display:grid;gap:10px;margin-bottom:10px}.myliba-repeater__row{background:#fff;border:1px solid #dcdcde;border-radius:5px;padding:10px}.myliba-repeater__row-head{align-items:center;display:flex;justify-content:space-between}.myliba-repeater__row .myliba-page-content__field{margin:10px 0}
    </style>
    <script>
        document.addEventListener('click', function (event) {
            var add = event.target.closest('.myliba-repeater__add');
            if (add) {
                var repeater = add.closest('.myliba-repeater');
                var rows = repeater.querySelector('.myliba-repeater__rows');
                var index = rows.children.length;
                var html = repeater.querySelector('template').innerHTML.replaceAll('__INDEX__', String(index));
                rows.insertAdjacentHTML('beforeend', html);
                renumber(rows);
                return;
            }
            var remove = event.target.closest('.myliba-repeater__remove');
            if (remove) {
                var currentRows = remove.closest('.myliba-repeater__rows');
                remove.closest('.myliba-repeater__row').remove();
                renumber(currentRows);
            }
        });
        function renumber(rows) {
            rows.querySelectorAll('.myliba-repeater__row').forEach(function (row, index) {
                row.querySelector('.myliba-repeater__number').textContent = String(index + 1);
                row.querySelectorAll('[name]').forEach(function (field) {
                    field.name = field.name.replace(/\[collections\]\[([^\]]+)\]\[\d+\]/, '[collections][$1][' + index + ']');
                });
            });
        }
    </script>
    <?php
}

function save(int $post_id, \WP_Post $post, bool $update): void
{
    unset($update);
    if (!isset($_POST['myliba_page_content_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['myliba_page_content_nonce'])), 'myliba_page_content_' . $post_id)) {
        return;
    }
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
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

    $document = ['schema' => $schema, 'version' => SCHEMA_VERSION, 'fields' => $fields, 'collections' => $collections];
    update_post_meta($post_id, META_KEY, wp_json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}
