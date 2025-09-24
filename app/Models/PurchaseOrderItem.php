<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'po_item_id',
        'po_id',
        'product_id',
        'set_id',
        'supplier_quantity',
        'staff_quantity',
        'unit_price',
        'total_price',
        'status',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrders::class, 'po_id', 'po_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function productSetting()
    {
        return $this->belongsTo(ProductSetting::class, 'set_id', 'set_id');
    }
}
