import { Container, SectionWrapper } from "@/components/ui";

export default function SecurityPage() {
  return (
    <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
      <Container className="max-w-4xl">
        <h1 className="text-5xl md:text-7xl font-black mb-6 tracking-tight">Security</h1>
        <div className="rounded-[2rem] border border-border bg-white p-8 shadow-sm">
          <div className="prose prose-lg max-w-none text-text-secondary">
            <p>
              At Myliba, we take the security of your data seriously. Our platform is designed with
              enterprise-grade security features to protect your organization&apos;s information.
            </p>
            <h2 className="text-foreground">Data Protection</h2>
            <ul>
              <li>End-to-end encryption for all data in transit and at rest</li>
              <li>Regular security audits and penetration testing</li>
              <li>GDPR and KVKK compliant data handling</li>
              <li>Secure cloud infrastructure with redundancy</li>
            </ul>
            <h2 className="text-foreground">Access Control</h2>
            <ul>
              <li>Role-based access control (RBAC)</li>
              <li>Multi-factor authentication support</li>
              <li>SSO integration capabilities</li>
              <li>Audit logging for all actions</li>
            </ul>
            <h2 className="text-foreground">Compliance</h2>
            <p>
              Our platform is designed to meet international security standards and regulatory
              requirements, ensuring your data is handled responsibly.
            </p>
          </div>
        </div>
      </Container>
    </SectionWrapper>
  );
}
