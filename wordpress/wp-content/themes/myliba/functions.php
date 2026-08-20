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
        'primary' => __('Primary Navigation (Genel / Varsayılan)', 'myliba'),
        'primary_tr' => __('Primary Navigation (TR - Türkçe Ana Menü)', 'myliba'),
        'primary_en' => __('Primary Navigation (EN - English Main Menu)', 'myliba'),

        'footer_solutions' => __('Footer Solutions (Genel)', 'myliba'),
        'footer_solutions_tr' => __('Footer Solutions (TR - Çözümlerimiz)', 'myliba'),
        'footer_solutions_en' => __('Footer Solutions (EN - Solutions)', 'myliba'),

        'footer_development' => __('Footer Development (Genel)', 'myliba'),
        'footer_development_tr' => __('Footer Development (TR - Gelişim Merkezi)', 'myliba'),
        'footer_development_en' => __('Footer Development (EN - Development Center)', 'myliba'),

        'footer_company' => __('Footer Company (Genel)', 'myliba'),
        'footer_company_tr' => __('Footer Company (TR - Şirket)', 'myliba'),
        'footer_company_en' => __('Footer Company (EN - Company)', 'myliba'),

        'footer_legal' => __('Footer Legal (Genel)', 'myliba'),
        'footer_legal_tr' => __('Footer Legal (TR - Güvenlik ve Yasal)', 'myliba'),
        'footer_legal_en' => __('Footer Legal (EN - Security & Legal)', 'myliba'),

        'footer_bottom' => __('Footer Bottom (Genel)', 'myliba'),
        'footer_bottom_tr' => __('Footer Bottom (TR - Alt Çubuk)', 'myliba'),
        'footer_bottom_en' => __('Footer Bottom (EN - Bottom Bar)', 'myliba'),

        'footer' => __('Footer Navigation (Legacy)', 'myliba'),
        'footer_blog' => __('Footer Blog Links', 'myliba'),
    ]);
}
add_action('after_setup_theme', 'myliba_theme_setup');

if (file_exists(get_template_directory() . '/inc/customizer.php')) {
    require_once get_template_directory() . '/inc/customizer.php';
}

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
    $css_uri = get_template_directory_uri() . '/assets/css/dist/';
    $enqueue_css = static function (string $handle, string $file, array $dependencies = []) use ($css_uri): void {
        wp_enqueue_style(
            $handle,
            $css_uri . $file,
            $dependencies,
            myliba_asset_version('assets/css/dist/' . $file)
        );
    };

    $enqueue_css('myliba-base', 'base.min.css');
    $pre_shared_dependency = 'myliba-base';

    if (myliba_is_academy_landing_page() || is_singular('myliba_academy') || is_post_type_archive('myliba_academy')) {
        $enqueue_css('myliba-academy', 'academy.min.css', [$pre_shared_dependency]);
        $pre_shared_dependency = 'myliba-academy';
    } elseif (is_page(['yazilim', 'urunler', 'software', 'our-products']) || is_singular('myliba_product') || is_post_type_archive('myliba_product')) {
        $enqueue_css('myliba-software', 'software.min.css', [$pre_shared_dependency]);
        $pre_shared_dependency = 'myliba-software';
    } elseif (is_page(['solutions', 'cozumler']) || is_singular('myliba_solution') || is_post_type_archive('myliba_solution')) {
        $enqueue_css('myliba-solutions', 'solutions.min.css', [$pre_shared_dependency]);
        $pre_shared_dependency = 'myliba-solutions';
    } elseif (
        is_page(['development-center', 'gelisim-merkezi'])
        || is_singular(['myliba_report', 'myliba_ebook', 'myliba_event'])
        || is_post_type_archive(['myliba_report', 'myliba_ebook', 'myliba_event'])
    ) {
        $enqueue_css('myliba-development', 'development.min.css', [$pre_shared_dependency]);
        $pre_shared_dependency = 'myliba-development';
    }

    $enqueue_css('myliba-shared', 'shared.min.css', [$pre_shared_dependency]);
    $page_dependency = 'myliba-shared';

    if (is_page(['hikayemiz', 'our-story', 'biz-kimiz', 'about', 'about-us', 'felsefemiz'])) {
        $enqueue_css('myliba-story', 'story.min.css', [$page_dependency]);
    } elseif (is_page(['etik-hat', 'etik-danismanlik', 'ethics-counsel', 'etik', 'ethics', 'whistleblowing'])) {
        $enqueue_css('myliba-ethics', 'ethics.min.css', [$page_dependency]);
    } elseif (is_page(['sss', 'faq', 'faqs', 'sikca-sorulan-sorular'])) {
        $enqueue_css('myliba-faq', 'faq.min.css', [$page_dependency]);
    }

    wp_enqueue_script('myliba-main', get_template_directory_uri() . '/assets/js/main.js', [], myliba_asset_version('assets/js/main.js'), [
        'strategy' => 'defer',
        'in_footer' => true,
    ]);
}
add_action('wp_enqueue_scripts', 'myliba_enqueue_assets');

function myliba_cleanup_head(): void
{
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    add_filter('emoji_svg_url', '__return_false');
}
add_action('init', 'myliba_cleanup_head');

function myliba_dequeue_block_styles(): void
{
    if (!is_admin()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('classic-theme-styles');
        wp_dequeue_style('global-styles');
    }
}
add_action('wp_enqueue_scripts', 'myliba_dequeue_block_styles', 100);

function myliba_preload_base_stylesheet(): void
{
    $href = get_template_directory_uri() . '/assets/css/dist/base.min.css?ver=' . rawurlencode(myliba_asset_version('assets/css/dist/base.min.css'));
    printf("<link rel=\"preload\" as=\"style\" href=\"%s\">\n", esc_url($href));
}
add_action('wp_head', 'myliba_preload_base_stylesheet', 1);

function myliba_render_critical_css(): void
{
    ?>
    <style id="myliba-critical-css">
        :root{--primary:#ff5a2f;--primary-dark:#dc3e18;--accent:#2f6df6;--success:#16b887;--background:#fffdfb;--surface:#f8fafc;--foreground:#12131a;--text-secondary:#667085;--border:#eceff4;--shadow:0 24px 70px rgba(18,19,26,.10);--page-max:1440px;--content-gutter:max(24px,calc((100vw - var(--page-max))/2))}*{box-sizing:border-box}body{margin:0;background:linear-gradient(180deg,#fffdfb 0%,#fff 34%,#f8fbff 100%);color:var(--foreground);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;line-height:1.6}a{color:inherit;text-decoration:none}img{display:block;height:auto;max-width:100%}.site-header{position:sticky;top:0;z-index:20;border-bottom:1px solid rgba(18,19,26,.06);background:rgba(255,253,251,.84);backdrop-filter:blur(18px)}.site-header__inner{align-items:center;display:flex;gap:28px;margin:0 auto;max-width:1240px;min-height:68px;padding-left:24px;padding-right:24px}.site-brand{align-items:center;display:inline-flex;gap:10px;font-weight:900}.site-brand__logo{display:block;max-height:40px;max-width:min(220px,44vw);object-fit:contain;width:auto}.site-brand__mark{background:transparent;border-radius:0;display:grid;gap:3px;grid-template-columns:repeat(3,8px);height:30px;place-content:center;width:32px}.site-brand__mark span{border-radius:999px;display:block;width:8px}.site-brand__mark span:nth-child(1){background:var(--primary);height:23px}.site-brand__mark span:nth-child(2){background:var(--accent);height:30px}.site-brand__mark span:nth-child(3){background:var(--success);height:18px}.site-brand__text{font-size:1.04rem;letter-spacing:0}.site-nav{align-items:center;display:flex;flex:1;gap:6px;justify-content:center}.site-nav__menu{align-items:center;display:flex;flex-wrap:wrap;gap:6px;justify-content:center;list-style:none;margin:0;padding:0}.site-nav a{border-radius:999px;color:#303645;font-size:.86rem;font-weight:700;padding:8px 11px}.site-actions{align-items:center;display:flex;gap:14px}.site-nav__mobile-cta,.nav-toggle{display:none}.myliba-button{align-items:center;border:1px solid transparent;border-radius:999px;display:inline-flex;font-size:.9rem;font-weight:900;justify-content:center;min-height:44px;padding:11px 18px}.myliba-button--small,.myliba-button--primary{background:linear-gradient(135deg,var(--primary),#ff764f);box-shadow:0 14px 30px rgba(255,90,47,.22);color:#fff}.myliba-button--ghost{background:#fff;border-color:var(--border);box-shadow:0 12px 30px rgba(18,19,26,.06);color:var(--foreground)}.hero{align-items:center;background:radial-gradient(circle at 76% 12%,rgba(47,109,246,.14),transparent 28%),radial-gradient(circle at 18% 28%,rgba(255,107,74,.14),transparent 30%),linear-gradient(180deg,#fffdfa 0%,#f7fbff 100%);display:grid;gap:clamp(36px,4vw,72px);grid-template-columns:minmax(430px,.86fr) minmax(600px,1.14fr);margin:0 auto;min-height:700px;padding:86px var(--content-gutter) 56px;position:relative;width:100%}.hero::before{background:linear-gradient(135deg,rgba(255,255,255,.88),rgba(245,248,252,.66)),linear-gradient(90deg,rgba(255,107,74,.08),rgba(47,109,246,.07),rgba(22,184,135,.07));border:1px solid rgba(18,19,26,.04);border-radius:0 0 40px 40px;content:"";inset:0;position:absolute;z-index:-1}.eyebrow{color:var(--primary-dark);font-size:.72rem;font-weight:900;letter-spacing:.12em;margin:0 0 12px;text-transform:uppercase}.hero__content h1{font-size:clamp(3rem,4.6vw,5rem);letter-spacing:0;line-height:.96;margin:0;max-width:760px}.hero-title-rotator{display:grid;max-width:780px;overflow-wrap:anywhere}.hero-title-rotator__item{grid-area:1/1;opacity:0}.hero-title-rotator__item.is-active{opacity:1}.hero__subtitle{color:#586174;font-size:1.08rem;line-height:1.75;margin-top:20px;max-width:720px}.hero__actions,.hero__proof{display:flex;flex-wrap:wrap;gap:12px;margin-top:26px}.hero__proof{color:var(--text-secondary);font-size:.9rem;font-weight:800;gap:10px;margin-top:22px}.hero__proof span{background:rgba(255,255,255,.82);border:1px solid rgba(18,19,26,.08);border-radius:999px;box-shadow:0 10px 24px rgba(18,19,26,.04);padding:7px 11px}.hero-media-rotator{align-self:center;aspect-ratio:16/10;background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(247,250,255,.9)),radial-gradient(circle at 82% 8%,rgba(47,109,246,.14),transparent 34%);border:1px solid rgba(47,109,246,.13);border-radius:22px;box-shadow:0 32px 92px rgba(18,19,26,.17);overflow:hidden;padding:10px;position:relative}.hero-media-rotator__frame{background:#eef3fb;border:1px solid rgba(21,23,34,.08);border-radius:16px;height:100%;overflow:hidden;position:relative}.hero-media-rotator__slide{inset:0;margin:0;opacity:0;position:absolute}.hero-media-rotator__slide.is-active{opacity:1;z-index:1}.hero-media-rotator__slide img{display:block;height:100%;object-fit:cover;object-position:top left;width:100%}.site-promo,.site-promo__content{height:50px;min-height:50px}.site-promo{border-radius:0;margin:0;max-width:none;width:100%}.site-promo__content{padding-bottom:0;padding-top:0}@media(max-width:1120px){.hero{grid-template-columns:1fr;min-height:auto}.site-actions{display:none}.nav-toggle{display:inline-flex}}@media(max-width:640px){.site-header__inner{min-height:64px;padding-left:18px;padding-right:18px}.hero{gap:28px;padding:44px 18px 40px}.hero__content h1{font-size:clamp(2.35rem,14vw,3.45rem)}.hero-media-rotator{border-radius:16px;padding:6px}}
        :root{--primary:#287f9f;--primary-dark:#155c75;--accent:#b63a48;--success:#3c9276;--background:#fbfcfb;--surface:#f4f8f7;--foreground:#26343a;--text-secondary:#607078;--border:#dce7e6;--brand-blue:#287f9f;--brand-yellow:#d2a51f;--brand-red:#b63a48;--brand-green:#3c9276;--blue-soft:#e1f0f5;--green-soft:#e4f2ed;--yellow-soft:#fff3c7;--red-soft:#f8e3e6;--shadow:0 24px 70px rgba(38,52,58,.10)}body{background:linear-gradient(180deg,#fbfcfb 0%,#fff 38%,#f3f8f7 100%);font-family:Manrope,Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.site-brand__mark{grid-template-columns:repeat(4,7px);width:37px}.site-brand__mark span{width:7px}.site-brand__mark span:nth-child(1){background:var(--brand-green)}.site-brand__mark span:nth-child(2){background:var(--brand-yellow)}.site-brand__mark span:nth-child(3){background:var(--brand-red)}.site-brand__mark span:nth-child(4){background:var(--brand-blue);height:27px}.myliba-button--small,.myliba-button--primary{background:linear-gradient(135deg,var(--primary),var(--primary-dark));box-shadow:0 14px 30px rgba(40,127,159,.22)}.hero{background:radial-gradient(circle at 78% 10%,rgba(40,127,159,.16),transparent 30%),radial-gradient(circle at 14% 22%,rgba(60,146,118,.14),transparent 31%),linear-gradient(180deg,#fbfcfb 0%,#f4f9f7 100%)}
        :root{--fs-display:clamp(3rem,4.25vw,3rem);--fs-h1:clamp(2.5rem,3.5vw,3rem);--fs-lead:1.08rem;--fs-body:1rem}
        .hero-slide__visual picture{display:block;width:100%}
        .hero-slider{background:radial-gradient(circle at 78% 12%,rgba(23,109,137,.34),transparent 30%),radial-gradient(circle at 12% 88%,rgba(52,125,103,.23),transparent 32%),linear-gradient(135deg,#0f252d 0%,#102f39 52%,#142a32 100%);color:#fff;isolation:isolate;overflow:hidden;position:relative}.hero-slider__viewport{display:grid}.hero-slide{align-items:center;display:grid;gap:clamp(40px,5vw,76px);grid-area:1/1;grid-template-columns:minmax(0,.92fr) minmax(0,1.08fr);min-height:660px;opacity:0;padding:36px var(--content-gutter) 40px;pointer-events:none;visibility:hidden}.hero-slide.is-active{opacity:1;pointer-events:auto;visibility:visible;z-index:1}.hero-slide__content{max-width:700px;position:relative;z-index:2}.hero-slide__title{color:#fff;font-size:var(--fs-display);letter-spacing:-.055em;line-height:1.015;margin:0;text-wrap:balance}.hero-slide__text{color:rgba(235,244,246,.76);font-size:var(--fs-lead);line-height:1.75;margin:22px 0 0;max-width:610px}.hero-slide__visual-wrap{align-self:center;display:flex;justify-content:center;min-width:0;position:relative;width:100%}.hero-slide__visual{align-items:center;background:rgba(255,255,255,.96);border:1px solid rgba(255,255,255,.54);border-radius:20px;box-shadow:0 42px 90px rgba(1,15,20,.42);display:flex;justify-content:center;max-width:100%;overflow:hidden;padding:10px;width:100%}.hero-slide__visual img{border-radius:13px;display:block;height:auto;max-height:520px;max-width:100%;object-fit:contain;width:100%}@media(max-width:1120px){.hero-slide{grid-template-columns:1fr}.hero-slide__visual-wrap{margin:20px auto 0;max-width:820px}}@media(max-width:640px){body{padding-bottom:calc(66px + env(safe-area-inset-bottom))}.hero-slider,.hero-slide{min-height:calc(100svh - 121px)}.hero-slide{align-items:start;gap:0;grid-template-columns:minmax(0,1fr);padding:70px 18px 82px}.hero-slide__title{font-size:var(--fs-h1);line-height:1.02}.hero-slide__text{font-size:var(--fs-body);line-height:1.55;margin-top:18px}.hero-slide__visual-wrap,.hero-slider .hero__actions,.hero-slider .hero__proof,.hero-slider__controls{display:none}}
    </style>
    <?php
}
add_action('wp_head', 'myliba_render_critical_css', 0);

function myliba_option(string $key, mixed $fallback = ''): mixed
{
    $theme_mod = get_theme_mod($key, null);
    if ($theme_mod !== null && $theme_mod !== '') {
        $value = $theme_mod;
    } elseif (function_exists('Myliba\\Core\\Options\\get')) {
        $value = \Myliba\Core\Options\get($key, $fallback);
    } else {
        $value = $fallback;
    }

    // Locale-specific content must win over legacy Customizer values.
    if (!is_admin() && function_exists('Myliba\\Core\\Options\\localized_keys') && in_array($key, \Myliba\Core\Options\localized_keys(), true)) {
        $localized_value = \Myliba\Core\Options\get($key . '_' . myliba_current_language(), '');
        if (is_string($localized_value) && trim($localized_value) !== '') {
            $value = $localized_value;
        }
    }

    $translatable_keys = [
        'demo_cta_label',
        'footer_cta_title',
        'footer_cta_eyebrow',
        'footer_note',
        'primary_cta_label',
        'promo_left_text',
        'promo_message',
        'promo_right_text',
        'portal_cta_label',
        'footer_col1_title',
        'footer_col2_title',
        'footer_col3_title',
        'footer_col4_title',
    ];

    if (is_string($value) && !is_admin() && in_array($key, $translatable_keys, true)) {
        return myliba_translate_text($value);
    }

    return $value;
}

function myliba_get_page_footer_cta(int $post_id = 0): array
{
    if ($post_id === 0) {
        $post_id = (int) get_queried_object_id();
    }

    $global_enabled = myliba_option('footer_cta_enabled', '1') !== '0';
    $global_eyebrow = (string) myliba_option('footer_cta_eyebrow', myliba_text('Culture, goals and performance'));
    $global_title = (string) myliba_option('footer_cta_title', myliba_text('Ready to make culture measurable?'));
    $global_primary_label = (string) myliba_option('primary_cta_label', myliba_text('Contact us'));
    $global_primary_url_opt = (string) myliba_option('primary_cta_url', myliba_page_url('contact'));
    $global_primary_url = $global_primary_url_opt !== '' ? $global_primary_url_opt : myliba_page_url('contact');
    if (str_contains($global_primary_url, '/en/contact')) {
        $global_primary_url = myliba_page_url('contact');
    }
    $global_secondary_label = (string) myliba_option('demo_cta_label', myliba_text('Request a demo'));
    $global_secondary_url = (string) myliba_option('demo_url', myliba_demo_url());

    $data = [
        'enabled' => $global_enabled,
        'eyebrow' => $global_eyebrow,
        'title' => $global_title,
        'primary_label' => $global_primary_label,
        'primary_url' => $global_primary_url,
        'primary_data_attr' => '',
        'secondary_label' => $global_secondary_label,
        'secondary_url' => $global_secondary_url,
        'secondary_data_attr' => '',
    ];

    if ($post_id <= 0) {
        return $data;
    }

    // 1. Explicitly hidden on this page
    $hidden = get_post_meta($post_id, '_myliba_footer_cta_hide', true);
    if ($hidden === '1' || $hidden === 'yes' || $hidden === true) {
        $data['enabled'] = false;
        return $data;
    }

    $post = get_post($post_id);
    $slug = $post instanceof \WP_Post ? $post->post_name : '';

    // 2. Check Academy special page
    if ($post instanceof \WP_Post && in_array($slug, ['okr-kultur-akademisi', 'okr-culture-academy'], true)) {
        $academy_title = trim((string) get_post_meta($post_id, '_myliba_academy_final_title', true));
        $academy_eyebrow = trim((string) get_post_meta($post_id, '_myliba_academy_final_eyebrow', true));
        $academy_primary_label = trim((string) get_post_meta($post_id, '_myliba_academy_final_primary_label', true));
        $academy_primary_url = trim((string) get_post_meta($post_id, '_myliba_academy_final_primary_url', true));
        $academy_secondary_label = trim((string) get_post_meta($post_id, '_myliba_academy_final_secondary_label', true));
        $academy_secondary_url = trim((string) get_post_meta($post_id, '_myliba_academy_final_secondary_url', true));

        if ($academy_title !== '') {
            $data['title'] = $academy_title;
        }
        if ($academy_eyebrow !== '') {
            $data['eyebrow'] = $academy_eyebrow;
        }
        if ($academy_primary_label !== '') {
            $data['primary_label'] = $academy_primary_label;
            if ($academy_primary_url !== '') {
                $data['primary_url'] = $academy_primary_url;
            } else {
                $data['primary_url'] = '#';
                $data['primary_data_attr'] = 'data-academy-form-open';
            }
        }
        if ($academy_secondary_label !== '') {
            $data['secondary_label'] = $academy_secondary_label;
            $data['secondary_url'] = $academy_secondary_url !== '' ? $academy_secondary_url : '#programlar';
        }
        return $data;
    }

    // 3. Check PageContent schema if available
    if (function_exists('\\Myliba\\Core\\PageContent\\schema_for_post') && function_exists('\\Myliba\\Core\\PageContent\\document')) {
        $schema = \Myliba\Core\PageContent\schema_for_post($post_id);
        if ($schema !== null) {
            $doc = \Myliba\Core\PageContent\document($post_id, $schema);
            $fields = $doc['fields'] ?? [];

            if (!empty($fields['cta_hide']) || !empty($fields['final_cta_hide'])) {
                $data['enabled'] = false;
                return $data;
            }

            $schema_eyebrow = $fields['cta_eyebrow'] ?? $fields['final_eyebrow'] ?? '';
            $schema_title = $fields['cta_title'] ?? $fields['final_title'] ?? '';
            $schema_primary_label = $fields['cta_button_label'] ?? $fields['cta_primary_label'] ?? $fields['final_button_label'] ?? '';
            $schema_primary_url = $fields['cta_button_url'] ?? $fields['cta_primary_url'] ?? $fields['final_button_url'] ?? '';
            $schema_secondary_label = $fields['cta_secondary_label'] ?? $fields['final_secondary_label'] ?? '';
            $schema_secondary_url = $fields['cta_secondary_url'] ?? $fields['final_secondary_url'] ?? '';

            if (trim((string) $schema_eyebrow) !== '') {
                $data['eyebrow'] = (string) $schema_eyebrow;
            }
            if (trim((string) $schema_title) !== '') {
                $data['title'] = (string) $schema_title;
            }
            if (trim((string) $schema_primary_label) !== '') {
                $data['primary_label'] = (string) $schema_primary_label;
            }
            if (trim((string) $schema_primary_url) !== '') {
                $data['primary_url'] = (string) $schema_primary_url;
            }
            if (trim((string) $schema_secondary_label) !== '') {
                $data['secondary_label'] = (string) $schema_secondary_label;
            }
            if (trim((string) $schema_secondary_url) !== '') {
                $data['secondary_url'] = (string) $schema_secondary_url;
            }
        }
    }

    // 4. Check explicit post meta overrides
    $meta_eyebrow = trim((string) get_post_meta($post_id, '_myliba_footer_cta_eyebrow', true));
    $meta_title = trim((string) get_post_meta($post_id, '_myliba_footer_cta_title', true));
    $meta_primary_label = trim((string) get_post_meta($post_id, '_myliba_footer_cta_primary_label', true));
    $meta_primary_url = trim((string) get_post_meta($post_id, '_myliba_footer_cta_primary_url', true));
    $meta_secondary_label = trim((string) get_post_meta($post_id, '_myliba_footer_cta_secondary_label', true));
    $meta_secondary_url = trim((string) get_post_meta($post_id, '_myliba_footer_cta_secondary_url', true));

    if ($meta_eyebrow !== '') {
        $data['eyebrow'] = $meta_eyebrow;
    }
    if ($meta_title !== '') {
        $data['title'] = $meta_title;
    }
    if ($meta_primary_label !== '') {
        $data['primary_label'] = $meta_primary_label;
    }
    if ($meta_primary_url !== '') {
        $data['primary_url'] = $meta_primary_url;
    }
    if ($meta_secondary_label !== '') {
        $data['secondary_label'] = $meta_secondary_label;
    }
    if ($meta_secondary_url !== '') {
        $data['secondary_url'] = $meta_secondary_url;
    }

    return $data;
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
    $page_id = (int) get_queried_object_id();
    $managed_images = myliba_home_media_images('hero', $page_id);
    if ($managed_images) {
        return $managed_images;
    }

    $combined = myliba_env('MYLIBA_HERO_BANNER_IMAGES');
    $sources = $combined !== ''
        ? preg_split('/[\r\n|,]+/', $combined) ?: []
        : [
            myliba_env('MYLIBA_HERO_BANNER_IMAGE_1', 'assets/images/hero-1.webp'),
            myliba_env('MYLIBA_HERO_BANNER_IMAGE_2', 'assets/images/hero-2.webp'),
        ];

    $alts = [
        myliba_env('MYLIBA_HERO_BANNER_ALT_1', myliba_text('Myliba weekly focus dashboard preview')),
        myliba_env('MYLIBA_HERO_BANNER_ALT_2', myliba_text('Myliba goal map dashboard preview')),
    ];

    $images = [];
    foreach ($sources as $index => $source) {
        $url = myliba_asset_url_from_env((string) $source);

        if ($url === '') {
            continue;
        }

        $images[] = [
            'url' => $url,
            'alt' => $alts[$index] ?? sprintf(myliba_text('Myliba product dashboard preview %d'), $index + 1),
            ...myliba_image_dimensions_for_source((string) $source, $url),
        ];
    }

    return $images;
}

function myliba_home_media_images(string $group, int $post_id = 0): array
{
    $post_id = $post_id ?: (int) get_queried_object_id();
    if (!$post_id || !in_array($group, ['hero', 'performance'], true)) {
        return [];
    }

    if ($group === 'hero' && metadata_exists('post', $post_id, '_myliba_home_hero_slides_v2')) {
        $saved_slides = get_post_meta($post_id, '_myliba_home_hero_slides_v2', true);
        $saved_slides = is_array($saved_slides) ? $saved_slides : [];
        $images = [];
        foreach ($saved_slides as $slide) {
            if (!is_array($slide) || empty($slide['enabled'])) {
                continue;
            }

            $attachment_id = absint($slide['image_id'] ?? 0);
            $source = $attachment_id ? wp_get_attachment_image_src($attachment_id, 'full') : false;
            if (!$source) {
                continue;
            }

            $custom_alt = trim((string) ($slide['image_alt'] ?? ''));
            $media_alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
            $images[] = [
                'url' => (string) $source[0],
                'alt' => $custom_alt !== '' ? $custom_alt : $media_alt,
                'width' => (int) $source[1],
                'height' => (int) $source[2],
                'attachment_id' => $attachment_id,
            ];
        }

        return $images;
    }

    $images = [];
    for ($index = 1; $index <= 3; $index++) {
        $attachment_id = absint(get_post_meta($post_id, '_myliba_home_' . $group . '_image_' . $index, true));
        if (!$attachment_id) {
            continue;
        }

        $source = wp_get_attachment_image_src($attachment_id, 'full');
        if (!$source) {
            continue;
        }

        $custom_alt = trim((string) get_post_meta($post_id, '_myliba_home_' . $group . '_image_' . $index . '_alt', true));
        $media_alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
        $images[] = [
            'url' => (string) $source[0],
            'alt' => $custom_alt !== '' ? $custom_alt : $media_alt,
            'width' => (int) $source[1],
            'height' => (int) $source[2],
            'attachment_id' => $attachment_id,
        ];
    }

    return $images;
}

function myliba_home_performance_tabs(int $post_id = 0): array
{
    $post_id = $post_id ?: (int) get_queried_object_id();

    if ($post_id && metadata_exists('post', $post_id, '_myliba_home_performance_tabs_v2')) {
        $saved = get_post_meta($post_id, '_myliba_home_performance_tabs_v2', true);
        if (is_string($saved)) {
            $decoded = json_decode($saved, true);
            $saved = is_array($decoded) ? $decoded : [];
        }

        $tabs = [];
        foreach (is_array($saved) ? $saved : [] as $tab) {
            if (!is_array($tab) || empty($tab['enabled'])) {
                continue;
            }

            $image = [];
            $attachment_id = absint($tab['image_id'] ?? 0);
            $source = $attachment_id ? wp_get_attachment_image_src($attachment_id, 'large') : false;
            if ($source) {
                $custom_alt = trim((string) ($tab['image_alt'] ?? ''));
                $media_alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
                $image = [
                    'url' => (string) $source[0],
                    'alt' => myliba_translate_text($custom_alt !== '' ? $custom_alt : $media_alt),
                    'width' => (int) $source[1],
                    'height' => (int) $source[2],
                    'attachment_id' => $attachment_id,
                ];
            }

            $tabs[] = [
                'id' => sanitize_html_class((string) ($tab['id'] ?? 'performance-' . count($tabs))),
                'label' => myliba_translate_text((string) ($tab['label'] ?? '')),
                'title' => myliba_translate_text((string) ($tab['title'] ?? '')),
                'text' => myliba_translate_text((string) ($tab['text'] ?? '')),
                'image' => $image,
            ];
        }

        return $tabs;
    }

    $images = myliba_home_media_images('performance', $post_id);
    $tabs = [];
    foreach (myliba_home_rows('performance_tabs', [], $post_id) as $index => $row) {
        [$label, $title, $text] = array_pad($row, 3, '');
        $tabs[] = [
            'id' => 'legacy-' . ($index + 1),
            'label' => $label,
            'title' => $title,
            'text' => $text,
            'image' => $images[$index] ?? [],
        ];
    }

    return $tabs;
}

function myliba_image_dimensions_for_source(string $source, string $url): array
{
    $path = '';

    if (!str_starts_with($source, 'http://') && !str_starts_with($source, 'https://') && !str_starts_with($source, '/')) {
        $path = get_template_directory() . '/' . ltrim($source, '/');
    } elseif (str_starts_with($url, get_template_directory_uri())) {
        $path = get_template_directory() . str_replace(get_template_directory_uri(), '', $url);
    }

    if ($path === '' || !file_exists($path)) {
        return [];
    }

    $size = getimagesize($path);

    if (!$size) {
        return [];
    }

    return [
        'width' => (int) $size[0],
        'height' => (int) $size[1],
    ];
}

function myliba_lcp_hero_image(): array
{
    if (!is_front_page() && !myliba_is_locale_landing_page()) {
        return [];
    }

    $slides = myliba_home_hero_slides((int) get_queried_object_id());

    return $slides[0]['image'] ?? [];
}

function myliba_preload_lcp_hero_image(): void
{
    $image = myliba_lcp_hero_image();

    if (empty($image['url'])) {
        return;
    }

    $attributes = sprintf(
        'href="%s" fetchpriority="high" media="(min-width: 641px)"',
        esc_url((string) $image['url'])
    );

    $attachment_id = absint($image['attachment_id'] ?? 0);
    if ($attachment_id) {
        $srcset = wp_get_attachment_image_srcset($attachment_id, 'full');
        if ($srcset) {
            $attributes .= sprintf(
                ' imagesrcset="%s" imagesizes="%s"',
                esc_attr($srcset),
                esc_attr('(min-width: 1440px) 760px, (max-width: 720px) calc(100vw - 36px), (max-width: 1120px) calc(100vw - 48px), 54vw')
            );
        }
    }

    printf("<link rel=\"preload\" as=\"image\" %s>\n", $attributes); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action('wp_head', 'myliba_preload_lcp_hero_image', 1);

function myliba_render_theme_meta(): void
{
    $site_icon = (int) get_option('site_icon');
    $favicon = $site_icon ? (string) wp_get_attachment_image_url($site_icon, 'full') : get_template_directory_uri() . '/assets/images/favicon.svg';

    echo '<meta name="theme-color" content="#287f9f">' . "\n";
    printf("<link rel=\"icon\" href=\"%s\">\n", esc_url($favicon));
    printf("<link rel=\"apple-touch-icon\" href=\"%s\">\n", esc_url($favicon));
}
add_action('wp_head', 'myliba_render_theme_meta', 2);

function myliba_meta(string $key, int $post_id = 0, mixed $fallback = ''): mixed
{
    $post_id = $post_id ?: get_queried_object_id();
    if (!$post_id || !metadata_exists('post', $post_id, $key)) {
        return $fallback;
    }

    return get_post_meta($post_id, $key, true);
}

function myliba_current_language(): string
{
    if (function_exists('pll_current_language')) {
        return (string) pll_current_language('slug');
    }

    if (defined('ICL_SITEPRESS_VERSION')) {
        $wpml_language = apply_filters('wpml_current_language', null);
        if (is_string($wpml_language) && $wpml_language !== '') {
            return sanitize_key($wpml_language);
        }
    }

    $path_locale = myliba_locale_from_path(myliba_request_path());
    if ($path_locale !== '') {
        return $path_locale;
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

    return array_values(array_unique(array_merge($items, ['en', 'tr'])));
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
    $languages = explode(',', strtolower($header));
    if (empty($languages) || trim($languages[0]) === '') {
        return 'en';
    }

    foreach ($languages as $language) {
        $locale = str_replace('_', '-', trim(explode(';', $language, 2)[0] ?? ''));
        if ($locale === 'tr' || str_starts_with($locale, 'tr-')) {
            return 'tr';
        }
    }

    return 'en';
}

function myliba_preferred_locale(): string
{
    $cookie_name = myliba_locale_cookie_name();
    $cookie_locale = isset($_COOKIE[$cookie_name]) ? sanitize_key(wp_unslash($_COOKIE[$cookie_name])) : '';

    if (in_array($cookie_locale, myliba_available_locales(), true)) {
        return $cookie_locale;
    }

    $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_ACCEPT_LANGUAGE'])) : '';
    if ($accept_language !== '') {
        return myliba_locale_from_accept_language($accept_language);
    }

    return 'en';
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

    $cookie_name = myliba_locale_cookie_name();
    $cookie_locale = isset($_COOKIE[$cookie_name]) ? sanitize_key(wp_unslash($_COOKIE[$cookie_name])) : '';
    if (in_array($cookie_locale, myliba_available_locales(), true)) {
        wp_safe_redirect(home_url('/' . $cookie_locale . '/'), 302);
        exit;
    }

    $browser_fallback = myliba_preferred_locale();
    $home_base = trailingslashit(home_url('/'));
    nocache_headers();
    status_header(200);
    ?>
    <!doctype html>
    <html lang="<?php echo esc_attr($browser_fallback === 'tr' ? 'tr-TR' : 'en-US'); ?>">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="robots" content="noindex,follow">
        <meta http-equiv="refresh" content="1;url=<?php echo esc_url(home_url('/' . $browser_fallback . '/')); ?>">
        <title><?php echo esc_html(get_bloginfo('name')); ?></title>
    </head>
    <body>
        <script>
        (() => {
            const supported = ["tr", "en"];
            let stored = "";
            try {
                stored = window.localStorage.getItem("myliba_locale") || "";
            } catch (error) {}

            let locale = "";
            if (supported.includes(stored)) {
                locale = stored;
            } else {
                const browserLanguages = Array.isArray(navigator.languages) && navigator.languages.length
                    ? navigator.languages
                    : [navigator.language || ""];
                const isTurkish = browserLanguages.some((lang) => String(lang).toLowerCase().startsWith("tr"));
                locale = isTurkish ? "tr" : "en";
                try {
                    window.localStorage.setItem("myliba_locale", locale);
                } catch (error) {}
            }

            document.cookie = `myliba_locale=${locale}; path=/; max-age=31536000; samesite=lax`;
            window.location.replace(<?php echo wp_json_encode($home_base); ?> + locale + "/");
        })();
        </script>
        <p><a href="<?php echo esc_url(home_url('/' . $browser_fallback . '/')); ?>"><?php echo esc_html(myliba_text('Continue')); ?></a></p>
    </body>
    </html>
    <?php
    exit;
}
add_action('template_redirect', 'myliba_redirect_root_to_preferred_locale', 0);

function myliba_register_turkish_post_rewrites(): void
{
    add_rewrite_rule(
        '^tr/yazilar/([^/]+)/?$',
        'index.php?name=$matches[1]',
        'top'
    );
    add_rewrite_rule(
        '^en/blog/([^/]+)/?$',
        'index.php?name=$matches[1]',
        'top'
    );
}
add_action('init', 'myliba_register_turkish_post_rewrites');

function myliba_turkish_post_link(string $permalink, \WP_Post $post): string
{
    if ($post->post_type !== 'post' || $post->post_status === 'auto-draft') {
        return $permalink;
    }

    $language = (string) get_post_meta($post->ID, '_myliba_language', true);
    $bases = [
        'tr' => 'tr/yazilar',
        'en' => 'en/blog',
    ];
    if (!isset($bases[$language])) {
        return $permalink;
    }

    return home_url('/' . $bases[$language] . '/' . $post->post_name . '/');
}
add_filter('post_link', 'myliba_turkish_post_link', 10, 2);

function myliba_redirect_legacy_urls(): void
{
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
        return;
    }

    $path = myliba_request_path();
    $page_redirects = [
        '/tr/urunler' => 'products',
        '/products' => 'products',
        '/solutions' => 'solutions',
        '/academy' => 'academy',
        '/events' => 'events',
        '/e-kitaplar' => 'development',
        '/tr/gelisim-merkezi/e-kitaplar' => 'development',
        '/en/development-center/ebooks' => 'development',
        '/raporlar-ve-trendler' => 'development',
        '/landing-pages' => 'development',
    ];

    if (isset($page_redirects[$path])) {
        if ($path === '/solutions') {
            $destination = myliba_preferred_locale() === 'tr'
                ? home_url('/tr/cozumler/')
                : home_url('/en/solutions/');
        } else {
            $destination = $page_redirects[$path] === 'home'
                ? home_url('/tr/')
                : myliba_page_url($page_redirects[$path]);
        }

        if (myliba_url_path($destination) !== $path) {
            wp_safe_redirect($destination, 301);
            exit;
        }
    }

    $legacy_singular_bases = [
        'solutions' => 'myliba_solution',
        'academy' => 'myliba_academy',
        'events' => 'myliba_event',
        'e-kitaplar' => 'myliba_ebook',
        'raporlar-ve-trendler' => 'myliba_report',
    ];

    foreach ($legacy_singular_bases as $base => $post_type) {
        if (!preg_match('#^/' . preg_quote($base, '#') . '/([^/]+)$#', $path, $matches)) {
            continue;
        }

        $posts = get_posts([
            'post_type' => $post_type,
            'name' => sanitize_title($matches[1]),
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'suppress_filters' => false,
        ]);

        if ($posts) {
            wp_safe_redirect(get_permalink((int) $posts[0]), 301);
            exit;
        }
    }

    if (preg_match('#^/([^/]+)$#', $path, $matches)) {
        $post = get_page_by_path(sanitize_title($matches[1]), OBJECT, 'post');
        if ($post && get_post_meta($post->ID, '_myliba_language', true) === 'tr') {
            wp_safe_redirect(get_permalink($post), 301);
            exit;
        }
    }
}
add_action('template_redirect', 'myliba_redirect_legacy_urls', 2);

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

function myliba_translation_defaults(): array
{
    return [
        'Your message has been received.' => 'Mesajınız alındı.',
        'Request demo' => 'Demo Talebi Et',
        'Send' => 'Gönder',
        'Search resources' => 'Kaynaklarda ara',
        'Category' => 'Kategori',
        'All categories' => 'Tüm kategoriler',
        'Filter' => 'Filtrele',
        'Clear' => 'Temizle',
        'Clear filters' => 'Filtreleri temizle',
        'Reset' => 'Sıfırla',
        'No articles found.' => 'Herhangi bir yazı bulunamadı.',
        'Try adjusting your search or filter to find what you are looking for.' => 'Aramanıza veya seçtiğiniz kategoriye uygun sonuç bulunamadı. Filtreleri temizleyerek tekrar deneyebilirsiniz.',
        'Previous' => 'Önceki',
        'Next' => 'Sonraki',
        'min read' => 'dk okuma',
        'All' => 'Tümü',
        'FAQ Categories' => 'SSS Kategorileri',
        'Search questions, topics, or features...' => 'Soru, konu veya özellik arayın...',
        'Search in frequently asked questions' => 'Sıkça sorulan sorularda ara',
        'Clear search' => 'Aramayı temizle',
        'No questions matched your search' => 'Eşleşen soru bulunamadı',
        'Try searching with different keywords or switch categories.' => 'Farklı bir arama terimi deneyebilir veya kategorileri değiştirebilirsiniz.',
        'Show All Questions' => 'Tüm Soruları Göster',
        'Direct Contact' => 'Doğrudan İletişim',
        'questions found' => 'soru bulundu',
        'I consent to being contacted about this request and accept the privacy notice.' => 'Bu taleple ilgili tarafımla iletişime geçilmesine izin veriyor ve gizlilik bildirimini kabul ediyorum.',
        'Website' => 'Web sitesi',
        'The form could not be sent. Please try again.' => 'Form gönderilemedi. Lütfen tekrar deneyin.',
        'First name' => 'Ad',
        'Name' => 'Adınız',
        'Last name' => 'Soyad',
        'Business email' => 'İş e-postası',
        'Phone' => 'Telefon',
        'Title' => 'Unvan',
        'Employee count' => 'Çalışan sayısı',
        '1-50' => '1-50',
        '51-250' => '51-250',
        '251-1000' => '251-1000',
        '1000+' => '1000+',
        'Program you are interested in' => 'İlgilendiğiniz program',
        'Select a program' => 'Bir program seçin',
        'Participation type' => 'Katılım türü',
        'Individual' => 'Bireysel',
        'Corporate' => 'Kurumsal',
        'Subject' => 'Konu',
        'Message' => 'Mesaj',
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
        'Proven experience across companies, industries and leadership teams.' => 'Şirketler, sektörler ve liderlik ekipleri genelinde kanıtlanmış deneyim.',
        'Trusted partnership' => 'Güvenilir iş ortaklığı',
        'Measurable impact' => 'Ölçülebilir etki',
        'Calibration' => 'Kalibrasyon',
        'Can Myliba support implementation and training?' => 'Myliba uygulama ve eğitim süreçlerini destekler mi?',
        'CEO / Executive Team' => 'CEO / Üst Yönetim',
        'Coach work without losing follow-up' => 'Takibi kaybetmeden işi koçlukla yönetin',
        'Choose the operating routines you want to strengthen across OKR, KPI, CFR, 1:1, feedback and analytics.' => 'OKR, KPI, CFR, 1:1, geri bildirim ve analitik genelinde güçlendirmek istediğiniz çalışma rutinlerini seçin.',
        'Clear gains for every role.' => 'Her rol için net kazanımlar.',
        'Coaching notes, recognition and actions stay connected to goals.' => 'Koçluk notları, takdirler ve aksiyonlar hedeflerle bağlantılı kalır.',
        'Company' => 'Şirket',
        'Companies' => 'Şirket',
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
        'companies' => 'şirket',
        'Copyright %1$s %2$s. All rights reserved.' => 'Copyright %1$s %2$s. Tüm hakları saklıdır.',
        'Culture' => 'Kültür',
        'Culture Analysis' => 'Kültür Analizi',
        'Culture, goals and performance' => 'Kültür, hedefler ve performans',
        'Development' => 'Gelişim',
        'Development signals stay connected to goals and coaching.' => 'Gelişim sinyalleri hedefler ve koçlukla bağlantılı kalır.',
        'Employee growth' => 'Çalışan gelişimi',
        'Employees' => 'Çalışanlar',
        'ICF-accredited training' => 'ICF akreditasyonlu eğitim',
        'Industries' => 'Sektör',
        'Leaders' => 'Lider',
        'Living and sustainable culture commitment' => 'Yaşayan ve sürdürülebilir kültür taahhüdü',
        'leaders' => 'lider',
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
        'Years of HR and organizational development experience' => 'Yıllık İK ve organizasyonel gelişim deneyimi',
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
        'stronger goals' => 'daha güçlü hedefler',
        'Seffaflik' => 'Şeffaflık',
        'Toplanti beklemeden ilerlemeyi, engelleri ve sahipligi gorun.' => 'Toplantı beklemeden ilerlemeyi, engelleri ve sahipliği görün.',
        'Gelisim' => 'Gelişim',
        '1:1, geri bildirim ve koclugu surekli rutine donusturun.' => '1:1, geri bildirim ve koçluğu sürekli rutine dönüştürün.',
        'Oncelikleri aksiyonlara, sahipliklere ve olculebilir sonuclara donusturun.' => 'Öncelikleri aksiyonlara, sahipliklere ve ölçülebilir sonuçlara dönüştürün.',
        'OKR, performans ve kultur konulari icin SEO hazir icerik.' => 'OKR, performans ve kültür konuları için SEO hazır içerik.',
        'Demo talep etmeden once sik sorulan sorular.' => 'Demo talep etmeden önce sık sorulan sorular.',
        'Security & Legal' => 'Güvenlik ve Yasal',
        'Legal' => 'Yasal',
        'Privacy Policy' => 'Gizlilik Politikası',
        'KVKK and GDPR' => 'KVKK ve GDPR',
        'Cookie Policy' => 'Çerez Politikası',
        'Terms of Use' => 'Kullanım Şartları',
        'Our Solutions' => 'Çözümlerimiz',
        'Development Center' => 'Gelişim Merkezi',
        'About Us' => 'Biz Kimiz',
        'Software' => 'Yazılım',
        'Corporate Development Programs' => 'Kurumsal Gelişim Programları',
        'Simulations and Team Coaching' => 'Simülasyonlar ve Takım Koçluğu',
        'Consulting' => 'Danışmanlık',
    ];

}

function myliba_translate_text(string $text): string
{
    $text = trim($text);
    if ($text === '' || !function_exists('Myliba\\Core\\Content\\materialize')) {
        return '';
    }

    return \Myliba\Core\Content\materialize($text, myliba_current_language());
}

function myliba_text(string $source): string
{
    return myliba_translate_text($source);
}

/**
 * Keep the route-based fallback working before a multilingual plugin is
 * installed. Once Polylang or WPML is active, never replace its gettext result.
 */
function myliba_translate_gettext(string $translation, string $text, string $domain): string
{
    if ($domain !== 'myliba' || is_admin()) {
        return $translation;
    }

    if (function_exists('pll_current_language') || defined('ICL_SITEPRESS_VERSION')) {
        return $translation;
    }

    $locale = myliba_current_language();
    if (function_exists('Myliba\\Core\\Content\\legacy_override')) {
        $override = \Myliba\Core\Content\legacy_override($text, $locale);
        if ($override !== null) {
            return $override;
        }
    }

    if ($locale === 'tr') {
        $defaults = myliba_translation_defaults();
        if (isset($defaults[$text]) && is_string($defaults[$text])) {
            return $defaults[$text];
        }
    }

    return $locale === 'en' ? $text : $translation;
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
        'products' => ['en' => 'en/software', 'tr' => 'tr/yazilim'],
        'academy' => ['en' => 'en/okr-culture-academy', 'tr' => 'tr/okr-kultur-akademisi'],
        'culture' => ['en' => 'en/culture-analysis', 'tr' => 'tr/kultur-analizi'],
        'ethics' => ['en' => 'en/ethics-counsel', 'tr' => 'tr/etik-danismanlik'],
        'blog' => ['en' => 'en/blog', 'tr' => 'tr/yazilar'],
        'solutions' => ['en' => 'en/solutions', 'tr' => 'tr/cozumler'],
        'development' => ['en' => 'en/development-center', 'tr' => 'tr/gelisim-merkezi'],
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

    $fallback_path = $paths[$key][$lang] ?? $paths[$key]['tr'] ?? '';

    return home_url('/' . trim($fallback_path, '/') . '/');
}

function myliba_nav_items(): array
{
    return [
        'products' => myliba_text('Yazılım'),
        'academy' => myliba_text('Akademi'),
        'solutions' => myliba_text('Çözümlerimiz'),
        'development' => myliba_text('Gelişim Merkezi'),
        'story' => myliba_text('Biz Kimiz'),
        'contact' => myliba_text('İletişim'),
    ];
}

function myliba_portal_url(): string
{
    $custom = (string) myliba_option('portal_url', '');
    return $custom !== '' ? $custom : 'https://portal.myliba.com/';
}

function myliba_get_translated_post_id(int $post_id, string $target_lang = ''): int
{
    if ($post_id <= 0) {
        return 0;
    }

    $target_lang = $target_lang !== '' ? $target_lang : myliba_current_language();

    // 1. Polylang support
    if (function_exists('pll_get_post')) {
        $pll_id = pll_get_post($post_id, $target_lang);
        if ($pll_id && (int) $pll_id > 0) {
            return (int) $pll_id;
        }
    }

    // 2. WPML support
    if (defined('ICL_SITEPRESS_VERSION')) {
        $wpml_id = apply_filters('wpml_object_id', $post_id, get_post_type($post_id) ?: 'page', true, $target_lang);
        if ($wpml_id && (int) $wpml_id > 0) {
            return (int) $wpml_id;
        }
    }

    // 3. Post already in target language
    $post_lang = (string) get_post_meta($post_id, '_myliba_language', true);
    if ($post_lang === $target_lang) {
        return $post_id;
    }

    // 4. Check translation group key
    $translation_key = trim((string) get_post_meta($post_id, '_myliba_translation_key', true));
    if ($translation_key !== '') {
        $matched = get_posts([
            'post_type' => get_post_type($post_id) ?: 'any',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'suppress_filters' => false,
            'meta_query' => [
                [
                    'key' => '_myliba_translation_key',
                    'value' => $translation_key,
                ],
                [
                    'key' => '_myliba_language',
                    'value' => $target_lang,
                ],
            ],
        ]);

        if (!empty($matched)) {
            return (int) $matched[0];
        }
    }

    return $post_id;
}

function myliba_localize_url(string $url, string $target_lang = ''): string
{
    $url = trim($url);
    if ($url === '' || $url === '#' || str_starts_with($url, 'javascript:') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
        return $url;
    }

    $target_lang = $target_lang !== '' ? $target_lang : myliba_current_language();

    $known_routes = [
        'products' => ['tr' => '/tr/yazilim/', 'en' => '/en/software/'],
        'academy' => ['tr' => '/tr/okr-kultur-akademisi/', 'en' => '/en/okr-culture-academy/'],
        'solutions' => ['tr' => '/tr/cozumler/', 'en' => '/en/solutions/'],
        'development' => ['tr' => '/tr/gelisim-merkezi/', 'en' => '/en/development-center/'],
        'story' => ['tr' => '/tr/hikayemiz/', 'en' => '/en/our-story/'],
        'contact' => ['tr' => '/tr/iletisim/', 'en' => '/en/contact/'],
        'demo' => ['tr' => '/tr/demo/', 'en' => '/en/demo/'],
        'blog' => ['tr' => '/tr/yazilar/', 'en' => '/en/blog/'],
        'events' => ['tr' => '/tr/etkinlikler/', 'en' => '/en/events/'],
        'faq' => ['tr' => '/tr/sss/', 'en' => '/en/faq/'],
        'security' => ['tr' => '/tr/guvenlik/', 'en' => '/en/security/'],
        'privacy' => ['tr' => '/tr/gizlilik-politikasi/', 'en' => '/en/privacy-policy/'],
        'kvkk' => ['tr' => '/tr/kvkk/', 'en' => '/en/kvkk/'],
        'cookie' => ['tr' => '/tr/cerez-politikasi/', 'en' => '/en/cookie-policy/'],
        'terms' => ['tr' => '/tr/kullanim-sartlari/', 'en' => '/en/terms-of-use/'],
    ];

    $path = (string) wp_parse_url($url, PHP_URL_PATH);
    $path = '/' . trim($path, '/') . '/';

    foreach ($known_routes as $route) {
        $source_key = $target_lang === 'en' ? 'tr' : 'en';
        $target_key = $target_lang;

        if ($path === $route[$source_key]) {
            return home_url($route[$target_key]);
        }
    }

    if ($target_lang === 'en' && str_starts_with($path, '/tr/')) {
        return home_url('/en/' . ltrim(substr($path, 4), '/'));
    } elseif ($target_lang === 'tr' && str_starts_with($path, '/en/')) {
        return home_url('/tr/' . ltrim(substr($path, 4), '/'));
    }

    return $url;
}

function myliba_resolve_nav_menu_location(string $location): string
{
    $lang = myliba_current_language();
    $locations = get_nav_menu_locations();

    // 1. Check language-specific location: e.g. primary_tr or primary_en
    $loc_lang = $location . '_' . $lang;
    if (isset($locations[$loc_lang]) && (int) $locations[$loc_lang] > 0) {
        return $loc_lang;
    }

    // 2. Check base location: e.g. primary
    if (isset($locations[$location]) && (int) $locations[$location] > 0) {
        return $location;
    }

    return '';
}

function myliba_nav_menu_title(string $location, string $fallback = ''): string
{
    $lang = myliba_current_language();
    $locations = get_nav_menu_locations();

    // 1. Check customizer title option override
    $customizer_title = (string) myliba_option($location . '_title', '');
    if ($customizer_title !== '') {
        return myliba_text($customizer_title);
    }

    // 2. Check assigned menu's name
    $resolved_loc = myliba_resolve_nav_menu_location($location);
    if ($resolved_loc !== '' && isset($locations[$resolved_loc]) && (int) $locations[$resolved_loc] > 0) {
        $menu = wp_get_nav_menu_object((int) $locations[$resolved_loc]);
        if ($menu && !empty($menu->name)) {
            return myliba_text($menu->name);
        }
    }

    return myliba_text($fallback);
}

function myliba_get_primary_nav_items(): array
{
    $lang = myliba_current_language();
    $locations = get_nav_menu_locations();
    $resolved_loc = myliba_resolve_nav_menu_location('primary');

    if ($resolved_loc !== '' && isset($locations[$resolved_loc]) && (int) $locations[$resolved_loc] > 0) {
        $menu_items = wp_get_nav_menu_items((int) $locations[$resolved_loc]);
        if (is_array($menu_items) && !empty($menu_items)) {
            $tree = [];
            $by_id = [];

            foreach ($menu_items as $item) {
                $item->children = [];
                $by_id[$item->ID] = $item;
            }

            foreach ($menu_items as $item) {
                $parent_id = (int) $item->menu_item_parent;
                if ($parent_id > 0 && isset($by_id[$parent_id])) {
                    $by_id[$parent_id]->children[] = $item;
                } else {
                    $tree[] = $item;
                }
            }

            $items = [];
            foreach ($tree as $top_item) {
                $url = (string) $top_item->url;
                $label = (string) $top_item->title;
                $slug = sanitize_title($top_item->title);
                $classes = is_array($top_item->classes) ? $top_item->classes : [];
                $object_id = (int) $top_item->object_id;

                // Smart translation for page/post object if viewing in another language
                if ($object_id > 0) {
                    $translated_id = myliba_get_translated_post_id($object_id, $lang);
                    if ($translated_id > 0 && $translated_id !== $object_id) {
                        $translated_post = get_post($translated_id);
                        if ($translated_post) {
                            $url = get_permalink($translated_id);
                            if ($label === get_the_title($object_id)) {
                                $label = $translated_post->post_title;
                            }
                        }
                    }
                }

                // If label is translatable string, translate it
                $label = myliba_text($label);
                $url = myliba_localize_url($url, $lang);

                // Localize children if present
                $localized_children = [];
                if (!empty($top_item->children)) {
                    foreach ($top_item->children as $child) {
                        $child_url = (string) $child->url;
                        $child_label = (string) $child->title;
                        $child_obj_id = (int) $child->object_id;

                        if ($child_obj_id > 0) {
                            $trans_child_id = myliba_get_translated_post_id($child_obj_id, $lang);
                            if ($trans_child_id > 0 && $trans_child_id !== $child_obj_id) {
                                $trans_child_post = get_post($trans_child_id);
                                if ($trans_child_post) {
                                    $child_url = get_permalink($trans_child_id);
                                    if ($child_label === get_the_title($child_obj_id)) {
                                        $child_label = $trans_child_post->post_title;
                                    }
                                }
                            }
                        }

                        $child->url = myliba_localize_url($child_url, $lang);
                        $child->title = myliba_text($child_label);
                        $localized_children[] = $child;
                    }
                }

                if (in_array('mega-solutions', $classes, true) || str_contains($url, '/cozumler') || str_contains($url, '/solutions') || in_array($slug, ['cozumler', 'cozumlerimiz', 'solutions', 'our-solutions'], true)) {
                    $key = 'solutions';
                } elseif (in_array('mega-development', $classes, true) || str_contains($url, '/gelisim-merkezi') || str_contains($url, '/development-center') || in_array($slug, ['gelisim-merkezi', 'development-center', 'development'], true)) {
                    $key = 'development';
                } elseif (str_contains($url, '/urunler') || str_contains($url, '/products') || str_contains($url, '/software') || in_array($slug, ['yazilim', 'urunler', 'products', 'software'], true)) {
                    $key = 'products';
                } elseif (str_contains($url, '/akademi') || str_contains($url, '/academy') || in_array($slug, ['akademi', 'academy'], true)) {
                    $key = 'academy';
                } elseif (str_contains($url, '/hikayemiz') || str_contains($url, '/biz-kimiz') || str_contains($url, '/our-story') || str_contains($url, '/about') || in_array($slug, ['biz-kimiz', 'hikayemiz', 'our-story', 'about-us', 'about'], true)) {
                    $key = 'story';
                } elseif (str_contains($url, '/iletisim') || str_contains($url, '/contact') || in_array($slug, ['iletisim', 'contact', 'contact-us'], true)) {
                    $key = 'contact';
                } else {
                    $key = $slug ?: 'item-' . $top_item->ID;
                }

                $items[] = [
                    'id' => (int) $top_item->ID,
                    'key' => $key,
                    'label' => $label,
                    'url' => $url,
                    'target' => $top_item->target ?: '',
                    'classes' => implode(' ', array_filter($classes)),
                    'children' => $localized_children,
                ];
            }

            if (!empty($items)) {
                return $items;
            }
        }
    }

    $fallback = myliba_header_menu();
    $items = [];
    foreach ($fallback as $item) {
        $items[] = [
            'id' => 0,
            'key' => $item['key'],
            'label' => $item['label'],
            'url' => $item['url'],
            'target' => '',
            'classes' => '',
            'children' => [],
        ];
    }

    return $items;
}

function myliba_header_menu(): array
{
    return [
        ['key' => 'products', 'label' => myliba_text('Yazılım'), 'url' => myliba_page_url('products')],
        ['key' => 'academy', 'label' => myliba_text('Akademi'), 'url' => myliba_page_url('academy')],
        ['key' => 'solutions', 'label' => myliba_text('Çözümlerimiz'), 'url' => myliba_page_url('solutions')],
        ['key' => 'development', 'label' => myliba_text('Gelişim Merkezi'), 'url' => myliba_page_url('development')],
        ['key' => 'story', 'label' => myliba_text('Biz Kimiz'), 'url' => myliba_page_url('story')],
        ['key' => 'contact', 'label' => myliba_text('İletişim'), 'url' => myliba_page_url('contact')],
    ];
}

function myliba_content_values(array $values): array
{
    foreach ($values as $key => $value) {
        if (is_array($value)) {
            $values[$key] = myliba_content_values($value);
        } elseif (is_string($value) && $value !== '') {
            $values[$key] = myliba_text($value);
        }
    }

    return $values;
}

function myliba_solution_catalog(): array
{
    $catalog = [
        'kurumsal-gelisim-programlari' => [
            'title' => 'Kurumsal Gelişim Programları',
            'kicker' => 'İşbaşı gelişim programları',
            'summary' => 'Hedefleri değerlerle yönetmek için kurumunuza özel, uygulamalı gelişim yolculukları tasarlayın.',
            'intro' => 'Stratejik hedefleri kurum kültürü ve liderlik davranışlarıyla buluşturan programlarla öğrenmeyi günlük iş akışının parçası haline getirin.',
            'items' => [
                'Kurum hedefleri ve değerleriyle bağlantılı gelişim tasarımı',
                'Canlı öğrenme, işbaşı uygulama ve ölçülebilir takip',
                'Liderler ve ekipler için sürdürülebilir gelişim ritmi',
            ],
            'audiences' => [
                'İnsan ve kültür ekipleri',
                'Liderlik ekipleri',
                'Dönüşüm ve gelişim ekipleri',
            ],
            'steps' => [
                ['title' => 'İhtiyaç Analizi', 'text' => 'Kurumun hedefleri, kültürü ve gelişim öncelikleri birlikte değerlendirilir.'],
                ['title' => 'Program Tasarımı', 'text' => 'İçerik, gerçek iş hedefleri ve ekip dinamikleri etrafında yapılandırılır.'],
                ['title' => 'Uygulama ve Takip', 'text' => 'Öğrenme işbaşında uygulanır, gelişim göstergeleri düzenli olarak izlenir.'],
            ],
        ],
        'simulasyonlar-ve-takim-koclugu' => [
            'title' => 'Simülasyonlar ve Takım Koçluğu',
            'kicker' => 'Deneyimleyerek öğrenme',
            'summary' => 'Gerçek iş senaryolarını güvenli bir laboratuvar ortamında deneyimleyin, ekip davranışlarını görünür hale getirin.',
            'intro' => 'Simülasyonlar ve takım koçluğu, ekiplerin hedef, bağlılık ve iş birliği pratiklerini birlikte keşfetmesini ve geliştirmesini sağlar.',
            'items' => [
                'Hedef Mars Simülasyonu — oyunlaştırılmış dijital laboratuvar',
                'Radikal Samimiyet Simülasyonu — geri bildirim isteyen ekipler yaratın',
                'Başarı Sahnesi Simülasyonu — otonom ekipleri anlamlı hedefler etrafında geliştirin',
            ],
            'audiences' => [
                'Birlikte çalışma ritmini güçlendiren ekipler',
                'Yeni kurulan veya dönüşen ekipler',
                'Geri bildirim kültürü geliştiren liderler',
            ],
            'steps' => [
                ['title' => 'Senaryoyu Yaşayın', 'text' => 'Ekipler gerçek iş yaşamını temsil eden karar ve iletişim anlarını deneyimler.'],
                ['title' => 'Davranışı Görün', 'text' => 'Koç eşliğinde güçlü yönler, engeller ve takım örüntüleri görünür hale gelir.'],
                ['title' => 'Yeni Ritmi Kurun', 'text' => 'Öğrenilenler somut takım anlaşmalarına ve takip aksiyonlarına dönüşür.'],
            ],
        ],
        'danismanlik' => [
            'title' => 'Danışmanlık',
            'kicker' => 'Stratejiden sürdürülebilir sisteme',
            'summary' => 'Stratejik hedeflerinizi netleştirin ve kurumunuza özel performans gelişim sistemini birlikte kurun.',
            'intro' => 'Danışmanlık çalışmalarımız, hedef belirlemeden uygulama ritmine kadar organizasyonunuzun ihtiyaçlarına göre yapılandırılır.',
            'items' => [
                'Stratejik Hedef Haritası Oluşturma — şirket tepe hedeflerinin belirlenmesi ve otonom ekiplerin oluşturulması',
                'Performans Gelişim Sistemi Kurulumu — performans gelişim altyapısının kurumunuza özel yapılandırılması',
                'Uygulama, iletişim ve liderlik rutinlerinin organizasyonla birlikte tasarlanması',
            ],
            'audiences' => [
                'Üst yönetim ekipleri',
                'İnsan ve kültür liderleri',
                'Strateji ve dönüşüm ekipleri',
            ],
            'steps' => [
                ['title' => 'Mevcut Durum', 'text' => 'Hedef, performans ve liderlik süreçlerinin bugünkü resmi çıkarılır.'],
                ['title' => 'Hedef Sistem', 'text' => 'Organizasyona uygun model, roller ve çalışma ritimleri tasarlanır.'],
                ['title' => 'Kurulum', 'text' => 'Sistem ekiplerle birlikte devreye alınır ve gelişim göstergeleri takip edilir.'],
            ],
        ],
        'kultur-analizi' => [
            'title' => 'Kültür Analizi',
            'kicker' => 'Veriye dayalı kültür dönüşümü',
            'summary' => 'Kurum kültürünüzü derinlemesine analiz edin, potansiyel engelleri belirleyin ve çalışan bağlılığını güçlendirin.',
            'intro' => 'Myliba Kültür Analizi, çalışanların gerçekten çalışmaktan keyif aldığı bir ortam oluşturmak için kültürü ölçülebilir içgörülere dönüştürür.',
            'items' => [
                'Mevcut kültürün güçlü ve zayıf yönlerinin keşfedilmesi',
                'Çalışan bağlılığı ve iş performansının artması',
                'Kurum içi sinerji ve iletişimin güçlenmesi',
                'Stratejik dönüşüm için veriye dayalı içgörüler edinilmesi',
            ],
            'audiences' => [
                'İnsan ve kültür liderleri',
                'Üst yönetim ekipleri',
                'Değişim ve dönüşüm ekipleri',
            ],
            'metrics' => [
                ['title' => 'Employee NPS', 'text' => 'Çalışan tavsiye skoru'],
                ['title' => 'Culture Fit', 'text' => 'Departmanlar arası kültürel uyum'],
                ['title' => 'Willingness', 'text' => 'Çalışanın işe olan isteği'],
                ['title' => 'Engagement', 'text' => 'Kuruma, işe ve lidere bağlılık'],
            ],
            'steps' => [
                ['title' => 'Anket Aşaması', 'text' => 'Kültür analizi, bağlılık analizi ve isteklilik analizi uygulanır.'],
                ['title' => 'Saha Araştırması', 'text' => 'Odak grup, yönetici görüşmeleri, doküman analizi ve gözlem yapılır.'],
                ['title' => 'Gelişim Planı', 'text' => 'Detaylı rapor, öncelikli alanlar, OKR/KPI hedefleri ve uygulama takvimi oluşturulur.'],
            ],
        ],
    ];

    $catalog = myliba_content_values($catalog);

    // Query published solutions dynamically from the database
    $args = [
        'post_type' => 'myliba_solution',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        'suppress_filters' => false,
    ];

    $language = myliba_current_language();
    $meta_query = $language === 'en'
        ? [
            [
                'key' => '_myliba_language',
                'value' => 'en',
                'compare' => '=',
            ],
        ]
        : [
            'relation' => 'OR',
            [
                'key' => '_myliba_language',
                'value' => 'tr',
                'compare' => '=',
            ],
            [
                'key' => '_myliba_language',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key' => '_myliba_language',
                'value' => '',
                'compare' => '=',
            ],
        ];

    $solution_posts = get_posts(array_merge($args, [
        'meta_query' => $meta_query,
    ]));

    if (!empty($solution_posts)) {
        $dynamic_catalog = [];
        foreach ($solution_posts as $solution_post) {
        $slug = $solution_post->post_name;
        $id = $solution_post->ID;
        $fallback_item = $catalog[$slug] ?? [];

        $title = function_exists('Myliba\\Core\\PageContent\\text')
            ? \Myliba\Core\PageContent\text($id, 'solution', 'hero_title')
            : '';
        if ($title === '') {
            $title = (string) (get_post_meta($id, '_myliba_hero_title', true) ?: get_the_title($id));
        }

        $kicker = function_exists('Myliba\\Core\\PageContent\\text')
            ? \Myliba\Core\PageContent\text($id, 'solution', 'kicker')
            : '';
        if ($kicker === '') {
            $kicker = (string) (get_post_meta($id, '_myliba_eyebrow', true) ?: get_post_meta($id, '_myliba_label', true) ?: ($fallback_item['kicker'] ?? 'Myliba Çözümü'));
        }

        $summary = function_exists('Myliba\\Core\\PageContent\\text')
            ? \Myliba\Core\PageContent\text($id, 'solution', 'hero_summary')
            : '';
        if ($summary === '') {
            $summary = (string) (get_post_meta($id, '_myliba_hero_subtitle', true) ?: get_post_field('post_excerpt', $id) ?: ($fallback_item['summary'] ?? ''));
        }

        $intro = function_exists('Myliba\\Core\\PageContent\\text')
            ? \Myliba\Core\PageContent\text($id, 'solution', 'intro')
            : '';
        if ($intro === '') {
            $intro = (string) (get_post_meta($id, '_myliba_solution', true) ?: get_post_meta($id, '_myliba_problem', true) ?: ($fallback_item['intro'] ?? ''));
        }

        $benefits = function_exists('Myliba\\Core\\PageContent\\collection')
            ? array_column(\Myliba\Core\PageContent\collection($id, 'solution', 'benefits'), 'text')
            : [];
        if (empty($benefits)) {
            $meta_benefits = myliba_lines((string) get_post_meta($id, '_myliba_benefits', true));
            $benefits = !empty($meta_benefits) ? $meta_benefits : ($fallback_item['items'] ?? []);
        }

        $audiences = function_exists('Myliba\\Core\\PageContent\\collection')
            ? array_column(\Myliba\Core\PageContent\collection($id, 'solution', 'audiences'), 'text')
            : [];
        if (empty($audiences)) {
            $meta_audiences = myliba_lines((string) get_post_meta($id, '_myliba_audiences', true));
            $audiences = !empty($meta_audiences) ? $meta_audiences : ($fallback_item['audiences'] ?? ['İnsan ve kültür ekipleri', 'Liderlik ekipleri', 'Dönüşüm ve gelişim ekipleri']);
        }

        $metrics = function_exists('Myliba\\Core\\PageContent\\collection')
            ? \Myliba\Core\PageContent\collection($id, 'solution', 'metrics')
            : [];
        if (empty($metrics)) {
            $metrics = $fallback_item['metrics'] ?? [];
        }

        $steps = function_exists('Myliba\\Core\\PageContent\\collection')
            ? \Myliba\Core\PageContent\collection($id, 'solution', 'steps')
            : [];
        if (empty($steps)) {
            $steps = $fallback_item['steps'] ?? [];
        }

        $dynamic_catalog[$slug] = [
            'title' => $title,
            'kicker' => $kicker,
            'summary' => $summary,
            'intro' => $intro,
            'items' => $benefits,
            'audiences' => $audiences,
            'metrics' => $metrics,
            'steps' => $steps,
        ];
    }

    return $dynamic_catalog;
}

return $language === 'tr' ? $catalog : [];
}

function myliba_solution_url(string $slug): string
{
    static $urls = [];
    $language = myliba_current_language();
    $cache_key = $language . ':' . $slug;

    if (isset($urls[$cache_key])) {
        return $urls[$cache_key];
    }

    $posts = get_posts([
        'post_type' => 'myliba_solution',
        'name' => $slug,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'suppress_filters' => false,
        'meta_key' => '_myliba_language',
        'meta_value' => $language,
    ]);

    if (!empty($posts)) {
        $post_id = (int) $posts[0];
        $redirect_url = function_exists('Myliba\\Core\\PageContent\\text')
            ? \Myliba\Core\PageContent\text($post_id, 'solution', 'redirect_url')
            : '';
        if ($redirect_url === '') {
            $redirect_url = (string) get_post_meta($post_id, '_myliba_redirect_url', true);
        }

        if ($redirect_url !== '') {
            $urls[$cache_key] = filter_var($redirect_url, FILTER_VALIDATE_URL) ? $redirect_url : home_url($redirect_url);
            return $urls[$cache_key];
        }

        $urls[$cache_key] = get_permalink($post_id);
        return $urls[$cache_key];
    }

    if ($slug === 'kurumsal-gelisim-programlari') {
        $urls[$cache_key] = home_url('/tr/okr-kultur-akademisi/');
        return $urls[$cache_key];
    }
    if ($slug === 'corporate-development-programs') {
        $urls[$cache_key] = home_url('/en/okr-culture-academy/');
        return $urls[$cache_key];
    }

    if ($language === 'en') {
        $urls[$cache_key] = myliba_page_url('solutions');
        return $urls[$cache_key];
    }

    $urls[$cache_key] = home_url('/tr/cozumler/' . $slug . '/');
    return $urls[$cache_key];
}

function myliba_development_center_page_id(): int
{
    $lang = myliba_current_language();
    $paths = $lang === 'en'
        ? ['en/development-center', 'development-center', 'en/reports', 'reports']
        : ['tr/gelisim-merkezi', 'gelisim-merkezi', 'tr/gelisim-merkezi/raporlar-ve-trendler', 'raporlar-ve-trendler'];

    foreach ($paths as $path) {
        $page = get_page_by_path($path);
        if ($page instanceof \WP_Post) {
            return (int) $page->ID;
        }
    }

    $posts = get_posts([
        'post_type' => 'page',
        'name' => $lang === 'en' ? 'development-center' : 'gelisim-merkezi',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);

    return !empty($posts) ? (int) $posts[0] : 0;
}

function myliba_development_center_context(): array
{
    $page_id = myliba_development_center_page_id();
    $page_title = $page_id ? get_the_title($page_id) : myliba_text('Gelişim Merkezi');
    $page_excerpt = $page_id ? trim((string) get_post_field('post_excerpt', $page_id)) : '';

    return [
        'page_id' => $page_id,
        'eyebrow' => $page_id ? (string) myliba_meta('_myliba_eyebrow', $page_id, myliba_text('Sürekli gelişim ve dönüşüm merkezi')) : myliba_text('Sürekli gelişim ve dönüşüm merkezi'),
        'title' => $page_id ? (string) myliba_meta('_myliba_hero_title', $page_id, $page_title) : $page_title,
        'subtitle' => $page_id ? (string) myliba_meta('_myliba_hero_subtitle', $page_id, $page_excerpt) : $page_excerpt,
        'section_eyebrow' => $page_id ? (string) myliba_meta('_myliba_development_section_eyebrow', $page_id, myliba_text('Gelişim kaynakları')) : myliba_text('Gelişim kaynakları'),
        'section_title' => $page_id ? (string) myliba_meta('_myliba_development_section_title', $page_id, myliba_text('Gelişim zihniyetini sürekli yeni bilgi ve tecrübeyle besleyin.')) : myliba_text('Gelişim zihniyetini sürekli yeni bilgi ve tecrübeyle besleyin.'),
        'section_text' => $page_id ? (string) myliba_meta('_myliba_development_section_text', $page_id, '') : '',
        'card_cta' => $page_id ? (string) myliba_meta('_myliba_development_card_cta', $page_id, myliba_text('İçerikleri inceleyin')) : myliba_text('İçerikleri inceleyin'),
    ];
}

function myliba_development_center_items(): array
{
    $language = myliba_current_language();
    $page_id = myliba_development_center_page_id();
    $blog_page_url = myliba_page_url('blog');
    $events_page_url = myliba_page_url('events');
    $events_page_id = url_to_postid($events_page_url);
    $report_type = get_post_type_object('myliba_report');

    if ($language === 'en') {
        return [
            'reports' => [
                'label' => $page_id ? (string) myliba_meta('_myliba_development_report_label', $page_id, 'Reports & Trends') : 'Reports & Trends',
                'description' => $page_id ? (string) myliba_meta('_myliba_development_report_text', $page_id, 'Current research and insights.') : 'Current research and insights.',
                'url' => home_url('/en/development-center/reports/'),
                'post_type' => 'myliba_report',
            ],
            'blog' => [
                'label' => $page_id ? (string) myliba_meta('_myliba_development_blog_label', $page_id, 'Blog') : 'Blog',
                'description' => $page_id ? (string) myliba_meta('_myliba_development_blog_text', $page_id, 'Expert articles and operating notes.') : 'Expert articles and operating notes.',
                'url' => $blog_page_url,
                'post_type' => 'post',
            ],
            'events' => [
                'label' => $page_id ? (string) myliba_meta('_myliba_development_events_label', $page_id, 'Events & Workshops') : 'Events & Workshops',
                'description' => $page_id ? (string) myliba_meta('_myliba_development_events_text', $page_id, 'Webinars, workshops, and community sessions.') : 'Webinars, workshops, and community sessions.',
                'url' => $events_page_url,
                'post_type' => 'myliba_event',
            ],
        ];
    }

    return [
        'reports' => [
            'label' => $page_id ? (string) myliba_meta('_myliba_development_report_label', $page_id, $report_type?->labels->name ?: myliba_text('Raporlar ve Trendler')) : ($report_type?->labels->name ?: myliba_text('Raporlar ve Trendler')),
            'description' => $page_id ? (string) myliba_meta('_myliba_development_report_text', $page_id, '') : '',
            'url' => home_url('/tr/gelisim-merkezi/raporlar-ve-trendler/'),
            'post_type' => 'myliba_report',
        ],
        'blog' => [
            'label' => $page_id ? (string) myliba_meta('_myliba_development_blog_label', $page_id, myliba_text('Blog')) : myliba_text('Blog'),
            'description' => $page_id ? (string) myliba_meta('_myliba_development_blog_text', $page_id, '') : '',
            'url' => $blog_page_url,
            'post_type' => 'post',
        ],
        'events' => [
            'label' => $page_id ? (string) myliba_meta('_myliba_development_events_label', $page_id, $events_page_id ? get_the_title($events_page_id) : myliba_text('Etkinlikler')) : ($events_page_id ? get_the_title($events_page_id) : myliba_text('Etkinlikler')),
            'description' => $page_id ? (string) myliba_meta('_myliba_development_events_text', $page_id, '') : '',
            'url' => $events_page_url,
            'post_type' => 'myliba_event',
        ],
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
        'development' => is_page(['development-center', 'gelisim-merkezi', 'blog', 'yazilar', 'events', 'etkinlikler']) || is_home() || is_singular('post') || is_category() || is_tag() || is_post_type_archive(['myliba_event', 'myliba_report']) || is_singular(['myliba_event', 'myliba_report']),
        'products' => is_page(['yazilim', 'urunler']) || is_singular('myliba_product') || is_post_type_archive('myliba_product'),
        'solutions' => is_page(['solutions', 'cozumler']) || is_singular('myliba_solution') || is_post_type_archive('myliba_solution'),
        'story' => is_page(['hikayemiz', 'our-story', 'biz-kimiz', 'about', 'about-us', 'felsefemiz']),
        default => false,
    };
}

function myliba_language_context_url(string $language): string
{
    $language = in_array($language, ['tr', 'en'], true) ? $language : 'en';
    $current_id = get_queried_object_id();

    if ($current_id > 0 && is_singular()) {
        $post_type = get_post_type($current_id);
        $post_lang = (string) get_post_meta($current_id, '_myliba_language', true);

        if ($post_lang === $language) {
            return get_permalink($current_id);
        }

        // 1. Direct translation key lookup
        $translation_key = trim((string) get_post_meta($current_id, '_myliba_translation_key', true));
        if ($translation_key !== '') {
            $translations = get_posts([
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'fields' => 'ids',
                'meta_query' => [
                    [
                        'key' => '_myliba_translation_key',
                        'value' => $translation_key,
                    ],
                    [
                        'key' => '_myliba_language',
                        'value' => $language,
                    ],
                ],
                'suppress_filters' => false,
            ]);

            if (!empty($translations)) {
                return get_permalink((int) $translations[0]);
            }
        }

        // 2. Standard page slug mapping
        if ($post_type === 'page') {
            $current_slug = get_post_field('post_name', $current_id);
            $page_map_tr_to_en = [
                'cozumler' => 'solutions',
                'yazilim' => 'software',
                'urunler' => 'software',
                'gelisim-merkezi' => 'development-center',
                'hikayemiz' => 'our-story',
                'biz-kimiz' => 'our-story',
                'okr-kultur-akademisi' => 'okr-culture-academy',
                'kultur-analizi' => 'culture-analysis',
                'etik-danismanlik' => 'ethics-counsel',
                'etik-hat' => 'ethics-counsel',
                'iletisim' => 'contact',
                'demo' => 'demo',
                'etkinlikler' => 'events',
                'yazilar' => 'blog',
                'sss' => 'faq',
                'guvenlik' => 'security',
                'gizlilik-politikasi' => 'privacy-policy',
                'kvkk' => 'kvkk',
                'kullanim-sartlari' => 'terms-of-use',
            ];
            $page_map_en_to_tr = array_flip($page_map_tr_to_en);

            $target_slug = $language === 'en'
                ? ($page_map_tr_to_en[$current_slug] ?? $current_slug)
                : ($page_map_en_to_tr[$current_slug] ?? $current_slug);

            $target_page = get_page_by_path($language . '/' . $target_slug);
            if ($target_page instanceof \WP_Post) {
                return get_permalink($target_page);
            }
        }
    }

    // Archives or index fallbacks
    if (is_page(['cozumler', 'solutions']) || is_post_type_archive('myliba_solution')) {
        return home_url($language === 'tr' ? '/tr/cozumler/' : '/en/solutions/');
    }

    if (is_page(['gelisim-merkezi', 'development-center'])) {
        return home_url($language === 'tr' ? '/tr/gelisim-merkezi/' : '/en/development-center/');
    }

    if (is_page(['yazilar', 'blog']) || is_home()) {
        return home_url($language === 'tr' ? '/tr/yazilar/' : '/en/blog/');
    }

    return home_url('/' . $language . '/');
}

function myliba_language_links(): array
{
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(['raw' => 1]);

        if (is_array($languages) && $languages) {
            $links = array_map(static function (array $language): array {
                return [
                    'label' => strtoupper((string) $language['slug']),
                    'url' => (string) $language['url'],
                    'active' => !empty($language['current_lang']),
                ];
            }, $languages);

            $existing = array_map(static fn (array $link): string => strtolower((string) $link['label']), $links);
            foreach (['tr', 'en'] as $language) {
                if (!in_array($language, $existing, true)) {
                    $links[] = [
                        'label' => strtoupper($language),
                        'url' => myliba_language_context_url($language),
                        'active' => myliba_current_language() === $language,
                    ];
                }
            }

            return $links;
        }
    }

    $links = [];
    foreach (['tr', 'en'] as $language) {
        $page = get_page_by_path($language);
        $links[] = [
            'label' => strtoupper($language),
            'url' => myliba_language_context_url($language),
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

function myliba_get_testimonials_for_page(int $page_id, int $limit = 12, bool $include_unassigned = false): WP_Query
{
    $placement_query = [
        'key' => '_myliba_testimonial_page',
        'value' => (string) $page_id,
        'compare' => '=',
    ];

    if ($include_unassigned) {
        $placement_query = [
            'relation' => 'OR',
            $placement_query,
            [
                'key' => '_myliba_testimonial_page',
                'compare' => 'NOT EXISTS',
            ],
        ];
    }

    $meta_query = [$placement_query];
    if (!function_exists('pll_current_language')) {
        $meta_query[] = [
            'key' => '_myliba_language',
            'value' => myliba_current_language(),
            'compare' => '=',
        ];
    }
    if (count($meta_query) > 1) {
        $meta_query['relation'] = 'AND';
    }

    return myliba_get_entries('myliba_testimonial', $limit, [
        'meta_query' => $meta_query,
    ]);
}

function myliba_testimonials_shortcode(array $attributes = []): string
{
    $page_id = get_queried_object_id();
    if (!$page_id || get_post_type($page_id) !== 'page') {
        return '';
    }

    $attributes = shortcode_atts([
        'eyebrow' => myliba_current_language() === 'tr' ? 'Gerçek deneyimler' : 'Real experiences',
        'title' => myliba_current_language() === 'tr' ? 'Katılımcı Yorumları' : 'Participant Testimonials',
        'text' => '',
        'limit' => '12',
    ], $attributes, 'myliba_testimonials');
    $testimonials = myliba_get_testimonials_for_page($page_id, max(1, min(50, absint($attributes['limit']))));
    if (!$testimonials->have_posts()) {
        return '';
    }

    ob_start();
    get_template_part('template-parts/testimonials', null, [
        'query' => $testimonials,
        'id' => 'yorumlar-' . $page_id,
        'eyebrow' => sanitize_text_field((string) $attributes['eyebrow']),
        'title' => sanitize_text_field((string) $attributes['title']),
        'text' => sanitize_text_field((string) $attributes['text']),
    ]);
    return (string) ob_get_clean();
}
add_shortcode('myliba_testimonials', 'myliba_testimonials_shortcode');

function myliba_client_logo_posts(int $limit = 24): array
{
    $query = myliba_get_entries('myliba_client_logo', $limit, ['meta_query' => []]);
    $logos = [];

    while ($query->have_posts()) {
        $query->the_post();
        if (has_post_thumbnail()) {
            $logos[] = get_post();
        }
    }
    wp_reset_postdata();

    return $logos;
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

    return $page_url ?: (string) myliba_option('demo_url', '/tr/demo/');
}

function myliba_brand_link(string $modifier = ''): void
{
    $classes = trim('site-brand ' . $modifier);
    $home_url = home_url('/' . myliba_current_language() . '/');
    echo '<a class="' . esc_attr($classes) . '" href="' . esc_url($home_url) . '" aria-label="' . esc_attr(get_bloginfo('name')) . '">';

    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        echo wp_get_attachment_image($custom_logo_id, 'medium', false, [
            'class' => 'site-brand__logo',
            'alt' => get_bloginfo('name'),
            'decoding' => 'async',
            'fetchpriority' => 'low',
            'loading' => 'eager',
            'sizes' => '(max-width: 640px) 44vw, 220px',
        ]);
    } else {
        echo '<span class="site-brand__mark" aria-hidden="true"><span></span><span></span><span></span><span></span></span>';
        echo '<span class="site-brand__text">' . esc_html((string) myliba_option('organization_name', get_bloginfo('name'))) . '</span>';
    }

    echo '</a>';
}

function myliba_home_value(string $key, mixed $fallback = '', int $post_id = 0): mixed
{
    if (is_string($fallback)) {
        $fallback = myliba_translate_text($fallback);
    }

    $value = myliba_meta('_myliba_home_' . $key, $post_id ?: get_queried_object_id(), $fallback);

    return is_string($value) ? myliba_translate_text($value) : $value;
}

function myliba_format_text(string $text): string
{
    if (trim($text) === '') {
        return '';
    }

    // Support markdown bold **text** or __text__ -> <strong>text</strong>
    $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.*?)__/s', '<strong>$1</strong>', $text);

    // Convert newlines to <br> tags
    $text = nl2br($text);

    return wp_kses_post($text);
}

function myliba_home_lines(string $key, array $fallback = [], int $post_id = 0): array
{
    $fallback = array_map(static fn ($line) => is_string($line) ? myliba_translate_text($line) : $line, $fallback);
    $value = (string) myliba_home_value($key, implode("\n", $fallback), $post_id);

    return array_map('myliba_translate_text', myliba_lines($value));
}

function myliba_home_rows(string $key, array $fallback = [], int $post_id = 0): array
{
    $rows = [];
    $fallback = array_map(static function (array $row): array {
        return array_map(static fn ($cell) => is_string($cell) ? myliba_translate_text($cell) : $cell, $row);
    }, $fallback);
    $raw_rows = myliba_home_lines($key, array_map(static fn ($row) => implode('|', $row), $fallback), $post_id);

    foreach ($raw_rows as $row) {
        $rows[] = array_map(static fn ($cell) => myliba_translate_text(trim($cell)), explode('|', $row));
    }

    return $rows;
}

function myliba_home_hero_slides(int $post_id = 0): array
{
    $post_id = $post_id ?: (int) get_queried_object_id();
    $slides = [];

    if ($post_id && metadata_exists('post', $post_id, '_myliba_home_hero_slides_v2')) {
        $saved = get_post_meta($post_id, '_myliba_home_hero_slides_v2', true);
        $saved = is_array($saved) ? $saved : [];

        foreach ($saved as $slide) {
            if (!is_array($slide) || empty($slide['enabled'])) {
                continue;
            }

            $buttons = [];
            foreach (($slide['buttons'] ?? []) as $button) {
                if (!is_array($button)) {
                    continue;
                }

                $label = myliba_translate_text((string) ($button['label'] ?? ''));
                $url = (string) ($button['url'] ?? '');
                if ($label === '' || $url === '') {
                    continue;
                }

                $style = sanitize_key((string) ($button['style'] ?? 'ghost'));
                $buttons[] = [
                    'label' => $label,
                    'url' => $url,
                    'style' => in_array($style, ['primary', 'ghost', 'link'], true) ? $style : 'ghost',
                    'new_tab' => !empty($button['new_tab']),
                    'aria_label' => myliba_translate_text((string) ($button['aria_label'] ?? '')),
                ];
            }

            $image = [];
            $attachment_id = absint($slide['image_id'] ?? 0);
            $source = $attachment_id ? wp_get_attachment_image_src($attachment_id, 'full') : false;
            if ($source) {
                $custom_alt = trim((string) ($slide['image_alt'] ?? ''));
                $media_alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
                $image = [
                    'url' => (string) $source[0],
                    'alt' => myliba_translate_text($custom_alt !== '' ? $custom_alt : $media_alt),
                    'width' => (int) $source[1],
                    'height' => (int) $source[2],
                    'attachment_id' => $attachment_id,
                ];
            }

            $slides[] = [
                'id' => sanitize_html_class((string) ($slide['id'] ?? 'hero-' . count($slides))),
                'eyebrow' => myliba_translate_text((string) ($slide['eyebrow'] ?? '')),
                'title' => myliba_translate_text((string) ($slide['title'] ?? '')),
                'text' => myliba_translate_text((string) ($slide['text'] ?? '')),
                'image' => $image,
                'buttons' => $buttons,
            ];
        }

        return $slides;
    }

    $images = myliba_hero_banner_images();
    foreach (myliba_home_rows('hero_slides', [], $post_id) as $index => $row) {
        [$eyebrow, $title, $text, $primary_label, $primary_url, $secondary_label, $secondary_url] = array_pad($row, 7, '');
        $buttons = [];
        if ($primary_label !== '') {
            $buttons[] = ['label' => $primary_label, 'url' => $primary_url ?: myliba_demo_url(), 'style' => 'primary', 'new_tab' => false, 'aria_label' => ''];
        }
        if ($secondary_label !== '') {
            $buttons[] = ['label' => $secondary_label, 'url' => $secondary_url ?: myliba_page_url('products'), 'style' => 'ghost', 'new_tab' => false, 'aria_label' => ''];
        }

        $slides[] = [
            'id' => 'legacy-' . ($index + 1),
            'eyebrow' => $eyebrow,
            'title' => $title,
            'text' => $text,
            'image' => $images ? $images[$index % count($images)] : [],
            'buttons' => $buttons,
        ];
    }

    return $slides;
}

function myliba_home_section_definitions(): array
{
    return [
        'hero' => __('Hero slider', 'myliba'),
        'trust_bar' => __('Client references', 'myliba'),
        'social_proof' => __('Social proof metrics', 'myliba'),
        'why_myliba' => __('Why Myliba', 'myliba'),
        'problem' => __('Problem cards', 'myliba'),
        'solutions' => __('Strategy flow', 'myliba'),
        'performance' => __('Performance approach', 'myliba'),
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
    $saved_keys = [];

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

    if ($saved_keys && in_array('trust_bar', $saved_keys, true)) {
        $trust_order = (int) ($sections['trust_bar']['order'] ?? 20);

        if (!in_array('social_proof', $saved_keys, true)) {
            $sections['social_proof']['order'] = $trust_order + 1;
        }

        if (!in_array('why_myliba', $saved_keys, true)) {
            $sections['why_myliba']['order'] = $trust_order + 2;
        }
    }

    uasort($sections, static function (array $a, array $b): int {
        return ($a['order'] <=> $b['order']) ?: strcmp($a['key'], $b['key']);
    });

    return array_values($sections);
}

function myliba_parse_bullet_items(string $text): array
{
    $text = trim($text);
    if ($text === '') {
        return [];
    }

    // Convert various HTML break tags to newlines
    $normalized = preg_replace('/<\s*\/?\s*br\s*\/?\s*>/i', "\n", $text);
    $normalized = preg_replace('/&lt;\s*\/?\s*br\s*\/?\s*&gt;/i', "\n", (string) $normalized);

    // If string contains bullet characters, insert newline before inline bullets
    $normalized = preg_replace('/(?<!^)\s*[•·▪▫●○◆◇⁃]\s*/u', "\n", (string) $normalized);

    // Split by newlines
    $lines = preg_split('/\r\n|\r|\n/', (string) $normalized);
    $items = [];

    foreach ($lines as $line) {
        // Strip leading bullets, dashes, asterisks, numbered list markers
        $clean = preg_replace('/^\s*([•·▪▫●○◆◇⁃\-\*]|\d+[\.\)])\s*/u', '', (string) $line);
        // Strip any residual trailing break tags
        $clean = preg_replace('/<\s*\/?\s*br\s*\/?\s*>/i', '', (string) $clean);
        $clean = preg_replace('/&lt;\s*\/?\s*br\s*\/?\s*&gt;/i', '', (string) $clean);
        $clean = trim((string) $clean);
        if ($clean !== '') {
            $items[] = $clean;
        }
    }

    return $items;
}

/**
 * Social media platform definitions and SVGs
 */
function myliba_social_platforms(): array
{
    return [
        'linkedin' => [
            'label' => 'LinkedIn',
            'option_key' => 'linkedin_url',
            'short' => 'in',
            'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v7.6h2.8v-7.6h-2.8M7.86 6.5a1.63 1.63 0 0 0-1.63 1.63c0 .9.73 1.63 1.63 1.63.9 0 1.63-.73 1.63-1.63A1.63 1.63 0 0 0 7.86 6.5Z"/></svg>',
        ],
        'instagram' => [
            'label' => 'Instagram',
            'option_key' => 'instagram_url',
            'short' => 'ig',
            'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        ],
        'twitter' => [
            'label' => 'X (Twitter)',
            'option_key' => 'twitter_url',
            'short' => 'x',
            'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        ],
        'youtube' => [
            'label' => 'YouTube',
            'option_key' => 'youtube_url',
            'short' => 'yt',
            'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'option_key' => 'facebook_url',
            'short' => 'fb',
            'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        ],
    ];
}

/**
 * Returns all active social links with URLs configured in admin
 */
function myliba_social_links(): array
{
    $links = [];
    foreach (myliba_social_platforms() as $key => $platform) {
        $url = trim((string) myliba_option($platform['option_key'], ''));
        if ($url !== '') {
            $links[$key] = [
                'key' => $key,
                'label' => myliba_text($platform['label']),
                'url' => $url,
                'svg' => $platform['svg'],
                'short' => $platform['short'],
            ];
        }
    }
    return $links;
}

/**
 * Renders social icons markup
 */
function myliba_render_social_icons(string $wrapper_class = 'contact-details__socials'): string
{
    $links = myliba_social_links();
    if (empty($links)) {
        return '';
    }

    $out = '<div class="' . esc_attr($wrapper_class) . '" role="navigation" aria-label="' . esc_attr(myliba_text('Sosyal Medya')) . '">';
    foreach ($links as $key => $item) {
        $out .= sprintf(
            '<a href="%s" class="contact-details__social-link contact-details__social-link--%s" target="_blank" rel="noopener noreferrer" aria-label="%s" title="%s">%s</a>',
            esc_url($item['url']),
            esc_attr($key),
            esc_attr($item['label']),
            esc_attr($item['label']),
            $item['svg']
        );
    }
    $out .= '</div>';
    return $out;
}

add_shortcode('myliba_social_links', static function (array $atts = []): string {
    $atts = shortcode_atts(['class' => 'contact-details__socials'], $atts, 'myliba_social_links');
    return myliba_render_social_icons((string) $atts['class']);
});

/**
 * Injects social media icons into the "Bize Ulaşın" card on contact pages
 */
function myliba_inject_contact_social_links(string $content): string
{
    if (!is_page_template('template-contact.php') && !is_page(['iletisim', 'contact'])) {
        return $content;
    }

    if (str_contains($content, 'contact-details__socials')) {
        return $content;
    }

    $socials_html = myliba_render_social_icons('contact-details__socials');
    if ($socials_html === '') {
        return $content;
    }

    // Match the "Bize Ulaşın" / "Contact Us" card and append the socials before the closing </section>
    $pattern = '/(<section[^>]*class="[^"]*contact-details__card[^"]*"[^>]*>[\s\S]*?<h2[^>]*>(?:Bize Ulaşın|Contact Us|İletişim|Contact)<\/h2>[\s\S]*?)(<\/section>)/iu';
    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, '$1' . $socials_html . '$2', $content, 1);
    }

    // Fallback: If there's a contact-details wrapper but no matching h2, append to the last card
    $fallback_pattern = '/(<div[^>]*class="[^"]*contact-details[^"]*"[^>]*>[\s\S]*?<section[^>]*class="[^"]*contact-details__card[^"]*"[^>]*>[\s\S]*?)(<\/section>\s*<\/div>)/iu';
    if (preg_match($fallback_pattern, $content)) {
        return preg_replace($fallback_pattern, '$1' . $socials_html . '$2', $content, 1);
    }

    return $content;
}
add_filter('the_content', 'myliba_inject_contact_social_links', 20);
