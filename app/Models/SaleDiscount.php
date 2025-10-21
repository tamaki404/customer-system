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
        'staff_id',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'staff_id', 'user_id');
    }
}
