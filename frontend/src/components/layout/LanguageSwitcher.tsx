"use client";

import { useLocale } from "next-intl";
import { useRouter, usePathname } from "next/navigation";
import { routing } from "@/i18n/routing";

const flagEmoji: Record<string, string> = {
  en: "🇬🇧",
  tr: "🇹🇷",
};

export function LanguageSwitcher() {
  const locale = useLocale();
  const router = useRouter();
  const pathname = usePathname();

  function switchLocale(newLocale: string) {
    const segments = pathname.split("/");
    segments[1] = newLocale;
    router.push(segments.join("/"));
  }

  const otherLocale = routing.locales.find((l) => l !== locale) ?? routing.defaultLocale;

  return (
    <button
      onClick={() => switchLocale(otherLocale)}
      className="flex items-center gap-1.5 rounded-full border border-border/70 bg-white/70 px-3 py-2 text-sm transition-colors hover:bg-surface"
      aria-label={`Switch to ${otherLocale}`}
    >
      <span className="text-lg">{flagEmoji[otherLocale]}</span>
      <span className="hidden sm:inline uppercase font-medium">{otherLocale}</span>
    </button>
  );
}
