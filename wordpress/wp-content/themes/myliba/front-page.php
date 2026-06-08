<?php
get_header();

$post_id = get_queried_object_id();
$demo_url = myliba_demo_url();
$products = static fn () => myliba_get_entries('myliba_product', 10);
$solutions = static fn () => myliba_get_entries('myliba_solution', 5);
$testimonials = static fn () => myliba_get_entries('myliba_testimonial', 2);
$faqs = static fn () => myliba_get_entries('myliba_faq', 5);
$hero_proof = myliba_home_lines('hero_proof', ['Strategy to action', 'Continuous performance', 'Academy + software']);
$dashboard_nav = myliba_home_lines('dashboard_nav', ['OKR', 'KPI', 'CFR', '1:1', 'Academy']);
$dashboard_rows = myliba_home_rows('dashboard_rows', [
    ['Leadership rhythm active', 'HR', 'On track', 'green'],
    ['Team OKR alignment', 'Strategy', 'Review', 'blue'],
    ['1:1 action follow-up', 'Leads', 'Focus', 'orange'],
]);
$trust_items = myliba_home_lines('trust_items', ['OKR', 'KPI', 'CFR', '1:1']);
$problem_cards = myliba_home_rows('problem_cards', [
    ['Fragmented routines', 'OKR workshops, KPI reviews, performance interviews and feedback notes become disconnected rituals.'],
    ['Weak visibility', 'Leadership, HR and teams cannot see contribution, blockers and progress in one operating rhythm.'],
]);
$academy_items = myliba_home_lines('academy_items', ['OKR culture and adoption programs', 'Leadership and coaching routines', 'Continuous performance development', 'Human and culture-focused transformation']);
$outcomes_cards = myliba_home_rows('outcomes_cards', [
    ['Alignment', 'Connect company strategy with team and individual contribution.'],
    ['Transparency', 'See progress, blockers and ownership without waiting for meetings.'],
    ['Development', 'Turn 1:1, feedback and coaching into a continuous routine.'],
    ['Execution', 'Transform priorities into actions, ownership and measurable results.'],
]);

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
                    <h1><?php echo esc_html(myliba_meta('_myliba_hero_title', $post_id, __('Turn strategy into goals, goals into action.', 'myliba'))); ?></h1>
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
                <div class="dashboard-preview" aria-label="<?php esc_attr_e('Myliba product dashboard preview', 'myliba'); ?>">
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
                                <span class="status-pill"><?php echo esc_html(myliba_home_value('dashboard_progress', '76')); ?>%</span>
                            </div>
                            <div class="progress-line"><span style="width:<?php echo esc_attr(myliba_home_value('dashboard_progress', '76')); ?>%"></span></div>
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
            </section>
            <?php
            break;

        case 'trust_bar':
            ?>
            <section class="section band">
                <div class="trust-row">
                    <strong><?php echo esc_html(myliba_home_value('trust_title', __('Built for teams that manage performance culture seriously.', 'myliba'))); ?></strong>
                    <?php foreach ($trust_items as $item) : ?><span><?php echo esc_html($item); ?></span><?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'problem':
            ?>
            <section class="section section--split">
                <div>
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('problem_eyebrow', __('The problem', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('problem_title', __('Strategy gets lost when goals, actions and feedback live in separate systems.', 'myliba'))); ?></h2>
                </div>
                <div class="split-panel">
                    <?php foreach ($problem_cards as $card) :
                        [$title, $text] = array_pad($card, 2, '');
                        ?>
                        <div class="split-panel__item"><h3><?php echo esc_html($title); ?></h3><p><?php echo esc_html($text); ?></p></div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'products':
            $product_query = $products();
            ?>
            <section class="section">
                <div class="section__heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('solution_eyebrow', __('The Myliba solution', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('solution_title', __('One platform for goals, performance conversations, actions and culture development.', 'myliba'))); ?></h2>
                </div>
                <div class="card-grid card-grid--three">
                    <?php while ($product_query->have_posts()) : $product_query->the_post(); ?>
                        <a class="module-card" href="<?php the_permalink(); ?>"><span class="module-card__icon"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span><h3><?php the_title(); ?></h3><p><?php echo esc_html(myliba_excerpt(get_the_ID(), 20)); ?></p><strong><?php echo esc_html(myliba_home_value('module_button', __('View module', 'myliba'))); ?></strong></a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
            <?php
            break;

        case 'academy':
            ?>
            <section class="section band">
                <div class="section--split">
                    <div>
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('academy_eyebrow', __('Academy + software', 'myliba'))); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('academy_title', __('Software power, academy experience.', 'myliba'))); ?></h2>
                        <p><?php echo esc_html(myliba_home_value('academy_text', __('Myliba helps organizations not only define goals, but also make goal-oriented work sustainable through leadership development, performance coaching, workshops and cultural transformation programs.', 'myliba'))); ?></p>
                        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('academy')); ?>"><?php echo esc_html(myliba_home_value('academy_button', __('Explore academy', 'myliba'))); ?></a>
                    </div>
                    <ul class="check-list"><?php foreach ($academy_items as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?></ul>
                </div>
            </section>
            <?php
            break;

        case 'solutions':
            $solution_query = $solutions();
            ?>
            <section class="section">
                <div class="section__heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('use_cases_eyebrow', __('Use cases', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('use_cases_title', __('Purpose-built paths for executives, HR, strategy teams and leaders.', 'myliba'))); ?></h2>
                </div>
                <div class="card-grid card-grid--three">
                    <?php while ($solution_query->have_posts()) : $solution_query->the_post(); ?>
                        <a class="use-case-card" href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3><p><?php echo esc_html(myliba_excerpt(get_the_ID(), 24)); ?></p><strong><?php echo esc_html(myliba_home_value('solution_button', __('See solution', 'myliba'))); ?></strong></a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
            <?php
            break;

        case 'outcomes':
            ?>
            <section class="section band">
                <div class="section__heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('outcomes_eyebrow', __('Business outcomes', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('outcomes_title', __('Make performance culture visible, coachable and measurable.', 'myliba'))); ?></h2>
                </div>
                <div class="card-grid card-grid--four">
                    <?php foreach ($outcomes_cards as $card) :
                        [$title, $text] = array_pad($card, 2, '');
                        ?>
                        <div class="feature-card"><h3><?php echo esc_html($title); ?></h3><p><?php echo esc_html($text); ?></p></div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
            break;

        case 'testimonials':
            $testimonial_query = $testimonials();
            ?>
            <section class="section section--split">
                <div>
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('b2b_trust_eyebrow', __('Trust', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('b2b_trust_title', __('Designed for B2B confidence.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('b2b_trust_text', __('Role-based access, privacy-first forms, security messaging and legal pages are part of the WordPress migration foundation.', 'myliba'))); ?></p>
                    <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(myliba_page_url('security')); ?>"><?php echo esc_html(myliba_home_value('b2b_trust_button', __('View security', 'myliba'))); ?></a>
                </div>
                <div class="post-list">
                    <?php if ($testimonial_query->have_posts()) : while ($testimonial_query->have_posts()) : $testimonial_query->the_post(); ?>
                        <article class="testimonial-card"><p><?php echo esc_html(wp_strip_all_tags(get_the_content())); ?></p><strong><?php the_title(); ?></strong><span><?php echo esc_html(myliba_meta('_myliba_company', get_the_ID())); ?></span></article>
                    <?php endwhile; wp_reset_postdata(); endif; ?>
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
            <section class="section">
                <div class="section--split">
                    <div>
                        <p class="eyebrow"><?php echo esc_html(myliba_home_value('resources_eyebrow', __('Resources', 'myliba'))); ?></p>
                        <h2><?php echo esc_html(myliba_home_value('resources_title', __('SEO-ready content for OKR, performance and culture topics.', 'myliba'))); ?></h2>
                    </div>
                    <div class="post-list">
                        <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                            <a class="post-row" href="<?php the_permalink(); ?>"><span><?php echo esc_html(get_the_date()); ?> &middot; <?php echo esc_html(myliba_reading_time()); ?> min</span><strong><?php the_title(); ?></strong><p><?php echo esc_html(myliba_excerpt(get_the_ID(), 18)); ?></p></a>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'faq':
            $faq_query = $faqs();
            ?>
            <section class="section band">
                <div class="section__heading">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('faq_eyebrow', __('FAQ', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('faq_title', __('Common questions before requesting a demo.', 'myliba'))); ?></h2>
                </div>
                <div class="card-grid card-grid--two">
                    <?php while ($faq_query->have_posts()) : $faq_query->the_post(); ?>
                        <article class="faq-card"><h3><?php the_title(); ?></h3><p><?php echo esc_html(wp_strip_all_tags(get_the_content())); ?></p></article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
            <?php
            break;

        case 'final_cta':
            ?>
            <section class="section">
                <div class="cta-panel">
                    <p class="eyebrow"><?php echo esc_html(myliba_home_value('final_eyebrow', __('Next step', 'myliba'))); ?></p>
                    <h2><?php echo esc_html(myliba_home_value('final_title', __('See how Myliba can connect your strategy, performance and culture routines.', 'myliba'))); ?></h2>
                    <p><?php echo esc_html(myliba_home_value('final_text', __('Share your team size and priorities. We will show the modules and academy path that fit your organization.', 'myliba'))); ?></p>
                    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?></a>
                </div>
            </section>
            <?php
            break;
    }
}

get_footer();
