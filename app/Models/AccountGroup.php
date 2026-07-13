<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountGroup extends Model
{
    use HasFactory;

    protected $table = 'account_group';

    protected $fillable = [
        'name', 
        'master_group', 
        
    ];

    /**
     * Get the parent group (the master group).
     */
    public function parent()
    {
        return $this->belongsTo(AccountGroup::class, 'master_group');
    }

    /**
     * Get the child groups.
     */
    public function children()
    {
        return $this->hasMany(AccountGroup::class, 'master_group');
    }

    /**
     * Scope for active groups only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}