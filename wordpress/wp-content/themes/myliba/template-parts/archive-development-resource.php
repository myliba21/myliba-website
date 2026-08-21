<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_type = (string) get_query_var('post_type');
$post_type_object = get_post_type_object($post_type);
$title = $post_type_object?->labels->name ?: post_type_archive_title('', false);
$is_report = $post_type === 'myliba_report';
$archive_key = $is_report ? 'reports' : 'ebooks';
if (!$is_report && myliba_current_language() === 'en') {
    $title = 'e-Books';
}
$development_context = myliba_development_center_context();
$development_page_id = (int) ($development_context['page_id'] ?? 0);
$shared_copy = static fn (string $key): string => \Myliba\Core\PageContent\text($development_page_id, 'development', 'archive_' . $key);
$archive_copy = static fn (string $key): string => \Myliba\Core\PageContent\text($development_page_id, 'development', $archive_key . '_' . $key);
$archive_rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($development_page_id, 'development', $archive_key . '_' . $key);
$archive_content = [
    'kicker' => $archive_copy('kicker'),
    'lead' => $archive_copy('lead'),
    'visual_label' => $archive_copy('visual_label'),
    'visual_title' => $archive_copy('visual_title'),
    'journey' => array_column($archive_rows('journey'), 'label'),
    'list_kicker' => $archive_copy('list_kicker'),
    'list_title' => $archive_copy('list_title'),
    'list_text' => $archive_copy('list_text'),
    'empty_title' => $archive_copy('empty_title'),
    'empty_text' => $archive_copy('empty_text'),
    'topics' => array_column($archive_rows('topics'), 'label'),
];
?>
<div class="development-resource-archive development-resource-archive--<?php echo esc_attr($archive_key); ?>">
<section class="development-archive-hero">
    <div class="development-shell">
        <div class="development-archive-hero__grid">
            <div class="development-archive-hero__copy">
                <a class="development-archive-hero__back" href="<?php echo esc_url(myliba_page_url('development')); ?>">← <?php echo esc_html($shared_copy('back_label')); ?></a>
                <p class="eyebrow"><?php echo esc_html($archive_content['kicker']); ?></p>
                <h1><?php echo esc_html($title); ?></h1>
                <p class="development-archive-hero__lead"><?php echo esc_html($archive_content['lead']); ?></p>
                <div class="development-archive-hero__actions">
                    <a class="myliba-button myliba-button--primary" href="#kaynaklar"><?php echo esc_html($shared_copy('discover_label')); ?></a>
                    <a class="development-archive-hero__text-link" href="<?php echo esc_url(myliba_page_url('development')); ?>"><?php echo esc_html($shared_copy('all_content_label')); ?> <span aria-hidden="true">→</span></a>
                </div>
            </div>

            <div class="development-insight-visual" aria-hidden="true">
                <div class="development-insight-visual__topline">
                    <span><?php echo esc_html($archive_content['visual_label']); ?></span>
                    <i></i>
                </div>
                <strong><?php echo esc_html($archive_content['visual_title']); ?></strong>
                <div class="development-insight-visual__signal">
                    <i></i><i></i><i></i><i></i><i></i>
                </div>
                <div class="development-insight-visual__steps">
                    <?php foreach ($archive_content['journey'] as $index => $journey_item) : ?>
                        <span><b><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo esc_html($journey_item); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="kaynaklar" class="development-resource-list development-shell">
    <?php if ($is_report) : ?>
        <header class="development-resource-list__heading">
            <div>
                <p class="eyebrow"><?php echo esc_html($archive_content['list_kicker']); ?></p>
                <h2><?php echo esc_html($archive_content['list_title']); ?></h2>
            </div>
            <p><?php echo esc_html($archive_content['list_text']); ?></p>
        </header>
    <?php endif; ?>

    <?php if (have_posts()) : ?>
        <div class="development-resource-list__grid">
            <?php $resource_index = 0; ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php
                $resource_id = get_the_ID();
                $resource_url = get_permalink($resource_id);
                $resource_summary = myliba_excerpt($resource_id, 28);
                $resource_link_label = $shared_copy('item_link_label');
                $resource_new_tab = false;
                $resource_author = '';

                if (!$is_report) {
                    $configured_url = \Myliba\Core\PageContent\text($resource_id, 'ebook', 'card_link_url');
                    $configured_summary = \Myliba\Core\PageContent\text($resource_id, 'ebook', 'listing_summary');
                    $configured_label = \Myliba\Core\PageContent\text($resource_id, 'ebook', 'card_link_label');
                    $resource_url = $configured_url !== '' ? $configured_url : $resource_url;
                    $resource_summary = $configured_summary !== '' ? $configured_summary : $resource_summary;
                    $resource_link_label = $configured_label !== '' ? $configured_label : $resource_link_label;
                    $resource_new_tab = \Myliba\Core\PageContent\text($resource_id, 'ebook', 'card_link_new_tab') === '1';
                    $resource_author = \Myliba\Core\PageContent\text($resource_id, 'ebook', 'kicker');
                }
                ?>
                <a class="development-resource-card <?php echo $resource_index === 0 ? 'development-resource-card--featured' : ''; ?>" href="<?php echo esc_url($resource_url); ?>"<?php echo $resource_new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="development-resource-card__image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="development-resource-card__meta">
                        <span><?php echo esc_html($shared_copy($is_report ? 'report_item_label' : 'ebook_item_label')); ?></span>
                        <?php if (!$is_report && $resource_author !== '') : ?>
                            <small><?php echo esc_html($resource_author); ?></small>
                        <?php else : ?>
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                        <?php endif; ?>
                    </div>
                    <h3><?php the_title(); ?></h3>
                    <p><?php echo esc_html($resource_summary); ?></p>
                    <strong><?php echo esc_html($resource_link_label); ?> <span aria-hidden="true">→</span></strong>
                </a>
                <?php $resource_index++; ?>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <div class="development-resource-list__empty">
            <div class="development-resource-list__empty-copy">
                <span aria-hidden="true">+</span>
                <p class="eyebrow"><?php echo esc_html($shared_copy('empty_eyebrow')); ?></p>
                <h3><?php echo esc_html($archive_content['empty_title']); ?></h3>
                <p><?php echo esc_html($archive_content['empty_text']); ?></p>
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('blog')); ?>"><?php echo esc_html($shared_copy('empty_button_label')); ?></a>
            </div>
            <div class="development-resource-list__topics" aria-label="<?php echo esc_attr($shared_copy('topics_aria')); ?>">
                <?php foreach ($archive_content['topics'] as $index => $topic) : ?>
                    <span><b><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo esc_html($topic); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
</div>
