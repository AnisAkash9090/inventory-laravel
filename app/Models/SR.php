<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SR extends Model
{
    protected $table = 'sr'; // Explicitly define the table name

    protected $fillable = [
        'ledger', 'name', 'contact', 'address', 'status', 
        'manager_id', 'created_by', 'company', 'branch', 'company_address'
    ];

    /**
     * Relationship: SR belongs to a Ledger
     */
    public function ledgerDetails()
    {
        return $this->belongsTo(Ledger::class, 'ledger');
    }
}
