# Myliba WordPress

This folder contains the WordPress version of the site. The content model is in `wp-content/plugins/myliba-core`; the theme handles only presentation in `wp-content/themes/myliba`.

## Local setup

1. Copy `.env.example` to `.env` and **change all passwords** (use `openssl rand -base64 32` for each).
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

## Import from myliba.com

To pull public content from the current live site:

```bash
docker compose --profile tools run --rm wpcli wp myliba import-current --yes
```

## Production plugin choices

For production SEO and multilingual management, keep the custom Myliba data model and add these WordPress plugins:

- **Multilingual**: Polylang or WPML. `myliba-core` already exposes the custom post types to Polylang when it is installed.
- **SEO**: Rank Math or Yoast SEO. `myliba-core` ships a minimal SEO fallback (canonical, OG, hreflang, schema) but defers to a full SEO plugin when active.
- **SMTP**: WP Mail SMTP, FluentSMTP, or your Natro SMTP settings.
- **Forms**: The built-in Myliba contact form works, but Fluent Forms or Gravity Forms can replace it later.
- **Security/cache**: Wordfence or Solid Security, plus the cache layer included in your Natro hosting plan.
- **Cookie consent**: CookieYes, Complianz, or similar for KVKK/GDPR banner requirements.
- **Redirects**: Redirection plugin for 301 redirect management.

## Staging index policy

Indexing is disabled by default in `Myliba > Site Settings`. While the site is on a staging subdomain, keep it disabled and add Basic Auth or IP restriction at hosting level. When the final domain goes live, enable indexing, refresh permalinks, and submit the sitemap in Search Console.

## Production checklist

- [ ] Change all `.env` passwords (never commit `.env` to git).
- [ ] Set `WP_ENVIRONMENT_TYPE=production`, disable `WP_DEBUG` in `wp-config.php`.
- [ ] Remove the `all-in-one-wp-migration` plugin after migration is complete (reduces attack surface).
- [ ] Install and configure Polylang or WPML for multilingual routing and `hreflang`.
- [ ] Install and configure Rank Math or Yoast SEO.
- [ ] Connect SMTP via WP Mail SMTP or FluentSMTP.
- [ ] Enable SSL and update nginx config (uncomment HTTPS block in `nginx/default.conf`).
- [ ] Enable HSTS header in `nginx/default.conf` (after SSL is confirmed working).
- [ ] Add 301 redirect map for any old URL paths.
- [ ] Enable indexing in `Myliba > Site Settings`.
- [ ] Submit sitemap to Google Search Console.
- [ ] Run Lighthouse mobile check (target: SEO ≥ 90).
- [ ] Verify forms, canonical, OG tags, and hreflang before launch.
