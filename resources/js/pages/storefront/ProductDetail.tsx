import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { StorefrontLayout } from '@/layouts/StorefrontLayout';
import { Button } from '@/components/common/Button';
import { Badge } from '@/components/common/Badge';
import { ProductCard, ProductCardProps } from '@/components/storefront/ProductCard';
import { Sparkles, Truck, ShieldCheck, Ruler } from 'lucide-react';

interface Variant {
  id: number;
  sku: string;
  size: string;
  color_name: string;
  color_hex: string;
  price_amount: number;
  compare_at_amount?: number;
  currency: string;
  stock_on_hand: number;
}

interface ProductDetailProps {
  product: ProductCardProps & {
    description?: string;
    material?: string;
    fit_type?: string;
    variants: Variant[];
  };
  relatedProducts: ProductCardProps[];
}

export default function ProductDetail({ product, relatedProducts }: ProductDetailProps) {
  const [selectedVariant, setSelectedVariant] = useState<Variant>(product.variants[0]);
  const [selectedImage, setSelectedImage] = useState<string>(product.images?.[0] || '');
  const [quantity, setQuantity] = useState<number>(1);

  const priceBDT = (selectedVariant?.price_amount || 0) / 100;
  const comparePriceBDT = selectedVariant?.compare_at_amount ? selectedVariant.compare_at_amount / 100 : null;

  const sizes = Array.from(new Set(product.variants.map(v => v.size)));
  const colors = Array.from(new Set(product.variants.map(v => v.color_name)));

  return (
    <StorefrontLayout>
      <Head title={`${product.name} — Nazeefa Dhaka`} />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
          {/* Gallery */}
          <div>
            <div className="aspect-[3/4] w-full overflow-hidden rounded-[var(--radius-lg)] bg-neutral-100">
              <img
                src={selectedImage}
                alt={product.name}
                className="w-full h-full object-cover"
              />
            </div>
            {product.images && product.images.length > 1 && (
              <div className="flex gap-3 mt-4">
                {product.images.map((img, i) => (
                  <button
                    key={i}
                    onClick={() => setSelectedImage(img)}
                    className={`w-20 aspect-square rounded-[var(--radius-sm)] overflow-hidden border-2 ${
                      selectedImage === img ? 'border-black' : 'border-transparent'
                    }`}
                  >
                    <img src={img} alt="" className="w-full h-full object-cover" />
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Details & Actions */}
          <div className="flex flex-col">
            {product.is_customizable && (
              <div className="mb-2">
                <Badge variant="danger">Customizable Print-On-Demand</Badge>
              </div>
            )}
            <h1 className="font-serif text-3xl font-bold tracking-tight text-[var(--foreground)]">
              {product.name}
            </h1>
            <p className="text-xs font-mono text-neutral-400 mt-1 uppercase">
              SKU: {selectedVariant?.sku}
            </p>

            {/* Price */}
            <div className="mt-4 flex items-baseline gap-3">
              <span className="text-2xl font-bold text-[var(--foreground)]">
                ৳ {priceBDT.toLocaleString('en-US')}
              </span>
              {comparePriceBDT && (
                <span className="text-base text-neutral-400 line-through">
                  ৳ {comparePriceBDT.toLocaleString('en-US')}
                </span>
              )}
            </div>

            {/* Size Selector */}
            <div className="mt-8">
              <div className="flex justify-between items-center text-xs font-semibold uppercase tracking-wider mb-2">
                <span>Select Size</span>
                <button className="flex items-center gap-1 text-neutral-500 hover:text-black">
                  <Ruler className="w-3.5 h-3.5" /> Size Guide
                </button>
              </div>
              <div className="flex gap-2">
                {sizes.map(size => {
                  const variantForSize = product.variants.find(
                    v => v.size === size && v.color_name === selectedVariant?.color_name
                  );
                  const isSelected = selectedVariant?.size === size;
                  return (
                    <button
                      key={size}
                      onClick={() => variantForSize && setSelectedVariant(variantForSize)}
                      className={`w-12 h-12 flex items-center justify-center font-medium text-sm rounded-[var(--radius-md)] border transition-all ${
                        isSelected
                          ? 'bg-black text-white border-black'
                          : 'bg-white text-black border-neutral-300 hover:border-black'
                      }`}
                    >
                      {size}
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Quantity and Actions */}
            <div className="mt-8 flex gap-4">
              <div className="flex items-center border border-neutral-300 rounded-[var(--radius-md)]">
                <button
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  className="px-3 py-2 text-sm text-neutral-600 hover:text-black"
                >
                  -
                </button>
                <span className="px-3 py-2 text-sm font-semibold">{quantity}</span>
                <button
                  onClick={() => setQuantity(quantity + 1)}
                  className="px-3 py-2 text-sm text-neutral-600 hover:text-black"
                >
                  +
                </button>
              </div>

              <Button variant="primary" size="lg" className="flex-1 bg-black text-white hover:bg-neutral-800">
                Add to Cart
              </Button>
            </div>

            {/* If product is customizable, show custom designer handoff */}
            {product.is_customizable && (
              <div className="mt-4">
                <Link href={`/custom-designer?product_id=${product.id}`}>
                  <Button variant="outline" size="lg" className="w-full border-red-700 text-red-700 hover:bg-red-50 flex items-center justify-center gap-2">
                    <Sparkles className="w-4 h-4" />
                    Customize & Print Your Artwork
                  </Button>
                </Link>
                <p className="text-[11px] text-neutral-500 mt-1 text-center">
                  Production time: 2-3 business days before courier dispatch.
                </p>
              </div>
            )}

            {/* Attributes list */}
            <div className="mt-8 border-t border-neutral-200 pt-6 space-y-3 text-xs text-neutral-600">
              <p><strong>Material:</strong> {product.material || '100% Combed Cotton'}</p>
              <p><strong>Fit Type:</strong> {product.fit_type || 'Regular / Streetwear Fit'}</p>
              <p><strong>Delivery:</strong> 24-48 hours inside Dhaka, 3-4 days nationwide via Pathao / Steadfast</p>
            </div>
          </div>
        </div>
      </div>
    </StorefrontLayout>
  );
}
