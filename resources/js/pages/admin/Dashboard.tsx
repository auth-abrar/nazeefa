import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';
import { AdminKpiCard } from '@/components/admin/AdminKpiCard';
import { AlertCircle, Truck, Package, ArrowRight } from 'lucide-react';

interface DashboardProps {
  metrics: {
    todaySalesBDT: number;
    totalOrdersToday: number;
    aovBDT: number;
    ordersToDispatchCount: number;
    pendingCodBDT: number;
  };
  lowStockVariants: Array<{
    id: number;
    sku: string;
    size: string;
    color_name: string;
    stock_on_hand: number;
    product: { name: string };
  }>;
  recentOrders: Array<{
    id: number;
    order_number: string;
    guest_name: string;
    status: string;
    grand_total: number;
    created_at: string;
  }>;
}

export default function Dashboard({ metrics, lowStockVariants, recentOrders }: DashboardProps) {
  return (
    <AdminLayout>
      <Head title="Executive Command Hub — CommerceOS Nazeefa" />

      {/* KPI Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <AdminKpiCard
          title="Today's Gross Sales"
          value={`৳ ${metrics.todaySalesBDT.toLocaleString('en-US')}`}
          subtitle={`${metrics.totalOrdersToday} orders placed today`}
          badge="Live"
          badgeType="success"
        />
        <AdminKpiCard
          title="Average Order Value"
          value={`৳ ${metrics.aovBDT.toLocaleString('en-US')}`}
          subtitle="Net revenue per order"
        />
        <AdminKpiCard
          title="Awaiting Dispatch"
          value={metrics.ordersToDispatchCount}
          subtitle="Pathao & Steadfast ready"
          badge="Action"
          badgeType={metrics.ordersToDispatchCount > 0 ? 'warning' : 'default'}
        />
        <AdminKpiCard
          title="Pending COD to Collect"
          value={`৳ ${metrics.pendingCodBDT.toLocaleString('en-US')}`}
          subtitle="Cash in transit with couriers"
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Recent Orders Action Desk */}
        <div className="lg:col-span-2 bg-white rounded-[var(--radius-lg)] border border-neutral-200 p-6 shadow-sm">
          <div className="flex items-center justify-between pb-4 border-b border-neutral-200 mb-4">
            <h3 className="text-sm font-semibold uppercase tracking-wider text-neutral-900">
              Recent Customer Orders
            </h3>
            <Link href="/admin/orders" className="text-xs font-semibold uppercase text-neutral-600 hover:text-black flex items-center gap-1">
              View All Orders <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead>
                <tr className="border-b border-neutral-200 text-neutral-400 font-mono uppercase">
                  <th className="py-2.5">Order</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th>Total BDT</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-100">
                {recentOrders.map((order) => (
                  <tr key={order.id} className="hover:bg-neutral-50">
                    <td className="py-3 font-mono font-bold text-neutral-900">{order.order_number}</td>
                    <td>{order.guest_name}</td>
                    <td>
                      <span className="px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-neutral-100 text-neutral-800">
                        {order.status}
                      </span>
                    </td>
                    <td className="font-mono font-semibold">
                      ৳ {(order.grand_total / 100).toLocaleString('en-US')}
                    </td>
                    <td>
                      <Link
                        href={`/admin/orders/${order.id}`}
                        className="text-xs font-semibold text-blue-600 hover:underline"
                      >
                        Inspect
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Low Stock Attention List */}
        <div className="bg-white rounded-[var(--radius-lg)] border border-neutral-200 p-6 shadow-sm">
          <div className="flex items-center gap-2 pb-4 border-b border-neutral-200 mb-4">
            <AlertCircle className="w-4 h-4 text-amber-600" />
            <h3 className="text-sm font-semibold uppercase tracking-wider text-neutral-900">
              Low Stock Warnings
            </h3>
          </div>

          <div className="space-y-4">
            {lowStockVariants.map((v) => (
              <div key={v.id} className="flex justify-between items-center text-xs pb-3 border-b border-neutral-100 last:border-0">
                <div>
                  <h4 className="font-medium text-neutral-900 line-clamp-1">{v.product.name}</h4>
                  <p className="text-[11px] text-neutral-400 font-mono mt-0.5">{v.sku} ({v.size})</p>
                </div>
                <span className="font-mono font-bold text-red-600 px-2 py-0.5 bg-red-50 rounded">
                  {v.stock_on_hand} left
                </span>
              </div>
            ))}
          </div>

          <div className="mt-6 pt-4 border-t border-neutral-200">
            <Link
              href="/admin/inventory"
              className="w-full inline-block text-center text-xs font-semibold uppercase tracking-wider bg-neutral-100 hover:bg-neutral-200 py-2.5 rounded text-neutral-800"
            >
              Open Inventory Desk
            </Link>
          </div>
        </div>
      </div>
    </AdminLayout>
  );
}
