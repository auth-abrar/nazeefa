<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // CJ Dropshipping, Alibaba, etc.
            $table->string('code')->unique(); // cj_dropshipping, alibaba
            $table->string('provider_class');
            $table->string('api_endpoint')->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('access_token')->nullable();
            $table->timestamp('access_token_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('reliability_rating', 3, 2)->default(4.50);
            $table->unsignedInteger('lead_time_days')->default(10);
            $table->json('config')->nullable(); // exchange rate, default warehouse, auto-fulfillment toggles
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // Local linked catalog product
            $table->string('external_product_id'); // e.g. CJ 'pid'
            $table->string('external_sku')->nullable();
            $table->string('title');
            $table->string('image_url')->nullable();
            $table->unsignedInteger('supplier_cost_cents')->default(0); // Wholesale cost in USD cents
            $table->string('supplier_currency')->default('USD');
            $table->unsignedInteger('estimated_freight_cents')->default(0); // Shipping cost in USD cents
            $table->json('raw_payload')->nullable(); // Full payload specs from CJ/Alibaba
            $table->string('status')->default('draft'); // draft, mapped, ignored
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'external_product_id']);
            $table->index('status');
        });

        Schema::create('supplier_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_variant_id'); // e.g. CJ 'vid'
            $table->string('external_sku')->nullable();
            $table->string('variant_name'); // e.g. "Black / XL"
            $table->unsignedInteger('supplier_cost_cents')->default(0);
            $table->unsignedInteger('supplier_stock')->default(0);
            $table->unsignedInteger('weight_grams')->default(300);
            $table->timestamps();

            $table->unique(['supplier_product_id', 'external_variant_id']);
        });

        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('external_order_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->default('pending'); // pending, created, paid, processing, shipped, delivered, cancelled
            $table->string('tracking_number')->nullable();
            $table->string('tracking_carrier')->nullable();
            $table->unsignedInteger('supplier_cost_total_cents')->default(0);
            $table->unsignedInteger('supplier_freight_total_cents')->default(0);
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_orders');
        Schema::dropIfExists('supplier_variants');
        Schema::dropIfExists('supplier_products');
        Schema::dropIfExists('suppliers');
    }
};
