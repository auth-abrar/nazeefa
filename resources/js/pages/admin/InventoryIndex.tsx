import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';

interface InventoryProps {
  variants: {
    data: Array<{
      id: number;
      sku: string;
      size: string;
      color_name: string;
      price_amount: number;
      stock_on_hand: number;
      stock_reserved: number;
      product: { name: string };
    }>;
  };
  warehouses: Array<{ id: number; name: string; code: string }>;
}

export default function InventoryIndex({ variants, warehouses }: InventoryProps) {
  return (
    <AdminLayout>
      <Head title="Inventory & Stock Desk — CommerceOS" />

      <div className="space-y-6">
        <div>
          <h1 className="text-xl font-bold uppercase tracking-tight text-neutral-900">
            Multi-Warehouse Inventory Control
          </h1>
          <p className="text-xs text-neutral-500 mt-0.5">
            Real-time double-entry stock ledger across all warehouses
          </p>
        </div>

        {/* Warehouses Bar */}
        <div className="flex gap-4">
          {warehouses.map((wh) => (
            <div key={wh.id} className="bg-white border border-neutral-200 rounded p-4 text-xs">
              <span className="font-mono text-neutral-400 uppercase">{wh.code}</span>
              <h4 className="font-bold text-sm text-neutral-900 mt-0.5">{wh.name}</h4>
            </div>
          ))}
        </div>

        {/* Stock Matrix Table */}
        <div className="bg-white rounded-[var(--radius-lg)] border border-neutral-200 overflow-hidden shadow-sm">
          <table className="w-full text-left text-xs">
            <thead className="bg-neutral-50 border-b border-neutral-200 font-mono uppercase text-neutral-500">
              <tr>
                <th className="py-3 px-4">SKU</th>
                <th>Apparel Product</th>
                <th>Size / Color</th>
                <th>On Hand</th>
                <th>Reserved</th>
                <th>Available</th>
                <th className="text-right px-4">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-neutral-200">
              {variants.data.map((v) => (
                <tr key={v.id} className="hover:bg-neutral-50">
                  <td className="py-3 px-4 font-mono font-bold text-neutral-900">{v.sku}</td>
                  <td>{v.product.name}</td>
                  <td>{v.size} • {v.color_name}</td>
                  <td className="font-mono font-semibold">{v.stock_on_hand}</td>
                  <td className="font-mono text-amber-700">{v.stock_reserved}</td>
                  <td className="font-mono font-bold text-green-700">
                    {Math.max(0, v.stock_on_hand - v.stock_reserved)}
                  </td>
                  <td className="text-right px-4">
                    <button
                      onClick={() => alert(`Adjust stock for ${v.sku}`)}
                      className="text-xs font-semibold text-blue-600 hover:underline"
                    >
                      Adjust
                    </button>
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
