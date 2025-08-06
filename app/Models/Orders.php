<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

       protected $table = 'orders';
        protected $fillable = [
            'order_id',
            'product_id',
            'quantity',
            'unit_price',
            'total_price',
            'customer_id',
        ];

    // Removed order() relationship since there is no orders table

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
