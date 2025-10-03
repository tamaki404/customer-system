<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalCeiling extends Model
{
    protected $fillable = [
        'product_id',
        'ceiling_price',
        'start_date',
        'end_date',
        'sale_price',
        'customer_id',
        'staff_id',
    ];
}
