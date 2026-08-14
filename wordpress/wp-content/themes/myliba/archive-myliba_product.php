<?php
get_header();

$eyebrow = (string) myliba_option('archive_product_eyebrow', myliba_text('Products'));
$title = (string) myliba_option('archive_product_title', myliba_text('Product modules for performance culture.'));
$subtitle = (string) myliba_option('archive_product_subtitle', '');
?>
<section class="archive-hero">
    <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
    <h1><?php echo esc_html($title); ?></h1>
    <?php if ($subtitle !== '') : ?>
        <p class="hero__subtitle"><?php echo esc_html($subtitle); ?></p>
    <?php endif; ?>
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

