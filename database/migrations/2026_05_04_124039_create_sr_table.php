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
    Schema::create('sr', function (Blueprint $table) {
        // 'id' will be the primary key (auto-incrementing bigint)
        $table->id(); 
        
        // 'ledger' will be unique, but not the primary key
        $table->string('ledger')->unique(); 
        
        $table->string('name');
        $table->string('contact');
        $table->text('address')->nullable();
        $table->string('status')->default('Active');
        $table->string('company')->nullable();
        $table->string('branch')->nullable();
        $table->text('company_address')->nullable();
        
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sr');
    }
};
