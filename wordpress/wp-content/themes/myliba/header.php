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
$promo_enabled = myliba_option('promo_enabled', '1') === '1' || myliba_option('promo_enabled', '1') === 1 || myliba_option('promo_enabled', '1') === true;
$promo_left = trim((string) myliba_option('promo_left_text', myliba_text('Upcoming workshop')));
$promo_message = trim((string) myliba_option('promo_message', myliba_text('Reserve your place in our next workshop.')));
$promo_right = trim((string) myliba_option('promo_right_text', myliba_text('Details')));
$promo_url = trim((string) myliba_option('promo_url', ''));
$promo_dismissible = myliba_option('promo_dismissible', '1') === '1' || myliba_option('promo_dismissible', '1') === 1 || myliba_option('promo_dismissible', '1') === true;

$show_lang_switcher = myliba_option('header_lang_switcher_enabled', '1') !== '0';
$language_links = $show_lang_switcher ? myliba_language_links() : [];
$active_language = $language_links[0] ?? ['label' => 'TR', 'url' => home_url('/tr/'), 'active' => true];
foreach ($language_links as $language) {
    if (!empty($language['active'])) {
        $active_language = $language;
        break;
    }
}
$portal_enabled = myliba_option('header_portal_enabled', '1') !== '0';
$portal_label = (string) myliba_option('portal_cta_label', myliba_text('Portal login'));
$portal_url = (string) myliba_option('portal_url', myliba_portal_url());
$demo_enabled = myliba_option('header_demo_cta_enabled', '1') !== '0';
$demo_label = (string) myliba_option('demo_cta_label', myliba_text('Request a demo'));
$demo_url = (string) myliba_option('demo_url', myliba_demo_url());
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
            <button class="site-promo__dismiss" type="button" aria-label="<?php echo esc_attr(myliba_text('Dismiss promotion')); ?>">&times;</button>
        <?php endif; ?>
    </div>
<?php endif; ?>
<header class="site-header">
    <div class="site-header__inner">
        <?php myliba_brand_link(); ?>

        <nav id="site-navigation" class="site-nav" aria-label="<?php echo esc_attr(myliba_text('Primary navigation')); ?>">
            <ul class="site-nav__menu">
                <?php foreach (myliba_get_primary_nav_items() as $item) : ?>
                    <?php
                    $is_active = myliba_header_menu_item_is_active((string) $item['key'], (string) $item['url']);
                    $has_custom_children = !empty($item['children']);
                    $is_mega_solutions = $item['key'] === 'solutions' && empty($item['children']);
                    $is_mega_development = $item['key'] === 'development' && empty($item['children']);
                    $is_mega_about = $item['key'] === 'story';
                    $is_dropdown = $has_custom_children || $is_mega_solutions || $is_mega_development || $is_mega_about;

                    $item_classes = 'site-nav__item';
                    if ($is_active) {
                        $item_classes .= ' is-active';
                    }
                    if ($is_mega_solutions || $is_mega_development || $is_mega_about) {
                        $item_classes .= ' site-nav__item--mega';
                    } elseif ($has_custom_children) {
                        $item_classes .= ' site-nav__item--dropdown menu-item-has-children';
                    }
                    if (!empty($item['classes'])) {
                        $item_classes .= ' ' . trim($item['classes']);
                    }

                    $link_classes = 'site-nav__link' . ($is_active ? ' is-active' : '');
                    $aria_current = $is_active ? ' aria-current="page"' : '';
                    $mega_menu_id = $item['key'] . '-mega-menu';
                    ?>
                    <?php if ($is_dropdown) : ?>
                        <li class="<?php echo esc_attr(trim($item_classes)); ?>">
                            <a class="<?php echo esc_attr(trim($link_classes)); ?>"
                               href="<?php echo esc_url($item['url']); ?>"
                               <?php if (!empty($item['target'])) : ?>target="<?php echo esc_attr($item['target']); ?>"<?php endif; ?>
                               aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr($mega_menu_id); ?>"<?php echo $aria_current; ?>>
                                <?php echo esc_html($item['label']); ?>
                            </a>
                            <?php if ($has_custom_children && !$is_mega_about) : ?>
                                <ul id="<?php echo esc_attr($mega_menu_id); ?>" class="site-nav__sub-menu sub-menu" aria-label="<?php echo esc_attr($item['label']); ?>">
                                    <?php foreach ($item['children'] as $child) : ?>
                                        <?php
                                        $req_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '/';
                                        $is_child_active = myliba_url_path(home_url($req_uri)) === myliba_url_path($child->url);
                                        ?>
                                        <li class="site-nav__sub-item<?php echo $is_child_active ? ' is-active' : ''; ?>">
                                            <a class="site-nav__sub-link<?php echo $is_child_active ? ' is-active' : ''; ?>"
                                               href="<?php echo esc_url($child->url); ?>"
                                               <?php if (!empty($child->target)) : ?>target="<?php echo esc_attr($child->target); ?>"<?php endif; ?>>
                                                <?php echo esc_html($child->title); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif ($is_mega_about) : ?>
                                <?php $header_lang = myliba_current_language(); ?>
                                <div id="<?php echo esc_attr($mega_menu_id); ?>" class="mega-menu mega-menu--about" aria-label="<?php echo esc_attr($header_lang === 'en' ? 'About Us menu' : 'Hakkımızda menüsü'); ?>">
                                    <div class="mega-menu__intro">
                                        <span><?php echo esc_html($header_lang === 'en' ? 'About Myliba' : 'Myliba Hakkında'); ?></span>
                                        <strong><?php echo esc_html($header_lang === 'en' ? 'Meet the idea and the people behind the transformation.' : 'Dönüşümün arkasındaki yaklaşımı ve insanları tanıyın.'); ?></strong>
                                        <p><?php echo esc_html($header_lang === 'en' ? 'Discover our story, trainers, and consultants.' : 'Hikâyemizi, eğitmenlerimizi ve danışmanlarımızı keşfedin.'); ?></p>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($header_lang === 'en' ? 'Who we are' : 'Biz Kimiz'); ?></a>
                                    </div>
                                    <div class="mega-menu__grid">
                                        <a class="mega-menu__card" href="<?php echo esc_url(myliba_page_url('story')); ?>">
                                            <span aria-hidden="true">B</span>
                                            <strong><?php echo esc_html($header_lang === 'en' ? 'Who We Are' : 'Biz Kimiz'); ?></strong>
                                            <small><?php echo esc_html($header_lang === 'en' ? 'Our purpose, approach, and values.' : 'Amacımızı, yaklaşımımızı ve değerlerimizi keşfedin.'); ?></small>
                                        </a>
                                        <a class="mega-menu__card" href="<?php echo esc_url(myliba_page_url('trainers')); ?>">
                                            <span aria-hidden="true">E</span>
                                            <strong><?php echo esc_html($header_lang === 'en' ? 'Our Trainers' : 'Eğitmenlerimiz'); ?></strong>
                                            <small><?php echo esc_html($header_lang === 'en' ? 'Meet our trainers, coaches, and consultants.' : 'Eğitmen, koç ve danışmanlarımızla tanışın.'); ?></small>
                                        </a>
                                    </div>
                                </div>
                            <?php elseif ($is_mega_solutions) : ?>
                                <?php $header_lang = myliba_current_language(); ?>
                                <div id="<?php echo esc_attr($mega_menu_id); ?>" class="mega-menu" aria-label="<?php echo esc_attr(myliba_text('Solutions menu')); ?>">
                                    <div class="mega-menu__intro">
                                        <span><?php echo esc_html($header_lang === 'en' ? 'Our Solutions' : myliba_text('Çözümlerimiz')); ?></span>
                                        <strong><?php echo esc_html($header_lang === 'en' ? 'Find the right solution for your needs.' : myliba_text('İhtiyacınıza uygun çözümü bulun.')); ?></strong>
                                        <p><?php echo esc_html($header_lang === 'en' ? 'Discover four areas of expertise in one place.' : myliba_text('Dört uzmanlık alanını tek noktadan keşfedin.')); ?></p>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($header_lang === 'en' ? 'All solutions' : myliba_text('Tüm çözümler')); ?></a>
                                    </div>
                                    <div class="mega-menu__grid">
                                        <?php
                                        $solution_menu_descriptions = [
                                            'kurumsal-gelisim-programlari' => 'Kuruma özel gelişim yolculukları.',
                                            'simulasyonlar-ve-takim-koclugu' => 'Simülasyon ve koçluk deneyimleri.',
                                            'danismanlik' => 'Stratejiden uygulamaya destek.',
                                            'kultur-analizi' => 'Veriye dayalı kültür içgörüleri.',
                                            'corporate-development-programs' => 'Tailored development journeys.',
                                            'simulations-and-team-coaching' => 'Simulation and coaching experiences.',
                                            'advisory-and-consulting' => 'Support from strategy to implementation.',
                                            'culture-analysis-solution' => 'Data-driven culture insights.',
                                        ];
                                        ?>
                                        <?php foreach (myliba_solution_catalog() as $solution_slug => $solution) : ?>
                                            <?php
                                            $card_url = myliba_solution_url($solution_slug);
                                            $req_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '/';
                                            $current_page_url = home_url($req_uri);
                                            $is_card_active = (is_singular('myliba_solution') && get_post_field('post_name', get_queried_object_id()) === $solution_slug)
                                                || (function_exists('myliba_url_path') && myliba_url_path($current_page_url) === myliba_url_path($card_url));
                                            $card_desc = $solution_menu_descriptions[$solution_slug] ?? ($solution['summary'] ?? '');
                                            ?>
                                            <a class="<?php echo esc_attr(trim('mega-menu__card ' . ($is_card_active ? 'is-active' : ''))); ?>" href="<?php echo esc_url($card_url); ?>"<?php echo $is_card_active ? ' aria-current="page"' : ''; ?>>
                                                <span><?php echo esc_html(mb_substr($solution['title'], 0, 1)); ?></span>
                                                <strong><?php echo esc_html($solution['title']); ?></strong>
                                                <small><?php echo esc_html($card_desc); ?></small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php elseif ($is_mega_development) : ?>
                                <?php
                                $development_items = myliba_development_center_items();
                                $header_lang = myliba_current_language();
                                ?>
                                <div id="<?php echo esc_attr($mega_menu_id); ?>" class="mega-menu mega-menu--development" aria-label="<?php echo esc_attr(myliba_text('Gelişim Merkezi menüsü')); ?>">
                                    <div class="mega-menu__intro">
                                        <span><?php echo esc_html($header_lang === 'en' ? 'Development Center' : myliba_text('Gelişim Merkezi')); ?></span>
                                        <strong><?php echo esc_html($header_lang === 'en' ? 'Discover the latest resources.' : myliba_text('Güncel kaynakları keşfedin.')); ?></strong>
                                        <p><?php echo esc_html($header_lang === 'en' ? 'Content, research, and events.' : myliba_text('İçerikler, araştırmalar ve etkinlikler.')); ?></p>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($header_lang === 'en' ? 'All content' : myliba_text('Tüm içerikler')); ?></a>
                                    </div>
                                    <div class="mega-menu__grid">
                                        <?php
                                        $development_menu_descriptions = [
                                            'ebooks' => $header_lang === 'en' ? 'Downloadable guides and practical resources.' : myliba_text('İndirilebilir rehberler ve uygulama kaynakları.'),
                                            'reports' => $header_lang === 'en' ? 'Current research and insights.' : myliba_text('Güncel araştırmalar ve içgörüler.'),
                                            'blog' => $header_lang === 'en' ? 'Expert articles and practical recommendations.' : myliba_text('Uzman yazıları ve pratik öneriler.'),
                                            'events' => $header_lang === 'en' ? 'Webinars, workshops, and community sessions.' : myliba_text('Webinar, atölye ve buluşmalar.'),
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
                                                <span><?php echo esc_html(mb_substr($development_item['label'], 0, 1)); ?></span>
                                                <strong><?php echo esc_html($development_item['label']); ?></strong>
                                                <small><?php echo esc_html($development_menu_descriptions[$development_key] ?? ($development_item['description'] ?? '')); ?></small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php else : ?>
                        <li class="<?php echo esc_attr(trim($item_classes)); ?>">
                            <a class="<?php echo esc_attr(trim($link_classes)); ?>"
                               href="<?php echo esc_url($item['url']); ?>"
                               <?php if (!empty($item['target'])) : ?>target="<?php echo esc_attr($item['target']); ?>"<?php endif; ?>
                               <?php echo $aria_current; ?>>
                                <?php echo esc_html($item['label']); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <?php if ($portal_enabled && $portal_url !== '') : ?>
                <a class="site-nav__mobile-cta site-nav__mobile-cta--portal" href="<?php echo esc_url($portal_url); ?>">
                    <?php echo esc_html($portal_label); ?>
                </a>
            <?php endif; ?>
            <?php if ($demo_enabled && $demo_url !== '') : ?>
                <a class="site-nav__mobile-cta site-nav__mobile-cta--primary" href="<?php echo esc_url($demo_url); ?>">
                    <?php echo esc_html($demo_label); ?>
                </a>
            <?php endif; ?>
        </nav>

        <div class="site-actions">
            <?php if ($show_lang_switcher && !empty($language_links)) : ?>
                <div class="language-switcher language-switcher--dropdown" aria-label="<?php echo esc_attr(myliba_text('Language switcher')); ?>">
                    <button class="language-switcher__trigger" type="button" aria-haspopup="true" aria-expanded="false">
                        <span class="language-switcher__flag"><?php echo esc_html(myliba_language_flag((string) $active_language['label'])); ?></span>
                        <span><?php echo esc_html($active_language['label']); ?></span>
                    </button>
                    <div class="language-switcher__menu">
                        <?php foreach ($language_links as $language) : ?>
                            <a class="<?php echo !empty($language['active']) ? 'is-active' : ''; ?>" href="<?php echo esc_url($language['url']); ?>" data-myliba-locale="<?php echo esc_attr(strtolower((string) $language['label'])); ?>"<?php echo !empty($language['active']) ? ' aria-current="true"' : ''; ?>>
                                <span class="language-switcher__flag"><?php echo esc_html(myliba_language_flag((string) $language['label'])); ?></span>
                                <span><?php echo esc_html($language['label']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($portal_enabled && $portal_url !== '') : ?>
                <a class="myliba-button myliba-button--portal" href="<?php echo esc_url($portal_url); ?>">
                    <?php echo esc_html($portal_label); ?>
                </a>
            <?php endif; ?>
            <?php if ($demo_enabled && $demo_url !== '') : ?>
                <a class="myliba-button myliba-button--small" href="<?php echo esc_url($demo_url); ?>">
                    <?php echo esc_html($demo_label); ?>
                </a>
            <?php endif; ?>
        </div>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">
            <span></span>
            <span></span>
            <span></span>
            <span class="screen-reader-text"><?php echo esc_html(myliba_text('Menu')); ?></span>
        </button>
    </div>
</header>
<main id="main" class="site-main">
