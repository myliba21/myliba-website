<?php

namespace Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

function defaults(): array
{
    $defaults = [
        'indexing_enabled' => '0',
        'default_locale' => 'en',
        'available_locales' => "en\ntr",
        'contact_email' => get_option('admin_email'),
        'phone_label' => '+90 553 986 86 99',
        'phone_url' => 'tel:+905539868699',
        'demo_url' => '/tr/demo/',
        'organization_name' => 'Myliba',
        'organization_url' => home_url('/'),
        'linkedin_url' => '',
        'instagram_url' => '',
        'footer_note' => 'OKR, kültür, etik ve güvenlik danışmanlığı.',
        'footer_cta_title' => 'Kültürü ölçülebilir hale getirmeye hazır mısınız?',
        'primary_cta_label' => 'İletişime geçin',
        'primary_cta_url' => '/tr/iletisim/',
        'demo_cta_label' => 'Demo talep et',
        'promo_enabled' => '1',
        'promo_left_text' => 'Yaklaşan atölye',
        'promo_message' => 'Bir sonraki atölyemizde yerinizi ayırtın.',
        'promo_right_text' => 'Detay',
        'promo_url' => '',
        'promo_dismissible' => '1',
    ];

    foreach (localized_keys() as $key) {
        $defaults[$key . '_en'] = '';
        $defaults[$key . '_tr'] = '';
    }

    return $defaults;
}

function localized_keys(): array
{
    return [
        'demo_cta_label',
        'demo_url',
        'footer_cta_title',
        'footer_note',
        'primary_cta_label',
        'primary_cta_url',
        'promo_left_text',
        'promo_message',
        'promo_right_text',
        'promo_url',
    ];
}

function get_all(): array
{
    $options = get_option('myliba_options', []);

    if (!is_array($options)) {
        $options = [];
    }

    return $options;
}

function get(string $key, mixed $fallback = null): mixed
{
    $options = get_all();

    return $options[$key] ?? $fallback;
}

function indexing_enabled(): bool
{
    return (string) get('indexing_enabled') === '1';
}

function locales(): array
{
    $raw = (string) get('available_locales', "en\ntr");
    $items = preg_split('/[\r\n,]+/', $raw) ?: [];
    $items = array_map('trim', $items);
    $items = array_filter($items, static fn ($item) => $item !== '');

    return array_values(array_unique(array_merge($items, ['en', 'tr'])));
}

function ensure_defaults(): void
{
    $stored = get_option('myliba_options', null);
    if (!is_array($stored)) {
        update_option('myliba_options', defaults(), false);
        return;
    }

    $merged = $stored;
    foreach (defaults() as $key => $value) {
        if (!array_key_exists($key, $merged)) {
            $merged[$key] = $value;
        }
    }

    if ($merged !== $stored) {
        update_option('myliba_options', $merged, false);
    }
}

function boot(): void
{
    add_action('init', __NAMESPACE__ . '\\ensure_defaults', 0);
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

    $sanitized = [
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

    $url_keys = ['demo_url', 'primary_cta_url', 'promo_url'];
    $textarea_keys = ['footer_note'];
    foreach (localized_keys() as $key) {
        foreach (['en', 'tr'] as $locale) {
            $localized_key = $key . '_' . $locale;
            $raw = $input[$localized_key] ?? '';
            $sanitized[$localized_key] = in_array($key, $url_keys, true)
                ? esc_url_raw($raw)
                : (in_array($key, $textarea_keys, true) ? sanitize_textarea_field($raw) : sanitize_text_field($raw));
        }
    }

    return $sanitized;
}
