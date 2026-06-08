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
                '_myliba_home_problem_title' => 'Hedefler, aksiyonlar ve geri bildirim farkli sistemlerde kaldiginda strateji kaybolur.',
                '_myliba_home_problem_cards' => "Parcali rutinler | OKR calismalari, KPI reviewlari, performans gorusmeleri ve geri bildirim notlari kopuk ritullere donusur.\nZayif gorunurluk | Liderlik, IK ve ekipler katkiyi, engelleri ve ilerlemeyi tek ritimde goremez.",
                '_myliba_home_solution_eyebrow' => 'Myliba cozumu',
                '_myliba_home_solution_title' => 'Hedefler, performans gorusmeleri, aksiyonlar ve kultur gelisimi icin tek platform.',
                '_myliba_home_products_button' => 'Urunleri incele',
                '_myliba_home_module_button' => 'Modulu gor',
                '_myliba_home_academy_eyebrow' => 'Akademi + yazilim',
                '_myliba_home_academy_title' => 'Yazilim gucu, akademi deneyimi.',
                '_myliba_home_academy_text' => 'Myliba kurumlarin sadece hedef tanimlamasina degil, liderlik gelisimi, performans koclugu, atolyeler ve kultur donusumu programlariyla hedef odakli calismayi surdurulebilir hale getirmesine yardimci olur.',
                '_myliba_home_academy_items' => "OKR kulturu ve adaptasyon programlari\nLiderlik ve kocluk rutinleri\nSurekli performans gelisimi\nInsan ve kultur odakli donusum",
                '_myliba_home_academy_button' => 'Akademiyi incele',
                '_myliba_home_use_cases_eyebrow' => 'Kullanim alanlari',
                '_myliba_home_use_cases_title' => 'Ust yonetim, IK, strateji ekipleri ve liderler icin amaca gore yollar.',
                '_myliba_home_solution_button' => 'Cozumu gor',
                '_myliba_home_outcomes_eyebrow' => 'Is sonuclari',
                '_myliba_home_outcomes_title' => 'Performans kulturunu gorunur, gelistirilebilir ve olculebilir hale getirin.',
                '_myliba_home_outcomes_cards' => "Hizalanma | Sirket stratejisini takim ve bireysel katkiyla baglayin.\nSeffaflik | Toplanti beklemeden ilerlemeyi, engelleri ve sahipligi gorun.\nGelisim | 1:1, geri bildirim ve koclugu surekli rutine donusturun.\nUygulama | Oncelikleri aksiyonlara, sahipliklere ve olculebilir sonuclara donusturun.",
                '_myliba_home_b2b_trust_eyebrow' => 'Guven',
                '_myliba_home_b2b_trust_title' => 'B2B guveni icin tasarlandi.',
                '_myliba_home_b2b_trust_text' => 'Rol bazli erisim, gizlilik odakli formlar, guvenlik mesajlari ve yasal sayfalar WordPress migrasyon temelinin parcasidir.',
                '_myliba_home_b2b_trust_button' => 'Guvenligi gor',
                '_myliba_home_resources_eyebrow' => 'Kaynaklar',
                '_myliba_home_resources_title' => 'OKR, performans ve kultur konulari icin SEO hazir icerik.',
                '_myliba_home_faq_eyebrow' => 'SSS',
                '_myliba_home_faq_title' => 'Demo talep etmeden once sik sorulan sorular.',
                '_myliba_home_final_eyebrow' => 'Sonraki adim',
                '_myliba_home_final_title' => 'Myliba strateji, performans ve kultur rutinlerinizi nasil baglar gorun.',
                '_myliba_home_final_text' => 'Ekip buyuklugunuzu ve onceliklerinizi paylasin. Kurumunuza uyan modul ve akademi yolunu gosterelim.',
            ];
        }

        return [
            '_myliba_home_builder' => $this->home_builder_defaults(),
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
            '_myliba_home_problem_title' => 'Strategy gets lost when goals, actions and feedback live in separate systems.',
            '_myliba_home_problem_cards' => "Fragmented routines | OKR workshops, KPI reviews, performance interviews and feedback notes become disconnected rituals.\nWeak visibility | Leadership, HR and teams cannot see contribution, blockers and progress in one operating rhythm.",
            '_myliba_home_solution_eyebrow' => 'The Myliba solution',
            '_myliba_home_solution_title' => 'One platform for goals, performance conversations, actions and culture development.',
            '_myliba_home_products_button' => 'Explore products',
            '_myliba_home_module_button' => 'View module',
            '_myliba_home_academy_eyebrow' => 'Academy + software',
            '_myliba_home_academy_title' => 'Software power, academy experience.',
            '_myliba_home_academy_text' => 'Myliba helps organizations not only define goals, but also make goal-oriented work sustainable through leadership development, performance coaching, workshops and cultural transformation programs.',
            '_myliba_home_academy_items' => "OKR culture and adoption programs\nLeadership and coaching routines\nContinuous performance development\nHuman and culture-focused transformation",
            '_myliba_home_academy_button' => 'Explore academy',
            '_myliba_home_use_cases_eyebrow' => 'Use cases',
            '_myliba_home_use_cases_title' => 'Purpose-built paths for executives, HR, strategy teams and leaders.',
            '_myliba_home_solution_button' => 'See solution',
            '_myliba_home_outcomes_eyebrow' => 'Business outcomes',
            '_myliba_home_outcomes_title' => 'Make performance culture visible, coachable and measurable.',
            '_myliba_home_outcomes_cards' => "Alignment | Connect company strategy with team and individual contribution.\nTransparency | See progress, blockers and ownership without waiting for meetings.\nDevelopment | Turn 1:1, feedback and coaching into a continuous routine.\nExecution | Transform priorities into actions, ownership and measurable results.",
            '_myliba_home_b2b_trust_eyebrow' => 'Trust',
            '_myliba_home_b2b_trust_title' => 'Designed for B2B confidence.',
            '_myliba_home_b2b_trust_text' => 'Role-based access, privacy-first forms, security messaging and legal pages are part of the WordPress migration foundation.',
            '_myliba_home_b2b_trust_button' => 'View security',
            '_myliba_home_resources_eyebrow' => 'Resources',
            '_myliba_home_resources_title' => 'SEO-ready content for OKR, performance and culture topics.',
            '_myliba_home_faq_eyebrow' => 'FAQ',
            '_myliba_home_faq_title' => 'Common questions before requesting a demo.',
            '_myliba_home_final_eyebrow' => 'Next step',
            '_myliba_home_final_title' => 'See how Myliba can connect your strategy, performance and culture routines.',
            '_myliba_home_final_text' => 'Share your team size and priorities. We will show the modules and academy path that fit your organization.',
        ];
    }

    private function home_builder_defaults(): string
    {
        $keys = ['hero', 'trust_bar', 'problem', 'products', 'academy', 'solutions', 'outcomes', 'testimonials', 'resources', 'faq', 'final_cta'];
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

        $locations = get_theme_mod('nav_menu_locations', []);
        $locations = is_array($locations) ? $locations : [];

        if ($primary_id) {
            $locations['primary'] = $primary_id;
        }

        if ($footer_id) {
            $locations['footer'] = $footer_id;
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
