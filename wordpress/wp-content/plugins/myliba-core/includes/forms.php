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

function render_form(string $context): string
{
    $status = isset($_GET['myliba_form']) ? sanitize_key(wp_unslash($_GET['myliba_form'])) : '';
    $is_demo = $context === 'demo';

    ob_start();
    ?>
    <form class="myliba-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="myliba_contact_form">
        <input type="hidden" name="form_context" value="<?php echo esc_attr($context); ?>">
        <?php wp_nonce_field('myliba_contact_form', 'myliba_contact_nonce'); ?>
        <div class="myliba-hp-field" aria-hidden="true">
            <label for="myliba-website">Website</label>
            <input id="myliba-website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <?php if ($status === 'success') : ?>
            <p class="myliba-form__status myliba-form__status--success"><?php esc_html_e('Your message has been received.', 'myliba'); ?></p>
        <?php elseif ($status === 'error') : ?>
            <p class="myliba-form__status myliba-form__status--error"><?php esc_html_e('The form could not be sent. Please try again.', 'myliba'); ?></p>
        <?php endif; ?>

        <div class="myliba-form__grid">
            <label>
                <span><?php echo esc_html($is_demo ? __('First name', 'myliba') : __('Name', 'myliba')); ?></span>
                <input name="name" required>
            </label>
            <?php if ($is_demo) : ?>
                <label>
                    <span><?php esc_html_e('Last name', 'myliba'); ?></span>
                    <input name="last_name" required>
                </label>
            <?php endif; ?>
            <label>
                <span><?php esc_html_e('Business email', 'myliba'); ?></span>
                <input type="email" name="email" required>
            </label>
            <label>
                <span><?php esc_html_e('Phone', 'myliba'); ?></span>
                <input name="phone" inputmode="tel" <?php echo $is_demo ? 'required' : ''; ?>>
            </label>
            <label>
                <span><?php esc_html_e('Company', 'myliba'); ?></span>
                <input name="company" <?php echo $is_demo ? 'required' : ''; ?>>
            </label>
            <?php if ($is_demo) : ?>
                <label>
                    <span><?php esc_html_e('Title', 'myliba'); ?></span>
                    <input name="job_title">
                </label>
                <label>
                    <span><?php esc_html_e('Employee count', 'myliba'); ?></span>
                    <select name="employee_count">
                        <option value="1-50">1-50</option>
                        <option value="51-250">51-250</option>
                        <option value="251-1000">251-1000</option>
                        <option value="1000+">1000+</option>
                    </select>
                </label>
            <?php endif; ?>
        </div>
        <?php if (!$is_demo) : ?>
            <label>
                <span><?php esc_html_e('Subject', 'myliba'); ?></span>
                <input name="subject" required>
            </label>
        <?php endif; ?>
        <input type="hidden" name="type" value="<?php echo esc_attr($is_demo ? 'demo' : 'contact'); ?>">
        <label>
            <span><?php esc_html_e('Message', 'myliba'); ?></span>
            <textarea name="message" rows="6" <?php echo $is_demo ? '' : 'required'; ?>></textarea>
        </label>
        <label class="myliba-form__consent">
            <input type="checkbox" name="kvkk" value="1" required>
            <span><?php esc_html_e('I consent to being contacted about this request and accept the privacy notice.', 'myliba'); ?></span>
        </label>
        <button class="myliba-button myliba-button--primary" type="submit"><?php echo esc_html($is_demo ? __('Request demo', 'myliba') : __('Send', 'myliba')); ?></button>
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
    $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    $key = 'myliba_contact_' . md5($ip);
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
        "Name: %s %s\nEmail: %s\nPhone: %s\nCompany: %s\nTitle: %s\nEmployee count: %s\nSubject: %s\nType: %s\nKVKK: %s\n\nMessage:\n%s",
        $data['name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $data['company'],
        $data['job_title'],
        $data['employee_count'],
        $data['subject'],
        $data['type'],
        $data['kvkk'],
        $data['message']
    );

    wp_mail($to, $subject, $body, ['Reply-To: ' . $data['email']]);
}
