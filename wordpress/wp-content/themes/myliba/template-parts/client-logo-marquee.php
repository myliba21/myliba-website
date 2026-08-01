<?php
if (!defined('ABSPATH')) {
    exit;
}

$component = wp_parse_args($args ?? [], [
    'label' => '',
    'title' => '',
    'text' => '',
    'class' => '',
    'heading_id' => '',
    'limit' => 24,
    'logos' => null,
    'fallback_items' => [],
]);

$logo_posts = is_array($component['logos'])
    ? $component['logos']
    : myliba_client_logo_posts((int) $component['limit']);
$fallback_items = is_array($component['fallback_items']) ? array_filter($component['fallback_items']) : [];

if (!$logo_posts && !$fallback_items) {
    return;
}

$section_classes = trim('section band trust-section client-logo-marquee ' . sanitize_html_class((string) $component['class']));
$heading_id = sanitize_html_class((string) $component['heading_id']);
$aria_label = (string) ($component['title'] ?: __('Client logos', 'myliba'));
?>
<section class="<?php echo esc_attr($section_classes); ?>" <?php echo $heading_id !== '' ? 'aria-labelledby="' . esc_attr($heading_id) . '"' : 'aria-label="' . esc_attr($aria_label) . '"'; ?>>
    <?php if ($component['label'] !== '' || $component['title'] !== '' || $component['text'] !== '') : ?>
        <div class="trust-section__heading">
            <div class="trust-section__heading-copy">
                <?php if ($component['label'] !== '') : ?>
                    <span class="trust-section__eyebrow">
                        <svg aria-hidden="true" viewBox="0 0 24 24">
                            <path d="m9 12 2 2 4-4" />
                            <path d="M12 3 4.5 6v5.5c0 4.6 3.2 7.7 7.5 9.5 4.3-1.8 7.5-4.9 7.5-9.5V6L12 3Z" />
                        </svg>
                        <?php echo esc_html($component['label']); ?>
                    </span>
                <?php endif; ?>
                <?php if ($component['title'] !== '') : ?>
                    <strong <?php echo $heading_id !== '' ? 'id="' . esc_attr($heading_id) . '"' : ''; ?>><?php echo esc_html($component['title']); ?></strong>
                <?php endif; ?>
                <?php if ($component['text'] !== '') : ?>
                    <p><?php echo esc_html($component['text']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($logo_posts) : ?>
        <div class="trust-marquee" aria-label="<?php echo esc_attr(__('Client logos', 'myliba')); ?>">
            <div class="trust-marquee__track">
                <?php for ($repeat = 0; $repeat < 2; $repeat++) : ?>
                    <?php foreach ($logo_posts as $logo_post) : ?>
                        <?php
                        $is_duplicate = $repeat > 0;
                        $logo_url = (string) myliba_meta('_myliba_logo_url', $logo_post->ID);
                        $logo_name = get_the_title($logo_post);
                        $logo_image = get_the_post_thumbnail($logo_post->ID, 'medium', [
                            'loading' => 'lazy',
                            'alt' => $logo_name,
                        ]);
                        ?>
                        <?php if ($logo_url !== '') : ?>
                            <a class="trust-logo" href="<?php echo esc_url($logo_url); ?>" aria-label="<?php echo esc_attr($logo_name); ?>" <?php echo $is_duplicate ? 'aria-hidden="true" tabindex="-1"' : ''; ?>><?php echo wp_kses_post($logo_image); ?></a>
                        <?php else : ?>
                            <span class="trust-logo" <?php echo $is_duplicate ? 'aria-hidden="true"' : ''; ?>><?php echo wp_kses_post($logo_image); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endfor; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="trust-row">
            <?php foreach ($fallback_items as $item) : ?><span><?php echo esc_html($item); ?></span><?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
