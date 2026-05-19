import { Container, SectionWrapper, Button } from "@/components/ui";

const products = [
  {
    title: "OKR and Culture Software",
    description: "Turn strategy into action with our OKR management platform. Set objectives, track key results, and align your entire organization towards common goals.",
    features: ["Goal alignment across teams", "Real-time progress tracking", "1-on-1 meeting tools", "360° feedback system"],
  },
  {
    title: "OKR and Culture Academy",
    description: "Comprehensive training programs for leaders and teams. Learn to implement OKR methodology and build a high-performance culture.",
    features: ["Customized training programs", "OKR certification", "Executive coaching", "Workshop facilitation"],
  },
  {
    title: "Culture Analysis",
    description: "Understand your organizational culture with data-driven insights. Identify strengths, gaps, and opportunities for transformation.",
    features: ["Culture assessment surveys", "Data analytics dashboard", "Benchmarking reports", "Action recommendations"],
  },
];

export default function ProductsPage() {
  return (
    <>
      <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
        <Container>
          <h1 className="text-5xl md:text-7xl font-black mb-5 tracking-tight">Our Products</h1>
          <p className="text-lg leading-8 text-text-secondary max-w-2xl mb-16">
            Discover our suite of tools and services designed to help organizations build
            high-performance cultures and achieve strategic objectives.
          </p>

          <div className="space-y-16">
            {products.map((product, i) => (
              <div key={product.title} className="grid grid-cols-1 md:grid-cols-2 gap-10 items-center rounded-[2rem] border border-border bg-white p-6 shadow-sm">
                <div className={i % 2 === 1 ? "md:order-2" : ""}>
                  <h2 className="text-3xl font-black mb-4">{product.title}</h2>
                  <p className="text-text-secondary mb-6 leading-7">{product.description}</p>
                  <ul className="space-y-3 mb-6">
                    {product.features.map((f) => (
                      <li key={f} className="flex items-center gap-3 text-sm font-semibold">
                        <span className="w-2 h-2 bg-accent rounded-full flex-shrink-0" />
                        {f}
                      </li>
                    ))}
                  </ul>
                  <Button variant="primary" href="/en/contact">Learn More</Button>
                </div>
                <div className={`aspect-[4/3] bg-surface rounded-[1.5rem] border border-border p-5 ${i % 2 === 1 ? "md:order-1" : ""}`}>
                  <div className="h-full rounded-2xl bg-white p-4 shadow-sm">
                    <div className="mb-5 h-3 w-28 rounded-full bg-primary/20" />
                    <div className="grid grid-cols-2 gap-3">
                      <div className="h-24 rounded-2xl bg-surface" />
                      <div className="h-24 rounded-2xl bg-surface" />
                    </div>
                    <div className="mt-4 space-y-3">
                      <div className="h-3 rounded-full bg-primary/15" />
                      <div className="h-3 w-4/5 rounded-full bg-accent/15" />
                      <div className="h-3 w-2/3 rounded-full bg-success/15" />
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="primary">
        <Container className="text-center">
          <h2 className="text-3xl md:text-5xl font-black mb-4">Ready to Transform Your Organization?</h2>
          <p className="text-white/80 mb-8">Get started with a personalized demo today.</p>
          <Button variant="accent" size="lg" href="/en/contact">Request a Demo</Button>
        </Container>
      </SectionWrapper>
    </>
  );
}
