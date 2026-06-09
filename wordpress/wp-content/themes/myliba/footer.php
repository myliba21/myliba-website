<?php
if (!defined('ABSPATH')) {
    exit;
}

$footer_cta_url = (string) myliba_option('primary_cta_url', myliba_page_url('contact'));
$footer_cta_url = $footer_cta_url !== '' ? $footer_cta_url : myliba_page_url('contact');
$footer_demo_url = myliba_demo_url();
$footer_contact_email = (string) myliba_option('contact_email', get_option('admin_email'));
$footer_phone_label = (string) myliba_option('phone_label', '');
$footer_phone_url = (string) myliba_option('phone_url', '');
$footer_languages = myliba_language_links();

$footer_page_links = [
    ['label' => __('Products', 'myliba'), 'url' => myliba_page_url('products')],
    ['label' => __('Academy', 'myliba'), 'url' => myliba_page_url('academy')],
    ['label' => __('Culture Analysis', 'myliba'), 'url' => myliba_page_url('culture')],
    ['label' => __('Ethics Counsel', 'myliba'), 'url' => myliba_page_url('ethics')],
    ['label' => __('Our Story', 'myliba'), 'url' => myliba_page_url('story')],
    ['label' => __('FAQ', 'myliba'), 'url' => myliba_page_url('faq')],
    ['label' => __('Security', 'myliba'), 'url' => myliba_page_url('security')],
    ['label' => __('Privacy', 'myliba'), 'url' => myliba_page_url('privacy')],
    ['label' => __('Contact', 'myliba'), 'url' => myliba_page_url('contact')],
];

$footer_company_fallback = [
    ['label' => __('Blog', 'myliba'), 'url' => myliba_page_url('blog')],
    ['label' => __('Our Story', 'myliba'), 'url' => myliba_page_url('story')],
    ['label' => __('FAQ', 'myliba'), 'url' => myliba_page_url('faq')],
    ['label' => __('Security', 'myliba'), 'url' => myliba_page_url('security')],
    ['label' => __('Privacy', 'myliba'), 'url' => myliba_page_url('privacy')],
];

$footer_product_links = [];
$footer_products_query = myliba_get_entries('myliba_product', 8);
if ($footer_products_query->have_posts()) {
    while ($footer_products_query->have_posts()) {
        $footer_products_query->the_post();
        $footer_product_links[] = [
            'label' => get_the_title(),
            'url' => get_permalink(),
        ];
    }
    wp_reset_postdata();
}

if (!$footer_product_links) {
    $footer_product_links = [
        ['label' => __('Goals', 'myliba'), 'url' => myliba_page_url('products')],
        ['label' => __('Conversations', 'myliba'), 'url' => myliba_page_url('products')],
        ['label' => __('1:1s', 'myliba'), 'url' => myliba_page_url('products')],
        ['label' => __('Feedback and Feedforward', 'myliba'), 'url' => myliba_page_url('products')],
        ['label' => __('Manager Effectiveness', 'myliba'), 'url' => myliba_page_url('products')],
        ['label' => __('Calibration', 'myliba'), 'url' => myliba_page_url('products')],
    ];
}

$footer_blog_fallback = [];
$footer_posts_query = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'ignore_sticky_posts' => true,
]);
if ($footer_posts_query->have_posts()) {
    while ($footer_posts_query->have_posts()) {
        $footer_posts_query->the_post();
        $footer_blog_fallback[] = [
            'label' => get_the_title(),
            'url' => get_permalink(),
        ];
    }
    wp_reset_postdata();
}

if (!$footer_blog_fallback) {
    $footer_blog_fallback = [
        ['label' => __('Blog', 'myliba'), 'url' => myliba_page_url('blog')],
        ['label' => __('Events', 'myliba'), 'url' => myliba_page_url('events')],
        ['label' => __('OKR Culture Academy', 'myliba'), 'url' => myliba_page_url('academy')],
        ['label' => __('FAQ', 'myliba'), 'url' => myliba_page_url('faq')],
    ];
}

$footer_social_links = [
    ['label' => __('LinkedIn', 'myliba'), 'url' => (string) myliba_option('linkedin_url', ''), 'short' => 'in'],
    ['label' => __('Instagram', 'myliba'), 'url' => (string) myliba_option('instagram_url', ''), 'short' => 'ig'],
];
?>
</main>
<footer class="site-footer">
    <section class="site-footer__cta-wrap" aria-label="<?php esc_attr_e('Footer call to action', 'myliba'); ?>">
        <div class="site-footer__cta">
            <div>
                <span><?php esc_html_e('Culture, goals and performance', 'myliba'); ?></span>
                <h2><?php echo esc_html(myliba_option('footer_cta_title', __('Ready to make culture measurable?', 'myliba'))); ?></h2>
            </div>
            <div class="site-footer__cta-actions">
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($footer_cta_url); ?>">
                    <?php echo esc_html(myliba_option('primary_cta_label', __('Contact us', 'myliba'))); ?>
                </a>
                <a class="myliba-button myliba-button--secondary" href="<?php echo esc_url($footer_demo_url); ?>">
                    <?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?>
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

            <div class="site-footer__socials" aria-label="<?php esc_attr_e('Social links', 'myliba'); ?>">
                <?php foreach ($footer_social_links as $social_link) : ?>
                    <?php if ($social_link['url'] !== '') : ?>
                        <a href="<?php echo esc_url($social_link['url']); ?>" aria-label="<?php echo esc_attr($social_link['label']); ?>" target="_blank" rel="noreferrer">
                            <?php echo esc_html($social_link['short']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <nav class="site-footer__column" aria-label="<?php esc_attr_e('Footer product links', 'myliba'); ?>">
            <h3><?php esc_html_e('Products', 'myliba'); ?></h3>
            <ul class="site-footer__link-list">
                <?php foreach ($footer_product_links as $footer_link) : ?>
                    <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <nav class="site-footer__column" aria-label="<?php esc_attr_e('Footer page links', 'myliba'); ?>">
            <h3><?php esc_html_e('Pages', 'myliba'); ?></h3>
            <ul class="site-footer__link-list">
                <?php foreach ($footer_page_links as $footer_link) : ?>
                    <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <nav class="site-footer__column" aria-label="<?php esc_attr_e('Footer blog links', 'myliba'); ?>">
            <h3><?php esc_html_e('Blog & resources', 'myliba'); ?></h3>
            <?php if (has_nav_menu('footer_blog')) : ?>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer_blog',
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else : ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_blog_fallback as $footer_link) : ?>
                        <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php esc_attr_e('Footer company links', 'myliba'); ?>">
            <h3><?php esc_html_e('Company', 'myliba'); ?></h3>
            <?php if (has_nav_menu('footer')) : ?>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer',
                    'container' => false,
                    'menu_class' => 'site-footer__link-list',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]);
                ?>
            <?php else : ?>
                <ul class="site-footer__link-list">
                    <?php foreach ($footer_company_fallback as $footer_link) : ?>
                        <li><a href="<?php echo esc_url($footer_link['url']); ?>"><?php echo esc_html($footer_link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </nav>
    </div>

    <div class="site-footer__bottom">
        <p><?php echo esc_html(sprintf(__('Copyright %1$s %2$s. All rights reserved.', 'myliba'), date_i18n('Y'), myliba_option('organization_name', 'Myliba'))); ?></p>
        <div class="site-footer__bottom-links">
            <?php foreach ($footer_languages as $language_link) : ?>
                <a href="<?php echo esc_url($language_link['url']); ?>" <?php echo !empty($language_link['active']) ? 'aria-current="true"' : ''; ?>>
                    <?php echo esc_html($language_link['label']); ?>
                </a>
            <?php endforeach; ?>
            <a href="<?php echo esc_url(myliba_page_url('security')); ?>"><?php esc_html_e('Security', 'myliba'); ?></a>
            <a href="<?php echo esc_url(myliba_page_url('privacy')); ?>"><?php esc_html_e('Privacy', 'myliba'); ?></a>
        </div>
    </div>
</footer>
<div class="mobile-sticky-cta" aria-label="<?php esc_attr_e('Mobile conversion actions', 'myliba'); ?>">
    <a class="mobile-sticky-cta__demo" href="<?php echo esc_url(myliba_demo_url()); ?>">
        <?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?>
    </a>
    <a class="mobile-sticky-cta__portal" href="<?php echo esc_url(myliba_portal_url()); ?>">
        <?php esc_html_e('Portal login', 'myliba'); ?>
    </a>
</div>
<?php wp_footer(); ?>
</body>
</html>
