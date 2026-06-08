<?php
get_header();
?>

<section class="archive-hero">
    <p class="eyebrow"><?php esc_html_e('404', 'myliba'); ?></p>
    <h1><?php esc_html_e('This page is not available, but the next step is clear.', 'myliba'); ?></h1>
    <p class="hero__subtitle"><?php esc_html_e('Explore Myliba products, read the blog, or request a demo to see the platform.', 'myliba'); ?></p>
    <div class="hero__actions">
        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_demo_url()); ?>"><?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?></a>
        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to home', 'myliba'); ?></a>
    </div>
</section>

<?php get_footer(); ?>

