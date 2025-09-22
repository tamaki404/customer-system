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

}
