<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    $person_id = get_the_ID();
    $language = (string) (get_post_meta($person_id, '_myliba_language', true) ?: myliba_current_language());
    $trainers_page = get_page_by_path($language === 'en' ? 'en/our-trainers' : 'tr/egitmenlerimiz');
    $trainers_page_id = $trainers_page instanceof WP_Post ? (int) $trainers_page->ID : 0;
    $copy = static function (string $key, string $fallback) use ($trainers_page_id): string {
        $value = $trainers_page_id ? trim((string) get_post_meta($trainers_page_id, $key, true)) : '';
        return $value !== '' ? $value : $fallback;
    };
    $headline = trim((string) get_post_meta($person_id, '_myliba_person_headline', true));
    $role = trim((string) get_post_meta($person_id, '_myliba_person_role', true));
    $website_url = trim((string) get_post_meta($person_id, '_myliba_person_website_url', true));
    $website_label = trim((string) get_post_meta($person_id, '_myliba_person_website_label', true));
    $social_profiles = [];
    foreach (myliba_social_platforms() as $social_key => $social_platform) {
        $social_url = trim((string) get_post_meta($person_id, '_myliba_' . $social_key . '_url', true));
        if ($social_url !== '') {
            $social_profiles[] = [
                'key' => $social_key,
                'label' => $social_platform['label'],
                'url' => $social_url,
                'svg' => $social_platform['svg'],
            ];
        }
    }
    $role_parts = array_values(array_filter(array_map('trim', preg_split('/\s*·\s*/u', $role) ?: [])));
    $back_url = myliba_page_url('trainers');
    ?>
    <main class="trainer-profile">
        <section class="trainer-profile__hero">
            <div class="solutions-shell">
                <a class="trainer-profile__back" href="<?php echo esc_url($back_url); ?>">← <?php echo esc_html($copy('_myliba_trainers_profile_back_label', $language === 'en' ? 'All trainers' : 'Tüm eğitmenler')); ?></a>
                <div class="trainer-profile__card">
                    <div class="trainer-profile__media">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', ['loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async']); ?>
                        <?php else : ?>
                            <span aria-hidden="true"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="trainer-profile__content">
                        <header class="trainer-profile__intro">
                            <p class="eyebrow"><?php echo esc_html($copy('_myliba_trainers_profile_kicker', $language === 'en' ? 'Trainer & Consultant' : 'Eğitmen & Danışman')); ?></p>
                            <h1><?php the_title(); ?></h1>
                            <?php if ($headline !== '') : ?><p class="trainer-profile__headline"><?php echo esc_html($headline); ?></p><?php endif; ?>
                            <?php if (!empty($role_parts)) : ?>
                                <ul class="trainer-profile__skills" aria-label="<?php echo esc_attr($copy('_myliba_trainers_skills_label', $language === 'en' ? 'Areas of expertise' : 'Uzmanlık alanları')); ?>">
                                    <?php foreach ($role_parts as $role_part) : ?><li><?php echo esc_html($role_part); ?></li><?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </header>
                        <section class="trainer-profile__about" aria-labelledby="trainer-profile-about">
                            <p class="eyebrow"><?php echo esc_html($copy('_myliba_trainers_profile_about_eyebrow', $language === 'en' ? 'About' : 'Hakkında')); ?></p>
                            <h2 id="trainer-profile-about"><?php echo esc_html(str_replace('{name}', get_the_title(), $copy('_myliba_trainers_profile_about_title', $language === 'en' ? 'About {name}' : '{name} hakkında'))); ?></h2>
                            <div class="trainer-profile__bio"><?php the_content(); ?></div>
                        </section>
                        <?php if ($website_url !== '' || !empty($social_profiles)) : ?>
                            <footer class="trainer-profile__links" aria-label="<?php echo esc_attr($copy('_myliba_trainers_profile_links_label', $language === 'en' ? 'Website and social media' : 'Web sitesi ve sosyal medya')); ?>">
                                <?php if (!empty($social_profiles)) : ?>
                                    <div class="trainer-profile__socials">
                                        <?php foreach ($social_profiles as $social_profile) : ?>
                                            <a class="trainer-profile__social trainer-profile__social--<?php echo esc_attr($social_profile['key']); ?>" href="<?php echo esc_url($social_profile['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social_profile['label']); ?>" title="<?php echo esc_attr($social_profile['label']); ?>">
                                                <?php echo $social_profile['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static theme-owned SVG. ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($website_url !== '') : ?>
                                    <div class="trainer-profile__website">
                                        <a href="<?php echo esc_url($website_url); ?>" target="_blank" rel="noopener noreferrer">
                                            <span><?php echo esc_html($website_label ?: $copy('_myliba_trainers_profile_website_label', $language === 'en' ? 'Personal website' : 'Kişisel web sitesi')); ?></span>
                                            <b aria-hidden="true">↗</b>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </footer>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php
        $other_people = new WP_Query([
            'post_type' => 'myliba_team',
            'post_status' => 'publish',
            'post__not_in' => [$person_id],
            'posts_per_page' => max(1, min(12, (int) $copy('_myliba_trainers_related_limit', '3'))),
            'meta_query' => [
                'language' => ['key' => '_myliba_language', 'value' => $language],
                'sort_order' => ['key' => '_myliba_order', 'compare' => 'EXISTS', 'type' => 'NUMERIC'],
            ],
            'orderby' => ['sort_order' => 'ASC', 'title' => 'ASC'],
            'order' => 'ASC',
        ]);
        ?>
        <?php if ($other_people->have_posts()) : ?>
            <section class="trainer-profile__others">
                <div class="solutions-shell">
                    <header>
                        <p class="eyebrow"><?php echo esc_html($copy('_myliba_trainers_related_eyebrow', $language === 'en' ? 'Our team' : 'Ekibimiz')); ?></p>
                        <h2><?php echo esc_html($copy('_myliba_trainers_related_title', $language === 'en' ? 'Meet other experts.' : 'Diğer uzmanlarımızla tanışın.')); ?></h2>
                    </header>
                    <div class="trainer-profile__others-grid">
                        <?php while ($other_people->have_posts()) : $other_people->the_post(); ?>
                            <a href="<?php the_permalink(); ?>">
                                <span class="trainer-profile__other-photo">
                                    <?php if (has_post_thumbnail()) : the_post_thumbnail('medium', ['loading' => 'lazy', 'decoding' => 'async']); else : ?>
                                        <b aria-hidden="true"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></b>
                                    <?php endif; ?>
                                </span>
                                <span><strong><?php the_title(); ?></strong><small><?php echo esc_html((string) get_post_meta(get_the_ID(), '_myliba_person_headline', true)); ?></small></span>
                                <i aria-hidden="true">→</i>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
        <?php endif; wp_reset_postdata(); ?>
    </main>
    <?php
endwhile;

get_footer();
