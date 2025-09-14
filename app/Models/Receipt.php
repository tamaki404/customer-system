<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Receipt extends Model

{
    use HasFactory;
    protected $table = 'receipts';
    protected $fillable = [
        'receipt_id',

        'receipt_image',
        'receipt_image_mime',
        'purchase_date',
        'store_name',
        'total_amount',
        'invoice_number',
        'notes',
        'receipt_number',
        'status',
        'id',
        'po_id',
        'payment_at',
        'rejected_note',
        'additional_note',
        'action_by',
        'action_at',
        'payment_method'
    ];

    protected $primaryKey = 'receipt_id';
    protected $keyType = 'string';
    public $incrementing = false; // very important!

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
