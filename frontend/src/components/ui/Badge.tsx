type BadgeVariant = "primary" | "accent" | "neutral";

interface BadgeProps {
  children: React.ReactNode;
  variant?: BadgeVariant;
  className?: string;
}

const badgeStyles: Record<BadgeVariant, string> = {
  primary: "bg-primary/10 text-primary",
  accent: "bg-accent/10 text-accent",
  neutral: "bg-surface text-text-secondary",
};

export function Badge({ children, variant = "primary", className = "" }: BadgeProps) {
  return (
    <span className={`inline-block rounded-full px-3 py-1 text-sm font-medium ${badgeStyles[variant]} ${className}`}>
      {children}
    </span>
  );
}
