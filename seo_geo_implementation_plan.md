# Myliba — Türkiye Kurumsal SEO, GEO ve Demo Dönüşüm Uygulama Planı

> **Durum:** Uygulamaya hazır başlangıç planı  
> **Tarih:** 22 Ekim 2026  
> **Pazar:** Türkiye / Türkçe  
> **Ana hedef:** Organik aramadan nitelikli **kurumsal demo talepleri** oluşturmak  
> **Platform:** myliba.com (mevcut WordPress yapısını ve eklentisiz/hafif yaklaşımı koruyarak)

## 0. İş hedefi ve temel prensipler

**Birincil KPI:** Organik kaynaktan gelen **nitelikli demo talebi sayısı** (aylık).  
**İkincil KPI'lar:** organik demo dönüşüm oranı, ilgili ticari sorgularda organik tıklama/gösterim, Search Console sorgu bazlı konum, demo → satış fırsatı oranı, marka adı geçen AI yanıtlarının düzenli ölçümü.

- Sırf daha fazla trafik getiren genel bloglardan önce satın alma niyetli sayfalara yatırım yap.
- Google sıralaması veya AI Overview görünürlüğü **garanti edilemez**. GEO için ayrı sihirli etiket yoktur; erişilebilirlik, kaliteli içerik, otorite ve özgün bilgi esastır.
- SEO amaçlı yapay ve ince içerikler, kopya landing sayfalar, uydurma müşteri referansları, sahte değerlendirmeler, doğrulanmamış fiyat/kabiliyet iddiaları kullanma.
- Teknik değişiklikleri **önce staging ortamında** doğrula; canlıya alma sonrası Search Console ile izle.
- Mevcut WordPress URL'lerini, sitemap ve eklenti düzenini analiz etmeden toplu silme/yönlendirme yapma.
- TR birinci öncelik; EN içerik ikinci fazdır.

## 1. Mevcut durum: Search Console Coverage baz çizgisi

**Kaynak:** `myliba.com-Coverage-2026-10-22.xlsx` (kullanıcının paylaştığı Google Search Console kapsam dışa aktarımı; sorgu ve landing-page performans raporu **değildir**).

| Durum / neden                      | URL sayısı | İlk aksiyon                                                                       |
| ---------------------------------- | ---------: | --------------------------------------------------------------------------------- |
| İndekslenmiş                       |    **138** | Önemli ticari sayfaları teyit et                                                  |
| İndekslenmemiş                     |     **82** | URL bazında sınıflandır; tamamını indeksletmeye çalışma                           |
| Page with redirect                 |         21 | 301 hedefleri, zincir, iç bağlantı ve sitemap kontrolü                            |
| Excluded by `noindex`              |         12 | Bilinçli mi? Yalnız değerli sayfalarda düzelt                                     |
| Not found (404)                    |         11 | Eski URL / trafik / backlink varsa uygun 301; aksi halde 404/410 kabul edilebilir |
| Blocked due to other 4xx issue     |          1 | HTTP yanıtı, erişim ve engel nedenini incele                                      |
| Discovered – currently not indexed |         21 | İç bağlantı, site haritası, kalite, crawl erişimi                                 |
| Crawled – currently not indexed    |         16 | İçerik özgünlüğü, canonical ve arama niyeti uyumu                                 |

> Bu sayılar rapor özeti niteliğindedir; **hangi URL'lerin** etkilendiği yüklenen dosyada yok. Bu dosya tek başına 'şu sayfa düzeltilmeli' kararına yetmez. İşaretlenen `noindex`, redirect, 404 değerlerini otomatik SEO hatası sayma. Dosyadaki `Impressions` sütunu günlük özet görünürlük bağlamıdır; sorgu bazlı fırsat analizi yerine geçmez.

### Coverage denetimi / çıktı

- [ ] Search Console > Indexing > Pages ekranından **her neden için örnek/tam URL listesini dışa aktar**.
- [ ] Search Console > Performance > Search results: son **3 ay**, Türkiye, Web; **Queries** ve **Pages** CSV/XLSX dosyalarını çıkar.
- [ ] Tüm indekslenmemiş URL'leri şu kategorilerden birine ata: `index olmalı`, `bilinçli hariç`, `301`, `404/410`, `araştırılacak`.
- [ ] Önemli ticari URL'ler için canlı URL inspection, HTTP response, canonical ve robots indeksleme kontrolü yap.
- [ ] `sitemap.xml` / `wp-sitemap.xml` / mevcut sitemap index kaynaklarını tespit et; sitemap yalnızca canonical, 200 dönen, indexlenebilir URL'ler içersin.
- [ ] Tekrarlayan HTTP/HTTPS, www/non-www, slash ve dil URL varyantlarını denetle.
- [ ] Kontrol tablosu üret: `URL | Tür | Durum | İndekslenmeli mi | HTTP | Canonical | Inlinks | Sorun | Aksiyon | Öncelik | Sahip | Sonuç`.

**Tamamlanma kriteri:** Her önemli ticari URL için indeksleme politikası ve düzeltme kararı belgelenmiş; kritik 4xx ve canonical yanlışları çözülmüş.

## 2. SEO anahtar kelime haritası (ilk hipotez)

> Öncelikler ürün uyumu ve satın alma niyetine göredir; henüz **arama hacmi, rekabet, pozisyon veya dönüşüm** verisiyle doğrulanmamıştır.

| Öncelik | Ana arama                               | Niyet          | Hedef sayfa tipi                              | Hedef aksiyon       |
| ------- | --------------------------------------- | -------------- | --------------------------------------------- | ------------------- |
| P0      | OKR yazılımı                            | Ticari         | Ürün landing                                  | Demo                |
| P0      | Performans yönetimi yazılımı            | Ticari         | Ürün landing                                  | Demo                |
| P0      | OKR danışmanlığı                        | Hizmet         | Danışmanlık landing                           | Görüşme talebi      |
| P1      | Hedef yönetimi yazılımı                 | Ticari         | Ürün landing                                  | Demo                |
| P1      | OKR araçları                            | Karşılaştırma  | Gerçek araştırma                              | Ürün landing → demo |
| P1      | OKR programı                            | Ticari         | OKR yazılımı ana sayfasının eş hedefi         | Demo                |
| P1      | Çalışan performans yönetimi             | Ticari         | Performans yönetimi ana sayfasının alt hedefi | Demo                |
| P2      | OKR örnekleri                           | Bilgilendirici | Rehber + şablon                               | İç bağlantı → demo  |
| P2      | OKR nedir                               | Bilgilendirici | Rehber                                        | İç bağlantı → demo  |
| P2      | KPI ve OKR farkı                        | Bilgilendirici | Rehber                                        | İç bağlantı → demo  |
| P2      | 360 derece performans değerlendirme     | Karma          | Gerçek modül/çözüm sayfası                    | Demo                |
| P2      | Yapay zeka destekli performans yönetimi | Karma          | Doğrulanmış özellik sayfası                   | Demo                |

**Kural:** Aynı arama niyetine sahip terimler için rakip iki sayfa üretme (keyword cannibalization). Her ana sorgunun **tek bir canonical hedef URL'si** olmalı.

## 3. Önerilen sayfa mimarisi

> Bunlar **önerilen** slug'lardır, mevcut URL'lerin yayında olduğu varsayılmaz. Önce var olan sayfaları tarayıp uygun olanları güncelle. Yeni URL kararı vermeden redirect haritası çıkar.

| Aday URL                       | Amaç                      | Temel sayfa bileşenleri                                                 |
| ------------------------------ | ------------------------- | ----------------------------------------------------------------------- |
| `/tr/okr-yazilimi/`            | Birincil demo landing     | Fayda, ekranlar, OKR yönetimi, entegrasyonlar, güven, SSS, CTA          |
| `/tr/performans-yonetimi/`     | HR ve yönetim alıcıları   | Süreç, modüller, KPI/OKR/360°, örnek iş akışı, CTA                      |
| `/tr/okr-danismanligi/`        | Hizmet dönüşümü           | Kimler için, kapsam, aşamalar, danışman özgeçmişi, görüşme CTA          |
| `/tr/hedef-yonetimi-yazilimi/` | Hedef hizalama            | Kurumsal/ekip/bireysel hedefler, haritalama, takip, CTA                 |
| `/tr/okr-araclari/`            | Karşılaştırmalı araştırma | Tarafsız metodoloji, açık kriterler, kaynaklar, Myliba dahil seçenekler |
| `/tr/okr-nedir/`               | Eğitim                    | Kavram, örnek, hata, SSS, ilgili ürüne iç bağlantı                      |
| `/tr/okr-ornekleri/`           | Eğitim                    | Sektör/birim bazlı özgün örnekler, şablon, ilgili ürüne bağlantı        |

**Mevcut** `/tr/yazilim/` ve diğer ilgili sayfalarla örtüşme varsa **yeni sayfaları hemen açma**: önce içerik matrisi çıkar, gerekirse mevcut sayfayı ana hedef yap veya kontrollü canonical/301 uygula.

### Her ticari landing page için içerik şablonu

1. **Title:** Birincil anahtar kelime + kullanıcı faydası + Myliba (yaklaşık 50–60 karakteri hedefle, zorunlu limit değildir).
2. **H1:** Doğal, net değer önerisi; arama niyetini karşılasın.
3. İlk ekran: Kim için, neyi çözer, **Demo Talep Et** CTA.
4. Gerçek ürün ekran görüntüsü veya açıklayıcı ürün videosu; görsellere alt metin.
5. Kullanım senaryoları: İK yöneticisi, üst yönetim, ekip yöneticisi.
6. Somut ve doğrulanmış yetenekler: OKR, hedef ağacı, raporlama, entegrasyon vb.; doğrulanmayan özelliği iddia etme.
7. Güven: Kullanım izni alınmış müşteri logosu, vaka çalışması, güvenlik açıklamaları, KVKK bağlantısı.
8. İtiraz giderme: Kurulum, geçiş, uygulama desteği, fiyatlandırma yaklaşımı (gerçekse).
9. Net CTA tekrarları; mobilde kısa form / uygun yerleşim.
10. İlgili içerik ve modül sayfalarına anlamlı iç bağlantılar.

## 4. Dönüşüm optimizasyonu (CRO)

**Temel dönüşüm:** Demo talebi başarıyla alındı (`generate_lead` veya açık isimlendirilmiş `demo_request_submitted` olayı). Butona tıklamak tek başına dönüşüm sayılmaz.

- [ ] Her ticari sayfada ilk ekranda ve içerik sonuna yakın demo CTA.
- [ ] Form: ad/soyad, kurumsal e-posta, şirket, görev/rol, çalışan sayısı (gerekli alanları minimumda tut), ihtiyaca dair isteğe bağlı not.
- [ ] Form için KVKK aydınlatma metni; gerekli açık rıza ayrımını hukuk ekibiyle netleştir.
- [ ] Spam/kalite kontrolü, başarı/hata mesajları, mobil erişilebilirlik.
- [ ] Başarı ekranı/olayı; event yalnız gerçek başarılı backend yanıtından sonra tetiklensin; çift event önle.
- [ ] GA4 + (varsa) Google Tag Manager + Search Console ölçüm altyapısını kontrol et.
- [ ] UTM ve sayfa kaynak bilgisini satış CRM'ine uygun biçimde aktar; kişisel veriyi GA4'e gönderme.
- [ ] Kurumsal nitelik: şirket büyüklüğü, rol ve ihtiyaç CRM'de opsiyonel puanlansın; kişisel e-posta kullanıcılarını otomatik engelleme.
- [ ] CTA varyantlarını mevcut trafik yeterli olduğunda test et.

### Ölçüm tablosu (haftalık)

`Hafta | Organic sessions | Ticari landing organic sessions | Demo başarı sayısı | Organic demo CVR | Nitelikli demo | SQL/satış fırsatı | Search Console clicks | Impressions | Önemli sorgularda konum | Notlar`

**Organik demo CVR = organikten gelen başarılı demo talepleri / organik oturumlar × 100.** GA4 attribution tanımını rapor öncesinde sabitle.

## 5. AI Overview / GEO için içerik standardı

- [ ] 'OKR araçları' ve 'en iyi OKR yazılımları' gibi araştırma niyetlerini karşılayan **tarafsız**, güncel ve kaynaklı karşılaştırma yayımla.
- [ ] Değerlendirme kriterleri açıklansın: OKR hiyerarşisi, entegrasyon, Türkçe destek, yönetici raporu, onboarding, fiyat şeffaflığı; yalnız **doğrulanmış** özellikleri işaretle.
- [ ] Yazar/uzman profili, yayımlanma-güncellenme tarihi, metodoloji ve ilgili güvenilir bağlantılar bulunsun.
- [ ] Özgün ekran görüntüleri, metodoloji materyalleri, vaka incelemeleri, saha verileri, örnek şablonlar kullan.
- [ ] FAQ blokları ancak gerçekten soruları yanıtlıyorsa ekle; `FAQPage` işaretlemesi Google'da özel rich result garantilemez.
- [ ] `Organization`, uygun yerlerde `SoftwareApplication`, `Service`, `BreadcrumbList` structured data kullan; JSON-LD yalnız görünen içerikle tutarlı olsun.
- [ ] İçerikler botlar için render edilebilir ve taranabilir olsun; önemli içerik istemci JS'ine bağımlı kalmasın.
- [ ] AI yanıtlarında görünme denemeleri için sabit bir sorgu paneli oluştur (örnek: 'Türkiye'de OKR yazılımları', 'Türkçe OKR yazılımı', 'kurumsal performans yönetimi yazılımı'). Tarih, platform, kaynak gösterimi, marka mention'ı ve kullanılan kaynakları kaydet. Bu sonuçlar kişiselleşebilir/değişebilir.
- [ ] `llms.txt` istenirse bilgilendirici yardımcı dosya olarak değerlendir; **Google sıralaması için gerekli veya kanıtlı hızlandırıcı değildir**.

## 6. Teknik SEO kontrol listesi (WordPress, performans öncelikli)

- [ ] Staging + yedek + rollback planı kur.
- [ ] WordPress tema, child theme, eklenti ve özel SEO kodlarının envanterini çıkar.
- [ ] `robots.txt`, `meta robots`, `X-Robots-Tag` ve giriş/kayıt/arama sayfaları için indeks politikasını denetle.
- [ ] Sitemap'ın canonical indexlenebilir sayfalarla tutarlılığını sağla.
- [ ] Her dilde self-canonical, dil alternatifleri varsa karşılıklı `hreflang` (TR/EN) ve uygun `x-default` politikasını test et.
- [ ] Başlık/h1/meta description ve Open Graph'ın sayfa bazlı üretildiğini, duplike olmadığını doğrula.
- [ ] 301 zincirleri ve eski slug'ları denetle; **alakalı hedef bulunmadıkça ana sayfaya 301 yapma**.
- [ ] URL Inspection ile kritik landing'lerin render, crawl ve indekslenme durumunu doğrula.
- [ ] Mobil Core Web Vitals: LCP, INP, CLS; font ve görsel optimizasyonu, gereksiz script kaldırma, cache. Önce baseline al.
- [ ] JSON-LD doğrula, hatalı/uydurma review ve aggregateRating kullanma.
- [ ] Analitik ve cookie/consent ayarları ile demo event'lerini test et.

## 7. İlk 12 hafta: sprint backlog

| Sprint | Hafta | Öncelik | Teslimat                                                        | Done kriteri                                             |
| ------ | ----- | ------- | --------------------------------------------------------------- | -------------------------------------------------------- |
| S01    | 1–2   | P0      | Coverage URL envanteri, GSC Queries/Pages analizi, teknik audit | P0 URL sorunlarının nedeni ve çözümü belgeli             |
| S02    | 3–4   | P0      | OKR yazılımı ana landing (mevcut/yeni kararından sonra)         | Responsive, indexlenebilir, gerçek özellikler, demo CTA  |
| S03    | 4–5   | P0      | Performans yönetimi ve danışmanlık sayfaları                    | İçerik farklılaşmış, CTA ve schema test edilmiş          |
| S04    | 5–6   | P1      | Hedef yönetimi, iç bağlantı, breadcrumbs                        | Tek ana hedef URL/niyet matrisi geçerli                  |
| S05    | 6–8   | P1      | OKR araçları karşılaştırması + OKR nedir/örnekleri              | Kaynaklı, özgün ve ürünlere bağlanan içerik              |
| S06    | 8–9   | P0      | Demo event, GA4 raporlama, CRM kaynak aktarımı                  | Gerçek test demo, doğru event ve attribution             |
| S07    | 10–11 | P1      | Müşteri başarı hikâyesi / metodoloji / sektörel dağıtım         | Doğrulanmış somut kanıtlar                               |
| S08    | 12    | P0      | Performans ve indeksleme değerlendirmesi                        | GSC ve GA4 baz çizgisiyle karşılaştırma, sonraki backlog |

> Sıralama bağımlılıklara göre ayarlanabilir: ölçüm altyapısını landing sayfalarından **önce** kurmak daha iyiyse S06'nın P0 bölümlerini S01'e çek.

## 8. AI coding agent için görev kartları

### TASK-SEO-001 — Salt okunur mevcut site denetimi

**Amaç:** Kod/tema/eklenti değiştirmeden SEO mimarisini çıkarmak.

**Görev:** WordPress kod tabanı ve varsa staging URL'lerini analiz et. Sitemap, robots, canonical, title/meta, schema, hreflang, mevcut TR URL'leri, redirect yönetimi, GSC raporuyla uyuşan sorunları ve mevcut demo form akışını tespit et. Kod değişikliği yapma.

**Teslim:** `docs/seo/audit.md`, `docs/seo/url-inventory.csv`, `docs/seo/keyword-url-map.md`, `docs/seo/implementation-backlog.md`.

**Done:** Kanıt/URL veya dosya yolu belirtilmiş; tespit ile varsayımlar ayrılmış; yeni URL önerileri mevcut içerik ve redirect'lerle çakışmıyor.

### TASK-SEO-002 — Teknik düzeltmeler

**Bağımlılık:** TASK-SEO-001 tamam + insan onayı.

**Görev:** Yalnız onaylanan critical indeksleme sorunlarını ve doğrulanmış canonical/sitemap/robots hatalarını küçük, bağımsız commit'lerle gider; mevcut SEO meta üretim yöntemini bozmadan uygula; eklenti eklemeden önce nedenini açıkla.

**Done:** 200/canonical/index/sitemap kontrolü, redirect döngüsü testi, staging doğrulaması, rollback notu.

### TASK-SEO-003 — OKR yazılımı landing

**Bağımlılık:** URL eşleme ve mevcut sayfa kararının onayı.

**Görev:** Myliba'nın doğrulanmış ürün yeteneklerine uygun, Türkçe, kurumsal demo odaklı sayfa üret/güncelle. Mevcut tema tasarım sistemi ve blok yapısına uy; SEO title/H1/meta, ürün görselleri, iç bağlantı, uygun JSON-LD ve CTA ekle.

**Done:** Mobil/masaüstü QA; yanlış vaat yok; tek H1; canonical doğru; CTA gerçek demo formuna çalışıyor; CMS üzerinden içerik düzenlenebilir.

### TASK-SEO-004 — Demo ölçümü

**Görev:** Başarılı form gönderimi için tekil GA4 event'i oluştur; source/medium/landing page takibini test et; PII gönderme; dönüşüm raporu ve test prosedürü yaz.

**Done:** Test lead backend'de var; GA4 DebugView'da event tek kez var; test dokümanı mevcut.

### TASK-SEO-005 — GEO karşılaştırma sayfası

**Görev:** Rakipleri objektif kriterlerle karşılaştıran ve kaynak tarihi gösteren içerik şablonu hazırla. Özellik/fiyat gibi değişebilir iddiaları kaynaksız yayımlama. Editoryal inceleme olmadan otomatik yayımlama yapma.

**Done:** Metodoloji ve kaynaklar, güncelleme tarihi, düzgün tablo erişilebilirliği, ilgili landing'lere bağlantılar.

## 9. İlk çalıştırılacak master prompt (AI agent'a kopyala)

```text
Myliba WordPress web sitesi için Türkiye kurumsal demo odaklı SEO/GEO iyileştirmesi yapacağız.

Önce docs/seo içindeki planı (MYLIBA_SEO_GEO_IMPLEMENTATION_PLAN.md) oku.
Bu ilk aşamada SADECE TASK-SEO-001'i uygula. Henüz kodu değiştirme,
WordPress ayarlarını veya sitemap/robots/redirect'leri değiştirme.

1) Repo/tema/eklenti ve routing yapısını keşfet.
2) Var olan Türkçe ürün, OKR, danışmanlık, performans ve demo sayfalarını haritala.
3) Title, meta, H1, canonical, schema, hreflang, sitemap, robots, redirect
   ve WP SEO hook'larının nereden yönetildiğini göster.
4) Mevcut sayfalar ile önerilen slug'ların çakışıp çakışmadığını değerlendir.
5) Search Console coverage raporu özeti: 138 indexed, 82 not indexed;
   redirect 21, noindex 12, 404 11, other 4xx 1,
   discovered-not-indexed 21, crawled-not-indexed 16.
   Not: URL bazlı export henüz yok; URL listesi olmadan spesifik sebep uydurma.
6) SEO/CRO açısından en yüksek etkiye sahip P0/P1 işleri sıralandır.
7) docs/seo/audit.md, url-inventory.csv, keyword-url-map.md
   ve implementation-backlog.md dosyalarını oluştur.

Kurallar: Mevcut teknoloji ve tasarım bütünlüğünü koru, gereksiz SEO plugin'i
kurma, veri/kanıt bulunmayan iddia üretme, staging-first yaklaşımını kullan.
Çıktı sonunda bulgular, riskler, uygulanacak dosyalar, bağımlılıklar,
önerilen sıradaki değişiklik ve kabul kriterlerini göster.
Kod değişikliği için benden onay bekle.
```

## 10. Uygulama öncesi gerekli veri / erişimler

- **Zorunlu (1. aşama için):** WordPress proje kodu/tema; Search Console coverage summary (sağlandı).
- **Yüksek önem:** GSC > Search Results > Queries + Pages, ülke Türkiye, son 3 ay; Coverage nedenlerine göre **URL export**.
- **Dönüşüm:** GA4 erişimi veya export; demo formunun başarı durumu / CRM lead tanımı.
- **İçerik:** Gerçek ürün özellikleri, müşteri izni bulunan örnekler, danışman özgeçmişleri, ekran görüntüleri, mevcut iletişim/demoya yönlendirme yolu.
- **Teknik:** Staging erişimi, deploy/rollback yöntemi, site haritası URL'si, mevcut redirect listesi.

## 11. Haftalık karar rutini

- **Her pazartesi:** GSC son 28 gün ile önceki 28 günü kıyasla; özellikle ticari sayfalara bak.
- **Her hafta:** Başarılı organik demo ve nitelikli demo sayısını kontrol et.
- **Her 2 haftada:** En çok gösterim alıp düşük CTR üreten sorguların title/snippet test hipotezlerini gözden geçir.
- **Her ay:** GEO sorgu panelini sabit koşullarla tekrar et; AI kaynak gösterimini kaydet.
- **Her sprint sonunda:** Yapılan değişikliklerin URL, tarih, beklenen etki ve ölçüm notunu kaydet.

## 12. Şimdi yapılacak ilk 5 işlem

1. [ ] Bu dosyayı repoda `docs/seo/MYLIBA_SEO_GEO_IMPLEMENTATION_PLAN.md` altına koy.
2. [ ] Yukarıdaki master prompt'u AI coding agent'a ver; salt okunur audit yaptır.
3. [ ] Search Console'dan **Queries, Pages ve indekslenmeme nedeni bazlı URL'leri** dışa aktar.
4. [ ] Audit sonucuna bakıp `OKR yazılımı` hedefinde **mevcut sayfayı geliştirme vs yeni landing** kararını ver.
5. [ ] Demo formundaki başarılı gönderimi ve GA4 event altyapısını teyit et; eksikse ilk sprintte düzelt.

---

**İlk yayınlama kriteri:** Onaylı, çakışmayan hedef URL; gerçek ürün bilgisini anlatan içerik; doğru canonical/index/sitemap; çalışan demo CTA; ölçülebilir başarılı demo eventi; mobil QA.  
**Asıl kazanım:** Google'da herhangi bir sorguda sırf görünmek değil, Myliba için **nitelikli kurumsal satış fırsatı** üretmek.
