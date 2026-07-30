<?php

namespace Myliba\Core\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

function boot(): void
{
    add_action('init', __NAMESPACE__ . '\\register');
    add_action('init', __NAMESPACE__ . '\\register_localized_rewrite_rules', 20);
    add_filter('post_type_link', __NAMESPACE__ . '\\localized_post_type_link', 10, 2);
    add_filter('query_vars', __NAMESPACE__ . '\\localized_query_vars');
    add_action('pre_get_posts', __NAMESPACE__ . '\\enforce_route_locale');
    add_action('template_redirect', __NAMESPACE__ . '\\redirect_missing_localized_solution', 1);
    add_filter('pll_get_post_types', __NAMESPACE__ . '\\polylang_post_types', 10, 2);
    add_filter('pll_get_taxonomies', __NAMESPACE__ . '\\polylang_taxonomies', 10, 2);
}

function localized_bases(): array
{
    return [
        'myliba_product' => [
            'tr' => 'tr/yazilim',
            'en' => 'en/our-products',
        ],
        'myliba_solution' => [
            'tr' => 'tr/cozumler',
            'en' => 'en/solutions',
        ],
        'myliba_academy' => [
            'tr' => 'tr/okr-kultur-akademisi',
            'en' => 'en/okr-culture-academy',
        ],
        'myliba_case_study' => [
            'tr' => 'tr/vaka-calismalari',
            'en' => 'en/case-studies',
        ],
        'myliba_landing' => [
            'tr' => 'tr/icerikler',
            'en' => 'en/content',
        ],
        'myliba_event' => [
            'tr' => 'tr/etkinlikler',
            'en' => 'en/events',
        ],
        'myliba_ebook' => [
            'tr' => 'tr/gelisim-merkezi/e-kitaplar',
            'en' => 'en/development-center/ebooks',
        ],
        'myliba_report' => [
            'tr' => 'tr/gelisim-merkezi/raporlar-ve-trendler',
            'en' => 'en/development-center/reports',
        ],
        'myliba_team' => [
            'tr' => 'tr/ekibimiz',
            'en' => 'en/team',
        ],
    ];
}

function register_localized_rewrite_rules(): void
{
    foreach (localized_bases() as $post_type => $locale_bases) {
        foreach ($locale_bases as $locale => $base) {
            add_rewrite_rule(
                '^' . preg_quote($base, '#') . '/([^/]+)/?$',
                'index.php?post_type=' . $post_type . '&name=$matches[1]&myliba_route_locale=' . $locale,
                'top'
            );
        }
    }

    add_rewrite_rule(
        '^en/development-center/ebooks/?$',
        'index.php?post_type=myliba_ebook&myliba_route_locale=en',
        'top'
    );
    add_rewrite_rule(
        '^en/development-center/reports/?$',
        'index.php?post_type=myliba_report&myliba_route_locale=en',
        'top'
    );
}

function localized_query_vars(array $query_vars): array
{
    $query_vars[] = 'myliba_route_locale';

    return $query_vars;
}

function enforce_route_locale(\WP_Query $query): void
{
    if (is_admin()) {
        return;
    }

    $locale = sanitize_key((string) $query->get('myliba_route_locale'));
    $post_type = $query->get('post_type');
    $post_types = is_array($post_type) ? $post_type : [$post_type];
    $supported_types = array_keys(localized_bases());

    if (!in_array($locale, ['tr', 'en'], true) || !array_intersect($post_types, $supported_types)) {
        return;
    }

    $meta_query = $query->get('meta_query');
    $meta_query = is_array($meta_query) ? $meta_query : [];
    $meta_query[] = [
        'key' => '_myliba_language',
        'value' => $locale,
        'compare' => '=',
    ];
    $query->set('meta_query', $meta_query);
}

function redirect_missing_localized_solution(): void
{
    if (!is_404() || is_admin()) {
        return;
    }

    $locale = sanitize_key((string) get_query_var('myliba_route_locale'));
    $post_type = get_query_var('post_type');
    if (!in_array($locale, ['tr', 'en'], true) || $post_type !== 'myliba_solution') {
        return;
    }

    $destination = $locale === 'tr' ? home_url('/tr/cozumler/') : home_url('/en/solutions/');
    wp_safe_redirect($destination, 302);
    exit;
}

function localized_post_type_link(string $permalink, \WP_Post $post): string
{
    $bases = localized_bases();
    if (!isset($bases[$post->post_type])) {
        return $permalink;
    }

    $language = sanitize_key((string) get_post_meta($post->ID, '_myliba_language', true));
    if (!isset($bases[$post->post_type][$language])) {
        $language = 'tr';
    }

    return home_url('/' . $bases[$post->post_type][$language] . '/' . $post->post_name . '/');
}

function register(): void
{
    register_post_type('myliba_product', [
        'labels' => [
            'name' => __('Products', 'myliba'),
            'singular_name' => __('Product', 'myliba'),
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-screenoptions',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'tr/yazilim', 'with_front' => false],
    ]);

    register_post_type('myliba_solution', [
        'labels' => [
            'name' => __('Solutions', 'myliba'),
            'singular_name' => __('Solution', 'myliba'),
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-businessperson',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'tr/cozumler', 'with_front' => false],
    ]);

    register_post_type('myliba_academy', [
        'labels' => [
            'name' => __('Academy Programs', 'myliba'),
            'singular_name' => __('Academy Program', 'myliba'),
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'tr/okr-kultur-akademisi', 'with_front' => false],
    ]);

    register_post_type('myliba_case_study', [
        'labels' => [
            'name' => __('Case Studies', 'myliba'),
            'singular_name' => __('Case Study', 'myliba'),
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-chart-line',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'rewrite' => ['slug' => 'tr/vaka-calismalari', 'with_front' => false],
    ]);

    register_post_type('myliba_testimonial', [
        'labels' => [
            'name' => __('Testimonials', 'myliba'),
            'singular_name' => __('Testimonial', 'myliba'),
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-format-quote',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes'],
    ]);

    register_post_type('myliba_faq', [
        'labels' => [
            'name' => __('FAQs', 'myliba'),
            'singular_name' => __('FAQ', 'myliba'),
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-editor-help',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'page-attributes'],
    ]);

    register_post_type('myliba_landing', [
        'labels' => [
            'name' => __('SEO Landing Pages', 'myliba'),
            'singular_name' => __('SEO Landing Page', 'myliba'),
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-search',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'rewrite' => ['slug' => 'tr/icerikler', 'with_front' => false],
    ]);

    register_post_type('myliba_event', [
        'labels' => [
            'name' => __('Events', 'myliba'),
            'singular_name' => __('Event', 'myliba'),
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'rewrite' => ['slug' => 'tr/etkinlikler', 'with_front' => false],
    ]);

    register_post_type('myliba_ebook', [
        'labels' => [
            'name' => 'e-Kitaplar',
            'singular_name' => 'e-Kitap',
            'add_new_item' => 'Yeni e-Kitap Ekle',
            'edit_item' => 'e-Kitabı Düzenle',
        ],
        'public' => true,
        'has_archive' => 'tr/gelisim-merkezi/e-kitaplar',
        'menu_icon' => 'dashicons-book-alt',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'tr/gelisim-merkezi/e-kitaplar', 'with_front' => false],
    ]);

    register_post_type('myliba_report', [
        'labels' => [
            'name' => 'Raporlar ve Trendler',
            'singular_name' => 'Rapor veya Trend',
            'add_new_item' => 'Yeni Rapor veya Trend Ekle',
            'edit_item' => 'Raporu veya Trendi Düzenle',
        ],
        'public' => true,
        'has_archive' => 'tr/gelisim-merkezi/raporlar-ve-trendler',
        'menu_icon' => 'dashicons-chart-area',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'tr/gelisim-merkezi/raporlar-ve-trendler', 'with_front' => false],
    ]);

    register_post_type('myliba_team', [
        'labels' => [
            'name' => __('Team Members', 'myliba'),
            'singular_name' => __('Team Member', 'myliba'),
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-groups',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'tr/ekibimiz', 'with_front' => false],
    ]);

    register_post_type('myliba_client_logo', [
        'labels' => [
            'name' => __('Client Logos', 'myliba'),
            'singular_name' => __('Client Logo', 'myliba'),
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-format-image',
        'show_in_rest' => true,
        'supports' => ['title', 'thumbnail', 'page-attributes'],
    ]);

    register_post_type('myliba_submission', [
        'labels' => [
            'name' => __('Form Submissions', 'myliba'),
            'singular_name' => __('Form Submission', 'myliba'),
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-email-alt2',
        'supports' => ['title'],
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);
}

function polylang_post_types(array $post_types, bool $is_settings): array
{
    $post_types['myliba_product'] = 'myliba_product';
    $post_types['myliba_solution'] = 'myliba_solution';
    $post_types['myliba_academy'] = 'myliba_academy';
    $post_types['myliba_case_study'] = 'myliba_case_study';
    $post_types['myliba_faq'] = 'myliba_faq';
    $post_types['myliba_landing'] = 'myliba_landing';
    $post_types['myliba_event'] = 'myliba_event';
    $post_types['myliba_ebook'] = 'myliba_ebook';
    $post_types['myliba_report'] = 'myliba_report';
    $post_types['myliba_team'] = 'myliba_team';

    return $post_types;
}

function polylang_taxonomies(array $taxonomies, bool $is_settings): array
{
    $taxonomies['category'] = 'category';
    $taxonomies['post_tag'] = 'post_tag';

    return $taxonomies;
}
