<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReceipt extends Model
{
    protected $fillable = [
        'po_id',
        'or_id',
        'feedback',
        'report_subject',
        'label',
        'status',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }


}
