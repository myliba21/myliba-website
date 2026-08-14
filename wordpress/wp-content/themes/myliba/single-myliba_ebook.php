<?php
get_header();

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $content_copy = static fn (string $key): string => \Myliba\Core\PageContent\text($post_id, 'ebook', $key);
    $content_rows = static fn (string $key): array => \Myliba\Core\PageContent\collection($post_id, 'ebook', $key);

    $title = $content_copy('hero_title');
    $kicker = $content_copy('kicker');
    $summary = $content_copy('hero_summary');
    $download_cta_label = $content_copy('download_cta_label');
    $download_file_url = $content_copy('download_file_url');

    $details_eyebrow = $content_copy('details_eyebrow');
    $details_title = $content_copy('details_title');
    $details_text = $content_copy('details_text');

    $chapters = $content_rows('chapters');
    $key_takeaways = $content_rows('key_takeaways');

    $cta_title = $content_copy('cta_title');
    $cta_text = $content_copy('cta_text');
    $cta_button_label = $content_copy('cta_button_label');

    $journey_eyebrow = $content_copy('journey_eyebrow');
    $journey_title = $content_copy('journey_title');
    $principle_1 = $content_copy('principle_1');
    $principle_2 = $content_copy('principle_2');
    $principle_3 = $content_copy('principle_3');

    $ebooks_archive_url = myliba_page_url('ebooks');
    $editor_content = trim(wp_strip_all_tags((string) get_the_content()));
    ?>
    <article class="ebook-detail solution-detail">
        <section class="solution-detail__hero">
            <div class="solutions-shell">
                <div class="solution-detail__hero-grid">
                    <div class="solution-detail__hero-copy">
                        <a class="solution-detail__back" href="<?php echo esc_url($ebooks_archive_url); ?>">← <?php echo esc_html(myliba_text('Tüm e-Kitaplar')); ?></a>
                        <p class="eyebrow"><?php echo esc_html($kicker); ?></p>
                        <h1><?php echo esc_html($title); ?></h1>
                        <p class="solution-detail__lead"><?php echo esc_html($summary); ?></p>
                        <div class="solution-detail__actions">
                            <a class="myliba-button myliba-button--primary" href="<?php echo esc_url($download_file_url); ?>"><?php echo esc_html($download_cta_label); ?></a>
                            <a class="solution-detail__text-link" href="#kitap-ozeti"><?php echo esc_html(myliba_text('Kitap özetini inceleyin')); ?> <span aria-hidden="true">↓</span></a>
                        </div>
                    </div>

                    <div class="solution-journey" aria-hidden="true">
                        <div class="solution-journey__topline">
                            <span><?php echo esc_html($journey_eyebrow); ?></span>
                            <i></i>
                        </div>
                        <strong><?php echo wp_kses_post(nl2br(esc_html($journey_title))); ?></strong>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="ebook-featured-preview" style="margin-top:16px;border-radius:10px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.2);">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="solution-detail__principles" aria-label="<?php echo esc_attr(myliba_text('e-Kitap özellikleri')); ?>">
                    <span><b>01</b> <?php echo esc_html($principle_1); ?></span>
                    <span><b>02</b> <?php echo esc_html($principle_2); ?></span>
                    <span><b>03</b> <?php echo esc_html($principle_3); ?></span>
                </div>
            </div>
        </section>

        <?php if ($details_text !== '') : ?>
            <section id="kitap-ozeti" class="solution-detail__intro solutions-shell">
                <div>
                    <p class="eyebrow"><?php echo esc_html($details_eyebrow); ?></p>
                    <h2><?php echo esc_html($details_title); ?></h2>
                </div>
                <div class="solution-detail__intro-copy">
                    <p><?php echo esc_html($details_text); ?></p>
                    <a href="<?php echo esc_url($download_file_url); ?>"><?php echo esc_html($download_cta_label); ?> <span aria-hidden="true">→</span></a>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($chapters)) : ?>
            <section class="solution-process solutions-shell">
                <header>
                    <p class="eyebrow"><?php echo esc_html(myliba_text('İçindekiler')); ?></p>
                    <h2><?php echo esc_html(myliba_text('Kitap Bölümleri')); ?></h2>
                    <p><?php echo esc_html(myliba_text('Her bölüm, ekiplerinizle hemen uygulayabileceğiniz pratik metodolojiler sunar.')); ?></p>
                </header>
                <div class="solution-process__grid">
                    <?php foreach ($chapters as $index => $chapter) : ?>
                        <article>
                            <span><?php echo esc_html($chapter['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <h3><?php echo esc_html($chapter['title'] ?? ''); ?></h3>
                            <p><?php echo esc_html($chapter['text'] ?? ''); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($key_takeaways)) : ?>
            <section class="solution-outcomes">
                <div class="solutions-shell">
                    <header class="solution-outcomes__heading">
                        <p class="eyebrow"><?php echo esc_html(myliba_text('Temel Kazanımlar')); ?></p>
                        <h2><?php echo esc_html(myliba_text('Bu Rehberle Ne Kazanacaksınız?')); ?></h2>
                    </header>
                    <div class="solution-outcomes__grid">
                        <?php foreach ($key_takeaways as $index => $item) : ?>
                            <article>
                                <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                <p><?php echo esc_html($item['text'] ?? ''); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($editor_content !== '' && $editor_content !== trim(wp_strip_all_tags($summary)) && $editor_content !== trim(wp_strip_all_tags($details_text))) : ?>
            <section class="solution-detail__editor solutions-shell">
                <article class="content">
                    <?php the_content(); ?>
                </article>
            </section>
        <?php endif; ?>

        <section class="solutions-cta">
            <div class="solutions-cta__inner solutions-shell">
                <div class="solutions-cta__copy">
                    <p class="eyebrow"><?php echo esc_html(myliba_text('Dönüşüm Adımı')); ?></p>
                    <h2><?php echo esc_html($cta_title); ?></h2>
                    <p><?php echo esc_html($cta_text); ?></p>
                </div>
                <div class="solutions-cta__actions">
                    <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('contact')); ?>"><?php echo esc_html($cta_button_label); ?></a>
                    <a class="solutions-cta__secondary" href="<?php echo esc_url($ebooks_archive_url); ?>"><?php echo esc_html(myliba_text('Tüm e-kitapları görün')); ?></a>
                </div>
            </div>
        </section>
    </article>
    <?php
endwhile;

get_footer();
