<?php
if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();
$meta = static fn(string $key): string => trim((string) get_post_meta($page_id, $key, true));
$copy = static fn(string $key): string => function_exists('Myliba\\Core\\PageContent\\text')
    ? \Myliba\Core\PageContent\text($page_id, 'academy', $key)
    : trim((string) get_post_meta($page_id, '_myliba_' . $key, true));
$rows = static fn(string $key): array => function_exists('Myliba\\Core\\PageContent\\collection')
    ? \Myliba\Core\PageContent\collection($page_id, 'academy', $key)
    : [];

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

$parse_modules = static function (string $value): array {
    $items = [];
    foreach (myliba_lines($value) as $line) {
        $item = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        if ($item[0] !== '') {
            $items[] = $item;
        }
    }
    return $items;
};

// 1. Programs
$programs_collection = $rows('programs');
$program_cards = [];
$featured_program = [];
$prepared_programs = [];

if (!empty($programs_collection)) {
    foreach ($programs_collection as $index => $item) {
        $layout = (string) ($item['layout'] ?? 'standard') ?: 'standard';
        $badges = myliba_lines((string) ($item['badges'] ?? ''));
        $benefits = myliba_lines((string) ($item['benefits'] ?? ''));
        $modules = $parse_modules((string) ($item['modules'] ?? ''));

        $program_data = [
            'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'title' => (string) ($item['title'] ?? ''),
            'eyebrow' => (string) ($item['eyebrow'] ?? ''),
            'layout' => $layout,
            'lead' => (string) ($item['lead'] ?? ''),
            'detailed_content' => (string) ($item['detailed_content'] ?? ''),
            'period' => (string) ($item['start_period'] ?? ''),
            'certificate' => (string) ($item['certificate_info'] ?? ''),
            'badges' => $badges,
            'benefits' => $benefits,
            'modules' => $modules,
            'primary_label' => (string) ($item['primary_label'] ?? ''),
            'primary_url' => (string) ($item['primary_url'] ?? ''),
            'secondary_label' => (string) ($item['secondary_label'] ?? ''),
            'secondary_url' => (string) ($item['secondary_url'] ?? ''),
            'thumbnail' => '',
        ];

        $prepared_programs[] = $program_data;
        $card = [
            'number' => $program_data['number'],
            'title' => $program_data['title'],
            'eyebrow' => $program_data['eyebrow'],
            'layout' => $layout,
            'period' => $program_data['period'],
            'certificate' => $program_data['certificate'],
            'badges' => $badges,
        ];
        $program_cards[] = $card;
        if ($layout === 'featured' && empty($featured_program)) {
            $featured_program = $card;
        }
    }
} else {
    // Fallback to myliba_academy custom post types
    $cpt_programs = myliba_get_entries('myliba_academy', -1);
    foreach ($cpt_programs->posts as $program_index => $program_post) {
        $p_id = $program_post->ID;
        $layout = (string) get_post_meta($p_id, '_myliba_academy_layout', true) ?: 'standard';
        $badges = myliba_lines((string) get_post_meta($p_id, '_myliba_academy_program_badges', true));
        $benefits = myliba_lines((string) get_post_meta($p_id, '_myliba_academy_program_benefits', true));
        $modules = $parse_modules((string) get_post_meta($p_id, '_myliba_academy_program_modules', true));

        $program_data = [
            'number' => str_pad((string) ($program_index + 1), 2, '0', STR_PAD_LEFT),
            'title' => get_the_title($program_post),
            'eyebrow' => (string) get_post_meta($p_id, '_myliba_academy_program_eyebrow', true),
            'layout' => $layout,
            'lead' => trim((string) get_the_excerpt($program_post)),
            'detailed_content' => trim((string) get_post_field('post_content', $p_id)),
            'period' => (string) get_post_meta($p_id, '_myliba_academy_start_period', true),
            'certificate' => (string) get_post_meta($p_id, '_myliba_academy_certificate_info', true),
            'badges' => $badges,
            'benefits' => $benefits,
            'modules' => $modules,
            'primary_label' => (string) get_post_meta($p_id, '_myliba_academy_program_primary_label', true),
            'primary_url' => (string) get_post_meta($p_id, '_myliba_academy_program_primary_url', true),
            'secondary_label' => (string) get_post_meta($p_id, '_myliba_academy_program_secondary_label', true),
            'secondary_url' => (string) get_post_meta($p_id, '_myliba_academy_program_secondary_url', true),
            'thumbnail' => has_post_thumbnail($p_id) ? get_the_post_thumbnail($p_id, 'large', ['loading' => 'lazy']) : '',
        ];

        $prepared_programs[] = $program_data;
        $card = [
            'number' => $program_data['number'],
            'title' => $program_data['title'],
            'eyebrow' => $program_data['eyebrow'],
            'layout' => $layout,
            'period' => $program_data['period'],
            'certificate' => $program_data['certificate'],
            'badges' => $badges,
        ];
        $program_cards[] = $card;
        if ($layout === 'featured' && empty($featured_program)) {
            $featured_program = $card;
        }
    }
}

// 2. Testimonials
$testimonials = myliba_get_entries('myliba_testimonial', 12);

// 3. FAQs
$faq_group = $copy('faq_group') ?: $meta('_myliba_academy_faq_group');
$faq_items = [];
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
$faqs_query = myliba_get_entries('myliba_faq', -1, $faq_args);
if ($faqs_query->have_posts()) {
    while ($faqs_query->have_posts()) {
        $faqs_query->the_post();
        $faq_items[] = [
            'question' => get_the_title(),
            'answer' => (string) get_the_content(),
        ];
    }
    wp_reset_postdata();
}
if (empty($faq_items)) {
    $custom_faqs = $rows('faqs');
    foreach ($custom_faqs as $cf) {
        if (!empty($cf['question'])) {
            $faq_items[] = [
                'question' => (string) $cf['question'],
                'answer' => (string) ($cf['answer'] ?? ''),
            ];
        }
    }
}

// 4. Hero & Other elements
$hero_badges = $rows('hero_badges');
$approach_steps = $rows('approach_steps');
$stats = $rows('stats');

$hero_visuals = array_filter([
    $image('_myliba_academy_certificate_image', 'academy-v2-hero__certificate', true),
    $image('_myliba_academy_icf_image', 'academy-v2-hero__icf', true),
    $image('_myliba_academy_digital_badge_image', 'academy-v2-hero__badge', true),
    $image('_myliba_academy_platform_image', 'academy-v2-hero__platform', true),
]);

$hero_eyebrow = $copy('hero_eyebrow') ?: $meta('_myliba_eyebrow');
$hero_title = $copy('hero_title') ?: $meta('_myliba_hero_title');
$hero_lead = $copy('hero_lead') ?: $meta('_myliba_hero_subtitle');
$hero_primary_label = $copy('hero_primary_label') ?: $meta('_myliba_cta_label');
$hero_primary_url = $copy('hero_primary_url') ?: $meta('_myliba_cta_url');
$hero_secondary_label = $copy('hero_secondary_label') ?: $meta('_myliba_academy_hero_secondary_label');
$hero_secondary_url = $copy('hero_secondary_url') ?: $meta('_myliba_academy_hero_secondary_url');
$hero_tertiary_label = $copy('hero_tertiary_label') ?: $meta('_myliba_academy_hero_tertiary_label');
$hero_tertiary_url = $copy('hero_tertiary_url') ?: $meta('_myliba_academy_hero_tertiary_url');

$trust_label = $copy('trust_label') ?: $meta('_myliba_academy_trust_label');
$trust_title = $copy('trust_title') ?: $meta('_myliba_academy_trust_title');
$trust_text = $copy('trust_text') ?: $meta('_myliba_academy_trust_text');

$programs_eyebrow = $copy('programs_eyebrow') ?: $meta('_myliba_academy_programs_eyebrow');
$programs_title = $copy('programs_title') ?: $meta('_myliba_academy_programs_title');
$programs_text = $copy('programs_text') ?: $meta('_myliba_academy_programs_text');
$benefits_title = $copy('benefits_title') ?: $meta('_myliba_academy_benefits_title');
$modules_title = $copy('modules_title') ?: $meta('_myliba_academy_modules_title');

$approach_title = $copy('approach_title') ?: $meta('_myliba_academy_approach_title');
$testimonials_title = $copy('testimonials_title') ?: $meta('_myliba_academy_testimonials_title');
$faq_title = $copy('faq_title') ?: $meta('_myliba_academy_faq_title');

$contact_title = $copy('contact_title') ?: $meta('_myliba_academy_contact_title');
$contact_text = $copy('contact_text') ?: $meta('_myliba_academy_contact_text');

get_header();
?>

<div class="academy-page academy-v2">
    <?php if ($hero_title !== ''): ?>
        <section class="academy-v2-hero">
            <div class="academy-v2-hero__content">
                <?php if ($hero_eyebrow !== ''): ?>
                    <p class="eyebrow"><?php echo esc_html($hero_eyebrow); ?></p>
                <?php endif; ?>
                <h1><?php echo esc_html($hero_title); ?></h1>
                <?php if ($hero_lead !== ''): ?>
                    <p class="academy-v2-hero__lead"><?php echo esc_html($hero_lead); ?></p>
                <?php endif; ?>
                <div class="academy-v2-hero__actions">
                    <?php if ($hero_primary_label !== ''): ?>
                        <?php if ($hero_primary_url !== ''): ?><a class="myliba-button myliba-button--primary"
                                href="<?php echo esc_url($hero_primary_url); ?>"><?php echo esc_html($hero_primary_label); ?></a>
                        <?php else: ?><button class="myliba-button myliba-button--primary" type="button"
                                data-academy-form-open><?php echo esc_html($hero_primary_label); ?></button><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($hero_secondary_label !== ''): ?>
                        <?php if ($hero_secondary_url !== ''): ?><a
                                class="myliba-button myliba-button--ghost"
                                href="<?php echo esc_url($hero_secondary_url); ?>"><?php echo esc_html($hero_secondary_label); ?></a>
                        <?php else: ?><button class="myliba-button myliba-button--ghost" type="button" data-academy-form-open
                                data-participation="corporate"><?php echo esc_html($hero_secondary_label); ?></button><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($hero_tertiary_label !== '' && $hero_tertiary_url !== ''): ?>
                        <a class="academy-v2-link"
                            href="<?php echo esc_url($hero_tertiary_url); ?>"><?php echo esc_html($hero_tertiary_label); ?></a>
                    <?php endif; ?>
                </div>
                <?php if (!empty($hero_badges)): ?>
                    <div class="academy-v2-hero__proof" aria-label="<?php echo esc_attr($hero_eyebrow); ?>">
                        <?php foreach ($hero_badges as $hb): ?>
                            <span><strong><?php echo esc_html($hb['value'] ?? ''); ?></strong><?php echo esc_html($hb['label'] ?? ''); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($hero_visuals || $featured_program): ?>
                <div class="academy-v2-hero__visual" aria-hidden="true">
                    <span class="academy-v2-orbit academy-v2-orbit--one"></span>
                    <span class="academy-v2-orbit academy-v2-orbit--two"></span>
                    <?php if ($hero_visuals): ?>
                        <?php echo implode('', $hero_visuals); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php else: ?>
                        <div class="academy-v2-hero-card">
                            <div class="academy-v2-hero-card__top">
                                <span><?php echo esc_html($featured_program['eyebrow'] ?? ''); ?></span>
                                <?php if (!empty($featured_program['number'])): ?><strong><?php echo esc_html($featured_program['number']); ?></strong><?php endif; ?>
                            </div>
                            <h2><?php echo esc_html($featured_program['title'] ?? ''); ?></h2>
                            <?php if (!empty($featured_program['period']) || !empty($featured_program['certificate'])): ?>
                                <div class="academy-v2-hero-card__credential">
                                    <?php if (!empty($featured_program['period'])): ?><strong><?php echo esc_html($featured_program['period']); ?></strong><?php endif; ?>
                                    <?php if (!empty($featured_program['certificate'])): ?><span><?php echo esc_html($featured_program['certificate']); ?></span><?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($featured_program['badges'])): ?>
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
    <?php endif; ?>

    <?php if ($trust_title !== ''): ?>
        <?php get_template_part('template-parts/client-logo-marquee', null, [
            'label' => $trust_label,
            'title' => $trust_title,
            'text' => $trust_text,
            'class' => 'academy-v2-trust-section',
            'heading_id' => 'academy-trust-title',
            'limit' => 30,
        ]); ?>
    <?php endif; ?>

    <?php if ($programs_title !== ''): ?>
        <section id="programlar" class="section academy-v2-intro">
            <div class="academy-v2-intro__copy">
                <div>
                    <?php if ($programs_eyebrow !== ''): ?>
                        <p class="eyebrow"><?php echo esc_html($programs_eyebrow); ?></p><?php endif; ?>
                    <h2><?php echo esc_html($programs_title); ?></h2>
                </div>
                <?php if ($programs_text !== ''): ?>
                    <p><?php echo esc_html($programs_text); ?></p><?php endif; ?>
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
    <?php endif; ?>

    <?php if (!empty($prepared_programs)): ?>
        <div class="academy-v2-programs">
            <?php foreach ($prepared_programs as $program_index => $program): ?>
                <?php
                $p_index = $program_index + 1;
                $layout = $program['layout'];
                $benefits = $program['benefits'];
                $badges = $program['badges'];
                $modules = $program['modules'];
                $primary_label = trim($program['primary_label']);
                $primary_url = trim($program['primary_url']);
                $secondary_label = trim($program['secondary_label']);
                $secondary_url = trim($program['secondary_url']);
                $eyebrow = trim($program['eyebrow']);
                $start_period = trim($program['period']);
                $certificate = trim($program['certificate']);
                $excerpt = trim($program['lead']);
                $detailed_content = trim($program['detailed_content']);
                $show_detailed_content = $detailed_content !== '' && trim(wp_strip_all_tags($detailed_content)) !== trim(wp_strip_all_tags($excerpt));
                ?>
                <section id="<?php echo esc_attr('program-' . $p_index); ?>"
                    class="academy-v2-program academy-v2-program--<?php echo esc_attr(sanitize_html_class($layout)); ?>">
                    <div class="section academy-v2-program__inner">
                        <div class="academy-v2-program__content">
                            <?php if ($eyebrow !== ''): ?>
                                <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
                            <h2><?php echo esc_html($program['title']); ?></h2>
                            <?php if ($excerpt !== ''): ?>
                                <p class="academy-v2-program__lead"><?php echo esc_html($excerpt); ?></p><?php endif; ?>
                            <?php if ($show_detailed_content): ?>
                                <div class="academy-v2-program__description">
                                    <?php echo wp_kses_post(apply_filters('the_content', $detailed_content)); ?>
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
                                            data-program="<?php echo esc_attr($program['title']); ?>"><?php echo esc_html($secondary_label); ?></button><?php endif; ?>
                                <?php endif; ?>
                                <?php if ($primary_label !== ''): ?>
                                    <?php if ($primary_url !== ''): ?><a class="myliba-button myliba-button--primary"
                                            href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?></a>
                                    <?php else: ?><button class="myliba-button myliba-button--primary" type="button"
                                            data-academy-form-open
                                            data-program="<?php echo esc_attr($program['title']); ?>"><?php echo esc_html($primary_label); ?></button><?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="academy-v2-program__details">
                            <?php if ($benefits): ?>
                                <?php if ($benefits_title !== ''): ?>
                                    <h3><?php echo esc_html($benefits_title); ?></h3><?php endif; ?>
                                <ul class="academy-v2-benefits">
                                    <?php foreach ($benefits as $benefit): ?>
                                        <li><?php echo esc_html($benefit); ?></li><?php endforeach; ?>
                                </ul>
                            <?php elseif (!empty($program['thumbnail'])): ?>
                                <?php echo $program['thumbnail']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($modules): ?>
                        <div class="section academy-v2-modules">
                            <?php if ($modules_title !== ''): ?>
                                <h3><?php echo esc_html($modules_title); ?></h3><?php endif; ?>
                            <div class="academy-v2-modules__grid">
                                <?php foreach ($modules as $module_index => [$module_title_item, $module_details]): ?>
                                    <article>
                                        <span><?php echo esc_html(str_pad((string) ($module_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                        <h4><?php echo esc_html($module_title_item); ?></h4>
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
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($approach_steps && $approach_title !== ''): ?>
        <section id="yaklasim" class="academy-v2-approach">
            <div class="section">
                <h2><?php echo esc_html($approach_title); ?></h2>
                <div class="academy-v2-process">
                    <?php foreach ($approach_steps as $index => $step): ?>
                        <article><span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <h3><?php echo esc_html($step['title'] ?? ''); ?></h3>
                            <p><?php echo esc_html($step['description'] ?? ''); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($stats): ?>
        <section class="section academy-v2-stats"
            aria-label="<?php echo esc_attr($approach_title); ?>">
            <div class="academy-v2-stats__heading">
                <span><?php echo esc_html(myliba_text('Programı bir bakışta')); ?></span>
                <i aria-hidden="true"></i>
            </div>
            <div class="academy-v2-stats__grid">
                <?php foreach ($stats as $stat_index => $stat): ?>
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
                            <strong><?php echo esc_html($stat['value'] ?? ''); ?></strong>
                            <span class="academy-v2-stats__label"><?php echo esc_html($stat['label'] ?? ''); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($testimonials->have_posts() && $testimonials_title !== ''): ?>
        <section id="yorumlar" class="section academy-v2-testimonials" data-academy-slider>
            <div class="academy-v2-section-head">
                <h2><?php echo esc_html($testimonials_title); ?></h2>
                <div class="academy-v2-slider-controls">
                    <button type="button" data-slider-previous
                        aria-label="<?php echo esc_attr(myliba_text('Previous testimonial')); ?>">←</button>
                    <button type="button" data-slider-next
                        aria-label="<?php echo esc_attr(myliba_text('Next testimonial')); ?>">→</button>
                </div>
            </div>
            <div class="academy-v2-testimonials__track" data-slider-track>
                <?php while ($testimonials->have_posts()):
                    $testimonials->the_post(); ?>
                    <article>
                        <div class="academy-v2-testimonial__person">
                            <?php if (has_post_thumbnail()): ?>
                                <?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail', ['loading' => 'lazy']); ?>
                            <?php endif; ?>
                            <div>
                                <h3><?php the_title(); ?></h3>
                                <p><?php echo esc_html(get_post_meta(get_the_ID(), '_myliba_person_role', true)); ?> ·
                                    <?php echo esc_html(get_post_meta(get_the_ID(), '_myliba_company', true)); ?>
                                </p>
                            </div>
                        </div>
                        <blockquote><?php echo wp_kses_post(get_the_content()); ?></blockquote>
                        <?php $testimonial_program = get_post_meta(get_the_ID(), '_myliba_academy_testimonial_program', true); ?>
                        <?php if ($testimonial_program): ?><span
                                class="academy-v2-testimonial__program"><?php echo esc_html($testimonial_program); ?></span><?php endif; ?>
                    </article>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    get_template_part('template-parts/expand', null, [
        'id' => 'sss',
        'title' => $faq_title,
        'items' => $faq_items,
    ]);
    ?>
</div>

<?php if ($contact_title !== ''): ?>
    <dialog class="academy-v2-dialog" data-academy-dialog aria-labelledby="academy-dialog-title">
        <button class="academy-v2-dialog__close" type="button" data-academy-form-close
            aria-label="<?php echo esc_attr(myliba_text('Close')); ?>">×</button>
        <div class="academy-v2-dialog__intro">
            <p class="eyebrow">
                <?php echo esc_html($hero_eyebrow); ?>
            </p>
            <h2 id="academy-dialog-title">
                <?php echo esc_html($contact_title); ?>
            </h2>
            <?php if ($contact_text !== ''): ?>
                <p>
                    <?php echo esc_html($contact_text); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php echo do_shortcode('[myliba_academy_form]'); ?>
    </dialog>
<?php endif; ?>

<?php get_footer(); ?>

