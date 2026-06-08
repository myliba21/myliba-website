<?php

namespace Myliba\Core\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

function boot(): void
{
    add_action('init', __NAMESPACE__ . '\\register');
    add_filter('pll_get_post_types', __NAMESPACE__ . '\\polylang_post_types', 10, 2);
    add_filter('pll_get_taxonomies', __NAMESPACE__ . '\\polylang_taxonomies', 10, 2);
}

function register(): void
{
    register_post_type('myliba_product', [
        'labels' => [
            'name' => __('Products', 'myliba'),
            'singular_name' => __('Product', 'myliba'),
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-screenoptions',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'products'],
    ]);

    register_post_type('myliba_solution', [
        'labels' => [
            'name' => __('Solutions', 'myliba'),
            'singular_name' => __('Solution', 'myliba'),
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-businessperson',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'solutions'],
    ]);

    register_post_type('myliba_academy', [
        'labels' => [
            'name' => __('Academy Programs', 'myliba'),
            'singular_name' => __('Academy Program', 'myliba'),
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions'],
        'rewrite' => ['slug' => 'academy'],
    ]);

    register_post_type('myliba_case_study', [
        'labels' => [
            'name' => __('Case Studies', 'myliba'),
            'singular_name' => __('Case Study', 'myliba'),
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-chart-line',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'rewrite' => ['slug' => 'case-studies'],
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
        'rewrite' => ['slug' => 'landing-pages'],
    ]);

    register_post_type('myliba_event', [
        'labels' => [
            'name' => __('Events', 'myliba'),
            'singular_name' => __('Event', 'myliba'),
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'rewrite' => ['slug' => 'events'],
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
        'rewrite' => ['slug' => 'team'],
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
    $post_types['myliba_team'] = 'myliba_team';

    return $post_types;
}

function polylang_taxonomies(array $taxonomies, bool $is_settings): array
{
    $taxonomies['category'] = 'category';
    $taxonomies['post_tag'] = 'post_tag';

    return $taxonomies;
}
