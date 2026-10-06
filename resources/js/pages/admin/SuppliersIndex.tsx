import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';
import { Button } from '@/components/common/Button';
import { Badge } from '@/components/common/Badge';
import {
  Globe,
  Search,
  ArrowDownToLine,
  RefreshCw,
  TrendingUp,
  Package,
  Truck,
  ExternalLink,
  ShieldCheck,
  AlertTriangle,
  Sliders,
  DollarSign,
  CheckCircle2
} from 'lucide-react';

interface Supplier {
  id: number;
  name: string;
  code: string;
  is_active: boolean;
  reliability_rating: number;
  lead_time_days: number;
  products_count: number;
  orders_count: number;
}

interface SourcingVariant {
  vid: string;
  variantName: string;
  variantSellPrice: string;
  variantStandard?: string;
}

interface SourcingProduct {
  pid: string;
  productName: string;
  productImage: string;
  sellPrice: string;
  categoryName?: string;
  variants: SourcingVariant[];
}

interface MappedProduct {
  id: number;
  title: string;
  image_url: string;
  external_product_id: string;
  supplier_cost_cents: number;
  estimated_freight_cents: number;
  status: string;
  product?: {
    id: number;
    name: string;
    slug: string;
    is_active: boolean;
  };
  variants: Array<{
    id: number;
    variant_name: string;
    supplier_cost_cents: number;
  }>;
}

interface SupplierOrder {
  id: number;
  order_number: string;
  external_order_id: string;
  status: string;
  tracking_number: string | null;
  tracking_carrier: string | null;
  supplier: { name: string };
  order: { order_number: string };
}

interface SuppliersIndexProps {
  suppliers: Supplier[];
  isCjConfigured: boolean;
  mappedProducts: {
    data: MappedProduct[];
    total: number;
  };
  recentSupplierOrders: SupplierOrder[];
  initialSourcingCatalog: {
    is_sandbox: boolean;
    warning?: string;
    data: SourcingProduct[];
  };
  settings: {
    exchange_rate: number;
    default_margin: number;
  };
}

export default function SuppliersIndex({
  suppliers,
  isCjConfigured,
  mappedProducts,
  recentSupplierOrders,
  initialSourcingCatalog,
  settings,
}: SuppliersIndexProps) {
  const [activeTab, setActiveTab] = useState<'catalog' | 'mapped' | 'orders'>('catalog');
  const [searchQuery, setSearchQuery] = useState('');
  const [isSearching, setIsSearching] = useState(false);
  const [isSyncing, setIsSyncing] = useState(false);
  const [sourcingCatalog, setSourcingCatalog] = useState(initialSourcingCatalog.data);
  const [isSandbox, setIsSandbox] = useState(initialSourcingCatalog.is_sandbox);

  // Import Modal State
  const [selectedProduct, setSelectedProduct] = useState<SourcingProduct | null>(null);
  const [targetMargin, setTargetMargin] = useState(settings.default_margin || 35);
  const [isSubmittingImport, setIsSubmittingImport] = useState(false);

  const exchangeRate = settings.exchange_rate || 122.00;

  const handleSearch = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSearching(true);
    try {
      const res = await fetch(`/admin/suppliers/search?keyword=${encodeURIComponent(searchQuery)}`);
      const data = await res.json();
      setSourcingCatalog(data.data || []);
      setIsSandbox(data.is_sandbox ?? false);
    } catch (err) {
      console.error(err);
    } finally {
      setIsSearching(false);
    }
  };

  const handleSyncStock = async () => {
    setIsSyncing(true);
    try {
      const res = await fetch('/admin/suppliers/sync-stock', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();
      alert(data.message || 'Stock successfully synchronised');
    } catch (err) {
      console.error(err);
    } finally {
      setIsSyncing(false);
    }
  };

  const handleImportSubmit = () => {
    if (!selectedProduct) return;
    setIsSubmittingImport(true);

    router.post('/admin/suppliers/import', {
      pid: selectedProduct.pid,
      title: selectedProduct.productName,
      image_url: selectedProduct.productImage,
      sell_price_usd: parseFloat(selectedProduct.sellPrice),
      estimated_freight_usd: 4.20,
      target_margin: targetMargin,
      exchange_rate: exchangeRate,
      variants: selectedProduct.variants,
    }, {
      onFinish: () => {
        setIsSubmittingImport(false);
        setSelectedProduct(null);
      }
    });
  };

  // Landed calculation helper for modal
  const calcLanded = (priceUsd: number, marginPercent: number) => {
    const freightUsd = 4.20;
    const totalUsd = priceUsd + freightUsd;
    const landedBdt = totalUsd * exchangeRate * 1.10; // 10% customs/buffer
    const divisor = Math.max(0.1, 1 - (marginPercent / 100 + 0.025));
    const retailBdt = Math.ceil((landedBdt / divisor) / 50) * 50;
    const profitBdt = retailBdt - landedBdt - (retailBdt * 0.025);

    return {
      landedBdt: Math.round(landedBdt),
      retailBdt: Math.round(retailBdt),
      profitBdt: Math.round(profitBdt),
    };
  };

  return (
    <AdminLayout>
      <Head title="Supplier & Dropshipping Sourcing — CommerceOS" />

      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
          <div className="flex items-center gap-2">
            <h1 className="text-2xl font-bold tracking-tight text-neutral-900">Global Suppliers & Dropshipping</h1>
            <span className="px-2 py-0.5 text-xs font-semibold rounded bg-neutral-900 text-white">B2B / Sourcing</span>
          </div>
          <p className="text-sm text-neutral-500 mt-1">
            Connect international apparel suppliers, simulate landed BDT costs, and one-click import items into Nazeefa.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <Button
            variant="outline"
            size="sm"
            onClick={handleSyncStock}
            disabled={isSyncing}
            className="flex items-center gap-2"
          >
            <RefreshCw className={`h-4 w-4 ${isSyncing ? 'animate-spin' : ''}`} />
            Sync Dropship Stock
          </Button>
        </div>
      </div>

      {/* Connection & Configuration Status Banner */}
      {!isCjConfigured && (
        <div className="mb-6 p-4 rounded-xl border border-amber-200 bg-amber-50 flex items-start gap-3">
          <AlertTriangle className="h-5 w-5 text-amber-600 mt-0.5 shrink-0" />
          <div className="text-sm">
            <h4 className="font-semibold text-amber-900">CJ Dropshipping Sandbox Mode Active</h4>
            <p className="text-amber-700 mt-0.5">
              No live API credentials detected in <code className="bg-amber-100 px-1 py-0.5 rounded text-amber-800">CJ_DROPSHIPPING_API_KEY</code>.
              Operating in zero-crash sandbox mode with simulated catalog and deterministic BDT calculations.
            </p>
          </div>
        </div>
      )}

      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Connected Suppliers</span>
            <Globe className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{suppliers.length} Partners</div>
          <div className="text-xs text-neutral-500 mt-1">CJ Dropshipping (Active), Alibaba (Ready)</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Mapped Catalog SKUs</span>
            <Package className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{mappedProducts.total} Products</div>
          <div className="text-xs text-emerald-600 font-medium mt-1">Live in Storefront / Draft</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">USD / BDT FX Rate</span>
            <DollarSign className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">৳{exchangeRate.toFixed(2)}</div>
          <div className="text-xs text-neutral-500 mt-1">Live calculation base rate</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Active Sourcing Orders</span>
            <Truck className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{recentSupplierOrders.length} In-Flight</div>
          <div className="text-xs text-neutral-500 mt-1">International air / packet transit</div>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-neutral-200 mb-6 flex gap-6">
        <button
          onClick={() => setActiveTab('catalog')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'catalog'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          CJ Sourcing Catalog ({sourcingCatalog.length})
        </button>
        <button
          onClick={() => setActiveTab('mapped')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'mapped'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          Imported Products ({mappedProducts.total})
        </button>
        <button
          onClick={() => setActiveTab('orders')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'orders'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          Sourcing Orders ({recentSupplierOrders.length})
        </button>
      </div>

      {/* TAB 1: SOURCING CATALOG */}
      {activeTab === 'catalog' && (
        <div>
          {/* Search Bar */}
          <form onSubmit={handleSearch} className="flex gap-2 max-w-xl mb-6">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
              <input
                type="text"
                placeholder="Search CJ catalog (e.g. Acid Wash Tee, Hoodie, Oversized)..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-neutral-900"
              />
            </div>
            <Button type="submit" size="sm" disabled={isSearching}>
              {isSearching ? 'Searching...' : 'Search'}
            </Button>
          </form>

          {/* Sourcing Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {sourcingCatalog.map((product) => {
              const priceUsd = parseFloat(product.sellPrice);
              const preview = calcLanded(priceUsd, targetMargin);

              return (
                <div key={product.pid} className="border border-neutral-200 rounded-xl bg-white overflow-hidden shadow-sm flex flex-col">
                  <div className="relative aspect-4/3 bg-neutral-100 overflow-hidden">
                    <img
                      src={product.productImage}
                      alt={product.productName}
                      className="w-full h-full object-cover"
                    />
                    <div className="absolute top-2 left-2 bg-neutral-900/80 backdrop-blur-sm text-white text-[11px] font-medium px-2 py-0.5 rounded">
                      PID: {product.pid}
                    </div>
                  </div>

                  <div className="p-4 flex-1 flex flex-col justify-between">
                    <div>
                      <div className="text-xs text-neutral-500 font-medium mb-1">{product.categoryName || 'Apparel Blank'}</div>
                      <h3 className="font-semibold text-neutral-900 text-sm line-clamp-2 mb-3">{product.productName}</h3>

                      <div className="bg-neutral-50 rounded-lg p-2.5 mb-4 text-xs space-y-1 border border-neutral-100">
                        <div className="flex justify-between">
                          <span className="text-neutral-500">Supplier Price:</span>
                          <span className="font-semibold text-neutral-900">${product.sellPrice} USD (৳{Math.round(priceUsd * exchangeRate)})</span>
                        </div>
                        <div className="flex justify-between">
                          <span className="text-neutral-500">Est. Packet Freight:</span>
                          <span className="text-neutral-700">$4.20 USD (৳{Math.round(4.20 * exchangeRate)})</span>
                        </div>
                        <div className="flex justify-between border-t border-neutral-200 pt-1 mt-1">
                          <span className="font-medium text-neutral-700">Recommended Retail:</span>
                          <span className="font-bold text-emerald-700">৳{preview.retailBdt.toLocaleString()} BDT</span>
                        </div>
                      </div>
                    </div>

                    <div className="flex items-center justify-between pt-2 border-t border-neutral-100">
                      <span className="text-xs text-neutral-500">{product.variants?.length || 1} Variants</span>
                      <Button
                        size="sm"
                        onClick={() => setSelectedProduct(product)}
                        className="flex items-center gap-1.5"
                      >
                        <ArrowDownToLine className="h-3.5 w-3.5" />
                        Import to Catalog
                      </Button>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* TAB 2: IMPORTED PRODUCTS */}
      {activeTab === 'mapped' && (
        <div className="bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="bg-neutral-50 text-neutral-600 text-xs uppercase tracking-wider border-b border-neutral-200">
                <tr>
                  <th className="px-4 py-3">Product</th>
                  <th className="px-4 py-3">Supplier PID</th>
                  <th className="px-4 py-3">Wholesale Cost</th>
                  <th className="px-4 py-3">Variants</th>
                  <th className="px-4 py-3">Catalog Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-200">
                {mappedProducts.data.map((item) => (
                  <tr key={item.id} className="hover:bg-neutral-50 transition-colors">
                    <td className="px-4 py-3">
                      <div className="flex items-center gap-3">
                        <img src={item.image_url} alt="" className="w-10 h-10 rounded object-cover border border-neutral-200" />
                        <div>
                          <div className="font-medium text-neutral-900">{item.title}</div>
                          {item.product && (
                            <span className="text-xs text-emerald-600 font-mono">/{item.product.slug}</span>
                          )}
                        </div>
                      </div>
                    </td>
                    <td className="px-4 py-3 font-mono text-xs text-neutral-600">{item.external_product_id}</td>
                    <td className="px-4 py-3">
                      <div className="font-medium text-neutral-900">${(item.supplier_cost_cents / 100).toFixed(2)} USD</div>
                      <div className="text-xs text-neutral-500">Freight: ${(item.estimated_freight_cents / 100).toFixed(2)}</div>
                    </td>
                    <td className="px-4 py-3 text-neutral-600 text-xs">{item.variants.length} mapped</td>
                    <td className="px-4 py-3">
                      <Badge variant={item.status === 'mapped' ? 'success' : 'neutral'}>
                        {item.status.toUpperCase()}
                      </Badge>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* TAB 3: SOURCING ORDERS */}
      {activeTab === 'orders' && (
        <div className="bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="bg-neutral-50 text-neutral-600 text-xs uppercase tracking-wider border-b border-neutral-200">
                <tr>
                  <th className="px-4 py-3">Customer Order</th>
                  <th className="px-4 py-3">Supplier Order Ref</th>
                  <th className="px-4 py-3">Carrier & Tracking</th>
                  <th className="px-4 py-3">Fulfillment Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-200">
                {recentSupplierOrders.length === 0 ? (
                  <tr>
                    <td colSpan={4} className="px-4 py-8 text-center text-neutral-500 text-sm">
                      No automated supplier dropship orders created yet.
                    </td>
                  </tr>
                ) : (
                  recentSupplierOrders.map((ord) => (
                    <tr key={ord.id} className="hover:bg-neutral-50">
                      <td className="px-4 py-3 font-semibold text-neutral-900">{ord.order.order_number}</td>
                      <td className="px-4 py-3 font-mono text-xs">{ord.external_order_id}</td>
                      <td className="px-4 py-3">
                        <div className="text-neutral-900 text-xs font-medium">{ord.tracking_carrier || 'CJ Packet'}</div>
                        <div className="text-xs text-neutral-500 font-mono">{ord.tracking_number || 'Pending Dispatch'}</div>
                      </td>
                      <td className="px-4 py-3">
                        <Badge variant="warning">{ord.status.toUpperCase()}</Badge>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* IMPORT & MARGIN CALCULATOR MODAL */}
      {selectedProduct && (
        <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-neutral-200 animate-in fade-in zoom-in-95 duration-150">
            <div className="flex items-start justify-between mb-4">
              <div>
                <h3 className="font-bold text-lg text-neutral-900">Import to Nazeefa Catalog</h3>
                <p className="text-xs text-neutral-500">Configure target margin and auto-calculate BDT retail price.</p>
              </div>
              <button
                onClick={() => setSelectedProduct(null)}
                className="text-neutral-400 hover:text-neutral-600 text-sm font-bold"
              >
                ✕
              </button>
            </div>

            <div className="flex gap-4 p-3 bg-neutral-50 rounded-xl border border-neutral-200 mb-6">
              <img
                src={selectedProduct.productImage}
                alt=""
                className="w-16 h-16 rounded-lg object-cover border border-neutral-200 shrink-0"
              />
              <div>
                <h4 className="font-semibold text-sm text-neutral-900 line-clamp-1">{selectedProduct.productName}</h4>
                <div className="text-xs text-neutral-500 mt-0.5">Supplier Wholesale: ${selectedProduct.sellPrice} USD</div>
                <div className="text-xs text-neutral-500">Variants: {selectedProduct.variants.length} SKU sizes</div>
              </div>
            </div>

            {/* Margin Slider */}
            <div className="mb-6">
              <div className="flex justify-between items-center mb-2">
                <label className="text-xs font-semibold text-neutral-700 flex items-center gap-1.5">
                  <Sliders className="h-3.5 w-3.5" />
                  Target Gross Margin: {targetMargin}%
                </label>
                <span className="text-xs text-neutral-500">Buffer: 10% customs + 2.5% gateway</span>
              </div>
              <input
                type="range"
                min="20"
                max="65"
                step="5"
                value={targetMargin}
                onChange={(e) => setTargetMargin(parseInt(e.target.value))}
                className="w-full accent-neutral-900 cursor-pointer"
              />
            </div>

            {/* Cost Breakdown */}
            {(() => {
              const p = calcLanded(parseFloat(selectedProduct.sellPrice), targetMargin);
              return (
                <div className="bg-neutral-900 text-white p-4 rounded-xl space-y-2 mb-6">
                  <div className="flex justify-between text-xs text-neutral-400">
                    <span>Landed Cost (Product + Freight + Buffer):</span>
                    <span>৳{p.landedBdt.toLocaleString()} BDT</span>
                  </div>
                  <div className="flex justify-between text-xs text-neutral-400">
                    <span>Target Gross Profit:</span>
                    <span className="text-emerald-400 font-semibold">+৳{p.profitBdt.toLocaleString()} BDT</span>
                  </div>
                  <div className="flex justify-between text-base font-bold border-t border-neutral-800 pt-2 mt-2">
                    <span>Suggested Nazeefa Retail Price:</span>
                    <span className="text-emerald-300">৳{p.retailBdt.toLocaleString()} BDT</span>
                  </div>
                </div>
              );
            })()}

            <div className="flex justify-end gap-3">
              <Button variant="outline" size="sm" onClick={() => setSelectedProduct(null)}>
                Cancel
              </Button>
              <Button
                size="sm"
                onClick={handleImportSubmit}
                disabled={isSubmittingImport}
                className="flex items-center gap-2"
              >
                <CheckCircle2 className="h-4 w-4" />
                {isSubmittingImport ? 'Importing...' : 'Confirm & Import as Draft'}
              </Button>
            </div>
          </div>
        </div>
      )}
    </AdminLayout>
  );
}
