<?php
if (!defined('ABSPATH')) {
    exit;
}

$items = isset($args['items']) && is_array($args['items']) ? $args['items'] : [];
$title = trim((string) ($args['title'] ?? ''));
$section_id = sanitize_title((string) ($args['id'] ?? 'expand'));
$classes = trim('section academy-v2-faq ' . (string) ($args['class'] ?? ''));

if (!$items || $title === '') {
    return;
}
?>
<section id="<?php echo esc_attr($section_id); ?>" class="<?php echo esc_attr($classes); ?>">
    <h2><?php echo esc_html($title); ?></h2>
    <div class="academy-v2-faq__items">
        <?php foreach ($items as $item) : ?>
            <?php
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));
            if ($question === '' || $answer === '') {
                continue;
            }
            ?>
            <details>
                <summary>
                    <span><?php echo esc_html($question); ?></span>
                    <span class="academy-v2-faq__icon" aria-hidden="true"></span>
                </summary>
                <div><?php echo wp_kses_post($answer); ?></div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
