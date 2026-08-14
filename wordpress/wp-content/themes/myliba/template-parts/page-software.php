<?php
if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();
$demo_url = myliba_demo_url();
$copy = static fn (string $key): string => \Myliba\Core\PageContent\text($page_id, 'software', $key);
$rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($page_id, 'software', $key);
$modules = $rows('modules');
$stats = $rows('stats');
$faqs = $rows('faqs');
$workflow_steps = $rows('workflow_steps');
$assurance_cards = $rows('assurance_cards');

get_header();
?>

<div class="software-page">
    <section class="software-hero">
        <div class="software-hero__content">
            <p class="eyebrow"><?php echo esc_html($copy('hero_eyebrow_primary')); ?> <span>|</span> <?php echo esc_html($copy('hero_eyebrow_secondary')); ?></p>
            <h1><?php echo esc_html($copy('hero_title_start')); ?> <em><?php echo esc_html($copy('hero_title_emphasis')); ?></em> <?php echo esc_html($copy('hero_title_end')); ?></h1>
            <p class="software-hero__lead"><?php echo esc_html($copy('hero_lead')); ?></p>
            <div class="software-hero__actions">
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html($copy('hero_primary_label')); ?></a>
                <a class="myliba-button myliba-button--ghost" href="#moduller"><?php echo esc_html($copy('hero_secondary_label')); ?> <span
                        aria-hidden="true">↓</span></a>
            </div>
            <div class="software-hero__proof" aria-label="<?php echo esc_attr($copy('hero_eyebrow_primary')); ?>">
                <?php foreach ($rows('hero_proof') as $proof) : ?>
                    <span><i></i> <?php echo esc_html((string) ($proof['label'] ?? '')); ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
        $hero_image_id = absint($copy('hero_image'));
        $hero_image_alt = $copy('hero_image_alt');
        ?>
        <div class="software-hero__visual-wrap">
            <div class="software-hero__visual">
                <?php if ($hero_image_id) : ?>
                    <?php echo wp_get_attachment_image($hero_image_id, 'full', false, [
                        'alt' => $hero_image_alt ?: get_post_meta($hero_image_id, '_wp_attachment_image_alt', true) ?: ($copy('hero_title_start') . ' ' . $copy('hero_title_emphasis')),
                        'loading' => 'eager',
                        'fetchpriority' => 'high',
                        'decoding' => 'async',
                    ]); ?>
                <?php else : ?>
                    <div class="software-hero__placeholder" aria-hidden="true"></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/client-logo-marquee', null, [
        'label' => $copy('trust_label'),
        'title' => $copy('trust_title'),
        'text' => $copy('trust_text'),
        'class' => 'software-trust-section',
        'heading_id' => 'software-trust-title',
    ]); ?>

    <section id="moduller" class="software-section software-modules">
        <div class="software-section__heading">
            <div>
                <p class="eyebrow"><?php echo esc_html($copy('modules_eyebrow')); ?></p>
                <h2><?php echo esc_html($copy('modules_title')); ?></h2>
            </div>
            <p><?php echo esc_html($copy('modules_text')); ?> <strong><?php echo esc_html($copy('modules_text_strong')); ?></strong></p>
        </div>
        <div class="software-modules__grid">
            <?php foreach ($modules as $module_index => $module): ?>
                <article>
                    <div class="software-module__top">
                        <span><?php echo esc_html((string) ($module['number'] ?? '')); ?></span>
                        <i aria-hidden="true">↗</i>
                    </div>
                    <div class="software-module-visual software-module-visual--<?php echo esc_attr((string) ($module_index + 1)); ?>" aria-hidden="true">
                        <?php if ($module_index === 0): ?>
                            <span class="software-module-visual__node is-primary"></span>
                            <span class="software-module-visual__node"></span>
                            <span class="software-module-visual__node"></span>
                            <span class="software-module-visual__node"></span>
                            <i></i><i></i><i></i>
                        <?php elseif ($module_index === 1): ?>
                            <span style="--value: 72%"></span><span style="--value: 46%"></span><span style="--value: 88%"></span><span style="--value: 62%"></span>
                        <?php elseif ($module_index === 2): ?>
                            <span><i></i><b></b></span><span><i></i><b></b></span><span><i></i><b></b></span>
                        <?php else: ?>
                            <?php for ($cell = 0; $cell < 9; $cell++): ?><span></span><?php endfor; ?>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo esc_html((string) ($module['title'] ?? '')); ?></h3>
                    <p><?php echo esc_html((string) ($module['text'] ?? '')); ?></p>
                    <ul>
                        <?php foreach (myliba_lines((string) ($module['items'] ?? '')) as $item): ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="software-workflow" aria-labelledby="software-workflow-title">
        <div class="software-workflow__inner">
            <header class="software-workflow__heading">
                <div>
                    <p class="eyebrow"><?php echo esc_html($copy('workflow_eyebrow')); ?></p>
                    <h2 id="software-workflow-title"><?php echo esc_html($copy('workflow_title')); ?></h2>
                </div>
                <p><?php echo esc_html($copy('workflow_text')); ?></p>
            </header>
            <div class="software-workflow__grid">
                <?php foreach ($workflow_steps as $index => $step): ?>
                    <article>
                        <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <div class="software-workflow__pulse" aria-hidden="true"><i></i><i></i><i></i></div>
                        <h3><?php echo esc_html((string) ($step['title'] ?? '')); ?></h3>
                        <p><?php echo esc_html((string) ($step['text'] ?? '')); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="software-why">
        <div class="software-why__inner">
            <div class="software-why__copy">
                <p class="eyebrow"><?php echo esc_html($copy('why_eyebrow')); ?></p>
                <h2><?php echo esc_html($copy('why_title')); ?></h2>
                <div class="software-formula">
                    <small><?php echo esc_html($copy('why_formula_label')); ?></small>
                    <strong><?php echo esc_html($copy('why_formula_left')); ?> <span>=</span> <?php echo esc_html($copy('why_formula_first')); ?> <span>−</span> <?php echo esc_html($copy('why_formula_second')); ?></strong>
                </div>
                <p><?php echo esc_html($copy('why_text')); ?></p>
            </div>
            <div class="software-stats">
                <?php foreach ($stats as $index => $stat): ?>
                    <article>
                        <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <strong><?php echo esc_html((string) ($stat['value'] ?? '')); ?></strong>
                        <h3><?php echo esc_html((string) ($stat['label'] ?? '')); ?></h3>
                        <p><?php echo esc_html((string) ($stat['text'] ?? '')); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="software-section software-assurance" aria-labelledby="software-assurance-title">
        <div class="software-assurance__heading">
            <div>
                <p class="eyebrow"><?php echo esc_html($copy('assurance_eyebrow')); ?></p>
                <h2 id="software-assurance-title"><?php echo esc_html($copy('assurance_title')); ?></h2>
            </div>
            <p><?php echo esc_html($copy('assurance_text')); ?></p>
        </div>
        <div class="software-assurance__grid">
            <?php foreach ($assurance_cards as $card) : ?>
                <article>
                    <span><?php echo esc_html((string) ($card['number'] ?? '')); ?></span>
                    <i aria-hidden="true"><?php echo esc_html((string) ($card['icon'] ?? '')); ?></i>
                    <h3><?php echo esc_html((string) ($card['title'] ?? '')); ?></h3>
                    <p><?php echo esc_html((string) ($card['text'] ?? '')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="software-assurance__rail" aria-label="<?php echo esc_attr($copy('assurance_rail_aria')); ?>">
            <?php foreach ($rows('assurance_rail') as $rail_index => $rail_item) : ?>
                <?php if ($rail_index > 0) : ?><i></i><?php endif; ?><span><?php echo esc_html((string) ($rail_item['label'] ?? '')); ?></span>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="software-section software-faq">
        <div class="software-faq__heading">
            <p class="eyebrow"><?php echo esc_html($copy('faq_eyebrow')); ?></p>
            <h2><?php echo esc_html($copy('faq_title')); ?></h2>
            <p><?php echo esc_html($copy('faq_text')); ?></p>
        </div>
        <div class="software-faq__items">
            <?php foreach ($faqs as $index => $faq): ?>
                <details <?php echo $index === 0 ? 'open' : ''; ?>>
                    <summary>
                        <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span><strong><?php echo esc_html((string) ($faq['question'] ?? '')); ?></strong><i
                            aria-hidden="true"></i></summary>
                    <div>
                        <p><?php echo esc_html((string) ($faq['answer'] ?? '')); ?></p>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="software-final">
        <div class="software-final__content">
            <p class="eyebrow"><?php echo esc_html($copy('final_eyebrow')); ?></p>
            <h2><?php echo esc_html($copy('final_title')); ?></h2>
            <p><?php echo esc_html($copy('final_text')); ?></p>
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html($copy('final_button_label')); ?> <span aria-hidden="true">→</span></a>
        </div>
        <div class="software-final__signal" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
    </section>
</div>

<?php get_footer(); ?>
