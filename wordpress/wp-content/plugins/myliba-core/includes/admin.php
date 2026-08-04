<?php

namespace Myliba\Core\Admin;

use Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

function boot(): void
{
    add_action('admin_menu', __NAMESPACE__ . '\\register_menu');
    add_action('admin_menu', __NAMESPACE__ . '\\simplify_admin_menu', 999);
    add_action('wp_dashboard_setup', __NAMESPACE__ . '\\simplify_dashboard', 999);
    add_action('admin_bar_menu', __NAMESPACE__ . '\\simplify_admin_bar', 999);
    add_action('admin_notices', __NAMESPACE__ . '\\admin_notices');
    add_action('dashboard_glance_items', __NAMESPACE__ . '\\dashboard_counts');
    add_filter('use_block_editor_for_post_type', __NAMESPACE__ . '\\use_classic_editor_for_myliba_content', 10, 2);
}

function simplify_admin_menu(): void
{
    remove_menu_page('edit-comments.php');

    global $submenu;
    if (empty($submenu['myliba-settings']) || !is_array($submenu['myliba-settings'])) {
        return;
    }

    $preferred_order = [
        'myliba-settings',
        'myliba-content',
        'edit.php?post_type=myliba_product',
        'edit.php?post_type=myliba_solution',
        'edit.php?post_type=myliba_academy',
        'edit.php?post_type=myliba_landing',
        'edit.php?post_type=myliba_event',
        'edit.php?post_type=myliba_ebook',
        'edit.php?post_type=myliba_report',
        'edit.php?post_type=myliba_faq',
        'edit.php?post_type=myliba_client_logo',
        'edit.php?post_type=myliba_testimonial',
        'edit.php?post_type=myliba_submission',
    ];
    $positions = array_flip($preferred_order);
    usort($submenu['myliba-settings'], static function (array $left, array $right) use ($positions): int {
        return ($positions[$left[2]] ?? 999) <=> ($positions[$right[2]] ?? 999);
    });
}

function simplify_dashboard(): void
{
    remove_action('welcome_panel', 'wp_welcome_panel');
    remove_meta_box('dashboard_welcome', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
}

function simplify_admin_bar(\WP_Admin_Bar $admin_bar): void
{
    $admin_bar->remove_node('comments');
}

function use_classic_editor_for_myliba_content(bool $use_block_editor, string $post_type): bool
{
    $classic_post_types = [
        'page',
        'myliba_product',
        'myliba_solution',
        'myliba_academy',
        'myliba_case_study',
        'myliba_testimonial',
        'myliba_faq',
        'myliba_landing',
        'myliba_event',
        'myliba_ebook',
        'myliba_report',
        'myliba_team',
        'myliba_client_logo',
    ];

    if (in_array($post_type, $classic_post_types, true)) {
        return false;
    }

    return $use_block_editor;
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

    add_submenu_page(
        'myliba-settings',
        __('Myliba Site Settings', 'myliba'),
        __('General Settings', 'myliba'),
        'manage_options',
        'myliba-settings',
        __NAMESPACE__ . '\\render_settings'
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
                        <input class="regular-text" name="myliba_options[demo_url]" value="<?php echo esc_attr($options['demo_url']); ?>" placeholder="/tr/demo/">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Promo banner', 'myliba'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="myliba_options[promo_enabled]" value="1" <?php checked($options['promo_enabled'], '1'); ?>>
                            <?php esc_html_e('Show the 50px global announcement bar above the header', 'myliba'); ?>
                        </label>
                        <p>
                            <input name="myliba_options[promo_left_text]" value="<?php echo esc_attr($options['promo_left_text']); ?>" placeholder="Backed by Plug and Play">
                            <input class="regular-text" name="myliba_options[promo_message]" value="<?php echo esc_attr($options['promo_message']); ?>" placeholder="Campaign message">
                            <input name="myliba_options[promo_right_text]" value="<?php echo esc_attr($options['promo_right_text']); ?>" placeholder="Endeavor">
                        </p>
                        <p>
                            <input class="regular-text" type="url" name="myliba_options[promo_url]" value="<?php echo esc_attr($options['promo_url']); ?>" placeholder="https://...">
                        </p>
                        <p class="description"><?php esc_html_e('Add the workshop, event, or campaign URL to make the full announcement clickable.', 'myliba'); ?></p>
                        <label>
                            <input type="checkbox" name="myliba_options[promo_dismissible]" value="1" <?php checked($options['promo_dismissible'], '1'); ?>>
                            <?php esc_html_e('Allow visitors to dismiss it for the current browser session', 'myliba'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-footer-note"><?php esc_html_e('Footer note', 'myliba'); ?></label></th>
                    <td><textarea class="regular-text" id="myliba-footer-note" name="myliba_options[footer_note]" rows="3"><?php echo esc_textarea($options['footer_note']); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="myliba-footer-cta-title"><?php esc_html_e('Footer CTA title', 'myliba'); ?></label></th>
                    <td><input class="regular-text" id="myliba-footer-cta-title" name="myliba_options[footer_cta_title]" value="<?php echo esc_attr($options['footer_cta_title']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('TR / EN global content', 'myliba'); ?></th>
                    <td>
                        <p class="description"><?php esc_html_e('Optional language-specific values. When left blank, the existing value and built-in translation remain active.', 'myliba'); ?></p>
                        <div style="display:grid;gap:16px;margin-top:12px;max-width:980px">
                            <?php
                            $localized_labels = [
                                'demo_cta_label' => __('Demo button label', 'myliba'),
                                'demo_url' => __('Demo URL', 'myliba'),
                                'footer_cta_title' => __('Footer CTA title', 'myliba'),
                                'footer_note' => __('Footer note', 'myliba'),
                                'primary_cta_label' => __('Primary CTA label', 'myliba'),
                                'primary_cta_url' => __('Primary CTA URL', 'myliba'),
                                'promo_left_text' => __('Promo left text', 'myliba'),
                                'promo_message' => __('Promo message', 'myliba'),
                                'promo_right_text' => __('Promo right text', 'myliba'),
                                'promo_url' => __('Promo URL', 'myliba'),
                            ];
                            foreach ($localized_labels as $localized_key => $localized_label) :
                                ?>
                                <div>
                                    <strong style="display:block;margin-bottom:6px"><?php echo esc_html($localized_label); ?></strong>
                                    <div style="display:grid;grid-template-columns:repeat(2,minmax(240px,1fr));gap:10px">
                                        <?php foreach (['en' => 'English', 'tr' => 'Türkçe'] as $locale => $locale_label) :
                                            $option_key = $localized_key . '_' . $locale;
                                            ?>
                                            <label>
                                                <span class="screen-reader-text"><?php echo esc_html($localized_label . ' — ' . $locale_label); ?></span>
                                                <input class="widefat" name="myliba_options[<?php echo esc_attr($option_key); ?>]" value="<?php echo esc_attr($options[$option_key] ?? ''); ?>" placeholder="<?php echo esc_attr($locale_label); ?>">
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Footer link menus', 'myliba'); ?></th>
                    <td>
                        <p class="description">
                            <?php esc_html_e('Use Appearance > Menus to assign custom links to Footer Navigation and Footer Blog Links. Product and page columns are filled automatically from public WordPress content.', 'myliba'); ?>
                        </p>
                    </td>
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
        'myliba_ebook' => 'e-Kitaplar',
        'myliba_report' => 'Raporlar ve Trendler',
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
