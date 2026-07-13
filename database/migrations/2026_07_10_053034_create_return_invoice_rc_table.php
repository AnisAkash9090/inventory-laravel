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
        Schema::create('return_invoice_rc', function (Blueprint $table) {
            $table->id();
            
            // Core Document Linkages & Indexes
            $table->unsignedBigInteger('invoice_id')->index();
            $table->unsignedBigInteger('ledger_id')->index();
            $table->unsignedBigInteger('manager_id')->index();
            $table->unsignedBigInteger('created_by')->index(); 
            
            // Customer / Client Metas
            $table->string('name', 150)->nullable();
            $table->text('address')->nullable();
            
            // Financial Ledger Values
            $table->decimal('amount', 14, 2)->default(0.00);
            $table->decimal('cost', 14, 2)->default(0.00);
            
            // Explanatory Text Strings
            $table->text('remarks')->nullable();
            
            // Precision Dates & Approval Tracks
            $table->date('invoice_date');
            $table->timestamp('approve_date')->nullable();
            $table->timestamps(); // Handles created_at and updated_at automatically
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_invoice_rc');
    }
};