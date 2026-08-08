<?php
get_header();

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $content_copy = static fn (string $key): string => \Myliba\Core\PageContent\text($post_id, 'report', $key);
    $content_rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($post_id, 'report', $key);

    $title = $content_copy('hero_title');
    if ($title === '') {
        $title = (string) (get_post_meta($post_id, '_myliba_hero_title', true) ?: get_the_title());
    }

    $kicker = $content_copy('kicker');
    if ($kicker === '') {
        $kicker = (string) (get_post_meta($post_id, '_myliba_eyebrow', true) ?: get_post_meta($post_id, '_myliba_label', true) ?: myliba_text('Raporlar ve Trendler'));
    }

    $summary = $content_copy('hero_summary');
    if ($summary === '') {
        $summary = (string) (get_post_meta($post_id, '_myliba_hero_subtitle', true) ?: get_the_excerpt());
    }

    $read_time = $content_copy('read_time');
    if ($read_time === '') {
        $read_time = myliba_reading_time() . ' ' . myliba_text('min read');
    }

    $primary_cta_label = $content_copy('primary_cta_label');
    if ($primary_cta_label === '') {
        $primary_cta_label = (string) (get_post_meta($post_id, '_myliba_cta_label', true) ?: myliba_text('Raporu İnceleyin'));
    }

    $primary_cta_url = $content_copy('primary_cta_url');
    if ($primary_cta_url === '') {
        $primary_cta_url = (string) (get_post_meta($post_id, '_myliba_cta_url', true) ?: '#arastirma-detayi');
    }

    $overview_eyebrow = $content_copy('overview_eyebrow') ?: myliba_text('Yönetici Özeti');
    $overview_title = $content_copy('overview_title') ?: myliba_text('Araştırmanın Amacı ve Kapsamı');
    $overview_text = $content_copy('overview_text');
    if ($overview_text === '') {
        $overview_text = (string) (get_post_meta($post_id, '_myliba_problem', true) ?: '');
    }

    $findings_eyebrow = $content_copy('findings_eyebrow') ?: myliba_text('Veri ve Trendler');
    $findings_title = $content_copy('findings_title') ?: myliba_text('Öne Çıkan Bulgular');
    $findings_lead = $content_copy('findings_lead');
    if ($findings_lead === '') {
        $findings_lead = (string) (get_post_meta($post_id, '_myliba_solution', true) ?: '');
    }

    $key_insights = $content_rows('key_insights');

    $takeaways_eyebrow = $content_copy('takeaways_eyebrow') ?: myliba_text('Uygulama Adımları');
    $takeaways_title = $content_copy('takeaways_title') ?: myliba_text('Kurumlar İçin Stratejik Öneriler');
    $takeaways_lead = $content_copy('takeaways_lead');
    $takeaways_list = $content_rows('takeaways_list');
    if (empty($takeaways_list)) {
        $meta_benefits = function_exists('myliba_lines') ? \myliba_lines((string) get_post_meta($post_id, '_myliba_benefits', true)) : [];
        if (!empty($meta_benefits)) {
            foreach ($meta_benefits as $b) {
                $takeaways_list[] = ['title' => $b, 'text' => ''];
            }
        }
    }

    $faqs = $content_rows('faqs');
    if (empty($faqs)) {
        $meta_faqs = function_exists('myliba_faq_pairs') ? \myliba_faq_pairs((string) get_post_meta($post_id, '_myliba_faq_items', true)) : [];
        if (!empty($meta_faqs)) {
            $faqs = $meta_faqs;
        }
    }

    $cta_eyebrow = $content_copy('cta_eyebrow') ?: myliba_text('Dönüşüm Yolculuğu');
    $cta_title = $content_copy('cta_title') ?: myliba_text('Bu İçgörüleri Kurumunuzda Hayata Geçirin.');
    $cta_text = $content_copy('cta_text') ?: myliba_text('Kurumunuza özel yüksek performans kültürü modelini birlikte tasarlayalım.');
    $cta_button_label = $content_copy('cta_button_label');
    if ($cta_button_label === '') {
        $cta_button_label = (string) (get_post_meta($post_id, '_myliba_cta_label', true) ?: myliba_text('İletişime Geçin'));
    }
    $cta_button_url = $content_copy('cta_button_url');
    if ($cta_button_url === '') {
        $cta_button_url = (string) (get_post_meta($post_id, '_myliba_cta_url', true) ?: myliba_page_url('contact'));
    }

    $journey_eyebrow = $content_copy('journey_eyebrow');
    if ($journey_eyebrow === '') {
        $journey_eyebrow = (string) (get_post_meta($post_id, '_myliba_journey_eyebrow', true) ?: myliba_text('Myliba Trend Radarı'));
    }

    $journey_title = $content_copy('journey_title');
    if ($journey_title === '') {
        $journey_title = (string) (get_post_meta($post_id, '_myliba_journey_title', true) ?: myliba_text("Veriye dayalı.\nStratejik.\nUygulanabilir."));
    }

    $principle_1 = $content_copy('principle_1');
    if ($principle_1 === '') {
        $principle_1 = (string) (get_post_meta($post_id, '_myliba_principle_1', true) ?: myliba_text('Güncel Saha Verisi'));
    }

    $principle_2 = $content_copy('principle_2');
    if ($principle_2 === '') {
        $principle_2 = (string) (get_post_meta($post_id, '_myliba_principle_2', true) ?: myliba_text('Stratejik İçgörüler'));
    }

    $principle_3 = $content_copy('principle_3');
    if ($principle_3 === '') {
        $principle_3 = (string) (get_post_meta($post_id, '_myliba_principle_3', true) ?: myliba_text('Uygulanabilir Çıkarımlar'));
    }

    $reports_archive_url = home_url(myliba_current_language() === 'en' ? '/en/development-center/reports/' : '/tr/gelisim-merkezi/raporlar-ve-trendler/');
    $editor_content = trim(wp_strip_all_tags((string) get_the_content()));
    ?>
    <article class="report-detail solution-detail">
        <section class="solution-detail__hero">
            <div class="solutions-shell">
                <div class="solution-detail__hero-grid">
                    <div class="solution-detail__hero-copy">
                        <a class="solution-detail__back" href="<?php echo esc_url($reports_archive_url); ?>">← <?php echo esc_html(myliba_text('Tüm Raporlar ve Trendler')); ?></a>
                        <p class="eyebrow"><?php echo esc_html($kicker); ?></p>
                        <h1><?php echo esc_html($title); ?></h1>
                        <p class="solution-detail__lead"><?php echo esc_html($summary); ?></p>
                        <div class="solution-detail__actions">
                            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($primary_cta_url); ?>"><?php echo esc_html($primary_cta_label); ?></a>
                            <span class="report-meta-badge" style="display:inline-flex;align-items:center;gap:6px;font-size:14px;color:rgba(255,255,255,0.85);margin-left:12px;">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time> &middot; <?php echo esc_html($read_time); ?>
                            </span>
                        </div>
                    </div>

                    <div class="solution-journey" aria-hidden="true">
                        <div class="solution-journey__topline">
                            <span><?php echo esc_html($journey_eyebrow); ?></span>
                            <i></i>
                        </div>
                        <strong><?php echo wp_kses_post(nl2br(esc_html($journey_title))); ?></strong>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="report-featured-preview" style="margin-top:16px;border-radius:10px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.2);">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="solution-detail__principles" aria-label="<?php echo esc_attr(myliba_text('Rapor özellikleri')); ?>">
                    <span><b>01</b> <?php echo esc_html($principle_1); ?></span>
                    <span><b>02</b> <?php echo esc_html($principle_2); ?></span>
                    <span><b>03</b> <?php echo esc_html($principle_3); ?></span>
                </div>
            </div>
        </section>

        <?php if ($overview_text !== '') : ?>
            <section id="arastirma-detayi" class="solution-detail__intro solutions-shell">
                <div>
                    <p class="eyebrow"><?php echo esc_html($overview_eyebrow); ?></p>
                    <h2><?php echo esc_html($overview_title); ?></h2>
                </div>
                <div class="solution-detail__intro-copy">
                    <p><?php echo esc_html($overview_text); ?></p>
                    <a href="<?php echo esc_url($cta_button_url); ?>"><?php echo esc_html(myliba_text('Rapor hakkında uzmanlarımızla görüşün')); ?> <span aria-hidden="true">→</span></a>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($key_insights)) : ?>
            <section class="solution-metrics">
                <div class="solutions-shell">
                    <p class="eyebrow"><?php echo esc_html($findings_eyebrow); ?></p>
                    <h2><?php echo esc_html($findings_title); ?></h2>
                    <?php if ($findings_lead !== '') : ?>
                        <p style="margin-bottom:28px;color:#4b5563;font-size:17px;"><?php echo esc_html($findings_lead); ?></p>
                    <?php endif; ?>
                    <div class="solution-metrics__grid">
                        <?php foreach ($key_insights as $insight) : ?>
                            <article>
                                <?php if (!empty($insight['stat'])) : ?>
                                    <span style="font-size:32px;font-weight:800;color:var(--color-primary,#0e7490);display:block;margin-bottom:8px;"><?php echo esc_html($insight['stat']); ?></span>
                                <?php endif; ?>
                                <h3><?php echo esc_html($insight['title']); ?></h3>
                                <p><?php echo esc_html($insight['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($takeaways_list)) : ?>
            <section class="solution-outcomes">
                <div class="solutions-shell">
                    <header class="solution-outcomes__heading">
                        <p class="eyebrow"><?php echo esc_html($takeaways_eyebrow); ?></p>
                        <h2><?php echo esc_html($takeaways_title); ?></h2>
                        <?php if ($takeaways_lead !== '') : ?>
                            <p><?php echo esc_html($takeaways_lead); ?></p>
                        <?php endif; ?>
                    </header>
                    <div class="solution-outcomes__grid">
                        <?php foreach ($takeaways_list as $index => $item) : ?>
                            <article>
                                <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                <?php if (!empty($item['title'])) : ?>
                                    <strong><?php echo esc_html($item['title']); ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($item['text'])) : ?>
                                    <p><?php echo esc_html($item['text']); ?></p>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($editor_content !== '' && $editor_content !== trim(wp_strip_all_tags($summary)) && $editor_content !== trim(wp_strip_all_tags($overview_text))) : ?>
            <section class="solution-detail__editor solutions-shell">
                <article class="content">
                    <?php the_content(); ?>
                </article>
            </section>
        <?php endif; ?>

        <?php if (!empty($faqs)) : ?>
            <section class="section solutions-shell" style="padding:48px 0;">
                <div class="section__heading" style="margin-bottom:32px;">
                    <p class="eyebrow"><?php echo esc_html(myliba_text('SSS')); ?></p>
                    <h2><?php echo esc_html(myliba_text('Sıkça Sorulan Sorular')); ?></h2>
                </div>
                <div class="card-grid card-grid--two">
                    <?php foreach ($faqs as $faq) : ?>
                        <article class="faq-card" style="background:#fff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                            <h3 style="font-size:18px;font-weight:700;margin-bottom:10px;"><?php echo esc_html($faq['question']); ?></h3>
                            <p style="color:#4b5563;line-height:1.6;margin:0;"><?php echo esc_html($faq['answer']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
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
                    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($cta_button_url); ?>"><?php echo esc_html($cta_button_label); ?></a>
                    <a class="solutions-cta__secondary" href="<?php echo esc_url($reports_archive_url); ?>"><?php echo esc_html(myliba_text('Tüm raporları görün')); ?></a>
                </div>
            </div>
        </section>

        <?php
        $related = new WP_Query([
            'post_type' => 'myliba_report',
            'posts_per_page' => 3,
            'post__not_in' => [get_the_ID()],
            'post_status' => 'publish',
        ]);
        if ($related->have_posts()) :
            ?>
            <section class="section solutions-shell" style="padding:48px 0 64px;">
                <div class="section__heading" style="margin-bottom:28px;">
                    <p class="eyebrow"><?php echo esc_html(myliba_text('Diğer Araştırmalar')); ?></p>
                    <h2><?php echo esc_html(myliba_text('İlginizi Çekebilecek Rapor ve Trendler')); ?></h2>
                </div>
                <div class="development-resource-list__grid">
                    <?php while ($related->have_posts()) : $related->the_post(); ?>
                        <a class="development-resource-card" href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="development-resource-card__image">
                                    <?php the_post_thumbnail('large'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="development-resource-card__meta">
                                <span><?php echo esc_html(myliba_text('Rapor')); ?></span>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                            </div>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 20)); ?></p>
                            <strong><?php echo esc_html(myliba_text('Raporu inceleyin')); ?> <span aria-hidden="true">→</span></strong>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>
    </article>
    <?php
endwhile;

get_footer();
