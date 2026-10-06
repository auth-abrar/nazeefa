import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { StorefrontLayout } from '@/layouts/StorefrontLayout';
import { BangladeshLocationSelector } from '@/components/storefront/BangladeshLocationSelector';
import { Button } from '@/components/common/Button';
import { ShieldCheck, Truck, Banknote } from 'lucide-react';

export default function Checkout() {
  const [formData, setFormData] = useState({
    full_name: '',
    phone: '',
    email: '',
    district: 'Dhaka',
    thana_area: '',
    street_address: '',
    landmark: '',
    payment_method: 'cod',
    notes: '',
  });

  const [isSubmitting, setIsSubmitting] = useState(false);

  // Default cart item for checkout test flow (Signature Oversized Tee)
  const cartItems = [
    {
      variant_id: 1,
      name: 'Nazeefa Signature Oversized Heavy Tee',
      size: 'L',
      color: 'Onyx Black',
      price_bdt: 1250,
      quantity: 1,
      image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=300&q=80',
    },
  ];

  const subtotal = cartItems.reduce((acc, item) => acc + item.price_bdt * item.quantity, 0);
  const isInsideDhaka = formData.district.toLowerCase().trim() === 'dhaka';
  const shippingCharge = subtotal >= 2500 ? 0 : (isInsideDhaka ? 70 : 130);
  const grandTotal = subtotal + shippingCharge;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    router.post('/checkout', {
      ...formData,
      items: cartItems.map(item => ({
        variant_id: item.variant_id,
        quantity: item.quantity,
      })),
    }, {
      onFinish: () => setIsSubmitting(false),
    });
  };

  return (
    <StorefrontLayout>
      <Head title="Checkout — Cash on Delivery | Nazeefa Dhaka" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 className="font-serif text-3xl font-bold tracking-tight mb-8">Direct Express Checkout</h1>

        <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-12 gap-12">
          {/* Customer & Shipping Information (7 cols) */}
          <div className="lg:col-span-7 space-y-8">
            {/* Contact details */}
            <div className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)]">
              <h2 className="text-base font-semibold uppercase tracking-wider mb-4 flex items-center gap-2">
                <span>1. Contact & Recipient</span>
              </h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-xs font-medium text-neutral-700 mb-1">Full Name *</label>
                  <input
                    type="text"
                    required
                    value={formData.full_name}
                    onChange={e => setFormData({ ...formData, full_name: e.target.value })}
                    placeholder="e.g. Abrar Hossain"
                    className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black"
                  />
                </div>
                <div>
                  <label className="block text-xs font-medium text-neutral-700 mb-1">
                    Bangladesh Mobile Number *
                  </label>
                  <input
                    type="tel"
                    required
                    value={formData.phone}
                    onChange={e => setFormData({ ...formData, phone: e.target.value })}
                    placeholder="017XXXXXXXX or 018XXXXXXXX"
                    className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black"
                  />
                  <p className="text-[11px] text-neutral-500 mt-1">Used for courier OTP & delivery confirmation.</p>
                </div>
                <div>
                  <label className="block text-xs font-medium text-neutral-700 mb-1">Email (Optional)</label>
                  <input
                    type="email"
                    value={formData.email}
                    onChange={e => setFormData({ ...formData, email: e.target.value })}
                    placeholder="For invoice copy"
                    className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black"
                  />
                </div>
              </div>
            </div>

            {/* Delivery Address */}
            <div className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)]">
              <h2 className="text-base font-semibold uppercase tracking-wider mb-4 flex items-center gap-2">
                <span>2. Delivery Address</span>
              </h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-xs font-medium text-neutral-700 mb-1">District *</label>
                  <BangladeshLocationSelector
                    value={formData.district}
                    onChange={district => setFormData({ ...formData, district })}
                  />
                </div>
                <div>
                  <label className="block text-xs font-medium text-neutral-700 mb-1">Thana / Area *</label>
                  <input
                    type="text"
                    required
                    value={formData.thana_area}
                    onChange={e => setFormData({ ...formData, thana_area: e.target.value })}
                    placeholder="e.g. Dhanmondi, Mirpur, Uttara, Banani..."
                    className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black"
                  />
                </div>
                <div>
                  <label className="block text-xs font-medium text-neutral-700 mb-1">Detailed Street Address *</label>
                  <textarea
                    required
                    rows={2}
                    value={formData.street_address}
                    onChange={e => setFormData({ ...formData, street_address: e.target.value })}
                    placeholder="House, Road number, Flat / Apartment level..."
                    className="w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black"
                  />
                </div>
              </div>
            </div>

            {/* Payment Method */}
            <div className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)]">
              <h2 className="text-base font-semibold uppercase tracking-wider mb-4 flex items-center gap-2">
                <span>3. Payment Choice</span>
              </h2>
              <div className="space-y-3">
                <label className="flex items-center gap-3 p-3.5 border border-black rounded-[var(--radius-md)] bg-white cursor-pointer">
                  <input
                    type="radio"
                    name="payment_method"
                    value="cod"
                    checked={formData.payment_method === 'cod'}
                    onChange={() => setFormData({ ...formData, payment_method: 'cod' })}
                    className="accent-black"
                  />
                  <Banknote className="w-5 h-5 text-neutral-800" />
                  <div className="flex-1">
                    <span className="text-sm font-semibold block">Cash on Delivery (COD)</span>
                    <span className="text-xs text-neutral-500">Pay cash directly to Pathao / Steadfast upon package receipt.</span>
                  </div>
                </label>
              </div>
            </div>
          </div>

          {/* Order Summary (5 cols) */}
          <div className="lg:col-span-5">
            <div className="bg-[var(--surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--border)] sticky top-24">
              <h2 className="text-base font-semibold uppercase tracking-wider mb-6">Order Summary</h2>
              
              {/* Items List */}
              <div className="divide-y divide-neutral-200">
                {cartItems.map((item, idx) => (
                  <div key={idx} className="py-4 flex gap-4 first:pt-0">
                    <img src={item.image} alt={item.name} className="w-16 h-20 object-cover rounded bg-neutral-100" />
                    <div className="flex-1 text-xs">
                      <h4 className="font-medium text-sm text-neutral-900 line-clamp-1">{item.name}</h4>
                      <p className="text-neutral-500 mt-1">Size: {item.size} • Color: {item.color}</p>
                      <p className="text-neutral-500">Qty: {item.quantity}</p>
                      <p className="font-semibold text-neutral-900 mt-1">৳ {item.price_bdt.toLocaleString('en-US')}</p>
                    </div>
                  </div>
                ))}
              </div>

              {/* Totals */}
              <div className="border-t border-neutral-300 pt-4 mt-4 space-y-2 text-sm">
                <div className="flex justify-between text-neutral-600">
                  <span>Subtotal</span>
                  <span>৳ {subtotal.toLocaleString('en-US')}</span>
                </div>
                <div className="flex justify-between text-neutral-600">
                  <span>Delivery ({isInsideDhaka ? 'Inside Dhaka' : 'Outside Dhaka'})</span>
                  <span>{shippingCharge === 0 ? 'FREE' : `৳ ${shippingCharge}`}</span>
                </div>
                <div className="border-t border-neutral-300 pt-3 flex justify-between font-bold text-base text-neutral-900">
                  <span>Total (Cash to Pay)</span>
                  <span>৳ {grandTotal.toLocaleString('en-US')}</span>
                </div>
              </div>

              {/* Submit CTA */}
              <div className="mt-6">
                <Button
                  type="submit"
                  variant="primary"
                  size="lg"
                  isLoading={isSubmitting}
                  className="w-full bg-[#111111] text-white hover:bg-neutral-800 py-3.5"
                >
                  Confirm Cash on Delivery Order (৳ {grandTotal.toLocaleString('en-US')})
                </Button>
              </div>

              <div className="mt-4 flex items-center justify-center gap-2 text-xs text-neutral-500">
                <ShieldCheck className="w-4 h-4 text-green-700" />
                <span>100% Genuine Apparel • Verified Dhaka Quality</span>
              </div>
            </div>
          </div>
        </form>
      </div>
    </StorefrontLayout>
  );
}
