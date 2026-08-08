<?php
get_header();

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $content_copy = static fn (string $key): string => \Myliba\Core\PageContent\text($post_id, 'solution', $key);
    $content_rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($post_id, 'solution', $key);

    $title = $content_copy('hero_title');
    if ($title === '') {
        $title = (string) (get_post_meta($post_id, '_myliba_hero_title', true) ?: get_the_title());
    }

    $kicker = $content_copy('kicker');
    if ($kicker === '') {
        $kicker = (string) (get_post_meta($post_id, '_myliba_eyebrow', true) ?: get_post_meta($post_id, '_myliba_label', true) ?: myliba_text('Myliba Çözümü'));
    }

    $summary = $content_copy('hero_summary');
    if ($summary === '') {
        $summary = (string) (get_post_meta($post_id, '_myliba_hero_subtitle', true) ?: get_the_excerpt());
    }

    $hero_primary_label = $content_copy('hero_primary_label');
    if ($hero_primary_label === '') {
        $hero_primary_label = (string) (get_post_meta($post_id, '_myliba_cta_label', true) ?: myliba_text('Programı birlikte tasarlayalım'));
    }

    $hero_secondary_label = $content_copy('hero_secondary_label');
    if ($hero_secondary_label === '') {
        $hero_secondary_label = myliba_text('Çalışma modelini inceleyin');
    }

    $journey_eyebrow = $content_copy('journey_eyebrow');
    if ($journey_eyebrow === '') {
        $journey_eyebrow = (string) (get_post_meta($post_id, '_myliba_journey_eyebrow', true) ?: myliba_text('Myliba gelişim yolculuğu'));
    }

    $journey_title = $content_copy('journey_title');
    if ($journey_title === '') {
        $journey_title = (string) (get_post_meta($post_id, '_myliba_journey_title', true) ?: myliba_text("Kuruma özel.\nİşin içinde.\nÖlçülebilir."));
    }

    $principle_1 = $content_copy('principle_1');
    if ($principle_1 === '') {
        $principle_1 = (string) (get_post_meta($post_id, '_myliba_principle_1', true) ?: myliba_text('Kuruma özel tasarım'));
    }

    $principle_2 = $content_copy('principle_2');
    if ($principle_2 === '') {
        $principle_2 = (string) (get_post_meta($post_id, '_myliba_principle_2', true) ?: myliba_text('İşbaşı uygulama'));
    }

    $principle_3 = $content_copy('principle_3');
    if ($principle_3 === '') {
        $principle_3 = (string) (get_post_meta($post_id, '_myliba_principle_3', true) ?: myliba_text('Ölçülebilir takip'));
    }

    $intro_eyebrow = $content_copy('intro_eyebrow');
    if ($intro_eyebrow === '') {
        $intro_eyebrow = (string) (get_post_meta($post_id, '_myliba_intro_eyebrow', true) ?: myliba_text('Myliba yaklaşımı'));
    }

    $intro_title = $content_copy('intro_title');
    if ($intro_title === '') {
        $intro_title = (string) (get_post_meta($post_id, '_myliba_intro_title', true) ?: myliba_text('Kültürü, hedefleri ve iş sonuçlarını birlikte geliştirin.'));
    }

    $intro = $content_copy('intro');
    if ($intro === '') {
        $intro = (string) (get_post_meta($post_id, '_myliba_solution', true) ?: get_post_meta($post_id, '_myliba_problem', true) ?: '');
    }

    $intro_link_label = $content_copy('intro_link_label');
    if ($intro_link_label === '') {
        $intro_link_label = myliba_text('İhtiyacınızı birlikte değerlendirelim');
    }

    $audiences_eyebrow = $content_copy('audiences_eyebrow');
    if ($audiences_eyebrow === '') {
        $audiences_eyebrow = (string) (get_post_meta($post_id, '_myliba_audiences_eyebrow', true) ?: myliba_text('Kimler için?'));
    }

    $audiences_title = $content_copy('audiences_title');
    if ($audiences_title === '') {
        $audiences_title = (string) (get_post_meta($post_id, '_myliba_audiences_title', true) ?: myliba_text('Değişimi birlikte yöneten ekipler için.'));
    }

    $items = array_column($content_rows('benefits'), 'text');
    if (empty($items)) {
        $meta_benefits = function_exists('myliba_lines') ? \myliba_lines((string) get_post_meta($post_id, '_myliba_benefits', true)) : [];
        if (!empty($meta_benefits)) {
            $items = $meta_benefits;
        }
    }

    $audiences = array_column($content_rows('audiences'), 'text');
    if (empty($audiences)) {
        $meta_audiences = function_exists('myliba_lines') ? \myliba_lines((string) get_post_meta($post_id, '_myliba_audiences', true)) : [];
        if (!empty($meta_audiences)) {
            $audiences = $meta_audiences;
        } else {
            $audiences = [
                myliba_text('İnsan ve kültür ekipleri'),
                myliba_text('Liderlik ekipleri'),
                myliba_text('Dönüşüm ekipleri'),
            ];
        }
    }

    $outcomes_eyebrow = $content_copy('outcomes_eyebrow');
    if ($outcomes_eyebrow === '') {
        $outcomes_eyebrow = (string) (get_post_meta($post_id, '_myliba_outcomes_eyebrow', true) ?: myliba_text('Beklenen kazanımlar'));
    }

    $outcomes_title = $content_copy('outcomes_title');
    if ($outcomes_title === '') {
        $outcomes_title = (string) (get_post_meta($post_id, '_myliba_outcomes_title', true) ?: myliba_text('Programla birlikte ne değişir?'));
    }

    $outcomes_lead = $content_copy('outcomes_lead');
    if ($outcomes_lead === '') {
        $outcomes_lead = (string) (get_post_meta($post_id, '_myliba_outcomes_lead', true) ?: myliba_text('Gelişimi tek seferlik bir müdahaleden çıkarıp, kurumun çalışma biçimine yerleştirin.'));
    }

    $metrics_eyebrow = $content_copy('metrics_eyebrow');
    if ($metrics_eyebrow === '') {
        $metrics_eyebrow = (string) (get_post_meta($post_id, '_myliba_metrics_eyebrow', true) ?: myliba_text('Ölçüm alanları'));
    }

    $metrics_title = $content_copy('metrics_title');
    if ($metrics_title === '') {
        $metrics_title = (string) (get_post_meta($post_id, '_myliba_metrics_title', true) ?: myliba_text('Kültürü dört kritik göstergeyle görünür kılın.'));
    }

    $metrics = $content_rows('metrics');

    $process_eyebrow = $content_copy('process_eyebrow');
    if ($process_eyebrow === '') {
        $process_eyebrow = (string) (get_post_meta($post_id, '_myliba_process_eyebrow', true) ?: myliba_text('Çalışma modeli'));
    }

    $process_title = $content_copy('process_title');
    if ($process_title === '') {
        $process_title = (string) (get_post_meta($post_id, '_myliba_process_title', true) ?: sprintf(myliba_text('%s süreci'), $title));
    }

    $process_lead = $content_copy('process_lead');
    if ($process_lead === '') {
        $process_lead = (string) (get_post_meta($post_id, '_myliba_process_lead', true) ?: myliba_text('Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim ritminin parçasıdır.'));
    }

    $steps = $content_rows('steps');

    $cta_eyebrow = $content_copy('cta_eyebrow');
    if ($cta_eyebrow === '') {
        $cta_eyebrow = (string) (get_post_meta($post_id, '_myliba_cta_eyebrow', true) ?: myliba_text('30 dakikalık keşif görüşmesi'));
    }

    $cta_title = $content_copy('cta_title');
    if ($cta_title === '') {
        $cta_title = (string) (get_post_meta($post_id, '_myliba_cta_title', true) ?: myliba_text('İhtiyacınıza uygun yolculuğu birlikte tasarlayalım.'));
    }

    $cta_text = $content_copy('cta_text');
    if ($cta_text === '') {
        $cta_text = (string) (get_post_meta($post_id, '_myliba_cta_text', true) ?: myliba_text('Kurumunuzun hedeflerini dinleyelim; doğru programı, kapsamı ve çalışma modelini birlikte netleştirelim.'));
    }

    $cta_button_label = $content_copy('cta_button_label');
    if ($cta_button_label === '') {
        $cta_button_label = (string) (get_post_meta($post_id, '_myliba_cta_button_label', true) ?: myliba_text('Görüşme planlayın'));
    }

    $cta_secondary_label = $content_copy('cta_secondary_label');
    if ($cta_secondary_label === '') {
        $cta_secondary_label = myliba_text('Tüm çözümleri görün');
    }

    $solution = [
        'title' => $title,
        'kicker' => $kicker,
        'summary' => $summary,
        'intro' => $intro,
        'items' => $items,
        'audiences' => $audiences,
        'metrics' => $metrics,
        'steps' => $steps,
    ];
    $editor_content = trim(wp_strip_all_tags((string) get_the_content()));
    ?>
    <article class="solution-detail">
        <section class="solution-detail__hero">
            <div class="solutions-shell">
                <div class="solution-detail__hero-grid">
                    <div class="solution-detail__hero-copy">
                        <a class="solution-detail__back" href="<?php echo esc_url(myliba_page_url('solutions')); ?>">← <?php echo esc_html(myliba_text('Tüm çözümler')); ?></a>
                        <p class="eyebrow"><?php echo esc_html($kicker); ?></p>
                        <h1><?php echo esc_html($title); ?></h1>
                        <p class="solution-detail__lead"><?php echo esc_html($summary); ?></p>
                        <div class="solution-detail__actions">
                            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html($hero_primary_label); ?></a>
                            <?php if (!empty($steps)) : ?>
                                <a class="solution-detail__text-link" href="#calisma-modeli"><?php echo esc_html($hero_secondary_label); ?> <span aria-hidden="true">↓</span></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="solution-journey" aria-hidden="true">
                        <div class="solution-journey__topline">
                            <span><?php echo esc_html($journey_eyebrow); ?></span>
                            <i></i>
                        </div>
                        <strong><?php echo wp_kses_post(nl2br(esc_html($journey_title))); ?></strong>
                        <?php if (!empty($steps)) : ?>
                            <div class="solution-journey__steps">
                                <?php foreach (array_slice($steps, 0, 3) as $index => $step) : ?>
                                    <span><b><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo esc_html($step['title']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="solution-detail__principles" aria-label="<?php echo esc_attr(myliba_text('Programın temel özellikleri')); ?>">
                    <span><b>01</b> <?php echo esc_html($principle_1); ?></span>
                    <span><b>02</b> <?php echo esc_html($principle_2); ?></span>
                    <span><b>03</b> <?php echo esc_html($principle_3); ?></span>
                </div>
            </div>
        </section>

        <section id="cozum-detaylari" class="solution-detail__intro solutions-shell">
            <div>
                <p class="eyebrow"><?php echo esc_html($intro_eyebrow); ?></p>
                <h2><?php echo esc_html($intro_title); ?></h2>
            </div>
            <div class="solution-detail__intro-copy">
                <p><?php echo esc_html($intro); ?></p>
                <a href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html($intro_link_label); ?> <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <section class="solution-audiences solutions-shell" aria-labelledby="solution-audiences-title">
            <div class="solution-audiences__heading">
                <p class="eyebrow"><?php echo esc_html($audiences_eyebrow); ?></p>
                <h2 id="solution-audiences-title"><?php echo esc_html($audiences_title); ?></h2>
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

        <?php if (!empty($items)) : ?>
            <section class="solution-outcomes">
                <div class="solutions-shell">
                    <header class="solution-outcomes__heading">
                        <p class="eyebrow"><?php echo esc_html($outcomes_eyebrow); ?></p>
                        <h2><?php echo esc_html($outcomes_title); ?></h2>
                        <p><?php echo esc_html($outcomes_lead); ?></p>
                    </header>
                    <div class="solution-outcomes__grid">
                        <?php foreach ($items as $index => $item) : ?>
                            <article>
                                <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                <p><?php echo esc_html($item); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($metrics)) : ?>
            <section class="solution-metrics">
                <div class="solutions-shell">
                    <p class="eyebrow"><?php echo esc_html($metrics_eyebrow); ?></p>
                    <h2><?php echo esc_html($metrics_title); ?></h2>
                    <div class="solution-metrics__grid">
                        <?php foreach ($metrics as $metric) : ?>
                            <article>
                                <h3><?php echo esc_html($metric['title']); ?></h3>
                                <p><?php echo esc_html($metric['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($steps)) : ?>
            <section id="calisma-modeli" class="solution-process solutions-shell">
                <header>
                    <p class="eyebrow"><?php echo esc_html($process_eyebrow); ?></p>
                    <h2><?php echo esc_html($process_title); ?></h2>
                    <p><?php echo esc_html($process_lead); ?></p>
                </header>
                <div class="solution-process__grid">
                    <?php foreach ($steps as $index => $step) : ?>
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
            'label' => myliba_text('Birlikte geliştiğimiz kurumlar'),
            'title' => myliba_text('Farklı sektörlerden ekiplerin dönüşüm yolculuğuna eşlik ediyoruz.'),
            'text' => myliba_text('Kurumların hedef, kültür ve liderlik pratiklerini birlikte güçlendiren deneyimler tasarlıyoruz.'),
            'class' => 'solution-trust-section',
            'heading_id' => 'solution-trust-title',
        ]); ?>

        <?php if ($editor_content !== '' && $editor_content !== trim(wp_strip_all_tags($summary))) : ?>
            <section class="solution-detail__editor solutions-shell">
                <?php the_content(); ?>
            </section>
        <?php endif; ?>

        <section class="solutions-cta">
            <div class="solutions-cta__inner solutions-shell">
                <div class="solutions-cta__copy">
                    <p class="eyebrow"><?php echo esc_html($cta_eyebrow); ?></p>
                    <h2><?php echo esc_html($cta_title); ?></h2>
                    <p><?php echo esc_html($cta_text); ?></p>
                </div>
                <div class="solutions-cta__actions">
                    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html($cta_button_label); ?></a>
                    <a class="solutions-cta__secondary" href="<?php echo esc_url(myliba_page_url('solutions')); ?>"><?php echo esc_html($cta_secondary_label); ?></a>
                </div>
            </div>
        </section>
    </article>
    <?php
endwhile;

get_footer();
