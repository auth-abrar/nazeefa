<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('discount_type'); // percentage, fixed
            $table->unsignedInteger('discount_value'); // in percentage or minor units (poisha)
            $table->unsignedInteger('min_order_amount')->default(0);
            $table->unsignedInteger('max_discount_amount')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('times_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. NZ-202610-0012
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name');
            $table->string('guest_phone');
            $table->string('guest_email')->nullable();
            $table->string('status')->default('confirmed'); // OrderStatus enum
            $table->unsignedInteger('items_subtotal'); // in poisha
            $table->unsignedInteger('shipping_amount'); // in poisha (৳70 or ৳130)
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('grand_total'); // in poisha
            $table->string('currency', 3)->default('BDT');
            $table->string('payment_method')->default('cod'); // cod, bkash, nagad, sslcommerz
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('courier_provider')->nullable(); // pathao, steadfast
            $table->string('courier_tracking_code')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('guest_phone');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->constrained();
            $table->string('product_name');
            $table->string('variant_sku');
            $table->string('size');
            $table->string('color');
            $table->unsignedInteger('unit_price'); // in poisha
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('total_price'); // in poisha
            $table->timestamps();
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('district'); // Dhaka, Chittagong, Sylhet, etc.
            $table->string('thana_area')->nullable(); // Dhanmondi, Gulshan, Mirpur...
            $table->text('street_address');
            $table->string('landmark')->nullable();
            $table->boolean('is_inside_dhaka')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
    }
};
