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
    Schema::create('invoice_rc', function (Blueprint $table) {
        $table->id(); // Primary Key
        $table->string('invoice_id')->unique(); // Unique identifier for the invoice
        $table->string('name');
        $table->text('address')->nullable();
        $table->string('ledger_id'); // Links to the 'ledger' column in your ledger table
        $table->date('invoice_date');
         $table->decimal('amount', 15, 2);
        // Accountability & Audit
        $table->unsignedBigInteger('manager_id')->nullable(); // Track the manager
        $table->string('createdBy')->nullable();
        $table->timestamp('create_date')->useCurrent();
        
        $table->timestamps(); // Adds created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_rc');
    }
};
