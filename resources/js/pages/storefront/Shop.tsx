import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { StorefrontLayout } from '@/layouts/StorefrontLayout';
import { ProductCard, ProductCardProps } from '@/components/storefront/ProductCard';

interface ShopProps {
  products: {
    data: ProductCardProps[];
    current_page: number;
    last_page: number;
    total: number;
  };
  categories: Array<{ id: number; name: string; slug: string }>;
  filters: { category?: string; customizable?: boolean; search?: string };
}

export default function Shop({ products, categories, filters }: ShopProps) {
  return (
    <StorefrontLayout>
      <Head title="Shop All Apparel — Nazeefa Dhaka" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {/* Header */}
        <div className="border-b border-[var(--border)] pb-6 mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 className="font-serif text-3xl font-bold tracking-tight">The Apparel Catalog</h1>
            <p className="text-xs text-neutral-500 mt-1 uppercase tracking-wider">
              Showing {products.total} curated styles
            </p>
          </div>

          {/* Quick Category Tabs */}
          <div className="flex flex-wrap gap-2 text-xs">
            <Link
              href="/shop"
              className={`px-3 py-1.5 rounded-full border transition-colors ${
                !filters.category && !filters.customizable
                  ? 'bg-neutral-900 text-white border-neutral-900'
                  : 'bg-white text-neutral-700 border-neutral-300 hover:border-neutral-400'
              }`}
            >
              All
            </Link>
            {categories.map(cat => (
              <Link
                key={cat.id}
                href={`/shop?category=${cat.slug}`}
                className={`px-3 py-1.5 rounded-full border transition-colors ${
                  filters.category === cat.slug
                    ? 'bg-neutral-900 text-white border-neutral-900'
                    : 'bg-white text-neutral-700 border-neutral-300 hover:border-neutral-400'
                }`}
              >
                {cat.name}
              </Link>
            ))}
            <Link
              href="/shop?customizable=1"
              className={`px-3 py-1.5 rounded-full border transition-colors ${
                filters.customizable
                  ? 'bg-red-700 text-white border-red-700'
                  : 'bg-white text-red-700 border-red-200 hover:border-red-400'
              }`}
            >
              Custom Print (POD)
            </Link>
          </div>
        </div>

        {/* Product Grid */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
          {products.data.map(product => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      </div>
    </StorefrontLayout>
  );
}
