import { Container, SectionWrapper, Button } from "@/components/ui";

export default function CultureAnalysisPage() {
  return (
    <>
      <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
        <Container>
          <h1 className="text-5xl md:text-7xl font-black mb-5 tracking-tight">Culture Analysis</h1>
          <p className="text-lg leading-8 text-text-secondary max-w-2xl mb-12">
            Understand your organizational culture with data-driven insights.
            Identify strengths, gaps, and opportunities for meaningful transformation.
          </p>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="surface">
        <Container>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-5xl font-black mb-6 tracking-tight">How It Works</h2>
              <div className="space-y-5">
                {[
                  { step: "01", title: "Assessment", text: "Comprehensive culture survey tailored to your organization." },
                  { step: "02", title: "Analysis", text: "Deep data analysis revealing cultural patterns and insights." },
                  { step: "03", title: "Reporting", text: "Detailed benchmarking reports with actionable recommendations." },
                  { step: "04", title: "Action Plan", text: "Customized transformation roadmap for your organization." },
                ].map((item) => (
                  <div key={item.step} className="flex gap-4 rounded-2xl border border-border bg-white p-5 shadow-sm">
                    <span className="text-2xl font-black text-primary/30">{item.step}</span>
                    <div>
                      <h3 className="font-black">{item.title}</h3>
                      <p className="text-sm text-text-secondary leading-6">{item.text}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            <div className="aspect-square bg-white rounded-[2rem] border border-border p-6 shadow-sm">
              <div className="h-full rounded-[1.5rem] bg-[#101523] p-5 text-white">
                <p className="text-xs uppercase tracking-[0.22em] text-white/40 font-bold">Analysis Dashboard</p>
                <div className="mt-8 grid grid-cols-2 gap-4">
                  {[86, 74, 68, 91].map((value) => (
                    <div key={value} className="rounded-2xl bg-white/[0.07] p-4">
                      <div className="text-3xl font-black">{value}%</div>
                      <div className="mt-3 h-2 rounded-full bg-white/10">
                        <div className="h-full rounded-full bg-gradient-to-r from-cyan-300 to-primary" style={{ width: `${value}%` }} />
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="primary">
        <Container className="text-center">
          <h2 className="text-3xl md:text-5xl font-black mb-4">Ready to Understand Your Culture?</h2>
          <Button variant="accent" size="lg" href="/en/contact">Request Analysis</Button>
        </Container>
      </SectionWrapper>
    </>
  );
}
