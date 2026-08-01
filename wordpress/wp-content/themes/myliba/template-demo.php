<?php
/**
 * Template Name: Myliba Demo
 */

get_header();
get_template_part('template-parts/hero');
?>

<section class="section section--contact">
    <article class="content">
        <?php
        while (have_posts()) {
            the_post();
            the_content();
        }
        ?>
    </article>
    <aside class="contact-panel">
        <?php echo do_shortcode('[myliba_demo_form]'); ?>
    </aside>
</section>

<?php get_footer(); ?>
