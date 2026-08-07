<?php
/**
 * Template Name: Myliba Blog Listing
 */

get_header();
get_template_part('template-parts/hero');

$paged = max(1, get_query_var('paged'), get_query_var('page'));
$search_query = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
$selected_category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$current_lang = myliba_current_language();

$query_args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => $paged,
];

if (!empty($current_lang)) {
    $query_args['meta_key'] = '_myliba_language';
    $query_args['meta_value'] = $current_lang;
}

if ($search_query !== '') {
    $query_args['s'] = $search_query;
}

if ($selected_category > 0) {
    $query_args['cat'] = $selected_category;
}

$posts = new WP_Query($query_args);
$has_active_filters = ($search_query !== '' || $selected_category > 0);
$form_action = remove_query_arg(['s', 'category', 'paged', 'page'], get_permalink());
$categories = get_categories([
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
]);
?>

<section class="section">
    <form class="resource-filters" method="get" action="<?php echo esc_url($form_action); ?>" role="search">
        <label class="resource-filters__field resource-filters__field--search">
            <span class="resource-filters__label"><?php echo esc_html(myliba_text('Search resources')); ?></span>
            <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="<?php echo esc_attr(myliba_text('OKR, KPI, feedback...')); ?>" autocomplete="off">
        </label>
        <label class="resource-filters__field resource-filters__field--category">
            <span class="resource-filters__label"><?php echo esc_html(myliba_text('Category')); ?></span>
            <select name="category" class="resource-filters__select">
                <option value="0"><?php echo esc_html(myliba_text('All categories')); ?></option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo esc_attr((string) $category->term_id); ?>" <?php selected($selected_category, $category->term_id); ?>>
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="resource-filters__actions">
            <button class="myliba-button myliba-button--primary" type="submit"><?php echo esc_html(myliba_text('Filter')); ?></button>
            <?php if ($has_active_filters) : ?>
                <a class="resource-filters__reset" href="<?php echo esc_url($form_action); ?>" title="<?php echo esc_attr(myliba_text('Clear filters')); ?>">
                    <?php echo esc_html(myliba_text('Clear')); ?>
                </a>
            <?php endif; ?>
        </div>
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

            <?php if ($posts->max_num_pages > 1) : ?>
                <div class="pagination">
                    <?php
                    echo paginate_links([
                        'total' => $posts->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '← ' . myliba_text('Previous'),
                        'next_text' => myliba_text('Next') . ' →',
                        'add_args' => array_filter([
                            's' => $search_query !== '' ? $search_query : null,
                            'category' => $selected_category > 0 ? $selected_category : null,
                        ]),
                    ]);
                    ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="post-list-empty">
                <p class="post-list-empty__title"><?php echo esc_html(myliba_text('No articles found.')); ?></p>
                <?php if ($has_active_filters) : ?>
                    <p class="post-list-empty__desc"><?php echo esc_html(myliba_text('Try adjusting your search or filter to find what you are looking for.')); ?></p>
                    <a class="myliba-button myliba-button--ghost" href="<?php echo esc_url($form_action); ?>">
                        <?php echo esc_html(myliba_text('Clear filters')); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>

