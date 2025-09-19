<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'order_id',
        'supplier_id',
        'status',
        'quantity',
        'ttoal_amount',

    ];

}
