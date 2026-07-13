<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    protected $table = 'ledger';
    protected $fillable = [
        'ledger', 'name', 'contact', 'address', 'status', 
        'account_group_id', 'manager_id', 'created_by'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(AccountGroup::class, 'account_group_id');
    }
}