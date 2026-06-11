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
<?php
$promo_enabled = myliba_option('promo_enabled', '0') === '1';
$promo_left = trim((string) myliba_option('promo_left_text', ''));
$promo_message = trim((string) myliba_option('promo_message', ''));
$promo_right = trim((string) myliba_option('promo_right_text', ''));
$promo_url = trim((string) myliba_option('promo_url', ''));
$promo_dismissible = myliba_option('promo_dismissible', '1') === '1';
?>
<?php if ($promo_enabled && ($promo_left !== '' || $promo_message !== '' || $promo_right !== '')) : ?>
    <div class="site-promo" data-site-promo="<?php echo esc_attr(md5($promo_left . $promo_message . $promo_right . $promo_url)); ?>">
        <?php if ($promo_url !== '') : ?>
            <a class="site-promo__content" href="<?php echo esc_url($promo_url); ?>">
        <?php else : ?>
            <div class="site-promo__content">
        <?php endif; ?>
                <span class="site-promo__side"><?php echo esc_html($promo_left); ?></span>
                <strong><?php echo esc_html($promo_message); ?></strong>
                <span class="site-promo__side site-promo__side--right"><?php echo esc_html($promo_right); ?></span>
        <?php if ($promo_url !== '') : ?>
            </a>
        <?php else : ?>
            </div>
        <?php endif; ?>
        <?php if ($promo_dismissible) : ?>
            <button class="site-promo__dismiss" type="button" aria-label="<?php esc_attr_e('Dismiss promotion', 'myliba'); ?>">&times;</button>
        <?php endif; ?>
    </div>
<?php endif; ?>
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
                    <?php
                    $is_active = myliba_header_menu_item_is_active((string) $item['key'], (string) $item['url']);
                    $item_classes = trim('site-nav__item ' . ($is_active ? 'is-active' : ''));
                    $link_classes = trim('site-nav__link ' . ($is_active ? 'is-active' : ''));
                    $aria_current = $is_active ? ' aria-current="page"' : '';
                    ?>
                    <?php if ($item['key'] === 'solutions') :
                        $mega_products = myliba_mega_menu_products();
                        ?>
                        <li class="<?php echo esc_attr(trim($item_classes . ' site-nav__item--mega')); ?>">
                            <a class="<?php echo esc_attr($link_classes); ?>" href="<?php echo esc_url($item['url']); ?>" aria-haspopup="true" aria-expanded="false" aria-controls="solutions-mega-menu"<?php echo $aria_current; ?>>
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
                                        <?php
                                        $is_card_active = is_singular('myliba_product') && get_queried_object_id() === get_the_ID();
                                        ?>
                                        <a class="<?php echo esc_attr(trim('mega-menu__card ' . ($is_card_active ? 'is-active' : ''))); ?>" href="<?php the_permalink(); ?>"<?php echo $is_card_active ? ' aria-current="page"' : ''; ?>>
                                            <span><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                                            <strong><?php the_title(); ?></strong>
                                            <small><?php echo esc_html(myliba_excerpt(get_the_ID(), 11)); ?></small>
                                        </a>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                            </div>
                        </li>
                    <?php else : ?>
                        <li class="<?php echo esc_attr($item_classes); ?>"><a class="<?php echo esc_attr($link_classes); ?>" href="<?php echo esc_url($item['url']); ?>"<?php echo $aria_current; ?>><?php echo esc_html($item['label']); ?></a></li>
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
                        <a class="<?php echo $language['active'] ? 'is-active' : ''; ?>" href="<?php echo esc_url($language['url']); ?>" data-myliba-locale="<?php echo esc_attr(strtolower((string) $language['label'])); ?>">
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
