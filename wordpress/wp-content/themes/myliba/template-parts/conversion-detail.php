<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$benefits = myliba_lines((string) myliba_meta('_myliba_benefits', $post_id));
$modules = myliba_lines((string) myliba_meta('_myliba_related_modules', $post_id));
$faqs = myliba_faq_pairs((string) myliba_meta('_myliba_faq_items', $post_id));
?>

<section class="section">
    <div class="section--split">
        <div>
            <p class="eyebrow"><?php echo esc_html(myliba_meta('_myliba_label', $post_id, __('Myliba', 'myliba'))); ?></p>
            <h2><?php esc_html_e('Problem', 'myliba'); ?></h2>
            <p><?php echo esc_html(myliba_meta('_myliba_problem', $post_id, myliba_excerpt($post_id, 32))); ?></p>
        </div>
        <div class="split-panel__item">
            <h2><?php esc_html_e('Myliba solution', 'myliba'); ?></h2>
            <p><?php echo esc_html(myliba_meta('_myliba_solution', $post_id, __('Myliba connects goals, routines and measurable actions in one operating flow.', 'myliba'))); ?></p>
        </div>
    </div>
</section>

<?php if ($benefits) : ?>
    <section class="section band">
        <div class="section__heading">
            <p class="eyebrow"><?php esc_html_e('Benefits', 'myliba'); ?></p>
            <h2><?php esc_html_e('What changes with Myliba?', 'myliba'); ?></h2>
        </div>
        <div class="card-grid card-grid--three">
            <?php foreach ($benefits as $benefit) : ?>
                <article class="feature-card">
                    <h3><?php echo esc_html($benefit); ?></h3>
                    <p><?php esc_html_e('Designed to make the behavior visible, repeatable and measurable.', 'myliba'); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($modules) : ?>
    <section class="section">
        <div class="section__heading">
            <p class="eyebrow"><?php esc_html_e('Related modules', 'myliba'); ?></p>
            <h2><?php esc_html_e('Connected product capabilities', 'myliba'); ?></h2>
        </div>
        <div class="module-pill-list">
            <?php foreach ($modules as $module) : ?>
                <span><?php echo esc_html($module); ?></span>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="section">
    <article class="content">
        <?php the_content(); ?>
    </article>
</section>

<?php if ($faqs) : ?>
    <section class="section band">
        <div class="section__heading">
            <p class="eyebrow"><?php esc_html_e('FAQ', 'myliba'); ?></p>
            <h2><?php esc_html_e('Questions teams ask before implementation.', 'myliba'); ?></h2>
        </div>
        <div class="card-grid card-grid--two">
            <?php foreach ($faqs as $faq) : ?>
                <article class="faq-card">
                    <h3><?php echo esc_html($faq['question']); ?></h3>
                    <p><?php echo esc_html($faq['answer']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="section">
    <div class="cta-panel">
        <h2><?php esc_html_e('See this flow in a real demo.', 'myliba'); ?></h2>
        <p><?php esc_html_e('We will map your current performance routines and show the product modules that fit.', 'myliba'); ?></p>
        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_meta('_myliba_cta_url', $post_id, myliba_demo_url())); ?>">
            <?php echo esc_html(myliba_meta('_myliba_cta_label', $post_id, myliba_option('demo_cta_label', __('Request a demo', 'myliba')))); ?>
        </a>
    </div>
</section>

