<?php

namespace App\Http\Controllers\Admin;

use App\Domain\POD\Models\ProductionJob;
use App\Enums\ProductionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductionAdminController
{
    public function index(Request $request): Response
    {
        $query = ProductionJob::with(['order', 'orderItem', 'artwork'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $jobs = $query->paginate(20)->withQueryString();

        return Inertia::render('admin/ProductionIndex', [
            'jobs' => $jobs,
            'filters' => $request->only(['status']),
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $job = ProductionJob::findOrFail($id);
        $newStatus = ProductionStatus::from($request->input('status'));

        $job->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === ProductionStatus::COMPLETED ? now() : $job->completed_at,
        ]);

        return back()->with('success', "Production job #{$job->job_number} transitioned to {$newStatus->value}.");
    }
}
