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
    add_filter('document_title_parts', __NAMESPACE__ . '\\document_title');
    add_action('send_headers', __NAMESPACE__ . '\\send_noindex_header');
    add_action('wp_head', __NAMESPACE__ . '\\render_head', 2);
}

function seo_plugin_active(): bool
{
    return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION');
}

function current_post_noindex(): bool
{
    if (!is_singular()) {
        return false;
    }

    return get_post_meta(get_queried_object_id(), '_myliba_noindex', true) === '1';
}

function should_noindex(): bool
{
    return !Options\indexing_enabled() || current_post_noindex();
}

function robots(array $robots): array
{
    if (should_noindex()) {
        unset($robots['index'], $robots['follow']);
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
    }

    return $robots;
}

function robots_txt(string $output, bool $public): string
{
    if (!Options\indexing_enabled()) {
        return "User-agent: *\nDisallow: /\n";
    }

    return $output;
}

function sitemaps_enabled(bool $enabled): bool
{
    return Options\indexing_enabled() ? $enabled : false;
}

function send_noindex_header(): void
{
    if (should_noindex()) {
        header('X-Robots-Tag: noindex, nofollow', true);
    }
}

function document_title(array $parts): array
{
    if (!is_singular()) {
        return $parts;
    }

    $seo_title = get_post_meta(get_queried_object_id(), '_myliba_seo_title', true);

    if ($seo_title) {
        $parts['title'] = $seo_title;
    }

    return $parts;
}

function render_head(): void
{
    if (should_noindex()) {
        echo "<meta name=\"robots\" content=\"noindex,nofollow\">\n";
    }

    if (!seo_plugin_active()) {
        render_fallback_meta();
    }

    render_schema();
}

function render_fallback_meta(): void
{
    $description = '';

    if (is_singular()) {
        $post_id = get_queried_object_id();
        $description = get_post_meta($post_id, '_myliba_seo_description', true);

        if (!$description) {
            $post = get_post($post_id);
            $description = $post ? wp_trim_words(wp_strip_all_tags($post->post_excerpt ?: $post->post_content), 28) : '';
        }
    } else {
        $description = get_bloginfo('description');
    }

    if ($description) {
        printf("<meta name=\"description\" content=\"%s\">\n", esc_attr($description));
        printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($description));
    }

    printf("<meta property=\"og:title\" content=\"%s\">\n", esc_attr(wp_get_document_title()));
    printf("<meta property=\"og:url\" content=\"%s\">\n", esc_url(current_url()));
    echo "<meta property=\"og:type\" content=\"" . (is_singular('post') ? 'article' : 'website') . "\">\n";
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    printf("<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr(wp_get_document_title()));

    if (is_singular() && has_post_thumbnail()) {
        $image = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
        if ($image) {
            printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url($image));
            printf("<meta name=\"twitter:image\" content=\"%s\">\n", esc_url($image));
        }
    }

    render_hreflang();
}

function render_hreflang(): void
{
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(['raw' => 1]);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                if (!empty($language['url']) && !empty($language['slug'])) {
                    printf("<link rel=\"alternate\" hreflang=\"%s\" href=\"%s\">\n", esc_attr($language['slug']), esc_url($language['url']));
                }
            }
        }
    }
}

function render_schema(): void
{
    $schemas = [];
    $same_as = array_filter([
        Options\get('linkedin_url'),
        Options\get('instagram_url'),
    ]);

    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => Options\get('organization_name', 'Myliba'),
        'url' => Options\get('organization_url', home_url('/')),
    ];

    if ($same_as) {
        $organization['sameAs'] = array_values($same_as);
    }

    $schemas[] = $organization;
    $schemas[] = breadcrumb_schema();

    if (is_singular('post')) {
        $schemas[] = article_schema();
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

function faq_schema(): array
{
    if (!is_singular()) {
        return [];
    }

    $items = get_post_meta(get_queried_object_id(), '_myliba_faq_items', true);
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

    if (!$pairs) {
        return [];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $pairs,
    ];
}

function current_url(): string
{
    global $wp;

    $path = isset($wp->request) ? '/' . ltrim((string) $wp->request, '/') : '/';

    return home_url($path);
}
