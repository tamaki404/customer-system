<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{

    protected $fillable = [
        'review_id',
        'head',
        'body',
        'user_id',
        'status',
        'raised_by',
        'raised_at',
        'resolved_at',
        'resolved_by',
    ];


}
