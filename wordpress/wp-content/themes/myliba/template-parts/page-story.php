<?php
if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();
$copy = static fn (string $key): string => \Myliba\Core\PageContent\text($page_id, 'story', $key);
$rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($page_id, 'story', $key);
$sections = \Myliba\Core\PageContent\sections($page_id, 'story');

$demo_url = myliba_demo_url();
$contact_url = myliba_page_url('contact');

$target_urls = [
    'products' => myliba_page_url('products'),
    'academy' => myliba_page_url('academy'),
    'development' => myliba_page_url('development'),
    'solutions' => myliba_page_url('solutions'),
];

$hero_badges = $rows('hero_badges');
$comparisons = $rows('comparisons');
$pillars = $rows('pillars');
$stats = $rows('stats');
$values = $rows('values');

get_header();
?>

<div class="story-page">
    <?php foreach ($sections as $section) :
        if (empty($section['enabled'])) {
            continue;
        }

        switch ($section['key']) {
            case 'hero':
                ?>
                <!-- Hero & Manifesto Section -->
                <section class="story-hero">
                    <div class="story-shell">
                        <div class="story-hero__header">
                            <?php if ($copy('hero_eyebrow') !== '') : ?>
                                <div class="story-hero__eyebrow-wrap">
                                    <span class="story-pill"><?php echo esc_html($copy('hero_eyebrow')); ?></span>
                                </div>
                            <?php endif; ?>

                            <h1 class="story-hero__title">
                                <?php echo nl2br(esc_html($copy('hero_title'))); ?>
                            </h1>

                            <?php if ($copy('hero_lead') !== '') : ?>
                                <p class="story-hero__lead">
                                    <?php echo nl2br(esc_html($copy('hero_lead'))); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($hero_badges)) : ?>
                                <div class="story-hero__badges">
                                    <?php foreach ($hero_badges as $badge) : ?>
                                        <span class="story-badge"><i>✦</i> <?php echo esc_html((string) ($badge['label'] ?? '')); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="story-hero__actions">
                                <?php if ($copy('hero_primary_label') !== '') : ?>
                                    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>">
                                        <?php echo esc_html($copy('hero_primary_label')); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($copy('hero_secondary_label') !== '') : ?>
                                    <a class="myliba-button myliba-button--ghost" href="#felsefemiz">
                                        <?php echo esc_html($copy('hero_secondary_label')); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                break;

            case 'formula':
                ?>
                <!-- Formula & Management Approach Section -->
                <section id="felsefemiz" class="story-section story-formula-section">
                    <div class="story-shell">
                        <div class="story-section__heading">
                            <?php if ($copy('formula_eyebrow') !== '') : ?>
                                <p class="eyebrow"><?php echo esc_html($copy('formula_eyebrow')); ?></p>
                            <?php endif; ?>
                            <h2><?php echo esc_html($copy('formula_title')); ?></h2>
                            <?php if ($copy('formula_lead') !== '') : ?>
                                <p><?php echo esc_html($copy('formula_lead')); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="story-formula-card">
                            <div class="story-formula-card__top">
                                <?php if ($copy('formula_badge') !== '') : ?>
                                    <span class="story-formula-badge"><?php echo esc_html($copy('formula_badge')); ?></span>
                                <?php endif; ?>
                                <?php if ($copy('formula_meta') !== '') : ?>
                                    <span class="story-formula-meta"><?php echo esc_html($copy('formula_meta')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="story-equation">
                                <div class="story-eq-box story-eq-box--result">
                                    <?php if ($copy('formula_result_tag') !== '') : ?>
                                        <div class="story-eq-box__tag"><?php echo esc_html($copy('formula_result_tag')); ?></div>
                                    <?php endif; ?>
                                    <div class="story-eq-box__header">
                                        <span class="story-eq-icon">🚀</span>
                                        <h3><?php echo esc_html($copy('formula_result_title')); ?></h3>
                                    </div>
                                    <p><?php echo esc_html($copy('formula_result_desc')); ?></p>
                                </div>

                                <div class="story-eq-symbol">=</div>

                                <div class="story-eq-box story-eq-box--potential">
                                    <?php if ($copy('formula_potential_tag') !== '') : ?>
                                        <div class="story-eq-box__tag"><?php echo esc_html($copy('formula_potential_tag')); ?></div>
                                    <?php endif; ?>
                                    <div class="story-eq-box__header">
                                        <span class="story-eq-icon">💡</span>
                                        <h3><?php echo esc_html($copy('formula_potential_title')); ?></h3>
                                    </div>
                                    <p><?php echo esc_html($copy('formula_potential_desc')); ?></p>
                                </div>

                                <div class="story-eq-symbol story-eq-symbol--minus">−</div>

                                <div class="story-eq-box story-eq-box--interference">
                                    <?php if ($copy('formula_interference_tag') !== '') : ?>
                                        <div class="story-eq-box__tag"><?php echo esc_html($copy('formula_interference_tag')); ?></div>
                                    <?php endif; ?>
                                    <div class="story-eq-box__header">
                                        <span class="story-eq-icon">⚠️</span>
                                        <h3><?php echo esc_html($copy('formula_interference_title')); ?></h3>
                                    </div>
                                    <p><?php echo esc_html($copy('formula_interference_desc')); ?></p>
                                </div>
                            </div>

                            <?php if ($copy('formula_leverage_text') !== '') : ?>
                                <div class="story-formula-footer">
                                    <div class="story-formula-footer__icon">✦</div>
                                    <div class="story-formula-footer__text">
                                        <?php if ($copy('formula_leverage_title') !== '') : ?>
                                            <strong><?php echo esc_html($copy('formula_leverage_title')); ?></strong>
                                        <?php endif; ?>
                                        <?php echo esc_html($copy('formula_leverage_text')); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php
                break;

            case 'why':
                ?>
                <!-- Problem & Solution Comparison Section -->
                <section class="story-section story-why-section">
                    <div class="story-shell">
                        <div class="story-section__heading">
                            <?php if ($copy('why_eyebrow') !== '') : ?>
                                <p class="eyebrow"><?php echo esc_html($copy('why_eyebrow')); ?></p>
                            <?php endif; ?>
                            <h2><?php echo esc_html($copy('why_title')); ?></h2>
                            <?php if ($copy('why_lead') !== '') : ?>
                                <p><?php echo esc_html($copy('why_lead')); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($comparisons)) : ?>
                            <div class="story-comparison-grid">
                                <?php foreach ($comparisons as $item) : ?>
                                    <div class="story-comparison-card">
                                        <div class="story-comparison-card__side story-comparison-card__side--problem">
                                            <div class="story-side-label"><span class="story-icon-cross">✕</span> <?php echo esc_html((string) ($item['problem_label'] ?? '')); ?></div>
                                            <h4><?php echo esc_html((string) ($item['problem_title'] ?? '')); ?></h4>
                                            <p><?php echo esc_html((string) ($item['problem_desc'] ?? '')); ?></p>
                                        </div>
                                        <div class="story-comparison-arrow" aria-hidden="true">→</div>
                                        <div class="story-comparison-card__side story-comparison-card__side--solution">
                                            <div class="story-side-label"><span class="story-icon-check">✓</span> <?php echo esc_html((string) ($item['solution_label'] ?? '')); ?></div>
                                            <h4><?php echo esc_html((string) ($item['solution_title'] ?? '')); ?></h4>
                                            <p><?php echo esc_html((string) ($item['solution_desc'] ?? '')); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($copy('why_manifesto_text') !== '') : ?>
                            <div class="story-manifesto-callout">
                                <div class="story-manifesto-callout__icon">💡</div>
                                <p><?php echo nl2br(esc_html($copy('why_manifesto_text'))); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'pillars':
                ?>
                <!-- 4 Pillars Bento Grid (What We Do) -->
                <section class="story-section story-pillars-section">
                    <div class="story-shell">
                        <div class="story-section__heading">
                            <?php if ($copy('pillars_eyebrow') !== '') : ?>
                                <p class="eyebrow"><?php echo esc_html($copy('pillars_eyebrow')); ?></p>
                            <?php endif; ?>
                            <h2><?php echo esc_html($copy('pillars_title')); ?></h2>
                            <?php if ($copy('pillars_lead') !== '') : ?>
                                <p><?php echo esc_html($copy('pillars_lead')); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($pillars)) : ?>
                            <div class="story-pillars-grid">
                                <?php foreach ($pillars as $pillar_index => $pillar) :
                                    $tags = array_filter(array_map('trim', explode("\n", (string) ($pillar['tags'] ?? ''))));
                                    $link_url = $target_urls[$pillar['link_target'] ?? ''] ?? $contact_url;
                                    $custom_class = !empty($pillar['link_target']) ? 'story-pillar-card--' . sanitize_html_class((string) $pillar['link_target']) : '';
                                    ?>
                                    <div class="story-pillar-card <?php echo esc_attr($custom_class); ?>">
                                        <div class="story-pillar-card__top">
                                            <?php if (!empty($pillar['badge'])) : ?>
                                                <span class="story-pillar-badge <?php echo $pillar_index === 1 ? 'story-pillar-badge--accent' : ''; ?>">
                                                    <?php echo esc_html((string) $pillar['badge']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="story-pillar-num"><?php echo esc_html((string) ($pillar['number'] ?? str_pad((string) ($pillar_index + 1), 2, '0', STR_PAD_LEFT))); ?></span>
                                        </div>
                                        <?php if (!empty($pillar['icon'])) : ?>
                                            <div class="story-pillar-card__icon"><?php echo esc_html((string) $pillar['icon']); ?></div>
                                        <?php endif; ?>
                                        <h3 class="story-pillar-card__title"><?php echo esc_html((string) ($pillar['title'] ?? '')); ?></h3>
                                        <p class="story-pillar-card__desc"><?php echo nl2br(esc_html((string) ($pillar['desc'] ?? ''))); ?></p>

                                        <?php if (!empty($tags)) : ?>
                                            <div class="story-pillar-tags">
                                                <?php foreach ($tags as $tag) : ?>
                                                    <span><?php echo esc_html($tag); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($pillar['link_label'])) : ?>
                                            <a class="story-pillar-link" href="<?php echo esc_url($link_url); ?>">
                                                <?php echo esc_html((string) $pillar['link_label']); ?> <span aria-hidden="true">→</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'proof':
                ?>
                <!-- Social Proof & Metrics Section -->
                <section class="story-section story-proof-section">
                    <div class="story-shell">
                        <div class="story-section__heading story-section__heading--center">
                            <?php if ($copy('proof_eyebrow') !== '') : ?>
                                <p class="eyebrow"><?php echo esc_html($copy('proof_eyebrow')); ?></p>
                            <?php endif; ?>
                            <h2><?php echo esc_html($copy('proof_title')); ?></h2>
                            <?php if ($copy('proof_lead') !== '') : ?>
                                <p><?php echo esc_html($copy('proof_lead')); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($stats)) : ?>
                            <div class="story-stats-grid">
                                <?php foreach ($stats as $stat) :
                                    $is_highlight = !empty($stat['is_highlight']) && (string) $stat['is_highlight'] === '1';
                                    ?>
                                    <div class="story-stat-card <?php echo $is_highlight ? 'story-stat-card--highlight' : ''; ?>">
                                        <div class="story-stat-card__val">
                                            <?php echo esc_html((string) ($stat['value'] ?? '')); ?>
                                            <?php if (!empty($stat['unit'])) : ?>
                                                <small><?php echo esc_html((string) $stat['unit']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <p class="story-stat-card__label"><?php echo esc_html((string) ($stat['label'] ?? '')); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'values':
                ?>
                <!-- Values & Principles Section -->
                <section class="story-section story-values-section">
                    <div class="story-shell">
                        <div class="story-section__heading">
                            <?php if ($copy('values_eyebrow') !== '') : ?>
                                <p class="eyebrow"><?php echo esc_html($copy('values_eyebrow')); ?></p>
                            <?php endif; ?>
                            <h2><?php echo esc_html($copy('values_title')); ?></h2>
                            <?php if ($copy('values_lead') !== '') : ?>
                                <p><?php echo esc_html($copy('values_lead')); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($values)) : ?>
                            <div class="story-values-grid">
                                <?php foreach ($values as $value_item) : ?>
                                    <div class="story-value-card">
                                        <?php if (!empty($value_item['icon'])) : ?>
                                            <div class="story-value-card__icon"><?php echo esc_html((string) $value_item['icon']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($value_item['tag'])) : ?>
                                            <div class="story-value-card__tag"><?php echo esc_html((string) $value_item['tag']); ?></div>
                                        <?php endif; ?>
                                        <h3><?php echo esc_html((string) ($value_item['title'] ?? '')); ?></h3>
                                        <p><?php echo nl2br(esc_html((string) ($value_item['desc'] ?? ''))); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'final':
                ?>
                <!-- Final Manifesto & High-Converting CTA Box -->
                <section class="story-cta-section">
                    <div class="story-shell">
                        <div class="story-cta-box">
                            <div class="story-cta-box__glow" aria-hidden="true"></div>
                            <div class="story-cta-box__content">
                                <?php if ($copy('final_pill') !== '') : ?>
                                    <span class="story-pill story-pill--light"><?php echo esc_html($copy('final_pill')); ?></span>
                                <?php endif; ?>
                                <h2><?php echo nl2br(esc_html($copy('final_title'))); ?></h2>
                                <?php if ($copy('final_text') !== '') : ?>
                                    <p><?php echo nl2br(esc_html($copy('final_text'))); ?></p>
                                <?php endif; ?>
                                <div class="story-cta-box__actions">
                                    <?php if ($copy('final_primary_label') !== '') : ?>
                                        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($contact_url); ?>">
                                            <?php echo esc_html($copy('final_primary_label')); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($copy('final_secondary_label') !== '') : ?>
                                        <a class="myliba-button myliba-button--ghost myliba-button--ghost-light" href="<?php echo esc_url($demo_url); ?>">
                                            <?php echo esc_html($copy('final_secondary_label')); ?>
                                        </a>
                                    <?php endif; ?>
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
