<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_item_id',
        'order_id',
        'product_id',
        'set_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
    ];
}