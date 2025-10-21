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
        'reason',
        'image',
        'image_mime_type',
        'image_filename',
        'image_size',
        'action_by',
        'action_at',
    ];
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'supplier_id');
    }
    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'action_by', 'user_id');
    }
}
