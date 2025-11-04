<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSetting extends Model
{
    protected $fillable = [
        'set_id',
        'product_id',
        'customer_id',
        'nego_price',
        'added_by',

        

    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
        public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }
        public function sale()
    {
        return $this->belongsTo(ProductSales::class, 'set_id', 'set_id');
    }
        public function user()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }
        public function activeSale()
    {
        return $this->belongsTo(SaleDiscount::class, 'product_id', 'product_id');
    }
}
