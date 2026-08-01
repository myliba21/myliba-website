<?php
if (!defined('ABSPATH')) {
    exit;
}

$demo_url = myliba_demo_url();
$modules = [
    [
        'number' => '01',
        'title' => 'Strateji ve Hedef Yönetimi',
        'text' => 'Çalışanlarınızı organizasyonunuzun strateji ve hedeflerine hizalayın. Siloları yıkın, herkes Kutup Yıldızı’na odaklansın.',
        'items' => ['Native OKR', 'Hedef Haritası', 'Anlık İlerleme Takibi', 'Hedef Zorluk Analizi', 'Stratejik Aksiyonların İzlenmesi'],
    ],
    [
        'number' => '02',
        'title' => 'Performans Yönetimi',
        'text' => 'Gerçek zamanlı performans yönetimi artık mümkün! AI destekli aksiyon ve KPI kartlarıyla prim hakedişlerini gerçek zamanlı takip ederek süreçlerinizi şeffaflaştırın.',
        'items' => ['AI Destekli Görev ve Aksiyon Yönetimi', 'KPI Kartları ve Veri Entegrasyonu'],
    ],
    [
        'number' => '03',
        'title' => 'Sürekli Diyalog ve Kültür Yönetimi',
        'text' => 'Yılda bir kez yapılan notlamaları unutun. 1:1 görüşmeler, anlık geri bildirim, ileri bildirim ve takdir kültürüyle gelişimi günlük işin bir parçası haline getirin.',
        'items' => ['Diyalog (1:1 Görüşmeler)', 'Geri Bildirim & İleri Bildirim', 'Takdir ve Oyunlaştırma'],
    ],
    [
        'number' => '04',
        'title' => 'Adil Kararlar',
        'text' => 'İnsan yönetiminde tahmin devrini kapatın. Hedef, performans, kültürel uyum ve liderlik verilerini tek noktada birleştirerek %100 veriye dayalı kararlar alın.',
        'items' => ['360°, 45° ve 90° Değer ve Yetkinlik Analizleri', 'Kültür, Bağlılık ve İsteklilik Analizi', 'Lider Kararı & Keeper Test', 'AI Destekli İçgörüler'],
    ],
];

$stats = [
    ['%85', 'Tasarruf', 'Farklı modülleri tek noktada sunarak toplam İK bütçenizde %85’e varan maliyet tasarrufu.'],
    ['+40', 'Gün Kazanç', 'Çalışanların operasyonel yükünü azaltarak stratejik çalışmalara 40 güne varan ek zaman.'],
    ['2×', 'Performans', 'Sosyal taahhüt yönetimi ve anlık geri bildirim döngüleri ile ekiplerinizde 2x performans artışı.'],
    ['%67', 'Daha Güçlü Hedefler', 'Geleneksel yöntemlere kıyasla %67 daha güçlü ve dönüşüme öncülük eden hedefler.'],
];

$faqs = [
    ['Myliba hangi şirketler için uygundur?', 'Myliba; hedeflerini, performansını ve kültürünü tek bir sistemde yönetmek isteyen büyüme aşamasındaki şirketlerden çok lokasyonlu büyük organizasyonlara kadar ölçeklenebilir. Yapı, sektör ve ekip büyüklüğüne göre özelleştirilebilir.'],
    ['Myliba Yazılım, geleneksel performans sistemlerinden nasıl ayrılır?', 'Geleneksel sistemler çoğunlukla yılda bir kez doldurulan formlara ve geriye dönük yorumlara dayanır. Myliba ise hedef, KPI, aksiyon, diyalog ve kültür verilerini canlı olarak birleştirir; liderlere güncel ve karşılaştırılabilir karar verisi sunar.'],
    ['Performans değerlendirme formlarının yerine geçebilir mi?', 'Evet. Sürekli hedef ilerlemesi, KPI sonuçları, aksiyonlar, geri bildirimler ve yetkinlik analizleri aynı çalışan görünümünde birleşir. Böylece dönem sonu formları yerine yıl boyunca oluşan kanıtlarla değerlendirme yapılabilir.'],
    ['Myliba Yazılım içinde hem OKR (Hedef Yönetimi) hem de geleneksel KPI takibi aynı anda yapılabilir mi?', 'Evet. Stratejik yönü OKR’larla, operasyonel başarı ölçütlerini KPI kartlarıyla aynı yapı içinde takip edebilirsiniz. İki yaklaşım birbirine bağlanabilir ve ortak analitik görünümde raporlanabilir.'],
    ['Myliba kültürel virüsleri ve riskleri nasıl tespit ediyor?', 'Bağlılık, isteklilik, kültürel uyum, geri bildirim ve liderlik verilerindeki eğilimler birlikte analiz edilir. Riskli değişimler ve tutarsızlıklar görünür hale getirilerek liderlerin erken aksiyon alması desteklenir.'],
    ['Kurulum ne kadar sürer?', 'Kurulum süresi organizasyon yapısına, veri aktarımına ve entegrasyon kapsamına göre değişir. İhtiyaç analizi sonrasında ekip yapısı, hedef döngüsü ve yetkilendirmeler planlanarak kademeli ve kontrollü bir devreye alma süreci yürütülür.'],
    ['Myliba Yazılım mevcut araçlarla nasıl entegre olur?', 'KPI ve organizasyon verileri API ve uygun veri bağlantıları üzerinden mevcut İK, iş zekâsı ve operasyon araçlarıyla ilişkilendirilebilir. Entegrasyon kapsamı kurumunuzun kullandığı sistemlere göre birlikte belirlenir.'],
];

get_header();
?>

<div class="software-page">
    <section class="software-hero">
        <div class="software-hero__content">
            <p class="eyebrow"><?php echo esc_html(myliba_text('Myliba Yazılım')); ?> <span>|</span> <?php echo esc_html(myliba_text('Veriyle Konuşan, Gelişim ve İnsan Odaklı Yazılım')); ?></p>
            <h1><?php echo esc_html(myliba_text('Formları Tarihe Gömün:')); ?> <em><?php echo esc_html(myliba_text('%100 Objektif Verilerle')); ?></em> <?php echo esc_html(myliba_text('Anlık Performans Yönetimi')); ?></h1>
            <p class="software-hero__lead"><?php echo esc_html(myliba_text('Performans değerlendirmeyi yılda bir kez yapılan öznel bir notlama süreci olmaktan çıkarın. Myliba Yazılım ile hedef ve performans yönetimini canlı analitik verilerle takip edin. Terfi, ücret ve gelişim gibi kritik lider kararlarını adil, şeffaf ve güven veren verilere dayandırın.')); ?></p>
            <div class="software-hero__actions">
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_text('Demo Talep Edin')); ?></a>
                <a class="myliba-button myliba-button--ghost" href="#moduller"><?php echo esc_html(myliba_text('Modülleri Keşfedin')); ?> <span
                        aria-hidden="true">↓</span></a>
            </div>
            <div class="software-hero__proof" aria-label="<?php echo esc_attr(myliba_text('Myliba yazılım avantajları')); ?>">
                <span><i></i> <?php echo esc_html(myliba_text('Canlı veri')); ?></span>
                <span><i></i> <?php echo esc_html(myliba_text('Adil karar')); ?></span>
                <span><i></i> <?php echo esc_html(myliba_text('İnsan odaklı gelişim')); ?></span>
            </div>
        </div>

        <div class="software-hero__visual" aria-label="<?php echo esc_attr(myliba_text('Çalışan sıralaması ve NineBox analiz ekranı')); ?>">
            <div class="software-screen">
                <div class="software-screen__top">
                    <span class="software-screen__brand"><?php echo esc_html(myliba_text('Myliba')); ?> <small><?php echo esc_html(myliba_text('Analytics')); ?></small></span>
                    <div><i></i><i></i><i></i></div>
                </div>
                <div class="software-screen__body">
                    <aside>
                        <span class="is-active"><?php echo esc_html(myliba_text('NineBox')); ?></span>
                        <span><?php echo esc_html(myliba_text('Çalışanlar')); ?></span>
                        <span><?php echo esc_html(myliba_text('Performans')); ?></span>
                        <span><?php echo esc_html(myliba_text('İçgörüler')); ?></span>
                    </aside>
                    <div class="software-ninebox">
                        <div class="software-ninebox__head">
                            <div><small><?php echo esc_html(myliba_text('Analiz')); ?></small><strong><?php echo esc_html(myliba_text('Çalışan Sıralaması')); ?></strong></div>
                            <span><?php echo esc_html(myliba_text('2026 · Canlı')); ?></span>
                        </div>
                        <div class="software-ninebox__chart">
                            <?php
                            $boxes = [
                                ['Gelişen Potansiyel', '2', 'yellow'],
                                ['Yüksek Potansiyel', '5', 'mint'],
                                ['Geleceğin Liderleri', '3', 'green'],
                                ['Güvenilir Oyuncu', '7', 'sand'],
                                ['Güçlü Performans', '9', 'blue'],
                                ['Yıldızlar', '4', 'teal'],
                                ['Destek Gerekli', '2', 'rose'],
                                ['Uzman Katkı', '6', 'violet'],
                                ['Kritik Yetenek', '3', 'navy'],
                            ];
                            foreach ($boxes as [$label, $count, $tone]):
                                ?>
                                <div class="software-ninebox__cell software-ninebox__cell--<?php echo esc_attr($tone); ?>">
                                    <span><?php echo esc_html(myliba_text($label)); ?></span>
                                    <strong><?php echo esc_html(myliba_text($count)); ?></strong>
                                    <div class="software-avatars"><i></i><i></i><i></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <span class="software-ninebox__axis software-ninebox__axis--x"><?php echo esc_html(myliba_text('Performans')); ?> →</span>
                        <span class="software-ninebox__axis software-ninebox__axis--y"><?php echo esc_html(myliba_text('Potansiyel')); ?> →</span>
                    </div>
                </div>
            </div>
            <div class="software-floating-card software-floating-card--score"><strong><?php echo esc_html(myliba_text('94')); ?></strong><span><?php echo esc_html(myliba_text('Adil Karar Skoru')); ?></span></div>
            <div class="software-floating-card software-floating-card--ai"><span>✦ <?php echo esc_html(myliba_text('AI İçgörüsü')); ?></span><strong><?php echo esc_html(myliba_text('3 kritik yetenek yükselişte')); ?></strong></div>
        </div>
    </section>

    <?php get_template_part('template-parts/client-logo-marquee', null, [
        'label' => myliba_text('Canlı performans kültürü kuran ekipler'),
        'title' => myliba_text('Veriyle daha adil kararlar alan kurumların yanında.'),
        'text' => myliba_text('Farklı sektörlerden ekipler hedef, performans ve gelişim ritimlerini Myliba ile tek noktada buluşturuyor.'),
        'class' => 'software-trust-section',
        'heading_id' => 'software-trust-title',
    ]); ?>

    <section id="moduller" class="software-section software-modules">
        <div class="software-section__heading">
            <div>
                <p class="eyebrow"><?php echo esc_html(myliba_text('Tek platform. Dört güçlü odak.')); ?></p>
                <h2><?php echo esc_html(myliba_text('Adil Karar Yönetimi')); ?></h2>
            </div>
            <p><?php echo esc_html(myliba_text('Terfi, ücret, prim ve liderlik kararlarını; OKR, KPI, aksiyonlar, 360° analizler, kültür, bağlılık ve yapay zekâ içgörüleriyle destekleyin. Dedikodu, mobbing ve adaletsizlik gibi kültürel virüsleri erkenden tespit edin.')); ?> <strong><?php echo esc_html(myliba_text('Kararlarınızı adil, şeffaf ve %100 objektif verilere dayandırın.')); ?></strong></p>
        </div>
        <div class="software-modules__grid">
            <?php foreach ($modules as $module_index => $module): ?>
                <article>
                    <div class="software-module__top">
                        <span><?php echo esc_html(myliba_text($module['number'])); ?></span>
                        <i aria-hidden="true">↗</i>
                    </div>
                    <div class="software-module-visual software-module-visual--<?php echo esc_attr((string) ($module_index + 1)); ?>" aria-hidden="true">
                        <?php if ($module_index === 0): ?>
                            <span class="software-module-visual__node is-primary"></span>
                            <span class="software-module-visual__node"></span>
                            <span class="software-module-visual__node"></span>
                            <span class="software-module-visual__node"></span>
                            <i></i><i></i><i></i>
                        <?php elseif ($module_index === 1): ?>
                            <span style="--value: 72%"></span><span style="--value: 46%"></span><span style="--value: 88%"></span><span style="--value: 62%"></span>
                        <?php elseif ($module_index === 2): ?>
                            <span><i></i><b></b></span><span><i></i><b></b></span><span><i></i><b></b></span>
                        <?php else: ?>
                            <?php for ($cell = 0; $cell < 9; $cell++): ?><span></span><?php endfor; ?>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo esc_html(myliba_text($module['title'])); ?></h3>
                    <p><?php echo esc_html(myliba_text($module['text'])); ?></p>
                    <ul>
                        <?php foreach ($module['items'] as $item): ?>
                            <li><?php echo esc_html(myliba_text($item)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="software-workflow" aria-labelledby="software-workflow-title">
        <div class="software-workflow__inner">
            <header class="software-workflow__heading">
                <div>
                    <p class="eyebrow"><?php echo esc_html(myliba_text('Tek ve sürekli bir çalışma döngüsü')); ?></p>
                    <h2 id="software-workflow-title"><?php echo esc_html(myliba_text('Stratejiden insan kararlarına kesintisiz veri akışı.')); ?></h2>
                </div>
                <p><?php echo esc_html(myliba_text('Yıl sonunu beklemeden hedefleri, aksiyonları, görüşmeleri ve gelişim sinyallerini aynı ritimde yönetin.')); ?></p>
            </header>
            <div class="software-workflow__grid">
                <?php
                $workflow_steps = [
                    ['Hedefleri belirle', 'Stratejiyi OKR, KPI ve sorumluluklarla görünür hale getirin.'],
                    ['Canlı takip et', 'İlerleme, aksiyon ve risk sinyallerini tek ekranda izleyin.'],
                    ['Görüş ve geliştir', '1:1, geri bildirim ve takdiri günlük çalışma akışına taşıyın.'],
                    ['Adil karar al', 'Terfi, ücret ve gelişim kararlarını yıl boyunca oluşan kanıtlara dayandırın.'],
                ];
                ?>
                <?php foreach ($workflow_steps as $index => [$step_title, $step_text]): ?>
                    <article>
                        <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <div class="software-workflow__pulse" aria-hidden="true"><i></i><i></i><i></i></div>
                        <h3><?php echo esc_html(myliba_text($step_title)); ?></h3>
                        <p><?php echo esc_html(myliba_text($step_text)); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="software-why">
        <div class="software-why__inner">
            <div class="software-why__copy">
                <p class="eyebrow"><?php echo esc_html(myliba_text('Neden Myliba?')); ?></p>
                <h2><?php echo esc_html(myliba_text('Myliba Yazılım: Şirketinizin Verimliliğini Katlayan Stratejik İş Ortağı')); ?></h2>
                <div class="software-formula">
                    <small><?php echo esc_html(myliba_text('Formülümüz')); ?></small>
                    <strong><?php echo esc_html(myliba_text('Performans')); ?> <span>=</span> <?php echo esc_html(myliba_text('Potansiyel')); ?> <span>−</span> <?php echo esc_html(myliba_text('Müdahale')); ?></strong>
                </div>
                <p><?php echo esc_html(myliba_text('İnsanlara daha fazla güven, net bir odak ve gelişim alanı sunduğunuzda performans doğal bir ritimle ortaya çıkar. Myliba Yazılım bu anlayışı canlı verilere dönüştürür.')); ?></p>
            </div>
            <div class="software-stats">
                <?php foreach ($stats as $index => [$value, $label, $text]): ?>
                    <article>
                        <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <strong><?php echo esc_html(myliba_text($value)); ?></strong>
                        <h3><?php echo esc_html(myliba_text($label)); ?></h3>
                        <p><?php echo esc_html(myliba_text($text)); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="software-section software-assurance" aria-labelledby="software-assurance-title">
        <div class="software-assurance__heading">
            <div>
                <p class="eyebrow"><?php echo esc_html(myliba_text('Kurumsal ölçekte güvenli geçiş')); ?></p>
                <h2 id="software-assurance-title"><?php echo esc_html(myliba_text('Sistemlerinize uyum sağlayan kontrollü bir kurulum.')); ?></h2>
            </div>
            <p><?php echo esc_html(myliba_text('Organizasyon yapısı, hedef döngüsü, veri aktarımı ve entegrasyon kapsamı ihtiyaç analiziyle belirlenir; devreye alma süreci ekiplerinizle birlikte planlanır.')); ?></p>
        </div>
        <div class="software-assurance__grid">
            <article>
                <span><?php echo esc_html(myliba_text('01')); ?></span>
                <i aria-hidden="true">↔</i>
                <h3><?php echo esc_html(myliba_text('API ve veri bağlantıları')); ?></h3>
                <p><?php echo esc_html(myliba_text('KPI ve organizasyon verilerini mevcut İK, iş zekâsı ve operasyon araçlarınızla ilişkilendirin.')); ?></p>
            </article>
            <article>
                <span><?php echo esc_html(myliba_text('02')); ?></span>
                <i aria-hidden="true">◎</i>
                <h3><?php echo esc_html(myliba_text('Kurumunuza özel yapı')); ?></h3>
                <p><?php echo esc_html(myliba_text('Ekip, hedef, değerlendirme ve yetkilendirme modelini organizasyonunuzun çalışma biçimine göre kurgulayın.')); ?></p>
            </article>
            <article>
                <span><?php echo esc_html(myliba_text('03')); ?></span>
                <i aria-hidden="true">✓</i>
                <h3><?php echo esc_html(myliba_text('Kontrollü devreye alma')); ?></h3>
                <p><?php echo esc_html(myliba_text('İhtiyaç analizinden veri aktarımına kadar kurulum adımlarını kademeli ve izlenebilir biçimde ilerletin.')); ?></p>
            </article>
        </div>
        <div class="software-assurance__rail" aria-label="<?php echo esc_attr(myliba_text('Entegrasyon kapsamları')); ?>">
            <span><?php echo esc_html(myliba_text('İK sistemleri')); ?></span><i></i><span><?php echo esc_html(myliba_text('İş zekâsı')); ?></span><i></i><span><?php echo esc_html(myliba_text('Operasyon verileri')); ?></span><i></i><span><?php echo esc_html(myliba_text('API bağlantıları')); ?></span>
        </div>
    </section>

    <section class="software-section software-faq">
        <div class="software-faq__heading">
            <p class="eyebrow"><?php echo esc_html(myliba_text('Merak Edilenler')); ?></p>
            <h2><?php echo esc_html(myliba_text('Myliba Yazılım Hakkında Sıkça Sorulan Sorular')); ?></h2>
            <p><?php echo esc_html(myliba_text('Yeni nesil performans yönetimine geçerken bilmek isteyeceğiniz temel noktalar.')); ?></p>
        </div>
        <div class="software-faq__items">
            <?php foreach ($faqs as $index => [$question, $answer]): ?>
                <details <?php echo $index === 0 ? 'open' : ''; ?>>
                    <summary>
                        <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span><strong><?php echo esc_html(myliba_text($question)); ?></strong><i
                            aria-hidden="true"></i></summary>
                    <div>
                        <p><?php echo esc_html(myliba_text($answer)); ?></p>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="software-final">
        <div class="software-final__content">
            <p class="eyebrow"><?php echo esc_html(myliba_text('Dönüşüm için ilk adım')); ?></p>
            <h2><?php echo esc_html(myliba_text('Performansı yılda bir kez değil, her gün geliştirin.')); ?></h2>
            <p><?php echo esc_html(myliba_text('Her organizasyonun performans yolculuğu farklıdır. İhtiyaçlarınıza özel kişiselleştirilmiş bir demo ile Myliba’nın şirketinizde nasıl değer yaratacağını birlikte keşfedelim.')); ?></p>
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_text('Kişiselleştirilmiş Demo Talep Edin')); ?> <span aria-hidden="true">→</span></a>
        </div>
        <div class="software-final__signal" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
    </section>
</div>

<?php get_footer(); ?>
