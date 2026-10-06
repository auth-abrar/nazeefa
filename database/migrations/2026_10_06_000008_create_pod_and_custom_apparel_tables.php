<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size_bytes');
            $table->string('print_placement')->default('front'); // front, back, left_chest, right_chest, sleeve
            $table->json('canvas_metadata')->nullable(); // scale, rotation, offset_x, offset_y
            $table->timestamps();
        });

        Schema::create('design_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->string('proof_image_url');
            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('needs_review'); // needs_review, sent_to_customer, approved, revision_requested
            $table->text('customer_feedback')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('production_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->unique(); // e.g. NZ-JOB-202610-0012
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('artwork_id')->nullable()->constrained()->nullOnDelete();
            $table->string('print_method')->default('DTF'); // DTF, screen_print, sublimation, embroidery
            $table->string('status')->default('queued'); // ProductionStatus enum: queued, approved, in_production, printing, quality_check, ready_for_packaging, completed, failed
            $table->foreignId('assigned_operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('production_cost')->default(0); // in poisha
            $table->text('operator_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'print_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_jobs');
        Schema::dropIfExists('design_proofs');
        Schema::dropIfExists('artworks');
    }
};
