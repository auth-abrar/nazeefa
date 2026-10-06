import React from 'react';

export type ButtonVariant = 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger';
export type ButtonSize = 'sm' | 'md' | 'lg';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: ButtonVariant;
  size?: ButtonSize;
  isLoading?: boolean;
}

export const Button: React.FC<ButtonProps> = ({
  children,
  variant = 'primary',
  size = 'md',
  isLoading = false,
  className = '',
  disabled,
  ...props
}) => {
  const baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none rounded-[var(--radius-md)]';

  const sizeClasses = {
    sm: 'text-xs px-3 py-1.5',
    md: 'text-sm px-4 py-2',
    lg: 'text-base px-6 py-3',
  };

  const variantClasses = {
    primary: 'bg-[#111111] text-white hover:bg-neutral-800 focus:ring-neutral-900',
    secondary: 'bg-[var(--surface-muted)] text-[var(--foreground)] hover:bg-neutral-200 focus:ring-neutral-400',
    outline: 'border border-[var(--border)] text-[var(--foreground)] hover:bg-[var(--surface)] focus:ring-neutral-400',
    ghost: 'text-[var(--foreground)] hover:bg-[var(--surface)] focus:ring-neutral-400',
    danger: 'bg-[var(--danger)] text-white hover:bg-red-800 focus:ring-red-600',
  };

  return (
    <button
      className={`${baseClasses} ${sizeClasses[size]} ${variantClasses[variant]} ${className}`}
      disabled={disabled || isLoading}
      {...props}
    >
      {isLoading ? (
        <span className="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin mr-2" />
      ) : null}
      {children}
    </button>
  );
};
