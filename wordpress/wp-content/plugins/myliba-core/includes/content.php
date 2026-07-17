<?php

namespace Myliba\Core\Content;

if (!defined('ABSPATH')) {
    exit;
}

const OPTION_NAME = 'myliba_content_overrides';

function boot(): void
{
    add_action('admin_menu', __NAMESPACE__ . '\\register_menu', 20);
    add_action('admin_post_myliba_save_content_overrides', __NAMESPACE__ . '\\save_overrides');
}

function register_menu(): void
{
    add_submenu_page(
        'myliba-settings',
        __('Site Texts — TR / EN', 'myliba'),
        __('Site Texts — TR / EN', 'myliba'),
        'manage_options',
        'myliba-content',
        __NAMESPACE__ . '\\render_page'
    );
}

function all_overrides(): array
{
    $overrides = get_option(OPTION_NAME, []);
    return is_array($overrides) ? $overrides : [];
}

function override(string $source, string $locale): ?string
{
    $overrides = all_overrides();
    $value = $overrides[$source][$locale] ?? null;
    return is_string($value) && trim($value) !== '' ? $value : null;
}

function save_overrides(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to manage these settings.', 'myliba'));
    }

    check_admin_referer('myliba_content_overrides');
    $sources = isset($_POST['sources']) && is_array($_POST['sources']) ? wp_unslash($_POST['sources']) : [];
    $values = isset($_POST['overrides']) && is_array($_POST['overrides']) ? wp_unslash($_POST['overrides']) : [];
    $saved = [];

    foreach ($sources as $key => $source) {
        $source = is_string($source) ? sanitize_text_field($source) : '';
        if ($source === '' || !isset($values[$key]) || !is_array($values[$key])) {
            continue;
        }

        foreach (['en', 'tr'] as $locale) {
            $value = isset($values[$key][$locale]) && is_string($values[$key][$locale])
                ? sanitize_textarea_field($values[$key][$locale])
                : '';
            if ($value !== '') {
                $saved[$source][$locale] = $value;
            }
        }
    }

    update_option(OPTION_NAME, $saved, false);
    wp_safe_redirect(add_query_arg(['page' => 'myliba-content', 'updated' => '1'], admin_url('admin.php')));
    exit;
}

function frontend_catalog(): array
{
    $paths = array_merge(
        glob(get_template_directory() . '/*.php') ?: [],
        glob(get_template_directory() . '/template-parts/*.php') ?: [],
        [MYLIBA_CORE_DIR . 'includes/forms.php']
    );
    $strings = [];
    $pattern = '~(?:__|_e|esc_html_e|esc_attr_e|esc_html__|esc_attr__)\\(\\s*(["\'])((?:\\\\.|(?!\\1).)*)\\1\\s*,\\s*["\']myliba["\']~s';

    foreach (array_unique($paths) as $path) {
        if (!is_readable($path)) {
            continue;
        }
        $contents = file_get_contents($path);
        if (!is_string($contents) || !preg_match_all($pattern, $contents, $matches)) {
            continue;
        }
        foreach ($matches[2] as $match) {
            $value = stripcslashes((string) $match);
            if ($value !== '') {
                $strings[$value] = $value;
            }
        }
    }

    natcasesort($strings);
    return array_values($strings);
}

function render_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $catalog = frontend_catalog();
    $overrides = all_overrides();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Site Texts — Turkish / English', 'myliba'); ?></h1>
        <p><?php esc_html_e('This screen covers reusable interface text in the header, footer, archives, blog, forms, 404 page, and content templates. Leave a field blank to use the built-in text.', 'myliba'); ?></p>
        <p><strong><?php esc_html_e('Page-specific copy:', 'myliba'); ?></strong> <?php esc_html_e('Edit the Turkish and English page/post separately. Homepage content is under Pages → the relevant language homepage → Myliba Homepage Sections.', 'myliba'); ?></p>
        <p><strong><?php esc_html_e('Images:', 'myliba'); ?></strong> <?php esc_html_e('Use Featured Image for pages, posts, products, solutions, academy items, team members and client logos. Use Settings → General → Site Icon for the favicon and Appearance → Customize → Site Identity for the logo.', 'myliba'); ?></p>
        <?php if (isset($_GET['updated'])) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('Site texts saved.', 'myliba'); ?></p></div><?php endif; ?>

        <p><input id="myliba-content-search" class="regular-text" type="search" placeholder="<?php esc_attr_e('Search site text…', 'myliba'); ?>"></p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="myliba_save_content_overrides">
            <?php wp_nonce_field('myliba_content_overrides'); ?>
            <table class="widefat striped" id="myliba-content-table">
                <thead><tr><th style="width:34%"><?php esc_html_e('Built-in English source', 'myliba'); ?></th><th><?php esc_html_e('English override', 'myliba'); ?></th><th><?php esc_html_e('Turkish override', 'myliba'); ?></th></tr></thead>
                <tbody>
                <?php foreach ($catalog as $index => $source) :
                    $key = 's' . $index;
                    ?>
                    <tr>
                        <td><code style="white-space:normal"><?php echo esc_html($source); ?></code><input type="hidden" name="sources[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($source); ?>"></td>
                        <td><textarea class="widefat" rows="2" name="overrides[<?php echo esc_attr($key); ?>][en]"><?php echo esc_textarea($overrides[$source]['en'] ?? ''); ?></textarea></td>
                        <td><textarea class="widefat" rows="2" name="overrides[<?php echo esc_attr($key); ?>][tr]"><?php echo esc_textarea($overrides[$source]['tr'] ?? ''); ?></textarea></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php submit_button(__('Save site texts', 'myliba')); ?>
        </form>
    </div>
    <script>
        document.getElementById('myliba-content-search').addEventListener('input', function () {
            var query = this.value.toLocaleLowerCase();
            document.querySelectorAll('#myliba-content-table tbody tr').forEach(function (row) {
                row.hidden = query !== '' && !row.textContent.toLocaleLowerCase().includes(query);
            });
        });
    </script>
    <?php
}
