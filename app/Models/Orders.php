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
        'document_path',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
    public function getTotalAmountAttribute()
    {
        return $this->items->sum('total_price');
    }

    public function receipts()
{
    return $this->hasMany(Receipts::class, 'order_id', 'order_id');
}


}
