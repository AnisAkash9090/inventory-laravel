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
        Schema::create('product_return_logs', function (Blueprint $table) {
            $table->id();
            
            // Relational Foreign Keys (Assumed unsignedBigInteger for Laravel conventions)
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('manager_id')->index();
            $table->unsignedBigInteger('created_by')->index(); // Snake_case preferred in Laravel
            $table->unsignedBigInteger('approved_by')->nullable()->index(); 
            
            // Product Specific Details
            $table->string('size', 50)->nullable();
            $table->integer('qty');
            $table->decimal('price', 12, 2); // Handles monetary structures safely
            $table->string('invoice_no', 100)->index();
            
            // Ledger / Accounting Tracking Reference
            $table->string('seller_ledger')->nullable()->index(); 
            
            // ENUM Logic Filters
            $table->enum('type', ['damage', 'solid'])->default('solid');
            $table->enum('status', ['pending', 'approve'])->default('pending');
            
            // Dates and Timestamps
            $table->date('return_date');
            $table->timestamp('approve_date')->nullable();
            $table->timestamps(); // Generates created_at and updated_at seamlessly
            
            /* * Optional: Uncomment these if your database tables exist and use native engine relationships
             * * $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
             * $table->foreign('manager_id')->references('id')->on('users');
             * $table->foreign('created_by')->references('id')->on('users');
             * $table->foreign('approved_by')->references('id')->on('users');
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_return_logs');
    }
};