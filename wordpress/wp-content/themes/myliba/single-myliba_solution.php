<?php
get_header();

while (have_posts()):
    the_post();
    $post_id = get_the_ID();
    $content_copy = static fn(string $key): string => \Myliba\Core\PageContent\text($post_id, 'solution', $key);
    $content_rows = static fn(string $key): array => \Myliba\Core\PageContent\collection($post_id, 'solution', $key);

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

    $hero_supporting = $content_copy('hero_supporting');

    $hero_primary_label = $content_copy('hero_primary_label');
    if ($hero_primary_label === '') {
        $hero_primary_label = (string) (get_post_meta($post_id, '_myliba_cta_label', true) ?: myliba_text('Programı birlikte tasarlayalım'));
    }

    $hero_secondary_label = $content_copy('hero_secondary_label');
    if ($hero_secondary_label === '') {
        $hero_secondary_label = myliba_text('Çalışma modelini inceleyin');
    }

    $hero_image_id = absint($content_copy('hero_image') ?: get_post_meta($post_id, '_myliba_hero_image', true));
    $hero_image_alt = $content_copy('hero_image_alt');
    if ($hero_image_alt === '') {
        $hero_image_alt = (string) (get_post_meta($post_id, '_myliba_hero_image_alt', true) ?: '');
    }

    $journey_eyebrow = $content_copy('journey_eyebrow');
    if ($journey_eyebrow === '') {
        $journey_eyebrow = (string) (get_post_meta($post_id, '_myliba_journey_eyebrow', true) ?: myliba_text('Myliba gelişim yolculuğu'));
    }

    $journey_title = $content_copy('journey_title');
    if ($journey_title === '') {
        $journey_title = (string) (get_post_meta($post_id, '_myliba_journey_title', true) ?: myliba_text("Kuruma özel.\nİşin içinde.\nÖlçülebilir."));
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
    $intro_points = array_column($content_rows('intro_points'), 'text');
    $principles = $content_rows('principles');
    $results = array_column($content_rows('results'), 'text');

    $principles_eyebrow = $content_copy('principles_eyebrow');
    $principles_title = $content_copy('principles_title');
    $principles_lead = $content_copy('principles_lead');
    $results_eyebrow = $content_copy('results_eyebrow');
    $results_title = $content_copy('results_title');
    $results_lead = $content_copy('results_lead');
    $experts_eyebrow = $content_copy('experts_eyebrow');
    $experts_title = $content_copy('experts_title');
    $experts_lead = $content_copy('experts_lead');
    $experts_button_label = $content_copy('experts_button_label');
    $thought_eyebrow = $content_copy('thought_eyebrow');
    $thought_title = $content_copy('thought_title');
    $thought_text = $content_copy('thought_text');
    $book_title = $content_copy('book_title');
    $book_subtitle = $content_copy('book_subtitle');
    $book_quote_one = $content_copy('book_quote_one');
    $book_quote_one_author = $content_copy('book_quote_one_author');
    $book_quote_two = $content_copy('book_quote_two');
    $book_quote_two_author = $content_copy('book_quote_two_author');
    $book_link_label = $content_copy('book_link_label');
    $book_link_url = $content_copy('book_link_url');
    $is_consulting = get_post_field('post_name', $post_id) === 'danismanlik';

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

    $cta_button_url = $content_copy('cta_button_url');
    if ($cta_button_url === '') {
        $cta_button_url = (string) (get_post_meta($post_id, '_myliba_cta_url', true) ?: myliba_page_url('contact'));
    }

    $cta_secondary_label = $content_copy('cta_secondary_label');
    if ($cta_secondary_label === '') {
        $cta_secondary_label = myliba_text('Tüm çözümleri görün');
    }

    $cta_secondary_url = $content_copy('cta_secondary_url');
    if ($cta_secondary_url === '') {
        $cta_secondary_url = myliba_page_url('solutions');
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
                        <a class="solution-detail__back" href="<?php echo esc_url(myliba_page_url('solutions')); ?>">←
                            <?php echo esc_html(myliba_text('Tüm çözümler')); ?></a>
                        <p class="eyebrow"><?php echo esc_html($kicker); ?></p>
                        <h1><?php echo esc_html($title); ?></h1>
                        <p class="solution-detail__lead"><?php echo esc_html($summary); ?></p>
                        <?php if ($hero_supporting !== '') : ?>
                            <p class="solution-detail__supporting"><?php echo esc_html($hero_supporting); ?></p>
                        <?php endif; ?>
                        <div class="solution-detail__actions">
                            <a class="myliba-button myliba-button--primary"
                                href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html($hero_primary_label); ?></a>
                            <?php if (!empty($steps)): ?>
                                <a class="solution-detail__text-link"
                                    href="<?php echo esc_attr($is_consulting ? '#danismanlarimiz' : '#calisma-modeli'); ?>"><?php echo esc_html($hero_secondary_label); ?> <span
                                        aria-hidden="true">↓</span></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $show_hero_image = $hero_image_id > 0;
                    if ($show_hero_image): ?>
                        <div class="solution-detail__hero-visual-wrap">
                            <div class="solution-detail__hero-visual">
                                <?php echo wp_get_attachment_image($hero_image_id, 'full', false, [
                                    'alt' => $hero_image_alt ?: get_post_meta($hero_image_id, '_wp_attachment_image_alt', true) ?: $title,
                                    'class' => 'solution-detail__hero-image',
                                    'loading' => 'eager',
                                    'fetchpriority' => 'high',
                                    'decoding' => 'async',
                                ]); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="solution-journey" aria-hidden="true">
                            <div class="solution-journey__topline">
                                <span><?php echo esc_html($journey_eyebrow); ?></span>
                                <i></i>
                            </div>
                            <strong><?php echo wp_kses_post(nl2br(esc_html($journey_title))); ?></strong>
                            <?php if (!empty($steps)): ?>
                                <div class="solution-journey__steps">
                                    <?php foreach (array_slice($steps, 0, 3) as $index => $step): ?>
                                        <span><b><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo esc_html($step['title']); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
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
                <?php if (!empty($intro_points)) : ?>
                    <ul class="solution-detail__intro-points">
                        <?php foreach ($intro_points as $point) : ?><li><?php echo esc_html($point); ?></li><?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <a href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html($intro_link_label); ?>
                    <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <?php if (!empty($principles)) : ?>
            <section class="consulting-principles">
                <div class="solutions-shell">
                    <header>
                        <?php if ($principles_eyebrow !== '') : ?><p class="eyebrow"><?php echo esc_html($principles_eyebrow); ?></p><?php endif; ?>
                        <h2><?php echo esc_html($principles_title); ?></h2>
                        <?php if ($principles_lead !== '') : ?><p><?php echo esc_html($principles_lead); ?></p><?php endif; ?>
                    </header>
                    <div class="consulting-principles__grid">
                        <?php foreach ($principles as $index => $principle) : ?>
                            <article>
                                <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                <h3><?php echo esc_html((string) ($principle['title'] ?? '')); ?></h3>
                                <p><?php echo esc_html((string) ($principle['text'] ?? '')); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
            <section class="solution-outcomes">
                <div class="solutions-shell">
                    <header class="solution-outcomes__heading">
                        <p class="eyebrow"><?php echo esc_html($outcomes_eyebrow); ?></p>
                        <h2><?php echo esc_html($outcomes_title); ?></h2>
                        <p><?php echo esc_html($outcomes_lead); ?></p>
                    </header>
                    <div class="solution-outcomes__grid">
                        <?php foreach ($items as $index => $item): ?>
                            <article>
                                <span
                                    aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                <p><?php echo esc_html($item); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($metrics)): ?>
            <section class="solution-metrics">
                <div class="solutions-shell">
                    <p class="eyebrow"><?php echo esc_html($metrics_eyebrow); ?></p>
                    <h2><?php echo esc_html($metrics_title); ?></h2>
                    <div class="solution-metrics__grid">
                        <?php foreach ($metrics as $index => $metric): ?>
                            <article>
                                <div class="solution-metrics__card-top">
                                    <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                    <i aria-hidden="true"><b></b><b></b><b></b></i>
                                </div>
                                <h3><?php echo esc_html($metric['title']); ?></h3>
                                <p><?php echo esc_html($metric['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($steps)): ?>
            <section id="calisma-modeli" class="solution-process solutions-shell">
                <header>
                    <p class="eyebrow"><?php echo esc_html($process_eyebrow); ?></p>
                    <h2><?php echo esc_html($process_title); ?></h2>
                    <p><?php echo esc_html($process_lead); ?></p>
                </header>
                <div class="solution-process__grid">
                    <?php foreach ($steps as $index => $step): ?>
                        <article>
                            <div class="solution-process__card-header">
                                <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                <h3><?php echo esc_html($step['title']); ?></h3>
                            </div>
                            <?php
                            $step_items = function_exists('myliba_parse_bullet_items')
                                ? myliba_parse_bullet_items($step['text'] ?? '')
                                : array_filter(array_map('trim', explode("\n", (string) ($step['text'] ?? ''))));
                            if (!empty($step_items)):
                            ?>
                                <ul class="solution-process__list">
                                    <?php foreach ($step_items as $item): ?>
                                        <li><?php echo esc_html($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($results)) : ?>
            <section class="consulting-results">
                <div class="solutions-shell">
                    <header>
                        <?php if ($results_eyebrow !== '') : ?><p class="eyebrow"><?php echo esc_html($results_eyebrow); ?></p><?php endif; ?>
                        <h2><?php echo esc_html($results_title); ?></h2>
                        <?php if ($results_lead !== '') : ?><p><?php echo esc_html($results_lead); ?></p><?php endif; ?>
                    </header>
                    <ul>
                        <?php foreach ($results as $result) : ?><li><span aria-hidden="true">✓</span><?php echo esc_html($result); ?></li><?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($is_consulting) : ?>
            <?php
            $consultants = new WP_Query([
                'post_type' => 'myliba_team',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'meta_query' => [
                    'language' => ['key' => '_myliba_language', 'value' => myliba_current_language()],
                    'sort_order' => ['key' => '_myliba_order', 'compare' => 'EXISTS', 'type' => 'NUMERIC'],
                ],
                'orderby' => ['sort_order' => 'ASC', 'title' => 'ASC'],
                'order' => 'ASC',
            ]);
            ?>
            <?php if ($consultants->have_posts()) : ?>
                <section id="danismanlarimiz" class="consulting-experts solutions-shell">
                    <header>
                        <?php if ($experts_eyebrow !== '') : ?><p class="eyebrow"><?php echo esc_html($experts_eyebrow); ?></p><?php endif; ?>
                        <h2><?php echo esc_html($experts_title); ?></h2>
                        <?php if ($experts_lead !== '') : ?><p><?php echo esc_html($experts_lead); ?></p><?php endif; ?>
                    </header>
                    <div class="consulting-experts__grid">
                        <?php while ($consultants->have_posts()) : $consultants->the_post(); ?>
                            <?php
                            $consultant_id = get_the_ID();
                            $headline = trim((string) get_post_meta($consultant_id, '_myliba_person_headline', true));
                            $role = trim((string) get_post_meta($consultant_id, '_myliba_person_role', true));
                            $consultant_url = get_permalink();
                            ?>
                            <article>
                                <a class="consulting-experts__photo" href="<?php echo esc_url($consultant_url); ?>" aria-label="<?php echo esc_attr(sprintf('%s profilini inceleyin', get_the_title())); ?>">
                                    <?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large', ['loading' => 'lazy', 'decoding' => 'async']); else : ?>
                                        <span aria-hidden="true"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
                                    <?php endif; ?>
                                </a>
                                <h3><a href="<?php echo esc_url($consultant_url); ?>"><?php the_title(); ?></a></h3>
                                <?php if ($headline !== '') : ?><strong><?php echo esc_html($headline); ?></strong><?php endif; ?>
                                <?php if ($role !== '') : ?><p><?php echo esc_html($role); ?></p><?php endif; ?>
                                <a class="consulting-experts__detail" href="<?php echo esc_url($consultant_url); ?>">Detaylı profil <span aria-hidden="true">→</span></a>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(myliba_page_url('trainers')); ?>"><?php echo esc_html($experts_button_label ?: 'Tüm Eğitmen ve Danışmanlarımız'); ?></a>
                </section>
            <?php endif; wp_reset_postdata(); ?>

            <?php if ($thought_title !== '' || $book_title !== '') : ?>
                <section class="consulting-thought">
                    <div class="solutions-shell consulting-thought__grid">
                        <div>
                            <?php if ($thought_eyebrow !== '') : ?><p class="eyebrow"><?php echo esc_html($thought_eyebrow); ?></p><?php endif; ?>
                            <h2><?php echo esc_html($thought_title); ?></h2>
                            <p><?php echo esc_html($thought_text); ?></p>
                            <?php if ($book_link_url !== '' && $book_link_label !== '') : ?><a href="<?php echo esc_url($book_link_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($book_link_label); ?> <span aria-hidden="true">↗</span></a><?php endif; ?>
                        </div>
                        <article>
                            <h3><?php echo esc_html($book_title); ?></h3>
                            <?php if ($book_subtitle !== '') : ?><strong><?php echo esc_html($book_subtitle); ?></strong><?php endif; ?>
                            <?php if ($book_quote_one !== '') : ?><blockquote><p>“<?php echo esc_html($book_quote_one); ?>”</p><cite>— <?php echo esc_html($book_quote_one_author); ?></cite></blockquote><?php endif; ?>
                            <?php if ($book_quote_two !== '') : ?><blockquote><p>“<?php echo esc_html($book_quote_two); ?>”</p><cite>— <?php echo esc_html($book_quote_two_author); ?></cite></blockquote><?php endif; ?>
                        </article>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>

        <?php get_template_part('template-parts/client-logo-marquee', null, [
            'label' => myliba_text('Birlikte geliştiğimiz kurumlar'),
            'title' => myliba_text('Farklı sektörlerden ekiplerin dönüşüm yolculuğuna eşlik ediyoruz.'),
            'text' => myliba_text('Kurumların hedef, kültür ve liderlik pratiklerini birlikte güçlendiren deneyimler tasarlıyoruz.'),
            'class' => 'solution-trust-section',
            'heading_id' => 'solution-trust-title',
        ]); ?>

        <?php if ($editor_content !== '' && $editor_content !== trim(wp_strip_all_tags($summary))): ?>
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
                    <a class="myliba-button myliba-button--primary"
                        href="<?php echo esc_url($cta_button_url); ?>"><?php echo esc_html($cta_button_label); ?></a>
                    <a class="solutions-cta__secondary"
                        href="<?php echo esc_url($cta_secondary_url); ?>"><?php echo esc_html($cta_secondary_label); ?></a>
                </div>
            </div>
        </section>
    </article>
    <?php
endwhile;

get_footer();
