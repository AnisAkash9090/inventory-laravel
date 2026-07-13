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
        Schema::table('product_return_logs', function (Blueprint $table) {
            // Adjust the data type (e.g., decimal) to match your needs
            $table->decimal('cost', 10, 2)->nullable()->before('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_return_logs', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
};