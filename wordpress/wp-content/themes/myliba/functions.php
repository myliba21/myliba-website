<?php

if (!defined('ABSPATH')) {
    exit;
}

function myliba_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', [
        'height' => 80,
        'width' => 240,
        'flex-height' => true,
        'flex-width' => true,
    ]);

    register_nav_menus([
        'primary' => __('Primary Navigation', 'myliba'),
        'footer' => __('Footer Navigation', 'myliba'),
        'footer_blog' => __('Footer Blog Links', 'myliba'),
    ]);
}
add_action('after_setup_theme', 'myliba_theme_setup');

function myliba_asset_version(string $relative_path): string
{
    $path = get_template_directory() . '/' . ltrim($relative_path, '/');

    if (file_exists($path)) {
        return (string) filemtime($path);
    }

    return (string) wp_get_theme()->get('Version');
}

function myliba_enqueue_assets(): void
{
    wp_enqueue_style('myliba-main', get_template_directory_uri() . '/assets/css/main.css', [], myliba_asset_version('assets/css/main.css'));
    wp_enqueue_script('myliba-main', get_template_directory_uri() . '/assets/js/main.js', [], myliba_asset_version('assets/js/main.js'), true);
}
add_action('wp_enqueue_scripts', 'myliba_enqueue_assets');

function myliba_option(string $key, mixed $fallback = ''): mixed
{
    if (function_exists('Myliba\\Core\\Options\\get')) {
        $value = \Myliba\Core\Options\get($key, $fallback);
    } else {
        $value = $fallback;
    }

    $translatable_keys = [
        'demo_cta_label',
        'footer_cta_title',
        'footer_note',
        'primary_cta_label',
        'promo_left_text',
        'promo_message',
        'promo_right_text',
    ];

    if (is_string($value) && !is_admin() && in_array($key, $translatable_keys, true)) {
        return myliba_translate_text($value);
    }

    return $value;
}

function myliba_env(string $key, string $fallback = ''): string
{
    $value = getenv($key);

    if ($value === false && isset($_ENV[$key])) {
        $value = $_ENV[$key];
    }

    if ($value === false && isset($_SERVER[$key])) {
        $value = $_SERVER[$key];
    }

    if ($value === false && defined($key)) {
        $value = constant($key);
    }

    if ($value === false && function_exists('apache_getenv')) {
        $value = apache_getenv($key);
    }

    return is_string($value) && trim($value) !== '' ? trim($value) : $fallback;
}

function myliba_asset_url_from_env(string $value): string
{
    $value = trim($value);

    if ($value === '') {
        return '';
    }

    if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
        return $value;
    }

    if (str_starts_with($value, '/')) {
        return home_url($value);
    }

    return get_template_directory_uri() . '/' . ltrim($value, '/');
}

function myliba_hero_banner_images(): array
{
    $combined = myliba_env('MYLIBA_HERO_BANNER_IMAGES');
    $sources = $combined !== ''
        ? preg_split('/[\r\n|,]+/', $combined) ?: []
        : [
            myliba_env('MYLIBA_HERO_BANNER_IMAGE_1', 'assets/images/hero-1.png'),
            myliba_env('MYLIBA_HERO_BANNER_IMAGE_2', 'assets/images/hero-2.png'),
        ];

    $alts = [
        myliba_env('MYLIBA_HERO_BANNER_ALT_1', __('Myliba weekly focus dashboard preview', 'myliba')),
        myliba_env('MYLIBA_HERO_BANNER_ALT_2', __('Myliba goal map dashboard preview', 'myliba')),
    ];

    $images = [];
    foreach ($sources as $index => $source) {
        $url = myliba_asset_url_from_env((string) $source);

        if ($url === '') {
            continue;
        }

        $images[] = [
            'url' => $url,
            'alt' => $alts[$index] ?? sprintf(__('Myliba product dashboard preview %d', 'myliba'), $index + 1),
        ];
    }

    return $images;
}

function myliba_meta(string $key, int $post_id = 0, mixed $fallback = ''): mixed
{
    $post_id = $post_id ?: get_queried_object_id();
    $value = $post_id ? get_post_meta($post_id, $key, true) : '';

    return $value !== '' ? $value : $fallback;
}

function myliba_current_language(): string
{
    if (function_exists('pll_current_language')) {
        return (string) pll_current_language('slug');
    }

    if (is_singular()) {
        return (string) myliba_meta('_myliba_language', get_queried_object_id(), myliba_option('default_locale', 'en'));
    }

    return (string) myliba_option('default_locale', 'en');
}

function myliba_is_academy_landing_page(int $post_id = 0): bool
{
    $post = get_post($post_id ?: get_queried_object_id());

    if (!$post || $post->post_type !== 'page') {
        return false;
    }

    return in_array($post->post_name, ['okr-culture-academy', 'okr-kultur-akademisi'], true);
}

function myliba_available_locales(): array
{
    $raw = (string) myliba_option('available_locales', "en\ntr");
    $items = preg_split('/[\r\n,]+/', $raw) ?: [];
    $items = array_map('sanitize_key', array_map('trim', $items));
    $items = array_filter($items, static fn ($item) => $item !== '');

    return array_values(array_unique($items)) ?: ['en', 'tr'];
}

function myliba_locale_cookie_name(): string
{
    return 'myliba_locale';
}

function myliba_request_path(): string
{
    $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '/';
    $request_path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);

    $request_path = '/' . trim($request_path, '/');
    $home_path = '/' . trim($home_path, '/');

    if ($home_path !== '/' && str_starts_with($request_path . '/', $home_path . '/')) {
        $request_path = substr($request_path, strlen($home_path));
        $request_path = '/' . trim($request_path, '/');
    }

    return $request_path === '/' ? '/' : untrailingslashit($request_path);
}

function myliba_locale_from_path(string $path): string
{
    $first_segment = strtok(trim($path, '/'), '/');
    $first_segment = $first_segment === false ? '' : sanitize_key($first_segment);

    return in_array($first_segment, myliba_available_locales(), true) ? $first_segment : '';
}

function myliba_locale_from_accept_language(string $header): string
{
    foreach (explode(',', strtolower($header)) as $language) {
        $locale = str_replace('_', '-', trim(explode(';', $language, 2)[0] ?? ''));

        foreach (myliba_available_locales() as $available_locale) {
            if ($locale === $available_locale || str_starts_with($locale, $available_locale . '-')) {
                return $available_locale;
            }
        }
    }

    return '';
}

function myliba_preferred_locale(): string
{
    $default_locale = (string) myliba_option('default_locale', 'en');
    $cookie_name = myliba_locale_cookie_name();
    $cookie_locale = isset($_COOKIE[$cookie_name]) ? sanitize_key(wp_unslash($_COOKIE[$cookie_name])) : '';

    if (in_array($cookie_locale, myliba_available_locales(), true)) {
        return $cookie_locale;
    }

    $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_ACCEPT_LANGUAGE'])) : '';
    $accepted_locale = myliba_locale_from_accept_language($accept_language);

    return $accepted_locale !== '' ? $accepted_locale : $default_locale;
}

function myliba_set_locale_cookie(string $locale): void
{
    if (!in_array($locale, myliba_available_locales(), true) || headers_sent()) {
        return;
    }

    $path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $path = '/' . trim($path, '/');

    setcookie(myliba_locale_cookie_name(), $locale, [
        'expires'  => time() + YEAR_IN_SECONDS,
        'path'     => $path === '/' ? '/' : $path,
        'samesite' => 'Lax',
        'secure'   => is_ssl(),
        'httponly'  => false, // Keep false: JS reads this cookie to sync locale UI state.
    ]);
}

function myliba_redirect_root_to_preferred_locale(): void
{
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
        return;
    }

    if (myliba_request_path() !== '/') {
        return;
    }

    $preferred_locale = myliba_preferred_locale();
    $default_locale = (string) myliba_option('default_locale', 'en');

    if ($preferred_locale !== $default_locale) {
        wp_safe_redirect(home_url('/' . $preferred_locale . '/'), 302);
        exit;
    }
}
add_action('template_redirect', 'myliba_redirect_root_to_preferred_locale', 0);

function myliba_sync_current_locale_cookie(): void
{
    $path_locale = myliba_locale_from_path(myliba_request_path());
    $locale = $path_locale !== '' ? $path_locale : myliba_current_language();

    myliba_set_locale_cookie($locale);
}
add_action('template_redirect', 'myliba_sync_current_locale_cookie', 1);

function myliba_filter_language_attributes(string $output): string
{
    $locale = myliba_current_language() === 'tr' ? 'tr-TR' : 'en-US';

    if (preg_match('/\blang="[^"]*"/', $output)) {
        return preg_replace('/\blang="[^"]*"/', 'lang="' . esc_attr($locale) . '"', $output) ?: $output;
    }

    return trim($output . ' lang="' . esc_attr($locale) . '"');
}
add_filter('language_attributes', 'myliba_filter_language_attributes');

function myliba_translate_text(string $text): string
{
    $text = trim($text);

    if (myliba_current_language() !== 'tr') {
        return $text;
    }

    $translations = [
        '1:1 notes' => '1:1 notları',
        '1:1s' => '1:1 görüşmeler',
        'Academy' => 'Akademi',
        'Academy + software' => 'Akademi + yazılım',
        'Action' => 'Aksiyon',
        'Action Management' => 'Aksiyon Yönetimi',
        'Alignment' => 'Uyum',
        'Alignment map' => 'Uyum haritası',
        'Better feedback' => 'Daha iyi geri bildirim',
        'Blog & resources' => 'Blog ve kaynaklar',
        'Build 1:1, CFR and learning routines around the work.' => 'İşin etrafında 1:1, CFR ve öğrenme rutinleri kurun.',
        'Build one connected operating rhythm for priorities, ownership, action and learning.' => 'Öncelikler, sahiplik, aksiyon ve öğrenme için tek bir bağlı çalışma ritmi kurun.',
        'Built for teams that manage performance culture seriously.' => 'Performans kültürünü ciddiyetle yöneten ekipler için tasarlandı.',
        'Business outcomes' => 'İş sonuçları',
        'Calibration' => 'Kalibrasyon',
        'Can Myliba support implementation and training?' => 'Myliba uygulama ve eğitim süreçlerini destekler mi?',
        'Coach work without losing follow-up' => 'Takibi kaybetmeden işi koçlukla yönetin',
        'Choose the operating routines you want to strengthen across OKR, KPI, CFR, 1:1, feedback and analytics.' => 'OKR, KPI, CFR, 1:1, geri bildirim ve analitik genelinde güçlendirmek istediğiniz çalışma rutinlerini seçin.',
        'Clear gains for every role.' => 'Her rol için net kazanımlar.',
        'Coaching notes, recognition and actions stay connected to goals.' => 'Koçluk notları, takdirler ve aksiyonlar hedeflerle bağlantılı kalır.',
        'Company' => 'Şirket',
        'Company objective' => 'Şirket hedefi',
        'Connect company strategy with team and individual contribution.' => 'Şirket stratejisini ekip ve bireysel katkıyla bağlayın.',
        'Connect OKR and KPI ownership from company to teams.' => 'OKR ve KPI sahipliğini şirketten ekiplere bağlayın.',
        'Connect priorities with execution' => 'Öncelikleri uygulamayla bağlayın',
        'Contact' => 'İletişim',
        'Contact us' => 'İletişime geçin',
        'Contribution to company priorities becomes visible.' => 'Şirket önceliklerine katkı görünür hale gelir.',
        'Continuous performance' => 'Sürekli performans',
        'Continuous performance development' => 'Sürekli performans gelişimi',
        'Conversations' => 'Görüşmeler',
        'Copyright %1$s %2$s. All rights reserved.' => 'Copyright %1$s %2$s. Tüm hakları saklıdır.',
        'Culture' => 'Kültür',
        'Culture Analysis' => 'Kültür Analizi',
        'Culture, goals and performance' => 'Kültür, hedefler ve performans',
        'Development' => 'Gelişim',
        'Development signals stay connected to goals and coaching.' => 'Gelişim sinyalleri hedefler ve koçlukla bağlantılı kalır.',
        'Employee growth' => 'Çalışan gelişimi',
        'Employees' => 'Çalışanlar',
        'Ethics Counsel' => 'Etik Danışmanlık',
        'Every rhythm leaves a measurable signal for better focus, coaching and decisions.' => 'Her ritim daha iyi odak, koçluk ve kararlar için ölçülebilir bir sinyal bırakır.',
        'Execution' => 'Uygulama',
        'Executive teams, HR, strategy offices, team leaders and employees use different views of the same operating rhythm.' => 'Üst yönetim, İK, strateji ekipleri, ekip liderleri ve çalışanlar aynı çalışma ritminin farklı görünümlerini kullanır.',
        'Explore academy' => 'Akademiyi keşfet',
        'Explore Myliba modules' => 'Myliba modüllerini keşfet',
        'Fairer decisions' => 'Daha adil kararlar',
        'FAQ' => 'SSS',
        'Faster decisions' => 'Daha hızlı kararlar',
        'Feedback and Feedforward' => 'Geri ve ileri bildirim',
        'Feedback card' => 'Geri bildirim kartı',
        'First questions in your mind.' => 'Aklınızdaki ilk sorular.',
        'Footer blog links' => 'Footer blog bağlantıları',
        'Footer call to action' => 'Footer aksiyon çağrısı',
        'Footer company links' => 'Footer şirket bağlantıları',
        'Footer page links' => 'Footer sayfa bağlantıları',
        'Footer product links' => 'Footer ürün bağlantıları',
        'Goal health' => 'Hedef sağlığı',
        'Goal hierarchy gets lost' => 'Hedef hiyerarşisi kaybolur',
        'Goal alignment across teams' => 'Ekipler arası hedef uyumu',
        'Goals' => 'Hedefler',
        'Give each stakeholder the view and routine they need inside the same operating system.' => 'Her paydaşa aynı çalışma sistemi içinde ihtiyaç duyduğu görünümü ve rutini verin.',
        'HR can run cycles without spreadsheet-heavy follow-up.' => 'İK, tablo ağırlıklı takip olmadan döngüleri yürütebilir.',
        'How is Myliba different from a classic OKR tool?' => 'Myliba klasik bir OKR aracından nasıl ayrılır?',
        'Human Resources' => 'İnsan Kaynakları',
        'Human and culture-focused transformation' => 'İnsan ve kültür odaklı dönüşüm',
        'Implementation and training' => 'Uygulama ve eğitim',
        'Insights and Analytics' => 'İçgörüler ve Analitik',
        'Institutions that want to establish a new generation performance improvement system' => 'Yeni nesil performans geliştirme sistemi kurmak isteyen kurumlar',
        'Key Result' => 'Anahtar Sonuç',
        'Leadership and Coaching' => 'Liderlik ve Koçluk',
        'Leadership and coaching routines' => 'Liderlik ve koçluk rutinleri',
        'Leadership can focus attention on the places that need support.' => 'Liderlik dikkatini destek gereken alanlara yöneltebilir.',
        'Lead strategy with a live operating view' => 'Stratejiyi canlı bir çalışma görünümüyle yönetin',
        'Make company, team and individual goals visible in one hierarchy.' => 'Şirket, ekip ve bireysel hedefleri tek hiyerarşide görünür kılın.',
        'Make one-on-ones structured, useful and connected to development.' => 'Bire bir görüşmeleri yapılandırılmış, faydalı ve gelişimle bağlantılı hale getirin.',
        'Make performance continuous and fair' => 'Performansı sürekli ve adil hale getirin',
        'Make performance culture visible, coachable and measurable.' => 'Performans kültürünü görünür, koçluk edilebilir ve ölçülebilir hale getirin.',
        'Make priorities visible and shared across the organization.' => 'Öncelikleri organizasyon genelinde görünür ve paylaşılır hale getirin.',
        'Make it actionable' => 'Aksiyona dönüştürün',
        'Manager Effectiveness' => 'Yönetici Etkinliği',
        'Manager rhythm' => 'Yönetici ritmi',
        'Manual operations cost time' => 'Manuel operasyonlar zaman kaybettirir',
        'Meetings become structured and connected to outcomes.' => 'Toplantılar yapılandırılır ve sonuçlarla bağlantılı hale gelir.',
        'Menu' => 'Menü',
        'Myliba combines OKR, KPI, CFR, 1:1 meetings, feedback, action management and academy programs so organizations can build a measurable high-performance culture.' => 'Myliba, kurumların ölçülebilir bir yüksek performans kültürü kurması için OKR, KPI, CFR, 1:1 görüşmeler, geri bildirim, aksiyon yönetimi ve akademi programlarını birleştirir.',
        'Myliba combines OKR, KPI, CFR, 1:1, feedback, actions, analytics and academy routines.' => 'Myliba OKR, KPI, CFR, 1:1, geri bildirim, aksiyon, analitik ve akademi rutinlerini birleştirir.',
        'Myliba connects goals, routines and measurable actions in one operating flow.' => 'Myliba hedefleri, rutinleri ve ölçülebilir aksiyonları tek bir çalışma akışında birleştirir.',
        'Myliba helps organizations not only define goals, but also make goal-oriented work sustainable through leadership development, performance coaching, workshops and cultural transformation programs.' => 'Myliba kurumların yalnızca hedef tanımlamasına değil; liderlik gelişimi, performans koçluğu, atölyeler ve kültürel dönüşüm programlarıyla hedef odaklı çalışmayı sürdürülebilir hale getirmesine yardımcı olur.',
        'Myliba product dashboard preview' => 'Myliba ürün paneli önizlemesi',
        'Myliba product dashboard preview %d' => 'Myliba ürün paneli önizlemesi %d',
        'Myliba product screenshots' => 'Myliba ürün ekran görüntüleri',
        'Myliba weekly focus dashboard preview' => 'Myliba haftalık odak paneli önizlemesi',
        'Myliba goal map dashboard preview' => 'Myliba hedef haritası paneli önizlemesi',
        'No upcoming events at this time.' => 'Şu anda yaklaşan etkinlik yok.',
        'OKR Management' => 'OKR Yönetimi',
        'OKR Culture Academy' => 'OKR Kültür Akademisi',
        'OKR culture and adoption programs' => 'OKR kültürü ve adaptasyon programları',
        'OKR progress' => 'OKR ilerlemesi',
        'OKR, KPI, CFR and performance culture' => 'OKR, KPI, CFR ve performans kültürü',
        'OKR, culture, ethics, and security consulting.' => 'OKR, kültür, etik ve güvenlik danışmanlığı.',
        'One platform for goals, performance conversations, actions and culture development.' => 'Hedefler, performans görüşmeleri, aksiyonlar ve kültür gelişimi için tek platform.',
        'Online' => 'Online',
        'Our Story' => 'Hikayemiz',
        'Owner' => 'Sahip',
        'Pages' => 'Sayfalar',
        'Performance conversations stay detached' => 'Performans görüşmeleri kopuk kalır',
        'Performance culture signals, risks and progress in one view.' => 'Performans kültürü sinyalleri, riskler ve ilerleme tek görünümde.',
        'Performance Development' => 'Performans Gelişimi',
        'Performance management becomes measurable only when goals, conversations and actions move in the same flow.' => 'Performans yönetimi ancak hedefler, görüşmeler ve aksiyonlar aynı akışta ilerlediğinde ölçülebilir hale gelir.',
        'Performance OS' => 'Performans OS',
        'Performance rhythm' => 'Performans ritmi',
        'Portal login' => 'Portal girişi',
        'Prepare 1:1s, follow actions and give feedback while keeping team goals visible.' => 'Ekip hedefleri görünür kalırken 1:1 görüşmeleri hazırlayın, aksiyonları takip edin ve geri bildirim verin.',
        'Primary navigation' => 'Ana navigasyon',
        'Problem' => 'Problem',
        'Process clarity' => 'Süreç netliği',
        'Products' => 'Ürünler',
        'Privacy' => 'Gizlilik',
        'Progress rhythm' => 'İlerleme ritmi',
        'Ready for review' => 'Gözden geçirmeye hazır',
        'Ready to make culture measurable?' => 'Kültürü ölçülebilir hale getirmeye hazır mısınız?',
        'Read practical insights for goal management, leadership routines and performance culture.' => 'Hedef yönetimi, liderlik rutinleri ve performans kültürü için pratik içgörüler okuyun.',
        'Real-time progress tracking' => 'Gerçek zamanlı ilerleme takibi',
        'References and partners' => 'Referanslar ve iş ortakları',
        'Request a demo' => 'Demo talep et',
        'Request demo' => 'Demo talep et',
        'Resources' => 'Kaynaklar',
        'Review routines stay measurable and repeatable.' => 'Gözden geçirme rutinleri ölçülebilir ve tekrarlanabilir kalır.',
        'Risk visibility' => 'Risk görünürlüğü',
        'Role clarity' => 'Rol netliği',
        'Role gains' => 'Rol kazanımları',
        'Role-based value' => 'Rol bazlı değer',
        'See all products' => 'Tüm ürünleri gör',
        'See company priorities, team contribution and risk signals without waiting for manual reporting.' => 'Manuel raporlama beklemeden şirket önceliklerini, ekip katkısını ve risk sinyallerini görün.',
        'See performance culture signals, risks and progress in one view.' => 'Performans kültürü sinyallerini, riskleri ve ilerlemeyi tek görünümde görün.',
        'See progress, blockers and ownership without waiting for meetings.' => 'Toplantıları beklemeden ilerlemeyi, engelleri ve sahipliği görün.',
        'Security' => 'Güvenlik',
        'Senior management and strategy professionals who want to turn strategy into action' => 'Stratejiyi aksiyona dönüştürmek isteyen üst yönetim ve strateji profesyonelleri',
        'Social links' => 'Sosyal bağlantılar',
        'Software power, academy experience.' => 'Yazılım gücü, akademi deneyimi.',
        'Solutions' => 'Çözümler',
        'Solutions menu' => 'Çözümler menüsü',
        'Spot adoption, blocker and engagement signals before they become late surprises.' => 'Adaptasyon, engel ve bağlılık sinyallerini gecikmiş sürprizlere dönüşmeden fark edin.',
        'Start with OKR, performance conversations and academy-supported adoption in one connected flow.' => 'OKR, performans görüşmeleri ve akademi destekli adaptasyonu tek bağlı akışta başlatın.',
        'Status' => 'Durum',
        'Strategy' => 'Strateji',
        'Strategy alignment' => 'Strateji uyumu',
        'Strategy does not turn into action' => 'Strateji aksiyona dönüşmez',
        'Strategy gets lost when goals, actions and feedback live in separate systems.' => 'Hedefler, aksiyonlar ve geri bildirim ayrı sistemlerde yaşadığında strateji kaybolur.',
        'Strategy Office' => 'Strateji Ofisi',
        'Strategy to action' => 'Stratejiden aksiyona',
        'Strategy to goals, action and culture.' => 'Stratejiden hedeflere, aksiyona ve kültüre.',
        'Strategic visibility' => 'Stratejik görünürlük',
        'Support leaders with practical routines for clarity and accountability.' => 'Liderleri netlik ve hesap verebilirlik için pratik rutinlerle destekleyin.',
        'Team focus' => 'Ekip odağı',
        'Team Leaders' => 'Ekip Liderleri',
        'The answers teams usually need before they start building a measurable performance rhythm.' => 'Ekiplerin ölçülebilir bir performans ritmi kurmaya başlamadan önce genellikle ihtiyaç duyduğu yanıtlar.',
        'The Myliba solution' => 'Myliba çözümü',
        'The problem' => 'Problem',
        'Transform priorities into actions, ownership and measurable results.' => 'Öncelikleri aksiyonlara, sahipliğe ve ölçülebilir sonuçlara dönüştürün.',
        'Translate strategic choices into OKRs, KPIs, initiatives and ownership that teams can follow.' => 'Stratejik seçimleri ekiplerin takip edebileceği OKR, KPI, inisiyatif ve sahipliğe çevirin.',
        'Transparency' => 'Şeffaflık',
        'Transparency gets harder' => 'Şeffaflık zorlaşır',
        'Turn 1:1, feedback and coaching into a continuous routine.' => '1:1, geri bildirim ve koçluğu sürekli bir rutine dönüştürün.',
        'Turn each priority into accountable actions and follow-up.' => 'Her önceliği sorumlu aksiyonlara ve takip rutinlerine dönüştürün.',
        'Turn priorities into owners, due dates and progress routines.' => 'Öncelikleri sahiplere, teslim tarihlerine ve ilerleme rutinlerine dönüştürün.',
        'Turn Strategy into Action: Make the contribution transparent by turning strategy into goals and goals into actions. Focus on "what matters most" by developing a motivated, committed and competent team.' => 'Stratejiyi Eyleme Dönüştürün: Stratejiyi hedeflere, hedefleri aksiyonlara dönüştürerek katkıyı şeffaflaştırın. İstekli, bağlı ve yetkin bir ekip geliştirerek "en önemli olana" odaklanın.',
        'Turn strategy into action today.' => 'Stratejinizi bugün aksiyona dönüştürün.',
        'Turn strategy into goals, goals into action.' => 'Stratejiyi hedeflere, hedefleri aksiyona dönüştürün.',
        'Turn your strategy into action today.' => 'Stratejinizi bugün aksiyona dönüştürün.',
        'Understand contribution and growth' => 'Katkıyı ve gelişimi anlayın',
        'Use evidence from goals, conversations and actions in performance growth.' => 'Performans gelişiminde hedeflerden, görüşmelerden ve aksiyonlardan gelen kanıtları kullanın.',
        'Use evidence from goals, conversations and actions in performance reviews.' => 'Performans değerlendirmelerinde hedef, görüşme ve aksiyon kanıtlarını kullanın.',
        'View all' => 'Tümünü gör',
        'View all solutions' => 'Tüm çözümleri gör',
        'View module' => 'Modülü gör',
        'Who uses Myliba most often?' => 'Myliba en çok kimler tarafından kullanılır?',
        'Workshops and coaching routines' => 'Atölye ve koçluk rutinleri',
        'Yes. The platform is supported by academy programs, workshops and coaching routines.' => 'Evet. Platform akademi programları, atölyeler ve koçluk rutinleriyle desteklenir.',
        'You can trace each priority to goals, owners and actions.' => 'Her önceliği hedeflere, sahiplere ve aksiyonlara kadar izleyebilirsiniz.',
        'Each priority can be traced to goals, owners and actions.' => 'Her öncelik hedeflere, sahiplere ve aksiyonlara kadar izlenebilir.',
        'Employee contribution to company priorities becomes visible.' => 'Çalışanın şirket önceliklerine katkısı görünür hale gelir.',
        'See goals, expectations, feedback and development actions in one place.' => 'Hedefleri, beklentileri, geri bildirimi ve gelişim aksiyonlarını tek yerde görün.',
        'People know what matters and what changes next.' => 'İnsanlar neyin önemli olduğunu ve sırada neyin değişeceğini bilir.',
        'Recognition and feedforward are easier to act on.' => 'Takdir ve ileri bildirimi aksiyona çevirmek kolaylaşır.',
        'Is sonuclari' => 'İş sonuçları',
        'Performans kulturunu gorunur, gelistirilebilir ve olculebilir hale getirin.' => 'Performans kültürünü görünür, geliştirilebilir ve ölçülebilir hale getirin.',
        'Sirket stratejisini takim ve bireysel katkiyla baglayin.' => 'Şirket stratejisini takım ve bireysel katkıyla bağlayın.',
        'Seffaflik' => 'Şeffaflık',
        'Toplanti beklemeden ilerlemeyi, engelleri ve sahipligi gorun.' => 'Toplantı beklemeden ilerlemeyi, engelleri ve sahipliği görün.',
        'Gelisim' => 'Gelişim',
        '1:1, geri bildirim ve koclugu surekli rutine donusturun.' => '1:1, geri bildirim ve koçluğu sürekli rutine dönüştürün.',
        'Oncelikleri aksiyonlara, sahipliklere ve olculebilir sonuclara donusturun.' => 'Öncelikleri aksiyonlara, sahipliklere ve ölçülebilir sonuçlara dönüştürün.',
        'OKR, performans ve kultur konulari icin SEO hazir icerik.' => 'OKR, performans ve kültür konuları için SEO hazır içerik.',
        'Demo talep etmeden once sik sorulan sorular.' => 'Demo talep etmeden önce sık sorulan sorular.',
    ];

    return $translations[$text] ?? $text;
}

function myliba_translate_gettext(string $translation, string $text, string $domain): string
{
    if ($domain !== 'myliba' || is_admin()) {
        return $translation;
    }

    return myliba_translate_text($text);
}
add_filter('gettext', 'myliba_translate_gettext', 10, 3);

function myliba_is_locale_landing_page(int $post_id = 0): bool
{
    $post = get_post($post_id ?: get_queried_object_id());

    if (!$post || $post->post_type !== 'page') {
        return false;
    }

    return in_array($post->post_name, myliba_available_locales(), true);
}

function myliba_page_url(string $key): string
{
    $lang = myliba_current_language();
    $paths = [
        'products' => ['en' => 'en/our-products', 'tr' => 'tr/urunler'],
        'academy' => ['en' => 'en/okr-culture-academy', 'tr' => 'tr/okr-kultur-akademisi'],
        'culture' => ['en' => 'en/culture-analysis', 'tr' => 'tr/kultur-analizi'],
        'ethics' => ['en' => 'en/ethics-counsel', 'tr' => 'tr/etik-danismanlik'],
        'blog' => ['en' => 'en/blog', 'tr' => 'tr/yazilar'],
        'solutions' => ['en' => 'en/solutions', 'tr' => 'tr/cozumler'],
        'events' => ['en' => 'en/events', 'tr' => 'tr/etkinlikler'],
        'contact' => ['en' => 'en/contact', 'tr' => 'tr/iletisim'],
        'demo' => ['en' => 'en/demo', 'tr' => 'tr/demo'],
        'story' => ['en' => 'en/our-story', 'tr' => 'tr/hikayemiz'],
        'faq' => ['en' => 'en/faq', 'tr' => 'tr/sss'],
        'security' => ['en' => 'en/security', 'tr' => 'tr/guvenlik'],
        'privacy' => ['en' => 'en/privacy-policy', 'tr' => 'tr/gizlilik-politikasi'],
    ];

    if (!empty($paths[$key][$lang])) {
        $page = get_page_by_path($paths[$key][$lang]);
        if ($page) {
            return get_permalink($page);
        }
    }

    return home_url('/' . ($paths[$key]['en'] ?? ''));
}

function myliba_nav_items(): array
{
    return [
        'products' => __('Products', 'myliba'),
        'academy' => __('Academy', 'myliba'),
        'solutions' => __('Solutions', 'myliba'),
        'story' => __('Our Story', 'myliba'),
        'blog' => __('Blog', 'myliba'),
        'contact' => __('Contact', 'myliba'),
    ];
}

function myliba_portal_url(): string
{
    return 'https://portal.myliba.com/';
}

function myliba_header_menu(): array
{
    return [
        ['key' => 'products', 'label' => __('Products', 'myliba'), 'url' => myliba_page_url('products')],
        ['key' => 'academy', 'label' => __('Academy', 'myliba'), 'url' => myliba_page_url('academy')],
        ['key' => 'solutions', 'label' => __('Solutions', 'myliba'), 'url' => myliba_page_url('solutions')],
        ['key' => 'story', 'label' => __('Our Story', 'myliba'), 'url' => myliba_page_url('story')],
        ['key' => 'blog', 'label' => __('Blog', 'myliba'), 'url' => myliba_page_url('blog')],
        ['key' => 'contact', 'label' => __('Contact', 'myliba'), 'url' => myliba_page_url('contact')],
    ];
}

function myliba_url_path(string $url): string
{
    $path = (string) wp_parse_url($url, PHP_URL_PATH);
    $path = '/' . trim($path, '/');

    return untrailingslashit($path);
}

function myliba_header_menu_item_is_active(string $key, string $url): bool
{
    $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '/';
    $current_path = myliba_url_path(home_url($request_uri));
    $item_path = myliba_url_path($url);

    if ($current_path === $item_path) {
        return true;
    }

    return match ($key) {
        'blog' => is_home() || is_singular('post') || is_category() || is_tag(),
        'solutions' => is_singular('myliba_product') || is_post_type_archive('myliba_product'),
        default => false,
    };
}

function myliba_mega_menu_products(): WP_Query
{
    $query = myliba_get_entries('myliba_product', 10);

    if ($query->have_posts()) {
        return $query;
    }

    return myliba_get_entries('myliba_product', 10, ['meta_query' => []]);
}

function myliba_language_links(): array
{
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(['raw' => 1]);

        if (is_array($languages) && $languages) {
            return array_map(static function (array $language): array {
                return [
                    'label' => strtoupper((string) $language['slug']),
                    'url' => (string) $language['url'],
                    'active' => !empty($language['current_lang']),
                ];
            }, $languages);
        }
    }

    $links = [];
    foreach (['en', 'tr'] as $language) {
        $page = get_page_by_path($language);
        $links[] = [
            'label' => strtoupper($language),
            'url' => $page ? get_permalink($page) : home_url('/' . $language . '/'),
            'active' => myliba_current_language() === $language,
        ];
    }

    return $links;
}

function myliba_language_flag(string $label): string
{
    return match (strtolower($label)) {
        'tr' => '🇹🇷',
        'en' => '🇬🇧',
        default => strtoupper(substr($label, 0, 2)),
    };
}

function myliba_post_language_filter(\WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query() || function_exists('pll_current_language')) {
        return;
    }

    if ($query->is_home() || $query->is_archive()) {
        $query->set('meta_query', [
            [
                'key' => '_myliba_language',
                'value' => myliba_current_language(),
                'compare' => '=',
            ],
        ]);
    }
}
add_action('pre_get_posts', 'myliba_post_language_filter');

function myliba_excerpt(int $post_id = 0, int $words = 28): string
{
    $post = get_post($post_id ?: get_the_ID());
    if (!$post) {
        return '';
    }

    $source = $post->post_excerpt ?: $post->post_content;

    return wp_trim_words(wp_strip_all_tags($source), $words);
}

function myliba_get_entries(string $post_type, int $limit = 6, array $args = []): WP_Query
{
    $query_args = array_merge([
        'post_type' => $post_type,
        'posts_per_page' => $limit,
        'meta_key' => '_myliba_order',
        'orderby' => ['meta_value_num' => 'ASC', 'date' => 'DESC'],
        'order' => 'ASC',
    ], $args);

    if (!function_exists('pll_current_language') && !isset($query_args['meta_query'])) {
        $query_args['meta_query'] = [
            [
                'key' => '_myliba_language',
                'value' => myliba_current_language(),
                'compare' => '=',
            ],
        ];
    }

    return new WP_Query($query_args);
}

function myliba_lines(string $value): array
{
    $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];
    $lines = array_map('trim', $lines);

    return array_values(array_filter($lines, static fn ($line) => $line !== ''));
}

function myliba_faq_pairs(string $value): array
{
    $pairs = [];
    foreach (myliba_lines($value) as $line) {
        [$question, $answer] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        if ($question && $answer) {
            $pairs[] = [
                'question' => myliba_translate_text($question),
                'answer' => myliba_translate_text($answer),
            ];
        }
    }

    return $pairs;
}

function myliba_reading_time(int $post_id = 0): int
{
    $post = get_post($post_id ?: get_the_ID());
    $words = $post ? str_word_count(wp_strip_all_tags($post->post_content)) : 0;

    return max(1, (int) ceil($words / 220));
}

function myliba_demo_url(): string
{
    $page_url = myliba_page_url('demo');

    return $page_url ?: (string) myliba_option('demo_url', '/en/demo/');
}

function myliba_brand_link(string $modifier = ''): void
{
    $classes = trim('site-brand ' . $modifier);
    echo '<a class="' . esc_attr($classes) . '" href="' . esc_url(home_url('/')) . '" aria-label="' . esc_attr(get_bloginfo('name')) . '">';

    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        echo wp_get_attachment_image($custom_logo_id, 'full', false, [
            'class' => 'site-brand__logo',
            'alt' => get_bloginfo('name'),
        ]);
    } else {
        echo '<span class="site-brand__mark" aria-hidden="true"><span></span><span></span><span></span></span>';
        echo '<span class="site-brand__text">Myliba</span>';
    }

    echo '</a>';
}

function myliba_home_value(string $key, mixed $fallback = ''): mixed
{
    if (is_string($fallback)) {
        $fallback = myliba_translate_text($fallback);
    }

    $value = myliba_meta('_myliba_home_' . $key, get_queried_object_id(), $fallback);

    return is_string($value) ? myliba_translate_text($value) : $value;
}

function myliba_home_lines(string $key, array $fallback = []): array
{
    $fallback = array_map(static fn ($line) => is_string($line) ? myliba_translate_text($line) : $line, $fallback);
    $value = (string) myliba_home_value($key, implode("\n", $fallback));

    return array_map('myliba_translate_text', myliba_lines($value));
}

function myliba_home_rows(string $key, array $fallback = []): array
{
    $rows = [];
    $fallback = array_map(static function (array $row): array {
        return array_map(static fn ($cell) => is_string($cell) ? myliba_translate_text($cell) : $cell, $row);
    }, $fallback);
    $raw_rows = myliba_home_lines($key, array_map(static fn ($row) => implode('|', $row), $fallback));

    foreach ($raw_rows as $row) {
        $rows[] = array_map(static fn ($cell) => myliba_translate_text(trim($cell)), explode('|', $row));
    }

    return $rows;
}

function myliba_home_section_definitions(): array
{
    return [
        'hero' => __('Hero + dashboard preview', 'myliba'),
        'trust_bar' => __('Trust bar', 'myliba'),
        'problem' => __('Problem cards', 'myliba'),
        'solutions' => __('Strategy flow', 'myliba'),
        'products' => __('Product grid', 'myliba'),
        'academy' => __('Academy block', 'myliba'),
        'role_gains' => __('Role gains', 'myliba'),
        'outcomes' => __('Business outcomes', 'myliba'),
        'resources' => __('Resources / blog', 'myliba'),
        'faq' => __('Homepage FAQ', 'myliba'),
        'final_cta' => __('Final CTA', 'myliba'),
    ];
}

function myliba_home_default_sections(): array
{
    $sections = [];
    $order = 10;

    foreach (array_keys(myliba_home_section_definitions()) as $key) {
        $sections[$key] = [
            'key' => $key,
            'enabled' => true,
            'order' => $order,
        ];
        $order += 10;
    }

    return $sections;
}

function myliba_home_sections(int $post_id = 0): array
{
    $post_id = $post_id ?: get_queried_object_id();
    $definitions = myliba_home_section_definitions();
    $sections = myliba_home_default_sections();
    $raw = $post_id ? get_post_meta($post_id, '_myliba_home_builder', true) : '';
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
