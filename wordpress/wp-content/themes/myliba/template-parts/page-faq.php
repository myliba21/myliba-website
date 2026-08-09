<?php
if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();
$copy = static fn (string $key): string => \Myliba\Core\PageContent\text($page_id, 'faq', $key);
$rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($page_id, 'faq', $key);
$sections = \Myliba\Core\PageContent\sections($page_id, 'faq');

$demo_url = $copy('cta_primary_url') !== '' ? $copy('cta_primary_url') : myliba_demo_url();
$contact_url = $copy('cta_secondary_url') !== '' ? $copy('cta_secondary_url') : myliba_page_url('contact');
$support_email = (string) myliba_option('contact_email', 'hello@myliba.com');
$support_phone = (string) myliba_option('contact_phone', '+90 553 986 86 99');

$faqs = $rows('faqs');

// Category filters
$cat_all = $copy('category_all') !== '' ? $copy('category_all') : myliba_text('All');
$categories = array_filter([
    $copy('category_1'),
    $copy('category_2'),
    $copy('category_3'),
    $copy('category_4'),
    $copy('category_5'),
]);

// If no categories configured in fields, collect unique categories from FAQs
if (empty($categories)) {
    $found_cats = [];
    foreach ($faqs as $item) {
        $c = trim((string) ($item['category'] ?? ''));
        if ($c !== '' && !in_array($c, $found_cats, true)) {
            $found_cats[] = $c;
        }
    }
    $categories = $found_cats;
}

get_header();
?>

<div class="faq-page" data-faq-app>
    <?php foreach ($sections as $section) :
        if (empty($section['enabled'])) {
            continue;
        }

        switch ($section['key']) {
            case 'hero':
                ?>
                <section class="faq-hero">
                    <div class="faq-shell">
                        <div class="faq-hero__header">
                            <?php if ($copy('hero_eyebrow') !== '') : ?>
                                <p class="eyebrow faq-hero__eyebrow"><?php echo esc_html($copy('hero_eyebrow')); ?></p>
                            <?php endif; ?>
                            
                            <h1 class="faq-hero__title">
                                <?php echo esc_html($copy('hero_title')); ?>
                            </h1>

                            <?php if ($copy('hero_lead') !== '') : ?>
                                <p class="faq-hero__lead">
                                    <?php echo nl2br(esc_html($copy('hero_lead'))); ?>
                                </p>
                            <?php endif; ?>

                            <!-- Live Instant Search Bar -->
                            <div class="faq-search-wrapper" role="search">
                                <div class="faq-search-input-group">
                                    <span class="faq-search-icon" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </span>
                                    <input
                                        type="search"
                                        id="faq-live-search"
                                        class="faq-search-input"
                                        placeholder="<?php echo esc_attr($copy('hero_search_placeholder') ?: myliba_text('Search questions, topics, or features...')); ?>"
                                        aria-label="<?php echo esc_attr(myliba_text('Search in frequently asked questions')); ?>"
                                        autocomplete="off"
                                    />
                                    <button type="button" class="faq-search-clear" id="faq-search-clear" aria-label="<?php echo esc_attr(myliba_text('Clear search')); ?>" hidden>✕</button>
                                </div>
                                <div class="faq-search-status" id="faq-search-status" aria-live="polite" hidden></div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                break;

            case 'categories':
                if (!empty($categories)) :
                    ?>
                    <nav class="faq-categories-bar" aria-label="<?php echo esc_attr(myliba_text('FAQ Categories')); ?>">
                        <div class="faq-shell">
                            <div class="faq-categories-list" role="tablist">
                                <button
                                    type="button"
                                    class="faq-cat-btn is-active"
                                    role="tab"
                                    aria-selected="true"
                                    data-filter-category="all"
                                >
                                    <span><?php echo esc_html($cat_all); ?></span>
                                    <small class="faq-cat-count" data-count-target="all"><?php echo count($faqs); ?></small>
                                </button>
                                <?php foreach ($categories as $idx => $cat_name) :
                                    $cat_count = count(array_filter($faqs, static fn ($f) => trim((string) ($f['category'] ?? '')) === $cat_name));
                                    ?>
                                    <button
                                        type="button"
                                        class="faq-cat-btn"
                                        role="tab"
                                        aria-selected="false"
                                        data-filter-category="<?php echo esc_attr($cat_name); ?>"
                                    >
                                        <span><?php echo esc_html($cat_name); ?></span>
                                        <small class="faq-cat-count" data-count-target="<?php echo esc_attr($cat_name); ?>"><?php echo $cat_count; ?></small>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </nav>
                    <?php
                endif;
                break;

            case 'faqs':
                ?>
                <section class="section faq-list-section">
                    <div class="faq-shell">
                        <div class="academy-v2-faq faq-main-container">
                            <div class="academy-v2-faq__items faq-accordion-list" id="faq-accordion-list">
                                <?php if (!empty($faqs)) : ?>
                                    <?php foreach ($faqs as $index => $item) :
                                        $q = trim((string) ($item['question'] ?? ''));
                                        $a = trim((string) ($item['answer'] ?? ''));
                                        $cat = trim((string) ($item['category'] ?? ''));
                                        $tag = trim((string) ($item['tag'] ?? ''));
                                        if ($q === '' || $a === '') {
                                            continue;
                                        }
                                        ?>
                                        <details
                                            class="faq-item"
                                            data-category="<?php echo esc_attr($cat); ?>"
                                            data-search-text="<?php echo esc_attr(mb_strtolower($q . ' ' . wp_strip_all_tags($a) . ' ' . $cat . ' ' . $tag)); ?>"
                                        >
                                            <summary>
                                                <div class="faq-item__title-wrap">
                                                    <?php if ($tag !== '') : ?>
                                                        <span class="faq-item__tag"><?php echo esc_html($tag); ?></span>
                                                    <?php endif; ?>
                                                    <span class="faq-item__question"><?php echo esc_html($q); ?></span>
                                                </div>
                                                <span class="academy-v2-faq__icon" aria-hidden="true"></span>
                                            </summary>
                                            <div class="faq-item__answer">
                                                <?php echo wp_kses_post(wpautop($a)); ?>
                                            </div>
                                        </details>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Empty state if search has no results -->
                            <div class="faq-no-results" id="faq-no-results" hidden>
                                <div class="faq-no-results__icon">🔍</div>
                                <h3><?php echo esc_html(myliba_text('No questions matched your search')); ?></h3>
                                <p><?php echo esc_html(myliba_text('Try searching with different keywords or switch categories.')); ?></p>
                                <button type="button" class="myliba-button myliba-button--ghost faq-reset-search-btn" id="faq-reset-search-btn">
                                    <?php echo esc_html(myliba_text('Show All Questions')); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                break;

            case 'cta':
                ?>
                <!-- Support & Contact CTA Section -->
                <section class="section faq-cta-section">
                    <div class="faq-shell">
                        <div class="faq-cta-card">
                            <div class="faq-cta-card__main">
                                <?php if ($copy('cta_eyebrow') !== '') : ?>
                                    <p class="eyebrow faq-cta-card__eyebrow"><?php echo esc_html($copy('cta_eyebrow')); ?></p>
                                <?php endif; ?>
                                
                                <h2 class="faq-cta-card__title">
                                    <?php echo esc_html($copy('cta_title')); ?>
                                </h2>

                                <?php if ($copy('cta_lead') !== '') : ?>
                                    <p class="faq-cta-card__lead">
                                        <?php echo nl2br(esc_html($copy('cta_lead'))); ?>
                                    </p>
                                <?php endif; ?>

                                <div class="faq-cta-card__actions">
                                    <?php if ($copy('cta_primary_label') !== '') : ?>
                                        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>">
                                            <?php echo esc_html($copy('cta_primary_label')); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($copy('cta_secondary_label') !== '') : ?>
                                        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url($contact_url); ?>">
                                            <?php echo esc_html($copy('cta_secondary_label')); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="faq-cta-card__aside">
                                <div class="faq-support-box">
                                    <div class="faq-support-box__header">
                                        <span class="faq-support-box__badge">24h SLA</span>
                                        <h4><?php echo esc_html($copy('cta_contact_title') ?: myliba_text('Direct Contact')); ?></h4>
                                    </div>
                                    <p class="faq-support-box__text">
                                        <?php echo esc_html($copy('cta_contact_text') ?: myliba_text('Mesai saatleri içindeki tüm sorularınıza en geç 24 saat içinde dönüş yapıyoruz.')); ?>
                                    </p>
                                    <div class="faq-support-box__channels">
                                        <a href="<?php echo esc_url('mailto:' . $support_email); ?>" class="faq-channel-link">
                                            <span class="faq-channel-icon">✉️</span>
                                            <span><?php echo esc_html($support_email); ?></span>
                                        </a>
                                        <a href="<?php echo esc_url('tel:' . preg_replace('/[^\d+]/', '', $support_phone)); ?>" class="faq-channel-link">
                                            <span class="faq-channel-icon">📞</span>
                                            <span><?php echo esc_html($support_phone); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                break;
        }
    endforeach; ?>
</div>

<?php get_footer(); ?>
