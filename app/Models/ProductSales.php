<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSales extends Model
{
    protected $fillable = [
        'sale_id',
        'supplier_id',
        'set_id',
        'sale_price',
        'start_date',
        'end_date',
    ];
}
