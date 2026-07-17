<?php
get_header();

$post_id = get_queried_object_id();
$demo_url = myliba_demo_url();
$products = static fn () => myliba_get_entries('myliba_product', 9);
$client_logos = static fn () => myliba_get_entries('myliba_client_logo', 24, ['meta_query' => []]);
$hero_banner_images = myliba_hero_banner_images();
$hero_proof = myliba_home_lines('hero_proof', ['Strategy to action', 'Continuous performance', 'Academy + software']);
$hero_slides = myliba_home_rows('hero_slides', [
    ['Performance & Culture Platform', 'Build the operating system behind high performance.', 'Myliba brings strategy, goals, actions and culture into one measurable flow, so transformation does not stop at another software rollout.', 'Contact us', myliba_page_url('contact'), 'Explore Myliba', myliba_page_url('products')],
    ['Myliba Software', 'Performance is built every day, not scored once a year.', 'Keep strategic priorities alive with a platform designed to connect goals, continuous feedback, action and measurable progress.', 'Request a demo', $demo_url, 'Explore software', myliba_page_url('products')],
    ['Myliba Academy', 'The world’s first ICF-approved OKR & Culture Coaching program.', 'Develop the leaders who will turn a strong platform into lasting organizational change through an accredited 40-hour certification journey.', 'Apply to the program', myliba_page_url('academy'), 'Explore academy', myliba_page_url('academy')],
]);
$trust_items = myliba_home_lines('trust_items', ['OKR', 'KPI', 'CFR', '1:1']);
$social_proof_items = myliba_home_rows('social_proof_items', [
    ['25+', 'Years of HR and organizational development experience'],
    ['44', 'Companies'],
    ['16', 'Industries'],
    ['500+', 'Leaders'],
    ['40 CCE', 'ICF-accredited training'],
    ['100%', 'Living and sustainable culture commitment'],
]);
$offering_rows = myliba_home_rows('offering_rows', [
    ['Myliba Software', 'Manage culture digitally and measurably.', '85% cost advantage', 'Bring performance development into one platform and reduce reliance on fragmented systems.', '40+ days saved', 'Give HR teams and leaders more time for strategic work.', '2x stronger performance', 'Build a fair, evidence-based and development-led performance rhythm.', '67% stronger goals', 'Improve strategic alignment and help teams set more ambitious goals.', 'Explore software', myliba_page_url('products')],
    ['Myliba Academy', 'Develop the leaders who will guide your transformation.', 'A world first', 'Join the first ICF 40 CCE-accredited OKR & Culture Coaching certification program.', 'Community', 'Access an ongoing learning community and complimentary update sessions.', 'Platform', 'Experience culture, leadership and self-discipline through immersive simulations.', 'Transformation at work', 'Turn coaching, powerful questions and strategic leadership into daily practice.', 'Explore academy', myliba_page_url('academy')],
]);
$with_minimum_rows = static function (array $rows, array $fallback): array {
    $fallback = array_map(static function (array $row): array {
        return array_map(static fn ($cell) => is_string($cell) ? myliba_translate_text($cell) : $cell, $row);
    }, $fallback);

    if (count($rows) >= count($fallback)) {
        return $rows;
    }

    return array_merge($rows, array_slice($fallback, count($rows)));
};
$problem_card_defaults = [
    ['Goal hierarchy gets lost', 'Company, team and individual goals cannot be read as one clear contribution map.'],
    ['Performance conversations stay detached', 'Check-ins, feedback and reviews are not connected to active goals and evidence.'],
    ['Feedback remains periodic', 'Recognition, coaching and feedforward arrive too late to change the next action.'],
    ['Strategy does not turn into action', 'Priorities stay in decks instead of becoming owners, deadlines and follow-up routines.'],
    ['Transparency gets harder', 'Leaders, HR and teams cannot see risks, blockers and progress in one operating view.'],
    ['Manual operations cost time', 'Spreadsheets, reminders and status chasing slow down the performance rhythm.'],
];
$problem_cards = $with_minimum_rows(myliba_home_rows('problem_cards', $problem_card_defaults), $problem_card_defaults);
$strategy_flow_steps = myliba_home_rows('strategy_flow_steps', [
    ['Strategy', 'Make priorities visible and shared across the organization.', 'S'],
    ['Goals', 'Connect OKR and KPI ownership from company to teams.', 'G'],
    ['Action', 'Turn each priority into accountable actions and follow-up.', 'A'],
    ['Culture', 'Build 1:1, CFR and learning routines around the work.', 'C'],
]);
$performance_tabs = myliba_home_rows('performance_tabs', [
    ['Goal management', 'Align work with business outcomes.', 'Carry strategy to every level of the organization with connected OKRs, KPIs and action management.'],
    ['Performance development', 'Make development continuous.', 'Reveal potential through ongoing feedback, feedforward and development-focused performance conversations.'],
    ['AI-powered insight', 'Read the DNA of your organization.', 'Spot signals around engagement, belonging and culture early with AI-supported analysis.'],
    ['Decision reports', 'Make fair decisions with evidence.', 'Use goals, actions, leadership signals and 360-degree insights to support objective promotion, reward and development decisions.'],
]);
$academy_items = myliba_home_lines('academy_items', ['OKR culture and adoption programs', 'Leadership and coaching routines', 'Continuous performance development', 'Human and culture-focused transformation']);
$outcomes_card_defaults = [
    ['Alignment', 'Connect company strategy with team and individual contribution.'],
    ['Transparency', 'See progress, blockers and ownership without waiting for meetings.'],
    ['Development', 'Turn 1:1, feedback and coaching into a continuous routine.'],
    ['Execution', 'Transform priorities into actions, ownership and measurable results.'],
    ['Fairer decisions', 'Use evidence from goals, conversations and actions in performance reviews.'],
    ['Risk visibility', 'Spot adoption, blocker and engagement signals before they become late surprises.'],
];
$outcomes_cards = $with_minimum_rows(myliba_home_rows('outcomes_cards', $outcomes_card_defaults), $outcomes_card_defaults);
$role_gain_rows = myliba_home_rows('role_gains_rows', [
    ['CEO / Executive Team', 'Lead strategy with a live operating view', 'See company priorities, team contribution and risk signals without waiting for manual reporting.', 'Strategic visibility', 'Goals, metrics and actions are connected in one screen.', 'Faster decisions', 'Leadership can focus attention on the places that need support.'],
    ['Human Resources', 'Make performance continuous and fair', 'Bring 1:1s, feedback, development notes and review evidence into a manageable rhythm.', 'Process clarity', 'HR can run cycles without spreadsheet-heavy follow-up.', 'Employee growth', 'Development signals stay connected to goals and coaching.'],
    ['Strategy Office', 'Connect priorities with execution', 'Translate strategic choices into OKRs, KPIs, initiatives and ownership that teams can follow.', 'Alignment map', 'Each priority can be traced to goals, owners and actions.', 'Progress rhythm', 'Review routines stay measurable and repeatable.'],
    ['Team Leaders', 'Coach work without losing follow-up', 'Prepare 1:1s, follow actions and give feedback while keeping team goals visible.', 'Manager rhythm', 'Meetings become structured and connected to outcomes.', 'Team focus', 'People know what matters and what changes next.'],
    ['Employees', 'Understand contribution and growth', 'See goals, expectations, feedback and development actions in one place.', 'Role clarity', 'Contribution to company priorities becomes visible.', 'Better feedback', 'Recognition and feedforward are easier to act on.'],
]);
$faq_fallback_items = myliba_faq_pairs((string) myliba_home_value('faq_items', "How is Myliba different from a classic OKR tool? | Myliba combines OKR, KPI, CFR, 1:1, feedback, actions, analytics and academy routines.\nCan Myliba support implementation and training? | Yes. The platform is supported by academy programs, workshops and coaching routines.\nWho uses Myliba most often? | Executive teams, HR, strategy offices, team leaders and employees use different views of the same operating rhythm."));

foreach (myliba_home_sections($post_id) as $section) {
    if (empty($section['enabled'])) {
        continue;
    }

    switch ($section['key']) {
        case 'hero':
            ?>
            <section class="hero-slider" data-hero-slider aria-roledescription="carousel" aria-label="<?php esc_attr_e('Myliba highlights', 'myliba'); ?>">
                <div class="hero-slider__viewport" aria-live="off">
                    <?php foreach ($hero_slides as $index => $row) :
                        [$eyebrow, $title, $text, $primary_label, $primary_url, $secondary_label, $secondary_url] = array_pad($row, 7, '');
                        $image = $hero_banner_images ? $hero_banner_images[$index % count($hero_banner_images)] : [];
                        ?>
                        <article id="hero-slide-<?php echo esc_attr((string) $index); ?>" class="hero-slide <?php echo $index === 0 ? 'is-active' : ''; ?>" data-hero-slide aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                            <div class="hero-slide__content">
                                <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
                                <<?php echo $index === 0 ? 'h1' : 'h2'; ?> class="hero-slide__title"><?php echo esc_html($title); ?></<?php echo $index === 0 ? 'h1' : 'h2'; ?>>
                                <p class="hero-slide__text"><?php echo esc_html($text); ?></p>
                                <div class="hero__actions">
                                    <?php if ($primary_label !== '') : ?><a class="myliba-button myliba-button--primary" href="<?php echo esc_url($primary_url ?: $demo_url); ?>" <?php echo $index === 0 ? '' : 'tabindex="-1"'; ?>><?php echo esc_html($primary_label); ?></a><?php endif; ?>
                                    <?php if ($secondary_label !== '') : ?><a class="myliba-button myliba-button--ghost" href="<?php echo esc_url($secondary_url ?: myliba_page_url('products')); ?>" <?php echo $index === 0 ? '' : 'tabindex="-1"'; ?>><?php echo esc_html($secondary_label); ?></a><?php endif; ?>
                                </div>
                                <div class="hero__proof">
                                    <?php foreach ($hero_proof as $item) : ?><span><?php echo esc_html($item); ?></span><?php endforeach; ?>
                                </div>
                            </div>
                            <div class="hero-slide__visual-wrap">
                                <div class="hero-slide__visual">
                                    <?php if (!empty($image['url'])) : ?>
                                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>" <?php echo $index === 0 ? 'fetchpriority="high"' : ''; ?> decoding="async">
                                    <?php else : ?>
                                        <div class="hero-slide__placeholder"><strong><?php esc_html_e('Myliba Performance OS', 'myliba'); ?></strong><span><?php echo esc_html($eyebrow); ?></span></div>
                                    <?php endif; ?>
                                </div>
                                <div class="hero-slide__metric hero-slide__metric--companies" aria-hidden="true">
                                    <span></span><strong>44</strong><small><?php esc_html_e('companies', 'myliba'); ?></small>
                                </div>
                                <div class="hero-slide__metric hero-slide__metric--leaders" aria-hidden="true">
                                    <span></span><strong>500+</strong><small><?php esc_html_e('leaders', 'myliba'); ?></small>
                                </div>
                                <div class="hero-slide__metric hero-slide__metric--impact" aria-hidden="true">
                                    <span></span><strong>67%</strong><small><?php esc_html_e('stronger goals', 'myliba'); ?></small>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if (count($hero_slides) > 1) : ?>
                    <div class="hero-slider__controls">
                        <button class="hero-slider__arrow" type="button" data-hero-prev aria-label="<?php esc_attr_e('Previous slide', 'myliba'); ?>">&#8592;</button>
                        <div class="hero-slider__dots" role="tablist" aria-label="<?php esc_attr_e('Choose slide', 'myliba'); ?>">
                            <?php foreach ($hero_slides as $index => $row) : ?><button class="<?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="hero-slide-<?php echo esc_attr((string) $index); ?>" data-hero-dot data-slide-label="<?php echo esc_attr(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?>"></button><?php endforeach; ?>
                        </div>
                        <button class="hero-slider__arrow" type="button" data-hero-next aria-label="<?php esc_attr_e('Next slide', 'myliba'); ?>">&#8594;</button>
                    </div>
                <?php endif; ?>
            </section>
            <?php
            break;

        case 'trust_bar':
            $logo_query = $client_logos();
            $logo_posts = [];
            if ($logo_query->have_posts()) {
                while ($logo_query->have_posts()) {
                    $logo_query->the_post();
                    if (has_post_thumbnail()) {
                        $logo_posts[] = get_post();
                    }
                }
                wp_reset_postdata();
            }
            ?>
            <section class="section band trust-section">
                <div class="trust-section__heading">
                    <strong><?php echo esc_html(myliba_home_value('trust_title', __('Built for teams that manage performance culture seriously.', 'myliba'))); ?></strong>
                    <?php if ($logo_posts) : ?>
                        <span><?php esc_html_e('References and partners', 'myliba'); ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($logo_posts) : ?>
                    <div class="trust-marquee" aria-label="<?php esc_attr_e('Client logos', 'myliba'); ?>">
                        <div class="trust-marquee__track">
                            <?php for ($repeat = 0; $repeat < 2; $repeat++) : ?>
                                <?php foreach ($logo_posts as $logo_post) : ?>
                                    <?php
                                    $logo_url = (string) myliba_meta('_myliba_logo_url', $logo_post->ID);
                                    $logo_name = get_the_title($logo_post);
                                    $logo_image = get_the_post_thumbnail($logo_post->ID, 'medium', [
                                        'loading' => 'lazy',
                                        'alt' => $logo_name,
                                    ]);
                                    ?>
                                    <?php if ($logo_url !== '') : ?>
                                        <a class="trust-logo" href="<?php echo esc_url($logo_url); ?>" aria-label="<?php echo esc_attr($logo_name); ?>"><?php echo wp_kses_post($logo_image); ?></a>
                                    <?php else : ?>
                                        <span class="trust-logo"><?php echo wp_kses_post($logo_image); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="trust-row">
                        <?php foreach ($trust_items as $item) : ?><span><?php echo esc_html($item); ?></span><?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="proof-grid" aria-label="<?php esc_attr_e('Myliba in numbers', 'myliba'); ?>">
                    <?php foreach ($social_proof_items as $item) :
                        [$value, $label] = array_pad($item, 2, '');
                        ?>
                        <div class="proof-stat"><strong><?php echo esc_html($value); ?></strong><span><?php echo esc_html($label); ?></span></div>
                    <?php endforeach; ?>
                </div>
                <div class="why-myliba">
                    <div class="why-myliba__intro">
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('why_eyebrow', __('Why Myliba?', 'myliba'))); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('why_title', __('Change cannot be delivered by software or training alone.', 'myliba'))); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('why_text', __('Our formula for cultural transformation combines people, technology and academy. We develop transformation leaders, then make the new operating rhythm part of everyday work.', 'myliba'))); ?></p>
                    </div>
                    <div class="offering-tabs" data-home-tabs>
                        <div class="offering-tabs__nav" role="tablist" aria-label="<?php esc_attr_e('Myliba solutions', 'myliba'); ?>">
                            <?php foreach ($offering_rows as $index => $row) :
                                [$label] = array_pad($row, 1, '');
                                ?>
                                <button id="offering-tab-<?php echo esc_attr((string) $index); ?>" class="<?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="offering-panel-<?php echo esc_attr((string) $index); ?>" data-home-tab><?php echo esc_html($label); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <?php foreach ($offering_rows as $index => $row) :
                            [$label, $intro, $item_1_title, $item_1_text, $item_2_title, $item_2_text, $item_3_title, $item_3_text, $item_4_title, $item_4_text, $cta_label, $cta_url] = array_pad($row, 12, '');
                            ?>
                            <div id="offering-panel-<?php echo esc_attr((string) $index); ?>" class="offering-tabs__panel <?php echo $index === 0 ? 'is-active' : ''; ?>" role="tabpanel" aria-labelledby="offering-tab-<?php echo esc_attr((string) $index); ?>" data-home-panel <?php echo $index === 0 ? '' : 'hidden'; ?>>
                                <p class="offering-tabs__lead"><?php echo esc_html($intro); ?></p>
                                <div class="offering-tabs__benefits">
                                    <?php foreach ([[$item_1_title, $item_1_text], [$item_2_title, $item_2_text], [$item_3_title, $item_3_text], [$item_4_title, $item_4_text]] as $benefit) : ?>
                                        <article><h3><?php echo esc_html($benefit[0]); ?></h3><p><?php echo esc_html($benefit[1]); ?></p></article>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($cta_label !== '') : ?><a class="myliba-button myliba-button--ghost" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a><?php endif; ?>
                            </div>
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
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('problem_eyebrow', __('The problem', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('problem_title', __('Strategy gets lost when goals, actions and feedback live in separate systems.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('problem_text', __('Performance management becomes measurable only when goals, conversations and actions move in the same flow.', 'myliba'))); ?></p>
                </div>
                <div class="homepage-card-grid homepage-card-grid--three">
                    <?php foreach ($problem_cards as $card) :
                        [$title, $text] = array_pad($card, 2, '');
                        ?>
                        <article class="homepage-card homepage-card--problem">
                            <span class="homepage-card__icon"><?php echo esc_html(function_exists('mb_substr') ? mb_substr($title, 0, 1) : substr($title, 0, 1)); ?></span>
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
            $product_fallbacks = [
                ['OKR Management', 'Make company, team and individual goals visible in one hierarchy.'],
                ['KPI Tracking', 'Follow business metrics while keeping their strategic context.'],
                ['CFR', 'Connect conversations, feedback and recognition to goals.'],
                ['Action Management', 'Turn priorities into owners, due dates and progress routines.'],
                ['1:1 Meetings', 'Make one-on-ones structured, useful and connected to development.'],
                ['Performance Development', 'Use evidence from goals, feedback and actions in performance growth.'],
                ['Leadership and Coaching', 'Support leaders with practical routines for clarity and accountability.'],
                ['Academy Programs', 'Strengthen adoption with workshops, coaching and culture programs.'],
                ['Insights and Analytics', 'See performance culture signals, risks and progress in one view.'],
            ];
            $rendered_products = 0;
            ?>
            <section class="section product-suite-section">
                <div class="section__heading product-suite__heading">
                    <div>
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('solution_eyebrow', __('The Myliba solution', 'myliba'))); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('solution_title', __('One platform for goals, performance conversations, actions and culture development.', 'myliba'))); ?></h2>
                    </div>
                    <a class="product-suite__overview" href="<?php echo esc_url(myliba_page_url('products')); ?>">
                        <?php esc_html_e('See all products', 'myliba'); ?>
                    </a>
                </div>
                <div class="module-matrix">
                    <?php while ($product_query->have_posts()) : $product_query->the_post(); $rendered_products++; ?>
                        <a class="module-card module-card--compact" href="<?php the_permalink(); ?>">
                            <span class="module-card__topline">
                                <span class="module-card__icon"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                                <strong><?php echo esc_html(myliba_home_value('module_button', __('View module', 'myliba'))); ?></strong>
                            </span>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 14)); ?></p>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                    <?php if ($rendered_products === 0) : ?>
                        <?php foreach ($product_fallbacks as $fallback_card) :
                            [$title, $text] = $fallback_card;
                            ?>
                            <article class="module-card module-card--compact">
                                <span class="module-card__topline">
                                    <span class="module-card__icon"><?php echo esc_html(substr($title, 0, 1)); ?></span>
                                </span>
                                <h3><?php echo esc_html($title); ?></h3>
                                <p><?php echo esc_html($text); ?></p>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
            <?php
            break;

        case 'academy':
            ?>
            <section class="section academy-spotlight-section">
                <div class="academy-spotlight">
                    <div class="academy-spotlight__content">
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('academy_eyebrow', __('Academy + software', 'myliba'))); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('academy_title', __('Software power, academy experience.', 'myliba'))); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('academy_text', __('Myliba helps organizations not only define goals, but also make goal-oriented work sustainable through leadership development, performance coaching, workshops and cultural transformation programs.', 'myliba'))); ?></p>
                        <div class="academy-spotlight__actions">
                            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('academy')); ?>"><?php echo esc_html(myliba_home_value('academy_button', __('Explore academy', 'myliba'))); ?></a>
                            <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?></a>
                        </div>
                    </div>
                    <div class="academy-spotlight__stack">
                        <?php foreach ($academy_items as $item) : ?>
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
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('role_gains_eyebrow', __('Role-based value', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('role_gains_title', __('Clear gains for every role.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('role_gains_text', __('Give each stakeholder the view and routine they need inside the same operating system.', 'myliba'))); ?></p>
                </div>
                <div class="role-gains" data-role-gains>
                    <div class="role-gains__tabs" role="tablist" aria-label="<?php esc_attr_e('Role gains', 'myliba'); ?>">
                        <?php foreach ($role_gain_rows as $index => $row) :
                            [$label] = array_pad($row, 1, '');
                            $tab_id = 'role-gain-tab-' . $index;
                            $panel_id = 'role-gain-panel-' . $index;
                            ?>
                            <button
                                class="role-gains__tab <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                id="<?php echo esc_attr($tab_id); ?>"
                                type="button"
                                role="tab"
                                aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo esc_attr($panel_id); ?>"
                                data-role-tab
                            >
                                <?php echo esc_html($label); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="role-gains__panels">
                        <?php foreach ($role_gain_rows as $index => $row) :
                            [$label, $title, $text, $primary_label, $primary_text, $secondary_label, $secondary_text] = array_pad($row, 7, '');
                            $tab_id = 'role-gain-tab-' . $index;
                            $panel_id = 'role-gain-panel-' . $index;
                            ?>
                            <div
                                class="role-gains__panel <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                id="<?php echo esc_attr($panel_id); ?>"
                                role="tabpanel"
                                aria-labelledby="<?php echo esc_attr($tab_id); ?>"
                                <?php echo $index === 0 ? '' : 'hidden'; ?>
                                data-role-panel
                            >
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
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('strategy_flow_eyebrow', __('Performance rhythm', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('strategy_flow_title', __('Strategy to goals, action and culture.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('strategy_flow_text', __('Build one connected operating rhythm for priorities, ownership, action and learning.', 'myliba'))); ?></p>
                </div>
                <div class="strategy-flow">
                    <?php foreach ($strategy_flow_steps as $index => $step) :
                        [$title, $text, $short_label] = array_pad($step, 3, '');
                        ?>
                        <article class="strategy-flow__step">
                            <span class="strategy-flow__badge"><?php echo esc_html($short_label !== '' ? $short_label : str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
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
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('performance_eyebrow', __('Performance management approach', 'myliba'))); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('performance_title', __('Turn performance management into a strategic advantage.', 'myliba'))); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('performance_text', __('Move beyond an annual, stressful scoring cycle with a fair and evidence-based approach that supports continuous growth.', 'myliba'))); ?></p>
                    </div>
                    <div class="performance-tabs" data-home-tabs>
                        <div class="performance-tabs__nav" role="tablist" aria-label="<?php esc_attr_e('Performance management capabilities', 'myliba'); ?>">
                            <?php foreach ($performance_tabs as $index => $row) :
                                [$label] = array_pad($row, 1, '');
                                ?>
                                <button id="performance-tab-<?php echo esc_attr((string) $index); ?>" class="<?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="performance-panel-<?php echo esc_attr((string) $index); ?>" data-home-tab><?php echo esc_html($label); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <?php foreach ($performance_tabs as $index => $row) :
                            [$label, $title, $text] = array_pad($row, 3, '');
                            $image = $hero_banner_images ? $hero_banner_images[$index % count($hero_banner_images)] : [];
                            ?>
                            <div id="performance-panel-<?php echo esc_attr((string) $index); ?>" class="performance-tabs__panel <?php echo $index === 0 ? 'is-active' : ''; ?>" role="tabpanel" aria-labelledby="performance-tab-<?php echo esc_attr((string) $index); ?>" data-home-panel <?php echo $index === 0 ? '' : 'hidden'; ?>>
                                <div><span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span><h3><?php echo esc_html($title); ?></h3><p><?php echo esc_html($text); ?></p><a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('products')); ?>"><?php esc_html_e('Explore Myliba products', 'myliba'); ?></a></div>
                                <?php if (!empty($image['url'])) : ?><figure><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="lazy" decoding="async"></figure><?php endif; ?>
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
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('outcomes_eyebrow', __('Business outcomes', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('outcomes_title', __('Make performance culture visible, coachable and measurable.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('outcomes_text', __('Every rhythm leaves a measurable signal for better focus, coaching and decisions.', 'myliba'))); ?></p>
                </div>
                <div class="homepage-card-grid homepage-card-grid--three">
                    <?php foreach ($outcomes_cards as $card) :
                        [$title, $text] = array_pad($card, 2, '');
                        ?>
                        <article class="homepage-card homepage-card--outcome">
                            <span class="homepage-card__icon"><?php echo esc_html(function_exists('mb_substr') ? mb_substr($title, 0, 1) : substr($title, 0, 1)); ?></span>
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
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('resources_eyebrow', __('Resources', 'myliba'))); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('resources_title', __('SEO-ready content for OKR, performance and culture topics.', 'myliba'))); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('resources_text', __('Read practical insights for goal management, leadership routines and performance culture.', 'myliba'))); ?></p>
                    </div>
                    <a class="product-suite__overview" href="<?php echo esc_url(myliba_page_url('blog')); ?>">
                        <?php echo esc_html(myliba_home_value('resources_button', __('View all', 'myliba'))); ?>
                    </a>
                </div>
                <div class="resources-grid">
                    <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                        <a class="resource-card" href="<?php the_permalink(); ?>">
                            <span><?php echo esc_html(get_the_date()); ?> &middot; <?php echo esc_html(myliba_reading_time()); ?> min</span>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 18)); ?></p>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
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
                $faq_items = $faq_fallback_items;
            }
            ?>
            <section class="section faq-section">
                <div class="section__heading homepage-section-heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('faq_eyebrow', __('FAQ', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('faq_title', __('First questions in your mind.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('faq_text', __('The answers teams usually need before they start building a measurable performance rhythm.', 'myliba'))); ?></p>
                </div>
                <div class="faq-accordion">
                    <?php foreach ($faq_items as $index => $faq_item) : ?>
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
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('final_cta_eyebrow', __('Make it actionable', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('final_cta_title', __('Begin your high-performance journey today.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('final_cta_text', __('Bring strategy, performance development and culture into one connected operating rhythm with Myliba.', 'myliba'))); ?></p>
                    <div class="final-cta__actions">
                        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_home_value('final_cta_primary_label', __('Talk to Myliba experts', 'myliba'))); ?></a>
                        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html(myliba_home_value('final_cta_secondary_label', __('Contact us', 'myliba'))); ?></a>
                    </div>
                </div>
            </section>
            <?php
            break;

    }
}

get_footer();
