<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    protected $fillable = [
        'po_id',
        'purchase_id',
        'delivery_id',
        'label',
        'amount',
        'status',
    ];
}
