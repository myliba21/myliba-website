<?php
get_header();
?>

<section class="archive-hero">
    <p class="eyebrow"><?php esc_html_e('Myliba', 'myliba'); ?></p>
    <h1><?php echo esc_html(get_the_archive_title() ?: get_bloginfo('name')); ?></h1>
</section>

<section class="section">
    <div class="post-list">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <a class="post-row" href="<?php the_permalink(); ?>">
                    <span><?php echo esc_html(get_the_date()); ?></span>
                    <strong><?php the_title(); ?></strong>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 24)); ?></p>
                </a>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e('No content found.', 'myliba'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>

