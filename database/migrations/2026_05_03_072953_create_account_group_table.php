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
    Schema::create('account_group', function (Blueprint $table) {
        $table->id(); // Auto-incrementing ID
        $table->string('name'); // Name of the group
        
        // master_group usually refers to a parent group in the same table
        // We use nullable() in case it's a top-level group
        $table->unsignedBigInteger('master_group')->nullable();
        
        // Status: 1 for Active, 0 for Inactive
        $table->tinyInteger('status')->default(1);
        
        $table->timestamps(); // Adds created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_group');
    }
};
