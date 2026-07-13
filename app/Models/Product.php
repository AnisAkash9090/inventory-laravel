<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $casts = [
    'size' => 'array',
    'variant' => 'array',
];
    protected $fillable = ['product_name', 'group_id', 'createdBy','quantity','size','variant'];

  public function group(): BelongsTo
    {
        // The second argument 'group_id' is your foreign key
        return $this->belongsTo(ProductGroup::class, 'group_id');
    }
    public function scopeByGroup(Builder $query, $groupId)
    {
        if ($groupId) {
            return $query->where('group_id', $groupId);
        }
        return $query;
    }

    /**
     * Scope: Created By a specific user
     */
    public function scopeCreatedBy(Builder $query, $user)
    {
        return $query->where('createdBy', $user);
    }
    // Inside class Product extends Model
public function stores()
{
    // A Product has Many Store variants (sizes/prices)
    return $this->hasMany(ProductStore::class, 'product_id');
}

}