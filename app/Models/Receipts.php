<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    protected $fillable = [
        'receipt_id',
        'supplier_id',
        'order_id',
        'status',
        'total_amount',
        'image',
        'image_mime_type',
        'image_filename',
        'image_size',
    ];
    
}
