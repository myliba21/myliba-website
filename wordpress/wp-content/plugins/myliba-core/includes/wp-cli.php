<?php

namespace Myliba\Core\CLI;

use Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

class Commands
{
    /**
     * Seed the local WordPress site with the Myliba content structure.
     *
     * ## OPTIONS
     *
     * [--yes]
     * : Confirm the operation.
     */
    public function seed(array $args, array $assoc_args): void
    {
        if (empty($assoc_args['yes'])) {
            \WP_CLI::confirm('Seed starter pages, posts, events, and settings?');
        }

        update_option('myliba_options', array_merge(Options\defaults(), [
            'indexing_enabled' => '0',
            'contact_email' => get_option('admin_email'),
            'demo_url' => '/en/demo/',
            'footer_cta_title' => 'Ready to make culture measurable?',
            'primary_cta_url' => '/en/contact/',
        ]));

        $this->cleanup_default_content();

        $en = $this->upsert_page('Myliba', 'en', $this->starter_content('home_en'), array_merge([
            '_myliba_language' => 'en',
            '_myliba_eyebrow' => 'OKR, KPI, CFR and performance culture',
            '_myliba_hero_title' => 'Build a stronger culture around clear goals',
            '_myliba_hero_subtitle' => 'Myliba helps teams turn OKRs, ethics, security, and culture into measurable operating habits.',
            '_myliba_cta_label' => 'Contact us',
            '_myliba_cta_url' => '/en/contact/',
            '_myliba_seo_title' => 'Myliba | OKR, Culture, Ethics and Security Consulting',
            '_myliba_seo_description' => 'Myliba helps organizations build measurable operating culture with OKR, culture analysis, ethics counsel, and security guidance.',
        ], $this->home_meta_defaults('en')));

        $tr = $this->upsert_page('Myliba TR', 'tr', $this->starter_content('home_tr'), array_merge([
            '_myliba_language' => 'tr',
            '_myliba_eyebrow' => 'OKR, KPI, CFR ve performans kulturu',
            '_myliba_hero_title' => 'Hedefleri net, kulturu guclu ekipler kurun',
            '_myliba_hero_subtitle' => 'Myliba OKR, etik, guvenlik ve kultur calismalarini olculebilir aliskanliklara donusturur.',
            '_myliba_cta_label' => 'Iletisime gec',
            '_myliba_cta_url' => '/tr/iletisim/',
            '_myliba_seo_title' => 'Myliba | OKR, Kultur, Etik ve Guvenlik Danismanligi',
            '_myliba_seo_description' => 'Myliba kurumlarin OKR, kultur analizi, etik danismanlik ve guvenlik rehberligi ile olculebilir is kulturu kurmasina yardimci olur.',
        ], $this->home_meta_defaults('tr')));

        update_option('show_on_front', 'page');
        update_option('page_on_front', $en);

        $pages = [
            ['Our Products', 'our-products', $en, 'en', 'Products that make operating culture visible.', 'Explore product modules, assessment flows, and advisory packages.'],
            ['OKR Culture Academy', 'okr-culture-academy', $en, 'en', 'OKR Culture Academy', 'Training journeys for teams that want to use OKRs with discipline.'],
            ['Culture Analysis', 'culture-analysis', $en, 'en', 'Culture Analysis', 'Understand patterns, risks, and blockers before they become expensive.'],
            ['Ethics Counsel', 'ethics-counsel', $en, 'en', 'Ethics Counsel', 'Build practical ethics routines for leaders and teams.'],
            ['Blog', 'blog', $en, 'en', 'Blog', 'Articles, guides, and operating notes from Myliba.', 'template-blog.php'],
            ['Solutions', 'solutions', $en, 'en', 'Solutions for every performance culture owner.', 'Persona-based paths for executives, HR, strategy teams, leaders and employees.'],
            ['Events', 'events', $en, 'en', 'Events', 'Upcoming webinars, workshops, and community sessions.', 'template-events.php'],
            ['Demo', 'demo', $en, 'en', 'Request a Myliba demo', 'See how OKR, KPI, CFR, 1:1, feedback, actions and academy programs connect in one platform.', 'template-demo.php'],
            ['Contact', 'contact', $en, 'en', 'Contact Myliba', 'Tell us what you are building and we will route the request.', 'template-contact.php'],
            ['Our Story', 'our-story', $en, 'en', 'Our Story', 'Why Myliba exists and how we work.'],
            ['FAQ', 'faq', $en, 'en', 'FAQ', 'Answers to common questions about Myliba services.'],
            ['Security', 'security', $en, 'en', 'Security', 'How Myliba approaches trust, data, and operating security.'],
            ['Privacy Policy', 'privacy-policy', $en, 'en', 'Privacy Policy', 'Privacy practices for website visitors and contacts.'],
            ['KVKK', 'kvkk', $en, 'en', 'KVKK and GDPR approach', 'How Myliba approaches personal data protection, consent and business privacy.'],
            ['Cookie Policy', 'cookie-policy', $en, 'en', 'Cookie Policy', 'Cookie usage and tracking preferences.'],
            ['Terms of Use', 'terms-of-use', $en, 'en', 'Terms of Use', 'Website and service usage terms.'],
            ['Urunlerimiz', 'urunler', $tr, 'tr', 'Calisma kulturunu gorunur yapan urunler.', 'Modulleri, analiz akislarini ve danismanlik paketlerini yonetin.'],
            ['OKR Kultur Akademisi', 'okr-kultur-akademisi', $tr, 'tr', 'OKR Kultur Akademisi', 'OKR disiplinini ekip rutinlerine yerlestiren egitim yolculuklari.'],
            ['Kultur Analizi', 'kultur-analizi', $tr, 'tr', 'Kultur Analizi', 'Riskleri, darbogazlari ve ekip kaliplarini erken gorun.'],
            ['Etik Danismanlik', 'etik-danismanlik', $tr, 'tr', 'Etik Danismanlik', 'Liderler ve ekipler icin uygulanabilir etik rutinleri kurun.'],
            ['Yazilar', 'yazilar', $tr, 'tr', 'Yazilar', 'Myliba yazilari, rehberleri ve operasyon notlari.', 'template-blog.php'],
            ['Cozumler', 'cozumler', $tr, 'tr', 'Performans kulturu sahipleri icin cozumler.', 'Ust yonetim, IK, strateji ekipleri, liderler ve calisanlar icin persona bazli akislar.'],
            ['Etkinlikler', 'etkinlikler', $tr, 'tr', 'Etkinlikler', 'Webinar, atolye ve topluluk bulusmalari.', 'template-events.php'],
            ['Demo', 'demo', $tr, 'tr', 'Myliba demosu isteyin', 'OKR, KPI, CFR, 1:1, geri bildirim, aksiyon ve akademi akislarini tek platformda gorun.', 'template-demo.php'],
            ['Iletisim', 'iletisim', $tr, 'tr', 'Myliba ile iletisime gecin', 'Ihtiyacinizi anlatin, talebinizi dogru ekibe yonlendirelim.', 'template-contact.php'],
            ['Hikayemiz', 'hikayemiz', $tr, 'tr', 'Hikayemiz', 'Myliba neden var ve nasil calisir.'],
            ['SSS', 'sss', $tr, 'tr', 'SSS', 'Myliba hizmetleri hakkinda sik sorulan sorular.'],
            ['Guvenlik', 'guvenlik', $tr, 'tr', 'Guvenlik', 'Guven, veri ve operasyonel guvenlik yaklasimimiz.'],
            ['Gizlilik Politikasi', 'gizlilik-politikasi', $tr, 'tr', 'Gizlilik Politikasi', 'Ziyaretci ve iletisim verilerine dair gizlilik pratikleri.'],
            ['KVKK', 'kvkk', $tr, 'tr', 'KVKK ve GDPR yaklasimi', 'Kisisel veri koruma, onay ve kurumsal gizlilik yaklasimi.'],
            ['Cerez Politikasi', 'cerez-politikasi', $tr, 'tr', 'Cerez Politikasi', 'Cerez kullanimi ve takip tercihleri.'],
            ['Kullanim Sartlari', 'kullanim-sartlari', $tr, 'tr', 'Kullanim Sartlari', 'Web sitesi ve servis kullanim sartlari.'],
        ];

        foreach ($pages as $page) {
            [$title, $slug, $parent, $language, $hero_title, $hero_subtitle] = $page;
            $template = $page[6] ?? '';
            $this->upsert_page($title, $slug, $this->starter_content('generic'), [
                '_myliba_language' => $language,
                '_myliba_hero_title' => $hero_title,
                '_myliba_hero_subtitle' => $hero_subtitle,
                '_myliba_seo_title' => $title . ' | Myliba',
                '_myliba_seo_description' => $hero_subtitle,
            ], $parent, $template);
        }

        $this->seed_navigation();

        $this->upsert_post('A practical OKR operating rhythm', 'practical-okr-operating-rhythm', 'en');
        $this->upsert_post('OKR rutinini pratik hale getirmek', 'okr-rutinini-pratik-hale-getirmek', 'tr');
        $this->seed_categories();
        $this->seed_products();
        $this->seed_solutions();
        $this->seed_academy();
        $this->seed_trust_content();
        $this->seed_landing_pages();
        $this->upsert_event('Myliba Culture Session', 'myliba-culture-session', 'en', '+30 days', 'Online');
        $this->upsert_event('Myliba Kultur Oturumu', 'myliba-kultur-oturumu', 'tr', '+45 days', 'Online');
        $this->upsert_team('Strategy Lead', 'OKR and culture strategy', 10);
        $this->upsert_logo('Example Client', 10);

        \WP_CLI::success('Myliba WordPress structure seeded. Indexing is disabled for staging.');
    }

    /**
     * Import basic content from a running Strapi API.
     *
     * ## OPTIONS
     *
     * --url=<url>
     * : Strapi base URL, for example http://host.docker.internal:1337.
     *
     * [--token=<token>]
     * : Optional Strapi API token.
     *
     * [--yes]
     * : Confirm the operation.
     *
     * @subcommand import-strapi
     */
    public function import_strapi(array $args, array $assoc_args): void
    {
        if (empty($assoc_args['url'])) {
            \WP_CLI::error('Missing --url.');
        }

        if (empty($assoc_args['yes'])) {
            \WP_CLI::confirm('Import reachable Strapi content into WordPress? Existing matching slugs will be updated.');
        }

        $base = rtrim((string) $assoc_args['url'], '/');
        $token = $assoc_args['token'] ?? '';

        $single_pages = [
            'homepage' => ['Myliba', 'en'],
            'our-products' => ['Our Products', 'our-products'],
            'okr-academy' => ['OKR Culture Academy', 'okr-culture-academy'],
            'culture-analysis' => ['Culture Analysis', 'culture-analysis'],
            'ethics-counsel' => ['Ethics Counsel', 'ethics-counsel'],
            'our-story' => ['Our Story', 'our-story'],
            'faq-page' => ['FAQ', 'faq'],
            'security-page' => ['Security', 'security'],
            'privacy-policy' => ['Privacy Policy', 'privacy-policy'],
        ];

        foreach ($single_pages as $endpoint => [$title, $slug]) {
            $payload = $this->fetch_strapi($base . '/api/' . $endpoint . '?populate=*', $token);
            $item = $payload['data']['attributes'] ?? null;

            if (!$item) {
                continue;
            }

            $page_title = $item['title'] ?? $item['heroTitle'] ?? $title;
            $content = $item['content'] ?? $this->starter_content('generic');

            $this->upsert_page($page_title, $slug, $content, [
                '_myliba_language' => $item['locale'] ?? 'en',
                '_myliba_hero_title' => $item['heroTitle'] ?? $page_title,
                '_myliba_hero_subtitle' => $item['heroSubtitle'] ?? '',
                '_myliba_seo_title' => $item['seoTitle'] ?? ($page_title . ' | Myliba'),
                '_myliba_seo_description' => $item['seoDescription'] ?? '',
            ]);

            \WP_CLI::log('Imported page: ' . $page_title);
        }

        $this->import_collection($base, $token, 'blog-posts', 'post');
        $this->import_collection($base, $token, 'events', 'myliba_event');
        $this->import_collection($base, $token, 'team-members', 'myliba_team');
        $this->import_collection($base, $token, 'client-logos', 'myliba_client_logo');

        \WP_CLI::success('Strapi import completed.');
    }

    /**
     * Import public content from the current myliba.com website.
     *
     * ## OPTIONS
     *
     * [--source=<url>]
     * : Public source URL. Defaults to https://myliba.com.
     *
     * [--yes]
     * : Confirm the operation.
     *
     * @subcommand import-current
     */
    public function import_current(array $args, array $assoc_args): void
    {
        $source = rtrim((string) ($assoc_args['source'] ?? 'https://myliba.com'), '/');

        if (empty($assoc_args['yes'])) {
            \WP_CLI::confirm('Import public Myliba content from ' . $source . '? Existing matching slugs will be updated.');
        }

        $home_en = $this->import_current_page($source, '/', 'Myliba', 'en', 'en');
        $home_tr = $this->import_current_page($source, '/tr/', 'Myliba TR', 'tr', 'tr');

        if ($home_en) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home_en);
        }

        $pages = [
            ['/our-products/', 'Our Products', 'our-products', 'en', $home_en],
            ['/okr-culture-academy/', 'OKR Culture Academy', 'okr-culture-academy', 'en', $home_en],
            ['/culture-analysis/', 'Culture Analysis', 'culture-analysis', 'en', $home_en],
            ['/ethics-counsel/', 'Ethics Counsel', 'ethics-counsel', 'en', $home_en],
            ['/our-story/', 'Our Story', 'our-story', 'en', $home_en],
            ['/faq/', 'FAQ', 'faq', 'en', $home_en],
            ['/security/', 'Security', 'security', 'en', $home_en],
            ['/privacy-policy/', 'Privacy Policy', 'privacy-policy', 'en', $home_en],
            ['/tr/urunlerimiz/', 'Urunlerimiz', 'urunler', 'tr', $home_tr],
            ['/tr/okr-ve-kultur-akademisi/', 'OKR Kultur Akademisi', 'okr-kultur-akademisi', 'tr', $home_tr],
            ['/tr/kultur-analizi/', 'Kultur Analizi', 'kultur-analizi', 'tr', $home_tr],
            ['/tr/etik-hat/', 'Etik Hat', 'etik-danismanlik', 'tr', $home_tr],
            ['/tr/hikayemiz/', 'Hikayemiz', 'hikayemiz', 'tr', $home_tr],
            ['/tr/sss/', 'SSS', 'sss', 'tr', $home_tr],
            ['/tr/guvenlik/', 'Guvenlik', 'guvenlik', 'tr', $home_tr],
            ['/tr/kvkk-aydinlatma-metni/', 'KVKK Aydinlatma Metni', 'kvkk-aydinlatma-metni', 'tr', $home_tr],
        ];

        foreach ($pages as [$path, $fallback_title, $slug, $language, $parent]) {
            $this->import_current_page($source, $path, $fallback_title, $slug, $language, (int) $parent);
        }

        $home_html_en = $this->fetch_public_html($source . '/');
        $home_html_tr = $this->fetch_public_html($source . '/tr/');

        if ($home_html_en !== '') {
            $this->import_current_home_meta($home_en, $home_html_en, 'en', $source . '/');
            $this->import_current_team($home_html_en, 'en', $source);
            $this->import_current_client_logos($home_html_en, $source);
        }

        if ($home_html_tr !== '') {
            $this->import_current_home_meta($home_tr, $home_html_tr, 'tr', $source . '/tr/');
            $this->import_current_team($home_html_tr, 'tr', $source);
        }

        $products_html = $this->fetch_public_html($source . '/our-products/');
        if ($products_html !== '') {
            $this->import_current_products($products_html, 'en', $source . '/our-products/');
        }

        $academy_html = $this->fetch_public_html($source . '/okr-culture-academy/');
        if ($academy_html !== '') {
            $this->import_current_academy($academy_html, 'en', $source . '/okr-culture-academy/');
        }

        $this->import_current_rest_posts($source);

        \WP_CLI::success('Public Myliba import completed.');
    }

    private function import_collection(string $base, string $token, string $endpoint, string $post_type): void
    {
        $payload = $this->fetch_strapi($base . '/api/' . $endpoint . '?populate=*&pagination[pageSize]=100', $token);
        $items = $payload['data'] ?? [];

        foreach ($items as $item) {
            $attrs = $item['attributes'] ?? [];
            $title = $attrs['title'] ?? $attrs['name'] ?? 'Untitled';
            $slug = $attrs['slug'] ?? sanitize_title($title);
            $content = $attrs['content'] ?? $attrs['description'] ?? $attrs['bio'] ?? '';

            $post_id = $this->upsert_post_type($post_type, $title, $slug, $content, [
                '_myliba_language' => $attrs['locale'] ?? 'en',
                '_myliba_seo_title' => $attrs['seoTitle'] ?? '',
                '_myliba_seo_description' => $attrs['seoDescription'] ?? ($attrs['excerpt'] ?? ''),
            ]);

            if ($post_type === 'myliba_event') {
                update_post_meta($post_id, '_myliba_event_date', $attrs['date'] ?? '');
                update_post_meta($post_id, '_myliba_event_location', $attrs['location'] ?? '');
            }

            if ($post_type === 'myliba_team') {
                update_post_meta($post_id, '_myliba_person_role', $attrs['title'] ?? '');
                update_post_meta($post_id, '_myliba_order', (string) intval($attrs['order'] ?? 0));
            }

            if ($post_type === 'myliba_client_logo') {
                update_post_meta($post_id, '_myliba_order', (string) intval($attrs['order'] ?? 0));
            }

            \WP_CLI::log('Imported ' . $post_type . ': ' . $title);
        }
    }

    private function fetch_strapi(string $url, string $token): array
    {
        $args = [
            'timeout' => 20,
            'headers' => [],
        ];

        if ($token !== '') {
            $args['headers']['Authorization'] = 'Bearer ' . $token;
        }

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            \WP_CLI::warning($url . ' failed: ' . $response->get_error_message());
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    private function import_current_page(string $source, string $path, string $fallback_title, string $slug, string $language, int $parent = 0): int
    {
        $url = $source . $path;
        $html = $this->fetch_public_html($url);

        if ($html === '') {
            return 0;
        }

        $title = in_array($slug, ['en', 'tr'], true) ? $fallback_title : $this->extract_page_title($html, $fallback_title);
        $content = $this->extract_readable_html($html);
        $excerpt = $this->extract_excerpt($html);
        $post_id = $this->upsert_page($title, $slug, $content, [
            '_myliba_language' => $language,
            '_myliba_translation_key' => $slug,
            '_myliba_hero_title' => $title,
            '_myliba_hero_subtitle' => $excerpt,
            '_myliba_source_url' => $url,
            '_myliba_seo_title' => $title . ' | Myliba',
            '_myliba_seo_description' => $excerpt,
        ], $parent);

        $content = $this->replace_remote_images($content, $source, $post_id, true);
        wp_update_post([
            'ID' => $post_id,
            'post_content' => $content,
        ]);

        \WP_CLI::log('Imported public page: ' . $title);

        return $post_id;
    }

    private function import_current_home_meta(int $post_id, string $html, string $language, string $source_url): void
    {
        if (!$post_id) {
            return;
        }

        $lines = $this->extract_text_lines($html);
        $is_tr = $language === 'tr';
        $hero_title = $is_tr
            ? $this->join_existing_lines($lines, ['Yuksek', 'Performans', 'Kulturu Yaratiyoruz!'], 3)
            : $this->join_existing_lines($lines, ['We Create', 'a High Performance', 'Culture!'], 3);

        if ($hero_title === '') {
            $hero_title = get_post_meta($post_id, '_myliba_hero_title', true) ?: get_the_title($post_id);
        }

        if (in_array($hero_title, ['Myliba', 'Myliba TR'], true)) {
            $hero_title = $is_tr ? 'Yüksek Performans Kültürü Yaratıyoruz!' : 'We Create a High Performance Culture!';
        }

        $hero_subtitle = $this->line_after_sequence($lines, $is_tr ? 'Kulturu Yaratiyoruz!' : 'Culture!');
        if ($hero_subtitle === '') {
            $hero_subtitle = $is_tr
                ? 'Stratejiyi Eyleme Dönüştürün: Stratejiyi hedeflere, hedefleri aksiyonlara dönüştürerek, katkıyı şeffaflaştırın. İstekli, bağlı ve yetkin bir ekip geliştirerek en önemli olana odaklanın.'
                : 'Turn Strategy into Action: Make the contribution transparent by turning strategy into goals and goals into actions. Focus on what matters most by developing a motivated, committed and competent team.';
        }
        $proof = $is_tr ? "OKR ve Kultur Yazilimi\nOKR ve Kultur Akademisi" : "OKR and Culture Software\nOKR and Culture Academy";

        $meta = array_merge($this->home_meta_defaults($language), [
            '_myliba_hero_title' => $hero_title,
            '_myliba_hero_subtitle' => $hero_subtitle,
            '_myliba_home_hero_rotating_titles' => $hero_title . "\n" . ($is_tr ? 'Stratejiyi eyleme indiren performans platformu' : 'The performance platform that turns strategy into action'),
            '_myliba_home_hero_proof' => $proof,
            '_myliba_source_url' => $source_url,
        ]);

        $this->save_meta($post_id, $meta);
    }

    private function import_current_products(string $html, string $language, string $source_url): void
    {
        $lines = $this->extract_text_lines($html);
        $titles = ['Goals', 'Conversations', '1:1s', 'Feedback and Feedforward', 'Manager Effectiveness', 'Calibration'];
        $fallbacks = [
            'Goals' => 'Align all teams according to your most important strategic priorities. Increase collaboration through transparent goal setting and progress sharing.',
            'Conversations' => 'Create a culture of continuous learning and development through structured check-ins for goal setting, growth-focused coaching, dialogue and performance reviews.',
            '1:1s' => 'Boost collaboration, alignment, and productivity by facilitating 1:1 meetings between your people.',
            'Feedback and Feedforward' => 'Create an environment where employees feel goal-oriented, prepared to achieve success with real-time feedback and feed-forward, and inspired to consistently do their best work.',
            'Manager Effectiveness' => 'Transform your managers into superstar coaches with Myliba.',
            'Calibration' => 'Make employee decisions fairly by identifying talent development opportunities without bias by providing data-driven insights.',
        ];

        foreach ($titles as $index => $title) {
            $excerpt = $this->extract_snippet_after($lines, $title, $titles);
            if ($excerpt === '' && isset($fallbacks[$title])) {
                $excerpt = $fallbacks[$title];
            }
            if ($excerpt === '') {
                continue;
            }

            $post_id = $this->upsert_post_type('myliba_product', $title, sanitize_title($title), '<p>' . esc_html($excerpt) . '</p>', [
                '_myliba_language' => $language,
                '_myliba_label' => 'Product module',
                '_myliba_hero_title' => $title,
                '_myliba_hero_subtitle' => $excerpt,
                '_myliba_solution' => $excerpt,
                '_myliba_cta_label' => 'Request a demo',
                '_myliba_cta_url' => '/en/demo/',
                '_myliba_order' => (string) (($index + 1) * 10),
                '_myliba_source_url' => $source_url . '#' . sanitize_title($title),
                '_myliba_seo_title' => $title . ' | Myliba',
                '_myliba_seo_description' => $excerpt,
            ]);

            $image = $this->find_image_by_alt($html, $title, $source_url);
            if ($image !== '') {
                $attachment_id = $this->sideload_image($image, $post_id);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }

            \WP_CLI::log('Imported public product: ' . $title);
        }
    }

    private function import_current_academy(string $html, string $language, string $source_url): void
    {
        $lines = $this->extract_text_lines($html);
        $titles = ['Goal Coaching', 'Performance Coaching', 'Culture Coaching'];
        $fallbacks = [
            'Goal Coaching' => 'Gains basic skills to coach other leaders in performance management, along with experience in designing and implementing next-generation performance development tools.',
            'Performance Coaching' => 'Coaches acquire essential skills to coach other leaders in performance management and design next-generation performance development tools.',
            'Culture Coaching' => 'Learns to develop and manage a culture of meaningful and continuous dialogue, feedback, feedforward, appreciation and recognition based on corporate values.',
        ];

        foreach ($titles as $index => $title) {
            $excerpt = $this->extract_snippet_after($lines, $title, $titles);
            if ($excerpt === '' && isset($fallbacks[$title])) {
                $excerpt = $fallbacks[$title];
            }
            if ($excerpt === '') {
                continue;
            }

            $this->upsert_post_type('myliba_academy', $title, sanitize_title($title), '<p>' . esc_html($excerpt) . '</p>', [
                '_myliba_language' => $language,
                '_myliba_label' => 'Academy program',
                '_myliba_hero_title' => $title,
                '_myliba_hero_subtitle' => $excerpt,
                '_myliba_solution' => $excerpt,
                '_myliba_order' => (string) (($index + 1) * 10),
                '_myliba_source_url' => $source_url . '#' . sanitize_title($title),
            ]);

            \WP_CLI::log('Imported public academy program: ' . $title);
        }
    }

    private function import_current_team(string $html, string $language, string $source): void
    {
        $lines = $this->extract_text_lines($html);
        $names = ['Dilek Mete', 'Aysel Eker', 'Huri Sankur'];
        $fallbacks = [
            'en' => [
                'Dilek Mete' => 'Managing Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach',
                'Aysel Eker' => 'Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach',
                'Huri Sankur' => 'People and Culture Advisor, Academy Coordinator, OKR Coach',
            ],
            'tr' => [
                'Dilek Mete' => 'Yonetici Ortak, Kulturel Donusum Danismani, Yonetici Kocu, PCC, OKR Kocu',
                'Aysel Eker' => 'Ortak, Kulturel Donusum Danismani, Yonetici Kocu, PCC, OKR Kocu',
                'Huri Sankur' => 'Insan ve Kultur Danismani, Akademi Koordinatoru, OKR Kocu',
            ],
        ];

        foreach ($names as $index => $name) {
            $role = $this->extract_snippet_after($lines, $name, $names, 4);
            if ($role === '') {
                $role = $fallbacks[$language][$name] ?? '';
            }
            if ($role === '') {
                continue;
            }

            $post_id = $this->upsert_post_type('myliba_team', $name, sanitize_title($name . '-' . $language), '<p>' . esc_html($role) . '</p>', [
                '_myliba_language' => $language,
                '_myliba_person_role' => $role,
                '_myliba_order' => (string) (($index + 1) * 10),
            ]);

            $image = $this->find_image_by_alt($html, $name, $source);
            if ($image !== '') {
                $attachment_id = $this->sideload_image($image, $post_id);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
        }
    }

    private function import_current_client_logos(string $html, string $source): void
    {
        $images = $this->extract_images($html, $source);
        $excluded = ['slider', 'mobil', 'myliba', 'basliksiz', 'strateji', 'seffaf', 'ikon', 'not-stresi', 'hiyerarsi', 'rekabet', 'veri', 'dilek', 'aysel', 'huri'];
        $order = 10;

        foreach ($images as $image) {
            $label = $image['alt'] !== '' ? $image['alt'] : basename((string) wp_parse_url($image['src'], PHP_URL_PATH));
            $key = strtolower(sanitize_title($label . ' ' . basename((string) wp_parse_url($image['src'], PHP_URL_PATH))));
            $skip = false;

            foreach ($excluded as $needle) {
                if (str_contains($key, $needle)) {
                    $skip = true;
                    break;
                }
            }

            if ($skip || $label === '') {
                continue;
            }

            $post_id = $this->upsert_logo($label, $order);
            update_post_meta($post_id, '_myliba_source_url', $image['src']);
            $attachment_id = $this->sideload_image($image['src'], $post_id);
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
                $order += 10;
            }

            if ($order > 240) {
                break;
            }
        }
    }

    private function import_current_rest_posts(string $source): void
    {
        $payload = $this->fetch_public_json($source . '/wp-json/wp/v2/posts?per_page=100&_embed=1');
        $posts = $payload ?: [];

        if (!$posts) {
            \WP_CLI::warning('Public posts REST endpoint returned no posts.');
            return;
        }

        foreach ($posts as $item) {
            if (!is_array($item)) {
                continue;
            }

            $title = wp_strip_all_tags($item['title']['rendered'] ?? 'Untitled');
            $slug = sanitize_title((string) ($item['slug'] ?? $title));
            $content = wp_kses_post($item['content']['rendered'] ?? '');
            $excerpt = wp_strip_all_tags($item['excerpt']['rendered'] ?? '');
            $post_id = $this->upsert_post_type('post', $title, $slug, $content, [
                '_myliba_language' => str_starts_with((string) ($item['link'] ?? ''), $source . '/tr/') ? 'tr' : 'en',
                '_myliba_hero_title' => $title,
                '_myliba_source_url' => (string) ($item['link'] ?? ''),
                '_myliba_seo_title' => $title . ' | Myliba',
                '_myliba_seo_description' => $excerpt,
            ]);

            $content = $this->replace_remote_images($content, $source, $post_id, true);
            wp_update_post([
                'ID' => $post_id,
                'post_content' => $content,
            ]);
        }
    }

    private function fetch_public_html(string $url): string
    {
        $response = wp_remote_get($url, [
            'timeout' => 25,
            'redirection' => 5,
            'headers' => [
                'User-Agent' => 'Myliba WordPress Importer',
            ],
        ]);

        if (is_wp_error($response)) {
            \WP_CLI::warning($url . ' failed: ' . $response->get_error_message());
            return '';
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            \WP_CLI::warning($url . ' returned HTTP ' . $code);
            return '';
        }

        return (string) wp_remote_retrieve_body($response);
    }

    private function fetch_public_json(string $url): array
    {
        $body = $this->fetch_public_html($url);
        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    private function extract_page_title(string $html, string $fallback): string
    {
        $dom = $this->dom_from_html($html);
        $headings = $dom ? (new \DOMXPath($dom))->query('//h1') : null;

        if ($headings && $headings->length > 0) {
            $title = trim($headings->item(0)?->textContent ?? '');
            if ($title !== '') {
                return $title;
            }
        }

        return $fallback;
    }

    private function extract_excerpt(string $html): string
    {
        $lines = $this->extract_text_lines($html);
        foreach ($lines as $line) {
            if (strlen($line) > 80 && !str_contains(strtolower($line), 'cookie')) {
                return wp_trim_words($line, 28);
            }
        }

        return '';
    }

    private function extract_readable_html(string $html): string
    {
        $dom = $this->dom_from_html($html);
        if (!$dom) {
            return '';
        }

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//main');
        $node = ($nodes && $nodes->length > 0) ? $nodes->item(0) : $dom->getElementsByTagName('body')->item(0);
        if (!$node) {
            return '';
        }

        $content = '';
        foreach ($node->childNodes as $child) {
            $content .= $dom->saveHTML($child);
        }

        $content = preg_replace('/<(script|style|noscript|form)\b[^>]*>.*?<\/\1>/is', '', $content) ?? $content;

        return wp_kses_post($content);
    }

    private function extract_text_lines(string $html): array
    {
        $dom = $this->dom_from_html($html);
        if (!$dom) {
            return [];
        }

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6|//p|//li|//a|//span');
        $lines = [];

        if (!$nodes) {
            return [];
        }

        foreach ($nodes as $node) {
            $text = trim(preg_replace('/\s+/', ' ', $node->textContent ?? '') ?? '');
            if ($text === '' || in_array($text, $lines, true)) {
                continue;
            }
            $lines[] = $text;
        }

        return $lines;
    }

    private function extract_snippet_after(array $lines, string $title, array $stop_titles, int $max_lines = 2): string
    {
        $start = array_search($title, $lines, true);
        if ($start === false) {
            return '';
        }

        $parts = [];
        for ($index = $start + 1; $index < count($lines); $index++) {
            $line = $lines[$index];
            if (in_array($line, ['Request a Demo', 'Contact Us', 'GET AN OFFER!', 'Teklif Al', 'Demo Iste'], true)) {
                if ($parts) {
                    break;
                }
                continue;
            }
            if (in_array($line, $stop_titles, true)) {
                break;
            }
            if (strlen($line) < 8) {
                continue;
            }
            $parts[] = $line;
            if (count($parts) >= $max_lines) {
                break;
            }
        }

        return trim(implode(' ', $parts));
    }

    private function join_existing_lines(array $lines, array $candidates, int $limit): string
    {
        $matches = [];
        foreach ($candidates as $candidate) {
            foreach ($lines as $line) {
                if (sanitize_title($line) === sanitize_title($candidate)) {
                    $matches[] = $line;
                    break;
                }
            }
        }

        return implode(' ', array_slice($matches, 0, $limit));
    }

    private function line_after_sequence(array $lines, string $needle): string
    {
        foreach ($lines as $index => $line) {
            if (sanitize_title($line) === sanitize_title($needle) && !empty($lines[$index + 1])) {
                return $lines[$index + 1];
            }
        }

        return '';
    }

    private function extract_images(string $html, string $source): array
    {
        $dom = $this->dom_from_html($html);
        if (!$dom) {
            return [];
        }

        $images = [];
        foreach ($dom->getElementsByTagName('img') as $image) {
            $src = $image->getAttribute('src') ?: $image->getAttribute('data-src');
            if ($src === '') {
                continue;
            }
            $images[] = [
                'src' => $this->absolute_url($src, $source),
                'alt' => trim($image->getAttribute('alt')),
            ];
        }

        return $images;
    }

    private function find_image_by_alt(string $html, string $needle, string $source = 'https://myliba.com'): string
    {
        foreach ($this->extract_images($html, $source) as $image) {
            if ($image['alt'] !== '' && str_contains(sanitize_title($image['alt']), sanitize_title($needle))) {
                return $image['src'];
            }
        }

        return '';
    }

    private function replace_remote_images(string $content, string $source, int $post_id, bool $set_featured = false): string
    {
        if (!preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
            return $content;
        }

        foreach (array_unique($matches[1]) as $src) {
            $absolute = $this->absolute_url($src, $source);
            $attachment_id = $this->sideload_image($absolute, $post_id);
            if (!$attachment_id) {
                continue;
            }
            if ($set_featured && !has_post_thumbnail($post_id)) {
                set_post_thumbnail($post_id, $attachment_id);
            }
            $local_url = wp_get_attachment_url($attachment_id);
            if ($local_url) {
                $content = str_replace($src, $local_url, $content);
            }
        }

        return $content;
    }

    private function sideload_image(string $url, int $post_id): int
    {
        $url = esc_url_raw($url);
        if ($url === '') {
            return 0;
        }

        $existing = get_posts([
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'fields' => 'ids',
            'numberposts' => 1,
            'meta_key' => '_myliba_source_url',
            'meta_value' => $url,
        ]);

        if ($existing) {
            return (int) $existing[0];
        }

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_sideload_image($url, $post_id, null, 'id');
        if (is_wp_error($attachment_id)) {
            \WP_CLI::warning('Image import failed: ' . $url . ' - ' . $attachment_id->get_error_message());
            return 0;
        }

        update_post_meta((int) $attachment_id, '_myliba_source_url', $url);

        return (int) $attachment_id;
    }

    private function absolute_url(string $url, string $source): string
    {
        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            $parts = wp_parse_url($source);
            $scheme = is_array($parts) && !empty($parts['scheme']) ? $parts['scheme'] : 'https';
            $host = is_array($parts) && !empty($parts['host']) ? $parts['host'] : 'myliba.com';

            return $scheme . '://' . $host . $url;
        }

        return rtrim($source, '/') . '/' . ltrim($url, '/');
    }

    private function dom_from_html(string $html): ?\DOMDocument
    {
        if ($html === '') {
            return null;
        }

        $dom = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $loaded ? $dom : null;
    }

    private function cleanup_default_content(): void
    {
        foreach (['sample-page', 'privacy-policy'] as $slug) {
            $page = get_page_by_path($slug);
            if ($page && (int) $page->post_parent === 0) {
                wp_delete_post($page->ID, true);
            }
        }

        $hello = get_page_by_path('hello-world', OBJECT, 'post');
        if ($hello) {
            wp_delete_post($hello->ID, true);
        }
    }

    private function seed_categories(): void
    {
        $categories = [
            'OKR Guide',
            'Performance Management',
            '1:1 Meetings',
            'Feedback Culture',
            'Leadership and Coaching',
            'KPI vs OKR',
            'People and Culture Analytics',
            'Strategy Management',
            'Cultural Transformation',
        ];

        foreach ($categories as $category) {
            if (!term_exists($category, 'category')) {
                wp_insert_term($category, 'category');
            }
        }
    }

    private function seed_products(): void
    {
        $items = [
            ['OKR Management', 'okr-management', 'Declare aligned goals, connect ownership and track progress from strategy to action.', 'Goal ownership stays unclear.', 'Myliba connects company, team and individual OKRs with visible contribution.', ['Clear ownership', 'Aligned progress', 'Strategy execution visibility'], ['OKR', 'Actions', 'Analytics'], 10],
            ['KPI Tracking', 'kpi-tracking', 'Track business metrics without losing the strategic context behind them.', 'Teams track numbers without clear priority.', 'Myliba combines KPI health with OKR and action context.', ['Metric clarity', 'Faster reviews', 'Better decisions'], ['KPI', 'OKR', 'Analytics'], 20],
            ['CFR', 'cfr', 'Keep conversation, feedback and recognition connected to performance goals.', 'Feedback becomes occasional and disconnected.', 'Myliba turns CFR into a continuous performance routine.', ['Continuous feedback', 'Recognition rhythm', 'Coaching notes'], ['CFR', 'Feedback', '1:1'], 30],
            ['Action Management', 'action-management', 'Turn goals into visible actions, owners and follow-up routines.', 'Priorities do not become accountable action.', 'Myliba connects every action to goals, owners and follow-up.', ['Accountability', 'Execution clarity', 'Risk visibility'], ['Actions', 'OKR', 'KPI'], 40],
            ['1:1 Meetings', '1-1-meetings', 'Make one-on-one meetings structured, useful and connected to development.', '1:1 meetings become unstructured conversations.', 'Myliba connects agenda, notes, feedback and next actions.', ['Better preparation', 'Development focus', 'Manager rhythm'], ['1:1', 'Feedback', 'Coaching'], 50],
            ['Feedback and Feedforward', 'feedback-feedforward', 'Create a safe feedback culture with actionable next steps.', 'Feedback does not turn into behavior change.', 'Myliba links feedback with feedforward, recognition and actions.', ['Behavior change', 'Psychological safety', 'Actionable notes'], ['Feedback', 'CFR', 'Development'], 60],
            ['Performance Development', 'performance-development', 'Replace annual stress with continuous performance development.', 'Performance reviews become heavy and subjective.', 'Myliba makes development continuous, transparent and evidence-based.', ['Continuous improvement', 'Fairer reviews', 'Better coaching'], ['Performance', '1:1', 'Feedback'], 70],
            ['Leadership and Coaching', 'leadership-coaching', 'Support leaders with routines that create clarity and accountability.', 'Leaders need practical routines, not more reports.', 'Myliba supports coaching, alignment and follow-up habits.', ['Leader consistency', 'Team clarity', 'Better coaching'], ['Leadership', 'Academy', 'CFR'], 80],
            ['Academy Programs', 'academy-programs', 'Strengthen adoption with workshops, coaching and culture programs.', 'Software adoption fails without behavior change.', 'Myliba combines product usage with academy and transformation support.', ['Adoption', 'Capability building', 'Cultural change'], ['Academy', 'OKR', 'Leadership'], 90],
            ['Insights and Analytics', 'insights-analytics', 'See performance culture signals, risks and progress in one view.', 'Leadership cannot see risks until it is late.', 'Myliba surfaces progress, contribution, feedback and action signals.', ['Real-time insight', 'Risk detection', 'Better decisions'], ['Analytics', 'KPI', 'OKR'], 100],
        ];

        foreach ($items as [$title, $slug, $excerpt, $problem, $solution, $benefits, $modules, $order]) {
            $this->upsert_post_type('myliba_product', $title, $slug, '<p>' . esc_html($excerpt) . '</p>', [
                '_myliba_language' => 'en',
                '_myliba_label' => 'Product module',
                '_myliba_hero_title' => $title,
                '_myliba_hero_subtitle' => $excerpt,
                '_myliba_problem' => $problem,
                '_myliba_solution' => $solution,
                '_myliba_benefits' => implode("\n", $benefits),
                '_myliba_related_modules' => implode("\n", $modules),
                '_myliba_faq_items' => $title . ' kimler icin uygundur? | Hedef, performans ve kultur rutinlerini olculebilir hale getirmek isteyen ekipler icin uygundur.',
                '_myliba_cta_label' => 'Request a demo',
                '_myliba_cta_url' => '/en/demo/',
                '_myliba_order' => (string) $order,
                '_myliba_seo_title' => $title . ' | Myliba',
                '_myliba_seo_description' => $excerpt,
            ]);
        }
    }

    private function seed_solutions(): void
    {
        $items = [
            ['For Executives', 'executives', 'Turn strategy into transparent goals, actions and progress signals.', 'Executives cannot see strategy execution in real time.', 'Myliba shows company goals, contribution, risks and progress in one operating view.', 10],
            ['For Human Resources', 'human-resources', 'Build continuous performance, feedback and development routines.', 'HR needs a fair, continuous and measurable performance system.', 'Myliba connects 1:1, feedback, development and academy adoption.', 20],
            ['For Strategy Office', 'strategy-office', 'Connect strategic priorities with measurable OKRs, KPIs and actions.', 'Strategy teams struggle to connect priorities with daily execution.', 'Myliba links OKRs, KPIs, actions and progress reporting.', 30],
            ['For Team Leaders', 'team-leaders', 'Run better 1:1 meetings, follow up actions and coach progress.', 'Leaders need practical routines that create clarity.', 'Myliba gives leaders agenda, feedback, action and progress visibility.', 40],
            ['For Employees', 'employees', 'See goals, contribution, feedback and growth path clearly.', 'Employees need clarity on contribution and development.', 'Myliba makes ownership, feedback and recognition visible.', 50],
        ];

        foreach ($items as [$title, $slug, $excerpt, $problem, $solution, $order]) {
            $this->upsert_post_type('myliba_solution', $title, $slug, '<p>' . esc_html($excerpt) . '</p>', [
                '_myliba_language' => 'en',
                '_myliba_label' => 'Use case',
                '_myliba_hero_title' => $title,
                '_myliba_hero_subtitle' => $excerpt,
                '_myliba_problem' => $problem,
                '_myliba_solution' => $solution,
                '_myliba_benefits' => "Role clarity\nFaster decisions\nMeasurable routines",
                '_myliba_related_modules' => "OKR\nKPI\nCFR\n1:1\nAnalytics",
                '_myliba_cta_label' => 'Request a demo',
                '_myliba_cta_url' => '/en/demo/',
                '_myliba_order' => (string) $order,
            ]);
        }
    }

    private function seed_academy(): void
    {
        $items = [
            ['OKR Culture Program', 'okr-culture-program', 'Workshops and coaching that help teams adopt OKR discipline.', 10],
            ['Leadership Development Program', 'leadership-development-program', 'Programs that help leaders coach, align and follow up better.', 20],
            ['Performance Coaching Program', 'performance-coaching-program', 'Practical routines for feedback, 1:1 and continuous development.', 30],
        ];

        foreach ($items as [$title, $slug, $excerpt, $order]) {
            $this->upsert_post_type('myliba_academy', $title, $slug, '<p>' . esc_html($excerpt) . '</p>', [
                '_myliba_language' => 'en',
                '_myliba_label' => 'Academy program',
                '_myliba_hero_title' => $title,
                '_myliba_hero_subtitle' => $excerpt,
                '_myliba_problem' => 'Software alone does not create sustainable behavior change.',
                '_myliba_solution' => 'Myliba academy programs combine training, coaching and implementation routines.',
                '_myliba_benefits' => "Adoption support\nLeader capability\nSustainable routines",
                '_myliba_related_modules' => "Academy\nLeadership\nOKR\nPerformance",
                '_myliba_order' => (string) $order,
            ]);
        }
    }

    private function seed_trust_content(): void
    {
        $this->upsert_post_type('myliba_testimonial', 'People and Culture Leader', 'people-culture-leader', 'Myliba helped us connect strategy, feedback and performance routines in a way teams can actually use.', [
            '_myliba_language' => 'en',
            '_myliba_person_role' => 'HR Director',
            '_myliba_company' => 'B2B organization',
            '_myliba_order' => '10',
        ]);

        $faqs = [
            ['How is Myliba different from a classic OKR tool?', 'Myliba combines software, academy and consulting routines around OKR, KPI, CFR, 1:1 and performance development.', 10],
            ['Can Myliba support both software and training needs?', 'Yes. The platform supports operating routines while academy programs help teams adopt the behavior change.', 20],
            ['Is demo data stored securely?', 'Form submissions are stored in WordPress admin and can be routed to SMTP or future CRM integrations.', 30],
            ['Can the site support multilingual SEO?', 'Yes. The custom content model is Polylang/WPML ready and exposes custom post types for translation.', 40],
        ];

        foreach ($faqs as [$question, $answer, $order]) {
            $this->upsert_post_type('myliba_faq', $question, sanitize_title($question), '<p>' . esc_html($answer) . '</p>', [
                '_myliba_language' => 'en',
                '_myliba_label' => 'Homepage',
                '_myliba_order' => (string) $order,
            ]);
        }
    }

    private function seed_landing_pages(): void
    {
        $pages = [
            ['OKR Software', 'okr-yazilimi', 'OKR software that connects strategy, goals, actions and feedback.', 'OKR yazilimi arayan kurumlar hedefleri aksiyona indirmekte zorlanir.', 'Myliba OKR, KPI, aksiyon, CFR ve analitik akislarini tek platformda birlestirir.'],
            ['OKR Management', 'okr-yonetimi', 'Manage OKRs with ownership, alignment and progress visibility.', 'OKR yonetimi sadece hedef yazmakla sinirli kaldiginda sonuc uretmez.', 'Myliba hedefleri sahiplik, aksiyon ve takip ritmine baglar.'],
            ['Performance Management', 'performans-yonetimi', 'Continuous performance management for modern teams.', 'Yillik performans gorusmeleri agir, gec ve subjektif kalir.', 'Myliba 1:1, geri bildirim ve gelisim notlarini surekli performans ritmine donusturur.'],
            ['Feedback Culture', 'geri-bildirim-kulturu', 'Create a feedback culture that turns insight into action.', 'Geri bildirim aksiyona donmediginde kultur degisimi yaratmaz.', 'Myliba feedback ve feedforward notlarini hedefler ve aksiyonlarla baglar.'],
            ['KPI and OKR', 'kpi-ve-okr', 'Use KPI and OKR together without losing strategic focus.', 'KPI ve OKR ayrik yonetildiginde ekipler onceligi kacirir.', 'Myliba metrikleri stratejik hedeflerle ve aksiyonlarla birlestirir.'],
        ];

        foreach ($pages as [$title, $slug, $subtitle, $problem, $solution]) {
            $this->upsert_page($title, $slug, '<p>' . esc_html($subtitle) . '</p>', [
                '_myliba_language' => 'tr',
                '_myliba_label' => 'SEO landing page',
                '_myliba_hero_title' => $title,
                '_myliba_hero_subtitle' => $subtitle,
                '_myliba_problem' => $problem,
                '_myliba_solution' => $solution,
                '_myliba_benefits' => "Temiz hedef akisi\nDaha iyi takip\nDemo ile hizli degerlendirme",
                '_myliba_related_modules' => "OKR\nKPI\nAksiyon\nCFR\nAnalitik",
                '_myliba_faq_items' => $title . ' hangi kurumlar icin uygundur? | Hedef ve performans rutinlerini olculebilir hale getirmek isteyen kurumlar icin uygundur.',
                '_myliba_cta_label' => 'Demo iste',
                '_myliba_cta_url' => '/tr/demo/',
                '_myliba_seo_title' => $title . ' | Myliba',
                '_myliba_seo_description' => $subtitle,
            ], 0, 'template-landing.php');
        }
    }

    private function home_meta_defaults(string $language): array
    {
        if ($language === 'tr') {
            return [
                '_myliba_home_builder' => $this->home_builder_defaults(),
                '_myliba_home_hero_rotating_titles' => "Hedefleri net, kulturu guclu ekipler kurun\nStratejiyi eyleme indiren performans platformu\nOKR ve kultur rutinlerini tek ritimde yonetin",
                '_myliba_home_hero_proof' => "Stratejiden aksiyona\nSurekli performans\nAkademi + yazilim",
                '_myliba_home_dashboard_brand' => 'Myliba',
                '_myliba_home_dashboard_title' => 'Performance OS',
                '_myliba_home_dashboard_nav' => "OKR\nKPI\nCFR\n1:1\nAkademi",
                '_myliba_home_dashboard_objective_label' => 'Sirket hedefi',
                '_myliba_home_dashboard_objective_title' => 'Strateji uygulama gorunurlugunu artirin',
                '_myliba_home_dashboard_progress' => '76',
                '_myliba_home_dashboard_rows' => "Liderlik ritmi aktif | IK | Yolunda | green\nTakim OKR uyumu | Strateji | Incele | blue\n1:1 aksiyon takibi | Liderler | Odak | orange",
                '_myliba_home_dashboard_col_1' => 'Ana sonuc',
                '_myliba_home_dashboard_col_2' => 'Sahip',
                '_myliba_home_dashboard_col_3' => 'Durum',
                '_myliba_home_metric_1_value' => '72%',
                '_myliba_home_metric_1_label' => 'OKR ilerleme',
                '_myliba_home_metric_2_value' => '148',
                '_myliba_home_metric_2_label' => '1:1 notu',
                '_myliba_home_feedback_title' => 'Geri bildirim karti',
                '_myliba_home_feedback_text' => 'Kocluk notlari, takdir ve aksiyonlar hedeflerle bagli kalir.',
                '_myliba_home_trust_title' => 'Performans kulturunu ciddiyetle yoneten ekipler icin.',
                '_myliba_home_trust_items' => "OKR\nKPI\nCFR\n1:1",
                '_myliba_home_problem_eyebrow' => 'Problem',
                '_myliba_home_problem_title' => 'Hedefler hiyerarsisinde kayboluyor, geri bildirim donemsel kaliyor.',
                '_myliba_home_problem_text' => 'Performans yonetimi ancak hedefler, gorusmeler ve aksiyonlar ayni akista ilerlediginde olculebilir hale gelir.',
                '_myliba_home_problem_cards' => "Hedef hiyerarsisi kayboluyor | Sirket, takim ve bireysel hedefler tek bir katki haritasi olarak okunamiyor.\nPerformans gorusmeleri ayrik kaliyor | Check-in, geri bildirim ve degerlendirme notlari aktif hedeflerle baglanmiyor.\nGeri bildirim donemsel kaliyor | Takdir, kocluk ve feedforward aksiyonu degistirmek icin gec geliyor.\nStrateji aksiyona donusmuyor | Oncelikler sahip, tarih ve takip rutini olan aksiyonlara inemiyor.\nSeffaflik azaliyor | Liderlik, IK ve ekipler riskleri, engelleri ve ilerlemeyi tek gorunumde izleyemiyor.\nManuel operasyon zaman kaybettiriyor | Tablolar, hatirlatmalar ve durum takibi performans ritmini yavaslatiyor.",
                '_myliba_home_strategy_flow_eyebrow' => 'Performans ritmi',
                '_myliba_home_strategy_flow_title' => 'Strateji, hedef, aksiyon ve kultur tek akista.',
                '_myliba_home_strategy_flow_text' => 'Oncelik, sahiplik, aksiyon ve ogrenme rutinlerini birbirine baglayan tek bir calisma ritmi kurun.',
                '_myliba_home_strategy_flow_steps' => "Strateji | Oncelikleri kurum genelinde gorunur ve ortak hale getirin. | S\nHedef | OKR ve KPI sahipligini sirketten takimlara baglayin. | H\nAksiyon | Her onceligi sorumlu, tarih ve takip adimina donusturun. | A\nKultur | 1:1, CFR ve ogrenme rutinlerini isin etrafina yerlestirin. | K",
                '_myliba_home_solution_eyebrow' => 'Myliba cozumu',
                '_myliba_home_solution_title' => 'Hedefler, performans gorusmeleri, aksiyonlar ve kultur gelisimi icin tek platform.',
                '_myliba_home_products_button' => 'Urunleri incele',
                '_myliba_home_module_button' => 'Modulu gor',
                '_myliba_home_academy_eyebrow' => 'Akademi + yazilim',
                '_myliba_home_academy_title' => 'Yazilim gucu, akademi deneyimi.',
                '_myliba_home_academy_text' => 'Myliba kurumlarin sadece hedef tanimlamasina degil, liderlik gelisimi, performans koclugu, atolyeler ve kultur donusumu programlariyla hedef odakli calismayi surdurulebilir hale getirmesine yardimci olur.',
                '_myliba_home_academy_items' => "OKR kulturu ve adaptasyon programlari\nLiderlik ve kocluk rutinleri\nSurekli performans gelisimi\nInsan ve kultur odakli donusum",
                '_myliba_home_academy_button' => 'Akademiyi incele',
                '_myliba_home_stepper_eyebrow' => 'Baslamak kolay',
                '_myliba_home_stepper_title' => 'Myliba yazilimini kullanmak icin sunlari yapman yeterli.',
                '_myliba_home_stepper_text' => 'Kisa bir baslangic akisiyle ihtiyaci anlar, yolu netlestirir ve ilk performans ritmini birlikte baslatiriz.',
                '_myliba_home_stepper_steps' => "Demo talep et | Kisa formdan bize ulas; ekip yapini ve ana hedefini anlayalim.\nEkibini ve onceliklerini paylas | Hangi performans rutinlerini kurmak istedigini beraber netlestirelim.\nSana uygun yolu belirleyelim | Modul, akademi ve baslangic planini kurumuna gore eslestirelim.\nIlk ritmi baslat | OKR, aksiyon ve 1:1 rutinlerini kullanima acip takip etmeye basla.",
                '_myliba_home_stepper_cta_label' => 'Demo Talep Et',
                '_myliba_home_role_gains_eyebrow' => 'Rol bazli kazanim',
                '_myliba_home_role_gains_title' => 'Her rol icin net kazanim.',
                '_myliba_home_role_gains_text' => 'Ayni calisma sistemi icinde her paydasa ihtiyaci olan gorunum ve rutini verin.',
                '_myliba_home_role_gains_rows' => "CEO / Ust Yonetim | Stratejiyi canli bir operasyon gorunumuyle yonetin | Sirket oncelikleri, takim katkisi ve risk sinyallerini manuel rapor beklemeden gorun. | Stratejik gorunurluk | Hedefler, metrikler ve aksiyonlar tek ekranda baglanir. | Daha hizli karar | Liderlik dikkatini destek gereken noktalara yoneltir.\nInsan Kaynaklari | Performansi surekli ve adil hale getirin | 1:1, geri bildirim, gelisim notu ve degerlendirme kanitlarini yonetilebilir bir ritme tasiyin. | Surec netligi | IK, agir tablo takibi olmadan donguleri yurutur. | Calisan gelisimi | Gelisim sinyalleri hedef ve kocluga bagli kalir.\nStrateji Ofisi | Oncelikleri uygulamayla baglayin | Stratejik tercihleri ekiplerin takip edebilecegi OKR, KPI, inisiyatif ve sahipliklere cevirin. | Hizalanma haritasi | Her oncelik hedef, sahip ve aksiyona kadar izlenir. | Ilerleme ritmi | Review rutinleri olculebilir ve tekrarlanabilir kalir.\nTakim Liderleri | Takibi kaybetmeden kocluk yapin | 1:1 hazirligi, aksiyon takibi ve geri bildirimi takim hedefleriyle birlikte yonetin. | Lider ritmi | Toplantilar yapilandirilmis ve sonuca bagli hale gelir. | Takim odagi | Herkes neyin onemli oldugunu ve siradaki aksiyonu bilir.\nCalisanlar | Katkiyi ve gelisimi net gorun | Hedef, beklenti, geri bildirim ve gelisim aksiyonlarini tek yerde takip edin. | Rol netligi | Sirket onceliklerine katki gorunur hale gelir. | Daha iyi geri bildirim | Takdir ve feedforward aksiyona doner.",
                '_myliba_home_outcomes_eyebrow' => 'Kazanimlar',
                '_myliba_home_outcomes_title' => 'Olculebilir gelisim kulturu.',
                '_myliba_home_outcomes_text' => 'Her ritim daha iyi odak, kocluk ve karar icin olculebilir bir sinyal uretir.',
                '_myliba_home_outcomes_cards' => "Strateji ve ekip hizalanmasi | Sirket stratejisini takim ve bireysel katkiyla baglayin.\nSeffaf hedef ilerletme | Ilerleme, engel ve sahipligi toplanti beklemeden gorun.\nSurekli gelisim | 1:1, geri bildirim ve koclugu surekli rutine donusturun.\nDaha az manuel is | Tablolar, hatirlatmalar ve parca parca guncelleme takibini azaltin.\nYuksek calisan bagliligi | Anlam, gorunurluk ve gelisim hissini guclendirin.\nKanita dayali performans kultur | Degerlendirme ve kararlar icin hedef, aksiyon ve geri bildirim kanitlarini kullanin.",
                '_myliba_home_resources_eyebrow' => 'Kaynaklar',
                '_myliba_home_resources_title' => 'Performans kulturu icin icgoruler.',
                '_myliba_home_resources_text' => 'OKR, performans, liderlik ve kultur uzerine pratik yazilar.',
                '_myliba_home_resources_button' => 'Tum yazilar',
                '_myliba_home_faq_eyebrow' => 'Sik sorulanlar',
                '_myliba_home_faq_title' => 'Aklinizdaki ilk sorular.',
                '_myliba_home_faq_text' => 'Olculebilir performans ritmine baslamadan once ekiplerin sordugu temel sorular.',
                '_myliba_home_faq_items' => "Myliba hangi ihtiyac icin uygundur? | Hedef, performans ve kultur rutinlerini tek sistemde yonetmek isteyen kurumlar icin uygundur.\nOKR yonetimi nasil yapilir? | Sirket oncelikleri, takim hedefleri, aksiyonlar ve ilerleme sinyalleri tek akista takip edilir.\nMyliba sadece yazilim mi sunar? | Hayir. Yazilim akademi, workshop ve kocluk rutinleriyle desteklenir.\nDemo sureci nasil ilerler? | Ihtiyac ve ekip yapisi anlasilir, ardindan kurumunuza uygun akis gosterilir.",
                '_myliba_home_final_cta_eyebrow' => 'Aksiyona donusturun',
                '_myliba_home_final_cta_title' => 'Stratejinizi bugun aksiyona donusturun.',
                '_myliba_home_final_cta_text' => 'OKR, performans, geri bildirim ve kultur gelisimini tek platformda birlestirin.',
                '_myliba_home_final_cta_primary_label' => 'Demo iste',
                '_myliba_home_final_cta_secondary_label' => 'Iletisime gec',
            ];
        }

        return [
            '_myliba_home_builder' => $this->home_builder_defaults(),
            '_myliba_home_hero_rotating_titles' => "Build a stronger culture around clear goals\nThe performance platform that turns strategy into action\nManage OKR and culture routines in one rhythm",
            '_myliba_home_hero_proof' => "Strategy to action\nContinuous performance\nAcademy + software",
            '_myliba_home_dashboard_brand' => 'Myliba',
            '_myliba_home_dashboard_title' => 'Performance OS',
            '_myliba_home_dashboard_nav' => "OKR\nKPI\nCFR\n1:1\nAcademy",
            '_myliba_home_dashboard_objective_label' => 'Company objective',
            '_myliba_home_dashboard_objective_title' => 'Increase strategy execution visibility',
            '_myliba_home_dashboard_progress' => '76',
            '_myliba_home_dashboard_rows' => "Leadership rhythm active | HR | On track | green\nTeam OKR alignment | Strategy | Review | blue\n1:1 action follow-up | Leads | Focus | orange",
            '_myliba_home_dashboard_col_1' => 'Key Result',
            '_myliba_home_dashboard_col_2' => 'Owner',
            '_myliba_home_dashboard_col_3' => 'Status',
            '_myliba_home_metric_1_value' => '72%',
            '_myliba_home_metric_1_label' => 'OKR progress',
            '_myliba_home_metric_2_value' => '148',
            '_myliba_home_metric_2_label' => '1:1 notes',
            '_myliba_home_feedback_title' => 'Feedback card',
            '_myliba_home_feedback_text' => 'Coaching notes, recognition and actions stay connected to goals.',
            '_myliba_home_trust_title' => 'Built for teams that manage performance culture seriously.',
            '_myliba_home_trust_items' => "OKR\nKPI\nCFR\n1:1",
            '_myliba_home_problem_eyebrow' => 'The problem',
            '_myliba_home_problem_title' => 'Goal hierarchies get lost, feedback stays periodic.',
            '_myliba_home_problem_text' => 'Performance management becomes measurable only when goals, conversations and actions move in the same flow.',
            '_myliba_home_problem_cards' => "Goal hierarchy gets lost | Company, team and individual goals cannot be read as one clear contribution map.\nPerformance conversations stay detached | Check-ins, feedback and reviews are not connected to active goals and evidence.\nFeedback remains periodic | Recognition, coaching and feedforward arrive too late to change the next action.\nStrategy does not turn into action | Priorities stay in decks instead of becoming owners, deadlines and follow-up routines.\nTransparency gets harder | Leaders, HR and teams cannot see risks, blockers and progress in one operating view.\nManual operations cost time | Spreadsheets, reminders and status chasing slow down the performance rhythm.",
            '_myliba_home_strategy_flow_eyebrow' => 'Performance rhythm',
            '_myliba_home_strategy_flow_title' => 'Strategy to goals, action and culture.',
            '_myliba_home_strategy_flow_text' => 'Build one connected operating rhythm for priorities, ownership, action and learning.',
            '_myliba_home_strategy_flow_steps' => "Strategy | Make priorities visible and shared across the organization. | S\nGoals | Connect OKR and KPI ownership from company to teams. | G\nAction | Turn each priority into accountable actions and follow-up. | A\nCulture | Build 1:1, CFR and learning routines around the work. | C",
            '_myliba_home_solution_eyebrow' => 'The Myliba solution',
            '_myliba_home_solution_title' => 'One platform for goals, performance conversations, actions and culture development.',
            '_myliba_home_products_button' => 'Explore products',
            '_myliba_home_module_button' => 'View module',
            '_myliba_home_academy_eyebrow' => 'Academy + software',
            '_myliba_home_academy_title' => 'Software power, academy experience.',
            '_myliba_home_academy_text' => 'Myliba helps organizations not only define goals, but also make goal-oriented work sustainable through leadership development, performance coaching, workshops and cultural transformation programs.',
            '_myliba_home_academy_items' => "OKR culture and adoption programs\nLeadership and coaching routines\nContinuous performance development\nHuman and culture-focused transformation",
            '_myliba_home_academy_button' => 'Explore academy',
            '_myliba_home_stepper_eyebrow' => 'Quick start',
            '_myliba_home_stepper_title' => 'Everything you need to start using Myliba.',
            '_myliba_home_stepper_text' => 'A clear onboarding flow helps us understand your needs, define the right path and launch the first performance rhythm together.',
            '_myliba_home_stepper_steps' => "Request a demo | Reach us through the short form so we can understand your team and main goal.\nShare your team and priorities | Clarify the performance routines you want to build first.\nChoose the right module and academy path | Match the software, academy and launch plan to your organization.\nLaunch your first OKR, action and 1:1 rhythm | Start using the first routines and track progress with your team.",
            '_myliba_home_stepper_cta_label' => 'Request a demo',
            '_myliba_home_role_gains_eyebrow' => 'Role-based value',
            '_myliba_home_role_gains_title' => 'Clear gains for every role.',
            '_myliba_home_role_gains_text' => 'Give each stakeholder the view and routine they need inside the same operating system.',
            '_myliba_home_role_gains_rows' => "CEO / Executive Team | Lead strategy with a live operating view | See company priorities, team contribution and risk signals without waiting for manual reporting. | Strategic visibility | Goals, metrics and actions are connected in one screen. | Faster decisions | Leadership can focus attention on the places that need support.\nHuman Resources | Make performance continuous and fair | Bring 1:1s, feedback, development notes and review evidence into a manageable rhythm. | Process clarity | HR can run cycles without spreadsheet-heavy follow-up. | Employee growth | Development signals stay connected to goals and coaching.\nStrategy Office | Connect priorities with execution | Translate strategic choices into OKRs, KPIs, initiatives and ownership that teams can follow. | Alignment map | Each priority can be traced to goals, owners and actions. | Progress rhythm | Review routines stay measurable and repeatable.\nTeam Leaders | Coach work without losing follow-up | Prepare 1:1s, follow actions and give feedback while keeping team goals visible. | Manager rhythm | Meetings become structured and connected to outcomes. | Team focus | People know what matters and what changes next.\nEmployees | Understand contribution and growth | See goals, expectations, feedback and development actions in one place. | Role clarity | Contribution to company priorities becomes visible. | Better feedback | Recognition and feedforward are easier to act on.",
            '_myliba_home_outcomes_eyebrow' => 'Outcomes',
            '_myliba_home_outcomes_title' => 'Measurable development culture.',
            '_myliba_home_outcomes_text' => 'Every rhythm leaves a measurable signal for better focus, coaching and decisions.',
            '_myliba_home_outcomes_cards' => "Strategy and team alignment | Connect company strategy with team and individual contribution.\nTransparent goal progress | See progress, blockers and ownership without waiting for meetings.\nContinuous development | Turn 1:1, feedback and coaching into a continuous routine.\nLess manual work | Reduce spreadsheet, reminder and fragmented update follow-up.\nHigher employee engagement | Strengthen meaning, visibility and development signals.\nEvidence-based performance culture | Use goal, action and feedback evidence for reviews and decisions.",
            '_myliba_home_resources_eyebrow' => 'Resources',
            '_myliba_home_resources_title' => 'Insights for performance culture.',
            '_myliba_home_resources_text' => 'Practical writing about OKR, performance, leadership and culture.',
            '_myliba_home_resources_button' => 'View all',
            '_myliba_home_faq_eyebrow' => 'FAQ',
            '_myliba_home_faq_title' => 'First questions in your mind.',
            '_myliba_home_faq_text' => 'The answers teams usually need before they start building a measurable performance rhythm.',
            '_myliba_home_faq_items' => "How is Myliba different from a classic OKR tool? | Myliba combines OKR, KPI, CFR, 1:1, feedback, actions, analytics and academy routines.\nCan Myliba support implementation and training? | Yes. The platform is supported by academy programs, workshops and coaching routines.\nWho uses Myliba most often? | Executive teams, HR, strategy offices, team leaders and employees use different views of the same operating rhythm.\nHow does the demo process work? | We understand your team structure and current routines, then show the flow that fits your organization.",
            '_myliba_home_final_cta_eyebrow' => 'Make it actionable',
            '_myliba_home_final_cta_title' => 'Turn your strategy into action today.',
            '_myliba_home_final_cta_text' => 'Bring OKR, performance, feedback and culture development together in one platform.',
            '_myliba_home_final_cta_primary_label' => 'Request a demo',
            '_myliba_home_final_cta_secondary_label' => 'Contact us',
        ];
    }

    private function home_builder_defaults(): string
    {
        $keys = ['hero', 'trust_bar', 'problem', 'solutions', 'products', 'academy', 'role_gains', 'outcomes', 'resources', 'faq', 'final_cta'];
        $sections = [];
        $order = 10;

        foreach ($keys as $key) {
            $sections[] = [
                'key' => $key,
                'enabled' => true,
                'order' => $order,
            ];
            $order += 10;
        }

        return wp_json_encode($sections);
    }

    private function seed_navigation(): void
    {
        $primary_id = $this->ensure_menu('Myliba Primary');
        $footer_id = $this->ensure_menu('Myliba Footer');
        $footer_blog_id = $this->ensure_menu('Myliba Footer Blog Links');

        if ($primary_id) {
            $this->seed_menu_items($primary_id, [
                ['en/our-products', 'Products'],
                ['en/okr-culture-academy', 'Academy'],
                ['en/solutions', 'Solutions'],
                ['en/our-story', 'Our Story'],
                ['en/blog', 'Blog'],
                ['en/contact', 'Contact'],
            ]);
        }

        if ($footer_id) {
            $this->seed_menu_items($footer_id, [
                ['en/blog', 'Blog'],
                ['en/our-story', 'Our Story'],
                ['en/faq', 'FAQ'],
                ['en/security', 'Security'],
                ['en/privacy-policy', 'Privacy'],
            ]);
        }

        if ($footer_blog_id) {
            $this->seed_menu_items($footer_blog_id, [
                ['en/blog', 'Blog'],
                ['en/events', 'Events'],
                ['en/okr-culture-academy', 'OKR Culture Academy'],
                ['en/faq', 'FAQ'],
            ]);
        }

        $locations = get_theme_mod('nav_menu_locations', []);
        $locations = is_array($locations) ? $locations : [];

        if ($primary_id) {
            $locations['primary'] = $primary_id;
        }

        if ($footer_id) {
            $locations['footer'] = $footer_id;
        }

        if ($footer_blog_id) {
            $locations['footer_blog'] = $footer_blog_id;
        }

        set_theme_mod('nav_menu_locations', $locations);
    }

    private function ensure_menu(string $name): int
    {
        $menu = wp_get_nav_menu_object($name);

        if ($menu) {
            return (int) $menu->term_id;
        }

        $menu_id = wp_create_nav_menu($name);

        return is_wp_error($menu_id) ? 0 : (int) $menu_id;
    }

    private function seed_menu_items(int $menu_id, array $items): void
    {
        $existing_items = wp_get_nav_menu_items($menu_id, ['post_status' => 'any']);

        if (is_array($existing_items) && count($existing_items) > 0) {
            return;
        }

        foreach ($items as [$path, $label]) {
            $page = get_page_by_path($path);

            if ($page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title' => $label,
                    'menu-item-object-id' => (int) $page->ID,
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                ]);
                continue;
            }

            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $label,
                'menu-item-url' => home_url('/' . trim($path, '/') . '/'),
                'menu-item-type' => 'custom',
                'menu-item-status' => 'publish',
            ]);
        }
    }

    private function upsert_page(string $title, string $slug, string $content, array $meta = [], int $parent = 0, string $template = ''): int
    {
        $existing = get_page_by_path($parent ? get_post_field('post_name', $parent) . '/' . $slug : $slug);
        $postarr = [
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => $content,
            'post_parent' => $parent,
        ];

        if ($existing) {
            $postarr['ID'] = $existing->ID;
            $post_id = wp_update_post($postarr);
        } else {
            $post_id = wp_insert_post($postarr);
        }

        if ($template !== '') {
            update_post_meta($post_id, '_wp_page_template', $template);
        }

        $this->save_meta($post_id, $meta);
        $this->set_polylang_language($post_id, $meta['_myliba_language'] ?? 'en');

        return (int) $post_id;
    }

    private function upsert_post(string $title, string $slug, string $language): int
    {
        return $this->upsert_post_type('post', $title, $slug, '<p>Use this starter article as a placeholder for the migrated blog content.</p>', [
            '_myliba_language' => $language,
            '_myliba_hero_title' => $title,
            '_myliba_seo_title' => $title . ' | Myliba',
            '_myliba_seo_description' => 'A Myliba article about operating culture, OKRs, and team habits.',
        ]);
    }

    private function upsert_event(string $title, string $slug, string $language, string $date, string $location): int
    {
        $post_id = $this->upsert_post_type('myliba_event', $title, $slug, '<p>Update this event with the final agenda, speakers, and registration details.</p>', [
            '_myliba_language' => $language,
            '_myliba_hero_title' => $title,
            '_myliba_event_date' => gmdate('Y-m-d H:i', strtotime($date)),
            '_myliba_event_location' => $location,
            '_myliba_event_status' => 'upcoming',
        ]);

        return $post_id;
    }

    private function upsert_team(string $name, string $role, int $order): int
    {
        return $this->upsert_post_type('myliba_team', $name, sanitize_title($name), '<p>Add a short biography and photo from the media library.</p>', [
            '_myliba_person_role' => $role,
            '_myliba_order' => (string) $order,
        ]);
    }

    private function upsert_logo(string $name, int $order): int
    {
        return $this->upsert_post_type('myliba_client_logo', $name, sanitize_title($name), '', [
            '_myliba_order' => (string) $order,
        ], 'publish');
    }

    private function upsert_post_type(string $post_type, string $title, string $slug, string $content, array $meta = [], string $status = 'publish'): int
    {
        $existing = get_page_by_path($slug, OBJECT, $post_type);
        $postarr = [
            'post_type' => $post_type,
            'post_status' => $status,
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => $content,
        ];

        if ($post_type === 'post') {
            $postarr['post_excerpt'] = $meta['_myliba_seo_description'] ?? '';
        }

        if ($existing) {
            $postarr['ID'] = $existing->ID;
            $post_id = wp_update_post($postarr);
        } else {
            $post_id = wp_insert_post($postarr);
        }

        $this->save_meta($post_id, $meta);
        $this->set_polylang_language($post_id, $meta['_myliba_language'] ?? Options\get('default_locale', 'en'));

        return (int) $post_id;
    }

    private function save_meta(int $post_id, array $meta): void
    {
        foreach ($meta as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }

    private function set_polylang_language(int $post_id, string $language): void
    {
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($post_id, $language);
        }
    }

    private function starter_content(string $key): string
    {
        $content = [
            'home_en' => '<p>This WordPress migration keeps Myliba editable from the admin panel while preserving a structured content model for SEO, multilingual publishing, events, forms, and team content.</p>',
            'home_tr' => '<p>Bu WordPress migrasyonu Myliba sitesini admin panelden yonetilebilir hale getirirken SEO, cok dilli yayin, etkinlik, form ve ekip icerigi icin duzenli bir model sunar.</p>',
            'generic' => '<p>Replace this starter content from the WordPress editor. Use the Myliba Hero and Myliba SEO boxes for page-level presentation and metadata.</p>',
        ];

        return $content[$key] ?? $content['generic'];
    }
}
