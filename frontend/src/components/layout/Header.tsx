"use client";

import Link from "next/link";
import { useLocale, useTranslations } from "next-intl";
import { useState } from "react";
import { Container } from "@/components/ui";
import { LanguageSwitcher } from "./LanguageSwitcher";

const navLinks = [
  { key: "products", href: "/our-products" },
  { key: "academy", href: "/okr-culture-academy" },
  { key: "cultureAnalysis", href: "/culture-analysis" },
  { key: "ethicsCounsel", href: "/ethics-counsel" },
  { key: "blog", href: "/blog" },
  { key: "events", href: "/events" },
] as const;

export function Header() {
  const t = useTranslations("nav");
  const tCommon = useTranslations("common");
  const locale = useLocale();
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <header className="sticky top-0 z-50 border-b border-white/60 bg-white/78 backdrop-blur-2xl">
      <Container className="flex h-16 items-center justify-between md:h-20">
        <Link href={`/${locale}`} className="flex flex-shrink-0 items-center gap-3">
          <span className="grid h-9 w-9 place-items-center rounded-xl bg-[linear-gradient(135deg,#101523,#5b7cfa)] text-sm font-black text-white shadow-lg shadow-primary/20">
            M
          </span>
          <span className="text-2xl font-black tracking-tight text-foreground">Myliba</span>
        </Link>

        <nav className="hidden items-center gap-1 rounded-full border border-border/70 bg-white/70 p-1 shadow-sm lg:flex">
          {navLinks.map((link) => (
            <Link
              key={link.key}
              href={`/${locale}${link.href}`}
              className="rounded-full px-4 py-2 text-sm font-semibold text-text-secondary transition-colors hover:bg-surface hover:text-foreground"
            >
              {t(link.key)}
            </Link>
          ))}
        </nav>

        <div className="flex items-center gap-3">
          <LanguageSwitcher />
          <Link
            href={`/${locale}/contact`}
            className="hidden items-center rounded-full bg-foreground px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition-all hover:-translate-y-0.5 hover:bg-primary sm:inline-flex"
          >
            {tCommon("requestDemo")}
          </Link>
          <button
            onClick={() => setMenuOpen(!menuOpen)}
            className="rounded-full p-2 text-foreground hover:bg-surface lg:hidden"
            aria-label="Toggle menu"
          >
            <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              {menuOpen ? (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              ) : (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              )}
            </svg>
          </button>
        </div>
      </Container>

      {menuOpen && (
        <div className="border-t border-border/70 bg-white/95 backdrop-blur-xl lg:hidden">
          <Container className="py-4 flex flex-col gap-3">
            {navLinks.map((link) => (
              <Link
                key={link.key}
                href={`/${locale}${link.href}`}
                onClick={() => setMenuOpen(false)}
                className="rounded-2xl px-3 py-2 text-base font-semibold text-foreground hover:bg-surface"
              >
                {t(link.key)}
              </Link>
            ))}
            <Link
              href={`/${locale}/contact`}
              onClick={() => setMenuOpen(false)}
              className="mt-2 inline-flex items-center justify-center rounded-full bg-[linear-gradient(135deg,#5b7cfa,#8b5cf6)] px-4 py-3 font-semibold text-white"
            >
              {tCommon("requestDemo")}
            </Link>
          </Container>
        </div>
      )}
    </header>
  );
}
