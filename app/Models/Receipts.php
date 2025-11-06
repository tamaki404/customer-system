<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    protected $fillable = [
        'receipt_id',
        'po_id',
        'customer_id',
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
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }
    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'action_by', 'user_id');
    }
}
