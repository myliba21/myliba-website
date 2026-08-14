<?php
get_header();

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $content_copy = static fn (string $key): string => \Myliba\Core\PageContent\text($post_id, 'solution', $key);
    $content_rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($post_id, 'solution', $key);

    $title = $content_copy('hero_title');
    $kicker = $content_copy('kicker');
    $summary = $content_copy('hero_summary');
    $hero_primary_label = $content_copy('hero_primary_label');
    $hero_secondary_label = $content_copy('hero_secondary_label');
    $journey_eyebrow = $content_copy('journey_eyebrow');
    $journey_title = $content_copy('journey_title');
    $principle_1 = $content_copy('principle_1');
    $principle_2 = $content_copy('principle_2');
    $principle_3 = $content_copy('principle_3');
    $intro_eyebrow = $content_copy('intro_eyebrow');
    $intro_title = $content_copy('intro_title');
    $intro = $content_copy('intro');
    $intro_link_label = $content_copy('intro_link_label');
    $audiences_eyebrow = $content_copy('audiences_eyebrow');
    $audiences_title = $content_copy('audiences_title');
    $items = array_column($content_rows('benefits'), 'text');
    $audiences = array_column($content_rows('audiences'), 'text');
    $outcomes_eyebrow = $content_copy('outcomes_eyebrow');
    $outcomes_title = $content_copy('outcomes_title');
    $outcomes_lead = $content_copy('outcomes_lead');
    $metrics_eyebrow = $content_copy('metrics_eyebrow');
    $metrics_title = $content_copy('metrics_title');
    $metrics = $content_rows('metrics');
    $process_eyebrow = $content_copy('process_eyebrow');
    $process_title = $content_copy('process_title');
    $process_lead = $content_copy('process_lead');
    $steps = $content_rows('steps');
    $cta_eyebrow = $content_copy('cta_eyebrow');
    $cta_title = $content_copy('cta_title');
    $cta_text = $content_copy('cta_text');
    $cta_button_label = $content_copy('cta_button_label');
    $cta_secondary_label = $content_copy('cta_secondary_label');
    $trust_label = $content_copy('trust_label');
    $trust_title = $content_copy('trust_title');
    $trust_text = $content_copy('trust_text');

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
            'label' => $trust_label,
            'title' => $trust_title,
            'text' => $trust_text,
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
