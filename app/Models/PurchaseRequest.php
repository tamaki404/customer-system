<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'status',
        'user_id',
        'total_amount',
        'notes',
        'action_by',
        'action_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'action_at' => 'datetime',
    ];

    /**
     * Get the delivery requests for this purchase request
     */
    public function items()
    {
        return $this->hasMany(DeliveryItemRequest::class, 'po_id', 'po_id');
    }
    public function deliveryRequests()
    {
        return $this->hasMany(DeliveryRequest::class, 'po_id', 'po_id');
    }

    /**
     * Get the user who created this purchase request
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function credit()
    {
        return $this->belongsTo(Credits::class, 'user_id', 'user_id');
    }
    /**
     * Get the customer for this purchase request
     */
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'user_id', 'user_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by customer
     */
    public function scopeForCustomer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    public function requirements()
    {
        return $this->belongsTo(DeliveryRequirements::class, 'user_id', 'user_id');
    }
}
