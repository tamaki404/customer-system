<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'order_id',
        'po_id',
        'supplier_id',
        'status',
        'total_amount',
        'order_date',
        'payment_status',
        'document_path',
    ];
    protected $casts = [
    'order_date' => 'datetime',
];
    
    public function item()
    {
        return $this->hasOne(OrderItem::class, 'order_id', 'order_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
    public function getTotalAmountAttribute()
    {
        return $this->items->sum('total_price');
    }
    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'supplier_id');
    }
    public function receipts()
{
    return $this->hasMany(Receipts::class, 'order_id', 'order_id');
}
    public function user()
{
    return $this->belongsTo(User::class, 'supplier_id', 'supplier_id');
}
    public function signatory()
{
    return $this->belongsTo(Signatories::class, 'supplier_id', 'supplier_id');
}
public function deliveries()
{
    return $this->hasMany(Delivery::class, 'order_id', 'order_id');
}
public function delitem()
{
    return $this->hasMany(DeliveryItems::class, 'order_id', 'order_id');
}
public function del()
{
    return $this->hasMany(Delivery::class, 'order_id', 'order_id');
}
    public function requirements()
    {
        return $this->belongsTo(DeliveryRequirements::class, 'supplier_id', 'supplier_id');
    }

}
