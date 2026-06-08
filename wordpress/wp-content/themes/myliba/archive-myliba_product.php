<?php
get_header();
?>
<section class="archive-hero">
    <p class="eyebrow"><?php esc_html_e('Products', 'myliba'); ?></p>
    <h1><?php esc_html_e('Product modules for performance culture.', 'myliba'); ?></h1>
</section>
<section class="section">
    <div class="card-grid card-grid--three">
        <?php while (have_posts()) : the_post(); ?>
            <a class="module-card" href="<?php the_permalink(); ?>">
                <span class="module-card__icon"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                <h2><?php the_title(); ?></h2>
                <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 22)); ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</section>
<?php get_footer(); ?>

