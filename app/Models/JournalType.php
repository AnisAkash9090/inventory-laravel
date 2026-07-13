<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalType extends Model
{
    use HasFactory;

    // Table name defaults to 'journal_types' matching Laravel conventions
    protected $fillable = ['name']; 
}