import { Container, SectionWrapper, Badge, Button } from "@/components/ui";
import { notFound } from "next/navigation";

const mockPosts: Record<string, { title: string; content: string; category: string; date: string; author: string }> = {
  "okr-best-practices": {
    title: "OKR Best Practices for 2024",
    content: "<p>Objectives and Key Results (OKR) is a goal-setting framework that helps organizations define and track objectives and their outcomes.</p><p>In this article, we explore the most effective strategies for implementing OKRs in your organization, including alignment, tracking, and review cycles.</p>",
    category: "OKR",
    date: "2024-03-15",
    author: "Dilek Mete",
  },
  "high-performance-culture": {
    title: "Building a High Performance Culture",
    content: "<p>A high-performance culture is characterized by a set of behaviors and norms that lead an organization to achieve superior results.</p><p>Learn how to foster accountability, transparency, and continuous improvement in your teams.</p>",
    category: "Culture",
    date: "2024-03-10",
    author: "Aysel Eker",
  },
  "strategy-to-action": {
    title: "From Strategy to Action: A Complete Guide",
    content: "<p>Many organizations struggle to translate their strategic vision into day-to-day actions.</p><p>This guide provides a framework for breaking down strategic objectives into measurable goals and actionable tasks.</p>",
    category: "Strategy",
    date: "2024-03-05",
    author: "Huri Sankur",
  },
};

export default async function BlogDetailPage({
  params,
}: {
  params: Promise<{ locale: string; slug: string }>;
}) {
  const { locale, slug } = await params;
  const post = mockPosts[slug];

  if (!post) {
    notFound();
  }

  return (
    <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
      <Container className="max-w-4xl">
        <Badge variant="accent" className="mb-4">{post.category}</Badge>
        <h1 className="text-4xl md:text-6xl font-black mb-4 tracking-tight">{post.title}</h1>
        <div className="flex items-center gap-4 text-sm text-text-secondary mb-8 font-semibold">
          <span>{post.author}</span>
          <span>/</span>
          <span>{post.date}</span>
        </div>
        <div className="rounded-[2rem] border border-border bg-white p-8 shadow-sm">
          <div
            className="prose prose-lg max-w-none"
            dangerouslySetInnerHTML={{ __html: post.content }}
          />
        </div>
        <div className="mt-12 pt-8 border-t border-border">
          <Button variant="secondary" href={`/${locale}/blog`}>
            Back to Blog
          </Button>
        </div>
      </Container>
    </SectionWrapper>
  );
}
