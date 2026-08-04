<?php

namespace Myliba\Core\Meta;

use Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

function boot(): void
{
    add_action('add_meta_boxes', __NAMESPACE__ . '\\register_meta_boxes');
    add_action('save_post', __NAMESPACE__ . '\\save', 10, 2);
    add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets');
}

function enqueue_admin_assets(): void
{
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_media();
}

function register_meta_boxes(string $post_type): void
{
    $current_post = get_post();
    $is_special_page = $post_type === 'page'
        && $current_post instanceof \WP_Post
        && (is_homepage_post($current_post->ID) || in_array($current_post->post_name, ['okr-kultur-akademisi', 'okr-culture-academy'], true));

    if (in_array($post_type, ['page', 'post', 'myliba_product', 'myliba_solution', 'myliba_academy', 'myliba_case_study', 'myliba_landing', 'myliba_event', 'myliba_ebook', 'myliba_report', 'myliba_team', 'myliba_client_logo', 'myliba_faq', 'myliba_testimonial'], true)) {
        add_meta_box('myliba_language', __('Myliba Language', 'myliba'), __NAMESPACE__ . '\\render_language_box', $post_type, 'side');
    }

    if (in_array($post_type, ['page', 'post', 'myliba_product', 'myliba_solution', 'myliba_academy', 'myliba_case_study', 'myliba_landing', 'myliba_event', 'myliba_ebook', 'myliba_report'], true)) {
        add_meta_box('myliba_hero', __('Myliba Hero', 'myliba'), __NAMESPACE__ . '\\render_hero_box', $post_type, 'normal', 'high');
        add_meta_box('myliba_seo', __('Myliba SEO', 'myliba'), __NAMESPACE__ . '\\render_seo_box', $post_type, 'normal');
    }

    $uses_conversion_template = in_array($post_type, ['myliba_product', 'myliba_academy', 'myliba_landing', 'myliba_ebook', 'myliba_report'], true)
        || ($post_type === 'page'
            && $current_post instanceof \WP_Post
            && get_page_template_slug($current_post->ID) === 'template-landing.php');

    if ($uses_conversion_template && !$is_special_page) {
        add_meta_box('myliba_conversion_content', __('Conversion Content', 'myliba'), __NAMESPACE__ . '\\render_conversion_box', $post_type, 'normal');
    }

    if ($post_type === 'page') {
        if ($current_post instanceof \WP_Post && is_homepage_post($current_post->ID)) {
            add_meta_box('myliba_homepage_sections', __('Myliba Homepage Sections', 'myliba'), __NAMESPACE__ . '\\render_homepage_box', $post_type, 'normal');
        }
        if ($current_post instanceof \WP_Post && in_array($current_post->post_name, ['okr-kultur-akademisi', 'okr-culture-academy'], true)) {
            add_meta_box('myliba_academy_page', __('Myliba Academy Landing Page', 'myliba'), __NAMESPACE__ . '\\render_academy_page_box', $post_type, 'normal');
        }
    }

    if ($post_type === 'myliba_academy') {
        add_meta_box('myliba_academy_program', __('Academy Program Details', 'myliba'), __NAMESPACE__ . '\\render_academy_program_box', $post_type, 'normal', 'high');
    }

    if ($post_type === 'myliba_event') {
        add_meta_box('myliba_event_details', __('Event Details', 'myliba'), __NAMESPACE__ . '\\render_event_box', $post_type, 'side');
    }

    if ($post_type === 'myliba_team') {
        add_meta_box('myliba_team_details', __('Team Details', 'myliba'), __NAMESPACE__ . '\\render_team_box', $post_type, 'side');
    }

    if ($post_type === 'myliba_client_logo') {
        add_meta_box('myliba_logo_details', __('Logo Details', 'myliba'), __NAMESPACE__ . '\\render_logo_box', $post_type, 'side');
    }

    if ($post_type === 'myliba_testimonial') {
        add_meta_box('myliba_testimonial_details', __('Testimonial Details', 'myliba'), __NAMESPACE__ . '\\render_testimonial_box', $post_type, 'side');
    }

    if ($post_type === 'myliba_faq') {
        add_meta_box('myliba_faq_details', __('FAQ Details', 'myliba'), __NAMESPACE__ . '\\render_faq_box', $post_type, 'side');
    }

    if ($post_type === 'myliba_submission') {
        add_meta_box('myliba_submission_details', __('Submission Details', 'myliba'), __NAMESPACE__ . '\\render_submission_box', $post_type, 'normal', 'high');
    }
}

function render_language_box(\WP_Post $post): void
{
    nonce();

    if (function_exists('pll_get_post_language')) {
        echo '<p>' . esc_html__('Polylang is active. Manage translations from the language panel.', 'myliba') . '</p>';
        return;
    }

    $language = get_post_meta($post->ID, '_myliba_language', true) ?: Options\get('default_locale', 'tr');
    $translation_key = get_post_meta($post->ID, '_myliba_translation_key', true);

    field_select('_myliba_language', __('Language', 'myliba'), $language, array_combine(Options\locales(), Options\locales()));
    field_text('_myliba_translation_key', __('Translation group key', 'myliba'), $translation_key, __('Use the same key for translated versions of the same content.', 'myliba'));
}

function render_hero_box(\WP_Post $post): void
{
    nonce();

    if (is_homepage_post($post->ID)) {
        echo '<p class="description">' . esc_html__('Edit the homepage hero copy inside Homepage Builder > Hero + dashboard preview.', 'myliba') . '</p>';
        echo '<p class="description">' . esc_html__('Use the featured image as the hero image.', 'myliba') . '</p>';
        return;
    }

    field_text('_myliba_eyebrow', __('Eyebrow', 'myliba'), get_post_meta($post->ID, '_myliba_eyebrow', true));
    field_text('_myliba_hero_title', __('Hero title override', 'myliba'), get_post_meta($post->ID, '_myliba_hero_title', true));
    field_textarea('_myliba_hero_subtitle', __('Hero subtitle', 'myliba'), get_post_meta($post->ID, '_myliba_hero_subtitle', true));
    field_text('_myliba_cta_label', __('Primary CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_cta_label', true));
    field_url('_myliba_cta_url', __('Primary CTA URL', 'myliba'), get_post_meta($post->ID, '_myliba_cta_url', true));
    echo '<p class="description">' . esc_html__('Use the featured image as the hero image.', 'myliba') . '</p>';
}

function render_seo_box(\WP_Post $post): void
{
    nonce();

    field_text('_myliba_seo_title', __('SEO title', 'myliba'), get_post_meta($post->ID, '_myliba_seo_title', true));
    field_textarea('_myliba_seo_description', __('Meta description', 'myliba'), get_post_meta($post->ID, '_myliba_seo_description', true));
    field_checkbox('_myliba_noindex', __('Noindex this content', 'myliba'), get_post_meta($post->ID, '_myliba_noindex', true) === '1');
}

function render_event_box(\WP_Post $post): void
{
    nonce();

    field_text('_myliba_event_date', __('Event date', 'myliba'), get_post_meta($post->ID, '_myliba_event_date', true), 'YYYY-MM-DD HH:MM');
    field_text('_myliba_event_location', __('Location', 'myliba'), get_post_meta($post->ID, '_myliba_event_location', true));
    field_url('_myliba_event_url', __('Registration URL', 'myliba'), get_post_meta($post->ID, '_myliba_event_url', true));
    field_select('_myliba_event_status', __('Status', 'myliba'), get_post_meta($post->ID, '_myliba_event_status', true) ?: 'upcoming', [
        'upcoming' => __('Upcoming', 'myliba'),
        'past' => __('Past', 'myliba'),
    ]);
}

function render_conversion_box(\WP_Post $post): void
{
    nonce();

    field_text('_myliba_label', __('Label / category', 'myliba'), get_post_meta($post->ID, '_myliba_label', true));
    field_textarea('_myliba_problem', __('Problem', 'myliba'), get_post_meta($post->ID, '_myliba_problem', true));
    field_textarea('_myliba_solution', __('Myliba solution', 'myliba'), get_post_meta($post->ID, '_myliba_solution', true));
    field_textarea('_myliba_benefits', __('Benefits', 'myliba'), get_post_meta($post->ID, '_myliba_benefits', true), __('One benefit per line.', 'myliba'));
    field_textarea('_myliba_related_modules', __('Related modules', 'myliba'), get_post_meta($post->ID, '_myliba_related_modules', true), __('One module per line.', 'myliba'));
    field_textarea('_myliba_faq_items', __('FAQ items', 'myliba'), get_post_meta($post->ID, '_myliba_faq_items', true), __('Use one item per line as Question | Answer.', 'myliba'));
    field_text('_myliba_cta_label', __('CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_cta_label', true));
    field_url('_myliba_cta_url', __('CTA URL', 'myliba'), get_post_meta($post->ID, '_myliba_cta_url', true));
}

function render_development_center_box(\WP_Post $post): void
{
    nonce();

    echo '<p class="description">Bu alanlar yalnızca Gelişim Merkezi sayfasının dinamik metinlerini yönetir.</p>';
    field_text('_myliba_development_section_eyebrow', 'Alt sayfalar üst etiketi', get_post_meta($post->ID, '_myliba_development_section_eyebrow', true));
    field_textarea('_myliba_development_section_title', 'Alt sayfalar bölüm başlığı', get_post_meta($post->ID, '_myliba_development_section_title', true));
    field_textarea('_myliba_development_section_text', 'Alt sayfalar bölüm açıklaması', get_post_meta($post->ID, '_myliba_development_section_text', true));
    field_text('_myliba_development_ebook_label', 'e-Kitaplar kart başlığı', get_post_meta($post->ID, '_myliba_development_ebook_label', true));
    field_textarea('_myliba_development_ebook_text', 'e-Kitaplar kart açıklaması', get_post_meta($post->ID, '_myliba_development_ebook_text', true));
    field_text('_myliba_development_report_label', 'Raporlar ve Trendler kart başlığı', get_post_meta($post->ID, '_myliba_development_report_label', true));
    field_textarea('_myliba_development_report_text', 'Raporlar ve Trendler kart açıklaması', get_post_meta($post->ID, '_myliba_development_report_text', true));
    field_text('_myliba_development_blog_label', 'Blog kart başlığı', get_post_meta($post->ID, '_myliba_development_blog_label', true));
    field_textarea('_myliba_development_blog_text', 'Blog kart açıklaması', get_post_meta($post->ID, '_myliba_development_blog_text', true));
    field_text('_myliba_development_events_label', 'Etkinlikler kart başlığı', get_post_meta($post->ID, '_myliba_development_events_label', true));
    field_textarea('_myliba_development_events_text', 'Etkinlikler kart açıklaması', get_post_meta($post->ID, '_myliba_development_events_text', true));
    field_text('_myliba_development_card_cta', 'Kart bağlantı etiketi', get_post_meta($post->ID, '_myliba_development_card_cta', true));
}

function render_academy_page_box(\WP_Post $post): void
{
    nonce();

    echo '<p class="description">' . esc_html__('These fields power the database-driven Academy landing page. Empty sections are not rendered.', 'myliba') . '</p>';
    echo '<h3>' . esc_html__('Hero and navigation', 'myliba') . '</h3>';
    field_text('_myliba_academy_hero_secondary_label', __('Secondary CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_hero_secondary_label', true));
    field_url('_myliba_academy_hero_secondary_url', __('Secondary CTA URL', 'myliba'), get_post_meta($post->ID, '_myliba_academy_hero_secondary_url', true));
    field_text('_myliba_academy_hero_tertiary_label', __('Tertiary CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_hero_tertiary_label', true));
    field_url('_myliba_academy_hero_tertiary_url', __('Tertiary CTA URL', 'myliba'), get_post_meta($post->ID, '_myliba_academy_hero_tertiary_url', true));
    field_textarea('_myliba_academy_hero_badges', __('Hero badges', 'myliba'), get_post_meta($post->ID, '_myliba_academy_hero_badges', true), __('One row per line as Value | Label.', 'myliba'));
    field_media('_myliba_academy_icf_image', __('ICF approval badge', 'myliba'), get_post_meta($post->ID, '_myliba_academy_icf_image', true));
    field_media('_myliba_academy_certificate_image', __('Certificate mock-up', 'myliba'), get_post_meta($post->ID, '_myliba_academy_certificate_image', true));
    field_media('_myliba_academy_digital_badge_image', __('Digital badge', 'myliba'), get_post_meta($post->ID, '_myliba_academy_digital_badge_image', true));
    field_media('_myliba_academy_platform_image', __('Myliba platform image', 'myliba'), get_post_meta($post->ID, '_myliba_academy_platform_image', true));
    field_textarea('_myliba_academy_nav_items', __('Sticky section navigation', 'myliba'), get_post_meta($post->ID, '_myliba_academy_nav_items', true), __('One row per line as Label | anchor-without-hash.', 'myliba'));

    echo '<h3>' . esc_html__('Trust and programs introduction', 'myliba') . '</h3>';
    field_text('_myliba_academy_organization_name', __('Educational organization name', 'myliba'), get_post_meta($post->ID, '_myliba_academy_organization_name', true));
    field_text('_myliba_academy_trust_title', __('Client references title', 'myliba'), get_post_meta($post->ID, '_myliba_academy_trust_title', true));
    field_text('_myliba_academy_trust_label', __('Client references label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_trust_label', true));
    field_textarea('_myliba_academy_trust_text', __('Client references description', 'myliba'), get_post_meta($post->ID, '_myliba_academy_trust_text', true));
    field_text('_myliba_academy_programs_eyebrow', __('Programs eyebrow', 'myliba'), get_post_meta($post->ID, '_myliba_academy_programs_eyebrow', true));
    field_textarea('_myliba_academy_programs_title', __('Programs title', 'myliba'), get_post_meta($post->ID, '_myliba_academy_programs_title', true));
    field_textarea('_myliba_academy_programs_text', __('Programs introduction', 'myliba'), get_post_meta($post->ID, '_myliba_academy_programs_text', true));
    field_text('_myliba_academy_benefits_title', __('Program benefits heading', 'myliba'), get_post_meta($post->ID, '_myliba_academy_benefits_title', true));
    field_text('_myliba_academy_modules_title', __('Program modules heading', 'myliba'), get_post_meta($post->ID, '_myliba_academy_modules_title', true));

    echo '<h3>' . esc_html__('Approach, proof and testimonials', 'myliba') . '</h3>';
    field_textarea('_myliba_academy_approach_title', __('Approach title', 'myliba'), get_post_meta($post->ID, '_myliba_academy_approach_title', true));
    field_textarea('_myliba_academy_approach_steps', __('Approach steps', 'myliba'), get_post_meta($post->ID, '_myliba_academy_approach_steps', true), __('One row per line as Title | Description.', 'myliba'));
    field_textarea('_myliba_academy_stats', __('Statistics', 'myliba'), get_post_meta($post->ID, '_myliba_academy_stats', true), __('One row per line as Value | Label.', 'myliba'));
    field_text('_myliba_academy_testimonials_title', __('Testimonials title', 'myliba'), get_post_meta($post->ID, '_myliba_academy_testimonials_title', true));
    field_text('_myliba_academy_faq_title', __('FAQ title', 'myliba'), get_post_meta($post->ID, '_myliba_academy_faq_title', true));
    field_text('_myliba_academy_faq_group', __('FAQ group', 'myliba'), get_post_meta($post->ID, '_myliba_academy_faq_group', true), __('Only FAQ records with this group are shown.', 'myliba'));

    echo '<h3>' . esc_html__('Final call to action and contact form', 'myliba') . '</h3>';
    field_textarea('_myliba_academy_final_title', __('Final CTA title', 'myliba'), get_post_meta($post->ID, '_myliba_academy_final_title', true));
    field_textarea('_myliba_academy_final_text', __('Final CTA description', 'myliba'), get_post_meta($post->ID, '_myliba_academy_final_text', true));
    field_text('_myliba_academy_final_primary_label', __('Final primary CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_final_primary_label', true));
    field_text('_myliba_academy_final_secondary_label', __('Final secondary CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_final_secondary_label', true));
    field_text('_myliba_academy_contact_title', __('Form title', 'myliba'), get_post_meta($post->ID, '_myliba_academy_contact_title', true));
    field_textarea('_myliba_academy_contact_text', __('Form description', 'myliba'), get_post_meta($post->ID, '_myliba_academy_contact_text', true));
    field_text('_myliba_academy_form_button', __('Form button label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_form_button', true));
    field_textarea('_myliba_academy_form_success', __('Successful submission message', 'myliba'), get_post_meta($post->ID, '_myliba_academy_form_success', true));
    field_textarea('_myliba_academy_kvkk_text', __('KVKK consent label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_kvkk_text', true));
}

function render_academy_program_box(\WP_Post $post): void
{
    nonce();

    field_select('_myliba_academy_layout', __('Program presentation', 'myliba'), get_post_meta($post->ID, '_myliba_academy_layout', true) ?: 'standard', [
        'featured' => __('Featured certificate program', 'myliba'),
        'leadership' => __('Leadership program', 'myliba'),
        'consulting' => __('Consulting program', 'myliba'),
        'standard' => __('Standard', 'myliba'),
    ]);
    field_text('_myliba_academy_program_eyebrow', __('Program eyebrow', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_eyebrow', true));
    field_textarea('_myliba_academy_program_benefits', __('Program benefits', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_benefits', true), __('One benefit per line.', 'myliba'));
    field_textarea('_myliba_academy_program_badges', __('Information badges', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_badges', true), __('One badge per line.', 'myliba'));
    field_textarea('_myliba_academy_program_modules', __('Program modules', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_modules', true), __('One row per line as Module title | Detail 1; Detail 2; Detail 3.', 'myliba'));
    field_text('_myliba_academy_program_primary_label', __('Primary CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_primary_label', true));
    field_url('_myliba_academy_program_primary_url', __('Primary CTA URL (optional)', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_primary_url', true));
    field_text('_myliba_academy_program_secondary_label', __('Secondary CTA label', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_secondary_label', true));
    field_url('_myliba_academy_program_secondary_url', __('Secondary CTA URL (optional)', 'myliba'), get_post_meta($post->ID, '_myliba_academy_program_secondary_url', true));
    field_text('_myliba_academy_start_period', __('Starting period', 'myliba'), get_post_meta($post->ID, '_myliba_academy_start_period', true));
    field_textarea('_myliba_academy_certificate_info', __('Certificate information', 'myliba'), get_post_meta($post->ID, '_myliba_academy_certificate_info', true));
    field_number('_myliba_order', __('Sort order', 'myliba'), get_post_meta($post->ID, '_myliba_order', true));
}

function render_homepage_box(\WP_Post $post): void
{
    nonce();

    echo '<p class="description">' . esc_html__('These fields are used by the custom front page template. Edit the page selected under Settings > Reading as the homepage.', 'myliba') . '</p>';
    render_homepage_builder($post);
}

function render_homepage_builder(\WP_Post $post): void
{
    $sections = homepage_sections($post->ID);
    $definitions = homepage_section_definitions();

    echo '<style>
        .myliba-builder{display:grid;gap:12px;margin:16px 0 22px}
        .myliba-builder-card{background:#fff;border:1px solid #dcdcde;border-radius:8px;overflow:hidden}
        .myliba-builder-card.is-open{border-color:#2271b1}
        .myliba-builder-card__head{align-items:center;display:grid;gap:10px;grid-template-columns:auto minmax(0,1fr) 80px auto;min-height:34px;padding:14px;cursor:pointer}
        .myliba-builder-card__handle{background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;cursor:grab;font-weight:700;padding:6px 9px;line-height:1}
        .myliba-builder-card__main{display:grid;gap:4px;min-width:0}
        .myliba-builder-card__title-row{align-items:center;display:flex;gap:8px;min-width:0}
        .myliba-builder-card__enabled{align-items:center;display:flex;margin:0}
        .myliba-builder-card__title{background:none;border:0;color:#1d2327;cursor:pointer;font-weight:700;margin:0;min-width:0;overflow:hidden;padding:0;text-align:left;text-overflow:ellipsis;white-space:nowrap}
        .myliba-builder-card__title:hover{color:#2271b1}
        .myliba-builder-card__source-line,.myliba-builder-card__summary{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .myliba-builder-card__source-line{color:#646970;font-size:12px}
        .myliba-builder-card__summary{color:#1d2327;font-size:12px}
        .myliba-builder-card__order{width:72px}
        .myliba-builder-card__toggle{background:none;border:1px solid #dcdcde;border-radius:6px;cursor:pointer;font-size:18px;line-height:1;padding:4px 10px;color:#2271b1;transition:transform .2s ease,background .15s}
        .myliba-builder-card__toggle:hover{background:#f0f6fc}
        .myliba-builder-card.is-open .myliba-builder-card__toggle{transform:rotate(180deg);background:#f0f6fc}
        .myliba-builder-card__body{display:none;border-top:1px solid #dcdcde;padding:16px 14px 10px;background:#f9f9f9}
        .myliba-builder-card.is-open .myliba-builder-card__body{display:block}
        .myliba-builder-card__body[hidden]{display:none}
        .myliba-builder-card__body p{margin:0 0 12px}
        .myliba-builder-card__body .widefat{background:#fff}
        .myliba-builder-card__source{color:#646970;font-size:12px;font-style:italic;margin:0 0 12px;padding:6px 10px;background:#fff;border:1px solid #e0e0e0;border-radius:4px}
        .myliba-builder-card__notice{background:#fff;border-left:4px solid #72aee6;padding:10px 12px}
        .myliba-hero-editor{display:grid;gap:12px;margin:14px 0}
        .myliba-hero-slide{background:#fff;border:1px solid #c3c4c7;border-radius:8px;overflow:hidden}
        .myliba-hero-slide__head{align-items:center;background:#f6f7f7;display:grid;gap:10px;grid-template-columns:auto minmax(0,1fr) auto;padding:10px 12px}
        .myliba-hero-slide__handle,.myliba-hero-button__handle{cursor:grab;color:#646970;font-weight:700}
        .myliba-hero-slide__title{font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .myliba-hero-slide__actions{align-items:center;display:flex;flex-wrap:wrap;gap:8px}
        .myliba-hero-slide__body{display:grid;gap:12px;padding:14px}
        .myliba-hero-slide.is-collapsed .myliba-hero-slide__body{display:none}
        .myliba-hero-grid{display:grid;gap:12px;grid-template-columns:repeat(2,minmax(0,1fr))}
        .myliba-hero-grid .myliba-hero-field--wide{grid-column:1/-1}
        .myliba-hero-field label{display:block;font-weight:600;margin-bottom:4px}
        .myliba-hero-buttons{display:grid;gap:8px}
        .myliba-hero-button{align-items:end;background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;display:grid;gap:8px;grid-template-columns:auto minmax(150px,1fr) minmax(180px,1.5fr) 130px auto auto;padding:10px}
        .myliba-hero-button label{font-size:12px;font-weight:600}
        .myliba-hero-empty{color:#646970;font-style:italic;margin:0}
        .myliba-hero-editor-toolbar{align-items:center;display:flex;gap:10px;margin-top:12px}
        @media (max-width:782px){
            .myliba-builder-card__head{grid-template-columns:auto minmax(0,1fr) auto}
            .myliba-builder-card__order{grid-column:2 / 3;width:100%}
            .myliba-builder-card__source-line,.myliba-builder-card__summary{white-space:normal}
            .myliba-hero-grid{grid-template-columns:1fr}
            .myliba-hero-grid .myliba-hero-field--wide{grid-column:auto}
            .myliba-hero-button{align-items:stretch;grid-template-columns:auto 1fr}
            .myliba-hero-button .myliba-hero-field{grid-column:2}
        }
    </style>';

    echo '<h3>' . esc_html__('Homepage Builder', 'myliba') . '</h3>';
    echo '<p class="description">' . esc_html__('Enable, disable, and reorder homepage sections. Click the arrow to expand a section and edit its content fields.', 'myliba') . '</p>';
    echo '<div class="myliba-builder">';

    foreach ($sections as $section) {
        $key = $section['key'];
        $label = $definitions[$key]['label'] ?? $key;
        $summary = homepage_section_summary($post->ID, $key);
        $panel_id = 'myliba-section-fields-' . sanitize_html_class($key);

        echo '<div class="myliba-builder-card" data-section-key="' . esc_attr($key) . '">';

        // Card header
        echo '<div class="myliba-builder-card__head">';
        echo '<span class="myliba-builder-card__handle" aria-hidden="true">&#8942;&#8942;</span>';
        echo '<div class="myliba-builder-card__main">';
        echo '<div class="myliba-builder-card__title-row">';
        printf(
            '<label class="myliba-builder-card__enabled"><input type="checkbox" name="_myliba_home_builder[%1$s][enabled]" value="1" %2$s><span class="screen-reader-text">%3$s</span></label>',
            esc_attr($key),
            checked(!empty($section['enabled']), true, false),
            esc_html(sprintf(__('Enable %s section', 'myliba'), $label))
        );
        echo '<button type="button" class="myliba-builder-card__title">' . esc_html($label) . '</button>';
        echo '</div>';
        echo '<span class="myliba-builder-card__source-line">' . esc_html($definitions[$key]['source'] ?? '') . '</span>';
        echo '<span class="myliba-builder-card__summary">' . esc_html($summary) . '</span>';
        echo '</div>';
        printf(
            '<input class="myliba-builder-card__order" type="number" name="_myliba_home_builder[%1$s][order]" value="%2$d" aria-label="%3$s">',
            esc_attr($key),
            (int) $section['order'],
            esc_attr__('Section order', 'myliba')
        );
        echo '<button type="button" class="myliba-builder-card__toggle" aria-expanded="false" aria-controls="' . esc_attr($panel_id) . '" aria-label="' . esc_attr__('Toggle section fields', 'myliba') . '">&#9660;</button>';
        echo '</div>';
        echo '<input type="hidden" name="_myliba_home_builder[' . esc_attr($key) . '][key]" value="' . esc_attr($key) . '">';

        // Card body - collapsible fields
        echo '<div class="myliba-builder-card__body" id="' . esc_attr($panel_id) . '" hidden>';
        if (!empty($definitions[$key]['fields'])) {
            echo '<p class="myliba-builder-card__source">' . esc_html($definitions[$key]['fields']) . '</p>';
        }
        render_section_fields($post, $key);
        echo '</div>';

        echo '</div>';
    }

    echo '</div>';
    echo '<script>
        jQuery(function($){
            var $builder = $(".myliba-builder");
            var storageKey = "mylibaHomeBuilderOpen:' . esc_js((string) $post->ID) . '";

            function readOpenKeys(){
                try {
                    var raw = window.localStorage ? window.localStorage.getItem(storageKey) : null;
                    return raw === null ? null : JSON.parse(raw);
                } catch (error) {
                    return null;
                }
            }

            function writeOpenKeys(){
                try {
                    if (!window.localStorage) {
                        return;
                    }

                    var keys = $builder.find(".myliba-builder-card.is-open").map(function(){
                        return $(this).data("section-key");
                    }).get();

                    window.localStorage.setItem(storageKey, JSON.stringify(keys));
                } catch (error) {}
            }

            function setCardOpen($card, isOpen, persist){
                var $button = $card.find("> .myliba-builder-card__head .myliba-builder-card__toggle");
                var $body = $card.find("> .myliba-builder-card__body");

                $card.toggleClass("is-open", isOpen);
                $button.attr("aria-expanded", isOpen ? "true" : "false");
                $body.prop("hidden", !isOpen);

                if (persist !== false) {
                    writeOpenKeys();
                }
            }

            function toggleCard($card){
                setCardOpen($card, !$card.hasClass("is-open"));
            }

            var openKeys = readOpenKeys();
            if ($.isArray(openKeys)) {
                openKeys.forEach(function(key){
                    setCardOpen($builder.find(".myliba-builder-card").filter(function(){
                        return $(this).data("section-key") === key;
                    }), true, false);
                });
            } else {
                setCardOpen($builder.find(".myliba-builder-card[data-section-key=\'hero\']").first(), true, false);
            }

            // Sortable drag & drop
            if ($builder.sortable) {
                $builder.sortable({
                    handle: ".myliba-builder-card__handle",
                    update: function(){
                        $builder.find(".myliba-builder-card__order").each(function(index){
                            $(this).val((index + 1) * 10);
                        });
                    }
                });
            }

            // Collapsible toggle
            $builder.on("click", ".myliba-builder-card__toggle", function(e){
                e.preventDefault();
                toggleCard($(this).closest(".myliba-builder-card"));
            });

            $builder.on("click", ".myliba-builder-card__title", function(e){
                e.preventDefault();
                toggleCard($(this).closest(".myliba-builder-card"));
            });

            $builder.on("click", ".myliba-builder-card__head", function(e){
                if ($(e.target).closest("input, button, a, textarea, select, label, .myliba-builder-card__handle").length) {
                    return;
                }

                toggleCard($(this).closest(".myliba-builder-card"));
            });
        });
    </script>';
}

function hero_slides_v2(int $post_id): array
{
    if (metadata_exists('post', $post_id, '_myliba_home_hero_slides_v2')) {
        $saved = get_post_meta($post_id, '_myliba_home_hero_slides_v2', true);
        if (is_string($saved)) {
            $decoded = json_decode($saved, true);
            $saved = is_array($decoded) ? $decoded : [];
        }

        return is_array($saved) ? array_values($saved) : [];
    }

    $legacy = (string) get_post_meta($post_id, '_myliba_home_hero_slides', true);
    $slides = [];
    foreach (preg_split('/\r\n|\r|\n/', $legacy) ?: [] as $index => $line) {
        if (trim($line) === '') {
            continue;
        }

        [$eyebrow, $title, $text, $primary_label, $primary_url, $secondary_label, $secondary_url] = array_pad(array_map('trim', explode('|', $line)), 7, '');
        $buttons = [];
        if ($primary_label !== '') {
            $buttons[] = ['label' => $primary_label, 'url' => $primary_url, 'style' => 'primary', 'new_tab' => false, 'aria_label' => ''];
        }
        if ($secondary_label !== '') {
            $buttons[] = ['label' => $secondary_label, 'url' => $secondary_url, 'style' => 'ghost', 'new_tab' => false, 'aria_label' => ''];
        }

        $image_number = count($slides) + 1;
        $slides[] = [
            'id' => 'legacy-' . ($index + 1),
            'enabled' => true,
            'eyebrow' => $eyebrow,
            'title' => $title,
            'text' => $text,
            'image_id' => $image_number <= 3 ? absint(get_post_meta($post_id, '_myliba_home_hero_image_' . $image_number, true)) : 0,
            'image_alt' => $image_number <= 3 ? (string) get_post_meta($post_id, '_myliba_home_hero_image_' . $image_number . '_alt', true) : '',
            'buttons' => $buttons,
        ];
    }

    return $slides;
}

function render_hero_button_editor(array $button, string $slide_key, string $button_key): void
{
    $name = '_myliba_home_hero_slides_v2[' . $slide_key . '][buttons][' . $button_key . ']';
    $style = in_array(($button['style'] ?? ''), ['primary', 'ghost', 'link'], true) ? $button['style'] : 'ghost';

    echo '<div class="myliba-hero-button">';
    echo '<span class="myliba-hero-button__handle" aria-hidden="true">&#8942;&#8942;</span>';
    echo '<div class="myliba-hero-field"><label>' . esc_html__('Button label', 'myliba') . '</label><input class="widefat" type="text" name="' . esc_attr($name . '[label]') . '" value="' . esc_attr((string) ($button['label'] ?? '')) . '"></div>';
    echo '<div class="myliba-hero-field"><label>' . esc_html__('URL', 'myliba') . '</label><input class="widefat" type="text" inputmode="url" name="' . esc_attr($name . '[url]') . '" value="' . esc_attr((string) ($button['url'] ?? '')) . '" placeholder="/tr/demo/"></div>';
    echo '<div class="myliba-hero-field"><label>' . esc_html__('Style', 'myliba') . '</label><select class="widefat" name="' . esc_attr($name . '[style]') . '">';
    foreach (['primary' => __('Primary', 'myliba'), 'ghost' => __('Secondary', 'myliba'), 'link' => __('Text link', 'myliba')] as $value => $label) {
        echo '<option value="' . esc_attr($value) . '" ' . selected($style, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select></div>';
    echo '<label><input type="checkbox" name="' . esc_attr($name . '[new_tab]') . '" value="1" ' . checked(!empty($button['new_tab']), true, false) . '> ' . esc_html__('New tab', 'myliba') . '</label>';
    echo '<button type="button" class="button-link-delete myliba-hero-button__remove">' . esc_html__('Remove', 'myliba') . '</button>';
    echo '<input type="hidden" name="' . esc_attr($name . '[aria_label]') . '" value="' . esc_attr((string) ($button['aria_label'] ?? '')) . '">';
    echo '</div>';
}

function render_hero_slide_editor(array $slide, string $slide_key): void
{
    $name = '_myliba_home_hero_slides_v2[' . $slide_key . ']';
    $title = trim((string) ($slide['title'] ?? '')) ?: __('Untitled slide', 'myliba');

    echo '<article class="myliba-hero-slide" data-slide-key="' . esc_attr($slide_key) . '">';
    echo '<header class="myliba-hero-slide__head">';
    echo '<span class="myliba-hero-slide__handle" aria-hidden="true">&#8942;&#8942;</span>';
    echo '<span class="myliba-hero-slide__title">' . esc_html($title) . '</span>';
    echo '<div class="myliba-hero-slide__actions">';
    echo '<label><input type="hidden" name="' . esc_attr($name . '[enabled]') . '" value="0"><input type="checkbox" name="' . esc_attr($name . '[enabled]') . '" value="1" ' . checked(!isset($slide['enabled']) || !empty($slide['enabled']), true, false) . '> ' . esc_html__('Active', 'myliba') . '</label>';
    echo '<button type="button" class="button-link myliba-hero-slide__duplicate">' . esc_html__('Duplicate', 'myliba') . '</button>';
    echo '<button type="button" class="button-link-delete myliba-hero-slide__remove">' . esc_html__('Remove', 'myliba') . '</button>';
    echo '<button type="button" class="button myliba-hero-slide__toggle" aria-expanded="true">' . esc_html__('Collapse', 'myliba') . '</button>';
    echo '</div></header>';
    echo '<div class="myliba-hero-slide__body"><div class="myliba-hero-grid">';
    echo '<input type="hidden" name="' . esc_attr($name . '[id]') . '" value="' . esc_attr((string) ($slide['id'] ?? $slide_key)) . '">';
    echo '<div class="myliba-hero-field"><label>' . esc_html__('Eyebrow', 'myliba') . '</label><input class="widefat" type="text" name="' . esc_attr($name . '[eyebrow]') . '" value="' . esc_attr((string) ($slide['eyebrow'] ?? '')) . '"></div>';
    echo '<div class="myliba-hero-field"><label>' . esc_html__('Title', 'myliba') . '</label><input class="widefat myliba-hero-slide__title-input" type="text" name="' . esc_attr($name . '[title]') . '" value="' . esc_attr((string) ($slide['title'] ?? '')) . '"></div>';
    echo '<div class="myliba-hero-field myliba-hero-field--wide"><label>' . esc_html__('Text', 'myliba') . '</label><textarea class="widefat" rows="3" name="' . esc_attr($name . '[text]') . '">' . esc_textarea((string) ($slide['text'] ?? '')) . '</textarea></div>';
    echo '</div>';
    field_media($name . '[image_id]', __('Slide image', 'myliba'), $slide['image_id'] ?? 0);
    echo '<div class="myliba-hero-field"><label>' . esc_html__('Image alternative text', 'myliba') . '</label><input class="widefat" type="text" name="' . esc_attr($name . '[image_alt]') . '" value="' . esc_attr((string) ($slide['image_alt'] ?? '')) . '"></div>';
    echo '<h4>' . esc_html__('Buttons', 'myliba') . '</h4><div class="myliba-hero-buttons">';
    foreach (($slide['buttons'] ?? []) as $button_index => $button) {
        if (is_array($button)) {
            render_hero_button_editor($button, $slide_key, 'button-' . ($button_index + 1));
        }
    }
    echo '<p class="myliba-hero-empty"' . (!empty($slide['buttons']) ? ' hidden' : '') . '>' . esc_html__('No buttons yet.', 'myliba') . '</p>';
    echo '</div><p><button type="button" class="button myliba-hero-button__add">' . esc_html__('Add button', 'myliba') . '</button></p>';
    echo '</div></article>';
}

function render_hero_slides_editor(int $post_id): void
{
    $slides = hero_slides_v2($post_id);
    echo '<input type="hidden" name="_myliba_home_hero_slides_v2_present" value="1">';
    echo '<p class="description">' . esc_html__('Add and reorder slides. Each slide can have its own image and multiple buttons. Drag cards and buttons to change their order.', 'myliba') . '</p>';
    echo '<div class="myliba-hero-editor">';
    foreach ($slides as $index => $slide) {
        if (is_array($slide)) {
            render_hero_slide_editor($slide, 'slide-' . ($index + 1));
        }
    }
    echo '</div><div class="myliba-hero-editor-toolbar"><button type="button" class="button button-primary myliba-hero-slide__add">' . esc_html__('Add slide', 'myliba') . '</button><span class="description">' . esc_html__('For best visual balance, use no more than three buttons per slide.', 'myliba') . '</span></div>';

    print_media_field_script();
    echo '<script type="text/html" id="myliba-hero-slide-template">';
    render_hero_slide_editor(['enabled' => true, 'buttons' => []], '__SLIDE__');
    echo '</script><script type="text/html" id="myliba-hero-button-template">';
    render_hero_button_editor([], '__SLIDE__', '__BUTTON__');
    echo '</script>';
    echo '<script>
        jQuery(function($){
            var $editor = $(".myliba-hero-editor");
            var slideTemplate = $("#myliba-hero-slide-template").html();
            var buttonTemplate = $("#myliba-hero-button-template").html();
            var sequence = Date.now();
            function nextKey(prefix){ sequence += 1; return prefix + "-" + sequence; }
            function refreshEmpty($slide){
                $slide.find(".myliba-hero-empty").prop("hidden", $slide.find(".myliba-hero-button").length > 0);
            }
            function initSortables(){
                if ($editor.sortable) {
                    $editor.sortable({items:"> .myliba-hero-slide",handle:".myliba-hero-slide__handle"});
                    $editor.find(".myliba-hero-buttons").sortable({items:"> .myliba-hero-button",handle:".myliba-hero-button__handle"});
                }
            }
            $(".myliba-hero-slide__add").on("click", function(){
                var key = nextKey("slide");
                $editor.append(slideTemplate.replaceAll("__SLIDE__", key));
                initSortables();
            });
            $editor.on("click", ".myliba-hero-slide__toggle", function(){
                var $slide = $(this).closest(".myliba-hero-slide");
                var collapsed = !$slide.hasClass("is-collapsed");
                $slide.toggleClass("is-collapsed", collapsed);
                $(this).attr("aria-expanded", collapsed ? "false" : "true").text(collapsed ? ' . wp_json_encode(__('Expand', 'myliba')) . ' : ' . wp_json_encode(__('Collapse', 'myliba')) . ');
            });
            $editor.on("input", ".myliba-hero-slide__title-input", function(){
                $(this).closest(".myliba-hero-slide").find(".myliba-hero-slide__title").text($(this).val() || ' . wp_json_encode(__('Untitled slide', 'myliba')) . ');
            });
            $editor.on("click", ".myliba-hero-slide__remove", function(){
                if (window.confirm(' . wp_json_encode(__('Remove this slide? It will be permanently removed after updating the page.', 'myliba')) . ')) {
                    $(this).closest(".myliba-hero-slide").remove();
                }
            });
            $editor.on("click", ".myliba-hero-slide__duplicate", function(){
                var $source = $(this).closest(".myliba-hero-slide");
                var oldKey = String($source.data("slide-key"));
                var newKey = nextKey("slide");
                var $copy = $source.clone(false, false).attr("data-slide-key", newKey).removeClass("is-collapsed");
                $copy.find("[name]").each(function(){ this.name = this.name.replace("[" + oldKey + "]", "[" + newKey + "]"); });
                $copy.find("input[name$=\"[id]\"]").first().val(nextKey("hero"));
                $copy.find(".myliba-hero-slide__title").append(" — ' . esc_js(__('Copy', 'myliba')) . '");
                $source.after($copy);
                initSortables();
            });
            $editor.on("click", ".myliba-hero-button__add", function(){
                var $slide = $(this).closest(".myliba-hero-slide");
                var slideKey = String($slide.data("slide-key"));
                $slide.find(".myliba-hero-buttons").append(buttonTemplate.replaceAll("__SLIDE__", slideKey).replaceAll("__BUTTON__", nextKey("button")));
                refreshEmpty($slide); initSortables();
            });
            $editor.on("click", ".myliba-hero-button__remove", function(){
                var $slide = $(this).closest(".myliba-hero-slide");
                $(this).closest(".myliba-hero-button").remove(); refreshEmpty($slide);
            });
            initSortables();
        });
    </script>';
}

function render_section_fields(\WP_Post $post, string $key): void
{
    $id = $post->ID;

    switch ($key) {
        case 'hero':
            if (!is_homepage_post($id)) {
                echo '<p class="myliba-builder-card__notice description">' . esc_html__('Hero eyebrow, title, and subtitle are edited in the Myliba Hero box. When this page is selected as the homepage, those fields appear here.', 'myliba') . '</p>';
            }

            render_hero_slides_editor($id);
            field_textarea('_myliba_home_hero_proof', __('Hero proof pills', 'myliba'), get_post_meta($id, '_myliba_home_hero_proof', true), __('One item per line.', 'myliba'));
            field_textarea('_myliba_home_hero_metrics', __('Floating hero metrics', 'myliba'), get_post_meta($id, '_myliba_home_hero_metrics', true), __('One row per line as Value | Label. Three rows are recommended.', 'myliba'));
            break;

        case 'trust_bar':
            field_textarea('_myliba_home_trust_title', __('Trust section title', 'myliba'), get_post_meta($id, '_myliba_home_trust_title', true));
            field_text('_myliba_home_trust_logo_label', __('Client logos label', 'myliba'), get_post_meta($id, '_myliba_home_trust_logo_label', true));
            field_textarea('_myliba_home_trust_items', __('Trust section items', 'myliba'), get_post_meta($id, '_myliba_home_trust_items', true), __('One item per line.', 'myliba'));
            break;

        case 'social_proof':
            field_textarea('_myliba_home_social_proof_items', __('Social proof metrics', 'myliba'), get_post_meta($id, '_myliba_home_social_proof_items', true), __('One row per line as Value | Label.', 'myliba'));
            break;

        case 'why_myliba':
            field_text('_myliba_home_why_eyebrow', __('Why Myliba eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_why_eyebrow', true));
            field_textarea('_myliba_home_why_title', __('Why Myliba title', 'myliba'), get_post_meta($id, '_myliba_home_why_title', true));
            field_textarea('_myliba_home_why_text', __('Why Myliba text', 'myliba'), get_post_meta($id, '_myliba_home_why_text', true));
            field_textarea('_myliba_home_offering_rows', __('Software and Academy cards', 'myliba'), get_post_meta($id, '_myliba_home_offering_rows', true), __('One row per line as Label | Intro | Benefit 1 title | Benefit 1 text | Benefit 2 title | Benefit 2 text | Benefit 3 title | Benefit 3 text | Benefit 4 title | Benefit 4 text | CTA label | CTA URL.', 'myliba'));
            break;

        case 'problem':
            field_text('_myliba_home_problem_eyebrow', __('Problem eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_problem_eyebrow', true));
            field_textarea('_myliba_home_problem_title', __('Problem title', 'myliba'), get_post_meta($id, '_myliba_home_problem_title', true));
            field_textarea('_myliba_home_problem_text', __('Problem intro text', 'myliba'), get_post_meta($id, '_myliba_home_problem_text', true));
            field_textarea('_myliba_home_problem_cards', __('Problem cards', 'myliba'), get_post_meta($id, '_myliba_home_problem_cards', true), __('One row per line as Title | Text.', 'myliba'));
            break;

        case 'solutions':
            field_text('_myliba_home_strategy_flow_eyebrow', __('Strategy flow eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_strategy_flow_eyebrow', true));
            field_textarea('_myliba_home_strategy_flow_title', __('Strategy flow title', 'myliba'), get_post_meta($id, '_myliba_home_strategy_flow_title', true));
            field_textarea('_myliba_home_strategy_flow_text', __('Strategy flow intro text', 'myliba'), get_post_meta($id, '_myliba_home_strategy_flow_text', true));
            field_textarea('_myliba_home_strategy_flow_steps', __('Strategy flow steps', 'myliba'), get_post_meta($id, '_myliba_home_strategy_flow_steps', true), __('One row per line as Title | Text | Short label.', 'myliba'));
            break;

        case 'performance':
            field_text('_myliba_home_performance_eyebrow', __('Performance approach eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_performance_eyebrow', true));
            field_textarea('_myliba_home_performance_title', __('Performance approach title', 'myliba'), get_post_meta($id, '_myliba_home_performance_title', true));
            field_textarea('_myliba_home_performance_text', __('Performance approach text', 'myliba'), get_post_meta($id, '_myliba_home_performance_text', true));
            field_textarea('_myliba_home_performance_tabs', __('Performance approach tabs', 'myliba'), get_post_meta($id, '_myliba_home_performance_tabs', true), __('One row per line as Tab label | Title | Text.', 'myliba'));
            field_text('_myliba_home_performance_button', __('Performance tab button label', 'myliba'), get_post_meta($id, '_myliba_home_performance_button', true));
            echo '<h4>' . esc_html__('Performance tab images', 'myliba') . '</h4>';
            for ($image_index = 1; $image_index <= 3; $image_index++) {
                field_media('_myliba_home_performance_image_' . $image_index, sprintf(__('Performance tab %d image', 'myliba'), $image_index), get_post_meta($id, '_myliba_home_performance_image_' . $image_index, true));
                field_text('_myliba_home_performance_image_' . $image_index . '_alt', sprintf(__('Performance tab %d alternative text', 'myliba'), $image_index), get_post_meta($id, '_myliba_home_performance_image_' . $image_index . '_alt', true));
            }
            break;

        case 'products':
            field_text('_myliba_home_solution_eyebrow', __('Solution eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_solution_eyebrow', true));
            field_textarea('_myliba_home_solution_title', __('Solution title', 'myliba'), get_post_meta($id, '_myliba_home_solution_title', true));
            field_text('_myliba_home_products_button', __('All products button label', 'myliba'), get_post_meta($id, '_myliba_home_products_button', true));
            field_text('_myliba_home_module_button', __('Module card link label', 'myliba'), get_post_meta($id, '_myliba_home_module_button', true));
            break;

        case 'academy':
            field_text('_myliba_home_academy_eyebrow', __('Academy eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_academy_eyebrow', true));
            field_textarea('_myliba_home_academy_title', __('Academy title', 'myliba'), get_post_meta($id, '_myliba_home_academy_title', true));
            field_textarea('_myliba_home_academy_text', __('Academy text', 'myliba'), get_post_meta($id, '_myliba_home_academy_text', true));
            field_textarea('_myliba_home_academy_items', __('Academy bullet items', 'myliba'), get_post_meta($id, '_myliba_home_academy_items', true), __('One item per line.', 'myliba'));
            field_text('_myliba_home_academy_button', __('Academy button label', 'myliba'), get_post_meta($id, '_myliba_home_academy_button', true));
            break;

        case 'role_gains':
            field_text('_myliba_home_role_gains_eyebrow', __('Role gains eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_role_gains_eyebrow', true));
            field_textarea('_myliba_home_role_gains_title', __('Role gains title', 'myliba'), get_post_meta($id, '_myliba_home_role_gains_title', true));
            field_textarea('_myliba_home_role_gains_text', __('Role gains intro text', 'myliba'), get_post_meta($id, '_myliba_home_role_gains_text', true));
            field_textarea('_myliba_home_role_gains_rows', __('Role gain tabs', 'myliba'), get_post_meta($id, '_myliba_home_role_gains_rows', true), __('One row per line as Label | Title | Description | Primary label | Primary text | Secondary label | Secondary text.', 'myliba'));
            break;

        case 'outcomes':
            field_text('_myliba_home_outcomes_eyebrow', __('Outcomes eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_outcomes_eyebrow', true));
            field_textarea('_myliba_home_outcomes_title', __('Outcomes title', 'myliba'), get_post_meta($id, '_myliba_home_outcomes_title', true));
            field_textarea('_myliba_home_outcomes_text', __('Outcomes intro text', 'myliba'), get_post_meta($id, '_myliba_home_outcomes_text', true));
            field_textarea('_myliba_home_outcomes_cards', __('Outcomes cards', 'myliba'), get_post_meta($id, '_myliba_home_outcomes_cards', true), __('One row per line as Title | Text.', 'myliba'));
            break;

        case 'resources':
            field_text('_myliba_home_resources_eyebrow', __('Resources eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_resources_eyebrow', true));
            field_textarea('_myliba_home_resources_title', __('Resources title', 'myliba'), get_post_meta($id, '_myliba_home_resources_title', true));
            field_textarea('_myliba_home_resources_text', __('Resources intro text', 'myliba'), get_post_meta($id, '_myliba_home_resources_text', true));
            field_text('_myliba_home_resources_button', __('Resources button label', 'myliba'), get_post_meta($id, '_myliba_home_resources_button', true));
            break;

        case 'faq':
            field_text('_myliba_home_faq_eyebrow', __('FAQ eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_faq_eyebrow', true));
            field_textarea('_myliba_home_faq_title', __('FAQ title', 'myliba'), get_post_meta($id, '_myliba_home_faq_title', true));
            field_textarea('_myliba_home_faq_text', __('FAQ intro text', 'myliba'), get_post_meta($id, '_myliba_home_faq_text', true));
            field_textarea('_myliba_home_faq_items', __('FAQ fallback items', 'myliba'), get_post_meta($id, '_myliba_home_faq_items', true), __('Used if no FAQ posts exist. One row per line as Question | Answer.', 'myliba'));
            break;

        case 'final_cta':
            field_text('_myliba_home_final_cta_eyebrow', __('Final CTA eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_final_cta_eyebrow', true));
            field_textarea('_myliba_home_final_cta_title', __('Final CTA title', 'myliba'), get_post_meta($id, '_myliba_home_final_cta_title', true));
            field_textarea('_myliba_home_final_cta_text', __('Final CTA text', 'myliba'), get_post_meta($id, '_myliba_home_final_cta_text', true));
            field_text('_myliba_home_final_cta_primary_label', __('Final CTA primary label', 'myliba'), get_post_meta($id, '_myliba_home_final_cta_primary_label', true));
            field_text('_myliba_home_final_cta_secondary_label', __('Final CTA secondary label', 'myliba'), get_post_meta($id, '_myliba_home_final_cta_secondary_label', true));
            break;
    }
}

function render_team_box(\WP_Post $post): void
{
    nonce();

    field_text('_myliba_person_role', __('Role', 'myliba'), get_post_meta($post->ID, '_myliba_person_role', true));
    field_url('_myliba_linkedin_url', __('LinkedIn URL', 'myliba'), get_post_meta($post->ID, '_myliba_linkedin_url', true));
    field_number('_myliba_order', __('Sort order', 'myliba'), get_post_meta($post->ID, '_myliba_order', true));
}

function render_logo_box(\WP_Post $post): void
{
    nonce();

    field_url('_myliba_logo_url', __('Client URL', 'myliba'), get_post_meta($post->ID, '_myliba_logo_url', true));
    field_number('_myliba_order', __('Sort order', 'myliba'), get_post_meta($post->ID, '_myliba_order', true));
    echo '<p class="description">' . esc_html__('Use the featured image as the logo image.', 'myliba') . '</p>';
}

function render_testimonial_box(\WP_Post $post): void
{
    nonce();

    field_text('_myliba_person_role', __('Person role', 'myliba'), get_post_meta($post->ID, '_myliba_person_role', true));
    field_text('_myliba_company', __('Company', 'myliba'), get_post_meta($post->ID, '_myliba_company', true));
    field_text('_myliba_academy_testimonial_program', __('Program name', 'myliba'), get_post_meta($post->ID, '_myliba_academy_testimonial_program', true));
    field_number('_myliba_order', __('Sort order', 'myliba'), get_post_meta($post->ID, '_myliba_order', true));
}

function render_faq_box(\WP_Post $post): void
{
    nonce();

    field_text('_myliba_label', __('FAQ group', 'myliba'), get_post_meta($post->ID, '_myliba_label', true));
    field_number('_myliba_order', __('Sort order', 'myliba'), get_post_meta($post->ID, '_myliba_order', true));
}

function render_submission_box(\WP_Post $post): void
{
    $fields = [
        '_myliba_form_name' => __('Name', 'myliba'),
        '_myliba_form_last_name' => __('Last name', 'myliba'),
        '_myliba_form_email' => __('Email', 'myliba'),
        '_myliba_form_phone' => __('Phone', 'myliba'),
        '_myliba_form_company' => __('Company', 'myliba'),
        '_myliba_form_job_title' => __('Title', 'myliba'),
        '_myliba_form_employee_count' => __('Employee count', 'myliba'),
        '_myliba_form_program' => __('Program', 'myliba'),
        '_myliba_form_participation_type' => __('Participation type', 'myliba'),
        '_myliba_form_subject' => __('Subject', 'myliba'),
        '_myliba_form_type' => __('Type', 'myliba'),
        '_myliba_form_form_context' => __('Form context', 'myliba'),
        '_myliba_form_kvkk' => __('KVKK consent', 'myliba'),
        '_myliba_form_message' => __('Message', 'myliba'),
    ];

    echo '<table class="widefat striped"><tbody>';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<tr><th style="width:180px">' . esc_html($label) . '</th><td>' . nl2br(esc_html($value)) . '</td></tr>';
    }
    echo '</tbody></table>';
}

function homepage_section_definitions(): array
{
    return [
        'hero' => [
            'label' => __('Hero slider', 'myliba'),
            'source' => __('Homepage slider fields', 'myliba'),
            'fields' => __('Three synchronized slides with eyebrow, title, text, CTA links, proof pills, and product imagery.', 'myliba'),
        ],
        'trust_bar' => [
            'label' => __('Client references', 'myliba'),
            'source' => __('Client logos + homepage text fields', 'myliba'),
            'fields' => __('Reference heading, label, and logos from the Client Logos content type.', 'myliba'),
        ],
        'social_proof' => [
            'label' => __('Social proof metrics', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Trust metrics using Value | Label rows.', 'myliba'),
        ],
        'why_myliba' => [
            'label' => __('Why Myliba', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Why Myliba copy and Software / Academy cards.', 'myliba'),
        ],
        'problem' => [
            'label' => __('Problem cards', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Eyebrow, title, intro text, and cards using Title | Text rows.', 'myliba'),
        ],
        'solutions' => [
            'label' => __('Strategy flow', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Strategy flow heading and steps.', 'myliba'),
        ],
        'performance' => [
            'label' => __('Performance approach', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Interactive performance management approach tabs.', 'myliba'),
        ],
        'products' => [
            'label' => __('Product grid', 'myliba'),
            'source' => __('Products content type', 'myliba'),
            'fields' => __('Section heading fields plus cards from Products.', 'myliba'),
        ],
        'academy' => [
            'label' => __('Academy block', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Academy eyebrow, title, text, bullets, and button label.', 'myliba'),
        ],
        'role_gains' => [
            'label' => __('Role gains', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Role gain tabs using Label | Title | Description | Primary label | Primary text | Secondary label | Secondary text rows.', 'myliba'),
        ],
        'outcomes' => [
            'label' => __('Business outcomes', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Outcomes eyebrow, title, and cards using Title | Text rows.', 'myliba'),
        ],
        'resources' => [
            'label' => __('Resources / blog', 'myliba'),
            'source' => __('Blog posts', 'myliba'),
            'fields' => __('Resources eyebrow and title, then latest posts for the current language.', 'myliba'),
        ],
        'faq' => [
            'label' => __('Homepage FAQ', 'myliba'),
            'source' => __('FAQ content type + homepage fallback rows', 'myliba'),
            'fields' => __('FAQ eyebrow, title, intro text, and optional Question | Answer fallback rows.', 'myliba'),
        ],
        'final_cta' => [
            'label' => __('Final CTA', 'myliba'),
            'source' => __('Homepage text fields + CTA options', 'myliba'),
            'fields' => __('CTA eyebrow, title, text, primary label, and secondary label.', 'myliba'),
        ],
    ];
}

function homepage_default_sections(): array
{
    $sections = [];
    $order = 10;

    foreach (array_keys(homepage_section_definitions()) as $key) {
        $sections[$key] = [
            'key' => $key,
            'enabled' => true,
            'order' => $order,
        ];
        $order += 10;
    }

    return $sections;
}

function homepage_sections(int $post_id): array
{
    $sections = homepage_default_sections();
    $definitions = homepage_section_definitions();
    $raw = get_post_meta($post_id, '_myliba_home_builder', true);
    $saved = is_string($raw) && $raw !== '' ? json_decode($raw, true) : [];
    $saved_keys = [];

    if (is_array($saved)) {
        $saved_keys = array_map(static fn ($item) => is_array($item) ? sanitize_key((string) ($item['key'] ?? '')) : '', $saved);
        $is_legacy_builder = $saved_keys && (!in_array('role_gains', $saved_keys, true) || in_array('testimonials', $saved_keys, true));

        foreach ($saved as $item) {
            if (!is_array($item)) {
                continue;
            }

            $key = sanitize_key((string) ($item['key'] ?? ''));
            if (!isset($definitions[$key])) {
                continue;
            }

            $sections[$key] = [
                'key' => $key,
                'enabled' => !empty($item['enabled']),
                'order' => $is_legacy_builder ? ($sections[$key]['order'] ?? 999) : (isset($item['order']) ? (int) $item['order'] : ($sections[$key]['order'] ?? 999)),
            ];
        }
    }

    if ($saved_keys && in_array('trust_bar', $saved_keys, true)) {
        $trust_order = (int) ($sections['trust_bar']['order'] ?? 20);

        if (!in_array('social_proof', $saved_keys, true)) {
            $sections['social_proof']['order'] = $trust_order + 1;
        }

        if (!in_array('why_myliba', $saved_keys, true)) {
            $sections['why_myliba']['order'] = $trust_order + 2;
        }
    }

    uasort($sections, static function (array $a, array $b): int {
        return ($a['order'] <=> $b['order']) ?: strcmp($a['key'], $b['key']);
    });

    return array_values($sections);
}

function homepage_section_summary(int $post_id, string $key): string
{
    return match ($key) {
        'hero' => get_post_meta($post_id, '_myliba_hero_title', true) ?: get_the_title($post_id),
        'trust_bar' => get_post_meta($post_id, '_myliba_home_trust_title', true),
        'social_proof' => get_post_meta($post_id, '_myliba_home_social_proof_items', true),
        'why_myliba' => get_post_meta($post_id, '_myliba_home_why_title', true),
        'problem' => get_post_meta($post_id, '_myliba_home_problem_title', true),
        'solutions' => get_post_meta($post_id, '_myliba_home_strategy_flow_title', true),
        'performance' => get_post_meta($post_id, '_myliba_home_performance_title', true),
        'products' => get_post_meta($post_id, '_myliba_home_solution_title', true),
        'academy' => get_post_meta($post_id, '_myliba_home_academy_title', true),
        'role_gains' => get_post_meta($post_id, '_myliba_home_role_gains_title', true),
        'outcomes' => get_post_meta($post_id, '_myliba_home_outcomes_title', true),
        'resources' => get_post_meta($post_id, '_myliba_home_resources_title', true),
        'faq' => get_post_meta($post_id, '_myliba_home_faq_title', true),
        'final_cta' => get_post_meta($post_id, '_myliba_home_final_cta_title', true),
        default => '',
    } ?: __('No summary yet. Expand to fill the content fields.', 'myliba');
}

function is_homepage_post(int $post_id): bool
{
    if ($post_id <= 0) {
        return false;
    }

    if (get_option('show_on_front') === 'page' && (int) get_option('page_on_front') === $post_id) {
        return true;
    }

    $post = get_post($post_id);
    return $post instanceof \WP_Post && in_array($post->post_name, ['tr', 'en'], true);
}

function save(int $post_id, \WP_Post $post): void
{
    if (!isset($_POST['myliba_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['myliba_meta_nonce'])), 'myliba_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = field_definitions($post->post_type);

    foreach ($fields as $field => $type) {
        if ($type === 'builder') {
            save_homepage_builder($post_id);
            continue;
        }

        if ($type === 'hero_slides') {
            save_hero_slides($post_id);
            continue;
        }

        if (!array_key_exists($field, $_POST) && $type !== 'checkbox') {
            continue;
        }

        $raw = $_POST[$field] ?? '';
        $value = is_string($raw) ? wp_unslash($raw) : $raw;

        if ($type === 'checkbox') {
            update_post_meta($post_id, $field, !empty($_POST[$field]) ? '1' : '0');
            continue;
        }

        if ($type === 'textarea') {
            update_post_meta($post_id, $field, sanitize_textarea_field($value));
            continue;
        }

        if ($type === 'url') {
            update_post_meta($post_id, $field, esc_url_raw($value));
            continue;
        }

        if ($type === 'number') {
            update_post_meta($post_id, $field, (string) intval($value));
            continue;
        }

        update_post_meta($post_id, $field, sanitize_text_field($value));
    }
}

function save_hero_slides(int $post_id): void
{
    if (empty($_POST['_myliba_home_hero_slides_v2_present'])) {
        return;
    }

    $raw_slides = isset($_POST['_myliba_home_hero_slides_v2']) && is_array($_POST['_myliba_home_hero_slides_v2'])
        ? wp_unslash($_POST['_myliba_home_hero_slides_v2'])
        : [];
    update_post_meta($post_id, '_myliba_home_hero_slides_v2', sanitize_hero_slides($raw_slides));
}

function sanitize_hero_slides(array $raw_slides): array
{
    $slides = [];

    foreach (array_slice($raw_slides, 0, 30, true) as $raw_slide) {
        if (!is_array($raw_slide)) {
            continue;
        }

        $buttons = [];
        $raw_buttons = isset($raw_slide['buttons']) && is_array($raw_slide['buttons']) ? $raw_slide['buttons'] : [];
        foreach (array_slice($raw_buttons, 0, 12, true) as $raw_button) {
            if (!is_array($raw_button)) {
                continue;
            }

            $label = sanitize_text_field((string) ($raw_button['label'] ?? ''));
            $url = esc_url_raw((string) ($raw_button['url'] ?? ''));
            if ($label === '' && $url === '') {
                continue;
            }

            $style = sanitize_key((string) ($raw_button['style'] ?? 'ghost'));
            $buttons[] = [
                'label' => $label,
                'url' => $url,
                'style' => in_array($style, ['primary', 'ghost', 'link'], true) ? $style : 'ghost',
                'new_tab' => !empty($raw_button['new_tab']),
                'aria_label' => sanitize_text_field((string) ($raw_button['aria_label'] ?? '')),
            ];
        }

        $eyebrow = sanitize_text_field((string) ($raw_slide['eyebrow'] ?? ''));
        $title = sanitize_text_field((string) ($raw_slide['title'] ?? ''));
        $text = sanitize_textarea_field((string) ($raw_slide['text'] ?? ''));
        $image_id = absint($raw_slide['image_id'] ?? 0);
        if ($eyebrow === '' && $title === '' && $text === '' && !$image_id && !$buttons) {
            continue;
        }

        $id = sanitize_key((string) ($raw_slide['id'] ?? ''));
        $slides[] = [
            'id' => $id !== '' ? $id : 'hero-' . wp_generate_uuid4(),
            'enabled' => !empty($raw_slide['enabled']),
            'eyebrow' => $eyebrow,
            'title' => $title,
            'text' => $text,
            'image_id' => $image_id,
            'image_alt' => sanitize_text_field((string) ($raw_slide['image_alt'] ?? '')),
            'buttons' => $buttons,
        ];
    }

    return $slides;
}

function save_homepage_builder(int $post_id): void
{
    if (!isset($_POST['_myliba_home_builder']) || !is_array($_POST['_myliba_home_builder'])) {
        return;
    }

    $definitions = homepage_section_definitions();
    $sections = [];

    foreach ($_POST['_myliba_home_builder'] as $raw_key => $raw_section) {
        if (!is_array($raw_section)) {
            continue;
        }

        $key = sanitize_key((string) ($raw_section['key'] ?? $raw_key));
        if (!isset($definitions[$key])) {
            continue;
        }

        $sections[] = [
            'key' => $key,
            'enabled' => !empty($raw_section['enabled']),
            'order' => isset($raw_section['order']) ? max(0, (int) $raw_section['order']) : 999,
        ];
    }

    usort($sections, static function (array $a, array $b): int {
        return ($a['order'] <=> $b['order']) ?: strcmp($a['key'], $b['key']);
    });

    update_post_meta($post_id, '_myliba_home_builder', wp_json_encode($sections));
}

function field_definitions(string $post_type): array
{
    $fields = [
        '_myliba_language' => 'text',
        '_myliba_translation_key' => 'text',
        '_myliba_eyebrow' => 'text',
        '_myliba_hero_title' => 'text',
        '_myliba_hero_subtitle' => 'textarea',
        '_myliba_cta_label' => 'text',
        '_myliba_cta_url' => 'url',
        '_myliba_seo_title' => 'text',
        '_myliba_seo_description' => 'textarea',
        '_myliba_noindex' => 'checkbox',
        '_myliba_label' => 'text',
        '_myliba_problem' => 'textarea',
        '_myliba_solution' => 'textarea',
        '_myliba_benefits' => 'textarea',
        '_myliba_related_modules' => 'textarea',
        '_myliba_faq_items' => 'textarea',
        '_myliba_home_builder' => 'builder',
        '_myliba_home_hero_slides_v2' => 'hero_slides',
        '_myliba_home_hero_slides' => 'textarea',
        '_myliba_home_hero_image_1' => 'number',
        '_myliba_home_hero_image_1_alt' => 'text',
        '_myliba_home_hero_image_2' => 'number',
        '_myliba_home_hero_image_2_alt' => 'text',
        '_myliba_home_hero_image_3' => 'number',
        '_myliba_home_hero_image_3_alt' => 'text',
        '_myliba_home_hero_proof' => 'textarea',
        '_myliba_home_hero_metrics' => 'textarea',
        '_myliba_home_dashboard_brand' => 'text',
        '_myliba_home_dashboard_title' => 'text',
        '_myliba_home_dashboard_nav' => 'textarea',
        '_myliba_home_dashboard_objective_label' => 'text',
        '_myliba_home_dashboard_objective_title' => 'text',
        '_myliba_home_dashboard_progress' => 'text',
        '_myliba_home_dashboard_rows' => 'textarea',
        '_myliba_home_dashboard_col_1' => 'text',
        '_myliba_home_dashboard_col_2' => 'text',
        '_myliba_home_dashboard_col_3' => 'text',
        '_myliba_home_metric_1_value' => 'text',
        '_myliba_home_metric_1_label' => 'text',
        '_myliba_home_metric_2_value' => 'text',
        '_myliba_home_metric_2_label' => 'text',
        '_myliba_home_feedback_title' => 'text',
        '_myliba_home_feedback_text' => 'textarea',
        '_myliba_home_trust_title' => 'textarea',
        '_myliba_home_trust_logo_label' => 'text',
        '_myliba_home_trust_items' => 'textarea',
        '_myliba_home_social_proof_items' => 'textarea',
        '_myliba_home_why_eyebrow' => 'text',
        '_myliba_home_why_title' => 'textarea',
        '_myliba_home_why_text' => 'textarea',
        '_myliba_home_offering_rows' => 'textarea',
        '_myliba_home_problem_eyebrow' => 'text',
        '_myliba_home_problem_title' => 'textarea',
        '_myliba_home_problem_text' => 'textarea',
        '_myliba_home_problem_cards' => 'textarea',
        '_myliba_home_strategy_flow_eyebrow' => 'text',
        '_myliba_home_strategy_flow_title' => 'textarea',
        '_myliba_home_strategy_flow_text' => 'textarea',
        '_myliba_home_strategy_flow_steps' => 'textarea',
        '_myliba_home_performance_eyebrow' => 'text',
        '_myliba_home_performance_title' => 'textarea',
        '_myliba_home_performance_text' => 'textarea',
        '_myliba_home_performance_tabs' => 'textarea',
        '_myliba_home_performance_button' => 'text',
        '_myliba_home_performance_image_1' => 'number',
        '_myliba_home_performance_image_1_alt' => 'text',
        '_myliba_home_performance_image_2' => 'number',
        '_myliba_home_performance_image_2_alt' => 'text',
        '_myliba_home_performance_image_3' => 'number',
        '_myliba_home_performance_image_3_alt' => 'text',
        '_myliba_home_solution_eyebrow' => 'text',
        '_myliba_home_solution_title' => 'textarea',
        '_myliba_home_products_button' => 'text',
        '_myliba_home_module_button' => 'text',
        '_myliba_home_academy_eyebrow' => 'text',
        '_myliba_home_academy_title' => 'textarea',
        '_myliba_home_academy_text' => 'textarea',
        '_myliba_home_academy_items' => 'textarea',
        '_myliba_home_academy_button' => 'text',
        '_myliba_home_role_gains_eyebrow' => 'text',
        '_myliba_home_role_gains_title' => 'textarea',
        '_myliba_home_role_gains_text' => 'textarea',
        '_myliba_home_role_gains_rows' => 'textarea',
        '_myliba_home_outcomes_eyebrow' => 'text',
        '_myliba_home_outcomes_title' => 'textarea',
        '_myliba_home_outcomes_text' => 'textarea',
        '_myliba_home_outcomes_cards' => 'textarea',
        '_myliba_home_resources_eyebrow' => 'text',
        '_myliba_home_resources_title' => 'textarea',
        '_myliba_home_resources_text' => 'textarea',
        '_myliba_home_resources_button' => 'text',
        '_myliba_home_faq_eyebrow' => 'text',
        '_myliba_home_faq_title' => 'textarea',
        '_myliba_home_faq_text' => 'textarea',
        '_myliba_home_faq_items' => 'textarea',
        '_myliba_home_final_cta_eyebrow' => 'text',
        '_myliba_home_final_cta_title' => 'textarea',
        '_myliba_home_final_cta_text' => 'textarea',
        '_myliba_home_final_cta_primary_label' => 'text',
        '_myliba_home_final_cta_secondary_label' => 'text',
        '_myliba_academy_hero_secondary_label' => 'text',
        '_myliba_academy_hero_secondary_url' => 'url',
        '_myliba_academy_hero_tertiary_label' => 'text',
        '_myliba_academy_hero_tertiary_url' => 'url',
        '_myliba_academy_hero_badges' => 'textarea',
        '_myliba_academy_icf_image' => 'number',
        '_myliba_academy_certificate_image' => 'number',
        '_myliba_academy_digital_badge_image' => 'number',
        '_myliba_academy_platform_image' => 'number',
        '_myliba_academy_nav_items' => 'textarea',
        '_myliba_academy_trust_title' => 'text',
        '_myliba_academy_trust_label' => 'text',
        '_myliba_academy_trust_text' => 'textarea',
        '_myliba_academy_organization_name' => 'text',
        '_myliba_academy_programs_eyebrow' => 'text',
        '_myliba_academy_programs_title' => 'textarea',
        '_myliba_academy_programs_text' => 'textarea',
        '_myliba_academy_benefits_title' => 'text',
        '_myliba_academy_modules_title' => 'text',
        '_myliba_academy_approach_title' => 'textarea',
        '_myliba_academy_approach_steps' => 'textarea',
        '_myliba_academy_stats' => 'textarea',
        '_myliba_academy_testimonials_title' => 'text',
        '_myliba_academy_faq_title' => 'text',
        '_myliba_academy_faq_group' => 'text',
        '_myliba_academy_final_title' => 'textarea',
        '_myliba_academy_final_text' => 'textarea',
        '_myliba_academy_final_primary_label' => 'text',
        '_myliba_academy_final_secondary_label' => 'text',
        '_myliba_academy_contact_title' => 'text',
        '_myliba_academy_contact_text' => 'textarea',
        '_myliba_academy_form_button' => 'text',
        '_myliba_academy_form_success' => 'textarea',
        '_myliba_academy_kvkk_text' => 'textarea',
        '_myliba_development_section_eyebrow' => 'text',
        '_myliba_development_section_title' => 'textarea',
        '_myliba_development_section_text' => 'textarea',
        '_myliba_development_ebook_label' => 'text',
        '_myliba_development_ebook_text' => 'textarea',
        '_myliba_development_report_label' => 'text',
        '_myliba_development_report_text' => 'textarea',
        '_myliba_development_blog_label' => 'text',
        '_myliba_development_blog_text' => 'textarea',
        '_myliba_development_events_label' => 'text',
        '_myliba_development_events_text' => 'textarea',
        '_myliba_development_card_cta' => 'text',
    ];

    if ($post_type === 'myliba_event') {
        $fields += [
            '_myliba_event_date' => 'text',
            '_myliba_event_location' => 'text',
            '_myliba_event_url' => 'url',
            '_myliba_event_status' => 'text',
        ];
    }

    if ($post_type === 'myliba_team') {
        $fields += [
            '_myliba_person_role' => 'text',
            '_myliba_linkedin_url' => 'url',
            '_myliba_order' => 'number',
        ];
    }

    if ($post_type === 'myliba_testimonial') {
        $fields += [
            '_myliba_person_role' => 'text',
            '_myliba_company' => 'text',
            '_myliba_academy_testimonial_program' => 'text',
            '_myliba_order' => 'number',
        ];
    }

    if ($post_type === 'myliba_academy') {
        $fields += [
            '_myliba_academy_layout' => 'text',
            '_myliba_academy_program_eyebrow' => 'text',
            '_myliba_academy_program_benefits' => 'textarea',
            '_myliba_academy_program_badges' => 'textarea',
            '_myliba_academy_program_modules' => 'textarea',
            '_myliba_academy_program_primary_label' => 'text',
            '_myliba_academy_program_primary_url' => 'url',
            '_myliba_academy_program_secondary_label' => 'text',
            '_myliba_academy_program_secondary_url' => 'url',
            '_myliba_academy_start_period' => 'text',
            '_myliba_academy_certificate_info' => 'textarea',
            '_myliba_order' => 'number',
        ];
    }

    if ($post_type === 'myliba_faq') {
        $fields += [
            '_myliba_order' => 'number',
        ];
    }

    if ($post_type === 'myliba_client_logo') {
        $fields += [
            '_myliba_logo_url' => 'url',
            '_myliba_order' => 'number',
        ];
    }

    return $fields;
}

function nonce(): void
{
    wp_nonce_field('myliba_meta', 'myliba_meta_nonce');
}

function field_media(string $name, string $label, mixed $value): void
{
    $attachment_id = absint($value);
    $preview = $attachment_id ? wp_get_attachment_image($attachment_id, 'medium', false, ['style' => 'display:block;max-height:160px;width:auto;margin:8px 0']) : '';
    $button_label = $attachment_id ? __('Change image', 'myliba') : __('Choose image', 'myliba');

    echo '<div class="myliba-media-field" style="margin:0 0 16px">';
    echo '<label style="display:block;font-weight:600;margin-bottom:4px">' . esc_html($label) . '</label>';
    echo '<div class="myliba-media-field__preview">' . wp_kses_post($preview) . '</div>';
    echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr((string) $attachment_id) . '">';
    echo '<button type="button" class="button myliba-media-field__choose">' . esc_html($button_label) . '</button> ';
    echo '<button type="button" class="button-link-delete myliba-media-field__remove"' . ($attachment_id ? '' : ' hidden') . '>' . esc_html__('Remove image', 'myliba') . '</button>';
    echo '</div>';

    print_media_field_script();
}

function print_media_field_script(): void
{
    static $script_printed = false;
    if ($script_printed) {
        return;
    }
    $script_printed = true;
    echo '<script>
        jQuery(function($){
            $(document).on("click", ".myliba-media-field__choose", function(e){
                e.preventDefault();
                var $field = $(this).closest(".myliba-media-field");
                var frame = wp.media({title:' . wp_json_encode(__('Choose an image', 'myliba')) . ', button:{text:' . wp_json_encode(__('Use this image', 'myliba')) . '}, multiple:false, library:{type:"image"}});
                frame.on("select", function(){
                    var item = frame.state().get("selection").first().toJSON();
                    var url = item.sizes && item.sizes.medium ? item.sizes.medium.url : item.url;
                    $field.find("input[type=hidden]").val(item.id);
                    $field.find(".myliba-media-field__preview").html($("<img>", {src:url, alt:"", css:{display:"block", maxHeight:"160px", width:"auto", margin:"8px 0"}}));
                    $field.find(".myliba-media-field__choose").text(' . wp_json_encode(__('Change image', 'myliba')) . ');
                    $field.find(".myliba-media-field__remove").prop("hidden", false);
                });
                frame.open();
            });
            $(document).on("click", ".myliba-media-field__remove", function(e){
                e.preventDefault();
                var $field = $(this).closest(".myliba-media-field");
                $field.find("input[type=hidden]").val("");
                $field.find(".myliba-media-field__preview").empty();
                $field.find(".myliba-media-field__choose").text(' . wp_json_encode(__('Choose image', 'myliba')) . ');
                $(this).prop("hidden", true);
            });
        });
    </script>';
}

function field_text(string $name, string $label, mixed $value, string $description = ''): void
{
    printf(
        '<p><label for="%1$s"><strong>%2$s</strong></label><br><input class="widefat" type="text" id="%1$s" name="%1$s" value="%3$s"></p>',
        esc_attr($name),
        esc_html($label),
        esc_attr((string) $value)
    );

    if ($description !== '') {
        echo '<p class="description">' . esc_html($description) . '</p>';
    }
}

function field_url(string $name, string $label, mixed $value): void
{
    printf(
        '<p><label for="%1$s"><strong>%2$s</strong></label><br><input class="widefat" type="url" id="%1$s" name="%1$s" value="%3$s"></p>',
        esc_attr($name),
        esc_html($label),
        esc_attr((string) $value)
    );
}

function field_number(string $name, string $label, mixed $value): void
{
    printf(
        '<p><label for="%1$s"><strong>%2$s</strong></label><br><input class="widefat" type="number" id="%1$s" name="%1$s" value="%3$s"></p>',
        esc_attr($name),
        esc_html($label),
        esc_attr((string) $value)
    );
}

function field_textarea(string $name, string $label, mixed $value, string $description = ''): void
{
    printf(
        '<p><label for="%1$s"><strong>%2$s</strong></label><br><textarea class="widefat" rows="4" id="%1$s" name="%1$s">%3$s</textarea></p>',
        esc_attr($name),
        esc_html($label),
        esc_textarea((string) $value)
    );

    if ($description !== '') {
        echo '<p class="description">' . esc_html($description) . '</p>';
    }
}

function field_checkbox(string $name, string $label, bool $checked): void
{
    printf(
        '<p><label><input type="checkbox" name="%1$s" value="1" %2$s> %3$s</label></p>',
        esc_attr($name),
        checked($checked, true, false),
        esc_html($label)
    );
}

function field_select(string $name, string $label, mixed $value, array $options): void
{
    printf('<p><label for="%1$s"><strong>%2$s</strong></label><br><select class="widefat" id="%1$s" name="%1$s">', esc_attr($name), esc_html($label));

    foreach ($options as $key => $option_label) {
        printf(
            '<option value="%1$s" %2$s>%3$s</option>',
            esc_attr((string) $key),
            selected((string) $value, (string) $key, false),
            esc_html((string) $option_label)
        );
    }

    echo '</select></p>';
}
