<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Userlist extends Model
{
    protected $table = 'userlists'; // VERY IMPORTANT
protected $primaryKey = 'idU'; // Very important since you aren't using 'id'
    protected $fillable = [
        'name',
        'address',
        'attendece_id',
        'img',
        'sts',
        'createinfo',
        'email',
        'type_manage',
        'password'
    ];
}
