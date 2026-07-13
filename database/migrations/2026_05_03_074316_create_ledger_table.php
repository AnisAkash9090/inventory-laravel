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
    Schema::create('ledger', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        
        // Linking to the account_group table
        $table->unsignedBigInteger('account_group');
        $table->foreign('account_group')->references('id')->on('account_group');
        
        $table->tinyInteger('status')->default(1);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger');
    }
};
