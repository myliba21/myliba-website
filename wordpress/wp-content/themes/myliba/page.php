<?php
if (myliba_is_locale_landing_page()) {
    require get_template_directory() . '/front-page.php';
    return;
}

get_header();
get_template_part('template-parts/hero');
?>

<section class="section">
    <article class="content">
        <?php
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>
    </article>
</section>

<?php get_footer(); ?>
