<?php
get_header();

$demo_url = myliba_demo_url();
$products = myliba_get_entries('myliba_product', 10);
$solutions = myliba_get_entries('myliba_solution', 5);
$testimonials = myliba_get_entries('myliba_testimonial', 2);
$faqs = myliba_get_entries('myliba_faq', 5);
?>

<section class="hero">
    <div class="hero__content">
        <p class="eyebrow"><?php esc_html_e('OKR, KPI, CFR and performance culture', 'myliba'); ?></p>
        <h1><?php echo esc_html(myliba_meta('_myliba_hero_title', get_queried_object_id(), __('Turn strategy into goals, goals into action.', 'myliba'))); ?></h1>
        <p class="hero__subtitle"><?php echo esc_html(myliba_meta('_myliba_hero_subtitle', get_queried_object_id(), __('Myliba combines OKR, KPI, CFR, 1:1 meetings, feedback, action management and academy programs so organizations can build a measurable high-performance culture.', 'myliba'))); ?></p>
        <div class="hero__actions">
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?></a>
            <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(myliba_page_url('products')); ?>"><?php esc_html_e('Explore products', 'myliba'); ?></a>
        </div>
        <div class="hero__proof">
            <span><?php esc_html_e('Strategy to action', 'myliba'); ?></span>
            <span><?php esc_html_e('Continuous performance', 'myliba'); ?></span>
            <span><?php esc_html_e('Academy + software', 'myliba'); ?></span>
        </div>
    </div>
    <div class="dashboard-preview" aria-label="<?php esc_attr_e('Myliba product dashboard preview', 'myliba'); ?>">
        <div class="dashboard-preview__bar"><span></span><span></span><span></span></div>
        <div class="dashboard-preview__body">
            <div class="metric-stack">
                <div class="metric-card">
                    <h3><?php esc_html_e('Goal progress', 'myliba'); ?></h3>
                    <strong>72%</strong>
                    <small><?php esc_html_e('aligned OKR progress', 'myliba'); ?></small>
                </div>
                <div class="metric-card">
                    <h3><?php esc_html_e('1:1 rhythm', 'myliba'); ?></h3>
                    <strong>148</strong>
                    <small><?php esc_html_e('conversations this month', 'myliba'); ?></small>
                </div>
            </div>
            <div class="dashboard-card">
                <h3><?php esc_html_e('Company objective', 'myliba'); ?></h3>
                <p><?php esc_html_e('Increase strategy execution visibility across teams.', 'myliba'); ?></p>
                <div class="progress-line"><span style="width:76%"></span></div>
                <div class="module-pill-list">
                    <span>OKR</span><span>KPI</span><span>CFR</span><span>Actions</span><span>Feedback</span>
                </div>
            </div>
            <div class="feedback-card">
                <h3><?php esc_html_e('Feedback card', 'myliba'); ?></h3>
                <p><?php esc_html_e('Recognition, coaching notes and next actions stay connected to goals.', 'myliba'); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section band">
    <div class="trust-row">
        <strong><?php esc_html_e('Built for teams that manage performance culture seriously.', 'myliba'); ?></strong>
        <span>OKR</span>
        <span>KPI</span>
        <span>CFR</span>
        <span>1:1</span>
    </div>
</section>

<section class="section section--split">
    <div>
        <p class="eyebrow"><?php esc_html_e('The problem', 'myliba'); ?></p>
        <h2><?php esc_html_e('Strategy gets lost when goals, actions and feedback live in separate systems.', 'myliba'); ?></h2>
    </div>
    <div class="split-panel">
        <div class="split-panel__item">
            <h3><?php esc_html_e('Fragmented routines', 'myliba'); ?></h3>
            <p><?php esc_html_e('OKR workshops, KPI reviews, performance interviews and feedback notes become disconnected rituals.', 'myliba'); ?></p>
        </div>
        <div class="split-panel__item">
            <h3><?php esc_html_e('Weak visibility', 'myliba'); ?></h3>
            <p><?php esc_html_e('Leadership, HR and teams cannot see contribution, blockers and progress in one operating rhythm.', 'myliba'); ?></p>
        </div>
    </div>
</section>

<section class="section">
    <div class="section__heading">
        <p class="eyebrow"><?php esc_html_e('The Myliba solution', 'myliba'); ?></p>
        <h2><?php esc_html_e('One platform for goals, performance conversations, actions and culture development.', 'myliba'); ?></h2>
    </div>
    <div class="card-grid card-grid--three">
        <?php while ($products->have_posts()) : $products->the_post(); ?>
            <a class="module-card" href="<?php the_permalink(); ?>">
                <span class="module-card__icon"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                <h3><?php the_title(); ?></h3>
                <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 20)); ?></p>
                <strong><?php esc_html_e('View module', 'myliba'); ?></strong>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>

<section class="section band">
    <div class="section--split">
        <div>
            <p class="eyebrow"><?php esc_html_e('Academy + software', 'myliba'); ?></p>
            <h2><?php esc_html_e('Software power, academy experience.', 'myliba'); ?></h2>
            <p><?php esc_html_e('Myliba helps organizations not only define goals, but also make goal-oriented work sustainable through leadership development, performance coaching, workshops and cultural transformation programs.', 'myliba'); ?></p>
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('academy')); ?>"><?php esc_html_e('Explore academy', 'myliba'); ?></a>
        </div>
        <ul class="check-list">
            <li><?php esc_html_e('OKR culture and adoption programs', 'myliba'); ?></li>
            <li><?php esc_html_e('Leadership and coaching routines', 'myliba'); ?></li>
            <li><?php esc_html_e('Continuous performance development', 'myliba'); ?></li>
            <li><?php esc_html_e('Human and culture-focused transformation', 'myliba'); ?></li>
        </ul>
    </div>
</section>

<section class="section">
    <div class="section__heading">
        <p class="eyebrow"><?php esc_html_e('Use cases', 'myliba'); ?></p>
        <h2><?php esc_html_e('Purpose-built paths for executives, HR, strategy teams and leaders.', 'myliba'); ?></h2>
    </div>
    <div class="card-grid card-grid--three">
        <?php while ($solutions->have_posts()) : $solutions->the_post(); ?>
            <a class="use-case-card" href="<?php the_permalink(); ?>">
                <h3><?php the_title(); ?></h3>
                <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 24)); ?></p>
                <strong><?php esc_html_e('See solution', 'myliba'); ?></strong>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>

<section class="section band">
    <div class="section__heading">
        <p class="eyebrow"><?php esc_html_e('Business outcomes', 'myliba'); ?></p>
        <h2><?php esc_html_e('Make performance culture visible, coachable and measurable.', 'myliba'); ?></h2>
    </div>
    <div class="card-grid card-grid--four">
        <div class="feature-card"><h3><?php esc_html_e('Alignment', 'myliba'); ?></h3><p><?php esc_html_e('Connect company strategy with team and individual contribution.', 'myliba'); ?></p></div>
        <div class="feature-card"><h3><?php esc_html_e('Transparency', 'myliba'); ?></h3><p><?php esc_html_e('See progress, blockers and ownership without waiting for meetings.', 'myliba'); ?></p></div>
        <div class="feature-card"><h3><?php esc_html_e('Development', 'myliba'); ?></h3><p><?php esc_html_e('Turn 1:1, feedback and coaching into a continuous routine.', 'myliba'); ?></p></div>
        <div class="feature-card"><h3><?php esc_html_e('Execution', 'myliba'); ?></h3><p><?php esc_html_e('Transform priorities into actions, ownership and measurable results.', 'myliba'); ?></p></div>
    </div>
</section>

<section class="section section--split">
    <div>
        <p class="eyebrow"><?php esc_html_e('Trust', 'myliba'); ?></p>
        <h2><?php esc_html_e('Designed for B2B confidence.', 'myliba'); ?></h2>
        <p><?php esc_html_e('Role-based access, privacy-first forms, security messaging and legal pages are part of the WordPress migration foundation.', 'myliba'); ?></p>
        <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url(myliba_page_url('security')); ?>"><?php esc_html_e('View security', 'myliba'); ?></a>
    </div>
    <div class="post-list">
        <?php if ($testimonials->have_posts()) : ?>
            <?php while ($testimonials->have_posts()) : $testimonials->the_post(); ?>
                <article class="testimonial-card">
                    <p><?php echo esc_html(wp_strip_all_tags(get_the_content())); ?></p>
                    <strong><?php the_title(); ?></strong>
                    <span><?php echo esc_html(myliba_meta('_myliba_company', get_the_ID())); ?></span>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="section--split">
        <div>
            <p class="eyebrow"><?php esc_html_e('Resources', 'myliba'); ?></p>
            <h2><?php esc_html_e('SEO-ready content for OKR, performance and culture topics.', 'myliba'); ?></h2>
        </div>
        <div class="post-list">
            <?php
            $posts = new WP_Query([
                'post_type' => 'post',
                'posts_per_page' => 3,
                'meta_key' => '_myliba_language',
                'meta_value' => myliba_current_language(),
            ]);
            while ($posts->have_posts()) :
                $posts->the_post();
                ?>
                <a class="post-row" href="<?php the_permalink(); ?>">
                    <span><?php echo esc_html(get_the_date()); ?> &middot; <?php echo esc_html(myliba_reading_time()); ?> min</span>
                    <strong><?php the_title(); ?></strong>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 18)); ?></p>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<section class="section band">
    <div class="section__heading">
        <p class="eyebrow"><?php esc_html_e('FAQ', 'myliba'); ?></p>
        <h2><?php esc_html_e('Common questions before requesting a demo.', 'myliba'); ?></h2>
    </div>
    <div class="card-grid card-grid--two">
        <?php while ($faqs->have_posts()) : $faqs->the_post(); ?>
            <article class="faq-card">
                <h3><?php the_title(); ?></h3>
                <p><?php echo esc_html(wp_strip_all_tags(get_the_content())); ?></p>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>

<section class="section">
    <div class="cta-panel">
        <p class="eyebrow"><?php esc_html_e('Next step', 'myliba'); ?></p>
        <h2><?php esc_html_e('See how Myliba can connect your strategy, performance and culture routines.', 'myliba'); ?></h2>
        <p><?php esc_html_e('Share your team size and priorities. We will show the modules and academy path that fit your organization.', 'myliba'); ?></p>
        <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($demo_url); ?>"><?php echo esc_html(myliba_option('demo_cta_label', __('Request a demo', 'myliba'))); ?></a>
    </div>
</section>

<?php get_footer(); ?>
