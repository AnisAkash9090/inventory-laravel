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
    Schema::create('journal_book', function (Blueprint $table) {
        $table->id();
        
        // Ledger tracking
        $table->string('dr_ledger'); // The account receiving value
        $table->string('cr_ledger'); // The account giving value
        
        // Amount tracking
        $table->decimal('amount', 15, 2);
        
        // Transaction details
        $table->text('remarks')->nullable();
        $table->date('transaction_date');
        
        // Accountability
        $table->unsignedBigInteger('manager_id')->nullable();
        $table->string('created_by')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_book');
    }
};
