<?php

namespace Myliba\Core\Images;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Allowed MIME types: WebP and SVG only.
 */
const ALLOWED_MIME_TYPES = [
    'webp' => 'image/webp',
    'svg'  => 'image/svg+xml',
];

function boot(): void
{
    add_filter('upload_mimes',               __NAMESPACE__ . '\\restrict_upload_mimes');
    add_filter('wp_handle_upload_prefilter', __NAMESPACE__ . '\\validate_upload');
    add_filter('wp_check_filetype_and_ext',  __NAMESPACE__ . '\\check_filetype_and_ext', 10, 4);
    add_action('admin_notices',              __NAMESPACE__ . '\\admin_upload_notice');
}

/**
 * Restrict allowed upload MIME types to WebP and SVG only.
 *
 * @param  array<string,string> $mimes
 * @return array<string,string>
 */
function restrict_upload_mimes(array $mimes): array
{
    $allowed = [
        'webp' => ALLOWED_MIME_TYPES['webp'],
    ];

    // SVG is an active document format. Limit it to administrators even
    // though every accepted file is sanitized below.
    if (current_user_can('manage_options')) {
        $allowed['svg'] = ALLOWED_MIME_TYPES['svg'];
    }

    return $allowed;
}

/**
 * Validate uploads before processing; reject anything other than WebP/SVG.
 *
 * @param  array{name:string,type:string,tmp_name:string,error:int,size:int} $file
 * @return array{name:string,type:string,tmp_name:string,error:int,size:int}
 */
function validate_upload(array $file): array
{
    $ext = strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['webp', 'svg'], true)) {
        $file['error'] = sprintf(
            /* translators: %s: uploaded file extension */
            __('"%s" dosya türü desteklenmiyor. Yalnızca WebP ve SVG görselleri yükleyebilirsiniz.', 'myliba'),
            esc_html(strtoupper($ext))
        );
        return $file;
    }

    if ($ext === 'svg' && !current_user_can('manage_options')) {
        $file['error'] = __('SVG yükleme yetkisi yalnızca yöneticilere açıktır.', 'myliba');
        return $file;
    }

    if ($ext === 'svg' && (int) ($file['size'] ?? 0) > 1024 * 1024) {
        $file['error'] = __('SVG dosyası en fazla 1 MB olabilir.', 'myliba');
        return $file;
    }

    // Extra safety: check actual MIME for WebP.
    if ($ext === 'webp' && !empty($file['tmp_name'])) {
        $mime = mime_content_type($file['tmp_name']);
        if ($mime !== 'image/webp') {
            $file['error'] = __('Dosya içeriği gerçek bir WebP görseli değil.', 'myliba');
            return $file;
        }
    }

    // Sanitize SVG content to strip potential XSS.
    if ($ext === 'svg' && !empty($file['tmp_name'])) {
        $result = sanitize_svg_file($file['tmp_name']);
        if (is_wp_error($result)) {
            $file['error'] = $result->get_error_message();
        }
    }

    return $file;
}

/**
 * Ensure WordPress's filetype check passes for WebP and SVG.
 *
 * @param  array{ext:string|false,type:string|false,proper_filename:string|false} $checked
 * @param  string  $file
 * @param  string  $filename
 * @param  array<string,string>|null $mimes
 * @return array{ext:string|false,type:string|false,proper_filename:string|false}
 */
function check_filetype_and_ext(array $checked, string $file, string $filename, ?array $mimes): array
{
    $ext = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));

    if ($ext === 'svg' && current_user_can('manage_options') && ($checked['type'] === false || $checked['ext'] === false)) {
        $checked['ext']  = 'svg';
        $checked['type'] = 'image/svg+xml';
    }

    if ($ext === 'webp' && ($checked['type'] === false || $checked['ext'] === false)) {
        $checked['ext']  = 'webp';
        $checked['type'] = 'image/webp';
    }

    return $checked;
}

/**
 * Sanitize an SVG file by stripping dangerous elements and attributes.
 *
 * @return true|\WP_Error
 */
function sanitize_svg_file(string $tmp_path)
{
    $content = file_get_contents($tmp_path);

    if ($content === false || trim($content) === '') {
        return new \WP_Error('svg_empty', __('SVG dosyası boş veya okunamıyor.', 'myliba'));
    }

    // Check for PHP open tags (extra safety).
    if (str_contains($content, '<?php') || str_contains($content, '<?=')) {
        return new \WP_Error('svg_php', __('SVG dosyası PHP kodu içeriyor.', 'myliba'));
    }

    $dom = new \DOMDocument();
    $dom->formatOutput = false;

    $previous_loader = null;
    if (function_exists('libxml_disable_entity_loader')) {
        $previous_loader = @libxml_disable_entity_loader(true);
    }

    libxml_use_internal_errors(true);
    $loaded = $dom->loadXML($content, LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors(false);

    if ($previous_loader !== null) {
        @libxml_disable_entity_loader($previous_loader);
    }

    if (!$loaded) {
        return new \WP_Error('svg_invalid', __('SVG dosyası geçerli bir XML belgesi değil.', 'myliba'));
    }

    $root = $dom->documentElement;
    if (!$root || strtolower($root->localName) !== 'svg') {
        return new \WP_Error('svg_no_root', __('SVG dosyasının kök öğesi &lt;svg&gt; değil.', 'myliba'));
    }

    // Remove dangerous elements.
    $dangerous_elements = [
        'script',
        'style',
        'foreignobject',
        'iframe',
        'object',
        'embed',
        'audio',
        'video',
        'animate',
        'animatemotion',
        'animatetransform',
        'set',
        'discard',
        'feimage',
    ];
    $elements_to_remove = [];

    // Remove dangerous attributes from all elements.
    $all_elements = $dom->getElementsByTagName('*');
    $dangerous_attrs = [];
    foreach ($all_elements as $element) {
        if (!($element instanceof \DOMElement)) {
            continue;
        }

        if (in_array(strtolower($element->localName), $dangerous_elements, true)) {
            $elements_to_remove[] = $element;
            continue;
        }

        for ($i = $element->attributes->length - 1; $i >= 0; $i--) {
            $attr = $element->attributes->item($i);
            if (!$attr) {
                continue;
            }
            $attr_name = strtolower($attr->nodeName);
            $attr_value = strtolower(html_entity_decode((string) $attr->nodeValue, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $has_unsafe_url = str_contains($attr_value, 'url(')
                && !preg_match('/^\s*url\(\s*["\']?#[a-z0-9_.:-]+["\']?\s*\)\s*$/i', $attr_value);

            // Remove event handlers, links, inline CSS and external resource
            // references. Fragment-only paint references such as url(#id)
            // remain available for gradients and clip paths.
            if (
                str_starts_with($attr_name, 'on') ||
                in_array($attr_name, ['href', 'xlink:href', 'src', 'action', 'style'], true) ||
                preg_match('/(?:javascript|vbscript)\s*:|data\s*:\s*text\/html/i', $attr_value) ||
                str_contains($attr_value, '@import') ||
                str_contains($attr_value, 'expression(') ||
                $has_unsafe_url
            ) {
                $dangerous_attrs[] = [$element, $attr->nodeName];
            }
        }
    }
    foreach ($elements_to_remove as $element) {
        if ($element->parentNode) {
            $element->parentNode->removeChild($element);
        }
    }

    foreach ($dangerous_attrs as [$element, $attr_name]) {
        $element->removeAttribute($attr_name);
    }

    $sanitized = $dom->saveXML($root);
    if ($sanitized === false) {
        return new \WP_Error('svg_save', __('SVG içeriği kaydedilemedi.', 'myliba'));
    }

    if (file_put_contents($tmp_path, $sanitized, LOCK_EX) === false) {
        return new \WP_Error('svg_write', __('Temizlenen SVG dosyası kaydedilemedi.', 'myliba'));
    }

    return true;
}

/**
 * Show an informational notice on the media upload page.
 */
function admin_upload_notice(): void
{
    $screen = get_current_screen();

    if (!$screen || !in_array($screen->id, ['upload', 'media'], true)) {
        return;
    }

    echo '<div class="notice notice-info is-dismissible"><p>'
        . esc_html__('Myliba: Yalnızca WebP görselleri yüklenebilir; SVG yükleme yalnızca yöneticilere açıktır. PNG, JPG ve diğer formatlar reddedilecektir.', 'myliba')
        . '</p></div>';
}
