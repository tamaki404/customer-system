<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $fillable = [
        'action_by',
        'action_at',
        'history_id',
        'status',
        'order_id',
        'label',
        'amount',
        'delivery_id',
        'receipt_id',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }
     public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'order_id', 'order_id');
    }   
    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'order_id', 'order_id');
    }
}
