<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    protected $fillable = [
        'po_id',
        'payment_id',
        'customer_id',
        'purchase_id',
        'delivery_id',
        'label',
        'amount',
        'status',
    ];

    public function payment()
    {
        return $this->belongsTo( Payments::class, 'payment_id', 'payment_id');
    }
    public function customer()
    {
        return $this->belongsTo( Customers::class, 'customer_id', 'customer_id');
    }
}
