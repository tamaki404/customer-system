<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryItems extends Model
{
    use HasFactory;

    protected $table = 'delivery_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'delivery_item_id',
        'delivery_id',
        'order_item_id',
        'product_id',
        'set_id',
        'planned_heads',
        'planned_kilos',
        'received_heads',
        'received_kilos',
        'status',
        'variance_kilos',
        'variance_heads',
    ];

    // A delivery item belongs to a delivery
    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id', 'delivery_id');
    }

    // A delivery item belongs to an order item
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'order_item_id');
    }

    // Each item may be linked directly to a product
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function orderProd()
    {
        return $this->belongsTo(OrderItem::class, 'product_id', 'product_id');
    }

    public function totalDelivered()
    {
        return ($this->received_kilos ?? 0) + ($this->received_heads ?? 0);
    }

    public function isFullyDelivered()
    {
        return $this->status === 'Delivered';
    }
}
