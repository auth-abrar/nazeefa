<?php

namespace App\Domain\POD\Actions;

use App\Domain\Orders\Models\OrderItem;
use App\Domain\POD\Models\Artwork;
use App\Domain\POD\Models\ProductionJob;
use App\Enums\ProductionStatus;
use Illuminate\Support\Str;

class CreateProductionJobAction
{
    public function execute(OrderItem $orderItem, ?Artwork $artwork = null, string $printMethod = 'DTF'): ProductionJob
    {
        $jobNumber = 'NZ-JOB-' . date('Ym') . '-' . strtoupper(Str::random(5));

        return ProductionJob::create([
            'job_number' => $jobNumber,
            'order_id' => $orderItem->order_id,
            'order_item_id' => $orderItem->id,
            'artwork_id' => $artwork?->id,
            'print_method' => $printMethod,
            'status' => ProductionStatus::QUEUED,
            'production_cost' => 35000, // Estimated ৳ 350 base DTF print cost
        ]);
    }
}
