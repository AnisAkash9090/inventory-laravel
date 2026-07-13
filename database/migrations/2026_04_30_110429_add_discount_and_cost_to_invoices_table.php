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
        Schema::table('invoices', function (Blueprint $table) {
            // Adding columns after 'price'
            $table->text('discount')->default(0)->after('price');
            $table->decimal('cost', 10, 2)->default(0)->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // This allows you to rollback the migration if needed
            $table->dropColumn(['discount', 'cost']);
        });
    }
};
