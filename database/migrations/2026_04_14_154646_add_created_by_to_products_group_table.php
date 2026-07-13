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
    Schema::table('products_group', function (Blueprint $table) {
        // Adds the column after manager_id
        $table->string('created_by')->nullable()->after('manager_id');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_group', function (Blueprint $table) {
            //
        });
    }
};
