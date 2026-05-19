const STRAPI_URL = process.env.STRAPI_URL || "http://localhost:1337";
const STRAPI_TOKEN = process.env.STRAPI_API_TOKEN || "";

const headers = {
  "Content-Type": "application/json",
  Authorization: `Bearer ${STRAPI_TOKEN}`,
};

async function createOrUpdate(endpoint, data, isSingleType = false) {
  const method = isSingleType ? "PUT" : "POST";
  const res = await fetch(`${STRAPI_URL}/api/${endpoint}`, {
    method,
    headers,
    body: JSON.stringify({ data }),
  });

  if (!res.ok) {
    const err = await res.text();
    console.warn(`  Warning: ${endpoint} - ${res.status}: ${err}`);
    return null;
  }
  return res.json();
}

async function seed() {
  console.log("Seeding Strapi with sample data...\n");

  if (!STRAPI_TOKEN) {
    console.error("ERROR: Set STRAPI_API_TOKEN environment variable");
    process.exit(1);
  }

  console.log("1. Site Settings (EN)...");
  await createOrUpdate("site-settings", {
    siteName: "Myliba",
    phone: "+90 553 986 86 99",
    email: "hello@myliba.com",
    whatsappNumber: "905539868699",
    addressUK: "Suite E2631, 82a James Carter Road, Mildenhall, Suffolk, IP28 7DE United Kingdom",
    addressTR: "Maslak Mah. Aos 55. Sk. 42 Maslak B Blok Sitesi No: 4 İç Kapı No: 542 Sarıyer /Istanbul",
    linkedinUrl: "https://www.linkedin.com/company/myliba/",
    youtubeUrl: "https://www.youtube.com/@mylibatr",
    instagramUrl: "https://www.instagram.com/mylibatr/",
    twitterUrl: "https://twitter.com/Myliba1",
    defaultSeoTitle: "Myliba - Next Generation Performance",
    defaultSeoDescription: "We create a high performance culture. Turn strategy into action with OKR methodology.",
    copyright: "Copyright © 2026 Myliba",
  }, true);

  console.log("2. Homepage (EN)...");
  await createOrUpdate("homepage", {
    heroTitle: "We Create a High Performance Culture!",
    heroSubtitle: "Turn Strategy into Action: Make the contribution transparent by turning strategy into goals and goals into actions.",
    heroCTAText: "Get a Quote",
    heroCTALink: "/en/contact",
    superpowersTitle: "For Employees Who Are Willing and Committed to the Goal;",
    superpowersSubtitle: "With Myliba's Three Superpowers, Performance And Potential Are Constantly Increased.",
    superpower1Title: "Declare your goal!",
    superpower1Text: "Declare your goal by knowing what you are working for and how you will contribute!",
    superpower2Title: "Clarify contribution and involvement!",
    superpower2Text: "Track who wants to achieve what and how much they contribute.",
    superpower3Title: "Celebrate results together!",
    superpower3Text: "See and appreciate the results achieved on time and celebrate together!",
    videoSectionTitle: "Videos",
    video1Title: "Watch alignment and progress",
    video1Url: "https://www.youtube.com/watch?v=d1eO_3qHXrQ",
    video2Title: "Unlock potential and manage talent",
    video2Url: "https://www.youtube.com/watch?v=jqnLdpfhKUI",
    video3Title: "See and appreciate results instantly",
    video3Url: "https://www.youtube.com/watch?v=73_Vu3wjEgA",
    ctaTitle: "How About Discovering Our Products?",
    ctaButtonText: "GET AN OFFER!",
    ctaButtonLink: "/en/contact",
    seoTitle: "Myliba - Next Generation Performance",
    seoDescription: "We create a high performance culture with OKR methodology.",
  }, true);

  console.log("3. Team Members...");
  const teamMembers = [
    { name: "Dilek Mete", title: "Managing Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach", order: 1 },
    { name: "Aysel Eker", title: "Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach", order: 2 },
    { name: "Huri Sankur", title: "People and Culture Advisor, Academy Coordinator, OKR Coach", order: 3 },
  ];
  for (const member of teamMembers) {
    await createOrUpdate("team-members", member);
  }

  console.log("4. Blog Categories...");
  const categories = [
    { name: "OKR", slug: "okr" },
    { name: "Culture", slug: "culture" },
    { name: "Strategy", slug: "strategy" },
  ];
  for (const cat of categories) {
    await createOrUpdate("blog-categories", cat);
  }

  console.log("5. Blog Posts...");
  const posts = [
    {
      title: "OKR Best Practices for 2024",
      slug: "okr-best-practices-2024",
      excerpt: "Learn the most effective OKR implementation strategies for your organization.",
      content: "<p>Objectives and Key Results (OKR) is a goal-setting framework that helps organizations define and track objectives and their outcomes.</p><p>In this article, we explore the most effective strategies for implementing OKRs in your organization.</p>",
      author: "Dilek Mete",
      publishedDate: "2024-03-15",
    },
    {
      title: "Building a High Performance Culture",
      slug: "building-high-performance-culture",
      excerpt: "Discover how to create and maintain a high-performance organizational culture.",
      content: "<p>A high-performance culture is characterized by behaviors and norms that lead an organization to achieve superior results.</p><p>Learn how to foster accountability, transparency, and continuous improvement.</p>",
      author: "Aysel Eker",
      publishedDate: "2024-03-10",
    },
    {
      title: "From Strategy to Action: A Complete Guide",
      slug: "strategy-to-action-guide",
      excerpt: "A step-by-step guide to turning your business strategy into actionable goals.",
      content: "<p>Many organizations struggle to translate their strategic vision into day-to-day actions.</p><p>This guide provides a framework for breaking down strategic objectives into measurable goals.</p>",
      author: "Huri Sankur",
      publishedDate: "2024-03-05",
    },
  ];
  for (const post of posts) {
    await createOrUpdate("blog-posts", post);
  }

  console.log("6. Events...");
  await createOrUpdate("events", {
    title: "OKR Workshop Istanbul",
    slug: "okr-workshop-istanbul",
    description: "<p>A hands-on workshop for implementing OKR in your organization.</p>",
    date: "2024-04-15T09:00:00.000Z",
    location: "Istanbul, Turkey",
  });
  await createOrUpdate("events", {
    title: "Culture Transformation Summit",
    slug: "culture-transformation-summit",
    description: "<p>Join industry leaders discussing the future of organizational culture.</p>",
    date: "2024-05-20T09:00:00.000Z",
    location: "London, UK",
  });

  console.log("7. Client Logos (placeholder names)...");
  const logos = ["TurkNet", "Telenity", "BtcTurk", "Company 4", "Company 5"];
  for (let i = 0; i < logos.length; i++) {
    await createOrUpdate("client-logos", { name: logos[i], order: i + 1 });
  }

  console.log("\n✓ Seed complete!");
}

seed().catch((err) => {
  console.error("Seed failed:", err);
  process.exit(1);
});
