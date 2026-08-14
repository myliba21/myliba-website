<?php
get_header();

$eyebrow = (string) myliba_option('404_eyebrow', myliba_text('404'));
$title = (string) myliba_option('404_title', myliba_text('This page is not available, but the next step is clear.'));
$subtitle = (string) myliba_option('404_subtitle', myliba_text('Explore Myliba products, read the blog, or request a demo to see the platform.'));
$home_btn_label = (string) myliba_option('404_home_btn_label', myliba_text('Back to home'));
?>

<section class="archive-hero">
    <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
    <h1><?php echo esc_html($title); ?></h1>
    <p class="hero__subtitle"><?php echo esc_html($subtitle); ?></p>
    <div class="hero__actions">
        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_demo_url()); ?>"><?php echo esc_html(myliba_option('demo_cta_label', myliba_text('Request a demo'))); ?></a>
        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($home_btn_label); ?></a>
    </div>
</section>

<?php get_footer(); ?>

