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
    Schema::table('products_group', function (Blueprint $table) {
        // Adding manager_id after the product_group name
        $table->unsignedBigInteger('manager_id')->nullable()->after('product_group');
        
        // Optional: Add a foreign key if manager_id refers to the 'id' in 'users' table
        // $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('products_group', function (Blueprint $table) {
        $table->dropColumn('manager_id');
    });
}
};
