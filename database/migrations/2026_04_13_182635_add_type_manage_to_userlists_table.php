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
    Schema::table('userlists', function (Blueprint $table) {
        // Adding 'type_manage' as a string. 
        // You can use this for roles like 'Admin', 'Manager', 'ISP-Staff', etc.
        $table->string('type_manage')->nullable()->after('sts');
    });
}

public function down(): void
{
    Schema::table('userlists', function (Blueprint $table) {
        $table->dropColumn('type_manage');
    });
}
};
