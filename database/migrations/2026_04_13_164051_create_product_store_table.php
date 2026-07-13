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
    Schema::create('product_store', function (Blueprint $table) {
        $table->id();
        // Link to the main products table
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        
        $table->string('size')->nullable(); // e.g., '100m', '500m', 'XL'
        $table->integer('qty')->default(0);
        $table->decimal('price', 12, 2)->default(0.00); // 12 digits total, 2 after decimal
        $table->integer('sold')->default(0);
        $table->decimal('rating', 3, 2)->default(0.00);
        $table->string('img')->nullable();
        
        $table->timestamps(); // Adds created_at and updated_at
    });
}

public function down(): void
{
    Schema::dropIfExists('product_store');
}
};
