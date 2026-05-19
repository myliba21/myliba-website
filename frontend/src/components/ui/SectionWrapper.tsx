type SectionBackground = "white" | "surface" | "primary" | "dark";

interface SectionWrapperProps {
  children: React.ReactNode;
  className?: string;
  background?: SectionBackground;
  id?: string;
}

const bgStyles: Record<SectionBackground, string> = {
  white: "bg-background text-foreground",
  surface: "bg-surface text-foreground",
  primary: "bg-[linear-gradient(135deg,#111827,#172554_55%,#4c1d95)] text-white",
  dark: "bg-secondary text-white",
};

export function SectionWrapper({
  children,
  className = "",
  background = "white",
  id,
}: SectionWrapperProps) {
  return (
    <section id={id} className={`py-16 md:py-24 ${bgStyles[background]} ${className}`}>
      {children}
    </section>
  );
}
