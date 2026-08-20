<?php
if (!defined('ABSPATH')) {
    exit;
}

$testimonials = $args['query'] ?? null;
if (!$testimonials instanceof WP_Query || !$testimonials->have_posts()) {
    return;
}

$section_id = sanitize_title((string) ($args['id'] ?? 'yorumlar')) ?: 'yorumlar';
$heading_id = $section_id . '-title';
$eyebrow = trim((string) ($args['eyebrow'] ?? ''));
$title = trim((string) ($args['title'] ?? ''));
$text = trim((string) ($args['text'] ?? ''));
$class = trim((string) ($args['class'] ?? ''));
?>
<section id="<?php echo esc_attr($section_id); ?>"
    class="testimonial-section <?php echo esc_attr($class); ?>"
    data-testimonial-slider aria-labelledby="<?php echo esc_attr($heading_id); ?>">
    <header class="testimonial-section__head">
        <div>
            <?php if ($eyebrow !== ''): ?>
                <p class="testimonial-section__eyebrow"><?php echo esc_html($eyebrow); ?></p>
            <?php endif; ?>
            <h2 id="<?php echo esc_attr($heading_id); ?>"><?php echo esc_html($title); ?></h2>
            <?php if ($text !== ''): ?>
                <p class="testimonial-section__text"><?php echo esc_html($text); ?></p>
            <?php endif; ?>
        </div>
        <div class="testimonial-slider__controls"
            aria-label="<?php echo esc_attr(myliba_current_language() === 'tr' ? 'Yorum gezinme kontrolleri' : 'Testimonial navigation'); ?>">
            <button type="button" data-slider-previous aria-label="<?php echo esc_attr(myliba_text('Previous testimonial')); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </button>
            <button type="button" data-slider-next aria-label="<?php echo esc_attr(myliba_text('Next testimonial')); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </div>
    </header>
    <div class="testimonial-slider__track" data-slider-track tabindex="0"
        aria-label="<?php echo esc_attr(myliba_current_language() === 'tr' ? 'Katılımcı yorumları' : 'Participant testimonials'); ?>">
        <?php while ($testimonials->have_posts()):
            $testimonials->the_post();
            $person_name = trim(get_the_title());
            $role = trim((string) get_post_meta(get_the_ID(), '_myliba_person_role', true));
            $company = trim((string) get_post_meta(get_the_ID(), '_myliba_company', true));
            $program = trim((string) get_post_meta(get_the_ID(), '_myliba_academy_testimonial_program', true));
            $initial = function_exists('mb_substr') ? mb_substr($person_name, 0, 1) : substr($person_name, 0, 1);
            ?>
            <article class="testimonial-card" aria-label="<?php echo esc_attr($person_name); ?>">
                <div class="testimonial-card__header">
                    <span class="testimonial-card__quote" aria-hidden="true">
                        <svg width="20" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.18zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.18z"/></svg>
                    </span>
                    <?php if ($program !== ''): ?>
                        <span class="testimonial-card__program"><?php echo esc_html($program); ?></span>
                    <?php endif; ?>
                </div>
                <blockquote class="testimonial-card__body"><?php echo wp_kses_post(get_the_content()); ?></blockquote>
                <div class="testimonial-card__person">
                    <?php if (has_post_thumbnail()): ?>
                        <?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail', [
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'alt' => $person_name,
                        ]); ?>
                    <?php else: ?>
                        <span class="testimonial-card__avatar" aria-hidden="true"><?php echo esc_html(strtoupper($initial)); ?></span>
                    <?php endif; ?>
                    <div class="testimonial-card__info">
                        <h3><?php echo esc_html($person_name); ?></h3>
                        <?php if ($role !== '' || $company !== ''): ?>
                            <p><?php echo esc_html(implode(' · ', array_filter([$role, $company]))); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
    <div class="testimonial-slider__pagination" data-slider-pagination aria-hidden="true"></div>
</section>
<?php wp_reset_postdata(); ?>
