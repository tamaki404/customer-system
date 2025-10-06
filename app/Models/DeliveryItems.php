<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryItems extends Model
{
    protected $fillable = [
        'order_id',
        'supplier_id',
        'status',
        'day',
        'quantity'
    ];
}
