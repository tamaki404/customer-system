<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credits extends Model
{
    protected $fillable = [
        'credit_id',
        'user_id',
        'status',
        'credit_limit',
        'balance',
    ];
}
