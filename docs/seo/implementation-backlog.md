# SEO / GEO uygulama backlog'u

22 Ekim 2026. TASK-SEO-001 başlangıç denetimi ve teslim dosyaları hazır. Kullanıcı onayıyla B01 yerelde uygulandı ve doğrulandı; [uygulama kaydı](changes.md). GSC URL bazlı analiz ve staging testleri eksik; coverage nedenleri henüz URL'lerle eşleştirilmedi.

| ID | Öncelik | İş / uygulanacak konum | Bağımlılık | Kabul kriteri | Durum / sahip |
| --- | --- | --- | --- | --- | --- |
| B00 | P0 | GSC Coverage URL listeleri + Queries/Pages | GSC sahibi dışa aktarımı | Her nedenin URL'leri; son 3 ay Türkiye/Web sorgu ve sayfa raporları; CSV'ye kanıta dayalı sınıflandırma | Veri bekliyor / GSC sahibi |
| B01 | P0 | Kayıt başarısızlığında başarıyı durdur; `includes/forms.php::handle` | Kullanıcı onayı alındı | Geçerli talepte DB başarısızlığında error ve bildirim yok; başarılı gerçek yerel kayıt doğrulandı; honeypot'un yanıltıcı başarı yanıtı dönüşüm kanıtı değildir | Yerelde tamamlandı: 8 senaryo / 48 kontrol; staging/yayın yapılmadı |
| B02 | P0 | Demo için tekil `generate_lead`; forms.php ve tema assets/js/main.js | B01; GA4 sahibi, consent ve başarı token tasarımı | Backend kayıt kanıtı; yalnız demo; refresh/back/query taklidi/honeypot/validasyon hatası event üretmez; PII yok; DebugView tek event; test kayıtları etiketli | Hazırlanacak / geliştirme + analitik |
| B03 | P1 | İlk landing/UTM/source-medium aktarımı, forms.php | B02 attribution tanımı, veri sahibi | İzinli alanlar sunucuda doğrulanmış; CRM veya mevcut submission hedefi belirli; kişisel veri GA4'e aktarılmıyor; organik CVR pay/payda tanımlı | Karar bekliyor / analitik + satış |
| B04 | P0 | OKR yazılımı ve performans hedef URL kararı; keyword-url-map.md | B00, içerik sahibi onayı | Her ana sorgunun tek birincil hedefi; ürün çatısı/landing farkı yazılı; yeni URL açılmadan redirect gereksinimi belgeli | Öneri hazır / içerik + ürün |
| B05 | P0 | Var olan OKR landing ve yazılım ilk ekranını geliştirme; meta.php, PageContent alanları, single-myliba_landing.php/conversion-detail.php ve page-software.php | B04, doğrulanmış ürün ekranları/özellikler | Tek H1; açık ürün tanımı; ilk ekran ve finalde gerçek demo CTA; CMS ile düzenlenebilir; gerçek alt metin; mobil/masaüstü QA; self-canonical/index/sitemap | Onay sonrası / içerik + geliştirme |
| B06 | P1 | Mevcut performans/danışmanlık içerik ayrımı | B04, gerçek ürün/hizmet bilgisi | Hizmet ve ürün niyeti ayrı; danışmanlık/koçluk/uzman iç bağlantıları; schema ikinci kez üretilmiyor | Planlandı / içerik |
| B07 | P0 | GSC'de görülen other-4xx / önemli 404'ler | B00 | Trafik/backlink ve kaynak hata belirli; anlamlı hedef varsa tek 301, yoksa uygun 404/410; döngü yok; sitemap/iç bağlantılar tutarlı | URL listesi bekliyor / geliştirme |
| B08 | P1 | Tüm sitemap alt listeleri ve URL politikası; seo.php | Tam tarama + gerekiyorsa TASK-SEO-002 onayı | Yalnız 200, canonical, indexlenebilir hedefler; query/noindex/redirect hariç; hiçbir staging koruması kalkmıyor | İlk 3 liste incelendi / geliştirme |
| B09 | P1 | Hreflang ve x-default politikası; seo.php/options.php | TR/EN içerik sahibi; tüm dil eşleri | Karşılıklı 200/self-canonical/indexlenebilir gerçek eşler; x-default hedefi bilinçli; Polylang/SEO eklentisi varsa çift üretim yok | Yazılım çifti doğrulandı / geliştirme |
| B10 | P1 | Nicel/garanti iddiaları, logo izinleri ve uzman bağlamı | Ürün, müşteri ve editör kanıtları | Her iddia için kaynak/kapsam/tarih; gerçek yazar/uzman; izinsiz logo veya uydurma vaka yok | Kanıt bekliyor / ürün + içerik |
| B11 | P1 | Mevcut dört SEO yazısını gözden geçir; content/seo-okr/ | B04, güncel birincil kaynaklar, editoryal onay | Karşılaştırmada metodoloji/kaynak tarihi; özgün niyet; gerçek yazar; mevcut URL'lere bağlantı; indirmeler gerçek hedefe bağlı; otomatik yayın yok | Yerel taslaklar mevcut / editör |
| B12 | P1 | Mobil CWV ve görsel/font/script baseline | Tarayıcı testi, üretim temsilî ortam | LCP/INP/CLS ölçüm kaynağı, tarih ve örneklem; ölçüm sonrası yalnız ilgili optimizasyon; önce/sonra kıyas | Ölçülmedi / geliştirme |
| B13 | P0 | Consent, GA4 ve CSP çalışma testi; functions.php, gerçek hosting headers | Analitik/hukuk sahibi, tarayıcı | İzin öncesi/sonrası ağ ve event davranışı; CSP script/connect uyumu; test ve üretim raporları ayrılmış; GA4 hesabı doğrulanmış | Ölçülmedi / analitik + geliştirme |
| B14 | P1 | Arama botu / eğitim botu erişim politikası; seo.php::robots_txt | İçerik sahibinin politika kararı | Amaçlar ayrı belgeli; özel bot gruplarında hassas yol politikaları değerlendirilmiş; eğitim erişimi görünürlük şartı sayılmıyor | Mevcut izinler korunuyor / ürün sahibi |
| B15 | P0 | Staging, yedek ve geri alma kanıtı | Hosting sahibi; herhangi bir deploy öncesi | DB+dosya yedeği ve geri dönüş provası; staging Basic Auth/IP koruması ve noindex; production onayı ayrı; eski sürüm/DB geri alma prosedürü | Doğrulanmadı / hosting |

## Önerilen uygulama sırası

1. B01 ile demo başarı akışını düzelt. Küçük ve bağımsız diff; ölçüm için güvenilir backend sonucu oluştur.
2. B02 ve B13 ile consent'e uygun tekil olayı ve gerçek DebugView testini tamamla. Sonra B03.
3. B00 verileriyle B04 URL kararını ver; yalnız gerçek kritik URL hataları için TASK-SEO-002 kapsamında küçük düzeltmeler hazırla.
4. B05/B06 mevcut sayfa geliştirmesi; B08/B09 teknik tutarlılık; B10/B11 kanıtlı içerik.
5. B12 baseline sonrası performans; veriyle ilk değerlendirme. Haftalık ölçüm rutini bu teslimde otomasyon olarak kurulmadı.

## İlk kod değişikliği için test ve rollback taslağı

- B01: başarılı wp_insert_post, 0/WP_Error, geçersiz nonce, honeypot, limit aşımı ve eksik alan dalları ile kayıt sonrası bildirim hatası yerel entegrasyon testinde geçti. Bildirimler yakalandı ve test kayıtları silindi. Staging testi ayrıca gerekli.
- B02: gerçek kayıt ve tekil başarı kanıtı; aynı GET'in yenilenmesi, back, iki sekme ve taklit query'de event tekrarı olmamalı. Başarılı contact/academy demo olayı sayılmamalı. PII içermeyen parametre listesi kontrol edilmeli.
- Yerel/staging: ana sayfa, ürün, blog, landing ve demo için HTTP, canonical/meta sayısı ve noindex regresyonu; gerçek staging noindex ve kapalı sitemap korunmalı.
- Rollback: önce sürüm/diff ve ayar/DB yedeği kaydet; gerekirse yalnız ilgili dosyaları önceki sürüme dön. Başarı token/attribution gibi yeni kalıcı veri varsa geri dönüş uyumluluğunu ayrıca belgele. Hazırlanmış bir yedek veya denenmiş rollback varmış gibi kabul etme.

## Teslim kontrolü

- [x] Plan docs/seo altında, özgün kök dosya korunmuş.
- [x] Salt okunur yerel içerik ve aktif eklenti envanteri.
- [x] Temsilî canlı ana sayfa, ticari sayfa, blog/liste, demo HTTP/HTML denetimi.
- [x] Mevcut landing'ler ve plan slug'ları için çakışma matrisi.
- [x] Coverage workbook sayfaları ve veri tarihi doğrulaması.
- [x] Audit, CSV envanteri, URL haritası ve backlog dosyaları.
- [ ] Coverage URL listeleri ile kararların tamamlanması.
- [ ] Queries/Pages performans analizi ve ana hedef onayı.
- [ ] Gerçek demo / GA4 / CRM / staging / mobil QA.

Kod uygulamasına geçiş için planın TASK-SEO-002/003 onay bağımlılıkları korunuyor. SEO denetimi canlı yayın yetkisi oluşturmaz.
