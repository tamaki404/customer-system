<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_id',
        'type',
        'name',
        'description',
        'value',
        'value_type',
        'category',
        'product_id',
        'start_date',
        'end_date',
        'user_id',
        'quantity',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
    public function set()
    {
        return $this->belongsTo(ProductSetting::class, 'product_id', 'product_id');
    }
    public function user()
    {
        return $this->belongsTo(Staffs::class, 'user_id', 'user_id');
    }
}
