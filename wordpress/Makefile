-include .env
export

WORDPRESS_URL ?= http://localhost:8080
WORDPRESS_TITLE ?= Myliba
WORDPRESS_ADMIN_USER ?= admin
WORDPRESS_ADMIN_PASSWORD ?= change-me-admin
WORDPRESS_ADMIN_EMAIL ?= admin@example.com

.PHONY: up install seed bootstrap down logs smtp-test sync-blog pull-live predeploy links-preview links-apply pairs-preview pairs-apply

up:
	docker compose up -d

install:
	docker compose --profile tools run --rm wpcli wp core is-installed || docker compose --profile tools run --rm wpcli wp core install --url="$(WORDPRESS_URL)" --title="$(WORDPRESS_TITLE)" --admin_user="$(WORDPRESS_ADMIN_USER)" --admin_password="$(WORDPRESS_ADMIN_PASSWORD)" --admin_email="$(WORDPRESS_ADMIN_EMAIL)" --skip-email
	docker compose --profile tools run --rm wpcli wp theme activate myliba
	docker compose --profile tools run --rm wpcli wp plugin activate myliba-core
	docker compose --profile tools run --rm wpcli wp plugin install wp-mail-smtp --activate 2>/dev/null || docker compose --profile tools run --rm wpcli wp plugin activate wp-mail-smtp
	docker compose --profile tools run --rm wpcli wp option update permalink_structure '/%postname%/'
	docker compose --profile tools run --rm wpcli wp rewrite flush

seed:
	docker compose --profile tools run --rm wpcli wp myliba seed --yes

bootstrap: up install seed

down:
	docker compose down

logs:
	docker compose logs -f wordpress

smtp-test:
	docker compose --profile tools run --rm wpcli wp eval 'wp_mail(get_option("admin_email"), "[Myliba] SMTP Test", "Bu bir test mailidir. WP Mail SMTP çalışıyor."); echo "Mail gönderildi.";'

sync-blog:
	docker compose --profile tools run --rm wpcli wp myliba sync-blog --yes

# Canlı veritabanının son anlık görüntüsünü yerele alır (tek yön: canlı → yerel).
pull-live:
	bash scripts/pull-live.sh

# All-in-One export almadan önce çalıştırın; hiçbir şeyi değiştirmez.
predeploy:
	bash scripts/predeploy-check.sh

# Gövdelerdeki kırık/yönlendirmeli iç bağlantılar (docs/seo/audit-2026-11-23.md, A5).
links-preview:
	docker compose --profile tools run --rm wpcli wp eval-file wp-content/plugins/myliba-core/migrations/2026-11-23-internal-links.php

links-apply:
	docker compose --profile tools run --rm wpcli wp eval-file wp-content/plugins/myliba-core/migrations/2026-11-23-internal-links.php apply

# Blog yazılarında TR–EN eşleştirme (hreflang). docs/seo/audit-2026-11-23.md, B4.
pairs-preview:
	docker compose --profile tools run --rm wpcli wp eval-file wp-content/plugins/myliba-core/migrations/2026-11-23-post-translation-pairs.php

pairs-apply:
	docker compose --profile tools run --rm wpcli wp eval-file wp-content/plugins/myliba-core/migrations/2026-11-23-post-translation-pairs.php apply
