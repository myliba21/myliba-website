import { Container, SectionWrapper } from "@/components/ui";

export default function PrivacyPolicyPage() {
  return (
    <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
      <Container className="max-w-4xl">
        <h1 className="text-5xl md:text-7xl font-black mb-6 tracking-tight">Privacy Policy</h1>
        <div className="rounded-[2rem] border border-border bg-white p-8 shadow-sm">
          <div className="prose prose-lg max-w-none text-text-secondary">
            <p><strong>Last updated:</strong> March 2024</p>

            <h2 className="text-foreground">1. Information We Collect</h2>
            <p>
              We collect information you provide directly, such as when you create an account,
              contact us, or submit a form. This may include your name, email address, phone
              number, company name, and message content.
            </p>

            <h2 className="text-foreground">2. How We Use Information</h2>
            <p>
              We use the information to provide and improve our services, communicate with you,
              and ensure the security of our platform.
            </p>

            <h2 className="text-foreground">3. Data Sharing</h2>
            <p>
              We do not sell your personal information. We may share data with service providers
              who assist in operating our platform, subject to confidentiality agreements.
            </p>

            <h2 className="text-foreground">4. Data Retention</h2>
            <p>
              We retain your data for as long as necessary to provide our services and comply
              with legal obligations.
            </p>

            <h2 className="text-foreground">5. Your Rights</h2>
            <p>
              You have the right to access, correct, or delete your personal data. Contact us
              at hello@myliba.com for any data-related requests.
            </p>

            <h2 className="text-foreground">6. Contact</h2>
            <p>
              For questions about this policy, contact us at{" "}
              <a href="mailto:hello@myliba.com" className="text-primary font-bold">hello@myliba.com</a>.
            </p>
          </div>
        </div>
      </Container>
    </SectionWrapper>
  );
}
