<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSales extends Model
{
    protected $fillable = [
        'sale_id',
        'customer_id',
        'set_id',
        'status',
        'sale_price',
        'start_date',
        'end_date',
        'action_by'
    ];

    public function set()
    {
        return $this->belongsTo(ProductSetting::class, 'set_id', 'set_id');
    }

       public function product()
    {
        return $this->hasMany(Products::class, 'product_id', 'product_id');
    } 
         public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }   
    public function scopeActive($query)
{
    return $query->where('start_date', '<=', now())
                 ->where('end_date', '>=', now());
}

}
