<?php
if (!defined('ABSPATH')) {
    exit;
}

$footer_cta_url_option = (string) myliba_option('primary_cta_url', myliba_page_url('contact'));
$footer_cta_url = $footer_cta_url_option !== '' ? $footer_cta_url_option : myliba_page_url('contact');
if (str_contains($footer_cta_url, '/en/contact')) {
    $footer_cta_url = myliba_page_url('contact');
}
$footer_demo_url = myliba_demo_url();
$footer_contact_email = (string) myliba_option('contact_email', get_option('admin_email'));
$footer_phone_label = (string) myliba_option('phone_label', '');
$footer_phone_url = (string) myliba_option('phone_url', '');
$footer_languages = myliba_language_links();
$footer_lang = myliba_current_language();

// 1. Column: Solutions (Çözümlerimiz & Yazılım)
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

// 2. Column: Development Center & Academy (Gelişim Merkezi & Akademi)
$development_items = myliba_development_center_items();
$footer_development_links = [
    [
        'label' => myliba_text('Akademi'),
        'url' => myliba_page_url('academy'),
    ],
    [
        'label' => myliba_text($development_items['ebooks']['label']),
        'url' => $development_items['ebooks']['url'],
    ],
    [
        'label' => myliba_text($development_items['reports']['label']),
        'url' => $development_items['reports']['url'],
    ],
    [
        'label' => myliba_text($development_items['blog']['label']),
        'url' => $development_items['blog']['url'],
    ],
    [
        'label' => myliba_text($development_items['events']['label']),
        'url' => $development_items['events']['url'],
    ],
];

// 3. Column: Company (Şirket & Kurumsal)
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
        'label' => (string) myliba_option('demo_cta_label', myliba_text('Request a demo')),
        'url' => $footer_demo_url,
    ],
    [
        'label' => myliba_text('SSS'),
        'url' => myliba_page_url('faq'),
    ],
];

// 4. Column: Security & Legal (Güvenlik ve Yasal)
$footer_legal_links = [
    [
        'label' => myliba_text('Güvenlik'),
        'url' => myliba_page_url('security'),
    ],
    [
        'label' => myliba_text('KVKK Aydınlatma Metni'),
        'url' => myliba_page_url('privacy'),
    ],
    [
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
    ],
];

$footer_social_links = [
    ['label' => myliba_text('LinkedIn'), 'url' => (string) myliba_option('linkedin_url', ''), 'short' => 'in'],
    ['label' => myliba_text('Instagram'), 'url' => (string) myliba_option('instagram_url', ''), 'short' => 'ig'],
];
?>
</main>
<footer class="site-footer">
    <section class="site-footer__cta-wrap" aria-label="<?php echo esc_attr(myliba_text('Footer call to action')); ?>">
        <div class="site-footer__cta">
            <div>
                <span><?php echo esc_html(myliba_text('Culture, goals and performance')); ?></span>
                <h2><?php echo esc_html(myliba_option('footer_cta_title', myliba_text('Ready to make culture measurable?'))); ?></h2>
            </div>
            <div class="site-footer__cta-actions">
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($footer_cta_url); ?>">
                    <?php echo esc_html(myliba_option('primary_cta_label', myliba_text('Contact us'))); ?>
                </a>
                <a class="myliba-button myliba-button--secondary" href="<?php echo esc_url($footer_demo_url); ?>">
                    <?php echo esc_html(myliba_option('demo_cta_label', myliba_text('Request a demo'))); ?>
                </a>
            </div>
        </div>
    </section>

    <div class="site-footer__main">
        <div class="site-footer__brand-panel">
            <?php myliba_brand_link('site-brand--footer'); ?>
            <p><?php echo esc_html(myliba_option('footer_note')); ?></p>

            <div class="site-footer__contact-list">
                <?php if ($footer_contact_email !== '') : ?>
                    <a href="<?php echo esc_url('mailto:' . $footer_contact_email); ?>"><?php echo esc_html($footer_contact_email); ?></a>
                <?php endif; ?>
                <?php if ($footer_phone_label !== '' && $footer_phone_url !== '') : ?>
                    <a href="<?php echo esc_url($footer_phone_url); ?>"><?php echo esc_html($footer_phone_label); ?></a>
                <?php endif; ?>
            </div>

            <div class="site-footer__socials" role="navigation" aria-label="<?php echo esc_attr(myliba_text('Social links')); ?>">
                <?php foreach ($footer_social_links as $social_link) : ?>
                    <?php if ($social_link['url'] !== '') : ?>
                        <a href="<?php echo esc_url($social_link['url']); ?>" aria-label="<?php echo esc_attr($social_link['label']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($social_link['short']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr(myliba_text('Çözümlerimiz')); ?>">
            <h3><?php echo esc_html(myliba_text('Çözümlerimiz')); ?></h3>
            <?php if (has_nav_menu('footer_solutions')) : ?>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer_solutions',
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else : ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_solution_links as $footer_link) : ?>
                        <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr(myliba_text('Gelişim Merkezi')); ?>">
            <h3><?php echo esc_html(myliba_text('Gelişim Merkezi')); ?></h3>
            <?php if (has_nav_menu('footer_development')) : ?>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer_development',
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else : ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_development_links as $footer_link) : ?>
                        <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr(myliba_text('Company')); ?>">
            <h3><?php echo esc_html(myliba_text('Company')); ?></h3>
            <?php if (has_nav_menu('footer_company')) : ?>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer_company',
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else : ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_company_links as $footer_link) : ?>
                        <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php echo esc_attr(myliba_text('Güvenlik ve Yasal')); ?>">
            <h3><?php echo esc_html(myliba_text('Güvenlik ve Yasal')); ?></h3>
            <?php if (has_nav_menu('footer_legal')) : ?>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer_legal',
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else : ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_legal_links as $footer_link) : ?>
                        <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>
    </div>

    <div class="site-footer__bottom">
        <p><?php echo esc_html(sprintf(myliba_text('Copyright %1$s %2$s. All rights reserved.'), date_i18n('Y'), myliba_option('organization_name', 'Myliba'))); ?></p>
        <div class="site-footer__bottom-links">
            <?php foreach ($footer_languages as $language_link) : ?>
                <a href="<?php echo esc_url($language_link['url']); ?>" data-myliba-locale="<?php echo esc_attr(strtolower((string) $language_link['label'])); ?>" <?php echo !empty($language_link['active']) ? 'aria-current="true"' : ''; ?>>
                    <?php echo esc_html($language_link['label']); ?>
                </a>
            <?php endforeach; ?>
            <a href="<?php echo esc_url(myliba_page_url('security')); ?>"><?php echo esc_html(myliba_text('Security')); ?></a>
            <a href="<?php echo esc_url(myliba_page_url('privacy')); ?>"><?php echo esc_html(myliba_text('Privacy')); ?></a>
        </div>
    </div>
</footer>
<div class="mobile-sticky-cta" aria-label="<?php echo esc_attr(myliba_text('Mobile conversion actions')); ?>">
    <a class="mobile-sticky-cta__demo" href="<?php echo esc_url(myliba_demo_url()); ?>">
        <?php echo esc_html(myliba_option('demo_cta_label', myliba_text('Request a demo'))); ?>
    </a>
    <a class="mobile-sticky-cta__portal" href="<?php echo esc_url(myliba_portal_url()); ?>">
        <?php echo esc_html(myliba_text('Portal login')); ?>
    </a>
</div>
<?php wp_footer(); ?>
</body>
</html>

