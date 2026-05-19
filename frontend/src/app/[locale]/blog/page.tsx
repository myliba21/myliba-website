import { Container, SectionWrapper, Card, Badge } from "@/components/ui";
import Link from "next/link";

const mockPosts = [
  { id: 1, slug: "okr-best-practices", title: "OKR Best Practices for 2024", excerpt: "Learn the most effective OKR implementation strategies for your organization.", category: "OKR", date: "2024-03-15", author: "Dilek Mete" },
  { id: 2, slug: "high-performance-culture", title: "Building a High Performance Culture", excerpt: "Discover how to create and maintain a high-performance organizational culture.", category: "Culture", date: "2024-03-10", author: "Aysel Eker" },
  { id: 3, slug: "strategy-to-action", title: "From Strategy to Action: A Complete Guide", excerpt: "A step-by-step guide to turning your business strategy into actionable goals.", category: "Strategy", date: "2024-03-05", author: "Huri Sankur" },
];

export default async function BlogPage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;

  return (
    <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
      <Container>
        <h1 className="text-5xl md:text-7xl font-black mb-4 tracking-tight">Blog</h1>
        <p className="text-text-secondary mb-8 text-lg">Insights on OKR, performance management, and organizational culture.</p>

        <div className="flex gap-3 mb-10 flex-wrap">
          <Badge variant="primary">All</Badge>
          <Badge variant="neutral">OKR</Badge>
          <Badge variant="neutral">Culture</Badge>
          <Badge variant="neutral">Strategy</Badge>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {mockPosts.map((post) => (
            <Link key={post.id} href={`/${locale}/blog/${post.slug}`} className="group">
              <Card hoverable className="h-full">
                <div className="aspect-video bg-[radial-gradient(circle_at_25%_25%,rgba(91,124,250,0.26),transparent_35%),linear-gradient(135deg,#101523,#eef3ff)] rounded-2xl mb-4" />
                <Badge variant="accent" className="mb-3">{post.category}</Badge>
                <h2 className="text-lg font-black group-hover:text-primary transition-colors mb-2">
                  {post.title}
                </h2>
                <p className="text-sm text-text-secondary mb-3 leading-6">{post.excerpt}</p>
                <div className="flex items-center justify-between text-xs text-text-secondary font-semibold">
                  <span>{post.author}</span>
                  <span>{post.date}</span>
                </div>
              </Card>
            </Link>
          ))}
        </div>
      </Container>
    </SectionWrapper>
  );
}
