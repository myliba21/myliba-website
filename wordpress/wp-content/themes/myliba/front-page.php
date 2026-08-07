<?php
get_header();

$post_id = get_queried_object_id();
$products = static fn() => myliba_get_entries('myliba_product', 9);
$performance_images = myliba_home_media_images('performance', $post_id);
$hero_metrics = myliba_home_rows('hero_metrics');
$hero_proof = myliba_home_lines('hero_proof');
$hero_slides = myliba_home_hero_slides($post_id);
$trust_items = myliba_home_lines('trust_items');
$social_proof_items = myliba_home_rows('social_proof_items');
$offering_rows = myliba_home_rows('offering_rows');
$problem_cards = myliba_home_rows('problem_cards');
$strategy_flow_steps = myliba_home_rows('strategy_flow_steps');
$performance_tabs = myliba_home_rows('performance_tabs');
$academy_items = myliba_home_lines('academy_items');
$outcomes_cards = myliba_home_rows('outcomes_cards');
$role_gain_rows = myliba_home_rows('role_gains_rows');
$homepage_faq_items = myliba_faq_pairs((string) myliba_home_value('faq_items'));

foreach (myliba_home_sections($post_id) as $section) {
    if (empty($section['enabled'])) {
        continue;
    }

    switch ($section['key']) {
        case 'hero':
            ?>
            <section class="hero-slider" data-hero-slider aria-roledescription="carousel"
                aria-label="<?php echo esc_attr(myliba_text('Myliba highlights')); ?>">
                <div class="hero-slider__viewport" aria-live="off">
                    <?php foreach ($hero_slides as $index => $slide):
                        $image = $slide['image'] ?? [];
                        ?>
                        <article id="hero-slide-<?php echo esc_attr((string) ($slide['id'] ?? $index)); ?>"
                            class="hero-slide <?php echo $index === 0 ? 'is-active' : ''; ?>" data-hero-slide
                            aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                            <div class="hero-slide__content">
                                <p class="eyebrow"><?php echo esc_html((string) ($slide['eyebrow'] ?? '')); ?></p>
                                <<?php echo $index === 0 ? 'h1' : 'h2'; ?> class="hero-slide__title"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></<?php echo $index === 0 ? 'h1' : 'h2'; ?>>
                                <p class="hero-slide__text"><?php echo esc_html((string) ($slide['text'] ?? '')); ?></p>
                                <?php if (!empty($slide['buttons'])): ?>
                                    <div class="hero__actions">
                                        <?php foreach ($slide['buttons'] as $button):
                                            $button_style = in_array(($button['style'] ?? ''), ['primary', 'ghost', 'link'], true) ? $button['style'] : 'ghost';
                                            $button_class = $button_style === 'link' ? 'myliba-button myliba-button--ghost myliba-button--link' : 'myliba-button myliba-button--' . $button_style;
                                            ?>
                                            <a class="<?php echo esc_attr($button_class); ?>" href="<?php echo esc_url((string) $button['url']); ?>"
                                                <?php echo !empty($button['new_tab']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
                                                <?php echo !empty($button['aria_label']) ? 'aria-label="' . esc_attr((string) $button['aria_label']) . '"' : ''; ?>
                                                <?php echo $index === 0 ? '' : 'tabindex="-1"'; ?>><?php echo esc_html((string) $button['label']); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <!--  <div class="hero__proof">
                                    <?php foreach ($hero_proof as $item): ?><span><?php echo esc_html($item); ?></span><?php endforeach; ?>
                                </div> -->
                            </div>
                            <div class="hero-slide__visual-wrap">
                                <div class="hero-slide__visual">
                                    <?php if (!empty($image['url'])): ?>
                                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>"
                                            <?php echo !empty($image['width']) ? 'width="' . esc_attr((string) $image['width']) . '"' : ''; ?>
                                            <?php echo !empty($image['height']) ? 'height="' . esc_attr((string) $image['height']) . '"' : ''; ?>
                                            loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>" <?php echo $index === 0 ? 'fetchpriority="high"' : ''; ?> decoding="async">
                                    <?php else: ?>
                                        <div class="hero-slide__placeholder" aria-hidden="true"></div>
                                    <?php endif; ?>
                                </div>
                                <?php foreach (array_slice($hero_metrics, 0, 3) as $metric_index => $metric):
                                    [$metric_value, $metric_label] = array_pad($metric, 2, '');
                                    $metric_classes = ['companies', 'leaders', 'impact'];
                                    ?>
                                    <div class="hero-slide__metric hero-slide__metric--<?php echo esc_attr($metric_classes[$metric_index]); ?>"
                                        aria-hidden="true">
                                        <span></span><strong><?php echo esc_html($metric_value); ?></strong><small><?php echo esc_html($metric_label); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if (count($hero_slides) > 1): ?>
                    <div class="hero-slider__controls">
                        <button class="hero-slider__arrow" type="button" data-hero-prev
                            aria-label="<?php echo esc_attr(myliba_text('Previous slide')); ?>">&#8592;</button>
                        <div class="hero-slider__dots" role="tablist" aria-label="<?php echo esc_attr(myliba_text('Choose slide')); ?>">
                            <?php foreach ($hero_slides as $index => $row): ?><button
                                    class="<?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" role="tab"
                                    aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    aria-controls="hero-slide-<?php echo esc_attr((string) ($row['id'] ?? $index)); ?>" data-hero-dot
                                    data-slide-label="<?php echo esc_attr(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?>"></button><?php endforeach; ?>
                        </div>
                        <button class="hero-slider__arrow" type="button" data-hero-next
                            aria-label="<?php echo esc_attr(myliba_text('Next slide')); ?>">&#8594;</button>
                    </div>
                <?php endif; ?>
            </section>
            <?php
            break;

        case 'trust_bar':
            get_template_part('template-parts/client-logo-marquee', null, [
                'label' => myliba_home_value('trust_logo_label'),
                'title' => myliba_home_value('trust_title'),
                'text' => myliba_text(''),
                'fallback_items' => $trust_items,
            ]);
            break;

        case 'social_proof':
            ?>
            <section class="section social-proof-section">
                <div class="proof-grid" aria-label="<?php echo esc_attr(myliba_text('Myliba in numbers')); ?>">
                    <?php foreach ($social_proof_items as $index => $item):
                        [$value, $label] = array_pad($item, 2, '');
                        ?>
                        <div class="proof-stat">
                            <span class="proof-stat__icon" aria-hidden="true">
                                <?php if ($index % 6 === 0): ?>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" />
                                        <path d="m8.8 14-1.3 7 4.5-2.5 4.5 2.5-1.3-7" />
                                        <path d="m10 10 1.3 1.3L14 8.6" />
                                    </svg>
                                <?php elseif ($index % 6 === 1): ?>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 21V7l8-4 8 4v14" />
                                        <path d="M8 9h2m4 0h2M8 13h2m4 0h2M8 17h2m4 0h2M2 21h20" />
                                    </svg>
                                <?php elseif ($index % 6 === 2): ?>
                                    <svg viewBox="0 0 24 24">
                                        <path d="m12 3 9 5-9 5-9-5 9-5Z" />
                                        <path d="m3 12 9 5 9-5M3 16l9 5 9-5" />
                                    </svg>
                                <?php elseif ($index % 6 === 3): ?>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M16 20v-1.5c0-2.5-1.8-4.5-4-4.5H7c-2.2 0-4 2-4 4.5V20" />
                                        <circle cx="9.5" cy="7.5" r="3.5" />
                                        <path d="M17 11a3 3 0 1 0-2.4-4.8M18 14c1.8.4 3 2.1 3 4.2V20" />
                                    </svg>
                                <?php elseif ($index % 6 === 4): ?>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M7 3h10v4H7z" />
                                        <path d="M5 5H3v4a4 4 0 0 0 4 4m12-8h2v4a4 4 0 0 1-4 4M12 7v11m-4 3h8M9 18h6" />
                                    </svg>
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 21a9 9 0 1 0-9-9" />
                                        <path d="M3 16v5h5M8 12l2.5 2.5L16 9" />
                                    </svg>
                                <?php endif; ?>
                            </span>
                            <span class="proof-stat__content">
                                <h3><?php echo esc_html($value); ?></h3><span><?php echo esc_html($label); ?></span>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'why_myliba':
            ?>
            <section class="section why-myliba-section">
                <div class="why-myliba">
                    <div class="why-myliba__intro">
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('why_eyebrow')); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('why_title')); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('why_text')); ?></p>
                    </div>
                    <div class="offering-cards">
                        <?php foreach ($offering_rows as $index => $row):
                            [$label, $intro, $item_1_title, $item_1_text, $item_2_title, $item_2_text, $item_3_title, $item_3_text, $item_4_title, $item_4_text, $cta_label, $cta_url] = array_pad($row, 12, '');
                            $benefits = array_filter(
                                [
                                    [$item_1_title, $item_1_text],
                                    [$item_2_title, $item_2_text],
                                    [$item_3_title, $item_3_text],
                                    [$item_4_title, $item_4_text],
                                ],
                                static fn(array $benefit): bool => $benefit[0] !== '' || $benefit[1] !== ''
                            );
                            ?>
                            <article class="offering-card offering-card--<?php echo esc_attr((string) (($index % 2) + 1)); ?>">
                                <header class="offering-card__header">
                                    <h3><?php echo esc_html($label); ?></h3>
                                </header>
                                <?php if ($intro !== ''): ?>
                                    <p class="offering-card__lead"><?php echo esc_html($intro); ?></p><?php endif; ?>
                                <div class="offering-card__benefits">
                                    <?php foreach ($benefits as $benefit): ?>
                                        <div class="offering-card__benefit">
                                            <?php if ($benefit[0] !== ''): ?><strong><?php echo esc_html($benefit[0]); ?></strong><?php endif; ?>
                                            <?php if ($benefit[1] !== ''): ?>
                                                <p><?php echo esc_html($benefit[1]); ?></p><?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($cta_label !== ''): ?>
                                    <a class="offering-card__cta" href="<?php echo esc_url($cta_url); ?>">
                                        <span><?php echo esc_html($cta_label); ?></span><span aria-hidden="true">&#8594;</span>
                                    </a>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'problem':
            ?>
            <section class="section homepage-problem">
                <div class="section__heading homepage-section-heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('problem_eyebrow')); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('problem_title')); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('problem_text')); ?></p>
                </div>
                <div class="homepage-card-grid homepage-card-grid--three">
                    <?php foreach ($problem_cards as $card):
                        [$title, $text] = array_pad($card, 2, '');
                        ?>
                        <article class="homepage-card homepage-card--problem">
                            <span
                                class="homepage-card__icon"><?php echo esc_html(function_exists('mb_substr') ? mb_substr($title, 0, 1) : substr($title, 0, 1)); ?></span>
                            <h3><?php echo esc_html($title); ?></h3>
                            <p><?php echo esc_html($text); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'products':
            $product_query = $products();
            ?>
            <section class="section product-suite-section">
                <div class="section__heading product-suite__heading">
                    <div>
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('solution_eyebrow')); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('solution_title')); ?></h2>
                    </div>
                    <a class="product-suite__overview" href="<?php echo esc_url(myliba_page_url('products')); ?>">
                        <?php echo esc_html(myliba_home_value('products_button')); ?>
                    </a>
                </div>
                <div class="module-matrix">
                    <?php while ($product_query->have_posts()):
                        $product_query->the_post(); ?>
                        <a class="module-card module-card--compact" href="<?php the_permalink(); ?>">
                            <span class="module-card__topline">
                                <span class="module-card__icon"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                                <strong><?php echo esc_html(myliba_home_value('module_button')); ?></strong>
                            </span>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 14)); ?></p>
                        </a>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            </section>
            <?php
            break;

        case 'academy':
            ?>
            <section class="section academy-spotlight-section">
                <div class="academy-spotlight">
                    <div class="academy-spotlight__content">
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('academy_eyebrow')); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('academy_title')); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('academy_text')); ?></p>
                        <div class="academy-spotlight__actions">
                            <a class="myliba-button myliba-button--primary"
                                href="<?php echo esc_url(myliba_page_url('academy')); ?>"><?php echo esc_html(myliba_home_value('academy_button')); ?></a>
                            <a class="myliba-button myliba-button--ghost"
                                href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_option('demo_cta_label')); ?></a>
                        </div>
                    </div>
                    <div class="academy-spotlight__stack">
                        <?php foreach ($academy_items as $item): ?>
                            <span><?php echo esc_html($item); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'role_gains':
            ?>
            <section class="section band role-gains-section">
                <div class="section__heading homepage-section-heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('role_gains_eyebrow')); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('role_gains_title')); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('role_gains_text')); ?></p>
                </div>
                <div class="role-gains" data-role-gains>
                    <div class="role-gains__tabs" role="tablist" aria-label="<?php echo esc_attr(myliba_text('Role gains')); ?>">
                        <?php foreach ($role_gain_rows as $index => $row):
                            [$label] = array_pad($row, 1, '');
                            $tab_id = 'role-gain-tab-' . $index;
                            $panel_id = 'role-gain-panel-' . $index;
                            ?>
                            <button class="role-gains__tab <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                id="<?php echo esc_attr($tab_id); ?>" type="button" role="tab"
                                aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo esc_attr($panel_id); ?>" data-role-tab>
                                <?php echo esc_html($label); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="role-gains__panels">
                        <?php foreach ($role_gain_rows as $index => $row):
                            [$label, $title, $text, $primary_label, $primary_text, $secondary_label, $secondary_text] = array_pad($row, 7, '');
                            $tab_id = 'role-gain-tab-' . $index;
                            $panel_id = 'role-gain-panel-' . $index;
                            ?>
                            <div class="role-gains__panel <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                id="<?php echo esc_attr($panel_id); ?>" role="tabpanel"
                                aria-labelledby="<?php echo esc_attr($tab_id); ?>" <?php echo $index === 0 ? '' : 'hidden'; ?>
                                data-role-panel>
                                <span><?php echo esc_html($label); ?></span>
                                <h3><?php echo esc_html($title); ?></h3>
                                <p><?php echo esc_html($text); ?></p>
                                <div class="role-gains__metrics">
                                    <div>
                                        <strong><?php echo esc_html($primary_label); ?></strong>
                                        <small><?php echo esc_html($primary_text); ?></small>
                                    </div>
                                    <div>
                                        <strong><?php echo esc_html($secondary_label); ?></strong>
                                        <small><?php echo esc_html($secondary_text); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'solutions':
            ?>
            <section class="section band strategy-flow-section">
                <div class="section__heading homepage-section-heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('strategy_flow_eyebrow')); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('strategy_flow_title')); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('strategy_flow_text')); ?></p>
                </div>
                <div class="strategy-flow">
                    <?php foreach ($strategy_flow_steps as $index => $step):
                        [$title, $text, $short_label] = array_pad($step, 3, '');
                        ?>
                        <article class="strategy-flow__step">
                            <span
                                class="strategy-flow__badge"><?php echo esc_html($short_label !== '' ? $short_label : str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <h3><?php echo esc_html($title); ?></h3>
                            <p><?php echo esc_html($text); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'performance':
            ?>
            <section class="section band strategy-flow-section performance-method-section">
                <div class="performance-method">
                    <div class="section__heading homepage-section-heading">
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('performance_eyebrow')); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('performance_title')); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('performance_text')); ?></p>
                    </div>
                    <div class="performance-tabs" data-home-tabs>
                        <div class="performance-tabs__nav" role="tablist"
                            aria-label="<?php echo esc_attr(myliba_text('Performance management capabilities')); ?>">
                            <?php foreach ($performance_tabs as $index => $row):
                                [$label] = array_pad($row, 1, '');
                                ?>
                                <button id="performance-tab-<?php echo esc_attr((string) $index); ?>"
                                    class="<?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" role="tab"
                                    aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    aria-controls="performance-panel-<?php echo esc_attr((string) $index); ?>"
                                    data-home-tab><?php echo esc_html($label); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <?php foreach ($performance_tabs as $index => $row):
                            [$label, $title, $text] = array_pad($row, 3, '');
                            $image_pool = $performance_images ?: $hero_banner_images;
                            $image = $image_pool ? $image_pool[$index % count($image_pool)] : [];
                            ?>
                            <div id="performance-panel-<?php echo esc_attr((string) $index); ?>"
                                class="performance-tabs__panel <?php echo $index === 0 ? 'is-active' : ''; ?>" role="tabpanel"
                                aria-labelledby="performance-tab-<?php echo esc_attr((string) $index); ?>" data-home-panel <?php echo $index === 0 ? '' : 'hidden'; ?>>
                                <div><span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                    <h3><?php echo esc_html($title); ?></h3>
                                    <p><?php echo esc_html($text); ?></p><a class="myliba-button myliba-button--primary"
                                        href="<?php echo esc_url(myliba_page_url('products')); ?>"><?php echo esc_html(myliba_home_value('performance_button')); ?></a>
                                </div>
                                <?php if (!empty($image['url'])): ?>
                                    <figure><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>"
                                            loading="lazy" decoding="async"></figure><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'outcomes':
            ?>
            <section class="section outcomes-section">
                <div class="section__heading homepage-section-heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('outcomes_eyebrow')); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('outcomes_title')); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('outcomes_text')); ?></p>
                </div>
                <div class="homepage-card-grid homepage-card-grid--three">
                    <?php foreach ($outcomes_cards as $card):
                        [$title, $text] = array_pad($card, 2, '');
                        ?>
                        <article class="homepage-card homepage-card--outcome">
                            <span
                                class="homepage-card__icon"><?php echo esc_html(function_exists('mb_substr') ? mb_substr($title, 0, 1) : substr($title, 0, 1)); ?></span>
                            <h3><?php echo esc_html($title); ?></h3>
                            <p><?php echo esc_html($text); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'resources':
            $posts = new WP_Query([
                'post_type' => 'post',
                'posts_per_page' => 3,
                'meta_key' => '_myliba_language',
                'meta_value' => myliba_current_language(),
            ]);
            ?>
            <section class="section band resources-section">
                <div class="resources-section__heading">
                    <div>
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('resources_eyebrow')); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('resources_title')); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('resources_text')); ?></p>
                    </div>
                    <a class="product-suite__overview" href="<?php echo esc_url(myliba_page_url('blog')); ?>">
                        <?php echo esc_html(myliba_home_value('resources_button')); ?>
                    </a>
                </div>
                <div class="resources-grid">
                    <?php while ($posts->have_posts()):
                        $posts->the_post(); ?>
                        <a class="resource-card" href="<?php the_permalink(); ?>">
                            <span><?php echo esc_html(get_the_date()); ?> &middot; <?php echo esc_html(myliba_reading_time()); ?>
                                <?php echo esc_html(myliba_text('min read')); ?></span>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 18)); ?></p>
                        </a>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            </section>
            <?php
            break;

        case 'faq':
            $faq_query = myliba_get_entries('myliba_faq', 6);
            $faq_items = [];
            if ($faq_query->have_posts()) {
                while ($faq_query->have_posts()) {
                    $faq_query->the_post();
                    $faq_items[] = [
                        'question' => myliba_translate_text(get_the_title()),
                        'answer' => myliba_translate_text(wp_strip_all_tags(get_the_content())),
                    ];
                }
                wp_reset_postdata();
            }
            if (!$faq_items) {
                $faq_items = $homepage_faq_items;
            }
            ?>
            <section class="section faq-section">
                <div class="section__heading homepage-section-heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('faq_eyebrow')); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('faq_title')); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('faq_text')); ?></p>
                </div>
                <div class="faq-accordion">
                    <?php foreach ($faq_items as $index => $faq_item): ?>
                        <details class="faq-accordion__item" <?php echo $index === 0 ? 'open' : ''; ?>>
                            <summary><?php echo esc_html($faq_item['question']); ?></summary>
                            <p><?php echo esc_html($faq_item['answer']); ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'final_cta':
            ?>
            <section class="section final-cta-section">
                <div class="final-cta">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('final_cta_eyebrow')); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('final_cta_title')); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('final_cta_text')); ?></p>
                    <div class="final-cta__actions">
                        <a class="myliba-button myliba-button--primary"
                            href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_home_value('final_cta_primary_label')); ?></a>
                        <a class="myliba-button myliba-button--ghost"
                            href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html(myliba_home_value('final_cta_secondary_label')); ?></a>
                    </div>
                </div>
            </section>
            <?php
            break;

    }
}

get_footer();
