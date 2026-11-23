<?php
/**
 * Myliba Wheel: inline animated SVG (replaces the former mp4 loop).
 *
 * Inline on purpose: the labels are real, translatable text in the DOM, the
 * page font is inherited, and main.js can pause the SMIL animation (reduced
 * motion, off-screen, pause button). Every label goes through myliba_text().
 */
if (!defined('ABSPATH')) {
    exit;
}

$t = static function (string $source): string {
    return esc_html(myliba_text($source));
};
$label_id = isset($args['describedby']) ? (string) $args['describedby'] : '';
?>
<svg class="software-wheel__svg" xmlns="http://www.w3.org/2000/svg" viewBox="598 70 564 564" width="564" height="564" role="img" focusable="false"
    aria-label="<?php echo esc_attr(myliba_text('Myliba Çarkı animasyonu: KPI, OKR, taahhütler ve davranışlar CFR etrafında dönüyor.')); ?>"<?php if ($label_id !== '') : ?> aria-describedby="<?php echo esc_attr($label_id); ?>"<?php endif; ?>>
    <title><?php echo $t('Myliba Kültürel Yönetim Modeli'); ?></title>
    <defs>
        <radialGradient id="mw-disc" cx="50%" cy="50%" r="50%"><stop offset="0" stop-color="#17234d"/><stop offset="1" stop-color="#0f1a3b"/></radialGradient>
        <linearGradient id="mw-arm" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#cfd5e0"/></linearGradient>
        <path id="mw-ring" d="M780 352A100 100 0 1 1 980 352A100 100 0 1 1 780 352"/>
        <path id="mw-arc-tr" d="M998.3 129.5A252 252 0 0 1 1128.2 308.2"/>
        <path id="mw-arc-bl" d="M622.0 397.5A262 262 0 0 0 757.0 583.3"/>
    </defs>
    <circle cx="880" cy="352" r="280" fill="none" stroke="#ffffff" stroke-opacity=".05" stroke-width="8"/>
    <circle cx="880" cy="352" r="268" fill="url(#mw-disc)"/>
    <circle cx="880" cy="352" r="268" fill="none" stroke="#ffffff" stroke-opacity=".18" stroke-width="2"/>
    <circle cx="880" cy="352" r="244" fill="none" stroke="#ffffff" stroke-opacity=".12"/>

    <path d="M880 352L880.0 108.0A244 244 0 0 1 1124.0 352.0z" fill="#309f78" fill-opacity=".10"/>
    <path d="M880.0 84.0A268 268 0 0 1 1148.0 352.0" stroke="#309f78" stroke-width="5" fill="none"/>
    <text x="1026" y="252" font-size="27" font-weight="800" fill="#3fc297" text-anchor="middle">OKR</text>
    <text x="1026" y="271" font-size="10.5" fill="#b4bbc9" text-anchor="middle"><?php echo $t('Farklı ne yapacağız?'); ?></text>

    <path d="M880 352L1124.0 352.0A244 244 0 0 1 880.0 596.0z" fill="#ffbf00" fill-opacity=".10"/>
    <path d="M1148.0 352.0A268 268 0 0 1 880.0 620.0" stroke="#ffbf00" stroke-width="5" fill="none"/>
    <text x="1026" y="450" font-size="21" font-weight="800" fill="#ffc933" text-anchor="middle"><?php echo $t('Taahhütler'); ?></text>
    <text x="1026" y="469" font-size="10.5" fill="#b4bbc9" text-anchor="middle"><?php echo $t('Nasıl katkı sağlarım?'); ?></text>

    <path d="M880 352L880.0 596.0A244 244 0 0 1 636.0 352.0z" fill="#c21e2d" fill-opacity=".10"/>
    <path d="M880.0 620.0A268 268 0 0 1 612.0 352.0" stroke="#c21e2d" stroke-width="5" fill="none"/>
    <text x="734" y="450" font-size="21" font-weight="800" fill="#e8505f" text-anchor="middle"><?php echo $t('Davranışlar'); ?></text>
    <text x="734" y="469" font-size="10.5" fill="#b4bbc9" text-anchor="middle"><?php echo $t('Etkim ve katkım ne?'); ?></text>

    <path d="M880 352L636.0 352.0A244 244 0 0 1 880.0 108.0z" fill="#0a7faf" fill-opacity=".10"/>
    <path d="M612.0 352.0A268 268 0 0 1 880.0 84.0" stroke="#0a7faf" stroke-width="5" fill="none"/>
    <text x="734" y="252" font-size="27" font-weight="800" fill="#3aa9dc" text-anchor="middle">KPI</text>
    <text x="734" y="271" font-size="10.5" fill="#b4bbc9" text-anchor="middle"><?php echo $t('Hangi sonuçlara ulaştık?'); ?></text>

    <line x1="880" y1="124" x2="880" y2="574" stroke="#ffffff" stroke-opacity=".6" stroke-width="2"/>
    <path d="M0 -7L6 6L-6 6z" transform="translate(880 584) rotate(180)" fill="#ffffff" fill-opacity=".8"/>
    <text x="880" y="102" font-size="10.5" font-weight="700" letter-spacing="3.5" fill="#a9b0bf" text-anchor="middle"><?php echo $t('STRATEJİ'); ?></text>
    <text x="880" y="610" font-size="10.5" font-weight="700" letter-spacing="3.5" fill="#a9b0bf" text-anchor="middle"><?php echo $t('EYLEM'); ?></text>
    <line x1="652" y1="352" x2="1102" y2="352" stroke="#ffffff" stroke-opacity=".6" stroke-width="2"/>
    <path d="M0 -7L6 6L-6 6z" transform="translate(1112 352) rotate(90)" fill="#ffffff" fill-opacity=".8"/>
    <circle cx="880" cy="352" r="5" fill="#fff"/>
    <text x="880" y="376" font-size="10" font-weight="700" letter-spacing="2" fill="#a9b0bf" text-anchor="middle"><?php echo $t('BUGÜN'); ?></text>
    <text transform="translate(630 352) rotate(-90)" font-size="10.5" font-weight="700" letter-spacing="3.5" fill="#a9b0bf" text-anchor="middle"><?php echo $t('GEÇMİŞ'); ?></text>
    <text transform="translate(1136 352) rotate(90)" font-size="10.5" font-weight="700" letter-spacing="3.5" fill="#a9b0bf" text-anchor="middle"><?php echo $t('GELECEK'); ?></text>

    <path d="M880 118L914 318L880 352L846 318z" fill="url(#mw-arm)"/>
    <path d="M1114 352L914 386L880 352L914 318z" fill="url(#mw-arm)"/>
    <g font-size="12.5" font-weight="700" fill="#0a1430" text-anchor="middle">
        <text transform="translate(880 202) rotate(-90)" dy="4"><?php echo $t('Odak'); ?></text>
        <text x="1030" y="356"><?php echo $t('İleri Bildirim'); ?></text>
    </g>
    <path d="M880 586L846 386L880 352L914 386z" fill="url(#mw-arm)"/>
    <path d="M646 352L846 318L880 352L846 386z" fill="url(#mw-arm)"/>
    <g font-size="12.5" font-weight="700" fill="#0a1430" text-anchor="middle">
        <text transform="translate(880 502) rotate(90)" dy="4"><?php echo $t('Aksiyon'); ?></text>
        <text x="730" y="356"><?php echo $t('Geri Bildirim'); ?></text>
    </g>

    <g>
        <circle cx="880" cy="352" r="88" fill="#0f1a3b"/>
        <path d="M889.2 264.5A88 88 0 0 1 967.5 342.8" stroke="#3fc297" stroke-width="4" stroke-linecap="round" fill="none"/>
        <path d="M967.5 361.2A88 88 0 0 1 889.2 439.5" stroke="#ffc933" stroke-width="4" stroke-linecap="round" fill="none"/>
        <path d="M870.8 439.5A88 88 0 0 1 792.5 361.2" stroke="#e8505f" stroke-width="4" stroke-linecap="round" fill="none"/>
        <path d="M792.5 342.8A88 88 0 0 1 870.8 264.5" stroke="#3aa9dc" stroke-width="4" stroke-linecap="round" fill="none"/>
        <g font-size="10.5" font-weight="700" letter-spacing=".6" fill="#ffffff" stroke="#0a1430" stroke-width="3.2" stroke-linejoin="round" paint-order="stroke" text-anchor="middle">
            <text><textPath href="#mw-ring" startOffset="15.3%"><?php echo $t('Tanıma ve Takdir'); ?></textPath></text>
            <text><textPath href="#mw-ring" startOffset="48.6%"><?php echo $t('Etkin Dinleme'); ?></textPath></text>
            <text><textPath href="#mw-ring" startOffset="81.9%"><?php echo $t('Güçlü Sorular'); ?></textPath></text>
        </g>
        <animateTransform attributeName="transform" type="rotate" from="0 880 352" to="360 880 352" dur="36s" repeatCount="indefinite"/>
    </g>
    <circle cx="880" cy="352" r="52" fill="#0a1430" stroke="#ffffff" stroke-opacity=".85" stroke-width="2"/>
    <text x="880" y="354" font-size="26" font-weight="800" fill="#ffffff" text-anchor="middle">CFR</text>
    <text x="880" y="371" font-size="9.5" fill="#a9b0bf" text-anchor="middle"><?php echo $t('Oyunun Kuralları'); ?></text>
    <g font-size="10.5" font-weight="600" letter-spacing="1.8" fill="#8d95a6" text-anchor="middle">
        <text><textPath href="#mw-arc-tr" startOffset="50%"><?php echo $t('Sürekli ve anlamlı diyalog'); ?></textPath></text>
        <text><textPath href="#mw-arc-bl" startOffset="50%"><?php echo $t('Sürekli ve anlamlı diyalog'); ?></textPath></text>
    </g>
    <g>
        <circle cx="880" cy="352" r="241" fill="none" stroke="#ffffff" stroke-opacity=".35" stroke-width="5" stroke-dasharray="1.5 11.5"/>
        <animateTransform attributeName="transform" type="rotate" from="360 880 352" to="0 880 352" dur="120s" repeatCount="indefinite"/>
    </g>
</svg>
