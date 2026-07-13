<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStore extends Model
{
    protected $table = 'product_store'; // Match your migration table name
    protected $fillable = ['product_id','sell_price', 'alertqty','size', 'qty', 'price', 'img', 'sold', 'rating'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}