import React from 'react';

interface AdminKpiCardProps {
  title: string;
  value: string | number;
  subtitle?: string;
  badge?: string;
  badgeType?: 'default' | 'success' | 'warning' | 'danger';
}

export const AdminKpiCard: React.FC<AdminKpiCardProps> = ({
  title,
  value,
  subtitle,
  badge,
  badgeType = 'default',
}) => {
  const badgeStyles = {
    default: 'bg-neutral-100 text-neutral-800',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-amber-100 text-amber-800',
    danger: 'bg-red-100 text-red-800',
  };

  return (
    <div className="bg-white p-5 rounded-[var(--radius-lg)] border border-neutral-200 shadow-sm flex flex-col justify-between">
      <div className="flex items-center justify-between">
        <span className="text-xs font-semibold uppercase tracking-wider text-neutral-500">{title}</span>
        {badge && (
          <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${badgeStyles[badgeType]}`}>
            {badge}
          </span>
        )}
      </div>
      <div className="mt-4">
        <h3 className="text-2xl font-bold font-mono tracking-tight text-neutral-900">{value}</h3>
        {subtitle && <p className="text-xs text-neutral-500 mt-1">{subtitle}</p>}
      </div>
    </div>
  );
};
