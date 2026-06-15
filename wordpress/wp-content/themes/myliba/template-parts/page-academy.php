<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();
$lang = myliba_current_language();
$is_tr = $lang === 'tr';
$demo_url = myliba_demo_url();
$contact_url = myliba_page_url('contact');
$hero_title = $is_tr ? 'OKR Kultur Akademisi' : 'OKR Culture Academy';
$hero_subtitle = $is_tr
    ? 'OKR, CFR, KPI ve kocluk rutinlerini kurum icinde uygulanabilir bir performans kulturu haline getiren hibrit gelisim programi.'
    : 'A hybrid development program that turns OKR, CFR, KPI and coaching routines into a practical performance culture inside the organization.';
$hero_eyebrow = $is_tr ? 'Akademi programi' : 'Academy program';
$hero_stats = $is_tr
    ? [
        ['40', 'saat hibrit gelisim'],
        ['ICF', 'CCE akreditasyonu'],
        ['OKR', 'kultur ve kocluk rutini'],
    ]
    : [
        ['40', 'hours of hybrid development'],
        ['ICF', 'CCE accredited'],
        ['OKR', 'culture and coaching routines'],
    ];
$proof_points = $is_tr
    ? ['Ust yonetimden takimlara net hedef ritmi', 'Uygulama atolyeleri ve kocluk pratikleri', 'Surekli performans ve geri bildirim kulturu']
    : ['A clear goal rhythm from leadership to teams', 'Implementation workshops and coaching practice', 'Continuous performance and feedback culture'];
$audience_items = $is_tr
    ? [
        ['Kurum ici OKR koclari', 'OKR disiplinini ekiplerin gunluk is akisina tasiyacak lider ve HR profesyonelleri.'],
        ['Liderlik ekipleri', 'Strateji, hedef ve aksiyon takibini ayni yonetim ritminde birlestirmek isteyen yoneticiler.'],
        ['Kultur ve insan ekipleri', 'Geri bildirim, takdir, 1:1 ve gelisim konusmalarini olculebilir hale getiren ekipler.'],
    ]
    : [
        ['Internal OKR coaches', 'Leaders and HR professionals who will carry OKR discipline into daily team routines.'],
        ['Leadership teams', 'Executives who want strategy, goals and action follow-up in the same operating rhythm.'],
        ['People and culture teams', 'Teams making feedback, recognition, 1:1 and development conversations measurable.'],
    ];
$journey_steps = $is_tr
    ? [
        ['01', 'Farkindalik', 'Hedef sistemleri, OKR prensipleri ve kurum kulturu arasindaki bag netlesir.'],
        ['02', 'Tasarim', 'Sirket, takim ve birey seviyesinde OKR/KPI mimarisi uygulanabilir hale gelir.'],
        ['03', 'Kocluk', 'CFR, 1:1, geri bildirim ve feedforward pratikleri davranis rutinine donusur.'],
        ['04', 'Surdurme', 'Raporlama, aksiyon takipleri ve gelisim donguleriyle ritim korunur.'],
    ]
    : [
        ['01', 'Awareness', 'The link between goal systems, OKR principles and culture becomes clear.'],
        ['02', 'Design', 'Company, team and individual OKR/KPI architecture becomes practical.'],
        ['03', 'Coaching', 'CFR, 1:1, feedback and feedforward practices turn into behavior routines.'],
        ['04', 'Sustain', 'Reporting, action follow-up and development loops keep the rhythm alive.'],
    ];
$curriculum_items = $is_tr
    ? [
        'Hedef yonetimi tarihi, OKR anatomisi ve KPI baglantisi',
        'Kurumsal, takim ve bireysel OKR ornekleri',
        'OKR haritalari, hizalanma ve onceliklendirme',
        'CFR, geri bildirim, feedforward ve takdir pratikleri',
        'Performans gelisimi, 1:1 gorusmeler ve aksiyon yonetimi',
        'OKR koclugu, fasilitasyon ve kurum ici yayilim plani',
    ]
    : [
        'Goal management history, OKR anatomy and KPI connection',
        'Corporate, team and individual OKR examples',
        'OKR maps, alignment and prioritization',
        'CFR, feedback, feedforward and recognition practices',
        'Performance development, 1:1 meetings and action management',
        'OKR coaching, facilitation and internal rollout planning',
    ];
$training_cards = $is_tr
    ? [
        ['Hedef Yonetimi ve OKR', 'OKR tasarimi, hizalanma ve olcumleme pratikleri.'],
        ['Performans Koclugu', 'Liderlerin ekiplerle daha etkili gelisim konusmalari yapmasi.'],
        ['Kultur Koclugu', 'Degerler, diyalog, takdir ve psikolojik guven rutinleri.'],
    ]
    : [
        ['Goal Management and OKR', 'OKR design, alignment and measurement practices.'],
        ['Performance Coaching', 'Helping leaders hold better development conversations with teams.'],
        ['Culture Coaching', 'Values, dialogue, recognition and psychological safety routines.'],
    ];

get_header();
?>

<div class="academy-page">
    <section class="academy-hero">
        <div class="academy-hero__content">
            <p class="eyebrow"><?php echo esc_html($hero_eyebrow); ?></p>
            <h1><?php echo esc_html($hero_title); ?></h1>
            <p><?php echo esc_html($hero_subtitle); ?></p>
            <div class="academy-hero__actions">
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html($is_tr ? 'Programi goruselim' : 'Discuss the program'); ?></a>
                <a class="myliba-button myliba-button--ghost" href="#academy-curriculum"><?php echo esc_html($is_tr ? 'Mufredati incele' : 'View curriculum'); ?></a>
            </div>
        </div>
        <div class="academy-hero__visual" aria-label="<?php echo esc_attr($hero_title); ?>">
            <?php if (has_post_thumbnail($post_id)) : ?>
                <?php echo get_the_post_thumbnail($post_id, 'large'); ?>
            <?php endif; ?>
            <div class="academy-hero__stat-grid">
                <?php foreach ($hero_stats as $stat) : ?>
                    <div>
                        <strong><?php echo esc_html($stat[0]); ?></strong>
                        <span><?php echo esc_html($stat[1]); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="academy-proof">
        <?php foreach ($proof_points as $point) : ?>
            <div><?php echo esc_html($point); ?></div>
        <?php endforeach; ?>
    </section>

    <section class="section academy-section academy-section--intro">
        <div class="academy-section__heading">
            <p class="eyebrow"><?php echo esc_html($is_tr ? 'Kimler icin' : 'Who it is for'); ?></p>
            <h2><?php echo esc_html($is_tr ? 'OKR davranisini kuruma yaymak isteyen ekipler icin tasarlandi.' : 'Designed for teams that want OKR behavior to scale across the organization.'); ?></h2>
        </div>
        <div class="academy-audience-grid">
            <?php foreach ($audience_items as $item) : ?>
                <article class="academy-card">
                    <h3><?php echo esc_html($item[0]); ?></h3>
                    <p><?php echo esc_html($item[1]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section academy-section academy-section--journey">
        <div class="academy-section__heading">
            <p class="eyebrow"><?php echo esc_html($is_tr ? 'Program akisi' : 'Program journey'); ?></p>
            <h2><?php echo esc_html($is_tr ? 'Egitim, atelye ve kocluk ayni is ritminde ilerler.' : 'Training, workshops and coaching move in one operating rhythm.'); ?></h2>
        </div>
        <div class="academy-journey">
            <?php foreach ($journey_steps as $step) : ?>
                <article class="academy-journey__step">
                    <span><?php echo esc_html($step[0]); ?></span>
                    <h3><?php echo esc_html($step[1]); ?></h3>
                    <p><?php echo esc_html($step[2]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="academy-curriculum" class="section academy-section academy-curriculum">
        <div class="academy-curriculum__panel">
            <div>
                <p class="eyebrow"><?php echo esc_html($is_tr ? '40 saatlik hibrit icerik' : '40-hour hybrid content'); ?></p>
                <h2><?php echo esc_html($is_tr ? 'Teoriden uygulamaya uzanan kompakt bir OKR kocluk cercevesi.' : 'A compact OKR coaching framework from theory to application.'); ?></h2>
                <p><?php echo esc_html($is_tr ? 'Program, katilimcilarin OKR sistemini sadece anlatmasini degil, kendi kurumlarinda uygulamaya alabilecek bir ritim kurmasini hedefler.' : 'The program helps participants move beyond explaining OKRs and build a rhythm they can implement in their own organizations.'); ?></p>
            </div>
            <ul class="academy-check-list">
                <?php foreach ($curriculum_items as $item) : ?>
                    <li><?php echo esc_html($item); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="section academy-section">
        <div class="academy-section__heading">
            <p class="eyebrow"><?php echo esc_html($is_tr ? 'Akademi egitimleri' : 'Academy trainings'); ?></p>
            <h2><?php echo esc_html($is_tr ? 'Programi guclendiren uzmanlik alanlari.' : 'Specialized tracks that strengthen the program.'); ?></h2>
        </div>
        <div class="academy-training-grid">
            <?php foreach ($training_cards as $card) : ?>
                <article class="academy-training-card">
                    <h3><?php echo esc_html($card[0]); ?></h3>
                    <p><?php echo esc_html($card[1]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section academy-final">
        <div class="academy-final__inner">
            <div>
                <p class="eyebrow"><?php echo esc_html($is_tr ? 'Sonraki adim' : 'Next step'); ?></p>
                <h2><?php echo esc_html($is_tr ? 'Kurumunuz icin dogru akademi yolunu birlikte netlestirelim.' : 'Clarify the right academy path for your organization.'); ?></h2>
                <p><?php echo esc_html($is_tr ? 'Ekip yapinizi, mevcut hedef yonetimi olgunlugunuzu ve yayilim ihtiyacinizi anlayip size uygun program akisini onerebiliriz.' : 'We can understand your team structure, goal-management maturity and rollout needs, then recommend the right program flow.'); ?></p>
            </div>
            <div class="academy-final__actions">
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html($is_tr ? 'Demo talep et' : 'Request a demo'); ?></a>
                <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url($contact_url); ?>"><?php echo esc_html($is_tr ? 'Iletisime gec' : 'Contact us'); ?></a>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>
