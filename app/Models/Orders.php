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
        'status',
        'action_at',
        'action_by',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        
        'quantity' => 'integer',
    ];

    /**
     * Get the product associated with the order.
     * Note: Adjust the foreign key type based on your products table
     */
    public function product()
    {
        // If product_id is string (ULID/UUID):
        return $this->belongsTo(Product::class, 'product_id', 'id');
        
        
        // If product_id is numeric, keep as is:
        // return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    /**
     * Get the customer associated with the order.
     */
    public function customer()
    {
        // Explicitly specify the foreign key since customer_id is string
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    /**
     * Scope to get orders by order_id
     */
    public function scopeByOrderId($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope to get orders by customer
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Get all items for a specific order
     */
    public static function getOrderItems($orderId)
    {
        return static::where('order_id', $orderId)->with('product')->get();
    }

    /**
     * Get total for a specific order
     */
    public static function getOrderTotal($orderId)
    {
        return static::where('order_id', $orderId)->sum('total_price');
    }
}