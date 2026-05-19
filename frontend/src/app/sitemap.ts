import type { MetadataRoute } from "next";

const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL || "https://myliba.com";

const staticPages = [
  "",
  "/our-products",
  "/okr-culture-academy",
  "/culture-analysis",
  "/ethics-counsel",
  "/blog",
  "/events",
  "/contact",
  "/our-story",
  "/faq",
  "/security",
  "/privacy-policy",
];

const locales = ["en", "tr"];

export default function sitemap(): MetadataRoute.Sitemap {
  const entries: MetadataRoute.Sitemap = [];

  for (const locale of locales) {
    for (const page of staticPages) {
      entries.push({
        url: `${BASE_URL}/${locale}${page}`,
        lastModified: new Date(),
        changeFrequency: page === "/blog" ? "daily" : "weekly",
        priority: page === "" ? 1.0 : 0.8,
      });
    }
  }

  return entries;
}
