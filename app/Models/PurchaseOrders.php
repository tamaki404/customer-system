<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrders extends Model
{
    protected $fillable = [
        'po_id',
        'supplier_id',
        'status',
        'notes',
        'total_amount',
        'staff_id',
        'placed_at',
        'confirmed_at',
    ];
    protected $casts = [
    'confirmed_at' => 'datetime',
];


    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'supplier_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'staff_id', 'staff_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id', 'po_id');
    }
}
