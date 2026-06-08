<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
</main>
<footer class="site-footer">
    <div class="site-footer__cta">
        <h2><?php esc_html_e('Ready to make culture measurable?', 'myliba'); ?></h2>
        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>">
            <?php echo esc_html(myliba_option('primary_cta_label', __('Contact us', 'myliba'))); ?>
        </a>
    </div>
    <div class="site-footer__inner">
        <div>
            <a class="site-brand site-brand--footer" href="<?php echo esc_url(home_url('/')); ?>">
                <span class="site-brand__mark">M</span>
                <span class="site-brand__text"><?php bloginfo('name'); ?></span>
            </a>
            <p><?php echo esc_html(myliba_option('footer_note')); ?></p>
        </div>
        <nav class="site-footer__nav" aria-label="<?php esc_attr_e('Footer navigation', 'myliba'); ?>">
            <a href="<?php echo esc_url(myliba_page_url('blog')); ?>"><?php esc_html_e('Blog', 'myliba'); ?></a>
            <a href="<?php echo esc_url(myliba_page_url('story')); ?>"><?php esc_html_e('Our Story', 'myliba'); ?></a>
            <a href="<?php echo esc_url(myliba_page_url('faq')); ?>"><?php esc_html_e('FAQ', 'myliba'); ?></a>
            <a href="<?php echo esc_url(myliba_page_url('security')); ?>"><?php esc_html_e('Security', 'myliba'); ?></a>
            <a href="<?php echo esc_url(myliba_page_url('privacy')); ?>"><?php esc_html_e('Privacy', 'myliba'); ?></a>
        </nav>
    </div>
</footer>
<div class="mobile-sticky-cta" aria-label="<?php esc_attr_e('Mobile conversion actions', 'myliba'); ?>">
    <a class="mobile-sticky-cta__demo" href="<?php echo esc_url(myliba_demo_url()); ?>">
        <?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?>
    </a>
    <a class="mobile-sticky-cta__call" href="<?php echo esc_url(myliba_option('phone_url', 'tel:+905539868699')); ?>">
        <?php esc_html_e('Call now', 'myliba'); ?>
    </a>
</div>
<?php wp_footer(); ?>
</body>
</html>
