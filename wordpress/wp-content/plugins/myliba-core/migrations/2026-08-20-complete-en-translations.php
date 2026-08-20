<?php

if (!defined('ABSPATH') || !defined('WP_CLI') || !WP_CLI) {
    exit;
}

/**
 * Translate any Turkish copy left in the freshly rebuilt English records.
 * HTML structure, structured-content JSON, media IDs, URLs and layout values
 * are preserved. The public Google Translate endpoint is only used for text
 * that passes the Turkish-language detector.
 */

$post_types = ['page', 'post', 'myliba_solution', 'myliba_academy', 'myliba_event', 'myliba_ebook', 'myliba_report', 'myliba_landing'];
$cache = [];
$translated_strings = 0;

$is_turkish = static function (string $text): bool {
    $plain = html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if (preg_match('/[çğıöşüÇĞİÖŞÜ]/u', $plain)) {
        return true;
    }
    return (bool) preg_match('/\b(ve|veya|icin|için|ile|bir|bu|sirket|şirket|kurum|kurumsal|calisan|calisanlar|çalışan|hedef|gelisim|gelişim|yolculugu|kultur|kültür|yonetim|yönetim|geri|bildirim|danismanlik|danışmanlık|performans|performansi|uygulama|uygula|egitim|eğitim|surekli|sürekli|olarak|isteyen|uygundur|aksiyon|aksiyona|birleştirir|takip|olculebilir|ölçülebilir|hizli|hızlı|daha|yillik|yıllık|olduğunda|ekipler|lider|liderlik|neden|akademi|topluluk|strateji|rol|bazli|kazanim|ust|insan|kaynaklari|ofisi|takim|gorunurluk|donusturun|saat|etkinlikler|yorumla|pratik|kitap|kitaplar|iletisime|gecin|talep|edin)\b/ui', $plain);
};

$translate_plain = static function (string $text) use (&$translate_plain, &$cache, &$translated_strings, $is_turkish): string {
    $trimmed = trim($text);
    if ($trimmed === '' || !$is_turkish($trimmed)) {
        return $text;
    }
    if (isset($cache[$text])) {
        return $cache[$text];
    }

    // Preserve newline-driven lists and the builder's "Title | Body" syntax.
    if (str_contains($text, "\n")) {
        $lines = preg_split('/(\r?\n)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        foreach ($lines ?: [] as $line) {
            $result .= preg_match('/^\r?\n$/', $line) ? $line : $translate_plain($line);
        }
        return $cache[$text] = $result;
    }
    if (str_contains($text, ' | ')) {
        $parts = explode(' | ', $text);
        foreach ($parts as &$part) {
            $part = $translate_plain($part);
        }
        unset($part);
        return $cache[$text] = implode(' | ', $parts);
    }

    $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=tr&tl=en&dt=t&q=' . rawurlencode($text);
    $response = null;
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $response = wp_remote_get($url, ['timeout' => 30, 'user-agent' => 'Myliba WordPress translation migration']);
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            break;
        }
    }
    if (is_wp_error($response)) {
        WP_CLI::warning('Translation skipped after 3 attempts: ' . $response->get_error_message());
        return $cache[$text] = $text;
    }
    if (wp_remote_retrieve_response_code($response) !== 200) {
        WP_CLI::warning('Translation skipped after HTTP ' . wp_remote_retrieve_response_code($response) . ': ' . mb_substr($text, 0, 80));
        return $cache[$text] = $text;
    }
    $payload = json_decode((string) wp_remote_retrieve_body($response), true);
    $translated = '';
    foreach (($payload[0] ?? []) as $segment) {
        $translated .= is_array($segment) ? (string) ($segment[0] ?? '') : '';
    }
    if ($translated === '') {
        WP_CLI::warning('Translation service returned an empty response for: ' . mb_substr($text, 0, 120));
        return $cache[$text] = $text;
    }
    $translated_strings++;
    return $cache[$text] = $translated;
};

$translate_html = static function (string $html) use ($translate_plain, $is_turkish): string {
    if ($html === '' || !$is_turkish($html) || !str_contains($html, '<')) {
        return $translate_plain($html);
    }
    $dom = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?><div id="myliba-translation-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    if (!$loaded) {
        return $translate_plain($html);
    }
    $xpath = new DOMXPath($dom);
    foreach ($xpath->query('//text()[not(ancestor::script) and not(ancestor::style)]') ?: [] as $node) {
        if ($node instanceof DOMText && $is_turkish($node->nodeValue)) {
            $node->nodeValue = $translate_plain($node->nodeValue);
        }
    }
    $root = $dom->getElementById('myliba-translation-root');
    if (!$root) {
        return $html;
    }
    $output = '';
    foreach ($root->childNodes as $child) {
        $output .= $dom->saveHTML($child);
    }
    return $output;
};

$translate_value = static function ($value) use (&$translate_value, $translate_plain, $translate_html, $is_turkish) {
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            if (is_string($item) && (strtolower((string) $key) === 'url' || str_ends_with(strtolower((string) $key), '_url'))) {
                $value[$key] = str_replace('/tr/', '/en/', $item);
            } else {
                $value[$key] = $translate_value($item);
            }
        }
        return $value;
    }
    if (is_string($value) && str_contains($value, '/tr/')) {
        $value = str_replace('/tr/', '/en/', $value);
    }
    if (!is_string($value) || !$is_turkish($value)) {
        return $value;
    }
    if (str_starts_with(ltrim($value), '{') || str_starts_with(ltrim($value), '[')) {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return wp_slash(wp_json_encode($translate_value($decoded), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }
    return str_contains($value, '<') ? $translate_html($value) : $translate_plain($value);
};

$skip_meta = static function (string $key): bool {
    if (in_array($key, ['_myliba_language', '_myliba_translation_key', '_myliba_source_url', '_edit_lock', '_edit_last', '_wp_page_template', '_thumbnail_id'], true)) {
        return true;
    }
    return (bool) preg_match('/(_image|_url|_order|_layout|_builder|_status|_date|_hide|_noindex)$/', $key);
};

$updated_posts = 0;
$items = get_posts([
    'post_type' => $post_types,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_key' => '_myliba_language',
    'meta_value' => 'en',
    'suppress_filters' => true,
]);
foreach ($items as $item) {
    if (\Myliba\Core\PageContent\schema_for_post($item) !== null) {
        \Myliba\Core\PageContent\materialize($item->ID);
    }
}
foreach ($items as $item) {
    $changed = false;
    $post_update = ['ID' => $item->ID];
    foreach (['post_title', 'post_content', 'post_excerpt'] as $field) {
        $value = (string) $item->{$field};
        $translated = $field === 'post_content' ? $translate_html($value) : $translate_plain($value);
        if ($translated !== $value) {
            $post_update[$field] = $translated;
            $changed = true;
        }
    }
    if ($changed) {
        wp_update_post(wp_slash($post_update));
    }
    foreach (get_post_meta($item->ID) as $meta_key => $values) {
        if ($skip_meta($meta_key)) {
            continue;
        }
        $value = maybe_unserialize($values[0] ?? '');
        $translated = $translate_value($value);
        if ($translated !== $value) {
            update_post_meta($item->ID, $meta_key, $translated);
            $changed = true;
        }
    }
    if ($changed) {
        $updated_posts++;
    }
}

WP_CLI::success(sprintf('Completed %d English text translations across %d records.', $translated_strings, $updated_posts));
