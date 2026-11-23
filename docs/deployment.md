# Yayın ve veri eşitleme

23 Kasım 2026'da kuruldu. Kural: **kod yerelden canlıya, veri canlıdan yerele** gider. All-in-One WP Migration ile yerelden canlıya import yapılmaz; araç yalnız canlı yedeği almak için kullanılır.

> **Durum (23 Kasım 2026, 15:10):** Kullanıcı All-in-One WP Migration ile tam klonlama düzenini sürdürmeyi seçti. Aşağıdaki GitHub Actions / `make pull-live` kurulumu **devrede değil**: cron eklenmedi, FTP hesabı ve Secrets yok. Geçerli akış bu bölümdedir.

## Geçerli akış: tam klon (All-in-One WP Migration)

1. Ekibe haber ver; yayın bitene kadar canlıda değişiklik yapılmasın.
2. Canlıyı yerele al (All-in-One export → yerelde import). **Kod dosyalarını canlıdan yerele kopyalama**; kodun kaynağı git deposudur.
3. Yerelde çalış, commit et.
4. `cd wordpress && make predeploy` çalıştır. ✗ varsa export alma.
5. Canlıdan All-in-One yedeği al; canlıdaki Form Submissions listesinde yeni talep var mı bak.
6. Yerelden export, canlıya import.
7. Canlıda formu bir kez dene.

Klonun bozmaması için kalıcı ayarlar canlı `wp-config.php` içinde durur (All-in-One bu dosyayı taşımaz): SMTP için `WPMS_*` sabitleri. Yalnız canlıda kurulan eklentiler import ile silinir; önce yerele kurulmalıdır.

---

| Ne | Asıl kaynak | Taşıma |
| --- | --- | --- |
| `themes/myliba`, `plugins/myliba-core` | GitHub `main` | GitHub Actions → FTPS (`.github/workflows/deploy.yml`) |
| İçerik, form kayıtları, ayarlar, uploads | Canlı site | Taşınmaz. Yerele `make pull-live` ile kopyalanır |
| Gizli ayarlar (SMTP vb.) | Canlı `wp-config.php` | Taşınmaz |
| Üçüncü parti eklentiler | Canlı WordPress paneli | Panelden güncellenir |

## Canlıya alma

1. Değişikliği yerelde test et, `main` dalına commit ve push et.
2. GitHub → Actions → "Canlıya al (tema + myliba-core)" → Run workflow. `confirm` alanına `deploy` yaz. İlk seferde ve emin olunmayan durumlarda `dry_run` işaretle; dosya yüklenmez, yalnız değişecekler listelenir.
3. İş akışı önce PHP sözdizimini ve form korumasını denetler, sonra yalnız iki klasörü yükler. Veritabanına, `uploads` klasörüne ve `wp-config.php` dosyasına dokunmaz.
4. Geri alma: önceki commit'e dönüp (revert) aynı iş akışını yeniden çalıştır.

Gerekli GitHub Secrets: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`. FTP hesabının kök dizini hosting ana dizini (`/home/u0269638`) olmalı.

## Canlı veriyi yerele alma

```bash
cd wordpress
make pull-live
```

Betik (`scripts/pull-live.sh`) sırayla: canlıdaki son anlık görüntüyü FTPS ile indirir ve özetini doğrular, yerel veritabanını `wordpress/.local-backups/` altına yedekler, canlı veriyi içe aktarır, adresleri `http://localhost:8080` olarak değiştirir, form kayıtlarını (kişisel veri) yerelden siler. Kod dosyalarına dokunmaz. Eksik görseller yerelde canlı siteden yüklenir (`includes/local-dev.php`, yalnız `local` ortamında çalışır).

Gerekli `wordpress/.env` değerleri: `LIVE_FTP_HOST`, `LIVE_FTP_USER`, `LIVE_FTP_PASSWORD`.

Anlık görüntüyü sunucuda `~/myliba-sync/myliba-db-dump.php` üretir (kaynağı `tools/server/`). Web kökünün dışındadır, veritabanı bilgilerini canlı `wp-config.php` dosyasından okur, parolası depoda tutulmaz. Zamanlanmış görev (cron) olarak çalışır.

## Bilinen sınırlar

- Canlıda komut satırı yok. `migrations/*.php` betiklerinin canlıda çalıştırılması için panel ekranı henüz yazılmadı.
- `make pull-live` yerel veritabanının yerine canlınınkini koyar; yerelde girilen içerik kaybolur (yedeği `.local-backups/` altında kalır).
- Anlık görüntü kullanıcı hesaplarını da içerir; dosyayı paylaşmayın.
