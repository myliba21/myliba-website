-include .env
export

WORDPRESS_URL ?= http://localhost:8080
WORDPRESS_TITLE ?= Myliba
WORDPRESS_ADMIN_USER ?= admin
WORDPRESS_ADMIN_PASSWORD ?= change-me-admin
WORDPRESS_ADMIN_EMAIL ?= admin@example.com

.PHONY: up install seed bootstrap down logs

up:
	docker compose up -d

install:
	docker compose --profile tools run --rm wpcli wp core is-installed || docker compose --profile tools run --rm wpcli wp core install --url="$(WORDPRESS_URL)" --title="$(WORDPRESS_TITLE)" --admin_user="$(WORDPRESS_ADMIN_USER)" --admin_password="$(WORDPRESS_ADMIN_PASSWORD)" --admin_email="$(WORDPRESS_ADMIN_EMAIL)" --skip-email
	docker compose --profile tools run --rm wpcli wp theme activate myliba
	docker compose --profile tools run --rm wpcli wp plugin activate myliba-core
	docker compose --profile tools run --rm wpcli wp option update permalink_structure '/%postname%/'
	docker compose --profile tools run --rm wpcli wp rewrite flush

seed:
	docker compose --profile tools run --rm wpcli wp myliba seed --yes

bootstrap: up install seed

down:
	docker compose down

logs:
	docker compose logs -f wordpress
