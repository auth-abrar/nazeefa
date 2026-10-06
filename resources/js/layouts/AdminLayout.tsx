import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { 
  LayoutDashboard, 
  ShoppingBag, 
  Boxes, 
  Truck, 
  Palette, 
  Users, 
  Settings, 
  Layers,
  Menu,
  X
} from 'lucide-react';

interface AdminLayoutProps {
  children: React.ReactNode;
}

export const AdminLayout: React.FC<AdminLayoutProps> = ({ children }) => {
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  const navItems = [
    { name: 'Dashboard', href: '/admin', icon: LayoutDashboard },
    { name: 'Orders Desk', href: '/admin/orders', icon: ShoppingBag },
    { name: 'Inventory & Stock', href: '/admin/inventory', icon: Boxes },
    { name: 'Shipments & Couriers', href: '/admin/shipments', icon: Truck },
    { name: 'Custom POD Pipeline', href: '/admin/pod', icon: Palette },
    { name: 'Suppliers & Dropship', href: '/admin/suppliers', icon: Layers },
    { name: 'Customers & CRM', href: '/admin/customers', icon: Users },
    { name: 'Operations Settings', href: '/admin/settings', icon: Settings },
  ];

  return (
    <div className="min-h-screen bg-neutral-100 flex text-neutral-900 font-sans antialiased">
      {/* Sidebar Desktop */}
      <aside className="hidden lg:flex flex-col w-64 bg-[#111111] text-white border-r border-neutral-800">
        <div className="h-16 flex items-center px-6 border-b border-neutral-800">
          <span className="font-serif text-lg font-bold tracking-widest text-white uppercase">
            NAZEEFA <span className="text-red-500 font-sans text-xs font-semibold ml-1">OS</span>
          </span>
        </div>

        <nav className="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
          {navItems.map((item) => {
            const Icon = item.icon;
            return (
              <Link
                key={item.name}
                href={item.href}
                className="flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius-md)] text-xs font-medium tracking-wide uppercase text-neutral-300 hover:text-white hover:bg-neutral-800 transition-colors"
              >
                <Icon className="w-4 h-4 text-neutral-400" />
                <span>{item.name}</span>
              </Link>
            );
          })}
        </nav>

        <div className="p-4 border-t border-neutral-800 text-xs text-neutral-400">
          <p className="font-mono text-[11px]">Dhaka Central Hub</p>
          <p className="text-[10px] text-neutral-500 mt-0.5">Hostinger Production Ready</p>
        </div>
      </aside>

      {/* Main Container */}
      <div className="flex-1 flex flex-col min-w-0 overflow-hidden">
        {/* Top Navbar */}
        <header className="h-16 bg-white border-b border-neutral-200 px-6 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <button
              onClick={() => setIsSidebarOpen(true)}
              className="lg:hidden p-1.5 text-neutral-600 hover:text-black"
            >
              <Menu className="w-5 h-5" />
            </button>
            <h2 className="text-sm font-semibold text-neutral-800 tracking-wide uppercase">
              BusinessOS Operations
            </h2>
          </div>

          <div className="flex items-center gap-4">
            <Link
              href="/"
              target="_blank"
              className="text-xs font-semibold uppercase tracking-wider text-neutral-600 hover:text-black border border-neutral-300 rounded px-3 py-1.5"
            >
              Live Storefront ↗
            </Link>
          </div>
        </header>

        {/* Content Area */}
        <main className="flex-1 p-6 sm:p-8 overflow-y-auto">{children}</main>
      </div>
    </div>
  );
};
