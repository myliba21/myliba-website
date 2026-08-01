<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$context = myliba_development_center_context();
$items = myliba_development_center_items();
$page_id = (int) $context['page_id'];
?>
<section class="development-hero">
    <div class="development-shell">
        <p class="eyebrow"><?php echo esc_html($context['eyebrow']); ?></p>
        <h1><?php echo esc_html($context['title']); ?></h1>
        <?php if ($context['subtitle'] !== '') : ?>
            <p class="development-hero__lead"><?php echo esc_html($context['subtitle']); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php if ($page_id && trim((string) get_post_field('post_content', $page_id)) !== '') : ?>
    <section class="development-intro development-shell">
        <?php echo apply_filters('the_content', get_post_field('post_content', $page_id)); ?>
    </section>
<?php endif; ?>

<section class="development-index development-shell">
    <header class="development-index__heading">
        <p class="eyebrow"><?php echo esc_html($context['section_eyebrow']); ?></p>
        <h2><?php echo esc_html($context['section_title']); ?></h2>
        <?php if ($context['section_text'] !== '') : ?>
            <p><?php echo esc_html($context['section_text']); ?></p>
        <?php endif; ?>
    </header>

    <div class="development-index__grid">
        <?php foreach ($items as $item_key => $item) :
            $latest_args = [
                'post_type' => $item['post_type'],
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
                'ignore_sticky_posts' => true,
            ];
            if (!function_exists('pll_current_language')) {
                $latest_args['meta_query'] = [[
                    'key' => '_myliba_language',
                    'value' => myliba_current_language(),
                    'compare' => '=',
                ]];
            }
            $latest = new WP_Query($latest_args);
            $latest_title = '';
            $latest_excerpt = '';
            if ($latest->have_posts()) {
                $latest->the_post();
                $latest_title = get_the_title();
                $latest_excerpt = myliba_excerpt(get_the_ID(), 18);
                wp_reset_postdata();
            }
            $card_description = trim((string) $item['description']);
            if ($card_description === '') {
                $card_description = $latest_excerpt;
            }
            ?>
            <a class="development-card development-card--<?php echo esc_attr($item_key); ?>" href="<?php echo esc_url($item['url']); ?>">
                <span class="development-card__eyebrow"><?php echo esc_html($item['label']); ?></span>
                <h2><?php echo esc_html($item['label']); ?></h2>
                <?php if ($card_description !== '') : ?>
                    <p><?php echo esc_html($card_description); ?></p>
                <?php endif; ?>
                <?php if ($latest_title !== '') : ?>
                    <span class="development-card__latest"><?php echo esc_html(myliba_text('Son içerik:')); ?> <?php echo esc_html($latest_title); ?></span>
                <?php endif; ?>
                <strong><?php echo esc_html($context['card_cta']); ?> <span aria-hidden="true">→</span></strong>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php get_footer(); ?>
