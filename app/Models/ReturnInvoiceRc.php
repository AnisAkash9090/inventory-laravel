<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnInvoiceRc extends Model
{
    use HasFactory;

    // Explicitly binding table to prevent pluralization issues (rc -> rcs)
    protected $table = 'return_invoice_rc';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_id',
        'name',
        'address',
        'ledger_id',
        'invoice_date',
        'amount',
        'cost',
        'remarks',
        'manager_id',
        'created_by',
        'status',
        'approve_date',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'invoice_id' => 'string',
        'ledger_id' => 'integer',
        'amount' => 'decimal:2',
        'cost' => 'decimal:2',
        'invoice_date' => 'date',
        'approve_date' => 'datetime',
    ];

    // ==========================================
    // ELOQUENT RELATIONSHIPS
    // ==========================================

    /**
     * Link to the target general invoice table registry.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Financial ledger map relationship.
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }

    /**
     * The manager structural user connection tracking block.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Account agent record configuration initialization profile pointer.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}