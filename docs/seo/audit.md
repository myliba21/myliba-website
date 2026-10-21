# SEO / GEO başlangıç denetimi

Tarih: 22 Ekim 2026, Europe/Istanbul. Görev: TASK-SEO-001. Durum: kaynak kod, yerel veritabanı ve seçili canlı HTTP yanıtları incelendi. Uygulama kodu, WordPress ayarları, içerik kayıtları ve canlı yayın değiştirilmedi.

Bu metin başlangıç denetiminin kaydıdır. Sonraki kullanıcı onayıyla B01 yerelde düzeltildi; güncel sonuç ve test kanıtı [uygulama kaydında](changes.md). Aşağıdaki bulgular denetim anını anlatır.

## Sonuç ve kapsam

Yeni landing açmadan mevcut sayfalar arasında niyet ayrımı yapılmalı. `/tr/yazilim/` yanında `/tr/icerikler/okr-yazilimi/` ve `/tr/icerikler/performans-yonetimi/` canlıda 200 dönüyor ve kendi canonical adreslerini üretiyor. Danışmanlık zaten `/tr/cozumler/danismanlik/` altında. Önerilen kısa slug'lara geçiş için henüz trafik veya backlink gerekçesi yok.

İncelenen ticari örneklerde yanlış noindex veya çift canonical görülmedi. Demo formunun kaynak kodunda kayıt başarısızlığında bile başarıya yönlenme riski bulundu. Başarılı kayda bağlı demo dönüşüm olayı bulunamadı. Bunlar düzeltme önerileridir; gerçek form gönderimi ve GA4 DebugView testi yapılmadı.

Kanıt dosyaları:

- `evidence/local-content-inventory.json`: yerel WordPress'ten seçili kamusal türlerin 101 kaydı. 38 page, 44 post, 8 solution, 10 landing yayımlanmış; 1 post çöp kutusunda. Ürün türü sorgulandı, kayıt yok. Bu sayı sitenin tüm içerik türlerinin toplamı değildir.
- `evidence/http-checks.json`: 28 başlangıç GET isteği, HTTP zinciri ve çıkarılmış HTML alanları. Tarihler UTC; Türkiye'de 22 Ekim'e karşılık gelir.
- `evidence/http-followup.json`: 11 tekrar/ek GET isteği. Başlangıçta zaman aşımı görülen yazılım ve OKR landing'i tekrar istekte 200 döndü. Sitemap index'e `/sitemap.xml` üzerinden erişildi. Zaman aşımı kalıcı sunucu hatası sayılmadı.
- `evidence/gsc-coverage-summary.json`: sağlanan XLSX'in dört sayfasının salt okunur dökümü.
- `url-inventory.csv`: yerel kayıtlar, örnek canlı yanıtlar ve incelenen sitemap listelerinin birleşimi. `Inlinks` yalnız örneklenen canlı HTML sayfalarından gelen farklı kaynak sayısıdır; tam site taraması değildir. Boş hücre ölçülmedi demektir.

Search Console URL Inspection, sorgu/sayfa performansı, tüm sitemap URL'lerinin HTTP taraması, mobil tarayıcı QA, Core Web Vitals, consent davranışı, gerçek form başarısı ve CRM entegrasyonu ölçülmedi. Canlı WordPress yönetimindeki etkin eklentiler okunmadı. Kaynak kod bulguları otomatik olarak canlı uygulama hatası kabul edilmedi.

## Search Console baz çizgisi

Kaynak: proje kökündeki `myliba.com-Coverage-2026-10-22.xlsx`. `Chart!A47:D47`: **2026-10-12**, 82 indekslenmemiş, 138 indekslenmiş, 114 günlük impression. Dosya tarihini veri tarihi olarak kullanma. Chart 28 Ağustos–12 Ekim arasında 46 günlük gözlem içeriyor. Bu günlük impression serisi sorgu bazlı performans raporu değildir.

| Critical issues satırı | Neden | Sayı |
| --- | --- | ---: |
| 2 | Page with redirect | 21 |
| 3 | Excluded by noindex | 12 |
| 4 | Not found (404) | 11 |
| 5 | Other 4xx | 1 |
| 6 | Discovered, currently not indexed | 21 |
| 7 | Crawled, currently not indexed | 16 |
| 8–9 | Alternate proper canonical / Google different canonical | 0 / 0 |

Altı pozitif nedenin toplamı 82 ile tutarlı. `Non-critical issues` yalnız başlık içeriyor. `Metadata` kapsamı “All known pages” olarak belirtiyor. Hiçbir sayfada sorunlu URL listesi, Queries veya Pages performans verisi yok. Bu nedenle CSV'deki URL'lere GSC nedeni atanmadı. Bugünkü 200 yanıtından Google'da indekslenmiş olduğu sonucu çıkarılmadı.

## Mimari ve SEO görev paylaşımı

| Yüzey | Sahibi / kanıt | Davranış ve sınır |
| --- | --- | --- |
| Tema / içerik modeli | `wordpress/wp-content/themes/myliba/`, `plugins/myliba-core/myliba-core.php` | Özel tema + Myliba Core 0.3.0. İncelenen yerel aktif tema myliba; child tema aktif değil. |
| Yerel aktif eklentiler | local-content-inventory.json | Myliba Core, All-in-One WP Migration, WP Mail SMTP, WPForms Lite. Yoast, Rank Math, AIOSEO ve Polylang aktif listede yok. Dosyanın varlığı aktiflik kanıtı değildir. |
| Title / açıklama alanları | `includes/meta.php:141`, `includes/seo.php:508`, `:540` | `_myliba_seo_title`, `_myliba_seo_description`; açıklama yoksa excerpt/body fallback. Sayfa içerikleri ayrıca PageContent koleksiyonlarından gelebilir; body fallback'in yeterliliği sayfa bazında kontrol edilmeli. |
| Meta / OG / canonical / schema | `includes/seo.php:530` | Yoast/Rank Math/AIOSEO sabiti varsa `render_head()` geri dönüyor. Tema `functions.php:123` ile WordPress `rel_canonical` çıktısını kaldırıyor. Yeni bir üretici ekleme. |
| Harici SEO eklentisi sınırı | `seo.php:11–28`, `:508` | Meta/schema geri çekilse de robots, native sitemap filtreleri, yönlendirmeler ve title filtresi kayıtlı kalıyor. İleride eklenti etkinleşirse sadece meta değil bu katmanların da birlikte testi gerekli. |
| Dil / permalink | `includes/post-types.php:22`, `:64`; tema `functions.php:838` | TR/EN CPT tabanları, `_myliba_language`; TR blog `/tr/yazilar/{slug}/`, landing `/tr/icerikler/{slug}/`. |
| Hreflang | `seo.php:657` | Translation key ile publish durumundaki eşler, özel arşivler ve ana sayfa dalları var. Yazılım TR/EN çiftinde canlı karşılıklılık doğrulandı. Diğer tüm eşler doğrulanmadı. |
| İndeks politikası | `seo.php:95–157`, `:360`; `options.php:267` | `test-web.myliba.com`, genel indeksleme anahtarı ve sayfa noindex alanı. Staging'de robots disallow, noindex başlığı ve sitemap kapatma kodu var. Gerçek staging HTTP testi yapılmadı. |
| Sitemap | `seo.php:257–409` | WordPress native sitemap; kullanıcı/kategori/etiket sağlayıcılarını ve noindex/redirect kayıtlarını filtreliyor, özel arşiv sağlayıcısı ekliyor. |
| SEO editör denetimi | `includes/seo-review.php`, `resources/seo-review-rules.json` | Blog denetimi var; canlı indeksleme kanıtı yerine geçmez. |

Dosya yollarındaki `includes/` aksi belirtilmedikçe `wordpress/wp-content/plugins/myliba-core/includes/` altındadır. Önceki migration/review belgeleri tarihsel bağlamdır; mevcut veritabanı ve gerçek HTTP yanıtı bunların önündedir. Örneğin README'deki “EN tamamlanmamış sayfalar çöp kutusunda” beklentisi mevcut yerel publish kayıtlarıyla örtüşmüyor.

## HTTP, robots, canonical, sitemap ve schema

- `/tr/`, yazılım, OKR landing, performans landing, danışmanlık, demo, OKR koçluğu ve örnek OKR nedir yazısı canlı örnekleminde 200, tek H1, tek açıklama ve self-canonical görüldü. Liste örneği `/tr/yazilar/` da 200, tek H1 ve tek canonical üretiyor. Gerçek Google indeks durumu ölçülmedi.
- `/` çerezsiz GET'te 200 ve `noindex,follow` dil seçicisi. Kod meta refresh ve JS dil seçimi kullanıyor; dil çerezi varsa 302. Bu root noindex bilinçli; otomatik kaldırılmamalı. Çerezli dal ayrıca HTTP test edilmedi.
- Yazılımın `http://myliba.com/`, `https://myliba.com/`, `http://www.myliba.com/` origin varyantları ve slash'sız HTTPS yolu tek 301 ile `https://www.myliba.com/tr/yazilim/` adresine ulaştı. Bu sonuç tüm siteye genellenmez.
- `/tr/kultur-analizi/` tek 301 ile `/tr/cozumler/kultur-analizi/` adresine ulaşıyor. Hata olarak sayılmadı; eski yol incelenen page sitemap'inde yok.
- `/sitemap.xml` tek 301 ile `/wp-sitemap.xml` index'ine ulaşıyor. Index'te 9 alt liste var. İncelenen üç canlı listede page 36, landing 10, solution 6 URL mevcut. Root, eski kültür sayfası ve redirect olan kurumsal gelişim solution kaydı bu listelere alınmıyor. Diğer alt listeler ve 52 URL'nin tamamının HTTP/robots uygunluğu taranmadı.
- Canlı robots native sitemap'i işaret ediyor. Arama, author, login ve form durum parametreleri genel bot grubunda kısıtlı. Özel bot gruplarında `Allow: /` var; genel gruptaki kısıtlamaların her özel gruba uygulanacağını varsayma. Tarama erişimi ve eğitim izni ayrı politika kararı olarak ele alınmalı; bu çalışmada değiştirilmedi. GPTBot eğitim, OAI-SearchBot arama amaçları [OpenAI bot belgesinde](https://developers.openai.com/api/docs/bots) ayrı tanımlanıyor (22 Ekim kontrolü).
- Yazılımda Organization, WebSite, BreadcrumbList, SoftwareApplication, FAQPage; danışmanlıkta Service; blogda Article JSON-LD görüldü. JSON'lar parse ediliyor, örneklerde tür başına çift üretim görülmedi. Rich Results Test / schema validator ve tüm özelliklerin görünür metinle birebir uyumu tamamlanmadı. FAQ'nın kaynak koleksiyonu yazılım şablonuyla ortak; bu tek başına bütün schema doğrulaması değildir.
- Yazılım TR/EN karşılıklı hreflang'i doğrulandı. Alt sayfalarda `x-default` EN, ana sayfada root görülüyor. Bu kendiliğinden hata değildir; TR öncelikli hedefle uyumu ayrıca kararlaştırılmalı. Ayar kaynağı `Options::get('default_locale')`; otomatik değiştirilmedi.

## Demo ve ölçüm

Akış: `/tr/demo/` → `[myliba_demo_form]` → `wp-admin/admin-post.php?action=myliba_contact_form` → `Forms::handle()` → private `myliba_submission` ve meta alanları → e-posta bildirimi → `?myliba_form=success`.

| Bulgu | Kanıt | Öncelik / öneri |
| --- | --- | --- |
| Kayıt oluşmasa da success yönlendirmesi var | `forms.php:279–298`: wp_insert_post kontrolü yalnız meta yazımını sarıyor; bildirim ve success dışarıda | **P0**: kayıt ID'si yoksa hata; başarılı demo yalnız kalıcı kayıtla tanımlansın. E-posta teslimini ayrı izleyin. |
| Success parametresi gerçek kaydı kanıtlamıyor | `forms.php:41`, `:178–186`: URL'den mesaj gösterimi; honeypot da success'e gider | **P0**: conversion tetikleyicisini query parametresine bağlamayın. Backend onaylı tek kullanımlık başarı kanıtı gerekli. |
| GA4 sayfa etiketi var, demo olayı yok | tema `functions.php:2855`; `main.js` ve forms.php araması; gerçek demo HTML'de tek GA etiketi | **P0**: tekil generate_lead, yalnız demo bağlamı + başarılı kayıt. Reload/back/çift gönderim ve spam/hata dalları test edilmeli. GA4 hesabındaki kurallar/GTM ölçülmedi. |
| UTM/ilk landing/source-medium aktarımı yok | `Forms::handle()` veri alanları ve bildirim gövdesi | **P1**: attribution tanımı belirlenip izinli kaynak alanları CRM akışına eklenmeli. CRM bu kodda tespit edilmedi. |
| Form alanları mevcut | render_form() | Ad, soyad, e-posta, telefon, şirket zorunlu; rol ve not isteğe bağlı; çalışan sayısı varsayılan 1–50. Varsayılanın yanlış nitelik verisi üretme riski değerlendirilmeli. Kişisel e-posta reddi yok. |
| Consent ve analitik ortamı | functions.php:2855, `nginx/default.conf` CSP | Tema GA'yı ortam/consent kontrolü göstermeden ekliyor. Örnek nginx CSP dış GA script ve bağlantısına izin vermiyor. Bu konfigürasyon yerel Apache/canlı hosting ile aynı varsayılmadı; gerçek tarayıcı consent ve ağ testi gerekli. |

Form gönderilmedi; satış ekibine e-posta/test lead oluşturulmadı. Hukuki metin yeterliliği hakkında karar verilmedi. Formda checkbox var; aydınlatma ve açık rıza ayrımı, bağlantılar ve metin hukuk sahibince değerlendirilmelidir.

## İçerik / AI hazırlığı ve ilk üç iyileştirme

1. **Demo başarı tanımını düzelt ve ölçümünü kur.** Kayıt başarısızken success dönebilen akış nitelikli demo KPI'ını güvenilmez kılıyor. Dosyalar: `includes/forms.php`, tema `assets/js/main.js`; ayrıntılar backlog'da.
2. **OKR yazılımı için tek birincil hedefi seç.** `/tr/yazilim/` ürün çatısı, mevcut OKR landing'i sorgu odaklı sayfa olarak değerlendirme önerisi var. GSC Queries/Pages gelmeden 301 veya canonical birleştirmesi yapılmamalı. `keyword-url-map.md` karar taslağıdır.
3. **Yazılım ilk ekranını somutlaştır ve iddiaları doğrula.** H1 genel bir performans vaadi; OKR yazılımı arayan kuruma ürünün ne yaptığı ilk cümlede söylenebilir. PageContent software hero alanları üzerinden CMS içeriği düzenlenmeli. Sayfadaki mutlak objektiflik/garanti ve sayısal kazanım iddiaları için ürün/araştırma kanıtı istenmeli; pazarlama metni kanıt sayılmamalı. Müşteri logo izinleri ve gerçek ürün ekranları doğrulanmalı.

`content/seo-okr/` altında dört tam yazı ve doğrulama bekleyen vaka taslağı zaten var. Karşılaştırma sayfasını sıfırdan çoğaltmak yerine mevcut paketin kaynakları, gerçek yazarı, yayın yolu ve indirilebilir dosyaları gözden geçirilmeli. Vaka verisi ve izin olmadan yayımlanmamalı. Blogun yazar kutusu genel Myliba Team; gerçek uzman profiliyle bağlama işi ayrıca gerekli.

### Skill v1.1: yalnız `/tr/yazilim/` örneği için geçici değerlendirme

Site geneli puanı değildir. Yerel kod + canlı HTTP örneklemi + yazılım metninin editoryal incelemesidir. Geçti=1, kısmi=0,5; ölçülmedi puana girmez. Ağırlıklar skill v1.1 ile aynıdır.

| Kriter | Ağırlık | Durum | Kanıt, gerekçe ve sonraki işlem |
| --- | ---: | --- | --- |
| C1 Arama niyeti | 10 | Kısmi | Ticari ürün ve demo yolu var; ilk ekran genel, sorgu hedefi netleştirilmeli. |
| C2 Başlık/açıklama | 10 | Kısmi | Tekil title ve description var; OKR niyetini title/ilk ekranla açıklaştırma gerekli. Site geneli özgünlük kontrol edilmedi. |
| C3 Başlık/okunabilirlik | 10 | Geçti | Tek H1, modül H2/H3 bölümleri; anlamlı ürün bölümleri mevcut. |
| C4 Dahili bağlantı/farklı değer | 10 | Ölçülmedi | CTA ve modül bağlantısı var; bütün içerik gövdeleri ve sorgu ayrımı incelenmedi. |
| T1 Erişim/indeks politikası | 10 | Geçti | Canlı 200, robots'ta bu yol engelli değil, meta/header noindex yok. GSC indeks durumu kapsam dışı. |
| T2 URL/dil/yönlendirme | 8 | Geçti | Tek self-canonical, test edilen dört varyant tek 301, TR/EN yazılım çiftinde karşılıklılık. |
| T3 Keşif/render | 7 | Geçti | İçerik/linkler HTTP HTML'de, ürün sayfası native page sitemap'inde; demo linki var. |
| T4 Schema uyumu/tekillik | 5 | Ölçülmedi | JSON parse ve tür sayımı yapıldı; tüm schema değerlerinin görünür içerikle eşliği/validator tamamlanmadı. |
| A1 Doğrudan cevap | 10 | Kısmi | Modüller açıklanıyor; ilk ekrandaki ürün tanımı belirginleştirilmeli. |
| A2 Doğrulanabilir iddialar | 10 | Ölçülmedi | Ürün demosu, müşteri izinleri ve nicel kanıtlar sağlanmadı; mutlak vaatler doğrulanmalı. |
| A3 Uzmanlık/özgün örnek | 10 | Ölçülmedi | Gerçek uzman/örnek ve ekranların doğrulaması tamamlanmadı. |

İçerik: **66,7/100**, kapsam **%75** (20/30 ölçülen sonuç). Teknik: **100/100**, kapsam **%83,3** (25/25 ölçülen sonuç). AI: **50/100**, kapsam **%33,3** (5/10 ölçülen sonuç). Toplam: **76,9/100**, kapsam **%65** (50/65). Bu sonuç yalnız ölçülen kriterlere aittir; yayına hazır kararı veya arama görünürlüğü tahmini değildir. Özellikle yüksek teknik alt puan, ölçülmeyen schema/CWV/staging testlerinin geçtiği anlamına gelmez.

Örneklenen canlı sayfalarda doğrulanmış kritik indeksleme engeli görülmedi. GSC'deki 1 other-4xx ve 11 404'ün ticari URL olup olmadığı bilinmiyor. Yanıltıcı iddialar için kanıt eksikliği ve demo kayıt/ölçüm sorunu ayrıca açık tutuldu.

## Sonraki adım ve bağımlılıklar

Önerilen ilk kod değişikliği: **B01 demo kayıt hatası akışı**. Ölçümün temeli olduğu için GA4 event'inden önce yapılmalı. Ardından B02 tekil dönüşüm ölçümü, B03 kaynak aktarımı, B04 niyet/URL kararı gelir. B01 için yerel testte wp_insert_post başarısızlığı error, başarılı kayıt success olmalı; spam ve hata dalları dönüşüm üretmemeli. Staging doğrulaması, yedek ve rollback kanıtı tamamlanmadan yayın yok.

Plan TASK-SEO-002'yi “TASK-SEO-001 tamam + insan onayı”, TASK-SEO-003'ü URL kararının onayına bağlıyor. Bu ilk teslim bu sınırda durur. Bu sınır skill'in eklediği bir onay kuralı değildir; kullanıcının verdiği uygulama planındandır. Açık kararlar ve uygulanacak dosyalar `implementation-backlog.md` içinde somutlaştırılmıştır.
