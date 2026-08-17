<?php
if (!defined('ABSPATH')) {
    exit;
}

$footer_cta_enabled = myliba_option('footer_cta_enabled', '1') !== '0';
$footer_cta_eyebrow = (string) myliba_option('footer_cta_eyebrow', myliba_text('Culture, goals and performance'));
$footer_cta_title = (string) myliba_option('footer_cta_title', myliba_text('Ready to make culture measurable?'));
$footer_cta_url_option = (string) myliba_option('primary_cta_url', myliba_page_url('contact'));
$footer_cta_url = $footer_cta_url_option !== '' ? $footer_cta_url_option : myliba_page_url('contact');
if (str_contains($footer_cta_url, '/en/contact')) {
    $footer_cta_url = myliba_page_url('contact');
}
$footer_primary_cta_label = (string) myliba_option('primary_cta_label', myliba_text('Contact us'));
$footer_demo_label = (string) myliba_option('demo_cta_label', myliba_text('Request a demo'));
$footer_demo_url = (string) myliba_option('demo_url', myliba_demo_url());

$footer_note = (string) myliba_option('footer_note', myliba_text('OKR, culture, ethics, and security consulting.'));
$footer_contact_email = (string) myliba_option('contact_email', get_option('admin_email'));
$footer_phone_label = (string) myliba_option('phone_label', '+90 553 986 86 99');
$footer_phone_url = (string) myliba_option('phone_url', 'tel:+905539868699');
$footer_organization_name = (string) myliba_option('organization_name', 'Myliba');

$footer_languages = myliba_language_links();
$footer_lang = myliba_current_language();

// Column titles (Customizer override -> Menu name -> Default fallback)
$col1_title = (string) myliba_option('footer_col1_title', myliba_nav_menu_title('footer_solutions', myliba_text('Çözümlerimiz')));
$col2_title = (string) myliba_option('footer_col2_title', myliba_nav_menu_title('footer_development', myliba_text('Gelişim Merkezi')));
$col3_title = (string) myliba_option('footer_col3_title', myliba_nav_menu_title('footer_company', myliba_text('Şirket')));
$col4_title = (string) myliba_option('footer_col4_title', myliba_nav_menu_title('footer_legal', myliba_text('Güvenlik ve Yasal')));

// 1. Column Fallback: Solutions (Çözümlerimiz & Yazılım)
$footer_solution_links = [
    [
        'label' => myliba_text('Yazılım'),
        'url' => myliba_page_url('products'),
    ],
];
foreach (myliba_solution_catalog() as $solution_slug => $solution) {
    $footer_solution_links[] = [
        'label' => myliba_text($solution['title']),
        'url' => myliba_solution_url($solution_slug),
    ];
}

// 2. Column Fallback: Development Center & Academy (Gelişim Merkezi & Akademi)
$development_items = myliba_development_center_items();
$footer_development_links = [
    [
        'label' => myliba_text('Akademi'),
        'url' => myliba_page_url('academy'),
    ],
    [
        'label' => myliba_text($development_items['reports']['label'] ?? 'Raporlar ve Trendler'),
        'url' => $development_items['reports']['url'] ?? home_url('/tr/gelisim-merkezi/raporlar-ve-trendler/'),
    ],
    [
        'label' => myliba_text($development_items['blog']['label'] ?? 'Blog'),
        'url' => $development_items['blog']['url'] ?? myliba_page_url('blog'),
    ],
    [
        'label' => myliba_text($development_items['events']['label'] ?? 'Etkinlikler'),
        'url' => $development_items['events']['url'] ?? myliba_page_url('events'),
    ],
];

// 3. Column Fallback: Company (Şirket & Kurumsal)
$footer_company_links = [
    [
        'label' => myliba_text('Biz Kimiz'),
        'url' => myliba_page_url('story'),
    ],
    [
        'label' => myliba_text('İletişim'),
        'url' => myliba_page_url('contact'),
    ],
    [
        'label' => $footer_demo_label,
        'url' => $footer_demo_url,
    ],
    [
        'label' => myliba_text('SSS'),
        'url' => myliba_page_url('faq'),
    ],
];

// 4. Column Fallback: Security & Legal (Güvenlik ve Yasal)
$footer_legal_links = [
    [
        'label' => myliba_text('Güvenlik'),
        'url' => myliba_page_url('security'),
    ],
    [
        'label' => myliba_text('KVKK Aydınlatma Metni'),
        'url' => myliba_page_url('privacy'),
    ],
    /*     [
            'label' => myliba_text('KVKK ve GDPR'),
            'url' => home_url($footer_lang === 'en' ? '/en/kvkk/' : '/tr/kvkk/'),
        ],
        [
            'label' => myliba_text('Çerez Politikası'),
            'url' => home_url($footer_lang === 'en' ? '/en/cookie-policy/' : '/tr/cerez-politikasi/'),
        ],
        [
            'label' => myliba_text('Kullanım Şartları'),
            'url' => home_url($footer_lang === 'en' ? '/en/terms-of-use/' : '/tr/kullanim-sartlari/'),
        ], */
];

// Social links from settings / customizer
$footer_social_links = function_exists('myliba_social_links') ? myliba_social_links() : [];
?>
</main>
<footer class="site-footer">
    <?php
    $page_cta = myliba_get_page_footer_cta();
    if ($page_cta['enabled'] && ($page_cta['title'] !== '' || $page_cta['primary_label'] !== '')):
    ?>
        <section class="site-footer__cta-wrap" aria-label="<?php echo esc_attr(myliba_text('Footer call to action')); ?>">
            <div class="site-footer__cta">
                <div>
                    <?php if ($page_cta['eyebrow'] !== ''): ?>
                        <span><?php echo esc_html($page_cta['eyebrow']); ?></span>
                    <?php endif; ?>
                    <?php if ($page_cta['title'] !== ''): ?>
                        <h2><?php echo esc_html($page_cta['title']); ?></h2>
                    <?php endif; ?>
                </div>
                <div class="site-footer__cta-actions">
                    <?php if ($page_cta['primary_label'] !== ''): ?>
                        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($page_cta['primary_url'] ?: '#'); ?>"<?php echo !empty($page_cta['primary_data_attr']) ? ' ' . esc_attr($page_cta['primary_data_attr']) : ''; ?>>
                            <?php echo esc_html($page_cta['primary_label']); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($page_cta['secondary_label'] !== ''): ?>
                        <a class="myliba-button myliba-button--secondary" href="<?php echo esc_url($page_cta['secondary_url'] ?: '#'); ?>"<?php echo !empty($page_cta['secondary_data_attr']) ? ' ' . esc_attr($page_cta['secondary_data_attr']) : ''; ?>>
                            <?php echo esc_html($page_cta['secondary_label']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="site-footer__main">
        <div class="site-footer__brand-panel">
            <?php myliba_brand_link('site-brand--footer'); ?>
            <?php if ($footer_note !== ''): ?>
                <p><?php echo esc_html($footer_note); ?></p>
            <?php endif; ?>

            <div class="site-footer__contact-list">
                <?php if ($footer_contact_email !== ''): ?>
                    <a
                        href="<?php echo esc_url('mailto:' . $footer_contact_email); ?>"><?php echo esc_html($footer_contact_email); ?></a>
                <?php endif; ?>
                <?php if ($footer_phone_label !== '' && $footer_phone_url !== ''): ?>
                    <a href="<?php echo esc_url($footer_phone_url); ?>"><?php echo esc_html($footer_phone_label); ?></a>
                <?php endif; ?>
            </div>

            <?php if (!empty($footer_social_links)): ?>
                <div class="site-footer__socials" role="navigation"
                    aria-label="<?php echo esc_attr(myliba_text('Social links')); ?>">
                    <?php foreach ($footer_social_links as $social_link): ?>
                        <a href="<?php echo esc_url($social_link['url']); ?>"
                            class="site-footer__social-link site-footer__social-link--<?php echo esc_attr($social_link['key'] ?? ''); ?>"
                            aria-label="<?php echo esc_attr($social_link['label']); ?>" title="<?php echo esc_attr($social_link['label']); ?>" target="_blank"
                            rel="noopener noreferrer">
                            <?php echo !empty($social_link['svg']) ? $social_link['svg'] : esc_html($social_link['short']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php
        $solutions_loc = myliba_resolve_nav_menu_location('footer_solutions');
        $development_loc = myliba_resolve_nav_menu_location('footer_development');
        $company_loc = myliba_resolve_nav_menu_location('footer_company');
        $legal_loc = myliba_resolve_nav_menu_location('footer_legal');
        $bottom_loc = myliba_resolve_nav_menu_location('footer_bottom');
        ?>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr($col1_title); ?>">
            <h3><?php echo esc_html($col1_title); ?></h3>
            <?php if ($solutions_loc !== ''): ?>
                <?php
                wp_nav_menu([
                    'theme_location' => $solutions_loc,
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else: ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_solution_links as $footer_link): ?>
                        <li><a
                                href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr($col2_title); ?>">
            <h3><?php echo esc_html($col2_title); ?></h3>
            <?php if ($development_loc !== ''): ?>
                <?php
                wp_nav_menu([
                    'theme_location' => $development_loc,
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else: ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_development_links as $footer_link): ?>
                        <li><a
                                href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr($col3_title); ?>">
            <h3><?php echo esc_html($col3_title); ?></h3>
            <?php if ($company_loc !== ''): ?>
                <?php
                wp_nav_menu([
                    'theme_location' => $company_loc,
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else: ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_company_links as $footer_link): ?>
                        <li><a
                                href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr($col4_title); ?>">
            <h3><?php echo esc_html($col4_title); ?></h3>
            <?php if ($legal_loc !== ''): ?>
                <?php
                wp_nav_menu([
                    'theme_location' => $legal_loc,
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else: ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_legal_links as $footer_link): ?>
                        <li><a
                                href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>
    </div>

    <div class="site-footer__bottom">
        <p><?php echo esc_html(sprintf(myliba_text('Copyright %1$s %2$s. All rights reserved.'), date_i18n('Y'), $footer_organization_name)); ?>
        </p>
        <div class="site-footer__bottom-links">
            <?php if ($bottom_loc !== ''): ?>
                <?php
                wp_nav_menu([
                    'theme_location' => $bottom_loc,
                    'container' => false,
                    'menu_class' => 'site-footer__bottom-menu',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else: ?>
                <?php foreach ($footer_languages as $language_link): ?>
                    <a href="<?php echo esc_url($language_link['url']); ?>"
                        data-myliba-locale="<?php echo esc_attr(strtolower((string) $language_link['label'])); ?>" <?php echo !empty($language_link['active']) ? 'aria-current="true"' : ''; ?>>
                        <?php echo esc_html($language_link['label']); ?>
                    </a>
                <?php endforeach; ?>
                <a
                    href="<?php echo esc_url(myliba_page_url('security')); ?>"><?php echo esc_html(myliba_text('Security')); ?></a>
                <a
                    href="<?php echo esc_url(myliba_page_url('privacy')); ?>"><?php echo esc_html(myliba_text('Privacy')); ?></a>
            <?php endif; ?>
        </div>
    </div>
</footer>
<div class="mobile-sticky-cta" aria-label="<?php echo esc_attr(myliba_text('Mobile conversion actions')); ?>">
    <a class="mobile-sticky-cta__demo" href="<?php echo esc_url($footer_demo_url); ?>">
        <?php echo esc_html($footer_demo_label); ?>
    </a>
    <a class="mobile-sticky-cta__portal" href="<?php echo esc_url(myliba_portal_url()); ?>">
        <?php echo esc_html(myliba_option('portal_cta_label', myliba_text('Portal login'))); ?>
    </a>
</div>
<?php wp_footer(); ?>
</body>

</html>