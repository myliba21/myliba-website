"use client";

import { useState } from "react";
import { Container, SectionWrapper } from "@/components/ui";

const faqItems = [
  { q: "What is OKR?", a: "OKR (Objectives and Key Results) is a goal-setting framework used by organizations to define measurable goals and track their outcomes." },
  { q: "How does Myliba help with performance management?", a: "Myliba provides software tools and consulting services that help organizations align strategy with execution through OKR methodology and culture transformation." },
  { q: "Is Myliba suitable for small companies?", a: "Yes, Myliba serves organizations of all sizes, from startups to large enterprises." },
  { q: "How long does an OKR implementation take?", a: "Typical implementations take 2-3 months for initial setup and 2-3 OKR cycles to fully embed the methodology." },
  { q: "Do you offer training?", a: "Yes, our OKR Culture & Academy offers comprehensive training programs, workshops, and certification courses." },
];

export default function FAQPage() {
  const [openIndex, setOpenIndex] = useState<number | null>(null);

  return (
    <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
      <Container className="max-w-4xl">
        <h1 className="text-5xl md:text-7xl font-black mb-4 tracking-tight">Frequently Asked Questions</h1>
        <p className="text-text-secondary mb-10 text-lg">Find answers to common questions about Myliba and our services.</p>

        <div className="space-y-4">
          {faqItems.map((item, i) => (
            <div key={i} className="border border-border rounded-3xl overflow-hidden bg-white shadow-sm">
              <button
                onClick={() => setOpenIndex(openIndex === i ? null : i)}
                className="w-full flex items-center justify-between gap-4 px-6 py-5 text-left font-black hover:bg-surface transition-colors"
              >
                {item.q}
                <span className="grid h-9 w-9 flex-shrink-0 place-items-center rounded-full bg-surface text-primary">
                  {openIndex === i ? "-" : "+"}
                </span>
              </button>
              {openIndex === i && (
                <div className="px-6 pb-5 text-text-secondary text-sm leading-7">
                  {item.a}
                </div>
              )}
            </div>
          ))}
        </div>
      </Container>
    </SectionWrapper>
  );
}
