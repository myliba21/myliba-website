<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    $page_id = get_the_ID();
    $language = myliba_current_language();
    $title = trim((string) get_post_meta($page_id, '_myliba_hero_title', true)) ?: get_the_title();
    $lead = trim((string) get_post_meta($page_id, '_myliba_hero_subtitle', true));
    if ($lead === '') {
        $lead = $language === 'en'
            ? 'Meet the trainers, coaches, and consultants who bring strategy, culture, and high performance into daily work.'
            : 'Stratejiyi, kültürü ve yüksek performansı günlük işin içine taşıyan eğitmen, koç ve danışmanlarımızla tanışın.';
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
    <main class="trainers-page">
        <section class="trainers-hero">
            <div class="solutions-shell">
                <p class="eyebrow"><?php echo esc_html($language === 'en' ? 'The people behind Myliba' : 'Myliba’nın arkasındaki insanlar'); ?></p>
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
                <p class="eyebrow"><?php echo esc_html($language === 'en' ? 'Our team' : 'Ekibimiz'); ?></p>
                <h2 id="trainers-directory-title"><?php echo esc_html($language === 'en' ? 'Learn with experienced practitioners.' : 'Deneyimli uygulayıcılarla gelişin.'); ?></h2>
            </header>

            <?php if ($trainers->have_posts()) : ?>
                <div class="trainer-grid">
                    <?php while ($trainers->have_posts()) : $trainers->the_post(); ?>
                        <?php
                        $trainer_id = get_the_ID();
                        $headline = trim((string) get_post_meta($trainer_id, '_myliba_person_headline', true));
                        $role = trim((string) get_post_meta($trainer_id, '_myliba_person_role', true));
                        $website_url = trim((string) get_post_meta($trainer_id, '_myliba_person_website_url', true));
                        $website_label = trim((string) get_post_meta($trainer_id, '_myliba_person_website_label', true));
                        $linkedin_url = trim((string) get_post_meta($trainer_id, '_myliba_linkedin_url', true));
                        ?>
                        <article class="trainer-card">
                            <div class="trainer-card__media">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                                <?php else : ?>
                                    <span aria-hidden="true"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="trainer-card__content">
                                <h3><?php the_title(); ?></h3>
                                <?php if ($headline !== '') : ?><strong><?php echo esc_html($headline); ?></strong><?php endif; ?>
                                <?php if ($role !== '') : ?><p class="trainer-card__role"><?php echo esc_html($role); ?></p><?php endif; ?>
                                <div class="trainer-card__bio"><?php the_content(); ?></div>
                                <?php if ($website_url !== '' || $linkedin_url !== '') : ?>
                                    <div class="trainer-card__links">
                                        <?php if ($website_url !== '') : ?>
                                            <a href="<?php echo esc_url($website_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($website_label ?: ($language === 'en' ? 'Learn more' : 'Hakkında daha fazlası')); ?> <span aria-hidden="true">↗</span></a>
                                        <?php endif; ?>
                                        <?php if ($linkedin_url !== '') : ?>
                                            <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer">LinkedIn <span aria-hidden="true">↗</span></a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <p class="trainers-empty"><?php echo esc_html($language === 'en' ? 'Trainer profiles will be published soon.' : 'Eğitmen profilleri yakında yayınlanacak.'); ?></p>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </section>
    </main>
    <?php
endwhile;

get_footer();
