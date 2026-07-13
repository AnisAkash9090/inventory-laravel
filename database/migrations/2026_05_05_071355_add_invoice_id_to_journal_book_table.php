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
        // Adding invoice_id after cr_ledger, default set to 0
        $table->string('invoice_id')->default('0')->after('cr_ledger');
    });
}

public function down(): void
{
    Schema::table('journal_book', function (Blueprint $table) {
        $table->dropColumn('invoice_id');
    });
}
};
