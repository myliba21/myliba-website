# WordPress Content Map

| Content type | WordPress target | Notes |
| --- | --- | --- |
| `homepage` | Page: `en` / `tr` | Static front page uses the English page; translations should be linked with Polylang or WPML in production. |
| `our-products` | Page | Hero and SEO fields are in the Myliba meta boxes. |
| `okr-academy` | Page | Use the block editor for body content and featured image for hero image. |
| `culture-analysis` | Page | Same page model as service pages. |
| `ethics-counsel` | Page | Same page model as service pages. |
| `our-story` | Page | Same page model as service pages. |
| `faq-page` | Page | Start as editable page content. A dedicated FAQ block/plugin can be added later. |
| `security-page` | Page | Same page model as service pages. |
| `privacy-policy` | Page | Keep public URL stable and add 301 redirects from old paths. |
| `blog-post` | Native WordPress `post` | Categories and tags can use native WordPress taxonomies. |
| `blog-category` | Native WordPress `category` | Recreate category slugs before importing posts if SEO URLs matter. |
| `event` | Custom post type: `myliba_event` | Event date, location, status, and registration URL live in the Event Details box. |
| `team-member` | Custom post type: `myliba_team` | Role, order, LinkedIn URL, and featured image. |
| `client-logo` | Custom post type: `myliba_client_logo` | Private admin content; featured image is the logo. |
| `form-submission` | Custom post type: `myliba_submission` | New submissions are stored locally by `[myliba_contact_form]`. |
| `site-settings` | `Myliba > Site Settings` | Indexing, contact email, organization schema, CTA, and footer note. |

## SEO migration checklist

- Preserve every existing public URL or add a 301 redirect.
- Fill SEO title and meta description for every important page.
- Add featured images for Open Graph sharing.
- Install Polylang or WPML before final launch and verify `hreflang`.
- Keep indexing disabled on staging until content, redirects, sitemap, and Search Console are ready.

