<?php
if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();
$copy = static fn (string $key): string => \Myliba\Core\PageContent\text($page_id, 'ethics', $key);
$rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($page_id, 'ethics', $key);
$sections = \Myliba\Core\PageContent\sections($page_id, 'ethics');

$contact_url = myliba_page_url('contact');
$why_items = $rows('why_items');
$scope_items = $rows('scope_items');

get_header();
?>

<div class="ethics-page">
    <?php foreach ($sections as $section) :
        if (empty($section['enabled'])) {
            continue;
        }

        switch ($section['key']) {
            case 'hero':
                ?>
                <!-- Hero Section -->
                <section class="ethics-hero">
                    <div class="ethics-shell">
                        <div class="ethics-hero__header">
                            <h1 class="ethics-hero__title">
                                <?php echo esc_html($copy('hero_title')); ?>
                            </h1>

                            <?php if ($copy('hero_lead') !== '') : ?>
                                <p class="ethics-hero__lead">
                                    <?php echo nl2br(esc_html($copy('hero_lead'))); ?>
                                </p>
                            <?php endif; ?>

                            <div class="ethics-hero__actions">
                                <?php if ($copy('hero_primary_label') !== '') : ?>
                                    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($contact_url); ?>">
                                        <?php echo esc_html($copy('hero_primary_label')); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($copy('hero_secondary_label') !== '') : ?>
                                    <a class="myliba-button myliba-button--ghost" href="#neden-etik-hat">
                                        <?php echo esc_html($copy('hero_secondary_label')); ?> <span aria-hidden="true">↓</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                break;

            case 'intro':
                ?>
                <!-- Introduction & Statement -->
                <section class="ethics-section ethics-intro-section">
                    <div class="ethics-shell">
                        <div class="ethics-intro-card">
                            <div class="ethics-intro-card__content">
                                <?php if ($copy('intro_eyebrow') !== '') : ?>
                                    <p class="eyebrow"><?php echo esc_html($copy('intro_eyebrow')); ?></p>
                                <?php endif; ?>
                                <h2><?php echo esc_html($copy('intro_title')); ?></h2>
                                <p class="ethics-intro-card__text"><?php echo nl2br(esc_html($copy('intro_lead'))); ?></p>
                                <?php if ($copy('intro_highlight') !== '') : ?>
                                    <p class="ethics-intro-card__highlight">
                                        <strong>Myliba</strong>, <?php echo esc_html(ltrim(str_replace('Myliba,', '', $copy('intro_highlight')))); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                break;

            case 'why':
                ?>
                <!-- Why Ethics Line -->
                <section id="neden-etik-hat" class="ethics-section ethics-why-section">
                    <div class="ethics-shell">
                        <div class="ethics-section__heading">
                            <?php if ($copy('why_eyebrow') !== '') : ?>
                                <p class="eyebrow"><?php echo esc_html($copy('why_eyebrow')); ?></p>
                            <?php endif; ?>
                            <h2><?php echo esc_html($copy('why_title')); ?></h2>
                        </div>

                        <?php if (!empty($why_items)) : ?>
                            <div class="ethics-why-grid">
                                <?php foreach ($why_items as $index => $item) : ?>
                                    <article class="ethics-why-card">
                                        <div class="ethics-why-card__bullet" aria-hidden="true">•</div>
                                        <div class="ethics-why-card__body">
                                            <h3><?php echo esc_html((string) ($item['title'] ?? '')); ?>:</h3>
                                            <p><?php echo esc_html((string) ($item['desc'] ?? '')); ?></p>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                break;

            case 'scope':
                ?>
                <!-- Service Scope & Features -->
                <section id="hizmet-kapsami" class="ethics-section ethics-scope-section">
                    <div class="ethics-shell">
                        <div class="ethics-section__heading">
                            <?php if ($copy('scope_eyebrow') !== '') : ?>
                                <p class="eyebrow"><?php echo esc_html($copy('scope_eyebrow')); ?></p>
                            <?php endif; ?>
                            <h2><?php echo esc_html($copy('scope_title')); ?></h2>
                        </div>

                        <div class="ethics-scope-box">
                            <?php if ($copy('scope_subtitle') !== '') : ?>
                                <h3 class="ethics-scope-box__title"><?php echo esc_html($copy('scope_subtitle')); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($scope_items)) : ?>
                                <ul class="ethics-scope-list">
                                    <?php foreach ($scope_items as $item) : ?>
                                        <li class="ethics-scope-item">
                                            <span class="ethics-scope-item__icon" aria-hidden="true">✓</span>
                                            <span class="ethics-scope-item__text"><?php echo esc_html((string) ($item['text'] ?? '')); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php
                break;

            case 'cta':
                ?>
                <!-- Contact / CTA Section -->
                <section class="ethics-section ethics-cta-section">
                    <div class="ethics-shell">
                        <div class="ethics-cta-box">
                            <div class="ethics-cta-box__glow" aria-hidden="true"></div>
                            <div class="ethics-cta-box__content">
                                <h2><?php echo esc_html($copy('cta_title')); ?></h2>
                                <?php if ($copy('cta_text') !== '') : ?>
                                    <p><?php echo esc_html($copy('cta_text')); ?></p>
                                <?php endif; ?>
                                <div class="ethics-cta-box__actions">
                                    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($contact_url); ?>">
                                        <?php echo esc_html($copy('cta_button_label')); ?>
                                    </a>
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

<?php
get_footer();
