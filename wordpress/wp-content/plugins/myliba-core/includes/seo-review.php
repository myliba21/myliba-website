<?php

namespace Myliba\Core\SEOReview;

use Myliba\Core\Options;
use Myliba\Core\SEO;

if (!defined('ABSPATH')) {
    exit;
}

const REPORT_KEY = '_myliba_seo_review';

function boot(): void
{
    add_action('add_meta_boxes_post', __NAMESPACE__ . '\\register_box');
    add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets');
    add_action('wp_ajax_myliba_seo_review', __NAMESPACE__ . '\\ajax_review');
    add_filter('manage_post_posts_columns', __NAMESPACE__ . '\\columns');
    add_action('manage_post_posts_custom_column', __NAMESPACE__ . '\\render_column', 10, 2);
}

function rules(): array
{
    static $rules;
    if ($rules === null) {
        $rules = json_decode(file_get_contents(MYLIBA_CORE_DIR . 'resources/seo-review-rules.json'), true);
    }
    return $rules;
}

function register_box(): void
{
    add_meta_box('myliba_seo_review', 'SEO ve AI Hazırlık Kontrolü', __NAMESPACE__ . '\\render_box', 'post', 'normal', 'high');
}

function enqueue_assets(string $hook): void
{
    if (!in_array($hook, ['post.php', 'post-new.php'], true) || get_current_screen()->post_type !== 'post') {
        return;
    }
    $base = MYLIBA_CORE_DIR . 'assets/';
    wp_enqueue_style('myliba-seo-review', MYLIBA_CORE_URL . 'assets/seo-review.css', [], (string) filemtime($base . 'seo-review.css'));
    wp_enqueue_script('myliba-seo-review', MYLIBA_CORE_URL . 'assets/seo-review.js', ['jquery', 'wp-data'], (string) filemtime($base . 'seo-review.js'), true);
}

function ai_config(): array
{
    return [
        'key' => defined('MYLIBA_SEO_AI_KEY') ? (string) MYLIBA_SEO_AI_KEY : (string) getenv('MYLIBA_SEO_AI_KEY'),
        'model' => defined('MYLIBA_SEO_AI_MODEL') ? (string) MYLIBA_SEO_AI_MODEL : (string) getenv('MYLIBA_SEO_AI_MODEL'),
    ];
}

function snapshot(\WP_Post $post): array
{
    // Include custom fields, SEO settings and author context, but not our own report or edit locks.
    $meta = get_post_meta($post->ID);
    unset($meta[REPORT_KEY], $meta['_edit_lock'], $meta['_edit_last']);
    ksort($meta);
    $author = get_userdata($post->post_author);
    return [
        'post' => array_intersect_key($post->to_array(), array_flip([
            'ID', 'post_title', 'post_content', 'post_excerpt', 'post_name', 'post_status',
            'post_password', 'post_author', 'post_modified_gmt', 'post_type',
        ])),
        'meta' => $meta,
        'author' => $author ? [$author->display_name, get_user_meta($author->ID, 'description', true)] : [],
        'terms' => wp_get_object_terms($post->ID, get_object_taxonomies($post->post_type), ['fields' => 'ids', 'orderby' => 'term_id']),
        'environment' => [home_url(), get_option('permalink_structure'), get_option('blog_public'), Options\indexing_enabled(), SEO\is_staging_host(), SEO\seo_plugin_active(), get_stylesheet(), wp_get_theme()->get('Version')],
        'version' => rules()['version'],
    ];
}

function fingerprint(\WP_Post $post): string
{
    return hash('sha256', wp_json_encode(snapshot($post)));
}

function report_is_stale(array $report, \WP_Post $post): bool
{
    return ($report['version'] ?? '') !== rules()['version'] || !hash_equals((string) ($report['hash'] ?? ''), fingerprint($post));
}

function render_box(\WP_Post $post): void
{
    \Myliba\Core\Meta\nonce();
    \Myliba\Core\Meta\field_text('_myliba_review_question', 'Yazının cevapladığı ana soru', get_post_meta($post->ID, '_myliba_review_question', true), 'Örnek: Bir şirket OKR yazılımını hangi ölçütlerle seçmeli?');
    \Myliba\Core\Meta\field_text('_myliba_review_audience', 'Hedef okuyucu', get_post_meta($post->ID, '_myliba_review_audience', true), 'Örnek: İnsan kaynakları ve strateji yöneticileri');
    $report = get_post_meta($post->ID, REPORT_KEY, true);
    $config = ai_config();
    ?>
    <div id="myliba-review" data-post-id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('myliba_seo_review_' . $post->ID)); ?>">
        <p>Kaydedilmiş yazı gövdesi ve SEO alanları değerlendirilir. Puan yalnızca ölçülen kriterleri kapsar; arama sıralaması veya AI yanıtlarında yer alma garantisi değildir.</p>
        <div class="myliba-review-actions">
            <button type="button" class="button button-secondary" data-review-mode="automatic">İçeriği analiz et</button>
            <?php if ($config['key'] !== '' && $config['model'] !== '') : ?>
                <button type="button" class="button button-primary" data-review-mode="ai">AI ile değerlendir</button>
            <?php endif; ?>
            <span class="spinner" aria-hidden="true"></span>
        </div>
        <?php if ($config['key'] !== '' && $config['model'] !== '') : ?>
            <p class="description">AI düğmesi kaydedilmiş yazıyı, SEO alanlarını ve yazarın herkese açık profilini değerlendirme için OpenAI'a gönderir; API kullanım ücreti oluşabilir. AI önerileri editör kontrolü gerektirir.</p>
        <?php else : ?>
            <p class="description">Yerel kontroller kullanılabilir. Anlam, arama niyeti ve AI içerik önerileri için sunucuda AI bağlantısı yapılandırılmalıdır.</p>
        <?php endif; ?>
        <p class="myliba-review-message" role="status" aria-live="polite"></p>
        <p class="myliba-review-dirty" hidden>Kaydedilmemiş değişiklikler var. Önce yazıyı ve SEO alanlarını kaydedin; ardından analizi yenileyin.</p>
        <div class="myliba-review-result" aria-live="polite"><?php
            if (is_array($report) && isset($report['checks'])) {
                echo report_html($report, report_is_stale($report, $post)); // Escaped by renderer.
            } else {
                echo '<p>Henüz analiz yapılmadı. Yazıyı kaydedip “İçeriği analiz et” düğmesine basın.</p>';
            }
        ?></div>
    </div>
    <?php
}

/** Only structural evidence is automated. Editorial quality is scored separately. */
function analyze(\WP_Post $post): array
{
    $checks = [];
    foreach (rules()['criteria'] as $criterion) {
        $checks[$criterion['id']] = $criterion + [
            'status' => 'unmeasured', 'evidence' => '', 'recommendation' => $criterion['guidance'],
        ];
    }
    $external = SEO\seo_plugin_active();
    $title = trim((string) (get_post_meta($post->ID, '_myliba_seo_title', true) ?: $post->post_title));
    $description = trim((string) get_post_meta($post->ID, '_myliba_seo_description', true));
    $description = $description !== '' ? $description : trim(wp_strip_all_tags($post->post_excerpt ?: $post->post_content));
    if (!$external) {
        $filled = ($title !== '' ? 1 : 0) + ($description !== '' ? 1 : 0);
        $checks['C2a']['status'] = ['fail', 'partial', 'pass'][$filled];
        $checks['C2a']['evidence'] = sprintf('Başlık: %d, açıklama kaynağı: %d karakter. Boş SEO alanlarında başlık ve özet/gövde yedeği kullanılır. Uzunluklar sıralama şartı değildir.', mb_strlen($title), mb_strlen($description));
        $checks['C2a']['recommendation'] = $filled === 2 ? 'Başlık ve açıklamanın niyeti doğru yansıttığını ayrıca değerlendirin.' : $checks['C2a']['guidance'];
    } else {
        $checks['C2a']['evidence'] = 'Haricî SEO eklentisi etkin; Myliba alanları gerçek çıktıyı temsil etmeyebilir. Eklentinin alanları ve canlı etiketler incelenmeli.';
    }

    $html = $post->post_content;
    $dom = new \DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?><html><body>' . $html . '</body></html>', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new \DOMXPath($dom);
    $headings = $xpath->query('//body//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6]');
    $issues = [];
    $previous_level = 1; // The theme supplies the article H1.
    $h2_count = 0;
    foreach ($headings as $heading) {
        $level = (int) substr($heading->nodeName, 1);
        if ($level === 1) {
            $issues[] = 'Gövde içinde H1 var; yazı başlığı temada H1 olarak gösterilir.';
        }
        if ($level > $previous_level + 1) {
            $issues[] = 'Başlık seviyesinde atlama: ' . $heading->nodeName . ' (' . mb_substr(trim($heading->textContent), 0, 80) . ').';
        }
        if (trim($heading->textContent) === '') {
            $issues[] = 'Boş bir alt başlık var.';
        }
        $h2_count += $level === 2 ? 1 : 0;
        $previous_level = $level;
    }
    // No minimum heading count: short, single-section posts can be valid.
    $checks['C3a']['status'] = $issues ? 'fail' : ($headings->length > 0 ? 'pass' : 'unmeasured');
    $checks['C3a']['evidence'] = $issues ? implode(' ', array_unique($issues)) : sprintf('%d H2 bulundu. Başlıkların anlamı ve bölümleme ihtiyacı editoryal kontrolde değerlendirilir.', $h2_count);
    if (!$issues && $headings->length > 0) {
        $checks['C3a']['recommendation'] = 'Başlıkların bölümleri açıkça anlattığını inceleyin.';
    }

    $internal = [];
    $sources = 0;
    $site_host = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
    foreach ($xpath->query('//body//a[@href]') as $link) {
        $href = trim($link->getAttribute('href'));
        $parts = wp_parse_url($href);
        if ($href === '' || $href[0] === '#' || $parts === false || (!empty($parts['scheme']) && !in_array(strtolower($parts['scheme']), ['http', 'https'], true))) {
            continue;
        }
        $is_internal = empty($parts['host']) || strtolower($parts['host']) === $site_host;
        if ($is_internal && trim($link->textContent) !== '') {
            $internal[] = mb_substr(trim($link->textContent), 0, 90);
        } elseif (!$is_internal) {
            $sources++;
        }
    }
    $checks['C4a']['status'] = $internal ? 'pass' : 'fail';
    $checks['C4a']['evidence'] = $internal ? count($internal) . ' metinli dahili bağlantı: ' . implode(', ', array_slice($internal, 0, 3)) : 'Yazı gövdesinde metinli dahili bağlantı bulunmadı.';
    if ($internal) {
        $checks['C4a']['recommendation'] = 'Bağlantı hedeflerinin çalıştığını ve bağlama uygunluğunu ayrıca kontrol edin.';
    }
    $checks['A2']['evidence'] = $sources . ' dış bağlantı bulundu. Bağlantı varlığı iddiaların doğrulandığı anlamına gelmez.';
    $critical = [];
    if (trim(wp_strip_all_tags($html)) === '') {
        $critical[] = 'Yazı gövdesinde okunabilir metin yok. Medya, bloklar veya kısa kod çıktısı ayrıca kontrol edilmeli.';
    }
    $staging = wp_get_environment_type() !== 'production' || SEO\is_staging_host();
    if (!$staging && $post->post_status === 'publish') {
        if (!Options\indexing_enabled() || (string) get_option('blog_public') === '0' || get_post_meta($post->ID, '_myliba_noindex', true) === '1') {
            $critical[] = 'Yayımlanmış yazıda/site ayarlarında indeksleme kapalı. Bilinçli bir tercih olup olmadığını doğrulayın.';
        }
        if ($post->post_password !== '') {
            $critical[] = 'Yayımlanmış yazı parola korumalı; kamusal erişim amacını doğrulayın.';
        }
    }
    return [
        'version' => rules()['version'], 'hash' => fingerprint($post), 'analyzed_at' => gmdate('c'),
        'mode' => 'automatic', 'model' => '', 'checks' => $checks, 'critical' => $critical,
        'notes' => [
            'Canlı URL, HTTP, robots.txt, canonical, hreflang, sitemap, performans ve schema bu analizde ölçülmedi.',
            $staging ? 'Yerel/staging ortamındaki indeksleme korumaları hata olarak puanlanmadı.' : 'Yerel ayarlar canlı URL doğrulamasının yerine geçmez.',
            'AI yanıtlarında kaynak gösterilme ve gerçek arama performansı ayrı ölçülmelidir.',
        ],
    ];
}

function summarize(array $checks): array
{
    $result = ['score' => null, 'coverage' => 0, 'measured' => 0, 'applicable' => 0, 'earned' => 0];
    foreach ($checks as $check) {
        if ($check['status'] === 'na') {
            continue;
        }
        $result['applicable'] += $check['weight'];
        if (in_array($check['status'], ['pass', 'partial', 'fail'], true)) {
            $result['measured'] += $check['weight'];
            $result['earned'] += $check['weight'] * ['pass' => 1, 'partial' => 0.5, 'fail' => 0][$check['status']];
        }
    }
    if ($result['measured'] > 0) {
        $result['score'] = (int) round(100 * $result['earned'] / $result['measured']);
    }
    if ($result['applicable'] > 0) {
        $result['coverage'] = (int) round(100 * $result['measured'] / $result['applicable']);
    }
    return $result;
}

function report_html(array $report, bool $stale = false): string
{
    $summary = summarize($report['checks']);
    $statuses = ['pass' => 'Geçti', 'partial' => 'Kısmi', 'fail' => 'Eksik', 'unmeasured' => 'Ölçülmedi', 'na' => 'Uygulanamaz'];
    ob_start();
    ?>
    <div class="myliba-review-report<?php echo $stale ? ' is-stale' : ''; ?>">
        <?php if ($stale) : ?><p class="myliba-review-warning">Bu analiz eski. İçerik, SEO ayarları veya kontrol kuralları değişmiş; analizi yenileyin.</p><?php endif; ?>
        <div class="myliba-review-summary">
            <div><strong><?php echo $summary['score'] === null ? '—' : esc_html($summary['score'] . '/100'); ?></strong><span>Geçici hazırlık puanı</span></div>
            <div><strong><?php echo esc_html('%' . $summary['coverage']); ?></strong><span>Ölçülen kriterlerin kapsamı</span></div>
        </div>
        <p class="description">Puan yalnızca ölçülen kriterlerden hesaplanır. Yüksek puan, tam denetim veya yayına hazır olunduğu anlamına gelmez.</p>
        <?php if ($report['critical']) : ?>
            <div class="myliba-review-warning"><strong>Öncelikli engeller</strong><ul><?php foreach ($report['critical'] as $issue) : ?><li><?php echo esc_html($issue); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <div class="myliba-review-groups">
            <?php foreach (rules()['groups'] as $prefix => $label) :
                $group = summarize(array_filter($report['checks'], static function ($check) use ($prefix) { return str_starts_with($check['id'], $prefix); })); ?>
                <p><strong><?php echo esc_html($label); ?></strong>: <?php echo $group['score'] === null ? 'Ölçülmedi' : esc_html($group['score'] . '/100 · kapsam %' . $group['coverage']); ?></p>
            <?php endforeach; ?>
        </div>
        <?php
        $priorities = array_filter($report['checks'], static function ($check) { return in_array($check['status'], ['fail', 'partial'], true); });
        uasort($priorities, static function ($a, $b) { return $b['weight'] <=> $a['weight']; });
        if ($priorities) : ?>
            <h4>Önce bunları iyileştirin</h4><ol><?php foreach (array_slice($priorities, 0, 3) as $check) : ?><li><strong><?php echo esc_html($check['label']); ?>:</strong> <?php echo esc_html($check['recommendation']); ?></li><?php endforeach; ?></ol>
        <?php endif; ?>
        <details><summary>Tüm kontroller, kanıtlar ve öneriler</summary>
            <div class="myliba-review-table-wrap"><table class="widefat striped"><thead><tr><th scope="col">Kontrol</th><th scope="col">Durum</th><th scope="col">Kanıt ve öneri</th></tr></thead><tbody>
            <?php foreach ($report['checks'] as $check) : ?>
                <tr><th scope="row"><?php echo esc_html($check['id'] . ' · ' . $check['label']); ?><small><?php echo esc_html($check['weight'] . ' puan ağırlık · ' . ($check['method'] === 'automatic' ? 'Yerel kontrol' : ($check['method'] === 'live' ? 'Canlı kontrol' : 'Editoryal değerlendirme'))); ?></small></th>
                    <td><span class="myliba-review-badge <?php echo esc_attr('status-' . $check['status']); ?>"><?php echo esc_html($statuses[$check['status']] ?? 'Ölçülmedi'); ?></span></td>
                    <td><?php echo esc_html($check['evidence'] ?: 'Bu kriter henüz ölçülmedi.'); ?><p><?php echo esc_html($check['recommendation']); ?></p></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </details>
        <ul class="myliba-review-notes"><?php foreach ($report['notes'] as $note) : ?><li><?php echo esc_html($note); ?></li><?php endforeach; ?></ul>
        <p class="description"><?php echo esc_html('Kural sürümü ' . $report['version'] . ' · ' . wp_date('d.m.Y H:i', strtotime($report['analyzed_at'])) . ' · ' . ($report['mode'] === 'ai' ? 'AI + yerel kontrol (' . $report['model'] . ')' : 'Yerel kontrol')); ?></p>
    </div>
    <?php
    return (string) ob_get_clean();
}

function ajax_review(): void
{
    $post_id = isset($_POST['post_id']) && is_scalar($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(['message' => 'Bu yazıyı analiz etme yetkiniz yok.'], 403);
    }
    check_ajax_referer('myliba_seo_review_' . $post_id, 'nonce');
    $post = get_post($post_id);
    if (!$post instanceof \WP_Post || $post->post_type !== 'post' || in_array($post->post_status, ['auto-draft', 'trash'], true)) {
        wp_send_json_error(['message' => 'Önce yazıyı taslak olarak kaydedin.'], 400);
    }
    $mode = isset($_POST['mode']) && is_string($_POST['mode']) ? sanitize_key($_POST['mode']) : '';
    if (!in_array($mode, ['automatic', 'ai'], true)) {
        wp_send_json_error(['message' => 'Geçersiz analiz türü.'], 400);
    }
    $lock = 'myliba_review_' . $post_id;
    // add_option is atomic (unique option_name); transient read-then-write is not a lock.
    if (!add_option($lock, time(), '', false)) {
        if ((int) get_option($lock) < time() - 120) {
            delete_option($lock);
        }
        wp_send_json_error(['message' => 'Bu yazı için analiz sürüyor. Biraz sonra tekrar deneyin.'], 409);
    }
    $error = null;
    try {
        $report = analyze($post);
        $previous = get_post_meta($post_id, REPORT_KEY, true);
        if ($mode === 'automatic' && is_array($previous) && ($previous['mode'] ?? '') === 'ai' && !report_is_stale($previous, $post)) {
            // Refresh local checks without throwing away a still-current editorial review.
            foreach ($report['checks'] as $id => $check) {
                if ($check['method'] === 'editorial' && isset($previous['checks'][$id])) {
                    $report['checks'][$id] = $previous['checks'][$id];
                }
            }
            $report['mode'] = 'ai';
            $report['model'] = $previous['model'];
            $report['notes'][] = 'Bu içerik sürümünün önceki editoryal AI değerlendirmesi korundu.';
        }
        if ($mode === 'ai') {
            $result = add_ai_review($report, $post);
            if (is_wp_error($result)) {
                $error = $result;
            } else {
                $report = $result;
            }
        }
        if (!$error) {
            clean_post_cache($post_id);
            $current = get_post($post_id);
            if (!$current || report_is_stale($report, $current)) {
                $error = new \WP_Error('changed', 'Analiz sırasında yazı değişti. Güncel sürümü kaydedip yeniden analiz edin.');
            } else {
                update_post_meta($post_id, REPORT_KEY, wp_slash($report));
            }
        }
    } finally {
        delete_option($lock);
    }
    if ($error) {
        wp_send_json_error(['message' => $error->get_error_message()], 400);
    }
    wp_send_json_success(['html' => report_html($report), 'hash' => $report['hash']]);
}

function columns(array $columns): array
{
    $columns['myliba_readiness'] = 'SEO / AI hazırlığı';
    return $columns;
}

function render_column(string $column, int $post_id): void
{
    if ($column !== 'myliba_readiness') {
        return;
    }
    $report = get_post_meta($post_id, REPORT_KEY, true);
    if (!is_array($report) || !isset($report['checks'])) {
        echo 'Henüz analiz yok';
        return;
    }
    $summary = summarize($report['checks']);
    echo esc_html(($summary['score'] === null ? '—' : $summary['score'] . '/100') . ' · kapsam %' . $summary['coverage']);
    echo '<br><small>' . esc_html(report_is_stale($report, get_post($post_id)) ? 'Eski analiz' : 'Geçici puan') . ($report['critical'] ? ' · Öncelikli engel var' : '') . '</small>';
}

require_once __DIR__ . '/seo-review-ai.php';
