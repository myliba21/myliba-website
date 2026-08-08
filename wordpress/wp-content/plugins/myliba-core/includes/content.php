<?php

namespace Myliba\Core\Content;

use Myliba\Core\Options;

if (!defined('ABSPATH')) {
    exit;
}

const LEGACY_OPTION_NAME = 'myliba_content_overrides';
const LEGACY_SCHEMA_OPTION = 'myliba_content_overrides_schema_version';
const LEGACY_SCHEMA_VERSION = 3;
const INTERFACE_OPTION_NAME = 'myliba_interface_text_overrides';

function boot(): void
{
    add_action('admin_menu', __NAMESPACE__ . '\\register_menu', 20);
    add_action('admin_post_myliba_save_site_texts', __NAMESPACE__ . '\\save_site_texts');
    add_action('admin_init', __NAMESPACE__ . '\\migrate_legacy_overrides', 1);
}

function register_menu(): void
{
    add_submenu_page(
        'myliba-settings',
        __('Site Texts — TR / EN', 'myliba'),
        __('Site Texts — TR / EN', 'myliba'),
        'manage_options',
        'myliba-content',
        __NAMESPACE__ . '\\render_page'
    );
}

/**
 * Keep real edits from the former source-string catalog, but remove the
 * materialized defaults that made every value look like an override.
 */
function migrate_legacy_overrides(): void
{
    if ((int) get_option(LEGACY_SCHEMA_OPTION, 0) >= LEGACY_SCHEMA_VERSION) {
        return;
    }

    $stored = get_option(LEGACY_OPTION_NAME, []);
    if (!is_array($stored)) {
        $stored = [];
    }

    if ($stored !== []) {
        add_option('myliba_content_overrides_pre_site_texts_v' . LEGACY_SCHEMA_VERSION, $stored, '', false);
    }

    $translations = function_exists('myliba_translation_defaults') ? \myliba_translation_defaults() : [];
    $overrides = [];

    foreach ($stored as $source => $values) {
        if (!is_string($source) || !is_array($values)) {
            continue;
        }

        $defaults = [
            'en' => $source,
            'tr' => isset($translations[$source]) && is_string($translations[$source])
                ? $translations[$source]
                : $source,
        ];

        foreach (['en', 'tr'] as $locale) {
            $value = $values[$locale] ?? null;
            if (
                is_string($value)
                && trim($value) !== ''
                && $value !== $defaults[$locale]
                && $value !== $source
            ) {
                $overrides[$source][$locale] = $value;
            }
        }
    }

    $catalog = interface_catalog();
    $interface_overrides = interface_overrides();
    foreach ($catalog as $key => $definition) {
        $source = $definition['source'];
        if (!isset($overrides[$source])) {
            continue;
        }

        foreach (['en', 'tr'] as $locale) {
            $value = $overrides[$source][$locale] ?? null;
            if (is_string($value) && $value !== $definition['defaults'][$locale]) {
                $interface_overrides[$key][$locale] = $value;
            }
        }
        unset($overrides[$source]);
    }

    update_option(LEGACY_OPTION_NAME, $overrides, false);
    update_option(INTERFACE_OPTION_NAME, $interface_overrides, false);
    update_option(LEGACY_SCHEMA_OPTION, LEGACY_SCHEMA_VERSION, false);
}

function interface_key(string $source): string
{
    return 'ui_' . substr(hash('sha256', $source), 0, 16);
}

function interface_translation_pairs(): array
{
    static $pairs = null;
    if (is_array($pairs)) {
        return $pairs;
    }

    $pairs = [];
    $turkish_defaults = function_exists('myliba_translation_defaults') ? \myliba_translation_defaults() : [];
    foreach ($turkish_defaults as $source => $turkish) {
        if (is_string($source) && is_string($turkish)) {
            $pairs[$source] = ['en' => $source, 'tr' => $turkish];
        }
    }

    $pairs = array_replace($pairs, [
        '%s süreci' => ['en' => '%s process', 'tr' => '%s süreci'],
        '30 dakikalık keşif görüşmesi' => ['en' => '30-minute discovery call', 'tr' => '30 dakikalık keşif görüşmesi'],
        'Akademi' => ['en' => 'Academy', 'tr' => 'Akademi'],
        'All categories' => ['en' => 'All categories', 'tr' => 'Tüm kategoriler'],
        'Back to home' => ['en' => 'Back to home', 'tr' => 'Ana sayfaya dön'],
        'Beklenen kazanımlar' => ['en' => 'Expected outcomes', 'tr' => 'Beklenen kazanımlar'],
        'Benefits' => ['en' => 'Benefits', 'tr' => 'Faydalar'],
        'Birlikte geliştiğimiz kurumlar' => ['en' => 'Organizations we grow with', 'tr' => 'Birlikte geliştiğimiz kurumlar'],
        'Biz Kimiz' => ['en' => 'About Us', 'tr' => 'Biz Kimiz'],
        'Category' => ['en' => 'Category', 'tr' => 'Kategori'],
        'Choose slide' => ['en' => 'Choose slide', 'tr' => 'Slayt seçin'],
        'Client logos' => ['en' => 'Client logos', 'tr' => 'Müşteri logoları'],
        'Close' => ['en' => 'Close', 'tr' => 'Kapat'],
        'Connected product capabilities' => ['en' => 'Connected product capabilities', 'tr' => 'Bağlantılı ürün yetkinlikleri'],
        'Continue' => ['en' => 'Continue', 'tr' => 'Devam et'],
        'Continue exploring performance culture.' => ['en' => 'Continue exploring performance culture.', 'tr' => 'Performans kültürünü keşfetmeye devam edin.'],
        'Date' => ['en' => 'Date', 'tr' => 'Tarih'],
        'Designed to make the behavior visible, repeatable and measurable.' => ['en' => 'Designed to make the behavior visible, repeatable and measurable.', 'tr' => 'Davranışı görünür, tekrarlanabilir ve ölçülebilir kılmak için tasarlandı.'],
        'Değişimi birlikte yöneten ekipler için.' => ['en' => 'For teams that lead change together.', 'tr' => 'Değişimi birlikte yöneten ekipler için.'],
        'Dismiss promotion' => ['en' => 'Dismiss promotion', 'tr' => 'Duyuruyu kapat'],
        'Dönüşüm ekipleri' => ['en' => 'Transformation teams', 'tr' => 'Dönüşüm ekipleri'],
        'Dört uzmanlık alanını tek noktadan keşfedin.' => ['en' => 'Discover four areas of expertise in one place.', 'tr' => 'Dört uzmanlık alanını tek noktadan keşfedin.'],
        'e-Kitaplar' => ['en' => 'e-Books', 'tr' => 'e-Kitaplar'],
        'Etkinlikler' => ['en' => 'Events', 'tr' => 'Etkinlikler'],
        'Event details' => ['en' => 'Event details', 'tr' => 'Etkinlik detayları'],
        'Events' => ['en' => 'Events', 'tr' => 'Etkinlikler'],
        'Explore Myliba products, read the blog, or request a demo to see the platform.' => ['en' => 'Explore Myliba products, read the blog, or request a demo to see the platform.', 'tr' => 'Myliba ürünlerini keşfedin, blogu okuyun veya platformu görmek için demo talep edin.'],
        'Farklı sektörlerden ekiplerin dönüşüm yolculuğuna eşlik ediyoruz.' => ['en' => 'We support the transformation journeys of teams across different industries.', 'tr' => 'Farklı sektörlerden ekiplerin dönüşüm yolculuğuna eşlik ediyoruz.'],
        'Filter' => ['en' => 'Filter', 'tr' => 'Filtrele'],
        'Gelişimi tek seferlik bir müdahaleden çıkarıp, kurumun çalışma biçimine yerleştirin.' => ['en' => 'Move development beyond a one-time intervention and embed it into how the organization works.', 'tr' => 'Gelişimi tek seferlik bir müdahaleden çıkarıp, kurumun çalışma biçimine yerleştirin.'],
        'Gelişim kaynakları' => ['en' => 'Development resources', 'tr' => 'Gelişim kaynakları'],
        'Gelişim Merkezi' => ['en' => 'Development Center', 'tr' => 'Gelişim Merkezi'],
        'Gelişim Merkezi menüsü' => ['en' => 'Development Center menu', 'tr' => 'Gelişim Merkezi menüsü'],
        'Gelişim zihniyetini sürekli yeni bilgi ve tecrübeyle besleyin.' => ['en' => 'Continuously strengthen the development mindset with new knowledge and experience.', 'tr' => 'Gelişim zihniyetini sürekli yeni bilgi ve tecrübeyle besleyin.'],
        'Görüşme planlayın' => ['en' => 'Schedule a meeting', 'tr' => 'Görüşme planlayın'],
        'Güncel araştırmalar ve içgörüler.' => ['en' => 'Current research and insights.', 'tr' => 'Güncel araştırmalar ve içgörüler.'],
        'Güncel kaynakları keşfedin.' => ['en' => 'Discover the latest resources.', 'tr' => 'Güncel kaynakları keşfedin.'],
        'Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim ritminin parçasıdır.' => ['en' => 'Each stage informs the next; design, implementation, and follow-up are part of the same development rhythm.', 'tr' => 'Her aşama bir sonraki adımı besler; tasarım, uygulama ve takip aynı gelişim ritminin parçasıdır.'],
        'Insights on OKR, KPI, CFR, performance culture, feedback and leadership routines.' => ['en' => 'Insights on OKR, KPI, CFR, performance culture, feedback and leadership routines.', 'tr' => 'OKR, KPI, CFR, performans kültürü, geri bildirim ve liderlik rutinleri üzerine içgörüler.'],
        'In this article' => ['en' => 'In this article', 'tr' => 'Bu yazıda'],
        'Kimler için?' => ['en' => 'Who is it for?', 'tr' => 'Kimler için?'],
        "Kuruma özel.\nİşin içinde.\nÖlçülebilir." => ['en' => "Tailored to your organization.\nEmbedded in the work.\nMeasurable.", 'tr' => "Kuruma özel.\nİşin içinde.\nÖlçülebilir."],
        'Kuruma özel gelişim yolculukları.' => ['en' => 'Tailored development journeys.', 'tr' => 'Kuruma özel gelişim yolculukları.'],
        'Kuruma özel tasarım' => ['en' => 'Tailored design', 'tr' => 'Kuruma özel tasarım'],
        'Kurumların hedef, kültür ve liderlik pratiklerini birlikte güçlendiren deneyimler tasarlıyoruz.' => ['en' => 'We design experiences that strengthen organizational goals, culture, and leadership practices together.', 'tr' => 'Kurumların hedef, kültür ve liderlik pratiklerini birlikte güçlendiren deneyimler tasarlıyoruz.'],
        'Kurumunuzun hedeflerini dinleyelim; doğru programı, kapsamı ve çalışma modelini birlikte netleştirelim.' => ['en' => 'Let us understand your organization’s goals and define the right program, scope, and working model together.', 'tr' => 'Kurumunuzun hedeflerini dinleyelim; doğru programı, kapsamı ve çalışma modelini birlikte netleştirelim.'],
        'Kültürü, hedefleri ve iş sonuçlarını birlikte geliştirin.' => ['en' => 'Develop culture, goals, and business outcomes together.', 'tr' => 'Kültürü, hedefleri ve iş sonuçlarını birlikte geliştirin.'],
        'Kültürü dört kritik göstergeyle görünür kılın.' => ['en' => 'Make culture visible through four critical indicators.', 'tr' => 'Kültürü dört kritik göstergeyle görünür kılın.'],
        'Language switcher' => ['en' => 'Language switcher', 'tr' => 'Dil seçici'],
        'Liderlik ekipleri' => ['en' => 'Leadership teams', 'tr' => 'Liderlik ekipleri'],
        'Location' => ['en' => 'Location', 'tr' => 'Konum'],
        'min read' => ['en' => 'min read', 'tr' => 'dk okuma'],
        'Mobile conversion actions' => ['en' => 'Mobile conversion actions', 'tr' => 'Mobil dönüşüm aksiyonları'],
        'Myliba gelişim yolculuğu' => ['en' => 'Myliba development journey', 'tr' => 'Myliba gelişim yolculuğu'],
        'Myliba highlights' => ['en' => 'Myliba highlights', 'tr' => 'Myliba’dan öne çıkanlar'],
        'Myliba in numbers' => ['en' => 'Myliba in numbers', 'tr' => 'Rakamlarla Myliba'],
        'Myliba solution' => ['en' => 'Myliba solution', 'tr' => 'Myliba çözümü'],
        'Myliba yaklaşımı' => ['en' => 'The Myliba approach', 'tr' => 'Myliba yaklaşımı'],
        'Next slide' => ['en' => 'Next slide', 'tr' => 'Sonraki slayt'],
        'Next testimonial' => ['en' => 'Next testimonial', 'tr' => 'Sonraki referans'],
        'No articles found.' => ['en' => 'No articles found.', 'tr' => 'Yazı bulunamadı.'],
        'No content found.' => ['en' => 'No content found.', 'tr' => 'İçerik bulunamadı.'],
        'Performance management capabilities' => ['en' => 'Performance management capabilities', 'tr' => 'Performans yönetimi yetkinlikleri'],
        'Previous slide' => ['en' => 'Previous slide', 'tr' => 'Önceki slayt'],
        'Previous testimonial' => ['en' => 'Previous testimonial', 'tr' => 'Önceki referans'],
        'Problem' => ['en' => 'Problem', 'tr' => 'Sorun'],
        'Product modules for performance culture.' => ['en' => 'Product modules for performance culture.', 'tr' => 'Performans kültürü için ürün modülleri.'],
        'Programla birlikte ne değişir?' => ['en' => 'What changes with the program?', 'tr' => 'Programla birlikte ne değişir?'],
        'Programs that make OKR and performance routines sustainable.' => ['en' => 'Programs that make OKR and performance routines sustainable.', 'tr' => 'OKR ve performans rutinlerini sürdürülebilir kılan programlar.'],
        'Programı bir bakışta' => ['en' => 'Program at a glance', 'tr' => 'Programı bir bakışta'],
        'Programı birlikte tasarlayalım' => ['en' => 'Let’s design the program together', 'tr' => 'Programı birlikte tasarlayalım'],
        'Programın temel özellikleri' => ['en' => 'Core features of the program', 'tr' => 'Programın temel özellikleri'],
        'Questions teams ask before implementation.' => ['en' => 'Questions teams ask before implementation.', 'tr' => 'Ekiplerin uygulama öncesinde sorduğu sorular.'],
        'Raporlar ve Trendler' => ['en' => 'Reports and Trends', 'tr' => 'Raporlar ve Trendler'],
        'Register' => ['en' => 'Register', 'tr' => 'Kayıt ol'],
        'Rehberler ve uygulama kaynakları.' => ['en' => 'Guides and practical resources.', 'tr' => 'Rehberler ve uygulama kaynakları.'],
        'Related modules' => ['en' => 'Related modules', 'tr' => 'İlgili modüller'],
        'Related reading' => ['en' => 'Related reading', 'tr' => 'İlgili içerikler'],
        'Request a demo and see how Myliba connects strategy, actions and performance routines.' => ['en' => 'Request a demo and see how Myliba connects strategy, actions and performance routines.', 'tr' => 'Demo talep edin ve Myliba’nın strateji, aksiyonlar ve performans rutinlerini nasıl birbirine bağladığını görün.'],
        'Search resources' => ['en' => 'Search resources', 'tr' => 'Kaynaklarda ara'],
        'See this flow in a real demo.' => ['en' => 'See this flow in a real demo.', 'tr' => 'Bu akışı gerçek bir demoda görün.'],
        'Simülasyon ve koçluk deneyimleri.' => ['en' => 'Simulation and coaching experiences.', 'tr' => 'Simülasyon ve koçluk deneyimleri.'],
        'Stratejiden uygulamaya destek.' => ['en' => 'Support from strategy to implementation.', 'tr' => 'Stratejiden uygulamaya destek.'],
        'Sürekli gelişim ve dönüşüm merkezi' => ['en' => 'Continuous development and transformation center', 'tr' => 'Sürekli gelişim ve dönüşüm merkezi'],
        'This page is not available, but the next step is clear.' => ['en' => 'This page is not available, but the next step is clear.', 'tr' => 'Bu sayfa kullanılamıyor, ancak sonraki adım belli.'],
        'Turn this into practice.' => ['en' => 'Turn this into practice.', 'tr' => 'Bunu uygulamaya dönüştürün.'],
        'Tüm içerikler' => ['en' => 'All content', 'tr' => 'Tüm içerikler'],
        'Tüm çözümler' => ['en' => 'All solutions', 'tr' => 'Tüm çözümler'],
        'Tüm çözümleri görün' => ['en' => 'View all solutions', 'tr' => 'Tüm çözümleri görün'],
        'Uzman yazıları ve pratik öneriler.' => ['en' => 'Expert articles and practical recommendations.', 'tr' => 'Uzman yazıları ve pratik öneriler.'],
        'Veriye dayalı kültür içgörüleri.' => ['en' => 'Data-driven culture insights.', 'tr' => 'Veriye dayalı kültür içgörüleri.'],
        'Webinar, atölye ve buluşmalar.' => ['en' => 'Webinars, workshops, and events.', 'tr' => 'Webinar, atölye ve buluşmalar.'],
        'We will map your current performance routines and show the product modules that fit.' => ['en' => 'We will map your current performance routines and show the product modules that fit.', 'tr' => 'Mevcut performans rutinlerinizi haritalandırıp uygun ürün modüllerini göstereceğiz.'],
        'What changes with Myliba?' => ['en' => 'What changes with Myliba?', 'tr' => 'Myliba ile ne değişir?'],
        'Workshops, webinars, and sessions' => ['en' => 'Workshops, webinars, and sessions', 'tr' => 'Atölyeler, webinarlar ve oturumlar'],
        'Yazılım' => ['en' => 'Software', 'tr' => 'Yazılım'],
        'Kurumsal Gelişim Programları' => ['en' => 'Corporate Development Programs', 'tr' => 'Kurumsal Gelişim Programları'],
        'Simülasyonlar ve Takım Koçluğu' => ['en' => 'Simulations and Team Coaching', 'tr' => 'Simülasyonlar ve Takım Koçluğu'],
        'Danışmanlık' => ['en' => 'Consulting', 'tr' => 'Danışmanlık'],
        'Kültür Analizi' => ['en' => 'Culture Analysis', 'tr' => 'Kültür Analizi'],
        'Çalışma modeli' => ['en' => 'Working model', 'tr' => 'Çalışma modeli'],
        'Çalışma modelini inceleyin' => ['en' => 'Explore the working model', 'tr' => 'Çalışma modelini inceleyin'],
        'Çözümlerimiz' => ['en' => 'Our Solutions', 'tr' => 'Çözümlerimiz'],
        'Güvenlik ve Yasal' => ['en' => 'Security & Legal', 'tr' => 'Güvenlik ve Yasal'],
        'Güvenlik' => ['en' => 'Security', 'tr' => 'Güvenlik'],
        'SSS' => ['en' => 'FAQ', 'tr' => 'SSS'],
        'KVKK Aydınlatma Metni' => ['en' => 'Privacy Policy', 'tr' => 'KVKK Aydınlatma Metni'],
        'KVKK ve GDPR' => ['en' => 'KVKK and GDPR', 'tr' => 'KVKK ve GDPR'],
        'Çerez Politikası' => ['en' => 'Cookie Policy', 'tr' => 'Çerez Politikası'],
        'Kullanım Şartları' => ['en' => 'Terms of Use', 'tr' => 'Kullanım Şartları'],
        'Ölçülebilir takip' => ['en' => 'Measurable follow-up', 'tr' => 'Ölçülebilir takip'],
        'Ölçüm alanları' => ['en' => 'Measurement areas', 'tr' => 'Ölçüm alanları'],
        'İhtiyacınıza uygun yolculuğu birlikte tasarlayalım.' => ['en' => 'Let’s design the right journey for your needs together.', 'tr' => 'İhtiyacınıza uygun yolculuğu birlikte tasarlayalım.'],
        'İhtiyacınıza uygun çözümü bulun.' => ['en' => 'Find the right solution for your needs.', 'tr' => 'İhtiyacınıza uygun çözümü bulun.'],
        'İhtiyacınızı birlikte değerlendirelim' => ['en' => 'Let’s assess your needs together', 'tr' => 'İhtiyacınızı birlikte değerlendirelim'],
        'İletişim' => ['en' => 'Contact', 'tr' => 'İletişim'],
        'İnsan ve kültür ekipleri' => ['en' => 'People and culture teams', 'tr' => 'İnsan ve kültür ekipleri'],
        'İçerikler, araştırmalar ve etkinlikler.' => ['en' => 'Content, research, and events.', 'tr' => 'İçerikler, araştırmalar ve etkinlikler.'],
        'İçerikleri inceleyin' => ['en' => 'Explore the content', 'tr' => 'İçerikleri inceleyin'],
        'İşbaşı uygulama' => ['en' => 'On-the-job practice', 'tr' => 'İşbaşı uygulama'],
    ]);

    return $pairs;
}

/**
 * Discover only explicit frontend component text. Unlike the former catalog,
 * this excludes gettext admin labels, URLs, and removed database keys.
 */
function interface_catalog(): array
{
    $theme_dir = get_template_directory();
    $paths = array_merge(
        glob($theme_dir . '/*.php') ?: [],
        glob($theme_dir . '/template-parts/*.php') ?: [],
        [MYLIBA_CORE_DIR . 'includes/forms.php']
    );
    $translation_pairs = interface_translation_pairs();
    $catalog = [];
    $pattern = '~myliba_text\(\s*(["\'])((?:\\\\.|(?!\1).)*)\1\s*\)~s';

    foreach (array_unique($paths) as $path) {
        if (!is_readable($path)) {
            continue;
        }

        $contents = file_get_contents($path);
        if (!is_string($contents) || !preg_match_all($pattern, $contents, $matches)) {
            continue;
        }

        foreach ($matches[2] as $encoded_source) {
            $source = stripcslashes((string) $encoded_source);
            if ($source === '') {
                continue;
            }

            $key = interface_key($source);
            if (!isset($catalog[$key])) {
                $catalog[$key] = [
                    'source' => $source,
                    'type' => str_contains($source, "\n") || strlen($source) > 110 ? 'textarea' : 'text',
                    'defaults' => [
                        'en' => isset($translation_pairs[$source]['en']) ? (string) $translation_pairs[$source]['en'] : $source,
                        'tr' => isset($translation_pairs[$source]['tr']) ? (string) $translation_pairs[$source]['tr'] : $source,
                    ],
                    'locations' => [],
                ];
            }

            $relative_path = str_replace([trailingslashit($theme_dir), MYLIBA_CORE_DIR], ['', 'myliba-core/'], $path);
            $catalog[$key]['locations'][$relative_path] = $relative_path;
        }
    }

    foreach ($catalog as &$definition) {
        $definition['locations'] = array_values($definition['locations']);
    }
    unset($definition);

    uasort($catalog, static fn (array $left, array $right): int => strnatcasecmp($left['source'], $right['source']));
    return $catalog;
}

function interface_overrides(): array
{
    $overrides = get_option(INTERFACE_OPTION_NAME, []);
    return is_array($overrides) ? $overrides : [];
}

function interface_override(string $source, string $locale): ?string
{
    $key = interface_key($source);
    $overrides = interface_overrides();
    $value = $overrides[$key][$locale] ?? null;
    return is_string($value) && trim($value) !== '' ? $value : null;
}

function format_placeholders(string $value): array
{
    preg_match_all('/%(?:\d+\$)?[-+0-9\'.]*([bcdeEfFgGosuxX])/', $value, $matches);
    $types = $matches[1] ?? [];
    sort($types);
    return $types;
}

function save_interface_texts(array $input): int
{
    $saved = [];
    $existing = interface_overrides();
    $invalid = 0;

    foreach (interface_catalog() as $key => $definition) {
        foreach (['en', 'tr'] as $locale) {
            $raw = $input[$key][$locale] ?? '';
            $value = ($definition['type'] ?? 'text') === 'textarea'
                ? sanitize_textarea_field($raw)
                : sanitize_text_field($raw);
            if (trim($value) === '') {
                $value = $definition['defaults'][$locale];
            }

            if (format_placeholders($value) !== format_placeholders($definition['source'])) {
                if (isset($existing[$key][$locale])) {
                    $saved[$key][$locale] = $existing[$key][$locale];
                }
                $invalid++;
                continue;
            }

            if (trim($value) !== '' && $value !== $definition['defaults'][$locale]) {
                $saved[$key][$locale] = $value;
            }
        }
    }

    update_option(INTERFACE_OPTION_NAME, $saved, false);
    return $invalid;
}

function legacy_overrides(): array
{
    $overrides = get_option(LEGACY_OPTION_NAME, []);
    return is_array($overrides) ? $overrides : [];
}

function legacy_override(string $source, string $locale): ?string
{
    $overrides = legacy_overrides();
    $value = $overrides[$source][$locale] ?? null;
    return is_string($value) && trim($value) !== '' ? $value : null;
}

/**
 * Resolve managed interface text first, then legacy edits, the active
 * WordPress language pack, and finally the built-in bilingual defaults.
 */
function materialize(string $source, string $locale): string
{
    $interface_value = interface_override($source, $locale);
    if ($interface_value !== null) {
        return $interface_value;
    }

    $override = legacy_override($source, $locale);
    if ($override !== null) {
        return $override;
    }

    $translated = translate($source, 'myliba');
    if (is_string($translated) && $translated !== $source) {
        return $translated;
    }

    $translation_pairs = interface_translation_pairs();
    if (isset($translation_pairs[$source][$locale]) && is_string($translation_pairs[$source][$locale])) {
        return $translation_pairs[$source][$locale];
    }

    if ($locale === 'tr') {
        $translations = function_exists('myliba_translation_defaults') ? \myliba_translation_defaults() : [];
        if (isset($translations[$source]) && is_string($translations[$source]) && trim($translations[$source]) !== '') {
            return $translations[$source];
        }
    }

    return $source;
}

function save_site_texts(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to manage these settings.', 'myliba'));
    }

    check_admin_referer('myliba_site_texts');
    $input = isset($_POST['site_texts']) && is_array($_POST['site_texts'])
        ? wp_unslash($_POST['site_texts'])
        : [];
    $interface_input = isset($_POST['interface_texts']) && is_array($_POST['interface_texts'])
        ? wp_unslash($_POST['interface_texts'])
        : [];

    Options\save_localized_values($input);
    $invalid_placeholders = save_interface_texts($interface_input);

    wp_safe_redirect(add_query_arg([
        'page' => 'myliba-content',
        'updated' => '1',
        'invalid_placeholders' => $invalid_placeholders,
    ], admin_url('admin.php')));
    exit;
}

function render_field(
    string $name_root,
    string $key,
    string $locale,
    string $type,
    string $value,
    string $default
): void
{
    $id = 'myliba-' . $name_root . '-' . $key . '-' . $locale;
    $name = $name_root . '[' . $key . '][' . $locale . ']';
    $is_textarea = $type === 'textarea';
    ?>
    <label for="<?php echo esc_attr($id); ?>" class="myliba-site-texts__locale">
        <span><?php echo esc_html($locale === 'en' ? 'English' : 'Türkçe'); ?></span>
        <?php if ($is_textarea) : ?>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" placeholder="<?php echo esc_attr($default); ?>"><?php echo esc_textarea($value); ?></textarea>
        <?php else : ?>
            <input
                class="widefat"
                id="<?php echo esc_attr($id); ?>"
                name="<?php echo esc_attr($name); ?>"
                value="<?php echo esc_attr($value); ?>"
                placeholder="<?php echo esc_attr($default); ?>"
                <?php if ($type === 'url') : ?>inputmode="url" spellcheck="false"<?php endif; ?>
            >
        <?php endif; ?>
    </label>
    <?php
}

function render_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = Options\get_all();
    $schema = Options\localized_schema();
    $groups = [
        'actions' => __('Global actions', 'myliba'),
        'footer' => __('Footer', 'myliba'),
        'promo' => __('Promo banner', 'myliba'),
        'forms' => __('Forms', 'myliba'),
    ];
    $interface_catalog = interface_catalog();
    $interface_overrides = interface_overrides();
    ?>
    <div class="wrap myliba-site-texts">
        <h1><?php esc_html_e('Site Texts — Turkish / English', 'myliba'); ?></h1>
        <p><?php esc_html_e('Edit reusable global content, form labels, and detected frontend component text here. Page and post copy still belongs to its translated WordPress entry.', 'myliba'); ?></p>
        <p><?php esc_html_e('Empty fields reset to the built-in value when saved. Relative paths such as /tr/iletisim/ and full https:// URLs are both supported.', 'myliba'); ?></p>

        <?php if (isset($_GET['updated'])) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Site texts saved.', 'myliba'); ?></p></div>
        <?php endif; ?>
        <?php if (!empty($_GET['invalid_placeholders'])) : ?>
            <div class="notice notice-error"><p><?php esc_html_e('Some interface translations were not saved because required placeholders such as %s or %1$s were changed.', 'myliba'); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="myliba_save_site_texts">
            <?php wp_nonce_field('myliba_site_texts'); ?>

            <div class="myliba-site-texts__toolbar">
                <div class="myliba-site-texts__toolbar-search">
                    <label for="myliba-site-text-search"><?php esc_html_e('Search site texts', 'myliba'); ?></label>
                    <input class="regular-text" type="search" id="myliba-site-text-search" placeholder="<?php esc_attr_e('Search source, translation, key or component…', 'myliba'); ?>">
                </div>
                <span id="myliba-site-text-search-status" aria-live="polite"></span>
            </div>

            <?php foreach ($groups as $group => $group_label) : ?>
                <section class="myliba-site-texts__section">
                    <h2><?php echo esc_html($group_label); ?></h2>
                    <?php foreach ($schema as $key => $definition) : ?>
                        <?php if (($definition['group'] ?? '') !== $group) {
                            continue;
                        } ?>
                        <div class="myliba-site-texts__field">
                            <div class="myliba-site-texts__heading">
                                <strong><?php echo esc_html($definition['label']); ?></strong>
                                <code><?php echo esc_html($key); ?></code>
                            </div>
                            <div class="myliba-site-texts__locales">
                                <?php foreach (['en', 'tr'] as $locale) : ?>
                                    <?php render_field(
                                        'site_texts',
                                        $key,
                                        $locale,
                                        (string) ($definition['type'] ?? 'text'),
                                        (string) ($options[$key . '_' . $locale] ?? Options\localized_default($key, $locale)),
                                        Options\localized_default($key, $locale)
                                    ); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>

            <section class="myliba-site-texts__section">
                <h2><?php echo esc_html(sprintf(__('Interface texts (%d)', 'myliba'), count($interface_catalog))); ?></h2>
                <div id="myliba-interface-text-list">
                    <?php foreach ($interface_catalog as $key => $definition) : ?>
                        <div class="myliba-site-texts__field myliba-interface-text-row">
                            <div class="myliba-site-texts__heading">
                                <strong><?php echo esc_html($definition['source']); ?></strong>
                                <code><?php echo esc_html($key); ?></code>
                                <small><?php echo esc_html(implode(', ', $definition['locations'])); ?></small>
                            </div>
                            <div class="myliba-site-texts__locales">
                                <?php foreach (['en', 'tr'] as $locale) : ?>
                                    <?php render_field(
                                        'interface_texts',
                                        $key,
                                        $locale,
                                        (string) ($definition['type'] ?? 'text'),
                                        (string) ($interface_overrides[$key][$locale] ?? $definition['defaults'][$locale]),
                                        (string) $definition['defaults'][$locale]
                                    ); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="myliba-site-texts__actions">
                <?php submit_button(__('Save site texts', 'myliba'), 'primary', 'submit', false); ?>
            </div>
        </form>
    </div>
    <style>
        .myliba-site-texts{max-width:1180px;padding-bottom:74px}.myliba-site-texts__toolbar{align-items:end;background:rgba(240,240,241,.96);border-bottom:1px solid #c3c4c7;display:flex;gap:12px;margin:16px 0 0;padding:12px 0;position:sticky;top:32px;z-index:20}.myliba-site-texts__toolbar-search{max-width:520px;width:min(520px,65vw)}.myliba-site-texts__toolbar label{display:block;font-weight:600;margin-bottom:6px}.myliba-site-texts__toolbar input{width:100%}.myliba-site-texts__toolbar span{color:#646970;font-size:12px;padding-bottom:7px}.myliba-site-texts__section{background:#fff;border:1px solid #dcdcde;border-radius:4px;margin:22px 0;overflow:hidden}.myliba-site-texts__section>h2{background:#f6f7f7;border-bottom:1px solid #dcdcde;font-size:14px;margin:0;padding:14px 18px}.myliba-site-texts__field{border-bottom:1px solid #f0f0f1;display:grid;gap:20px;grid-template-columns:minmax(180px,240px) 1fr;padding:18px}.myliba-site-texts__field:last-child{border-bottom:0}.myliba-site-texts__heading strong,.myliba-site-texts__heading small{display:block;margin-bottom:6px}.myliba-site-texts__heading small{color:#646970;font-size:11px;overflow-wrap:anywhere}.myliba-site-texts__heading code{font-size:11px}.myliba-site-texts__locales{display:grid;gap:14px;grid-template-columns:repeat(2,minmax(220px,1fr))}.myliba-site-texts__locale>span{display:block;font-size:12px;font-weight:600;margin-bottom:6px}.myliba-site-texts__locale textarea{resize:vertical}.myliba-site-texts__actions{background:rgba(255,255,255,.96);border-top:1px solid #c3c4c7;bottom:0;box-shadow:0 -8px 24px rgba(0,0,0,.08);left:160px;padding:12px 24px;position:fixed;right:0;z-index:30}.folded .myliba-site-texts__actions{left:36px}@media(max-width:960px){.myliba-site-texts__actions{left:36px}}@media(max-width:782px){.myliba-site-texts__toolbar{align-items:stretch;display:block;top:46px}.myliba-site-texts__toolbar-search{max-width:none;width:100%}.myliba-site-texts__toolbar span{display:block;padding:7px 0 0}.myliba-site-texts__field{grid-template-columns:1fr}.myliba-site-texts__locales{grid-template-columns:1fr}.myliba-site-texts__actions{left:0;padding:10px 12px}}
    </style>
    <script>
        var searchInput = document.getElementById('myliba-site-text-search');
        var searchStatus = document.getElementById('myliba-site-text-search-status');
        var textRows = Array.from(document.querySelectorAll('.myliba-site-texts__field'));
        searchInput.addEventListener('input', function () {
            var query = this.value.toLocaleLowerCase();
            var visibleCount = 0;
            textRows.forEach(function (row) {
                var matches = query === '' || row.textContent.toLocaleLowerCase().includes(query);
                row.hidden = !matches;
                visibleCount += matches ? 1 : 0;
            });
            document.querySelectorAll('.myliba-site-texts__section').forEach(function (section) {
                section.hidden = query !== '' && !section.querySelector('.myliba-site-texts__field:not([hidden])');
            });
            searchStatus.textContent = query === '' ? '' : visibleCount + ' <?php echo esc_js(__('results', 'myliba')); ?>';
        });
    </script>
    <?php
}
