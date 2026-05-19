import { Container, SectionWrapper, Button } from "@/components/ui";

export default function EthicsCounselPage() {
  return (
    <>
      <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
        <Container className="max-w-4xl">
          <h1 className="text-5xl md:text-7xl font-black mb-5 tracking-tight">Ethics Counsel</h1>
          <p className="text-lg leading-8 text-text-secondary mb-8">
            A safe and confidential channel for reporting ethical concerns, violations, and compliance issues within your organization.
          </p>
          <div className="rounded-[2rem] border border-border bg-white p-8 shadow-sm">
            <div className="prose prose-lg max-w-none text-text-secondary">
              <p>
                Our Ethics Counsel service provides organizations with a structured, confidential reporting system.
                Employees can report concerns about ethical violations, compliance issues, and misconduct through
                a secure and anonymous channel.
              </p>
              <h2 className="text-foreground">Key Features</h2>
              <ul>
                <li>Confidential and anonymous reporting</li>
                <li>Structured investigation process</li>
                <li>Compliance with international standards</li>
                <li>Regular reporting to management</li>
                <li>Follow-up and resolution tracking</li>
              </ul>
              <h2 className="text-foreground">Why Ethics Matter</h2>
              <p>
                A strong ethical foundation builds trust, improves employee engagement, and protects
                your organization from risks. Our service helps create a culture where ethical behavior
                is valued and concerns are addressed promptly.
              </p>
            </div>
          </div>
          <div className="mt-8">
            <Button variant="primary" size="lg" href="/en/contact">Learn More</Button>
          </div>
        </Container>
      </SectionWrapper>
    </>
  );
}
