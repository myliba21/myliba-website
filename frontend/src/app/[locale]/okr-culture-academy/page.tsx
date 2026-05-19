import { Container, SectionWrapper, Card, Button } from "@/components/ui";

export default function OKRAcademyPage() {
  return (
    <>
      <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
        <Container>
          <h1 className="text-5xl md:text-7xl font-black mb-5 tracking-tight">OKR Culture & Academy</h1>
          <p className="text-lg leading-8 text-text-secondary max-w-2xl mb-12">
            Learn the art and science of Objectives and Key Results. Our academy provides
            comprehensive training for leaders, managers, and teams.
          </p>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="surface">
        <Container>
          <h2 className="text-3xl md:text-5xl font-black text-center mb-12 tracking-tight">Our Methodology</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              { title: "OKR Foundation", text: "Understand the fundamentals of OKR methodology and how it drives organizational alignment." },
              { title: "Culture Transformation", text: "Learn how to shift organizational culture towards transparency, accountability and high performance." },
              { title: "Coaching & Mentoring", text: "Get personalized guidance from certified OKR coaches and executive coaches." },
            ].map((item, index) => (
              <Card key={item.title} hoverable className="text-center min-h-64">
                <div className="w-14 h-14 bg-gradient-to-br from-primary/15 to-accent/15 rounded-2xl mx-auto mb-5 flex items-center justify-center text-primary font-black">
                  {index + 1}
                </div>
                <h3 className="text-xl font-black mb-3">{item.title}</h3>
                <p className="text-text-secondary leading-7">{item.text}</p>
              </Card>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="primary">
        <Container className="text-center">
          <h2 className="text-3xl md:text-5xl font-black mb-4">Start Your OKR Journey</h2>
          <p className="text-white/80 mb-8 max-w-xl mx-auto">
            Join organizations that have transformed their performance with our academy.
          </p>
          <Button variant="accent" size="lg" href="/en/contact">Get Started</Button>
        </Container>
      </SectionWrapper>
    </>
  );
}
