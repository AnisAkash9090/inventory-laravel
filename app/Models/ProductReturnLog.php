<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReturnLog extends Model
{
    use HasFactory;

    // Explicitly declaring table name since it uses multiple underscores
    protected $table = 'product_return_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'manager_id',
        'created_by',
        'approved_by',
        'size',
        'cost',
        'qty',
        'price',
        'invoice_no',
        'seller_ledger',
        'type',
        'return_date',
        'approve_date',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'return_date' => 'date',
        'approve_date' => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the product being returned.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the manager handling this return session.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the user who initialized the log entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the return entry.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}