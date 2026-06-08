# Myliba WordPress Migration

This folder contains the local WordPress version of the site. The WordPress data model is kept in `wp-content/plugins/myliba-core`; the theme is only the presentation layer in `wp-content/themes/myliba`.

## Local setup

1. Copy `.env.example` to `.env` and change the passwords.
2. Bootstrap WordPress from this `wordpress/` directory:

```bash
make bootstrap
```

This starts containers, installs WordPress if needed, activates the theme/plugin, sets permalinks, and seeds starter content. The same steps can be run manually:

```bash
docker compose up -d
docker compose --profile tools run --rm wpcli wp core install --url="${WORDPRESS_URL:-http://localhost:8080}" --title="${WORDPRESS_TITLE:-Myliba}" --admin_user="${WORDPRESS_ADMIN_USER:-admin}" --admin_password="${WORDPRESS_ADMIN_PASSWORD:-change-me-admin}" --admin_email="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}" --skip-email
docker compose --profile tools run --rm wpcli wp theme activate myliba
docker compose --profile tools run --rm wpcli wp plugin activate myliba-core
docker compose --profile tools run --rm wpcli wp option update permalink_structure '/%postname%/'
docker compose --profile tools run --rm wpcli wp rewrite flush
docker compose --profile tools run --rm wpcli wp myliba seed --yes
```

The local site will be available at `http://localhost:8080`.

## Production plugin choices

For production SEO and multilingual management, keep the custom Myliba data model and add these WordPress plugins:

- Multilingual: Polylang or WPML. `myliba-core` already exposes the custom post types to Polylang when it is installed.
- SEO: Rank Math or Yoast SEO. `myliba-core` ships a minimal SEO fallback but avoids taking over from a full SEO plugin.
- SMTP: WP Mail SMTP, FluentSMTP, or your Natro SMTP settings.
- Forms: The built-in Myliba contact form works, but Fluent Forms or Gravity Forms can replace it later.
- Security/cache: Wordfence or Solid Security, plus the cache layer included in your Natro hosting plan.

## Staging index policy

Indexing is disabled by default in `Myliba > Site Settings`. While the site is on a staging subdomain, keep it disabled and add Basic Auth or IP restriction at hosting level. When the final domain goes live, enable indexing, refresh permalinks, and submit the sitemap in Search Console.

## Strapi import

If the existing Strapi app is running locally, import reachable content with:

```bash
docker compose --profile tools run --rm wpcli wp myliba import-strapi --url=http://host.docker.internal:1337 --yes
```

The importer maps the current Strapi content types into WordPress pages, posts, events, team members, client logos, and form submissions where possible.
