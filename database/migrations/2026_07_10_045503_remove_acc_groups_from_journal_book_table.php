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
        Schema::table('journal_book', function (Blueprint $table) {
            // Drop the specified columns
            $table->dropColumn(['dr_acc_group', 'cr_acc_group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_book', function (Blueprint $table) {
            // Add them back in case you need to roll back this migration later
            $table->string('dr_acc_group')->nullable();
            $table->string('cr_acc_group')->nullable();
        });
    }
};