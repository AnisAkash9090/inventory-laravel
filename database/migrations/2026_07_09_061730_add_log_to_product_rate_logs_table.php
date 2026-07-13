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
        Schema::table('product_rate_logs', function (Blueprint $table) {
            // 1. Adds a decimal column for the active sale rate with financial precision
            $table->decimal('sale_rate', 12, 2)->default(0.00)->after('new_rate');
            
            // 2. Adds the descriptive text log column
            $table->string('log', 255)->nullable()->after('sale_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_rate_logs', function (Blueprint $table) {
            // Drop both columns if rolled back
            $table->dropColumn(['sale_rate', 'log']);
        });
    }
};