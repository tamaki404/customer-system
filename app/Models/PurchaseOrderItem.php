<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
<<<<<<< HEAD
        'status',
        'placed_heads',
        'placed_kilos',
        'alt_kilos',
        'alt_heads',
        'original_price'
=======
        'new_quantity',
        'poi_id'
>>>>>>> parent of 54b5d0c3 (Add revised system code)
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];


    public function purchaseOrder()
{
    return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
}

    
    public function orderItem(){
        return $this->belongsTo(Orders::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedUnitPriceAttribute()
    {
        return '₱' . number_format($this->unit_price, 2);
    }

<<<<<<< HEAD
    public function req()
    {
        return $this->belongsTo(ProductRequirements::class, 'user_id', 'user_id');
    }
    public function set() {
        return $this->belongsTo(ProductSetting::class, 'set_id', 'set_id');
    }

}
=======
    public function getFormattedTotalPriceAttribute()
    {
        return '₱' . number_format($this->total_price, 2);
    }
}
>>>>>>> parent of 54b5d0c3 (Add revised system code)
