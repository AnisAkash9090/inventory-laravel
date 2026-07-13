<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    use HasFactory;

    // Explicitly map the model to a specific table if it doesn't match the plural naming convention
    protected $table = 'warranty_claims';

    // Specify the primary key if it is not an auto-incrementing integer named 'id'
    protected $primaryKey = 'id';

    // Whitelist columns that can be safely saved using mass-assignment arrays (e.g., WarrantyClaim::create($request->all()))
    protected $fillable = [
        'invoice_no',
        'seller_ledger_id',
        'vendor_ledger_id',
        'product_id',
        'size',
        'qty',
        'status',
        'client_claim_remarks',
        'create_by',
        'client_claim_date',
        'store_receive_date',
        'client_return_date',
        'resolution_remarks',
        'provide_by'
    ];

    // Explicitly cast raw database column string values to specific data types automatically
    protected $casts = [
        'client_claim_date' => 'date',
        'store_receive_date' => 'date',
        'client_return_date' => 'date',
        'qty' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | ELONGATED RELATIONSHIPS MATRIX
    |--------------------------------------------------------------------------
    */

    // A warranty claim belongs to a specific Product row profile
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    // A warranty claim belongs to a user who logged it (Staff)
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by', 'id');
    }
}