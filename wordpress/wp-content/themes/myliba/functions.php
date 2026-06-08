<?php

if (!defined('ABSPATH')) {
    exit;
}

function myliba_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('automatic-feed-links');

    register_nav_menus([
        'primary' => __('Primary Navigation', 'myliba'),
        'footer' => __('Footer Navigation', 'myliba'),
    ]);
}
add_action('after_setup_theme', 'myliba_theme_setup');

function myliba_enqueue_assets(): void
{
    wp_enqueue_style('myliba-main', get_template_directory_uri() . '/assets/css/main.css', [], wp_get_theme()->get('Version'));
    wp_enqueue_script('myliba-main', get_template_directory_uri() . '/assets/js/main.js', [], wp_get_theme()->get('Version'), true);
}
add_action('wp_enqueue_scripts', 'myliba_enqueue_assets');

function myliba_option(string $key, mixed $fallback = ''): mixed
{
    if (function_exists('Myliba\\Core\\Options\\get')) {
        return \Myliba\Core\Options\get($key, $fallback);
    }

    return $fallback;
}

function myliba_meta(string $key, int $post_id = 0, mixed $fallback = ''): mixed
{
    $post_id = $post_id ?: get_queried_object_id();
    $value = $post_id ? get_post_meta($post_id, $key, true) : '';

    return $value !== '' ? $value : $fallback;
}

function myliba_current_language(): string
{
    if (function_exists('pll_current_language')) {
        return (string) pll_current_language('slug');
    }

    if (is_singular()) {
        return (string) myliba_meta('_myliba_language', get_queried_object_id(), myliba_option('default_locale', 'en'));
    }

    return (string) myliba_option('default_locale', 'en');
}

function myliba_page_url(string $key): string
{
    $lang = myliba_current_language();
    $paths = [
        'products' => ['en' => 'en/our-products', 'tr' => 'tr/urunler'],
        'academy' => ['en' => 'en/okr-culture-academy', 'tr' => 'tr/okr-kultur-akademisi'],
        'culture' => ['en' => 'en/culture-analysis', 'tr' => 'tr/kultur-analizi'],
        'ethics' => ['en' => 'en/ethics-counsel', 'tr' => 'tr/etik-danismanlik'],
        'blog' => ['en' => 'en/blog', 'tr' => 'tr/yazilar'],
        'solutions' => ['en' => 'en/solutions', 'tr' => 'tr/cozumler'],
        'events' => ['en' => 'en/events', 'tr' => 'tr/etkinlikler'],
        'contact' => ['en' => 'en/contact', 'tr' => 'tr/iletisim'],
        'demo' => ['en' => 'en/demo', 'tr' => 'tr/demo'],
        'story' => ['en' => 'en/our-story', 'tr' => 'tr/hikayemiz'],
        'faq' => ['en' => 'en/faq', 'tr' => 'tr/sss'],
        'security' => ['en' => 'en/security', 'tr' => 'tr/guvenlik'],
        'privacy' => ['en' => 'en/privacy-policy', 'tr' => 'tr/gizlilik-politikasi'],
    ];

    if (!empty($paths[$key][$lang])) {
        $page = get_page_by_path($paths[$key][$lang]);
        if ($page) {
            return get_permalink($page);
        }
    }

    return home_url('/' . ($paths[$key]['en'] ?? ''));
}

function myliba_nav_items(): array
{
    return [
        'products' => __('Products', 'myliba'),
        'academy' => __('Academy', 'myliba'),
        'solutions' => __('Solutions', 'myliba'),
        'story' => __('Our Story', 'myliba'),
        'blog' => __('Blog', 'myliba'),
        'contact' => __('Contact', 'myliba'),
    ];
}

function myliba_language_links(): array
{
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(['raw' => 1]);

        if (is_array($languages) && $languages) {
            return array_map(static function (array $language): array {
                return [
                    'label' => strtoupper((string) $language['slug']),
                    'url' => (string) $language['url'],
                    'active' => !empty($language['current_lang']),
                ];
            }, $languages);
        }
    }

    $links = [];
    foreach (['en', 'tr'] as $language) {
        $page = get_page_by_path($language);
        $links[] = [
            'label' => strtoupper($language),
            'url' => $page ? get_permalink($page) : home_url('/' . $language . '/'),
            'active' => myliba_current_language() === $language,
        ];
    }

    return $links;
}

function myliba_post_language_filter(\WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query() || function_exists('pll_current_language')) {
        return;
    }

    if ($query->is_home() || $query->is_archive()) {
        $query->set('meta_query', [
            [
                'key' => '_myliba_language',
                'value' => myliba_current_language(),
                'compare' => '=',
            ],
        ]);
    }
}
add_action('pre_get_posts', 'myliba_post_language_filter');

function myliba_excerpt(int $post_id = 0, int $words = 28): string
{
    $post = get_post($post_id ?: get_the_ID());
    if (!$post) {
        return '';
    }

    $source = $post->post_excerpt ?: $post->post_content;

    return wp_trim_words(wp_strip_all_tags($source), $words);
}

function myliba_get_entries(string $post_type, int $limit = 6, array $args = []): WP_Query
{
    $query_args = array_merge([
        'post_type' => $post_type,
        'posts_per_page' => $limit,
        'meta_key' => '_myliba_order',
        'orderby' => ['meta_value_num' => 'ASC', 'date' => 'DESC'],
        'order' => 'ASC',
    ], $args);

    if (!function_exists('pll_current_language') && !isset($query_args['meta_query'])) {
        $query_args['meta_query'] = [
            [
                'key' => '_myliba_language',
                'value' => myliba_current_language(),
                'compare' => '=',
            ],
        ];
    }

    return new WP_Query($query_args);
}

function myliba_lines(string $value): array
{
    $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];
    $lines = array_map('trim', $lines);

    return array_values(array_filter($lines, static fn ($line) => $line !== ''));
}

function myliba_faq_pairs(string $value): array
{
    $pairs = [];
    foreach (myliba_lines($value) as $line) {
        [$question, $answer] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        if ($question && $answer) {
            $pairs[] = ['question' => $question, 'answer' => $answer];
        }
    }

    return $pairs;
}

function myliba_reading_time(int $post_id = 0): int
{
    $post = get_post($post_id ?: get_the_ID());
    $words = $post ? str_word_count(wp_strip_all_tags($post->post_content)) : 0;

    return max(1, (int) ceil($words / 220));
}

function myliba_demo_url(): string
{
    $page_url = myliba_page_url('demo');

    return $page_url ?: (string) myliba_option('demo_url', '/en/demo/');
}
