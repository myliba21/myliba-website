<?php
/**
 * Template Name: Myliba Event Listing
 */

get_header();
get_template_part('template-parts/hero');

$events = new WP_Query([
    'post_type' => 'myliba_event',
    'posts_per_page' => 12,
    'meta_query' => [
        [
            'key' => '_myliba_language',
            'value' => myliba_current_language(),
            'compare' => '=',
        ],
    ],
    'meta_key' => '_myliba_event_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
]);
?>

<section class="section">
    <div class="card-grid card-grid--two">
        <?php if ($events->have_posts()) : ?>
            <?php while ($events->have_posts()) : $events->the_post(); ?>
                <a class="event-card" href="<?php the_permalink(); ?>">
                    <span><?php echo esc_html(myliba_meta('_myliba_event_date', get_the_ID(), get_the_date())); ?></span>
                    <h2><?php the_title(); ?></h2>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 24)); ?></p>
                    <strong><?php echo esc_html(myliba_meta('_myliba_event_location', get_the_ID(), __('Online', 'myliba'))); ?></strong>
                </a>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p><?php esc_html_e('No upcoming events at this time.', 'myliba'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>

