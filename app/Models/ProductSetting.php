<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSetting extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_id',
        'price',
        'added_by',
    ];
}
