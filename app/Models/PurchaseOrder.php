<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'po_number',
        'receiver_name',
        'receiver_mobile',
        'company_name',
        'postal_code', 'region_id', 'province_id',
        'municipality_id', 'barangay_id', 'street',
        'billing_address',
        'contact_phone',
        'contact_email',
        'order_notes',
        'subtotal',
        'tax_amount',
        'grand_total',
        'status',
        'order_date',
        'approved_at',
        'rejected_at',
        'delivered_at',
        'approved_by',
        'rejected_by',
        'cancelled_by',
        'cancelled_at',
        'cancelled_user_type',
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
    
        'quantity' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->hasOne(Orders::class, 'po_id', 'po_number');
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function orderItem(){
        return $this->belongsTo(Orders::class);
    }




        public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id', 'po_number');
    }


    public function orderItems()
{
    return $this->hasMany(PurchaseOrderItem::class, 'po_id', 'po_number');
}


    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    
    public function region() {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    public function province() {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function municipality() {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }

    public function barangay() {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'barangay_id');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            'delivered' => 'badge-info',
            default => 'badge-light'
        };
    }

    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status);
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function canBeCancelled()
    {
        return $this->status === 'pending';
    }

    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
