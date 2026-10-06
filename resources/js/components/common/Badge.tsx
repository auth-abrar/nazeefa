import React from 'react';

export type BadgeVariant = 'default' | 'success' | 'warning' | 'danger' | 'info';

interface BadgeProps {
  children: React.ReactNode;
  variant?: BadgeVariant;
  className?: string;
}

export const Badge: React.FC<BadgeProps> = ({
  children,
  variant = 'default',
  className = '',
}) => {
  const variantStyles = {
    default: 'bg-neutral-100 text-neutral-800 border-neutral-200',
    success: 'bg-[var(--success-surface)] text-[var(--success)] border-green-200',
    warning: 'bg-[var(--warning-surface)] text-[var(--warning)] border-amber-200',
    danger: 'bg-[var(--danger-surface)] text-[var(--danger)] border-red-200',
    info: 'bg-[var(--info-surface)] text-[var(--info)] border-blue-200',
  };

  return (
    <span
      className={`inline-flex items-center px-2 py-0.5 rounded-[var(--radius-pill)] text-xs font-medium border ${variantStyles[variant]} ${className}`}
    >
      {children}
    </span>
  );
};
