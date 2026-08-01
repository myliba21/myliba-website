<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
$solutions = myliba_solution_catalog();
?>
<section class="solutions-hero">
    <div class="solutions-shell">
        <p class="eyebrow"><?php echo esc_html(myliba_text('Myliba Çözümlerimiz')); ?></p>
        <h1><?php echo esc_html(myliba_text('Birbiri ile entegre,')); ?><br><?php echo esc_html(myliba_text('bütünleşik çözümler')); ?></h1>
        <p><?php echo esc_html(myliba_text('Myliba’nın özel geliştirdiği modeli ile yazılımı, akademiyi ve organizasyonel dönüşümü tek çatı altında buluşturarak yüksek performans kültürü inşa edin.')); ?></p>
    </div>
</section>

<section class="solutions-index solutions-shell">
    <header class="solutions-index__heading">
        <p class="eyebrow"><?php echo esc_html(myliba_text('İhtiyacınıza uygun çözümü seçin')); ?></p>
        <h2><?php echo esc_html(myliba_text('İster tek tek kullanın, ister bütünleştirin.')); ?></h2>
        <p><?php echo esc_html(myliba_text('Neye ihtiyacınız varsa Myliba çözümleri ile kültürünüzü geliştirin.')); ?></p>
    </header>
    <div class="solutions-index__grid">
        <?php foreach ($solutions as $slug => $solution) : ?>
            <a class="solution-index-card" href="<?php echo esc_url(myliba_solution_url($slug)); ?>">
                <span class="solution-index-card__number"><?php echo esc_html(str_pad((string) (array_search($slug, array_keys($solutions), true) + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                <p><?php echo esc_html($solution['kicker']); ?></p>
                <h2><?php echo esc_html($solution['title']); ?></h2>
                <span class="solution-index-card__summary"><?php echo esc_html($solution['summary']); ?></span>
                <strong><?php echo esc_html(myliba_text('Çözümü inceleyin')); ?> <span aria-hidden="true">→</span></strong>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="solutions-cta solutions-shell">
    <div>
        <p class="eyebrow"><?php echo esc_html(myliba_text('Birlikte belirleyelim')); ?></p>
        <h2><?php echo esc_html(myliba_text('Hangi çözüm size uygun?')); ?></h2>
        <p><?php echo esc_html(myliba_text('İhtiyacınıza en uygun programı veya danışmanlık modelini bulmak için Myliba ile tanışın.')); ?></p>
    </div>
    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html(myliba_text('Uzmanlarımızla görüşün')); ?></a>
</section>
<?php get_footer(); ?>
