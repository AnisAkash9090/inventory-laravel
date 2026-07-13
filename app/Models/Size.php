<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    // Allows these fields to be filled during creation
    protected $fillable = [
        'name', 
        'manager_id'
    ];

    /**
     * Scope: Filter by Manager OR show Public (NULL) rows
     */
    public function scopeForManager($query, $managerId)
    {
        return $query->where(function($q) use ($managerId) {
          $q->where('manager_id', $managerId)
  ->orWhere('manager_id', 0);
        });
    }
}