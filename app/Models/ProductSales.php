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

    public function set()
    {
        return $this->belongsTo(ProductSetting::class, 'set_id', 'set_id');
    }

       public function product()
    {
        return $this->hasMany(Products::class, 'product_id', 'product_id');
    } 
}
