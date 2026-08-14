<?php
get_header();
get_template_part('template-parts/hero');
?>

<section class="section section--split">
    <article class="content content--article">
        <?php
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>
    </article>
    <aside class="event-detail-panel">
        <h2><?php echo esc_html(myliba_text('Event details')); ?></h2>
        <p><strong><?php echo esc_html(myliba_text('Date')); ?></strong><br><?php echo esc_html(myliba_meta('_myliba_event_date')); ?></p>
        <p><strong><?php echo esc_html(myliba_text('Location')); ?></strong><br><?php echo esc_html(myliba_meta('_myliba_event_location')); ?></p>
        <?php if (myliba_meta('_myliba_event_url')) : ?>
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_meta('_myliba_event_url')); ?>"><?php echo esc_html(myliba_meta('_myliba_event_cta_label', get_the_ID(), myliba_text('Register'))); ?></a>
        <?php endif; ?>
    </aside>
</section>

<?php get_footer(); ?>

