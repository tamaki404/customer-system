<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries';
    protected $primaryKey = 'id';

    protected $fillable = [
        'delivery_id',
        'order_id',
        'supplier_id',
        'delivery_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
    ];


    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryItems::class, 'delivery_id', 'delivery_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'supplier_id');
    }
    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItems::class, 'delivery_id', 'delivery_id');
    }


    public function scopeScheduled($query)
    {
        return $query->where('status', 'Scheduled');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'Delivered');
    }


    public function isDelivered()
    {
        return $this->status === 'Delivered';
    }


}
