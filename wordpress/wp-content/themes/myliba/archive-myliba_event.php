<?php
get_header();
?>

<section class="archive-hero">
    <p class="eyebrow"><?php esc_html_e('Events', 'myliba'); ?></p>
    <h1><?php esc_html_e('Workshops, webinars, and sessions', 'myliba'); ?></h1>
</section>

<section class="section">
    <div class="card-grid card-grid--two">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <a class="event-card" href="<?php the_permalink(); ?>">
                    <span><?php echo esc_html(myliba_meta('_myliba_event_date', get_the_ID(), get_the_date())); ?></span>
                    <h2><?php the_title(); ?></h2>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 24)); ?></p>
                    <strong><?php echo esc_html(myliba_meta('_myliba_event_location', get_the_ID(), __('Online', 'myliba'))); ?></strong>
                </a>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php esc_html_e('No upcoming events at this time.', 'myliba'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>

