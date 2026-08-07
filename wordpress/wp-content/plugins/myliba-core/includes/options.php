<?php

namespace Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

const SCHEMA_VERSION_OPTION = 'myliba_options_schema_version';
const SCHEMA_VERSION = 3;

function localized_schema(): array
{
    return [
        'primary_cta_label' => [
            'label' => __('Primary CTA label', 'myliba'),
            'group' => 'actions',
            'type' => 'text',
            'defaults' => ['en' => 'Contact us', 'tr' => 'İletişime geçin'],
        ],
        'primary_cta_url' => [
            'label' => __('Primary CTA URL', 'myliba'),
            'group' => 'actions',
            'type' => 'url',
            'defaults' => ['en' => '/en/contact/', 'tr' => '/tr/iletisim/'],
        ],
        'demo_cta_label' => [
            'label' => __('Demo button label', 'myliba'),
            'group' => 'actions',
            'type' => 'text',
            'defaults' => ['en' => 'Request a demo', 'tr' => 'Demo talep et'],
        ],
        'demo_url' => [
            'label' => __('Demo URL', 'myliba'),
            'group' => 'actions',
            'type' => 'url',
            'defaults' => ['en' => '/en/demo/', 'tr' => '/tr/demo/'],
        ],
        'footer_cta_title' => [
            'label' => __('Footer CTA title', 'myliba'),
            'group' => 'footer',
            'type' => 'text',
            'defaults' => [
                'en' => 'Ready to make culture measurable?',
                'tr' => 'Kültürü ölçülebilir hale getirmeye hazır mısınız?',
            ],
        ],
        'footer_note' => [
            'label' => __('Footer note', 'myliba'),
            'group' => 'footer',
            'type' => 'textarea',
            'defaults' => [
                'en' => 'OKR, culture, ethics, and security consulting.',
                'tr' => 'OKR, kültür, etik ve güvenlik danışmanlığı.',
            ],
        ],
        'promo_left_text' => [
            'label' => __('Promo left text', 'myliba'),
            'group' => 'promo',
            'type' => 'text',
            'defaults' => ['en' => 'Upcoming workshop', 'tr' => 'Yaklaşan atölye'],
        ],
        'promo_message' => [
            'label' => __('Promo message', 'myliba'),
            'group' => 'promo',
            'type' => 'textarea',
            'defaults' => [
                'en' => 'Reserve your place in our next workshop.',
                'tr' => 'Bir sonraki atölyemizde yerinizi ayırtın.',
            ],
        ],
        'promo_right_text' => [
            'label' => __('Promo right text', 'myliba'),
            'group' => 'promo',
            'type' => 'text',
            'defaults' => ['en' => 'Details', 'tr' => 'Detay'],
        ],
        'promo_url' => [
            'label' => __('Promo URL', 'myliba'),
            'group' => 'promo',
            'type' => 'url',
            'defaults' => ['en' => '', 'tr' => ''],
        ],
        'form_success_message' => [
            'label' => __('Success message', 'myliba'),
            'group' => 'forms',
            'type' => 'textarea',
            'defaults' => ['en' => 'Your message has been received.', 'tr' => 'Mesajınız alındı.'],
        ],
        'form_error_message' => [
            'label' => __('Error message', 'myliba'),
            'group' => 'forms',
            'type' => 'textarea',
            'defaults' => [
                'en' => 'The form could not be sent. Please try again.',
                'tr' => 'Form gönderilemedi. Lütfen tekrar deneyin.',
            ],
        ],
        'form_first_name' => [
            'label' => __('First name label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'First name', 'tr' => 'Ad'],
        ],
        'form_name' => [
            'label' => __('Name label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Name', 'tr' => 'Adınız'],
        ],
        'form_last_name' => [
            'label' => __('Last name label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Last name', 'tr' => 'Soyad'],
        ],
        'form_business_email' => [
            'label' => __('Business email label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Business email', 'tr' => 'İş e-postası'],
        ],
        'form_phone' => [
            'label' => __('Phone label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Phone', 'tr' => 'Telefon'],
        ],
        'form_company' => [
            'label' => __('Company label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Company', 'tr' => 'Şirket'],
        ],
        'form_title' => [
            'label' => __('Title label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Title', 'tr' => 'Unvan'],
        ],
        'form_employee_count' => [
            'label' => __('Employee count label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Employee count', 'tr' => 'Çalışan sayısı'],
        ],
        'form_program_interest' => [
            'label' => __('Program interest label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Program you are interested in', 'tr' => 'İlgilendiğiniz program'],
        ],
        'form_select_program' => [
            'label' => __('Program placeholder', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Select a program', 'tr' => 'Bir program seçin'],
        ],
        'form_participation_type' => [
            'label' => __('Participation type label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Participation type', 'tr' => 'Katılım türü'],
        ],
        'form_individual' => [
            'label' => __('Individual option', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Individual', 'tr' => 'Bireysel'],
        ],
        'form_corporate' => [
            'label' => __('Corporate option', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Corporate', 'tr' => 'Kurumsal'],
        ],
        'form_subject' => [
            'label' => __('Subject label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Subject', 'tr' => 'Konu'],
        ],
        'form_message' => [
            'label' => __('Message label', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Message', 'tr' => 'Mesaj'],
        ],
        'form_request_demo' => [
            'label' => __('Demo form button', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Request demo', 'tr' => 'Demo talep et'],
        ],
        'form_send' => [
            'label' => __('Contact form button', 'myliba'),
            'group' => 'forms',
            'type' => 'text',
            'defaults' => ['en' => 'Send', 'tr' => 'Gönder'],
        ],
        'form_consent' => [
            'label' => __('Consent text', 'myliba'),
            'group' => 'forms',
            'type' => 'textarea',
            'defaults' => [
                'en' => 'I consent to being contacted about this request and accept the privacy notice.',
                'tr' => 'Bu taleple ilgili tarafımla iletişime geçilmesine izin veriyor ve gizlilik bildirimini kabul ediyorum.',
            ],
        ],
    ];
}

function localized_default(string $key, string $locale): string
{
    $schema = localized_schema();
    return isset($schema[$key]['defaults'][$locale]) ? (string) $schema[$key]['defaults'][$locale] : '';
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
        foreach (['en', 'tr'] as $locale) {
            $defaults[$key . '_' . $locale] = localized_default($key, $locale);
        }
    }

    return $defaults;
}

function localized_keys(): array
{
    // Keep this list translation-free: it is also consulted from the gettext
    // fallback while WordPress is resolving the current request language.
    return [
        'primary_cta_label',
        'primary_cta_url',
        'demo_cta_label',
        'demo_url',
        'footer_cta_title',
        'footer_note',
        'promo_left_text',
        'promo_message',
        'promo_right_text',
        'promo_url',
        'form_success_message',
        'form_error_message',
        'form_first_name',
        'form_name',
        'form_last_name',
        'form_business_email',
        'form_phone',
        'form_company',
        'form_title',
        'form_employee_count',
        'form_program_interest',
        'form_select_program',
        'form_participation_type',
        'form_individual',
        'form_corporate',
        'form_subject',
        'form_message',
        'form_request_demo',
        'form_send',
        'form_consent',
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
        update_option(SCHEMA_VERSION_OPTION, SCHEMA_VERSION, false);
        return;
    }

    $merged = $stored;
    foreach (defaults() as $key => $value) {
        if (!array_key_exists($key, $merged)) {
            $merged[$key] = $value;
        }
    }

    if ((int) get_option(SCHEMA_VERSION_OPTION, 0) < SCHEMA_VERSION) {
        add_option('myliba_options_pre_site_texts_v' . SCHEMA_VERSION, $stored, '', false);
        foreach (localized_keys() as $key) {
            foreach (['en', 'tr'] as $locale) {
                $localized_key = $key . '_' . $locale;
                if (!isset($merged[$localized_key]) || trim((string) $merged[$localized_key]) === '') {
                    $merged[$localized_key] = localized_default($key, $locale);
                }
            }
        }
        update_option(SCHEMA_VERSION_OPTION, SCHEMA_VERSION, false);
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
    $current = get_all();

    $sanitized = [
        'indexing_enabled' => !empty($input['indexing_enabled']) ? '1' : '0',
        'default_locale' => sanitize_key($input['default_locale'] ?? $defaults['default_locale']),
        'available_locales' => sanitize_textarea_field($input['available_locales'] ?? $defaults['available_locales']),
        'contact_email' => sanitize_email($input['contact_email'] ?? $defaults['contact_email']),
        'phone_label' => sanitize_text_field($input['phone_label'] ?? $defaults['phone_label']),
        'phone_url' => esc_url_raw($input['phone_url'] ?? $defaults['phone_url']),
        'demo_url' => esc_url_raw($input['demo_url'] ?? $current['demo_url'] ?? $defaults['demo_url']),
        'organization_name' => sanitize_text_field($input['organization_name'] ?? $defaults['organization_name']),
        'organization_url' => esc_url_raw($input['organization_url'] ?? $defaults['organization_url']),
        'linkedin_url' => esc_url_raw($input['linkedin_url'] ?? ''),
        'instagram_url' => esc_url_raw($input['instagram_url'] ?? ''),
        'footer_note' => sanitize_textarea_field($input['footer_note'] ?? $current['footer_note'] ?? $defaults['footer_note']),
        'footer_cta_title' => sanitize_text_field($input['footer_cta_title'] ?? $current['footer_cta_title'] ?? $defaults['footer_cta_title']),
        'primary_cta_label' => sanitize_text_field($input['primary_cta_label'] ?? $current['primary_cta_label'] ?? $defaults['primary_cta_label']),
        'primary_cta_url' => esc_url_raw($input['primary_cta_url'] ?? $current['primary_cta_url'] ?? $defaults['primary_cta_url']),
        'demo_cta_label' => sanitize_text_field($input['demo_cta_label'] ?? $current['demo_cta_label'] ?? $defaults['demo_cta_label']),
        'promo_enabled' => !empty($input['promo_enabled']) ? '1' : '0',
        'promo_left_text' => sanitize_text_field($input['promo_left_text'] ?? $current['promo_left_text'] ?? $defaults['promo_left_text']),
        'promo_message' => sanitize_text_field($input['promo_message'] ?? $current['promo_message'] ?? $defaults['promo_message']),
        'promo_right_text' => sanitize_text_field($input['promo_right_text'] ?? $current['promo_right_text'] ?? $defaults['promo_right_text']),
        'promo_url' => esc_url_raw($input['promo_url'] ?? $current['promo_url'] ?? $defaults['promo_url']),
        'promo_dismissible' => !empty($input['promo_dismissible']) ? '1' : '0',
    ];

    foreach (localized_keys() as $key) {
        foreach (['en', 'tr'] as $locale) {
            $localized_key = $key . '_' . $locale;
            $raw = $input[$localized_key] ?? $current[$localized_key] ?? localized_default($key, $locale);
            $sanitized[$localized_key] = sanitize_localized_value($key, $locale, $raw);
        }
    }

    return $sanitized;
}

function sanitize_localized_value(string $key, string $locale, mixed $value): string
{
    $schema = localized_schema();
    if (!isset($schema[$key]) || !in_array($locale, ['en', 'tr'], true)) {
        return '';
    }

    $raw = is_string($value) ? $value : '';
    if (trim($raw) === '') {
        return localized_default($key, $locale);
    }

    return match ($schema[$key]['type']) {
        'url' => esc_url_raw($raw),
        'textarea' => sanitize_textarea_field($raw),
        default => sanitize_text_field($raw),
    };
}

function save_localized_values(array $input): void
{
    $options = get_all();

    foreach (localized_keys() as $key) {
        foreach (['en', 'tr'] as $locale) {
            $options[$key . '_' . $locale] = sanitize_localized_value(
                $key,
                $locale,
                $input[$key][$locale] ?? ''
            );
        }
    }

    update_option('myliba_options', $options, false);
}
