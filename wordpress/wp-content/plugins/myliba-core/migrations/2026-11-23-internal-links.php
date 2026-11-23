<?php
/**
 * 2026-11-23 — Blog ve sayfa gövdelerindeki kırık / yönlendirmeli iç bağlantılar.
 *
 * Kaynak: docs/seo/audit-2026-11-23.md (A5). Yalnızca aşağıdaki haritadaki
 * iç bağlantıların href değerini nihai adrese çevirir; başka içeriğe dokunmaz.
 * İdempotenttir: ikinci çalıştırmada değişecek bağlantı kalmaz.
 *
 * Önizleme (hiçbir şey yazmaz):
 *   docker compose --profile tools run --rm wpcli wp eval-file wp-content/plugins/myliba-core/migrations/2026-11-23-internal-links.php
 * Uygula:
 *   docker compose --profile tools run --rm wpcli wp eval-file wp-content/plugins/myliba-core/migrations/2026-11-23-internal-links.php apply
 *
 * Yalnız yerel ortamda çalıştırın; değişiklik canlıya klonla gider.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('myliba_internal_link_map_20261123')) {
    /** Eski yol (sonunda / olmadan) => yeni yol. */
    function myliba_internal_link_map_20261123(): array
    {
        return [
            '/category/blog' => '/en/blog/',
            '/tr/category/blog' => '/tr/yazilar/',
            '/our-products' => '/en/software/',
            '/tr/urunlerimiz' => '/tr/yazilim/',
            '/contact' => '/en/contact/',
            '/en/solutions/corporate-development-programs' => '/en/okr-culture-academy/',
            '/tr/yazilar/okr-hatalari-okr-ne-degildir' => '/tr/yazilar/okr-hatalari-okr-ne-degildir-sirketlerin-dustugu-3-buyuk-hedef-yanilgisi/',
            '/tr/yazilar/okr-ile-kulturel-donusum' => '/tr/yazilar/okr-ile-kulturel-donusum/',
            '/tr/yazilar/okrin-iki-yuzu-populist-okr-mi-native-okr-mi' => '/tr/yazilar/okrin-iki-yuzu-populist-okr-mi-native-okr-mi/',
            '/tr/yazilar/performans-degerlendirmesine-veda' => '/tr/yazilar/performans-degerlendirmesine-veda/',
            '/tr/yazilar/cfr-nedir-hedef-ve-performans-yonetiminde-cfrin-rolu' => '/tr/yazilar/cfr-nedir-hedef-ve-performans-yonetiminde-cfrin-rolu/',
        ];
    }

    /**
     * Returns [new content, list of "old => new" changes].
     *
     * @param string[] $site_hosts Hosts that count as this site.
     */
    function myliba_rewrite_internal_links_20261123(string $content, array $site_hosts): array
    {
        $map = myliba_internal_link_map_20261123();
        $changes = [];

        $result = preg_replace_callback(
            '/(<a\b[^>]*?\bhref\s*=\s*)(["\'])(.*?)\2/is',
            static function (array $m) use ($map, $site_hosts, &$changes): string {
                $href = html_entity_decode(trim($m[3]), ENT_QUOTES);
                $parts = parse_url($href);
                if (!is_array($parts) || !isset($parts['path'])) {
                    return $m[0];
                }
                if (isset($parts['scheme']) && !in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
                    return $m[0];
                }
                if (isset($parts['host']) && !in_array(strtolower($parts['host']), $site_hosts, true)) {
                    return $m[0];
                }
                // Query strings are left alone on purpose (form states, tracking).
                if (isset($parts['query'])) {
                    return $m[0];
                }

                $path = $parts['path'];
                $key = $path === '/' ? '/' : rtrim($path, '/');
                if (!isset($map[$key])) {
                    return $m[0];
                }
                $new_path = $map[$key];
                // Slash-only fixes: an href that already ends with / is final.
                if ($new_path === $key . '/' && substr($path, -1) === '/') {
                    return $m[0];
                }

                $origin = '';
                if (isset($parts['host'])) {
                    $origin = ($parts['scheme'] ?? 'https') . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
                }
                $new_href = $origin . $new_path . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
                $changes[] = $href . ' => ' . $new_href;

                return $m[1] . $m[2] . esc_attr($new_href) . $m[2];
            },
            $content
        );

        return [is_string($result) ? $result : $content, $changes];
    }
}

if (!defined('MYLIBA_MIGRATION_TEST')) {
    $apply = isset($args) && is_array($args) && in_array('apply', $args, true);

    $home_host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
    // The wpcli container does not receive WORDPRESS_CONFIG_EXTRA, so the
    // environment type reads as "production" there. The site address is the
    // reliable signal that this is the local Docker stack.
    $is_local = in_array(wp_get_environment_type(), ['local', 'development'], true)
        || in_array($home_host, ['localhost', '127.0.0.1'], true);
    if (!$is_local) {
        WP_CLI::error('Bu betik yalnız yerel ortamda çalıştırılır.');
    }

    $site_hosts = array_values(array_unique([$home_host, 'myliba.com', 'www.myliba.com', 'localhost']));

    global $wpdb;
    $ids = $wpdb->get_col(
        "SELECT ID FROM {$wpdb->posts}
         WHERE post_status IN ('publish','draft','pending','future','private')
           AND post_type NOT IN ('revision','nav_menu_item','attachment','myliba_submission')
           AND post_content LIKE '%href=%'"
    );

    $posts_changed = 0;
    $links_changed = 0;
    foreach ($ids as $id) {
        $post = get_post((int) $id);
        if (!$post instanceof WP_Post) {
            continue;
        }
        [$content, $changes] = myliba_rewrite_internal_links_20261123((string) $post->post_content, $site_hosts);
        if (!$changes || $content === $post->post_content) {
            continue;
        }

        $posts_changed++;
        $links_changed += count($changes);
        WP_CLI::log(sprintf('#%d %s (%s)', $post->ID, $post->post_name, $post->post_type));
        foreach ($changes as $change) {
            WP_CLI::log('    ' . $change);
        }

        if ($apply) {
            $updated = wp_update_post(['ID' => $post->ID, 'post_content' => wp_slash($content)], true);
            if (is_wp_error($updated)) {
                WP_CLI::warning(sprintf('#%d güncellenemedi: %s', $post->ID, $updated->get_error_message()));
            }
        }
    }

    $summary = sprintf('%d kayıtta %d bağlantı', $posts_changed, $links_changed);
    if ($apply) {
        WP_CLI::success($summary . ' güncellendi.');
    } else {
        WP_CLI::log($summary . ' değişecek. Uygulamak için komutun sonuna "apply" ekleyin.');
    }
}
