<?php
get_header();
?>

<section class="archive-hero">
    <p class="eyebrow"><?php echo esc_html(myliba_text('404')); ?></p>
    <h1><?php echo esc_html(myliba_text('This page is not available, but the next step is clear.')); ?></h1>
    <p class="hero__subtitle"><?php echo esc_html(myliba_text('Explore Myliba products, read the blog, or request a demo to see the platform.')); ?></p>
    <div class="hero__actions">
        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_demo_url()); ?>"><?php echo esc_html(myliba_option('demo_cta_label', myliba_text('Request a demo'))); ?></a>
        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(myliba_text('Back to home')); ?></a>
    </div>
</section>

<?php get_footer(); ?>

