<?php
/**
 * Plugin Name: Myliba Core
 * Description: Site-specific content model, SEO controls, multilingual hooks, and contact submissions for Myliba.
 * Version: 0.1.0
 * Author: Myliba
 */

namespace Myliba\Core;

if (!defined('ABSPATH')) {
    exit;
}

const VERSION = '0.1.0';

define('MYLIBA_CORE_FILE', __FILE__);
define('MYLIBA_CORE_DIR', plugin_dir_path(__FILE__));
define('MYLIBA_CORE_URL', plugin_dir_url(__FILE__));

$myliba_core_files = [
    'includes/options.php',
    'includes/post-types.php',
    'includes/meta.php',
    'includes/admin.php',
    'includes/seo.php',
    'includes/forms.php',
    'includes/wp-cli.php',
];

foreach ($myliba_core_files as $myliba_core_file) {
    require_once MYLIBA_CORE_DIR . $myliba_core_file;
}

function boot(): void
{
    Options\boot();
    PostTypes\boot();
    Meta\boot();
    Admin\boot();
    SEO\boot();
    Forms\boot();

    if (defined('WP_CLI') && WP_CLI) {
        \WP_CLI::add_command('myliba', CLI\Commands::class);
    }
}
add_action('plugins_loaded', __NAMESPACE__ . '\\boot');

function activate(): void
{
    Options\ensure_defaults();
    PostTypes\register();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\\activate');

function deactivate(): void
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\\deactivate');

