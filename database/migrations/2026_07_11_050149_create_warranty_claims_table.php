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
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            
            // Core Relations Matrix Links
            $table->string('invoice_no', 50)->nullable(); // References the original purchase or return link
            $table->unsignedBigInteger('seller_ledger_id'); // Client/Reseller making the claim
            $table->unsignedBigInteger('vendor_ledger_id')->nullable(); // Target supply manufacturer handling repairs
            $table->unsignedBigInteger('product_id');
            $table->string('size', 20);
            $table->integer('qty')->default(1);
            
            // Workflow Step Status Mapping Engine
            // Steps: 'claimed_by_client', 'kept_in_store', 'returned_to_client'
            $table->enum('status', ['claimed_by_client', 'kept_in_store', 'returned_to_client'])
                  ->default('claimed_by_client');

            // STEP 1: Initial Client Claim Metrics
           
            $table->text('client_claim_remarks')->nullable();
            $table->unsignedBigInteger('create_by')->nullable(); // Logged user tracking entries
 $table->date('client_claim_date')->nullable();
            // STEP 2: Warehouse Internal Retention Metrics (Kept in Store)
            $table->date('store_receive_date')->nullable();

            // STEP 3: Return Lifecycle Resolutions (Returned to Client)
            $table->date('client_return_date')->nullable();
            $table->text('resolution_remarks')->nullable();
            $table->unsignedBigInteger('provide_by')->nullable();

            // Auditing Timestamps
            $table->timestamps();

            // Indexing configuration profiles for ultra-fast tracking filtering
            $table->index(['seller_ledger_id', 'product_id', 'size'], 'warranty_lookup_index');
            $table->index('vendor_ledger_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};