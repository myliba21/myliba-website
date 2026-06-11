<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();
$eyebrow = myliba_meta('_myliba_eyebrow', $post_id);
$title = myliba_meta('_myliba_hero_title', $post_id, get_the_title($post_id));
$subtitle = myliba_meta('_myliba_hero_subtitle', $post_id);
$cta_label = myliba_meta('_myliba_cta_label', $post_id);
$cta_url = myliba_meta('_myliba_cta_url', $post_id);
?>
<section class="hero hero--subpage">
    <div class="hero__content">
        <?php if ($eyebrow) : ?>
            <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
        <?php endif; ?>
        <h1><?php echo esc_html($title); ?></h1>
        <?php if ($subtitle) : ?>
            <p class="hero__subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
        <?php if ($cta_label && $cta_url) : ?>
            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
        <?php endif; ?>
    </div>
    <?php if (has_post_thumbnail($post_id)) : ?>
        <div class="hero__media">
            <?php echo get_the_post_thumbnail($post_id, 'large'); ?>
        </div>
    <?php endif; ?>
</section>
