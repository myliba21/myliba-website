<?php
/**
 * Template Name: Myliba Demo
 */

get_header();
get_template_part('template-parts/hero');
?>

<section class="section section--contact">
    <article class="content">
        <p class="eyebrow"><?php esc_html_e('Demo request', 'myliba'); ?></p>
        <h2><?php esc_html_e('Tell us your team size and performance priorities.', 'myliba'); ?></h2>
        <p><?php esc_html_e('We will show how Myliba connects OKR, KPI, CFR, 1:1 meetings, feedback, action management and academy programs around your operating rhythm.', 'myliba'); ?></p>
        <ul class="check-list">
            <li><?php esc_html_e('Short form, no unnecessary fields.', 'myliba'); ?></li>
            <li><?php esc_html_e('Stored in WordPress admin for follow-up.', 'myliba'); ?></li>
            <li><?php esc_html_e('Ready for future CRM integration.', 'myliba'); ?></li>
        </ul>
    </article>
    <aside class="contact-panel">
        <?php echo do_shortcode('[myliba_demo_form]'); ?>
    </aside>
</section>

<?php get_footer(); ?>

