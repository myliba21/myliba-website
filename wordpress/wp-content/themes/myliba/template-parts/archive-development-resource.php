<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_type = (string) get_query_var('post_type');
$post_type_object = get_post_type_object($post_type);
$title = $post_type_object?->labels->name ?: post_type_archive_title('', false);
$is_report = $post_type === 'myliba_report';
$archive_key = $is_report ? 'reports' : 'ebooks';
$archive_content = $is_report ? [
    'kicker' => 'Araştırma, veri ve gelecek sinyalleri',
    'lead' => 'İş dünyasını, performans kültürünü ve liderliği şekillendiren güncel araştırmaları uygulanabilir içgörülere dönüştürün.',
    'visual_label' => 'Myliba trend radarı',
    'visual_title' => 'Sinyalleri okuyun. Değişime yön verin.',
    'journey' => ['Araştır', 'Yorumla', 'Uygula'],
    'principles' => ['Güncel araştırmalar', 'Uygulanabilir içgörüler', 'Kurumsal perspektif'],
    'list_kicker' => 'Seçili araştırmalar',
    'list_title' => 'Bugünün verisiyle yarının çalışma kültürünü okuyun.',
    'list_text' => 'Raporları, araştırma notlarını ve öne çıkan trend analizlerini tek yerde keşfedin.',
    'empty_title' => 'İlk araştırma dosyaları hazırlanıyor.',
    'empty_text' => 'Yeni raporlar yayınlandığında bu alanda otomatik olarak yerini alacak. Bu sırada güncel yazılarımızı keşfedebilirsiniz.',
    'topics' => ['Performans kültürü', 'Liderlik ve dönüşüm', 'İş dünyasının geleceği'],
] : [
    'kicker' => 'Rehberler, araçlar ve uygulama kaynakları',
    'lead' => 'Hedef, liderlik ve kültür pratiklerini günlük işe taşımanıza yardımcı olacak indirilebilir kaynakları keşfedin.',
    'visual_label' => 'Myliba uygulama kütüphanesi',
    'visual_title' => 'Bilgiyi alın. Ekibinizle uygulayın.',
    'journey' => ['Keşfet', 'İndir', 'Uygula'],
    'principles' => ['Pratik rehberler', 'Kullanıma hazır araçlar', 'Ekip uygulamaları'],
    'list_kicker' => 'Kaynak kütüphanesi',
    'list_title' => 'Gelişimi günlük çalışma ritmine taşıyan kaynaklar.',
    'list_text' => 'İhtiyacınıza uygun rehberi seçin, ekibinizle paylaşın ve uygulamaya başlayın.',
    'empty_title' => 'İlk e‑kitaplar hazırlanıyor.',
    'empty_text' => 'Yeni kaynaklar yayınlandığında bu alanda otomatik olarak yerini alacak. Bu sırada güncel yazılarımızı keşfedebilirsiniz.',
    'topics' => ['OKR ve hedef yönetimi', 'Liderlik pratikleri', 'Kültür ve performans'],
];
$resource_navigation = myliba_development_center_items();
?>
<div class="development-resource-archive development-resource-archive--<?php echo esc_attr($archive_key); ?>">
<section class="development-archive-hero">
    <div class="development-shell">
        <div class="development-archive-hero__grid">
            <div class="development-archive-hero__copy">
                <a class="development-archive-hero__back" href="<?php echo esc_url(myliba_page_url('development')); ?>">← Gelişim Merkezi</a>
                <p class="eyebrow"><?php echo esc_html($archive_content['kicker']); ?></p>
                <h1><?php echo esc_html($title); ?></h1>
                <p class="development-archive-hero__lead"><?php echo esc_html($archive_content['lead']); ?></p>
                <div class="development-archive-hero__actions">
                    <a class="myliba-button myliba-button--primary" href="#kaynaklar">Kaynakları keşfedin</a>
                    <a class="development-archive-hero__text-link" href="<?php echo esc_url(myliba_page_url('development')); ?>">Tüm gelişim içerikleri <span aria-hidden="true">→</span></a>
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

        <div class="development-archive-hero__principles" aria-label="Kaynakların temel özellikleri">
            <?php foreach ($archive_content['principles'] as $index => $principle) : ?>
                <span><b><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo esc_html($principle); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<nav class="development-resource-nav development-shell" aria-label="Gelişim Merkezi içerik türleri">
    <?php foreach ($resource_navigation as $resource_key => $resource_item) : ?>
        <a href="<?php echo esc_url($resource_item['url']); ?>" <?php echo $resource_key === $archive_key ? 'aria-current="page"' : ''; ?>>
            <span><?php echo esc_html($resource_item['label']); ?></span>
            <small><?php echo esc_html($resource_item['description']); ?></small>
        </a>
    <?php endforeach; ?>
</nav>

<section id="kaynaklar" class="development-resource-list development-shell">
    <header class="development-resource-list__heading">
        <div>
            <p class="eyebrow"><?php echo esc_html($archive_content['list_kicker']); ?></p>
            <h2><?php echo esc_html($archive_content['list_title']); ?></h2>
        </div>
        <p><?php echo esc_html($archive_content['list_text']); ?></p>
    </header>

    <?php if (have_posts()) : ?>
        <div class="development-resource-list__grid">
            <?php $resource_index = 0; ?>
            <?php while (have_posts()) : the_post(); ?>
                <a class="development-resource-card <?php echo $resource_index === 0 ? 'development-resource-card--featured' : ''; ?>" href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="development-resource-card__image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="development-resource-card__meta">
                        <span><?php echo esc_html($is_report ? 'Rapor ve trend' : 'e‑Kitap'); ?></span>
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    </div>
                    <h3><?php the_title(); ?></h3>
                    <p><?php echo esc_html(myliba_excerpt(get_the_ID(), 28)); ?></p>
                    <strong>İçeriği inceleyin <span aria-hidden="true">→</span></strong>
                </a>
                <?php $resource_index++; ?>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <div class="development-resource-list__empty">
            <div class="development-resource-list__empty-copy">
                <span aria-hidden="true">+</span>
                <p class="eyebrow">Çok yakında</p>
                <h3><?php echo esc_html($archive_content['empty_title']); ?></h3>
                <p><?php echo esc_html($archive_content['empty_text']); ?></p>
                <a class="myliba-button myliba-button--primary" href="<?php echo esc_url(myliba_page_url('blog')); ?>">Blog yazılarını keşfedin</a>
            </div>
            <div class="development-resource-list__topics" aria-label="Yaklaşan içerik konuları">
                <?php foreach ($archive_content['topics'] as $index => $topic) : ?>
                    <span><b><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo esc_html($topic); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
</div>
