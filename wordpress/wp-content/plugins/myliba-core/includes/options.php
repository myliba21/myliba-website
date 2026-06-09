<?php

namespace Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

function defaults(): array
{
    return [
        'indexing_enabled' => '0',
        'default_locale' => 'en',
        'available_locales' => "en\ntr",
        'contact_email' => get_option('admin_email'),
        'phone_label' => '+90 553 986 86 99',
        'phone_url' => 'tel:+905539868699',
        'demo_url' => '/en/demo/',
        'organization_name' => 'Myliba',
        'organization_url' => home_url('/'),
        'linkedin_url' => '',
        'instagram_url' => '',
        'footer_note' => 'OKR, culture, ethics, and security consulting.',
        'footer_cta_title' => 'Ready to make culture measurable?',
        'primary_cta_label' => 'Contact us',
        'primary_cta_url' => '/en/contact/',
        'demo_cta_label' => 'Request a demo',
        'promo_enabled' => '0',
        'promo_left_text' => 'Backed by Plug and Play',
        'promo_message' => 'Chosen for Endeavor Turkey\'s ScaleUp Program!',
        'promo_right_text' => 'Endeavor',
        'promo_url' => '',
        'promo_dismissible' => '1',
    ];
}

function get_all(): array
{
    $options = get_option('myliba_options', []);

    if (!is_array($options)) {
        $options = [];
    }

    return array_merge(defaults(), $options);
}

function get(string $key, mixed $fallback = null): mixed
{
    $options = get_all();

    return $options[$key] ?? $fallback;
}

function indexing_enabled(): bool
{
    return get('indexing_enabled') === '1';
}

function locales(): array
{
    $raw = (string) get('available_locales', "en\ntr");
    $items = preg_split('/[\r\n,]+/', $raw) ?: [];
    $items = array_map('trim', $items);
    $items = array_filter($items, static fn ($item) => $item !== '');

    return array_values(array_unique($items)) ?: ['en', 'tr'];
}

function ensure_defaults(): void
{
    if (!get_option('myliba_options')) {
        add_option('myliba_options', defaults());
    }
}

function boot(): void
{
    add_action('admin_init', __NAMESPACE__ . '\\register_settings');
}

function register_settings(): void
{
    register_setting(
        'myliba_options',
        'myliba_options',
        [
            'type' => 'array',
            'sanitize_callback' => __NAMESPACE__ . '\\sanitize',
            'default' => defaults(),
        ]
    );
}

function sanitize(array $input): array
{
    $defaults = defaults();

    return [
        'indexing_enabled' => !empty($input['indexing_enabled']) ? '1' : '0',
        'default_locale' => sanitize_key($input['default_locale'] ?? $defaults['default_locale']),
        'available_locales' => sanitize_textarea_field($input['available_locales'] ?? $defaults['available_locales']),
        'contact_email' => sanitize_email($input['contact_email'] ?? $defaults['contact_email']),
        'phone_label' => sanitize_text_field($input['phone_label'] ?? $defaults['phone_label']),
        'phone_url' => esc_url_raw($input['phone_url'] ?? $defaults['phone_url']),
        'demo_url' => esc_url_raw($input['demo_url'] ?? $defaults['demo_url']),
        'organization_name' => sanitize_text_field($input['organization_name'] ?? $defaults['organization_name']),
        'organization_url' => esc_url_raw($input['organization_url'] ?? $defaults['organization_url']),
        'linkedin_url' => esc_url_raw($input['linkedin_url'] ?? ''),
        'instagram_url' => esc_url_raw($input['instagram_url'] ?? ''),
        'footer_note' => sanitize_textarea_field($input['footer_note'] ?? $defaults['footer_note']),
        'footer_cta_title' => sanitize_text_field($input['footer_cta_title'] ?? $defaults['footer_cta_title']),
        'primary_cta_label' => sanitize_text_field($input['primary_cta_label'] ?? $defaults['primary_cta_label']),
        'primary_cta_url' => esc_url_raw($input['primary_cta_url'] ?? $defaults['primary_cta_url']),
        'demo_cta_label' => sanitize_text_field($input['demo_cta_label'] ?? $defaults['demo_cta_label']),
        'promo_enabled' => !empty($input['promo_enabled']) ? '1' : '0',
        'promo_left_text' => sanitize_text_field($input['promo_left_text'] ?? $defaults['promo_left_text']),
        'promo_message' => sanitize_text_field($input['promo_message'] ?? $defaults['promo_message']),
        'promo_right_text' => sanitize_text_field($input['promo_right_text'] ?? $defaults['promo_right_text']),
        'promo_url' => esc_url_raw($input['promo_url'] ?? $defaults['promo_url']),
        'promo_dismissible' => !empty($input['promo_dismissible']) ? '1' : '0',
    ];
}
