# Uygulama kaydı

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
