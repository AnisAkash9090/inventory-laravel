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
    Schema::create('sellers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('address')->nullable();
        $table->string('branch')->nullable();
        $table->string('contact')->nullable();
        
        // Ledger often tracks the balance owed or paid to the seller
        $table->decimal('ledger')->nullable(); 
        
        // Tracking ownership
        $table->unsignedBigInteger('manager_id');
        $table->unsignedBigInteger('created_by');

        $table->timestamps();
        
        // Optional: Foreign keys for data integrity
        // $table->foreign('manager_id')->references('id')->on('users');
        // $table->foreign('created_by')->references('id')->on('users');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
