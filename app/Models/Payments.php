<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $fillable = [
        'payment_id',
        'po_id',
        'customer_id',
        'delivery_id',
        'status',
        'total_amount',
        'reason',
        'image',
        'label',
        'action_by',
        'action_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }
}
