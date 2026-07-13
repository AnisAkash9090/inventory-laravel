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
    // Sizes Table
    Schema::create('sizes', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., XL, 10kg
        $table->unsignedBigInteger('manager_id')->nullable(); // null = public
        $table->timestamps();
    });

    // Variants Table
    Schema::create('variants', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., Red, Cotton
        $table->unsignedBigInteger('manager_id')->nullable(); // null = public
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_tables');
    }
};
