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
       
        // 2. ADD WARRANTY COUNTER METRICS
        Schema::table('product_store', function (Blueprint $table) {
            if (!Schema::hasColumn('product_store', 'warenty_in_count')) {
                $table->integer('warenty_in_count')->default(0)->after('qty');
            }
            if (!Schema::hasColumn('product_store', 'warenty_out_count')) {
                $table->integer('warenty_out_count')->default(0)->after('warenty_in_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_store', function (Blueprint $table) {
            $table->dropColumn(['warenty_in_count', 'warenty_out_count']);
        });

    }
};