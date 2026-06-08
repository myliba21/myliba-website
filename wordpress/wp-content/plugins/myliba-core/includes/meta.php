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
}

function register_meta_boxes(string $post_type): void
{
    if (in_array($post_type, ['page', 'post', 'myliba_product', 'myliba_solution', 'myliba_academy', 'myliba_case_study', 'myliba_landing', 'myliba_event', 'myliba_team', 'myliba_client_logo', 'myliba_faq', 'myliba_testimonial'], true)) {
        add_meta_box('myliba_language', __('Myliba Language', 'myliba'), __NAMESPACE__ . '\\render_language_box', $post_type, 'side');
    }

    if (in_array($post_type, ['page', 'post', 'myliba_product', 'myliba_solution', 'myliba_academy', 'myliba_case_study', 'myliba_landing', 'myliba_event'], true)) {
        add_meta_box('myliba_hero', __('Myliba Hero', 'myliba'), __NAMESPACE__ . '\\render_hero_box', $post_type, 'normal', 'high');
        add_meta_box('myliba_seo', __('Myliba SEO', 'myliba'), __NAMESPACE__ . '\\render_seo_box', $post_type, 'normal');
    }

    if (in_array($post_type, ['page', 'myliba_product', 'myliba_solution', 'myliba_academy', 'myliba_landing'], true)) {
        add_meta_box('myliba_conversion_content', __('Conversion Content', 'myliba'), __NAMESPACE__ . '\\render_conversion_box', $post_type, 'normal');
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

    $language = get_post_meta($post->ID, '_myliba_language', true) ?: Options\get('default_locale', 'en');
    $translation_key = get_post_meta($post->ID, '_myliba_translation_key', true);

    field_select('_myliba_language', __('Language', 'myliba'), $language, array_combine(Options\locales(), Options\locales()));
    field_text('_myliba_translation_key', __('Translation group key', 'myliba'), $translation_key, __('Use the same key for translated versions of the same content.', 'myliba'));
}

function render_hero_box(\WP_Post $post): void
{
    nonce();

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
