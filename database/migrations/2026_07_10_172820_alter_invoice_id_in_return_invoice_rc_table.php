<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_invoice_rc', function (Blueprint $table) {
            // Converts the field to a string type that accepts letters, numbers, and hyphens
            $table->string('invoice_id', 255)->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('return_invoice_rc', function (Blueprint $table) {
            $table->integer('invoice_id')->change();
        });
    }
};