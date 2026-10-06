import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { StorefrontLayout } from '@/layouts/StorefrontLayout';
import { CheckCircle2, Package, ArrowRight, MessageCircle } from 'lucide-react';

interface ConfirmationProps {
  order: {
    order_number: string;
    guest_name: string;
    guest_phone: string;
    grand_total: number;
    payment_method: string;
    items: Array<{
      product_name: string;
      size: string;
      color: string;
      quantity: number;
      unit_price: number;
    }>;
    address: {
      district: string;
      thana_area: string;
      street_address: string;
    };
  };
}

export default function Confirmation({ order }: ConfirmationProps) {
  const totalBDT = order.grand_total / 100;

  return (
    <StorefrontLayout>
      <Head title={`Order Confirmed #${order.order_number} — Nazeefa Dhaka`} />

      <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <CheckCircle2 className="w-16 h-16 text-green-700 mx-auto" />
        <span className="text-xs font-semibold uppercase tracking-widest text-green-700 mt-4 block">
          Order Successfully Placed
        </span>
        <h1 className="font-serif text-3xl font-bold tracking-tight mt-1">Thank You, {order.guest_name}!</h1>
        <p className="text-sm text-neutral-500 mt-2">
          Your order is confirmed and will be dispatched promptly via Pathao / Steadfast courier.
        </p>

        {/* Order Number Badge */}
        <div className="mt-8 bg-neutral-100 border border-neutral-300 rounded-[var(--radius-md)] p-4 inline-block">
          <span className="text-xs text-neutral-500 uppercase block tracking-wider">Tracking Order Number</span>
          <span className="font-mono text-xl font-bold text-neutral-900">{order.order_number}</span>
        </div>

        {/* Order Details Card */}
        <div className="mt-10 bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)] text-left space-y-4">
          <h3 className="font-semibold text-sm uppercase tracking-wider border-b border-neutral-200 pb-2">
            Delivery Information
          </h3>
          <p className="text-xs text-neutral-700">
            <strong>Recipient:</strong> {order.guest_name} ({order.guest_phone})
          </p>
          <p className="text-xs text-neutral-700">
            <strong>Address:</strong> {order.address.street_address}, {order.address.thana_area}, {order.address.district}
          </p>
          <p className="text-xs text-neutral-700">
            <strong>Payment Method:</strong> Cash on Delivery (৳ {totalBDT.toLocaleString('en-US')})
          </p>
        </div>

        {/* Action Buttons */}
        <div className="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            href={`/track-order?order_number=${order.order_number}&phone=${order.guest_phone}`}
            className="inline-flex items-center justify-center gap-2 bg-black text-white px-6 py-3 rounded-[var(--radius-md)] text-sm font-medium hover:bg-neutral-800"
          >
            <Package className="w-4 h-4" />
            Track Real-Time Status
          </Link>
          <a
            href={`https://wa.me/8801700000000?text=Hello%20Nazeefa,%20my%20order%20is%20${order.order_number}`}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center justify-center gap-2 border border-neutral-300 text-neutral-800 px-6 py-3 rounded-[var(--radius-md)] text-sm font-medium hover:bg-neutral-100"
          >
            <MessageCircle className="w-4 h-4 text-green-700" />
            WhatsApp Customer Support
          </a>
        </div>
      </div>
    </StorefrontLayout>
  );
}
