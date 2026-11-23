# Uygulama kaydı

## 23 Kasım 2026 — Kök adres yönlendirmesi ve eski adresler (yerel; canlıya alınmadı)

Kullanıcı `audit-2026-11-23.md` A3 ve A6 için öneriyi onayladı.

**Kök adres (`themes/myliba/functions.php::myliba_redirect_root_to_preferred_locale`).** noindex + meta-refresh'li dil seçici sayfası kaldırıldı. Yeni davranış: dil çerezi varsa o dile 302 (değişmedi); çerez yoksa ve `Accept-Language` başlığı yoksa ya da Türkçe içeriyorsa `/tr/` adresine **301**; başlık başka bir dil istiyorsa `/en/` adresine 302. Yanıt `nocache` başlıkları ve `Vary: Accept-Language, Cookie` taşır; aksi halde tarayıcı 301'i önbelleğe alıp sonraki dil seçimini yok sayar.

**hreflang (`seo.php::render_hreflang`).** Dil ana sayfalarında `x-default` artık köke değil, iç sayfalarla aynı kurala (`default_locale`, şu an `en`) göre dil ana sayfasına işaret ediyor. Denetimdeki "x-default → /tr/" önerisi uygulanmadı: `x-default` yalnız Türkçe ve İngilizce dışındaki dillerde arayanlar için yedektir; onlar için İngilizce sayfa daha uygun ve iç sayfalarla tutarlı. Türkiye aramalarını etkilemez.

**Eski adresler (`seo.php::handle_legacy_urls`, `template_redirect` öncelik 1, yalnız 404 durumunda).** 12 yol için 301: `/tr/hakkimizda` → `/tr/hikayemiz/`; `/tr/kvkk-aydinlatma`, `/tr/myliba-privacy-policy` → `/tr/gizlilik-politikasi/`; `/privacy-policy-webinar` → `/en/privacy-policy/`; `/myliba-user-agreement` → `/en/terms-of-use/`; `/etik-hat-ihlali-bildirimi` → `/tr/etik-danismanlik/`; `/iletisim` → `/tr/iletisim/`; `/okr-academy-contact-form` → `/en/okr-culture-academy/`; `/our-products` → `/en/software/`; `/category/blog` → `/en/blog/`; `/tr/category/blog` (sayfalama dahil) → `/tr/yazilar/`; `/tr/urunlerimiz` → `/tr/yazilim/`. Eski etkinlik yolları (`/event/…`, `/tr/event/…`, `/events/…`) için **410**. İngilizce slug'lar denetimdeki taslaktan farklı olarak İngilizce karşılıklara yönlendirildi. `/en/blog/practical-okr-operating-rhythm/`, feed ve alt alan adı kayıtları kapsam dışı.

Yerel doğrulama (tarayıcıdan): kök `tr` başlığıyla `/tr/`, `en` ve `de` başlığıyla `/en/` sonucuna ulaşıyor; 10 eski adres beklenen hedefe ulaşıyor; üç etkinlik yolu 410; rastgele olmayan adres 404; `/tr/etkinlikler/`, `/en/events/`, `/tr/yazilim/`, `/tr/iletisim/` 200. TR ana sayfa hreflang: `en>/en/ tr>/tr/ x-default>/en/`, canonical `/tr/`. İki dosya PHP 8.3 sözdizimi kontrolünden geçti; eşleme işlevleri ayrı denendi.

Doğrulanmayan: yönlendirme durum kodları (tarayıcı `fetch` 301/302 ayrımını göstermez; kod incelemesine dayanıyor), `Accept-Language` göndermeyen istemci (Googlebot) davranışı, canlıda yönlendirme zinciri (`myliba.com` → `www` → `/tr/` iki adım olacak). Canlıya alındıktan sonra harici bir istekle teyit edilmeli. Sitemap, canonical ve noindex mantığına dokunulmadı; kök zaten sitemap dışında.

Geri alma: iki dosyadaki bu değişiklikleri önceki commit'e döndürmek. 301'ler tarayıcıda önbelleğe alınmıyor; arama motorları birkaç günde eski duruma döner.

## 23 Kasım 2026 — SEO paketi 1 canlıda: yayın sonrası denetim

Kullanıcı `make links-apply` (17 kayıtta 25 bağlantı) ve `make predeploy` (HAZIR; form testi geçti) sonrasında All-in-One ile canlıya aldı. Canlıda sitemap'teki 121 URL'nin tamamı tarandı (16:10 TSİ, kullanıcının tarayıcısından).

| Kontrol | Sonuç |
| --- | --- |
| HTTP 200, yönlendirmesiz | 121 / 121 |
| Tek ve self canonical, tek description, noindex yok | 121 / 121 |
| JSON-LD parse hatası / PHP uyarı çıktısı | 0 / 0 |
| Footer "Öne çıkan konular" (6 bağlantı) | 121 / 121 |
| Sitemap sayfalarından 404'e giden iç bağlantı | 0 (önce `/category/blog/`, `/tr/category/blog/`, `/our-products/`) |
| Para sayfalarına bağlantı veren sayfa sayısı | TR 63, EN 60 (önce 2–4) |
| `/en/solutions/corporate-development-programs/` | `/en/okr-culture-academy/` (önce TR sayfa); EN footer doğrudan bağlanıyor |
| `/tr/yazilim/` video, `/tr/demo/` form | Yerinde |
| WP Mail SMTP | SMTP, `smtp.gmail.com`, sabitten; klon sonrası korunmuş |
| Site Settings | contact_email hello@myliba.com, indexing açık; "arama motorlarını engelle" kapalı |
| HTML ilk bayt (boşta, ardışık) | 0,5–0,7 sn; boşta kalmış ilk istek 2,3 sn |

Kalanlar: çift H1 olan 6 yazı (denetim B7); `okr-hatalari-…` yazısının hero düğmesinde kısa slug'a giden tek bağlantı (şablon/alan kaynaklı, gövde betiği kapsamaz); `/category/blog/` adresinin kendisi hâlâ 404 (301 haritası onay bekliyor). Form Submissions 39 kayda döndü (bugünkü test kayıtları klonla silindi, beklenen).

Search Console dizin istekleri (üçünde de "Indexing requested" onayı): `/tr/icerikler/geri-bildirim-kulturu/`, `/tr/yazilar/okr-ornekleri/`, `/tr/okr-kultur-akademisi/icf-onayli-okr-kultur-koclugu/`. Bugünkü toplam sekiz URL. Denetim ekranı hâlâ "Referring page: None detected" gösteriyor; Google yeni iç bağlantıları henüz taramadı.

Ölçülmeyen: gerçek form gönderimi (klon sonrası), mobil görünüm, Core Web Vitals.

## 23 Kasım 2026 — SEO paketi 1 (yerel): iç bağlantılar, EN yönlendirme, form sınırı

Yalnız yerelde; canlıya alınmadı. Kaynak: `audit-2026-11-23.md` A1 ve A5.

- **Footer "Öne çıkan konular" satırı.** `themes/myliba/functions.php::myliba_footer_topic_links()` geçerli dildeki yayımlanmış ve noindex olmayan `myliba_landing` kayıtlarını ve OKR koçluğu sayfasını listeler; `footer.php` bunu sütunların altında gösterir. Stil `assets/css/src/base/theme-refinements.css`, `node tools/build-css.mjs` ile `dist/base.min.css` yeniden üretildi (yalnız bu paket değişti). Başlık `myliba_text('Featured topics')` üzerinden çevrilir.
- **EN çözüm yönlendirmesi.** `myliba-core/includes/post-types.php::redirect_solution_custom_url()` hedefi kaydın diline çeviriyor; `/en/solutions/corporate-development-programs/` artık `/en/okr-culture-academy/` adresine gidiyor (önce `/tr/okr-kultur-akademisi/`). Yerel panelde "Footer Solutions (EN)" menüsündeki özel bağlantı doğrudan `/en/okr-culture-academy/` yapıldı (veritabanı değişikliği; klonla taşınır).
- **Form deneme sınırı.** `forms.php::rate_limited()` oturum açmış yöneticileri (`manage_options`) muaf tutuyor. Ziyaretçi sınırı (saatte 5) değişmedi.
- **Gövde bağlantıları için betik.** `migrations/2026-11-23-internal-links.php`; `make links-preview` / `make links-apply`. Henüz çalıştırılmadı.

Yerel doğrulama (tarayıcıdan, `localhost:8080`): `/tr/`, `/en/`, `/tr/demo/`, `/tr/iletisim/`, `/tr/icerikler/okr-yazilimi/`, `/en/content/okr-software/`, `/en/blog/what-is-okr/` 200; her birinde tek canonical, tek H1 (ölçülenlerde), footer satırında 6 bağlantı (5 landing + OKR koçluğu), PHP uyarı çıktısı yok. EN footer'da eski çözüm adresine bağlantı kalmadı. Değişen dört PHP dosyası ve betik PHP 8.3 sözdizimi kontrolünden geçti; betiğin dönüştürme işlevi örnek HTML ile denendi (iç/dış bağlantı, sorgu dizgili adres, çapa, idempotentlik).

Doğrulanmayan: form test betiği ve `make predeploy` (Docker'a erişim yok), mobil görünüm, tam site taraması. Yeni canonical/meta/schema üreticisi eklenmedi; staging noindex korumalarına dokunulmadı.

## 23 Kasım 2026 — Canlı e-posta gönderimi wp-config sabitleriyle kuruldu

Kullanıcı Google uygulama şifresi oluşturdu ve `WPMS_*` sabitlerini canlı `public_html/wp-config.php` dosyasına kendisi yazdı (ilk denemede blok ana dizindeki kullanılmayan `~/wp-config.php` dosyasına girilmişti; orada hâlâ duruyor, silinmesi önerildi). Parola görülmedi; dosyada yalnız satırların varlığı ve biçimi denetlendi (10/10, 82–91. satırlar).

Doğrulama: WP Mail SMTP ekranında yöntem SMTP, `smtp.gmail.com:587` TLS, kullanıcı ve gönderen okan.toper@myliba.com; alanlar sabitten kilitli, "gönderici kurulu değil" uyarısı kalktı. `/tr/`, `/tr/demo/`, `/tr/yazilim/` 200. Kullanıcı onayıyla okan.toper@myliba.com adresine tek test e-postası gönderildi; eklenti "Test email sent successfully" yanıtı verdi. Bu, SMTP sunucusunun iletiyi kabul ettiğini gösterir; gelen kutusuna teslim ve gerçek form bildirimi (hello@myliba.com) kullanıcı tarafından teyit edilecek.

Ayar `wp-config.php` içinde olduğu için All-in-One klonlarında silinmez. Yerel akış `docs/deployment.md` içinde; export öncesi `make predeploy`.

## 23 Kasım 2026 — Yayın yöntemi: kod ve veri ayrıldı (kurulum sürüyor)

Kullanıcı isteğiyle All-in-One import yerine geçecek düzen hazırlandı; ayrıntı `docs/deployment.md`. Eklenen dosyalar: `tools/deploy/deploy.yml` (GitHub Actions; `.github/workflows/` altına kopyalanacak), `tools/server/myliba-db-dump.php`, `wordpress/scripts/pull-live.sh`, `myliba-core/includes/local-dev.php`; `Makefile`, `.env.example`, `.gitignore`, `myliba-core.php` güncellendi.

Doğrulanan: yeni PHP dosyaları ve `myliba-core.php` için PHP 8.3 sözdizimi kontrolü geçti; döküm betiği sahte `wp-config.php` ve sahte `mysqldump` ile çalıştırılıp çıktı/meta dosyası üretildi; `pull-live.sh` için `bash -n` geçti. Sunucuda `~/myliba-sync/` (0700) oluşturuldu ve döküm betiği yüklendi.

Doğrulanmayan / bekleyen: zamanlanmış görev eklenmedi (kullanıcı onayı bekliyor), sunucuda gerçek döküm alınmadı, FTP hesabı ve GitHub Secrets yok, iş akışı hiç çalışmadı, `make pull-live` yerel Docker'da denenmedi. `local-dev.php` yalnız `local` ortam türünde çalışır; canlıda davranış değişikliği ve SEO etkisi yok.

## 23 Kasım 2026 — Canlı form çökmesi: korumalı forms.php yeniden yüklendi

Kullanıcı canlıda form gönderiminin hata verdiğini bildirdi. Canlı panelde 14:47 tarihli yeni kayıt görüldü (39 → 40): talep kaydedilmiş, hata kayıttan sonraki bildirim adımında. Canlıdaki `includes/forms.php` (14.709 bayt, sha256 `a8d8bf9f…`) 22 Ekim'deki Throwable korumasını ve kayıt kontrolünü içermiyordu. Neden: canlı dosyalar yerele alınırken korumasız sürüm çalışma klasörünün üzerine yazılmış ve `db32f9dd` commit'i ile kayda geçip yayımlanmış. Hata ekranı doğrudan görülmedi; teşhis kayıt saati, kod ve 28 Ağustos/22 Ekim'deki aynı hata kaydına dayanıyor.

Yapılan (kullanıcı onayıyla): yerelde `includes/forms.php` ve `tests/forms.php` `60e724b3` sürümüne döndürüldü (commit edilmedi). Canlıda eski dosya web kökü dışına `/home/u0269638/forms.php.bak-20261123-live` olarak yedeklendi; korumalı dosya cPanel Dosya Yöneticisi ile üzerine yüklendi. Canlı dosya geri okunarak doğrulandı: 15.477 bayt, sha256 `6737ca97…`, yerel dosyayla aynı. Sonrasında `/tr/demo/`, `/tr/iletisim/`, `/tr/okr-kultur-akademisi/`, `/en/demo/` 200, form HTML'de, hata çıktısı yok.

Doğrulanmayan: gerçek form gönderimi (kullanıcı test edecek) ve e-posta teslimi. Bu düzeltme çökmeyi önler, e-posta göndermez; canlıda gönderici hâlâ yapılandırılmamış. PHP sözdizimi kontrolü ve test betiği bu turda çalıştırılamadı (Docker'a erişim yok); yüklenen dosya 22 Ekim'de 10 senaryo / 70 kontrolle test edilen sürümle aynıdır. Geri alma: yedek dosyayı aynı konuma kopyalamak. SEO etkisi yok.

## 23 Kasım 2026 — Yayın sonrası kontrol, OPcache ve dizin istekleri

Kullanıcı yazılım sayfası videosunu ve form düzeltmelerini All-in-One WP Migration ile canlıya aldı; ardından www.myliba.com ve Natro paneli üzerinde işlem yapılmasını onayladı. Kod veya içerik değiştirilmedi.

**Yayın sonrası kontrol (canlı, salt okunur).** `/tr/yazilim/` 200, tek H1, tek description, self-canonical, noindex yok; Organization, WebSite, BreadcrumbList, SoftwareApplication, FAQPage JSON-LD parse ediliyor. Video öğesi sayfada, `preload="none"` ve poster ile; `myliba-cark-tr-720.mp4` 200. `robots.txt` engelsiz ve sitemap satırı var; `wp-sitemap.xml` 9 alt liste, page listesi 36 URL. `/tr/`, `/tr/demo/`, `/en/software/` 200 ve indekslenebilir; demo formu HTML'de. cPanel hata günlüğünde kayıt yok.

**OPcache açıldı (Natro cPanel → Select PHP Version, PHP 8.3).** `opcache` uzantısı kapalıydı; işaretlendi, panel "Module opcache saved" yanıtı verdi. Aynı tarayıcıdan `no-store` isteklerle HTML ilk bayt süresi: önce 1,1–1,4 sn, sonra 0,5–0,6 sn (`/tr/`, `/tr/yazilim/`, `/tr/demo/`, `/tr/yazilar/`). Tek konum, tek zaman dilimi. Geri alma: aynı ekranda işareti kaldırmak. Sayfa önbelleği hâlâ yok.

**Search Console dizin istekleri.** "Keşfedildi – dizine eklenmedi" durumundaki beş URL için URL Denetimi'nden istek gönderildi ve "Indexing requested" onayı görüldü: `/tr/icerikler/okr-yazilimi/`, `/tr/icerikler/performans-yonetimi/`, `/tr/icerikler/okr-yonetimi/`, `/tr/icerikler/kpi-ve-okr/`, `/tr/okr-koclugu/`. Denetimde "Referring page: None detected" görüldü; iç bağlantı eksikliği sürüyor. İstek, dizine eklenme garantisi değildir.

**Açık bulgu: canlıda e-posta göndericisi yapılandırılmamış.** WP Mail SMTP 4.10.4 "Henüz bir posta gönderici kurulumunuz yok" uyarısı gösteriyor; seçili yöntem varsayılan PHP mail, Google istemci bilgileri kayıtlı değil. Form kayıtları oluşuyor (Form Submissions: 39 kayıt, sonuncusu 23.11.2026 13:18) fakat bildirim e-postasının teslimi doğrulanmadı. Gizli anahtar girişi ve Google yetkilendirmesi kullanıcıya bırakıldı; test e-postası gönderilmedi.

## 23 Kasım 2026 — Yazılım sayfasına Myliba Çarkı animasyonu (yalnız yerel)

`/tr/yazilim/` ve `/en/software/` sayfalarında "Tek ve sürekli bir çalışma döngüsü" bölümüne, başlık ile adım kartları arasına Myliba Çarkı animasyonu eklendi. Hero'ya konmadı; video LCP öğesi olmuyor.

- Dosyalar temada: `assets/video/myliba-cark-tr-{720,480}.mp4`, `myliba-wheel-en-{720,480}.mp4` (sessiz, H.264, faststart, yaklaşık 0,5–0,8 MB) ve WebP posterler (~40 KB). Kaynak videolar 1080 px ve ~3,6 MB idi.
- HTML'de video `src` yok, `preload="none"`. `main.js` bölüm görünüme yaklaşınca 600 px altında 480, üstünde 720 sürümünü yükleyip sessiz döngüde oynatır, görünümden çıkınca durdurur. `prefers-reduced-motion` ve Save-Data kullanıcılarına yalnız poster gösterilir. Duraklat/oynat düğmesi var (WCAG 2.2.2).
- CLS önlemi: `width/height=720` ve `aspect-ratio: 1/1`.
- Videodaki bilgi HTML'de de var: başlık (h3), açıklama ve KPI / OKR / Taahhütler / Davranışlar listesi. Video `aria-describedby` ile bu metne bağlı. Metinler `myliba_text()` üzerinden geçiyor, TR/EN karşılıkları `content.php` içinde; Myliba → Site Texts'ten düzenlenebilir.
- **Schema eklenmedi.** Animasyon dekoratif/anlatım amaçlı olduğundan VideoObject üretilmiyor; canonical, meta ve schema çıktısı değişmedi (sayfada tek canonical doğrulandı).
- Editör alanı: Yazılım Sayfası İçeriği → Çalışma Döngüsü → `workflow_wheel_hide` (1: gizle).

Doğrulanan: `php -l` (3 dosya), `node --check main.js`, CSS build; localhost'ta ilk yüklemede mp4 isteği olmadığı, kaydırınca oynadığı, 1440 px'de iki sütun, 375 px'de tek sütun/taşma yok ve 480 sürümü, EN sayfasında EN video ve metinler, konsolda hata yok. Canlıya alınmadı, commit yapılmadı.

## 22 Ekim 2026 — Yeni istemci kimliğinin eşleştirilmesi

Kullanıcı istemci oluşturma/WordPress kayıt adımlarını tamamladığını belirtti. Google Cloud'da “OAuth client created” penceresi görüldü; yeni public istemci kimliği `…7lqipo2bdtstvct90qte16g0vrrhqetq.apps.googleusercontent.com`. WordPress'te eski istemci kimliği kaldığı için yalnız public istemci kimliği yeni kayıtla eşleştirildi ve “Ayarlar başarıyla kaydedildi” doğrulandı. Kullanıcının girdiği gizli anahtar değiştirilmedi veya çıktıya alınmadı.

Yeni istemciyle yetkilendirme başlatıldı ve okan.toper@myliba.com seçildi. Google, wpmailsmtp.com için aynı geniş Gmail okuma/oluşturma/gönderme/kalıcı silme kapsamını yeniden soruyor. Yeni istemcinin Allow adımı henüz onaylanmadı; bağlantı henüz doğrulanmadı. Son Google onayı ve hello@myliba.com'a tek test bildirimi gönderimi için kullanıcı onayı bekleniyor.

## 22 Ekim 2026 — Ayrı WordPress OAuth istemcisi için hazırlık

Kullanıcı etkin anahtarın kayıtlı olmadığını belirtti. Aynı MyLiba projesinde `Myliba WordPress Mail` adlı Web application istemcisinin oluşturma formu hazırlandı; Authorized redirect URI `https://connect.wpmailsmtp.com/google/`, JavaScript origin alanı boş. Create düğmesi etkin ve henüz tıklanmadı. Mevcut istemci/anahtarlar silinmedi, kapatılmadı veya yeniden etkinleştirilmedi.

Yeni kimlik bilgisi oluşturma ve WordPress'e giriş adımları için kullanıcıya kontrol devredildi. Bu adım yeni istemci oluşturulmuş, yeni gizli anahtar kaydedilmiş veya e-posta teslimi doğrulanmış anlamına gelmez. Yeni istemciyle Google yetkilendirmesi ve onaylı alıcıya teslim testi daha sonra tamamlanmalı.

## 22 Ekim 2026 — invalid_client nedeninin doğrulanması

Google Cloud MyLiba projesindeki mevcut MylibaWebSite istemcisi incelendi. İstemci kimliği WordPress ile eşleşiyor ve izinli redirect URI `https://connect.wpmailsmtp.com/google/`. 8 Ağustos 2026 tarihli maskelenmiş `…4_M9` anahtarının durumu Disabled; 10 Eylül 2026 tarihli `…QyqN` anahtarının durumu Enabled. Yerel anahtarın yalnız suffix eşleşmesi kontrol edilerek devre dışı eski anahtar olduğu doğrulandı. Bu, aktarılan eski anahtarla alınan invalid_client hatasını açıklıyor.

Google mevcut gizli anahtarların tam değerini görüntüleme/indirmeye artık izin vermiyor. Etkin anahtar bu ekrandan alınamadı. Hiçbir anahtar etkinleştirilmedi, kapatılmadı, silinmedi veya yenilenmedi. Kullanıcıda etkin anahtarın güvenli kaydı olup olmadığı bekleniyor; yoksa kimlik bilgisi yenileme adımı kullanıcıyla planlanmalı. Yeni uygulama gerekmiyor; bağlantı ve teslim testi henüz tamamlanmadı.

## 22 Ekim 2026 — Google onayı sonrası invalid_client

Kullanıcı ekranda açıklanan geniş Gmail erişimini açıkça onayladı; okan.toper@myliba.com hesabının Google izin ekranında Allow tıklandı. WordPress dönüşünde Gmail `invalid_client` hatası gösterdi. Bu yüzden bağlantı başarılı sayılmadı, e-posta gönderilmedi.

Mevcut local gizli anahtarın Google biçiminde olduğu ve çevresinde boşluk/localhost metni bulunmadığı, değer gösterilmeden kontrol edildi. Bu biçim kontrolleri anahtarın Google tarafından hâlâ geçerli kabul edildiğini kanıtlamaz. Yeni anahtar oluşturulmadı veya mevcut anahtar sıfırlanmadı.

Mevcut istemciyi Google Cloud'da incelemek için konsol açıldı; kuruluş hesabı “Verify it’s you” ekranında yeniden parola doğrulaması istiyor. Kullanıcının açık tuttuğu diğer Google Cloud sayfası tanıtım/free-tier sayfası; konsola yetkili erişim doğrulanmadı. Kullanıcının yeni açılan konsol girişinde kimliğini doğrulaması bekleniyor. Canlı Gmail seçimi ve istemci bilgileri kayıtlı, fakat OAuth bağlantısı tamamlanmış değil. Form bildirimlerinin teslimi henüz doğrulanmadı.

## 22 Ekim 2026 — Mevcut OAuth istemcisinin canlıya aktarılması

Kullanıcı mevcut istemci kimliği ve gizli anahtarının localden www.myliba.com'a aktarılmasını açıkça onayladı. İlk geçici dosya tasarımı web kökünde saklama riski nedeniyle otomatik güvenlik denetimince reddedildi ve çalıştırılmadı. Güvenli alternatifte web kökü dışında 0700 geçici klasör ve 0600 dosya kullanıldı; aktarımın finally bloğunda klasör/dosya silindi ve silinmiş olduğu doğrulandı. Gizli değerler tool çıktısına veya rapora yazılmadı. Eski access/refresh tokenlar taşınmadı.

Canlı WP Mail SMTP Google/Gmail seçimi ve mevcut istemci bilgileri kaydedildi; arayüzde “Ayarlar başarıyla kaydedildi” görüldü. okan.toper@myliba.com hesabı seçilince Google izin ekranı açıldı. Ekran wpmailsmtp.com için Gmail e-postalarını okuma, oluşturma, gönderme ve kalıcı silme yetkisi istiyor. Bu geniş kapsam için **Allow tıklanmadı**; kullanıcının bu kapsamı ayrıca onaylaması bekleniyor. Yeni Google uygulaması oluşturulmadı. Hesap bağlantısı ve test e-postası henüz tamamlanmadı; yalnız istemci aktarımının başarıyla kaydedilmesi teslim testi değildir.

## 22 Ekim 2026 — Önceden kurulmuş yerel bağlantının bulunması

Kullanıcı daha önce SMTP kurulumunun yapıldığını belirtti. Yerel WordPress ayarları gizli değerler çıktıya alınmadan incelendi: gönderici `gmail`, istemci kimliği/gizli anahtarı ve access/refresh token alanları mevcut. SMTP host/kullanıcı/parola alanları boş. Eklentinin kendi Options API'sindeki kullanıcı adresi `okan.toper@localhost`; bu nedenle mevcut yetkilendirme kaydının canlıdaki onaylı hesabı doğrudan doğruladığı kabul edilmedi.

Canlı arayüzde seçili yöntem PHP mail olmaya devam ediyor. Kaydetmeden açılarak bakılan Google istemci alanları ve SMTP host alanı boş; SMTP auth kapalı. Yeni uygulama oluşturulmadı veya mevcut tokenlar taşınmadı. İlk salt okunur incelemedeki “kurulum yok” sonucu canlı ortamla sınırlıdır; yerelde eski bağlantı bulunmaktadır. Bağlantının ne zaman/nasıl canlıdan ayrıştığı henüz bilinmiyor.

Öneri: yeni Google uygulaması oluşturmadan önce mevcut yerel uygulamanın istemci bilgilerini canlı WordPress'te yeniden kullanmak; access/refresh token kopyalamadan onaylı hesapla yeni yetkilendirme yapmak. Gizli anahtarın localden canlı www.myliba.com sunucusuna aktarılması için açık onay bekleniyor. Canlı gönderim yöntemi değiştirilmedi ve test e-postası gönderilmedi.

## 22 Ekim 2026 — Canlı gönderici/alıcı adresleri ve Google bağlantısı hazırlığı

Kullanıcı hello@myliba.com'un grup adresi olduğunu açıkladı ve gönderen için okan.toper@myliba.com hesabını onayladı. Canlı WP Mail SMTP'de From Email okan.toper@myliba.com, From Name Myliba olarak kaydedildi; başarı bildirimi görüldü. Myliba Site Settings içindeki Contact recipient hello@myliba.com olarak kaydedildi; kayıt sonrası ekranda doğrulandı. İndeksleme seçimi açık kaldı; locale ve diğer site ayarları için değişiklik amaçlanmadı.

Google/Gmail bağlantısı için mevcut Lite arayüzünde istemci kimliği/gizli anahtarı ve OAuth izni gerekiyor. Google Cloud konsolu okan.toper@myliba.com hesabı için yeniden kimlik doğrulama istedi. Kullanıcının giriş yapması bekleniyor; Google uygulaması/kimlik bilgisi oluşturulmadı, erişim izni verilmedi. Gönderim yöntemi mevcut PHP mail olarak kaldı; Google bağlantısı ve gerçek test e-postası henüz tamamlanmadı. Bu adres değişikliği tek başına e-posta teslim sorununu çözmüş sayılmaz.

## 22 Ekim 2026 — Canlı e-posta ayarlarının salt okunur incelenmesi

Kullanıcının yetkili Chrome oturumunda `https://www.myliba.com/wp-admin/admin.php?page=wp-mail-smtp` açıldı. WP Mail SMTP 4.10.3 etkin; seçili gönderici **Varsayılan (yok)**, Google/Gmail ve Diğer SMTP seçili değil. Ayar ekranında gönderici e-posta ve ad alanları boş görünüyor. SMTP üzerinden gönderim için yapılandırılmış bir bağlantı bu ekranda bulunmuyor.

Bu, mevcut kurulumda WordPress'in varsayılan posta yolunun seçili olduğunu doğrular. Önceki mail() bulunamadı hatasıyla uyumludur; tek başına bugünkü PHP hatasının teşhisi veya mevcut mail teslim testi değildir. Test e-postası gönderilmedi, alıcı gelen kutusu okunmadı, ayarlar kaydedilmedi. Sonraki adım mevcut kurumsal gönderici hesabını belirleyip uygun SMTP/Google bağlantısını kullanıcı yetkisiyle yapılandırmaktır. Canlı yapılandırma için parola veya OAuth yetkilendirmesi gerekebilir; gizli bilgiler rapora alınmamalı.

## 22 Ekim 2026 — Bildirim hatasına karşı form koruması

Kullanıcı canlı `wp-admin/admin-post.php` ekranında WordPress kritik hata görüntüsü paylaştı. Ekran hata ayrıntısını içermiyor. Repodaki debug.log içinde **28 Ağustos 2026** tarihli eski bir canlı hata kaydı bulundu: `Call to undefined function PHPMailer\PHPMailer\mail()`, çağrı zinciri `Forms::handle → send_notification → wp_mail`. Bu eski kayıt bugünkü olayın kesin teşhisi değildir; güncel hosting logu veya WordPress hata e-postası bekleniyor.

Önceki test yalnız wp_mail'in false dönmesini deniyordu. PHP Error ve exception fırlatma senaryoları eklenince korumasız kodda yerel çökme yeniden üretildi. forms.php artık yalnız bildirim çağrısını Throwable yakalamasıyla sınırlar. Kayıt başarısızlığı hâlâ form hatasıdır. Talep kaydı oluşturulduktan sonra e-posta hatası formu çökertmez veya kaydı silmez.

Bildirim fonksiyonu bool döndürür. `_myliba_notification_status` meta alanında `accepted` veya `failed` tutulur. `accepted` wp_mail'in isteği kabul ettiğini gösterir; alıcıya teslim kanıtı değildir. Başarısızlıkta loga yalnız kayıt ID'siyle sabit hata mesajı yazılır; exception metni, SMTP erişim bilgileri ve formdaki kişisel veriler yazılmaz. Bu alan mevcut admin ekranına yeni bir gösterge olarak eklenmedi; kayıt metası ve log üzerinden incelenebilir. Otomatik tekrar gönderim eklenmedi.

Yerel test: **10 senaryo / 70 kontrol geçti**. Önceki sekiz senaryoya mailer PHP Error ve exception yolları eklendi; dört kayıtlı talep senaryosunda bildirim durumu da doğrulandı. Gerçek yerel kayıtlar temizlendi, mail çağrıları yakalandı, dışarıya e-posta gönderilmedi. forms.php PHP sözdizimi kontrolü geçti.

Canlıya dosya yüklenmedi ve SMTP ayarları değiştirilmedi. Gerçek e-posta gönderimi/teslimi doğrulanmadı. Güncel hata aynı mail transport sorununu doğrularsa hostingde WP Mail SMTP'nin etkinliği ve yapılandırılmış gönderim yöntemi kontrol edilmeli. Doğrudan SEO etkisi yok; canonical, robots, sitemap, schema, içerik ve staging politikası değişmedi.

## 22 Ekim 2026 — B01: kayıt hatası doğru form sonucuna yansıyor

Kullanıcı başlangıç denetimi sonrasında bu yerel düzeltmeyi ve testlerini onayladı.

`wordpress/wp-content/plugins/myliba-core/includes/forms.php::handle()` artık `wp_insert_post(..., true)` kullanıyor. Sonuç 0 veya WP_Error ise `myliba_form=error` yönlendirmesiyle duruyor; meta yazımı ve e-posta bildirimi çalışmıyor. Kayıt başarılıysa meta alanları ve bildirim akışı devam ediyor. Başarı, kaydın oluşturulması anlamına geliyor; e-posta teslim garantisi değil. Ortak handler nedeniyle bu koruma contact/academy bağlamlarına da uygulanıyor.

Honeypot'un botlara yanıltıcı başarı yanıtı korunuyor; bu dal kayıt veya bildirim oluşturmuyor. Kullanıcının query parametresiyle başarı mesajını açabilmesi de bu kapsamda değiştirilmedi. Dolayısıyla GA4 dönüşümü hâlâ `?myliba_form=success` değerine bağlanmamalı; B02 için backend onaylı tekil kanıt gerekiyor. GA4 event eklenmedi.

### Yerel doğrulama

Komut (wordpress/ dizininden):

```sh
docker compose exec -T wordpress php wp-content/plugins/myliba-core/tests/forms.php
```

Sonuç: **8 senaryo, 48 kontrol geçti**.

- Gerçek WordPress kaydı private oluşuyor ve demo/e-posta/şirket meta alanları saklanıyor.
- Kayıt başarılıyken bildirim false dönerse kayıt başarısı korunuyor.
- 0 ve WP_Error sonuçları hata yönlendirmesi veriyor, bildirim göndermiyor.
- Geçersiz nonce, eksik şirket ve rate limit kayıt/bildirim oluşturmuyor.
- Honeypot kayıt/bildirim oluşturmadan mevcut yanıltıcı success cevabını koruyor.

Test yalnız CLI ve local/development ortamlarında çalışır. Başarılı yol gerçek yerel veritabanını kullanır; ekleme hataları taklit edilir ve yönlendirmeler yakalanır. E-postalar fonksiyon sınırında yakalanır, harici SMTP'ye çıkmaz. Geçici kayıtlar finally bloğunda silinir. Değişen iki PHP dosyasında PHP 8.3 sözdizimi kontrolü geçti.

### SEO ve AI görünürlüğü değerlendirmesi

Doğrudan arama içeriği değişmedi. `seo-ai-review` kapsamında etkilenen yerel demo yüzeyi GET ile kontrol edildi:

| Yerel yol | HTTP | Canonical sayısı | Canonical hedef | Robots |
| --- | ---: | ---: | --- | --- |
| `/tr/demo/` | 200 | 1 | `/tr/demo/` | noindex yok |
| `/tr/demo/?myliba_form=error` | 200 | 1 | `/tr/demo/` | meta ve X-Robots-Tag: noindex, follow |
| `/tr/demo/?myliba_form=success` | 200 | 1 | `/tr/demo/` | meta ve X-Robots-Tag: noindex, follow |

Yeni meta/canonical/schema üreticisi eklenmedi. SEO ayarları, staging korumaları ve sitemap kodu değiştirilmedi. Bu yerel regresyon kontrolüdür; canlı yayın, gerçek staging ve GA4 DebugView doğrulaması yapılmadı. Bu sınırlı form düzeltmesi için yeni içerik/AI puanı hesaplanmadı.

### Geri alma ve sonraki iş

Veritabanı şeması/ayar migrasyonu yok. Geri almak için yalnız forms.php içindeki bu küçük diff önceki sürüme döndürülebilir; başka kullanıcı değişikliklerini geri alma. Staging ve canlı dağıtımı yapılmadı. Sonraki iş B02 tekil başarılı demo olayıdır; başarı parametresi veya buton tıklaması tek başına dönüşüm değildir.
