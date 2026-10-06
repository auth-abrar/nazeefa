import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';
import { Search, Truck, Filter } from 'lucide-react';

interface OrdersIndexProps {
  orders: {
    data: Array<{
      id: number;
      order_number: string;
      guest_name: string;
      guest_phone: string;
      status: string;
      payment_method: string;
      payment_status: string;
      grand_total: number;
      courier_tracking_code?: string;
      created_at: string;
    }>;
  };
  filters: { status?: string; search?: string };
}

export default function OrdersIndex({ orders, filters }: OrdersIndexProps) {
  const [search, setSearch] = useState(filters.search || '');

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get('/admin/orders', { search, status: filters.status });
  };

  const handleDispatch = (orderId: number, courier: 'pathao' | 'steadfast') => {
    if (confirm(`Dispatch this order via ${courier.toUpperCase()}?`)) {
      router.post(`/admin/orders/${orderId}/dispatch`, { courier });
    }
  };

  return (
    <AdminLayout>
      <Head title="Order Management Desk — CommerceOS" />

      <div className="space-y-6">
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h1 className="text-xl font-bold uppercase tracking-tight text-neutral-900">
              Orders Operations Desk
            </h1>
            <p className="text-xs text-neutral-500 mt-0.5">Manage statuses, fulfillment, and courier dispatch</p>
          </div>

          {/* Search Bar */}
          <form onSubmit={handleSearch} className="flex gap-2 w-full sm:w-auto">
            <input
              type="text"
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Search by order #, phone..."
              className="text-xs px-3 py-2 border border-neutral-300 rounded bg-white w-64 focus:outline-none focus:ring-1 focus:ring-black"
            />
            <button
              type="submit"
              className="bg-black text-white px-3 py-2 text-xs font-semibold rounded hover:bg-neutral-800"
            >
              Search
            </button>
          </form>
        </div>

        {/* Orders Table */}
        <div className="bg-white rounded-[var(--radius-lg)] border border-neutral-200 overflow-hidden shadow-sm">
          <table className="w-full text-left text-xs">
            <thead className="bg-neutral-50 border-b border-neutral-200 font-mono uppercase text-neutral-500">
              <tr>
                <th className="py-3 px-4">Order #</th>
                <th>Recipient</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Total BDT</th>
                <th>Courier Dispatch</th>
                <th className="text-right px-4">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-neutral-200">
              {orders.data.map((order) => (
                <tr key={order.id} className="hover:bg-neutral-50">
                  <td className="py-3 px-4 font-mono font-bold text-neutral-900">{order.order_number}</td>
                  <td>
                    <div>
                      <p className="font-medium text-neutral-900">{order.guest_name}</p>
                      <p className="text-neutral-400 font-mono text-[11px]">{order.guest_phone}</p>
                    </div>
                  </td>
                  <td>
                    <span className="uppercase font-semibold text-[10px]">
                      {order.payment_method} ({order.payment_status})
                    </span>
                  </td>
                  <td>
                    <span className="px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-neutral-100 text-neutral-800">
                      {order.status}
                    </span>
                  </td>
                  <td className="font-mono font-bold text-neutral-900">
                    ৳ {(order.grand_total / 100).toLocaleString('en-US')}
                  </td>
                  <td>
                    {order.courier_tracking_code ? (
                      <span className="font-mono text-[11px] text-green-700 font-medium">
                        {order.courier_tracking_code}
                      </span>
                    ) : (
                      <div className="flex gap-1.5">
                        <button
                          onClick={() => handleDispatch(order.id, 'pathao')}
                          className="bg-red-50 text-red-700 hover:bg-red-100 text-[10px] font-bold px-2 py-1 rounded"
                        >
                          Pathao
                        </button>
                        <button
                          onClick={() => handleDispatch(order.id, 'steadfast')}
                          className="bg-blue-50 text-blue-700 hover:bg-blue-100 text-[10px] font-bold px-2 py-1 rounded"
                        >
                          Steadfast
                        </button>
                      </div>
                    )}
                  </td>
                  <td className="text-right px-4">
                    <Link
                      href={`/admin/orders/${order.id}`}
                      className="text-xs font-semibold text-neutral-900 hover:underline"
                    >
                      View
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </AdminLayout>
  );
}
