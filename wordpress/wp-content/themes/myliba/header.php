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
                    <?php if (in_array($item['key'], ['solutions', 'development'], true)) : ?>
                        <li class="<?php echo esc_attr(trim($item_classes . ' site-nav__item--mega')); ?>">
                            <?php $mega_menu_id = $item['key'] . '-mega-menu'; ?>
                            <a class="<?php echo esc_attr($link_classes); ?>" href="<?php echo esc_url($item['url']); ?>" aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr($mega_menu_id); ?>"<?php echo $aria_current; ?>>
                                <?php echo esc_html($item['label']); ?>
                            </a>
                            <?php if ($item['key'] === 'solutions') : ?>
                                <div id="<?php echo esc_attr($mega_menu_id); ?>" class="mega-menu" aria-label="<?php esc_attr_e('Solutions menu', 'myliba'); ?>">
                                    <div class="mega-menu__intro">
                                        <span>Çözümlerimiz</span>
                                        <strong>İhtiyacınıza uygun çözümü bulun.</strong>
                                        <p>Dört uzmanlık alanını tek noktadan keşfedin.</p>
                                        <a href="<?php echo esc_url($item['url']); ?>">Tüm çözümler</a>
                                    </div>
                                    <div class="mega-menu__grid">
                                        <?php
                                        $solution_menu_descriptions = [
                                            'kurumsal-gelisim-programlari' => 'Kuruma özel gelişim yolculukları.',
                                            'simulasyonlar-ve-takim-koclugu' => 'Simülasyon ve koçluk deneyimleri.',
                                            'danismanlik' => 'Stratejiden uygulamaya destek.',
                                            'kultur-analizi' => 'Veriye dayalı kültür içgörüleri.',
                                        ];
                                        ?>
                                        <?php foreach (myliba_solution_catalog() as $solution_slug => $solution) : ?>
                                            <?php
                                            $is_card_active = is_singular('myliba_solution') && get_post_field('post_name', get_queried_object_id()) === $solution_slug;
                                            ?>
                                            <a class="<?php echo esc_attr(trim('mega-menu__card ' . ($is_card_active ? 'is-active' : ''))); ?>" href="<?php echo esc_url(myliba_solution_url($solution_slug)); ?>"<?php echo $is_card_active ? ' aria-current="page"' : ''; ?>>
                                                <span><?php echo esc_html(substr($solution['title'], 0, 1)); ?></span>
                                                <strong><?php echo esc_html($solution['title']); ?></strong>
                                                <small><?php echo esc_html($solution_menu_descriptions[$solution_slug] ?? ''); ?></small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else :
                                $development_items = myliba_development_center_items();
                                ?>
                                <div id="<?php echo esc_attr($mega_menu_id); ?>" class="mega-menu mega-menu--development" aria-label="Gelişim Merkezi menüsü">
                                    <div class="mega-menu__intro">
                                        <span>Gelişim Merkezi</span>
                                        <strong>Güncel kaynakları keşfedin.</strong>
                                        <p>İçerikler, araştırmalar ve etkinlikler.</p>
                                        <a href="<?php echo esc_url($item['url']); ?>">Tüm içerikler</a>
                                    </div>
                                    <div class="mega-menu__grid">
                                        <?php
                                        $development_menu_descriptions = [
                                            'ebooks' => 'Rehberler ve uygulama kaynakları.',
                                            'reports' => 'Güncel araştırmalar ve içgörüler.',
                                            'blog' => 'Uzman yazıları ve pratik öneriler.',
                                            'events' => 'Webinar, atölye ve buluşmalar.',
                                        ];
                                        ?>
                                        <?php foreach ($development_items as $development_key => $development_item) : ?>
                                            <?php
                                            $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '/';
                                            $is_card_active = myliba_url_path(home_url($request_uri)) === myliba_url_path($development_item['url'])
                                                || is_post_type_archive($development_item['post_type'])
                                                || is_singular($development_item['post_type']);
                                            ?>
                                            <a class="<?php echo esc_attr(trim('mega-menu__card ' . ($is_card_active ? 'is-active' : ''))); ?>" href="<?php echo esc_url($development_item['url']); ?>"<?php echo $is_card_active ? ' aria-current="page"' : ''; ?>>
                                                <span><?php echo esc_html(substr($development_item['label'], 0, 1)); ?></span>
                                                <strong><?php echo esc_html($development_item['label']); ?></strong>
                                                <small><?php echo esc_html($development_menu_descriptions[$development_key] ?? ''); ?></small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
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
            $active_language = $language_links[0] ?? ['label' => 'TR', 'url' => home_url('/tr/'), 'active' => true];
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
