<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrders extends Model
{
    protected $fillable = [
        'po_id',
        'supplier_id',
        'order_id',
        'status',
        'image',
        'image_mime_type',
        'image_filename',
        'image_size',
    ];
}
