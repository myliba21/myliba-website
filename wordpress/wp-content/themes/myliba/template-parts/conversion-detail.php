<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$benefits = myliba_lines((string) myliba_meta('_myliba_benefits', $post_id));
$modules = myliba_lines((string) myliba_meta('_myliba_related_modules', $post_id));
$faqs = myliba_faq_pairs((string) myliba_meta('_myliba_faq_items', $post_id));

$problem_title = (string) myliba_meta('_myliba_problem_title', $post_id, myliba_text('Problem'));
$solution_title = (string) myliba_meta('_myliba_solution_title', $post_id, myliba_text('Myliba solution'));
$benefits_eyebrow = (string) myliba_meta('_myliba_benefits_eyebrow', $post_id, myliba_text('Benefits'));
$benefits_title = (string) myliba_meta('_myliba_benefits_title', $post_id, myliba_text('What changes with Myliba?'));
$modules_eyebrow = (string) myliba_meta('_myliba_modules_eyebrow', $post_id, myliba_text('Related modules'));
$modules_title = (string) myliba_meta('_myliba_modules_title', $post_id, myliba_text('Connected product capabilities'));
$faq_eyebrow = (string) myliba_meta('_myliba_faq_eyebrow', $post_id, myliba_text('FAQ'));
$faq_title = (string) myliba_meta('_myliba_faq_title', $post_id, myliba_text('Questions teams ask before implementation.'));
$cta_title = (string) myliba_meta('_myliba_cta_title', $post_id, myliba_text('See this flow in a real demo.'));
$cta_subtitle = (string) myliba_meta('_myliba_cta_subtitle', $post_id, myliba_text('We will map your current performance routines and show the product modules that fit.'));
?>

<section class="section">
    <div class="section--split">
        <div>
            <p class="eyebrow"><?php echo esc_html(myliba_meta('_myliba_label', $post_id, myliba_text('Myliba'))); ?></p>
            <h2><?php echo esc_html($problem_title); ?></h2>
            <p><?php echo esc_html(myliba_meta('_myliba_problem', $post_id, myliba_excerpt($post_id, 32))); ?></p>
        </div>
        <div class="split-panel__item">
            <h2><?php echo esc_html($solution_title); ?></h2>
            <p><?php echo esc_html(myliba_meta('_myliba_solution', $post_id, myliba_text('Myliba connects goals, routines and measurable actions in one operating flow.'))); ?></p>
        </div>
    </div>
</section>

<?php if ($benefits) : ?>
    <section class="section band">
        <div class="section__heading">
            <p class="eyebrow"><?php echo esc_html($benefits_eyebrow); ?></p>
            <h2><?php echo esc_html($benefits_title); ?></h2>
        </div>
        <div class="card-grid card-grid--three">
            <?php foreach ($benefits as $benefit) : ?>
                <?php
                $parts = explode('|', $benefit, 2);
                $b_title = trim($parts[0] ?? '');
                $b_desc = isset($parts[1]) ? trim($parts[1]) : myliba_text('Designed to make the behavior visible, repeatable and measurable.');
                ?>
                <article class="feature-card">
                    <h3><?php echo esc_html($b_title); ?></h3>
                    <?php if ($b_desc !== '') : ?>
                        <p><?php echo esc_html($b_desc); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($modules) : ?>
    <section class="section">
        <div class="section__heading">
            <p class="eyebrow"><?php echo esc_html($modules_eyebrow); ?></p>
            <h2><?php echo esc_html($modules_title); ?></h2>
        </div>
        <div class="module-pill-list">
            <?php foreach ($modules as $module) : ?>
                <?php
                $m_parts = explode('|', $module, 2);
                $m_label = trim($m_parts[0] ?? '');
                $m_url = isset($m_parts[1]) ? trim($m_parts[1]) : '';
                ?>
                <?php if ($m_url !== '') : ?>
                    <a href="<?php echo esc_url($m_url); ?>" class="module-pill"><span><?php echo esc_html($m_label); ?></span></a>
                <?php else : ?>
                    <span><?php echo esc_html($m_label); ?></span>
                <?php endif; ?>
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
            <p class="eyebrow"><?php echo esc_html($faq_eyebrow); ?></p>
            <h2><?php echo esc_html($faq_title); ?></h2>
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
        <h2><?php echo esc_html($cta_title); ?></h2>
        <p><?php echo esc_html($cta_subtitle); ?></p>
        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_meta('_myliba_cta_url', $post_id, myliba_demo_url())); ?>">
            <?php echo esc_html(myliba_meta('_myliba_cta_label', $post_id, myliba_option('demo_cta_label', myliba_text('Request a demo')))); ?>
        </a>
    </div>
</section>

