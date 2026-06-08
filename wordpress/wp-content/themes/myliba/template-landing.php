<?php
/**
 * Template Name: Myliba SEO Landing
 */

get_header();
get_template_part('template-parts/hero');
while (have_posts()) :
    the_post();
    get_template_part('template-parts/conversion-detail');
endwhile;
get_footer();

