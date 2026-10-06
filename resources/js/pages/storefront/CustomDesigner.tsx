import React, { useState, useRef } from 'react';
import { Head, router } from '@inertiajs/react';
import { StorefrontLayout } from '@/layouts/StorefrontLayout';
import { Button } from '@/components/common/Button';
import { Upload, Sparkles, Layers, CheckCircle2, RotateCw } from 'lucide-react';

interface Product {
  id: number;
  name: string;
  material: string;
  images: string[];
  variants: Array<{ id: number; size: string; color_name: string; price_amount: number }>;
}

interface CustomDesignerProps {
  products: Product[];
  selectedProduct: Product;
}

export default function CustomDesigner({ products, selectedProduct }: CustomDesignerProps) {
  const [placement, setPlacement] = useState<'front' | 'back' | 'left_chest'>('front');
  const [uploadedImage, setUploadedImage] = useState<string | null>(null);
  const [artworkId, setArtworkId] = useState<number | null>(null);
  const [isUploading, setIsUploading] = useState(false);
  const [customText, setCustomText] = useState('');
  const [selectedSize, setSelectedSize] = useState('L');
  
  // Canvas element position state
  const [artPos, setArtPos] = useState({ x: 120, y: 130, scale: 1 });
  const fileInputRef = useRef<HTMLInputElement>(null);

  const garmentImage = selectedProduct.images?.[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&q=80';
  const basePriceBDT = (selectedProduct.variants?.[0]?.price_amount || 145000) / 100;

  const handleFileUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setIsUploading(true);
    const formData = new FormData();
    formData.append('artwork', file);
    formData.append('placement', placement);

    try {
      const response = await fetch('/custom-designer/artwork', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
        },
        body: formData,
      });

      const data = await response.json();
      if (data.status === 'success') {
        setUploadedImage(data.file_url);
        setArtworkId(data.artwork_id);
      }
    } catch (err) {
      // Fallback local preview
      setUploadedImage(URL.createObjectURL(file));
    } finally {
      setIsUploading(false);
    }
  };

  const handleAddToCart = () => {
    alert(`Custom ${selectedProduct.name} (Size: ${selectedSize}, Placement: ${placement.toUpperCase()}) added to your cart with custom artwork!`);
    router.visit('/checkout');
  };

  return (
    <StorefrontLayout>
      <Head title="Custom Print Studio — Nazeefa Dhaka" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center pb-6 border-b border-neutral-200 mb-8 gap-4">
          <div>
            <span className="text-xs font-bold uppercase tracking-widest text-red-600 flex items-center gap-1.5">
              <Sparkles className="w-3.5 h-3.5" /> High-Density DTF Custom Apparel
            </span>
            <h1 className="font-serif text-3xl font-bold tracking-tight text-neutral-900 mt-1">
              Custom Apparel Design Studio
            </h1>
          </div>
          <div className="text-right">
            <span className="text-xs text-neutral-400">Estimated Total</span>
            <p className="font-mono text-2xl font-bold text-neutral-900">৳ {basePriceBDT.toLocaleString('en-US')}</p>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
          {/* Interactive Garment & Canvas Viewport (7 cols) */}
          <div className="lg:col-span-7 flex flex-col items-center">
            {/* Print Placement Tabs */}
            <div className="flex gap-2 p-1 bg-neutral-200 rounded-[var(--radius-pill)] mb-6 text-xs font-semibold uppercase">
              <button
                onClick={() => setPlacement('front')}
                className={`px-4 py-2 rounded-full transition-all ${
                  placement === 'front' ? 'bg-black text-white shadow-sm' : 'text-neutral-700 hover:text-black'
                }`}
              >
                Front Chest
              </button>
              <button
                onClick={() => setPlacement('back')}
                className={`px-4 py-2 rounded-full transition-all ${
                  placement === 'back' ? 'bg-black text-white shadow-sm' : 'text-neutral-700 hover:text-black'
                }`}
              >
                Back Print
              </button>
              <button
                onClick={() => setPlacement('left_chest')}
                className={`px-4 py-2 rounded-full transition-all ${
                  placement === 'left_chest' ? 'bg-black text-white shadow-sm' : 'text-neutral-700 hover:text-black'
                }`}
              >
                Pocket / Left Chest
              </button>
            </div>

            {/* Garment Mockup Container */}
            <div className="relative aspect-[4/5] w-full max-w-md bg-neutral-100 rounded-[var(--radius-lg)] border border-neutral-300 overflow-hidden shadow-inner flex items-center justify-center">
              <img
                src={garmentImage}
                alt="Blank Apparel Garment"
                className="w-full h-full object-cover"
              />

              {/* Printable Safe Boundary */}
              <div className="absolute inset-x-1/4 top-1/5 bottom-1/4 border-2 border-dashed border-red-500/60 rounded flex flex-col items-center justify-center pointer-events-none">
                <span className="text-[10px] uppercase font-mono tracking-wider text-red-500/80 bg-white/80 px-1 rounded absolute top-1">
                  Print Area Safe Zone
                </span>

                {/* Rendered Uploaded Artwork */}
                {uploadedImage && (
                  <img
                    src={uploadedImage}
                    alt="Uploaded Artwork"
                    className="max-w-[160px] max-h-[160px] object-contain drop-shadow pointer-events-auto cursor-move"
                  />
                )}

                {/* Rendered Custom Text */}
                {customText && (
                  <p className="font-bold text-lg text-black mt-2 tracking-wide uppercase drop-shadow pointer-events-auto">
                    {customText}
                  </p>
                )}

                {!uploadedImage && !customText && (
                  <p className="text-xs text-neutral-400 font-mono text-center px-4">
                    Artwork will appear here in real-time
                  </p>
                )}
              </div>
            </div>

            <p className="text-[11px] text-neutral-500 mt-4 text-center">
              Drag artwork to adjust positioning. Multi-color DTF prints are heat-cured at 160°C.
            </p>
          </div>

          {/* Controls & Configuration Sidebar (5 cols) */}
          <div className="lg:col-span-5 space-y-6">
            {/* 1. Artwork Upload Card */}
            <div className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)]">
              <h3 className="text-sm font-semibold uppercase tracking-wider mb-4 flex items-center gap-2">
                <Upload className="w-4 h-4 text-neutral-700" /> Upload Your Graphics
              </h3>
              
              <input
                type="file"
                ref={fileInputRef}
                onChange={handleFileUpload}
                accept="image/png, image/jpeg, image/svg+xml"
                className="hidden"
              />

              <div
                onClick={() => fileInputRef.current?.click()}
                className="border-2 border-dashed border-neutral-300 hover:border-black rounded-[var(--radius-md)] p-6 text-center cursor-pointer transition-colors bg-white"
              >
                <Upload className="w-8 h-8 text-neutral-400 mx-auto mb-2" />
                <span className="text-xs font-semibold text-neutral-800 block">
                  {isUploading ? 'Uploading & Analyzing...' : 'Click to Browse Artwork'}
                </span>
                <span className="text-[11px] text-neutral-400 mt-1 block">
                  PNG with transparent background, JPEG or SVG (up to 25MB)
                </span>
              </div>

              {uploadedImage && (
                <div className="mt-3 flex items-center justify-between text-xs text-green-700 bg-green-50 p-2 rounded">
                  <span className="flex items-center gap-1 font-medium">
                    <CheckCircle2 className="w-4 h-4" /> Artwork Loaded
                  </span>
                  <button
                    onClick={() => { setUploadedImage(null); setArtworkId(null); }}
                    className="text-neutral-500 hover:text-red-700 text-[11px] underline"
                  >
                    Remove
                  </button>
                </div>
              )}
            </div>

            {/* 2. Custom Typography Text Layer */}
            <div className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)]">
              <h3 className="text-sm font-semibold uppercase tracking-wider mb-3">Add Custom Text</h3>
              <input
                type="text"
                value={customText}
                onChange={e => setCustomText(e.target.value)}
                placeholder="e.g. DHAKA 1998 / BRAND NAME"
                className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black uppercase font-mono"
              />
            </div>

            {/* 3. Garment Size & Details */}
            <div className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)]">
              <h3 className="text-sm font-semibold uppercase tracking-wider mb-3">Select Size</h3>
              <div className="flex gap-2">
                {['M', 'L', 'XL'].map(size => (
                  <button
                    key={size}
                    onClick={() => setSelectedSize(size)}
                    className={`w-12 h-12 flex items-center justify-center font-bold text-sm rounded-[var(--radius-md)] border transition-all ${
                      selectedSize === size
                        ? 'bg-black text-white border-black'
                        : 'bg-white text-black border-neutral-300 hover:border-black'
                    }`}
                  >
                    {size}
                  </button>
                ))}
              </div>

              <div className="mt-4 pt-4 border-t border-neutral-200 text-xs text-neutral-500 space-y-1">
                <p>• <strong>Lead Time:</strong> 2-3 business days custom production</p>
                <p>• <strong>Print Durability:</strong> 50+ wash cycles guaranteed</p>
              </div>
            </div>

            {/* Add to Cart CTA */}
            <Button
              onClick={handleAddToCart}
              variant="primary"
              size="lg"
              className="w-full bg-[#111111] text-white hover:bg-neutral-800 py-4 font-bold"
            >
              Order Custom Apparel (৳ {basePriceBDT.toLocaleString('en-US')})
            </Button>
          </div>
        </div>
      </div>
    </StorefrontLayout>
  );
}
