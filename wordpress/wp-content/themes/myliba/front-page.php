<?php
get_header();

$post_id = get_queried_object_id();
$demo_url = myliba_demo_url();
$products = static fn () => myliba_get_entries('myliba_product', 9);
$client_logos = static fn () => myliba_get_entries('myliba_client_logo', 24, ['meta_query' => []]);
$hero_title = (string) myliba_meta('_myliba_hero_title', $post_id, __('Turn strategy into goals, goals into action.', 'myliba'));
$hero_titles = myliba_home_lines('hero_rotating_titles', [$hero_title]);
$hero_banner_images = myliba_hero_banner_images();
$hero_proof = myliba_home_lines('hero_proof', ['Strategy to action', 'Continuous performance', 'Academy + software']);
$dashboard_nav = myliba_home_lines('dashboard_nav', ['OKR', 'KPI', 'CFR', '1:1', 'Academy']);
$dashboard_progress = max(0, min(100, (int) myliba_home_value('dashboard_progress', '76')));
$dashboard_rows = myliba_home_rows('dashboard_rows', [
    ['Leadership rhythm active', 'HR', 'On track', 'green'],
    ['Team OKR alignment', 'Strategy', 'Review', 'blue'],
    ['1:1 action follow-up', 'Leads', 'Focus', 'orange'],
]);
$trust_items = myliba_home_lines('trust_items', ['OKR', 'KPI', 'CFR', '1:1']);
$with_minimum_rows = static function (array $rows, array $fallback): array {
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
            <section class="hero">
                <div class="hero__content">
                    <p class="eyebrow"><?php echo esc_html(myliba_meta('_myliba_eyebrow', $post_id, __('OKR, KPI, CFR and performance culture', 'myliba'))); ?></p>
                    <h1 class="hero-title-rotator" data-rotating-title>
                        <?php foreach ($hero_titles as $index => $title) : ?>
                            <span class="hero-title-rotator__item <?php echo $index === 0 ? 'is-active' : ''; ?>"><?php echo esc_html($title); ?></span>
                        <?php endforeach; ?>
                    </h1>
                    <p class="hero__subtitle"><?php echo esc_html(myliba_meta('_myliba_hero_subtitle', $post_id, __('Myliba combines OKR, KPI, CFR, 1:1 meetings, feedback, action management and academy programs so organizations can build a measurable high-performance culture.', 'myliba'))); ?></p>
                    <div class="hero__actions">
                        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?></a>
                        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(myliba_page_url('products')); ?>"><?php echo esc_html(myliba_home_value('products_button', __('Explore products', 'myliba'))); ?></a>
                    </div>
                    <div class="hero__proof">
                        <?php foreach ($hero_proof as $item) : ?>
                            <span><?php echo esc_html($item); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if ($hero_banner_images) : ?>
                    <div class="hero-media-rotator" data-hero-media-rotator aria-label="<?php esc_attr_e('Myliba product screenshots', 'myliba'); ?>">
                        <div class="hero-media-rotator__frame">
                            <?php foreach ($hero_banner_images as $index => $image) : ?>
                                <figure class="hero-media-rotator__slide <?php echo $index === 0 ? 'is-active' : ''; ?>" data-hero-media-slide>
                                    <img
                                        src="<?php echo esc_url($image['url']); ?>"
                                        alt="<?php echo esc_attr($image['alt']); ?>"
                                        loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                                        decoding="async"
                                    >
                                </figure>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($hero_banner_images) > 1) : ?>
                            <div class="hero-media-rotator__dots" aria-hidden="true">
                                <?php foreach ($hero_banner_images as $index => $image) : ?>
                                    <span class="<?php echo $index === 0 ? 'is-active' : ''; ?>" data-hero-media-dot></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                <div class="dashboard-preview dashboard-preview--premium" aria-label="<?php esc_attr_e('Myliba product dashboard preview', 'myliba'); ?>">
                    <div class="dashboard-preview__bar">
                        <span></span><span></span><span></span>
                        <strong><?php echo esc_html(myliba_home_value('dashboard_title', __('Performance OS', 'myliba'))); ?></strong>
                    </div>
                    <div class="dashboard-preview__body">
                        <aside class="dashboard-sidebar">
                            <strong><?php echo esc_html(myliba_home_value('dashboard_brand', myliba_option('organization_name', __('Myliba', 'myliba')))); ?></strong>
                            <?php foreach ($dashboard_nav as $index => $item) : ?>
                                <span class="<?php echo $index === 0 ? 'is-active' : ''; ?>"><?php echo esc_html($item); ?></span>
                            <?php endforeach; ?>
                        </aside>
                        <div class="dashboard-workspace">
                            <div class="dashboard-topline">
                                <div>
                                    <small><?php echo esc_html(myliba_home_value('dashboard_objective_label', __('Company objective', 'myliba'))); ?></small>
                                    <h3><?php echo esc_html(myliba_home_value('dashboard_objective_title', __('Increase strategy execution visibility', 'myliba'))); ?></h3>
                                </div>
                                <span class="status-pill"><?php echo esc_html((string) $dashboard_progress); ?>%</span>
                            </div>
                            <div class="progress-line"><span style="width:<?php echo esc_attr((string) $dashboard_progress); ?>%"></span></div>
                            <div class="objective-table">
                                <div>
                                    <strong><?php echo esc_html(myliba_home_value('dashboard_col_1', __('Key Result', 'myliba'))); ?></strong>
                                    <strong><?php echo esc_html(myliba_home_value('dashboard_col_2', __('Owner', 'myliba'))); ?></strong>
                                    <strong><?php echo esc_html(myliba_home_value('dashboard_col_3', __('Status', 'myliba'))); ?></strong>
                                </div>
                                <?php foreach ($dashboard_rows as $row) :
                                    [$key_result, $owner, $status, $color] = array_pad($row, 4, '');
                                    $color = in_array($color, ['green', 'blue', 'orange'], true) ? $color : 'blue';
                                    ?>
                                    <div><span><?php echo esc_html($key_result); ?></span><span><?php echo esc_html($owner); ?></span><em class="tag tag--<?php echo esc_attr($color); ?>"><?php echo esc_html($status); ?></em></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="dashboard-workspace__footer">
                                <span><?php esc_html_e('Goal health', 'myliba'); ?></span>
                                <strong><?php esc_html_e('Ready for review', 'myliba'); ?></strong>
                            </div>
                        </div>
                        <div class="dashboard-insights">
                            <div class="metric-card"><strong><?php echo esc_html(myliba_home_value('metric_1_value', '72%')); ?></strong><small><?php echo esc_html(myliba_home_value('metric_1_label', __('OKR progress', 'myliba'))); ?></small></div>
                            <div class="metric-card"><strong><?php echo esc_html(myliba_home_value('metric_2_value', '148')); ?></strong><small><?php echo esc_html(myliba_home_value('metric_2_label', __('1:1 notes', 'myliba'))); ?></small></div>
                        </div>
                        <div class="feedback-card">
                            <h3><?php echo esc_html(myliba_home_value('feedback_title', __('Feedback card', 'myliba'))); ?></h3>
                            <p><?php echo esc_html(myliba_home_value('feedback_text', __('Coaching notes, recognition and actions stay connected to goals.', 'myliba'))); ?></p>
                        </div>
                    </div>
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
                            <article
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
                            </article>
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
                        'question' => get_the_title(),
                        'answer' => wp_strip_all_tags(get_the_content()),
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
                    <h2><?php echo esc_html(myliba_home_value('final_cta_title', __('Turn your strategy into action today.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('final_cta_text', __('Start with OKR, performance conversations and academy-supported adoption in one connected flow.', 'myliba'))); ?></p>
                    <div class="final-cta__actions">
                        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_home_value('final_cta_primary_label', myliba_option('demo_cta_label', __('Request a demo', 'myliba')))); ?></a>
                        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html(myliba_home_value('final_cta_secondary_label', __('Contact us', 'myliba'))); ?></a>
                    </div>
                </div>
            </section>
            <?php
            break;

    }
}

get_footer();
