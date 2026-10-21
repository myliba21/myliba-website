# SEO ve AI görünürlüğü kontrolü

Her blog yazısı ve geliştirme tesliminden önce SEO ve AI görünürlüğü etkisini değerlendir. Kamusal içeriği veya arama davranışını etkileyen işlerde `seo-ai-review` skill'ini kullan. Skill erişilebilir değilse bunu belirt ve mevcut proje SEO kurallarıyla denetimi sürdür.

Kamusal SEO etkisi olmayan geliştirmelerde kısa bir “SEO etkisi yok” değerlendirmesi yeterlidir. İçerik ve site değişikliklerinde ölçülen kontrolleri, eksikleri ve kritik engelleri raporla; taslak incelemesini canlı URL denetimi gibi sunma.

Myliba SEO altyapısı `wordpress/wp-content/plugins/myliba-core/includes/seo.php`, editör SEO alanları `includes/meta.php` içindedir. Mevcut SEO eklentisiyle görev paylaşımını incele; çift canonical/meta/schema üretme. Staging noindex korumalarını koru. Bu kontrol talimatı canlı yayına alma yetkisi vermez.
