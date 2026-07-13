<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalBook extends Model
{
      protected $table = 'journal_book';
    protected $fillable = [
        'dr_ledger', 'cr_ledger', 'amount', 'particulars', 'invoice_id',
        'transaction_date', 'manager_id', 'created_by','remarks','journal_type'
    ];

    // Relationship to the Debit Ledger
    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'dr_ledger', 'ledger');
    }

    // Relationship to the Credit Ledger
    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'cr_ledger', 'ledger');
    }
}