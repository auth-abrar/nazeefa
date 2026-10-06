import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { StorefrontLayout } from '@/layouts/StorefrontLayout';
import { ProductCard, ProductCardProps } from '@/components/storefront/ProductCard';
import { Button } from '@/components/common/Button';
import { Sparkles, ArrowRight, ShieldCheck, Truck, RefreshCw } from 'lucide-react';

interface HomeProps {
  featuredProducts: ProductCardProps[];
  categories: Array<{ id: number; name: string; slug: string; image_url: string; description: string }>;
  featuredCollection?: { id: number; name: string; slug: string; banner_url: string; description: string };
}

export default function Home({ featuredProducts, categories, featuredCollection }: HomeProps) {
  return (
    <StorefrontLayout>
      <Head title="Nazeefa — Premium Bangladesh DTC Apparel & Custom Streetwear" />

      {/* Hero Section */}
      <section className="relative bg-[#111111] text-white overflow-hidden py-24 md:py-36">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div>
            <span className="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-neutral-400 font-semibold mb-4">
              <Sparkles className="w-3.5 h-3.5 text-red-500" />
              Autumn / Winter Heavyweight Series
            </span>
            <h1 className="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-tight">
              Elevate Your Everyday Standard.
            </h1>
            <p className="mt-4 text-base text-neutral-300 max-w-lg leading-relaxed">
              240 GSM organic combed cotton crafted for Dhaka's streets. Heavyweight structure, drop-shoulder silhouettes, and custom on-demand prints.
            </p>
            <div className="mt-8 flex flex-wrap gap-4">
              <Link href="/shop">
                <Button variant="primary" size="lg" className="bg-white text-black hover:bg-neutral-200">
                  Shop Collection
                </Button>
              </Link>
              <Link href="/shop?customizable=1">
                <Button variant="outline" size="lg" className="border-neutral-700 text-white hover:bg-neutral-800">
                  Design Custom Tee
                </Button>
              </Link>
            </div>
          </div>
          <div className="relative aspect-[4/5] rounded-[var(--radius-lg)] overflow-hidden shadow-2xl border border-neutral-800">
            <img
              src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1200&q=85"
              alt="Nazeefa Streetwear Lookbook"
              className="w-full h-full object-cover"
            />
          </div>
        </div>
      </section>

      {/* Value Propositions */}
      <section className="border-b border-[var(--border)] py-8 bg-[var(--surface)]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-3 gap-6 text-center sm:text-left">
          <div className="flex items-center gap-4 justify-center sm:justify-start">
            <Truck className="w-6 h-6 text-neutral-700" />
            <div>
              <h4 className="text-xs font-semibold uppercase tracking-wider">Fast Courier Dispatch</h4>
              <p className="text-xs text-neutral-500">24-48h in Dhaka • Nationwide COD</p>
            </div>
          </div>
          <div className="flex items-center gap-4 justify-center sm:justify-start">
            <ShieldCheck className="w-6 h-6 text-neutral-700" />
            <div>
              <h4 className="text-xs font-semibold uppercase tracking-wider">100% Combed Cotton</h4>
              <p className="text-xs text-neutral-500">Bio-washed, zero shrink guarantee</p>
            </div>
          </div>
          <div className="flex items-center gap-4 justify-center sm:justify-start">
            <RefreshCw className="w-6 h-6 text-neutral-700" />
            <div>
              <h4 className="text-xs font-semibold uppercase tracking-wider">7-Day Hassle-Free Exchange</h4>
              <p className="text-xs text-neutral-500">Easy size swap at your doorstep</p>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Apparel Section */}
      <section className="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-end justify-between mb-10">
          <div>
            <h2 className="font-serif text-2xl sm:text-3xl font-bold tracking-tight">Best Sellers</h2>
            <p className="text-xs text-neutral-500 mt-1 uppercase tracking-wider">Signature Essentials</p>
          </div>
          <Link href="/shop" className="text-xs font-semibold uppercase tracking-wider hover:underline flex items-center gap-1">
            View All <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
          {featuredProducts.map(product => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      </section>

      {/* Custom Print & POD Showcase CTA */}
      <section className="bg-neutral-900 text-white py-20 my-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
          <div className="aspect-[4/3] rounded-[var(--radius-lg)] overflow-hidden">
            <img
              src="https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1000&q=85"
              alt="Custom Print on Demand T-shirt"
              className="w-full h-full object-cover"
            />
          </div>
          <div>
            <span className="text-xs uppercase tracking-widest text-red-400 font-bold">First-Class POD Engine</span>
            <h2 className="font-serif text-3xl sm:text-4xl font-bold mt-2 leading-tight">
              Bring Your Vision to Life. Zero Minimums.
            </h2>
            <p className="mt-4 text-sm text-neutral-300 leading-relaxed">
              Upload personal artwork, community graphics, or corporate logos. Our DTF (Direct to Film) multi-color printing ensures razor-sharp detail and wash-proof vibrancy.
            </p>
            <div className="mt-6">
              <Link href="/shop?customizable=1">
                <Button variant="primary" size="lg" className="bg-red-600 hover:bg-red-700 text-white">
                  Customize a T-Shirt Now
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>
    </StorefrontLayout>
  );
}
