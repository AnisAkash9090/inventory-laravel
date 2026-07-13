<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'name', 
        'address', 
        'branch', 
        'contact', 
        'ledger', 
        'manager_id', 
        'created_by'
    ];
public function ledgerDetails2()
    {
        // Option A: If your 'sellers' table has a 'ledger' column that references an 'id' on the ledgers table:
        return $this->belongsTo(Ledger::class, 'ledger', 'id');
        
        // Option B: If it's a standard one-to-one relationship where ledger has the foreign key:
        // return $this->hasOne(Ledger::class, 'seller_id', 'id');
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}