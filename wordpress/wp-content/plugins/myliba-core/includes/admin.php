<?php

namespace Myliba\Core\Admin;

use Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

function boot(): void
{
    add_action('admin_menu', __NAMESPACE__ . '\\register_menu');
    add_action('admin_notices', __NAMESPACE__ . '\\admin_notices');
    add_action('dashboard_glance_items', __NAMESPACE__ . '\\dashboard_counts');
}

function register_menu(): void
{
    add_menu_page(
        __('Myliba', 'myliba'),
        __('Myliba', 'myliba'),
        'manage_options',
        'myliba-settings',
        __NAMESPACE__ . '\\render_settings',
        'dashicons-admin-site-alt3',
        58
    );
}

function render_settings(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = Options\get_all();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Myliba Site Settings', 'myliba'); ?></h1>
        <p><?php esc_html_e('These settings control the WordPress migration layer: indexing, fallback SEO, contact delivery, and global brand values.', 'myliba'); ?></p>

        <form method="post" action="options.php">
            <?php settings_fields('myliba_options'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Indexing', 'myliba'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="myliba_options[indexing_enabled]" value="1" <?php checked($options['indexing_enabled'], '1'); ?>>
                            <?php esc_html_e('Allow search engines to index this site', 'myliba'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('Keep this disabled on staging. It also adds X-Robots-Tag: noindex, nofollow.', 'myliba'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-default-locale"><?php esc_html_e('Default locale', 'myliba'); ?></label></th>
                    <td><input class="regular-text" id="myliba-default-locale" name="myliba_options[default_locale]" value="<?php echo esc_attr($options['default_locale']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-locales"><?php esc_html_e('Available locales', 'myliba'); ?></label></th>
                    <td>
                        <textarea class="regular-text" id="myliba-locales" name="myliba_options[available_locales]" rows="3"><?php echo esc_textarea($options['available_locales']); ?></textarea>
                        <p class="description"><?php esc_html_e('One locale per line. Polylang or WPML should own final production routing.', 'myliba'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-contact-email"><?php esc_html_e('Contact recipient', 'myliba'); ?></label></th>
                    <td><input class="regular-text" type="email" id="myliba-contact-email" name="myliba_options[contact_email]" value="<?php echo esc_attr($options['contact_email']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Phone CTA', 'myliba'); ?></th>
                    <td>
                        <input name="myliba_options[phone_label]" value="<?php echo esc_attr($options['phone_label']); ?>" placeholder="+90 553 986 86 99">
                        <input class="regular-text" name="myliba_options[phone_url]" value="<?php echo esc_attr($options['phone_url']); ?>" placeholder="tel:+905539868699">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-organization-name"><?php esc_html_e('Organization name', 'myliba'); ?></label></th>
                    <td><input class="regular-text" id="myliba-organization-name" name="myliba_options[organization_name]" value="<?php echo esc_attr($options['organization_name']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-organization-url"><?php esc_html_e('Organization URL', 'myliba'); ?></label></th>
                    <td><input class="regular-text" type="url" id="myliba-organization-url" name="myliba_options[organization_url]" value="<?php echo esc_attr($options['organization_url']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-primary-cta-label"><?php esc_html_e('Primary CTA', 'myliba'); ?></label></th>
                    <td>
                        <input id="myliba-primary-cta-label" name="myliba_options[primary_cta_label]" value="<?php echo esc_attr($options['primary_cta_label']); ?>" placeholder="Label">
                        <input class="regular-text" name="myliba_options[primary_cta_url]" value="<?php echo esc_attr($options['primary_cta_url']); ?>" placeholder="/contact/">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Demo CTA', 'myliba'); ?></th>
                    <td>
                        <input name="myliba_options[demo_cta_label]" value="<?php echo esc_attr($options['demo_cta_label']); ?>" placeholder="Request a demo">
                        <input class="regular-text" name="myliba_options[demo_url]" value="<?php echo esc_attr($options['demo_url']); ?>" placeholder="/en/demo/">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-footer-note"><?php esc_html_e('Footer note', 'myliba'); ?></label></th>
                    <td><textarea class="regular-text" id="myliba-footer-note" name="myliba_options[footer_note]" rows="3"><?php echo esc_textarea($options['footer_note']); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Social links', 'myliba'); ?></th>
                    <td>
                        <input class="regular-text" type="url" name="myliba_options[linkedin_url]" value="<?php echo esc_attr($options['linkedin_url']); ?>" placeholder="LinkedIn URL">
                        <br><br>
                        <input class="regular-text" type="url" name="myliba_options[instagram_url]" value="<?php echo esc_attr($options['instagram_url']); ?>" placeholder="Instagram URL">
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function admin_notices(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!Options\indexing_enabled()) {
        echo '<div class="notice notice-warning"><p><strong>Myliba:</strong> ' . esc_html__('Indexing is currently disabled. This is correct for staging, but must be enabled before final launch.', 'myliba') . '</p></div>';
    }

    if (!function_exists('pll_the_languages') && !defined('ICL_SITEPRESS_VERSION')) {
        echo '<div class="notice notice-info"><p><strong>Myliba:</strong> ' . esc_html__('Install Polylang or WPML before production to manage multilingual URLs, hreflang, and translation relations cleanly.', 'myliba') . '</p></div>';
    }
}

function dashboard_counts(): void
{
    $types = [
        'myliba_product' => __('Products', 'myliba'),
        'myliba_solution' => __('Solutions', 'myliba'),
        'myliba_academy' => __('Academy Programs', 'myliba'),
        'myliba_event' => __('Events', 'myliba'),
        'myliba_team' => __('Team Members', 'myliba'),
        'myliba_submission' => __('Form Submissions', 'myliba'),
    ];

    foreach ($types as $type => $label) {
        $count = wp_count_posts($type);
        $total = $type === 'myliba_submission'
            ? (int) (($count->private ?? 0) + ($count->publish ?? 0))
            : (int) ($count->publish ?? 0);
        echo '<li>' . esc_html($total . ' ' . $label) . '</li>';
    }
}
