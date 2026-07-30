<?php
get_header();
$solutions = myliba_solution_catalog();
?>
<section class="solutions-hero">
    <div class="solutions-shell">
        <p class="eyebrow">Myliba Çözümlerimiz</p>
        <h1>Birbiri ile entegre,<br>bütünleşik çözümler</h1>
        <p>Neye ihtiyacınız varsa Myliba çözümleri ile kültürünüzü geliştirin.</p>
    </div>
</section>
<section class="solutions-index solutions-shell">
    <div class="solutions-index__grid">
        <?php foreach ($solutions as $slug => $solution) : ?>
            <a class="solution-index-card" href="<?php echo esc_url(myliba_solution_url($slug)); ?>">
                <p><?php echo esc_html($solution['kicker']); ?></p>
                <h2><?php echo esc_html($solution['title']); ?></h2>
                <span class="solution-index-card__summary"><?php echo esc_html($solution['summary']); ?></span>
                <strong>Çözümü inceleyin <span aria-hidden="true">→</span></strong>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php get_footer(); ?>
