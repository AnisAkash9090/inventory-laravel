<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRateLog extends Model
{
    protected $table = 'product_rate_logs';

    protected $fillable = [
        'product_id',
        'manager_id',
        'created_by',
        'prev_rate',
        'new_rate',
        'sold_total',
        'sale_rate',
        'log'
    ];
}