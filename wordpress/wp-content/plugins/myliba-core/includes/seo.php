<?php

namespace Myliba\Core\SEO;

use Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

function boot(): void
{
    add_filter('wp_robots', __NAMESPACE__ . '\\robots');
    add_filter('robots_txt', __NAMESPACE__ . '\\robots_txt', 10, 2);
    add_filter('wp_sitemaps_enabled', __NAMESPACE__ . '\\sitemaps_enabled');
    add_filter('wp_sitemaps_post_types', __NAMESPACE__ . '\\sitemap_post_types');
    add_filter('wp_sitemaps_add_provider', __NAMESPACE__ . '\\sitemap_provider', 10, 2);
    add_filter('wp_sitemaps_posts_query_args', __NAMESPACE__ . '\\sitemap_post_query_args', 10, 2);
    add_filter('document_title_parts', __NAMESPACE__ . '\\document_title');
    add_action('template_redirect', __NAMESPACE__ . '\\redirect_legacy_locale_duplicate', 0);
    add_action('template_redirect', __NAMESPACE__ . '\\render_llms_txt', 0);
    add_action('send_headers', __NAMESPACE__ . '\\send_noindex_header');
    add_action('wp_head', __NAMESPACE__ . '\\render_head', 2);
    add_action('admin_notices', __NAMESPACE__ . '\\external_seo_plugin_notice');
}

function seo_plugin_active(): bool
{
    return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION');
}

function external_seo_plugin_notice(): void
{
    if (!seo_plugin_active() || !current_user_can('manage_options')) {
        return;
    }

    echo '<div class="notice notice-warning"><p>'
        . esc_html__('An external SEO plugin is active. Myliba avoids duplicate meta tags, so manage titles and descriptions in that plugin or deactivate it to use the Myliba SEO fields.', 'myliba')
        . '</p></div>';
}

function current_post_noindex(): bool
{
    if (!is_singular()) {
        return false;
    }

    return get_post_meta(get_queried_object_id(), '_myliba_noindex', true) === '1';
}

function staging_hosts(): array
{
    return apply_filters('myliba_staging_hosts', [
        'test-web.myliba.com',
    ]);
}

function current_host(): string
{
    $host = !empty($_SERVER['HTTP_HOST']) ? wp_unslash($_SERVER['HTTP_HOST']) : '';

    if (!$host) {
        $host = wp_parse_url(home_url('/'), PHP_URL_HOST);
    }

    return strtolower((string) preg_replace('/:\d+$/', '', (string) $host));
}

function is_staging_host(): bool
{
    return in_array(current_host(), array_map('strtolower', staging_hosts()), true);
}

function should_noindex(): bool
{
    return is_staging_host() || !Options\indexing_enabled() || current_post_noindex();
}

function should_noindex_soft(): bool
{
    return !is_admin() && (is_author() || is_date() || is_search() || !empty($_GET['myliba_form']));
}

function robots(array $robots): array
{
    if (should_noindex()) {
        unset($robots['index'], $robots['follow']);
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
    } elseif (should_noindex_soft()) {
        unset($robots['index']);
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }

    return $robots;
}

function robots_txt(string $output, bool $public): string
{
    if (is_staging_host() || !Options\indexing_enabled()) {
        return "User-agent: *\nDisallow: /\n";
    }

    $sitemap_url = home_url('/wp-sitemap.xml');

    return implode("\n", [
        '# ── General Crawlers ─────────────────────────────────────',
        'User-agent: *',
        'Disallow: /wp-admin/',
        'Disallow: /wp-login.php',
        'Disallow: /xmlrpc.php',
        'Disallow: /?s=',
        'Disallow: /*?s=*',
        'Disallow: /*?myliba_form=*',
        'Disallow: /author/',
        'Allow: /wp-admin/admin-ajax.php',
        'Allow: /wp-content/uploads/',
        'Allow: /wp-content/themes/',
        '',
        '# ── OpenAI / ChatGPT & Search ─────────────────────────────',
        'User-agent: GPTBot',
        'Allow: /',
        '',
        'User-agent: OAI-SearchBot',
        'Allow: /',
        '',
        'User-agent: ChatGPT-User',
        'Allow: /',
        '',
        '# ── Perplexity AI ─────────────────────────────────────────',
        'User-agent: PerplexityBot',
        'Allow: /',
        '',
        '# ── Anthropic / Claude ────────────────────────────────────',
        'User-agent: ClaudeBot',
        'Allow: /',
        '',
        'User-agent: Claude-Web',
        'Allow: /',
        '',
        'User-agent: anthropic-ai',
        'Allow: /',
        '',
        '# ── Google Gemini & AI Overviews ──────────────────────────',
        'User-agent: Google-Extended',
        'Allow: /',
        '',
        '# ── Apple Intelligence ────────────────────────────────────',
        'User-agent: Applebot',
        'Allow: /',
        '',
        'User-agent: Applebot-Extended',
        'Allow: /',
        '',
        '# ── Microsoft Copilot / Bing ──────────────────────────────',
        'User-agent: Bingbot',
        'Allow: /',
        '',
        '# ── Sitemap ───────────────────────────────────────────────',
        'Sitemap: ' . esc_url($sitemap_url),
        '',
    ]);
}

function render_llms_txt(): void
{
    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
    $path = trim((string) wp_parse_url($request_uri, PHP_URL_PATH), '/');

    if ($path !== 'llms.txt') {
        return;
    }

    if (should_noindex()) {
        status_header(404);
        exit;
    }

    header('Content-Type: text/plain; charset=utf-8');
    header('X-Robots-Tag: noindex, follow', true);

    $site_url = rtrim(home_url('/'), '/');

    echo "# Myliba\n\n";
    echo "> Myliba connects OKR, KPI, CFR, 1:1 meetings, feedback, actions and academy routines to help organizations build measurable performance and leadership culture.\n\n";
    echo "## Core Solutions & Products\n\n";
    echo "- [Yazılım / Platform]({$site_url}/tr/yazilim/): OKR ve performans yönetimi yazılımı.\n";
    echo "- [Çözümlerimiz]({$site_url}/tr/cozumler/): Kurumsal gelişim programları, danışmanlık ve kültür analizi.\n";
    echo "- [OKR & Kültür Akademisi]({$site_url}/tr/okr-kultur-akademisi/): Sertifikalı OKR ve liderlik eğitimleri.\n";
    echo "- [Gelişim Merkezi]({$site_url}/tr/gelisim-merkezi/): Araştırmalar, e-kitaplar, raporlar ve etkinlikler.\n";
    echo "- [İletişim & Demo]({$site_url}/tr/demo/): Demo talebi ve kurumsal iletişim.\n\n";
    echo "## Resources\n\n";
    echo "- [Raporlar ve Trendler]({$site_url}/tr/gelisim-merkezi/raporlar-ve-trendler/)\n";
    echo "- [e-Kitaplar]({$site_url}/tr/gelisim-merkezi/e-kitaplar/)\n";
    echo "- [Blog]({$site_url}/tr/yazilar/)\n";
    echo "- [Etkinlikler]({$site_url}/tr/etkinlikler/)\n\n";
    echo "## Sitemap\n\n";
    echo "- {$site_url}/wp-sitemap.xml\n";

    exit;
}

function sitemaps_enabled(bool $enabled): bool
{
    return is_staging_host() || !Options\indexing_enabled() ? false : $enabled;
}

function sitemap_post_types(array $post_types): array
{
    foreach (['myliba_solution', 'myliba_academy', 'myliba_landing', 'myliba_ebook'] as $post_type) {
        unset($post_types[$post_type]);
    }

    return $post_types;
}

function sitemap_provider($provider, string $name)
{
    return $name === 'users' ? false : $provider;
}

function send_noindex_header(): void
{
    if (should_noindex()) {
        header('X-Robots-Tag: noindex, nofollow', true);
    } elseif (should_noindex_soft()) {
        header('X-Robots-Tag: noindex, follow', true);
    }
}

function sitemap_post_query_args(array $args, string $post_type): array
{
    $meta_query = isset($args['meta_query']) && is_array($args['meta_query']) ? $args['meta_query'] : [];
    $meta_query[] = [
        'relation' => 'OR',
        ['key' => '_myliba_noindex', 'compare' => 'NOT EXISTS'],
        ['key' => '_myliba_noindex', 'value' => '1', 'compare' => '!='],
    ];
    $args['meta_query'] = $meta_query;

    if ($post_type === 'page') {
        $excluded = isset($args['post__not_in']) && is_array($args['post__not_in']) ? $args['post__not_in'] : [];
        $args['post__not_in'] = array_values(array_unique(array_merge($excluded, legacy_locale_duplicate_ids())));
    }

    return $args;
}

function legacy_locale_duplicate_map(): array
{
    static $map = null;

    if (is_array($map)) {
        return $map;
    }

    $map = [];
    foreach (Options\locales() as $locale) {
        $locale_page = get_page_by_path($locale);
        if (!$locale_page instanceof \WP_Post) {
            continue;
        }

        $localized_pages = get_posts([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_parent' => $locale_page->ID,
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'meta_query' => [
                ['key' => '_myliba_language', 'value' => $locale],
                ['key' => '_myliba_translation_key', 'compare' => 'EXISTS'],
            ],
        ]);

        foreach ($localized_pages as $localized_page) {
            $translation_key = trim((string) get_post_meta($localized_page->ID, '_myliba_translation_key', true));
            if ($translation_key === '') {
                continue;
            }

            $legacy_pages = get_posts([
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_parent' => 0,
                'post__not_in' => [$locale_page->ID],
                'posts_per_page' => -1,
                'no_found_rows' => true,
                'meta_query' => [
                    ['key' => '_myliba_language', 'value' => $locale],
                    ['key' => '_myliba_translation_key', 'value' => $translation_key],
                ],
            ]);

            foreach ($legacy_pages as $legacy_page) {
                $map[(int) $legacy_page->ID] = (int) $localized_page->ID;
            }
        }
    }

    // Earlier migrations also created root-level pages for several landing
    // resources. Prefer the localized custom-post URL when slug and language
    // are an exact match.
    $landing_pages = get_posts([
        'post_type' => 'myliba_landing',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'no_found_rows' => true,
    ]);
    foreach ($landing_pages as $landing_page) {
        $language = (string) get_post_meta($landing_page->ID, '_myliba_language', true);
        $legacy_page = get_page_by_path($landing_page->post_name);
        if (!$legacy_page instanceof \WP_Post || (int) $legacy_page->post_parent !== 0) {
            continue;
        }
        if ((string) get_post_meta($legacy_page->ID, '_myliba_language', true) !== $language) {
            continue;
        }
        $map[(int) $legacy_page->ID] = (int) $landing_page->ID;
    }

    return $map;
}

function legacy_locale_duplicate_ids(): array
{
    return array_map('intval', array_keys(legacy_locale_duplicate_map()));
}

function redirect_legacy_locale_duplicate(): void
{
    if (!is_page() || is_admin() || wp_doing_ajax()) {
        return;
    }

    $map = legacy_locale_duplicate_map();
    $post_id = (int) get_queried_object_id();
    if (!isset($map[$post_id])) {
        return;
    }

    wp_safe_redirect(get_permalink($map[$post_id]), 301);
    exit;
}

function document_title(array $parts): array
{
    if (is_post_type_archive('myliba_ebook') && function_exists('myliba_current_language') && \myliba_current_language() === 'en') {
        $parts['title'] = 'e-Books';

        return $parts;
    }

    if (!is_singular()) {
        return $parts;
    }

    $seo_title = get_post_meta(get_queried_object_id(), '_myliba_seo_title', true);

    if ($seo_title) {
        $parts['title'] = $seo_title;
        unset($parts['site'], $parts['tagline']);
    }

    return $parts;
}

function render_head(): void
{
    if (!seo_plugin_active()) {
        render_fallback_meta();
    }

    render_schema();
}

function render_fallback_meta(): void
{
    if (is_404()) {
        return;
    }

    $description = '';
    $post_id     = is_singular() ? get_queried_object_id() : 0;

    if ($post_id) {
        $description = get_post_meta($post_id, '_myliba_seo_description', true);

        if (!$description) {
            $post        = get_post($post_id);
            $description = $post ? wp_trim_words(wp_strip_all_tags($post->post_excerpt ?: $post->post_content), 28) : '';
        }
    } else {
        $description = get_bloginfo('description');
    }

    if (!$description && (is_front_page() || is_home())) {
        $description = __('Myliba connects OKR, KPI, CFR, 1:1 meetings, feedback, actions and academy routines to help organizations build measurable performance culture.', 'myliba');
    }

    // ── Canonical ──────────────────────────────────────────────────────
    if (!is_404() && !is_search()) {
        printf("<link rel=\"canonical\" href=\"%s\">\n", esc_url(current_url()));
    }

    // ── Description ────────────────────────────────────────────────────
    if ($description) {
        printf("<meta name=\"description\" content=\"%s\">\n", esc_attr($description));
        printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($description));
        printf("<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr($description));
    }

    // ── Open Graph ─────────────────────────────────────────────────────
    $og_locale = function_exists('Myliba\\Core\\Options\\get') && \Myliba\Core\Options\get('default_locale') === 'tr' ? 'tr_TR' : 'en_US';
    if (is_singular()) {
        $lang      = get_post_meta($post_id, '_myliba_language', true);
        $og_locale = $lang === 'tr' ? 'tr_TR' : 'en_US';
    }

    printf("<meta property=\"og:locale\" content=\"%s\">\n", esc_attr($og_locale));
    printf("<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr(get_bloginfo('name')));
    printf("<meta property=\"og:title\" content=\"%s\">\n", esc_attr(wp_get_document_title()));
    printf("<meta property=\"og:url\" content=\"%s\">\n", esc_url(current_url()));
    echo "<meta property=\"og:type\" content=\"" . (is_singular('post') ? 'article' : 'website') . "\">\n";

    // ── Twitter / X Card ───────────────────────────────────────────────
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    printf("<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr(wp_get_document_title()));

    // ── Featured image for OG / Twitter ────────────────────────────────
    $image_id = is_singular() && has_post_thumbnail() ? get_post_thumbnail_id() : (int) get_theme_mod('custom_logo');
    $image = $image_id ? wp_get_attachment_image_src($image_id, 'large') : false;
    if ($image) {
        printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url($image[0]));
        printf("<meta property=\"og:image:width\" content=\"%d\">\n", (int) $image[1]);
        printf("<meta property=\"og:image:height\" content=\"%d\">\n", (int) $image[2]);
        printf("<meta property=\"og:image:alt\" content=\"%s\">\n", esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name')));
        printf("<meta name=\"twitter:image\" content=\"%s\">\n", esc_url($image[0]));
    }

    if (!is_404() && !is_search()) {
        render_hreflang();
    }
}

function render_hreflang(): void
{
    if (function_exists('pll_the_languages')) {
        // Polylang handles hreflang — defer to it.
        $languages = pll_the_languages(['raw' => 1]);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                if (!empty($language['url']) && !empty($language['slug'])) {
                    printf("<link rel=\"alternate\" hreflang=\"%s\" href=\"%s\">\n", esc_attr($language['slug']), esc_url($language['url']));
                }
            }
        }
        return;
    }

    if (is_page()) {
        $current_page = get_post(get_queried_object_id());
        if ($current_page && in_array($current_page->post_name, ['tr', 'en'], true)) {
            foreach (Options\locales() as $locale) {
                $locale_page = get_page_by_path($locale);
                if ($locale_page && get_post_status($locale_page) === 'publish') {
                    printf(
                        "<link rel=\"alternate\" hreflang=\"%s\" href=\"%s\">\n",
                        esc_attr($locale),
                        esc_url(home_url('/' . $locale . '/'))
                    );
                }
            }
            printf("<link rel=\"alternate\" hreflang=\"x-default\" href=\"%s\">\n", esc_url(home_url('/')));
            return;
        }
    }

    $current_id = is_singular() ? (int) get_queried_object_id() : 0;
    $current_lang = $current_id
        ? (get_post_meta($current_id, '_myliba_language', true) ?: Options\get('default_locale', 'tr'))
        : Options\get('default_locale', 'tr');
    $translation_key = $current_id ? trim((string) get_post_meta($current_id, '_myliba_translation_key', true)) : '';
    $alternates = [];

    if ($current_id && $translation_key !== '') {
        $peers = get_posts([
            'post_type' => get_post_type($current_id) ?: 'any',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'meta_key' => '_myliba_translation_key',
            'meta_value' => $translation_key,
        ]);

        foreach ($peers as $peer) {
            $peer_lang = (string) get_post_meta($peer->ID, '_myliba_language', true);
            if (!in_array($peer_lang, Options\locales(), true) || isset($alternates[$peer_lang])) {
                continue;
            }

            if ($peer->post_type === 'page') {
                $locale_page = get_page_by_path($peer_lang);
                $is_locale_home = $locale_page instanceof \WP_Post && (int) $peer->ID === (int) $locale_page->ID;
                $is_locale_child = $locale_page instanceof \WP_Post && (int) $peer->post_parent === (int) $locale_page->ID;
                if (!$is_locale_home && !$is_locale_child) {
                    continue;
                }
            }

            $alternates[$peer_lang] = get_permalink($peer);
        }
    }

    if (!$alternates) {
        $alternates[(string) $current_lang] = current_url();
    }

    foreach ($alternates as $locale => $url) {
        printf("<link rel=\"alternate\" hreflang=\"%s\" href=\"%s\">\n", esc_attr($locale), esc_url($url));
    }

    $default_locale = (string) Options\get('default_locale', 'tr');
    $default_url = $alternates[$default_locale] ?? reset($alternates) ?: home_url('/');
    printf("<link rel=\"alternate\" hreflang=\"x-default\" href=\"%s\">\n", esc_url($default_url));
}

function render_schema(): void
{
    if (is_404() || is_search()) {
        return;
    }

    $schemas  = [];
    $same_as  = array_filter([
        Options\get('linkedin_url'),
        Options\get('instagram_url'),
        Options\get('twitter_url'),
        Options\get('youtube_url'),
        Options\get('facebook_url'),
    ]);

    $organization = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => Options\get('organization_name', 'Myliba'),
        'url'      => Options\get('organization_url', home_url('/')),
    ];

    $logo_id = (int) get_theme_mod('custom_logo');
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
    if ($logo_url) {
        $organization['logo'] = $logo_url;
    }

    if ($same_as) {
        $organization['sameAs'] = array_values($same_as);
    }

    // WebSite identity. SearchAction is intentionally omitted because the
    // public site does not expose a dedicated search results experience.
    $website = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => Options\get('organization_name', 'Myliba'),
        'url' => home_url('/'),
    ];

    $schemas[] = $organization;
    $schemas[] = $website;
    if (!is_front_page()) {
        $schemas[] = breadcrumb_schema();
    }

    if (is_academy_landing()) {
        $schemas[] = educational_organization_schema();
        $schemas = array_merge($schemas, academy_course_schemas());
    }

    if (is_singular('post')) {
        $schemas[] = article_schema();
    }

    if (is_singular('myliba_product')) {
        $schemas[] = software_application_schema();
    }

    if (is_singular('myliba_event')) {
        $schemas[] = event_schema();
    }

    if (is_singular('myliba_academy')) {
        $schemas[] = course_schema(get_queried_object_id());
    }

    $faq = faq_schema();
    if ($faq) {
        $schemas[] = $faq;
    }

    foreach (array_filter($schemas) as $schema) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . "</script>\n";
    }
}

function breadcrumb_schema(): array
{
    $items = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => get_bloginfo('name'),
            'item' => home_url('/'),
        ],
    ];

    if (is_singular()) {
        $post = get_post(get_queried_object_id());
        if ($post && $post->post_parent) {
            $parent = get_post($post->post_parent);
            if ($parent) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => count($items) + 1,
                    'name' => get_the_title($parent),
                    'item' => get_permalink($parent),
                ];
            }
        }

        $items[] = [
            '@type' => 'ListItem',
            'position' => count($items) + 1,
            'name' => get_the_title(),
            'item' => current_url(),
        ];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

function article_schema(): array
{
    $post_id = get_queried_object_id();
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title($post_id),
        'datePublished' => get_the_date(DATE_W3C, $post_id),
        'dateModified' => get_the_modified_date(DATE_W3C, $post_id),
        'author' => [
            '@type' => 'Organization',
            'name' => Options\get('organization_name', 'Myliba'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => Options\get('organization_name', 'Myliba'),
            'url' => Options\get('organization_url', home_url('/')),
        ],
        'mainEntityOfPage' => current_url(),
    ];

    if (has_post_thumbnail($post_id)) {
        $image = wp_get_attachment_image_url(get_post_thumbnail_id($post_id), 'large');
        if ($image) {
            $schema['image'] = $image;
        }
    }

    return $schema;
}

function post_description(int $post_id): string
{
    $description = trim((string) get_post_meta($post_id, '_myliba_seo_description', true));
    if ($description !== '') {
        return $description;
    }

    $post = get_post($post_id);
    if (!$post instanceof \WP_Post) {
        return '';
    }

    return wp_trim_words(wp_strip_all_tags($post->post_excerpt ?: $post->post_content), 32);
}

function software_application_schema(): array
{
    $post_id = (int) get_queried_object_id();
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => get_the_title($post_id),
        'description' => post_description($post_id),
        'url' => current_url(),
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'Web',
        'provider' => [
            '@type' => 'Organization',
            'name' => Options\get('organization_name', 'Myliba'),
            'url' => Options\get('organization_url', home_url('/')),
        ],
    ];

    if (has_post_thumbnail($post_id)) {
        $schema['image'] = wp_get_attachment_image_url(get_post_thumbnail_id($post_id), 'large');
    }

    return array_filter($schema);
}

function event_schema(): array
{
    $post_id = (int) get_queried_object_id();
    $date = trim((string) get_post_meta($post_id, '_myliba_event_date', true));
    $location = trim((string) get_post_meta($post_id, '_myliba_event_location', true));
    $registration_url = trim((string) get_post_meta($post_id, '_myliba_event_url', true));
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => get_the_title($post_id),
        'description' => post_description($post_id),
        'url' => current_url(),
        'eventStatus' => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => stripos($location, 'online') !== false
            ? 'https://schema.org/OnlineEventAttendanceMode'
            : 'https://schema.org/OfflineEventAttendanceMode',
        'organizer' => [
            '@type' => 'Organization',
            'name' => Options\get('organization_name', 'Myliba'),
            'url' => Options\get('organization_url', home_url('/')),
        ],
    ];

    if ($date !== '') {
        $timestamp = strtotime($date);
        if ($timestamp) {
            $schema['startDate'] = wp_date(DATE_W3C, $timestamp);
        }
    }

    if (stripos($location, 'online') !== false) {
        $schema['location'] = [
            '@type' => 'VirtualLocation',
            'url' => $registration_url ?: current_url(),
        ];
    } elseif ($location !== '') {
        $schema['location'] = [
            '@type' => 'Place',
            'name' => $location,
        ];
    }

    if (has_post_thumbnail($post_id)) {
        $schema['image'] = [wp_get_attachment_image_url(get_post_thumbnail_id($post_id), 'large')];
    }

    return array_filter($schema);
}

function course_schema(int $post_id): array
{
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => get_the_title($post_id),
        'description' => post_description($post_id),
        'url' => current_url(),
        'provider' => [
            '@type' => 'EducationalOrganization',
            'name' => Options\get('organization_name', 'Myliba'),
            'url' => Options\get('organization_url', home_url('/')),
        ],
    ];

    $certificate = trim((string) get_post_meta($post_id, '_myliba_academy_certificate_info', true));
    if ($certificate !== '') {
        $schema['educationalCredentialAwarded'] = $certificate;
    }

    return array_filter($schema);
}

function faq_schema(): array
{
    if (!is_singular()) {
        return [];
    }

    $post_id = get_queried_object_id();
    $items = get_post_meta($post_id, '_myliba_faq_items', true);
    $pairs = [];

    foreach (preg_split('/\r\n|\r|\n/', (string) $items) ?: [] as $line) {
        [$question, $answer] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        if ($question && $answer) {
            $pairs[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }
    }

    if (is_academy_landing()) {
        $group = trim((string) get_post_meta($post_id, '_myliba_academy_faq_group', true));
        $meta_query = [];

        if ($group !== '') {
            $meta_query[] = [
                'key' => '_myliba_label',
                'value' => $group,
            ];
        }

        $language = get_post_meta($post_id, '_myliba_language', true);
        if ($language !== '' && !function_exists('pll_current_language')) {
            $meta_query[] = [
                'key' => '_myliba_language',
                'value' => $language,
            ];
        }

        $faq_query = new \WP_Query([
            'post_type' => 'myliba_faq',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_key' => '_myliba_order',
            'orderby' => ['meta_value_num' => 'ASC', 'date' => 'DESC'],
            'meta_query' => $meta_query,
        ]);

        while ($faq_query->have_posts()) {
            $faq_query->the_post();
            $answer = trim(wp_strip_all_tags((string) get_post_field('post_content', get_the_ID())));
            if ($answer === '') {
                continue;
            }
            $pairs[] = [
                '@type' => 'Question',
                'name' => get_the_title(),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }
        wp_reset_postdata();
    }

    if (!$pairs) {
        return [];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $pairs,
    ];
}

function is_academy_landing(): bool
{
    if (!is_page()) {
        return false;
    }

    $post = get_post(get_queried_object_id());

    return $post && in_array($post->post_name, ['okr-culture-academy', 'okr-kultur-akademisi'], true);
}

function educational_organization_schema(): array
{
    $academy_name = trim((string) get_post_meta(get_queried_object_id(), '_myliba_academy_organization_name', true));

    return [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => $academy_name ?: Options\get('organization_name', 'Myliba'),
        'url' => current_url(),
        'parentOrganization' => [
            '@type' => 'Organization',
            'name' => Options\get('organization_name', 'Myliba'),
            'url' => Options\get('organization_url', home_url('/')),
        ],
    ];
}

function academy_course_schemas(): array
{
    $language = get_post_meta(get_queried_object_id(), '_myliba_language', true);
    $academy_name = trim((string) get_post_meta(get_queried_object_id(), '_myliba_academy_organization_name', true));
    $meta_query = [];
    if ($language !== '' && !function_exists('pll_current_language')) {
        $meta_query[] = [
            'key' => '_myliba_language',
            'value' => $language,
        ];
    }

    $query = new \WP_Query([
        'post_type' => 'myliba_academy',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_myliba_order',
        'orderby' => ['meta_value_num' => 'ASC', 'date' => 'DESC'],
        'meta_query' => $meta_query,
    ]);
    $schemas = [];

    while ($query->have_posts()) {
        $query->the_post();
        $program_id = get_the_ID();
        $description = get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $program_id)), 32);
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => get_the_title(),
            'description' => $description,
            'url' => current_url() . '#program-' . (count($schemas) + 1),
            'provider' => [
                '@type' => 'EducationalOrganization',
                'name' => $academy_name ?: Options\get('organization_name', 'Myliba'),
                'url' => current_url(),
            ],
        ];

        $certificate = trim((string) get_post_meta($program_id, '_myliba_academy_certificate_info', true));
        if ($certificate !== '') {
            $schema['educationalCredentialAwarded'] = $certificate;
        }
        $schemas[] = $schema;
    }
    wp_reset_postdata();

    return $schemas;
}

function current_url(): string
{
    global $wp;

    $path = isset($wp->request) ? '/' . ltrim((string) $wp->request, '/') : '/';
    $path = $path === '/' ? '/' : user_trailingslashit($path);

    return home_url($path);
}
