# Uygulama kaydı

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
