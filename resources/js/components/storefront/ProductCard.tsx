import React from 'react';
import { Link } from '@inertiajs/react';
import { Badge } from '@/components/common/Badge';

interface Variant {
  id: number;
  sku: string;
  size: string;
  color_name: string;
  color_hex: string;
  price_amount: number;
  compare_at_amount?: number;
  currency: string;
}

export interface ProductCardProps {
  id: number;
  name: string;
  slug: string;
  short_description?: string;
  is_customizable: boolean;
  images?: string[];
  variants?: Variant[];
}

export const ProductCard: React.FC<{ product: ProductCardProps }> = ({ product }) => {
  const primaryVariant = product.variants?.[0];
  const priceBDT = primaryVariant ? primaryVariant.price_amount / 100 : 0;
  const comparePriceBDT = primaryVariant?.compare_at_amount ? primaryVariant.compare_at_amount / 100 : null;
  const image = product.images?.[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&q=80';

  return (
    <div className="group relative flex flex-col">
      {/* Product Image Frame */}
      <Link href={`/products/${product.slug}`} className="relative aspect-[3/4] w-full overflow-hidden bg-neutral-100 rounded-[var(--radius-md)]">
        <img
          src={image}
          alt={product.name}
          className="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
          loading="lazy"
        />
        {product.is_customizable && (
          <div className="absolute top-2.5 left-2.5">
            <Badge variant="danger">Custom POD</Badge>
          </div>
        )}
      </Link>

      {/* Info Block */}
      <div className="mt-3 flex flex-col flex-1">
        <h3 className="text-sm font-medium text-[var(--foreground)] line-clamp-1 group-hover:underline">
          <Link href={`/products/${product.slug}`}>{product.name}</Link>
        </h3>
        
        {/* Price & Currency */}
        <div className="mt-1 flex items-baseline gap-2">
          <span className="text-sm font-semibold text-[var(--foreground)]">
            ৳ {priceBDT.toLocaleString('en-US')}
          </span>
          {comparePriceBDT && (
            <span className="text-xs text-neutral-400 line-through">
              ৳ {comparePriceBDT.toLocaleString('en-US')}
            </span>
          )}
        </div>

        {/* Color Swatches */}
        {product.variants && (
          <div className="mt-2 flex items-center gap-1.5">
            {Array.from(new Set(product.variants.map(v => v.color_hex))).slice(0, 4).map((hex, i) => (
              <span
                key={i}
                className="w-2.5 h-2.5 rounded-full border border-neutral-300"
                style={{ backgroundColor: hex }}
              />
            ))}
          </div>
        )}
      </div>
    </div>
  );
};
