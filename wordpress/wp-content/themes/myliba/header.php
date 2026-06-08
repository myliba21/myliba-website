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
        <?php myliba_brand_link(); ?>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">
            <span></span>
            <span></span>
            <span></span>
            <span class="screen-reader-text"><?php esc_html_e('Menu', 'myliba'); ?></span>
        </button>

        <nav id="site-navigation" class="site-nav" aria-label="<?php esc_attr_e('Primary navigation', 'myliba'); ?>">
            <ul class="site-nav__menu">
                <?php foreach (myliba_header_menu() as $item) : ?>
                    <?php if ($item['key'] === 'solutions') :
                        $mega_products = myliba_mega_menu_products();
                        ?>
                        <li class="site-nav__item site-nav__item--mega">
                            <a class="site-nav__link" href="<?php echo esc_url($item['url']); ?>" aria-haspopup="true" aria-expanded="false" aria-controls="solutions-mega-menu">
                                <?php echo esc_html($item['label']); ?>
                            </a>
                            <div id="solutions-mega-menu" class="mega-menu" aria-label="<?php esc_attr_e('Solutions menu', 'myliba'); ?>">
                                <div class="mega-menu__intro">
                                    <span><?php esc_html_e('Solutions', 'myliba'); ?></span>
                                    <strong><?php esc_html_e('Explore Myliba modules', 'myliba'); ?></strong>
                                    <p><?php esc_html_e('Choose the operating routines you want to strengthen across OKR, KPI, CFR, 1:1, feedback and analytics.', 'myliba'); ?></p>
                                    <a href="<?php echo esc_url($item['url']); ?>"><?php esc_html_e('View all solutions', 'myliba'); ?></a>
                                </div>
                                <div class="mega-menu__grid">
                                    <?php while ($mega_products->have_posts()) : $mega_products->the_post(); ?>
                                        <a class="mega-menu__card" href="<?php the_permalink(); ?>">
                                            <span><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                                            <strong><?php the_title(); ?></strong>
                                            <small><?php echo esc_html(myliba_excerpt(get_the_ID(), 11)); ?></small>
                                        </a>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                            </div>
                        </li>
                    <?php else : ?>
                        <li class="site-nav__item"><a class="site-nav__link" href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <a class="site-nav__mobile-cta site-nav__mobile-cta--portal" href="<?php echo esc_url(myliba_portal_url()); ?>">
                <?php esc_html_e('Portal login', 'myliba'); ?>
            </a>
            <a class="site-nav__mobile-cta site-nav__mobile-cta--primary" href="<?php echo esc_url(myliba_demo_url()); ?>">
                <?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?>
            </a>
        </nav>

        <div class="site-actions">
            <?php
            $language_links = myliba_language_links();
            $active_language = $language_links[0] ?? ['label' => 'EN', 'url' => home_url('/'), 'active' => true];
            foreach ($language_links as $language) {
                if (!empty($language['active'])) {
                    $active_language = $language;
                    break;
                }
            }
            ?>
            <div class="language-switcher language-switcher--dropdown" aria-label="<?php esc_attr_e('Language switcher', 'myliba'); ?>">
                <button class="language-switcher__trigger" type="button" aria-haspopup="true">
                    <span class="language-switcher__flag"><?php echo esc_html(myliba_language_flag((string) $active_language['label'])); ?></span>
                    <span><?php echo esc_html($active_language['label']); ?></span>
                </button>
                <div class="language-switcher__menu">
                    <?php foreach ($language_links as $language) : ?>
                        <a class="<?php echo $language['active'] ? 'is-active' : ''; ?>" href="<?php echo esc_url($language['url']); ?>">
                            <span class="language-switcher__flag"><?php echo esc_html(myliba_language_flag((string) $language['label'])); ?></span>
                            <span><?php echo esc_html($language['label']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a class="myliba-button myliba-button--portal" href="<?php echo esc_url(myliba_portal_url()); ?>">
                <?php esc_html_e('Portal login', 'myliba'); ?>
            </a>
            <a class="myliba-button myliba-button--small" href="<?php echo esc_url(myliba_demo_url()); ?>">
                <?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?>
            </a>
        </div>
    </div>
</header>
<main id="main" class="site-main">
