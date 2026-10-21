<?php
/**
 * Local integration checks. Notifications are intercepted; fixtures are removed.
 * Run: docker compose exec -T wordpress php wp-content/plugins/myliba-core/tests/forms.php
 */
namespace Myliba\Core\Forms;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__, 4) . '/wp-load.php';

if (!in_array(wp_get_environment_type(), ['local', 'development'], true)) {
    throw new \RuntimeException('Run only in a local or development WordPress.');
}

class FormTestRedirect extends \RuntimeException {}

// Intercept only this handler's dependencies, keeping real WordPress storage.
function wp_safe_redirect($location, $status = 302, $x_redirect_by = 'WordPress')
{
    throw new FormTestRedirect($location);
}

function wp_insert_post($postarr, $wp_error = false, $fire_after_hooks = true)
{
    $GLOBALS['form_test_insert_attempts']++;
    if (!$wp_error) {
        throw new \RuntimeException('Insertion errors must be requested as WP_Error.');
    }
    if ($GLOBALS['form_test_insert_mode'] === 'zero') {
        return 0;
    }
    if ($GLOBALS['form_test_insert_mode'] === 'error') {
        return new \WP_Error('db_insert_error', 'Simulated database failure');
    }
    $id = \wp_insert_post($postarr, $wp_error, $fire_after_hooks);
    if ($id && !is_wp_error($id)) {
        $GLOBALS['form_test_ids'][] = $id;
    }
    return $id;
}

function wp_mail($to, $subject, $message, $headers = '', $attachments = [])
{
    $GLOBALS['form_test_mails'][] = compact('to', 'subject', 'message');
    return $GLOBALS['form_test_mail_result'];
}

function form_test_verify(bool $condition, string $message): void
{
    if (!$condition) {
        throw new \RuntimeException($message);
    }
    $GLOBALS['form_test_checks']++;
}

$GLOBALS['form_test_ids'] = [];
$GLOBALS['form_test_checks'] = 0;
$original_post = $_POST;
$ip = '192.0.2.' . random_int(1, 254);
$ip_filter = static fn() => $ip;
add_filter('myliba_form_client_ip', $ip_filter);
$rate_key = 'myliba_contact_' . hash('sha256', $ip);
$previous_rate = get_transient($rate_key);
try {
    $valid = [
        'myliba_contact_nonce' => wp_create_nonce('myliba_contact_form'),
        '_wp_http_referer' => home_url('/tr/demo/'),
        'form_context' => 'demo',
        'name' => 'Integration', 'last_name' => 'Fixture',
        'email' => 'forms-test@example.invalid', 'phone' => '0000000000',
        'company' => 'Local integration fixture', 'employee_count' => '1-50',
        'job_title' => 'Tester', 'message' => 'Local test only', 'kvkk' => '1',
    ];
    $cases = [
        ['saved', [], 'real', true, false, 'success', 1, 1],
        ['notification failed after save', [], 'real', false, false, 'success', 1, 1],
        ['zero insertion result', [], 'zero', true, false, 'error', 1, 0],
        ['WP_Error insertion result', [], 'error', true, false, 'error', 1, 0],
        ['invalid nonce', ['myliba_contact_nonce' => 'invalid'], 'real', true, false, 'error', 0, 0],
        ['missing required company', ['company' => ''], 'real', true, false, 'error', 0, 0],
        ['rate limited', [], 'real', true, true, 'error', 0, 0],
        // Honeypot intentionally looks successful to bots but stores/sends nothing.
        ['honeypot', ['website' => 'spam.invalid'], 'real', true, false, 'success', 0, 0],
    ];
    foreach ($cases as [$label, $overrides, $mode, $mail_result, $limited, $expected, $attempts, $mails]) {
        $_POST = array_replace($valid, $overrides);
        $GLOBALS['form_test_insert_mode'] = $mode;
        $GLOBALS['form_test_mail_result'] = $mail_result;
        $GLOBALS['form_test_insert_attempts'] = 0;
        $GLOBALS['form_test_mails'] = [];
        $before = count($GLOBALS['form_test_ids']);
        set_transient($rate_key, $limited ? 5 : 0, HOUR_IN_SECONDS);
        try {
            handle();
            throw new \RuntimeException($label . ': handler did not redirect');
        } catch (FormTestRedirect $redirect) {
            parse_str((string) wp_parse_url($redirect->getMessage(), PHP_URL_QUERY), $query);
            form_test_verify(($query['myliba_form'] ?? '') === $expected, $label . ': wrong form status');
            form_test_verify(!str_contains($redirect->getMessage(), 'Simulated'), $label . ': database details leaked');
        }
        form_test_verify($GLOBALS['form_test_insert_attempts'] === $attempts, $label . ': unexpected insert attempt');
        form_test_verify(count($GLOBALS['form_test_mails']) === $mails, $label . ': unexpected notification');
        $created = $mode === 'real' && $attempts === 1;
        form_test_verify(count($GLOBALS['form_test_ids']) === $before + (int) $created, $label . ': unexpected stored record');
        if ($created) {
            $id = end($GLOBALS['form_test_ids']);
            form_test_verify(get_post_status($id) === 'private', $label . ': record must be private');
            foreach (['form_context' => 'demo', 'email' => $valid['email'], 'company' => $valid['company']] as $key => $value) {
                form_test_verify(get_post_meta($id, '_myliba_form_' . $key, true) === $value, $label . ': missing ' . $key);
            }
        }
        echo 'PASS: ' . $label . "\n";
    }
    echo 'Passed ' . $GLOBALS['form_test_checks'] . " checks across 8 scenarios.\n";
} finally {
    foreach ($GLOBALS['form_test_ids'] as $id) {
        \wp_delete_post($id, true);
    }
    if ($previous_rate === false) {
        delete_transient($rate_key);
    } else {
        set_transient($rate_key, $previous_rate, HOUR_IN_SECONDS);
    }
    remove_filter('myliba_form_client_ip', $ip_filter);
    $_POST = $original_post;
}
