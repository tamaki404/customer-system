<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'order_id',
        'supplier_id',
        'status',
        'image',
        'image_mime_type',
        'image_filename',
        'image_size',
    ];

}
