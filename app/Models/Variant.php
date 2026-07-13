<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = [
        'name', 
        'manager_id'
    ];

    public function scopeForManager($query, $managerId)
    {
        return $query->where(function($q) use ($managerId) {
         $q->where('manager_id', $managerId)
  ->orWhere('manager_id', 0);
        });
    }
}