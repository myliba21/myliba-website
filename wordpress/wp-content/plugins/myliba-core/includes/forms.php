<?php

namespace Myliba\Core\Forms;

use Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

function boot(): void
{
    add_shortcode('myliba_contact_form', __NAMESPACE__ . '\\shortcode');
    add_shortcode('myliba_demo_form', __NAMESPACE__ . '\\demo_shortcode');
    add_shortcode('myliba_academy_form', __NAMESPACE__ . '\\academy_shortcode');
    add_action('admin_post_nopriv_myliba_contact_form', __NAMESPACE__ . '\\handle');
    add_action('admin_post_myliba_contact_form', __NAMESPACE__ . '\\handle');
}

function shortcode(): string
{
    return render_form('contact');
}

function demo_shortcode(): string
{
    return render_form('demo');
}

function academy_shortcode(): string
{
    return render_form('academy');
}

function site_text(string $key, string $source): string
{
    $fallback = function_exists('myliba_text') ? myliba_text($source) : $source;
    return function_exists('myliba_option') ? (string) myliba_option($key, $fallback) : $fallback;
}

function render_form(string $context): string
{
    $status = isset($_GET['myliba_form']) ? sanitize_key(wp_unslash($_GET['myliba_form'])) : '';
    $is_demo = $context === 'demo';
    $is_academy = $context === 'academy';
    $page_id = get_queried_object_id();
    $success_message = $is_academy && metadata_exists('post', $page_id, '_myliba_academy_form_success')
        ? (string) get_post_meta($page_id, '_myliba_academy_form_success', true)
        : site_text('form_success_message', 'Your message has been received.');
    $button_label = $is_academy && metadata_exists('post', $page_id, '_myliba_academy_form_button')
        ? (string) get_post_meta($page_id, '_myliba_academy_form_button', true)
        : ($is_demo ? site_text('form_request_demo', 'Request demo') : site_text('form_send', 'Send'));
    $kvkk_text = $is_academy && metadata_exists('post', $page_id, '_myliba_academy_kvkk_text')
        ? (string) get_post_meta($page_id, '_myliba_academy_kvkk_text', true)
        : site_text('form_consent', 'I consent to being contacted about this request and accept the privacy notice.');
    $programs = [];

    if ($is_academy) {
        $program_query = new \WP_Query([
            'post_type' => 'myliba_academy',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_key' => '_myliba_order',
            'orderby' => ['meta_value_num' => 'ASC', 'title' => 'ASC'],
            'meta_query' => function_exists('pll_current_language') ? [] : [[
                'key' => '_myliba_language',
                'value' => function_exists('myliba_current_language') ? myliba_current_language() : 'tr',
            ]],
        ]);
        while ($program_query->have_posts()) {
            $program_query->the_post();
            $programs[] = get_the_title();
        }
        wp_reset_postdata();
    }

    ob_start();
    ?>
    <form class="myliba-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="myliba_contact_form">
        <input type="hidden" name="form_context" value="<?php echo esc_attr($context); ?>">
        <?php wp_nonce_field('myliba_contact_form', 'myliba_contact_nonce'); ?>
        <div class="myliba-hp-field" aria-hidden="true">
            <label for="myliba-website"><?php echo esc_html(myliba_text('Website')); ?></label>
            <input id="myliba-website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <?php if ($status === 'success') : ?>
            <p class="myliba-form__status myliba-form__status--success" role="status"><?php echo esc_html($success_message); ?></p>
        <?php elseif ($status === 'error') : ?>
            <p class="myliba-form__status myliba-form__status--error"><?php echo esc_html(site_text('form_error_message', 'The form could not be sent. Please try again.')); ?></p>
        <?php endif; ?>

        <div class="myliba-form__grid">
            <label>
                <span><?php echo esc_html($is_demo ? site_text('form_first_name', 'First name') : site_text('form_name', 'Name')); ?></span>
                <input name="name" required>
            </label>
            <?php if ($is_demo) : ?>
                <label>
                    <span><?php echo esc_html(site_text('form_last_name', 'Last name')); ?></span>
                    <input name="last_name" required>
                </label>
            <?php endif; ?>
            <label>
                <span><?php echo esc_html(site_text('form_business_email', 'Business email')); ?></span>
                <input type="email" name="email" required>
            </label>
            <label>
                <span><?php echo esc_html(site_text('form_phone', 'Phone')); ?></span>
                <input name="phone" inputmode="tel" <?php echo ($is_demo || $is_academy) ? 'required' : ''; ?>>
            </label>
            <label>
                <span><?php echo esc_html(site_text('form_company', 'Company')); ?></span>
                <input name="company" <?php echo ($is_demo || $is_academy) ? 'required' : ''; ?>>
            </label>
            <?php if ($is_demo || $is_academy) : ?>
                <label>
                    <span><?php echo esc_html(site_text('form_title', 'Title')); ?></span>
                    <input name="job_title" <?php echo $is_academy ? 'required' : ''; ?>>
                </label>
            <?php endif; ?>
            <?php if ($is_demo) : ?>
                <label>
                    <span><?php echo esc_html(site_text('form_employee_count', 'Employee count')); ?></span>
                    <select name="employee_count">
                        <option value="1-50"><?php echo esc_html(myliba_text('1-50')); ?></option>
                        <option value="51-250"><?php echo esc_html(myliba_text('51-250')); ?></option>
                        <option value="251-1000"><?php echo esc_html(myliba_text('251-1000')); ?></option>
                        <option value="1000+"><?php echo esc_html(myliba_text('1000+')); ?></option>
                    </select>
                </label>
            <?php endif; ?>
            <?php if ($is_academy) : ?>
                <label>
                    <span><?php echo esc_html(site_text('form_program_interest', 'Program you are interested in')); ?></span>
                    <select name="program" required data-academy-program-select>
                        <option value=""><?php echo esc_html(site_text('form_select_program', 'Select a program')); ?></option>
                        <?php foreach ($programs as $program) : ?>
                            <option value="<?php echo esc_attr($program); ?>"><?php echo esc_html($program); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <fieldset class="myliba-form__choice">
                    <legend><?php echo esc_html(site_text('form_participation_type', 'Participation type')); ?></legend>
                    <label><input type="radio" name="participation_type" value="individual" required> <span><?php echo esc_html(site_text('form_individual', 'Individual')); ?></span></label>
                    <label><input type="radio" name="participation_type" value="corporate" required> <span><?php echo esc_html(site_text('form_corporate', 'Corporate')); ?></span></label>
                </fieldset>
            <?php endif; ?>
        </div>
        <?php if (!$is_demo && !$is_academy) : ?>
            <label>
                <span><?php echo esc_html(site_text('form_subject', 'Subject')); ?></span>
                <input name="subject" required>
            </label>
        <?php endif; ?>
        <input type="hidden" name="type" value="<?php echo esc_attr($is_demo ? 'demo' : ($is_academy ? 'academy' : 'contact')); ?>">
        <label>
            <span><?php echo esc_html(site_text('form_message', 'Message')); ?></span>
            <textarea name="message" rows="6" <?php echo $is_demo ? '' : 'required'; ?>></textarea>
        </label>
        <label class="myliba-form__consent">
            <input type="checkbox" name="kvkk" value="1" required>
            <span><?php echo esc_html($kvkk_text); ?></span>
        </label>
        <button class="myliba-button myliba-button--primary" type="submit"><?php echo esc_html($button_label); ?></button>
    </form>
    <?php

    return (string) ob_get_clean();
}

function handle(): void
{
    $redirect = wp_get_referer() ?: home_url('/');

    if (!isset($_POST['myliba_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['myliba_contact_nonce'])), 'myliba_contact_form')) {
        wp_safe_redirect(add_query_arg('myliba_form', 'error', $redirect));
        exit;
    }

    if (!empty($_POST['website'])) {
        wp_safe_redirect(add_query_arg('myliba_form', 'success', $redirect));
        exit;
    }

    if (rate_limited()) {
        wp_safe_redirect(add_query_arg('myliba_form', 'error', $redirect));
        exit;
    }

    $data = [
        'name' => sanitize_text_field(wp_unslash($_POST['name'] ?? '')),
        'last_name' => sanitize_text_field(wp_unslash($_POST['last_name'] ?? '')),
        'email' => sanitize_email(wp_unslash($_POST['email'] ?? '')),
        'phone' => sanitize_text_field(wp_unslash($_POST['phone'] ?? '')),
        'company' => sanitize_text_field(wp_unslash($_POST['company'] ?? '')),
        'job_title' => sanitize_text_field(wp_unslash($_POST['job_title'] ?? '')),
        'employee_count' => sanitize_text_field(wp_unslash($_POST['employee_count'] ?? '')),
        'program' => sanitize_text_field(wp_unslash($_POST['program'] ?? '')),
        'participation_type' => sanitize_key(wp_unslash($_POST['participation_type'] ?? '')),
        'subject' => sanitize_text_field(wp_unslash($_POST['subject'] ?? '')),
        'type' => sanitize_key(wp_unslash($_POST['type'] ?? 'contact')),
        'form_context' => sanitize_key(wp_unslash($_POST['form_context'] ?? 'contact')),
        'message' => sanitize_textarea_field(wp_unslash($_POST['message'] ?? '')),
        'kvkk' => !empty($_POST['kvkk']) ? 'yes' : 'no',
    ];

    if (!$data['name'] || !$data['email'] || $data['kvkk'] !== 'yes') {
        wp_safe_redirect(add_query_arg('myliba_form', 'error', $redirect));
        exit;
    }

    if ($data['form_context'] === 'contact' && (!$data['subject'] || !$data['message'])) {
        wp_safe_redirect(add_query_arg('myliba_form', 'error', $redirect));
        exit;
    }

    if ($data['form_context'] === 'demo' && (!$data['last_name'] || !$data['phone'] || !$data['company'])) {
        wp_safe_redirect(add_query_arg('myliba_form', 'error', $redirect));
        exit;
    }

    if ($data['form_context'] === 'academy' && (!$data['phone'] || !$data['company'] || !$data['job_title'] || !$data['program'] || !in_array($data['participation_type'], ['individual', 'corporate'], true))) {
        wp_safe_redirect(add_query_arg('myliba_form', 'error', $redirect));
        exit;
    }

    $post_id = wp_insert_post([
        'post_type' => 'myliba_submission',
        'post_status' => 'private',
        'post_title' => sprintf('%s %s - %s', $data['name'], $data['last_name'], current_time('mysql')),
    ]);

    if ($post_id && !is_wp_error($post_id)) {
        foreach ($data as $key => $value) {
            update_post_meta($post_id, '_myliba_form_' . $key, $value);
        }
    }

    send_notification($data);

    wp_safe_redirect(add_query_arg('myliba_form', 'success', $redirect));
    exit;
}

function rate_limited(): bool
{
    // Prefer X-Real-IP set by a trusted nginx proxy; fall back to REMOTE_ADDR.
    $ip = '';
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REAL_IP']));
    }
    if ($ip === '' && !empty($_SERVER['REMOTE_ADDR'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
    }
    if ($ip === '') {
        $ip = 'unknown';
    }

    $key   = 'myliba_contact_' . md5($ip);
    $count = (int) get_transient($key);

    if ($count >= 5) {
        return true;
    }

    set_transient($key, $count + 1, HOUR_IN_SECONDS);

    return false;
}

function send_notification(array $data): void
{
    $to = Options\get('contact_email', get_option('admin_email'));
    $subject = sprintf('[Myliba] %s request from %s', ucfirst($data['type']), $data['name']);
    $body = sprintf(
        "Name: %s %s\nEmail: %s\nPhone: %s\nCompany: %s\nTitle: %s\nEmployee count: %s\nProgram: %s\nParticipation: %s\nSubject: %s\nType: %s\nKVKK: %s\n\nMessage:\n%s",
        $data['name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $data['company'],
        $data['job_title'],
        $data['employee_count'],
        $data['program'],
        $data['participation_type'],
        $data['subject'],
        $data['type'],
        $data['kvkk'],
        $data['message']
    );

    $headers = [];
    if (is_email($data['email'])) {
        $headers[] = 'Reply-To: ' . $data['email'];
    }
    wp_mail($to, $subject, $body, $headers);
}
