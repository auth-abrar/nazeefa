import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';
import { Button } from '@/components/common/Button';
import { Badge } from '@/components/common/Badge';
import {
  Tag,
  ShoppingBag,
  Send,
  Plus,
  Percent,
  CheckCircle2,
  Clock,
  Sparkles,
  TrendingUp,
  AlertCircle
} from 'lucide-react';

interface Coupon {
  id: number;
  code: string;
  type: string;
  value_amount: number;
  min_spend_amount: number | null;
  max_discount_amount: number | null;
  usage_limit: number | null;
  used_count: number;
  is_active: boolean;
  expires_at: string | null;
}

interface AbandonedCheckout {
  id: number;
  session_id: string;
  customer_phone: string;
  customer_name: string | null;
  district: string | null;
  subtotal_amount: number;
  status: string;
  recovery_sent_count: number;
  created_at: string;
}

interface MarketingIndexProps {
  coupons: {
    data: Coupon[];
    total: number;
  };
  abandonedCheckouts: {
    data: AbandonedCheckout[];
    total: number;
  };
  kpis: {
    active_coupons_count: number;
    abandoned_count: number;
    abandoned_revenue_bdt: number;
    recovered_revenue_bdt: number;
  };
}

export default function MarketingIndex({
  coupons,
  abandonedCheckouts,
  kpis,
}: MarketingIndexProps) {
  const [activeTab, setActiveTab] = useState<'coupons' | 'abandoned'>('coupons');
  const [showCreateCouponModal, setShowCreateCouponModal] = useState(false);
  const [sendingRecoveryId, setSendingRecoveryId] = useState<number | null>(null);

  // New coupon form state
  const [code, setCode] = useState('');
  const [type, setType] = useState('percentage');
  const [value, setValue] = useState(15);
  const [minSpendBdt, setMinSpendBdt] = useState(1500);
  const [maxDiscountBdt, setMaxDiscountBdt] = useState(500);
  const [usageLimit, setUsageLimit] = useState(250);
  const [isSubmittingCoupon, setIsSubmittingCoupon] = useState(false);

  const handleCreateCoupon = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmittingCoupon(true);

    const valueAmount = type === 'percentage' ? Math.round(value * 100) : Math.round(value * 100); // 15% -> 1500, or flat BDT -> poisha

    router.post('/admin/marketing/coupons', {
      code,
      type,
      value_amount: valueAmount,
      min_spend_amount: Math.round(minSpendBdt * 100),
      max_discount_amount: Math.round(maxDiscountBdt * 100),
      usage_limit: usageLimit,
    }, {
      onFinish: () => {
        setIsSubmittingCoupon(false);
        setShowCreateCouponModal(false);
        setCode('');
      }
    });
  };

  const handleSendRecovery = async (id: number) => {
    setSendingRecoveryId(id);
    try {
      const res = await fetch(`/admin/marketing/abandoned/${id}/recovery`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
        }
      });
      const data = await res.json();
      alert(data.message || 'Recovery reminder sent successfully!');
      router.reload();
    } catch (err) {
      console.error(err);
    } finally {
      setSendingRecoveryId(null);
    }
  };

  return (
    <AdminLayout>
      <Head title="Marketing & Abandoned Carts — CommerceOS" />

      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
          <div className="flex items-center gap-2">
            <h1 className="text-2xl font-bold tracking-tight text-neutral-900">Promotions & Cart Recovery</h1>
            <span className="px-2 py-0.5 text-xs font-semibold rounded bg-neutral-900 text-white">Growth</span>
          </div>
          <p className="text-sm text-neutral-500 mt-1">
            Drive higher checkout conversions with targeted promotional vouchers and automated abandoned cart recovery.
          </p>
        </div>

        <Button
          onClick={() => setShowCreateCouponModal(true)}
          className="flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          Create Coupon Campaign
        </Button>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Active Coupons</span>
            <Tag className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{kpis.active_coupons_count} Codes</div>
          <div className="text-xs text-neutral-500 mt-1">Live in checkout verification engine</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Abandoned Carts</span>
            <ShoppingBag className="h-4 w-4 text-amber-500" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{kpis.abandoned_count} Sessions</div>
          <div className="text-xs text-neutral-500 mt-1">Uncompleted customer checkouts</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Potential Abandoned BDT</span>
            <AlertCircle className="h-4 w-4 text-rose-500" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">৳{kpis.abandoned_revenue_bdt.toLocaleString()}</div>
          <div className="text-xs text-neutral-500 mt-1">Recoverable checkout revenue</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Recovered Revenue</span>
            <TrendingUp className="h-4 w-4 text-emerald-500" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">৳{kpis.recovered_revenue_bdt.toLocaleString()}</div>
          <div className="text-xs text-emerald-600 font-semibold mt-1">Converted via recovery SMS</div>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-neutral-200 mb-6 flex gap-6">
        <button
          onClick={() => setActiveTab('coupons')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'coupons'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          Coupons & Vouchers ({coupons.total})
        </button>
        <button
          onClick={() => setActiveTab('abandoned')}
          className={`pb-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'abandoned'
              ? 'border-neutral-900 text-neutral-900'
              : 'border-transparent text-neutral-500 hover:text-neutral-700'
          }`}
        >
          Abandoned Checkouts ({abandonedCheckouts.total})
        </button>
      </div>

      {/* TAB 1: COUPONS */}
      {activeTab === 'coupons' && (
        <div className="bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="bg-neutral-50 text-neutral-600 text-xs uppercase tracking-wider border-b border-neutral-200">
                <tr>
                  <th className="px-4 py-3">Coupon Code</th>
                  <th className="px-4 py-3">Discount Type</th>
                  <th className="px-4 py-3">Value</th>
                  <th className="px-4 py-3">Min Spend & Cap</th>
                  <th className="px-4 py-3">Redemptions</th>
                  <th className="px-4 py-3">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-200">
                {coupons.data.map((c) => (
                  <tr key={c.id} className="hover:bg-neutral-50 transition-colors">
                    <td className="px-4 py-3 font-mono font-bold text-neutral-900">{c.code}</td>
                    <td className="px-4 py-3 text-neutral-600 text-xs uppercase">{c.type.replace('_', ' ')}</td>
                    <td className="px-4 py-3 font-semibold text-neutral-900">
                      {c.type === 'percentage'
                        ? `${c.value_amount / 100}% OFF`
                        : `৳${(c.value_amount / 100).toLocaleString()} OFF`}
                    </td>
                    <td className="px-4 py-3 text-xs text-neutral-500">
                      <div>Min: ৳{c.min_spend_amount ? (c.min_spend_amount / 100).toLocaleString() : '0'}</div>
                      {c.max_discount_amount && (
                        <div>Cap: ৳{(c.max_discount_amount / 100).toLocaleString()}</div>
                      )}
                    </td>
                    <td className="px-4 py-3 text-xs text-neutral-600">
                      {c.used_count} / {c.usage_limit || '∞'} uses
                    </td>
                    <td className="px-4 py-3">
                      <Badge variant={c.is_active ? 'success' : 'neutral'}>
                        {c.is_active ? 'ACTIVE' : 'DISABLED'}
                      </Badge>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* TAB 2: ABANDONED CHECKOUTS */}
      {activeTab === 'abandoned' && (
        <div className="bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="bg-neutral-50 text-neutral-600 text-xs uppercase tracking-wider border-b border-neutral-200">
                <tr>
                  <th className="px-4 py-3">Customer Phone</th>
                  <th className="px-4 py-3">Name & District</th>
                  <th className="px-4 py-3">Cart Subtotal</th>
                  <th className="px-4 py-3">Reminders Sent</th>
                  <th className="px-4 py-3">Status</th>
                  <th className="px-4 py-3 text-right">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-200">
                {abandonedCheckouts.data.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="px-4 py-8 text-center text-neutral-500 text-sm">
                      No abandoned checkouts recorded.
                    </td>
                  </tr>
                ) : (
                  abandonedCheckouts.data.map((item) => (
                    <tr key={item.id} className="hover:bg-neutral-50 transition-colors">
                      <td className="px-4 py-3 font-mono font-semibold text-neutral-900">{item.customer_phone}</td>
                      <td className="px-4 py-3">
                        <div className="text-neutral-900 text-xs font-medium">{item.customer_name || 'Anonymous Shopper'}</div>
                        <div className="text-[11px] text-neutral-500">{item.district || 'Dhaka'}</div>
                      </td>
                      <td className="px-4 py-3 font-semibold text-neutral-900">
                        ৳{(item.subtotal_amount / 100).toLocaleString()} BDT
                      </td>
                      <td className="px-4 py-3 text-xs text-neutral-600">{item.recovery_sent_count} notices</td>
                      <td className="px-4 py-3">
                        <Badge variant={item.status === 'recovered' ? 'success' : 'warning'}>
                          {item.status.toUpperCase()}
                        </Badge>
                      </td>
                      <td className="px-4 py-3 text-right">
                        {item.status !== 'recovered' && (
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleSendRecovery(item.id)}
                            disabled={sendingRecoveryId === item.id}
                            className="text-xs flex items-center gap-1.5 ml-auto"
                          >
                            <Send className="h-3 w-3" />
                            {sendingRecoveryId === item.id ? 'Sending...' : 'Send SMS Recovery'}
                          </Button>
                        )}
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* CREATE COUPON MODAL */}
      {showCreateCouponModal && (
        <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-neutral-200">
            <h3 className="font-bold text-lg text-neutral-900 mb-1">Create Promotional Coupon</h3>
            <p className="text-xs text-neutral-500 mb-4">Set up percentage or flat discounts with minimum spend rules.</p>

            <form onSubmit={handleCreateCoupon} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-neutral-700 mb-1">Coupon Code</label>
                <input
                  type="text"
                  placeholder="e.g. EID2026, SUMMER20"
                  value={code}
                  onChange={(e) => setCode(e.target.value.toUpperCase())}
                  className="w-full text-sm border border-neutral-300 rounded-lg p-2 font-mono uppercase focus:ring-2 focus:ring-neutral-900"
                  required
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">Discount Type</label>
                  <select
                    value={type}
                    onChange={(e) => setType(e.target.value)}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                  >
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed_amount">Flat BDT (৳)</option>
                    <option value="free_shipping">Free Shipping</option>
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">
                    {type === 'percentage' ? 'Percentage (%)' : 'Amount (BDT)'}
                  </label>
                  <input
                    type="number"
                    min="1"
                    value={value}
                    onChange={(e) => setValue(parseFloat(e.target.value))}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                    required
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">Min Spend (BDT)</label>
                  <input
                    type="number"
                    value={minSpendBdt}
                    onChange={(e) => setMinSpendBdt(parseInt(e.target.value))}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-neutral-700 mb-1">Max Cap (BDT)</label>
                  <input
                    type="number"
                    value={maxDiscountBdt}
                    onChange={(e) => setMaxDiscountBdt(parseInt(e.target.value))}
                    className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-neutral-700 mb-1">Max Redemptions</label>
                <input
                  type="number"
                  value={usageLimit}
                  onChange={(e) => setUsageLimit(parseInt(e.target.value))}
                  className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                />
              </div>

              <div className="flex justify-end gap-3 pt-2">
                <Button variant="outline" size="sm" type="button" onClick={() => setShowCreateCouponModal(false)}>
                  Cancel
                </Button>
                <Button size="sm" type="submit" disabled={isSubmittingCoupon}>
                  {isSubmittingCoupon ? 'Saving...' : 'Save Coupon'}
                </Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </AdminLayout>
  );
}
