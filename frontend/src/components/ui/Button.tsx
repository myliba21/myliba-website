import Link from "next/link";

type ButtonVariant = "primary" | "secondary" | "accent" | "ghost";
type ButtonSize = "sm" | "md" | "lg";

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: ButtonVariant;
  size?: ButtonSize;
  href?: string;
}

const variantStyles: Record<ButtonVariant, string> = {
  primary: "bg-[linear-gradient(135deg,#5b7cfa,#8b5cf6)] text-white shadow-[0_16px_36px_rgba(91,124,250,0.28)] hover:shadow-[0_18px_44px_rgba(91,124,250,0.36)]",
  secondary: "border border-border bg-white/70 text-foreground hover:border-primary/40 hover:bg-white",
  accent: "bg-[linear-gradient(135deg,#16c7f2,#5b7cfa)] text-white shadow-[0_16px_36px_rgba(22,199,242,0.22)] hover:shadow-[0_18px_44px_rgba(22,199,242,0.32)]",
  ghost: "text-foreground hover:bg-white/70",
};

const sizeStyles: Record<ButtonSize, string> = {
  sm: "px-4 py-2 text-sm",
  md: "px-6 py-3 text-base",
  lg: "px-8 py-4 text-lg",
};

export function Button({
  variant = "primary",
  size = "md",
  href,
  className = "",
  children,
  ...props
}: ButtonProps) {
  const classes = `inline-flex items-center justify-center rounded-full font-semibold transition-all duration-300 cursor-pointer hover:-translate-y-0.5 ${variantStyles[variant]} ${sizeStyles[size]} ${className}`;

  if (href) {
    return (
      <Link href={href} className={classes}>
        {children}
      </Link>
    );
  }

  return (
    <button className={classes} {...props}>
      {children}
    </button>
  );
}
