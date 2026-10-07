<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('customer_phone')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('district')->nullable();
            $table->json('cart_payload'); // products, sizes, quantities, poisha subtotal
            $table->unsignedBigInteger('subtotal_amount')->default(0);
            $table->string('recovery_token')->unique();
            $table->string('status')->default('abandoned'); // abandoned, recovered, expired
            $table->unsignedInteger('recovery_sent_count')->default(0);
            $table->timestamp('recovered_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique(); // Primary commerce identifier in Bangladesh
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('default_district')->nullable();
            $table->unsignedInteger('total_orders_count')->default(0);
            $table->unsignedInteger('delivered_orders_count')->default(0);
            $table->unsignedInteger('returned_orders_count')->default(0);
            $table->unsignedBigInteger('total_spent_amount')->default(0); // LTV in poisha
            $table->decimal('cod_return_risk_score', 3, 2)->default(0.00); // 0.00 to 1.00 ratio
            $table->string('risk_tier')->default('low_risk'); // low_risk, moderate_risk, high_risk, verified_vip
            $table->json('tags')->nullable(); // vip, frequent_buyer, eid_shopper, high_return_risk
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('risk_tier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('abandoned_checkouts');
    }
};
