<?php

namespace Myliba\Core\LocalDev;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Local development helpers. Nothing here runs outside the `local`
 * environment type, which only the Docker stack declares.
 */
function boot(): void
{
    if (wp_get_environment_type() !== 'local') {
        return;
    }

    add_action('template_redirect', __NAMESPACE__ . '\\redirect_missing_upload', 0);
}

/**
 * After `make pull-live` the database references media that only exists on
 * the production host. Serve those files from production instead of copying
 * the whole uploads folder to every developer machine.
 */
function redirect_missing_upload(): void
{
    if (!is_404()) {
        return;
    }

    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    if (strpos($path, '/wp-content/uploads/') !== 0 || strpos($path, '..') !== false) {
        return;
    }

    $origin = defined('MYLIBA_LIVE_ORIGIN') ? (string) MYLIBA_LIVE_ORIGIN : 'https://www.myliba.com';
    // wp_safe_redirect() refuses external hosts; the target origin is fixed above.
    wp_redirect(esc_url_raw(untrailingslashit($origin) . $path), 302);
    exit;
}
