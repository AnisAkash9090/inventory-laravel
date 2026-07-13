<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    protected $table = 'products_group';
protected $fillable = [
    'product_group',
    'manager_id', // Add this here
];
    // Relationship: A group has many products
    public function products()
    {
        // 'group_id' is the column name in your products table
        return $this->hasMany(Product::class, 'group_id');
    }
}