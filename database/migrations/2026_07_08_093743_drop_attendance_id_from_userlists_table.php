<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('userlists', function (Blueprint $table) {
            // Drops the column safely
            $table->dropColumn('attendece_id'); 
        });
    }

    public function down(): void
    {
        Schema::table('userlists', function (Blueprint $table) {
            // Restores the column if you ever roll back this migration
            $table->unsignedBigInteger('attendece_id')->nullable(); 
        });
    }
};
