<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('courier_provider'); // pathao, steadfast
            $table->string('consignment_id')->unique();
            $table->string('tracking_code')->unique();
            $table->string('status')->default('pending'); // pending, picked_up, in_transit, out_for_delivery, delivered, returned, cancelled
            $table->unsignedInteger('cod_amount')->default(0); // in poisha
            $table->unsignedInteger('courier_fee')->default(0); // in poisha
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('district');
            $table->text('recipient_address');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('courier_metadata')->nullable();
            $table->timestamps();

            $table->index(['courier_provider', 'status']);
            $table->index('tracking_code');
        });

        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('status'); // current normalized shipment status
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index('shipment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
        Schema::dropIfExists('shipments');
    }
};
