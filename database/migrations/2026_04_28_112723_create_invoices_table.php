<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_no')->unique();
        $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('group_id');
        $table->string('size')->nullable();
        $table->string('qty');
        $table->string('price');
        $table->unsignedBigInteger('createdBy'); // The user who made the sale
        $table->unsignedBigInteger('manager_id'); // The manager owning the stock
        $table->timestamps();

        // Foreign keys for data integrity
        $table->foreign('product_id')->references('id')->on('products');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
