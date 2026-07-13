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
        Schema::table('return_invoice_rc', function (Blueprint $table) {
            // Places 'qty' right before 'price'
            $table->integer('qty')->default(1)->before('cost');
            
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_invoice_rc', function (Blueprint $table) {
            $table->dropColumn(['qty' ]);
        });
    }
};