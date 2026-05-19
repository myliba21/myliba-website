interface CardProps {
  children: React.ReactNode;
  className?: string;
  hoverable?: boolean;
}

export function Card({ children, className = "", hoverable = false }: CardProps) {
  const hoverClass = hoverable ? "hover:shadow-[0_24px_80px_rgba(13,24,45,0.12)] hover:-translate-y-1 transition-all duration-300" : "";
  return (
    <div className={`rounded-2xl bg-white border border-border/80 shadow-sm p-6 ${hoverClass} ${className}`}>
      {children}
    </div>
  );
}
