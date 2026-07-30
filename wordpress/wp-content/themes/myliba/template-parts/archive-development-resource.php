<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_type = (string) get_query_var('post_type');
$post_type_object = get_post_type_object($post_type);
$title = $post_type_object?->labels->name ?: post_type_archive_title('', false);
?>
<section class="development-archive-hero">
    <div class="development-shell">
        <a href="<?php echo esc_url(myliba_page_url('development')); ?>">← Gelişim Merkezi</a>
        <p class="eyebrow">Sürekli gelişim kaynakları</p>
        <h1><?php echo esc_html($title); ?></h1>
    </div>
</section>

<section class="development-resource-list development-shell">
    <?php if (have_posts()) : ?>
        <div class="development-resource-list__grid">
            <?php while (have_posts()) : the_post(); ?>
                <a class="development-resource-card" href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="development-resource-card__image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    <span><?php echo esc_html(get_the_date()); ?></span>
                    <h2><?php the_title(); ?></h2>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 28)); ?></p>
                    <strong>İçeriği inceleyin <span aria-hidden="true">→</span></strong>
                </a>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <div class="development-resource-list__empty">
            <h2>Yeni içerikler hazırlanıyor.</h2>
            <p>Bu bölüme eklenecek içerikler WordPress yönetim panelinden otomatik olarak burada yayınlanır.</p>
        </div>
    <?php endif; ?>
</section>
