# Myliba OKR içerik paketi

Bu paket dört tam yazı, gerçek müşteri bilgileri bekleyen bir vaka taslağı ve iki biçimde seçim kontrol listesi içerir. Dosyalar yerel editoryal taslaktır; WordPress’e aktarılmadı veya yayımlanmadı.

## Yazılar

| Dosya | Amaç ve hedef okur | Durum |
| --- | --- | --- |
| [En iyi OKR yazılımları](01-en-iyi-okr-yazilimlari.md) | Yazılım kısa listesi hazırlayan İK, strateji ve dönüşüm lideri | Tam metin; editoryal incelemeye hazır |
| [Excel mi, OKR yazılımı mı?](02-excel-mi-okr-yazilimi-mi.md) | Dosyalarla takipten sisteme geçmeyi değerlendiren süreç sahibi | Tam metin; editoryal incelemeye hazır |
| [OKR ve KPI aynı sistemde](03-okr-ve-kpi-ayni-sistemde.md) | Ölçüm düzeni kuran ekip lideri ve operasyon yöneticisi | Tam metin; editoryal incelemeye hazır |
| [Seçim için 12 soru](04-okr-yazilimi-secim-sorulari.md) | Demoları ve teklifleri karşılaştıran satın alma ekibi | Tam metin; indirme bağlantısı yayın sırasında bağlanacak |
| [Müşteri hikâyesi](05-musteri-hikayesi-dogrulama-bekliyor.md) | Benzer kurum deneyimi arayan karar verici | Gerçek vaka verisi gelmeden yayımlanmayacak |

## İndirilebilir dosyalar

- [Doldurulabilir ve yazdırılabilir HTML kontrol listesi](downloads/myliba-okr-yazilimi-secim-kontrol-listesi.html)
- [CSV kontrol listesi](downloads/myliba-okr-yazilimi-secim-kontrol-listesi.csv)

HTML formunun yanıtları tarayıcıda kalıcı saklanmaz. Form üzerindeki yazdırma düğmesiyle doldurulmuş hâli yazdırılabilir veya PDF olarak saklanabilir. CSV, formun boş ve ayrı bir çalışma kopyasıdır; HTML’de yazılan yanıtları dışa aktarmaz. CSV Türkçe karakterler için UTF-8 BOM ile kaydedildi.

## Anlatım yaklaşımı

Yazılar kısa bir iş hikâyesiyle açılır, ardından teknik açıklama, örnek tablo ve karar adımlarına geçer. Deniz ve Pusula Teknoloji kurgusaldır; gerçek müşteri veya kullanıcı araştırması değildir. İkinci ve üçüncü yazıda aynı ilk değer senaryosu kullanılarak tutarlılık sağlandı. Örnek rakamlar vaat, müşteri sonucu veya ürünün varsayılan davranışı olarak sunulmadı.

Karşılaştırma Myliba’nın yayıncı rolünü açıklar. Ürün sırası puan sıralaması değildir. Uygulamalı test yapılmadı; resmî sayfalar esas alındı. Doğrulanamayan Türkçe arayüz/destek bilgisi eksik özellik olarak işaretlenmedi. Myliba’nın genel pazarlama sayfalarındaki sayısal kazanç iddiaları müşteri kanıtı yerine kullanılmadı.

## SEO ve AI görünürlüğü için hazırlanan alanlar

Her yazının başındaki YAML bölümünde başlık, SEO başlığı, açıklama, özet, ana konu ve önerilen URL bulunur. YAML bir WordPress içe aktarma dosyası değildir; gövdeye yapıştırılmamalıdır. Yazıların başında doğrudan yanıt, açıklayıcı alt başlıklar, metin olarak okunabilen tablolar, kaynak bağlantıları ve görünür sorular bulunur. İçerikler farklı arama niyetlerine ayrılmıştır: kısa liste, araç değiştirme, uygulama yöntemi, seçim ve müşteri kanıtı.

Bu düzen AI yanıtlarında yer alma garantisi vermez. Google, AI arama özellikleri için temel SEO uygulamalarının geçerli olduğunu, özel bir AI dosyası veya özel schema gerekmediğini açıklıyor. [Google AI özellikleri rehberi](https://developers.google.com/search/docs/appearance/ai-features)

Karşılaştırma yazısında gerçekten test edilen noktalar varsa sonradan, test tarihi ve kanıtıyla eklenmeli. Bağımsız değerlendirme veya denenmiş ürün iddiası eklenmemeli. [Google inceleme rehberi](https://developers.google.com/search/docs/specialty/ecommerce/write-high-quality-reviews)

## WordPress’e aktarırken

1. İlk dört yazıyı `post` türünde taslak olarak oluşturun. Projedeki mevcut Türkçe yazı yapısı `/tr/yazilar/{slug}/` şeklindedir. Dosyalardaki `proposed_url` alanları öneridir, canlı sayfa değildir.
2. SEO başlığını `_myliba_seo_title`, açıklamayı `_myliba_seo_description` alanına; özeti yazı özetine taşıyın. Aktif SEO eklentisi farklı alan kullanıyorsa eklentinin alanlarını esas alın.
3. Gerçek yazar ve varsa gerçekten inceleyen uzmanı atayın. Araştırma tarihi, yayın tarihi ve son değişiklik tarihini ayırın; araştırma tarihini otomatik yayın tarihi yapmayın.
4. Yazı gövdesindeki H1’i temanın ürettiği başlıkla yinelemeyin. Karşılaştırma ve soru tablolarının mobilde okunmasını kontrol edin.
5. HTML ve CSV indirmelerini birlikte aynı medya klasörüne yükleyin veya birinci taraf statik indirme konumunda sunun. WordPress HTML yüklemeyi kabul etmiyorsa statik dosya sunumu kullanın ya da kontrol listesini PDF’ye dönüştürün; genel dosya yükleme güvenliğini değiştirmeyin. Dördüncü yazıdaki `/wp-content/uploads/myliba-okr-yazilimi-secim-kontrol-listesi.html` bağlantısı hedef taslağıdır; gerçek konumla değiştirin. HTML içindeki CSV bağlantısını aynı konuma göre eşleyin.
6. Yazılar arası bağlantılar önerilen canlı URL’leri kullanır. Bunları ilgili yazılar yayına alındığında etkinleştirin; henüz yayımlanmayan hedeflere bağlantı bırakmayın. Yerel dosya adları yukarıdaki tabloda eşlenmiştir.
7. Gerçek ürün ekranı eklenecekse ikinci yazıda hedef haritası, üçüncüde KPI ve hedef ilişkisini anlatan açıklamalı görüntü kullanın. Görüntünün göstermediği bir özelliği alt metinde iddia etmeyin. Editör yer tutucuları ilk dört yazının gövdesine eklenmedi.
8. Tema/SEO katmanındaki mevcut Article ve breadcrumb üretimini kullanın; aynı şemayı ikinci kez eklemeyin. Sahte puan veya Review/AggregateRating verisi eklemeyin. Görünür SSS bölümleri için aramada özel görünüm vaat etmeyin.
9. Yayından sonra URL, canonical, indekslenebilirlik, sitemap, kaynak ve indirme bağlantılarını kontrol edin. Canlı teknik SEO denetimi bu yazım paketinin kapsamında yapılmadı.

Müşteri vakası istenirse mevcut `myliba_case_study` türüne de alınabilir; o durumda `/tr/vaka-calismalari/{slug}/` yolunu kullanın. İsim, olay, kapsam, sonuç, alıntı ve izinler doğrulanmadan dosyayı yayımlamayın.

## Araştırmada kullanılan ürün kaynakları

Kontrol tarihi: 21 Ekim 2026. Kaynaklar uygulama üzerinde bağımsız test anlamına gelmez. Fiyat ve paket sayfaları yayından önce yeniden kontrol edilmeli.

- [Myliba ürün sayfası](https://www.myliba.com/tr/yazilim/)
- [Myliba SSS](https://www.myliba.com/tr/sss/)
- [Twiser ürün kapsamı](https://twiser.com/tr/ana-sayfa/) ve [planlar](https://twiser.com/tr/planlar/)
- [Perdoo ürün sayfası](https://www.perdoo.com/) ve [fiyatlandırma](https://www.perdoo.com/pricing)
- [Weekdone ürün sayfası](https://weekdone.com/) ve [fiyatlandırma](https://weekdone.com/prices)
- [Profit.co paket ve fiyatlandırma kapsamı](https://www.profit.co/pricing)

## Ölçüm önerisi

İlk dört yazıyı ayrı ayrı izleyin: Search Console’da ilgili sorgular ve organik tıklamalar; analitikte kontrol listesi indirme, ürün sayfasına geçiş ve nitelikli demo talebi. AI kaynaklı yönlendirmeleri göründüğü ölçüde ayrı inceleyin. Sabit bir soru kümesiyle AI yanıtlarını tarih ve kaynaklarıyla örneklemek yardımcı olabilir; tek bir yanıtı kalıcı görünürlük veya trafik ölçümü saymayın.
