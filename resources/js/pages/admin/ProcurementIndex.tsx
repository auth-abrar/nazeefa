import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';
import { Button } from '@/components/common/Button';
import { Badge } from '@/components/common/Badge';
import {
  Building2,
  Ship,
  FileText,
  Plus,
  CheckCircle2,
  Clock,
  ArrowRight,
  ShieldCheck,
  AlertCircle,
  TrendingUp,
  Package,
  Layers,
  Sparkles
} from 'lucide-react';

interface PurchaseOrderItem {
  id: number;
  item_name: string;
  quantity_ordered: number;
  quantity_received: number;
  unit_cost_cents: number;
  total_cost_cents: number;
}

interface PurchaseOrder {
  id: number;
  po_number: string;
  status: string;
  currency: string;
  total_cost_cents: number;
  shipping_freight_cents: number;
  customs_duty_cents: number;
  payment_terms: string;
  port_of_entry: string;
  tracking_bol_number: string | null;
  estimated_arrival_at: string | null;
  supplier: { name: string; code: string };
  warehouse: { name: string; code: string };
  items: PurchaseOrderItem[];
}

interface AlibabaTier {
  min_qty: number;
  price_usd: number;
}

interface AlibabaProduct {
  item_id: string;
  title: string;
  supplier_name: string;
  verified_supplier: boolean;
  image_url: string;
  moq: number;
  lead_time_days: number;
  tiers: AlibabaTier[];
  certifications: string[];
  sample_cost_usd: number;
}

interface ProcurementIndexProps {
  purchaseOrders: {
    data: PurchaseOrder[];
    total: number;
  };
  bulkCatalog: {
    data: AlibabaProduct[];
  };
  warehouses: Array<{ id: number; name: string; code: string }>;
  supplier: { id: number; name: string };
  kpis: {
    active_pos_count: number;
    in_transit_usd: number;
    in_transit_bdt: number;
    customs_pending_count: number;
  };
}

export default function ProcurementIndex({
  purchaseOrders,
  bulkCatalog,
  warehouses,
  supplier,
  kpis,
}: ProcurementIndexProps) {
  const [activeTab, setActiveTab] = useState<'orders' | 'catalog' | 'hybrid'>('orders');
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [selectedAlibabaItem, setSelectedAlibabaItem] = useState<AlibabaProduct | null>(null);

  // Form state for creating a PO
  const [itemName, setItemName] = useState('');
  const [quantity, setQuantity] = useState(200);
  const [unitCostUsd, setUnitCostUsd] = useState(4.80);
  const [warehouseId, setWarehouseId] = useState(warehouses[0]?.id || 1);
  const [portOfEntry, setPortOfEntry] = useState('Chittagong Sea Port');
  const [paymentTerms, setPaymentTerms] = useState('30_70_milestone');
  const [notes, setNotes] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleOpenPoWithAlibaba = (item: AlibabaProduct) => {
    setSelectedAlibabaItem(item);
    setItemName(item.title);
    setQuantity(item.moq);
    setUnitCostUsd(item.tiers[0]?.price_usd || 5.00);
    setShowCreateModal(true);
  };

  const handleCreatePo = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    const unitCostCents = Math.round(unitCostUsd * 100);

    router.post('/admin/procurement/purchase-orders', {
      supplier_id: supplier.id,
      warehouse_id: warehouseId,
      currency: 'USD',
      payment_terms: paymentTerms,
      port_of_entry: portOfEntry,
      notes,
      items: [
        {
          item_name: itemName,
          quantity_ordered: quantity,
          unit_cost_cents: unitCostCents,
        }
      ]
    }, {
      onFinish: () => {
        setIsSubmitting(false);
        setShowCreateModal(false);
      }
    });
  };

  const handleAdvanceStatus = (id: number, currentStatus: string) => {
    const nextStatusMap: Record<string, string> = {
      draft: 'submitted',
      submitted: 'deposit_paid',
      deposit_paid: 'in_production',
      in_production: 'in_transit',
      in_transit: 'customs_clearance',
      customs_clearance: 'received',
    };

    const next = nextStatusMap[currentStatus];
    if (!next) return;

    if (confirm(`Advance Purchase Order status to "${next.replace('_', ' ').toUpperCase()}"?`)) {
      router.post(`/admin/procurement/purchase-orders/${id}/status`, {
        status: next,
      });
    }
  };

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'received': return 'success';
      case 'in_transit':
      case 'customs_clearance': return 'primary';
      case 'in_production':
      case 'deposit_paid': return 'warning';
      default: return 'neutral';
    }
  };

  return (
    <AdminLayout>
      <Head title="B2B Procurement & Purchase Orders — CommerceOS" />

      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
          <div className="flex items-center gap-2">
            <h1 className="text-2xl font-bold tracking-tight text-neutral-900">Procurement & Bulk Sourcing</h1>
            <span className="px-2 py-0.5 text-xs font-semibold rounded bg-neutral-900 text-white">Alibaba B2B</span>
          </div>
          <p className="text-sm text-neutral-500 mt-1">
            Manage bulk fabric & blank procurement, overseas port logistics, and automated warehouse inventory intake.
          </p>
        </div>

        <Button
          onClick={() => {
            setSelectedAlibabaItem(null);
            setItemName('Custom French Terry Blanks');
            setQuantity(200);
            setUnitCostUsd(4.80);
            setShowCreateModal(true);
          }}
          className="flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          Create Purchase Order
        </Button>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Active Purchase Orders</span>
            <FileText className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{kpis.active_pos_count} Batches</div>
          <div className="text-xs text-neutral-500 mt-1">In production or international transit</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">In-Transit Value (USD)</span>
            <Ship className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">${kpis.in_transit_usd.toLocaleString()}</div>
          <div className="text-xs text-neutral-500 mt-1">৳{kpis.in_transit_bdt.toLocaleString()} BDT landed</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Customs Clearance</span>
            <AlertCircle className="h-4 w-4 text-amber-500" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{kpis.customs_pending_count} Shipments</div>
          <div className="text-xs text-amber-600 font-medium mt-1">Chittagong Port inspection</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Average Sea Lead Time</span>
            <Clock className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">18–24 Days</div>
          <div className="text-xs text-neutral-500 mt-1">Ningbo/Shanghai to Chittagong</div>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-neutral-200 mb-6 flex gap-6">
        <button
          onClick={() => setActiveTab('orders')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'orders'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          Purchase Orders ({purchaseOrders.total})
        </button>
        <button
          onClick={() => setActiveTab('catalog')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'catalog'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          Alibaba Wholesale Blanks ({bulkCatalog.data.length})
        </button>
        <button
          onClick={() => setActiveTab('hybrid')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'hybrid'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          Hybrid Sourcing Engine Rules
        </button>
      </div>

      {/* TAB 1: PURCHASE ORDERS TABLE */}
      {activeTab === 'orders' && (
        <div className="bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="bg-neutral-50 text-neutral-600 text-xs uppercase tracking-wider border-b border-neutral-200">
                <tr>
                  <th className="px-4 py-3">PO Number</th>
                  <th className="px-4 py-3">Supplier & Port</th>
                  <th className="px-4 py-3">Items & Qty</th>
                  <th className="px-4 py-3">Total Value</th>
                  <th className="px-4 py-3">Status</th>
                  <th className="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-200">
                {purchaseOrders.data.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="px-4 py-8 text-center text-neutral-500 text-sm">
                      No purchase orders recorded yet. Create one above to initiate wholesale batch restocking.
                    </td>
                  </tr>
                ) : (
                  purchaseOrders.data.map((po) => {
                    const totalQty = po.items.reduce((acc, i) => acc + i.quantity_ordered, 0);
                    const totalUsd = (po.total_cost_cents + po.shipping_freight_cents + po.customs_duty_cents) / 100;

                    return (
                      <tr key={po.id} className="hover:bg-neutral-50 transition-colors">
                        <td className="px-4 py-3">
                          <div className="font-bold text-neutral-900 font-mono text-xs">{po.po_number}</div>
                          <div className="text-[11px] text-neutral-500">Dest: {po.warehouse.name}</div>
                        </td>
                        <td className="px-4 py-3">
                          <div className="font-medium text-neutral-900 text-xs">{po.supplier.name}</div>
                          <div className="text-[11px] text-neutral-500 flex items-center gap-1 mt-0.5">
                            <Ship className="h-3 w-3 text-neutral-400" />
                            {po.port_of_entry}
                          </div>
                        </td>
                        <td className="px-4 py-3">
                          <div className="text-xs text-neutral-900 font-medium">{po.items[0]?.item_name}</div>
                          <div className="text-[11px] text-neutral-500">{totalQty.toLocaleString()} units ordered</div>
                        </td>
                        <td className="px-4 py-3">
                          <div className="font-semibold text-neutral-900">${totalUsd.toLocaleString()} USD</div>
                          <div className="text-[11px] text-neutral-500">৳{Math.round(totalUsd * 122).toLocaleString()} BDT</div>
                        </td>
                        <td className="px-4 py-3">
                          <Badge variant={getStatusBadgeVariant(po.status)}>
                            {po.status.replace('_', ' ').toUpperCase()}
                          </Badge>
                        </td>
                        <td className="px-4 py-3 text-right">
                          {po.status !== 'received' && po.status !== 'cancelled' ? (
                            <Button
                              variant="outline"
                              size="sm"
                              onClick={() => handleAdvanceStatus(po.id, po.status)}
                              className="text-xs"
                            >
                              Advance Status →
                            </Button>
                          ) : (
                            <span className="text-xs text-emerald-600 font-semibold flex items-center justify-end gap-1">
                              <CheckCircle2 className="h-3.5 w-3.5" />
                              Stock Received
                            </span>
                          )}
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* TAB 2: ALIBABA WHOLESALE CATALOG */}
      {activeTab === 'catalog' && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {bulkCatalog.data.map((item) => (
            <div key={item.item_id} className="border border-neutral-200 rounded-xl bg-white overflow-hidden shadow-sm flex flex-col justify-between">
              <div>
                <div className="relative aspect-4/3 bg-neutral-100 overflow-hidden">
                  <img src={item.image_url} alt="" className="w-full h-full object-cover" />
                  <div className="absolute top-2 left-2 bg-neutral-900/80 backdrop-blur-sm text-white text-[11px] font-medium px-2 py-0.5 rounded">
                    MOQ: {item.moq} Units
                  </div>
                  {item.verified_supplier && (
                    <div className="absolute top-2 right-2 bg-emerald-600/90 text-white text-[10px] font-semibold px-2 py-0.5 rounded flex items-center gap-1">
                      <ShieldCheck className="h-3 w-3" />
                      Trade Assurance
                    </div>
                  )}
                </div>

                <div className="p-4">
                  <div className="text-xs text-neutral-500 font-medium mb-1">{item.supplier_name}</div>
                  <h3 className="font-semibold text-neutral-900 text-sm line-clamp-2 mb-3">{item.title}</h3>

                  <div className="bg-neutral-50 rounded-lg p-3 text-xs mb-4 border border-neutral-100">
                    <span className="font-semibold text-neutral-700 block mb-1.5">Wholesale Volume Tiers:</span>
                    <div className="space-y-1">
                      {item.tiers.map((t, idx) => (
                        <div key={idx} className="flex justify-between text-neutral-600">
                          <span>{t.min_qty}+ pieces:</span>
                          <span className="font-bold text-neutral-900">${t.price_usd.toFixed(2)} USD / pc</span>
                        </div>
                      ))}
                    </div>
                  </div>

                  <div className="flex flex-wrap gap-1.5 mb-2">
                    {item.certifications.map((c, i) => (
                      <span key={i} className="px-2 py-0.5 text-[10px] font-medium bg-neutral-100 text-neutral-600 rounded">
                        {c}
                      </span>
                    ))}
                  </div>
                </div>
              </div>

              <div className="p-4 pt-0">
                <Button
                  size="sm"
                  className="w-full flex items-center justify-center gap-2"
                  onClick={() => handleOpenPoWithAlibaba(item)}
                >
                  <Plus className="h-3.5 w-3.5" />
                  Generate Purchase Order
                </Button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* TAB 3: HYBRID SOURCING RULES */}
      {activeTab === 'hybrid' && (
        <div className="space-y-6 max-w-4xl">
          <div className="bg-white border border-neutral-200 rounded-xl p-6 shadow-sm">
            <h3 className="font-bold text-lg text-neutral-900 mb-2">CommerceOS Multi-Stream Hybrid Fulfillment Architecture</h3>
            <p className="text-sm text-neutral-600 mb-6">
              When a customer places an order on Nazeefa, every line item is automatically inspected by the{' '}
              <code className="text-xs bg-neutral-100 px-1 py-0.5 rounded text-neutral-800">RouteOrderFulfillmentAction</code>{' '}
              to optimize delivery speed and reduce landed supply costs:
            </p>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="p-4 rounded-xl border border-neutral-200 bg-neutral-50">
                <div className="flex items-center gap-2 font-bold text-neutral-900 text-sm mb-1">
                  <Package className="h-4 w-4 text-emerald-600" />
                  1. Local Warehouse
                </div>
                <p className="text-xs text-neutral-600 leading-relaxed">
                  Fulfilled directly from <strong>Dhaka Central Distribution Hub</strong>. Dispatched via Pathao or Steadfast courier with 24–48h delivery across Bangladesh.
                </p>
              </div>

              <div className="p-4 rounded-xl border border-neutral-200 bg-neutral-50">
                <div className="flex items-center gap-2 font-bold text-neutral-900 text-sm mb-1">
                  <Sparkles className="h-4 w-4 text-purple-600" />
                  2. Print-On-Demand (POD)
                </div>
                <p className="text-xs text-neutral-600 leading-relaxed">
                  Customized apparel routed to the <strong>Factory Floor Kanban</strong> for Direct-To-Film (DTF) printing. Completed in 2–3 business days then packed for courier dispatch.
                </p>
              </div>

              <div className="p-4 rounded-xl border border-neutral-200 bg-neutral-50">
                <div className="flex items-center gap-2 font-bold text-neutral-900 text-sm mb-1">
                  <Ship className="h-4 w-4 text-blue-600" />
                  3. Dropship / Split
                </div>
                <p className="text-xs text-neutral-600 leading-relaxed">
                  International items dispatched via <strong>CJ Dropshipping packet</strong>. Mixed carts are automatically handled via Split Shipment with independent tracking numbers.
                </p>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* CREATE PURCHASE ORDER MODAL */}
      {showCreateModal && (
        <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-neutral-200">
            <h3 className="font-bold text-lg text-neutral-900 mb-1">New B2B Purchase Order</h3>
            <p className="text-xs text-neutral-500 mb-4">Supplier: {supplier.name} • Currency: USD</p>

            <form onSubmit={handleCreatePo} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-neutral-700 mb-1">Item Specification</label>
                <input
                  type="text"
                  value={itemName}
                  onChange={(e) => setItemName(e.target.value)}
                  className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                  required
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">Quantity (Units)</label>
                  <input
                    type="number"
                    min="1"
                    value={quantity}
                    onChange={(e) => setQuantity(parseInt(e.target.value))}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                    required
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">Unit Price ($ USD)</label>
                  <input
                    type="number"
                    step="0.01"
                    min="0.10"
                    value={unitCostUsd}
                    onChange={(e) => setUnitCostUsd(parseFloat(e.target.value))}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                    required
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">Destination Warehouse</label>
                  <select
                    value={warehouseId}
                    onChange={(e) => setWarehouseId(parseInt(e.target.value))}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                  >
                    {warehouses.map((w) => (
                      <option key={w.id} value={w.id}>{w.name}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">Port of Entry</label>
                  <select
                    value={portOfEntry}
                    onChange={(e) => setPortOfEntry(e.target.value)}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                  >
                    <option value="Chittagong Sea Port">Chittagong Sea Port</option>
                    <option value="Dhaka Airport Cargo">Dhaka Airport Cargo</option>
                    <option value="Benapole Land Port">Benapole Land Port</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-neutral-700 mb-1">Payment Milestone Terms</label>
                <select
                  value={paymentTerms}
                  onChange={(e) => setPaymentTerms(e.target.value)}
                  className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                >
                  <option value="30_70_milestone">30% Deposit upon PO, 70% Pre-Shipment</option>
                  <option value="100_advance">100% Advance Payment</option>
                  <option value="net_30">Net 30 Days (Letter of Credit)</option>
                </select>
              </div>

              {/* Total Calculation Preview */}
              <div className="bg-neutral-900 text-white rounded-xl p-3.5 space-y-1.5 text-xs">
                <div className="flex justify-between text-neutral-400">
                  <span>Manufacturing Subtotal:</span>
                  <span>${(quantity * unitCostUsd).toFixed(2)} USD</span>
                </div>
                <div className="flex justify-between text-neutral-400">
                  <span>Est. Sea Freight + 15% Duty:</span>
                  <span>${(350 + (quantity * unitCostUsd * 0.15)).toFixed(2)} USD</span>
                </div>
                <div className="flex justify-between text-sm font-bold border-t border-neutral-800 pt-1.5 mt-1.5 text-emerald-300">
                  <span>Est. Landed Total:</span>
                  <span>৳{Math.round((quantity * unitCostUsd * 1.15 + 350) * 122).toLocaleString()} BDT</span>
                </div>
              </div>

              <div className="flex justify-end gap-3 pt-2">
                <Button variant="outline" size="sm" type="button" onClick={() => setShowCreateModal(false)}>
                  Cancel
                </Button>
                <Button size="sm" type="submit" disabled={isSubmitting}>
                  {isSubmitting ? 'Creating PO...' : 'Submit Purchase Order'}
                </Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </AdminLayout>
  );
}
