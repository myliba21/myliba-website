<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    $page_id = get_the_ID();
    $language = myliba_current_language();
    $copy = static function (string $key, string $fallback) use ($page_id): string {
        $value = trim((string) get_post_meta($page_id, $key, true));
        return $value !== '' ? $value : $fallback;
    };
    $title = trim((string) get_post_meta($page_id, '_myliba_hero_title', true)) ?: get_the_title();
    $lead = trim((string) get_post_meta($page_id, '_myliba_hero_subtitle', true));
    if ($lead === '') {
        $lead = $language === 'en'
            ? 'Meet Myliba consultants, coaches, and trainers specializing in OKRs, performance management, leadership, goal systems, and cultural transformation.'
            : 'Myliba’nın OKR, performans yönetimi, hedef sistemleri, liderlik ve kültürel dönüşüm alanlarında çalışan danışman, koç ve eğitmenleriyle tanışın.';
    }

    $trainers = new WP_Query([
        'post_type' => 'myliba_team',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => [
            'language' => ['key' => '_myliba_language', 'value' => $language],
            'sort_order' => ['key' => '_myliba_order', 'compare' => 'EXISTS', 'type' => 'NUMERIC'],
        ],
        'orderby' => ['sort_order' => 'ASC', 'title' => 'ASC'],
        'order' => 'ASC',
    ]);
    ?>
    <div class="trainers-page">
        <section class="trainers-hero">
            <div class="solutions-shell">
                <p class="eyebrow"><?php echo esc_html($copy('_myliba_eyebrow', $language === 'en' ? 'OKR & performance experts' : 'OKR ve performans uzmanları')); ?></p>
                <h1><?php echo esc_html($title); ?></h1>
                <p><?php echo esc_html($lead); ?></p>
            </div>
        </section>

        <?php if (trim((string) get_the_content()) !== '') : ?>
            <section class="trainers-intro solutions-shell">
                <?php the_content(); ?>
            </section>
        <?php endif; ?>

        <section class="trainers-directory solutions-shell" aria-labelledby="trainers-directory-title">
            <header>
                <p class="eyebrow"><?php echo esc_html($copy('_myliba_trainers_directory_eyebrow', $language === 'en' ? 'Our team' : 'Ekibimiz')); ?></p>
                <h2 id="trainers-directory-title"><?php echo esc_html($copy('_myliba_trainers_directory_title', $language === 'en' ? 'Meet our experienced OKR and performance consultants.' : 'Deneyimli OKR ve performans danışmanlarımızla tanışın.')); ?></h2>
            </header>

            <?php if ($trainers->have_posts()) : ?>
                <div class="trainer-grid">
                    <?php while ($trainers->have_posts()) : $trainers->the_post(); ?>
                        <?php
                        $trainer_id = get_the_ID();
                        $headline = trim((string) get_post_meta($trainer_id, '_myliba_person_headline', true));
                        $role = trim((string) get_post_meta($trainer_id, '_myliba_person_role', true));
                        $role_parts = array_values(array_filter(array_map('trim', preg_split('/\s*·\s*/u', $role) ?: [])));
                        $summary = myliba_excerpt($trainer_id, 34);
                        $profile_url = get_permalink();
                        ?>
                        <article class="trainer-card">
                            <a class="trainer-card__media" href="<?php echo esc_url($profile_url); ?>" aria-label="<?php echo esc_attr(str_replace('{name}', get_the_title(), $copy('_myliba_trainers_card_aria_template', $language === 'en' ? 'View {name} profile' : '{name} profilini inceleyin'))); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                                <?php else : ?>
                                    <span aria-hidden="true"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
                                <?php endif; ?>
                                <span class="trainer-card__media-action" aria-hidden="true"><?php echo esc_html($copy('_myliba_trainers_card_overlay_label', $language === 'en' ? 'View profile' : 'Profili incele')); ?> →</span>
                            </a>
                            <div class="trainer-card__content">
                                <p class="trainer-card__kicker"><?php echo esc_html($copy('_myliba_trainers_card_kicker', $language === 'en' ? 'Trainer & Consultant' : 'Eğitmen & Danışman')); ?></p>
                                <h3><a href="<?php echo esc_url($profile_url); ?>"><?php the_title(); ?></a></h3>
                                <?php if ($headline !== '') : ?><strong><?php echo esc_html($headline); ?></strong><?php endif; ?>
                                <?php if (!empty($role_parts)) : ?>
                                    <ul class="trainer-card__skills" aria-label="<?php echo esc_attr($copy('_myliba_trainers_skills_label', $language === 'en' ? 'Areas of expertise' : 'Uzmanlık alanları')); ?>">
                                        <?php foreach (array_slice($role_parts, 0, 3) as $role_part) : ?><li><?php echo esc_html($role_part); ?></li><?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <?php if ($summary !== '') : ?><p class="trainer-card__summary"><?php echo esc_html($summary); ?></p><?php endif; ?>
                                <a class="trainer-card__detail-link" href="<?php echo esc_url($profile_url); ?>"><?php echo esc_html($copy('_myliba_trainers_card_detail_label', $language === 'en' ? 'View profile' : 'Detaylı profili incele')); ?> <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <p class="trainers-empty"><?php echo esc_html($copy('_myliba_trainers_empty_text', $language === 'en' ? 'Trainer profiles will be published soon.' : 'Eğitmen profilleri yakında yayınlanacak.')); ?></p>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </section>
    </div>
    <?php
endwhile;

get_footer();
