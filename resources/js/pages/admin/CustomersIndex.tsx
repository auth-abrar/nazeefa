import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';
import { Button } from '@/components/common/Button';
import { Badge } from '@/components/common/Badge';
import {
  Users,
  ShieldCheck,
  ShieldAlert,
  Search,
  Phone,
  DollarSign,
  TrendingUp,
  AlertTriangle,
  CheckCircle2,
  FileEdit
} from 'lucide-react';

interface Customer {
  id: number;
  phone: string;
  name: string;
  email: string | null;
  default_district: string | null;
  total_orders_count: number;
  delivered_orders_count: number;
  returned_orders_count: number;
  total_spent_amount: number;
  cod_return_risk_score: number;
  risk_tier: string;
  tags: string[] | null;
  admin_notes: string | null;
}

interface CustomersIndexProps {
  customers: {
    data: Customer[];
    total: number;
  };
  filters: {
    search?: string;
    risk_tier?: string;
  };
  kpis: {
    total_customers: number;
    vip_customers: number;
    high_risk_customers: number;
    total_ltv_bdt: number;
  };
}

export default function CustomersIndex({
  customers,
  filters,
  kpis,
}: CustomersIndexProps) {
  const [search, setSearch] = useState(filters.search || '');
  const [riskFilter, setRiskFilter] = useState(filters.risk_tier || '');
  const [selectedCustomer, setSelectedCustomer] = useState<Customer | null>(null);

  // Edit drawer state
  const [notes, setNotes] = useState('');
  const [riskTier, setRiskTier] = useState('low_risk');
  const [isUpdating, setIsUpdating] = useState(false);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get('/admin/customers', {
      search,
      risk_tier: riskFilter || undefined,
    }, { preserveState: true });
  };

  const handleOpenDrawer = (customer: Customer) => {
    setSelectedCustomer(customer);
    setNotes(customer.admin_notes || '');
    setRiskTier(customer.risk_tier);
  };

  const handleSaveNotes = (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedCustomer) return;

    setIsUpdating(true);
    router.post(`/admin/customers/${selectedCustomer.id}/notes`, {
      admin_notes: notes,
      risk_tier: riskTier,
    }, {
      onFinish: () => {
        setIsUpdating(false);
        setSelectedCustomer(null);
      }
    });
  };

  const getRiskBadge = (tier: string, score: number) => {
    switch (tier) {
      case 'verified_vip':
        return <Badge variant="success">⭐ VERIFIED VIP</Badge>;
      case 'high_risk':
        return <Badge variant="danger">⚠️ HIGH COD RISK ({(score * 100).toFixed(0)}%)</Badge>;
      case 'moderate_risk':
        return <Badge variant="warning">MODERATE ({(score * 100).toFixed(0)}%)</Badge>;
      default:
        return <Badge variant="neutral">LOW RISK ({(score * 100).toFixed(0)}%)</Badge>;
    }
  };

  return (
    <AdminLayout>
      <Head title="Customer CRM & COD Risk Intelligence — CommerceOS" />

      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
          <div className="flex items-center gap-2">
            <h1 className="text-2xl font-bold tracking-tight text-neutral-900">Customer CRM & COD Intelligence</h1>
            <span className="px-2 py-0.5 text-xs font-semibold rounded bg-neutral-900 text-white">Risk Engine</span>
          </div>
          <p className="text-sm text-neutral-500 mt-1">
            Track customer lifetime value, Bangladesh phone records, and eliminate COD return fee losses.
          </p>
        </div>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Total Customers</span>
            <Users className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{kpis.total_customers.toLocaleString()}</div>
          <div className="text-xs text-neutral-500 mt-1">Normalized around BD mobile numbers</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Verified VIPs</span>
            <ShieldCheck className="h-4 w-4 text-emerald-500" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{kpis.vip_customers} High-LTV</div>
          <div className="text-xs text-emerald-600 font-semibold mt-1">৳10,000+ Lifetime Spend</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">High COD Risk Returners</span>
            <ShieldAlert className="h-4 w-4 text-rose-500" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">{kpis.high_risk_customers} Flags</div>
          <div className="text-xs text-rose-600 font-semibold mt-1">Requires advance delivery charge</div>
        </div>

        <div className="p-4 rounded-xl border border-neutral-200 bg-white shadow-sm">
          <div className="flex items-center justify-between text-neutral-500 mb-2">
            <span className="text-xs font-medium uppercase tracking-wider">Cumulative LTV</span>
            <DollarSign className="h-4 w-4 text-neutral-400" />
          </div>
          <div className="text-2xl font-bold text-neutral-900">৳{kpis.total_ltv_bdt.toLocaleString()}</div>
          <div className="text-xs text-neutral-500 mt-1">Total revenue generated</div>
        </div>
      </div>

      {/* Filter and Search Bar */}
      <form onSubmit={handleSearch} className="flex flex-col sm:flex-row gap-3 max-w-3xl mb-6">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
          <input
            type="text"
            placeholder="Search customer phone or name (e.g. 01711...)"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-neutral-900"
          />
        </div>

        <select
          value={riskFilter}
          onChange={(e) => setRiskFilter(e.target.value)}
          className="text-sm rounded-lg border border-neutral-300 py-2 px-3 focus:ring-2 focus:ring-neutral-900"
        >
          <option value="">All Risk Tiers</option>
          <option value="verified_vip">Verified VIP</option>
          <option value="low_risk">Low Risk</option>
          <option value="moderate_risk">Moderate Risk</option>
          <option value="high_risk">High Risk Returners</option>
        </select>

        <Button type="submit" size="sm">
          Filter Customers
        </Button>
      </form>

      {/* Customers Table */}
      <div className="bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm">
            <thead className="bg-neutral-50 text-neutral-600 text-xs uppercase tracking-wider border-b border-neutral-200">
              <tr>
                <th className="px-4 py-3">Customer Phone</th>
                <th className="px-4 py-3">Name & District</th>
                <th className="px-4 py-3">Completed Trips</th>
                <th className="px-4 py-3">Lifetime Value (LTV)</th>
                <th className="px-4 py-3">COD Risk Status</th>
                <th className="px-4 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-neutral-200">
              {customers.data.map((c) => (
                <tr key={c.id} className="hover:bg-neutral-50 transition-colors">
                  <td className="px-4 py-3 font-mono font-semibold text-neutral-900 flex items-center gap-1.5">
                    <Phone className="h-3.5 w-3.5 text-neutral-400" />
                    {c.phone}
                  </td>
                  <td className="px-4 py-3">
                    <div className="font-medium text-neutral-900">{c.name}</div>
                    <div className="text-xs text-neutral-500">{c.default_district || 'Dhaka'}</div>
                  </td>
                  <td className="px-4 py-3 text-xs">
                    <span className="text-emerald-700 font-semibold">{c.delivered_orders_count} delivered</span>
                    {c.returned_orders_count > 0 && (
                      <span className="text-rose-600 ml-1.5">({c.returned_orders_count} returns)</span>
                    )}
                  </td>
                  <td className="px-4 py-3 font-semibold text-neutral-900">
                    ৳{(c.total_spent_amount / 100).toLocaleString()} BDT
                  </td>
                  <td className="px-4 py-3">
                    {getRiskBadge(c.risk_tier, c.cod_return_risk_score)}
                  </td>
                  <td className="px-4 py-3 text-right">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => handleOpenDrawer(c)}
                      className="text-xs flex items-center gap-1.5 ml-auto"
                    >
                      <FileEdit className="h-3 w-3" />
                      Profile & Notes
                    </Button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* CUSTOMER PROFILE & RISK OVERRIDE MODAL */}
      {selectedCustomer && (
        <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-neutral-200">
            <div className="flex items-start justify-between mb-4">
              <div>
                <h3 className="font-bold text-lg text-neutral-900">{selectedCustomer.name}</h3>
                <p className="text-xs font-mono text-neutral-500">{selectedCustomer.phone} • {selectedCustomer.default_district}</p>
              </div>
              <button
                onClick={() => setSelectedCustomer(null)}
                className="text-neutral-400 hover:text-neutral-600 text-sm font-bold"
              >
                ✕
              </button>
            </div>

            {/* Performance Snapshot */}
            <div className="grid grid-cols-3 gap-3 p-3 bg-neutral-50 rounded-xl border border-neutral-200 text-xs mb-4">
              <div>
                <span className="text-neutral-500 block">Delivered</span>
                <span className="font-bold text-emerald-700 text-sm">{selectedCustomer.delivered_orders_count}</span>
              </div>
              <div>
                <span className="text-neutral-500 block">Returns/Rejected</span>
                <span className="font-bold text-rose-700 text-sm">{selectedCustomer.returned_orders_count}</span>
              </div>
              <div>
                <span className="text-neutral-500 block">LTV Spend</span>
                <span className="font-bold text-neutral-900 text-sm">৳{(selectedCustomer.total_spent_amount / 100).toLocaleString()}</span>
              </div>
            </div>

            <form onSubmit={handleSaveNotes} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-neutral-700 mb-1">Risk Classification Override</label>
                <select
                  value={riskTier}
                  onChange={(e) => setRiskTier(e.target.value)}
                  className="w-full text-sm border border-neutral-300 rounded-lg p-2 focus:ring-2 focus:ring-neutral-900"
                >
                  <option value="low_risk">Low Risk (Standard 1-Click Dispatch)</option>
                  <option value="moderate_risk">Moderate Risk</option>
                  <option value="high_risk">High Risk (Requires Advance Delivery Charge)</option>
                  <option value="verified_vip">Verified VIP Shopper</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-semibold text-neutral-700 mb-1">Merchant Support Notes</label>
                <textarea
                  rows={4}
                  value={notes}
                  onChange={(e) => setNotes(e.target.value)}
                  placeholder="Record customer behavior, preferred courier call timing, or return reasons..."
                  className="w-full text-sm border border-neutral-300 rounded-lg p-2.5 focus:ring-2 focus:ring-neutral-900"
                />
              </div>

              <div className="flex justify-end gap-3 pt-2">
                <Button variant="outline" size="sm" type="button" onClick={() => setSelectedCustomer(null)}>
                  Cancel
                </Button>
                <Button size="sm" type="submit" disabled={isUpdating}>
                  {isUpdating ? 'Saving...' : 'Update Customer Profile'}
                </Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </AdminLayout>
  );
}
