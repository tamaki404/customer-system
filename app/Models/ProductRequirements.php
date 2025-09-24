<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequirements extends Model
{
    protected $fillable = [
        'user_id',
        'supplier_id',

    ];
}
