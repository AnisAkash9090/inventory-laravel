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
        Schema::table('journal_book', function (Blueprint $blueprint) {
            // Adds the three new column metrics cleanly right after 'cr_ledger'
            $blueprint->unsignedBigInteger('dr_acc_group')->nullable()->after('cr_ledger');
            $blueprint->unsignedBigInteger('cr_acc_group')->nullable()->after('dr_acc_group');
            $blueprint->string('journal_type', 50)->nullable()->after('cr_acc_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_book', function (Blueprint $blueprint) {
            // Drops the layout definitions safely if rolled back
            $blueprint->dropColumn(['dr_acc_group', 'cr_acc_group', 'journal_type']);
        });
    }
};