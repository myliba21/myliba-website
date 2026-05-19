const WP_API = "https://myliba.com/wp-json/wp/v2";
const STRAPI_URL = process.env.STRAPI_URL || "http://localhost:1337";
const STRAPI_TOKEN = process.env.STRAPI_API_TOKEN || "";

async function fetchWPPosts(page = 1, perPage = 100) {
  const res = await fetch(`${WP_API}/posts?page=${page}&per_page=${perPage}&_embed`);
  if (!res.ok) {
    if (res.status === 400) return [];
    throw new Error(`WP API error: ${res.status}`);
  }
  return res.json();
}

async function uploadImageToStrapi(imageUrl) {
  try {
    const imgRes = await fetch(imageUrl);
    if (!imgRes.ok) return null;

    const buffer = await imgRes.arrayBuffer();
    const filename = imageUrl.split("/").pop().split("?")[0];

    const formData = new FormData();
    formData.append("files", new Blob([buffer]), filename);

    const uploadRes = await fetch(`${STRAPI_URL}/api/upload`, {
      method: "POST",
      headers: { Authorization: `Bearer ${STRAPI_TOKEN}` },
      body: formData,
    });

    if (!uploadRes.ok) {
      console.warn(`  Failed to upload image: ${filename}`);
      return null;
    }

    const [uploaded] = await uploadRes.json();
    return uploaded.id;
  } catch (err) {
    console.warn(`  Image upload failed for ${imageUrl}: ${err.message}`);
    return null;
  }
}

function stripHtmlToPlainText(html) {
  return html.replace(/<[^>]*>/g, "").trim();
}

function generateSlug(title) {
  return title
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, "")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-")
    .slice(0, 200);
}

async function createStrapiPost(postData) {
  const res = await fetch(`${STRAPI_URL}/api/blog-posts`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${STRAPI_TOKEN}`,
    },
    body: JSON.stringify({ data: postData }),
  });

  if (!res.ok) {
    const err = await res.text();
    throw new Error(`Strapi error: ${res.status} - ${err}`);
  }

  return res.json();
}

async function migrate() {
  console.log("Starting WordPress → Strapi blog migration...");
  console.log(`WP API: ${WP_API}`);
  console.log(`Strapi: ${STRAPI_URL}`);

  if (!STRAPI_TOKEN) {
    console.error("ERROR: Set STRAPI_API_TOKEN environment variable");
    process.exit(1);
  }

  let page = 1;
  let totalMigrated = 0;
  let totalFailed = 0;

  while (true) {
    console.log(`\nFetching page ${page}...`);
    const posts = await fetchWPPosts(page);

    if (!posts.length) {
      console.log("No more posts.");
      break;
    }

    console.log(`Found ${posts.length} posts on page ${page}`);

    for (const post of posts) {
      const title = stripHtmlToPlainText(post.title.rendered);
      console.log(`\nMigrating: "${title}"`);

      try {
        let featuredImageId = null;
        const featuredMedia = post._embedded?.["wp:featuredmedia"]?.[0];
        if (featuredMedia?.source_url) {
          console.log(`  Uploading featured image...`);
          featuredImageId = await uploadImageToStrapi(featuredMedia.source_url);
        }

        const slug = post.slug || generateSlug(title);
        const excerpt = stripHtmlToPlainText(post.excerpt?.rendered || "");

        const postData = {
          title,
          slug,
          content: post.content.rendered,
          excerpt: excerpt.slice(0, 500),
          publishedDate: post.date?.split("T")[0],
          author: post._embedded?.author?.[0]?.name || "Myliba",
          ...(featuredImageId && { featuredImage: featuredImageId }),
        };

        await createStrapiPost(postData);
        console.log(`  ✓ Migrated successfully`);
        totalMigrated++;
      } catch (err) {
        console.error(`  ✗ Failed: ${err.message}`);
        totalFailed++;
      }
    }

    page++;
  }

  console.log(`\n${"=".repeat(50)}`);
  console.log(`Migration complete!`);
  console.log(`  Migrated: ${totalMigrated}`);
  console.log(`  Failed: ${totalFailed}`);
}

migrate().catch((err) => {
  console.error("Migration failed:", err);
  process.exit(1);
});
