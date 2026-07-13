<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStoreLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_store_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'size',
        'manager_id',
        'createdBy',
        'qty',
        'cost',
        'sellerledger',
        'buydate',
        'invoiceno'
    ];

    /**
     * Get the product associated with this log.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the user who created this log entry.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    /**
     * Get the manager associated with this log.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}