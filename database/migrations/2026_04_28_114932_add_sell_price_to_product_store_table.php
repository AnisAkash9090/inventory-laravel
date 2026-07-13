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
    Schema::table('product_store', function (Blueprint $table) {
        // Creates sell_price with 8 total digits and 2 decimal places
        $table->decimal('sell_price', 8, 2)->after('id')->nullable(); 
    });
}

public function down(): void
{
    Schema::table('product_store', function (Blueprint $table) {
        $table->dropColumn('sell_price');
    });
}




};
