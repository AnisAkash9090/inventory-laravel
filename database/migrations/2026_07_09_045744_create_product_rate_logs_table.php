<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_rate_logs', function (Blueprint $table) {
            $table->id();
            
            // Core Relationships / Trackers
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('created_by');

            // Rate Delta Metrics (Using decimal for precise financial calculations)
            $table->decimal('prev_rate', 12, 2)->default(0.00);
            $table->decimal('new_rate', 12, 2)->default(0.00);

            // Cumulative Total Volume Sold up to this rate change phase
            $table->integer('sold_total')->default(0);

            // System Timestamps (Handles created_at & updated_at)
            $table->timestamps();

            // Optional: Indexing strategy for blazing-fast dashboard reports
            $table->index(['product_id', 'manager_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_rate_logs');
    }
};