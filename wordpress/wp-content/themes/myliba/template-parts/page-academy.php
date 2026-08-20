<?php
if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();
$meta = static fn(string $key): string => trim((string) get_post_meta($page_id, $key, true));
$rows = static function (string $value, int $parts = 2): array {
    $items = [];
    foreach (myliba_lines($value) as $line) {
        $item = array_pad(array_map('trim', explode('|', $line, $parts)), $parts, '');
        if ($item[0] !== '') {
            $items[] = $item;
        }
    }
    return $items;
};
$image = static function (string $key, string $class = '', bool $eager = false) use ($page_id): string {
    $attachment_id = absint(get_post_meta($page_id, $key, true));
    if (!$attachment_id) {
        return '';
    }
    return wp_get_attachment_image($attachment_id, 'large', false, [
        'class' => $class,
        'loading' => $eager ? 'eager' : 'lazy',
        'fetchpriority' => $eager ? 'high' : 'auto',
        'decoding' => 'async',
    ]);
};

$programs = myliba_get_entries('myliba_academy', -1);
$testimonials = myliba_get_testimonials_for_page($page_id, 12, true);
$faq_group = $meta('_myliba_academy_faq_group');
$faq_args = [];
if ($faq_group !== '') {
    $faq_args['meta_query'] = [
        'relation' => 'AND',
        [
            'key' => '_myliba_language',
            'value' => myliba_current_language(),
        ],
        [
            'key' => '_myliba_label',
            'value' => $faq_group,
        ],
    ];
}
$faqs = myliba_get_entries('myliba_faq', -1, $faq_args);
$faq_items = [];
while ($faqs->have_posts()) {
    $faqs->the_post();
    $faq_items[] = [
        'question' => get_the_title(),
        'answer' => (string) get_the_content(),
    ];
}
wp_reset_postdata();

$hero_badges = $rows($meta('_myliba_academy_hero_badges'));
$nav_items = $rows($meta('_myliba_academy_nav_items'));
$approach_steps = $rows($meta('_myliba_academy_approach_steps'));
$stats = $rows($meta('_myliba_academy_stats'));

$hero_images_meta = get_post_meta($page_id, '_myliba_academy_hero_images', true);
$hero_image_ids = [];
if (is_array($hero_images_meta)) {
    $hero_image_ids = array_values(array_filter(array_map('absint', $hero_images_meta)));
} elseif (is_string($hero_images_meta) && trim($hero_images_meta) !== '') {
    $decoded = json_decode($hero_images_meta, true);
    if (is_array($decoded)) {
        $hero_image_ids = array_values(array_filter(array_map('absint', $decoded)));
    }
}
if (empty($hero_image_ids)) {
    $single_id = absint(get_post_meta($page_id, '_myliba_academy_hero_image', true));
    if ($single_id) {
        $hero_image_ids = [$single_id];
    }
}

$hero_slider_images = [];
foreach ($hero_image_ids as $idx => $img_id) {
    $img_html = wp_get_attachment_image($img_id, 'large', false, [
        'class' => 'academy-v2-hero__main-image',
        'loading' => $idx === 0 ? 'eager' : 'lazy',
        'fetchpriority' => $idx === 0 ? 'high' : 'auto',
        'decoding' => 'async',
    ]);
    if ($img_html) {
        $hero_slider_images[] = $img_html;
    }
}

$hero_visuals = array_filter([
    $image('_myliba_academy_certificate_image', 'academy-v2-hero__certificate', true),
    $image('_myliba_academy_icf_image', 'academy-v2-hero__icf', true),
    $image('_myliba_academy_digital_badge_image', 'academy-v2-hero__badge', true),
    $image('_myliba_academy_platform_image', 'academy-v2-hero__platform', true),
]);
$program_cards = [];
$featured_program = [];
foreach ($programs->posts as $program_index => $program_post) {
    $layout = (string) get_post_meta($program_post->ID, '_myliba_academy_layout', true);
    $card = [
        'id' => $program_post->ID,
        'number' => str_pad((string) ($program_index + 1), 2, '0', STR_PAD_LEFT),
        'title' => get_the_title($program_post),
        'eyebrow' => (string) get_post_meta($program_post->ID, '_myliba_academy_program_eyebrow', true),
        'layout' => $layout ?: 'standard',
        'period' => (string) get_post_meta($program_post->ID, '_myliba_academy_start_period', true),
        'certificate' => (string) get_post_meta($program_post->ID, '_myliba_academy_certificate_info', true),
        'badges' => myliba_lines((string) get_post_meta($program_post->ID, '_myliba_academy_program_badges', true)),
    ];
    $program_cards[] = $card;
    if ($layout === 'featured') {
        $featured_program = $card;
    }
}
$academy_sections = function_exists('\\Myliba\\Core\\Meta\\academy_sections')
    ? \Myliba\Core\Meta\academy_sections($page_id)
    : [];
$academy_section_map = [];
foreach ($academy_sections as $section) {
    if (is_array($section) && !empty($section['key'])) {
        $academy_section_map[(string) $section['key']] = $section;
    }
}
$academy_section_enabled = static fn(string $key): bool => !isset($academy_section_map[$key]) || !empty($academy_section_map[$key]['enabled']);
$academy_section_style = static fn(string $key): string => sprintf(
    'order:%d',
    (int) ($academy_section_map[$key]['order'] ?? 999)
);
$academy_program_key = static fn(int $program_id): string => 'program_' . $program_id;
$program_cards = array_values(array_filter(
    $program_cards,
    static fn(array $card): bool => $academy_section_enabled($academy_program_key((int) $card['id']))
));
$featured_program = [];
foreach ($program_cards as $card_index => &$card) {
    $card['number'] = str_pad((string) ($card_index + 1), 2, '0', STR_PAD_LEFT);
    if (!$featured_program && $card['layout'] === 'featured') {
        $featured_program = $card;
    }
}
unset($card);
$available_anchors = array_filter([
    'programlar' => $academy_section_enabled('program_intro') && !empty($program_cards),
    'yaklasim' => $academy_section_enabled('approach') && $approach_steps && $meta('_myliba_academy_approach_title') !== '',
    'yorumlar' => $academy_section_enabled('testimonials') && $testimonials->found_posts > 0 && $meta('_myliba_academy_testimonials_title') !== '',
    'sss' => $academy_section_enabled('faq') && $faq_items && $meta('_myliba_academy_faq_title') !== '',
    'iletisim' => $meta('_myliba_academy_final_title') !== '',
]);
$nav_items = array_values(array_filter($nav_items, static fn(array $item): bool => isset($available_anchors[sanitize_title($item[1] ?? '')])));

get_header();
?>

<div class="academy-page academy-v2" style="display:flex;flex-direction:column">
    <?php if ($academy_section_enabled('hero') && $meta('_myliba_hero_title') !== ''): ?>
        <div class="academy-v2-component" style="<?php echo esc_attr($academy_section_style('hero')); ?>">
        <section class="academy-v2-hero">
            <div class="academy-v2-hero__content">
                <?php if ($meta('_myliba_eyebrow') !== ''): ?>
                    <p class="eyebrow"><?php echo esc_html($meta('_myliba_eyebrow')); ?></p>
                <?php endif; ?>
                <h1><?php echo esc_html($meta('_myliba_hero_title')); ?></h1>
                <?php if ($meta('_myliba_hero_subtitle') !== ''): ?>
                    <p class="academy-v2-hero__lead"><?php echo esc_html($meta('_myliba_hero_subtitle')); ?></p>
                <?php endif; ?>
                <div class="academy-v2-hero__actions">
                    <?php if ($meta('_myliba_cta_label') !== ''): ?>
                        <?php if ($meta('_myliba_cta_url') !== ''): ?><a class="myliba-button myliba-button--primary"
                                href="<?php echo esc_url($meta('_myliba_cta_url')); ?>"><?php echo esc_html($meta('_myliba_cta_label')); ?></a>
                        <?php else: ?><button class="myliba-button myliba-button--primary" type="button"
                                data-academy-form-open><?php echo esc_html($meta('_myliba_cta_label')); ?></button><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($meta('_myliba_academy_hero_secondary_label') !== ''): ?>
                        <?php if ($meta('_myliba_academy_hero_secondary_url') !== ''): ?><a
                                class="myliba-button myliba-button--ghost"
                                href="<?php echo esc_url($meta('_myliba_academy_hero_secondary_url')); ?>"><?php echo esc_html($meta('_myliba_academy_hero_secondary_label')); ?></a>
                        <?php else: ?><button class="myliba-button myliba-button--ghost" type="button" data-academy-form-open
                                data-participation="corporate"><?php echo esc_html($meta('_myliba_academy_hero_secondary_label')); ?></button><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($meta('_myliba_academy_hero_tertiary_label') !== '' && $meta('_myliba_academy_hero_tertiary_url') !== ''): ?>
                        <a class="academy-v2-link"
                            href="<?php echo esc_url($meta('_myliba_academy_hero_tertiary_url')); ?>"><?php echo esc_html($meta('_myliba_academy_hero_tertiary_label')); ?></a>
                    <?php endif; ?>
                </div>
                <?php if ($hero_badges): ?>
                    <div class="academy-v2-hero__proof" aria-label="<?php echo esc_attr($meta('_myliba_eyebrow')); ?>">
                        <?php foreach ($hero_badges as [$value, $label]): ?>
                            <span><strong><?php echo esc_html($value); ?></strong><?php echo esc_html($label); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($hero_slider_images) || $hero_visuals || $featured_program): ?>
                <div class="academy-v2-hero__visual" aria-hidden="true">
                    <span class="academy-v2-orbit academy-v2-orbit--one"></span>
                    <span class="academy-v2-orbit academy-v2-orbit--two"></span>
                    <?php if (count($hero_slider_images) > 1): ?>
                        <div class="academy-v2-hero__slider" data-academy-hero-slider aria-roledescription="carousel" aria-label="<?php echo esc_attr(myliba_text('Academy hero gallery')); ?>">
                            <div class="academy-v2-hero__slider-track">
                                <?php foreach ($hero_slider_images as $slider_idx => $slider_img): ?>
                                    <div class="academy-v2-hero__slide<?php echo $slider_idx === 0 ? ' is-active' : ''; ?>" data-academy-hero-slide aria-hidden="<?php echo $slider_idx === 0 ? 'false' : 'true'; ?>">
                                        <?php echo $slider_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="academy-v2-hero__slider-dots" role="tablist" aria-label="<?php echo esc_attr(myliba_text('Slides')); ?>">
                                <?php foreach ($hero_slider_images as $slider_idx => $slider_img): ?>
                                    <button type="button" class="academy-v2-hero__slider-dot<?php echo $slider_idx === 0 ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo $slider_idx === 0 ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr(sprintf(myliba_text('Slide %d'), $slider_idx + 1)); ?>" data-academy-hero-dot="<?php echo $slider_idx; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php elseif (!empty($hero_slider_images)): ?>
                        <?php echo $hero_slider_images[0]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php elseif ($hero_visuals): ?>
                        <?php echo implode('', $hero_visuals); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php else: ?>
                        <div class="academy-v2-hero-card">
                            <div class="academy-v2-hero-card__top">
                                <span><?php echo esc_html($featured_program['eyebrow']); ?></span>
                                <?php if ($featured_program['number']): ?><strong><?php echo esc_html($featured_program['number']); ?></strong><?php endif; ?>
                            </div>
                            <h2><?php echo esc_html($featured_program['title']); ?></h2>
                            <?php if ($featured_program['period'] || $featured_program['certificate']): ?>
                                <div class="academy-v2-hero-card__credential">
                                    <?php if ($featured_program['period']): ?><strong><?php echo esc_html($featured_program['period']); ?></strong><?php endif; ?>
                                    <?php if ($featured_program['certificate']): ?><span><?php echo esc_html($featured_program['certificate']); ?></span><?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($featured_program['badges']): ?>
                                <div class="academy-v2-hero-card__badges">
                                    <?php foreach (array_slice($featured_program['badges'], 0, 4) as $badge): ?><span><?php echo esc_html($badge); ?></span><?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="academy-v2-hero-card__signal"><i></i><i></i><i></i><i></i><i></i></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
        </div>
    <?php endif; ?>

    <?php if ($academy_section_enabled('trust') && $meta('_myliba_academy_trust_title') !== ''): ?>
        <div class="academy-v2-component" style="<?php echo esc_attr($academy_section_style('trust')); ?>">
        <?php get_template_part('template-parts/client-logo-marquee', null, [
            'label' => $meta('_myliba_academy_trust_label'),
            'title' => $meta('_myliba_academy_trust_title'),
            'text' => $meta('_myliba_academy_trust_text'),
            'class' => 'academy-v2-trust-section',
            'heading_id' => 'academy-trust-title',
            'limit' => 30,
        ]); ?>
        </div>
    <?php endif; ?>

    <?php if ($academy_section_enabled('program_intro') && $meta('_myliba_academy_programs_title') !== ''): ?>
        <div class="academy-v2-component" style="<?php echo esc_attr($academy_section_style('program_intro')); ?>">
        <section id="programlar" class="section academy-v2-intro">
            <div class="academy-v2-intro__copy">
                <div>
                    <?php if ($meta('_myliba_academy_programs_eyebrow') !== ''): ?>
                        <p class="eyebrow"><?php echo esc_html($meta('_myliba_academy_programs_eyebrow')); ?></p><?php endif; ?>
                    <h2><?php echo esc_html($meta('_myliba_academy_programs_title')); ?></h2>
                </div>
                <?php if ($meta('_myliba_academy_programs_text') !== ''): ?>
                    <p><?php echo esc_html($meta('_myliba_academy_programs_text')); ?></p><?php endif; ?>
            </div>
            <?php if ($program_cards): ?>
                <div class="academy-v2-program-index">
                    <?php foreach ($program_cards as $card_index => $card): ?>
                        <a class="<?php echo esc_attr('academy-v2-program-index__item academy-v2-program-index__item--' . sanitize_html_class($card['layout'])); ?>"
                            href="#program-<?php echo esc_attr((string) ($card_index + 1)); ?>">
                            <span><?php echo esc_html($card['number']); ?></span>
                            <div>
                                <small><?php echo esc_html($card['eyebrow']); ?></small><strong><?php echo esc_html($card['title']); ?></strong>
                            </div>
                            <i aria-hidden="true">↘</i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        </div>
    <?php endif; ?>

    <?php if ($programs->have_posts()): ?>
            <?php $program_index = 0; ?>
            <?php while ($programs->have_posts()):
                $programs->the_post(); ?>
                <?php
                $program_id = get_the_ID();
                $program_section_key = $academy_program_key($program_id);
                if (!$academy_section_enabled($program_section_key)) {
                    continue;
                }
                $program_index++;
                $layout = get_post_meta($program_id, '_myliba_academy_layout', true) ?: 'standard';
                $benefits = myliba_lines((string) get_post_meta($program_id, '_myliba_academy_program_benefits', true));
                $badges = myliba_lines((string) get_post_meta($program_id, '_myliba_academy_program_badges', true));
                $modules = $rows((string) get_post_meta($program_id, '_myliba_academy_program_modules', true));
                $primary_label = trim((string) get_post_meta($program_id, '_myliba_academy_program_primary_label', true));
                $primary_url = trim((string) get_post_meta($program_id, '_myliba_academy_program_primary_url', true));
                $secondary_label = trim((string) get_post_meta($program_id, '_myliba_academy_program_secondary_label', true));
                $secondary_url = trim((string) get_post_meta($program_id, '_myliba_academy_program_secondary_url', true));
                $eyebrow = trim((string) get_post_meta($program_id, '_myliba_academy_program_eyebrow', true));
                $start_period = trim((string) get_post_meta($program_id, '_myliba_academy_start_period', true));
                $certificate = trim((string) get_post_meta($program_id, '_myliba_academy_certificate_info', true));
                $content = trim((string) get_post_field('post_content', $program_id));
                $excerpt = trim((string) get_the_excerpt());
                $show_detailed_content = $content !== '' && trim(wp_strip_all_tags($content)) !== trim(wp_strip_all_tags($excerpt));
                ?>
                <section id="<?php echo esc_attr('program-' . $program_index); ?>"
                    class="academy-v2-component academy-v2-program academy-v2-program--<?php echo esc_attr(sanitize_html_class($layout)); ?>"
                    style="<?php echo esc_attr($academy_section_style($program_section_key)); ?>">
                    <div class="section academy-v2-program__inner">
                        <div class="academy-v2-program__content">
                            <?php if ($eyebrow !== ''): ?>
                                <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
                            <h2><?php the_title(); ?></h2>
                            <?php if ($excerpt !== ''): ?>
                                <p class="academy-v2-program__lead"><?php echo esc_html($excerpt); ?></p><?php endif; ?>
                            <?php if ($show_detailed_content): ?>
                                <div class="academy-v2-program__description">
                                    <?php echo wp_kses_post(apply_filters('the_content', $content)); ?>
                                </div><?php endif; ?>
                            <?php if ($start_period !== '' || $certificate !== ''): ?>
                                <div class="academy-v2-program__meta">
                                    <?php if ($start_period !== ''): ?><strong><?php echo esc_html($start_period); ?></strong><?php endif; ?>
                                    <?php if ($certificate !== ''): ?><span><?php echo esc_html($certificate); ?></span><?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($badges): ?>
                                <div class="academy-v2-badges">
                                    <?php foreach ($badges as $badge): ?><span><?php echo esc_html($badge); ?></span><?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="academy-v2-program__actions">
                                <?php if ($secondary_label !== ''): ?>
                                    <?php if ($secondary_url !== ''): ?><a class="myliba-button myliba-button--ghost"
                                            href="<?php echo esc_url($secondary_url); ?>"><?php echo esc_html($secondary_label); ?></a>
                                    <?php else: ?><button class="myliba-button myliba-button--ghost" type="button"
                                            data-academy-form-open
                                            data-program="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html($secondary_label); ?></button><?php endif; ?>
                                <?php endif; ?>
                                <?php if ($primary_label !== ''): ?>
                                    <?php if ($primary_url !== ''): ?><a class="myliba-button myliba-button--primary"
                                            href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?></a>
                                    <?php else: ?><button class="myliba-button myliba-button--primary" type="button"
                                            data-academy-form-open
                                            data-program="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html($primary_label); ?></button><?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="academy-v2-program__details">
                            <?php if ($benefits): ?>
                                <?php if ($meta('_myliba_academy_benefits_title') !== ''): ?>
                                    <h3><?php echo esc_html($meta('_myliba_academy_benefits_title')); ?></h3><?php endif; ?>
                                <ul class="academy-v2-benefits">
                                    <?php foreach ($benefits as $benefit): ?>
                                        <li><?php echo esc_html($benefit); ?></li><?php endforeach; ?>
                                </ul>
                            <?php elseif (has_post_thumbnail()): ?>
                                <?php echo get_the_post_thumbnail($program_id, 'large', ['loading' => 'lazy']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($modules): ?>
                        <div class="section academy-v2-modules">
                            <?php if ($meta('_myliba_academy_modules_title') !== ''): ?>
                                <h3><?php echo esc_html($meta('_myliba_academy_modules_title')); ?></h3><?php endif; ?>
                            <div class="academy-v2-modules__grid">
                                <?php foreach ($modules as $module_index => [$module_title, $module_details]): ?>
                                    <article>
                                        <span><?php echo esc_html(str_pad((string) ($module_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                        <h4><?php echo esc_html($module_title); ?></h4>
                                        <?php if ($module_details !== ''): ?>
                                            <ul>
                                                <?php foreach (array_filter(array_map('trim', explode(';', $module_details))) as $detail): ?>
                                                    <li><?php echo esc_html($detail); ?></li><?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endwhile;
            wp_reset_postdata(); ?>
    <?php endif; ?>

    <?php if ($academy_section_enabled('approach') && $approach_steps && $meta('_myliba_academy_approach_title') !== ''): ?>
        <div class="academy-v2-component" style="<?php echo esc_attr($academy_section_style('approach')); ?>">
        <section id="yaklasim" class="academy-v2-approach">
            <div class="section">
                <h2><?php echo esc_html($meta('_myliba_academy_approach_title')); ?></h2>
                <div class="academy-v2-process">
                    <?php foreach ($approach_steps as $index => [$title, $description]): ?>
                        <article><span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <h3><?php echo esc_html($title); ?></h3>
                            <p><?php echo esc_html($description); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        </div>
    <?php endif; ?>

    <?php if ($academy_section_enabled('stats') && $stats): ?>
        <div class="academy-v2-component" style="<?php echo esc_attr($academy_section_style('stats')); ?>">
        <section class="section academy-v2-stats"
            aria-label="<?php echo esc_attr($meta('_myliba_academy_approach_title')); ?>">
            <div class="academy-v2-stats__heading">
                <span><?php echo esc_html(myliba_text('Programı bir bakışta')); ?></span>
                <i aria-hidden="true"></i>
            </div>
            <div class="academy-v2-stats__grid">
                <?php foreach ($stats as $stat_index => [$value, $label]): ?>
                    <article>
                        <span class="academy-v2-stats__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <?php if ($stat_index % 4 === 0): ?>
                                    <circle cx="12" cy="12" r="8.5" />
                                    <path d="M12 7.5V12l3 2" />
                                <?php elseif ($stat_index % 4 === 1): ?>
                                    <path d="m12 4 8 4-8 4-8-4 8-4Z" />
                                    <path d="m4 12 8 4 8-4M4 16l8 4 8-4" />
                                <?php elseif ($stat_index % 4 === 2): ?>
                                    <rect x="3.5" y="5" width="17" height="14" rx="3" />
                                    <path d="m10 9 5 3-5 3V9Z" />
                                <?php else: ?>
                                    <circle cx="9" cy="9" r="3" />
                                    <circle cx="17" cy="10" r="2.25" />
                                    <path
                                        d="M3.5 19c.5-3.1 2.3-4.7 5.5-4.7s5 1.6 5.5 4.7M14 15.3c.8-.7 1.8-1 3.1-1 2.2 0 3.4 1.2 3.8 3.5" />
                                <?php endif; ?>
                            </svg>
                        </span>
                        <div>
                            <strong><?php echo esc_html($value); ?></strong>
                            <span class="academy-v2-stats__label"><?php echo esc_html($label); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        </div>
    <?php endif; ?>

    <?php if ($academy_section_enabled('testimonials') && $testimonials->have_posts() && $meta('_myliba_academy_testimonials_title') !== ''): ?>
        <div class="academy-v2-component" style="<?php echo esc_attr($academy_section_style('testimonials')); ?>">
            <?php get_template_part('template-parts/testimonials', null, [
                'query' => $testimonials,
                'id' => 'yorumlar',
                'eyebrow' => myliba_current_language() === 'tr' ? 'Gerçek deneyimler' : 'Real experiences',
                'title' => $meta('_myliba_academy_testimonials_title'),
                'class' => 'section academy-testimonials',
            ]); ?>
        </div>
    <?php endif; ?>

    <?php if ($academy_section_enabled('faq')): ?>
        <div class="academy-v2-component" style="<?php echo esc_attr($academy_section_style('faq')); ?>">
            <?php
            get_template_part('template-parts/expand', null, [
                'id' => 'sss',
                'title' => $meta('_myliba_academy_faq_title'),
                'items' => $faq_items,
            ]);
            ?>
        </div>
    <?php endif; ?>

    <!--     <?php if ($meta('_myliba_academy_final_title') !== ''): ?>
        <section id="iletisim" class="section academy-v2-final">
            <div>
                <h2><?php echo esc_html($meta('_myliba_academy_final_title')); ?></h2>
                <?php if ($meta('_myliba_academy_final_text') !== ''): ?>
                    <p><?php echo esc_html($meta('_myliba_academy_final_text')); ?></p><?php endif; ?>
            </div>
            <div class="academy-v2-final__actions">
                <?php if ($meta('_myliba_academy_final_primary_label') !== ''): ?><button
                        class="myliba-button myliba-button--primary" type="button"
                        data-academy-form-open><?php echo esc_html($meta('_myliba_academy_final_primary_label')); ?></button><?php endif; ?>
                <?php if ($meta('_myliba_academy_final_secondary_label') !== ''): ?><a
                        class="myliba-button myliba-button--ghost"
                        href="#programlar"><?php echo esc_html($meta('_myliba_academy_final_secondary_label')); ?></a><?php endif; ?>
            </div>
        </section>
    <?php endif; ?> -->
</div>

<?php if ($meta('_myliba_academy_contact_title') !== ''): ?>
    <dialog class="academy-v2-dialog" data-academy-dialog aria-labelledby="academy-dialog-title">
        <button class="academy-v2-dialog__close" type="button" data-academy-form-close
            aria-label="<?php echo esc_attr(myliba_text('Close')); ?>">×</button>
        <div class="academy-v2-dialog__intro">
            <p class="eyebrow">
                <?php echo esc_html($meta('_myliba_eyebrow')); ?>
            </p>
            <h2 id="academy-dialog-title">
                <?php echo esc_html($meta('_myliba_academy_contact_title')); ?>
            </h2>
            <?php if ($meta('_myliba_academy_contact_text') !== ''): ?>
                <p>
                    <?php echo esc_html($meta('_myliba_academy_contact_text')); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php echo do_shortcode('[myliba_academy_form]'); ?>
    </dialog>
<?php endif; ?>

<?php get_footer(); ?>
