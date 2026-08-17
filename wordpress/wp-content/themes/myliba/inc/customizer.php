<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Customizer Settings and Controls for Myliba Theme
 */
function myliba_customize_register(\WP_Customize_Manager $wp_customize): void
{
    // Sanitize callbacks
    $sanitize_checkbox = static function ($checked): string {
        return (isset($checked) && true === (bool) $checked) ? '1' : '0';
    };

    $sanitize_text = static function ($value): string {
        return sanitize_text_field((string) $value);
    };

    $sanitize_textarea = static function ($value): string {
        return sanitize_textarea_field((string) $value);
    };

    $sanitize_url = static function ($value): string {
        return esc_url_raw((string) $value);
    };

    $sanitize_email = static function ($value): string {
        return sanitize_email((string) $value);
    };

    // =========================================================================
    // 1. PANEL: Myliba Theme Options (Görünüm > Özelleştir > Myliba Tema Ayarları)
    // =========================================================================
    $wp_customize->add_panel('myliba_theme_panel', [
        'title' => __('Myliba Theme Options', 'myliba'),
        'description' => __('Header, footer, promo bar, CTA buttons, and social media settings.', 'myliba'),
        'priority' => 30,
    ]);

    // -------------------------------------------------------------------------
    // SECTION: Header & Announcement Settings (Üstbilgi & Duyuru)
    // -------------------------------------------------------------------------
    $wp_customize->add_section('myliba_header_section', [
        'title' => __('Header & Announcement', 'myliba'),
        'panel' => 'myliba_theme_panel',
        'priority' => 10,
        'description' => __('Manage promo banner, portal button, demo button, and language switcher.', 'myliba'),
    ]);

    // Promo Bar Enabled
    $wp_customize->add_setting('promo_enabled', [
        'default' => '1',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_checkbox,
    ]);
    $wp_customize->add_control('promo_enabled', [
        'label' => __('Enable Announcement Bar (Üst Duyuru)', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'checkbox',
    ]);

    // Promo Left Text
    $wp_customize->add_setting('promo_left_text', [
        'default' => 'Yaklaşan atölye',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('promo_left_text', [
        'label' => __('Promo Left Badge', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'text',
    ]);

    // Promo Message
    $wp_customize->add_setting('promo_message', [
        'default' => 'Bir sonraki atölyemizde yerinizi ayırtın.',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_textarea,
    ]);
    $wp_customize->add_control('promo_message', [
        'label' => __('Promo Message', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'textarea',
    ]);

    // Promo Right Text
    $wp_customize->add_setting('promo_right_text', [
        'default' => 'Detay',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('promo_right_text', [
        'label' => __('Promo Right Action Text', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'text',
    ]);

    // Promo URL
    $wp_customize->add_setting('promo_url', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_url,
    ]);
    $wp_customize->add_control('promo_url', [
        'label' => __('Promo Link URL', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'url',
    ]);

    // Promo Dismissible
    $wp_customize->add_setting('promo_dismissible', [
        'default' => '1',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_checkbox,
    ]);
    $wp_customize->add_control('promo_dismissible', [
        'label' => __('Allow Visitors to Dismiss Promo Bar', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'checkbox',
    ]);

    // Header Language Switcher Enabled
    $wp_customize->add_setting('header_lang_switcher_enabled', [
        'default' => '1',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_checkbox,
    ]);
    $wp_customize->add_control('header_lang_switcher_enabled', [
        'label' => __('Show Language Switcher (TR / EN)', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'checkbox',
    ]);

    // Header Portal Login Button Enabled
    $wp_customize->add_setting('header_portal_enabled', [
        'default' => '1',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_checkbox,
    ]);
    $wp_customize->add_control('header_portal_enabled', [
        'label' => __('Show Portal Login Button', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'checkbox',
    ]);

    // Portal CTA Label
    $wp_customize->add_setting('portal_cta_label', [
        'default' => 'Portal girişi',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('portal_cta_label', [
        'label' => __('Portal Button Label', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'text',
    ]);

    // Portal URL
    $wp_customize->add_setting('portal_url', [
        'default' => 'https://portal.myliba.com/',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_url,
    ]);
    $wp_customize->add_control('portal_url', [
        'label' => __('Portal URL', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'url',
    ]);

    // Header Demo CTA Enabled
    $wp_customize->add_setting('header_demo_cta_enabled', [
        'default' => '1',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_checkbox,
    ]);
    $wp_customize->add_control('header_demo_cta_enabled', [
        'label' => __('Show Demo Request Button in Header', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'checkbox',
    ]);

    // Demo CTA Label
    $wp_customize->add_setting('demo_cta_label', [
        'default' => 'Demo talep et',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('demo_cta_label', [
        'label' => __('Demo Button Label', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'text',
    ]);

    // Demo URL
    $wp_customize->add_setting('demo_url', [
        'default' => '/tr/demo/',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('demo_url', [
        'label' => __('Demo URL', 'myliba'),
        'section' => 'myliba_header_section',
        'type' => 'text',
    ]);

    // -------------------------------------------------------------------------
    // SECTION: Footer General Settings (Altbilgi Genel Ayarları)
    // -------------------------------------------------------------------------
    $wp_customize->add_section('myliba_footer_section', [
        'title' => __('Footer Settings', 'myliba'),
        'panel' => 'myliba_theme_panel',
        'priority' => 20,
        'description' => __('Manage footer CTA banner, brand description, contact email, phone, and copyright.', 'myliba'),
    ]);

    // Footer CTA Enabled
    $wp_customize->add_setting('footer_cta_enabled', [
        'default' => '1',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_checkbox,
    ]);
    $wp_customize->add_control('footer_cta_enabled', [
        'label' => __('Show Footer CTA Banner', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'checkbox',
    ]);

    // Footer CTA Eyebrow
    $wp_customize->add_setting('footer_cta_eyebrow', [
        'default' => 'Culture, goals and performance',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('footer_cta_eyebrow', [
        'label' => __('Footer CTA Eyebrow', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'text',
    ]);

    // Footer CTA Title
    $wp_customize->add_setting('footer_cta_title', [
        'default' => 'Ready to make culture measurable?',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('footer_cta_title', [
        'label' => __('Footer CTA Main Title', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'text',
    ]);

    // Primary CTA Label (Contact Us)
    $wp_customize->add_setting('primary_cta_label', [
        'default' => 'Contact us',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('primary_cta_label', [
        'label' => __('Primary Button Label (Contact)', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'text',
    ]);

    // Primary CTA URL
    $wp_customize->add_setting('primary_cta_url', [
        'default' => '/tr/iletisim/',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('primary_cta_url', [
        'label' => __('Primary Button URL', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'text',
    ]);

    // Footer Note / Description
    $wp_customize->add_setting('footer_note', [
        'default' => 'OKR, kültür, etik ve güvenlik danışmanlığı.',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_textarea,
    ]);
    $wp_customize->add_control('footer_note', [
        'label' => __('Footer Brand Note / Slogan', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'textarea',
    ]);

    // Contact Email
    $wp_customize->add_setting('contact_email', [
        'default' => 'hello@myliba.com',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_email,
    ]);
    $wp_customize->add_control('contact_email', [
        'label' => __('Footer Contact Email', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'email',
    ]);

    // Phone Label
    $wp_customize->add_setting('phone_label', [
        'default' => '+90 553 986 86 99',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('phone_label', [
        'label' => __('Phone Number Display Label', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'text',
    ]);

    // Phone URL
    $wp_customize->add_setting('phone_url', [
        'default' => 'tel:+905539868699',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('phone_url', [
        'label' => __('Phone Link URL (tel:+...)', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'text',
    ]);

    // Organization Name / Copyright
    $wp_customize->add_setting('organization_name', [
        'default' => 'Myliba',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('organization_name', [
        'label' => __('Organization / Copyright Name', 'myliba'),
        'section' => 'myliba_footer_section',
        'type' => 'text',
    ]);

    // -------------------------------------------------------------------------
    // SECTION: Footer Column Headings (Altbilgi Kolon Başlıkları)
    // -------------------------------------------------------------------------
    $wp_customize->add_section('myliba_footer_columns_section', [
        'title' => __('Footer Column Headings', 'myliba'),
        'panel' => 'myliba_theme_panel',
        'priority' => 25,
        'description' => __('Customize headings for the 4 footer columns (menus can be assigned in Customize > Menus).', 'myliba'),
    ]);

    // Column 1 Title
    $wp_customize->add_setting('footer_col1_title', [
        'default' => 'Çözümlerimiz',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('footer_col1_title', [
        'label' => __('Column 1 Title (Solutions / Çözümlerimiz)', 'myliba'),
        'section' => 'myliba_footer_columns_section',
        'type' => 'text',
    ]);

    // Column 2 Title
    $wp_customize->add_setting('footer_col2_title', [
        'default' => 'Gelişim Merkezi',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('footer_col2_title', [
        'label' => __('Column 2 Title (Development Center / Gelişim Merkezi)', 'myliba'),
        'section' => 'myliba_footer_columns_section',
        'type' => 'text',
    ]);

    // Column 3 Title
    $wp_customize->add_setting('footer_col3_title', [
        'default' => 'Şirket',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('footer_col3_title', [
        'label' => __('Column 3 Title (Company / Şirket)', 'myliba'),
        'section' => 'myliba_footer_columns_section',
        'type' => 'text',
    ]);

    // Column 4 Title
    $wp_customize->add_setting('footer_col4_title', [
        'default' => 'Güvenlik ve Yasal',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_text,
    ]);
    $wp_customize->add_control('footer_col4_title', [
        'label' => __('Column 4 Title (Security & Legal / Güvenlik ve Yasal)', 'myliba'),
        'section' => 'myliba_footer_columns_section',
        'type' => 'text',
    ]);

    // -------------------------------------------------------------------------
    // SECTION: Social Media Links (Sosyal Medya)
    // -------------------------------------------------------------------------
    $wp_customize->add_section('myliba_social_section', [
        'title' => __('Social Media Links', 'myliba'),
        'panel' => 'myliba_theme_panel',
        'priority' => 30,
        'description' => __('Enter URLs for social media accounts displayed in the footer.', 'myliba'),
    ]);

    // LinkedIn URL
    $wp_customize->add_setting('linkedin_url', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_url,
    ]);
    $wp_customize->add_control('linkedin_url', [
        'label' => __('LinkedIn Profile URL', 'myliba'),
        'section' => 'myliba_social_section',
        'type' => 'url',
    ]);

    // Instagram URL
    $wp_customize->add_setting('instagram_url', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_url,
    ]);
    $wp_customize->add_control('instagram_url', [
        'label' => __('Instagram Profile URL', 'myliba'),
        'section' => 'myliba_social_section',
        'type' => 'url',
    ]);

    // Twitter / X URL
    $wp_customize->add_setting('twitter_url', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_url,
    ]);
    $wp_customize->add_control('twitter_url', [
        'label' => __('X / Twitter Profile URL', 'myliba'),
        'section' => 'myliba_social_section',
        'type' => 'url',
    ]);

    // YouTube URL
    $wp_customize->add_setting('youtube_url', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_url,
    ]);
    $wp_customize->add_control('youtube_url', [
        'label' => __('YouTube Channel URL', 'myliba'),
        'section' => 'myliba_social_section',
        'type' => 'url',
    ]);

    // Facebook URL
    $wp_customize->add_setting('facebook_url', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => $sanitize_url,
    ]);
    $wp_customize->add_control('facebook_url', [
        'label' => __('Facebook Page URL', 'myliba'),
        'section' => 'myliba_social_section',
        'type' => 'url',
    ]);
}
add_action('customize_register', 'myliba_customize_register');
