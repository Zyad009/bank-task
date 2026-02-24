<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['amount', 'refrance_key', 'date', 'notes'];
    protected $casts = [ 'notes' => 'array'];
}
