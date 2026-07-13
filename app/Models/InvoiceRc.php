<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceRc extends Model
{
    // Define the custom table name
    protected $table = 'invoice_rc';

    // Define the primary key if it's not 'id' (optional, only if different)
    protected $primaryKey = 'id';

    // Allow mass assignment for these fields
    protected $fillable = [
        'invoice_id',
        'name',
        'address',
        'ledger_id',
        'invoice_date',
        'amount',
        'manager_id',
        'createdBy', // Note: Check if your DB uses camelCase or snake_case
        'create_date',
        'discount',
        'cost'
    ];

    // Disable timestamps if you aren't using created_at/updated_at,
    // otherwise, keep them as true.
    public $timestamps = false;
}