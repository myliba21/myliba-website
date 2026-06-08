<?php
/**
 * Template Name: Myliba Blog Listing
 */

get_header();
get_template_part('template-parts/hero');

$posts = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 12,
    'paged' => max(1, get_query_var('paged')),
    's' => get_search_query(),
    'cat' => isset($_GET['category']) ? intval($_GET['category']) : 0,
    'meta_key' => '_myliba_language',
    'meta_value' => myliba_current_language(),
]);
?>

<section class="section">
    <form class="resource-filters" method="get" action="<?php echo esc_url(get_permalink()); ?>">
        <label>
            <span><?php esc_html_e('Search resources', 'myliba'); ?></span>
            <input type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('OKR, KPI, feedback...', 'myliba'); ?>">
        </label>
        <label>
            <span><?php esc_html_e('Category', 'myliba'); ?></span>
            <select name="category">
                <option value="0"><?php esc_html_e('All categories', 'myliba'); ?></option>
                <?php foreach (get_categories(['hide_empty' => false]) as $category) : ?>
                    <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected(isset($_GET['category']) ? intval($_GET['category']) : 0, $category->term_id); ?>>
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="myliba-button myliba-button--primary" type="submit"><?php esc_html_e('Filter', 'myliba'); ?></button>
    </form>
    <div class="post-list">
        <?php if ($posts->have_posts()) : ?>
            <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                <a class="post-row" href="<?php the_permalink(); ?>">
                    <span><?php echo esc_html(get_the_date()); ?></span>
                    <strong><?php the_title(); ?></strong>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 26)); ?></p>
                </a>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p><?php esc_html_e('No articles found.', 'myliba'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
