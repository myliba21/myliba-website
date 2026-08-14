<?php
get_header();
get_template_part('template-parts/hero');

$content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));
preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $content, $matches);
$headings = $matches[1] ?? [];
?>

<section class="section">
    <div class="content-grid">
        <article class="content content--article">
            <?php while (have_posts()) : the_post(); ?>
                <p class="article-meta">
                    <?php echo esc_html(get_the_date()); ?> &middot;
                    <?php echo esc_html(get_the_modified_date()); ?> &middot;
                    <?php echo esc_html(myliba_reading_time()); ?> <?php echo esc_html(myliba_text('min read')); ?>
                </p>
                <?php echo $content; ?>
            <?php endwhile; ?>
        </article>
        <aside>
            <?php if ($headings) : ?>
                <div class="toc-card">
                    <h2><?php echo esc_html(myliba_text('In this article')); ?></h2>
                    <ol>
                        <?php foreach ($headings as $heading) : ?>
                            <li><?php echo esc_html(wp_strip_all_tags($heading)); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>
            <div class="author-card">
                <h2><?php echo esc_html(myliba_option('blog_author_title', myliba_text('Myliba Team'))); ?></h2>
                <p><?php echo esc_html(myliba_option('blog_author_text', myliba_text('Insights on OKR, KPI, CFR, performance culture, feedback and leadership routines.'))); ?></p>
            </div>
            <div class="cta-panel">
                <h2><?php echo esc_html(myliba_option('blog_cta_title', myliba_text('Turn this into practice.'))); ?></h2>
                <p><?php echo esc_html(myliba_option('blog_cta_text', myliba_text('Request a demo and see how Myliba connects strategy, actions and performance routines.'))); ?></p>
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_demo_url()); ?>"><?php echo esc_html(myliba_option('demo_cta_label', myliba_text('Request a demo'))); ?></a>
            </div>
        </aside>
    </div>
</section>

<section class="section band">
    <div class="section__heading">
        <p class="eyebrow"><?php echo esc_html(myliba_option('blog_related_eyebrow', myliba_text('Related reading'))); ?></p>
        <h2><?php echo esc_html(myliba_option('blog_related_title', myliba_text('Continue exploring performance culture.'))); ?></h2>
    </div>
    <div class="card-grid card-grid--three">
        <?php
        $related = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => 3,
            'post__not_in' => [get_the_ID()],
            'meta_key' => '_myliba_language',
            'meta_value' => myliba_current_language(),
        ]);
        while ($related->have_posts()) :
            $related->the_post();
            ?>
            <a class="post-row" href="<?php the_permalink(); ?>">
                <span><?php echo esc_html(get_the_date()); ?> &middot; <?php echo esc_html(myliba_reading_time()); ?> <?php echo esc_html(myliba_text('min read')); ?></span>
                <strong><?php the_title(); ?></strong>
                <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 18)); ?></p>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>

<?php get_footer(); ?>
