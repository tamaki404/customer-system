<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promos extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_id',
        'name',
        'description',
        'value',
        'value_type',
        'category',
        'product_id',
        'start_date',
        'end_date',
        'quantity',
        'status',
        'user_id',
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
