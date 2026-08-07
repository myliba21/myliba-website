<?php
get_header();
?>

<section class="archive-hero">
    <p class="eyebrow"><?php echo esc_html(myliba_text('Events')); ?></p>
    <h1><?php echo esc_html(myliba_text('Workshops, webinars, and sessions')); ?></h1>
</section>

<section class="section">
    <div class="card-grid card-grid--two">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <a class="event-card" href="<?php the_permalink(); ?>">
                    <span><?php echo esc_html(myliba_meta('_myliba_event_date', get_the_ID(), get_the_date())); ?></span>
                    <h2><?php the_title(); ?></h2>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 24)); ?></p>
                    <strong><?php echo esc_html(myliba_meta('_myliba_event_location', get_the_ID(), myliba_text('Online'))); ?></strong>
                </a>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php echo esc_html(myliba_text('No upcoming events at this time.')); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>

