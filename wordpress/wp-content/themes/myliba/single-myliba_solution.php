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
    ?>
    <article class="solution-detail">
        <section class="solution-detail__hero">
            <div class="solutions-shell">
                <a class="solution-detail__back" href="<?php echo esc_url(myliba_page_url('solutions')); ?>">← Tüm çözümler</a>
                <p class="eyebrow"><?php echo esc_html($solution['kicker']); ?></p>
                <h1><?php echo esc_html($title); ?></h1>
                <p class="solution-detail__lead"><?php echo esc_html($summary); ?></p>
                <a class="myliba-button myliba-button--primary" href="#cozum-detaylari">Çözümü keşfedin</a>
            </div>
        </section>

        <section id="cozum-detaylari" class="solution-detail__intro solutions-shell">
            <div>
                <p class="eyebrow">Myliba yaklaşımı</p>
                <h2>Kültürü, hedefleri ve iş sonuçlarını birlikte geliştirin.</h2>
            </div>
            <div>
                <p><?php echo esc_html($solution['intro']); ?></p>
                <?php if (!empty($solution['items'])) : ?>
                    <ul class="solution-detail__list">
                        <?php foreach ($solution['items'] as $item) : ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

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
            <section class="solution-process solutions-shell">
                <header>
                    <p class="eyebrow">Çalışma modeli</p>
                    <h2><?php echo esc_html($solution['title']); ?> süreci</h2>
                </header>
                <div class="solution-process__grid">
                    <?php foreach ($solution['steps'] as $index => $step) : ?>
                        <article>
                            <span><?php echo esc_html((string) ($index + 1)); ?></span>
                            <h3><?php echo esc_html($step['title']); ?></h3>
                            <p><?php echo esc_html($step['text']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($editor_content !== '' && $editor_content !== trim(wp_strip_all_tags($summary))) : ?>
            <section class="solution-detail__editor solutions-shell">
                <?php the_content(); ?>
            </section>
        <?php endif; ?>

        <section class="solutions-cta solutions-shell">
            <div>
                <p class="eyebrow">Birlikte belirleyelim</p>
                <h2>Hangi çözüm size uygun?</h2>
                <p>İhtiyacınıza en uygun programı veya danışmanlık modelini bulmak için Myliba ile tanışın.</p>
            </div>
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>">Uzmanlarımızla görüşün</a>
        </section>
    </article>
    <?php
endwhile;

get_footer();
