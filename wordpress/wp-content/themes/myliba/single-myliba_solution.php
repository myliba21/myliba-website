<?php
get_header();

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $slug = (string) get_post_field('post_name', $post_id);
    $catalog = myliba_solution_catalog();
    $solution = $catalog[$slug] ?? [
        'title' => get_the_title(),
        'kicker' => (string) myliba_meta('_myliba_label', $post_id, 'Myliba çözümü'),
        'summary' => (string) myliba_meta('_myliba_hero_subtitle', $post_id, myliba_excerpt($post_id, 30)),
        'intro' => (string) myliba_meta('_myliba_solution', $post_id, myliba_excerpt($post_id, 45)),
        'items' => myliba_lines((string) myliba_meta('_myliba_benefits', $post_id)),
        'steps' => [],
    ];
    $stored_kicker = trim((string) myliba_meta('_myliba_label', $post_id));
    $stored_intro = trim((string) myliba_meta('_myliba_solution', $post_id));
    $stored_items = myliba_lines((string) myliba_meta('_myliba_benefits', $post_id));
    $post_excerpt = trim((string) get_the_excerpt());

    if ($stored_kicker !== '') {
        $solution['kicker'] = $stored_kicker;
    }
    if ($stored_intro !== '') {
        $solution['intro'] = $stored_intro;
    }
    if ($stored_items) {
        $solution['items'] = $stored_items;
    }

    $title = (string) myliba_meta('_myliba_hero_title', $post_id, get_the_title());
    $summary = (string) myliba_meta('_myliba_hero_subtitle', $post_id, $post_excerpt !== '' ? $post_excerpt : $solution['summary']);
    $editor_content = trim(wp_strip_all_tags((string) get_the_content()));
    $audiences = !empty($solution['audiences']) ? $solution['audiences'] : [
        'İnsan ve kültür ekipleri',
        'Liderlik ekipleri',
        'Dönüşüm ekipleri',
    ];
    ?>
    <article class="solution-detail">
        <section class="solution-detail__hero">
            <div class="solutions-shell">
                <div class="solution-detail__hero-grid">
                    <div class="solution-detail__hero-copy">
                        <a class="solution-detail__back" href="<?php echo esc_url(myliba_page_url('solutions')); ?>">← Tüm çözümler</a>
                        <p class="eyebrow"><?php echo esc_html($solution['kicker']); ?></p>
                        <h1><?php echo esc_html($title); ?></h1>
                        <p class="solution-detail__lead"><?php echo esc_html($summary); ?></p>
                        <div class="solution-detail__actions">
                            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>">Programı birlikte tasarlayalım</a>
                            <?php if (!empty($solution['steps'])) : ?>
                                <a class="solution-detail__text-link" href="#calisma-modeli">Çalışma modelini inceleyin <span aria-hidden="true">↓</span></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="solution-journey" aria-hidden="true">
                        <div class="solution-journey__topline">
                            <span>Myliba gelişim yolculuğu</span>
                            <i></i>
                        </div>
                        <strong>Kuruma özel.<br>İşin içinde.<br>Ölçülebilir.</strong>
                        <?php if (!empty($solution['steps'])) : ?>
                            <div class="solution-journey__steps">
                                <?php foreach (array_slice($solution['steps'], 0, 3) as $index => $step) : ?>
                                    <span><b><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo esc_html($step['title']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="solution-detail__principles" aria-label="Programın temel özellikleri">
                    <span><b>01</b> Kuruma özel tasarım</span>
                    <span><b>02</b> İşbaşı uygulama</span>
                    <span><b>03</b> Ölçülebilir takip</span>
                </div>
            </div>
        </section>

        <section id="cozum-detaylari" class="solution-detail__intro solutions-shell">
            <div>
                <p class="eyebrow">Myliba yaklaşımı</p>
                <h2>Kültürü, hedefleri ve iş sonuçlarını birlikte geliştirin.</h2>
            </div>
            <div class="solution-detail__intro-copy">
                <p><?php echo esc_html($solution['intro']); ?></p>
                <a href="<?php echo esc_url(myliba_page_url('contact')); ?>">İhtiyacınızı birlikte değerlendirelim <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <section class="solution-audiences solutions-shell" aria-labelledby="solution-audiences-title">
            <div class="solution-audiences__heading">
                <p class="eyebrow">Kimler için?</p>
                <h2 id="solution-audiences-title">Değişimi birlikte yöneten ekipler için.</h2>
            </div>
            <div class="solution-audiences__grid">
                <?php foreach ($audiences as $index => $audience) : ?>
                    <article>
                        <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <h3><?php echo esc_html($audience); ?></h3>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($solution['items'])) : ?>
            <section class="solution-outcomes">
                <div class="solutions-shell">
                    <header class="solution-outcomes__heading">
                        <p class="eyebrow">Beklenen kazanımlar</p>
                        <h2>Programla birlikte ne değişir?</h2>
                        <p>Gelişimi tek seferlik bir müdahaleden çıkarıp, kurumun çalışma biçimine yerleştirin.</p>
                    </header>
                    <div class="solution-outcomes__grid">
                        <?php foreach ($solution['items'] as $index => $item) : ?>
                            <article>
                                <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                <p><?php echo esc_html($item); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($solution['metrics'])) : ?>
            <section class="solution-metrics">
                <div class="solutions-shell">
                    <p class="eyebrow">Ölçüm alanları</p>
                    <h2>Kültürü dört kritik göstergeyle görünür kılın.</h2>
                    <div class="solution-metrics__grid">
                        <?php foreach ($solution['metrics'] as $metric) : ?>
                            <article>
                                <h3><?php echo esc_html($metric['title']); ?></h3>
                                <p><?php echo esc_html($metric['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($solution['steps'])) : ?>
            <section id="calisma-modeli" class="solution-process solutions-shell">
                <header>
                    <p class="eyebrow">Çalışma modeli</p>
                    <h2><?php echo esc_html($solution['title']); ?> süreci</h2>
                    <p>Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim ritminin parçasıdır.</p>
                </header>
                <div class="solution-process__grid">
                    <?php foreach ($solution['steps'] as $index => $step) : ?>
                        <article>
                            <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <h3><?php echo esc_html($step['title']); ?></h3>
                            <p><?php echo esc_html($step['text']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php get_template_part('template-parts/client-logo-marquee', null, [
            'label' => 'Birlikte geliştiğimiz kurumlar',
            'title' => 'Farklı sektörlerden ekiplerin dönüşüm yolculuğuna eşlik ediyoruz.',
            'text' => 'Kurumların hedef, kültür ve liderlik pratiklerini birlikte güçlendiren deneyimler tasarlıyoruz.',
            'class' => 'solution-trust-section',
            'heading_id' => 'solution-trust-title',
        ]); ?>

        <?php if ($editor_content !== '' && $editor_content !== trim(wp_strip_all_tags($summary))) : ?>
            <section class="solution-detail__editor solutions-shell">
                <?php the_content(); ?>
            </section>
        <?php endif; ?>

        <section class="solutions-cta solutions-shell">
            <div>
                <p class="eyebrow">30 dakikalık keşif görüşmesi</p>
                <h2>İhtiyacınıza uygun yolculuğu birlikte tasarlayalım.</h2>
                <p>Kurumunuzun hedeflerini dinleyelim; doğru programı, kapsamı ve çalışma modelini birlikte netleştirelim.</p>
            </div>
            <div class="solutions-cta__actions">
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>">Görüşme planlayın</a>
                <a class="solutions-cta__secondary" href="<?php echo esc_url(myliba_page_url('solutions')); ?>">Tüm çözümleri görün</a>
            </div>
        </section>
    </article>
    <?php
endwhile;

get_footer();
