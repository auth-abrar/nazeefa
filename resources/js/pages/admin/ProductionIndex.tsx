import React from 'react';
import { Head, router } from '@inertiajs/react';
import { AdminLayout } from '@/layouts/AdminLayout';
import { Printer, CheckCircle, Clock, AlertTriangle } from 'lucide-react';

interface ProductionIndexProps {
  jobs: {
    data: Array<{
      id: number;
      job_number: string;
      print_method: string;
      status: string;
      production_cost: number;
      order: { order_number: string };
      order_item: { product_name: string; size: string };
      artwork?: { file_path: string; print_placement: string };
      created_at: string;
    }>;
  };
  filters: { status?: string };
}

export default function ProductionIndex({ jobs }: ProductionIndexProps) {
  const handleStatusChange = (jobId: number, status: string) => {
    router.post(`/admin/pod/jobs/${jobId}/status`, { status });
  };

  const statusColors: Record<string, string> = {
    queued: 'bg-neutral-100 text-neutral-800',
    in_production: 'bg-amber-100 text-amber-800',
    printing: 'bg-blue-100 text-blue-800',
    quality_check: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
  };

  return (
    <AdminLayout>
      <Head title="Print-On-Demand Production Floor — CommerceOS" />

      <div className="space-y-6">
        <div>
          <h1 className="text-xl font-bold uppercase tracking-tight text-neutral-900">
            POD & Apparel Production Floor
          </h1>
          <p className="text-xs text-neutral-500 mt-0.5">
            Manage DTF / Screen printing jobs, quality inspection, and packing hand-off
          </p>
        </div>

        {/* Jobs Table */}
        <div className="bg-white rounded-[var(--radius-lg)] border border-neutral-200 overflow-hidden shadow-sm">
          <table className="w-full text-left text-xs">
            <thead className="bg-neutral-50 border-b border-neutral-200 font-mono uppercase text-neutral-500">
              <tr>
                <th className="py-3 px-4">Job #</th>
                <th>Order Ref</th>
                <th>Garment & Size</th>
                <th>Print Method & Placement</th>
                <th>Artwork</th>
                <th>Status</th>
                <th className="text-right px-4">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-neutral-200">
              {jobs.data.map((job) => (
                <tr key={job.id} className="hover:bg-neutral-50">
                  <td className="py-3 px-4 font-mono font-bold text-neutral-900">{job.job_number}</td>
                  <td className="font-mono text-neutral-600">{job.order.order_number}</td>
                  <td>
                    <p className="font-medium text-neutral-900">{job.order_item.product_name}</p>
                    <span className="font-mono text-[11px] text-neutral-400">Size: {job.order_item.size}</span>
                  </td>
                  <td>
                    <span className="font-bold text-neutral-800 block">{job.print_method}</span>
                    <span className="text-[11px] text-neutral-500 uppercase">{job.artwork?.print_placement || 'Front'}</span>
                  </td>
                  <td>
                    {job.artwork?.file_path ? (
                      <a
                        href={job.artwork.file_path}
                        target="_blank"
                        rel="noreferrer"
                        className="text-blue-600 font-medium underline text-[11px]"
                      >
                        Inspect Proof
                      </a>
                    ) : (
                      <span className="text-neutral-400 text-[11px]">N/A</span>
                    )}
                  </td>
                  <td>
                    <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase ${statusColors[job.status] || 'bg-neutral-100'}`}>
                      {job.status}
                    </span>
                  </td>
                  <td className="text-right px-4">
                    <select
                      value={job.status}
                      onChange={(e) => handleStatusChange(job.id, e.target.value)}
                      className="text-xs border border-neutral-300 rounded px-2 py-1 bg-white font-medium"
                    >
                      <option value="queued">Queued</option>
                      <option value="in_production">In Production</option>
                      <option value="printing">Printing (DTF)</option>
                      <option value="quality_check">Quality Check</option>
                      <option value="ready_for_packaging">Ready for Packaging</option>
                      <option value="completed">Completed</option>
                    </select>
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
