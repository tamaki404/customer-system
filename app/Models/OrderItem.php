<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;


        protected $fillable = [
            'order_id',
            'product_id',
            'quantity',
            'unit_price',
            'total_price',
        ];

    // Removed order() relationship since there is no orders table

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
