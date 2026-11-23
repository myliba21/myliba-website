#!/usr/bin/env bash
# Canlı veritabanının son anlık görüntüsünü yerel Docker ortamına alır.
# Yön tektir: canlı → yerel. Bu betik canlıya hiçbir şey yazmaz.
#
# Kullanım (wordpress/ klasöründen): make pull-live
set -euo pipefail

cd "$(dirname "$0")/.."

if [ -f .env ]; then
    set -a
    # shellcheck disable=SC1091
    . ./.env
    set +a
fi

: "${LIVE_FTP_HOST:?wordpress/.env içinde LIVE_FTP_HOST tanımlı değil}"
: "${LIVE_FTP_USER:?wordpress/.env içinde LIVE_FTP_USER tanımlı değil}"
: "${LIVE_FTP_PASSWORD:?wordpress/.env içinde LIVE_FTP_PASSWORD tanımlı değil}"
LIVE_URL="${LIVE_URL:-https://www.myliba.com}"
LOCAL_URL="${WORDPRESS_URL:-http://localhost:8080}"
REMOTE_DIR="${LIVE_SYNC_DIR:-myliba-sync}"

case "$LOCAL_URL" in
    http://localhost*|http://127.0.0.1*) ;;
    *) echo "WORDPRESS_URL yerel bir adres değil ($LOCAL_URL). Durduruldu." >&2; exit 1 ;;
esac

TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

fetch() {
    curl --fail --silent --show-error --ssl-reqd \
        --user "$LIVE_FTP_USER:$LIVE_FTP_PASSWORD" \
        "ftp://$LIVE_FTP_HOST/$REMOTE_DIR/$1" -o "$TMP/$1"
}

echo "1/6 Canlı anlık görüntü indiriliyor…"
fetch latest.json
fetch latest.sql.gz

meta="$(cat "$TMP/latest.json")"
field() { printf '%s' "$meta" | sed -n "s/.*\"$1\":\"\\([^\"]*\\)\".*/\\1/p"; }
created_at="$(field created_at)"
live_prefix="$(field table_prefix)"
expected_sha="$(field sha256)"

if command -v shasum >/dev/null 2>&1; then
    actual_sha="$(shasum -a 256 "$TMP/latest.sql.gz" | cut -d' ' -f1)"
else
    actual_sha="$(sha256sum "$TMP/latest.sql.gz" | cut -d' ' -f1)"
fi
if [ -z "$expected_sha" ] || [ "$expected_sha" != "$actual_sha" ]; then
    echo "İndirilen dosyanın özeti eşleşmiyor. Durduruldu." >&2
    exit 1
fi
echo "    Anlık görüntü tarihi (UTC): $created_at"

db() { docker compose exec -T db sh -c "$1"; }
wp() { docker compose --profile tools run --rm -T wpcli wp "$@"; }

echo "2/6 Yerel veritabanı yedekleniyor…"
mkdir -p .local-backups
backup=".local-backups/local-before-pull-$(date +%Y%m%d-%H%M%S).sql.gz"
db 'exec mariadb-dump -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' | gzip > "$backup"
echo "    $backup"

local_prefix="$(wp config get table_prefix 2>/dev/null | tr -d '\r\n')"
local_prefix="${local_prefix:-wp_}"

echo "3/6 Canlı veriler içe aktarılıyor…"
if [ -n "$live_prefix" ] && [ "$live_prefix" != "$local_prefix" ]; then
    gunzip -c "$TMP/latest.sql.gz" | sed "s/\`${live_prefix}/\`${local_prefix}/g" \
        | db 'exec mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"'
    like_prefix="$(printf '%s' "$live_prefix" | sed 's/_/\\\\_/g')"
    wp db query "UPDATE ${local_prefix}options SET option_name = '${local_prefix}user_roles' WHERE option_name = '${live_prefix}user_roles'; UPDATE ${local_prefix}usermeta SET meta_key = CONCAT('${local_prefix}', SUBSTRING(meta_key, CHAR_LENGTH('${live_prefix}') + 1)) WHERE meta_key LIKE '${like_prefix}%';"
else
    gunzip -c "$TMP/latest.sql.gz" \
        | db 'exec mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"'
fi

echo "4/6 Adresler yerel ortama çevriliyor…"
wp search-replace "$LIVE_URL" "$LOCAL_URL" --all-tables-with-prefix --skip-columns=guid --report-changed-only

echo "5/6 Form kayıtları yerelden siliniyor (kişisel veri)…"
ids="$(wp post list --post_type=myliba_submission --post_status=any --format=ids | tr -d '\r\n')"
if [ -n "$ids" ]; then
    # shellcheck disable=SC2086
    wp post delete $ids --force --quiet
fi

echo "6/6 Önbellek ve kalıcı bağlantılar yenileniyor…"
wp cache flush --quiet || true
wp rewrite flush --quiet

echo "Tamam. Yerel site canlının $created_at (UTC) tarihli içeriğini gösteriyor: $LOCAL_URL"
echo "Eksik görseller canlı siteden yüklenir; yerel kod dosyalarına dokunulmadı."
