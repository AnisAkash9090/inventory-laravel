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
    Schema::table('product_store', function (Blueprint $table) {
        // Adding variant after the size column
        $table->string('variant')->nullable()->after('size');
    });
}

public function down()
{
    Schema::table('product_store', function (Blueprint $table) {
        $table->dropColumn('variant');
    });
}
};
