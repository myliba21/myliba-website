<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="site-header__inner">
        <a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php bloginfo('name'); ?>">
            <span class="site-brand__mark">M</span>
            <span class="site-brand__text"><?php bloginfo('name'); ?></span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">
            <span></span>
            <span></span>
            <span></span>
            <span class="screen-reader-text"><?php esc_html_e('Menu', 'myliba'); ?></span>
        </button>

        <nav id="site-navigation" class="site-nav" aria-label="<?php esc_attr_e('Primary navigation', 'myliba'); ?>">
            <?php foreach (myliba_nav_items() as $key => $label) : ?>
                <a href="<?php echo esc_url(myliba_page_url($key)); ?>"><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
            <a class="site-nav__mobile-cta" href="<?php echo esc_url(myliba_option('phone_url', 'tel:+905539868699')); ?>">
                <?php esc_html_e('Call now', 'myliba'); ?>
            </a>
            <a class="site-nav__mobile-cta site-nav__mobile-cta--primary" href="<?php echo esc_url(myliba_demo_url()); ?>">
                <?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?>
            </a>
        </nav>

        <div class="site-actions">
            <div class="language-switcher" aria-label="<?php esc_attr_e('Language switcher', 'myliba'); ?>">
                <?php foreach (myliba_language_links() as $language) : ?>
                    <a class="<?php echo $language['active'] ? 'is-active' : ''; ?>" href="<?php echo esc_url($language['url']); ?>"><?php echo esc_html($language['label']); ?></a>
                <?php endforeach; ?>
            </div>
            <a class="site-phone" href="<?php echo esc_url(myliba_option('phone_url', 'tel:+905539868699')); ?>">
                <?php echo esc_html(myliba_option('phone_label', '+90 553 986 86 99')); ?>
            </a>
            <a class="myliba-button myliba-button--small" href="<?php echo esc_url(myliba_demo_url()); ?>">
                <?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?>
            </a>
        </div>
    </div>
</header>
<main id="main" class="site-main">
