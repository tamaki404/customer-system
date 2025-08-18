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
        'reference_code',
        'receiver_name',
        'shipping_address',
        'billing_address',
        'contact_phone',
        'contact_email',
        'order_notes',
        'po_attachment',
        'subtotal',
        'tax_amount',
        'grand_total',
        'status',
        'order_date',
        'approved_at',
        'rejected_at',
        'delivered_at',
        'approved_by',
        'rejected_by'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'order_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
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
