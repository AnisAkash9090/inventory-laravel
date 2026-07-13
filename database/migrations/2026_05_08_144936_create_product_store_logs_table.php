<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_store_logs', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys / IDs
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('createdBy'); // The user who performed the action
            
            // Log Details
            $table->string('size')->nullable();
            $table->integer('qty');
            $table->decimal('cost', 15, 2); // Price at which item was bought/logged
            $table->string('sellerledger'); // Vendor/Supplier name
            $table->date('buydate')->nullable(); // The actual date of purchase
            $table->string('invoiceno')->nullable(); // Reference invoice number
            
            // Standard Timestamps (handles createdate)
            $table->timestamps();

            // Optional: Foreign key constraints (uncomment if tables exist)
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // $table->foreign('manager_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_store_logs');
    }
};