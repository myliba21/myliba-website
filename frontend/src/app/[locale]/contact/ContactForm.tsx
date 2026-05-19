"use client";

import { useState } from "react";
import { useTranslations } from "next-intl";
import { Button } from "@/components/ui";

export function ContactForm() {
  const t = useTranslations("contact");
  const [status, setStatus] = useState<"idle" | "loading" | "success" | "error">("idle");

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setStatus("loading");

    const form = e.currentTarget;
    const formData = new FormData(form);
    const data = {
      name: formData.get("name") as string,
      email: formData.get("email") as string,
      phone: formData.get("phone") as string,
      company: formData.get("company") as string,
      message: formData.get("message") as string,
      type: formData.get("type") as string,
    };

    try {
      const res = await fetch(
        `${process.env.NEXT_PUBLIC_STRAPI_URL || "http://localhost:1337"}/api/form-submissions`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ data }),
        }
      );

      if (!res.ok) throw new Error("Failed");
      setStatus("success");
      form.reset();
    } catch {
      setStatus("error");
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-5">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label htmlFor="name" className="mb-1.5 block text-sm font-bold">{t("name")}</label>
          <input id="name" name="name" type="text" required className="w-full rounded-2xl border border-border bg-surface/60 px-4 py-3 text-sm transition focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10" />
        </div>
        <div>
          <label htmlFor="email" className="mb-1.5 block text-sm font-bold">{t("email")}</label>
          <input id="email" name="email" type="email" required className="w-full rounded-2xl border border-border bg-surface/60 px-4 py-3 text-sm transition focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10" />
        </div>
        <div>
          <label htmlFor="phone" className="mb-1.5 block text-sm font-bold">{t("phone")}</label>
          <input id="phone" name="phone" type="tel" className="w-full rounded-2xl border border-border bg-surface/60 px-4 py-3 text-sm transition focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10" />
        </div>
        <div>
          <label htmlFor="company" className="mb-1.5 block text-sm font-bold">{t("company")}</label>
          <input id="company" name="company" type="text" className="w-full rounded-2xl border border-border bg-surface/60 px-4 py-3 text-sm transition focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10" />
        </div>
      </div>
      <div>
        <label htmlFor="type" className="mb-1.5 block text-sm font-bold">{t("type")}</label>
        <select id="type" name="type" className="w-full rounded-2xl border border-border bg-surface/60 px-4 py-3 text-sm transition focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10">
          <option value="contact">{t("typeContact")}</option>
          <option value="demo">{t("typeDemo")}</option>
        </select>
      </div>
      <div>
        <label htmlFor="message" className="mb-1.5 block text-sm font-bold">{t("message")}</label>
        <textarea id="message" name="message" rows={5} required className="w-full resize-none rounded-2xl border border-border bg-surface/60 px-4 py-3 text-sm transition focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10" />
      </div>

      {status === "success" && <p className="text-success font-medium">{t("success")}</p>}
      {status === "error" && <p className="text-accent font-medium">{t("error")}</p>}

      <Button type="submit" variant="primary" size="lg" disabled={status === "loading"}>
        {status === "loading" ? "..." : t("submit")}
      </Button>
    </form>
  );
}
