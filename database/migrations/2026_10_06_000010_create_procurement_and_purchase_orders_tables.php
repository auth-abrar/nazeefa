<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('po_number')->unique();
            $table->string('status')->default('draft'); // draft, submitted, deposit_paid, in_production, in_transit, customs_clearance, received, cancelled
            $table->string('currency')->default('USD');
            $table->unsignedBigInteger('total_cost_cents')->default(0);
            $table->unsignedBigInteger('shipping_freight_cents')->default(0);
            $table->unsignedBigInteger('customs_duty_cents')->default(0);
            $table->string('payment_terms')->default('30_70_milestone'); // 30_70_milestone, 100_advance, net_30
            $table->timestamp('deposit_paid_at')->nullable();
            $table->timestamp('balance_paid_at')->nullable();
            $table->timestamp('estimated_arrival_at')->nullable();
            $table->timestamp('actual_received_at')->nullable();
            $table->string('port_of_entry')->default('Chittagong Sea Port'); // Chittagong Sea Port, Dhaka Airport Cargo, Benapole Land Port
            $table->string('tracking_bol_number')->nullable(); // Bill of Lading or Airway Bill
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->string('external_sku')->nullable();
            $table->unsignedInteger('quantity_ordered');
            $table->unsignedInteger('quantity_received')->default(0);
            $table->unsignedInteger('unit_cost_cents');
            $table->unsignedBigInteger('total_cost_cents');
            $table->timestamps();
        });

        // Add hybrid fulfillment tracking columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'fulfillment_strategy')) {
                $table->string('fulfillment_strategy')->default('local_warehouse')->after('status'); // local_warehouse, print_on_demand, dropship, hybrid
            }
            if (!Schema::hasColumn('orders', 'is_split_shipment')) {
                $table->boolean('is_split_shipment')->default(false)->after('fulfillment_strategy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['fulfillment_strategy', 'is_split_shipment']);
        });
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
