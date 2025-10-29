<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSetting extends Model
{
    protected $fillable = [
        'set_id',
        'product_id',
        'supplier_id',
        'nego_price',
        'added_by',

        

    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
        public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'supplier_id');
    }
        public function sale()
    {
        return $this->belongsTo(ProductSales::class, 'set_id', 'set_id');
    }
        public function user()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'supplier_id');
    }
        public function activeSale()
    {
        return $this->belongsTo(SaleDiscount::class, 'product_id', 'product_id');
    }
}
