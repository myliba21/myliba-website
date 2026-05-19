import { Container, SectionWrapper } from "@/components/ui";
import { ContactForm } from "./ContactForm";

export default function ContactPage() {
  return (
    <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
      <Container className="max-w-5xl">
        <h1 className="text-5xl md:text-7xl font-black mb-4 tracking-tight">Contact Us</h1>
        <p className="text-text-secondary mb-10 text-lg">
          Get in touch with us to learn more about Myliba or request a demo.
        </p>

        <div className="grid grid-cols-1 lg:grid-cols-5 gap-10 rounded-[2rem] border border-border bg-white p-6 md:p-8 shadow-sm">
          <div className="lg:col-span-3">
            <ContactForm />
          </div>
          <div className="lg:col-span-2 space-y-6 rounded-[1.5rem] bg-surface p-6">
            <div>
              <h3 className="font-black mb-2">United Kingdom</h3>
              <p className="text-sm text-text-secondary leading-6">
                Suite E2631, 82a James Carter Road, Mildenhall, Suffolk, IP28 7DE
              </p>
            </div>
            <div>
              <h3 className="font-black mb-2">Istanbul, Turkey</h3>
              <p className="text-sm text-text-secondary leading-6">
                Maslak Mah. Aos 55. Sk. 42 Maslak B Blok Sitesi No: 4 İç Kapı No: 542 Sarıyer /Istanbul
              </p>
            </div>
            <div>
              <h3 className="font-black mb-2">Phone</h3>
              <a href="tel:+905539868699" className="text-sm text-primary font-bold hover:underline">+90 553 986 86 99</a>
            </div>
            <div>
              <h3 className="font-black mb-2">Email</h3>
              <a href="mailto:hello@myliba.com" className="text-sm text-primary font-bold hover:underline">hello@myliba.com</a>
            </div>
          </div>
        </div>
      </Container>
    </SectionWrapper>
  );
}
