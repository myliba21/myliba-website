# Myliba WordPress Review and Production Roadmap

Date: 2026-06-08

## Executive Summary

The current local WordPress migration is a clean foundation, but it still reads like a starter site: it has the right content model, noindex staging controls, and basic page templates, yet it needs a stronger B2B SaaS narrative, richer conversion paths, stronger mobile navigation, dedicated demo flow, managed product/solution/academy content, and deeper SEO templates.

The reference site communicates OKR + culture + academy clearly, but the new WordPress version should feel more modern, denser, and more conversion-focused than a standard WordPress template.

## UI Review

- The current theme is lightweight and readable, but the first version is too sparse for a premium B2B SaaS site.
- Hero needs a stronger product message, dashboard/product UI visual, and visible dual CTA.
- Header needs clearer product/academy/solutions/resources grouping, call CTA, demo CTA, and mobile hamburger behavior.
- Footer needs stronger trust, legal, contact, and conversion messaging.
- Cards should show modern SaaS hierarchy with module tags, outcomes, and role-based use cases.
- The design should avoid generic WordPress template patterns by using structured sections, product UI mockups, proof points, and business outcomes.

## UX Review

- The first 5 seconds must explain that Myliba combines OKR, KPI, CFR, 1:1, action management, feedback, performance development, academy, and consulting.
- Demo and phone actions must be present in header, hero, final CTA, footer, and mobile sticky bar.
- Product, academy, solutions, blog/resources, and contact paths should be obvious from the first viewport.
- Mobile needs a bottom sticky CTA and an accessible hamburger menu.
- Blog and SEO content should be reachable from the main navigation, not hidden in footer-only paths.

## SEO Review

- Staging noindex works and must stay disabled until launch.
- Current fallback SEO exists, but production should use Rank Math or Yoast for editor workflow, XML sitemap, redirects, and richer Open Graph controls.
- Custom post types are needed for products, academy programs, solutions, case studies, testimonials, FAQs, and SEO landing pages.
- Blog detail needs reading time, table of contents, author box, related posts, article schema, and internal CTA.
- Landing pages should support clean URLs for OKR software, OKR management, performance management, feedback culture, KPI vs OKR, 1:1 meetings, action management, and cultural transformation.
- FAQ schema and breadcrumb schema should be generated where relevant.

## Responsive Review

- Mobile layout must use one-column cards, larger tap targets, sticky CTA, and safe-area padding.
- Header must collapse into a controlled panel instead of wrapping links.
- Forms must remain easy to complete on mobile and not be hidden behind sticky CTA.
- Footer should stack into readable groups.

## Content Review

- The content should lead with outcomes: strategy-to-action, performance culture, transparency, continuous feedback, leadership development, and measurable culture.
- The software + academy distinction is important and should be a dedicated homepage section.
- Product modules should be explicit: OKR, KPI, CFR, action management, 1:1, feedback, performance development, leadership/coaching, academy, analytics.
- Persona pages should be available for executives, HR, strategy office, team leaders, and employees.
- Security, privacy, KVKK, cookie policy, and terms pages should be present for B2B trust.

## WordPress Technical Recommendation

- Keep the site-specific data model in `myliba-core`; do not put business content types only in the theme.
- Use the custom theme for presentation; no child theme is needed while this is the primary custom theme.
- Avoid heavy page builders for the first production version. Use Gutenberg + custom templates + custom meta boxes.
- Add Polylang or WPML before launch for production multilingual routing and `hreflang`.
- Add Rank Math or Yoast SEO before launch. The custom SEO fallback should remain a safety net.
- Add FluentSMTP or WP Mail SMTP with Natro SMTP settings.
- Add Redirection for old URL mapping.
- Add a cache/image optimization solution based on the final Natro hosting stack: LiteSpeed Cache if the server is LiteSpeed, otherwise WP Rocket plus ShortPixel/Imagify.

## Required Page and Template Work

- Homepage: hero, trust, problem, solution, modules, academy + software, use cases, dashboard preview, benefits, testimonial/case study, resources, FAQ, final CTA.
- Products: listing and detail template.
- Academy: listing and detail template.
- Solutions: persona listing and detail template.
- Blog: listing with categories/search/future pagination, detail with TOC, author box, related posts, CTA, schema.
- Demo: short conversion form with KVKK consent.
- Legal: KVKK, privacy, cookie policy, terms.
- Security: data security, role-based access, authorization, infrastructure, compliance messaging.
- 404: helpful conversion-oriented page.

## SEO Landing Page Targets

- `/okr-yazilimi`
- `/okr-yonetimi`
- `/performans-yonetimi`
- `/hedef-yonetimi`
- `/geri-bildirim-kulturu`
- `/1-1-gorusme`
- `/kpi-ve-okr`
- `/calisan-performans-yonetimi`
- `/surekli-performans-yonetimi`
- `/aksiyon-yonetimi`
- `/kulturel-donusum`
- `/liderlik-gelisimi`

## Production Checklist

- Keep indexing disabled until staging content, redirects, sitemap, and Search Console are ready.
- Install and configure multilingual plugin.
- Install and configure SEO plugin.
- Connect SMTP.
- Add real product screenshots and optimized WebP images.
- Replace starter copy with approved Turkish and English content.
- Add old-to-new 301 redirect map.
- Add analytics and conversion tracking.
- Run Lighthouse mobile checks after final assets are in place.
- Verify forms, noindex/index switch, sitemap, robots, canonical, and Open Graph tags before launch.

