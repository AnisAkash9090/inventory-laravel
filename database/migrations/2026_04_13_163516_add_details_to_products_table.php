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
   Schema::table('products', function (Blueprint $table) {
        $table->string('img')->nullable()->after('product_name');
        $table->integer('quantity')->default(0)->after('group_id');
        $table->integer('sold')->default(0)->after('quantity');
        $table->decimal('rating', 3, 2)->default(0.00)->after('sold'); // Supports 0.00 to 5.00
    });
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['img', 'quantity', 'sold', 'rating']);
    });
}
};
