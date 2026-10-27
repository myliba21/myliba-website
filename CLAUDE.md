# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

myliba.com corporate website: a WordPress site whose goal is qualified **enterprise demo requests** from organic search in Turkey. Turkish is the primary market and language; English is phase two. Copy, docs and commit context are mostly Turkish. **Reply to the user in Turkish.**

The SEO/AI-visibility rules in `AGENTS.md` are mandatory and imported below.

@AGENTS.md

## Commands (run from `wordpress/`)

```bash
cp .env.example .env            # first time; change every password
make bootstrap                  # docker compose up + install + activate theme/plugin + permalinks + seed
make up | make down | make logs
make seed                       # wp myliba seed --yes (starter content)
make sync-blog                  # wp myliba sync-blog --yes
make smtp-test

# any WP-CLI command
docker compose --profile tools run --rm wpcli wp <command>
docker compose --profile tools run --rm wpcli wp myliba <seed|tr-first|import-current|refresh-site-pages|materialize-home|materialize-academy|sync-blog> --yes

# plugin integration checks (local/development environment only)
docker compose exec -T wordpress php wp-content/plugins/myliba-core/tests/forms.php
docker compose exec -T wordpress php wp-content/plugins/myliba-core/tests/seo-review.php

# theme CSS (from repo root): src/ → dist/ (dist is committed)
node tools/build-css.mjs
```

Local site: http://localhost:8080 (ports bound to 127.0.0.1; Docker stack is for local only). Stack: `wordpress:php8.3-apache`, `mariadb:11.4`, `wordpress:cli-php8.3`; `nginx/default.conf` is the reverse-proxy config.

## Layout

```
wordpress/
  wp-content/plugins/myliba-core/   ALL content model & behaviour (namespace Myliba\Core, v0.3.0)
    myliba-core.php                 loads includes/ in order
    includes/post-types.php         CPTs: myliba_product, _solution, _academy, _case_study, _testimonial, _faq, _landing, _event, _ebook, _report, _team
    includes/meta.php               editor SEO/meta fields          includes/seo.php     canonical, OG, hreflang, schema, sitemaps
    includes/seo-review*.php        "SEO ve AI Hazırlık Kontrolü" on blog edit screens; rubric resources/seo-review-rules.json
    includes/forms.php              contact/demo forms              includes/options.php site texts (Myliba → Site Texts TR/EN)
    includes/wp-cli.php             `wp myliba …` commands          migrations/*.php     one-off data scripts (wp eval-file)
    tests/                          CLI integration checks (refuse to run outside local/development)
  wp-content/themes/myliba/         PRESENTATION ONLY: templates (single-/archive-myliba_*.php, template-*.php),
                                    functions.php (myliba_text(), myliba_localize_url()), assets/css/src → dist
  migration/                        content map & review report from the old site
content/seo-okr/                    local editorial drafts (Markdown) — NOT published to WordPress
docs/seo/                           audit, backlog, keyword→URL map, url-inventory.csv, change log (changes.md)
seo_geo_implementation_plan.md      the SEO/GEO roadmap and business rules (read before SEO work)
```

## Rules

- **Plugin vs theme:** data, CPTs, meta, SEO output, forms and CLI go in `myliba-core`; markup and styling go in `themes/myliba`. Don't put business logic in templates.
- Don't edit WordPress core or third-party plugins (`wpforms-lite`, `wp-mail-smtp`, `all-in-one-wp-migration`, `akismet`). Uploads are local state and ignored by git.
- **Languages:** TR and EN routes are both permanent (`/tr/…`, `/en/…`). Unfinished English subpages stay in Trash until cloned from approved Turkish pages (`wp myliba tr-first`). Every user-facing string goes through `myliba_text()` (site texts) or the translated WP entry; internal URLs go through `myliba_localize_url()`.
- **SEO output:** `myliba-core` is a fallback that defers to a full SEO plugin when one is active. Never emit duplicate canonical/meta/schema, keep staging `noindex` protections, and record SEO-affecting changes in `docs/seo/changes.md`.
- **Content honesty:** no fabricated customers, testimonials, reviews, prices or capability claims; `05-musteri-hikayesi-dogrulama-bekliyor.md` waits for real customer data. Drafts in `content/` are not "published".
- **Data changes** to pages/posts are written as dated scripts in `myliba-core/migrations/YYYY-MM-DD-<topic>.php` (run with `wp eval-file`), idempotent where possible. Don't run them or `import-current` against production.
- **Secrets:** API keys (e.g. `MYLIBA_SEO_AI_KEY`, `MYLIBA_SEO_AI_MODEL`) come from the server environment or `wp-config.php`; never commit `.env` or keys.
- After PHP changes: `php -l <file>` (or via the wordpress container), run the relevant test script, then check the page on http://localhost:8080. Report what you verified.
- No deployment to production and no commits/pushes unless the user asks.
