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
        'unit_price',
        'total_price',
        'status',
        'placed_heads',
        'placed_kilos',
        
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    // Relation to ProductSetting through supplier_id
    public function productSetting()
    {
        return $this->hasOne(ProductSetting::class, 'set_id', 'set_id');
    }
    public function delItem()
    {
        return $this->belongsTo(DeliveryItems::class, 'order_item_id', 'order_item_id');
    }
}