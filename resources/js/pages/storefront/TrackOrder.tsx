import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { StorefrontLayout } from '@/layouts/StorefrontLayout';
import { Search, PackageCheck, Truck, CheckCircle2, Clock } from 'lucide-react';

interface TrackOrderProps {
  searchedOrder?: {
    order_number: string;
    status: string;
    guest_name: string;
    grand_total: number;
    created_at: string;
    courier_provider?: string;
    courier_tracking_code?: string;
    items: Array<{
      product_name: string;
      size: string;
      quantity: number;
    }>;
  };
  filters: {
    order_number?: string;
    phone?: string;
  };
}

export default function TrackOrder({ searchedOrder, filters }: TrackOrderProps) {
  const [orderNumber, setOrderNumber] = useState(filters.order_number || '');
  const [phone, setPhone] = useState(filters.phone || '');

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get('/track-order', { order_number: orderNumber, phone });
  };

  const steps = [
    { key: 'confirmed', label: 'Order Confirmed', icon: CheckCircle2 },
    { key: 'processing', label: 'In Production / QC', icon: Clock },
    { key: 'packed', label: 'Packed & Labeled', icon: PackageCheck },
    { key: 'shipped', label: 'Dispatched to Courier', icon: Truck },
    { key: 'delivered', label: 'Delivered', icon: CheckCircle2 },
  ];

  const currentStepIndex = searchedOrder
    ? steps.findIndex(s => s.key === searchedOrder.status)
    : -1;

  return (
    <StorefrontLayout>
      <Head title="Live Order Tracking — Nazeefa Dhaka" />

      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div className="text-center mb-10">
          <h1 className="font-serif text-3xl font-bold tracking-tight">Order Tracking Desk</h1>
          <p className="text-xs text-neutral-500 mt-1 uppercase tracking-wider">
            Check real-time production, packaging & courier delivery milestones
          </p>
        </div>

        {/* Search Bar */}
        <form onSubmit={handleSearch} className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)] max-w-xl mx-auto">
          <div className="space-y-4">
            <div>
              <label className="block text-xs font-medium text-neutral-700 mb-1">Order Number *</label>
              <input
                type="text"
                required
                value={orderNumber}
                onChange={e => setOrderNumber(e.target.value)}
                placeholder="e.g. NZ-202610-XXXXX"
                className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black uppercase font-mono"
              />
            </div>
            <div>
              <label className="block text-xs font-medium text-neutral-700 mb-1">Phone Number (Optional)</label>
              <input
                type="tel"
                value={phone}
                onChange={e => setPhone(e.target.value)}
                placeholder="017XXXXXXXX"
                className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black"
              />
            </div>
            <button
              type="submit"
              className="w-full bg-[#111111] text-white py-2.5 rounded-[var(--radius-md)] text-xs font-semibold uppercase tracking-wider hover:bg-neutral-800 flex items-center justify-center gap-2"
            >
              <Search className="w-4 h-4" /> Check Order Status
            </button>
          </div>
        </form>

        {/* Order Result Card */}
        {searchedOrder && (
          <div className="mt-12 bg-white rounded-[var(--radius-lg)] border border-neutral-300 p-8 shadow-sm">
            <div className="flex flex-col sm:flex-row justify-between sm:items-center border-b border-neutral-200 pb-4 mb-6">
              <div>
                <span className="text-xs text-neutral-400 font-mono">Order Number</span>
                <h3 className="font-mono text-xl font-bold text-neutral-900">{searchedOrder.order_number}</h3>
              </div>
              <div className="mt-2 sm:mt-0 text-right">
                <span className="text-xs text-neutral-400">Total BDT</span>
                <p className="font-bold text-lg text-neutral-900">
                  ৳ {(searchedOrder.grand_total / 100).toLocaleString('en-US')}
                </p>
              </div>
            </div>

            {/* Stepper Progress */}
            <div className="py-6">
              <div className="grid grid-cols-5 gap-2 text-center">
                {steps.map((step, idx) => {
                  const StepIcon = step.icon;
                  const isCompleted = idx <= (currentStepIndex === -1 ? 0 : currentStepIndex);
                  return (
                    <div key={step.key} className="flex flex-col items-center">
                      <div
                        className={`w-10 h-10 rounded-full flex items-center justify-center border-2 mb-2 ${
                          isCompleted
                            ? 'bg-black text-white border-black'
                            : 'bg-white text-neutral-300 border-neutral-200'
                        }`}
                      >
                        <StepIcon className="w-5 h-5" />
                      </div>
                      <span className={`text-[11px] font-medium ${isCompleted ? 'text-black' : 'text-neutral-400'}`}>
                        {step.label}
                      </span>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>
        )}
      </div>
    </StorefrontLayout>
  );
}
