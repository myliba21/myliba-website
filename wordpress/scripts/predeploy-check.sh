#!/usr/bin/env bash
# All-in-One WP Migration ile export almadan ÖNCE çalıştırın: make predeploy
# Hiçbir şeyi değiştirmez; yalnızca kontrol eder ve sonucu yazar.
set -uo pipefail

cd "$(dirname "$0")/.."
CORE="wp-content/plugins/myliba-core"
THEME="wp-content/themes/myliba"
fail=0
ok()   { echo "  ✓ $1"; }
bad()  { echo "  ✗ $1"; fail=1; }
warn() { echo "  ! $1"; }

echo "1/4 Form koruması"
if grep -q '_myliba_notification_status' "$CORE/includes/forms.php" && grep -q 'Throwable' "$CORE/includes/forms.php"; then
    ok "forms.php korumalı sürüm"
else
    bad "forms.php korumasız (eski) sürüm. Bu haliyle canlıya çıkarsa form gönderimi bozulur."
fi

echo "2/4 Kaydedilmemiş kod değişikliği"
changes="$(git status --short -- "$CORE" "$THEME" 2>/dev/null)"
if [ -z "$changes" ]; then
    ok "tema ve myliba-core commit edilmiş"
else
    warn "commit edilmemiş değişiklik var (yayına engel değil, ama kaybolmaması için commit edin):"
    echo "$changes" | sed 's/^/      /'
fi

echo "3/4 PHP sözdizimi"
if docker compose exec -T wordpress true 2>/dev/null; then
    errors="$(docker compose exec -T wordpress sh -c "find $CORE $THEME -name '*.php' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v '^No syntax errors' || true")"
    if [ -z "$errors" ]; then
        ok "hata yok"
    else
        bad "sözdizimi hatası:"
        echo "$errors" | sed 's/^/      /'
    fi

    echo "4/4 Form testi"
    if docker compose exec -T wordpress php "$CORE/tests/forms.php" > /tmp/myliba-forms-test.log 2>&1; then
        ok "form testi geçti"
    else
        bad "form testi başarısız (ayrıntı: /tmp/myliba-forms-test.log)"
    fi
else
    bad "Docker çalışmıyor; önce 'make up' ile yerel siteyi başlatın."
fi

echo
if [ "$fail" -eq 0 ]; then
    echo "HAZIR. Export almadan önce son hatırlatmalar:"
    echo "  - Canlıdan All-in-One ile yedek alın."
    echo "  - Canlıda 'Form Submissions' listesine bakın: canlıyı yerele aldıktan sonra gelen talep varsa not edin."
    echo "  - Import'tan sonra canlıda formu bir kez deneyin."
else
    echo "DURUN. Yukarıdaki ✗ satırları düzelmeden export almayın."
    exit 1
fi
