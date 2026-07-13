<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    
    protected $fillable = [
        'invoice_no', 'product_id', 'group_id', 
        'size', 'qty', 'price', 'createdBy', 'manager_id','status','discount','cost'
    ];
public function getSizeNameAttribute()
{
    // Fetch the size name based on the ID stored in the 'size' column
    $size = \App\Models\Size::find($this->size);
    return $size ? $size->name : 'N/A';
}

// Ensure you include this so the attribute is included in your JSON response
protected $appends = ['size_name'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}