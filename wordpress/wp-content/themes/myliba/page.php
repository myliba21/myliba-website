<?php
if (myliba_is_locale_landing_page()) {
    require get_template_directory() . '/front-page.php';
    return;
}

if (myliba_is_academy_landing_page()) {
    require get_template_directory() . '/template-parts/page-academy.php';
    return;
}

get_header();
get_template_part('template-parts/hero');
?>

<?php while (have_posts()) : the_post(); ?>
    <?php
    $raw_content = (string) get_post_field('post_content', get_the_ID());
    $content_classes = ['content'];

    if (str_contains($raw_content, 'id="post-detail"')) {
        $content_classes[] = 'content--legacy-import';
    }
    ?>
    <section class="section">
        <article class="<?php echo esc_attr(implode(' ', $content_classes)); ?>">
            <?php the_content(); ?>
        </article>
    </section>
<?php endwhile; ?>

<?php get_footer(); ?>
