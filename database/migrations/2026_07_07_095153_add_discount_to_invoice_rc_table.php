<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_rc', function (Blueprint $table) {
            // Adds a discount column defaulting to 0.00, placed after the price column
            $table->decimal('discount', 10, 2)->default(0.00)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_rc', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};