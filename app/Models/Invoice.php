<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_number',
        'po_number',
        'billing_address',
        'subtotal',
        'tax_amount',
        'grand_total',
        'status',
        'order_date',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'order_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'delivered_at' => 'datetime',  
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'invoice_date' => 'datetime',  
        'quantity' => 'integer',
    ];



    public function purchaseOrder() {
        return $this->hasOne(PurchaseOrder::class, 'po_number', 'po_number');
    }
    public function invoiceItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_number', 'po_number');
    }

}
