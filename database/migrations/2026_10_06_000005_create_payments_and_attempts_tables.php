<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // cod, sslcommerz, bkash, nagad
            $table->string('transaction_id')->unique();
            $table->unsignedInteger('amount'); // in poisha (e.g. 125000)
            $table->unsignedInteger('fee_amount')->default(0);
            $table->string('currency', 3)->default('BDT');
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->string('bank_tran_id')->nullable();
            $table->string('card_type')->nullable(); // VISA, Mastercard, bKash-bKash...
            $table->json('payload_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['provider', 'status']);
            $table->index('transaction_id');
        });

        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('transaction_id');
            $table->string('status')->default('initiated'); // initiated, completed, failed, cancelled
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('BDT');
            $table->string('gateway_redirect_url')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_attempts');
        Schema::dropIfExists('payments');
    }
};
