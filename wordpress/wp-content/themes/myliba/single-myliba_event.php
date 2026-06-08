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
        <h2><?php esc_html_e('Event details', 'myliba'); ?></h2>
        <p><strong><?php esc_html_e('Date', 'myliba'); ?></strong><br><?php echo esc_html(myliba_meta('_myliba_event_date')); ?></p>
        <p><strong><?php esc_html_e('Location', 'myliba'); ?></strong><br><?php echo esc_html(myliba_meta('_myliba_event_location')); ?></p>
        <?php if (myliba_meta('_myliba_event_url')) : ?>
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_meta('_myliba_event_url')); ?>"><?php esc_html_e('Register', 'myliba'); ?></a>
        <?php endif; ?>
    </aside>
</section>

<?php get_footer(); ?>

