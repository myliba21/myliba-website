<?php
/** Run inside an installed local WordPress; all OpenAI traffic is mocked. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__, 4) . '/wp-load.php';

use Myliba\Core\SEOReview as Review;

if (wp_get_environment_type() === 'production') {
    throw new RuntimeException('Run these checks only in a local or test WordPress.');
}
define('MYLIBA_SEO_AI_KEY', 'test-key-not-real');
define('MYLIBA_SEO_AI_MODEL', 'test-model');
define('DOING_AJAX', true);

$count = 0;
$post_id = 0;
$http_filter = null;
$original_user = get_current_user_id();
function verify($condition, string $message): void
{
    global $count;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $count++;
}

// Capture AJAX exits without exiting the test process.
add_filter('wp_die_ajax_handler', static function () {
    return static function ($message = '') {
        if (is_scalar($message) && $message !== '') {
            echo $message;
        }
        throw new RuntimeException('ajax-exit');
    };
});
function ajax_call(array $data): array
{
    $_POST = $data;
    $_REQUEST = $data;
    ob_start();
    try {
        Review\ajax_review();
    } catch (RuntimeException $error) {
        if ($error->getMessage() !== 'ajax-exit') {
            ob_end_clean();
            throw $error;
        }
    }
    $text = ob_get_clean();
    return [json_decode($text, true), $text];
}

try {
    $criteria = Review\rules()['criteria'];
    verify(array_sum(array_column($criteria, 'weight')) === 100, 'Rubric weights must sum to 100.');
    foreach (['C' => 40, 'T' => 30, 'A' => 30] as $prefix => $weight) {
        verify(array_sum(array_column(array_filter($criteria, static fn($c) => str_starts_with($c['id'], $prefix)), 'weight')) === $weight, 'Group weights changed.');
    }
    $post_id = wp_insert_post([
        'post_type' => 'post', 'post_status' => 'draft', 'post_title' => 'SEO review integration fixture',
        'post_content' => '<p>OKR, hedeflerin sonuçlarla izlenmesini sağlar.</p><h2>Nasıl uygulanır?</h2><p>Bir örnek.</p><h3>Ölçüm</h3><p><a href="/tr/yazilim/">OKR yazılımı</a> ile izleyin. <a href="https://example.org/source">Kaynak</a></p>',
    ], true);
    verify(!is_wp_error($post_id), 'Could not create fixture.');
    update_post_meta($post_id, '_myliba_review_question', 'OKR nasıl uygulanır?');
    update_post_meta($post_id, '_myliba_review_audience', 'Strateji yöneticileri');
    $post = get_post($post_id);
    $report = Review\analyze($post);
    verify($report['checks']['C2a']['status'] === 'pass', 'Title/body fallback should be recognized.');
    verify($report['checks']['C3a']['status'] === 'pass', 'Valid heading hierarchy should pass.');
    verify($report['checks']['C4a']['status'] === 'pass', 'Relative internal link should pass.');
    verify($report['checks']['A2']['status'] === 'unmeasured', 'External link must not prove source truth.');
    verify(!$report['critical'], 'Staging draft should not fail intentional indexing protection.');
    $summary = Review\summarize($report['checks']);
    verify($summary['score'] === 100 && $summary['coverage'] === 12, 'Partial coverage must not become a full audit.');
    verify(Review\analyze($post)['checks'] === $report['checks'], 'Same deterministic input must return same checks.');
    verify(Review\summarize([['status' => 'unmeasured', 'weight' => 10]])['score'] === null, 'Unknown-only score must be absent.');
    verify(Review\summarize([['status' => 'partial', 'weight' => 10], ['status' => 'fail', 'weight' => 10], ['status' => 'na', 'weight' => 10]])['score'] === 25, 'Partial/NA math is incorrect.');

    update_post_meta($post_id, Review\REPORT_KEY, $report);
    verify(!Review\report_is_stale($report, get_post($post_id)), 'Saving the report must not stale itself.');
    update_post_meta($post_id, '_edit_lock', 'test');
    verify(!Review\report_is_stale($report, get_post($post_id)), 'Edit lock must not stale analysis.');
    update_post_meta($post_id, '_myliba_seo_description', 'Updated description');
    verify(Review\report_is_stale($report, get_post($post_id)), 'SEO metadata changes must stale analysis.');
    wp_update_post(['ID' => $post_id, 'post_content' => '<h1>Duplicate H1</h1><h4>Skipped level</h4><p><a href="#section">An anchor</a><a href="mailto:test@example.org">Mail</a></p>']);
    $bad = Review\analyze(get_post($post_id));
    verify($bad['checks']['C3a']['status'] === 'fail', 'Invalid heading levels should be detected.');
    verify($bad['checks']['C4a']['status'] === 'fail', 'Anchor/mail/script links must not pass internal-link check.');
    wp_update_post(['ID' => $post_id, 'post_content' => '<p>A short, valid single-section article.</p>']);
    $short = Review\analyze(get_post($post_id));
    verify($short['checks']['C3a']['status'] === 'unmeasured', 'A short post should not fail a minimum heading count.');
    $escaped = $short;
    $escaped['checks']['C2a']['evidence'] = '<script>alert(1)</script>';
    verify(!str_contains(Review\report_html($escaped), '<script>'), 'Report evidence must be escaped.');

    $calls = 0;
    $http_filter = static function ($pre, $args, $url) use (&$calls) {
        if ($url !== 'https://api.openai.com/v1/responses') {
            return $pre;
        }
        $calls++;
        $request = json_decode($args['body'], true);
        verify($request['store'] === false && $args['redirection'] === 0, 'API storage/redirect settings changed.');
        verify(!str_contains($args['body'], 'test-key-not-real'), 'Key leaked into model input.');
        $results = [];
        foreach ($request['text']['format']['schema']['required'] as $id) {
            $results[$id] = ['status' => 'pass', 'evidence' => 'Fixture evidence', 'recommendation' => 'Fixture recommendation'];
        }
        return ['headers' => [], 'response' => ['code' => 200], 'body' => wp_json_encode([
            'status' => 'completed', 'output' => [['type' => 'message', 'content' => [['type' => 'output_text', 'text' => wp_json_encode($results)]]]],
        ])];
    };
    add_filter('pre_http_request', $http_filter, 10, 3);
    $ai = Review\add_ai_review($short, get_post($post_id));
    verify(!is_wp_error($ai) && $ai['mode'] === 'ai', 'Mock AI review should succeed.');
    foreach (['C4b', 'A2', 'A3'] as $id) {
        verify($ai['checks'][$id]['status'] === 'unmeasured', 'AI must not invent verification for ' . $id);
    }
    verify($ai['checks']['T1']['status'] === 'unmeasured', 'AI must never score live checks.');
    update_post_meta($post_id, Review\REPORT_KEY, $ai);
    Review\add_ai_review($short, get_post($post_id));
    verify($calls === 1, 'Matching AI report should be reused without charges.');
    verify(is_wp_error(Review\apply_editorial_results($short, ['T1' => ['status' => 'pass']])), 'Unexpected AI keys must be rejected.');
    remove_filter('pre_http_request', $http_filter, 10);
    $http_filter = static function () { return new WP_Error('network', 'fixture failure'); };
    add_filter('pre_http_request', $http_filter, 10, 3);
    update_post_meta($post_id, '_myliba_review_question', 'A new question');
    verify(is_wp_error(Review\add_ai_review(Review\analyze(get_post($post_id)), get_post($post_id))), 'Network failure must fail cleanly.');
    verify(get_post_meta($post_id, Review\REPORT_KEY, true) === $ai, 'Network failure must preserve stored report.');
    remove_filter('pre_http_request', $http_filter, 10);
    $http_filter = null;

    $admin = get_users(['role' => 'administrator', 'number' => 1]);
    wp_set_current_user($admin[0]->ID);
    $params = ['post_id' => $post_id, 'mode' => 'automatic', 'nonce' => wp_create_nonce('myliba_seo_review_' . $post_id)];
    [$response] = ajax_call($params);
    verify($response['success'] === true && isset($response['data']['html']), 'Authorized AJAX review should succeed.');
    add_option('myliba_review_' . $post_id, time(), '', false);
    [$response] = ajax_call($params);
    verify($response['success'] === false, 'Concurrent review must be rejected.');
    delete_option('myliba_review_' . $post_id);
    [, $invalid_nonce] = ajax_call(array_merge($params, ['nonce' => 'invalid']));
    verify($invalid_nonce === '-1', 'Invalid nonce must be rejected.');
    wp_set_current_user(0);
    [$response] = ajax_call($params);
    verify($response['success'] === false, 'Unauthenticated request must be rejected.');
    wp_set_current_user($admin[0]->ID);
    define('WPSEO_VERSION', 'fixture');
    $external = Review\analyze(get_post($post_id));
    verify($external['checks']['C2a']['status'] === 'unmeasured', 'External SEO output must not be guessed from Myliba fields.');
    echo "PASS: {$count} integration checks; no external AI calls.\n";
} finally {
    if ($http_filter) remove_filter('pre_http_request', $http_filter, 10);
    if ($post_id && !is_wp_error($post_id)) {
        delete_option('myliba_review_' . $post_id);
        wp_delete_post($post_id, true);
    }
    wp_set_current_user($original_user);
}
