import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { ShoppingBag, Search, Menu, X, Heart, Globe } from 'lucide-react';

interface StorefrontLayoutProps {
  children: React.ReactNode;
}

export const StorefrontLayout: React.FC<StorefrontLayoutProps> = ({ children }) => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [lang, setLang] = useState<'en' | 'bn'>('en');

  const toggleLanguage = () => {
    setLang(prev => (prev === 'en' ? 'bn' : 'en'));
  };

  return (
    <div className="min-h-screen flex flex-col bg-[var(--background)] text-[var(--foreground)] antialiased font-sans">
      {/* Editorial Announcement Bar */}
      <div className="bg-[#111111] text-white text-xs py-2 px-4 text-center font-medium tracking-wide flex items-center justify-between">
        <span className="hidden sm:inline opacity-70">Dhaka & Nationwide Express Delivery • Cash on Delivery Available</span>
        <span className="mx-auto sm:mx-0">
          {lang === 'en' 
            ? 'Free shipping across Bangladesh on orders over ৳ 2,500' 
            : '৳ ২,৫০০+ অর্ডারে সমগ্র বাংলাদেশে ফ্রি হোম ডেলিভারি'}
        </span>
        <button
          onClick={toggleLanguage}
          className="flex items-center gap-1 opacity-80 hover:opacity-100 transition-opacity text-xs border border-white/20 rounded px-1.5 py-0.5 ml-2"
        >
          <Globe className="w-3 h-3" />
          <span>{lang === 'en' ? 'বাংলা' : 'English'}</span>
        </button>
      </div>

      {/* Main Header */}
      <header className="sticky top-0 z-40 bg-[var(--background)]/90 backdrop-blur-md border-b border-[var(--border)]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
          {/* Mobile hamburger */}
          <button
            onClick={() => setIsMobileMenuOpen(true)}
            className="md:hidden p-2 text-[var(--foreground)] focus:outline-none"
            aria-label="Open Navigation Menu"
          >
            <Menu className="w-6 h-6" />
          </button>

          {/* Brand Logo */}
          <Link href="/" className="flex items-center gap-2">
            <span className="font-serif text-2xl font-bold tracking-widest text-[#111111] uppercase">
              NAZEEFA
            </span>
            <span className="text-[10px] tracking-wider uppercase font-semibold text-neutral-400 border-l border-neutral-300 pl-2">
              Dhaka
            </span>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center space-x-8 text-sm font-medium tracking-wider uppercase">
            <Link href="/shop" className="hover:text-neutral-500 transition-colors">
              {lang === 'en' ? 'Shop All' : 'সকল পণ্য'}
            </Link>
            <Link href="/shop?category=t-shirts" className="hover:text-neutral-500 transition-colors">
              {lang === 'en' ? 'T-Shirts' : 'টি-শার্ট'}
            </Link>
            <Link href="/shop?category=hoodies" className="hover:text-neutral-500 transition-colors">
              {lang === 'en' ? 'Hoodies' : 'হুডি'}
            </Link>
            <Link href="/shop?customizable=1" className="text-red-700 hover:text-red-800 font-semibold transition-colors flex items-center gap-1">
              <span>{lang === 'en' ? 'Custom Print / POD' : 'কাস্টম প্রিন্ট'}</span>
              <span className="bg-red-100 text-red-700 text-[9px] px-1.5 py-0.2 rounded-full font-bold">DTF</span>
            </Link>
          </nav>

          {/* Action Icons */}
          <div className="flex items-center space-x-4">
            <Link href="/shop" className="p-2 hover:opacity-75 transition-opacity" aria-label="Search">
              <Search className="w-5 h-5" />
            </Link>
            <Link href="/wishlist" className="hidden sm:block p-2 hover:opacity-75 transition-opacity" aria-label="Wishlist">
              <Heart className="w-5 h-5" />
            </Link>
            <Link href="/cart" className="p-2 relative hover:opacity-75 transition-opacity" aria-label="Shopping Cart">
              <ShoppingBag className="w-5 h-5" />
              <span className="absolute top-1 right-1 bg-[#111111] text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
                0
              </span>
            </Link>
          </div>
        </div>
      </header>

      {/* Mobile Drawer Menu */}
      {isMobileMenuOpen && (
        <div className="fixed inset-0 z-50 flex md:hidden">
          <div className="fixed inset-0 bg-black/40 backdrop-blur-sm" onClick={() => setIsMobileMenuOpen(false)} />
          <div className="relative w-4/5 max-w-sm bg-white h-full shadow-2xl p-6 flex flex-col justify-between">
            <div>
              <div className="flex items-center justify-between pb-6 border-b border-neutral-200">
                <span className="font-serif text-xl font-bold tracking-widest">NAZEEFA</span>
                <button onClick={() => setIsMobileMenuOpen(false)} className="p-1">
                  <X className="w-6 h-6" />
                </button>
              </div>
              <div className="mt-6 flex flex-col space-y-4 font-medium uppercase tracking-wider text-base">
                <Link href="/shop" onClick={() => setIsMobileMenuOpen(false)}>Shop All</Link>
                <Link href="/shop?category=t-shirts" onClick={() => setIsMobileMenuOpen(false)}>T-Shirts</Link>
                <Link href="/shop?category=hoodies" onClick={() => setIsMobileMenuOpen(false)}>Hoodies</Link>
                <Link href="/shop?customizable=1" className="text-red-700 font-semibold" onClick={() => setIsMobileMenuOpen(false)}>
                  Custom Print / POD
                </Link>
              </div>
            </div>
            <div className="pt-6 border-t border-neutral-200 text-xs text-neutral-500">
              <p>Helpline: +880 1700-000000</p>
              <p className="mt-1">support@nazeefa.com</p>
            </div>
          </div>
        </div>
      )}

      {/* Main Page Slot */}
      <main className="flex-1">{children}</main>

      {/* Editorial Footer */}
      <footer className="bg-[#0A0A0A] text-neutral-300 pt-16 pb-12 border-t border-neutral-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10">
          <div>
            <h3 className="font-serif text-xl font-bold tracking-widest text-white mb-4">NAZEEFA</h3>
            <p className="text-xs leading-relaxed text-neutral-400">
              Bangladesh-first direct-to-consumer apparel and high-density print-on-demand platform. Designed and engineered for the modern streetwear aesthetic.
            </p>
          </div>
          <div>
            <h4 className="text-xs font-semibold uppercase tracking-wider text-white mb-4">Shop & Collections</h4>
            <ul className="text-xs space-y-2 text-neutral-400">
              <li><Link href="/shop" className="hover:text-white">All Apparel</Link></li>
              <li><Link href="/shop?category=t-shirts" className="hover:text-white">Oversized T-Shirts</Link></li>
              <li><Link href="/shop?category=hoodies" className="hover:text-white">Fleece Hoodies</Link></li>
              <li><Link href="/shop?customizable=1" className="hover:text-white">Custom T-Shirt Printing</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="text-xs font-semibold uppercase tracking-wider text-white mb-4">Customer Care</h4>
            <ul className="text-xs space-y-2 text-neutral-400">
              <li><Link href="/track-order" className="hover:text-white">Order Tracking</Link></li>
              <li><Link href="/shipping-policy" className="hover:text-white">Shipping & COD Terms</Link></li>
              <li><Link href="/return-policy" className="hover:text-white">Easy Returns & Exchanges</Link></li>
              <li><Link href="/contact" className="hover:text-white">Help Center</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="text-xs font-semibold uppercase tracking-wider text-white mb-4">Bangladesh Fulfillment</h4>
            <p className="text-xs text-neutral-400 mb-3">
              We deliver via Pathao Express and Steadfast Courier to all 64 districts in Bangladesh with Cash on Delivery.
            </p>
            <div className="flex flex-wrap gap-2 text-[10px] text-neutral-400 font-mono">
              <span className="bg-neutral-800 px-2 py-1 rounded">bKash</span>
              <span className="bg-neutral-800 px-2 py-1 rounded">Nagad</span>
              <span className="bg-neutral-800 px-2 py-1 rounded">SSLCOMMERZ</span>
              <span className="bg-neutral-800 px-2 py-1 rounded">COD</span>
            </div>
          </div>
        </div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-8 border-t border-neutral-900 text-center text-xs text-neutral-500">
          © {new Date().getFullYear()} Nazeefa (`nazeefa.com`). All rights reserved.
        </div>
      </footer>
    </div>
  );
};
