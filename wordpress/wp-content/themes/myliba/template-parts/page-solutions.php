<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
$page_id = get_queried_object_id();
$copy = static fn(string $key): string => \Myliba\Core\PageContent\text($page_id, 'solutions', $key);
$solutions = myliba_solution_catalog();
?>
<section class="solutions-hero">
    <div class="solutions-shell">
        <p class="eyebrow"><?php echo esc_html($copy('hero_eyebrow')); ?></p>
        <h1><?php echo esc_html($copy('hero_title_start')); ?><br><?php echo esc_html($copy('hero_title_end')); ?></h1>
        <p><?php echo esc_html($copy('hero_text')); ?></p>
    </div>
</section>

<section class="solutions-index solutions-shell">
    <header class="solutions-index__heading">
        <p class="eyebrow"><?php echo esc_html($copy('index_eyebrow')); ?></p>
        <h2><?php echo esc_html($copy('index_title')); ?></h2>
        <p><?php echo esc_html($copy('index_text')); ?></p>
    </header>
    <div class="solutions-index__grid">
        <?php foreach ($solutions as $slug => $solution): ?>
            <a class="solution-index-card" href="<?php echo esc_url(myliba_solution_url($slug)); ?>">
                <p><?php echo esc_html($solution['kicker']); ?></p>
                <h2><?php echo esc_html($solution['title']); ?></h2>
                <span class="solution-index-card__summary"><?php echo esc_html($solution['summary']); ?></span>
                <strong><?php echo esc_html($copy('card_link_label')); ?> <span aria-hidden="true">→</span></strong>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="solutions-cta">
    <div class="solutions-cta__inner solutions-shell">
        <div class="solutions-cta__copy">
            <p class="eyebrow"><?php echo esc_html($copy('cta_eyebrow')); ?></p>
            <h2><?php echo esc_html($copy('cta_title')); ?></h2>
            <p><?php echo esc_html($copy('cta_text')); ?></p>
        </div>
        <div class="solutions-cta__actions">
            <?php if ($copy('cta_button_label') !== ''): ?>
                <a class="myliba-button myliba-button--primary"
                    href="<?php echo esc_url($copy('cta_button_url') ?: myliba_page_url('contact')); ?>"><?php echo esc_html($copy('cta_button_label')); ?></a>
            <?php endif; ?>
            <?php if ($copy('cta_secondary_label') !== ''): ?>
                <a class="solutions-cta__secondary"
                    href="<?php echo esc_url($copy('cta_secondary_url') ?: myliba_demo_url()); ?>"><?php echo esc_html($copy('cta_secondary_label')); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>