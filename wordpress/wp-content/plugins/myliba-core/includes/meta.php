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

    if ($post_type === 'page') {
        add_meta_box('myliba_homepage_sections', __('Myliba Homepage Sections', 'myliba'), __NAMESPACE__ . '\\render_homepage_box', $post_type, 'normal');
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
        @media (max-width:782px){
            .myliba-builder-card__head{grid-template-columns:auto minmax(0,1fr) auto}
            .myliba-builder-card__order{grid-column:2 / 3;width:100%}
            .myliba-builder-card__source-line,.myliba-builder-card__summary{white-space:normal}
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

function render_section_fields(\WP_Post $post, string $key): void
{
    $id = $post->ID;

    switch ($key) {
        case 'hero':
            if (is_homepage_post($id)) {
                field_text('_myliba_eyebrow', __('Eyebrow', 'myliba'), get_post_meta($id, '_myliba_eyebrow', true));
                field_text('_myliba_hero_title', __('Hero title override', 'myliba'), get_post_meta($id, '_myliba_hero_title', true));
                field_textarea('_myliba_hero_subtitle', __('Hero subtitle', 'myliba'), get_post_meta($id, '_myliba_hero_subtitle', true));
            } else {
                echo '<p class="myliba-builder-card__notice description">' . esc_html__('Hero eyebrow, title, and subtitle are edited in the Myliba Hero box. When this page is selected as the homepage, those fields appear here.', 'myliba') . '</p>';
            }

            field_textarea('_myliba_home_hero_rotating_titles', __('Hero rotating titles', 'myliba'), get_post_meta($id, '_myliba_home_hero_rotating_titles', true), __('One title per line. Leave empty to use the hero title override.', 'myliba'));
            field_textarea('_myliba_home_hero_proof', __('Hero proof pills', 'myliba'), get_post_meta($id, '_myliba_home_hero_proof', true), __('One item per line.', 'myliba'));
            field_text('_myliba_home_dashboard_brand', __('Dashboard brand label', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_brand', true));
            field_text('_myliba_home_dashboard_title', __('Dashboard label', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_title', true));
            field_textarea('_myliba_home_dashboard_nav', __('Dashboard sidebar items', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_nav', true), __('One item per line.', 'myliba'));
            field_text('_myliba_home_dashboard_objective_label', __('Dashboard objective label', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_objective_label', true));
            field_text('_myliba_home_dashboard_objective_title', __('Dashboard objective title', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_objective_title', true));
            field_text('_myliba_home_dashboard_progress', __('Dashboard progress percent', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_progress', true), __('Number only. Example: 76', 'myliba'));
            field_textarea('_myliba_home_dashboard_rows', __('Dashboard table rows', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_rows', true), __('One row per line as Key Result | Owner | Status | green/blue/orange.', 'myliba'));
            field_text('_myliba_home_dashboard_col_1', __('Dashboard column 1 label', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_col_1', true));
            field_text('_myliba_home_dashboard_col_2', __('Dashboard column 2 label', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_col_2', true));
            field_text('_myliba_home_dashboard_col_3', __('Dashboard column 3 label', 'myliba'), get_post_meta($id, '_myliba_home_dashboard_col_3', true));
            field_text('_myliba_home_metric_1_value', __('Metric 1 value', 'myliba'), get_post_meta($id, '_myliba_home_metric_1_value', true));
            field_text('_myliba_home_metric_1_label', __('Metric 1 label', 'myliba'), get_post_meta($id, '_myliba_home_metric_1_label', true));
            field_text('_myliba_home_metric_2_value', __('Metric 2 value', 'myliba'), get_post_meta($id, '_myliba_home_metric_2_value', true));
            field_text('_myliba_home_metric_2_label', __('Metric 2 label', 'myliba'), get_post_meta($id, '_myliba_home_metric_2_label', true));
            field_text('_myliba_home_feedback_title', __('Dashboard feedback title', 'myliba'), get_post_meta($id, '_myliba_home_feedback_title', true));
            field_textarea('_myliba_home_feedback_text', __('Dashboard feedback text', 'myliba'), get_post_meta($id, '_myliba_home_feedback_text', true));
            break;

        case 'trust_bar':
            field_textarea('_myliba_home_trust_title', __('Trust section title', 'myliba'), get_post_meta($id, '_myliba_home_trust_title', true));
            field_textarea('_myliba_home_trust_items', __('Trust section items', 'myliba'), get_post_meta($id, '_myliba_home_trust_items', true), __('One item per line.', 'myliba'));
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

        case 'products':
            field_text('_myliba_home_solution_eyebrow', __('Solution eyebrow', 'myliba'), get_post_meta($id, '_myliba_home_solution_eyebrow', true));
            field_textarea('_myliba_home_solution_title', __('Solution title', 'myliba'), get_post_meta($id, '_myliba_home_solution_title', true));
            field_text('_myliba_home_products_button', __('Hero products button label', 'myliba'), get_post_meta($id, '_myliba_home_products_button', true));
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

function homepage_section_definitions(): array
{
    return [
        'hero' => [
            'label' => __('Hero + dashboard preview', 'myliba'),
            'source' => __('Page hero fields + dashboard fields', 'myliba'),
            'fields' => __('Hero title, subtitle, proof pills, dashboard labels, metrics, and table rows.', 'myliba'),
        ],
        'trust_bar' => [
            'label' => __('Trust bar', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Trust section title and one item per line.', 'myliba'),
        ],
        'problem' => [
            'label' => __('Problem cards', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Eyebrow, title, intro text, and cards using Title | Text rows.', 'myliba'),
        ],
        'solutions' => [
            'label' => __('Strategy flow', 'myliba'),
            'source' => __('Homepage text fields', 'myliba'),
            'fields' => __('Flow eyebrow, title, intro text, and steps using Title | Text | Short label rows.', 'myliba'),
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
        'problem' => get_post_meta($post_id, '_myliba_home_problem_title', true),
        'solutions' => get_post_meta($post_id, '_myliba_home_strategy_flow_title', true),
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
    return $post_id > 0
        && get_option('show_on_front') === 'page'
        && (int) get_option('page_on_front') === $post_id;
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
        '_myliba_home_hero_rotating_titles' => 'textarea',
        '_myliba_home_hero_proof' => 'textarea',
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
        '_myliba_home_trust_items' => 'textarea',
        '_myliba_home_problem_eyebrow' => 'text',
        '_myliba_home_problem_title' => 'textarea',
        '_myliba_home_problem_text' => 'textarea',
        '_myliba_home_problem_cards' => 'textarea',
        '_myliba_home_strategy_flow_eyebrow' => 'text',
        '_myliba_home_strategy_flow_title' => 'textarea',
        '_myliba_home_strategy_flow_text' => 'textarea',
        '_myliba_home_strategy_flow_steps' => 'textarea',
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
