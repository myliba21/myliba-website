<?php
get_header();
?>
<section class="archive-hero">
    <p class="eyebrow"><?php esc_html_e('Academy', 'myliba'); ?></p>
    <h1><?php esc_html_e('Programs that make OKR and performance routines sustainable.', 'myliba'); ?></h1>
</section>
<section class="section">
    <div class="card-grid card-grid--three">
        <?php while (have_posts()) : the_post(); ?>
            <a class="feature-card" href="<?php the_permalink(); ?>">
                <h2><?php the_title(); ?></h2>
                <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 24)); ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</section>
<?php get_footer(); ?>

